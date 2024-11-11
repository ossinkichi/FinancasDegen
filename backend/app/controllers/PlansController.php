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
            $plans = $this->getPlans();

            if (empty($plans)) {
                $this->helper->message(['message' => 'nenhum plano encontrado'], 400);
                return;
            }

            $this->helper->message(['message' => 'success', 'data' => $plans]);
        } catch (Exception $e) {
            throw new Exception('Planos não encontrados: ' . $e->getMessage(), 404);
        }
    }

    public function register()
    {
        $this->helper->verifyMethod('POST');
        $plan = file_get_contents("php://input");
        $plan = $this->helper->getData($plan);
        $plan = $this->helper->sanitizeArray($plan);

        if ($plan['type'] !== "anual" && $plan['type'] !== "mensal") {
            $this->helper->message(['error' => 'Tipo de plano invalido'], 400);
            return;
        }

        $this->setNewPlan($plan['name'], $plan['describe'], $plan['users'], $plan['clients'], $plan['price'], $plan['type']);
        $this->helper->message(['message' => 'success']);
    }

    public function promotion(string $type, string $price) {}

    private function promotionPrice(string $type, int $price) {}
}
