<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\Users;
use Database\Seeders\Categories;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(Users::class);
        $this->call(Categories::class);
    }
}
