<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemPreference extends Model
{
    use HasFactory;

    /**
     * Los atributos que son asignables masivamente
     */
    protected $fillable = [
        'key',
        'value',
        'description',
        'type',
        'group',
        'is_public',
        'created_by',
        'updated_by'
    ];

    /**
     * Los atributos que deben convertirse a tipos nativos
     */
    protected $casts = [
        'is_public' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Obtener el usuario que creó la preferencia
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Obtener el usuario que actualizó la preferencia
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    
    /**
     * Obtener el valor de una preferencia por su clave
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getValue($key, $default = null)
    {
        $preference = static::where('key', $key)->first();
        
        if (!$preference) {
            return $default;
        }
        
        // Convertir el valor según el tipo
        switch ($preference->type) {
            case 'boolean':
                return filter_var($preference->value, FILTER_VALIDATE_BOOLEAN);
            case 'number':
                return is_numeric($preference->value) ? (float)$preference->value : $default;
            case 'json':
                return json_decode($preference->value, true) ?: $default;
            default:
                return $preference->value;
        }
    }
    
    /**
     * Establecer el valor de una preferencia
     *
     * @param string $key
     * @param mixed $value
     * @param int|null $userId ID del usuario que realiza el cambio
     * @return bool
     */
    public static function setValue($key, $value, $userId = null)
    {
        $preference = static::where('key', $key)->first();
        
        if (!$preference) {
            return false;
        }
        
        // Formatear el valor según el tipo
        if ($preference->type === 'json' && is_array($value)) {
            $value = json_encode($value);
        } elseif ($preference->type === 'boolean') {
            $value = filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false';
        }
        
        $preference->value = $value;
        
        if ($userId) {
            $preference->updated_by = $userId;
        }
        
        return $preference->save();
    }
    
    /**
     * Obtener todas las preferencias agrupadas por grupo
     *
     * @param bool $publicOnly Obtener solo preferencias públicas
     * @return array
     */
    public static function getAllGrouped($publicOnly = false)
    {
        $query = static::orderBy('group')->orderBy('key');
        
        if ($publicOnly) {
            $query->where('is_public', true);
        }
        
        $preferences = $query->get();
        
        $grouped = [];
        
        foreach ($preferences as $preference) {
            $group = $preference->group ?? 'general';
            
            if (!isset($grouped[$group])) {
                $grouped[$group] = [];
            }
            
            // Convertir el valor según el tipo
            switch ($preference->type) {
                case 'boolean':
                    $value = filter_var($preference->value, FILTER_VALIDATE_BOOLEAN);
                    break;
                case 'number':
                    $value = is_numeric($preference->value) ? (float)$preference->value : $preference->value;
                    break;
                case 'json':
                    $value = json_decode($preference->value, true) ?: $preference->value;
                    break;
                default:
                    $value = $preference->value;
            }
            
            $grouped[$group][$preference->key] = $value;
        }
        
        return $grouped;
    }
} 