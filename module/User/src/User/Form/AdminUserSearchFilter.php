<?php

namespace User\Form;

use Zend\Db\Adapter\Adapter;
use Zend\InputFilter\InputFilter;

class AdminUserSearchFilter extends InputFilter
{
    public function __construct(Adapter $dbAdapter)
    {
        $this->add([
            'name' => 'first_name_keyword',
            'filters' => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
        ]);

        $this->add([
            'name' => 'last_name_keyword',
            'filters' => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
        ]);

        $this->add([
            'name' => 'email_keyword',
            'filters' => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
        ]);

        $this->add([
            'name' => 'role',
            'validators' => [
                [
                    'name' => 'InArray',
                    'options' => [
                        'haystack' => ['', 'admin', 'member'],
                    ],
                ],
            ],
        ]);

    }
}
