<?php

namespace User\Form;

use Zend\Db\Adapter\Adapter;
use Zend\Form\Form;

use User\Form\LoginFilter;

class LoginForm extends Form
{
    public function __construct()
    {
        parent::__construct('login');
        $this->setInputFilter(new LoginFilter());
        $this->setAttribute('method', 'post');

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
                'autofocus' => 'autofocus',
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
            'type'  => 'checkbox',
            'name' => 'remember_me',
            'options' => [
                'label' => 'Remember me',
            ],
        ]);

        $this->add([
            'type'  => 'hidden',
            'name' => 'redirect_url'
        ]);

        $this->add([
            'type' => 'csrf',
            'name' => 'csrf',
            'options' => [
                'csrf_options' => [
                'timeout' => 600
                ]
            ],
        ]);

        $this->add([
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => [
                'value' => 'Login',
                'class' => 'btn btn-primary btn-block',
                'id' => 'passwordButton',
            ],
        ]);
    }
}
