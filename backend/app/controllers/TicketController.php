<?php

namespace app\Controllers;

use app\classes\Helper;
use app\Classes\JwtHelper;
use \Exception;
use Klein\Request;
use Klein\Response;
use app\models\TicketModel;

class TicketController extends TicketModel
{

    private Helper $helper;
    private JwtHelper $jwt;

    public function __construct()
    {
        $this->helper = new Helper();
        $this->jwt = new JwtHelper();
    }


    public function getTicketsForRequest(Request $request, Response $response)
    {
        try {
            $this->jwt->validate();
            $param = $request->param('account');

            $this->helper->arrayValidate([$param], [0]);
            $param = $this->helper->sanitizeArray([$param])[0];
            $param = $this->helper->convertType([$param], ['int'])[0];
        } catch (Exception $e) {
            throw new Exception('Controler Error: ' . $e->getMessage());
        }
    }

    public function create(Request $request, Response $response)
    {
        try {
        } catch (Exception $e) {
            throw new Exception('Controler Error: ' . $e->getMessage());
        }
    }

    public function paid(Request $request, Response $response)
    {
        try {
        } catch (Exception $e) {
            throw new Exception('Controler Error: ' . $e->getMessage());
        }
    }

    public function ticketFinalized(Request $request, Response $response)
    {
        try {
        } catch (Exception $e) {
            throw new Exception('Controler Error: ' . $e->getMessage());
        }
    }
}
