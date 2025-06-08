<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CreditType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'min_amount',
        'max_amount',
        'interest_rate',
        'min_term_months',
        'max_term_months',
        'requires_guarantee',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'requires_guarantee' => 'boolean',
        'is_active' => 'boolean'
    ];

    // Relaciones
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function credits()
    {
        return $this->hasMany(Credit::class);
    }

    public function loanApplications()
    {
        return $this->hasMany(LoanApplication::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Métodos
    public function isAmountValid($amount)
    {
        return $amount >= $this->min_amount && $amount <= $this->max_amount;
    }

    public function isTermValid($months)
    {
        return $months >= $this->min_term_months && $months <= $this->max_term_months;
    }

    public function calculateMonthlyInterest($amount)
    {
        return ($amount * $this->interest_rate) / 100;
    }
} 