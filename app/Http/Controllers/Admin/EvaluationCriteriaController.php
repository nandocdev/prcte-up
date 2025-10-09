<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreEvaluationCriteriaRequest;
use App\Http\Requests\Admin\SyncEvaluationCriteriaRequest;
use App\Http\Requests\Admin\UpdateEvaluationCriteriaRequest;
use App\Models\EvaluationCriteria;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Http\RedirectResponse;

class EvaluationCriteriaController extends Controller
{
    public function index(): ViewContract
    {
        $criteria = EvaluationCriteria::query()
            ->ordered()
            ->withCount('evaluationDetails')
            ->get();

        $activeWeight = $criteria->where('is_active', true)->sum('weight');

        return view('admin.evaluation-criteria.index', [
            'criteria' => $criteria,
            'activeWeight' => $activeWeight,
        ]);
    }

    public function create(): ViewContract
    {
        return view('admin.evaluation-criteria.create');
    }

    public function store(StoreEvaluationCriteriaRequest $request): RedirectResponse
    {
        EvaluationCriteria::create($request->validated());

        return $this->attachWeightWarning(
            redirect()
                ->route('admin.evaluation-criteria.index')
                ->with('success', __('Criterio de evaluacion creado correctamente.'))
        );
    }

    public function show(EvaluationCriteria $evaluationCriteria): ViewContract
    {
        $evaluationCriteria->loadCount('evaluationDetails');

        return view('admin.evaluation-criteria.show', [
            'evaluationCriteria' => $evaluationCriteria,
        ]);
    }

    public function edit(EvaluationCriteria $evaluationCriteria): ViewContract
    {
        return view('admin.evaluation-criteria.edit', [
            'evaluationCriteria' => $evaluationCriteria,
        ]);
    }

    public function update(UpdateEvaluationCriteriaRequest $request, EvaluationCriteria $evaluationCriteria): RedirectResponse
    {
        $evaluationCriteria->update($request->validated());

        return $this->attachWeightWarning(
            redirect()
                ->route('admin.evaluation-criteria.index')
                ->with('success', __('Criterio de evaluacion actualizado correctamente.'))
        );
    }

    public function destroy(EvaluationCriteria $evaluationCriteria): RedirectResponse
    {
        if ($evaluationCriteria->hasEvaluationDetails()) {
            return redirect()
                ->route('admin.evaluation-criteria.index')
                ->with('error', __('No se puede eliminar un criterio con evaluaciones asociadas.'));
        }

        $evaluationCriteria->delete();

        return $this->attachWeightWarning(
            redirect()
                ->route('admin.evaluation-criteria.index')
                ->with('success', __('Criterio de evaluacion eliminado correctamente.'))
        );
    }

    public function sync(SyncEvaluationCriteriaRequest $request): RedirectResponse
    {
        foreach ($request->validated('criteria') as $item) {
            $criterion = EvaluationCriteria::findOrFail($item['id']);
            $criterion->update([
                'weight' => $item['weight'],
                'order' => $item['order'],
                'is_active' => $item['is_active'],
            ]);
        }

        return $this->attachWeightWarning(
            redirect()
                ->route('admin.evaluation-criteria.index')
                ->with('success', __('Pesos y orden actualizados correctamente.'))
        );
    }

    private function attachWeightWarning(RedirectResponse $response): RedirectResponse
    {
        $total = EvaluationCriteria::totalActiveWeight();

        if ($total !== 100) {
            $response->with('warning', __('La suma de los pesos activos es :total%. Ajusta los criterios para alcanzar 100%.', [
                'total' => $total,
            ]));
        }

        return $response;
    }
}
