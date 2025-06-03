<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanApplication extends Model
{
    use HasFactory;

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

    protected $fillable = [
        'user_id',
        'loan_type',
        'amount_requested',
        'term_months',
        'purpose',
        'interest_rate',
        'status',
        'analyst_id',
        'application_date',
        'approval_date',
        'rejection_reason',
        'notes'
    ];

    protected $dates = [
        'application_date',
        'approval_date'
    ];

    /**
     * Relación con el usuario (cliente)
     */
    public function client()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relación con el analista
     */
    public function analyst()
    {
        return $this->belongsTo(User::class, 'analyst_id');
    }

    /**
     * Relación con los documentos financieros
     */
    public function financialStatements()
    {
        return $this->hasMany(FinancialStatement::class);
    }

    /**
     * Relación con el crédito (si se aprueba)
     */
    public function credit()
    {
        return $this->hasOne(Credit::class);
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
}
