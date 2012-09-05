<?php

namespace Gregwar\FormBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FormTest extends WebTestCase
{
    static protected function createKernel(array $options = array())
    {
        return self::$kernel = new AppKernel('test', true);
    }

    /**
     * @dataProvider getTestFormData
     */
    public function testForm($hidden, $type)
    {
        $kernel = $this->createKernel();
        $kernel->boot();

        $formBuilder = $kernel->getContainer()->get('form.factory')->createBuilder('form');
        $formBuilder->add('user', 'entity_id', array(
            'class' => 'Gregwar\FormBundle\Tests\Functional\User',
            'hidden' => $hidden,
        ));
        $form = $formBuilder->getForm();

        $html = $kernel->getContainer()->get('twig')->render('::view.html.twig', array(
            'form' => $form->createView(),
        ));

        $this->assertEquals('<input type="'.$type.'" id="form_user" name="form[user]" required="required" />', trim($html));
    }

    /**
     * @expectedException Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     * @expectedExceptionMessage The required option "class" is  missing.
     */
    public function testFormWithNoClass()
    {
        $kernel = $this->createKernel();
        $kernel->boot();

        $formBuilder = $kernel->getContainer()->get('form.factory')->createBuilder('form');
        $formBuilder->add('user', 'entity_id');
        $form = $formBuilder->getForm();
    }

    public function getTestFormData()
    {
        return array(
            array(true, 'hidden'),
            array(false, 'text')
        );
    }
}
