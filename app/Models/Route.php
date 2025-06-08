<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\User;

class Route extends Model
{
    use HasFactory;

    /**
     * La tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'routes';

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'assigned_agent_id',
        'is_active',
        'schedule',
        'notes'
    ];

    /**
     * Los atributos que deben convertirse a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
        'schedule' => 'array'
    ];

    /**
     * Los atributos que deben convertirse a fechas.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at'
    ];

    /**
     * Obtener el cobrador asociado a la ruta
     */
    public function collector()
    {
        return $this->belongsTo(User::class, 'collector_id');
    }

    /**
     * Relación con los créditos asignados a esta ruta
     */
    public function credits(): HasMany
    {
        return $this->hasMany(Credit::class, 'route_id');
    }

    /**
     * Obtener los clientes asignados a esta ruta
     */
    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
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
     * Obtener el agente asignado a esta ruta
     */
    public function assignedAgent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_agent_id');
    }

    /**
     * Relación con el usuario que creó la ruta
     */
    public function createdBy(): BelongsTo
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

    /**
     * Obtener todos los pagos programados para hoy en esta ruta
     */
    public function getTodaysPayments()
    {
        return Payment::whereHas('credit', function ($query) {
            $query->where('route_id', $this->id);
        })
        ->where('status', 'pending')
        ->where('due_date', now()->format('Y-m-d'))
        ->get();
    }

    /**
     * Obtener los pagos pendientes para esta ruta
     */
    public function getPendingPayments()
    {
        return Payment::whereHas('credit', function ($query) {
            $query->where('route_id', $this->id);
        })
        ->where('status', 'pending')
        ->orderBy('due_date')
        ->get();
    }

    /**
     * Obtener los pagos atrasados para esta ruta
     */
    public function getLatePayments()
    {
        return Payment::whereHas('credit', function ($query) {
            $query->where('route_id', $this->id);
        })
        ->where('status', 'late')
        ->orderBy('due_date')
        ->get();
    }

    /**
     * Obtener el monto total de pagos pendientes para esta ruta
     */
    public function getTotalPendingAmount()
    {
        return Payment::whereHas('credit', function ($query) {
            $query->where('route_id', $this->id);
        })
        ->where('status', 'pending')
        ->sum('amount');
    }

    /**
     * Obtener el monto total de pagos atrasados para esta ruta
     */
    public function getTotalLateAmount()
    {
        return Payment::whereHas('credit', function ($query) {
            $query->where('route_id', $this->id);
        })
        ->where('status', 'late')
        ->sum('amount');
    }

    /**
     * Obtener el monto total cobrado hoy para esta ruta
     */
    public function getTodaysCollectedAmount()
    {
        return Payment::whereHas('credit', function ($query) {
            $query->where('route_id', $this->id);
        })
        ->where('status', 'paid')
        ->whereDate('payment_date', now())
        ->sum('amount');
    }

    /**
     * Obtener clientes en esta ruta
     */
    public function getClients()
    {
        return Client::whereHas('credits', function ($query) {
            $query->where('route_id', $this->id)
                  ->where('status', 'active');
        })->get();
    }

    /**
     * Verificar si la ruta tiene pagos programados para hoy
     */
    public function hasPaymentsForToday()
    {
        return $this->getTodaysPayments()->count() > 0;
    }

    /**
     * Asignar un agente a esta ruta
     */
    public function assignAgent(User $agent)
    {
        $this->assigned_agent_id = $agent->id;
        $this->save();
        
        return $this;
    }
} 