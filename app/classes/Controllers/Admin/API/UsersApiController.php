<?php


namespace App\Controllers\Admin\API;


use App\App;
use App\Controllers\Base\API\AdminController;
use App\Views\Forms\Admin\User\UserUpdateForm;
use Core\Api\Response;

class UsersApiController extends AdminController
{
    public function index()
    {
        $response = new Response();
        $users = App::$db->getRowsWhere('users');
        $rows = $this->buildRows($users);

        // Setting "what" to json-encode
        $response->setData($rows);

        // Returns json-encoded response

        return $response->toJson();
    }

    private function buildRows($users)
    {
        foreach ($users as $id => &$row) {
            $row = [
                'id' => $id,
                'email' => $row['email'],
                'role' => $row['role']
            ];

            $user = App::$session->getUser();

            if ($user['email'] === $row['email'] && $user['role'] == 'admin') {
                $row['buttons']['-'] = '-';
            } else {
                $row['buttons']['edit'] = 'Edit';
            }
        }

        return $users;
    }

    private function buildRow($row, $id)
    {
        return $row = [
            'id' => $id,
            'email' => $row['email'],
            'role' => $row['role'],
            'buttons' => [
                'edit' => 'Edit'
            ]
        ];
    }

    public function edit()
    {
        // This is a helper class to make sure
        // we use the same API json response structure
        $response = new Response();

        $id = $_POST['id'] ?? null;

        if ($id === null) {
            $response->appendError('ApiController could not update, since ID is not provided! Check JS!');
        } else {
            $user = App::$db->getRowById('users', $id);
            $user['id'] = $id;

            // Setting "what" to json-encode
            $response->setData($user);
        }

        // Returns json-encoded response
        return $response->toJson();
    }

    public function update()
    {
        // This is a helper class to make sure
        // we use the same API json response structure
        $response = new Response();

        $id = $_POST['id'] ?? null;

        if ($id === null || $id == 'undefined') {
            $response->appendError('ApiController could not update, since ID is not provided! Check JS!');
        } else {
            $form = new UserUpdateForm();
            $user = App::$db->getRowById('users', $id);

            if ($form->validate()) {
                $user['role'] = $form->value('role');

                App::$db->updateRow('users', $id, $user);

                $row = $this->buildRow($user, $id);

                $response->setData($row);
            } else {
                $response->setErrors($form->getErrors());
            }
        }

        // Returns json-encoded response
        return $response->toJson();
    }
}