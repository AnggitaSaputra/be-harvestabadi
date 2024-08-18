<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\Users;
use Database\Seeders\Categories;
use Database\Seeders\Abouts;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(Users::class);
        $this->call(Categories::class);
        $this->call(Abouts::class);
    }
}
