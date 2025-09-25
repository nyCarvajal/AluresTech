@extends('layouts.vertical', ['subtitle' => 'Crear Área'])

@section('content')
<div class="container">
    <h1 class="mb-4">Crear Área</h1>
    <div class="card">
        <div class="card-body">
            <form action="{{ route('areas.store') }}" method="POST">
                @include('areas._form')
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success">Guardar</button>
                    <a href="{{ route('areas.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
