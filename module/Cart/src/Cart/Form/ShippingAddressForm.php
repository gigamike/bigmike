<?php

namespace Cart\Form;

use Zend\Db\Adapter\Adapter;
use Zend\Form\Form;
use Zend\Stdlib\Hydrator\ClassMethods;

use Cart\Form\ShippingAddressFilter;

class ShippingAddressForm extends Form
{
    public function __construct(Adapter $dbAdapter, $countryMapper)
    {
        parent::__construct('shipping-address');
        $this->setInputFilter(new ShippingAddressFilter($dbAdapter));
        $this->setAttribute('method', 'post');
        $this->setHydrator(new ClassMethods());

        $this->add([
            'name' => 'country_id',
            'type' => 'Select',
            'options' => [
                'label' => 'Country',
            ],
            'attributes' => [
                'class' => 'form-control',
                'id' => 'country_id',
                'required' => 'required',
                'placeholder' => 'Country',
                'options' => $this->_getCountries($countryMapper),
            ],
        ]);

        $this->add([
            'name' => 'first_name',
            'type' => 'text',
            'options' => [
                'label' => 'First Name',
            ],
            'attributes' => [
                'class' => 'form-control',
                'id' => 'first_name',
                'required' => 'required',
                'placeholder' => 'First Name',
            ],
        ]);

        $this->add([
            'name' => 'last_name',
            'type' => 'text',
            'options' => [
                'label' => 'Last Name',
            ],
            'attributes' => [
                'class' => 'form-control',
                'id' => 'last_name',
                'required' => 'required',
                'placeholder' => 'Last Name',
            ],
        ]);

        $this->add([
            'name' => 'address',
            'type' => 'text',
            'options' => [
                'label' => 'Address',
            ],
            'attributes' => [
                'class' => 'form-control',
                'id' => 'address',
                'required' => 'required',
                'placeholder' => 'Address',
            ],
        ]);

        $this->add([
            'name' => 'city',
            'type' => 'text',
            'options' => [
                'label' => 'City',
            ],
            'attributes' => [
                'class' => 'form-control',
                'id' => 'city',
                'required' => 'required',
                'placeholder' => 'City',
            ],
        ]);

        $this->add([
            'name' => 'zip',
            'type' => 'text',
            'options' => [
                'label' => 'Zip Code',
            ],
            'attributes' => [
                'class' => 'form-control',
                'id' => 'zip',
                'required' => 'required',
                'placeholder' => 'Zip Code',
            ],
        ]);

        $this->add([
            'name' => 'company_name',
            'type' => 'text',
            'options' => [
                'label' => 'Company Name',
            ],
            'attributes' => [
                'class' => 'form-control',
                'id' => 'company_name',
                'required' => 'required',
                'autofocus' => 'autofocus',
                'placeholder' => 'Company Name',
            ],
        ]);

        $this->add([
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => [
                'value' => 'Continue',
                'class' => 'btn btn-primary btn-block',
            ],
        ]);
    }

    private function _getCountries($countryMapper){
      $temp = array(
	        '' => 'Select Country',
	    );

	    $filter = array();
      $order = array('country_name');
	    $countries = $countryMapper->fetch(false, $filter, $order);
	    foreach ($countries as $row){
	        $temp[$row->getId()] = $row->getCountryName();
	    }

	    return $temp;
    }
}
