<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Actividad extends Model
{
    use HasFactory;
    
    /**
     * La tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'actividades';
    
    /**
     * Los atributos que son asignables en masa.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'cliente_id',
        'tipo_actividad',
        'descripcion',
        'resultado',
        'latitud',
        'longitud',
        'direccion',
    ];
    
    /**
     * Relación con el usuario
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    /**
     * Relación con el cliente
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }
}
