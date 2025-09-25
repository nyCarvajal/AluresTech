@extends('layouts.vertical', ['subtitle' => 'Editar tipo de cita'])

@section('content')
<div class="container">
    <h1 class="h3 mb-4">Editar tipo de cita</h1>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('tipocitas.update', $tipocita) }}">
                @csrf
                @method('PUT')
                @include('tipocitas.form')

                <div class="d-flex justify-content-between">
                    <a href="{{ route('tipocitas.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
