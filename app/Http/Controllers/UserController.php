<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

/**
 * @group User Management
 * 
 * APIs para gestionar usuarios del sistema
 */
class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     * Obtiene todos los usuarios activos del sistema con paginación
     * 
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        // Obtener usuarios paginados (15 por página)
        $users = User::where('isActive', true)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'message' => 'Usuarios obtenidos correctamente',
            'data' => $users
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     * 
     * Crea un nuevo usuario en el sistema
     * 
     * @param StoreUserRequest $request
     * @return JsonResponse
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        try {
            // Crear el usuario con los datos validados
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role ?? 'user',
                'isActive' => $request->isActive ?? true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Usuario creado exitosamente',
                'data' => $user
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     * 
     * Obtiene la información de un usuario específico
     * 
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Usuario obtenido correctamente',
                'data' => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     * 
     * Actualiza la información de un usuario existente
     * 
     * @param UpdateUserRequest $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(UpdateUserRequest $request, string $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);

            // Preparar datos para actualizar
            $dataToUpdate = $request->only(['name', 'email', 'role', 'isActive']);

            // Si se proporciona contraseña, hashearla
            if ($request->has('password')) {
                $dataToUpdate['password'] = Hash::make($request->password);
            }

            // Actualizar el usuario
            $user->update($dataToUpdate);

            return response()->json([
                'success' => true,
                'message' => 'Usuario actualizado exitosamente',
                'data' => $user->fresh() // Obtener datos actualizados
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     * 
     * Elimina un usuario del sistema (soft delete)
     * 
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);

            // Soft delete del usuario
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'Usuario eliminado exitosamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Restore a soft deleted user.
     * 
     * Restaura un usuario eliminado (soft delete)
     * 
     * @param string $id
     * @return JsonResponse
     */
    public function restore(string $id): JsonResponse
    {
        try {
            $user = User::withTrashed()->findOrFail($id);

            if (!$user->trashed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'El usuario no está eliminado'
                ], 400);
            }

            $user->restore();

            return response()->json([
                'success' => true,
                'message' => 'Usuario restaurado exitosamente',
                'data' => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al restaurar el usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all deleted users.
     * 
     * Obtiene todos los usuarios eliminados
     * 
     * @return JsonResponse
     */
    public function trashed(): JsonResponse
    {
        $users = User::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'message' => 'Usuarios eliminados obtenidos correctamente',
            'data' => $users
        ], 200);
    }
}
