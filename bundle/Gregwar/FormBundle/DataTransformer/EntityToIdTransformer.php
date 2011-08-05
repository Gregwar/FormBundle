<?php

namespace Gregwar\FormBundle\DataTransformer;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

use Doctrine\ORM\NoResultException;

/**
 * Data transformation class
 *
 * @author Gregwar <g.passault@gmail.com>
 */
class EntityToIdTransformer implements DataTransformerInterface
{
    private $em;
    private $class;
    private $queryBuilder;

    public function __construct($em, $class, $queryBuilder)
    {
        if (!(null === $queryBuilder || $queryBuilder instanceof QueryBuilder || $queryBuilder instanceof \Closure)) {
            throw new UnexpectedTypeException($queryBuilder, 'Doctrine\ORM\QueryBuilder or \Closure');
        }   

        if (null == $class)
            throw new UnexpectedTypeException($class, 'string');

        $this->em = $em;
        $this->class = $class;
        $this->queryBuilder = $queryBuilder;
    }

    public function transform($data)
    {
        if (null === $data)
            return null;

        $meta = $this->em->getClassMetadata($this->class);

        if (!$meta->getReflectionClass()->isInstance($data))
            throw new TransformationFailedException('Invalid data, must be an instance of '.$this->class);

        $identifierField = $meta->getSingleIdentifierFieldName();
        $id = $meta->getReflectionProperty($identifierField)->getValue($data);

        return array(
            'id' => $id
        );
    }

    public function reverseTransform($data)
    {
        if (!$data) {
            return null;
        }

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
                throw new TransformationFailedException('No entities found');
            }
        } else {
            $result = $repository->find($data);
        }

        if (!$result)
            throw new TransformationFailedException('Entity does not exists');

        return $result;
    }
}

