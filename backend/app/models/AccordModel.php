<?php

namespace app\Models;

use PDO;
use \PDOException;
use app\models\ConnectModel;

class AccordModel extends ConnectModel
{
    /**
     * @return array {status: number, data: array}
     */
    protected function getAccords(): array
    {
        try {
            $sql = $this->connect()->prepare("SELECT * FROM accords");
            $sql->execute();

            if ($sql->rowCount() == 0) {
                return ['status' => 404, 'data' => 'Nenhum acordo encontrado'];
            }
            return ['status' => 200, 'data' => $sql->fetchAll(PDO::FETCH_ASSOC)];
        } catch (PDOException $pe) {
            throw new PDOException('Erro ao buscar os acordos: ' . $pe->getMessage(), $pe->getCode());
        }
    }

    /**
     * @return array {status: number, data: string}
     */
    protected function setNewAccord(
        int $client,
        string $price,
        int $numberofinstallments,
        int $installmentspaid,
        string $fees,
        array|string $requests,
        array|string $tickets
    ): void {}

    /**
     * @return array {status: number, data: string}
     */
    protected function payInstallmentOfAccord(int $accord, int $installments): void {}

    /**
     * @return array {status: number, data: string}
     */
    protected function updateStatusOfAccord(int $accord, string $status): void {}
}
