<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

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
        return $this->belongsTo(User::class, 'client_id');
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
        return $this->belongsTo(Credit::class, 'credit_id');
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

        // Generar número de crédito
        $creditNumber = 'CR-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);

        return Credit::create([
            'client_id' => $this->client_id,
            'id_user' => $this->client_id,
            'credit_number' => $creditNumber,
            'amount' => $this->amount_requested,
            'amount_requested' => $this->amount_requested,
            'amount_approved' => $this->amount_requested,
            'payment_number' => $this->term_months,
            'status' => 'active',
            'loan_application_id' => $this->id,
            'interest_rate' => $this->interest_rate ?? 0,
            'credit_type' => $this->loan_type ?? 'pyme',
            'id_agent' => $this->analyst_id,
            'approved_by' => $this->approved_by,
            'approved_at' => now(),
            'first_payment_date' => now()->addDays(30),
            'notes' => $this->notes
        ]);
    }
}
