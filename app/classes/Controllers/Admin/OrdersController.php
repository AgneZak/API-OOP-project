<?php

namespace App\Controllers\Admin;

use App\Controllers\Base\AdminController;
use App\Views\BasePage;
use App\Views\Forms\Admin\Order\OrderUpdateForm;
use App\Views\Tables\Admin\OrderTable;

/**
 * Class AdminOrders
 *
 * @package App\Controllers\Admin
 * @author  Dainius Vaičiulis   <denncath@gmail.com>
 */
class OrdersController extends AdminController
{
    protected BasePage $page;

    public function __construct()
    {
        parent::__construct();
        $this->page = new BasePage([
            'title' => 'Orders',
            'js' => ['/media/js/admin/orders.js']

        ]);
    }

    public function index()
    {
        $forms = [
            'update' => (new OrderUpdateForm())->render()
        ];

        $table = new OrderTable($forms);
        $this->page->setContent($table->render());
        return $this->page->render();
    }
}