<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'credit_id',
        'installment_number',
        'due_date',
        'principal_amount',
        'interest_amount',
        'total_amount',
        'paid_amount',
        'payment_date',
        'status',
        'days_late',
        'late_fee',
        'payment_id'
    ];

    protected $dates = [
        'due_date',
        'payment_date'
    ];

    /**
     * Relación con el crédito
     */
    public function credit()
    {
        return $this->belongsTo(Credit::class);
    }

    /**
     * Relación con el pago
     */
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
