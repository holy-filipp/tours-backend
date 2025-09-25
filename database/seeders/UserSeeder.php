<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            "first_name" => "Filipp",
            "last_name" => "Podriadov",
            "patronymic" => "Alekseevich",
            "birthday" => "21.07.2006",
            "email" => "im1@filipp.su",
            "password" => "qwerasd1!"
        ]);
    }
}
