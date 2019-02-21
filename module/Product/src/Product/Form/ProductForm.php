<?php

namespace Product\Form;

use Zend\Db\Adapter\Adapter;
use Zend\Form\Form;
use Zend\Stdlib\Hydrator\ClassMethods;

use Product\Form\ProductFilter;

class ProductForm extends Form
{
    public function __construct(Adapter $dbAdapter, $brandMapper, $categoryMapper)
    {
      parent::__construct('brand-add');
      $this->setInputFilter(new ProductFilter($dbAdapter));
      $this->setAttribute('method', 'post');
      $this->setAttribute('enctype', 'multipart/form-data');
      $this->setHydrator(new ClassMethods());

      $this->add([
        'name' => 'name',
        'type' => 'text',
        'options' => [
            'label' => 'Name',
        ],
        'attributes' => [
          'class' => 'form-control',
          'id' => 'name',
          'required' => 'required',
          'data-validation-required-message' => 'Please enter your name.',
          'autofocus' => 'autofocus',
          'placeholder' => 'Name',
        ],
      ]);

      $this->add([
        'name' => 'description',
        'type' => 'textarea',
        'options' => [
          'label' => 'Description',
        ],
        'attributes' => [
          'class' => 'form-control',
          'id' => 'description',
          'required' => 'required',
          'data-validation-required-message' => 'Please enter your description.',
          'placeholder' => 'Description',
        
        ],
      ]);

      $this->add([
        'name' => 'stock',
        'type' => 'text',
        'options' => [
            'label' => 'Stock',
        ],
        'attributes' => [
          'class' => 'form-control',
          'id' => 'stock',
          'required' => 'required',
          'data-validation-required-message' => 'Please enter your stock.',
          'placeholder' => 'Stock',
        ],
      ]);

      $this->add([
        'name' => 'price',
        'type' => 'text',
        'options' => [
            'label' => 'Price',
        ],
        'attributes' => [
          'class' => 'form-control',
          'id' => 'price',
          'required' => 'required',
          'data-validation-required-message' => 'Please enter your price.',
          'placeholder' => 'Price',
        ],
      ]);

      $this->add([
        'name' => 'discount_type',
        'type' => 'Select',
        'options' => [
          'label' => 'Discount Type',
        ],
        'attributes' => [
	        'class' => 'form-control',
	        'options' => array(
            '' => '',
            'amount' => 'Less amount',
            'percentage' => 'Less percentage',
          ),
          'id' => 'discount_type',
		    ],
      ]);

      $this->add([
        'name' => 'discount',
        'type' => 'text',
        'options' => [
            'label' => 'Discount',
        ],
        'attributes' => [
          'class' => 'form-control',
          'id' => 'discount',
          'placeholder' => 'Discount',
        ],
      ]);

      $this->add([
        'name' => 'category_id',
        'type' => 'Select',
        'options' => [
          'label' => 'Category',
        ],
        'attributes' => [
          'multiple' => 'multiple',
	        'class' => 'form-control',
	        'options' => $this->_getCategories($categoryMapper),
          'id' => 'category_id',
          'required' => 'required',
          'data-validation-required-message' => 'Please enter your category.',
          'size' => 5,
		    ],
      ]);

      $this->add([
        'name' => 'brand_id',
        'type' => 'Select',
        'options' => [
          'label' => 'Brand',
        ],
        'attributes' => [
	        'class' => 'form-control',
	        'options' => $this->_getBrands($brandMapper),
          'id' => 'brand_id',
          'required' => 'required',
          'data-validation-required-message' => 'Please enter your brand.',
		    ],
      ]);

      $this->add(array(
		    'name' => 'photo1',
		    'attributes' => array(
	        'type'  => 'file',
          'id' => 'photo1',
		    ),
		    'options' => array(
	        'label' => 'Photo 1',
		    ),
  		));

      $this->add(array(
		    'name' => 'photo2',
		    'attributes' => array(
	        'type'  => 'file',
          'id' => 'photo2',
		    ),
		    'options' => array(
	        'label' => 'Photo 2',
		    ),
  		));

      $this->add(array(
		    'name' => 'photo3',
		    'attributes' => array(
	        'type'  => 'file',
          'id' => 'photo3',
		    ),
		    'options' => array(
	        'label' => 'Photo 3',
		    ),
  		));

      $this->add(array(
		    'name' => 'panorama',
		    'attributes' => array(
	        'type'  => 'file',
          'id' => 'panorama',
		    ),
		    'options' => array(
	        'label' => 'Panorama',
		    ),
  		));

      $this->add([
        'name' => 'submit',
        'type' => 'submit',
        'attributes' => [
          'value' => 'Submit',
          'class' => 'btn btn-primary',
        ],
      ]);
    }

    private function _getCategories($categoryMapper){
      $temp = array();

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
	       '' => 'Select Brand',
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
