<?php

namespace Category\Form;

use Zend\Db\Adapter\Adapter;
use Zend\Form\Form;
use Zend\Stdlib\Hydrator\ClassMethods;

use Category\Form\CategoryFilter;

class CategoryForm extends Form
{
    public function __construct(Adapter $dbAdapter, $id = 0)
    {
        parent::__construct('category-add');
        $this->setInputFilter(new CategoryFilter($dbAdapter, $id));
        $this->setAttribute('method', 'post');
        $this->setHydrator(new ClassMethods());

        $this->add([
            'name' => 'category',
            'type' => 'text',
            'options' => [
                'label' => 'Category',
            ],
            'attributes' => [
                'class' => 'form-control',
                'id' => 'category',
                'required' => 'required',
                'data-validation-required-message' => 'Please enter your category.',
                'autofocus' => 'autofocus',
                'placeholder' => 'Category',
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
