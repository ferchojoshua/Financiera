@extends('layouts.app')

@section('content')
    <!-- APP MAIN ==========-->
    <main id="app-main" class="app-main">
        <div class="wrap">
            <section class="app-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="widget card">
                            <div class="card-header bg-success text-white">
                                <div class="row align-items-center">
                                    <div class="col-8">
                                        <h4 class="m-0">
                                            <i class="fa fa-tags"></i> Tipos de Cliente
                                        </h4>
                                        <small>Gestión de categorías de clientes</small>
                                    </div>
                                    <div class="col-4 text-right">
                                        <button type="button" class="btn btn-light btn-sm" data-toggle="modal" data-target="#createTypeModal">
                                            <i class="fa fa-plus-circle"></i> Nuevo Tipo
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>Nombre</th>
                                                <th>Descripción</th>
                                                <th>Color</th>
                                                <th>Estado</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($types as $type)
                                                <tr>
                                                    <td>{{ $type->name }}</td>
                                                    <td>{{ $type->description }}</td>
                                                    <td>
                                                        <span class="badge" style="background-color: {{ $type->color }}">
                                                            {{ $type->color }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if($type->is_active)
                                                            <span class="badge badge-success">Activo</span>
                                                        @else
                                                            <span class="badge badge-danger">Inactivo</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <button type="button" class="btn btn-sm btn-warning edit-type" 
                                                                    data-id="{{ $type->id }}"
                                                                    data-name="{{ $type->name }}"
                                                                    data-description="{{ $type->description }}"
                                                                    data-color="{{ $type->color }}"
                                                                    data-is-active="{{ $type->is_active }}">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-danger delete-type" 
                                                                    data-id="{{ $type->id }}"
                                                                    data-name="{{ $type->name }}">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <!-- Modal para crear nuevo tipo -->
    <div class="modal fade" id="createTypeModal" tabindex="-1" role="dialog" aria-labelledby="createTypeModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('client.types.store') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="createTypeModalLabel">Nuevo Tipo de Cliente</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Nombre</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Descripción</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="color">Color</label>
                            <input type="color" class="form-control" id="color" name="color" value="#10775c">
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" checked>
                                <label class="custom-control-label" for="is_active">Activo</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para editar tipo -->
    <div class="modal fade" id="editTypeModal" tabindex="-1" role="dialog" aria-labelledby="editTypeModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="editTypeForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="editTypeModalLabel">Editar Tipo de Cliente</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit_name">Nombre</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_description">Descripción</label>
                            <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="edit_color">Color</label>
                            <input type="color" class="form-control" id="edit_color" name="color">
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="edit_is_active" name="is_active">
                                <label class="custom-control-label" for="edit_is_active">Activo</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Guardar cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
.bg-success {
    background-color: #10775c !important;
}
.btn-success {
    background-color: #10775c !important;
    border-color: #10775c !important;
}
.btn-success:hover {
    background-color: #0a5640 !important;
    border-color: #0a5640 !important;
}
.card-header {
    padding: 1rem;
}
.table-responsive {
    overflow-x: auto;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Editar tipo
    $('.edit-type').click(function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var description = $(this).data('description');
        var color = $(this).data('color');
        var isActive = $(this).data('is-active');
        
        $('#editTypeForm').attr('action', '/client/types/' + id);
        $('#edit_name').val(name);
        $('#edit_description').val(description);
        $('#edit_color').val(color);
        $('#edit_is_active').prop('checked', isActive == 1);
        
        $('#editTypeModal').modal('show');
    });
    
    // Eliminar tipo
    $('.delete-type').click(function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        
        if (confirm('¿Está seguro que desea eliminar el tipo de cliente "' + name + '"?')) {
            $.ajax({
                url: '/client/types/' + id,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    location.reload();
                },
                error: function(xhr) {
                    alert('Error al eliminar el tipo de cliente');
                }
            });
        }
    });
});
</script>
@endpush 