<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

class EnsureTestDatabaseExists extends Command
{
    protected $signature = 'db:ensure-test-db';

    protected $description = 'Create the test database if it does not exist';

    public function handle()
    {
        $testDbName = Config::get('database.connections.mysql_test.database', 'motac_irms_test');
        $host       = Config::get('database.connections.mysql_test.host', '127.0.0.1');
        $username   = Config::get('database.connections.mysql_test.username', 'root');
        $password   = Config::get('database.connections.mysql_test.password', '');
        $port       = Config::get('database.connections.mysql_test.port', 3306);

        try {
            $pdo = new \PDO("mysql:host=$host;port=$port", $username, $password);
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `$testDbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
            $this->info("Test database '$testDbName' ensured.");
        } catch (\Exception $e) {
            $this->error('Failed to create test database: '.$e->getMessage());

            return 1;
        }

        return 0;
    }
}
