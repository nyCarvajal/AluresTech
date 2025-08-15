{{-- resources/views/pagos/create.blade.php --}}
@extends('layouts.vertical', ['subtitle' => 'Calendario de Reservas'])

@section('content')
<div class="container">
    <h1>
        <i class="fa fa-plus-circle me-1"></i> Registrar Nuevo Pago
    </h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                   <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('pagos.store') }}" method="POST" id="pago-form">
        @csrf

        @include('pagos._form')
    </form>
</div>
@endsection
