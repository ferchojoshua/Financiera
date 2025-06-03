<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditScoring extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_application_id',
        'user_id',
        'score',
        'risk_level',
        'scoring_model',
        'financial_indicators', // JSON con indicadores financieros
        'qualitative_factors', // JSON con factores cualitativos
        'external_bureau_data', // JSON con datos de bureau
        'calculated_by',
        'calculation_date',
        'recommendation',
        'notes'
    ];

    protected $dates = [
        'calculation_date'
    ];

    protected $casts = [
        'financial_indicators' => 'array',
        'qualitative_factors' => 'array',
        'external_bureau_data' => 'array',
        'score' => 'float'
    ];

    /**
     * Relación con la solicitud de préstamo
     */
    public function loanApplication()
    {
        return $this->belongsTo(LoanApplication::class);
    }

    /**
     * Relación con el usuario (cliente)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con el analista que calculó el score
     */
    public function analyst()
    {
        return $this->belongsTo(User::class, 'calculated_by');
    }
}
