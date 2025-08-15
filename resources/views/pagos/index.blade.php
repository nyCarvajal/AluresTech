@extends('layouts.vertical', ['subtitle' => 'Pagos'])

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">

      {{-- Encabezado --}}
      <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Pagos</h4>

        
      </div>

      {{-- Tabla --}}
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>Fecha / hora</th>
              <th>Valor</th>
              <th>Cuenta</th>
              <th>Estado</th>
              <th>Banco</th>
              <th>Responsable</th>
              <th>Acciones</th>
            </tr>
          </thead>

          <tbody>
          @forelse ($pagos as $pago)
            <tr>
              {{-- Enumeración respetando la paginación --}}
              <td>{{ $loop->iteration + ($pagos->currentPage() - 1) * $pagos->perPage() }}</td>

              <td>{{ \Carbon\Carbon::parse($pago->fecha_hora)->format('d/m/Y H:i') }}</td>

              <td>${{ number_format($pago->valor, 0, ',', '.') }}</td>

              <td>
  <a href="{{ route('orden_de_compras.show', $pago->cuenta) }}"
     class="text-decoration-underline">
    {{ $pago->cuenta }}
  </a>
</td>

			  
			  <td>
  @if ($pago->estado == 1)
    <span class="badge bg-success">Activo</span>
  @else
    <span class="badge bg-danger">Anulado</span>
  @endif
</td>

              <td>{{ $pago->bancoModel?->nombre }}</td>
              <td>{{ $pago->responsable }}</td>
              <td>
                <a href="{{ route('pagos.edit', $pago) }}" class="btn btn-sm btn-primary">

                  <i class='bx bx-edit'></i>

                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="text-center py-4">No hay pagos registrados.</td>
            </tr>
          @endforelse
          </tbody>
        </table>
      </div>

      {{-- Paginación --}}
      <div class="card-footer">
        {{ $pagos->links() }}
      </div>

    </div>
  </div>
</div>
@endsection
