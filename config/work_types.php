<?php

return [
    'activity_types' => [
        'curso' => 'Curso',
        'taller' => 'Taller',
        'seminario' => 'Seminario',
        'conferencia' => 'Conferencia',
        'diplomado' => 'Diplomado',
        'capacitacion' => 'Capacitación',
        'otro' => 'Otro',
    ],

    'modalities' => [
        'presencial' => 'Presencial',
        'virtual' => 'Virtual',
        'hibrida' => 'Híbrida',
    ],

    'publication_types' => [
        'articulo' => 'Artículo',
        'libro' => 'Libro',
        'manual' => 'Manual',
        'guia' => 'Guía',
        'cartilla' => 'Cartilla',
        'folleto' => 'Folleto',
        'material_audiovisual' => 'Material Audiovisual',
        'otro' => 'Otro',
    ],

    'languages' => [
        'español' => 'Español',
        'ingles' => 'Inglés',
        'portugues' => 'Portugués',
        'frances' => 'Francés',
        'otro' => 'Otro',
    ],

    'assistance_types' => [
        'consultoria' => 'Consultoría',
        'asesoria' => 'Asesoría',
        'diagnostico' => 'Diagnóstico',
        'evaluacion' => 'Evaluación',
        'auditoria' => 'Auditoría',
        'capacitacion_tecnica' => 'Capacitación Técnica',
        'otro' => 'Otro',
    ],

    'work_modalities' => [
        'presencial' => 'Presencial',
        'remota' => 'Remota',
        'mixta' => 'Mixta',
    ],

    'academic_periods' => [
        '2024-I' => '2024-I',
        '2024-II' => '2024-II',
        '2025-I' => '2025-I',
        '2025-II' => '2025-II',
        '2026-I' => '2026-I',
        '2026-II' => '2026-II',
    ],

    // Mapeo de tipos de trabajo a secciones
    'work_type_sections' => [
        1 => 'proyecto',    // Proyecto de Extensión
        2 => 'actividad',   // Actividad de Extensión
        3 => 'publicacion', // Publicación
        4 => 'asistencia',  // Asistencia Técnica
    ],

    // Configuración de Correos Institucionales
    'viex_projects_email' => env('VIEX_PROJECTS_EMAIL', 'viexproyectos@up.ac.pa'),
];
