@extends('layouts.vertical', ['subtitle' => 'Nuevo tipo de identificación'])

@section('content')
<div class="container">
    <h1 class="mb-4">Crear tipo de identificación</h1>
    <div class="card">
        <div class="card-body">
            <form action="{{ route('tipo-identificaciones.store') }}" method="POST">
                @include('tipo-identificaciones._form')
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success">Guardar</button>
                    <a href="{{ route('tipo-identificaciones.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
