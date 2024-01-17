<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
 
    public function run(): void
    {
        User::create([
            "name"          =>  "admin",
            "email"         =>  "admin@admin.com",
            "admin_acess"   =>  "1",
            "password"      =>  Hash::make("admin123")
        ]);
    }
}
