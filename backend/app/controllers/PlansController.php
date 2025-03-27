<?php

namespace app\controllers;

use Exception;
use Klein\Request;
use Klein\Response;
use app\classes\Helper;
use app\models\PlansModel;

class PlansController extends PlansModel
{

    private Helper $helper;

    public function __construct()
    {
        $this->helper = new Helper;
    }

    // Puxa todos os planos
    public function plans(Request $request, Response $response): Response
    {
        try {
            $res = $this->getPlans(); // Envia um pedido ao banco e recebe sua resposta

            // Verifica se tem alguma resposta
            if (empty($res) || !isset($res['message'])) {
                return $response->code(203)->header('Content-Type', 'application/json')->body(\json_encode(['message' => 'nenhum plano encontrado']));
            }

            // Verifica se é um array e sanitiza
            if (is_array($res['message'])) {
                $res['message'] = \array_map([$this->helper, 'sanitizeArray'], $res['message']);
            }

            // retorna a resposta final ao front
            return $response
                ->code($res['status'])
                ->header('Content-Type', 'application/json')
                ->body(\json_encode(['message' => $res['message'], 'error' => $res['error'] ?? []]));
        } catch (Exception $e) {
            throw new Exception('Planos não encontrados: ' . $e->getMessage(), 404);
        }
    }

    // Registra um novo plano
    public function register(Request $request, Response $response)
    {
        $planParams = \json_decode($request->body(), true); // Pega os dados do body

        $this->helper->arrayValidate($planParams, ['name', 'describe', 'users', 'clients', 'price', 'type']); // Valida se os campos foram enviados
        $planParams = $this->helper->sanitizeArray($planParams); // Sanitiza os dados dos campos
        $planParams = $this->helper->convertType($planParams, ['string', 'string', 'int', 'int', 'decimals', 'string']); // Converte os tipos

        // Verifica se o typo do plano é aceitavel
        if (\strtolower($planParams['type']) !== "anual" && strtolower($planParams['type']) !== "mensal") {
            return $response->code(422)->header('Content-Type', 'application/json')->body(\json_encode(['message' => 'Tipo de plano invalido']));
        }

        $res = $this->setNewPlan($planParams['name'], $planParams['describe'], $planParams['users'], $planParams['clients'], $planParams['price'], $planParams['type']); // Envia um pedido ao banco de dados e recebe sua resposta

        // Envia a resposta final ao front
        return $response->code($res['status'])->header('Content-Type', 'application/json')->body(\json_encode(['message' => $res['message'], 'error' => $res['error'] ?? []]));
    }

    public function update(Request $request, Response $response): Response
    {
        try {
            $planData = \json_decode($request->body(), true);

            if (empty($planData)) {
                return $response->code(403)->header('Content-Type', 'application/json')->body(['message' => 'Dados incompletos']);
            }

            if (\strtolower($planData['type']) !== "anual" && \strtolower($planData['type']) !== "mensal") {
                return $response->code(400)->header('Content-Type', 'application/json')->body(['message' => 'Tipo de plano invalido']);
            }

            $res = $this->updatePlan($planData['id'], $planData['name'], $planData['describe'], $planData['users'], $planData['clients'], $planData['price'], $planData['type']);
            return $response->code($res['status'])->header('Content-Type', 'application/json')->body(['message' => $res['message']]);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
