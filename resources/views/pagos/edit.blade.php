{{-- resources/views/pagos/edit.blade.php --}}
@extends('layouts.vertical', ['subtitle' => 'Calendario de Reservas'])

@section('content')
<div class="container">
    <h1>
        <i class="fa fa-edit me-1"></i> Editar Pago #{{ $pago->id }}
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

    <form action="{{ route('pagos.update', $pago) }}" method="POST" id="pago-form">
        @csrf
        @method('PUT')

        @include('pagos._form')
    </form>
</div>
@endsection
