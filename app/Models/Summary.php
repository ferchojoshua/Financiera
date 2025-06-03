<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Summary extends Model
{
    protected $table = 'summary';
    
    protected $fillable = [
        'id_agent',
        'created_at',
        'amount',
        'id_credit',
        'number_index'
    ];

    public function agent()
    {
        return $this->belongsTo(User::class, 'id_agent');
    }

    public function credit()
    {
        return $this->belongsTo(Credit::class, 'id_credit');
    }
} 