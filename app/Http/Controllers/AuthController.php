<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

/**
 * @group Authentication
 * 
 * APIs para autenticación de usuarios
 */
class AuthController extends Controller
{
    /**
     * Login user and create token.
     * 
     * Autentica un usuario y genera un token de acceso que expira en 5 minutos
     * 
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            // Buscar el usuario por email
            $user = User::where('email', $request->email)->first();

            // Verificar si el usuario existe, está activo y la contraseña es correcta
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Credenciales incorrectas'
                ], 401);
            }

            // Verificar si el usuario está activo
            if (!$user->isActive) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario inactivo. Contacte al administrador.'
                ], 403);
            }

            // Revocar tokens anteriores del usuario
            $user->tokens()->delete();

            // Crear token con expiración de 5 minutos
            $token = $user->createToken(
                'auth_token',
                ['*'],
                now()->addMinutes(5)
            )->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login exitoso',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'expires_in' => 300 // 5 minutos en segundos
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error en el login',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Refresh user token.
     * 
     * Refresca el token de acceso del usuario autenticado
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function refresh(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            // Revocar el token actual
            $request->user()->currentAccessToken()->delete();

            // Crear nuevo token con expiración de 5 minutos
            $token = $user->createToken(
                'auth_token',
                ['*'],
                now()->addMinutes(5)
            )->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Token refrescado exitosamente',
                'data' => [
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'expires_in' => 300 // 5 minutos en segundos
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al refrescar el token',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Logout user.
     * 
     * Cierra sesión del usuario y revoca el token actual
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            // Revocar el token actual del usuario
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Logout exitoso'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cerrar sesión',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get authenticated user information.
     * 
     * Obtiene la información del usuario autenticado
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function me(Request $request): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'Usuario autenticado',
                'data' => $request->user()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener información del usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Register a new user (public).
     *
     * Permite crear un nuevo usuario y devuelve un token de acceso.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8',
                'role' => 'nullable|string|in:user,admin',
            ]);

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => $data['role'] ?? 'user',
                'isActive' => true,
            ]);

            // Crear token con expiración de 5 minutos
            $token = $user->createToken(
                'auth_token',
                ['*'],
                now()->addMinutes(5)
            )->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Registro exitoso',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'expires_in' => 300,
                ]
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $ve->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Logout from all devices.
     * 
     * Revoca todos los tokens del usuario (logout de todos los dispositivos)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function logoutAll(Request $request): JsonResponse
    {
        try {
            // Revocar todos los tokens del usuario
            $request->user()->tokens()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Sesión cerrada en todos los dispositivos'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cerrar sesión en todos los dispositivos',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
