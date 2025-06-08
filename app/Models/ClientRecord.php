<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\User;

class ClientRecord extends Model
{
    /**
     * La tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'client_records';

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array
     */
    protected $fillable = [
        'client_id', 'record_type', 'description', 'status',
        'created_by', 'updated_by', 'media_urls', 'notes'
    ];

    /**
     * Los atributos que deben convertirse a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'media_urls' => 'array'
    ];

    /**
     * Los atributos que deben convertirse a fechas.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at'
    ];

    /**
     * Tipos de registros disponibles
     */
    const TYPE_NOTE = 'note';           // Nota general
    const TYPE_DOCUMENT = 'document';   // Documento adjunto
    const TYPE_CALL = 'call';           // Registro de llamada
    const TYPE_VISIT = 'visit';         // Registro de visita
    const TYPE_PAYMENT = 'payment';     // Registro de pago
    const TYPE_LATE = 'late';           // Registro de mora
    const TYPE_CREDIT = 'credit';       // Registro de crédito
    const TYPE_WARNING = 'warning';     // Advertencia
    const TYPE_BLACKLIST = 'blacklist'; // Lista negra

    /**
     * Estados de registros disponibles
     */
    const STATUS_ACTIVE = 'active';           // Registro activo
    const STATUS_ARCHIVED = 'archived';       // Registro archivado
    const STATUS_DELETED = 'deleted';         // Registro eliminado
    const STATUS_PENDING = 'pending';         // Registro pendiente
    const STATUS_IMPORTANT = 'important';     // Registro importante
    const STATUS_RESOLVED = 'resolved';       // Registro resuelto

    /**
     * Relación con el cliente
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    /**
     * Relación con el usuario que creó el registro
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relación con el usuario que actualizó el registro
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Añadir una URL de medios al registro
     */
    public function addMediaUrl($url)
    {
        $urls = $this->media_urls ?? [];
        $urls[] = $url;
        $this->media_urls = $urls;
        $this->save();
        
        return $this;
    }

    /**
     * Marcar como activo
     */
    public function markAsActive()
    {
        $this->status = self::STATUS_ACTIVE;
        $this->save();
        
        return $this;
    }

    /**
     * Marcar como archivado
     */
    public function markAsArchived()
    {
        $this->status = self::STATUS_ARCHIVED;
        $this->save();
        
        return $this;
    }

    /**
     * Marcar como eliminado
     */
    public function markAsDeleted()
    {
        $this->status = self::STATUS_DELETED;
        $this->save();
        
        return $this;
    }

    /**
     * Scope para filtrar registros activos
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope para filtrar registros por tipo
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('record_type', $type);
    }
} 