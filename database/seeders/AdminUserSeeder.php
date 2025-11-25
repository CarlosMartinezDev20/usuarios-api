<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Crea usuarios de prueba para el sistema
     */
    public function run(): void
    {
        // Crear usuario administrador
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'isActive' => true,
        ]);

        // Crear usuario regular
        User::create([
            'name' => 'Usuario Test',
            'email' => 'user@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'isActive' => true,
        ]);

        // Crear usuarios adicionales para estadísticas
        User::factory(50)->create();

        $this->command->info('Usuarios de prueba creados exitosamente!');
        $this->command->info('Admin: admin@example.com / password123');
        $this->command->info('User: user@example.com / password123');
    }
}
