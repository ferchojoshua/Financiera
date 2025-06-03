<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'amount_neto',
        'order_list',
        'id_user',
        'id_agent',
        'payment_number',
        'utility',
        'status',
        'disbursement_date',
        'cancellation_date',
    ];

    /**
     * Los atributos que deben ser convertidos a fechas.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'disbursement_date',
        'cancellation_date',
    ];

    /**
     * Relación con el usuario (cliente)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
    
    /**
     * Relación con el agente
     */
    public function agent()
    {
        return $this->belongsTo(User::class, 'id_agent');
    }
    
    /**
     * Relación con la ruta
     */
    public function route()
    {
        return $this->belongsTo(Route::class, 'route_id');
    }
    
    /**
     * Alias para acceso del cliente
     */
    public function client()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
    
    /**
     * Alias para el monto (amount_neto)
     */
    public function getAmountAttribute()
    {
        return $this->amount_neto;
    }
    
    /**
     * Alias para el interés (utility)
     */
    public function getInterestAmountAttribute()
    {
        return $this->utility;
    }
} 