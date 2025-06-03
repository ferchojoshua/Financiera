<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'document_type',
        'name',
        'file_path',
        'description',
        'upload_date',
        'expiry_date',
        'status'
    ];

    protected $dates = [
        'upload_date',
        'expiry_date'
    ];

    /**
     * Relación con el usuario (cliente)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
