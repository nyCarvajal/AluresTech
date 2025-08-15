@extends('layouts.vertical', ['subtitle' => 'Proveedores'])
   
@section('content')
<div class="card">
<div class="card-body">
                   
  <h1>Nuevo Proveedor</h1>
  <form action="{{ route('proveedores.store') }}" method="POST">
    @csrf
    @include('proveedores._form')
    <button class="btn btn-primary">Crear</button>
  </form>
  </div>
</div>
@endsection