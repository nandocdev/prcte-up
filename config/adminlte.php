<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    |
    | Here you can change the default title of your admin panel.
    |
    | For detailed instructions you can look the title section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'title' => 'VIEX - Sistema de Extensión',
    'title_prefix' => '',
    'title_postfix' => ' | Universidad de Panamá',

    /*
    |--------------------------------------------------------------------------
    | Favicon
    |--------------------------------------------------------------------------
    |
    | Here you can activate the favicon.
    |
    | For detailed instructions you can look the favicon section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_ico_only' => false,
    'use_full_favicon' => false,

    /*
    |--------------------------------------------------------------------------
    | Google Fonts
    |--------------------------------------------------------------------------
    |
    | Here you can allow or not the use of external google fonts. Disabling the
    | google fonts may be useful if your admin panel internet access is
    | restricted somehow.
    |
    | For detailed instructions you can look the google fonts section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'google_fonts' => [
        'allowed' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Logo
    |--------------------------------------------------------------------------
    |
    | Here you can change the logo of your admin panel.
    |
    | For detailed instructions you can look the logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'logo' => '<b>VIEX</b> UP',
    'logo_img' => 'vendor/adminlte/dist/img/AdminLTELogo.png',
    'logo_img_class' => 'brand-image img-circle elevation-3',
    'logo_img_xl' => null,
    'logo_img_xl_class' => 'brand-image-xs',
    'logo_img_alt' => 'VIEX Logo',

    /*
    |--------------------------------------------------------------------------
    | Authentication Logo
    |--------------------------------------------------------------------------
    |
    | Here you can setup an alternative logo to use on your login and register
    | screens. When disabled, the admin panel logo will be used instead.
    |
    | For detailed instructions you can look the auth logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'auth_logo' => [
        'enabled' => false,
        'img' => [
            'path' => 'vendor/adminlte/dist/img/AdminLTELogo.png',
            'alt' => 'Auth Logo',
            'class' => '',
            'width' => 50,
            'height' => 50,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Preloader Animation
    |--------------------------------------------------------------------------
    |
    | Here you can change the preloader animation configuration. Currently, two
    | modes are supported: 'fullscreen' for a fullscreen preloader animation
    | and 'cwrapper' to attach the preloader animation into the content-wrapper
    | element and avoid overlapping it with the sidebars and the top navbar.
    |
    | For detailed instructions you can look the preloader section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'preloader' => [
        'enabled' => false,
        'mode' => 'fullscreen',
        'img' => [
            'path' => 'vendor/adminlte/dist/img/AdminLTELogo.png',
            'alt' => 'AdminLTE Preloader Image',
            'effect' => 'animation__shake',
            'width' => 60,
            'height' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Menu
    |--------------------------------------------------------------------------
    |
    | Here you can activate and change the user menu.
    |
    | For detailed instructions you can look the user menu section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'usermenu_enabled' => true,
    'usermenu_header' => false,
    'usermenu_header_class' => 'bg-success',
    'usermenu_image' => false,
    'usermenu_desc' => false,
    'usermenu_profile_url' => false,

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | Here we change the layout of your admin panel.
    |
    | For detailed instructions you can look the layout section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'layout_topnav' => null,
    'layout_boxed' => null,
    'layout_fixed_sidebar' => null,
    'layout_fixed_navbar' => null,
    'layout_fixed_footer' => null,
    'layout_dark_mode' => null,

    /*
    |--------------------------------------------------------------------------
    | Authentication Views Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the authentication views.
    |
    | For detailed instructions you can look the auth classes section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_auth_card' => 'card-outline card-success',
    'classes_auth_header' => '',
    'classes_auth_body' => '',
    'classes_auth_footer' => '',
    'classes_auth_icon' => '',
    'classes_auth_btn' => 'btn-flat btn-success',

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the admin panel.
    |
    | For detailed instructions you can look the admin panel classes here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_body' => '',
    'classes_brand' => '',
    'classes_brand_text' => '',
    'classes_content_wrapper' => '',
    'classes_content_header' => '',
    'classes_content' => '',
    'classes_sidebar' => 'sidebar-dark-success elevation-4',
    'classes_sidebar_nav' => '',
    'classes_topnav' => 'navbar-white navbar-light',
    'classes_topnav_nav' => 'navbar-expand',
    'classes_topnav_container' => 'container',

    /*
    |--------------------------------------------------------------------------
    | Sidebar
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar of the admin panel.
    |
    | For detailed instructions you can look the sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'sidebar_mini' => 'lg',
    'sidebar_collapse' => false,
    'sidebar_collapse_auto_size' => false,
    'sidebar_collapse_remember' => false,
    'sidebar_collapse_remember_no_transition' => true,
    'sidebar_scrollbar_theme' => 'os-theme-light',
    'sidebar_scrollbar_auto_hide' => 'l',
    'sidebar_nav_accordion' => true,
    'sidebar_nav_animation_speed' => 300,

    /*
    |--------------------------------------------------------------------------
    | Control Sidebar (Right Sidebar)
    |--------------------------------------------------------------------------
    |
    | Here we can modify the right sidebar aka control sidebar of the admin panel.
    |
    | For detailed instructions you can look the right sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'right_sidebar' => false,
    'right_sidebar_icon' => 'fas fa-cogs',
    'right_sidebar_theme' => 'dark',
    'right_sidebar_slide' => true,
    'right_sidebar_push' => true,
    'right_sidebar_scrollbar_theme' => 'os-theme-light',
    'right_sidebar_scrollbar_auto_hide' => 'l',

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    |
    | Here we can modify the url settings of the admin panel.
    |
    | For detailed instructions you can look the urls section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_route_url' => false,
    'dashboard_url' => 'dashboard',
    'logout_url' => 'logout',
    'login_url' => 'login',
    'register_url' => 'register',
    'password_reset_url' => 'password/reset',
    'password_email_url' => 'password/email',
    'profile_url' => false,
    'disable_darkmode_routes' => false,

    /*
    |--------------------------------------------------------------------------
    | Laravel Asset Bundling
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Laravel Asset Bundling option for the admin panel.
    | Currently, the next modes are supported: 'mix', 'vite' and 'vite_js_only'.
    | When using 'vite_js_only', it's expected that your CSS is imported using
    | JavaScript. Typically, in your application's 'resources/js/app.js' file.
    | If you are not using any of these, leave it as 'false'.
    |
    | For detailed instructions you can look the asset bundling section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'laravel_asset_bundling' => false,
    'laravel_css_path' => 'css/app.css',
    'laravel_js_path' => 'js/app.js',

    /*
    |--------------------------------------------------------------------------
    | Menu Items
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar/top navigation of the admin panel.
    |
    | For detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'menu' => [
        // Navbar items:
        [
            'type' => 'navbar-search',
            'text' => 'Buscar',
            'topnav_right' => true,
        ],
        [
            'type' => 'fullscreen-widget',
            'topnav_right' => true,
        ],

        // Sidebar items:
        [
            'type' => 'sidebar-menu-search',
            'text' => 'Buscar en menú',
        ],

        // Dashboard Principal
        [
            'text' => 'Dashboard',
            'route' => 'dashboard',
            'icon' => 'fas fa-tachometer-alt',
            'active' => ['dashboard'],
        ],

        // SECCIÓN TRABAJOS DE EXTENSIÓN
        ['header' => 'TRABAJOS DE EXTENSIÓN', 'can' => 'works.view.own'],

        // Para Profesores (Todos los usuarios autenticados)
        [
            'text' => 'Mis Trabajos',
            'icon' => 'fas fa-folder-open',
            'submenu' => [
                [
                    'text' => 'Ver Todos',
                    'route' => 'works.index',
                    'can' => 'works.view.own',
                    'icon' => 'fas fa-list',
                ],
                [
                    'text' => 'Crear Nuevo',
                    'route' => 'works.create',
                    'can' => 'works.create',
                    'icon' => 'fas fa-plus',
                ],
                [
                    'text' => 'Borradores',
                    'url' => '/works?status=draft',
                    'icon' => 'fas fa-edit',
                ],
                [
                    'text' => 'En Revisión',
                    'url' => '/works?status=review',
                    'icon' => 'fas fa-clock',
                ],
            ],
        ],

        // SECCIÓN COORDINACIÓN DE EXTENSIÓN
        ['header' => 'COORDINACIÓN DE EXTENSIÓN', 'can' => 'works.coordinate'],

        // Para Coordinadores de Extensión
        [
            'text' => 'Panel Coordinador',
            'route' => 'coordinator.dashboard',
            'icon' => 'fas fa-user-tie',
            'can' => 'works.coordinate',
            'active' => ['coordinator.*'],
        ],

        // SECCIÓN DECANATO/DIRECCIÓN
        ['header' => 'DECANATO/DIRECCIÓN', 'can' => 'works.manage.dean'],

        // Para Decanos/Directores
        [
            'text' => 'Panel Decano/Director',
            'route' => 'dean.dashboard',
            'icon' => 'fas fa-university',
            'can' => 'works.manage.dean',
            'active' => ['dean.*'],
        ],

        // SECCIÓN VIEX ADMIN
        ['header' => 'VICERRECTORÍA DE EXTENSIÓN', 'can' => 'works.manage.viex'],

        // Para Administradores VIEX
        [
            'text' => 'Administración VIEX',
            'icon' => 'fas fa-cogs',
            'can' => 'works.manage.viex',
            'submenu' => [
                [
                    'text' => 'Dashboard VIEX',
                    'route' => 'viex.dashboard',
                    'icon' => 'fas fa-chart-line',
                    'can' => 'works.manage.viex',
                ],
                [
                    'text' => 'Trabajos en VIEX',
                    'route' => 'viex.index',
                    'icon' => 'fas fa-inbox',
                    'can' => 'works.evaluate',
                ],
                [
                    'text' => 'Reportes y Análisis',
                    'icon' => 'fas fa-chart-bar',
                    'submenu' => [
                        [
                            'text' => 'Reporte de Trabajo',
                            'url' => '#',
                            'icon' => 'fas fa-file-pdf',
                            'shift' => 'ml-3',
                            'can' => 'works.generate-report',
                        ],
                        [
                            'text' => 'Descargar Certificados',
                            'url' => '#',
                            'icon' => 'fas fa-certificate',
                            'shift' => 'ml-3',
                            'can' => 'works.generate-report',
                        ],
                    ],
                ],
            ],
        ],

        // SECCIÓN ADMINISTRACIÓN DEL SISTEMA
        ['header' => 'ADMINISTRACIÓN', 'can' => 'system.manage'],

        // Para Super Administradores
        [
            'text' => 'Gestión de Usuarios',
            'icon' => 'fas fa-users-cog',
            'can' => 'manage-system',
            'submenu' => [
                [
                    'text' => 'Todos los Usuarios',
                    'route' => 'admin.users.index',
                    'icon' => 'fas fa-users',
                    'can' => 'users.view.all',
                ],
                [
                    'text' => 'Crear Usuario',
                    'route' => 'admin.users.create',
                    'icon' => 'fas fa-user-plus',
                    'can' => 'users.create',
                ],
                [
                    'text' => 'Roles y Permisos',
                    'route' => 'admin.roles.index',
                    'icon' => 'fas fa-user-shield',
                    'can' => 'roles.manage',
                ],
                [
                    'text' => 'Dashboard de Roles',
                    'route' => 'admin.role-assignment.index',
                    'icon' => 'fas fa-project-diagram',
                    'can' => 'roles.manage',
                ],
            ],
        ],

        [
            'text' => 'Configuración del Sistema',
            'icon' => 'fas fa-cog',
            'can' => 'manage-system',
            'submenu' => [
                // CATÁLOGOS ORGANIZACIONALES
                [
                    'text' => 'Unidades Académicas',
                    'route' => 'admin.organizational-units.index',
                    'icon' => 'fas fa-university',
                    'can' => 'system.manage',
                ],
                [
                    'text' => 'Tipos de Proyectos Institucionales',
                    'route' => 'admin.institutional-project-types.index',
                    'icon' => 'fas fa-layer-group',
                    'can' => 'system.manage',
                ],

                // CATÁLOGOS DE TRABAJOS
                [
                    'text' => 'Tipos de Trabajos',
                    'route' => 'admin.work-types.index',
                    'icon' => 'fas fa-tags',
                    'can' => 'system.manage',
                ],
                [
                    'text' => 'Estados de Trabajos',
                    'route' => 'admin.work-statuses.index',
                    'icon' => 'fas fa-traffic-light',
                    'can' => 'system.manage',
                ],

                // GESTIÓN DE SEGURIDAD
                [
                    'text' => 'Roles del Sistema',
                    'route' => 'admin.roles.index',
                    'icon' => 'fas fa-user-tag',
                    'can' => 'roles.manage',
                ],
                [
                    'text' => 'Permisos del Sistema',
                    'route' => 'admin.permissions.index',
                    'icon' => 'fas fa-key',
                    'can' => 'permissions.manage',
                ],

                ['header' => 'MANTENIMIENTO'],

                // HERRAMIENTAS DE SISTEMA
                [
                    'text' => 'Logs del Sistema',
                    'url' => '#',
                    'icon' => 'fas fa-file-alt',
                    'can' => 'system.manage',
                ],
                [
                    'text' => 'Backup y Mantenimiento',
                    'url' => '#',
                    'icon' => 'fas fa-database',
                    'can' => 'system.manage',
                ],
                [
                    'text' => 'Cache del Sistema',
                    'url' => '#',
                    'icon' => 'fas fa-memory',
                    'can' => 'system.manage',
                ],
            ],
        ],

        // SECCIÓN CUENTA DEL USUARIO
        ['header' => 'MI CUENTA'],

        [
            'text' => 'Mi Perfil',
            'route' => 'profile.edit',
            'icon' => 'fas fa-user',
        ],

        [
            'text' => 'Notificaciones',
            'route' => 'notifications.index',
            'icon' => 'fas fa-bell',
            'label' => 3,
            'label_color' => 'warning',
        ],

        // SECCIÓN AYUDA Y SOPORTE
        ['header' => 'AYUDA Y SOPORTE'],

        [
            'text' => 'Manual de Usuario',
            'url' => '#',
            'icon' => 'fas fa-question-circle',
            'target' => '_blank',
        ],

        [
            'text' => 'Contactar Soporte',
            'url' => '#',
            'icon' => 'fas fa-envelope',
        ],

        [
            'text' => 'Información Técnica',
            'route' => 'testing.info',
            'icon' => 'fas fa-info-circle',
            'can' => 'manage-system',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Menu Filters
    |--------------------------------------------------------------------------
    |
    | Here we can modify the menu filters of the admin panel.
    |
    | For detailed instructions you can look the menu filters section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'filters' => [
        // Nuestro filtro de roles personalizados permite usar el atributo 'role' en las entradas del menú
        App\Menu\Filters\RoleFilter::class,

        // GateFilter sigue presente para compatibilidad con 'can' y policies
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SearchFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\DataFilter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugins Initialization
    |--------------------------------------------------------------------------
    |
    | Here we can modify the plugins used inside the admin panel.
    |
    | For detailed instructions you can look the plugins section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Plugins-Configuration
    |
    */

    'plugins' => [
        'Datatables' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css',
                ],
            ],
        ],
        'Select2' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css',
                ],
            ],
        ],
        'Chartjs' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js',
                ],
            ],
        ],
        'Sweetalert2' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.jsdelivr.net/npm/sweetalert2@8',
                ],
            ],
        ],
        'Pace' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-center-radar.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js',
                ],
            ],
        ],
        'Toastr' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css',
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | IFrame
    |--------------------------------------------------------------------------
    |
    | Here we change the IFrame mode configuration. Note these changes will
    | only apply to the view that extends and enable the IFrame mode.
    |
    | For detailed instructions you can look the iframe mode section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/IFrame-Mode-Configuration
    |
    */

    'iframe' => [
        'default_tab' => [
            'url' => null,
            'title' => null,
        ],
        'buttons' => [
            'close' => true,
            'close_all' => true,
            'close_all_other' => true,
            'scroll_left' => true,
            'scroll_right' => true,
            'fullscreen' => true,
        ],
        'options' => [
            'loading_screen' => 1000,
            'auto_show_new_tab' => true,
            'use_navbar_items' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Livewire
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Livewire support.
    |
    | For detailed instructions you can look the livewire here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'livewire' => false,
];
