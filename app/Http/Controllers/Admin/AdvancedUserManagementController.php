<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\OrganizationalUnit;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;
use App\Imports\UsersImport;

class AdvancedUserManagementController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::with(['roles', 'organizationalUnit']);

        // Filtros avanzados
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('professor_code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->get('role'));
            });
        }

        if ($request->filled('organizational_unit')) {
            $query->where('organizational_unit_id', $request->get('organizational_unit'));
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->get('status') === 'active');
        }

        if ($request->filled('created_from')) {
            $query->whereDate('created_at', '>=', $request->get('created_from'));
        }

        if ($request->filled('created_to')) {
            $query->whereDate('created_at', '<=', $request->get('created_to'));
        }

        $users = $query->orderBy('name')->paginate(20);

        $roles = Role::all();
        $organizationalUnits = OrganizationalUnit::orderBy('name')->get();

        return view('admin.users.advanced-index', compact('users', 'roles', 'organizationalUnits'));
    }

    public function massAssignRoles(Request $request): JsonResponse
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'role_ids' => 'required|array',
            'role_ids.*' => 'exists:roles,id',
            'action' => 'required|in:assign,remove,sync'
        ]);

        $users = User::whereIn('id', $request->user_ids)->get();
        $roles = Role::whereIn('id', $request->role_ids)->get();

        $successCount = 0;
        $errors = [];

        foreach ($users as $user) {
            try {
                switch ($request->action) {
                    case 'assign':
                        $user->assignRole($roles);
                        break;
                    case 'remove':
                        $user->removeRole($roles);
                        break;
                    case 'sync':
                        $user->syncRoles($roles);
                        break;
                }
                $successCount++;
            } catch (\Exception $e) {
                $errors[] = "Error con usuario {$user->name}: " . $e->getMessage();
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Operación completada para {$successCount} usuarios.",
            'errors' => $errors
        ]);
    }

    public function massChangeStatus(Request $request): JsonResponse
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'status' => 'required|boolean'
        ]);

        $users = User::whereIn('id', $request->user_ids)->get();
        $successCount = 0;

        foreach ($users as $user) {
            // No permitir desactivar el propio usuario
            if ($user->id === auth()->id() && !$request->status) {
                continue;
            }

            $user->update(['is_active' => $request->status]);
            $successCount++;
        }

        return response()->json([
            'success' => true,
            'message' => "Estado actualizado para {$successCount} usuarios."
        ]);
    }

    public function massDelete(Request $request): JsonResponse
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        $users = User::whereIn('id', $request->user_ids)->get();
        $successCount = 0;
        $errors = [];

        foreach ($users as $user) {
            // No permitir eliminar el propio usuario
            if ($user->id === auth()->id()) {
                $errors[] = "No puedes eliminar tu propia cuenta.";
                continue;
            }

            // Verificar si tiene trabajos de extensión
            if ($user->workOfExtensions()->exists()) {
                $errors[] = "Usuario {$user->name} tiene trabajos de extensión asociados.";
                continue;
            }

            $user->delete();
            $successCount++;
        }

        return response()->json([
            'success' => true,
            'message' => "Eliminados {$successCount} usuarios.",
            'errors' => $errors
        ]);
    }

    public function generatePasswords(Request $request): JsonResponse
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'send_email' => 'boolean'
        ]);

        $users = User::whereIn('id', $request->user_ids)->get();
        $passwords = [];

        foreach ($users as $user) {
            $newPassword = Str::random(12);
            $user->update(['password' => Hash::make($newPassword)]);
            
            $passwords[] = [
                'user' => $user->name,
                'email' => $user->email,
                'password' => $newPassword
            ];

            // TODO: Enviar email con nueva contraseña si se solicita
            if ($request->send_email) {
                // Mail::to($user)->send(new NewPasswordMail($newPassword));
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Contraseñas generadas exitosamente.',
            'passwords' => $passwords
        ]);
    }

    public function export(Request $request)
    {
        $filters = $request->only(['search', 'role', 'organizational_unit', 'status']);
        
        return Excel::download(new UsersExport($filters), 'usuarios_' . date('Y-m-d') . '.xlsx');
    }

    public function importView(): View
    {
        return view('admin.users.import');
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        try {
            Excel::import(new UsersImport, $request->file('file'));
            
            return redirect()
                ->back()
                ->with('success', 'Usuarios importados exitosamente.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error al importar usuarios: ' . $e->getMessage());
        }
    }

    public function userStatistics(): JsonResponse
    {
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'inactive_users' => User::where('is_active', false)->count(),
            'professors' => User::whereNotNull('professor_code')->count(),
            'users_by_role' => Role::withCount('users')->get(),
            'users_by_unit' => OrganizationalUnit::withCount('users')->get(),
            'recent_signups' => User::where('created_at', '>=', now()->subDays(30))->count(),
        ];

        return response()->json($stats);
    }

    public function systemHealth(): JsonResponse
    {
        $health = [
            'database_connection' => true,
            'cache_status' => true,
            'queue_status' => true,
            'storage_usage' => [
                'total' => disk_total_space(storage_path()),
                'free' => disk_free_space(storage_path()),
                'used_percentage' => round((1 - disk_free_space(storage_path()) / disk_total_space(storage_path())) * 100, 2)
            ],
            'memory_usage' => [
                'current' => memory_get_usage(true),
                'peak' => memory_get_peak_usage(true),
            ]
        ];

        try {
            \DB::connection()->getPdo();
            $health['database_connection'] = true;
        } catch (\Exception $e) {
            $health['database_connection'] = false;
        }

        return response()->json($health);
    }

    public function clearCache(): JsonResponse
    {
        try {
            \Artisan::call('cache:clear');
            \Artisan::call('config:clear');
            \Artisan::call('route:clear');
            \Artisan::call('view:clear');

            return response()->json([
                'success' => true,
                'message' => 'Caché del sistema limpiado exitosamente.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al limpiar caché: ' . $e->getMessage()
            ], 500);
        }
    }

    public function optimizeSystem(): JsonResponse
    {
        try {
            \Artisan::call('optimize');
            \Artisan::call('config:cache');
            \Artisan::call('route:cache');

            return response()->json([
                'success' => true,
                'message' => 'Sistema optimizado exitosamente.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al optimizar sistema: ' . $e->getMessage()
            ], 500);
        }
    }
}