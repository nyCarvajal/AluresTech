@extends('layouts.vertical', ['subtitle' => 'Gastos'])

@section('content')
<div class="card">
    <div class="card-body">
        <h1>Nueva Salida</h1>
        <form action="{{ route('salidas.store') }}" method="POST">
            @csrf
            @include('salidas._form')
            <button class="btn btn-primary">Guardar</button>
        </form>
    </div>
</div>
@endsection

