<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        if (!$request->user()->hasRole('Administrador')) {
            return response()->json([
                'success' => false,
                'message' => 'No tiene permisos para ver usuarios'
            ], 403);
        }

        try {
            $users = User::with('role')
                ->get()
                ->map(fn($u) => [
                    'id' => $u->id,
                    'name' => $u->name,
                    'email' => $u->email,
                    'role' => $u->role->name,
                    'is_active' => $u->is_active,
                    'last_login_at' => $u->last_login_at,
                ]);

            return response()->json([
                'success' => true,
                'data' => $users
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener usuarios'
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        if (!$request->user()->hasRole('Administrador')) {
            return response()->json([
                'success' => false,
                'message' => 'No tiene permisos para crear usuarios'
            ], 403);
        }

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8',
                'role_id' => 'required|exists:roles,id',
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role_id' => $validated['role_id'],
                'is_active' => true,
            ]);

            return response()->json([
                'success' => true,
                'data' => $user,
                'message' => 'Usuario creado exitosamente'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear usuario'
            ], 500);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        if (!$request->user()->hasRole('Administrador')) {
            return response()->json([
                'success' => false,
                'message' => 'No tiene permisos para editar usuarios'
            ], 403);
        }

        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no encontrado'
                ], 404);
            }

            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|email|unique:users,email,' . $id,
                'role_id' => 'sometimes|exists:roles,id',
                'is_active' => 'sometimes|boolean',
            ]);

            $user->update($validated);

            return response()->json([
                'success' => true,
                'data' => $user,
                'message' => 'Usuario actualizado exitosamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar usuario'
            ], 500);
        }
    }

    public function getRoles(): JsonResponse
    {
        try {
            $roles = Role::all()
                ->map(fn($r) => [
                    'id' => $r->id,
                    'name' => $r->name,
                    'description' => $r->description,
                ]);

            return response()->json([
                'success' => true,
                'data' => $roles
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener roles'
            ], 500);
        }
    }
}
