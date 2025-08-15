@extends('layouts.vertical', ['subtitle' => 'Ver Item'])


@section('content')
<div class="container">
    <h1 class="mb-4">Detalle del Ítem #{{ $item->id }}</h1>

    <div class="card mb-4">
        <div class="card-body">
            <p><strong>Nombre:</strong> {{ $item->nombre }}</p>
            <p><strong>Cantidad:</strong> {{ $item->cantidad }}</p>
            <p><strong>Valor:</strong> {{ number_format($item->valor, 2, ',', '.') }}</p>
            <p><strong>Tipo:</strong> {{ $item->tipo }}</p>
            <p><strong>Área:</strong> {{ $item->area }}</p>
            <p><strong>Creado:</strong> {{ $item->created_at->format('d/m/Y H:i') }}</p>
            <p><strong>Última actualización:</strong> {{ $item->updated_at->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <a href="{{ route('items.edit', $item) }}" class="btn btn-warning">Editar</a>
    <a href="{{ route('items.index') }}" class="btn btn-secondary">Volver al listado</a>
</div>
@endsection
