<?php

namespace Supplier\Form;

use Zend\Db\Adapter\Adapter;
use Zend\InputFilter\InputFilter;

class SupplierRegistrationFilter extends InputFilter
{
    public function __construct(Adapter $dbAdapter)
    {
      $this->add([
          'name' => 'company_name',
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
          ],
      ]);

      $this->add([
          'name' => 'country_id',
          'required' => true,
          'validators' => [
              [
                'name' => 'Zend\Validator\Db\RecordExists',
                'options' => [
                  'adapter' => $dbAdapter,
                  'table' => 'country',
                  'field' => 'id',
                ],
              ],
          ],
      ]);

        $this->add([
            'name' => 'first_name',
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
            ],
        ]);

        $this->add([
            'name' => 'last_name',
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
            ],
        ]);

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
                  'name' => 'Zend\Validator\Db\NoRecordExists',
                  'options' => [
                    'adapter' => $dbAdapter,
                    'table' => 'user',
                    'field' => 'email',
                  ],
                ],
            ],
        ]);

        $this->add([
            'name' => 'password',
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
                        'min' => 5,
                        'max' => 255,
                    ],
                ],
                [
                  'name'    => 'Identical',
                  'options' => [
                      'token' => 'confirm_password',
                  ],
                ],
            ],
        ]);

        $this->add([
            'name' => 'confirm_password',
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
                        'min' => 5,
                        'max' => 255,
                    ],
                ],
            ],
        ]);

        $this->add([
            'name' => 'mobile_no',
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
                        'min' => 5,
                        'max' => 255,
                    ],
                ],
            ],
        ]);

        $this->add([
            'name' => 'telephone',
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
                        'min' => 5,
                        'max' => 255,
                    ],
                ],
            ],
        ]);

        $this->add([
            'name' => 'address',
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
            ],
        ]);

        $this->add([
            'name' => 'city',
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
            ],
        ]);

        $this->add([
            'name' => 'zip',
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
            ],
        ]);
    }
}
