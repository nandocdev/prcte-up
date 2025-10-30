<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WorkOfExtension;
use App\Models\WorkType;
use App\Models\WorkStatus;
use App\Models\OrganizationalUnit;
use App\Models\InstitutionalProjectType;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

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

        // Estadísticas de catálogos
        $catalogStats = [
            'work_types' => [
                'total' => WorkType::count(),
                'active' => WorkType::where('is_active', true)->count(),
                'with_works' => WorkType::has('workOfExtensions')->count()
            ],
            'work_statuses' => [
                'total' => WorkStatus::count(),
                'active' => WorkStatus::where('is_active', true)->count(),
                'final_states' => WorkStatus::where('is_final', true)->count()
            ],
            'organizational_units' => [
                'total' => OrganizationalUnit::count(),
                'active' => OrganizationalUnit::where('is_active', true)->count(),
                'with_users' => OrganizationalUnit::has('users')->count()
            ],
            'institutional_project_types' => [
                'total' => InstitutionalProjectType::count(),
                'active' => InstitutionalProjectType::where('is_active', true)->count(),
                'with_projects' => InstitutionalProjectType::has('projectDetails')->count()
            ]
        ];

        // Distribución de trabajos por estado
        $worksByStatus = WorkStatus::withCount('workOfExtensions')
            ->orderBy('sort_order')
            ->get();

        // Usuarios por rol
        $usersByRole = Role::withCount('users')->get();

        // Trabajos recientes
        $recentWorks = WorkOfExtension::with(['primaryResponsibleUser', 'organizationalUnit', 'currentStatus'])
            ->latest()
            ->take(5)
            ->get();

        // Usuarios recientes
        $recentUsers = User::with('roles')
            ->latest()
            ->take(5)
            ->get();

        // Actividad reciente del sistema
        $systemActivity = [
            'recent_users_count' => User::where('created_at', '>=', now()->subDays(7))->count(),
            'recent_works_count' => WorkOfExtension::where('created_at', '>=', now()->subDays(7))->count(),
            'pending_works' => WorkOfExtension::whereHas('currentStatus', function ($query) {
                $query->where('name', 'LIKE', '%pendiente%')
                    ->orWhere('name', 'LIKE', '%revision%');
            })->count()
        ];

        return view('admin.dashboard.index', compact(
            'stats',
            'catalogStats',
            'worksByStatus',
            'usersByRole',
            'recentWorks',
            'recentUsers',
            'systemActivity'
        ));
    }
}
