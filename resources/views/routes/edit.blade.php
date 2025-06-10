@extends('layouts.app')
@section('title', 'Editar Ruta')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Editar Ruta</h4>
                <div class="card-tools">
                    <a href="{{ route('routes.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('routes.update', $route->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Nombre de la Ruta <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $route->name) }}" required>
                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="description">Descripción</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $route->description) }}</textarea>
                                @error('description')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="zone">Zona</label>
                                <input type="text" class="form-control @error('zone') is-invalid @enderror" id="zone" name="zone" value="{{ old('zone', $route->zone) }}">
                                @error('zone')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="collector_id">Cobrador <span class="text-danger">*</span></label>
                                <select class="form-control @error('collector_id') is-invalid @enderror" id="collector_id" name="collector_id" required>
                                    <option value="">Seleccionar cobrador</option>
                                    @foreach($collectors as $collector)
                                        <option value="{{ $collector->id }}" {{ old('collector_id', $route->collector_id) == $collector->id ? 'selected' : '' }}>
                                            {{ $collector->name }} {{ $collector->last_name ?? '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('collector_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="supervisor_id">Supervisor</label>
                                <select class="form-control @error('supervisor_id') is-invalid @enderror" id="supervisor_id" name="supervisor_id">
                                    <option value="">Seleccionar supervisor</option>
                                    @foreach($supervisors as $supervisor)
                                        @php
                                            $currentSupervisorId = DB::table('agent_has_supervisor')
                                                ->where('id_user_agent', $route->collector_id)
                                                ->value('id_supervisor');
                                        @endphp
                                        <option value="{{ $supervisor->id }}" {{ old('supervisor_id', $currentSupervisorId) == $supervisor->id ? 'selected' : '' }}>
                                            {{ $supervisor->name }} {{ $supervisor->last_name ?? '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('supervisor_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="status">Estado <span class="text-danger">*</span></label>
                                <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="active" {{ old('status', $route->status) == 'active' ? 'selected' : '' }}>Activa</option>
                                    <option value="inactive" {{ old('status', $route->status) == 'inactive' ? 'selected' : '' }}>Inactiva</option>
                                </select>
                                @error('status')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Días de visita <span class="text-danger">*</span></label>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="monday" name="days[]" value="monday" {{ in_array('monday', $selectedDays ?? []) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="monday">Lunes</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="tuesday" name="days[]" value="tuesday" {{ in_array('tuesday', $selectedDays ?? []) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="tuesday">Martes</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="wednesday" name="days[]" value="wednesday" {{ in_array('wednesday', $selectedDays ?? []) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="wednesday">Miércoles</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="thursday" name="days[]" value="thursday" {{ in_array('thursday', $selectedDays ?? []) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="thursday">Jueves</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="friday" name="days[]" value="friday" {{ in_array('friday', $selectedDays ?? []) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="friday">Viernes</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="saturday" name="days[]" value="saturday" {{ in_array('saturday', $selectedDays ?? []) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="saturday">Sábado</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="sunday" name="days[]" value="sunday" {{ in_array('sunday', $selectedDays ?? []) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="sunday">Domingo</label>
                                </div>
                                @error('days')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-12 text-center">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Cambios
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 