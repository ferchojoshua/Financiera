<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Collateral extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'credit_id',
        'type',
        'description',
        'value',
        'status',
        'document_path',
        'verification_date',
        'verified_by',
        'notes',
        'is_pyme',
        'client_type'
    ];

    protected $dates = [
        'verification_date',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'value' => 'float',
        'is_pyme' => 'boolean'
    ];

    /**
     * Relación con el usuario (cliente)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con el crédito
     */
    public function credit()
    {
        return $this->belongsTo(Credit::class);
    }

    /**
     * Relación con el usuario que verificó la garantía
     */
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Scope para garantías de préstamos PYME
     */
    public function scopePyme($query)
    {
        return $query->where('is_pyme', true);
    }

    /**
     * Scope para garantías de préstamos personales
     */
    public function scopePersonal($query)
    {
        return $query->where('is_pyme', false);
    }

    /**
     * Scope para garantías de personas naturales
     */
    public function scopeNatural($query)
    {
        return $query->where('client_type', 'natural');
    }

    /**
     * Scope para garantías de personas jurídicas
     */
    public function scopeJuridica($query)
    {
        return $query->where('client_type', 'juridica');
    }
}
