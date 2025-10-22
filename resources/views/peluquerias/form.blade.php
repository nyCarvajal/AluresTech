@php
    $stylistLabelSingular = $stylistLabelSingular ?? \App\Models\Peluqueria::defaultRoleLabel(\App\Models\Peluqueria::ROLE_STYLIST);
    $stylistLabelPlural = $stylistLabelPlural ?? \App\Models\Peluqueria::defaultRoleLabel(\App\Models\Peluqueria::ROLE_STYLIST, true);
@endphp

<div class="mb-3">
  <label>Nombre</label>
  <input
    type="text"
    name="nombre"
    value="{{ old('nombre', $peluqueria->nombre ?? '') }}"
    class="form-control @error('nombre') is-invalid @enderror"
    required
  >
  @error('nombre')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>

<div class="form-check form-check-inline">
  <input type="checkbox" name="cuentaCobro" value="1" id="cuentaCobro" class="form-check-input" {{ (old('cuentaCobro', $peluqueria->cuentaCobro ?? false) ? 'checked' : '') }}>
  <label for="cuentaCobro">Cuenta Cobro</label>
</div>

<div class="mb-3">
  <label>Mensaje Nueva Reserva</label>
  <textarea name="msj_reserva_confirmada" class="form-control @error('msj_reserva_confirmada') is-invalid @enderror">{{ old('msj_reserva_confirmada', $peluqueria->msj_reserva_confirmada ?? '') }}</textarea>
  @error('msj_reserva_confirmada')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>
<div class="mb-3">
  <label>Mensaje Bienvenida</label>
  <textarea name="msj_bienvenida" class="form-control @error('msj_bienvenida') is-invalid @enderror">{{ old('msj_bienvenida', $peluqueria->msj_bienvenida ?? '') }}</textarea>
  @error('msj_bienvenida')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>

<div class="mb-3">
  <label>NIT</label>
  <input
    type="text"
    name="nit"
    value="{{ old('nit', $peluqueria->nit ?? '') }}"
    class="form-control @error('nit') is-invalid @enderror"
  >
  @error('nit')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>
<div class="mb-3">
  <label>Dirección</label>
  <input
    type="text"
    name="direccion"
    value="{{ old('direccion', $peluqueria->direccion ?? '') }}"
    class="form-control @error('direccion') is-invalid @enderror"
  >
  @error('direccion')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>

<div class="mb-3">
  <label class="form-label" for="logo">Logo</label>
  <input
    type="file"
    id="logo"
    name="logo"
    accept="image/*"
    class="form-control @error('logo') is-invalid @enderror"
  >
  <small class="form-text text-muted">Sube una imagen en formato JPG, PNG, GIF o WebP (máx. 10&nbsp;MB).</small>

  @error('logo')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror

  @if(isset($peluqueria) && ($peluqueria->logo || $peluqueria->logo_url))
    <div class="mt-3">
      <p class="mb-2 text-muted">Logo actual:</p>
      <img
        src="{{ $peluqueria->resolvedLogoUrl() }}"
        alt="Logo actual de la peluquería"
        class="img-fluid rounded border"
        style="max-height: 160px;"
      >
    </div>
  @endif
</div>

<div class="row mb-3">
  <div class="col-md-6">
    <label class="form-label" for="menu_color">Color del menú</label>
    <input
      type="color"
      id="menu_color"
      name="menu_color"
      value="{{ old('menu_color', $peluqueria->menu_color ?: '#393f4a') }}"
      class="form-control form-control-color @error('menu_color') is-invalid @enderror"
      title="Selecciona el color de fondo del menú"
    >
    <small class="form-text text-muted">Elige el color que tendrá el menú lateral.</small>
    @error('menu_color')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>

  <div class="col-md-6">
    <label class="form-label" for="topbar_color">Color del topbar</label>
    <input
      type="color"
      id="topbar_color"
      name="topbar_color"
      value="{{ old('topbar_color', $peluqueria->topbar_color ?: '#393f4a') }}"
      class="form-control form-control-color @error('topbar_color') is-invalid @enderror"
      title="Selecciona el color de fondo del topbar"
    >
    <small class="form-text text-muted">Elige el color que tendrá la barra superior.</small>
    @error('topbar_color')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>
</div>

<div class="row mb-3">
  <div class="col-md-6">
    <label class="form-label" for="trainer_label_singular">Nombre del rol (singular)</label>
    <input
      type="text"
      id="trainer_label_singular"
      name="trainer_label_singular"
      class="form-control @error('trainer_label_singular') is-invalid @enderror"
      value="{{ old('trainer_label_singular', $stylistLabelSingular) }}"
      placeholder="Ej. Barbero"
    >
    <small class="form-text text-muted">Déjalo vacío para usar el nombre predeterminado.</small>
    @error('trainer_label_singular')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>
  <div class="col-md-6">
    <label class="form-label" for="trainer_label_plural">Nombre del rol (plural)</label>
    <input
      type="text"
      id="trainer_label_plural"
      name="trainer_label_plural"
      class="form-control @error('trainer_label_plural') is-invalid @enderror"
      value="{{ old('trainer_label_plural', $stylistLabelPlural) }}"
      placeholder="Ej. Barberos"
    >
    <small class="form-text text-muted">Déjalo vacío para usar el nombre predeterminado.</small>
    @error('trainer_label_plural')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>
</div>

