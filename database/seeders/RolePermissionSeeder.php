<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $administrador = Role::create(['name' => 'Administrador', 'description' => 'Rol de administrador con permisos totales']);
        $recepcionista = Role::create(['name' => 'Recepcionista', 'description' => 'Rol de recepcionista para gestión de reservas']);
        $supervisor = Role::create(['name' => 'Supervisor', 'description' => 'Rol de supervisor para reportes']);

        $permisos = [
            'crear_cliente' => 'Crear clientes',
            'editar_cliente' => 'Editar clientes',
            'ver_cliente' => 'Ver información de clientes',
            'crear_reserva' => 'Crear reservas',
            'editar_reserva' => 'Editar reservas',
            'ver_reserva' => 'Ver reservas',
            'crear_pago' => 'Crear pagos',
            'editar_pago' => 'Editar pagos',
            'ver_pago' => 'Ver pagos',
            'crear_resena' => 'Crear reseñas',
            'ver_resena' => 'Ver reseñas',
            'crear_habitacion' => 'Crear habitaciones',
            'editar_habitacion' => 'Editar habitaciones',
            'ver_habitacion' => 'Ver habitaciones',
            'ver_reportes' => 'Ver reportes',
            'crear_usuario' => 'Crear usuarios',
            'editar_usuario' => 'Editar usuarios',
            'ver_usuario' => 'Ver usuarios',
        ];

        $permisosModelos = [];
        foreach ($permisos as $nombre => $descripcion) {
            $permisosModelos[$nombre] = Permission::create(['name' => $nombre, 'description' => $descripcion]);
        }

        $administrador->permissions()->sync(array_keys($permisosModelos));

        $recepcionistaPermisos = ['crear_cliente', 'editar_cliente', 'ver_cliente', 'crear_reserva', 'editar_reserva', 'ver_reserva', 'crear_pago', 'ver_pago', 'ver_habitacion'];
        $recepcionista->permissions()->sync(array_map(function ($p) use ($permisosModelos) {
            return $permisosModelos[$p]->id;
        }, $recepcionistaPermisos));

        $supervisorPermisos = ['ver_cliente', 'ver_reserva', 'ver_pago', 'ver_habitacion', 'ver_reportes'];
        $supervisor->permissions()->sync(array_map(function ($p) use ($permisosModelos) {
            return $permisosModelos[$p]->id;
        }, $supervisorPermisos));
    }
}
