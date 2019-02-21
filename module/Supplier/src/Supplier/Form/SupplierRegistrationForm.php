<?php

namespace Supplier\Form;

use Zend\Db\Adapter\Adapter;
use Zend\Form\Form;
use Zend\Stdlib\Hydrator\ClassMethods;

use Supplier\Form\SupplierRegistrationFilter;

class SupplierRegistrationForm extends Form
{
    public function __construct(Adapter $dbAdapter, $countryMapper)
    {
        parent::__construct('registration');
        $this->setInputFilter(new SupplierRegistrationFilter($dbAdapter));
        $this->setAttribute('method', 'post');
        $this->setHydrator(new ClassMethods());

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
            'name' => 'email',
            'type' => 'email',
            'options' => [
                'label' => 'Email Address',
            ],
            'attributes' => [
                'class' => 'form-control',
                'id' => 'email',
                'required' => 'required',
                'placeholder' => 'Email Address',
            ],
        ]);

        $this->add([
            'name' => 'password',
            'type' => 'password',
            'options' => [
                'label' => 'Password',
            ],
            'attributes' => [
                'class' => 'form-control',
                'id' => 'password',
                'required' => 'required',
            ],
        ]);

        $this->add([
            'name' => 'confirm_password',
            'type' => 'password',
            'options' => [
                'label' => 'Confirm password',
            ],
            'attributes' => [
                'class' => 'form-control',
                'id' => 'confirm_password',
                'required' => 'required',
            ],
        ]);

        $this->add([
            'name' => 'telephone',
            'type' => 'text',
            'options' => [
                'label' => 'Telephone',
            ],
            'attributes' => [
                'class' => 'form-control',
                'id' => 'telephone',
                'required' => 'required',
                'placeholder' => 'Telephone',
            ],
        ]);

        $this->add([
            'name' => 'mobile_no',
            'type' => 'text',
            'options' => [
                'label' => 'Mobile No.',
            ],
            'attributes' => [
                'class' => 'form-control',
                'id' => 'mobile_no',
                'required' => 'required',
                'placeholder' => 'Mobile No.',
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

        $this->add(array(
    			'name' => 'security',
    			'type' => 'Csrf',
    		));

    		$this->add(array(
    			'name' => 'captcha',
    			'type' => 'Captcha',
    			'attributes' => array(
    				'class' => 'form-control',
    				'id' => 'captcha',
    				'placeholder'  => 'Please verify you are human.',
    				'required' => 'required',
    			),
    			'options' => array(
    				'label' => 'Security Code *',
    				'captcha' => array(
    					'class' => 'Dumb',
    	        'wordLen' => 3,
    				),
    			),
    		));

        $this->add([
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => [
                'value' => 'Register',
                'class' => 'btn btn-primary btn-block',
                'id' => 'registerButton',
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
