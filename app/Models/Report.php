<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'report_type',
        'query_string',
        'parameters',
        'output_format',
        'created_by',
        'is_public',
        'schedule',
        'last_run_at',
        'recipients',
        'status'
    ];

    protected $dates = [
        'last_run_at'
    ];

    protected $casts = [
        'parameters' => 'array',
        'recipients' => 'array',
        'is_public' => 'boolean'
    ];

    /**
     * Relación con el usuario que creó el reporte
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relación con las ejecuciones del reporte
     */
    public function executions()
    {
        return $this->hasMany(ReportExecution::class);
    }
}
