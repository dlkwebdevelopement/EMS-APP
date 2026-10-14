<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            AdminUserSeeder::class,
            DemoDataSeeder::class,
        ]);

        // Load external SQL dump if available (Only data, no CREATE TABLE)
        $sqlPath = base_path('crorebah_employee_data.sql');
        if (file_exists($sqlPath)) {
            $sql = file_get_contents($sqlPath);
            \Illuminate\Support\Facades\DB::unprepared($sql);
            $this->command->info('Successfully imported crorebah_employee_data.sql');
        }
    }
}