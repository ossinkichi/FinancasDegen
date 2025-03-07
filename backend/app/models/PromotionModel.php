<?php

namespace app\Models;

use app\models\ConnectModel;
use \Exception;
use PDOException;

class PromotionModel extends ConnectModel
{

    protected function setPromotion(int $plan, $price, $expered)
    {
        try {
            $sql = $this->connect()->prepare('SELECT * FROM promotion WHERE plan == :plan');
            $sql->bindValue(':plan', $plan);
            $sql->execute();
        } catch (PDOException $pe) {
            throw new PDOException('Promotion error: ' . $pe->getMessage());
        } catch (Exception $e) {
            throw new PDOException('Promotion error: ' . $e->getMessage());
        }
    }

    protected function getPromotion(int $plan)
    {
        try {
            $sql = $this->connect()->prepare('SELECT * FROM promotion WHERE plan == :plan');
            $sql->bindValue(':plan', $plan);
            $sql->execute();
        } catch (PDOException $pe) {
            throw new PDOException('Promotion error: ' . $pe->getMessage());
        } catch (Exception $e) {
            throw new PDOException('Promotion error: ' . $e->getMessage());
        }
    }

    protected function desactivatedPromotion($promotion)
    {
        try {
            $sql = $this->connect()->prepare('SELECT * FROM promotion WHERE plan == :price');
            $sql->bindValue(':price', $promotion);
            $sql->execute();
        } catch (PDOException $pe) {
            throw new PDOException('Promotion error: ' . $pe->getMessage());
        } catch (Exception $e) {
            throw new PDOException('Promotion error: ' . $e->getMessage());
        }
    }
}
