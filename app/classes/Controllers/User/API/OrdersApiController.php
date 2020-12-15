<?php


namespace App\Controllers\User\API;


use App\App;
use App\Controllers\Base\API\UserController;
use App\Views\BasePage;
use App\Views\Forms\Admin\Order\OrderCreateForm;
use Core\Api\Response;

class OrdersApiController extends UserController
{
    protected BasePage $page;

    public function __construct()
    {
        parent::__construct();
        $this->page = new BasePage([
            'title' => 'Orders',
        ]);
    }

    public function index(): string
    {
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