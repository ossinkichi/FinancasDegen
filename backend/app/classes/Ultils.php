<?php

namespace App\Classes;

use App\Concern\InteractsWithDatabase;
use App\Models\ConnectModel;

class Ultils extends ConnectModel
{

    public function createTables(): void
    {
        $this->tablesInicialization();
    }
}
