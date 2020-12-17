<?php


namespace App\Controllers\Admin\API;


use App\App;
use App\Controllers\Base\API\AdminController;
use App\Views\Forms\Admin\Order\OrderUpdateForm;
use Core\Api\Response;

class OrdersApiController extends AdminController
{
    public function index(): string
    {
        $response = new Response();
        $orders = App::$db->getRowsWhere('orders');

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
                'timestamp' => $this->timeFormat($row),
                'buttons' => [
                    'edit' => 'Edit'
                ]
            ];
        }

        return $orders;
    }

    public function edit(): string
    {
        // This is a helper class to make sure
        // we use the same API json response structure
        $response = new Response();

        $id = $_POST['id'] ?? null;

        if ($id === null) {
            $response->appendError('ApiController could not update, since ID is not provided! Check JS!');
        } else {
            $order = App::$db->getRowById('orders', $id);
            $order['id'] = $id;

            // Setting "what" to json-encode
            $response->setData($order);
        }

        // Returns json-encoded response
        return $response->toJson();
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
        $pizza = App::$db->getRowById('pizzas', $row['pizza_id']);

        return $row = [
            'id' => $id,
            'status' => $row['status'],
            'name' => $pizza['name'],
            'timestamp' => $this->timeFormat($row),
            'buttons' => [
                'edit' => 'Edit'
            ]
        ];
    }

    /**
     * Updates order data
     * and returns array from which JS generates table row
     *
     * @return string
     */
    public function update(): string
    {
        // This is a helper class to make sure
        // we use the same API json response structure
        $response = new Response();

        $id = $_POST['id'] ?? null;

        if ($id === null || $id == 'undefined') {
            $response->appendError('ApiController could not update, since ID is not provided! Check JS!');
        } else {

            $form = new OrderUpdateForm();
            $order = App::$db->getRowById('orders', $id);

            if ($form->validate()) {
                $order['status'] = $form->value('status');

                App::$db->updateRow('orders', $id, $order);

                $row = $this->buildRow($order, $id);

                $response->setData($row);
            } else {
                $response->setErrors($form->getErrors());
            }
        }

        // Returns json-encoded response
        return $response->toJson();
    }

}