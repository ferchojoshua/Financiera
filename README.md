## Sistema de préstamos
#### Actualizado

Sistema de préstamos con roles de usuarios, desarrollado en Laravel + Blade. Puede gestionar bóvedas, rutas de cobro, agentes, supervisores, cierres de ruta, histórico de pagos, estadística de agente, reporte de gastos, perfil de cliente, entre otras cosas.

[![Deploy](https://www.herokucdn.com/deploy/button.svg)](https://heroku.com/deploy?template=https://github.com/leifermendez/sistema-prestamos) <a href="https://www.buymeacoffee.com/leifermendez" target="_blank"><img src="https://www.buymeacoffee.com/assets/img/custom_images/orange_img.png" alt="Buy Me A Coffee" style="height: 41px !important;width: 174px !important;box-shadow: 0px 3px 2px 0px rgba(190, 190, 190, 0.5) !important;-webkit-box-shadow: 0px 3px 2px 0px rgba(190, 190, 190, 0.5) !important;" ></a>

#### Video Instalación
[Ver Video](https://www.youtube.com/watch?v=g1KBAwJ8r4k)

#### Ver demo
[DEMO](https://sistema-prestamos-youtube.herokuapp.com/login)

> Los datos se borran automaticamente cada 60 minutos

#### ¿Que puedo hacer con este sistema?

Gestionar roles de usuarios:
- Usuario
- Agente
- Supervisor
- Admin

#### Usuario
Hace referencia de una persona a quien se presta el dinero esta persona puede elegir entre N número de cuotas a pagar su préstamo con un % de interés.

#### Agente
Será el encargado de buscar nuevos clientes, registrar pagos, realizar la ruta de cobro siempre bajo el mando de un supervisor

![](https://i.imgur.com/kbvwudQ.gif)

#### Supervisor
Tiene bajo su control una bóveda de cual posee un historial de transacciones con un monto base y a su vez puede asignar sub montos a los agentes los cuales deben realizar préstamos y cobros.

![](https://i.imgur.com/DdkdJds.gif)

#### Admin
Encargado de crear las bóvedas, supervisor y agente. Siempre tiene el control de todos los otros módulos

![](https://i.imgur.com/KAX76ui.gif)

### Requerimientos
```
 "php": ">=5.6.4"
```

### Instalación
Ejecutar los siguientes comandos en orden
```cmd
git clone https://github.com/leifermendez/sistema-prestamos.git
```
```cmd
cd sistema-prestamos
```
```cmd
composer install
```
Seguidamente recuerda que por seguridad el archivo <b>"<em>.env</em>"</b> no se copia, para ello dispones del mismo pero con el nombre
<b>"<em>.env.example</em>"</b> el cual deberás renombrar a <b>"<em>.env</em>"</b> solamente.

Recuerda también ingresar en el archivo <b>"<em>.env</b>"</em> los datos de conexión a la base de datos que deberas haber creado previamente, esto es importante para poder continuar con el siguiente paso y generar el <b>"<em>key</b>"</em>.
```cmd
php artisan key:generate
```
```cmd
php artisan migrate:install
```
```cmd
php artisan migrate
```
```cmd
php artisan db:seed

php artisan migrate:fresh --seed

php artisan serve
```

Optimiza el funcionamiento de las fechas estableciendo tu zona horaria [Ver zonas horarias](https://www.php.net/manual/es/timezones.php)

__config/app.php__
```php
    ....
    'timezone' => 'Europe/Madrid',
    ....
```

__NOTA:__ Recuerda para un optimo funcionamiento en modo PRODUCCION en el archivo `.env` establece
 los siguientes valores de esta manera se desactiva los logs.
```
APP_ENV=production
APP_DEBUG=false
```


### Usurios
Luego de correr con exito la migracion y los seeders, el sistema crea varios usuarios para comenzar a probar

__Rol__: `admin`
__User__:`admin@admin.com`
__Contraseña__:`12345678`


__Rol__: `supervisor`
__User__:`supervisor@supervisor.com`
__Contraseña__:`12345678`


__Rol__: `agente`
__User__:`agente@agente.com`
__Contraseña__:`12345678`


## Migraciones Seguras

Para evitar la pérdida de datos durante las migraciones, se ha implementado un sistema de migraciones seguras con las siguientes características:

### 1. Comando `migrate:safe`

Este comando personalizado permite ejecutar migraciones sin perder datos existentes:

```bash
php artisan migrate:safe
```

Características:
- Crea un respaldo en memoria de los usuarios antes de migrar
- Restaura automáticamente los usuarios si se pierden durante la migración
- Ejecuta migraciones de forma gradual (step by step)
- Ofrece la opción de ejecutar seeders de restauración si no hay usuarios

Opciones disponibles:
- `--path`: Especificar una ruta de migración específica
- `--force`: Forzar ejecución en producción

### 2. Migraciones con Verificación

Todas las nuevas migraciones incluyen verificación de existencia previa:

```php
if (!Schema::hasTable('nombre_tabla')) {
    Schema::create('nombre_tabla', function (Blueprint $table) {
        // Definición de la tabla
    });
}
```

### 3. Sistema de Respaldo de Usuarios

Se ha implementado un sistema de auditoría y respaldo de usuarios mediante:
- Tabla `user_management` para registrar cambios
- Triggers de base de datos que capturan automáticamente modificaciones
- Seeder `RestoreUsersSeeder` para restaurar usuarios base si es necesario

### Recomendaciones para Desarrolladores

1. **Siempre usar `migrate:safe`** en lugar de `migrate` o `migrate:fresh`
2. Agregar la verificación `if (!Schema::hasTable())` en todas las migraciones nuevas
3. Para modificar tablas existentes, usar `Schema::table()` en lugar de `Schema::create()`
4. Realizar respaldos regulares de la base de datos completa

## Usuarios por Defecto

El sistema incluye los siguientes usuarios predeterminados:

| Rol | Usuario | Contraseña | Email |
|-----|---------|------------|-------|
| Super Admin | superadmin | password | admin@prestamos.com |
| Admin | admin | password | admin2@prestamos.com |
| Supervisor | supervisor | password | supervisor@prestamos.com |
| Agente | agente | password | agente@prestamos.com |

