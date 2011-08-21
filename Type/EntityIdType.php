<?php

namespace Gregwar\FormBundle\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

use Gregwar\FormBundle\DataTransformer\EntityToIdTransformer;

/**
 * Entity identitifer
 *
 * @author Gregwar <g.passault@gmail.com>
 */
class EntityIdType extends AbstractType
{
    protected $registry;

    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->prependClientTransformer(new EntityToIdTransformer(
            $this->registry->getEntityManager($options['em']),
            $options['class'], 
            $options['query_builder']
        ));
    }

    public function getDefaultOptions(array $options)
    {
        $defaultOptions = array(
            'em'                => null,
            'class'             => null,
            'property'          => null,
            'query_builder'     => null,
            'type'              => 'hidden',
            'hidden'            => true,
        );

        $options = array_replace($defaultOptions, $options);

        if (null === $options['class']) {
            throw new \RunTimeException('You must provide a class option for the entity identifier field');
        }

        return $options;
    }

    public function getParent(array $options)
    {
        return $options['hidden'] ? 'hidden' : 'text';
    }

    public function getName()
    {
        return 'entity_id';
    }
}
