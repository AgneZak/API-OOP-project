<?php


namespace App\Views\Forms\Admin\Order;


class OrderCreateForm extends OrderBaseForm
{
    public function __construct() {
        parent::__construct();

        $this->data['buttons']['order'] = [
            'title' => 'Order',
        ];
    }
}