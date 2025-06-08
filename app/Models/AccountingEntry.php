<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountingEntry extends Model
{
    use HasFactory;

    // Tipos de entrada
    const ENTRY_TYPE_INGRESO = 'ingreso';
    const ENTRY_TYPE_GASTO = 'gasto';
    const ENTRY_TYPE_AJUSTE = 'ajuste';

    protected $fillable = [
        'entry_date',
        'description',
        'entry_type',
        'amount',
        'reference',
        'category',
        'subcategory',
        'credit_id',
        'loan_application_id',
        'user_id',
        'accounting_account',
        'status',
        'notes',
        'attachment_path',
        'created_by'
    ];

    protected $casts = [
        'entry_date' => 'datetime',
        'amount' => 'decimal:2'
    ];

    /**
     * Obtener el crédito relacionado
     */
    public function credit()
    {
        return $this->belongsTo(Credit::class);
    }

    /**
     * Obtener la solicitud de préstamo relacionada
     */
    public function loanApplication()
    {
        return $this->belongsTo(LoanApplication::class, 'loan_application_id');
    }

    /**
     * Obtener el usuario relacionado
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Obtener el usuario que creó la entrada
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
