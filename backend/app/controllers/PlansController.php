<?php

namespace app\controllers;

use app\models\PlansModel;
use app\classes\Helper;
use Exception;

class PlansController extends PlansModel
{

    private object $helper;

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
        $data = $_POST;

        $plan = [
            'planname' => filter_var($data['name'], FILTER_SANITIZE_SPECIAL_CHARS),
            'plandescribe' => filter_var($data['describe'], FILTER_SANITIZE_SPECIAL_CHARS),
            'numberofusers' => filter_var($data['price'], FILTER_SANITIZE_SPECIAL_CHARS),
            'numberofclients' => filter_var($data['numberofusers'], FILTER_SANITIZE_SPECIAL_CHARS),
            'price' => filter_var($data['numberofclients'], FILTER_SANITIZE_SPECIAL_CHARS),
            'type' => filter_var($data['type'], FILTER_SANITIZE_SPECIAL_CHARS),
        ];

        foreach ($plan as $key => $value) {
            if (empty($value)) {
                $this->helper->message(['error' => 'Campo obrigatorio não informado'], 400);
                return;
            }
        }

        if ($plan['typer'] != 'anual' || $plan['typer'] != 'mensal') {
            $this->helper->message(['error' => 'Tipo de plano invalido'], 400);
            return;
        }

        $this->setNewPlan($plan);
        $this->helper->message(['message' => 'success']);
    }

    public function promotion(string $type, string $price) {}

    private function promotionPrice(string $type, int $price) {}
}
