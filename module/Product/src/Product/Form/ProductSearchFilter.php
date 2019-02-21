<?php

namespace Product\Form;

use Zend\Db\Adapter\Adapter;
use Zend\InputFilter\InputFilter;

class ProductSearchFilter extends InputFilter
{
    public function __construct(Adapter $dbAdapter)
    {
      $this->add([
        'name' => 'category_id',
        'required' => false,
      ]);

      $this->add([
        'name' => 'brand_id',
        'required' => false,
      ]);

      $this->add([
        'name' => 'keyword',
        'required' => false,
      ]);
    }
}
