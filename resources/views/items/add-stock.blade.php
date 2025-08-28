@extends('layouts.vertical', ['subtitle' => 'Agregar Unidades'])

@section('content')
<div class="container">
    <h1 class="mb-4">Agregar Unidades a {{ $item->nombre }}</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('items.add-units', $item) }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="cantidad" class="form-label">Cantidad a agregar</label>
            <input type="number" name="cantidad" id="cantidad" class="form-control @error('cantidad') is-invalid @enderror" min="1" required>
            @error('cantidad')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary">Agregar</button>
        <a href="{{ route('items.show', $item) }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
