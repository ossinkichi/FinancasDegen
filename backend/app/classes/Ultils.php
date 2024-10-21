<?php

namespace App\classes;

use app\models\ConnectModel;
use app\controllers\UserController;

class Ultils extends ConnectModel
{

    public static function createTables()
    {
        $db = new ConnectModel;
        $db->connect();
        $db->plainsTable();
        $db->companyTable();
        $db->usersTable();
        $db->clientsTable();
        $db->accountClientTable();
    }

    public static function seedUsersTable(array $user)
    {
        $users = new UserController;
        $users->register($user);
    }
}
