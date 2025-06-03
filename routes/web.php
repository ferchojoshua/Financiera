<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SimulatorController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PymesController;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\DebugController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CollectionActionController;
use App\Http\Controllers\PaymentAgreementController;
use App\Http\Controllers\CashController;
use App\Http\Controllers\GastosController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\DailyCloseController;
use App\Http\Controllers\WalletMigrationController;
use App\Http\Controllers\RouteCollectorController;
use App\Http\Controllers\CreditController;
use App\Http\Controllers\SupervisorController;
use App\Http\Controllers\SummaryController;
use App\Http\Controllers\FixPermissionsController;
use App\Http\Controllers\AuditController;

Route::get('/', function () {
    return redirect('/home');
});
Route::get('/logout', 'Auth\LoginController@logout');
Route::get('/cron', 'closeController@close_automatic');

// Ruta para redirigir /creditos a /credit (solucionar error 404)
Route::get('/creditos', function () {
    return redirect('/credit');
});

// Ruta para cobranzas
Route::get('/cobranzas', function () {
    return redirect('/collection/actions');
});

// Ruta para caja (movimientos)
Route::get('/caja', [CashController::class, 'index'])->name('caja.index');
Route::get('/caja/create', [CashController::class, 'create'])->name('cash.create');

Auth::routes();

// Rutas para usuarios autenticados
Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    
    // Admin prefijo para todas las rutas administrativas
    Route::prefix('admin')->group(function () {
        // Rutas de Usuario
        Route::resource('user', 'UserController')->names([
            'create' => 'admin.user.create',
            'store' => 'admin.user.store',
            'show' => 'admin.user.show',
            'edit' => 'admin.user.edit',
            'update' => 'admin.user.update',
            'destroy' => 'admin.user.destroy',
            'index' => 'admin.user.index',
        ]);
        
        // Rutas de Wallet Summary
        Route::get('summary-wallet', 'SummaryController@wallet')->name('summary.wallet');
        Route::get('summary-wallet/{id}', 'SummaryController@showWallet')->name('summary.wallet.show');
        
        // Otras rutas administrativas
        Route::resource('wallet', 'AdminWalletController');
        Route::post('wallet/{wallet}/deposit', 'AdminWalletController@deposit')->name('admin.wallet.deposit');
        Route::post('wallet/{wallet}/withdraw', 'AdminWalletController@withdraw')->name('admin.wallet.withdraw');
    });
    
    // Rutas de cliente
    Route::resource('client', 'ClientController');
    Route::get('clients/report', 'ClientController@report')->name('clients.report');
    Route::get('clients/performance', 'ClientController@performance')->name('clients.performance');
    
    // Rutas de Wallet
    Route::resource('wallets', 'WalletController');
    Route::get('/wallet/index', 'WalletController@index')->name('wallets.manage');
    
    // Rutas de crédito
    Route::resource('credit', 'CreditController');
    
    // Rutas de pago
    Route::resource('payment', 'PaymentController');
    
    // Rutas de transacción
    Route::resource('transaction', 'TransactionController');
    
    // Rutas de supervisión
    Route::prefix('supervisor')->name('supervisor.')->group(function () {
        Route::get('/', 'SupervisorController@index')->name('index');
        Route::get('/agent', 'SupervisorController@agent')->name('agent');
        Route::get('/agent/{id}/edit', 'SupervisorController@agentEdit')->name('agent.edit');
        Route::put('/agent/{id}', 'SupervisorController@agentUpdate')->name('agent.update');
        Route::get('/close', 'SupervisorController@close')->name('close');
        Route::get('/client', 'SupervisorController@client')->name('client');
        Route::get('/tracker', 'SupervisorController@tracker')->name('tracker');
        Route::get('/review/create', 'SupervisorController@reviewCreate')->name('review.create');
        Route::get('/statistics', 'SupervisorController@statistics')->name('statistics');
        Route::get('/cash', 'SupervisorController@cash')->name('cash');
        Route::get('/bill/create', 'SupervisorController@billCreate')->name('bill.create');
        
        // Caja
        Route::post('/cash/income', 'SupervisorController@cashIncome')->name('cash.income');
        Route::post('/cash/expense', 'SupervisorController@cashExpense')->name('cash.expense');
    });
    
    // Rutas de ruta
    Route::resource('route', 'RouteController')->names([
        'index' => 'routes.index',
        'create' => 'routes.create',
        'store' => 'routes.store',
        'show' => 'routes.show',
        'edit' => 'routes.edit',
        'update' => 'routes.update',
        'destroy' => 'routes.destroy',
    ]);
    Route::get('route/{route}/assign-credits', 'RouteController@assign_credits')->name('routes.assign_credits');
    Route::post('route/{route}/assign-credits', 'RouteController@save_assign_credits')->name('routes.save_assign_credits');
    
    
    // Rutas de estadísticas
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', 'ReportController@index')->name('index');
        Route::get('/cancelled', 'ReportController@cancelled')->name('cancelled');
        Route::get('/disbursements', 'ReportController@disbursements')->name('disbursements');
        Route::get('/active', 'ReportController@active')->name('active');
        Route::get('/overdue', 'ReportController@overdue')->name('overdue');
        Route::get('/to_cancel', 'ReportController@toCancel')->name('to_cancel');
        Route::get('/monthly_close', 'ReportController@monthlyClose')->name('monthly_close');
        Route::get('/recovery_and_disbursements', 'ReportController@recoveryAndDisbursements')->name('recovery_and_disbursements');
        Route::get('/export/{type}/{format}', 'ReportController@export')->name('export');
    });
    
    // Rutas de resumen
    Route::resource('summary', 'SummaryController');
    
    // Rutas de configuración
    Route::prefix('config')->name('config.')->group(function () {
        Route::get('/', 'ConfigController@index')->name('index');
        Route::get('/company', 'ConfigController@editCompany')->name('company.edit');
        Route::put('/company', 'ConfigController@updateCompany')->name('company.update');
        
        // Rutas para gestión de usuarios
        Route::get('/users', 'ConfigController@usersIndex')->name('users.index');
        Route::get('/users/create', 'ConfigController@usersCreate')->name('users.create');
        Route::post('/users', 'ConfigController@usersStore')->name('users.store');
        Route::get('/users/{id}/edit', 'ConfigController@usersEdit')->name('users.edit');
        Route::put('/users/{id}', 'ConfigController@usersUpdate')->name('users.update');
        Route::delete('/users/{id}', 'ConfigController@usersDestroy')->name('users.destroy');
        
        // Rutas para permisos de acceso
        Route::get('/permisos', 'ConfigController@permisosIndex')->name('permisos.index');
        Route::post('/permisos', 'ConfigController@permisosUpdate')->name('permisos.update');
        
        // Rutas para preferencias
        Route::get('/preferences', 'ConfigController@preferencesEdit')->name('preferences.edit');
        Route::put('/preferences', 'ConfigController@preferencesUpdate')->name('preferences.update');
        
        // Ruta para preferencias del sistema
        Route::get('/system-preferences', 'ConfigController@systemPreferences')->name('system_preferences');
    });
    
    // PYMES
    Route::prefix('pymes')->name('pymes.')->group(function () {
        Route::get('/clientes', 'PymesController@clientes')->name('clientes');
        Route::get('/clientes/create', 'PymesController@clientesCreate')->name('clientes.create');
        Route::post('/clientes', 'PymesController@clientesStore')->name('clientes.store');
        Route::get('/clientes/{id}', 'PymesController@clientesShow')->name('clientes.show');
        Route::get('/garantias', 'PymesController@garantias')->name('garantias');
        Route::get('/garantias/create', 'PymesController@garantiasCreate')->name('garantias.create');
        Route::post('/garantias', 'PymesController@garantiasStore')->name('garantias.store');
        Route::get('/garantias/{id}', 'PymesController@garantiasShow')->name('garantias.show');
        Route::get('/solicitudes', 'PymesController@solicitudes')->name('solicitudes');
        Route::get('/solicitudes/create', 'PymesController@solicitudesCreate')->name('solicitudes.create');
        Route::post('/solicitudes', 'PymesController@solicitudesStore')->name('solicitudes.store');
        Route::get('/solicitudes/{id}', 'PymesController@solicitudesShow')->name('solicitudes.show');
        Route::get('/analisis', 'PymesController@analisis')->name('analisis');
        Route::get('/analisis/create/{solicitud_id}', 'PymesController@analisisCreate')->name('analisis.create');
        Route::post('/analisis', 'PymesController@analisisStore')->name('analisis.store');
        Route::get('/analisis/{id}', 'PymesController@analisisShow')->name('analisis.show');
    });
    
    // Garantías
    // Route::resource('garantias', 'GarantiasController');
    
    // Cobranzas
    Route::prefix('collection')->name('collection.')->middleware(['auth', 'bypass.permissions'])->group(function () {
    Route::prefix('actions')->name('actions.')->group(function () {
        Route::get('/', 'CollectionActionController@index')->name('index');
        Route::get('/create', 'CollectionActionController@create')->name('create');
        Route::post('/', 'CollectionActionController@store')->name('store');
        Route::get('/{id}', 'CollectionActionController@show')->name('show');
        Route::get('/{id}/edit', 'CollectionActionController@edit')->name('edit');
        Route::put('/{id}', 'CollectionActionController@update')->name('update');
        Route::delete('/{id}', 'CollectionActionController@destroy')->name('destroy');
        });
    });
    
    // Contabilidad
    Route::prefix('contabilidad')->name('contabilidad.')->group(function () {
        Route::get('/', 'ContabilidadController@index')->name('index');
        Route::get('/ingresos', 'ContabilidadController@ingresos')->name('ingresos');
        Route::get('/gastos', 'ContabilidadController@gastos')->name('gastos');
        Route::get('/balance', 'ContabilidadController@balance')->name('balance');
    });
    
    // Simulador
    Route::get('simulator', 'SimulatorController@index')->name('simulator.index');
    Route::post('simulator/simulate', 'SimulatorController@simulate')->name('simulator.simulate');
});

// Ruta para solucionar problemas de configuración
Route::get('/fix-system-config', function() {
    try {
        // Insertar permisos de módulos para los roles si no existen
        $roles = \App\Models\Role::all();
        $modules = ['dashboard', 'clientes', 'creditos', 'pagos', 'cobranzas', 'reportes', 'configuracion', 'usuarios', 'contabilidad'];
        
        foreach ($roles as $role) {
            foreach ($modules as $module) {
                $hasAccess = ($role->slug === 'superadmin' || $role->slug === 'admin') ? true : false;
                
                if (!\DB::table('role_module_permissions')->where('role_id', $role->id)->where('module', $module)->exists()) {
                    \DB::table('role_module_permissions')->insert([
                        'role_id' => $role->id,
                        'module' => $module,
                        'has_access' => $hasAccess,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
        }
        
        return "Configuración del sistema corregida con éxito.";
    } catch (\Exception $e) {
        return "Error al corregir la configuración: " . $e->getMessage();
    }
});

Route::get('/fix-permissions', [FixPermissionsController::class, 'fixPermissions']);

// Rutas de auditoría y seguridad
Route::prefix('auditoria')->name('audit.')->middleware(['auth', 'bypass.permissions'])->group(function () {
    Route::get('/', [AuditController::class, 'index'])->name('index');
    Route::get('/export', [AuditController::class, 'export'])->name('export');
    Route::get('/{id}', [AuditController::class, 'show'])->name('show');
});

/**
 * Rutas para la configuración
 */
Route::group(['middleware' => ['auth']], function () {
    Route::get('/config', 'ConfigController@index')->name('config.index');
    Route::get('/config/company', 'ConfigController@editCompany')->name('config.company.edit');
    Route::put('/config/company', 'ConfigController@updateCompany')->name('config.company.update');
    
    // Rutas para la gestión de usuarios
    Route::get('/config/users', [ConfigController::class, 'usersIndex'])->name('config.users.index');
    Route::get('/config/users/create', [ConfigController::class, 'usersCreate'])->name('config.users.create');
    Route::post('/config/users', [ConfigController::class, 'usersStore'])->name('config.users.store');
    Route::get('/config/users/{id}/edit', [ConfigController::class, 'usersEdit'])->name('config.users.edit');
    Route::put('/config/users/{id}', [ConfigController::class, 'usersUpdate'])->name('config.users.update');
    Route::delete('/config/users/{id}', [ConfigController::class, 'usersDestroy'])->name('config.users.destroy');
    
    // Ruta alternativa para listado de usuarios
    Route::get('/usuarios', [ConfigController::class, 'usersIndex'])->name('usuarios.index');
    
    // Otras rutas de configuración
    Route::get('/config/permisos', 'ConfigController@permisosIndex')->name('config.permisos.index');
    Route::get('/config/system-preferences', 'ConfigController@systemPreferences')->name('config.system_preferences');
    Route::post('/config/permisos', 'ConfigController@permisosUpdate')->name('config.permisos.update');
    
    // Gestión de preferencias
    Route::get('/config/preferences', 'ConfigController@preferencesEdit')->name('config.preferences.edit');
    Route::post('/config/preferences', 'ConfigController@preferencesUpdate')->name('config.preferences.update');
});

// Rutas para el cierre diario
Route::prefix('supervisor/close')->name('supervisor.close.')->group(function () {
    Route::get('/', 'SupervisorController@close')->name('index');
    Route::post('/store', 'SupervisorController@storeClose')->name('store');
    Route::post('/all', 'SupervisorController@storeAllClose')->name('all');
});
