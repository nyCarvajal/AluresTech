{{-- resources/views/deportes/index.blade.php --}}
@extends('layouts.vertical', ['subtitle' => 'Deportes'])

@section('content')

{{-- Mensaje de éxito --}}
@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="card-title mb-0">Listado de Deportes</h5>
    <!-- Botón para abrir modal en modo crear -->
    <button type="button"
            class="btn btn-primary"
            data-bs-toggle="modal"
            data-bs-target="#deporteModal"
            data-mode="create">
      Crear Deporte
    </button>
  </div>

  <div class="card-body table-responsive">
    <table class="table table-hover">
      <thead class="table-light">
        <tr>
          <th>Nombre</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        @foreach($deportes as $deporte)
          <tr>
            <td>{{ $deporte->deporte }}</td>
            <td class="d-flex gap-1">
              <!-- Editar: abre modal en modo edit -->
              <button type="button"
                      class="btn btn-sm btn-outline-primary"
                      data-bs-toggle="modal"
                      data-bs-target="#deporteModal"
                      data-mode="edit"
                      data-id="{{ $deporte->id }}"
                      data-deporte="{{ $deporte->deporte }}">
                Editar
              </button>
              <!-- Eliminar -->
              <form action="{{ route('deportes.destroy', $deporte) }}"
                    method="POST"
                    onsubmit="return confirm('¿Eliminar este deporte?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
              </form>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

<!-- Modal único para crear/editar -->
<div class="modal fade" id="deporteModal" tabindex="-1" aria-labelledby="deporteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="deporteForm" method="POST" action="">
        @csrf
        <input type="hidden" name="_method" id="formMethod" value="POST">
        <div class="modal-header">
          <h5 class="modal-title" id="deporteModalLabel"></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="modalNombre" class="form-label">Deporte</label>
            <input type="text"
                   id="modalNombre"
                   name="deporte"
                   class="form-control @error('deporte') is-invalid @enderror"
                   required>
            <div class="invalid-feedback" id="errorNombre"></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary" id="modalSubmit"></button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  var deporteModal = document.getElementById('deporteModal');
  deporteModal.addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget;
    var mode   = button.getAttribute('data-mode');
    var form   = document.getElementById('deporteForm');
    var title  = document.getElementById('deporteModalLabel');
    var submit = document.getElementById('modalSubmit');
    var method = document.getElementById('formMethod');
    var nombreInput = document.getElementById('modalNombre');
    
    if (mode === 'create') {
      form.action = '{{ route('deportes.store') }}';
      method.value = 'POST';
      title.textContent = 'Crear Deporte';
      submit.textContent = 'Crear';
      nombreInput.value = '';
    } else if (mode === 'edit') {
      var id     = button.getAttribute('data-id');
      var deporte = button.getAttribute('data-deporte');
      form.action = '/deportes/' + id;
      method.value = 'PUT';
      title.textContent = 'Editar Deporte';
      submit.textContent = 'Actualizar';
      nombreInput.value = deporte;
    }
    // Quitar errores previos
    document.getElementById('errorNombre').textContent = '';
    nombreInput.classList.remove('is-invalid');
  });

  // Mostrar errores de validación si vienen de la sesión
  @if($errors->has('nombre'))
    var modal = new bootstrap.Modal(deporteModal);
    modal.show();
    var nombreInput = document.getElementById('modalNombre');
    nombreInput.classList.add('is-invalid');
    document.getElementById('errorNombre').textContent = '{{ $errors->first('nombre') }}';
  @endif
</script>
@endsection
