<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('db:seed-hotel', function () {
    $this->call('db:seed', ['--class' => 'RolePermissionSeeder']);
    $this->info('Hotel roles and permissions seeded successfully.');
})->purpose('Seed hotel roles and permissions');

Artisan::command('hotel:generate-sample-data', function () {
    \DB::table('roles')->insert([
        ['name' => 'Administrador', 'description' => 'Full access to system'],
        ['name' => 'Recepcionista', 'description' => 'Reception staff access'],
        ['name' => 'Supervisor', 'description' => 'Supervisor access'],
        ['name' => 'Cliente', 'description' => 'Guest access'],
    ]);
    
    $this->info('Sample data generated successfully.');
})->purpose('Generate sample data for testing');

Artisan::command('hotel:create-admin {email} {password}', function ($email, $password) {
    $role = \App\Models\Role::where('name', 'Administrador')->first();
    
    if (!$role) {
        $this->error('Admin role not found. Run db:seed-hotel first.');
        return;
    }
    
    $user = \App\Models\User::create([
        'name' => 'Administrator',
        'email' => $email,
        'password' => bcrypt($password),
        'role_id' => $role->id,
        'email_verified_at' => now(),
    ]);
    
    $this->info("Admin user created: {$email}");
})->purpose('Create an admin user');
