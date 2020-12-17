<?php


namespace App\Views\Forms\Admin\Order;


use Core\Views\Form;

class OrderBaseForm extends Form
{
    public function __construct()
    {
        parent::__construct([
            'fields' => [
                'status' => [
                    'type' => 'select',
                    'options' => [
                        'active' => 'Active',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled'
                    ],
                    'validators' => [
                        'validate_select',
                    ]
                ]
            ]
        ]);
    }
}