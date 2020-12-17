<?php


namespace App\Views;


use Core\View;

class Table extends View
{
    public function render($template_path = ROOT . '/app/templates/content/table.tpl.php')
    {
        return parent::render($template_path);
    }
}