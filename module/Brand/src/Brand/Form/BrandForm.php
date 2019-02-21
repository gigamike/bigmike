<?php

namespace Brand\Form;

use Zend\Db\Adapter\Adapter;
use Zend\Form\Form;
use Zend\Stdlib\Hydrator\ClassMethods;

use Brand\Form\BrandFilter;

class BrandForm extends Form
{
    public function __construct(Adapter $dbAdapter, $id = 0)
    {
        parent::__construct('brand-add');
        $this->setInputFilter(new BrandFilter($dbAdapter, $id));
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');
        $this->setHydrator(new ClassMethods());

        $this->add([
            'name' => 'brand',
            'type' => 'text',
            'options' => [
                'label' => 'Brand',
            ],
            'attributes' => [
                'class' => 'form-control',
                'id' => 'brand',
                'required' => 'required',
                'data-validation-required-message' => 'Please enter your brand.',
                'autofocus' => 'autofocus',
                'placeholder' => 'Brand',
            ],
        ]);

        $this->add([
            'name' => 'description',
            'type' => 'textarea',
            'options' => [
                'label' => 'Description',
            ],
            'attributes' => [
                'class' => 'form-control',
                'id' => 'description',
                'required' => 'required',
                'data-validation-required-message' => 'Please enter your description.',
                'placeholder' => 'Description',
                'rows' => 10,
            ],
        ]);

        $this->add(array(
    		    'name' => 'logo',
    		    'attributes' => array(
    		        'type'  => 'file',
                'id' => 'logo',
    		    ),
    		    'options' => array(
    		        'label' => 'Logo',
    		    ),
    		));

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
