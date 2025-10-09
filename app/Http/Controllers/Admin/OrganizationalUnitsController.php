<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreOrganizationalUnitRequest;
use App\Http\Requests\Admin\UpdateOrganizationalUnitRequest;
use App\Models\OrganizationalUnit;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class OrganizationalUnitsController extends Controller
{
    public function index(): View
    {
        $units = OrganizationalUnit::with(['parent', 'children', 'users'])
            ->orderBy('name')
            ->paginate(20);

        return view('admin.organizational-units.index', [
            'units' => $units,
        ]);
    }

    public function create(): View
    {
        $parentUnits = OrganizationalUnit::orderBy('name')->get();

        return view('admin.organizational-units.create', [
            'parentUnits' => $parentUnits,
            'typeOptions' => OrganizationalUnit::typeOptions(),
        ]);
    }

    public function store(StoreOrganizationalUnitRequest $request): RedirectResponse
    {
        OrganizationalUnit::create($request->validated());

        return redirect()
            ->route('admin.organizational-units.index')
            ->with('success', __('Unidad organizacional creada exitosamente.'));
    }

    public function show(OrganizationalUnit $organizationalUnit): View
    {
        $organizationalUnit->load(['parent', 'children', 'users']);

        return view('admin.organizational-units.show', [
            'unit' => $organizationalUnit,
        ]);
    }

    public function edit(OrganizationalUnit $organizationalUnit): View
    {
        $excludedIds = OrganizationalUnit::descendantIds($organizationalUnit->getKey());
        $availableParents = OrganizationalUnit::whereNotIn('id', $excludedIds)
            ->orderBy('name')
            ->get();

        return view('admin.organizational-units.edit', [
            'unit' => $organizationalUnit,
            'parentUnits' => $availableParents,
            'typeOptions' => OrganizationalUnit::typeOptions(),
        ]);
    }

    public function update(UpdateOrganizationalUnitRequest $request, OrganizationalUnit $organizationalUnit): RedirectResponse
    {
        $organizationalUnit->update($request->validated());

        return redirect()
            ->route('admin.organizational-units.index')
            ->with('success', __('Unidad organizacional actualizada exitosamente.'));
    }

    public function destroy(OrganizationalUnit $organizationalUnit): RedirectResponse
    {
        if ($organizationalUnit->children()->exists()) {
            return redirect()
                ->route('admin.organizational-units.index')
                ->with('error', __('No se puede eliminar una unidad que tiene subunidades.'));
        }

        if ($organizationalUnit->users()->exists()) {
            return redirect()
                ->route('admin.organizational-units.index')
                ->with('error', __('No se puede eliminar una unidad que tiene usuarios asignados.'));
        }

        $organizationalUnit->delete();

        return redirect()
            ->route('admin.organizational-units.index')
            ->with('success', __('Unidad organizacional eliminada exitosamente.'));
    }
}
