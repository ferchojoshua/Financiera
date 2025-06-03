<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RouteCollector extends Model
{
    use HasFactory;
    
    /**
     * Los atributos que son asignables en masa
     */
    protected $fillable = [
        'route_id',
        'user_id',
        'is_active',
        'assigned_date',
        'end_date',
        'notes',
        'assigned_by'
    ];
    
    /**
     * Los atributos que deben convertirse a tipos nativos
     */
    protected $casts = [
        'is_active' => 'boolean',
        'assigned_date' => 'date',
        'end_date' => 'date',
    ];
    
    /**
     * Obtiene la ruta asociada a esta asignación
     */
    public function route()
    {
        return $this->belongsTo(Route::class);
    }
    
    /**
     * Obtiene el usuario colector/cobrador asignado
     */
    public function collector()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    /**
     * Obtiene el usuario que realizó la asignación
     */
    public function assignedByUser()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
    
    /**
     * Scope para obtener solo asignaciones activas
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
