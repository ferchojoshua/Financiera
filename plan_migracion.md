# Plan de Migración: Sistema de Préstamos

## 1. Fase de Preparación

### 1.1 Respaldo de Datos
```bash
# Crear respaldo completo de la base de datos
php artisan db:backup

# Respaldo manual desde MySQL
# mysqldump -u root -p sistema_prestamos > sistema_prestamos_backup_$(date +%Y%m%d).sql
```

### 1.2 Crear Migraciones Necesarias
```php
// Modificación de tablas para añadir columnas de referencia
php artisan make:migration add_legacy_references_to_wallets_table
```

## 2. Fase de Implementación

### 2.1 Migrar Datos de wallet a wallets

```php
// Script para migrar datos de wallet a wallets (ejecutar en tinker)
$wallets = DB::table('wallet')->get();
foreach ($wallets as $oldWallet) {
    // Determinar el usuario asociado (basado en agent_has_supervisor)
    $supervisor = DB::table('agent_has_supervisor')
        ->where('id_wallet', $oldWallet->id)
        ->first();
    
    $userId = $supervisor ? $supervisor->id_supervisor : 1;
    
    // Insertar en nueva tabla wallets
    DB::table('wallets')->updateOrInsert(
        ['legacy_id' => $oldWallet->id],
        [
            'user_id' => $userId,
            'balance' => 0, // Calcular saldo real si es posible
            'description' => $oldWallet->name ?? 'Wallet migrada',
            'created_at' => $oldWallet->created_at ?? now(),
            'updated_at' => now(),
        ]
    );
}
```

### 2.2 Actualizar Referencias en el Código

Archivos a modificar:

1. **Controladores**:
   - `adminWalletController.php`
   - `adminUserController.php`
   - `reviewController.php`
   - `paymentController.php` 
   - `closeController.php`
   - Otros que usen `db_wallet` o `wallet`

2. **Modelos**:
   - Crear o actualizar `Wallet.php` para usar `wallets` en lugar de `wallet`
   - Actualizar referencias en relaciones

3. **Vistas**:
   - Actualizar vistas que hagan referencia a `wallet`

## 3. Fase de Pruebas

### 3.1 Pruebas Unitarias y de Integración
```bash
# Crear pruebas para verificar que todo funcione correctamente
php artisan make:test WalletMigrationTest
```

### 3.2 Pruebas Manuales
- Crear billeteras
- Verificar saldos
- Verificar transacciones
- Comprobar relaciones usuario-wallet

## 4. Fase de Transición

### 4.1 Período de Operación Dual
Mantener ambos sistemas funcionando durante un tiempo limitado:

```php
// En los controladores críticos
try {
    // Primero intentar con el nuevo sistema
    $wallet = Wallet::findOrFail($id);
} catch (\Exception $e) {
    // Fallback al antiguo sistema
    $wallet = db_wallet::findOrFail($id);
    // Registrar uso del sistema antiguo para seguimiento
    Log::info("Usando wallet antigua: {$id}");
}
```

### 4.2 Desactivación Gradual
Configuración para control:
```php
// En .env o config
'USE_LEGACY_WALLET' => false,
```

## 5. Fase Final

### 5.1 Limpiar Referencias Antiguas
```php
// Una vez confirmado que todo funciona, eliminar referencias y código antiguo
```

### 5.2 Documentación
- Actualizar documentación para reflejar nueva estructura
- Crear guía de nuevas funcionalidades

## Implementación Técnica

### Cambios en Schema
```php
// Añadir legacy_id para mapeo
Schema::table('wallets', function (Blueprint $table) {
    $table->unsignedInteger('legacy_id')->nullable()->after('id');
    $table->index('legacy_id');
});
```

### Cambios en Modelos
```php
// En Wallet.php
protected $table = 'wallets';

// Relación con usuarios
public function user()
{
    return $this->belongsTo(User::class);
}

// Método para compatibilidad
public static function findByLegacyId($legacyId)
{
    return static::where('legacy_id', $legacyId)->first();
}
```

## Recomendaciones

1. Realizar esta migración en un entorno de desarrollo primero
2. Programar la migración durante un período de baja actividad
3. Tener un plan de reversión listo por si surgen problemas
4. Monitorear estrechamente el sistema durante las primeras 48 horas después de la migración 