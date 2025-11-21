<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Objetivos de Desarrollo Sostenible (ONU)
 */
class SdgGoal extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
    ];

    public function works()
    {
        return $this->hasMany(WorkOfExtension::class, 'sdg_goal_id');
    }
}
