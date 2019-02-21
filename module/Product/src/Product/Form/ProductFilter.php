<?php

namespace Product\Form;

use Zend\Db\Adapter\Adapter;
use Zend\InputFilter\InputFilter;

class ProductFilter extends InputFilter
{
  public function __construct(Adapter $dbAdapter)
  {
    $this->add([
      'name' => 'name',
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
      'name' => 'description',
      'required' => true,
      'filters' => [
        ['name' => 'StripTags'],
        ['name' => 'StringTrim'],
      ],
    ]);

    $this->add([
      'name' => 'stock',
      'required' => true,
      'validators' => [
        [
          'name' => 'Callback',
          'options' => [
            'callback' => function($value) {
              if(!is_numeric($value)){
                return false;
              }
              return true;
            }
          ],
        ],
      ],
    ]);

    $this->add([
      'name' => 'price',
      'required' => true,
      'validators' => [
        [
          'name' => 'Callback',
          'options' => [
            'callback' => function($value) {
              if(!is_numeric($value)){
                return false;
              }
              return true;
            }
          ],
        ],
      ],
    ]);

    $this->add([
      'name' => 'discount_type',
      'required' => false,
    ]);

    $this->add([
      'name' => 'discount',
      'required' => false,
      'validators' => [
        [
          'name' => 'Callback',
          'options' => [
            'callback' => function($value) {
              if(!is_numeric($value)){
                return false;
              }
              return true;
            }
          ],
        ],
      ],
    ]);

    $this->add([
      'name' => 'brand_id',
      'required' => true,
    ]);

    $this->add([
      'name' => 'category_id',
      'required' => true,
    ]);

    $this->add(array(
	    'name' => 'photo1',
	    'required' => false,
		));

    $this->add(array(
	    'name' => 'photo2',
	    'required' => false,
		));

    $this->add(array(
	    'name' => 'photo3',
	    'required' => false,
		));

    $this->add(array(
	    'name' => 'panorama',
	    'required' => false,
		));
  }
}
