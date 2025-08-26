@extends('layouts.vertical', ['subtitle' => 'Editar Usuario'])

@section('content')
  <div class="container">
    <h1>Editar Usuario</h1>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
<div class="card card-body">
    <form method="POST" action="{{ route('users.update', $user) }}">
      @csrf
      @method('PUT')

      @include('users.partials.form-fields', ['user' => $user])

      <button type="submit" class="btn btn-primary">Guardar Cambios</button>
    </form>
    </div>
  </div>
@endsection

