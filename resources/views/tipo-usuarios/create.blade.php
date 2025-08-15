

@extends('layouts.vertical', ['subtitle' => 'Crear tipo'])
@section('content')
<form method="POST" action="{{ isset($tipoUsuario) ? route('tipo-usuarios.update', $tipoUsuario) : route('tipo-usuarios.store') }}">
  @csrf
  @if(isset($tipoUsuario)) @method('PUT') @endif
  @include('tipo-usuarios._form')
  <button class="btn btn-primary">
    {{ isset($tipoUsuario) ? 'Actualizar' : 'Crear' }}
  </button>
</form>
