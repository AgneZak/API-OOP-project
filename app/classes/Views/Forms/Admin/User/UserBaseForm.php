<?php

namespace App\Views\Forms\Admin\User;


use Core\Views\Form;

class UserBaseForm extends Form
{
    public function __construct()
    {
        parent::__construct([
            'fields' => [
                'role' => [
                    'type' => 'select',
                    'options' => [
                        'admin' => 'Admin',
                        'user' => 'User'
                    ],
                    'validators' => [
                        'validate_select',
                    ]
                ]
            ]
        ]);
    }

}