<?php


namespace App\Controllers\User\API;


use App\App;
use App\Controllers\Base\API\UserController;
use Core\Api\Response;

class OrdersApiController extends UserController
{
    public function index(): string
    {
        $response = new Response();

        $user = App::$session->getUser();
        $orders = App::$db->getRowsWhere('orders', ['email' => $user['email']]);

        foreach ($orders as $id => &$row) {
            $pizza = App::$db->getRowById('pizzas', $row['pizza_id']);

            $timeStamp = date('Y-m-d H:i:s', $row['timestamp']);
            $difference = abs(strtotime("now") - strtotime($timeStamp));
            $days = floor($difference / (3600 * 24));
            $hours = floor($difference / 3600);
            $minutes = floor(($difference - ($hours * 3600)) / 60);
            $result = "{$days}d {$hours}:{$minutes} H";

            $row = [
                'id' => $id,
                'status' => $row['status'],
                'name' => $pizza['name'],
                'timestamp' => $result
            ];
        }

        // Setting "what" to json-encode
        $response->setData($orders);

        // Returns json-encoded response

        return $response->toJson();
    }

    public function create(): string
    {

        // This is a helper class to make sure
        // we use the same API json response structure
        $response = new Response();

        $id = $_POST['id'] ?? null;
        $user = App::$session->getUser();

        if ($id === null || $id == 'undefined') {
            $response->appendError('ApiController could not create, since ID is not provided! Check JS!');
        } else {
            $response->setData([
                'id' => $id
            ]);
            App::$db->insertRow('orders', [
                'pizza_id' => $id,
                'status' => 'active',
                'timestamp' => time(),
                'email' => $user['email']
            ]);
        }

        // Returns json-encoded response
        return $response->toJson();
    }
}