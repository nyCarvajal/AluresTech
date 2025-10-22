@php
    $stylistLabelSingular = $stylistLabelSingular ?? \App\Models\Peluqueria::defaultRoleLabel(\App\Models\Peluqueria::ROLE_STYLIST);
    $stylistLabelPlural = $stylistLabelPlural ?? \App\Models\Peluqueria::defaultRoleLabel(\App\Models\Peluqueria::ROLE_STYLIST, true);
@endphp

<div class="mb-3">
  <label>Nombre</label>
  <input type="text" name="nombre" value="{{ old('nombre', $peluqueria->nombre ?? '') }}" class="form-control" required>
</div>

<div class="form-check form-check-inline">
  <input type="checkbox" name="cuentaCobro" value="1" id="cuentaCobro" class="form-check-input" {{ (old('cuentaCobro', $peluqueria->cuentaCobro ?? false) ? 'checked' : '') }}>
  <label for="cuentaCobro">Cuenta Cobro</label>
</div>

<div class="mb-3">
  <label>Mensaje Nueva Reserva</label>
  <textarea name="msj_reserva_confirmada" class="form-control">{{ old('msj_reserva_confirmada', $peluqueria->msj_reserva_confirmada ?? '') }}</textarea>
</div>
<div class="mb-3">
  <label>Mensaje Bienvenida</label>
  <textarea name="msj_bienvenida" class="form-control">{{ old('msj_bienvenida', $peluqueria->msj_bienvenida ?? '') }}</textarea>
</div>

<div class="mb-3">
  <label>NIT</label>
  <input type="text" name="nit" value="{{ old('nit', $peluqueria->nit ?? '') }}" class="form-control">
</div>
<div class="mb-3">
  <label>Dirección</label>
  <input type="text" name="direccion" value="{{ old('direccion', $peluqueria->direccion ?? '') }}" class="form-control">
</div>

<div class="row mb-3">
  <div class="col-md-6">
    <label class="form-label" for="menu_color">Color del menú</label>
    <input
      type="color"
      id="menu_color"
      name="menu_color"
      value="{{ old('menu_color', $peluqueria->menu_color ?: '#393f4a') }}"
      class="form-control form-control-color"
      title="Selecciona el color de fondo del menú"
    >
    <small class="form-text text-muted">Elige el color que tendrá el menú lateral.</small>
  </div>

  <div class="col-md-6">
    <label class="form-label" for="topbar_color">Color del topbar</label>
    <input
      type="color"
      id="topbar_color"
      name="topbar_color"
      value="{{ old('topbar_color', $peluqueria->topbar_color ?: '#393f4a') }}"
      class="form-control form-control-color"
      title="Selecciona el color de fondo del topbar"
    >
    <small class="form-text text-muted">Elige el color que tendrá la barra superior.</small>
  </div>
</div>

<div id="role-labels" class="card mb-4">
  <div class="card-header">
    Personaliza cómo llamas a tu equipo
  </div>
  <div class="card-body">
    <p class="text-muted small mb-4">
      Estos nombres reemplazan la palabra "Estilista" en toda la aplicación: listas de usuarios,
      agenda, reservas y formularios públicos.
    </p>
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label" for="trainer_label_singular">Nombre del rol (singular)</label>
        <input
          type="text"
          id="trainer_label_singular"
          name="trainer_label_singular"
          class="form-control"
          value="{{ old('trainer_label_singular', $stylistLabelSingular) }}"
          placeholder="Ej. Barbero"
        >
        <small class="form-text text-muted">Déjalo vacío para usar el nombre predeterminado.</small>
      </div>
      <div class="col-md-6">
        <label class="form-label" for="trainer_label_plural">Nombre del rol (plural)</label>
        <input
          type="text"
          id="trainer_label_plural"
          name="trainer_label_plural"
          class="form-control"
          value="{{ old('trainer_label_plural', $stylistLabelPlural) }}"
          placeholder="Ej. Barberos"
        >
        <small class="form-text text-muted">Déjalo vacío para usar el nombre predeterminado.</small>
      </div>
    </div>
  </div>
</div>

