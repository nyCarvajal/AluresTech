@extends('layouts.vertical', ['subtitle' => 'Crear Usuario'])


@section('content')
<div class="container">
  <h1>Crear Administrador</h1>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
<div class="card card-body">
  <form method="POST" action="{{ route('users.admins.store') }}">
    @csrf

    {{-- Los mismos campos que en create_trainer --}}
    @include('users.partials.form-fields')

    <button type="submit" class="btn btn-primary">Crear Administrador</button>
  </form>
  </div>
</div>
@endsection
