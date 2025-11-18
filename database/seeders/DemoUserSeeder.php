<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DemoUserSeeder extends Seeder
{
    public function run()
    {
        User::updateOrCreate([
            'email' => 'admin@himmah.com'
        ],[
            'name' => 'Administrator',
            'email' => 'admin@himmah.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'active' => true,
        ]);
    }
}
