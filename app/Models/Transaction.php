<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array
     */
    protected $fillable = [
        'wallet_id',
        'amount',
        'type',
        'description',
        'reference_id',
        'created_by',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /**
     * Obtener la billetera asociada a esta transacción.
     */
    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    /**
     * Obtener el usuario que creó esta transacción.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Obtener la billetera de referencia en caso de transferencia.
     */
    public function referenceWallet()
    {
        return $this->belongsTo(Wallet::class, 'reference_id');
    }

    /**
     * Verificar si la transacción es un depósito.
     *
     * @return bool
     */
    public function isDeposit()
    {
        return $this->type === 'deposit';
    }

    /**
     * Verificar si la transacción es un retiro.
     *
     * @return bool
     */
    public function isWithdrawal()
    {
        return $this->type === 'withdrawal';
    }

    /**
     * Verificar si la transacción es una transferencia saliente.
     *
     * @return bool
     */
    public function isTransferOut()
    {
        return $this->type === 'transfer_out';
    }

    /**
     * Verificar si la transacción es una transferencia entrante.
     *
     * @return bool
     */
    public function isTransferIn()
    {
        return $this->type === 'transfer_in';
    }
} 