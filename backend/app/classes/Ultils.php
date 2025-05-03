<?php

namespace app\classes;

use app\models\ConnectModel;
use App\Controllers\UserController;

class Ultils extends ConnectModel
{

    public function createTables(): void
    {
        $db = new ConnectModel;
        $db->connect();
        $db->createTables();
    }
}
