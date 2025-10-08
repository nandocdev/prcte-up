<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\WorkOfExtension;
use App\Events\WorkSubmitted;

/**
 * Comando para probar el sistema de notificaciones
 */
class TestNotificationSystem extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:notifications {--work-id=1 : ID del trabajo para probar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Probar el sistema de notificaciones disparando un evento WorkSubmitted';

    /**
     * Execute the console command.
     */
    public function handle() {
        $workId = $this->option('work-id');

        // Buscar el trabajo
        $work = WorkOfExtension::find($workId);
        if (!$work) {
            $this->error("No se encontró trabajo con ID: {$workId}");
            return 1;
        }

        // Buscar un usuario para la prueba
        $user = User::first();
        if (!$user) {
            $this->error("No se encontró ningún usuario en el sistema");
            return 1;
        }

        $this->info("Probando sistema de notificaciones...");
        $this->info("Trabajo: " . $work->getAttribute('title'));
        $this->info("Usuario: {$user->name}");

        try {
            // Disparar el evento
            WorkSubmitted::dispatch($work, $user);
            $this->info("✅ Evento WorkSubmitted disparado exitosamente");

            // Verificar si hay notificaciones pendientes en la base de datos
            $pendingNotifications = DB::table('notifications')
                ->where('created_at', '>=', now()->subMinutes(1))
                ->count();

            $this->info("📬 Notificaciones creadas: {$pendingNotifications}");

            return 0;
        } catch (\Exception $e) {
            $this->error("❌ Error al disparar evento: " . $e->getMessage());
            return 1;
        }
    }
}
