<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrganizationalUnit;
use Illuminate\Http\Request;

class OrganizationalUnitsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $units = OrganizationalUnit::with('parent', 'children')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.organizational-units.index', compact('units'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $parentUnits = OrganizationalUnit::whereNull('parent_id')
            ->orWhere('unit_type', 'faculty')
            ->orderBy('name')
            ->get();

        return view('admin.organizational-units.create', compact('parentUnits'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:organizational_units',
            'unit_type' => 'required|in:faculty,department,school,center',
            'parent_id' => 'nullable|exists:organizational_units,id',
        ]);

        OrganizationalUnit::create($request->validated());

        return redirect()->route('admin.organizational-units.index')
            ->with('success', __('Unidad académica creada exitosamente.'));
    }

    /**
     * Display the specified resource.
     */
    public function show(OrganizationalUnit $organizationalUnit)
    {
        $organizationalUnit->load('parent', 'children', 'users');

        return view('admin.organizational-units.show', compact('organizationalUnit'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OrganizationalUnit $organizationalUnit)
    {
        $parentUnits = OrganizationalUnit::where('id', '!=', $organizationalUnit->id)
            ->whereNull('parent_id')
            ->orWhere('unit_type', 'faculty')
            ->orderBy('name')
            ->get();

        return view('admin.organizational-units.edit', compact('organizationalUnit', 'parentUnits'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, OrganizationalUnit $organizationalUnit)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:organizational_units,code,' . $organizationalUnit->id,
            'unit_type' => 'required|in:faculty,department,school,center',
            'parent_id' => 'nullable|exists:organizational_units,id',
        ]);

        $organizationalUnit->update($request->validated());

        return redirect()->route('admin.organizational-units.index')
            ->with('success', __('Unidad académica actualizada exitosamente.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OrganizationalUnit $organizationalUnit)
    {
        if ($organizationalUnit->children()->exists()) {
            return redirect()->route('admin.organizational-units.index')
                ->with('error', __('No se puede eliminar una unidad que tiene sub-unidades.'));
        }

        if ($organizationalUnit->users()->exists()) {
            return redirect()->route('admin.organizational-units.index')
                ->with('error', __('No se puede eliminar una unidad que tiene usuarios asignados.'));
        }

        $organizationalUnit->delete();

        return redirect()->route('admin.organizational-units.index')
            ->with('success', __('Unidad académica eliminada exitosamente.'));
    }
}
