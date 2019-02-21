<?php

namespace User\Form;

use Zend\Db\Adapter\Adapter;
use Zend\InputFilter\InputFilter;

class ForgotPasswordFilter extends InputFilter
{
    public function __construct(Adapter $dbAdapter)
    {
        $this->add([
            'name' => 'email',
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
                        'max' => 100,
                    ],
                ],
                [
                  'name' => 'EmailAddress',
                  'options' => [
                    'allow' => \Zend\Validator\Hostname::ALLOW_DNS,
                    'useMxCheck' => false,
                  ],
                ],
                [
                  'name' => 'Zend\Validator\Db\RecordExists',
                  'options' => [
                    'adapter' => $dbAdapter,
                    'table' => 'user',
                    'field' => 'email',
                  ],
                ],
            ],
        ]);

    }
}
