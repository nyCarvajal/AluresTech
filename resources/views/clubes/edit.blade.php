@extends('layouts.vertical', ['subtitle' => 'Club'])



@section('content')
<div class="container">
  <h1>Editar Club</h1>
  <form action="{{ route('clubes.update', $club) }}" method="POST">
    @csrf @method('PUT')
    @include('clubes.form')
    <button class="btn btn-success">Actualizar</button>
  </form>
</div>
@endsection