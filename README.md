Gregwar's FormBundle
=====================

`GregwarFormBundle` provides the form type "entity_id"

Installation
============

To install `GregwarFormBundle`, first adds it to your `deps`:

    [GregwarFormBundle]
        git=git://github.com/Gregwar/FormBundle.git
        target=/bundles/Gregwar/FormBundle

And run `php bin/vendors install`. Then add the namespace to your `app/autoload.php` 
file:

```php
<?php
...
'Gregwar' => __DIR__.'/../vendor/bundles',
...
```

And registers the bundle in your `app/AppKernel.php`:

```php
<?php
//...
public function registerBundles()
{
    $bundles = array(
        ...
        new Gregwar\FormBundle\GregwarFormBundle(),
        ...
    );
...
```

Adds the following configuration to your `app/config/config.yml`:

    gregwar_form: ~

Usage
=====

The entity_id is a field that contains an entity id, this assumes you set up
javascripts or any UI logics to fill it programmatically.

The usage look like the entity field type one, except that the query builder have
to returns one unique result. One full example :

```php
<?php
//...
$builder
    ->add('city', 'entity_id', array(
        'class' => 'Project\Entity\City',
        'query_builder' => function(EntityRepository $repo, $id) {
            return $repo->createQueryBuilder('c')
                ->where('c.id = :id AND c.available = 1')
                ->setParameter('id', $id)
        }
    ))
    ;
```

Note that if you don't provide any query builder, `->find($id)` will be used.

You can also chose to show the field, by passing the `hidden` option to `false`:

```php
<?php
//...
$builder
    ->add('city', 'entity_id', array(
        'class' => 'Project\Entity\City',
        'hidden' => false,
        'label' => 'Enter the City id'
    ))
    ;
```

Using the `property` option, you can also use another identifier than the primary
key:

```php
<?php
//...
$builder
    ->add('recipient', 'entity_id', array(
        'class' => 'Project\Entity\User',
        'hidden' => false,
        'property' => 'login',
        'label' => 'Recipient login'
    ))
    ;
```

Notes
=====

There is maybe bugs in this implementations, this package is just an idea of a form
field type which can be very useful for the Symfony2 project.

License
=======

This bundle is under MIT license
