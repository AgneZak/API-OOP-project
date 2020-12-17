<?php


namespace App\Views\Forms\Admin\User;


class UserUpdateForm extends UserBaseForm
{
    public function __construct() {
        parent::__construct();

        $this->data['attr']['id'] = 'user-update-form';
        $this->data['buttons']['update'] = [
            'title' => 'Update',
        ];
    }
}