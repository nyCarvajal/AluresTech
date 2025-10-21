@php
    $trainerLabelSingular = $trainerLabelSingular ?? \App\Models\Peluqueria::defaultRoleLabel(\App\Models\Peluqueria::ROLE_STYLIST);
    $trainerLabelPlural = $trainerLabelPlural ?? \App\Models\Peluqueria::defaultRoleLabel(\App\Models\Peluqueria::ROLE_STYLIST, true);
@endphp

@extends('layouts.vertical', ['subtitle' => 'Crear ' . $trainerLabelSingular])




@section('content')
  <div class="container">
    <h1>Crear {{ $trainerLabelSingular }}</h1>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
<div class="card card-body">
    <form method="POST" action="{{ route('users.trainers.store') }}">
      @csrf

      @include('users.partials.form-fields')

      <button type="submit" class="btn btn-primary">Crear {{ $trainerLabelSingular }}</button>
    </form>
	</div>
  </div>
@endsection

