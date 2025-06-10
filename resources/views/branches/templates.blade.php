@extends('layouts.app')

@section('title', 'Editar Plantillas de Sucursal')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Editando Plantillas para: {{ $branch->name }}</h4>
            <a href="{{ route('branches.index') }}" class="btn btn-secondary">
                <i class="fa fa-arrow-left"></i> Volver a Sucursales
            </a>
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('branches.updateTemplates', $branch->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="contract_template" class="form-label"><h5>Plantilla del Contrato</h5></label>
                    <p class="text-muted">Edite el contenido del contrato que se generará para esta sucursal. Utilice las variables disponibles para insertar datos dinámicos.</p>
                    <textarea name="contract_template" id="contract_template" class="form-control editor" rows="20">{{ old('contract_template', $branch->contract_template) }}</textarea>
                </div>

                <div class="mb-4">
                    <label for="voucher_template" class="form-label"><h5>Plantilla del Voucher / Recibo</h5></label>
                    <p class="text-muted">Edite el contenido del recibo de pago que se imprimirá.</p>
                    <textarea name="voucher_template" id="voucher_template" class="form-control editor" rows="15">{{ old('voucher_template', $branch->voucher_template) }}</textarea>
                </div>

                <div class="alert alert-info">
                    <strong>Variables Disponibles:</strong>
                    <p>Ejemplos: <code>@{{ cliente.nombre_completo }}</code>, <code>@{{ cliente.cedula }}</code>, <code>@{{ credito.monto }}</code>, <code>@{{ sucursal.nombre }}</code>, etc.</p>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> Guardar Plantillas
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.tiny.cloud/1/AQUI_VA_TU_API_KEY_DE_TINYMCE/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        let options = {
            selector: 'textarea.editor',
            plugins: 'advlist autolink lists link image charmap print preview hr anchor pagebreak',
            toolbar_mode: 'floating',
            height: 500,
            menubar: 'file edit view insert format tools table help',
            content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }'
        };
        if (localStorage.getItem("tablerTheme") === 'dark') {
            options.skin = 'oxide-dark';
            options.content_css = 'dark';
        }
        tinymce.init(options);
    });
</script>
@endpush 