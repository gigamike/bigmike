<?php

namespace Category\Form;

use Zend\Db\Adapter\Adapter;
use Zend\InputFilter\InputFilter;

class CategoryFilter extends InputFilter
{
    public function __construct(Adapter $dbAdapter, $id = 0)
    {
        $this->add([
            'name' => 'category',
            'required' => true,
            'filters' => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name' => 'StringLength',
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 1,
                        'max' => 255,
                    ],
                ],
                [
                  'name' => 'Zend\Validator\Db\NoRecordExists',
                  'options' => [
                    'adapter' => $dbAdapter,
                    'table' => 'category',
                    'field' => 'category',
                    'exclude' => array(
                      'field' => 'id',
                      'value' => $id,
                    )
                  ],
                ],
            ],
        ]);

    }
}
