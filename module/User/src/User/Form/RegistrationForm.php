<?php

namespace User\Form;

use Zend\Db\Adapter\Adapter;
use Zend\Form\Form;
use Zend\Stdlib\Hydrator\ClassMethods;

use User\Form\RegistrationFilter;

class RegistrationForm extends Form
{
    public function __construct(Adapter $dbAdapter)
    {
        parent::__construct('registration');
        $this->setInputFilter(new RegistrationFilter($dbAdapter));
        $this->setAttribute('method', 'post');
        $this->setHydrator(new ClassMethods());

        $this->add([
            'name' => 'first_name',
            'type' => 'text',
            'options' => [
                'label' => 'First Name',
            ],
            'attributes' => [
                'class' => 'form-control',
                'id' => 'first_name',
                'required' => 'required',
                'autofocus' => 'autofocus',
                'placeholder' => 'First Name',
            ],
        ]);

        $this->add([
            'name' => 'last_name',
            'type' => 'text',
            'options' => [
                'label' => 'Last Name',
            ],
            'attributes' => [
                'class' => 'form-control',
                'id' => 'last_name',
                'required' => 'required',
                'placeholder' => 'Last Name',
            ],
        ]);

        $this->add([
            'name' => 'email',
            'type' => 'email',
            'options' => [
                'label' => 'Email Address',
            ],
            'attributes' => [
                'class' => 'form-control',
                'id' => 'email',
                'required' => 'required',
                'placeholder' => 'Email Address',
            ],
        ]);

        $this->add([
            'name' => 'password',
            'type' => 'password',
            'options' => [
                'label' => 'Password',
            ],
            'attributes' => [
                'class' => 'form-control',
                'id' => 'password',
                'required' => 'required',
            ],
        ]);

        $this->add([
            'name' => 'confirm_password',
            'type' => 'password',
            'options' => [
                'label' => 'Confirm password',
            ],
            'attributes' => [
                'class' => 'form-control',
                'id' => 'confirm_password',
                'required' => 'required',
            ],
        ]);

        $this->add([
            'name' => 'mobile_no',
            'type' => 'text',
            'options' => [
                'label' => 'Mobile No.',
            ],
            'attributes' => [
                'class' => 'form-control',
                'id' => 'mobile_no',
                'required' => 'required',
                'placeholder' => 'Mobile No.',
            ],
        ]);

        $this->add(array(
    			'name' => 'security',
    			'type' => 'Csrf',
    		));

    		$this->add(array(
    			'name' => 'captcha',
    			'type' => 'Captcha',
    			'attributes' => array(
    				'class' => 'form-control',
    				'id' => 'captcha',
    				'placeholder'  => 'Please verify you are human.',
    				'required' => 'required',
    			),
    			'options' => array(
    				'label' => 'Security Code *',
    				'captcha' => array(
    					'class' => 'Dumb',
    	        'wordLen' => 3,
    				),
    			),
    		));

        $this->add([
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => [
                'value' => 'Register',
                'class' => 'btn btn-primary btn-block',
                'id' => 'registerButton',
            ],
        ]);
    }
}
