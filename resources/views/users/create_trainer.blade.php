@extends('layouts.vertical', ['subtitle' => 'Crear Entrenador'])




@section('content')
  <div class="container">
    <h1>Crear Entrenador</h1>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
<div class="card card-body">
    <form method="POST" action="{{ route('users.trainers.store') }}">
      @csrf

      @include('users.partials.form-fields')

      <button type="submit" class="btn btn-primary">Crear Entrenador</button>
    </form>
	</div>
  </div>
@endsection

