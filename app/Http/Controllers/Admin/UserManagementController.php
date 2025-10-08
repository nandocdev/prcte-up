<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use App\Models\OrganizationalUnit;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserManagementController extends Controller {
    // El middleware se maneja en las rutas

    public function index(Request $request): View {
        $users = User::with(['roles', 'organizationalUnit'])
            ->when($request->get('search'), function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('professor_code', 'like', "%{$search}%");
                });
            })
            ->when($request->get('role'), function ($query, $role) {
                $query->whereHas('roles', function ($q) use ($role) {
                    $q->where('name', $role);
                });
            })
            ->orderBy('name')
            ->paginate(15);

        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create(): View {
        $roles = Role::all();
        $organizationalUnits = OrganizationalUnit::orderBy('name')->get();

        return view('admin.users.create', compact('roles', 'organizationalUnits'));
    }

    public function store(StoreUserRequest $request): RedirectResponse {
        $user = User::createUser($request->validated());

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', __('Usuario creado exitosamente.'));
    }

    public function show(User $user): View {
        $user->load(['roles.permissions', 'organizationalUnit', 'workOfExtensions']);

        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user): View {
        $roles = Role::all();
        $organizationalUnits = OrganizationalUnit::orderBy('name')->get();

        return view('admin.users.edit', compact('user', 'roles', 'organizationalUnits'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse {
        $user->updateUser($request->validated());

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', __('Usuario actualizado exitosamente.'));
    }

    public function destroy(User $user): RedirectResponse {
        if ($user->getAttribute('id') === Auth::id()) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', __('No puedes eliminar tu propia cuenta.'));
        }

        if ($user->workOfExtensions()->exists()) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', __('No se puede eliminar este usuario porque tiene trabajos de extensión asociados.'));
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', __('Usuario eliminado exitosamente.'));
    }

    public function assignRole(Request $request, User $user): RedirectResponse {
        $request->validate([
            'role' => 'required|string|exists:roles,name'
        ]);

        $user->assignRole($request->input('role'));

        return redirect()
            ->back()
            ->with('success', __('Rol asignado exitosamente.'));
    }

    public function removeRole(Request $request, User $user): RedirectResponse {
        $request->validate([
            'role' => 'required|string|exists:roles,name'
        ]);

        $user->removeRole($request->input('role'));

        return redirect()
            ->back()
            ->with('success', __('Rol removido exitosamente.'));
    }

    public function givePermission(Request $request, User $user): RedirectResponse {
        $request->validate([
            'permission' => 'required|string|exists:permissions,name'
        ]);

        $user->givePermissionTo($request->input('permission'));

        return redirect()
            ->back()
            ->with('success', __('Permiso otorgado exitosamente.'));
    }

    public function revokePermission(Request $request, User $user): RedirectResponse {
        $request->validate([
            'permission' => 'required|string|exists:permissions,name'
        ]);

        $user->revokePermissionTo($request->input('permission'));

        return redirect()
            ->back()
            ->with('success', __('Permiso revocado exitosamente.'));
    }
}
