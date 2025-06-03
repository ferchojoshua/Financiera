<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollectionAction extends Model
{
    use HasFactory;

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array
     */
    protected $fillable = [
        'credit_id',
        'user_id',
        'action_type',
        'action_date',
        'description',
        'result',
        'status',
        'priority',
        'next_action_date',
        'contact_name',
        'contact_phone',
        'created_by',
        'updated_by',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'action_date' => 'datetime',
        'next_action_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Obtener el préstamo relacionado con esta acción.
     */
    public function credit()
    {
        return $this->belongsTo(Credit::class);
    }

    /**
     * Obtener el usuario (cliente) relacionado con esta acción.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtener el usuario que creó esta acción.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Obtener el usuario que actualizó esta acción.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Verificar si la acción está pendiente.
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Verificar si la acción está completada.
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    /**
     * Verificar si la acción falló.
     */
    public function isFailed()
    {
        return $this->status === 'failed';
    }

    /**
     * Verificar si la acción fue cancelada.
     */
    public function isCanceled()
    {
        return $this->status === 'canceled';
    }

    /**
     * Obtener la clase CSS para la prioridad.
     */
    public function getPriorityClass()
    {
        switch ($this->priority) {
            case 'low':
                return 'bg-success';
            case 'medium':
                return 'bg-info';
            case 'high':
                return 'bg-warning';
            case 'urgent':
                return 'bg-danger';
            default:
                return 'bg-secondary';
        }
    }

    /**
     * Obtener la clase CSS para el estado.
     */
    public function getStatusClass()
    {
        switch ($this->status) {
            case 'pending':
                return 'bg-warning';
            case 'completed':
                return 'bg-success';
            case 'failed':
                return 'bg-danger';
            case 'canceled':
                return 'bg-secondary';
            default:
                return 'bg-info';
        }
    }

    /**
     * Obtener el nombre del tipo de acción en español.
     */
    public function getActionTypeName()
    {
        switch ($this->action_type) {
            case 'call':
                return 'Llamada';
            case 'visit':
                return 'Visita';
            case 'message':
                return 'Mensaje';
            case 'email':
                return 'Correo';
            case 'other':
                return 'Otra';
            default:
                return $this->action_type;
        }
    }
}
