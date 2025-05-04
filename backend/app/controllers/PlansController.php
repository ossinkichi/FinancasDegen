<?php

namespace App\Controllers;

use Exception;
use Klein\Request;
use Klein\Response;
use App\Controllers\BaseController;
use App\DTO\PlansDto;
use App\Entities\PlansEntity;
use App\Repositories\PlansRepository;

class PlansController extends BaseController
{

    private PlansRepository $repository;
    private PlansEntity $entity;
    // private PlansDto $dto;

    public function __construct()
    {
        parent::__construct(); // Chama o construtor da classe pai
        $this->repository = new PlansRepository(); // Instancia o repositório de planos
        // $this->dto = new PlansDto(); // Instancia o DTO de planos
    }

    // Puxa todos os planos
    public function plans(Request $request, Response $response): Response
    {
        try {
            $plans = $this->repository->getPlans(); // Envia um pedido ao banco e recebe sua resposta

            return $this->successRequest(response: $response, payload: [
                'data' => array_map(fn($model) => $model->JsonSerialize(), $plans), // Converte os dados para o formato JSON
                'message' => 'Planos encontrados com sucesso',
            ]); // Envia a resposta ao front
        } catch (Exception $e) {
            return $this->errorRequest(response: $response, throwable: $e, context: ['Erro ao executar o pedido']);
        }
    }

    // Registra um novo plano
    public function register(Request $request, Response $response)
    {
        try {
            $plansPayload = \json_decode($request->body(), true); // Pega os dados do body
            $plansDto = PlansDto::make($plansPayload); // Cria um novo DTO de planos

            $this->repository->setNewPlan($plansDto); // Envia um pedido ao banco de dados e recebe sua resposta

            return $this->successRequest(response: $response, payload: [], statusCode: 201); // Envia uma resposta ao front
        } catch (Exception $e) {
            $this->errorRequest(response: $response, throwable: $e, context: ['Erro ao executar o pedido']);
        }
    }

    // Atualiza um plano existente
    public function update(Request $request, Response $response): Response
    {
        try {
            $planPayload = \json_decode($request->body(), true); // Pega os dados enviados do front
            $planDto = PlansDto::make($planPayload); // Cria um novo DTO de planos

            // Envia o pedido ao banco de dados e recebe sua resposta
            $this->repository->updatePlan($planDto);
            return $this->successRequest(response: $response, payload: [], statusCode: 201); // Envia uma resposta ao front
        } catch (Exception $e) {
            return $this->errorRequest(response: $response, throwable: $e, context: ['Erro ao executar o pedido']);
        }
    }


    // Ativa um plano
    public function enable(Request $request, Response $response): Response
    {
        try {
            $planId = $request->param('plan'); // Pega os dados enviados

            $this->repository->enableThePlan($planId); // Envia o pedido ao banco de dados e recebe sua resposta

            // Envia uma resposta ao front
            return $this->successRequest(response: $response, payload: [], statusCode: 201); // Verifica se há um retorno
        } catch (Exception $e) {
            return $this->errorRequest(response: $response, throwable: $e, context: ['Erro ao executar o pedido']);
        }
    }

    // Desativa um plano
    public function disable(Request $request, Response $response): Response
    {
        try {
            $planId = $request->param('plan'); // Pega os dados enviados

            $this->repository->disableThePlan($planId); // Envia o pedido ao // Verifica se há um retorno

            // Envia uma resposta ao front
            return $this->successRequest(response: $response, payload: [], statusCode: 201);
        } catch (Exception $e) {
            return $this->errorRequest(response: $response, throwable: $e, context: ['Erro ao executar o pedido']);
        }
    }
}
