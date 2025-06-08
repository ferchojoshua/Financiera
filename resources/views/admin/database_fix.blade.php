@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-database"></i> Administración de Base de Datos</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5>Estado de Tablas</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Tabla</th>
                                                    <th>Estado</th>
                                                    <th>Acción</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($tablesStatus as $table => $exists)
                                                <tr>
                                                    <td>{{ $table }}</td>
                                                    <td>
                                                        @if($exists)
                                                            <span class="badge bg-success">Existe</span>
                                                        @else
                                                            <span class="badge bg-danger">No existe</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if(!$exists)
                                                            @if($table == 'payments')
                                                                <button class="btn btn-sm btn-primary fix-table" data-table="{{ $table }}">
                                                                    Crear tabla
                                                                </button>
                                                            @else
                                                                <button class="btn btn-sm btn-secondary" disabled>
                                                                    No disponible
                                                                </button>
                                                            @endif
                                                        @else
                                                            <button class="btn btn-sm btn-secondary" disabled>
                                                                No requiere acción
                                                            </button>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5>Ejecutar Migración</h5>
                                </div>
                                <div class="card-body">
                                    <form id="migration-form" class="mb-3">
                                        <div class="mb-3">
                                            <label for="migration" class="form-label">Seleccione la migración</label>
                                            <select class="form-select" id="migration" name="migration">
                                                <option value="">Seleccione una migración</option>
                                                <option value="2023_09_20_create_payments_table">2023_09_20_create_payments_table</option>
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Ejecutar Migración</button>
                                    </form>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header">
                                    <h5>Ejecutar Comando</h5>
                                </div>
                                <div class="card-body">
                                    <form id="command-form">
                                        <div class="mb-3">
                                            <label for="command" class="form-label">Seleccione el comando</label>
                                            <select class="form-select" id="command" name="command">
                                                <option value="">Seleccione un comando</option>
                                                <option value="payments:create-table">payments:create-table</option>
                                                <option value="migrate">migrate</option>
                                                <option value="migrate:fresh">migrate:fresh (Advertencia: ¡Borrará todas las tablas!)</option>
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-warning">Ejecutar Comando</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Resultado</h5>
                                </div>
                                <div class="card-body">
                                    <pre id="result" class="bg-dark text-light p-3 rounded" style="min-height: 150px;">Esperando operación...</pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Función para mostrar mensajes de resultado
        function showResult(message, isError = false) {
            const resultEl = document.getElementById('result');
            resultEl.textContent = message;
            resultEl.scrollTop = resultEl.scrollHeight;
            
            if (isError) {
                resultEl.classList.add('bg-danger');
            } else {
                resultEl.classList.remove('bg-danger');
            }
        }

        // Manejador para arreglar tabla
        document.querySelectorAll('.fix-table').forEach(button => {
            button.addEventListener('click', function() {
                const table = this.getAttribute('data-table');
                showResult(`Intentando crear la tabla ${table}...`);
                
                fetch(`/admin/database/fix/${table}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            showResult(`Éxito: ${data.message}`);
                            // Recargar página después de 2 segundos
                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);
                        } else {
                            showResult(`Error: ${data.message}`, true);
                        }
                    })
                    .catch(error => {
                        showResult(`Error: ${error.message}`, true);
                    });
            });
        });

        // Manejador para ejecutar migración
        document.getElementById('migration-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const migration = document.getElementById('migration').value;
            
            if (!migration) {
                showResult('Error: Debe seleccionar una migración', true);
                return;
            }
            
            showResult(`Ejecutando migración ${migration}...`);
            
            fetch('/admin/database/run-migration', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ migration })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showResult(`Éxito: ${data.message}\n\n${data.output}`);
                    // Recargar página después de 3 segundos
                    setTimeout(() => {
                        window.location.reload();
                    }, 3000);
                } else {
                    showResult(`Error: ${data.message}`, true);
                }
            })
            .catch(error => {
                showResult(`Error: ${error.message}`, true);
            });
        });

        // Manejador para ejecutar comando
        document.getElementById('command-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const command = document.getElementById('command').value;
            
            if (!command) {
                showResult('Error: Debe seleccionar un comando', true);
                return;
            }
            
            // Confirmación adicional para comandos peligrosos
            if (command === 'migrate:fresh') {
                if (!confirm('¡ADVERTENCIA! Este comando borrará TODAS las tablas y datos. ¿Está seguro de continuar?')) {
                    return;
                }
            }
            
            showResult(`Ejecutando comando ${command}...`);
            
            fetch('/admin/database/run-command', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ command })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showResult(`Éxito: ${data.message}\n\n${data.output}`);
                    // Recargar página después de 3 segundos
                    setTimeout(() => {
                        window.location.reload();
                    }, 3000);
                } else {
                    showResult(`Error: ${data.message}`, true);
                }
            })
            .catch(error => {
                showResult(`Error: ${error.message}`, true);
            });
        });
    });
</script>
@endpush 