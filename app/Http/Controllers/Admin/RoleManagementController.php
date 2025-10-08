<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoleRequest;
use App\Http\Requests\Admin\UpdateRoleRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleManagementController extends Controller {
    // El middleware se maneja en las rutas

    public function index(): View {
        $roles = Role::with(['permissions', 'users'])->get();

        return view('admin.roles.index', compact('roles'));
    }

    public function create(): View {
        $permissions = Permission::all()->groupBy(function ($permission) {
            return explode('.', $permission->name)[0] ?? 'general';
        });

        return view('admin.roles.create', compact('permissions'));
    }

    public function store(StoreRoleRequest $request): RedirectResponse {
        $role = Role::create(['name' => $request->input('name')]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->input('permissions'));
        }
        return redirect()
            ->route('admin.roles.index')
            ->with('success', __('Rol creado exitosamente.'));
    }

    public function show(Role $role): View {
        $role->load(['permissions', 'users']);

        return view('admin.roles.show', compact('role'));
    }

    public function edit(Role $role): View {
        $permissions = Permission::all()->groupBy(function ($permission) {
            return explode('.', $permission->name)[0] ?? 'general';
        });

        $rolePermissions = $role->permissions()->pluck('name')->toArray();

        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse {
        $role->update(['name' => $request->input('name')]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->input('permissions'));
        } else {
            $role->syncPermissions([]);
        }
        return redirect()
            ->route('admin.roles.show', $role)
            ->with('success', __('Rol actualizado exitosamente.'));
    }

    public function destroy(Role $role): RedirectResponse {
        if ($role->users()->exists()) {
            return redirect()
                ->route('admin.roles.index')
                ->with('error', __('No se puede eliminar este rol porque tiene usuarios asignados.'));
        }

        $role->delete();

        return redirect()
            ->route('admin.roles.index')
            ->with('success', __('Rol eliminado exitosamente.'));
    }
}
