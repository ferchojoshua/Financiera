<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    use HasFactory;

    /**
     * Tabla asociada al modelo
     */
    protected $table = 'routes';

    /**
     * Atributos asignables masivamente
     */
    protected $fillable = [
        'name',
        'description',
        'collector_id',
        'status',
        'frequency',
        'start_time',
        'end_time',
        'client_count',
        'credit_count'
    ];

    /**
     * Obtener el cobrador asociado a la ruta
     */
    public function collector()
    {
        return $this->belongsTo(User::class, 'collector_id');
    }

    /**
     * Obtener los créditos asociados a la ruta
     */
    public function credits()
    {
        return $this->hasMany(Credit::class, 'route_id');
    }

    /**
     * Obtener los clientes asociados a la ruta
     */
    public function clients()
    {
        return $this->hasMany(Client::class, 'route_id');
    }

    /**
     * Get the branch that owns the route.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Relación con el supervisor
     */
    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    /**
     * Usuario que creó la ruta
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Usuario que actualizó la ruta
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Obtener los días formateados para mostrar
     */
    public function getDaysFormattedAttribute()
    {
        $daysMap = [
            'monday' => 'Lunes',
            'tuesday' => 'Martes',
            'wednesday' => 'Miércoles',
            'thursday' => 'Jueves',
            'friday' => 'Viernes',
            'saturday' => 'Sábado',
            'sunday' => 'Domingo',
        ];
        
        $daysArray = json_decode($this->days, true) ?? [];
        $formatted = [];
        
        foreach ($daysArray as $day) {
            $formatted[] = $daysMap[$day] ?? $day;
        }
        
        return implode(', ', $formatted);
    }

    /**
     * Obtener las estadísticas de la ruta
     */
    public function getStats()
    {
        $activeCredits = $this->credits()->where('status', 'active')->get();
        
        return [
            'total_credits' => $activeCredits->count(),
            'total_amount' => $activeCredits->sum('amount'),
            'total_clients' => $activeCredits->pluck('user_id')->unique()->count(),
            'overdue_credits' => $activeCredits->where('is_overdue', true)->count(),
        ];
    }
} 