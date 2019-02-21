<?php

namespace Contact\Form;

use Zend\Form\Form;

class ContactForm extends Form
{
	public function __construct($name = null)
	{
		parent::__construct($name);

		$this->add(array(
	    'name' => 'name',
	    'type' => 'Text',
	    'attributes' => array(
        'class' => 'form-control',
        'placeholder'  => 'Full Name',
        'id' => 'name',
        'required' => 'required',
	    ),
	    'options' => array(
        'label' => 'Full Name:',
	    ),
		));

		$this->add(array(
	    'name' => 'phone',
	    'type' => 'Text',
	    'attributes' => array(
        'class' => 'form-control',
        'placeholder'  => 'Phone',
        'id' => 'phone',
	    ),
	    'options' => array(
        'label' => 'Phone Number:',
	    ),
		));

		$this->add(array(
			'name' => 'email',
			'type' => 'Email',
			'attributes' => array(
				'class' => 'form-control',
				'placeholder'  => 'Email Address',
				'id' => 'email',
				'required' => 'required',
			),
			'options' => array(
					'label' => 'Email Address:',
			),
		));

		$this->add(array(
			'name' => 'message',
			'type' => 'Textarea',
			'attributes' => array(
				'class' => 'form-control',
				'id' => 'message',
				'rows' => '10',
				'cols' => '100',
				'required' => 'required',
				'maxlength' => '999',
				'style' => 'resize:none',
			),
			'options' => array(
				'label' => 'Message:',
			),
		));

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

		$this->add(array(
			'name' => 'submit',
			'type' => 'Submit',
			'attributes' => array(
				'class' => 'btn btn-primary',
				'value' => 'Submit',
			),
		));
	}
}
