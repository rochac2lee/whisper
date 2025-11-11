<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed do usuário administrador padrão
     */
    public function run(): void
    {
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@whisper.com.br',
            'password' => Hash::make('admin123'),
            'is_admin' => true,
            'force_password_change' => true,
        ]);
    }
}
