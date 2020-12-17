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

        $rows = $this->buildRows($orders);

        // Setting "what" to json-encode
        $response->setData($rows);

        // Returns json-encoded response

        return $response->toJson();
    }

    /**
     * Returns formatted time from timestamp given in row.
     *
     * @param $row
     * @return string
     */
    private function timeFormat($row)
    {
        $timeStamp = date('Y-m-d H:i:s', $row['timestamp']);
        $difference = abs(strtotime("now") - strtotime($timeStamp));
        $days = floor($difference / (3600 * 24));
        $hours = floor($difference / 3600);
        $minutes = floor(($difference - ($hours * 3600)) / 60);
        $seconds = floor($difference % 60);

        if ($days) {
            $hours = $hours - 24;
            $result = "{$days}d {$hours}:{$minutes} H";
        } elseif ($minutes) {
            $result = "{$minutes} min";
        } elseif ($hours) {
            $result = "{$hours}:{$minutes} H";
        } else {
            $result = "{$seconds} seconds";
        }

        return $result;
    }

    /**
     * Formats rows from given @param (in this case - orders data)
     * Intended use is for setting data in json.
     *
     * @param $orders
     * @return mixed
     */
    private function buildRows($orders)
    {
        foreach ($orders as $id => &$row) {
            $pizza = App::$db->getRowById('pizzas', $row['pizza_id']);

            $row = [
                'id' => $id,
                'status' => $row['status'],
                'name' => $pizza['name'],
                'timestamp' => $this->timeFormat($row)
            ];
        }

        return $orders;
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