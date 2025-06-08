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
        'loan_application_id',
        'credit_type_id',
        'amount',
        'term_months',
        'interest_rate',
        'payment_frequency',
        'status',
        'start_date',
        'created_by',
        'credit_number',
        'id_agent',
        'amount_requested',
        'amount_approved',
        'first_payment_date',
        'notes',
        'approved_by',
        'approved_at',
        'cancelled_at',
        'cancelled_by',
        'cancellation_reason',
        'route_id',
        'id_wallet'
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
        return $this->belongsTo(Client::class, 'client_id');
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

        $totalAmount = $this->total_amount;
        $installmentAmount = $totalAmount / $this->term;
        $currentDate = $this->first_payment_date;

        for ($i = 0; $i < $this->term; $i++) {
            $this->payments()->create([
                'due_date' => $currentDate,
                'amount' => round($installmentAmount, 2),
                'status' => 'pending',
                'installment_number' => $i + 1
            ]);

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
                    $currentDate = $currentDate->addMonth();
                    break;
                default:
                    $currentDate = $currentDate->addMonth();
            }
        }

        return true;
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