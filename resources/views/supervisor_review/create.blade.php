@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Revisión de Cartera</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> Seleccione los filtros para revisar la cartera de créditos.
                    </div>

                    <form action="{{ route('review.show', 1) }}" method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="wallet_id">Cartera:</label>
                                    <select name="wallet_id" id="wallet_id" class="form-control" required>
                                        <option value="">Seleccione una cartera</option>
                                        @foreach($wallet as $w)
                                            <option value="{{ $w->id }}">{{ $w->name }} ({{ $w->user_name }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="agent_id">Agente:</label>
                                    <select name="agent_id" id="agent_id" class="form-control">
                                        <option value="">Todos los agentes</option>
                                        @foreach($agents as $agent)
                                            <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="status">Estado:</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="">Todos los estados</option>
                                        <option value="active">Activos</option>
                                        <option value="inprogress">En progreso</option>
                                        <option value="completed">Completados</option>
                                        <option value="rejected">Rechazados</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="country">País:</label>
                                    <select name="country" id="country" class="form-control">
                                        <option value="">Todos los países</option>
                                        @foreach($countries as $country)
                                            <option value="{{ $country->name }}">{{ $country->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="date_start">Fecha inicial:</label>
                                    <input type="text" class="form-control datepicker" id="date_start" name="date_start" placeholder="DD/MM/AAAA">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="date_end">Fecha final:</label>
                                    <input type="text" class="form-control datepicker" id="date_end" name="date_end" placeholder="DD/MM/AAAA">
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-search"></i> Buscar
                                </button>
                                <a href="{{ route('review.create') }}" class="btn btn-secondary">
                                    <i class="fa fa-undo"></i> Reiniciar
                                </a>
                            </div>
                        </div>
                    </form>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title">Información de Cartera</h5>
                                    
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Total Créditos:</label>
                                                <h4>0</h4>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Monto Total:</label>
                                                <h4>0.00</h4>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Créditos Activos:</label>
                                                <h4>0</h4>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Créditos Vencidos:</label>
                                                <h4>0</h4>
                                            </div>
                                        </div>
                                    </div>
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

@section('scripts')
<script>
    $(document).ready(function() {
        $('.datepicker').datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            todayHighlight: true,
            language: 'es'
        });

        $('#wallet_id').change(function() {
            var walletId = $(this).val();
            if (walletId) {
                // Aquí se podría hacer una llamada AJAX para cargar los agentes de esta cartera
                console.log("Cartera seleccionada: " + walletId);
            }
        });
    });
</script>
@endsection
