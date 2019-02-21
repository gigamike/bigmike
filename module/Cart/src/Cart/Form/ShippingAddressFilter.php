<?php

namespace Cart\Form;

use Zend\Db\Adapter\Adapter;
use Zend\InputFilter\InputFilter;

class ShippingAddressFilter extends InputFilter
{
    public function __construct(Adapter $dbAdapter)
    {
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
