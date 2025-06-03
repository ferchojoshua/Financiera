<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    /**
     * La tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'branches';

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'address',
        'city',
        'state',
        'country',
        'phone',
        'email',
        'manager_id',
        'status',
        'created_by',
        'updated_by'
    ];

    /**
     * Relación con el usuario gerente
     */
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Relación con los usuarios asignados a esta sucursal
     */
    public function users()
    {
        return $this->hasMany(User::class, 'branch_id');
    }

    /**
     * Relación con las carteras de esta sucursal
     */
    public function wallets()
    {
        return $this->hasMany(Wallet::class, 'branch_id');
    }

    /**
     * Relación con los créditos asignados a esta sucursal
     */
    public function credits()
    {
        return $this->hasMany(Credit::class, 'branch_id');
    }

    /**
     * Obtener sucursales activas
     */
    public static function getActive()
    {
        return self::where('status', 'active')->get();
    }
} 