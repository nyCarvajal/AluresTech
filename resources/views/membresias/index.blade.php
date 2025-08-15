@extends('layouts.vertical', ['subtitle' => 'Membresias'])


@section('content')
<div class="container-fluid">
    {{-- Mensajes flash --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Listado de Membresías</h5>
                    {{-- Botón para abrir modal de creación --}}
                    <button 
                        class="btn btn-primary" 
                        data-bs-toggle="modal" 
                        data-bs-target="#createMembresiaModal"
                    >
                        + Nueva Membresía
                    </button>
                </div>

                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Descripción</th>
                                <th>Clases</th>
                                <th>Reservas</th>
                                <th>Valor</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($membresias as $membresia)
                                <tr>
                                    <td>{{ $loop->iteration + ($membresias->currentPage() - 1) * $membresias->perPage() }}</td>
                                    <td>{{ $membresia->descripcion }}</td>
                                    <td>{{ $membresia->clases }}</td>
                                    <td>{{ $membresia->reservas }}</td>
                                    <td>${{ number_format($membresia->valor, 2) }}</td>
                                    <td>
                                        {{-- Botón Editar --}}
                                        <button 
                                            class="btn btn-sm btn-info btn-edit-membresia"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editMembresiaModal"
                                            data-id="{{ $membresia->id }}"
                                            data-descripcion="{{ $membresia->descripcion }}"
                                            data-clases="{{ $membresia->clases }}"
                                            data-reservas="{{ $membresia->reservas }}"
                                            data-valor="{{ $membresia->valor }}"
                                        >
                                            Editar
                                        </button>

                                        {{-- Formulario para eliminar --}}
                                        <form 
                                            action="{{ route('membresias.destroy', $membresia) }}" 
                                            method="POST" 
                                            class="d-inline"
                                            onsubmit="return confirm('¿Desea eliminar esta membresía?')"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger">
                                                Eliminar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach

                            @if($membresias->isEmpty())
                                <tr>
                                    <td colspan="6" class="text-center">No hay membresías registradas.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ===========================
     Modal: Crear Membresía
   =========================== --}}
<div 
    class="modal fade" 
    id="createMembresiaModal" 
    tabindex="-1" 
    aria-labelledby="createMembresiaModalLabel" 
    aria-hidden="true"
>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crear Membresía</h5>
                <button 
                    type="button" 
                    class="btn-close" 
                    data-bs-dismiss="modal" 
                    aria-label="Cerrar"
                ></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('membresias.store') }}" method="POST">
                    @csrf

                    {{-- Usamos el partial de campos, con $membresia = null --}}
                    @include('membresias.partials.fields', ['membresia' => null])

                    <div class="text-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ===========================
     Modal: Editar Membresía
   =========================== --}}
<div 
    class="modal fade" 
    id="editMembresiaModal" 
    tabindex="-1" 
    aria-labelledby="editMembresiaModalLabel" 
    aria-hidden="true"
>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Membresía</h5>
                <button 
                    type="button" 
                    class="btn-close" 
                    data-bs-dismiss="modal" 
                    aria-label="Cerrar"
                ></button>
            </div>
            <div class="modal-body">
                {{-- Notar que la acción la seteamos desde JS --}}
                <form 
                    id="formEditMembresia" 
                    action="#" 
                    method="POST"
                >
                    @csrf
                    @method('PUT')

                    {{-- Partial para campos --}}
                    @include('membresias.partials.fields', ['membresia' => null])

                    <div class="text-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            Actualizar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ===========================
     Script para llenar edición
   =========================== --}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const editMembresiaModal = document.getElementById('editMembresiaModal');
        editMembresiaModal.addEventListener('show.bs.modal', event => {
            const button = event.relatedTarget;

            const id          = button.getAttribute('data-id');
            const descripcion = button.getAttribute('data-descripcion');
            const clases      = button.getAttribute('data-clases');
            const reservas    = button.getAttribute('data-reservas');
            const valor       = button.getAttribute('data-valor');

            const form = document.getElementById('formEditMembresia');
            form.action = `/membresias/${id}`;

            form.querySelector('input[name="descripcion"]').value = descripcion;
            form.querySelector('input[name="clases"]').value      = clases;
            form.querySelector('input[name="reservas"]').value    = reservas;
            form.querySelector('input[name="valor"]').value       = valor;
        });
    });
</script>
@endpush

@endsection
