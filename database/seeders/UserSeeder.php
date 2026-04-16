<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Primero, borramos los usuarios existentes para evitar correos duplicados
        DB::table('users')->truncate();

        // Creamos los usuarios usando el Hasher nativo de Laravel
        DB::table('users')->insert([
            [
                'name' => 'Administrador Sistema',
                'email' => 'admin@hotel.com',
                'password' => Hash::make('Admin@2024'), // <--- La magia ocurre aquí
                'role_id' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Maria García - Recepcionista',
                'email' => 'recepcion@hotel.com',
                'password' => Hash::make('Recepcion@2024'),
                'role_id' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Juan Pérez - Supervisor',
                'email' => 'supervisor@hotel.com',
                'password' => Hash::make('Supervisor@2024'),
                'role_id' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Carlos López - Gerente General',
                'email' => 'gerente@hotel.com',
                'password' => Hash::make('Gerente@2024'),
                'role_id' => 4,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}