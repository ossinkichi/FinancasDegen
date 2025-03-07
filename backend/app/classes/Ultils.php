<?php

namespace app\classes;

use app\models\ConnectModel;
use app\controllers\UserController;

class Ultils extends ConnectModel
{

    public static function createTables()
    {
        $db = new ConnectModel;
        $db->connect();
        $db->plansTable();
        $db->companyTable();
        $db->usersTable();
        $db->clientsTable();
        $db->requestTable();
    }

    public static function seedUsersTable(array $user)
    {
        $users = new UserController;
        // $users->register($user);
    }
}
