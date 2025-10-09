<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreWorkStatusRequest;
use App\Http\Requests\Admin\UpdateWorkStatusRequest;
use App\Models\WorkStatus;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Http\RedirectResponse;

class WorkStatusesController extends Controller
{
    public function index(): ViewContract
    {
        $statuses = WorkStatus::query()
            ->withCount(['currentWorks', 'transitionsFrom', 'transitionsTo'])
            ->orderBy('name')
            ->paginate(20);

        return view('admin.work-statuses.index', [
            'statuses' => $statuses,
        ]);
    }

    public function create(): ViewContract
    {
        return view('admin.work-statuses.create');
    }

    public function store(StoreWorkStatusRequest $request): RedirectResponse
    {
        WorkStatus::create($request->validated());

        return redirect()
            ->route('admin.work-statuses.index')
            ->with('success', __('Estado de trabajo creado exitosamente.'));
    }

    public function show(WorkStatus $workStatus): ViewContract
    {
        $workStatus->loadCount(['currentWorks', 'transitionsFrom', 'transitionsTo']);

        return view('admin.work-statuses.show', [
            'workStatus' => $workStatus,
        ]);
    }

    public function edit(WorkStatus $workStatus): ViewContract
    {
        return view('admin.work-statuses.edit', [
            'workStatus' => $workStatus,
        ]);
    }

    public function update(UpdateWorkStatusRequest $request, WorkStatus $workStatus): RedirectResponse
    {
        $workStatus->update($request->validated());

        return redirect()
            ->route('admin.work-statuses.index')
            ->with('success', __('Estado de trabajo actualizado exitosamente.'));
    }

    public function destroy(WorkStatus $workStatus): RedirectResponse
    {
        if ($workStatus->hasAssociations()) {
            return redirect()
                ->route('admin.work-statuses.index')
                ->with('error', __('No se puede eliminar un estado que está asociado a trabajos o historial.'));
        }

        $workStatus->delete();

        return redirect()
            ->route('admin.work-statuses.index')
            ->with('success', __('Estado de trabajo eliminado exitosamente.'));
    }
}
