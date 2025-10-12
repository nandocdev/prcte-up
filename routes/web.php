<?php

use App\Http\Controllers\CoordinatorController;
use App\Http\Controllers\DeanDirectorController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ViexAdminController;
use App\Http\Controllers\ViexController;
use App\Http\Controllers\EvaluatorController;
use App\Http\Controllers\WorkOfExtensionController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\RoleManagementController;
use App\Http\Controllers\Admin\RoleAssignmentController;
use App\Http\Controllers\Admin\SystemReportsController;
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

    // Ruta adicional para autorización de publicación (CU06)
    Route::patch('works/{work}/authorize-publication', [WorkOfExtensionController::class, 'authorizePublication'])
        ->name('works.authorize-publication');

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

    // Rutas para VIEX - Nuevo Sistema de Evaluación (CU9)
    Route::middleware(['auth', 'role:viex_admin'])->prefix('viex-evaluation')->name('viex.')->group(function () {
        // Dashboard y listados
        Route::get('/', [ViexController::class, 'index'])->name('evaluation.index');
        Route::get('/works/{work}', [ViexController::class, 'show'])->name('evaluation.show');

        // Recibir trabajo en VIEX
        Route::post('/works/{work}/receive', [ViexController::class, 'receive'])->name('receive');

        // Asignación de evaluadores
        Route::get('/works/{work}/assign-evaluators', [ViexController::class, 'showAssignEvaluatorsForm'])->name('assign-evaluators');
        Route::post('/works/{work}/assign-evaluator', [ViexController::class, 'assignEvaluator'])->name('assign-evaluator');

        // Inicio de evaluación
        Route::post('/works/{work}/start-evaluation', [ViexController::class, 'startEvaluation'])->name('start-evaluation');

        // Revisión y decisión final
        Route::get('/works/{work}/review-evaluations', [ViexController::class, 'reviewEvaluations'])->name('review-evaluations');
        Route::post('/works/{work}/approve', [ViexController::class, 'approve'])->name('evaluation.approve');
        Route::post('/works/{work}/approve-and-certify', [ViexController::class, 'approveAndCertify'])->name('approve-and-certify');
        Route::post('/works/{work}/reject', [ViexController::class, 'reject'])->name('evaluation.reject');
        Route::post('/works/{work}/request-changes', [ViexController::class, 'requestChanges'])->name('request-changes');
    });

    // Rutas para Evaluadores (CU9)
    Route::middleware(['auth', 'role:evaluador'])->prefix('evaluator')->name('evaluator.')->group(function () {
        // Dashboard del evaluador
        Route::get('/', [EvaluatorController::class, 'index'])->name('index');

        // Ver trabajo asignado
        Route::get('/works/{work}', [EvaluatorController::class, 'show'])->name('show');

        // Aceptar/Rechazar asignación
        Route::post('/works/{work}/accept', [EvaluatorController::class, 'acceptAssignment'])->name('accept-assignment');
        Route::post('/works/{work}/decline', [EvaluatorController::class, 'declineAssignment'])->name('decline-assignment');

        // Formulario de evaluación
        Route::get('/works/{work}/evaluate', [EvaluatorController::class, 'evaluate'])->name('evaluate');
        Route::post('/works/{work}/submit-evaluation', [EvaluatorController::class, 'submitEvaluation'])->name('submit-evaluation');
    });

    // Rutas públicas para descargas de certificados (accesible por profesores)
    Route::get('/certificates/{certification}/download-public', [WorkOfExtensionController::class, 'downloadCertificate'])
        ->name('certificates.download')
        ->middleware('auth');

    // Rutas de Administración
    Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
        Route::middleware('role:super_admin')->group(function () {
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

            // Configuración exclusiva de Super Administrador
            Route::resource('organizational-units', \App\Http\Controllers\Admin\OrganizationalUnitsController::class);
            Route::resource('roles', \App\Http\Controllers\Admin\RolesController::class);
            Route::resource('permissions', \App\Http\Controllers\Admin\PermissionsController::class);
        });

        Route::middleware('role:super_admin|viex_admin')->group(function () {
            Route::resource('work-types', \App\Http\Controllers\Admin\WorkTypesController::class);
            Route::resource('work-statuses', \App\Http\Controllers\Admin\WorkStatusesController::class);
            Route::resource('institutional-project-types', \App\Http\Controllers\Admin\InstitutionalProjectTypesController::class);
            Route::post('evaluation-criteria/sync', [\App\Http\Controllers\Admin\EvaluationCriteriaController::class, 'sync'])
                ->name('evaluation-criteria.sync');
            Route::resource('evaluation-criteria', \App\Http\Controllers\Admin\EvaluationCriteriaController::class)
                ->parameters([
                    'evaluation-criteria' => 'evaluationCriteria',
                ]);

            Route::get('reports', [SystemReportsController::class, 'index'])->name('reports.index');
            Route::get('reports/{report}/download', [SystemReportsController::class, 'download'])->name('reports.download');
            Route::get('reports/{report}', [SystemReportsController::class, 'show'])->name('reports.show');
        });
    });
});

require __DIR__ . '/auth.php';
