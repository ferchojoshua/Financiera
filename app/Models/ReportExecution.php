<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportExecution extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'executed_by',
        'execution_date',
        'parameters_used',
        'result_file_path',
        'status',
        'execution_time',
        'error_message'
    ];

    protected $dates = [
        'execution_date'
    ];

    protected $casts = [
        'parameters_used' => 'array',
        'execution_time' => 'float'
    ];

    /**
     * Relación con el reporte
     */
    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    /**
     * Relación con el usuario que ejecutó el reporte
     */
    public function executedBy()
    {
        return $this->belongsTo(User::class, 'executed_by');
    }
}
