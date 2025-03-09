<?php

namespace app\controllers;

use Exception;
use app\classes\Helper;
use app\models\PlansModel;
use Klein\Response;
use Klein\Request;

class PlansController extends PlansModel
{

    private Helper $helper;

    public function __construct()
    {
        $this->helper = new Helper;
    }

    public function index(Request $request, Response $response): Response
    {
        try {
            $res = $this->getPlans();

            if (empty($res['message'])) {
                return $response->code(400)->header('Content-Type', 'application/json')->body(\json_encode(['message' => 'nenhum plano encontrado']));
            }

            if (is_array($res['message'])) {
                $res['message'] = \array_map([$this->helper, 'sanitizeArray'], $res['message']);
            }

            return $response->code($response['status'])->header('Content-Type', 'application/json')->body(\json_encode(['message' => $res['message']]));
        } catch (Exception $e) {
            throw new Exception('Planos não encontrados: ' . $e->getMessage(), 404);
        }
    }

    public function register(Request $request, Response $response): Response
    {
        $plan = \json_decode($request->body(), true);

        if (empty($plan)) {
            return $response->code(400)->header('Content-Type', 'application/json')->body(\json_encode(['message' => 'Dados não informados']));
        }

        $plan = $this->helper->sanitizeArray($plan);

        if (\strtolower($plan['type']) !== "anual" && strtolower($plan['type']) !== "mensal") {
            return $response->code(400)->header('Content-Type', 'application/json')->body(\json_encode(['message' => 'Tipo de plano invalido']));
        }

        $res = $this->setNewPlan($plan['name'], $plan['describe'], $plan['users'], $plan['clients'], $plan['price'], $plan['type']);
        return $response->code($res['status'])->header('Content-Type', 'application/json')->body(\json_encode(['message' => $res['message']]));
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
