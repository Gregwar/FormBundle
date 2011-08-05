Gregwar's FormBundle
=====================

`GregwarFormBundle` provides the form type "hidden_entity"

Installation
============

To install `GregwarFormBundle`, first adds it to your deps and clone it in your
vendor directory, then add the namespace to your `app/autoload.php` file:

      'Gregwar' => __DIR__.'/../vendor/gregwar-form/bundle/',

And registers the bundle in your `app/AppKernel.php`:

    ...
    public function registerBundles()
    {
        $bundles = array(
            ...
            new Gregwar\FormBundle\GregwarFormBundle(),
            ...
        );
    ...

Adds the following configuration to your `app/config/config.yml`:

    gregwar_form: ~

Usage
=====

The hidden_entity is a field that contains an entity id, this assumes you set up
javascripts or any UI logics to fill it programmatically.

The usage look like the entity field type one, except that the query builder have
to returns one unique result. One full example :

    $builder
        ->add('city', 'hidden_entity', array(
            'required' => false,
            'class' => 'Project\Entity\City',
            'query_builder' => function(EntityRepository $repo, $id) {
                return $repo->createQueryBuilder('c')
                    ->where('c.id = :id AND c.available = 1')
                    ->setParameter('id', $id)
            }
        ))
        ;

Note that if you don't provide any query builder, `->find($id)` will be used.

Notes
=====

There is maybe bugs in this implementations, this package is just an idea of a form
field type which can be very useful for the Symfony2 project.

License
=======

This bundle is under MIT license
