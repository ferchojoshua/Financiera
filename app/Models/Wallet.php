<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wallet extends Model
{
    use HasFactory;

    /**
     * La tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'wallets';

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'supervisor_id',
        'balance',
        'description',
        'wallet_type',
        'status',
        'country_id',
        'address',
        'legacy_id',
        'created_by',
        'updated_by',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'balance' => 'decimal:2',
        'legacy_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Obtener el usuario relacionado con esta billetera.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtener el supervisor relacionado con esta billetera.
     */
    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    /**
     * Obtener el usuario que creó esta transacción.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Obtener el usuario que actualizó esta transacción.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Obtener las transacciones asociadas a esta billetera.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'wallet_id');
    }

    /**
     * Aumentar el saldo de la billetera.
     *
     * @param float $amount
     * @param string $description
     * @param string $type
     * @return bool
     */
    public function deposit($amount, $description = 'Depósito', $type = 'deposit')
    {
        if ($amount <= 0) {
            return false;
        }

        // Iniciar transacción en la base de datos
        \DB::beginTransaction();

        try {
            // Actualizar saldo
            $this->balance += $amount;
            $this->save();

            // Registrar transacción
            $this->transactions()->create([
                'amount' => $amount,
                'type' => $type,
                'description' => $description,
                'created_by' => auth()->id() ?? $this->user_id,
            ]);

            \DB::commit();
            return true;
        } catch (\Exception $e) {
            \DB::rollback();
            Log::error('Error en depósito: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Disminuir el saldo de la billetera.
     *
     * @param float $amount
     * @param string $description
     * @param string $type
     * @return bool
     */
    public function withdraw($amount, $description = 'Retiro', $type = 'withdrawal')
    {
        if ($amount <= 0 || $this->balance < $amount) {
            return false;
        }

        // Iniciar transacción en la base de datos
        \DB::beginTransaction();

        try {
            // Actualizar saldo
            $this->balance -= $amount;
            $this->save();

            // Registrar transacción
            $this->transactions()->create([
                'amount' => $amount,
                'type' => $type,
                'description' => $description,
                'created_by' => auth()->id() ?? $this->user_id,
            ]);

            \DB::commit();
            return true;
        } catch (\Exception $e) {
            \DB::rollback();
            Log::error('Error en retiro: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Transferir saldo a otra billetera.
     *
     * @param Wallet $destinationWallet
     * @param float $amount
     * @param string $description
     * @return bool
     */
    public function transfer(Wallet $destinationWallet, $amount, $description = 'Transferencia')
    {
        if ($amount <= 0 || $this->balance < $amount) {
            return false;
        }

        // Iniciar transacción en la base de datos
        \DB::beginTransaction();

        try {
            // Retirar de esta billetera
            $this->balance -= $amount;
            $this->save();

            // Depositar en la billetera destino
            $destinationWallet->balance += $amount;
            $destinationWallet->save();

            // Registrar transacción de origen
            $this->transactions()->create([
                'amount' => $amount,
                'type' => 'transfer_out',
                'description' => $description,
                'created_by' => auth()->id() ?? $this->user_id,
                'reference_id' => $destinationWallet->id,
            ]);

            // Registrar transacción de destino
            $destinationWallet->transactions()->create([
                'amount' => $amount,
                'type' => 'transfer_in',
                'description' => $description,
                'created_by' => auth()->id() ?? $destinationWallet->user_id,
                'reference_id' => $this->id,
            ]);

            \DB::commit();
            return true;
        } catch (\Exception $e) {
            \DB::rollback();
            Log::error('Error en transferencia: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Método para compatibilidad - Encontrar billetera por ID antiguo
     *
     * @param int $legacyId
     * @return Wallet|null
     */
    public static function findByLegacyId($legacyId)
    {
        return static::where('legacy_id', $legacyId)->first();
    }

    /**
     * Método para compatibilidad - Actualizar ID antiguo
     *
     * @param int $legacyId
     * @return bool
     */
    public function updateLegacyId($legacyId)
    {
        $this->legacy_id = $legacyId;
        return $this->save();
    }

    /**
     * Relación con los créditos
     */
    public function credits(): HasMany
    {
        return $this->hasMany(Credit::class, 'id_wallet');
    }

    /**
     * Relación con la sucursal
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Relación con el usuario que creó la cartera
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relación con el usuario que actualizó la cartera
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
} 