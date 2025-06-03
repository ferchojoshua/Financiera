<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Audit;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuditController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->middleware('auth');
        // Middleware para verificar permisos
        $this->middleware('bypass.permissions');
    }
    
    /**
     * Muestra el listado de registros de auditoría
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        try {
            // Crear la consulta base
            $query = Audit::with('user');
            
            // Aplicar filtros si existen
            if ($request->has('user_id') && $request->user_id) {
                $query->where('id_user', $request->user_id);
            }
            
            if ($request->has('action') && $request->action) {
                $query->where('action', $request->action);
            }
            
            if ($request->has('date_from') && $request->date_from) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            
            if ($request->has('date_to') && $request->date_to) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }
            
            // Ordenar y paginar resultados
            $audits = $query->orderBy('created_at', 'desc')->paginate(20);
            
            // Obtener usuarios para el filtro
            $users = User::orderBy('name')->get();
            
            // Obtener acciones únicas para el filtro
            $actions = Audit::select('action')->distinct()->pluck('action')->filter();
            
            return view('audit.index', compact('audits', 'users', 'actions'));
        } catch (\Exception $e) {
            \Log::error('Error en el módulo de auditoría: ' . $e->getMessage());
            return redirect('/home')->with('error', 'Ha ocurrido un error al acceder al módulo de auditoría: ' . $e->getMessage());
        }
    }
    
    /**
     * Muestra un registro de auditoría específico
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        try {
            $audit = Audit::with('user')->findOrFail($id);
            
            // Intentar decodificar los datos JSON si existen
            if (!empty($audit->data) && isJson($audit->data)) {
                $audit->decoded_data = json_decode($audit->data, true);
            } else {
                $audit->decoded_data = $audit->data;
            }
            
            // Intentar decodificar la información del dispositivo
            if (!empty($audit->device) && isJson($audit->device)) {
                $audit->decoded_device = json_decode($audit->device, true);
            } else {
                $audit->decoded_device = $audit->device;
            }
            
            return view('audit.show', compact('audit'));
        } catch (\Exception $e) {
            \Log::error('Error al mostrar registro de auditoría: ' . $e->getMessage());
            return redirect()->route('audit.index')->with('error', 'Error al mostrar el registro de auditoría: ' . $e->getMessage());
        }
    }
    
    /**
     * Exporta los registros de auditoría a CSV
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(Request $request)
    {
        try {
            // Crear la consulta base
            $query = Audit::with('user');
            
            // Aplicar filtros si existen (los mismos que en index)
            if ($request->has('user_id') && $request->user_id) {
                $query->where('id_user', $request->user_id);
            }
            
            if ($request->has('action') && $request->action) {
                $query->where('action', $request->action);
            }
            
            if ($request->has('date_from') && $request->date_from) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            
            if ($request->has('date_to') && $request->date_to) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }
            
            // Obtener todos los registros para exportar
            $audits = $query->orderBy('created_at', 'desc')->get();
            
            // Nombre del archivo
            $filename = 'audit_logs_' . date('Y-m-d_His') . '.csv';
            
            // Crear un archivo temporal
            $handle = fopen(storage_path('app/' . $filename), 'w');
            
            // Escribir cabeceras CSV
            fputcsv($handle, ['ID', 'Usuario', 'Acción', 'Descripción', 'Fecha', 'IP']);
            
            // Escribir datos
            foreach ($audits as $audit) {
                $device = json_decode($audit->device, true);
                $ip = $device['ip'] ?? 'No disponible';
                
                fputcsv($handle, [
                    $audit->id,
                    $audit->user ? $audit->user->name : 'Sistema',
                    $audit->action,
                    $audit->description,
                    $audit->created_at->format('d/m/Y H:i:s'),
                    $ip
                ]);
            }
            
            fclose($handle);
            
            // Registrar la exportación en la auditoría
            Audit::log(
                'export_audit', 
                'Exportación de registros de auditoría', 
                ['count' => $audits->count(), 'filters' => $request->all()]
            );
            
            // Devolver el archivo para descarga
            return response()->download(storage_path('app/' . $filename))->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            \Log::error('Error al exportar registros de auditoría: ' . $e->getMessage());
            return redirect()->route('audit.index')->with('error', 'Error al exportar los registros: ' . $e->getMessage());
        }
    }
}

/**
 * Helper para verificar si una cadena es JSON válido
 *
 * @param string $string
 * @return boolean
 */
function isJson($string) {
    if (!is_string($string)) return false;
    json_decode($string);
    return (json_last_error() == JSON_ERROR_NONE);
}
