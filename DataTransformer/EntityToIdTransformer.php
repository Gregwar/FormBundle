<?php

namespace Gregwar\FormBundle\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\PropertyAccess\PropertyAccessor;

use Doctrine\ORM\NoResultException;

/**
 * Data transformation class
 *
 * @author Gregwar <g.passault@gmail.com>
 */
class EntityToIdTransformer implements DataTransformerInterface
{
    protected $em;
    private $class;
    private $property;
    private $queryBuilder;
    private $multiple;

    private $unitOfWork;

    public function __construct(EntityManager $em, $class, $property, $queryBuilder, $multiple)
    {
        if (!(null === $queryBuilder || $queryBuilder instanceof QueryBuilder || $queryBuilder instanceof \Closure)) {
            throw new UnexpectedTypeException($queryBuilder, 'Doctrine\ORM\QueryBuilder or \Closure');
        }

        if (null == $class) {
            throw new UnexpectedTypeException($class, 'string');
        }

        $this->em = $em;
        $this->unitOfWork = $this->em->getUnitOfWork();
        $this->class = $class;
        $this->queryBuilder = $queryBuilder;
        $this->multiple = $multiple;

        if ($property) {
            $this->property = $property;
        }
    }

    public function transform($data)
    {
        if (null === $data) {
            return null;
        }

        if (!$this->multiple) {
            return $this->transformSingleEntity($data);
        }

        $return = array();

        foreach ($data as $element) {
            $return[] = $this->transformSingleEntity($element);
        }

        return implode(', ', $return);
    }

    protected function splitData($data)
    {
        return is_array($data) ? $data : explode(',', $data);
    }


    protected function transformSingleEntity($data)
    {
        if (!$this->unitOfWork->isInIdentityMap($data)) {
            throw new TransformationFailedException('Entities passed to the choice field must be managed');
        }

        if ($this->property) {
            $propertyAccessor = new PropertyAccessor();
            return $propertyAccessor->getValue($data, $this->property);
        }

        return current($this->unitOfWork->getEntityIdentifier($data));
    }

    public function reverseTransform($data)
    {
        if (!$data) {
            return null;
        }

        if (!$this->multiple) {
            return $this->reverseTransformSingleEntity($data);
        }

        $return = array();

        foreach ($this->splitData($data) as $element) {
            $return[] = $this->reverseTransformSingleEntity($element);
        }

        return $return;
    }

    protected function reverseTransformSingleEntity($data)
    {
        $em = $this->em;
        $class = $this->class;
        $repository = $em->getRepository($class);

        if ($qb = $this->queryBuilder) {
            if ($qb instanceof \Closure) {
                $qb = $qb($repository, $data);
            }

            try {
                $result = $qb->getQuery()->getSingleResult();
            } catch (NoResultException $e) {
                $result = null;
            }
        } else {
            if ($this->property) {
                $result = $repository->findOneBy(array($this->property => $data));
            } else {
                $result = $repository->find($data);
            }
        }

        if (!$result) {
            throw new TransformationFailedException('Can not find entity');
        }

        return $result;
    }
}
