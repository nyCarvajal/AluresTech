@extends('layouts.vertical', ['subtitle' => 'Gastos'])
   
@section('content')
<div class="card">
<div class="card-body">
  <h1>Detalle de Salida #{{ \$salida->id }}</h1>
  <div class="card p-3">
    <p><strong>Concepto:</strong> {{ \$salida->concepto }}</p>
    <p><strong>Fecha:</strong> {{ \$salida->fecha->format('Y-m-d') }}</p>
    <p><strong>Banco:</strong> {{ \$salida->cuentaBancaria->nombre }}</p>
    <p><strong>Valor:</strong> {{ number_format(\$salida->valor,2) }}</p>
    <p><strong>Cuenta contable:</strong> {{ \$salida->cuenta_contable }}</p>
    <p><strong>Observaciones:</strong> {{ \$salida->observaciones }}</p>
    <p><strong>Responsable:</strong> {{ \$salida->responsable->name }}</p>
    <p><strong>Tercero:</strong> {{ \$salida->tercero->nombre }}</p>
  </div>
  <a href="{{ route('salidas.index') }}" class="btn btn-secondary mt-3">Volver</a>
</div>
</div>
@endsection