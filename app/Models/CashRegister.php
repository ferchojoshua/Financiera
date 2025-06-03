<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashRegister extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cash_register';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'amount',
        'category_id',
        'description',
        'id_user_agent',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the category that owns the cash register record.
     */
    public function category()
    {
        return $this->belongsTo(CashCategory::class, 'category_id');
    }

    /**
     * Get the agent that owns the cash register record.
     */
    public function agent()
    {
        return $this->belongsTo(\App\User::class, 'id_user_agent');
    }
}
