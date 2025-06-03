<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialStatement extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_application_id',
        'user_id',
        'statement_type', // Balance, Estado de resultados, Flujo de caja
        'statement_date',
        'period_start',
        'period_end',
        'data', // JSON con los datos financieros
        'file_path', // Si se sube un archivo
        'is_validated',
        'validated_by',
        'validation_date',
        'notes'
    ];

    protected $dates = [
        'statement_date',
        'period_start',
        'period_end',
        'validation_date'
    ];

    protected $casts = [
        'data' => 'array',
        'is_validated' => 'boolean'
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
     * Relación con el validador
     */
    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }
}
