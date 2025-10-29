<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Controlador para el sistema de documentación integrado
 * Maneja la navegación y visualización de la documentación del usuario
 */
class DocumentationController extends Controller
{
    /**
     * Índice principal de documentación
     */
    public function index(): View
    {
        $sections = $this->getDocumentationSections();
        
        return view('documentation.index', [
            'sections' => $sections,
            'currentSection' => 'index'
        ]);
    }

    /**
     * Mostrar una sección específica de documentación
     */
    public function show(string $section): View
    {
        $sections = $this->getDocumentationSections();
        
        // Verificar que la sección existe
        if (!array_key_exists($section, $sections)) {
            abort(404, 'Sección de documentación no encontrada');
        }

        $currentSection = $sections[$section];
        $navigationSections = $this->getNavigationSections();
        
        return view('documentation.show', [
            'section' => $currentSection,
            'sections' => $sections,
            'navigationSections' => $navigationSections,
            'currentSection' => $section
        ]);
    }

    /**
     * Búsqueda en la documentación
     */
    public function search(Request $request): View
    {
        $query = $request->get('q', '');
        $results = [];
        
        if (strlen($query) >= 3) {
            $results = $this->searchInDocumentation($query);
        }
        
        return view('documentation.search', [
            'query' => $query,
            'results' => $results,
            'currentSection' => 'search'
        ]);
    }

    /**
     * Obtener todas las secciones de documentación
     */
    private function getDocumentationSections(): array
    {
        return [
            'introduction' => [
                'title' => 'Introducción',
                'description' => '¿Qué es VIEX y cómo empezar?',
                'icon' => 'fas fa-home',
                'order' => 1,
                'subsections' => [
                    'what-is-viex' => 'Qué es VIEX',
                    'benefits' => 'Beneficios del sistema',
                    'requirements' => 'Requisitos del sistema'
                ]
            ],
            'getting-started' => [
                'title' => 'Primeros Pasos',
                'description' => 'Guía para nuevos usuarios',
                'icon' => 'fas fa-play-circle',
                'order' => 2,
                'subsections' => [
                    'access' => 'Acceso al sistema',
                    'first-login' => 'Primer inicio de sesión',
                    'profile-setup' => 'Configuración del perfil',
                    'navigation' => 'Navegación general'
                ]
            ],
            'roles-permissions' => [
                'title' => 'Roles y Permisos',
                'description' => 'Tipos de usuarios y sus funciones',
                'icon' => 'fas fa-users-cog',
                'order' => 3,
                'subsections' => [
                    'user-types' => 'Tipos de usuarios',
                    'permissions' => 'Permisos por rol',
                    'context-switching' => 'Cambio de contexto'
                ]
            ],
            'professors' => [
                'title' => 'Para Profesores',
                'description' => 'Guía completa para docentes',
                'icon' => 'fas fa-chalkboard-teacher',
                'order' => 4,
                'subsections' => [
                    'dashboard' => 'Dashboard del profesor',
                    'create-work' => 'Crear trabajo de extensión',
                    'work-types' => 'Tipos de trabajos',
                    'edit-draft' => 'Editar borrador',
                    'submit-work' => 'Enviar para revisión',
                    'manage-files' => 'Gestión de archivos',
                    'resubmit' => 'Reenvío después de correcciones',
                    'check-status' => 'Consultar estado',
                    'publication-auth' => 'Autorización de publicación'
                ]
            ],
            'coordinators' => [
                'title' => 'Para Coordinadores',
                'description' => 'Revisión y aprobación de trabajos',
                'icon' => 'fas fa-user-tie',
                'order' => 5,
                'subsections' => [
                    'coordinator-dashboard' => 'Dashboard del coordinador',
                    'review-pending' => 'Revisar trabajos pendientes',
                    'approve-works' => 'Aprobar y remitir',
                    'request-changes' => 'Solicitar subsanaciones',
                    'reject-works' => 'Rechazar trabajos',
                    'unit-management' => 'Gestión de unidad'
                ]
            ],
            'deans' => [
                'title' => 'Para Decanos y Directores',
                'description' => 'Aprobación a nivel institucional',
                'icon' => 'fas fa-university',
                'order' => 6,
                'subsections' => [
                    'dean-dashboard' => 'Dashboard ejecutivo',
                    'faculty-review' => 'Revisar trabajos de facultad',
                    'approve-to-viex' => 'Aprobar y tramitar a VIEX',
                    'return-observations' => 'Devolver con observaciones',
                    'institutional-reports' => 'Reportes institucionales'
                ]
            ],
            'viex-admin' => [
                'title' => 'Para Administradores VIEX',
                'description' => 'Evaluación final y certificación',
                'icon' => 'fas fa-star',
                'order' => 7,
                'subsections' => [
                    'viex-dashboard' => 'Dashboard VIEX',
                    'receive-works' => 'Recibir trabajos',
                    'evaluation-system' => 'Sistema de evaluación',
                    'assign-evaluators' => 'Asignar evaluadores',
                    'review-evaluations' => 'Revisar evaluaciones',
                    'approve-certify' => 'Aprobar y certificar',
                    'reject-works' => 'Rechazar trabajos',
                    'generate-certificates' => 'Generar certificaciones',
                    'viex-reports' => 'Reportes y estadísticas'
                ]
            ],
            'evaluators' => [
                'title' => 'Para Evaluadores',
                'description' => 'Proceso de evaluación especializada',
                'icon' => 'fas fa-clipboard-check',
                'order' => 8,
                'subsections' => [
                    'evaluator-dashboard' => 'Dashboard del evaluador',
                    'accept-assignments' => 'Aceptar asignaciones',
                    'perform-evaluations' => 'Realizar evaluaciones',
                    'evaluation-criteria' => 'Criterios de evaluación',
                    'submit-evaluation' => 'Enviar evaluación'
                ]
            ],
            'notifications' => [
                'title' => 'Notificaciones',
                'description' => 'Sistema de comunicación',
                'icon' => 'fas fa-bell',
                'order' => 9,
                'subsections' => [
                    'notification-system' => 'Sistema de notificaciones',
                    'email-notifications' => 'Notificaciones por email',
                    'notification-center' => 'Centro de notificaciones',
                    'preferences' => 'Configurar preferencias'
                ]
            ],
            'certificates' => [
                'title' => 'Certificados',
                'description' => 'Gestión de documentos oficiales',
                'icon' => 'fas fa-certificate',
                'order' => 10,
                'subsections' => [
                    'download-certificates' => 'Descargar certificados',
                    'validate-certificates' => 'Validar certificados',
                    'document-archive' => 'Archivo de documentos',
                    'share-certifications' => 'Compartir certificaciones'
                ]
            ],
            'administration' => [
                'title' => 'Administración',
                'description' => 'Configuración del sistema',
                'icon' => 'fas fa-cogs',
                'order' => 11,
                'subsections' => [
                    'user-management' => 'Gestión de usuarios',
                    'role-configuration' => 'Configuración de roles',
                    'organizational-units' => 'Unidades organizacionales',
                    'system-configuration' => 'Configuración del sistema'
                ]
            ],
            'reports' => [
                'title' => 'Reportes',
                'description' => 'Consultas y análisis de datos',
                'icon' => 'fas fa-chart-bar',
                'order' => 12,
                'subsections' => [
                    'report-types' => 'Tipos de reportes',
                    'filters-search' => 'Filtros y búsquedas',
                    'export-data' => 'Exportar datos',
                    'custom-reports' => 'Reportes personalizados'
                ]
            ],
            'troubleshooting' => [
                'title' => 'Resolución de Problemas',
                'description' => 'Soporte y preguntas frecuentes',
                'icon' => 'fas fa-life-ring',
                'order' => 13,
                'subsections' => [
                    'common-problems' => 'Problemas comunes',
                    'faq' => 'Preguntas frecuentes',
                    'technical-support' => 'Soporte técnico',
                    'escalation' => 'Escalación de incidencias'
                ]
            ],
            'appendices' => [
                'title' => 'Anexos',
                'description' => 'Referencias y documentos adicionales',
                'icon' => 'fas fa-book',
                'order' => 14,
                'subsections' => [
                    'glossary' => 'Glosario de términos',
                    'approval-flow' => 'Flujo de aprobación',
                    'templates' => 'Formatos y plantillas',
                    'regulatory-framework' => 'Marco normativo'
                ]
            ]
        ];
    }

    /**
     * Obtener secciones para navegación
     */
    private function getNavigationSections(): array
    {
        $sections = $this->getDocumentationSections();
        
        return collect($sections)
            ->sortBy('order')
            ->map(function ($section, $key) {
                return [
                    'key' => $key,
                    'title' => $section['title'],
                    'icon' => $section['icon'],
                    'subsections' => $section['subsections'] ?? []
                ];
            })
            ->toArray();
    }

    /**
     * Buscar en la documentación
     */
    private function searchInDocumentation(string $query): array
    {
        $sections = $this->getDocumentationSections();
        $results = [];
        
        foreach ($sections as $sectionKey => $section) {
            // Buscar en título y descripción
            if (Str::contains(strtolower($section['title']), strtolower($query)) ||
                Str::contains(strtolower($section['description']), strtolower($query))) {
                $results[] = [
                    'type' => 'section',
                    'title' => $section['title'],
                    'description' => $section['description'],
                    'url' => route('documentation.show', $sectionKey),
                    'relevance' => $this->calculateRelevance($query, $section['title'] . ' ' . $section['description'])
                ];
            }
            
            // Buscar en subsecciones
            if (isset($section['subsections'])) {
                foreach ($section['subsections'] as $subKey => $subTitle) {
                    if (Str::contains(strtolower($subTitle), strtolower($query))) {
                        $results[] = [
                            'type' => 'subsection',
                            'title' => $subTitle,
                            'description' => "En sección: {$section['title']}",
                            'url' => route('documentation.show', $sectionKey) . "#{$subKey}",
                            'relevance' => $this->calculateRelevance($query, $subTitle)
                        ];
                    }
                }
            }
        }
        
        // Ordenar por relevancia
        usort($results, function($a, $b) {
            return $b['relevance'] <=> $a['relevance'];
        });
        
        return array_slice($results, 0, 20); // Limitar a 20 resultados
    }

    /**
     * Calcular relevancia de búsqueda
     */
    private function calculateRelevance(string $query, string $text): float
    {
        $query = strtolower($query);
        $text = strtolower($text);
        
        // Coincidencia exacta
        if (Str::contains($text, $query)) {
            return 1.0;
        }
        
        // Coincidencia de palabras
        $queryWords = explode(' ', $query);
        $matches = 0;
        
        foreach ($queryWords as $word) {
            if (Str::contains($text, $word)) {
                $matches++;
            }
        }
        
        return $matches / count($queryWords);
    }
}