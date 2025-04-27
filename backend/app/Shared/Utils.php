<?php

namespace App\Shared;

use App\models\ConnectModel;

class Utils extends ConnectModel
{
    public function createTables(): void
    {
        $db = new ConnectModel;
        $db->connect();
        $db->createTables();
    }
}
