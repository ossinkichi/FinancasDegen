<?php

namespace app\models;

use app\models\ConnectModel;
use PDOException;

class CompanyModel extends ConnectModel
{

    private object $db;

    public function __construct()
    {
        $this->db = $this->connect();
    }

    public function getCompany(): array
    {
        $data = [];
        try {
            $sql = $this->db->prepare('SELECT * FROM companies');


            return $data;
        } catch (PDOException $pe) {
            throw new PDOException('GetCompany error: ' . $pe->getMessage());
        }
    }
    public function setNewCompany($companyData) {}
}
