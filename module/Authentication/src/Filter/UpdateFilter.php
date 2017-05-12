<?php

namespace Authentication\Filter;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\FileInput;

class UpdateFilter extends InputFilter
{
    public function __construct()
    {
        $this->add([
            'name'       => 'firstName',
            'required'   => false,
            'filters'    => [
                ['name' => 'stripTags'],
                ['name' => 'stringTrim'],
            ],
            'validators' => [
                [
                    'name'    => 'stringLength',
                    'options' => [
                        'encoding' => 'utf-8',
                        'min' => 2,
                        'max' => 50,
                    ],
                ],
            ],
        ]);

        $this->add([
            'name'       => 'lastName',
            'required'   => false,
            'filters'    => [
                ['name' => 'stripTags'],
                ['name' => 'stringTrim'],
            ],
            'validators' => [
                [
                    'name'    => 'stringLength',
                    'options' => [
                        'encoding' => 'utf-8',
                        'min' => 2,
                        'max' => 50,
                    ],
                ],
            ],
        ]);

        $this->add([
            'name' => 'password',
            'required' => false,
            'allowEmpty' => true,
            'filters' => [
                ['name' => 'stripTags'],
                ['name' => 'stringTrim'],
            ],
            'validators' => [
                [
                    'name' => 'stringLength',
                    'options' => [
                        'encoding' => 'utf-8',
                        'min' => 2,
                        'max' => 100,
                    ],
                ]
            ],
        ]);

        $this->add([
            'name'       => 'location',
            'required'   => false,
            'filters'    => [
                ['name' => 'stripTags'],
                ['name' => 'stringTrim'],
            ],
            'validators' => [
                [
                    'name'    => 'stringLength',
                    'options' => [
                        'encoding' => 'utf-8',
                        'min' => 2,
                        'max' => 50,
                    ],
                ],
            ],
        ]);

        $this->add([
            'name'       => 'role',
            'filters'    => [
                ['name' => 'stripTags'],
                ['name' => 'stringTrim'],
            ],
            'validators' => [
                [
                    'name'    => 'stringLength',
                    'options' => [
                        'encoding' => 'utf-8',
                        'min' => 4,
                        'max' => 5,
                    ],
                ],
            ],
        ]);

        $this->add([
            'name'       => 'active',
            'validators' => [
                [
                    'name'    => 'InArray',
                    'options' => [
                        'haystack' => array(0, 1),
                        'messages' => array(
                            \Zend\Validator\InArray::NOT_IN_ARRAY => 'Please select right value!'
                        ),
                    ],
                ],
            ],
        ]);

        $this->add([
            'type'     => FileInput::class,
            'name'     => 'file',
            'required' => false,
            'allowEmpty' => true,
            'validators' => [
                ['name'    => 'FileUploadFile'],
                ['name'    => 'FileIsImage'],
                [
                    'name' => 'Zend\Validator\File\Extension',
                    'options' => [
                        'extension' => ['png', 'jpg', 'jpeg', 'gif'],
                    ],
                ],
            ],
            /* Use this filter in controller (see Profile controller edit action) */
            /*'filters'  => [
                [
                    'name' => 'FileRenameUpload',
                    'options' => [
                        'target'            => './public_html/img/user/',
                        'useUploadName'     => true,
                        'useUploadExtension'=> true,
                        'overwrite'         => true,
                        'randomize'         => false
                    ]
                ]
            ],*/
        ]);
    }
}
