<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    protected $table = 'agent_has_client';
    public $timestamps = false;

    /**
     * Campos asignables masivamente
     */
    protected $fillable = [
        'id_agent', 'id_client', 'id_wallet'
    ];

    /**
     * Relación con el usuario (agente)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_agent');
    }

    /**
     * Relación con el cliente
     */
    public function client()
    {
        return $this->belongsTo(User::class, 'id_client');
    }

    /**
     * Relación con la billetera/cartera
     */
    public function wallet()
    {
        return $this->belongsTo(Wallet::class, 'id_wallet');
    }
} 