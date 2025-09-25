@extends('layouts.vertical', ['subtitle' => 'Editar tipo de identificación'])

@section('content')
<div class="container">
    <h1 class="mb-4">Editar tipo de identificación</h1>
    <div class="card">
        <div class="card-body">
            <form action="{{ route('tipo-identificaciones.update', $tipoIdentificacion) }}" method="POST">
                @method('PUT')
                @include('tipo-identificaciones._form')
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                    <a href="{{ route('tipo-identificaciones.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
