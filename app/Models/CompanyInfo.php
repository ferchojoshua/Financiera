<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyInfo extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'ruc',
        'address',
        'phone',
        'email',
        'website',
        'logo_path',
        'tax_info',
        'legal_representative',
        'footer_text',
        'receipt_message',
        'contract_footer',
        'payment_terms'
    ];
} 