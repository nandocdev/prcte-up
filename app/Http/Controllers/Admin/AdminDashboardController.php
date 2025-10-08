<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WorkOfExtension;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdminDashboardController extends Controller {
    // public function __construct() {
    //     $this->middleware(['auth', 'role:super_admin']);
    // }

    public function index(): View {
        // Estadísticas generales
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'professors' => User::whereNotNull('professor_code')->count(),
            'total_works' => WorkOfExtension::count(),
            'total_roles' => Role::count(),
            'total_permissions' => Permission::count(),
        ];

        // Usuarios por rol
        $usersByRole = Role::withCount('users')->get();

        // Trabajos recientes
        $recentWorks = WorkOfExtension::with(['primaryResponsibleUser', 'organizationalUnit'])
            ->latest()
            ->take(5)
            ->get();

        // Usuarios recientes
        $recentUsers = User::with('roles')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard.index', compact(
            'stats',
            'usersByRole',
            'recentWorks',
            'recentUsers'
        ));
    }
}
