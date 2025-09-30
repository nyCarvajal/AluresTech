@extends('layouts.vertical', ['subtitle' => 'Gastos'])

@section('content')
<div class="card">
    <div class="card-body">
        <h1>Detalle de Salida #{{ $salida->id }}</h1>
        <div class="card p-3">
            <p><strong>Concepto:</strong> {{ $salida->concepto }}</p>
            <p><strong>Fecha:</strong> {{ optional($salida->fecha)->format('Y-m-d') }}</p>
            <p>
                <strong>Origen:</strong>
                {{ $salida->cuenta_bancaria_id ? 'Cuenta bancaria: ' . optional($salida->cuentaBancaria)->nombre : 'Caja' }}
            </p>
            <p><strong>Valor:</strong> {{ number_format($salida->valor, 2, ',', '.') }}</p>
            <p><strong>Observaciones:</strong> {{ $salida->observaciones }}</p>
            <p><strong>Responsable:</strong> {{ optional($salida->responsable)->name }}</p>
            <p><strong>Tercero:</strong> {{ optional($salida->tercero)->nombre }}</p>
        </div>
        <a href="{{ route('salidas.index') }}" class="btn btn-secondary mt-3">Volver</a>
    </div>
</div>
@endsection

