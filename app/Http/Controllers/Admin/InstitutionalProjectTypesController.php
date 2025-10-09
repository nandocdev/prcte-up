<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreInstitutionalProjectTypeRequest;
use App\Http\Requests\Admin\UpdateInstitutionalProjectTypeRequest;
use App\Models\InstitutionalProjectType;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Http\RedirectResponse;

class InstitutionalProjectTypesController extends Controller
{
    public function index(): ViewContract
    {
        $types = InstitutionalProjectType::query()
            ->withCount('projectDetails')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.institutional-project-types.index', [
            'types' => $types,
        ]);
    }

    public function create(): ViewContract
    {
        return view('admin.institutional-project-types.create');
    }

    public function store(StoreInstitutionalProjectTypeRequest $request): RedirectResponse
    {
        InstitutionalProjectType::create($request->validated());

        return redirect()
            ->route('admin.institutional-project-types.index')
            ->with('success', __('Tipo de proyecto institucional creado exitosamente.'));
    }

    public function show(InstitutionalProjectType $institutionalProjectType): ViewContract
    {
        $institutionalProjectType->loadCount('projectDetails');

        return view('admin.institutional-project-types.show', [
            'institutionalProjectType' => $institutionalProjectType,
        ]);
    }

    public function edit(InstitutionalProjectType $institutionalProjectType): ViewContract
    {
        return view('admin.institutional-project-types.edit', [
            'institutionalProjectType' => $institutionalProjectType,
        ]);
    }

    public function update(UpdateInstitutionalProjectTypeRequest $request, InstitutionalProjectType $institutionalProjectType): RedirectResponse
    {
        $institutionalProjectType->update($request->validated());

        return redirect()
            ->route('admin.institutional-project-types.index')
            ->with('success', __('Tipo de proyecto institucional actualizado exitosamente.'));
    }

    public function destroy(InstitutionalProjectType $institutionalProjectType): RedirectResponse
    {
        if ($institutionalProjectType->hasAssociatedProjects()) {
            return redirect()
                ->route('admin.institutional-project-types.index')
                ->with('error', __('No se puede eliminar un tipo institucional con proyectos asociados.'));
        }

        $institutionalProjectType->delete();

        return redirect()
            ->route('admin.institutional-project-types.index')
            ->with('success', __('Tipo de proyecto institucional eliminado exitosamente.'));
    }
}
