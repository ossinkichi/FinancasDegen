<?php

namespace app\controllers;

use \Exception;
use Klein\Request;
use Klein\Response;
use app\Classes\helper;
use app\Classes\JwtHelper;
use app\Models\AccordModel;

class AccordController extends AccordModel
{
    private JwtHelper $jwt;
    private helper $helper;

    public function __construct()
    {
        $this->jwt = new JwtHelper();
        $this->helper = new helper();
    }

    /**
     * Busca todos os acordos de um usuário
     * @return Response
     */
    public function getAcoords(Request $request, Response $response): Response
    {
        try {
            $this->jwt->validate();
            $client = $request->param('client');
            $client = $this->helper->convertType([$client], ['int']);

            $accords = $this->getAccordsOfClient($client[0]);
            $accords['message'] = \array_map([$this->helper, 'sanitizeArray'], $accords['message']);

            return $response->code($accords['status'])
                ->header('Content-Type', 'application/json')
                ->body(\json_encode(['message' => $accords['message'], 'error' => $accords['error'] ?? []]));
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Registra um novo acordo
     * @return Response
     */
    public function register(Request $request, Response $response): Response
    {
        try {
            $this->jwt->validate();
            $accordData = \json_decode($request->body());
            $this->helper->arrayValidate($accordData, ['client', 'price', 'numberofinstallments', 'installmentspaid', 'fees', 'requests', 'tickets']);
            $accordData = $this->helper->convertType($accordData, ['int', 'string', 'int', 'int', 'string', 'array', 'array']);

            $accordResponse = $this->setNewAccord(
                $accordData['client'],
                $accordData['price'],
                $accordData['numberofinstallments'],
                $accordData['installmentspaid'],
                $accordData['fees'],
                $accordData['requests'],
                $accordData['tickets']
            );
            return $response->code($accordResponse['status'])
                ->header('Content-Type', 'application/json')
                ->body(['message' => $accordResponse['message'], 'error' => $accordResponse['error'] ?? []]);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Paga um parcela do acordo
     * @return Response
     */
    public function payInstallment(Request $request, Response $response): void
    {
        try {
            $this->jwt->validate();
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Atualiza o status do acordo
     * @return Response
     */
    public function updateStatus(Request $request, Response $response): Response
    {
        try {
            $this->jwt->validate();
            $accord = \json_decode($request->body());
            $accord = $this->helper->convertType($accord, ['int', 'string']);
            $this->helper->arrayValidate($accord, ['accord', 'status']);

            $accordResponse = $this->updateStatusOfAccord($accord['accord'], $accord['status']);
            return $response->code($accordResponse['status'])
                ->header('Content-Type', 'application/json')
                ->body(['message' => $accordResponse['message'], 'error' => $accordResponse['error'] ?? []]);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Deleta um acordo teoricamente
     * @return Response
     */
    public function delete(Request $request, Response $response): Response
    {
        try {
            $this->jwt->validate();
            $accord = $request->param('accord');
            $accord = $this->helper->convertType([$accord], ['int']);

            $deleteResponse = $this->deleteAccord($accord[0]);
            return $response->code($deleteResponse['status'])
                ->header('Content-Type', 'application/json')
                ->body(['message' => $deleteResponse['message'], 'error' => $deleteResponse['error'] ?? []]);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }
}
