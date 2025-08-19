{{-- resources/views/clientes/show.blade.php --}}

@extends('layouts.vertical', ['subtitle' => 'Perfil Cliente'])

@section('css')
  <style>
    :root {
      --color-white: #ffffff;
      --color-gray: #f5f5f5;
      --color-light-gray: #e0e0e0;
      --color-lilac: rgba(96,66,245,0.35);
	  --color-verde: rgba(0,168,89,0.35);
      --text-dark: #3C4A60;
      --text-light: #9CA3AF;
      --font-family: 'Arial', sans-serif;
    }
    .profile-body {
      background-color: var(--color-gray);
      font-family: var(--font-family);
      color: var(--text-dark);
      padding: 20px;
      display: flex;
      justify-content: center;
    }
    .profile-container {
      max-width: 900px;
      width: 100%;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 20px;
    }
    .card-custom {
      background-color: var(--color-white);
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      overflow: hidden;
      display: flex;
      flex-direction: column;
    }
    .card-custom .card-header {
      background-color: var(--color-lilac);
      padding: 15px;
      color: var(--color-white);
      font-size: 1.1em;
      font-weight: bold;
    }
    .card-custom .card-body {
      padding: 15px;
      flex: 1;
    }
    .profile-info img {
      width: 100%;
      height: auto;
      border-bottom: 1px solid var(--color-light-gray);
    }
    .info-list {
      list-style: none;
      padding: 0;
      margin: 0;
    }
    .info-list li {
      margin-bottom: 8px;
      font-size: 0.95em;
      color: var(--text-dark);
    }
    .info-list li span {
      color: var(--text-light);
    }
    .badge-custom {
      display: inline-block;
      background-color: var(--color-lilac);
      color: var(--color-white);
      padding: 4px 10px;
      border-radius: 12px;
      font-size: 0.85em;
      font-weight: bold;
      text-transform: uppercase;
    }
    .subscription-info .stats {
      display: flex;
      justify-content: space-between;
      margin-top: 10px;
    }
    .subscription-info .stat {
      background-color: var(--color-light-gray);
      padding: 10px;
      border-radius: 6px;
      flex: 1;
      text-align: center;
      margin-right: 10px;
      color: var(--text-dark);
    }
    .subscription-info .stat:last-child {
      margin-right: 0;
    }
    .reservations-list {
      list-style: none;
      padding: 0;
      margin: 0;
    }
    .reservations-list li {
      background-color: var(--color-light-gray);
      margin-bottom: 8px;
      padding: 10px;
      border-radius: 6px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 0.95em;
      color: var(--text-dark);
    }
    .reservations-list li .status {
    
      color: var(--color-white);
      padding: 4px 8px;
      border-radius: 12px;
      font-size: 0.85em;
      font-weight: bold;
    }
	.confirmada {
      background-color: var(--color-verde);
      
    }
	.pendiente{
		background-color: rgba(255, 193, 7, 0.35);
	}
	.cancelada{
	   background-color: rgba(220, 53, 69, 0.35);
	}
	
  </style>
@endsection

@section('content')
<div class="profile-body">
  <div class="profile-container">

    <!-- Tarjeta 1: Información de Usuario -->
    <div class="card-custom profile-info">
      <div class="card-header" style="background: linear-gradient(to center, #0053BF, #6366F1);">Perfil de Usuario</div>
@if ($cliente->foto)
  <img
    src="{{ cloudinary()->image($cliente->foto)->toUrl() }}"
    alt="Foto de {{ $cliente->nombres }}"
    class="avatar-sm rounded-circle d-block mx-auto my-3"
    style="width:250px;height:250px;object-fit:cover;">
@endif

      <div class="card-body">
        <ul class="info-list">
          <li><strong>Nombre:</strong> <span>{{ $cliente->nombres }} {{ $cliente->apellidos }}</span></li>
          <li><strong>Tipo de identificación:</strong> <span>{{ optional($cliente->tipoIdentificacion)->tipo }}</span></li>
          <li><strong>Número de identificación:</strong> <span>{{ $cliente->numero_identificacion }}</span></li>
          <li><strong>Correo:</strong> <span>{{ $cliente->correo }}</span></li>
          <li><strong>WhatsApp:</strong> <span>{{ $cliente->whatsapp }}</span></li>
          <li><strong>Nivel:</strong> <span class="badge-custom">{{ optional($cliente->nivel)->nivel }}</span></li>
        </ul>
      </div>
    </div>
	
	
   
  </div>



  

  </div>
</div>
@endsection
