<?php

namespace Category\Form;

use Zend\Db\Adapter\Adapter;
use Zend\Form\Form;

use Category\Form\CategorySearchFilter;

class CategorySearchForm extends Form
{
    public function __construct(Adapter $dbAdapter)
    {
        parent::__construct('category');
        $this->setInputFilter(new CategorySearchFilter($dbAdapter));
        $this->setAttribute('method', 'post');

        $this->add([
            'name' => 'category_keyword',
            'type' => 'text',
            'attributes' => [
                'class' => 'form-control',
                'placeholder' => '%Category%',
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
