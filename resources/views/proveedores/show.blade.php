@extends('layouts.vertical', ['subtitle' => 'Proveedores'])
 
@section('content')
<div class="container">
  <h1>Proveedor #{{ $proveedor->id }}</h1>
  <div class="card p-3">
    <p><strong>Tipo documento:</strong> {{ optional($proveedor->tipoIdentificacion)->tipo }}</p>
    <p><strong>Número documento:</strong> {{ $proveedor->numero_documento }}</p>
    <p><strong>Nombre:</strong> {{ $proveedor->nombre }}</p>
    <p><strong>Régimen:</strong> {{ $proveedor->regimen==1?'Natural':'Jurídica' }}</p>
    <p><strong>Responsable IVA:</strong> {{ $proveedor->responsable_iva?'Sí':'No' }}</p>
    <p><strong>Dirección:</strong> {{ $proveedor->direccion }}</p>
    <p><strong>Ciudad:</strong> {{ optional($proveedor->municipio)->nombre }}</p>
  </div>
  <a href="{{ route('proveedores.index') }}" class="btn btn-secondary mt-3">Volver</a>
</div>
@endsection