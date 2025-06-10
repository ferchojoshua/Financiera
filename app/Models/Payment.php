<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\User;

class Payment extends Model
{
    /**
     * La tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'payments';

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array
     */
    protected $fillable = [
        'credit_id',
        'installment_number',
        'due_date',
        'interest',
        'principal',
        'penalties',
        'balance',
        'payment_date',
        'amount',
        'payment_method',
        'reference_number',
        'status',
        'id_agent',
        'notes'
    ];

    /**
     * Los atributos que deben convertirse a fechas.
     *
     * @var array
     */
    protected $dates = [
        'payment_date',
        'due_date',
        'created_at',
        'updated_at'
    ];

    /**
     * Los atributos que deben convertirse a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'amount' => 'float',
        'interest' => 'float',
        'principal' => 'float',
        'penalties' => 'float',
        'balance' => 'float'
    ];

    /**
     * Relación con el agente que recolectó el pago
     */
    public function collector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_agent');
    }

    /**
     * Relación con el crédito
     */
    public function credit(): BelongsTo
    {
        return $this->belongsTo(Credit::class, 'credit_id');
    }

    /**
     * Verifica si el pago está atrasado
     */
    public function isLate()
    {
        return $this->status === 'late';
    }

    /**
     * Verifica si el pago está pendiente
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Verifica si el pago está pagado
     */
    public function isPaid()
    {
        return $this->status === 'paid';
    }

    /**
     * Registra un pago
     */
    public function markAsPaid($amount, $payment_method = 'cash', $reference = null, $collector_id = null)
    {
        $this->amount = $amount;
        $this->payment_date = now();
        $this->payment_method = $payment_method;
        $this->reference_number = $reference;
        $this->id_agent = $collector_id ?? auth()->id();
        $this->status = 'paid';
        $this->save();

        // Actualizar estado del crédito si todos los pagos están completos
        $this->updateCreditStatus();

        return $this;
    }

    /**
     * Calcular mora si aplica
     */
    public function calculateLateFee()
    {
        // Solo calcular si el pago está atrasado y la fecha de vencimiento ya pasó
        if ($this->status !== 'late' || $this->due_date->isFuture()) {
            return 0;
        }

        // Días de atraso
        $daysLate = $this->due_date->diffInDays(now());
        
        // Tasa de mora diaria (ejemplo: 0.5% diario)
        $dailyRate = 0.005;
        
        // Calcular mora como porcentaje del monto original
        $lateFee = $this->amount * $dailyRate * $daysLate;
        
        // Actualizar el campo de mora
        $this->late_fee = $lateFee;
        $this->save();
        
        return $lateFee;
    }

    /**
     * Actualizar estado del crédito basado en pagos
     */
    protected function updateCreditStatus()
    {
        $credit = $this->credit;
        
        // Si todos los pagos están pagados, marcar el crédito como cerrado
        $pendingPayments = $credit->payments()->where('status', '!=', 'paid')->count();
        
        if ($pendingPayments === 0) {
            $credit->status = 'close';
            $credit->save();
        }
    }

    /**
     * Actualiza el estado de los pagos vencidos
     */
    public static function updateLatePayments()
    {
        // Buscar pagos pendientes con fecha de vencimiento pasada
        return self::where('status', 'pending')
            ->where('due_date', '<', now())
            ->update(['status' => 'late']);
    }
} 