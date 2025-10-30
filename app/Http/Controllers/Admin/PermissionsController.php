<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsController extends Controller
{
    public function index(): View
    {
        $permissions = Permission::with(['roles'])->get();
        $allRoles = Role::all();

        // Agrupar permisos por categoría
        $groupedPermissions = $permissions->groupBy(function ($permission) {
            return explode('.', $permission->name)[0] ?? 'general';
        });

        // Estadísticas
        $stats = [
            'total_permissions' => $permissions->count(),
            'total_categories' => $groupedPermissions->count(),
            'permissions_with_roles' => $permissions->filter(fn($p) => $p->roles->count() > 0)->count(),
            'orphan_permissions' => $permissions->filter(fn($p) => $p->roles->count() === 0)->count(),
        ];

        return view('admin.permissions.index', compact('groupedPermissions', 'stats', 'allRoles'));
    }

    public function create(): View
    {
        $roles = Role::all();
        $categories = Permission::all()
            ->map(fn($p) => explode('.', $p->name)[0] ?? 'general')
            ->unique()
            ->sort()
            ->values();

        return view('admin.permissions.create', compact('roles', 'categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions'],
            'description' => ['nullable', 'string', 'max:500'],
            'roles' => ['array'],
            'roles.*' => ['exists:roles,id'],
        ]);

        $permission = Permission::create([
            'name' => $request->name,
            'guard_name' => 'web'
        ]);

        if ($request->has('roles')) {
            $roles = Role::whereIn('id', $request->roles)->get();
            foreach ($roles as $role) {
                $role->givePermissionTo($permission);
            }
        }

        return redirect()
            ->route('admin.permissions.index')
            ->with('success', __('Permiso creado exitosamente.'));
    }

    public function show(Permission $permission): View
    {
        $permission->load(['roles']);

        // Obtener usuarios que tienen este permiso (directamente o via roles)
        $usersWithPermission = collect();

        // Usuarios con el permiso via roles
        foreach ($permission->roles as $role) {
            $usersWithPermission = $usersWithPermission->merge($role->users);
        }

        // Usuarios con el permiso directo
        $usersWithDirectPermission = \App\Models\User::permission($permission->name)->get();
        $usersWithPermission = $usersWithPermission->merge($usersWithDirectPermission);

        $usersWithPermission = $usersWithPermission->unique('id');

        return view('admin.permissions.show', compact('permission', 'usersWithPermission'));
    }

    public function edit(Permission $permission): View
    {
        $permission->load(['roles']);
        $allRoles = Role::all();

        return view('admin.permissions.edit', compact('permission', 'allRoles'));
    }

    public function update(Request $request, Permission $permission): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name,' . $permission->id],
            'description' => ['nullable', 'string', 'max:500'],
            'roles' => ['array'],
            'roles.*' => ['exists:roles,id'],
        ]);

        // Verificar si es un permiso crítico del sistema
        if ($this->isSystemPermission($permission->name) && $request->name !== $permission->name) {
            return redirect()
                ->back()
                ->with('error', __('No se puede renombrar este permiso porque es crítico para el sistema.'));
        }

        $permission->update([
            'name' => $request->name
        ]);

        // Sincronizar roles
        if ($request->has('roles')) {
            $roles = Role::whereIn('id', $request->roles)->get();

            // Remover el permiso de todos los roles actuales
            foreach ($permission->roles as $role) {
                $role->revokePermissionTo($permission);
            }

            // Asignar a los nuevos roles
            foreach ($roles as $role) {
                $role->givePermissionTo($permission);
            }
        } else {
            // Si no se seleccionaron roles, remover de todos
            foreach ($permission->roles as $role) {
                $role->revokePermissionTo($permission);
            }
        }

        return redirect()
            ->route('admin.permissions.show', $permission)
            ->with('success', __('Permiso actualizado exitosamente.'));
    }

    public function destroy(Permission $permission): RedirectResponse
    {
        // Verificar si es un permiso crítico del sistema
        if ($this->isSystemPermission($permission->name)) {
            return redirect()
                ->route('admin.permissions.index')
                ->with('error', __('No se puede eliminar este permiso porque es crítico para el sistema.'));
        }

        // Verificar si está asignado a algún rol
        if ($permission->roles()->exists()) {
            return redirect()
                ->route('admin.permissions.index')
                ->with('error', __('No se puede eliminar este permiso porque está asignado a uno o más roles.'));
        }

        $permission->delete();

        return redirect()
            ->route('admin.permissions.index')
            ->with('success', __('Permiso eliminado exitosamente.'));
    }

    /**
     * Verificar si es un permiso crítico del sistema
     */
    private function isSystemPermission(string $permissionName): bool
    {
        $systemPermissions = [
            'system.manage',
            'users.manage',
            'roles.manage',
            'permissions.manage',
            'works.manage.viex',
            'works.coordinate',
            'works.manage.dean',
        ];

        return in_array($permissionName, $systemPermissions);
    }
}
