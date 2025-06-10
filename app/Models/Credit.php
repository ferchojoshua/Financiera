<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\User;

class Credit extends Model
{
    use HasFactory;

    /**
     * La tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'credit';

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array
     */
    protected $fillable = [
        'client_id',
        'id_user',
        'credit_number',
        'amount',
        'amount_requested',
        'amount_approved',
        'payment_number',
        'status',
        'loan_application_id',
        'interest_rate',
        'credit_type',
        'id_agent',
        'approved_by',
        'approved_at',
        'first_payment_date',
        'notes',
        'id_wallet',
        'route_id',
        'cancelled_at',
        'cancelled_by',
        'cancellation_reason',
        'payment_frequency'
    ];

    /**
     * Los atributos que deben ser convertidos a fechas.
     *
     * @var array
     */
    protected $dates = [
        'first_payment_date',
        'approved_at',
        'cancelled_at',
        'created_at',
        'updated_at'
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'amount_requested' => 'float',
        'amount_approved' => 'float',
        'interest_rate' => 'float',
        'term' => 'integer'
    ];

    /**
     * Relación con el cliente
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Relación con el agente que otorgó el crédito
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_agent');
    }

    /**
     * Relación con el usuario que aprobó el crédito
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Relación con el usuario que canceló el crédito
     */
    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    /**
     * Relación con la ruta asignada
     */
    public function route(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Route::class, 'route_id');
    }

    /**
     * Relación con los pagos
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'credit_id');
    }

    /**
     * Relación con la cartera/wallet
     */
    public function wallet(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Wallet::class, 'id_wallet');
    }

    /**
     * Accesorio para obtener el monto total a pagar (capital + intereses)
     */
    public function getTotalAmountAttribute()
    {
        $interest = $this->amount_approved * ($this->interest_rate / 100);
        return $this->amount_approved + $interest;
    }

    /**
     * Accesorio para obtener el monto pagado hasta el momento
     */
    public function getPaidAmountAttribute()
    {
        return $this->payments()->where('status', 'paid')->sum('amount');
    }

    /**
     * Accesorio para obtener el monto pendiente por pagar
     */
    public function getRemainingAmountAttribute()
    {
        return $this->total_amount - $this->paid_amount;
    }

    /**
     * Accesorio para obtener el progreso de pago en porcentaje
     */
    public function getPaymentProgressAttribute()
    {
        if ($this->total_amount <= 0) {
            return 0;
        }
        return min(100, ($this->paid_amount / $this->total_amount) * 100);
    }

    /**
     * Generar el plan de pagos para este crédito
     */
    public function generatePaymentPlan()
    {
        // Solo generar plan si no hay pagos existentes
        if ($this->payments()->count() > 0) {
            return false;
        }

        // Calcular monto de cada cuota
        $totalAmount = $this->amount_approved;
        $interestRate = $this->interest_rate / 100; // Convertir a decimal
        $term = $this->payment_number;
        
        // Calcular tasa de interés por período según frecuencia de pago
        $periodsPerYear = $this->getPeriodsPerYear();
        $ratePerPeriod = $interestRate / $periodsPerYear;
        
        // Calcular cuota usando fórmula de amortización
        $payment = $totalAmount * $ratePerPeriod * pow(1 + $ratePerPeriod, $term) / (pow(1 + $ratePerPeriod, $term) - 1);
        
        // Establecer fecha del primer pago
        $currentDate = $this->first_payment_date ?? now()->addDays(30);
        $balance = $totalAmount;

        for ($i = 0; $i < $term; $i++) {
            // Calcular interés y principal para este pago
            $interest = $balance * $ratePerPeriod;
            $principal = $payment - $interest;
            
            // Ajustar el último pago para evitar decimales
            if ($i === $term - 1) {
                $principal = $balance;
                $payment = $principal + $interest;
            }
            
            // Crear el pago programado
            $this->payments()->create([
                'installment_number' => $i + 1,
                'due_date' => $currentDate,
                'amount' => round($payment, 2),
                'principal' => round($principal, 2),
                'interest' => round($interest, 2),
                'balance' => round($balance - $principal, 2),
                'status' => 'pending'
            ]);

            // Actualizar balance y fecha para el siguiente pago
            $balance -= $principal;
            
            // Calcular próxima fecha según frecuencia de pago
            switch ($this->payment_frequency) {
                case 'daily':
                    $currentDate = $currentDate->addDay();
                    break;
                case 'weekly':
                    $currentDate = $currentDate->addWeek();
                    break;
                case 'biweekly':
                    $currentDate = $currentDate->addDays(15);
                    break;
                case 'monthly':
                default:
                    $currentDate = $currentDate->addMonth();
            }
        }

        return true;
    }

    /**
     * Obtener número de períodos por año según frecuencia de pago
     */
    private function getPeriodsPerYear()
    {
        switch ($this->payment_frequency) {
            case 'daily':
                return 360; // Usando año comercial
            case 'weekly':
                return 52;
            case 'biweekly':
                return 24;
            case 'monthly':
            default:
                return 12;
        }
    }

    /**
     * Alias para generatePaymentPlan para mantener consistencia con el nombre usado en el controlador
     */
    public function generatePaymentSchedule()
    {
        return $this->generatePaymentPlan();
    }

    /**
     * Relación con el usuario que creó el crédito
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }
} 