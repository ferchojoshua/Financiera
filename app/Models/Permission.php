<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    /**
     * Los atributos que son asignables masivamente
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'module',
        'is_system',
    ];

    /**
     * Los atributos que deben convertirse a tipos nativos
     */
    protected $casts = [
        'is_system' => 'boolean',
    ];

    /**
     * Relación muchos a muchos con roles
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions');
    }

    /**
     * Comprueba si es un permiso del sistema
     */
    public function isSystemPermission()
    {
        return $this->is_system;
    }

    /**
     * Devuelve permisos agrupados por módulo
     */
    public static function getByModule()
    {
        return self::orderBy('module')->orderBy('name')->get()->groupBy('module');
    }
} 