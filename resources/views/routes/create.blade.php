@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Crear Nueva Ruta</h4>
                </div>
                <div class="card-body">
                    <!-- Mensajes de alerta -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ route('routes.store') }}">
                        @csrf
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Nombre de la Ruta <span class="text-danger">*</span></label>
                                    <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" id="name" placeholder="Nombre de la ruta" required>
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="zone">Zona</label>
                                    <input type="text" name="zone" value="{{ old('zone') }}" class="form-control @error('zone') is-invalid @enderror" id="zone" placeholder="Zona o área geográfica">
                                    @error('zone')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="description">Descripción</label>
                                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" id="description" rows="3" placeholder="Descripción de la ruta">{{ old('description') }}</textarea>
                                    @error('description')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="collector_id">Cobrador <span class="text-danger">*</span></label>
                                    <select name="collector_id" class="form-select @error('collector_id') is-invalid @enderror" id="collector_id" required>
                                        <option value="">Seleccione un Colector</option>
                                        @foreach($collectors as $collector)
                                            <option value="{{ $collector->id }}" {{ old('collector_id') == $collector->id ? 'selected' : '' }}>
                                                {{ $collector->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('collector_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="supervisor_id">Supervisor</label>
                                    <select name="supervisor_id" class="form-select @error('supervisor_id') is-invalid @enderror" id="supervisor_id">
                                        <option value="">Seleccione un supervisor</option>
                                        @foreach($supervisors as $supervisor)
                                            <option value="{{ $supervisor->id }}" {{ old('supervisor_id') == $supervisor->id ? 'selected' : '' }}>
                                                {{ $supervisor->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('supervisor_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Estado <span class="text-danger">*</span></label>
                                    <select name="status" class="form-select @error('status') is-invalid @enderror" id="status" required>
                                        <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Activa</option>
                                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactiva</option>
                                    </select>
                                    @error('status')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="branch_id">Sucursal</label>
                                    <select name="branch_id" class="form-select @error('branch_id') is-invalid @enderror" id="branch_id">
                                        <option value="">Seleccione una sucursal</option>
                                        @foreach(\App\Models\Branch::where('status', 'active')->get() as $branch)
                                            <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                                {{ $branch->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('branch_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label>Días de cobranza <span class="text-danger">*</span></label>
                                <div class="d-flex flex-wrap">
                                    <div class="form-check me-4 mb-2">
                                        <input class="form-check-input" type="checkbox" name="days[]" value="monday" id="monday" {{ in_array('monday', old('days', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="monday">Lunes</label>
                                    </div>
                                    <div class="form-check me-4 mb-2">
                                        <input class="form-check-input" type="checkbox" name="days[]" value="tuesday" id="tuesday" {{ in_array('tuesday', old('days', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="tuesday">Martes</label>
                                    </div>
                                    <div class="form-check me-4 mb-2">
                                        <input class="form-check-input" type="checkbox" name="days[]" value="wednesday" id="wednesday" {{ in_array('wednesday', old('days', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="wednesday">Miércoles</label>
                                    </div>
                                    <div class="form-check me-4 mb-2">
                                        <input class="form-check-input" type="checkbox" name="days[]" value="thursday" id="thursday" {{ in_array('thursday', old('days', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="thursday">Jueves</label>
                                    </div>
                                    <div class="form-check me-4 mb-2">
                                        <input class="form-check-input" type="checkbox" name="days[]" value="friday" id="friday" {{ in_array('friday', old('days', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="friday">Viernes</label>
                                    </div>
                                    <div class="form-check me-4 mb-2">
                                        <input class="form-check-input" type="checkbox" name="days[]" value="saturday" id="saturday" {{ in_array('saturday', old('days', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="saturday">Sábado</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="days[]" value="sunday" id="sunday" {{ in_array('sunday', old('days', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="sunday">Domingo</label>
                                    </div>
                                </div>
                                @error('days')
                                    <span class="text-danger">
                                        <small>{{ $message }}</small>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('routes.index') }}" class="btn btn-secondary">
                                <i class="fa fa-arrow-left"></i> Volver
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Guardar Ruta
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Asegurarse de que al menos un día esté seleccionado
    document.querySelector('form').addEventListener('submit', function(e) {
        const checkboxes = document.querySelectorAll('input[name="days[]"]:checked');
        if (checkboxes.length === 0) {
            e.preventDefault();
            alert('Debe seleccionar al menos un día de cobranza');
        }
    });
</script>
@endsection 