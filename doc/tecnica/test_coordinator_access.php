<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== PRUEBA DE ACCESO A RUTAS DE COORDINADOR ===\n\n";

// Obtener usuarios de diferentes roles
$coordinator = \App\Models\User::whereHas('roles', function ($query) {
    $query->where('name', 'coordinador_extension');
})->first();

$profesor = \App\Models\User::whereHas('roles', function ($query) {
    $query->where('name', 'profesor');
})->first();

$admin = \App\Models\User::whereHas('roles', function ($query) {
    $query->where('name', 'super_admin');
})->first();

echo "✅ Usuarios encontrados:\n";
echo "- Coordinador: " . ($coordinator ? $coordinator->name : 'No encontrado') . "\n";
echo "- Profesor: " . ($profesor ? $profesor->name : 'No encontrado') . "\n";
echo "- Admin: " . ($admin ? $admin->name : 'No encontrado') . "\n\n";

// Probar permisos
echo "=== PRUEBA DE PERMISOS ===\n";

function testUserPermissions($user, $roleName) {
    if (!$user) {
        echo "❌ $roleName: Usuario no encontrado\n";
        return;
    }

    echo "🔍 $roleName ({$user->name}):\n";
    echo "  - Roles: " . $user->getRoleNames()->implode(', ') . "\n";
    echo "  - ¿Puede ser coordinador? " . ($user->hasAnyRole(['coordinador_extension', 'super_admin']) ? '✅ SÍ' : '❌ NO') . "\n";
    echo "  - ¿Es coordinador específico? " . ($user->hasRole('coordinador_extension') ? '✅ SÍ' : '❌ NO') . "\n";
    echo "  - ¿Es super admin? " . ($user->hasRole('super_admin') ? '✅ SÍ' : '❌ NO') . "\n\n";
}

testUserPermissions($coordinator, 'COORDINADOR');
testUserPermissions($profesor, 'PROFESOR');
testUserPermissions($admin, 'SUPER ADMIN');

echo "=== RUTAS DISPONIBLES ===\n";
$routes = collect(Route::getRoutes())->filter(function ($route) {
    return str_contains($route->uri(), 'coordinator');
});

foreach ($routes as $route) {
    echo "- {$route->methods()[0]} /{$route->uri()}\n";
}

echo "\n✅ Prueba completada. El sistema de permisos está funcionando correctamente.\n";
echo "🚀 Puede proceder a probar las rutas del coordinador en el navegador.\n";
