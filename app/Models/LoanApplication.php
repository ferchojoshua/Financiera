<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoanApplication extends Model
{
    use HasFactory, SoftDeletes;

    // Tipos de préstamo
    const LOAN_TYPE_PERSONAL = 'personal';
    const LOAN_TYPE_PYME = 'pyme';
    
    // Estados de solicitud
    const STATUS_PENDING = 'pending';
    const STATUS_ANALYSIS = 'analysis';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_DISBURSED = 'disbursed';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array
     */
    protected $fillable = [
        'client_id',
        'credit_type_id',
        'amount_requested',
        'term_months',
        'payment_frequency',
        'notes',
        'status',
        'analyst_id',
        'approved_by',
        'approval_date',
        'approval_notes',
        'rejected_by',
        'rejection_date',
        'rejection_reason',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'amount_requested' => 'decimal:2',
        'approval_date' => 'datetime',
        'rejection_date' => 'datetime',
    ];

    /**
     * Relación con el cliente
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Relación con el analista
     */
    public function analyst(): BelongsTo
    {
        return $this->belongsTo(User::class, 'analyst_id');
    }

    /**
     * Relación con el tipo de crédito
     */
    public function creditType(): BelongsTo
    {
        return $this->belongsTo(CreditType::class);
    }

    /**
     * Relación con el usuario que creó la solicitud
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relación con el usuario que aprobó la solicitud
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Relación con el usuario que rechazó la solicitud
     */
    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    /**
     * Obtener el cliente (compatible con ambos sistemas)
     * Este método verifica si hay un client_id y usa la nueva tabla,
     * o si no, usa el user_id de la tabla users
     */
    public function getClient()
    {
        if ($this->client_id && $this->client) {
            return $this->client;
        }
        
        // Compatibilidad hacia atrás - usar tabla users
        return $this->user;
    }

    /**
     * Verifica si la solicitud es para una PYME
     */
    public function isPymeLoan()
    {
        return $this->loan_type === self::LOAN_TYPE_PYME;
    }

    /**
     * Verifica si la solicitud es para una persona natural
     */
    public function isPersonalLoan()
    {
        return $this->loan_type === self::LOAN_TYPE_PERSONAL;
    }

    /**
     * Relación con el crédito asociado
     */
    public function credit()
    {
        return $this->hasOne(Credit::class);
    }

    /**
     * Relación con los documentos de la solicitud
     */
    public function documents()
    {
        return $this->hasMany(ClientDocument::class, 'loan_application_id');
    }

    /**
     * Relación con los estados financieros
     */
    public function financialStatements()
    {
        return $this->hasMany(FinancialStatement::class);
    }

    /**
     * Método para crear un crédito asociado a la solicitud
     */
    public function createCredit()
    {
        if ($this->status !== 'approved') {
            throw new \Exception('No se puede crear un crédito para una solicitud no aprobada.');
        }

        if ($this->credit()->exists()) {
            throw new \Exception('Esta solicitud ya tiene un crédito asociado.');
        }

        return Credit::create([
            'client_id' => $this->client_id,
            'credit_type_id' => $this->credit_type_id,
            'loan_application_id' => $this->id,
            'amount' => $this->amount_requested,
            'term_months' => $this->term_months,
            'payment_frequency' => $this->payment_frequency,
            'interest_rate' => $this->creditType->interest_rate,
            'status' => 'active',
            'start_date' => now(),
            'created_by' => $this->approved_by,
        ]);
    }
}
