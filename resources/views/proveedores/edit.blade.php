@extends('layouts.vertical', ['subtitle' => 'Proveedores'])
   
@section('content')
<div class="card">
<div class="card-body">
  <h1>Editar Proveedor</h1>
  <form action="{{ route('proveedores.update', $proveedor) }}" method="POST">
    @csrf
    @method('PUT')
    @include('proveedores._form')
    <button class="btn btn-success">Actualizar</button>
  </form>
  </div>
</div>
@endsection
