<?php


namespace App\Views\Tables\Admin;

use App\Views\Table;

class OrderTable extends Table
{
    public function __construct($forms)
    {
        parent::__construct([
            'headers' => [
                'ID',
                'Status',
                'Pizza name',
                'Time Ago'
            ],
            'forms' => $forms ?? []
        ]);
    }

}