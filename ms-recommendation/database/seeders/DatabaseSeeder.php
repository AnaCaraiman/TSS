<?php

namespace Database\Seeders;

use App\Models\Action;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Factories\ActionFactory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    public function run(): void
    {
        Action::factory()->count(1000)->create();
    }
}
