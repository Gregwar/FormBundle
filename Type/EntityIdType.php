<?php

namespace Gregwar\FormBundle\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

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

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new EntityToIdTransformer(
            $this->registry->getEntityManager($options['em']),
            $options['class'],
            $options['property'],
            $options['query_builder'],
            $options['multiple']
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(array(
            'class',
        ));

        $resolver->setDefaults(array(
            'em'            => null,
            'property'      => null,
            'query_builder' => null,
            'hidden'        => true,
            'multiple'      => false,
        ));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (true === $options['hidden']) {
            $view->vars['type'] = 'hidden';
        }
    }

    public function getParent()
    {
        return 'text';
    }

    public function getName()
    {
        return 'entity_id';
    }
}
