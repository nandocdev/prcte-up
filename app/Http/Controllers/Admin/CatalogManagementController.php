<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkType;
use App\Models\WorkStatus;
use App\Models\OrganizationalUnit;
use App\Models\InstitutionalProjectType;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CatalogManagementController extends Controller
{
    public function index(): View
    {
        $statistics = [
            'work_types' => WorkType::count(),
            'work_statuses' => WorkStatus::count(),
            'organizational_units' => OrganizationalUnit::count(),
            'institutional_project_types' => InstitutionalProjectType::count(),
        ];

        // Obtener datos recientes para cada catálogo
        $recentWorkTypes = WorkType::latest()->take(5)->get();
        $recentWorkStatuses = WorkStatus::latest()->take(5)->get();
        $recentOrgUnits = OrganizationalUnit::with('parent')->latest()->take(5)->get();
        $recentProjectTypes = InstitutionalProjectType::latest()->take(5)->get();

        return view('admin.catalogs.index', compact(
            'statistics',
            'recentWorkTypes',
            'recentWorkStatuses',
            'recentOrgUnits',
            'recentProjectTypes'
        ));
    }

    public function workTypes(): View
    {
        $workTypes = WorkType::withCount('works')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.catalogs.work-types.index', compact('workTypes'));
    }

    public function createWorkType(): View
    {
        return view('admin.catalogs.work-types.create');
    }

    public function storeWorkType(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:work_types,name',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean'
        ]);

        $workType = WorkType::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true)
        ]);

        // Limpiar caché relacionado
        Cache::forget('work_types_active');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Tipo de trabajo creado exitosamente.',
                'data' => $workType
            ]);
        }

        return redirect()->route('admin.catalogs.work-types')
            ->with('success', 'Tipo de trabajo creado exitosamente.');
    }

    public function editWorkType(WorkType $workType): View
    {
        $workType->loadCount('works');
        return view('admin.catalogs.work-types.edit', compact('workType'));
    }

    public function updateWorkType(Request $request, WorkType $workType)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:work_types,name,' . $workType->id,
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean'
        ]);

        $workType->update([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true)
        ]);

        Cache::forget('work_types_active');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Tipo de trabajo actualizado exitosamente.',
                'data' => $workType
            ]);
        }

        return redirect()->route('admin.catalogs.work-types')
            ->with('success', 'Tipo de trabajo actualizado exitosamente.');
    }

    public function showWorkType(WorkType $workType): JsonResponse
    {
        $workType->loadCount('works');
        
        return response()->json([
            'id' => $workType->id,
            'name' => $workType->name,
            'description' => $workType->description,
            'is_active' => $workType->is_active,
            'work_of_extensions_count' => $workType->works_count,
            'created_at' => $workType->created_at,
            'updated_at' => $workType->updated_at
        ]);
    }

    public function destroyWorkType(WorkType $workType): JsonResponse
    {
        // Verificar si está en uso
        $usageCount = $workType->works()->count();
        if ($usageCount > 0) {
            return response()->json([
                'success' => false,
                'message' => "No se puede eliminar. Está siendo usado por {$usageCount} trabajo(s) de extensión."
            ], 422);
        }

        $workType->delete();
        Cache::forget('work_types_active');

        return response()->json([
            'success' => true,
            'message' => 'Tipo de trabajo eliminado exitosamente.'
        ]);
    }

    public function workStatuses(): View
    {
        $workStatuses = WorkStatus::with(['transitionsFrom', 'transitionsTo'])
            ->orderBy('sort_order')
            ->paginate(20);

        return view('admin.catalogs.work-statuses.index', compact('workStatuses'));
    }

    public function storeWorkStatus(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:work_statuses,name',
            'description' => 'nullable|string|max:1000',
            'color' => 'required|string|max:7',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'is_final' => 'boolean'
        ]);

        $workStatus = WorkStatus::create([
            'name' => $request->name,
            'description' => $request->description,
            'color' => $request->color,
            'sort_order' => $request->sort_order,
            'is_active' => $request->boolean('is_active', true),
            'is_final' => $request->boolean('is_final', false)
        ]);

        Cache::forget('work_statuses_active');

        return response()->json([
            'success' => true,
            'message' => 'Estado de trabajo creado exitosamente.',
            'data' => $workStatus
        ]);
    }

    public function updateWorkStatus(Request $request, WorkStatus $workStatus): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:work_statuses,name,' . $workStatus->id,
            'description' => 'nullable|string|max:1000',
            'color' => 'required|string|max:7',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'is_final' => 'boolean'
        ]);

        $workStatus->update([
            'name' => $request->name,
            'description' => $request->description,
            'color' => $request->color,
            'sort_order' => $request->sort_order,
            'is_active' => $request->boolean('is_active'),
            'is_final' => $request->boolean('is_final')
        ]);

        Cache::forget('work_statuses_active');

        return response()->json([
            'success' => true,
            'message' => 'Estado de trabajo actualizado exitosamente.',
            'data' => $workStatus
        ]);
    }

    public function updateWorkStatusOrder(Request $request): JsonResponse
    {
        $request->validate([
            'order' => 'required|array',
            'order.*.id' => 'required|integer|exists:work_statuses,id',
            'order.*.order' => 'required|integer|min:1'
        ]);

        try {
            DB::transaction(function () use ($request) {
                foreach ($request->order as $item) {
                    WorkStatus::where('id', $item['id'])
                        ->update(['sort_order' => $item['order']]);
                }
            });

            Cache::forget('work_statuses_active');

            return response()->json([
                'success' => true,
                'message' => __('Orden actualizado exitosamente')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error al actualizar el orden: ') . $e->getMessage()
            ], 500);
        }
    }

    public function organizationalUnits(): View
    {
        $units = OrganizationalUnit::with(['parent', 'children', 'users'])
            ->orderBy('name')
            ->paginate(20);

        // Obtener estructura jerárquica para el selector de padre
        $parentOptions = OrganizationalUnit::getHierarchicalList();

        return view('admin.catalogs.organizational-units.index', compact('units', 'parentOptions'));
    }

    public function storeOrganizationalUnit(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:organizational_units,code',
            'type' => 'required|string|in:universidad,facultad,centro,departamento,escuela,instituto',
            'parent_id' => 'nullable|exists:organizational_units,id',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean'
        ]);

        $unit = OrganizationalUnit::create([
            'name' => $request->name,
            'code' => $request->code,
            'type' => $request->type,
            'parent_id' => $request->parent_id,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true)
        ]);

        Cache::forget('organizational_units_hierarchy');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Unidad organizacional creada exitosamente.',
                'data' => $unit->load('parent')
            ]);
        }

        return redirect()->route('admin.catalogs.organizational-units.show', $unit)
            ->with('success', 'Unidad organizacional creada exitosamente.');
    }

    public function updateOrganizationalUnit(Request $request, OrganizationalUnit $unit)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:organizational_units,code,' . $unit->id,
            'type' => 'required|string|in:universidad,facultad,centro,departamento,escuela,instituto',
            'parent_id' => 'nullable|exists:organizational_units,id',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean'
        ]);

        // Verificar que no se asigne como padre a sí mismo o a un descendiente
        if ($request->parent_id) {
            $descendantIds = $unit->getDescendantIds();
            if ($request->parent_id === $unit->id || in_array($request->parent_id, $descendantIds)) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No se puede asignar como padre a sí mismo o a un descendiente.'
                    ], 422);
                }
                return back()->withErrors(['parent_id' => 'No se puede asignar como padre a sí mismo o a un descendiente.']);
            }
        }

        $unit->update([
            'name' => $request->name,
            'code' => $request->code,
            'type' => $request->type,
            'parent_id' => $request->parent_id,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active')
        ]);

        Cache::forget('organizational_units_hierarchy');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Unidad organizacional actualizada exitosamente.',
                'data' => $unit->load('parent')
            ]);
        }

        return redirect()->route('admin.catalogs.organizational-units.show', $unit)
            ->with('success', 'Unidad organizacional actualizada exitosamente.');
    }

    public function showOrganizationalUnit(OrganizationalUnit $unit): View
    {
        $unit->load(['parent', 'children.children', 'users']);
        
        // Obtener la jerarquía completa hacia arriba
        $breadcrumbs = [];
        $current = $unit;
        
        while ($current) {
            array_unshift($breadcrumbs, $current);
            $current = $current->parent;
        }
        
        return view('admin.catalogs.organizational-units.show', compact('unit', 'breadcrumbs'));
    }

    public function createOrganizationalUnit(): View
    {
        $parentOptions = OrganizationalUnit::getHierarchicalList();
        
        return view('admin.catalogs.organizational-units.create', compact('parentOptions'));
    }

    public function editOrganizationalUnit(OrganizationalUnit $unit): View
    {
        $parentOptions = OrganizationalUnit::getHierarchicalList();
        $unit->load(['parent', 'children', 'users']);
        
        return view('admin.catalogs.organizational-units.edit', compact('unit', 'parentOptions'));
    }

    public function destroyOrganizationalUnit(OrganizationalUnit $unit)
    {
        // Verificar si tiene hijos
        if ($unit->children()->exists()) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar una unidad que tiene subunidades.'
                ], 422);
            }
            return back()->withErrors(['delete' => 'No se puede eliminar una unidad que tiene subunidades.']);
        }

        // Verificar si tiene usuarios
        if ($unit->users()->exists()) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar una unidad que tiene usuarios asignados.'
                ], 422);
            }
            return back()->withErrors(['delete' => 'No se puede eliminar una unidad que tiene usuarios asignados.']);
        }

        $unit->delete();
        Cache::forget('organizational_units_hierarchy');

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Unidad organizacional eliminada exitosamente.'
            ]);
        }

        return redirect()->route('admin.catalogs.organizational-units')
            ->with('success', 'Unidad organizacional eliminada exitosamente.');
    }

    public function institutionalProjectTypes(): View
    {
        $projectTypes = InstitutionalProjectType::withCount('projectDetails')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.catalogs.institutional-project-types.index', compact('projectTypes'));
    }

    public function storeInstitutionalProjectType(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:inst_project_types,name',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean'
        ]);

        $projectType = InstitutionalProjectType::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true)
        ]);

        Cache::forget('institutional_project_types_active');

        return response()->json([
            'success' => true,
            'message' => 'Tipo de proyecto institucional creado exitosamente.',
            'data' => $projectType
        ]);
    }

    public function updateInstitutionalProjectType(Request $request, InstitutionalProjectType $institutionalProjectType): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:inst_project_types,name,' . $institutionalProjectType->id,
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean'
        ]);

        $institutionalProjectType->update([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active')
        ]);

        Cache::forget('institutional_project_types_active');

        return response()->json([
            'success' => true,
            'message' => 'Tipo de proyecto institucional actualizado exitosamente.',
            'data' => $institutionalProjectType
        ]);
    }

    public function destroyInstitutionalProjectType(InstitutionalProjectType $institutionalProjectType): JsonResponse
    {
        // Verificar si está en uso
        if ($institutionalProjectType->projectDetails()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar. Está siendo usado por uno o más proyectos.'
            ], 422);
        }

        $institutionalProjectType->delete();
        Cache::forget('institutional_project_types_active');

        return response()->json([
            'success' => true,
            'message' => 'Tipo de proyecto institucional eliminado exitosamente.'
        ]);
    }

    public function bulkUpdate(Request $request): JsonResponse
    {
        $request->validate([
            'catalog_type' => 'required|string|in:work_types,work_statuses,organizational_units,institutional_project_types',
            'action' => 'required|string|in:activate,deactivate,delete',
            'ids' => 'required|array',
            'ids.*' => 'integer'
        ]);

        $catalogType = $request->catalog_type;
        $action = $request->action;
        $ids = $request->ids;

        $model = $this->getModelClass($catalogType);
        $items = $model::whereIn('id', $ids)->get();

        $successCount = 0;
        $errors = [];

        foreach ($items as $item) {
            try {
                switch ($action) {
                    case 'activate':
                        $item->update(['is_active' => true]);
                        $successCount++;
                        break;
                    case 'deactivate':
                        $item->update(['is_active' => false]);
                        $successCount++;
                        break;
                    case 'delete':
                        // Verificar dependencias antes de eliminar
                        if ($this->canDelete($item)) {
                            $item->delete();
                            $successCount++;
                        } else {
                            $errors[] = "No se puede eliminar '{$item->name}' porque está en uso.";
                        }
                        break;
                }
            } catch (\Exception $e) {
                $errors[] = "Error con '{$item->name}': " . $e->getMessage();
            }
        }

        // Limpiar caché relevante
        $this->clearRelevantCache($catalogType);

        return response()->json([
            'success' => true,
            'message' => "Operación completada para {$successCount} elemento(s).",
            'errors' => $errors
        ]);
    }

    private function getModelClass(string $catalogType): string
    {
        return match($catalogType) {
            'work_types' => WorkType::class,
            'work_statuses' => WorkStatus::class,
            'organizational_units' => OrganizationalUnit::class,
            'institutional_project_types' => InstitutionalProjectType::class,
            default => throw new \InvalidArgumentException("Invalid catalog type: {$catalogType}")
        };
    }

    private function canDelete($item): bool
    {
        // Verificar dependencias específicas por tipo
        if ($item instanceof WorkType) {
            return $item->workOfExtensions()->count() === 0;
        }
        
        if ($item instanceof WorkStatus) {
            return $item->workStatusHistories()->count() === 0;
        }
        
        if ($item instanceof OrganizationalUnit) {
            return $item->children()->count() === 0 && $item->users()->count() === 0;
        }
        
        if ($item instanceof InstitutionalProjectType) {
            return $item->projectDetails()->count() === 0;
        }

        return true;
    }

    private function clearRelevantCache(string $catalogType): void
    {
        switch ($catalogType) {
            case 'work_types':
                Cache::forget('work_types_active');
                break;
            case 'work_statuses':
                Cache::forget('work_statuses_active');
                break;
            case 'organizational_units':
                Cache::forget('organizational_units_hierarchy');
                break;
            case 'institutional_project_types':
                Cache::forget('institutional_project_types_active');
                break;
        }
    }

    public function exportCatalog(Request $request, string $catalogType)
    {
        $request->validate([
            'format' => 'required|string|in:csv,xlsx,json'
        ]);

        $model = $this->getModelClass($catalogType);
        $data = $model::all();

        $filename = $catalogType . '_' . date('Y-m-d') . '.' . $request->format;

        // TODO: Implementar exportación según formato
        // return Excel::download(new CatalogExport($data), $filename);

        return response()->json([
            'success' => true,
            'message' => 'Exportación preparada',
            'filename' => $filename
        ]);
    }
}