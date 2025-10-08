#!/usr/bin/env php
<?php

/**
 * Script de Prueba: Sistema de Notificaciones CU9
 * 
 * Este script verifica que el sistema de notificaciones
 * está correctamente configurado y puede enviar notificaciones.
 * 
 * Uso:
 *   php test-notifications.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\WorkOfExtension;
use App\Events\WorkReceivedInViex;
use App\Events\EvaluatorAssigned;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

echo "==========================================\n";
echo "Sistema de Notificaciones CU9 - Tests\n";
echo "==========================================\n\n";

// Test 1: Verificar que existen usuarios con roles necesarios
echo "✓ Test 1: Verificar roles de usuarios...\n";

$viexAdmins = User::role('viex_admin')->count();
$evaluators = User::role('evaluador')->count();

echo "  - Administradores VIEX: {$viexAdmins}\n";
echo "  - Evaluadores: {$evaluators}\n";

if ($viexAdmins === 0) {
    echo "  ⚠️  ADVERTENCIA: No hay usuarios con rol 'viex_admin'\n";
    echo "     Ejecute: php artisan db:seed --class=RoleSeeder\n";
}

if ($evaluators === 0) {
    echo "  ⚠️  ADVERTENCIA: No hay usuarios con rol 'evaluador'\n";
}

echo "\n";

// Test 2: Verificar configuración de mail
echo "✓ Test 2: Verificar configuración de correo...\n";

$mailDriver = config('mail.default');
$mailFrom = config('mail.from.address');

echo "  - Driver: {$mailDriver}\n";
echo "  - From: {$mailFrom}\n";

if ($mailDriver === 'log') {
    echo "  ℹ️  INFO: Usando driver 'log' (desarrollo)\n";
    echo "     Los correos se guardarán en: storage/logs/laravel.log\n";
} else {
    echo "  ℹ️  INFO: Usando driver '{$mailDriver}' (producción)\n";
}

echo "\n";

// Test 3: Verificar configuración de colas
echo "✓ Test 3: Verificar configuración de colas...\n";

$queueDriver = config('queue.default');
echo "  - Driver: {$queueDriver}\n";

if ($queueDriver === 'sync') {
    echo "  ⚠️  ADVERTENCIA: Usando driver 'sync' (desarrollo)\n";
    echo "     Para producción, use 'database' o 'redis'\n";
} else {
    echo "  ✓ OK: Driver '{$queueDriver}' configurado\n";
}

echo "\n";

// Test 4: Verificar tabla de notificaciones
echo "✓ Test 4: Verificar tabla de notificaciones...\n";

try {
    $notificationCount = DB::table('notifications')->count();
    echo "  ✓ Tabla 'notifications' existe\n";
    echo "  - Notificaciones en BD: {$notificationCount}\n";
} catch (Exception $e) {
    echo "  ❌ ERROR: Tabla 'notifications' no existe\n";
    echo "     Ejecute: php artisan migrate\n";
}

echo "\n";

// Test 5: Verificar eventos registrados
echo "✓ Test 5: Verificar eventos en EventServiceProvider...\n";

$events = app('events')->getListeners(WorkReceivedInViex::class);
if (!empty($events)) {
    echo "  ✓ WorkReceivedInViex está registrado\n";
} else {
    echo "  ❌ WorkReceivedInViex NO está registrado\n";
}

$events = app('events')->getListeners(EvaluatorAssigned::class);
if (!empty($events)) {
    echo "  ✓ EvaluatorAssigned está registrado\n";
} else {
    echo "  ❌ EvaluatorAssigned NO está registrado\n";
}

echo "\n";

// Test 6: Intentar disparar evento de prueba (opcional)
echo "✓ Test 6: Prueba de envío de evento...\n";

$work = WorkOfExtension::first();
$admin = User::role('viex_admin')->first();

if ($work && $admin) {
    echo "  ℹ️  Disparando evento de prueba...\n";
    
    try {
        event(new WorkReceivedInViex($work, $admin));
        echo "  ✓ Evento disparado correctamente\n";
        
        if ($mailDriver === 'log') {
            echo "  ℹ️  Revise el archivo: storage/logs/laravel.log\n";
        }
        
        if ($queueDriver === 'database') {
            $jobsCount = DB::table('jobs')->count();
            echo "  - Trabajos en cola: {$jobsCount}\n";
            if ($jobsCount > 0) {
                echo "  ⚠️  Ejecute: php artisan queue:work\n";
            }
        }
        
    } catch (Exception $e) {
        echo "  ❌ ERROR al disparar evento: " . $e->getMessage() . "\n";
    }
    
} else {
    echo "  ⚠️  No hay datos de prueba (trabajo o admin VIEX)\n";
    echo "     Cree un trabajo y un usuario viex_admin primero\n";
}

echo "\n";

// Resumen final
echo "==========================================\n";
echo "Resumen de la Verificación\n";
echo "==========================================\n";

$allOk = true;

if ($viexAdmins === 0 || $evaluators === 0) {
    echo "❌ Faltan roles necesarios\n";
    $allOk = false;
}

if (!isset($notificationCount)) {
    echo "❌ Tabla de notificaciones no existe\n";
    $allOk = false;
}

if ($allOk) {
    echo "✅ Sistema de notificaciones configurado correctamente\n";
    echo "\nPara producción, recuerde:\n";
    echo "1. Configurar SMTP en .env\n";
    echo "2. Cambiar QUEUE_CONNECTION=database\n";
    echo "3. Ejecutar: php artisan queue:work\n";
} else {
    echo "⚠️  Hay configuraciones pendientes\n";
    echo "Revise los mensajes de advertencia arriba\n";
}

echo "\n";
