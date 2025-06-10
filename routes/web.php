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
use App\Http\Controllers\UbicacionController;
use App\Http\Controllers\ActividadController;
use App\Http\Controllers\DatabaseFixController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AccountingController;
use App\Http\Controllers\LoanApplicationController;

Route::get('/', function () {
    return redirect('/home');
});

// Simulador
Route::middleware(['auth'])->group(function () {
    Route::get('simulator', [SimulatorController::class, 'index'])->name('simulator.index');
    Route::post('simulator/simulate', [SimulatorController::class, 'simulate'])->name('simulator.simulate');
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

// Ruta de diagnóstico temporal
Route::get('/diagnose-view', function() {
    dd([
        'current_url' => url()->current(),
        'intended_view' => 'clients.index',
        'client_class' => class_exists('App\\Models\\Client') ? 'Existe' : 'No existe',
        'Client_model_methods' => get_class_methods('App\\Models\\Client'),
        'clients_route_exists' => Route::has('clients.index') ? 'Sí' : 'No'
    ]);
});

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
        
        // Rutas de AdminWalletController con nombres completos
        Route::resource('wallet', 'AdminWalletController')->names([
            'index' => 'admin.wallet.index',
            'create' => 'admin.wallet.create',
            'store' => 'admin.wallet.store',
            'show' => 'admin.wallet.show',
            'edit' => 'admin.wallet.edit',
            'update' => 'admin.wallet.update',
            'destroy' => 'admin.wallet.destroy',
        ]);
        Route::post('wallet/{wallet}/deposit', 'AdminWalletController@deposit')->name('admin.wallet.deposit');
        Route::post('wallet/{wallet}/withdraw', 'AdminWalletController@withdraw')->name('admin.wallet.withdraw');
    });
    
    // Rutas de cliente (nueva versión)
    Route::prefix('client')->name('client.')->middleware(['auth'])->group(function() {
        Route::get('/', 'ClientController@index')->name('index');
        Route::get('/create', 'ClientController@create')->name('create');
        Route::post('/', 'ClientController@store')->name('store');
        Route::get('/{id}', 'ClientController@show')->name('show');
        Route::get('/{id}/edit', 'ClientController@edit')->name('edit');
        Route::put('/{id}', 'ClientController@update')->name('update');
        Route::delete('/{id}', 'ClientController@destroy')->name('destroy');
        
        // Filtros y cambio de sucursal
        Route::get('/filter', 'ClientController@filter')->name('filter');
        Route::get('/change-branch', 'ClientController@changeBranch')->name('change_branch');
    });
    
    // Rutas de clientes
    Route::prefix('clients')->name('clients.')->middleware(['auth'])->group(function() {
        Route::get('/', 'ClientController@index')->name('index');
        Route::get('/create', 'ClientController@create')->name('create');
        Route::post('/', 'ClientController@store')->name('store');
        Route::get('/{id}', 'ClientController@show')->name('show');
        Route::get('/{id}/edit', 'ClientController@edit')->name('edit');
        Route::put('/{id}', 'ClientController@update')->name('update');
        Route::delete('/{id}', 'ClientController@destroy')->name('destroy');
        Route::post('/{id}/records', 'ClientController@addRecord')->name('records.add');
        Route::post('/{id}/reactivate', 'ClientController@reactivate')->name('reactivate');
        Route::get('/export/{format}', 'ClientController@export')->name('export');
        
        // Filtros y cambio de sucursal
        Route::get('/filter', 'ClientController@filter')->name('filter');
        Route::get('/change-branch', 'ClientController@changeBranch')->name('change_branch');
        
        // Rutas de reportes de clientes
        Route::prefix('reports')->name('reports.clients.')->group(function () {
            Route::get('/performance', 'ClientController@performance')->name('performance');
            Route::get('/report', 'ClientController@report')->name('report');
        });
    });
    
    // Rutas de Wallet
    Route::resource('wallets', 'WalletController');
    Route::get('/wallet/index', 'WalletController@index')->name('wallets.manage');
    
    // Rutas de créditos
    Route::prefix('credits')->name('credits.')->middleware(['auth'])->group(function() {
        Route::get('/', 'CreditController@index')->name('index');
        Route::get('/create', 'CreditController@create')->name('create');
        Route::post('/', 'CreditController@store')->name('store');
        Route::get('/{id}', 'CreditController@show')->name('show');
        Route::get('/{id}/edit', 'CreditController@edit')->name('edit');
        Route::put('/{id}', 'CreditController@update')->name('update');
        Route::delete('/{id}', 'CreditController@destroy')->name('destroy');
        Route::get('/create/{client_id}', 'CreditController@createForClient')->name('create.client');
    });
    
    // Rutas para solicitudes de crédito
    Route::resource('loan-applications', LoanApplicationController::class);
    Route::post('loan-applications/{loan_application}/approve', [LoanApplicationController::class, 'approve'])->name('loan-applications.approve');
    Route::post('loan-applications/{loan_application}/reject', [LoanApplicationController::class, 'reject'])->name('loan-applications.reject');
    
    // Rutas de PYMES
    Route::prefix('pymes')->name('pymes.')->middleware(['auth'])->group(function() {
        // Solicitudes
        Route::get('/solicitudes', [PymesController::class, 'solicitudes'])->name('solicitudes');
        Route::get('/solicitudes/create', [PymesController::class, 'solicitudesCreate'])->name('solicitudes.create');
        Route::post('/solicitudes', [PymesController::class, 'solicitudesStore'])->name('solicitudes.store');
        Route::get('/solicitudes/{id}', [PymesController::class, 'solicitudesShow'])->name('solicitudes.show');
        Route::post('/solicitudes/{id}/approve', [PymesController::class, 'solicitudesApprove'])->name('solicitudes.approve');
        Route::post('/solicitudes/{id}/reject', [PymesController::class, 'solicitudesReject'])->name('solicitudes.reject');
        
        // Análisis
        Route::get('/analisis', [PymesController::class, 'analisis'])->name('analisis');
        Route::get('/analisis/create/{solicitud_id}', [PymesController::class, 'analisisCreate'])->name('analisis.create');
        Route::post('/analisis/store', [PymesController::class, 'analisisStore'])->name('analisis.store');
        Route::get('/analisis/{id}', [PymesController::class, 'analisisShow'])->name('analisis.show');
        Route::post('/analisis/store-statement', [PymesController::class, 'storeFinancialStatement'])->name('analisis.store-statement');
        Route::post('/analisis/validate-statement/{id}', [PymesController::class, 'validateFinancialStatement'])->name('analisis.validate-statement');

        // Clientes
        Route::get('/clientes', [PymesController::class, 'clientes'])->name('clientes');
        Route::get('/clientes/create', [PymesController::class, 'clientesCreate'])->name('clientes.create');
        Route::post('/clientes', [PymesController::class, 'clientesStore'])->name('clientes.store');
        Route::get('/clientes/{id}', [PymesController::class, 'clientesShow'])->name('clientes.show');
        
        // Productos
        Route::get('/productos', [PymesController::class, 'productos'])->name('productos');
        Route::get('/productos/create', [PymesController::class, 'productosCreate'])->name('productos.create');
        Route::post('/productos', [PymesController::class, 'productosStore'])->name('productos.store');
        Route::get('/productos/{id}', [PymesController::class, 'productosShow'])->name('productos.show');
        
        // Garantías
        Route::get('/garantias', [PymesController::class, 'garantias'])->name('garantias');
        Route::get('/garantias/create', [PymesController::class, 'garantiasCreate'])->name('garantias.create');
        Route::post('/garantias', [PymesController::class, 'garantiasStore'])->name('garantias.store');
        Route::get('/garantias/{id}', [PymesController::class, 'garantiasShow'])->name('garantias.show');
        Route::put('/garantias/{id}', [PymesController::class, 'garantiasUpdate'])->name('garantias.update');
    });
    
    // Rutas de crédito (mantener por compatibilidad)
    Route::resource('credit', 'CreditController');
    
    // Rutas para aprobación de créditos
    Route::get('/credit/pending/approval', 'CreditController@pendingApproval')->name('credit.pending_approval');
    Route::get('/credit/{id}/approve', 'CreditController@showApprovalForm')->name('credit.approval.form');
    Route::post('/credit/{id}/approval/process', 'CreditController@processApproval')->name('credit.approval.process');
    
    // Rutas de pago
    Route::resource('payment', 'PaymentController');
    
    // Rutas de transacción
    Route::resource('transaction', 'TransactionController');
    
    // Rutas de sucursales
    Route::resource('branches', 'BranchController');
    Route::get('branches/{branch}/templates', 'BranchController@editTemplates')->name('branches.editTemplates');
    Route::put('branches/{branch}/templates', 'BranchController@updateTemplates')->name('branches.updateTemplates');
    
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
    Route::post('routes/change-branch', 'RouteController@changeBranch')->name('routes.change_branch');
    
    // Rutas de reportes de clientes
    Route::prefix('reports/clients')->name('reports.clients.')->group(function () {
        Route::get('/performance', 'ClientController@performance')->name('performance');
        Route::get('/report', 'ClientController@report')->name('report');
    });
    
    // Rutas de estadísticas
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', 'ReportController@index')->name('index');
        Route::get('/cancelled', 'ReportController@cancelled')->name('cancelled');
        Route::get('/disbursements', 'ReportController@disbursements')->name('disbursements');
        Route::get('/active', 'ReportController@active')->name('active');
        Route::get('/overdue', 'ReportController@overdue')->name('overdue');
        Route::get('/to_cancel', 'ReportController@cancelled')->name('to_cancel');
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
    
    // Rutas de productos
    Route::resource('products', 'ProductController');
    
    // Rutas de cobranza
    Route::prefix('collection')->name('collection.')->middleware(['auth'])->group(function () {
        // Acciones de cobranza
        Route::get('/actions', [CollectionActionController::class, 'index'])->name('actions.index');
        Route::get('/actions/create', [CollectionActionController::class, 'create'])->name('actions.create');
        Route::post('/actions', [CollectionActionController::class, 'store'])->name('actions.store');
        Route::get('/actions/{id}', [CollectionActionController::class, 'show'])->name('actions.show');
        Route::get('/actions/{id}/edit', [CollectionActionController::class, 'edit'])->name('actions.edit');
        Route::put('/actions/{id}', [CollectionActionController::class, 'update'])->name('actions.update');
        Route::delete('/actions/{id}', [CollectionActionController::class, 'destroy'])->name('actions.destroy');

        // Acuerdos de pago
        Route::get('/agreements', [PaymentAgreementController::class, 'index'])->name('agreements.index');
        Route::get('/agreements/create', [PaymentAgreementController::class, 'create'])->name('agreements.create');
        Route::post('/agreements', [PaymentAgreementController::class, 'store'])->name('agreements.store');
        Route::get('/agreements/{id}', [PaymentAgreementController::class, 'show'])->name('agreements.show');
        Route::get('/agreements/{id}/edit', [PaymentAgreementController::class, 'edit'])->name('agreements.edit');
        Route::put('/agreements/{id}', [PaymentAgreementController::class, 'update'])->name('agreements.update');
        Route::delete('/agreements/{id}', [PaymentAgreementController::class, 'destroy'])->name('agreements.destroy');
    });
    
    // Rutas de contabilidad
    Route::prefix('accounting')->name('accounting.')->middleware(['auth'])->group(function () {
        Route::get('/', [AccountingController::class, 'index'])->name('index');
        Route::get('/create', [AccountingController::class, 'create'])->name('create');
        Route::post('/', [AccountingController::class, 'store'])->name('store');
        Route::get('/{id}', [AccountingController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [AccountingController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AccountingController::class, 'update'])->name('update');
        Route::delete('/{id}', [AccountingController::class, 'destroy'])->name('destroy');
        
        // Rutas para cierre mensual
        Route::get('/month-close', [AccountingController::class, 'monthClose'])->name('month-close');
        Route::post('/month-close', [AccountingController::class, 'processMonthClose'])->name('month-close.process');
        
        // Rutas para desembolsos
        Route::get('/disbursements', [AccountingController::class, 'disbursements'])->name('disbursements');
        Route::get('/disbursements/export', [AccountingController::class, 'exportDisbursements'])->name('disbursements.export');
    });
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
    Route::get('/config/preferences', [ConfigController::class, 'preferencesEdit'])->name('config.preferences.edit');
    Route::post('/config/preferences', [ConfigController::class, 'preferencesUpdate'])->name('config.preferences.update');
});

// Rutas para el cierre diario
Route::prefix('supervisor/close')->name('supervisor.close.')->group(function () {
    Route::get('/', 'SupervisorController@close')->name('index');
    Route::post('/store', 'SupervisorController@storeClose')->name('store');
    Route::post('/all', 'SupervisorController@storeAllClose')->name('all');
});

// Rutas para el módulo de ubicaciones
Route::prefix('ubicaciones')->middleware(['auth'])->group(function () {
    // Vista principal del mapa
    Route::get('/', 'UbicacionController@index')->name('ubicaciones.index');
    
    // Obtener ubicación de un agente específico
    Route::get('/{id}', 'UbicacionController@obtenerUbicacion')->name('ubicaciones.obtener');
    
    // Obtener todas las ubicaciones de los agentes
    Route::get('/api/todas', 'UbicacionController@obtenerTodasUbicaciones')->name('ubicaciones.todas');
    
    // Actualizar ubicación (para agentes)
    Route::post('/actualizar', 'UbicacionController@actualizarUbicacion')->name('ubicaciones.actualizar');
    
    // Vista de registro de actividades
    Route::get('/registro/actividades', 'UbicacionController@registroActividades')->name('ubicaciones.registro_actividades');
});

// Rutas para el módulo de actividades
Route::resource('actividades', 'ActividadController')->middleware(['auth']);
Route::get('api/cliente/{id}/actividades', 'ActividadController@actividadesCliente')
    ->name('api.cliente.actividades')
    ->middleware(['auth']);

// API para obtener clientes
Route::get('api/clientes', function() {
    return App\Models\Cliente::select('id', 'nombre', 'apellido')
        ->orderBy('nombre')
        ->get()
        ->map(function($cliente) {
            return [
                'id' => $cliente->id,
                'nombre' => $cliente->nombre . ' ' . $cliente->apellido
            ];
        });
})->name('api.clientes')->middleware(['auth']);

// Ruta de prueba para diagnosticar layouts
Route::get('clients-test', function() {
    return view('clients.index_test');
})->name('clients.test');

// Rutas para manejo de errores
Route::get('/access-denied/{module?}', function ($module = null) {
    return view('errors.access_denied', ['module' => $module]);
})->name('accessDenied');

Route::get('/error', function () {
    $message = request('message', 'Ha ocurrido un error');
    return view('errors.generic', ['message' => $message]);
})->name('error');

// Rutas para arreglar problemas de base de datos
Route::prefix('admin/database')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/fix', 'DatabaseFixController@fixDatabaseView')->name('admin.database.fix');
    Route::get('/fix/payments', 'DatabaseFixController@fixPaymentsTable')->name('admin.database.fix.payments');
    Route::post('/run-migration', 'DatabaseFixController@runMigration')->name('admin.database.run-migration');
    Route::post('/run-command', 'DatabaseFixController@runCommand')->name('admin.database.run-command');
});

// Ruta directa para el nuevo formulario de clientes (acceso garantizado)
Route::get('/new-client', function() {
    return view('client.create_client');
})->middleware(['auth'])->name('client.new');

// Ruta para guardar el cliente
Route::post('/client/store', 'ClientController@store')->middleware(['auth'])->name('client.store');

// Rutas de solicitudes
Route::prefix('solicitudes')->name('solicitudes.')->middleware(['auth'])->group(function () {
    Route::get('/', 'CreditController@solicitudes')->name('index');
    Route::get('/create', 'CreditController@createSolicitud')->name('create');
    Route::post('/', 'CreditController@storeSolicitud')->name('store');
    Route::get('/{id}', 'CreditController@showSolicitud')->name('show');
    Route::get('/{id}/edit', 'CreditController@editSolicitud')->name('edit');
    Route::put('/{id}', 'CreditController@updateSolicitud')->name('update');
    Route::delete('/{id}', 'CreditController@destroySolicitud')->name('destroy');
});
