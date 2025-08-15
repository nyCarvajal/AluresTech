@extends('layouts.vertical', ['subtitle' => 'Niveles'])


@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            {{-- Mensajes flash --}}
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Listado de Niveles</h5>
                    {{-- Botón para abrir modal de creación --}}
                    <button 
                        class="btn btn-primary" 
                        data-bs-toggle="modal" 
                        data-bs-target="#createNivelModal"
                    >
                        + Nuevo Nivel
                    </button>
                </div>

                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nivel</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($niveles as $nivel)
                                <tr>
                                    <td>{{ $nivel->id }}</td>
                                    <td>{{ $nivel->nivel }}</td>
                                    <td>
                                        {{-- Botón Editar --}}
                                        <button
    class="btn btn-sm btn-warning btn-edit-nivel"
    data-bs-toggle="modal"
    data-bs-target="#editNivelModal"
    data-id="{{ $nivel->id }}"
    data-nivel="{{ $nivel->nivel }}"
>
    Editar
</button>


                                        {{-- Formulario para eliminar --}}
                                        <form 
                                            action="{{ route('niveles.destroy', $nivel) }}" 
                                            method="POST" 
                                            class="d-inline"
                                            onsubmit="return confirm('¿Seguro que desea eliminar este nivel?')"
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

                            @if($niveles->isEmpty())
                                <tr>
                                    <td colspan="3" class="text-center">No hay niveles registrados.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- =======================
     Modal: Crear Nivel
   ======================= --}}
<div 
    class="modal fade" 
    id="createNivelModal" 
    tabindex="-1" 
    aria-labelledby="createNivelModalLabel" 
    aria-hidden="true"
>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crear Nivel</h5>
                <button 
                    type="button" 
                    class="btn-close" 
                    data-bs-dismiss="modal" 
                    aria-label="Cerrar"
                ></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('niveles.store') }}" method="POST">
                    @csrf

                    {{-- Incluimos los campos del partial --}}
                    @include('niveles.partials.fields', ['nivel' => null])

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

{{-- =======================
     Modal: Editar Nivel
   ======================= --}}
<div class="modal fade" id="editNivelModal" tabindex="-1" aria-labelledby="editNivelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- ...cabecera del modal... -->
            <div class="modal-body">
                <form id="formEditNivel" action="#" method="POST">
                    @csrf
                    @method('PUT')

                    @include('niveles.partials.fields', ['nivel' => null])

                    <div class="text-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


{{-- =======================
     Script para llenar edición
   ======================= --}}


@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const editNivelModal = document.getElementById('editNivelModal');
	 console.log('Entre a modal');

    if (!editNivelModal) {
        console.error('No existe ningún elemento con id="editNivelModal"');
        return;
    }

    editNivelModal.addEventListener('show.bs.modal', event => {
        // El botón que disparó el modal:
        const button = event.relatedTarget;
        if (!button) {
            console.error('event.relatedTarget es null');
            return;
        }

        // Leer atributos data-id y data-nivel
        const id    = button.getAttribute('data-id');
        const nivel = button.getAttribute('data-nivel');

        console.log('Vamos a cargar en el formulario: id=', id, 'nivel=', nivel);

        // Formulario dentro del modal
        const form = document.getElementById('formEditNivel');
        if (!form) {
            console.error('No existe form con id="formEditNivel"');
            return;
        }

        // Setear la URL de acción: /niveles/{id}
        form.action = `/niveles/${id}`;

        // Rellenar el input
        const inputNivel = form.querySelector('input[name="nivel"]');
        if (!inputNivel) {
            console.error('No existe input con name="nivel" dentro de #formEditNivel');
            return;
        }
        inputNivel.value = nivel;
    });
});
</script>
@endpush
