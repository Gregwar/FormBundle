<?php

namespace Gregwar\FormBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FormTest extends WebTestCase
{
    static protected function createKernel(array $options = array())
    {
        return self::$kernel = new AppKernel('test', true);
    }

    public function testForm()
    {
        $kernel = $this->createKernel();
        $kernel->boot();

        $formBuilder = $kernel->getContainer()->get('form.factory')->createBuilder('form');
        $formBuilder->add('user', 'entity_id', array(
            'class' => 'Gregwar\FormBundle\Tests\Functional\User',
        ));
        $form = $formBuilder->getForm();
    }
}
