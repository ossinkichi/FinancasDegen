<?php

namespace app\Models;

use app\models\ConnectModel;
use \Exception;
use PDO;
use PDOException;

class PromotionModel extends ConnectModel
{

    protected function setPromotion(int $plan, string|int $price, mixed $expired): array
    {
        try {
            $sql = $this->connect()->prepare('INSERT INTO promotion(plan, price, dateofexpired) VALUE(:plan,:price,:expired)');
            $sql->bindValue(':plan', $plan);
            $sql->bindValue(':price', $price);
            $sql->bindValue(':expired', $expired);
            $sql->execute();
            if ($sql->rowCount() == 0) {
                return ['status' => 404, 'message' => 'Houve um erro ao buscar o dado'];
            }
            return ['status' => 201, 'message' => ''];
        } catch (PDOException $pe) {
            throw new PDOException('Promotion error: ' . $pe->getMessage());
        }
    }

    protected function getPromotion(int $plan): array
    {
        try {
            $sql = $this->connect()->prepare('SELECT * FROM promotion WHERE plan = :plan');
            $sql->bindValue(':plan', $plan);
            $sql->execute();

            if ($sql->rowCount() == 0) {
                return ['status' => 404, 'message' => 'Nenhum dado encontrado'];
            }
            return ['status' => 200, 'message' => $sql->fetchAll(PDO::FETCH_ASSOC)];
        } catch (PDOException $pe) {
            throw new PDOException('Promotion error: ' . $pe->getMessage());
        } catch (Exception $e) {
            throw new PDOException('Promotion error: ' . $e->getMessage());
        }
    }

    protected function desactivatedPromotion(int $promotion): array
    {
        try {
            $sql = $this->connect()->prepare('UPDATE promotion SET status = false WHERE plan = :plan');
            $sql->bindValue(':plan', $promotion);
            $sql->execute();

            if ($sql->rowCount() == 0) {
                return ['status' => 404, 'message' => 'A promoção não pode ser destivada'];
            }
            return ['status' => 201, 'message' => ''];
        } catch (PDOException $pe) {
            throw new PDOException('Promotion error: ' . $pe->getMessage());
        }
    }

    protected function activatedPromotion(int $promotion): array
    {
        try {
            $sql = $this->connect()->prepare('UPDATE promotion SET status = true WHERE plan = :plan');
            $sql->bindValue(':plan', $promotion);
            $sql->execute();

            if ($sql->rowCount() == 0) {
                return ['status' => 404, 'message' => 'A promoção não pode ser ativada'];
            }
            return ['status' => 201, 'message' => ''];
        } catch (PDOException $pe) {
            throw new PDOException('Promotion error: ' . $pe->getMessage());
        }
    }
}
