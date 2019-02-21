<?php

namespace Category\Form;

use Zend\Db\Adapter\Adapter;
use Zend\InputFilter\InputFilter;

class CategorySearchFilter extends InputFilter
{
    public function __construct(Adapter $dbAdapter)
    {
        $this->add([
            'name' => 'category_keyword',
            'filters' => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
        ]);
    }
}
