<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;


class SchemaSeeder extends Seeder
{
    public function __construct($entityClassName)
    {
        switch (Config::get('database.default')) {
            case 'mysql':
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                $entityClassName::truncate();
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                break;
            case 'pgsql':
                DB::statement("SET session_replication_role = 'replica';");
                $entityClassName::truncate();
                DB::statement("SET session_replication_role = 'origin';");
                break;
            case 'sqlite':
                DB::statement('PRAGMA foreign_keys = OFF;');
                $entityClassName::truncate();
                DB::statement('PRAGMA foreign_keys = ON;');
                break;
        }
    }
}
