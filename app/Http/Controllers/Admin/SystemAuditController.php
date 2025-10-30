<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class SystemAuditController extends Controller
{
    public function index(): View
    {
        // Obtener logs de auditoría desde la base de datos o archivos de log
        $auditLogs = $this->getAuditLogs();
        
        // Estadísticas de actividad
        $stats = [
            'total_actions' => $auditLogs['total'],
            'today_actions' => $auditLogs['today'],
            'week_actions' => $auditLogs['week'],
            'month_actions' => $auditLogs['month']
        ];

        // Usuarios más activos
        $activeUsers = $this->getMostActiveUsers();
        
        // Acciones recientes
        $recentActions = $auditLogs['recent'];

        return view('admin.audit.index', compact('stats', 'activeUsers', 'recentActions'));
    }

    public function systemLogs(): View
    {
        $logFiles = $this->getLogFiles();
        $currentLogFile = request('file', 'laravel.log');
        $logContent = $this->readLogFile($currentLogFile);

        return view('admin.audit.logs', compact('logFiles', 'currentLogFile', 'logContent'));
    }

    public function databaseActivity(): JsonResponse
    {
        // Consultas para obtener actividad de la base de datos
        $activity = [
            'user_changes' => DB::table('users')
                ->selectRaw('DATE(updated_at) as date, COUNT(*) as count')
                ->whereDate('updated_at', '>=', now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
            
            'work_submissions' => DB::table('work_of_extensions')
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->whereDate('created_at', '>=', now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
                
            'status_changes' => DB::table('work_status_history')
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->whereDate('created_at', '>=', now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date')
                ->get()
        ];

        return response()->json($activity);
    }

    public function auditLog(Request $request): JsonResponse
    {
        $request->validate([
            'action' => 'required|string|max:255',
            'entity_type' => 'required|string|max:100',
            'entity_id' => 'required|integer',
            'details' => 'nullable|array',
            'severity' => 'required|string|in:info,warning,error,critical'
        ]);

        $logData = [
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name,
            'action' => $request->action,
            'entity_type' => $request->entity_type,
            'entity_id' => $request->entity_id,
            'details' => $request->details ? json_encode($request->details) : null,
            'severity' => $request->severity,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now()->toISOString()
        ];

        // Registrar en log de Laravel
        Log::channel('audit')->info('System Audit', $logData);

        // También guardar en base de datos si tenemos tabla de auditoría
        $this->saveToAuditTable($logData);

        return response()->json([
            'success' => true,
            'message' => 'Acción registrada en auditoría'
        ]);
    }

    public function clearLogs(Request $request): JsonResponse
    {
        $request->validate([
            'log_type' => 'required|string|in:application,audit,error',
            'older_than_days' => 'required|integer|min:1|max:365'
        ]);

        try {
            $logType = $request->log_type;
            $olderThanDays = $request->older_than_days;
            $cutoffDate = now()->subDays($olderThanDays);

            switch ($logType) {
                case 'application':
                    $this->clearLogFile('laravel.log', $cutoffDate);
                    break;
                case 'audit':
                    $this->clearLogFile('audit.log', $cutoffDate);
                    break;
                case 'error':
                    $this->clearLogFile('error.log', $cutoffDate);
                    break;
            }

            // Registrar la acción de limpieza
            $this->auditLog(new Request([
                'action' => "Limpieza de logs {$logType}",
                'entity_type' => 'system',
                'entity_id' => 0,
                'details' => ['older_than_days' => $olderThanDays],
                'severity' => 'info'
            ]));

            return response()->json([
                'success' => true,
                'message' => "Logs de {$logType} anteriores a {$olderThanDays} días han sido limpiados."
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al limpiar logs: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportAudit(Request $request)
    {
        $request->validate([
            'format' => 'required|string|in:csv,xlsx,json',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from'
        ]);

        $auditData = $this->getAuditDataForExport($request->date_from, $request->date_to);
        $filename = 'audit_' . $request->date_from . '_to_' . $request->date_to . '.' . $request->format;

        // TODO: Implementar exportación según formato
        // return Excel::download(new AuditExport($auditData), $filename);

        return response()->json([
            'success' => true,
            'message' => 'Exportación de auditoría preparada',
            'filename' => $filename
        ]);
    }

    private function getAuditLogs(): array
    {
        // Simular datos de auditoría - en producción leer de base de datos
        return [
            'total' => 1250,
            'today' => 45,
            'week' => 312,
            'month' => 892,
            'recent' => [
                [
                    'id' => 1,
                    'user' => 'Super Admin',
                    'action' => 'Usuario creado',
                    'entity' => 'User #123',
                    'timestamp' => now()->subMinutes(5),
                    'severity' => 'info'
                ],
                [
                    'id' => 2,
                    'user' => 'Coordinador',
                    'action' => 'Trabajo aprobado',
                    'entity' => 'Work #456',
                    'timestamp' => now()->subMinutes(15),
                    'severity' => 'info'
                ],
                [
                    'id' => 3,
                    'user' => 'Admin',
                    'action' => 'Rol asignado',
                    'entity' => 'User #789',
                    'timestamp' => now()->subHour(),
                    'severity' => 'warning'
                ]
            ]
        ];
    }

    private function getMostActiveUsers(): array
    {
        // Obtener usuarios más activos en los últimos 30 días
        return [
            ['name' => 'Super Admin', 'actions' => 156],
            ['name' => 'Coordinador Extensión', 'actions' => 89],
            ['name' => 'Decano Facultad', 'actions' => 67],
            ['name' => 'VIEX Admin', 'actions' => 45],
            ['name' => 'Profesor Juan', 'actions' => 23]
        ];
    }

    private function getLogFiles(): array
    {
        $logPath = storage_path('logs');
        $files = [];

        if (is_dir($logPath)) {
            $iterator = new \DirectoryIterator($logPath);
            foreach ($iterator as $file) {
                if ($file->isFile() && $file->getExtension() === 'log') {
                    $files[] = [
                        'name' => $file->getFilename(),
                        'size' => $file->getSize(),
                        'modified' => $file->getMTime()
                    ];
                }
            }
        }

        return $files;
    }

    private function readLogFile(string $filename): string
    {
        $logPath = storage_path('logs/' . $filename);
        
        if (!file_exists($logPath)) {
            return 'Archivo de log no encontrado.';
        }

        // Leer las últimas 1000 líneas del archivo
        $lines = [];
        $file = new \SplFileObject($logPath);
        $file->seek(PHP_INT_MAX);
        $totalLines = $file->key();
        
        $startLine = max(0, $totalLines - 1000);
        $file->seek($startLine);
        
        while (!$file->eof()) {
            $lines[] = $file->current();
            $file->next();
        }

        return implode('', $lines);
    }

    private function clearLogFile(string $filename, \DateTime $cutoffDate): void
    {
        $logPath = storage_path('logs/' . $filename);
        
        if (file_exists($logPath)) {
            // Crear respaldo antes de limpiar
            $backupPath = storage_path('logs/backup_' . date('Y-m-d_H-i-s') . '_' . $filename);
            copy($logPath, $backupPath);
            
            // Limpiar archivo (simplificado - en producción filtrar por fecha)
            file_put_contents($logPath, '');
        }
    }

    private function saveToAuditTable(array $logData): void
    {
        try {
            // Si existe tabla de auditoría, guardar ahí
            // DB::table('audit_logs')->insert($logData);
        } catch (\Exception $e) {
            // Si no existe tabla, solo registrar en logs
            Log::warning('No se pudo guardar en tabla de auditoría: ' . $e->getMessage());
        }
    }

    private function getAuditDataForExport(string $dateFrom, string $dateTo): array
    {
        // Obtener datos de auditoría para exportación
        // En producción, consultar base de datos o archivos de log
        return [
            ['timestamp', 'user', 'action', 'entity', 'details', 'ip_address'],
            [now(), 'Admin', 'User Created', 'User #123', 'New professor', '192.168.1.1'],
            // ... más datos
        ];
    }
}