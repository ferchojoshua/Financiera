@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4>Simulador de Préstamos</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('simulator.simulate') }}" method="POST">
                @csrf
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="loan_type" class="form-label">Tipo de Préstamo</label>
                            <select name="loan_type" id="loan_type" class="form-control" required>
                                <option value="personal">Préstamo Personal</option>
                                <option value="pyme">Préstamo para PYME</option>
                            </select>
                            <small class="text-muted">Las PYMEs pueden tener tasas preferenciales más bajas.</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="amount" class="form-label">Monto del Préstamo</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" name="amount" id="amount" class="form-control" min="100" step="100" value="5000" required>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="term" class="form-label">Plazo (meses)</label>
                            <input type="number" name="term" id="term" class="form-control" min="1" max="60" value="12" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="interest_rate" class="form-label">Tasa de Interés Anual (%)</label>
                            <div class="input-group">
                                <input type="number" name="interest_rate" id="interest_rate" class="form-control" min="1" max="100" step="0.01" value="18" required>
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="payment_frequency" class="form-label">Frecuencia de Pago</label>
                            <select name="payment_frequency" id="payment_frequency" class="form-control" required>
                                <option value="monthly">Mensual</option>
                                <option value="biweekly">Quincenal</option>
                                <option value="weekly">Semanal</option>
                                <option value="daily">Diario</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-calculator"></i> Calcular
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const loanTypeSelect = document.getElementById('loan_type');
        const interestRateInput = document.getElementById('interest_rate');
        const defaultRate = 18;
        
        loanTypeSelect.addEventListener('change', function() {
            if (this.value === 'pyme') {
                // Mostrar tasa preferencial para PYMEs
                interestRateInput.value = (defaultRate * 0.9).toFixed(2);
            } else {
                // Restablecer tasa para préstamos personales
                interestRateInput.value = defaultRate;
            }
        });
    });
</script>
@endsection
