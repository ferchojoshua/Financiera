<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditRestructure extends Model
{
    use HasFactory;

    protected $fillable = [
        'original_credit_id',
        'new_credit_id',
        'restructure_date',
        'restructure_type',
        'reason',
        'approved_by',
        'approval_date',
        'old_term_months',
        'new_term_months',
        'old_interest_rate',
        'new_interest_rate',
        'old_payment_frequency',
        'new_payment_frequency',
        'old_installment_amount',
        'new_installment_amount',
        'notes'
    ];

    protected $dates = [
        'restructure_date',
        'approval_date'
    ];

    /**
     * Relación con el crédito original
     */
    public function originalCredit()
    {
        return $this->belongsTo(Credit::class, 'original_credit_id');
    }

    /**
     * Relación con el nuevo crédito
     */
    public function newCredit()
    {
        return $this->belongsTo(Credit::class, 'new_credit_id');
    }

    /**
     * Relación con el aprobador
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
