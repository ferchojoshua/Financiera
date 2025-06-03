<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CloseDay extends Model
{
    use HasFactory;
    
    /**
     * Nombre de la tabla en la base de datos.
     *
     * @var string
     */
    protected $table = 'close_day';
    
    /**
     * Los atributos que pueden ser asignados masivamente.
     *
     * @var array
     */
    protected $fillable = [
        'id_agent',
        'id_supervisor',
        'base_before',
        'collections',
        'expenses',
        'base_after',
        'notes'
    ];
    
    /**
     * Relación con el agente.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function agent()
    {
        return $this->belongsTo(User::class, 'id_agent');
    }
    
    /**
     * Relación con el supervisor.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function supervisor()
    {
        return $this->belongsTo(User::class, 'id_supervisor');
    }
}
