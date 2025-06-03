<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentAgreement extends Model
{
    use HasFactory;

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array
     */
    protected $fillable = [
        'credit_id',
        'agreement_date',
        'amount',
        'payment_date',
        'status',
        'description',
        'payment_method',
        'approved_by',
        'notes',
        'created_by',
        'updated_by',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'agreement_date' => 'date',
        'payment_date' => 'date',
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Obtener el préstamo relacionado con este acuerdo.
     */
    public function credit()
    {
        return $this->belongsTo(Credit::class);
    }

    /**
     * Obtener el usuario que aprobó este acuerdo.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Obtener el usuario que creó este acuerdo.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Obtener el usuario que actualizó este acuerdo.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Verificar si el acuerdo está pendiente.
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Verificar si el acuerdo está completado.
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    /**
     * Verificar si el acuerdo fue cancelado.
     */
    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    /**
     * Verificar si el acuerdo tiene pago parcial.
     */
    public function isPartial()
    {
        return $this->status === 'partial';
    }

    /**
     * Obtener la clase CSS para el estado.
     */
    public function getStatusClass()
    {
        switch ($this->status) {
            case 'pending':
                return 'bg-warning';
            case 'completed':
                return 'bg-success';
            case 'cancelled':
                return 'bg-danger';
            case 'partial':
                return 'bg-info';
            default:
                return 'bg-secondary';
        }
    }

    /**
     * Obtener el nombre del método de pago en español.
     */
    public function getPaymentMethodName()
    {
        switch ($this->payment_method) {
            case 'cash':
                return 'Efectivo';
            case 'bank_transfer':
                return 'Transferencia Bancaria';
            case 'check':
                return 'Cheque';
            case 'debit_card':
                return 'Tarjeta de Débito';
            case 'credit_card':
                return 'Tarjeta de Crédito';
            case 'mobile_payment':
                return 'Pago Móvil';
            case 'other':
                return 'Otro';
            default:
                return $this->payment_method;
        }
    }
}
