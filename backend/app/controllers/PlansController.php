<?php

namespace app\controllers;

use app\models\PlansModel;
use app\classes\Helper;
use Exception;

class PlansController extends PlansModel
{

    private Helper $helper;

    public function __construct()
    {
        $this->helper = new Helper;
    }

    public function index(): void
    {
        $this->helper->verifyMethod('GET');
        try {
            $response = $this->getPlans();

            if (empty($response['message'])) {
                $this->helper->message(['message' => 'nenhum plano encontrado'], 400);
                return;
            }

            if (is_array($response['message'])) {
                foreach ($response['message'] as $key => $value) {
                    $response['message'][$key] = $this->helper->sanitizeArray($response['message'][$key]);
                }
            }

            $this->helper->message(['message' => $response['message']], $response['status']);
        } catch (Exception $e) {
            throw new Exception('Planos não encontrados: ' . $e->getMessage(), 404);
        }
    }

    public function register(): void
    {
        $this->helper->verifyMethod('POST');
        $plan = file_get_contents("php://input");
        $plan = $this->helper->getData($plan);
        $plan = $this->helper->sanitizeArray($plan);

        if ($plan['type'] !== "anual" && $plan['type'] !== "mensal") {
            $this->helper->message(['error' => 'Tipo de plano invalido'], 400);
            return;
        }

        $response = $this->setNewPlan($plan['name'], $plan['describe'], $plan['users'], $plan['clients'], $plan['price'], $plan['type']);
        $this->helper->message(['message' => $response['message']], $response['status']);
    }

    public function update(): void
    {
        try {
            $this->helper->verifyMethod('PUT');
            $planData = file_get_contents('php://input');

            if (empty($planData)) {
                $this->helper->message(['message' => 'Dados incompletos'], 403);
                return;
            }

            $planData = $this->helper->getData($planData);

            if ($planData['type'] !== "anual" && $planData['type'] !== "mensal") {
                $this->helper->message(['error' => 'Tipo de plano invalido'], 400);
                return;
            }

            $response = $this->updatePlan($planData['id'], $planData['name'], $planData['describe'], $planData['users'], $planData['clients'], $planData['price'], $planData['type']);
            $this->helper->message(['message' => $response['message']], $response['status']);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function promotion(): void {}

    private function promotionPrice(): void {}
}
