<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\About;

class Abouts extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        About::create([
            'content' => 'test',
        ]);
    }
}
