<?php


namespace App\Views\Forms\Admin\Order;


class OrderUpdateForm extends OrderBaseForm
{
    public function __construct() {
        parent::__construct();

        $this->data['attr']['id'] = 'order-update-form';
        $this->data['buttons']['update'] = [
            'title' => 'Update',
        ];
    }
}