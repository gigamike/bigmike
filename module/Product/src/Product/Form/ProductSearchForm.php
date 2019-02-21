<?php

namespace Product\Form;

use Zend\Db\Adapter\Adapter;
use Zend\Form\Form;

use Product\Form\ProductSearchFilter;

class ProductSearchForm extends Form
{
    public function __construct(Adapter $dbAdapter, $brandMapper, $categoryMapper)
    {
        parent::__construct('product-search');
        $this->setInputFilter(new ProductSearchFilter($dbAdapter));
        $this->setAttribute('method', 'post');

        $this->add([
          'name' => 'category_id',
          'type' => 'Select',
          'attributes' => [
		        'class' => 'form-control',
		        'options' => $this->_getCategories($categoryMapper),
  		    ],
        ]);

        $this->add([
          'name' => 'brand_id',
          'type' => 'Select',
          'attributes' => [
		        'class' => 'form-control',
		        'options' => $this->_getBrands($brandMapper),
  		    ],
        ]);

        $this->add([
          'name' => 'keyword',
          'type' => 'text',
          'attributes' => [
            'class' => 'form-control',
            'placeholder' => 'Keyword/Name',
          ],
        ]);

        $this->add([
          'name' => 'submit',
          'type' => 'submit',
          'attributes' => [
            'value' => 'Search',
            'class' => 'btn btn-primary',
          ],
        ]);
    }

    private function _getCategories($categoryMapper){
      $temp = array(
	        '' => 'All Categories',
	    );

	    $filter = array();
      $order = array('category');
	    $categories = $categoryMapper->fetch(false, $filter, $order);
	    foreach ($categories as $row){
	       $temp[$row->getId()] = $row->getCategory();
	    }

	    return $temp;
    }

    private function _getBrands($brandMapper){
      $temp = array(
	       '' => 'All Brands',
	    );

	    $filter = array();
      $order = array('brand');
	    $brands = $brandMapper->fetch(false, $filter, $order);
	    foreach ($brands as $row){
	       $temp[$row->getId()] = $row->getBrand();
	    }

	    return $temp;
    }
}
