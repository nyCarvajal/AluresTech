// resources/views/tipo-usuarios/index.blade.php


@extends('layouts.vertical', ['subtitle' => 'Crear tipo'])

@section('content')
<div class="container">
    <h1>Tipos de Usuario</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('tipo-usuarios.create') }}" class="btn btn-primary mb-3">Nuevo Tipo</a>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tipos as $tipo)
                <tr>
                    <td>{{ $tipo->id }}</td>
                    <td>{{ $tipo->nombre }}</td>
                    <td>
                        <a href="{{ route('tipo-usuarios.edit', $tipo) }}" class="btn btn-sm btn-secondary">Editar</a>
                        <form action="{{ route('tipo-usuarios.destroy', $tipo) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar este tipo?')">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
