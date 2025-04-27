<?php

namespace App\Controllers;

use App\models\TicketModel;
use App\Shared\Helper;
use App\Shared\JWT;
use Exception;
use Klein\Request;
use Klein\Response;

class TicketController extends TicketModel
{
    private Helper $helper;

    private JWT $jwt;

    public function __construct()
    {
        $this->helper = new Helper;
        $this->jwt = new JWT;
    }

    public function getTicketsForRequest(Request $request, Response $response)
    {
        try {
            $this->jwt->validate();
            $param = $request->param('account');

            arrayValidate([$param], [0]);
            $param = sanitizeArray([$param])[0];
            $param = convertType([$param], ['int'])[0];

            $res = $this->getTickets($param);

            if (empty($res)) {
                return $response
                    ->code(400)
                    ->header('Content-Type', 'application/json')
                    ->body(\json_encode([
                        'message' => 'Não foi encontrado nenhum boleto!',
                    ]));
            }

            \is_array($res['message']) ? $res['message'] = \array_map([$this->helper, 'sanitizeArray'], $res['message']) : null;

            return $response
                ->code($res['status'])
                ->header('Content-Type', 'application/json')
                ->body(\json_encode([
                    'message' => $res['message'],
                    'error' => $res['error'] ?? [],
                ]));
        } catch (Exception $e) {
            throw new Exception('Controler Error: '.$e->getMessage());
        }
    }

    public function create(Request $request, Response $response)
    {
        try {
            $this->jwt->validate();
            $body = \json_decode($request->body(), true);

            arrayValidate($body, [
                'request',
                'price',
                'numberofinstallment',
                'dateofpayment',
                'fees',
            ]);
            $body = sanitizeArray($body);
            $body = convertType($body, ['int', 'decimals', 'int', 'string', 'int']);

            $res = $this->setNewTicket($body['request'], (string) $body['price'], $body['numberofinstallment'], $body['dateofpayment'], $body['fees']);

            if (empty($res)) {
                return $response
                    ->code(400)
                    ->header('Content-Type', 'application/json')
                    ->body(\json_encode([
                        'message' => 'Não foi possível criar o boleto!',
                    ]));
            }

            return $response
                ->code($res['status'])
                ->header('Content-Type', 'application/json')
                ->body(\json_encode([
                    'message' => $res['message'],
                    'error' => $res['error'] ?? [],
                ]));
        } catch (Exception $e) {
            throw new Exception('Controler Error: '.$e->getMessage());
        }
    }

    public function paid(Request $request, Response $response)
    {
        try {
            $this->jwt->validate();
            $body = \json_decode($request->body(), true);

            arrayValidate($body, [
                'request',
                'account',
            ]);
            $body = sanitizeArray($body);
            $body = convertType($body, ['int', 'int']);

            $res = $this->payinstallment($body['request'], $body['account']);

            if (empty($res)) {
                return $response
                    ->code(400)
                    ->header('Content-Type', 'application/json')
                    ->body(\json_encode([
                        'message' => 'Não foi possível criar o boleto!',
                    ]));
            }

            return $response
                ->code($res['status'])
                ->header('Content-Type', 'application/json')
                ->body(\json_encode([
                    'message' => $res['message'],
                    'error' => $res['error'] ?? [],
                ]));
        } catch (Exception $e) {
            throw new Exception('Controler Error: '.$e->getMessage());
        }
    }
}
