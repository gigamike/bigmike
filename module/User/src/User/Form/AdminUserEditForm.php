<?php

namespace User\Form;

use Zend\Db\Adapter\Adapter;
use Zend\Form\Form;
use Zend\Stdlib\Hydrator\ClassMethods;

use User\Form\AdminUserEditFilter;

class AdminUserEditForm extends Form
{
    public function __construct(Adapter $dbAdapter, $user)
    {
        parent::__construct('admin-user-edit');
        $this->setInputFilter(new AdminUserEditFilter($dbAdapter, $user));
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
                'data-validation-required-message' => 'Please enter your first name.',
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
                'data-validation-required-message' => 'Please enter your last name.',
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
                'data-validation-required-message' => 'Please enter your email address.',
                'placeholder' => 'Email Address',
            ],
        ]);

        $this->add([
            'name' => 'role',
            'type' => 'Select',
            'options' => [
                'label' => 'Role',
            ],
            'attributes' => [
    		        'class' => 'form-control',
    		        'options' => [
                    '' => 'Select Role',
    		            'admin' => 'admin',
    		            'member' => 'member',
    		        ],
    		    ],
        ]);

        $this->add([
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => [
                'value' => 'Submit',
                'class' => 'btn btn-primary',
            ],
        ]);
    }
}
