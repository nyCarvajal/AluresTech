{{-- Nombre --}}
<div class="mb-3">
  <label for="nombre" class="form-label">Nombre</label>
  <input id="nombre"
         name="nombre"
         type="text"
         class="form-control @error('nombre') is-invalid @enderror"
         value="{{ old('nombre') }}"
         required>
  @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

{{-- Apellidos --}}
<div class="mb-3">
  <label for="apellidos" class="form-label">Apellidos</label>
  <input id="apellidos"
         name="apellidos"
         type="text"
         class="form-control @error('apellidos') is-invalid @enderror"
         value="{{ old('apellidos') }}"
         required>
  @error('apellidos') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

{{-- Email --}}
<div class="mb-3">
  <label for="email" class="form-label">Correo electrónico</label>
  <input id="email"
         name="email"
         type="email"
         class="form-control @error('email') is-invalid @enderror"
         value="{{ old('email') }}"
         required>
  @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
<?php
/*
{{-- Nivel --}}
<div class="mb-3">
  <label for="nivel" class="form-label">Nivel</label>
  <input id="nivel"
         name="nivel"
         type="text"
         class="form-control @error('nivel') is-invalid @enderror"
         value="{{ old('nivel') }}"
         required>
  @error('nivel') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
**/ ?>

{{-- Tipo de identificación --}}
<div class="mb-3">
  <label for="tipo_identificacion" class="form-label">Tipo de identificación</label>
  <input id="tipo_identificacion"
         name="tipo_identificacion"
         type="text"
         class="form-control @error('tipo_identificacion') is-invalid @enderror"
         value="{{ old('tipo_identificacion') }}"
         required>
  @error('tipo_identificacion') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

{{-- Número de identificación --}}
<div class="mb-3">
  <label for="numero_identificacion" class="form-label">Número de identificación</label>
  <input id="numero_identificacion"
         name="numero_identificacion"
         type="text"
         class="form-control @error('numero_identificacion') is-invalid @enderror"
         value="{{ old('numero_identificacion') }}"
         required>
  @error('numero_identificacion') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

{{-- Dirección --}}
<div class="mb-3">
  <label for="direccion" class="form-label">Dirección</label>
  <input id="direccion"
         name="direccion"
         type="text"
         class="form-control @error('direccion') is-invalid @enderror"
         value="{{ old('direccion') }}"
         required>
  @error('direccion') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

{{-- WhatsApp --}}
<div class="mb-3">
  <label for="whatsapp" class="form-label">WhatsApp</label>
  <input id="whatsapp"
         name="whatsapp"
         type="tel"
         class="form-control @error('whatsapp') is-invalid @enderror"
         value="{{ old('whatsapp') }}"
         placeholder="+57 300 123 4567"
         required>
  @error('whatsapp') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>


{{-- Password --}}
<div class="mb-3">
  <label for="password" class="form-label">Contraseña</label>
  <input id="password"
         name="password"
         type="password"
         class="form-control @error('password') is-invalid @enderror"
         required>
  @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

{{-- Confirmar Password --}}
<div class="mb-3">
  <label for="password_confirmation" class="form-label">Confirmar contraseña</label>
  <input id="password_confirmation"
         name="password_confirmation"
         type="password"
         class="form-control"
         required>
</div>
