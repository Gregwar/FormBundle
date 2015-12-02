<?php

namespace Gregwar\FormBundle\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpKernel\Kernel;

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
        if ('2' == Kernel::MAJOR_VERSION && Kernel::MINOR_VERSION < '1') {
            $em = $this->registry->getEntityManager($options['em']);
        } else {
            $em = $this->registry->getManager($options['em']);
        }

        $builder->addModelTransformer(new EntityToIdTransformer(
            $em,
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
        // BC for SF < 2.8
        if (!method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix')) {
            return 'text';
        }

        return 'Symfony\Component\Form\Extension\Core\Type\TextType';
    }

    public function getBlockPrefix()
    {
        return 'entity_id';
    }

    // BC for SF < 2.8
    public function getName()
    {
        return $this->getBlockPrefix();
    }
}
