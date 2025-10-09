<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreWorkTypeRequest;
use App\Http\Requests\Admin\UpdateWorkTypeRequest;
use App\Models\WorkType;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Http\RedirectResponse;

class WorkTypesController extends Controller
{
    public function index(): ViewContract
    {
        $workTypes = WorkType::query()
            ->withCount('works')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.work-types.index', [
            'workTypes' => $workTypes,
        ]);
    }

    public function create(): ViewContract
    {
        return view('admin.work-types.create');
    }

    public function store(StoreWorkTypeRequest $request): RedirectResponse
    {
        WorkType::create($request->validated());

        return redirect()
            ->route('admin.work-types.index')
            ->with('success', __('Tipo de trabajo creado exitosamente.'));
    }

    public function show(WorkType $workType): ViewContract
    {
        $workType->loadCount('works');

        return view('admin.work-types.show', [
            'workType' => $workType,
        ]);
    }

    public function edit(WorkType $workType): ViewContract
    {
        return view('admin.work-types.edit', [
            'workType' => $workType,
        ]);
    }

    public function update(UpdateWorkTypeRequest $request, WorkType $workType): RedirectResponse
    {
        $workType->update($request->validated());

        return redirect()
            ->route('admin.work-types.index')
            ->with('success', __('Tipo de trabajo actualizado exitosamente.'));
    }

    public function destroy(WorkType $workType): RedirectResponse
    {
        if ($workType->hasAssociatedWorks()) {
            return redirect()
                ->route('admin.work-types.index')
                ->with('error', __('No se puede eliminar un tipo de trabajo que tiene trabajos registrados.'));
        }

        $workType->delete();

        return redirect()
            ->route('admin.work-types.index')
            ->with('success', __('Tipo de trabajo eliminado exitosamente.'));
    }
}
