<?php

use App\Http\Controllers\CoordinatorController;
use App\Http\Controllers\DeanDirectorController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ViexAdminController;
use App\Http\Controllers\WorkOfExtensionController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\RoleManagementController;
use App\Http\Controllers\Admin\RoleAssignmentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Página de información para testing
Route::get('/testing-info', function () {
    return view('testing-info');
})->name('testing.info');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rutas de Notificaciones
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-as-read');

    // Rutas para gestión de trabajos de extensión

    // Rutas para gestión de trabajos de extensión
    Route::resource('works', WorkOfExtensionController::class);

    // Ruta adicional para envío a coordinador (CU04)
    Route::patch('works/{work}/submit', [WorkOfExtensionController::class, 'submit'])
        ->name('works.submit');

    // Ruta adicional para reenvío después de corrección (CU05)
    Route::patch('works/{work}/resubmit', [WorkOfExtensionController::class, 'resubmit'])
        ->name('works.resubmit');

    // Rutas para Coordinador de Extensión (CU06, CU07, CU08)
    // Usamos auth middleware y validación en el controlador para mejor control
    Route::middleware(['auth'])->group(function () {
        Route::get('/coordinator', [CoordinatorController::class, 'dashboard'])->name('coordinator.dashboard');
        Route::get('/coordinator/works/{work}', [CoordinatorController::class, 'show'])->name('coordinator.show');
        Route::post('/coordinator/works/{work}/approve', [CoordinatorController::class, 'approve'])->name('coordinator.approve');
        Route::post('/coordinator/works/{work}/request-changes', [CoordinatorController::class, 'requestChanges'])->name('coordinator.request-changes');
        Route::post('/coordinator/works/{work}/reject', [CoordinatorController::class, 'reject'])->name('coordinator.reject');
    });

    // Rutas para Decano/Director (CU10, CU11)
    // Usamos auth middleware y validación en el controlador para mejor control
    Route::middleware(['auth'])->group(function () {
        Route::get('/dean', [DeanDirectorController::class, 'dashboard'])->name('dean.dashboard');
        Route::get('/dean/works/{work}', [DeanDirectorController::class, 'show'])->name('dean.show');
        Route::post('/dean/works/{work}/approve', [DeanDirectorController::class, 'approve'])->name('dean.approve');
        Route::post('/dean/works/{work}/request-changes', [DeanDirectorController::class, 'requestChanges'])->name('dean.request-changes');
        Route::post('/dean/works/{work}/reject', [DeanDirectorController::class, 'reject'])->name('dean.reject');
    });

    // Rutas para VIEX Admin (CU12, CU13, CU14, CU15)
    // Usamos auth middleware y validación en el controlador para mejor control
    Route::middleware(['auth'])->prefix('viex')->name('viex.')->group(function () {
        Route::get('/', [ViexAdminController::class, 'index'])->name('index');
        Route::get('/dashboard', [ViexAdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/works/{work}', [ViexAdminController::class, 'show'])->name('show');
        Route::post('/works/{work}/assign-evaluator', [ViexAdminController::class, 'assignEvaluator'])->name('assign-evaluator');
        Route::post('/works/{work}/approve', [ViexAdminController::class, 'approve'])->name('approve');
        Route::post('/works/{work}/reject', [ViexAdminController::class, 'reject'])->name('reject');
        Route::post('/works/{work}/certify', [ViexAdminController::class, 'certify'])->name('certify');

        // Rutas adicionales para reportes y certificados
        Route::get('/works/{work}/report', [ViexAdminController::class, 'generateReport'])->name('report');
        Route::get('/certificates/{certification}/download', [ViexAdminController::class, 'downloadCertificate'])->name('certificate.download');
    });

    // Rutas públicas para descargas de certificados (accesible por profesores)
    Route::get('/certificates/{certification}/download-public', [WorkOfExtensionController::class, 'downloadCertificate'])
        ->name('certificates.download')
        ->middleware('auth');

    // Rutas de Administración (solo para super_admin)
    Route::middleware(['auth', 'role:super_admin'])->prefix('admin')->name('admin.')->group(function () {
        // Gestión de Usuarios
        Route::resource('users', UserManagementController::class);
        Route::post('/users/{user}/assign-role', [UserManagementController::class, 'assignRole'])->name('users.assign-role');
        Route::delete('/users/{user}/remove-role', [UserManagementController::class, 'removeRole'])->name('users.remove-role');
        Route::post('/users/{user}/give-permission', [UserManagementController::class, 'givePermission'])->name('users.give-permission');
        Route::delete('/users/{user}/revoke-permission', [UserManagementController::class, 'revokePermission'])->name('users.revoke-permission');

        // Gestión de Roles
        Route::resource('roles', RoleManagementController::class);

        // Dashboard Avanzado de Asignación de Roles
        Route::get('/role-assignment', [RoleAssignmentController::class, 'index'])->name('role-assignment.index');
        Route::get('/users/{user}/roles', [RoleAssignmentController::class, 'getUserRoles'])->name('users.roles');
        Route::post('/users/{user}/sync-roles', [RoleAssignmentController::class, 'syncUserRoles'])->name('users.sync-roles');
        Route::post('/role-assignment/mass-assign', [RoleAssignmentController::class, 'massAssign'])->name('role-assignment.mass-assign');
        Route::get('/role-assignment/export', [RoleAssignmentController::class, 'export'])->name('role-assignment.export');

        // Configuración del Sistema - CRUDs
        Route::resource('organizational-units', \App\Http\Controllers\Admin\OrganizationalUnitsController::class);
        Route::resource('work-types', \App\Http\Controllers\Admin\WorkTypesController::class);
        Route::resource('work-statuses', \App\Http\Controllers\Admin\WorkStatusesController::class);
        Route::resource('institutional-project-types', \App\Http\Controllers\Admin\InstitutionalProjectTypesController::class);
        Route::resource('roles', \App\Http\Controllers\Admin\RolesController::class);
        Route::resource('permissions', \App\Http\Controllers\Admin\PermissionsController::class);
    });
});

require __DIR__ . '/auth.php';
