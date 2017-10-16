<?php

namespace Authentication\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class UpdateForm extends Form
{
    public function __construct()
    {
        parent::__construct('update-form');

        $this->setAttributes([
            'class' => 'form-horizontal',
        ]);

        $this->createElements();
    }

    protected function createElements()
    {
        $csrf = new Element\Csrf('csrf');
        $csrf->setOptions([
            'crsf_options' => [
                'timeout' => 600,
            ],
        ]);
        $this->add($csrf);

        $username = new Element\Text('username');
        $username->setLabel('Username:');
        $username->setLabelAttributes([
            'class' => 'label-control',
        ]);
        $username->setAttributes([
            'class'    => 'form-control',
            'required' => 'required',
            'id'       => 'username',
        ]);
        $username->setOptions([
            'min' => 2,
            'max' => 50,
        ]);
        $this->add($username);

        $firstName = new Element\Text('firstName');
        $firstName->setLabel('First name:');
        $firstName->setLabelAttributes([
            'class' => 'label-control',
        ]);
        $firstName->setAttributes([
            'class' => 'form-control',
            'id'    => 'firstName',
        ]);
        $firstName->setOptions([
            'min' => 2,
            'max' => 50,
        ]);
        $this->add($firstName);

        $lastName = new Element\Text('lastName');
        $lastName->setLabel('Last name:');
        $lastName->setLabelAttributes([
            'class' => 'label-control',
        ]);
        $lastName->setAttributes([
            'class' => 'form-control',
            'id'    => 'lastName',
        ]);
        $lastName->setOptions([
            'min' => 2,
            'max' => 50,
        ]);
        $this->add($lastName);

        $password = new Element\Password('password');
        $password->setLabel('Change Password:');
        $password->setLabelAttributes([
            'class' => 'control-label',
        ]);
        $password->setAttributes([
            'class' => 'form-control',
            'id'    => 'password',
        ]);
        $password->setOptions([
            'min' => 2,
            'max' => 50,
        ]);
        $this->add($password);

        $location = new Element\Text('location');
        $location->setLabel('Location:');
        $location->setLabelAttributes([
            'class' => 'control-label',
        ]);
        $location->setAttributes([
            'class' => 'form-control',
            'id'    => 'location',
        ]);
        $location->setOptions([
            'min' => 2,
            'max' => 50,
        ]);
        $this->add($location);

        $role = new Element\Select('role');
        $role->setLabel('Role:');
        $role->setLabelAttributes([
            'class' => 'control-label',
        ]);
        $role->setAttributes([
            'class' => 'form-control',
            'id'    => 'role',
        ]);
        $role->setOptions([
            'min' => 4,
            'max' => 5,
            'value_options' => [
                'user'  => 'user',
                'admin' => 'admin',
            ],
        ]);
        $this->add($role);

        $file = new Element\File('file');
        $file->setLabel('Change profile image:');
        $role->setLabelAttributes([
            'class' => 'control-label',
        ]);
        $file->setAttributes([
            'class' => 'form-control jfilestyle',
            'id'    => 'file',
        ]);
        $this->add($file);

        $active = new Element\Radio('active');
        $active->setLabel('Active:');
        $active->setAttributes([
            'class' => 'radio-block',
            'id'    => 'active',
        ]);
        $active->setValueOptions(array(
            '0' => ' No',
            '1' => ' Yes',
        ));
        $this->add($active);

        $submit = new Element\Submit('submit');
        $submit->setAttributes([
            'class' => 'btn btn-default',
            'value' => 'Edit',
            'id'    => 'submit',
        ]);
        $this->add($submit);
    }
}
