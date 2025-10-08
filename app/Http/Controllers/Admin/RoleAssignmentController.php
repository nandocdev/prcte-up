<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrganizationalUnit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

/**
 * Controlador para la gestión avanzada de asignación de roles
 * Proporciona una vista matricial y herramientas de gestión masiva
 */
class RoleAssignmentController extends Controller {
    /**
     * Vista principal del dashboard de asignación de roles
     */
    public function index(Request $request): View {
        $search = $request->get('search');
        $roleFilter = $request->get('role_filter');
        $unitFilter = $request->get('unit_filter');

        // Construir query base de usuarios
        $usersQuery = User::with(['roles', 'organizationalUnit'])
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%")
                        ->orWhere('professor_code', 'LIKE', "%{$search}%");
                });
            })
            ->when($roleFilter, function ($query, $roleFilter) {
                return $query->whereHas('roles', function ($q) use ($roleFilter) {
                    $q->where('name', $roleFilter);
                });
            })
            ->when($unitFilter, function ($query, $unitFilter) {
                return $query->where('organizational_unit_id', $unitFilter);
            })
            ->orderBy('name');

        $users = $usersQuery->paginate(20)->withQueryString();

        // Datos para la vista
        $roles = Role::orderBy('name')->get();
        $organizationalUnits = OrganizationalUnit::orderBy('name')->get();
        $allUsers = User::orderBy('name')->get(); // Para el modal de asignación masiva

        // Estadísticas
        $totalUsers = User::count();
        $totalRoles = Role::count();
        $totalAssignments = DB::table('model_has_roles')->count();
        $usersWithoutRoles = User::doesntHave('roles')->count();

        // Datos para gráficos
        $roleDistribution = $this->getRoleDistribution();
        $unitDistribution = $this->getUnitDistribution();

        return view('admin.role-assignment.index', compact(
            'users',
            'roles',
            'organizationalUnits',
            'allUsers',
            'totalUsers',
            'totalRoles',
            'totalAssignments',
            'usersWithoutRoles',
            'roleDistribution',
            'unitDistribution'
        ));
    }

    /**
     * Obtener los roles de un usuario específico
     */
    public function getUserRoles(User $user): JsonResponse {
        return response()->json(
            $user->getRoleNames()->toArray()
        );
    }

    /**
     * Sincronizar roles de un usuario (edición rápida)
     */
    public function syncUserRoles(Request $request, User $user): RedirectResponse {
        $request->validate([
            'roles' => 'array',
            'roles.*' => 'exists:roles,name'
        ]);

        $oldRoles = $user->getRoleNames()->toArray();
        $newRoles = $request->input('roles', []);

        // Sincronizar roles
        $user->syncRoles($newRoles);

        // Log de auditoría
        $this->logRoleChanges($user, $oldRoles, $newRoles);

        return redirect()
            ->back()
            ->with('success', __('Roles actualizados correctamente para :user', ['user' => $user->name]));
    }

    /**
     * Asignación masiva de roles
     */
    public function massAssign(Request $request): RedirectResponse {
        $request->validate([
            'users' => 'required|array|min:1',
            'users.*' => 'exists:users,id',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,name',
            'action' => 'required|in:assign,remove,replace'
        ]);

        $userIds = $request->input('users');
        $roles = $request->input('roles');
        $action = $request->input('action');

        $users = User::whereIn('id', $userIds)->get();
        $affectedCount = 0;

        DB::transaction(function () use ($users, $roles, $action, &$affectedCount) {
            foreach ($users as $user) {
                $oldRoles = $user->getRoleNames()->toArray();

                switch ($action) {
                    case 'assign':
                        $user->assignRole($roles);
                        break;
                    case 'remove':
                        $user->removeRole($roles);
                        break;
                    case 'replace':
                        $user->syncRoles($roles);
                        break;
                }

                $newRoles = $user->fresh()->getRoleNames()->toArray();

                if ($oldRoles !== $newRoles) {
                    $this->logRoleChanges($user, $oldRoles, $newRoles);
                    $affectedCount++;
                }
            }
        });

        $actionText = match ($action) {
            'assign' => __('asignados'),
            'remove' => __('removidos'),
            'replace' => __('reemplazados'),
        };

        return redirect()
            ->back()
            ->with('success', __('Roles :action correctamente para :count usuarios', [
                'action' => $actionText,
                'count' => $affectedCount
            ]));
    }

    /**
     * Exportar matriz de roles en formato CSV
     */
    public function export(Request $request): Response {
        $search = $request->get('search');
        $roleFilter = $request->get('role_filter');
        $unitFilter = $request->get('unit_filter');

        $users = User::with(['roles', 'organizationalUnit'])
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%")
                        ->orWhere('professor_code', 'LIKE', "%{$search}%");
                });
            })
            ->when($roleFilter, function ($query, $roleFilter) {
                return $query->whereHas('roles', function ($q) use ($roleFilter) {
                    $q->where('name', $roleFilter);
                });
            })
            ->when($unitFilter, function ($query, $unitFilter) {
                return $query->where('organizational_unit_id', $unitFilter);
            })
            ->orderBy('name')
            ->get();

        $roles = Role::orderBy('name')->get();

        $csvContent = $this->generateCsvContent($users, $roles);

        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="matriz_roles_' . date('Y-m-d') . '.csv"');
    }

    /**
     * Generar contenido CSV para la exportación
     */
    private function generateCsvContent($users, $roles): string {
        $csv = fopen('php://temp', 'w+');

        // Encabezados
        $headers = ['Usuario', 'Email', 'Código Profesor', 'Unidad Organizacional'];
        foreach ($roles as $role) {
            $headers[] = ucfirst(str_replace('_', ' ', $role->name));
        }
        fputcsv($csv, $headers);

        // Datos
        foreach ($users as $user) {
            $row = [
                $user->name,
                $user->email,
                $user->professor_code ?? '',
                $user->organizationalUnit?->name ?? 'Sin asignar'
            ];

            foreach ($roles as $role) {
                $row[] = $user->hasRole($role->name) ? 'Sí' : 'No';
            }

            fputcsv($csv, $row);
        }

        rewind($csv);
        $csvContent = stream_get_contents($csv);
        fclose($csv);

        return $csvContent;
    }

    /**
     * Obtener distribución de roles para gráficos
     */
    private function getRoleDistribution(): array {
        return Role::withCount('users')
            ->get()
            ->pluck('users_count', 'name')
            ->toArray();
    }

    /**
     * Obtener distribución por unidades organizacionales
     */
    private function getUnitDistribution(): array {
        return OrganizationalUnit::withCount('users')
            ->get()
            ->pluck('users_count', 'name')
            ->filter(function ($count) {
                return $count > 0;
            })
            ->toArray();
    }

    /**
     * Registrar cambios en roles para auditoría usando Laravel Log
     */
    private function logRoleChanges(User $user, array $oldRoles, array $newRoles): void {
        $added = array_diff($newRoles, $oldRoles);
        $removed = array_diff($oldRoles, $newRoles);

        if (!empty($added) || !empty($removed)) {
            Log::info('Roles modificados para usuario', [
                'user_id' => $user->getKey(),
                'user_name' => $user->name,
                'performed_by' => Auth::user()?->name ?? 'Sistema',
                'roles_added' => $added,
                'roles_removed' => $removed,
                'old_roles' => $oldRoles,
                'new_roles' => $newRoles,
                'timestamp' => now()
            ]);
        }
    }
}
