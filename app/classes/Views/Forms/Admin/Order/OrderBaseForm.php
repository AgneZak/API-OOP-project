<?php


namespace App\Views\Forms\Admin\Order;


use Core\Views\Form;

class OrderBaseForm extends Form
{
    public function __construct($value = null)
    {
        parent::__construct([
            'attr' => [
                'method' => 'POST'
            ],
            'fields' => [
                'id' => [
                    'type' => 'hidden',
                    'value' => 'ORDER'
                ],
                'name' => [
                    'type' => 'hidden',
                    'value' => $value
                ],
            ]
        ]);
    }
}


