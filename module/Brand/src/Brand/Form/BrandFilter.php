<?php

namespace Brand\Form;

use Zend\Db\Adapter\Adapter;
use Zend\InputFilter\InputFilter;

class BrandFilter extends InputFilter
{
    public function __construct(Adapter $dbAdapter, $id = 0)
    {
        $this->add([
            'name' => 'brand',
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
                    'table' => 'brand',
                    'field' => 'brand',
                    'exclude' => array(
                      'field' => 'id',
                      'value' => $id,
                    )
                  ],
                ],
            ],
        ]);

        $this->add([
            'name' => 'description',
            'required' => true,
            'filters' => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
        ]);

        $this->add(array(
  		    'name' => 'logo',
  		    'required' => false,
    		));

    }
}
