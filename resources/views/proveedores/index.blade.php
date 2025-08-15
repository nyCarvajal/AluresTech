
@extends('layouts.vertical', ['subtitle' => 'Proveedores'])
   
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
  <h1>Proveedores</h1>
  @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
  <a href="{{ route('proveedores.create') }}" class="btn btn-primary mb-3">Nuevo Proveedor</a>
  
  <div class="card">
  <table class="table table-striped">
    <thead>
      <tr>
        <th>ID</th><th>Documento</th><th>Nombre</th><th>Regimen</th><th>Ciudad</th><th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      @foreach($proveedores as $p)
      <tr>
        <td>{{ $p->id }}</td>
        <td>{{ $p->tipoDocumento->sigla ?? '' }} {{ $p->numero_documento }}</td>
        <td>{{ $p->nombre }}</td>
        <td>{{ $p->regimen==1?'Natural':'Jurídica' }}</td>
        <td>{{ $p->municipio->nombre }}</td>
        <td>
          <a href="{{ route('proveedores.show',$p) }}" class="btn btn-sm btn-success">Ver</a>
          <a href="{{ route('proveedores.edit',$p) }}" class="btn btn-sm btn-info">Editar</a>
          <form action="{{ route('proveedores.destroy',$p) }}" method="POST" class="d-inline">
            @csrf @method('DELETE')
            <button onclick="return confirm('¿Eliminar?')" class="btn btn-sm btn-danger">Eliminar</button>
          </form>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
  </div>
</div>
</div>
</div>
@endsection