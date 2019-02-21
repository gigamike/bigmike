<?php

namespace Brand\Form;

use Zend\Db\Adapter\Adapter;
use Zend\Form\Form;

use Brand\Form\BrandSearchFilter;

class BrandSearchForm extends Form
{
    public function __construct(Adapter $dbAdapter)
    {
        parent::__construct('category');
        $this->setInputFilter(new BrandSearchFilter($dbAdapter));
        $this->setAttribute('method', 'post');

        $this->add([
            'name' => 'category_keyword',
            'type' => 'text',
            'attributes' => [
                'class' => 'form-control',
                'placeholder' => '%Brand%',
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
