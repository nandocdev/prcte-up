<?php

return [
    'reports' => [
        'title' => 'Reportes del Sistema',
        'actions' => [
            'view_report' => 'Ver reporte',
            'back' => 'Regresar',
            'clear_filters' => 'Limpiar filtros',
            'generate' => 'Generar reporte',
            'download_csv' => 'Descargar CSV',
        ],
        'filters' => [
            'title' => 'Filtros del reporte',
            'any_option' => 'Todos',
            'date_from' => 'Fecha desde',
            'date_to' => 'Fecha hasta',
            'work_type' => 'Tipo de trabajo',
            'organizational_unit' => 'Unidad organizacional',
            'final_decision' => 'Decisión final',
        ],
        'messages' => [
            'apply_filters' => 'Aplica los filtros y presiona "Generar reporte" para visualizar los resultados.',
            'empty_results' => 'No hay resultados para los filtros seleccionados.',
        ],
        'results' => [
            'title' => 'Resultados del reporte',
            'rows' => 'registros',
        ],
        'columns' => [
            'status' => 'Estado',
            'unit' => 'Unidad organizacional',
            'total' => 'Total',
            'final_decision' => 'Decisión final',
            'total_evaluations' => 'Evaluaciones',
            'average_score' => 'Promedio total',
            'average_weighted_score' => 'Promedio ponderado',
        ],
        'summary' => [
            'total_records' => 'Total de trabajos',
            'distinct_statuses' => 'Estados distintos',
            'distinct_units' => 'Unidades distintas',
            'total_evaluations' => 'Total de evaluaciones',
            'average_total_score' => 'Promedio total (global)',
            'average_weighted_score' => 'Promedio ponderado (global)',
        ],
        'decisions' => [
            'approve' => 'Aprobado',
            'approve_with_conditions' => 'Aprobado con condiciones',
            'reject' => 'Rechazado',
            'pending' => 'Pendiente',
        ],
        'works_by_status' => [
            'title' => 'Trabajos por estado',
            'description' => 'Distribución de trabajos de extensión según su estado actual.',
        ],
        'works_by_unit' => [
            'title' => 'Trabajos por unidad académica',
            'description' => 'Cantidad de trabajos registrados por cada unidad organizacional.',
        ],
        'evaluation_statistics' => [
            'title' => 'Estadísticas de evaluación',
            'description' => 'Resumen de evaluaciones realizadas, decisiones finales y promedios de puntajes.',
        ],
    ],
];
