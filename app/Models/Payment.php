<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'summary';
    
    protected $fillable = [
        'amount',
        'id_credit',
        'id_agent',
        'created_at'
    ];

    public function credit()
    {
        return $this->belongsTo(Credit::class, 'id_credit');
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'id_agent');
    }
} 