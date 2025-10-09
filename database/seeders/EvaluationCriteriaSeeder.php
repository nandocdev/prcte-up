<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EvaluationCriteria;

/**
 * Seeder: EvaluationCriteriaSeeder
 * 
 * Puebla la tabla evaluation_criteria con los criterios predefinidos
 * para la evaluación de trabajos de extensión en VIEX.
 */
class EvaluationCriteriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $criteria = [
            // Criterios de Pertinencia y Relevancia (peso: 30%)
            [
                'name' => 'Pertinencia del trabajo',
                'description' => 'Evalúa si el trabajo responde a necesidades reales de la comunidad o sector al que se dirige. Considera la alineación con problemáticas identificadas y la relevancia del contexto.',
                'category' => 'Pertinencia y Relevancia',
                'max_score' => 10,
                'weight' => 14,
                'order' => 1,
                'is_active' => true,
                'is_required' => true,
            ],
            [
                'name' => 'Alineación con objetivos institucionales',
                'description' => 'Valora la coherencia del trabajo con la misión, visión y objetivos estratégicos de extensión de la Universidad de Panamá. Considera la contribución al desarrollo social y académico.',
                'category' => 'Pertinencia y Relevancia',
                'max_score' => 10,
                'weight' => 9,
                'order' => 2,
                'is_active' => true,
                'is_required' => true,
            ],

            // Criterios de Metodología y Ejecución (peso: 25%)
            [
                'name' => 'Metodología aplicada',
                'description' => 'Analiza la claridad, coherencia y pertinencia de la metodología empleada. Considera la descripción de actividades, cronograma y recursos utilizados.',
                'category' => 'Metodología y Ejecución',
                'max_score' => 10,
                'weight' => 9,
                'order' => 3,
                'is_active' => true,
                'is_required' => true,
            ],
            [
                'name' => 'Calidad de la ejecución',
                'description' => 'Valora el cumplimiento de las actividades planificadas, la gestión de recursos y la capacidad de resolución de problemas durante la implementación del trabajo.',
                'category' => 'Metodología y Ejecución',
                'max_score' => 10,
                'weight' => 9,
                'order' => 4,
                'is_active' => true,
                'is_required' => true,
            ],
            [
                'name' => 'Innovación y creatividad',
                'description' => 'Evalúa el grado de innovación en los enfoques, métodos o herramientas utilizadas. Considera la originalidad de las soluciones propuestas y su aplicabilidad.',
                'category' => 'Metodología y Ejecución',
                'max_score' => 10,
                'weight' => 5,
                'order' => 5,
                'is_active' => true,
                'is_required' => false,
            ],

            // Criterios de Impacto (peso: 30%)
            [
                'name' => 'Impacto social o comunitario',
                'description' => 'Mide el efecto del trabajo en la comunidad o sector beneficiario. Considera cambios generados, alcance, número de beneficiarios directos e indirectos.',
                'category' => 'Impacto',
                'max_score' => 10,
                'weight' => 14,
                'order' => 6,
                'is_active' => true,
                'is_required' => true,
            ],
            [
                'name' => 'Sostenibilidad y continuidad',
                'description' => 'Valora la capacidad del trabajo para generar efectos duraderos. Considera estrategias de seguimiento, transferencia de conocimientos y proyección a futuro.',
                'category' => 'Impacto',
                'max_score' => 10,
                'weight' => 10,
                'order' => 7,
                'is_active' => true,
                'is_required' => false,
            ],

            // Criterios de Documentación y Evidencias (peso: 15%)
            [
                'name' => 'Calidad de evidencias',
                'description' => 'Evalúa la calidad, pertinencia y suficiencia de las evidencias presentadas: fotografías, documentos, informes, testimonios, etc.',
                'category' => 'Documentación y Evidencias',
                'max_score' => 10,
                'weight' => 10,
                'order' => 8,
                'is_active' => true,
                'is_required' => true,
            ],
            [
                'name' => 'Presentación y organización del informe',
                'description' => 'Valora la claridad, orden, redacción y presentación formal del trabajo. Considera el uso correcto de normas académicas y la estructura del documento.',
                'category' => 'Documentación y Evidencias',
                'max_score' => 10,
                'weight' => 5,
                'order' => 9,
                'is_active' => true,
                'is_required' => false,
            ],

            // Criterios de Participación y Colaboración (peso: 10% - opcionales)
            [
                'name' => 'Participación de estudiantes',
                'description' => 'Evalúa la integración y rol de estudiantes en el trabajo. Considera la formación práctica y el aporte al aprendizaje de los participantes.',
                'category' => 'Participación y Colaboración',
                'max_score' => 10,
                'weight' => 5,
                'order' => 10,
                'is_active' => true,
                'is_required' => false,
            ],
            [
                'name' => 'Colaboración interinstitucional',
                'description' => 'Valora las alianzas y colaboraciones establecidas con otras instituciones, organizaciones o comunidades. Considera la sinergia y beneficios mutuos.',
                'category' => 'Participación y Colaboración',
                'max_score' => 10,
                'weight' => 5,
                'order' => 11,
                'is_active' => true,
                'is_required' => false,
            ],

            [
                'name' => 'Ética y responsabilidad social',
                'description' => 'Evalúa el respeto a principios éticos, inclusión, equidad y responsabilidad social en la ejecución del trabajo. Considera el trato a beneficiarios y el respeto a normativas.',
                'category' => 'Aspectos Éticos',
                'max_score' => 10,
                'weight' => 5,
                'order' => 12,
                'is_active' => true,
                'is_required' => false,
            ],
        ];

        foreach ($criteria as $criterion) {
            EvaluationCriteria::create($criterion);
        }

        $this->command->info('Criterios de evaluación creados exitosamente.');
        $this->command->info('Total de criterios: ' . count($criteria));
        
        // Mostrar resumen de pesos
        $totalWeight = EvaluationCriteria::sum('weight');
        $this->command->info('Peso total de criterios: ' . $totalWeight);
    }
}
