<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RouteCredit extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'route_credits';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'route_id',
        'credit_id',
        'status',
        'created_by',
        'updated_by',
    ];
    
    /**
     * Get the route that owns the credit association.
     */
    public function route()
    {
        return $this->belongsTo(\App\Models\Route::class);
    }
    
    /**
     * Get the credit that belongs to the route.
     */
    public function credit()
    {
        return $this->belongsTo(\App\db_credit::class, 'credit_id');
    }
} 