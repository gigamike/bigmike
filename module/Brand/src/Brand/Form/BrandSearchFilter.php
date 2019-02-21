<?php

namespace Brand\Form;

use Zend\Db\Adapter\Adapter;
use Zend\InputFilter\InputFilter;

class BrandSearchFilter extends InputFilter
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
