<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\User;

class Client extends Model
{
    /**
     * La tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'clients';

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'last_name',
        'email',
        'phone',
        'nit',
        'dui',
        'address',
        'city',
        'state',
        'country',
        'province',
        'birthdate',
        'gender',
        'civil_status',
        'house_type',
        'spouse_name',
        'spouse_job',
        'spouse_phone',
        'business_name',
        'business_type',
        'business_time',
        'business_sector',
        'economic_activity',
        'annual_revenue',
        'employee_count',
        'founding_date',
        'sales_good',
        'sales_bad',
        'weekly_average',
        'net_profit',
        'risk_category',
        'credit_notes',
        'route_id',
        'assigned_agent_id',
        'credit_score',
        'is_active',
        'status',
        'blacklisted',
        'blacklist_reason',
        'lat',
        'lng'
    ];

    /**
     * Los atributos que deben convertirse a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'birthdate' => 'date',
        'founding_date' => 'date',
        'annual_revenue' => 'decimal:2',
        'employee_count' => 'integer',
        'credit_score' => 'integer',
        'blacklisted' => 'boolean',
        'is_active' => 'boolean',
        'sales_good' => 'decimal:2',
        'sales_bad' => 'decimal:2',
        'weekly_average' => 'decimal:2',
        'net_profit' => 'decimal:2',
        'business_time' => 'integer',
        'lat' => 'decimal:7',
        'lng' => 'decimal:7'
    ];

    /**
     * Los atributos que deben convertirse a fechas.
     *
     * @var array
     */
    protected $dates = [
        'birthdate',
        'founding_date',
        'created_at',
        'updated_at'
    ];

    /**
     * Los atributos que tienen valores por defecto.
     *
     * @var array
     */
    protected $attributes = [
        'credit_score' => 70,
        'is_active' => true,
        'blacklisted' => false,
        'status' => 'active',
        'country' => 'El Salvador'
    ];

    /**
     * Accesorio para obtener el nombre completo
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->name} {$this->last_name}";
    }

    /**
     * Relación con la ruta
     */
    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }

    /**
     * Relación con el agente asignado
     */
    public function assignedAgent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_agent_id');
    }

    /**
     * Relación con el usuario que creó el registro
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relación con los registros del cliente
     */
    public function records(): HasMany
    {
        return $this->hasMany(ClientRecord::class);
    }

    /**
     * Agregar un registro al expediente del cliente
     */
    public function addRecord($type, $description, $status = 'active')
    {
        return $this->records()->create([
            'record_type' => $type,
            'description' => $description,
            'status' => $status,
            'created_by' => auth()->id()
        ]);
    }

    /**
     * Relación con los créditos
     */
    public function credits(): HasMany
    {
        return $this->hasMany(Credit::class);
    }

    /**
     * Relación con las solicitudes de crédito
     */
    public function loanApplications(): HasMany
    {
        return $this->hasMany(LoanApplication::class);
    }

    /**
     * Obtener el historial de pagos
     */
    public function paymentHistory()
    {
        // Obtener pagos realizados para todos los créditos del cliente
        return Payment::whereHas('credit', function($query) {
            $query->where('client_id', $this->id);
        })->orderBy('payment_date', 'desc')->get();
    }

    /**
     * Verificar si el cliente tiene pagos atrasados
     */
    public function hasOverduePayments()
    {
        // Verificar si hay cuotas vencidas no pagadas
        return $this->credits()->whereHas('installments', function($query) {
            $query->where('due_date', '<', now())
                  ->where('status', '!=', 'paid');
        })->exists();
    }

    /**
     * Calcular puntaje de crédito basado en historial de pagos
     * Esto es simplificado, en un sistema real sería más complejo
     */
    public function calculateCreditScore()
    {
        // Implementación simple de puntuación crediticia
        $score = 70; // Puntaje base

        // Historial de pagos (total de créditos cerrados exitosamente)
        $closedCredits = $this->credits()->where('status', 'close')->count();
        $score += $closedCredits * 5; // 5 puntos por cada crédito cerrado exitosamente

        // Créditos activos
        $activeCredits = $this->credits()->where('status', 'active')->count();
        $score -= $activeCredits * 3; // -3 puntos por cada crédito activo

        // Pagos atrasados
        $latePayments = $this->credits()
            ->whereHas('payments', function($query) {
                $query->where('status', 'late');
            })
            ->count();
        $score -= $latePayments * 10; // -10 puntos por cada pago atrasado

        // Ajustar dentro del rango 0-100
        $score = max(0, min(100, $score));

        // Actualizar el score en la base de datos
        $this->credit_score = $score;
        $this->save();

        return $score;
    }

    /**
     * Obtener notas específicas del expediente
     */
    public function getRecordsByType($type)
    {
        return $this->records()
            ->where('record_type', $type)
            ->orderBy('record_date', 'desc')
            ->get();
    }

    /**
     * Verifica si el cliente es elegible para un nuevo crédito
     */
    public function isEligibleForNewCredit()
    {
        // Un cliente no es elegible si está en lista negra
        if ($this->blacklisted) {
            return false;
        }

        // Un cliente no es elegible si tiene créditos activos con pagos atrasados
        $activeCreditsWithLatePayments = $this->credits()
            ->where('status', 'active')
            ->whereHas('payments', function($query) {
                $query->where('status', 'late');
            })
            ->count();

        if ($activeCreditsWithLatePayments > 0) {
            return false;
        }

        // Verificar score crediticio
        if ($this->credit_score < 50) { // Puntaje mínimo arbitrario
            return false;
        }

        return true;
    }

    /**
     * Obtener el tipo de cliente al que pertenece.
     */
    public function clientType(): BelongsTo
    {
        return $this->belongsTo(ClientType::class);
    }

    /**
     * Obtener el estado del cliente.
     */
    public function getStatusTextAttribute(): string
    {
        if ($this->blacklisted) {
            return 'Lista Negra';
        }
        
        if (!$this->is_active) {
            return 'Inactivo';
        }
        
        return 'Activo';
    }

    /**
     * Obtener la clase CSS para el estado del cliente.
     */
    public function getStatusClassAttribute(): string
    {
        if ($this->blacklisted) {
            return 'badge-danger';
        }
        
        if (!$this->is_active) {
            return 'badge-warning';
        }
        
        return 'badge-success';
    }
} 