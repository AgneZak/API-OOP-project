<?php

namespace App\Controllers\Admin;

use App\App;
use App\Controllers\Base\AdminController;
use App\Views\BasePage;
use App\Views\Forms\Admin\User\UserUpdateForm;
use Core\View;

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

        $table = new View([
            'headers' => [
                'ID',
                'Email',
                'Role',
                'Actions'
            ],
            'forms' => $forms ?? []
        ]);
        $this->page->setContent($table->render(ROOT . '/app/templates/content/table.tpl.php'));
        return $this->page->render();
    }
}