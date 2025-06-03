<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    protected $table = 'bills';
    protected $fillable = [
        'created_at',
        'description',
        'id_agent',
        'amount',
        'type',
        'id_wallet'
    ];
    public $timestamps = false;
} 