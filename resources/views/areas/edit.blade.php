@extends('layouts.vertical', ['subtitle' => 'Editar Área'])

@section('content')
<div class="container">
    <h1 class="mb-4">Editar Área</h1>
    <div class="card">
        <div class="card-body">
            <form action="{{ route('areas.update', $area) }}" method="POST">
                @method('PUT')
                @include('areas._form')
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                    <a href="{{ route('areas.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
