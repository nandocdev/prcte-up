<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkType;
use Illuminate\Http\Request;

class WorkTypesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $workTypes = WorkType::orderBy('name')->paginate(20);

        return view('admin.work-types.index', compact('workTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.work-types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:work_type',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        WorkType::create($request->validated());

        return redirect()->route('admin.work-types.index')
            ->with('success', __('Tipo de trabajo creado exitosamente.'));
    }

    /**
     * Display the specified resource.
     */
    public function show(WorkType $workType)
    {
        $workType->loadCount('workOfExtensions');

        return view('admin.work-types.show', compact('workType'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WorkType $workType)
    {
        return view('admin.work-types.edit', compact('workType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WorkType $workType)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:work_type,name,' . $workType->id,
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        $workType->update($request->validated());

        return redirect()->route('admin.work-types.index')
            ->with('success', __('Tipo de trabajo actualizado exitosamente.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WorkType $workType)
    {
        if ($workType->workOfExtensions()->exists()) {
            return redirect()->route('admin.work-types.index')
                ->with('error', __('No se puede eliminar un tipo de trabajo que tiene trabajos registrados.'));
        }

        $workType->delete();

        return redirect()->route('admin.work-types.index')
            ->with('success', __('Tipo de trabajo eliminado exitosamente.'));
    }
}
