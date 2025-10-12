<?php

namespace App\Http\Controllers;

use App\Models\Certification;
use App\Models\User;
use App\Models\WorkOfExtension;
use App\Models\WorkStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
/**
 * Controlador para administradores VIEX
 *
 * Maneja los casos de uso:
 * - CU12: Recibir trabajos de Unidades Académicas
 * - CU13: Asignar a evaluadores (Comisión)
 * - CU14: Evaluar y emitir dictamen
 * - CU15: Registrar y emitir certificación
 */
class ViexAdminController extends Controller {

    /**
     * CU12: Listado de trabajos en VIEX - Recibir trabajos de Unidades Académicas
     */
    public function index(Request $request) {
        // Verificar autorización básica
        $user = Auth::user();
        if (!$user) {
            abort(403, 'Usuario no autenticado.');
        }

        try {
            $query = WorkOfExtension::with(['workType', 'primaryResponsible', 'organizationalUnit', 'currentStatus'])
                ->whereHas('currentStatus', function ($q) {
                    $q->whereIn('name', [
                        'Enviado a VIEX',
                        'En VIEX - Pendiente Asignación',
                        'En VIEX - En Evaluación',
                        'En VIEX - Aprobado',
                        'Certificado',
                        'Rechazado por VIEX'
                    ]);
                });

            // Aplicar filtros
            if ($request->filled('status')) {
                switch ($request->input('status')) {
                    case 'pending':
                        $query->whereHas('currentStatus', fn($q) => $q->whereIn('name', ['Enviado a VIEX', 'En VIEX - Pendiente Asignación']));
                        break;
                    case 'evaluation':
                        $query->whereHas('currentStatus', fn($q) => $q->where('name', 'En VIEX - En Evaluación'));
                        break;
                    case 'approved':
                        $query->whereHas('currentStatus', fn($q) => $q->where('name', 'En VIEX - Aprobado'));
                        break;
                    case 'certified':
                        $query->whereHas('currentStatus', fn($q) => $q->where('name', 'Certificado'));
                        break;
                    case 'rejected':
                        $query->whereHas('currentStatus', fn($q) => $q->where('name', 'Rechazado por VIEX'));
                        break;
                }
            }

            if ($request->filled('work_type')) {
                $query->where('work_type_id', $request->input('work_type'));
            }

            if ($request->filled('unit')) {
                $query->where('organizational_unit_id', $request->input('unit'));
            }

            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', '%' . $search . '%')
                        ->orWhereHas('primaryResponsible', function ($subQ) use ($search) {
                            $subQ->where('name', 'like', '%' . $search . '%');
                        });
                });
            }

            $works = $query->orderBy('updated_at', 'desc')->paginate(20);

            // Datos para filtros
            $workTypes = \App\Models\WorkType::all();
            $organizationalUnits = \App\Models\OrganizationalUnit::all();
            $evaluators = User::whereHas('roles', function ($q) {
                $q->where('name', 'viex_admin');
            })->where('is_active', true)->get();

            return view('viex.index', compact('works', 'workTypes', 'organizationalUnits', 'evaluators'));

        } catch (\Exception $e) {
            Log::error('Error en VIEX index', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return back()->withErrors(['error' => 'Error al cargar la lista de trabajos.']);
        }
    }

    /**
     * CU12: Dashboard principal de VIEX - Recibir trabajos de Unidades Académicas
     *
     * Muestra todos los trabajos que llegan a VIEX desde las unidades académicas
     * para su evaluación final y certificación.
     */
    public function dashboard() {
        // Verificar autorización
        $user = Auth::user();
        if (!$user) {
            abort(403, 'Usuario no autenticado.');
        }

        try {
            // Obtener trabajos en estado "Enviado a VIEX"
            $pendingWorks = WorkOfExtension::with(['workType', 'primaryResponsible', 'organizationalUnit', 'currentStatus'])
                ->whereHas('currentStatus', function ($query) {
                    $query->where('name', 'Enviado a VIEX');
                })
                ->orderBy('updated_at', 'asc')
                ->get();

            // Obtener trabajos en evaluación
            $evaluationWorks = WorkOfExtension::with(['workType', 'primaryResponsible', 'organizationalUnit', 'currentStatus'])
                ->whereHas('currentStatus', function ($query) {
                    $query->where('name', 'En Evaluación VIEX');
                })
                ->orderBy('updated_at', 'asc')
                ->get();

            // Obtener trabajos certificados recientemente
            $recentCertifications = WorkOfExtension::with(['workType', 'primaryResponsible', 'organizationalUnit', 'currentStatus'])
                ->whereHas('currentStatus', function ($query) {
                    $query->where('name', 'Certificado');
                })
                ->orderBy('updated_at', 'desc')
                ->take(10)
                ->get();

            // Obtener trabajos recientes en VIEX (para el dashboard general)
            $recentWorks = WorkOfExtension::with(['workType', 'primaryResponsible', 'organizationalUnit', 'currentStatus'])
                ->whereHas('currentStatus', function ($query) {
                    $query->whereIn('name', [
                        'Enviado a VIEX',
                        'En VIEX - Pendiente Asignación',
                        'En VIEX - En Evaluación',
                        'En VIEX - Aprobado',
                        'Certificado',
                        'Rechazado por VIEX'
                    ]);
                })
                ->orderBy('updated_at', 'desc')
                ->take(15)
                ->get();

            // Estadísticas para el dashboard
            $statistics = [
                'pending_review' => $pendingWorks->count(),
                'in_evaluation' => $evaluationWorks->count(),
                'certified_this_month' => WorkOfExtension::whereHas('currentStatus', function ($query) {
                    $query->where('name', 'Certificado');
                })->whereMonth('updated_at', now()->month)->count(),
                'rejected_this_month' => WorkOfExtension::whereHas('currentStatus', function ($query) {
                    $query->where('name', 'Rechazado por VIEX');
                })->whereMonth('updated_at', now()->month)->count(),
                'total_received' => WorkOfExtension::whereHas('statusHistory', function ($query) {
                    $query->whereHas('status', function ($subQuery) {
                        $subQuery->where('name', 'Enviado a VIEX');
                    });
                })->count(),
            ];

            // Obtener conteo de evaluadores activos (usuarios con rol viex_admin)
            $evaluatorCount = User::role('viex_admin')->where('is_active', true)->count();

            Log::info('VIEX Admin Dashboard cargado', [
                'user_id' => Auth::id(),
                'statistics' => $statistics,
                'pending_works' => $pendingWorks->count(),
                'evaluation_works' => $evaluationWorks->count()
            ]);

            return view('viex.dashboard', compact(
                'pendingWorks',
                'evaluationWorks',
                'recentCertifications',
                'recentWorks',
                'statistics',
                'evaluatorCount'
            ));

        } catch (\Exception $e) {
            Log::error('Error en VIEX Admin Dashboard', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return back()->withErrors(['error' => 'Error al cargar el dashboard de VIEX.']);
        }
    }

    /**
     * CU12/CU14: Mostrar detalles del trabajo para evaluación
     *
     * Vista detallada del trabajo que permite asignar evaluadores,
     * evaluar y emitir dictamen.
     */
    public function show(WorkOfExtension $work) {
        // Verificación básica de autorización - se asume que middleware ya verificó permisos VIEX
        if (!Auth::user()) {
            abort(403, 'No tiene permisos para acceder a este trabajo.');
        }

        try {
            // Cargar relaciones necesarias
            $work->load([
                'workType',
                'primaryResponsible',
                'organizationalUnit',
                'currentStatus',
                'statusHistory.status',
                'statusHistory.changedBy'
            ]);

            // Obtener evaluadores disponibles (usuarios con rol viex_admin)
            $availableEvaluators = User::role('viex_admin')
                ->where('id', '!=', Auth::id())
                ->where('is_active', true)
                ->get();

            // Verificar si ya tiene evaluador asignado (buscar en comentarios del historial)
            $assignedEvaluator = null;
            $evaluationHistory = $work->statusHistory()
                ->whereHas('status', function ($query) {
                    $query->where('name', 'En Evaluación VIEX');
                })
                ->first();

            if ($evaluationHistory && $evaluationHistory->changedBy) {
                $assignedEvaluator = $evaluationHistory->changedBy;
            }

            Log::info('VIEX Admin - Vista trabajo', [
                'work_id' => $work->getAttribute('id'),
                'work_title' => $work->getAttribute('title'),
                'current_status' => $work->currentStatus->getAttribute('name'),
                'user_id' => Auth::id()
            ]);

            return view('viex.show', compact(
                'work',
                'availableEvaluators',
                'assignedEvaluator'
            ));

        } catch (\Exception $e) {
            Log::error('Error al mostrar trabajo en VIEX', [
                'work_id' => $work->getAttribute('id'),
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return back()->withErrors(['error' => 'Error al cargar el trabajo.']);
        }
    }

    /**
     * CU13: Asignar a evaluadores (Comisión)
     *
     * Asigna un evaluador específico al trabajo y cambia su estado
     * a "En Evaluación VIEX".
     */
    public function assignEvaluator(Request $request, WorkOfExtension $work) {
        $user = Auth::user();
        if (!$user) {
            abort(403, 'Usuario no autenticado.');
        }

        $request->validate([
            'evaluator_id' => 'required|exists:users,id',
            'evaluation_instructions' => 'nullable|string|max:1000',
        ], [
            'evaluator_id.required' => 'Debe seleccionar un evaluador.',
            'evaluator_id.exists' => 'El evaluador seleccionado no es válido.',
        ]);

        try {
            DB::beginTransaction();

            // Verificar que el trabajo esté en estado correcto
            $currentStatus = $work->currentStatus;
            if (!$currentStatus || !in_array($currentStatus->getAttribute('name'), ['Enviado a VIEX', 'En VIEX - Pendiente Asignación'])) {
                return back()->withErrors(['error' => 'El trabajo no está en el estado correcto para asignar evaluador.']);
            }

            $evaluatorId = $request->input('evaluator_id');
            $evaluator = User::findOrFail($evaluatorId);

            // Verificar que el evaluador tenga el rol correcto
            $hasRole = $evaluator->roles()->whereIn('name', ['viex_admin', 'super_admin'])->exists();
            if (!$hasRole) {
                return back()->withErrors(['error' => 'El usuario seleccionado no tiene permisos de evaluador.']);
            }

            // Usar método del modelo para asignar evaluador
            $instructions = $request->input('evaluation_instructions');
            $work->assignToEvaluator($evaluator, $user, $instructions);

            DB::commit();

            Log::info('Evaluador asignado exitosamente', [
                'work_id' => $work->getAttribute('id'),
                'evaluator_id' => $evaluator->getAttribute('id'),
                'assigned_by' => Auth::id(),
                'instructions' => $instructions
            ]);

            return back()->with('success', "Trabajo asignado exitosamente a {$evaluator->getAttribute('name')} para evaluación.");

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al asignar evaluador', [
                'work_id' => $work->getAttribute('id'),
                'evaluator_id' => $request->input('evaluator_id'),
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return back()->withErrors(['error' => 'Error al asignar evaluador: ' . $e->getMessage()]);
        }
    }

    /**
     * CU14: Evaluar y emitir dictamen - Aprobar trabajo
     *
     * Evalúa positivamente el trabajo y lo marca para certificación.
     */
    public function approve(Request $request, WorkOfExtension $work) {
        $user = Auth::user();
        if (!$user) {
            abort(403, 'Usuario no autenticado.');
        }

        $request->validate([
            'evaluation_comments' => 'nullable|string|max:2000',
            'recommendations' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            // Usar método del modelo para aprobar
            $evaluationComments = $request->input('evaluation_comments');
            $recommendations = $request->input('recommendations');
            $work->approveByViex($user, $evaluationComments, $recommendations);

            DB::commit();

            Log::info('Trabajo aprobado por VIEX', [
                'work_id' => $work->getAttribute('id'),
                'evaluator_id' => Auth::id(),
                'comments' => $evaluationComments
            ]);

            return redirect()->route('viex.show', $work)
                ->with('success', 'Trabajo evaluado y aprobado exitosamente. Listo para certificación.');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al aprobar trabajo en VIEX', [
                'work_id' => $work->getAttribute('id'),
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return back()->withErrors(['error' => 'Error al aprobar trabajo: ' . $e->getMessage()]);
        }
    }

    /**
     * CU14: Evaluar y emitir dictamen - Rechazar trabajo
     *
     * Evalúa negativamente el trabajo y lo rechaza con observaciones.
     */
    public function reject(Request $request, WorkOfExtension $work) {
        $user = Auth::user();
        if (!$user) {
            abort(403, 'Usuario no autenticado.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|min:10|max:2000',
            'recommendations' => 'nullable|string|max:1000',
        ], [
            'rejection_reason.required' => 'Debe proporcionar una razón para el rechazo.',
            'rejection_reason.min' => 'La razón del rechazo debe tener al menos 10 caracteres.',
            'rejection_reason.max' => 'La razón del rechazo no puede superar 2000 caracteres.',
        ]);

        try {
            DB::beginTransaction();

            // Usar método del modelo para rechazar
            $rejectionReason = $request->input('rejection_reason');
            $recommendations = $request->input('recommendations');
            $work->rejectByViex($user, $rejectionReason, $recommendations);

            DB::commit();

            Log::info('Trabajo rechazado por VIEX', [
                'work_id' => $work->getAttribute('id'),
                'evaluator_id' => Auth::id(),
                'reason' => $rejectionReason
            ]);

            return redirect()->route('viex.show', $work)
                ->with('success', 'Trabajo evaluado y rechazado. Se han enviado las observaciones correspondientes.');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al rechazar trabajo en VIEX', [
                'work_id' => $work->getAttribute('id'),
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return back()->withErrors(['error' => 'Error al rechazar trabajo: ' . $e->getMessage()]);
        }
    }

    /**
     * CU15: Registrar y emitir certificación
     *
     * Genera la certificación oficial del trabajo de extensión.
     */
    public function certify(Request $request, WorkOfExtension $work) {
        $user = Auth::user();
        if (!$user) {
            abort(403, 'Usuario no autenticado.');
        }

        $request->validate([
            'certification_number' => 'nullable|string|max:50',
            'certification_comments' => 'nullable|string|max:1000',
            'certificate_validity_years' => 'nullable|integer|min:1|max:10',
        ], [
            'certificate_validity_years.integer' => 'La vigencia debe ser un número entero.',
            'certificate_validity_years.min' => 'La vigencia mínima es de 1 año.',
            'certificate_validity_years.max' => 'La vigencia máxima es de 10 años.',
        ]);

        try {
            DB::beginTransaction();

            // Verificar que el trabajo esté aprobado
            $currentStatus = $work->currentStatus;
            if (!$currentStatus || $currentStatus->getAttribute('name') !== 'En VIEX - Aprobado') {
                return back()->withErrors(['error' => 'El trabajo debe estar aprobado antes de emitir la certificación oficial.']);
            }

            // Usar método del modelo para certificar
            $certificationNumber = $request->input('certification_number');
            $certificationComments = $request->input('certification_comments');
            $validityYears = $request->input('certificate_validity_years') ?? 5;

            $certification = $work->generateCertification(
                $user,
                $certificationNumber,
                $certificationComments,
                $validityYears
            );

            DB::commit();

            Log::info('Certificación generada exitosamente', [
                'work_id' => $work->getAttribute('id'),
                'certification_id' => $certification->getAttribute('id'),
                'certification_number' => $certificationNumber ?: 'auto-generado',
                'issued_by' => Auth::id()
            ]);

            return redirect()->route('viex.show', $work)
                ->with('success', "Certificación generada exitosamente.");

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al generar certificación', [
                'work_id' => $work->getAttribute('id'),
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return back()->withErrors(['error' => 'Error al generar certificación: ' . $e->getMessage()]);
        }
    }

    /**
     * CU13: Descargar certificación emitida
     */
    public function downloadCertificate(Certification $certification)
    {
        $user = Auth::user();
        if (!$user) {
            abort(403, __('certifications.unauthenticated'));
        }

        $work = $certification->work;

        if (!$work) {
            abort(404, __('certifications.work_not_found'));
        }

        $this->authorize('view', $work);

        try {
            return $certification->buildDownloadResponse();
        } catch (\RuntimeException $exception) {
            Log::warning('Archivo de certificación no disponible.', [
                'certification_id' => $certification->getKey(),
                'work_id' => $work->getKey(),
                'error' => $exception->getMessage(),
            ]);

            return back()->with('error', __('certifications.download_missing_file'));
        } catch (\Throwable $exception) {
            Log::error('Error inesperado al descargar certificación.', [
                'certification_id' => $certification->getKey(),
                'work_id' => $work->getKey(),
                'error' => $exception->getMessage(),
            ]);

            return back()->with('error', __('certifications.download_error'));
        }
    }

    /**
     * Vista para gestión de evaluadores y estadísticas avanzadas
     */
    public function evaluators() {
        // Verificación básica de autorización - se asume que middleware ya verificó permisos VIEX
        if (!Auth::user()) {
            abort(403, 'No tiene permisos para gestionar evaluadores.');
        }

        try {
            // Obtener evaluadores activos
            $evaluators = User::role('viex_admin')
                ->where('is_active', true)
                ->with(['roles'])
                ->get();

            // Estadísticas de evaluadores
            $evaluatorStats = [];
            foreach ($evaluators as $evaluator) {
                $assigned = WorkOfExtension::whereHas('statusHistory', function ($query) use ($evaluator) {
                    $query->where('changed_by_user_id', $evaluator->id)
                        ->whereHas('status', function ($subQuery) {
                            $subQuery->where('name', 'En Evaluación VIEX');
                        });
                })->count();

                $completed = WorkOfExtension::whereHas('statusHistory', function ($query) use ($evaluator) {
                    $query->where('changed_by_user_id', $evaluator->id)
                        ->whereHas('status', function ($subQuery) {
                            $subQuery->whereIn('name', ['Certificado', 'Rechazado por VIEX']);
                        });
                })->count();

                $evaluatorStats[$evaluator->id] = [
                    'assigned' => $assigned,
                    'completed' => $completed,
                    'pending' => $assigned - $completed
                ];
            }

            return view('viex.evaluators', compact('evaluators', 'evaluatorStats'));

        } catch (\Exception $e) {
            Log::error('Error al cargar evaluadores', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return back()->withErrors(['error' => 'Error al cargar la gestión de evaluadores.']);
        }
    }

    /**
     * CU16: Generar reporte detallado del trabajo
     */
    public function generateReport(WorkOfExtension $work)
    {
        $user = Auth::user();
        if (!$user) {
            abort(403, 'Usuario no autenticado.');
        }

        // Verificar autorización
        $this->authorize('view', $work);

        // Verificar que se puede generar reporte
        if (!$work->canGenerateReport()) {
            return back()->withErrors(['error' => 'No se puede generar reporte para este trabajo en su estado actual.']);
        }

        try {
            // Cargar relaciones necesarias para el reporte
            $work->load([
                'workType',
                'primaryResponsible',
                'organizationalUnit',
                'currentStatus',
                'statusHistory.status',
                'statusHistory.changedBy',
                'participants',
                'certification.issuedByUser',
                'projectDetail',
                'activityDetail',
                'publicationDetail',
                'technicalAssistanceDetail',
            ]);

            // Generar PDF del reporte
            $pdf = app('dompdf.wrapper');
            $pdf->loadView('reports.pdf', [
                'work' => $work,
            ]);
            $pdf->setPaper('a4');

            $fileName = sprintf('reporte-trabajo-%s.pdf', Str::slug($work->title, '_'));

            Log::info('Reporte generado exitosamente', [
                'work_id' => $work->getKey(),
                'generated_by' => Auth::id(),
                'file_name' => $fileName
            ]);

            return $pdf->download($fileName);
        } catch (\Throwable $exception) {
            Log::error('Error al generar reporte del trabajo', [
                'work_id' => $work->getKey(),
                'error' => $exception->getMessage(),
                'user_id' => Auth::id()
            ]);

            return back()->withErrors(['error' => 'Error al generar reporte: ' . $exception->getMessage()]);
        }
    }
}
