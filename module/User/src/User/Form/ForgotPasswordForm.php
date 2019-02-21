<?php

namespace User\Form;

use Zend\Db\Adapter\Adapter;
use Zend\Form\Form;

use User\Form\ForgotPasswordFilter;

class ForgotPasswordForm extends Form
{
    public function __construct(Adapter $dbAdapter)
    {
        parent::__construct('forgot-password');
        $this->setInputFilter(new ForgotPasswordFilter($dbAdapter));
        $this->setAttribute('method', 'post');

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
                'autofocus' => 'autofocus',
            ],
        ]);

        $this->add([
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => [
                'value' => 'Submit',
                'class' => 'btn btn-primary btn-block',
                'id' => 'forgotPasswordButton',
            ],
        ]);
    }
}
