@extends('layouts.vertical', ['subtitle' => 'Peluqueria'])

@section('content')
<div class="container">
  <h1>Editar Peluqueria</h1>

  @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  @if (session('warning'))
    <div class="alert alert-warning">{{ session('warning') }}</div>
  @endif

  @if ($errors->any())
    <div class="alert alert-danger">
      <p class="mb-1">Se encontraron algunos problemas con la información enviada:</p>
      <ul class="mb-0">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ $formAction ?? route('peluquerias.update') }}" method="POST" enctype="multipart/form-data">
    @csrf @method('PUT')
    @include('peluquerias.form')
    <button class="btn btn-success">Actualizar</button>
  </form>
</div>
@endsection
