<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Audit extends Model
{
    /**
     * La tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'audit';

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array
     */
    protected $fillable = [
        'id_user',
        'data',
        'action',
        'device',
        'description',
    ];

    /**
     * Los atributos que deben convertirse a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Obtener el usuario relacionado con este registro de auditoría.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    /**
     * Método estático para registrar una nueva acción en la auditoría
     *
     * @param string $action Tipo de acción realizada
     * @param string $description Descripción breve de la acción
     * @param array|string $data Datos relacionados con la acción
     * @return Audit
     */
    public static function log($action, $description, $data = null)
    {
        $userId = Auth::id() ?? null;
        
        // Preparar datos
        if (is_array($data)) {
            $data = json_encode($data);
        }
        
        // Obtener información del dispositivo/navegador
        $device = [
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ];
        
        // Crear registro de auditoría
        return self::create([
            'id_user' => $userId,
            'action' => $action,
            'description' => $description,
            'data' => $data,
            'device' => json_encode($device),
        ]);
    }
} 