<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkMessage extends Model
{
    protected $fillable = [
        'work_of_extension_id',
        'sender_user_id',
        'recipient_user_id',
        'message',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * Relación con el trabajo de extensión
     */
    public function work(): BelongsTo
    {
        return $this->belongsTo(WorkOfExtension::class, 'work_of_extension_id');
    }

    /**
     * Relación con el usuario remitente
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_user_id');
    }

    /**
     * Relación con el usuario destinatario
     */
    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_user_id');
    }

    /**
     * Marcar mensaje como leído
     */
    public function markAsRead(): void
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }

    /**
     * Verificar si el mensaje es del usuario actual
     */
    public function isFrom(User $user): bool
    {
        return $this->sender_user_id === $user->id;
    }

    /**
     * Verificar si el mensaje es para el usuario actual
     */
    public function isTo(User $user): bool
    {
        return $this->recipient_user_id === $user->id;
    }
}
