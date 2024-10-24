<?php

namespace App\Controllers;

use app\models\PlansModel;
use app\classes\Helper;
use BadFunctionCallException;
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

            $this->helper->message(['data' => $plans]);
        } catch (Exception $e) {
            throw new Exception('Planos não encontrados: ' . $e->getMessage(), 404);
        }
    }

    public function register(string $name, string $describe, string $price, int $numberofusers, int $numberofclients, string $type)
    {
        $this->helper->verifyMethod('POST');

        $plan = [
            'planname' => filter_var($name, FILTER_SANITIZE_SPECIAL_CHARS),
            'plandescribe' => filter_var($describe, FILTER_SANITIZE_SPECIAL_CHARS),
            'numberofusers' => filter_var($price, FILTER_SANITIZE_SPECIAL_CHARS),
            'numberofclients' => filter_var($numberofusers, FILTER_SANITIZE_SPECIAL_CHARS),
            'price' => filter_var($numberofclients, FILTER_SANITIZE_SPECIAL_CHARS),
            'type' => filter_var($type, FILTER_SANITIZE_SPECIAL_CHARS),
        ];

        foreach($plan as $key => $value){
            if(empty($value)){
                $this->helper->message(['error' => 'Campo obrigatorio não informado'], 400);
                return;
            }
        }

        if($plan['typer'] != 'anual' || $plan['typer'] != 'mensal'){
            $this->helper->message(['error' => 'Tipo de plano invalido'], 400);
            return;
        }

        $this->setNewPlan($plan);
        $this->helper->message(['message' => 'success']);
    }

    public function promotion(string $type, string $price) {}

    private function promotionPrice(string $type, int $price) {}
}
