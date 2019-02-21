<?php

namespace Contact\Form;

use Zend\InputFilter\InputFilter;
use Zend\Validator\Hostname as HostnameValidator;

class ContactFilter extends InputFilter
{
	public function __construct()
	{
    $this->add(array(
      'name' => 'name',
      'required' => true,
      'filters' => array(
        array(
          'name' => 'StripTags',
        ),
      ),
      'validators' => array(
        array(
          'name' => 'StringLength',
          'options' => array(
            'encoding' => 'UTF-8',
            'min' => 2,
            'max' => 140,
          ),
        ),
      ),
    ));

    $this->add(array(
        'name' => 'phone',
        'required' => false,
        'filters' => array(
          array(
            'name' => 'StripTags',
          ),
        ),
        'validators' => array(
          array(
            'name' => 'StringLength',
            'options' => array(
              'encoding' => 'UTF-8',
              'min' => 2,
              'max' => 140,
            ),
          ),
        ),
    ));

		$this->add(array(
			'name' => 'email',
			'required' => true,
			'validators' => array(
				array(
					'name' => 'EmailAddress',
					'options' => array(
						'allow' => HostnameValidator::ALLOW_DNS,
						'domain' => true,
					),
				),
			),
		));

		$this->add(array(
			'name' => 'message',
			'required' => true,
		));
	}
}
