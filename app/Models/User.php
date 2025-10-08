<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable {
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'cedula',
        'professor_code',
        'main_organizational_unit_id',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the user's full name.
     */
    protected function fullName(): Attribute {
        return Attribute::make(
            get: fn() => $this->name,
        );
    }

    // Relaciones

    /**
     * Unidad organizacional principal del usuario
     */
    public function organizationalUnit() {
        return $this->belongsTo(OrganizationalUnit::class, 'main_organizational_unit_id');
    }

    /**
     * Trabajos de extensión donde el usuario es el responsable principal
     */
    public function worksAsResponsible() {
        return $this->hasMany(WorkOfExtension::class, 'primary_responsible_user_id');
    }

    /**
     * Trabajos de extensión donde el usuario participa
     */
    public function worksAsParticipant() {
        return $this->hasMany(WorkParticipant::class);
    }

    /**
     * Historial de cambios de estado realizados por el usuario
     */
    public function statusChanges() {
        return $this->hasMany(WorkStatusHistory::class, 'changed_by_user_id');
    }

    /**
     * Certificaciones emitidas por el usuario
     */
    public function issuedCertifications() {
        return $this->hasMany(Certification::class, 'issued_by_user_id');
    }

    /**
     * Proyectos donde el usuario es tutor de servicio social
     */
    public function projectsAsTutor() {
        return $this->hasMany(ProjectDetail::class, 'ss_tutor_user_id');
    }

    // Métodos de negocio

    /**
     * Determinar si el usuario es profesor (tiene código de profesor)
     */
    public function isProfessor(): bool {
        return !empty($this->professor_code);
    }

    /**
     * Asignar rol por defecto basado en el código de profesor
     */
    public function assignDefaultRole(): void {
        if ($this->isProfessor()) {
            $this->assignRole('profesor');
        }
    }

    /**
     * Verificar si el usuario está activo
     */
    public function isActive(): bool {
        return $this->is_active;
    }

    /**
     * Crear un nuevo usuario con roles asignados
     */
    public static function createUser(array $data): User {
        $user = static::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'professor_code' => $data['professor_code'] ?? null,
            'main_organizational_unit_id' => $data['organizational_unit_id'],
            'is_active' => $data['is_active'] ?? true,
        ]);

        if (isset($data['roles'])) {
            $user->syncRoles($data['roles']);
        }

        return $user;
    }

    /**
     * Actualizar usuario con roles
     */
    public function updateUser(array $data): bool {
        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'professor_code' => $data['professor_code'] ?? null,
            'main_organizational_unit_id' => $data['organizational_unit_id'],
            'is_active' => $data['is_active'] ?? true,
        ];

        if (!empty($data['password'])) {
            $updateData['password'] = $data['password'];
        }

        $updated = $this->update($updateData);

        if (isset($data['roles'])) {
            $this->syncRoles($data['roles']);
        }

        return $updated;
    }

    /**
     * Obtener todos los trabajos de extensión del usuario
     */
    public function workOfExtensions() {
        return $this->hasMany(WorkOfExtension::class, 'primary_responsible_user_id');
    }

    /**
     * Scope para usuarios activos
     */
    public function scopeActive($query) {
        return $query->where('is_active', true);
    }

    /**
     * Scope para profesores
     */
    public function scopeProfessors($query) {
        return $query->whereNotNull('professor_code');
    }
}
