<?php

namespace User\Form;

use Zend\Db\Adapter\Adapter;
use Zend\Form\Form;

use User\Form\AdminSearchUserFilter;

class AdminUserSearchForm extends Form
{
    public function __construct(Adapter $dbAdapter)
    {
        parent::__construct('admin-search-user');
        $this->setInputFilter(new AdminUserSearchFilter($dbAdapter));
        $this->setAttribute('method', 'post');

        $this->add([
            'name' => 'first_name_keyword',
            'type' => 'text',
            'attributes' => [
                'class' => 'form-control',
                'placeholder' => '%First Name%',
            ],
        ]);

        $this->add([
            'name' => 'last_name_keyword',
            'type' => 'text',
            'attributes' => [
                'class' => 'form-control',
                'placeholder' => '%Last Name%',
            ],
        ]);

        $this->add([
            'name' => 'email_keyword',
            'type' => 'text',
            'attributes' => [
                'class' => 'form-control',
                'placeholder' => '%Email Address%',
            ],
        ]);

        $this->add([
            'name' => 'role',
            'type' => 'Select',
            'attributes' => [
    		        'class' => 'form-control',
    		        'options' => [
                    '' => 'All Roles',
    		            'admin' => 'admin',
    		            'member' => 'member',
    		        ],
    		    ],
        ]);

        $this->add([
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => [
                'value' => 'Search',
                'class' => 'btn btn-primary',
            ],
        ]);
    }
}
