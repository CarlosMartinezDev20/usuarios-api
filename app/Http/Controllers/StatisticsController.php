<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * @group Statistics
 * 
 * APIs para obtener estadísticas de usuarios
 */
class StatisticsController extends Controller
{
    /**
     * Get users statistics.
     * 
     * Obtiene estadísticas de usuarios registrados por día, semana y mes
     * 
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            // Estadísticas generales
            $totalUsers = User::count();
            $activeUsers = User::where('isActive', true)->count();
            $inactiveUsers = User::where('isActive', false)->count();
            $deletedUsers = User::onlyTrashed()->count();

            // Usuarios por rol
            $usersByRole = User::select('role', DB::raw('count(*) as total'))
                ->groupBy('role')
                ->get()
                ->pluck('total', 'role');

            // Usuarios registrados hoy
            $today = Carbon::today();
            $usersToday = User::whereDate('created_at', $today)->count();

            // Usuarios registrados esta semana
            $startOfWeek = Carbon::now()->startOfWeek();
            $endOfWeek = Carbon::now()->endOfWeek();
            $usersThisWeek = User::whereBetween('created_at', [$startOfWeek, $endOfWeek])->count();

            // Usuarios registrados este mes
            $startOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now()->endOfMonth();
            $usersThisMonth = User::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();

            // Usuarios por día (últimos 7 días)
            $usersByDay = User::select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('count(*) as total')
                )
                ->where('created_at', '>=', Carbon::now()->subDays(7))
                ->groupBy('date')
                ->orderBy('date', 'asc')
                ->get();

            // Usuarios por mes (últimos 6 meses)
            $usersByMonth = User::select(
                    DB::raw('YEAR(created_at) as year'),
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('count(*) as total')
                )
                ->where('created_at', '>=', Carbon::now()->subMonths(6))
                ->groupBy('year', 'month')
                ->orderBy('year', 'asc')
                ->orderBy('month', 'asc')
                ->get()
                ->map(function ($item) {
                    return [
                        'period' => Carbon::create($item->year, $item->month)->format('Y-m'),
                        'month_name' => Carbon::create($item->year, $item->month)->locale('es')->monthName,
                        'total' => $item->total
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'Estadísticas obtenidas correctamente',
                'data' => [
                    'summary' => [
                        'total_users' => $totalUsers,
                        'active_users' => $activeUsers,
                        'inactive_users' => $inactiveUsers,
                        'deleted_users' => $deletedUsers,
                    ],
                    'by_role' => $usersByRole,
                    'registrations' => [
                        'today' => $usersToday,
                        'this_week' => $usersThisWeek,
                        'this_month' => $usersThisMonth,
                    ],
                    'by_day' => $usersByDay,
                    'by_month' => $usersByMonth,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get daily statistics.
     * 
     * Obtiene estadísticas de usuarios registrados por día
     * 
     * @return JsonResponse
     */
    public function daily(): JsonResponse
    {
        try {
            // Usuarios por día (últimos 30 días)
            $usersByDay = User::select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('count(*) as total')
                )
                ->where('created_at', '>=', Carbon::now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Estadísticas diarias obtenidas correctamente',
                'data' => $usersByDay
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas diarias',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get weekly statistics.
     * 
     * Obtiene estadísticas de usuarios registrados por semana
     * 
     * @return JsonResponse
     */
    public function weekly(): JsonResponse
    {
        try {
            // Usuarios por semana (últimas 12 semanas)
            $usersByWeek = User::select(
                    DB::raw('YEAR(created_at) as year'),
                    DB::raw('WEEK(created_at) as week'),
                    DB::raw('count(*) as total')
                )
                ->where('created_at', '>=', Carbon::now()->subWeeks(12))
                ->groupBy('year', 'week')
                ->orderBy('year', 'asc')
                ->orderBy('week', 'asc')
                ->get()
                ->map(function ($item) {
                    return [
                        'period' => "Año {$item->year} - Semana {$item->week}",
                        'year' => $item->year,
                        'week' => $item->week,
                        'total' => $item->total
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'Estadísticas semanales obtenidas correctamente',
                'data' => $usersByWeek
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas semanales',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get monthly statistics.
     * 
     * Obtiene estadísticas de usuarios registrados por mes
     * 
     * @return JsonResponse
     */
    public function monthly(): JsonResponse
    {
        try {
            // Usuarios por mes (últimos 12 meses)
            $usersByMonth = User::select(
                    DB::raw('YEAR(created_at) as year'),
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('count(*) as total')
                )
                ->where('created_at', '>=', Carbon::now()->subMonths(12))
                ->groupBy('year', 'month')
                ->orderBy('year', 'asc')
                ->orderBy('month', 'asc')
                ->get()
                ->map(function ($item) {
                    return [
                        'period' => Carbon::create($item->year, $item->month)->format('Y-m'),
                        'month_name' => Carbon::create($item->year, $item->month)->locale('es')->monthName,
                        'year' => $item->year,
                        'month' => $item->month,
                        'total' => $item->total
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'Estadísticas mensuales obtenidas correctamente',
                'data' => $usersByMonth
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas mensuales',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
