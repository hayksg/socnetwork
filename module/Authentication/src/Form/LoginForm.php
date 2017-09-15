<?php

namespace Authentication\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class LoginForm extends Form
{
    public function __construct()
    {
        parent::__construct('login-form');

        $this->setAttributes([
            'class' => 'form-horizontal',
        ]);

        $this->createElements();
    }

    private function createElements()
    {
        $csrf = new Element\Csrf('csrf');
        $csrf->setOptions([
            'crsf_options' => [
                'timeout' => 600,
            ],
        ]);
        $this->add($csrf);

        $username = new Element\Text('username');
        $username->setLabel('Username');
        $username->setAttributes([
            'class'    => 'form-control',
            'id'       => 'username',
            'required' => 'required',
        ]);
        $username->setOptions([
            'min' => 2,
            'max' => 100,
        ]);
        $this->add($username);

        $password = new Element\Password('password');
        $password->setLabel('Password');
        $password->setAttributes([
            'class'    => 'form-control',
            'id'       => 'password',
            'required' => 'required',
        ]);
        $password->setOptions([
            'min' => 2,
            'max' => 100,
        ]);
        $this->add($password);

        $rememberMe = new Element\Checkbox('rememberMe');
        $rememberMe->setLabel('Remember Me');
        $rememberMe->setAttributes([
            'id'       => 'rememberMe',
        ]);
        $this->add($rememberMe);

        $submit = new Element\Submit('submit');
        $submit->setAttributes([
            'class' => 'btn btn-default',
            'value' => 'Submit',
        ]);
        $this->add($submit);
    }
}
