<?php

namespace App\controllers;

use AccordDTO;
use App\Models\AccordRepository;
use Exception;
use Klein\Request;
use Klein\Response;

class AccordController extends BaseController
{
    private AccordRepository $repository;

    public function __construct()
    {
        parent::__construct(); // Chama o construtor da classe pai
        $this->repository = new AccordRepository;
    }

    /**
     * Busca todos os acordos de um usuário
     */
    public function getAgreements(Request $request, Response $response): Response
    {
        $this->jwt->validate();
        $clientId = $request->param('client');

        try {
            $accords = $this->repository->getAccordsOfClient($clientId);

            return $this->success($response, [
                'data' => $accords
            ]);
        } catch (Exception $e) {
            return $this->error($response, $e, [
                'message' => 'Erro ao buscar os acordos',
            ]);
        }
    }

    public function register(Request $request, Response $response): Response
    {

        $this->jwt->validate();
        $accordPayload = json_decode($request->body());
        $accordDTO = AccordDTO::make($accordPayload);

        $this->repository->setNewAccord($accordDTO);

        return $this->success(response: $response, payload: [], statusCode: 201);
    }

    public function payInstallment(Request $request, Response $response): void
    {

        $this->jwt->validate();
        // TODO: implement payInstallment
    }

    public function updateStatus(Request $request, Response $response): Response
    {
        $this->jwt->validate();
        $accord = json_decode($request->body());
        $accord = convertType($accord, ['int', 'string']);

        arrayValidate($accord, ['accord', 'status']);

        $accordResponse = $this->repository->updateStatusOfAccord($accord['accord'], $accord['status']);

        return $response->code($accordResponse['status'])
            ->header('Content-Type', 'application/json')
            ->body(['message' => $accordResponse['message'], 'error' => $accordResponse['error'] ?? []]);

    }

    public function delete(Request $request, Response $response): Response
    {
        $this->jwt->validate();
        $accord = $request->param('accord');
        $accord = convertType([$accord], ['int']);

        $deleteResponse = $this->repository->deleteAccord($accord[0]);

        return $response->code($deleteResponse['status'])
            ->header('Content-Type', 'application/json')
            ->body(['message' => $deleteResponse['message'], 'error' => $deleteResponse['error'] ?? []]);
    }
}
