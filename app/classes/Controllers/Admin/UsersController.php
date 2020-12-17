<?php

namespace App\Controllers\Admin;

use App\Controllers\Base\AdminController;
use App\Views\BasePage;
use App\Views\Forms\Admin\User\UserUpdateForm;
use App\Views\Tables\Admin\UsersTable;

/**
 * Class AdminUsers
 *
 * @package App\Controllers\Admin
 * @author  Dainius Vaičiulis   <denncath@gmail.com>
 */
class UsersController extends AdminController
{
    protected BasePage $page;

    public function __construct()
    {
        parent::__construct();
        $this->page = new BasePage([
            'title' => 'Users',
            'js' => ['/media/js/admin/users.js']
        ]);
    }

    public function index()
    {
        $forms = [
            'update' => (new UserUpdateForm())->render()
        ];

        $table = new UsersTable($forms);
        $this->page->setContent($table->render());
        return $this->page->render();
    }
}