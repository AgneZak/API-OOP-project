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

    /**
     * Formats rows from given @param (in this case - users data)
     * Intended use is for setting data in json.
     *
     * @param $users
     * @return mixed
     */
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

    /**
     * Formats row for json to be used in update method,
     * so that the data would be updated in the same format.
     *
     * @param $row
     * @param $id
     * @return array
     */
    private function buildRow($row, $id): array
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