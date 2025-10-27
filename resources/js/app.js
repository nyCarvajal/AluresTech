import bootstrap from 'bootstrap/dist/js/bootstrap'
window.bootstrap = bootstrap;
import 'iconify-icon';
import 'simplebar/dist/simplebar'
// resources/js/app.js
import './pages/dashboard.js';
import './pages/chart';

import '@fullcalendar/common/main.css';

import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';  
import interactionPlugin from '@fullcalendar/interaction';
import esLocale from '@fullcalendar/core/locales/es';
import axios from 'axios';

// Importa estilos de Flatpickr
import "flatpickr/dist/flatpickr.min.css";
import "flatpickr/dist/themes/dark.css";

// Importa la librería y exponerla globalmente
import flatpickr from "flatpickr";
window.flatpickr = flatpickr;

import intlTelInput from 'intl-tel-input';
import 'intl-tel-input/build/css/intlTelInput.css';

// Exponer la función en window para que tus componentes la encuentren
window.intlTelInput = intlTelInput;

import TomSelect from 'tom-select';
import 'tom-select/dist/css/tom-select.default.css';

//calendario



// Esperamos a que el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
  const cfg = window.CalendarConfig;
  if (!cfg) return;
  
  console.log('🚀 app.js arrancó, intentando FullCalendar…');

  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
  if (csrfToken) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
  }
  axios.defaults.headers.common['Accept'] = 'application/json';

  // 1) Obtener elementos comunes
  const calendarEl = document.querySelector(cfg.selector);
  const modalEl    = document.querySelector(cfg.modalSelector);
  const modal      = new bootstrap.Modal(modalEl);
  const form       = modalEl.querySelector('form');
  form.setAttribute('method', 'POST');
  const entrenadorFilter = document.querySelector(cfg.filterSelector);

  const methodIn      = form.querySelector('#reservationMethod');
  const typeSelect    = form.querySelector('#eventType');
  const durationSelect= form.querySelector('#reservaDuracion');
  const fechaInput = document.getElementById('reservaFecha');
  const horaSelect  = document.getElementById('reservaHora');
  const eventIdInput = form.querySelector('#eventId');
  const cancelBtn    = form.querySelector('#reservationCancel');
  const cancelBtnLabel = cancelBtn?.querySelector('[data-cancel-label]') ?? null;
  const cancelBtnDefaultText = cancelBtnLabel
    ? cancelBtnLabel.textContent.trim()
    : (cancelBtn ? cancelBtn.textContent.trim() : 'Cancelar reserva');
  const cancelLabelsByType = {
    Reserva: cancelBtn?.dataset?.labelReserva || cancelBtnDefaultText,
    Clase: cancelBtn?.dataset?.labelClase || cancelBtnDefaultText,
    Torneo: cancelBtnDefaultText,
  };
  const estadoSelect = form.querySelector('#reservaEstado');

  const setCancelButtonText = (text) => {
    if (cancelBtnLabel) {
      cancelBtnLabel.textContent = text;
    } else if (cancelBtn) {
      cancelBtn.textContent = text;
    }
  };

  const refreshCancelButtonTextForType = (typeValue) => {
    if (!cancelBtn) return;
    const typeKey = (typeValue || '').trim();
    const fallback = cancelBtnDefaultText;
    const label = cancelLabelsByType[typeKey] || fallback;
    setCancelButtonText(label);
  };

  const TYPE_MAP = {
    Reserva: { url: '/reservas' },
    Clase:   { url: '/clases' },
    Torneo:  { url: '/torneos' },
  };

  const resolveReservaId = () => {
    if (eventIdInput && eventIdInput.value && eventIdInput.value.trim()) {
      return eventIdInput.value.trim();
    }

    const datasetId = cancelBtn?.dataset?.reservaId;
    if (datasetId && datasetId.trim()) {
      return datasetId.trim();
    }

    const action = form?.getAttribute('action') ?? '';
    const match = action.match(/\/reservas\/(\d+)/);
    if (match && match[1]) {
      return match[1];
    }

    return '';
  };

  const disableCancelButton = () => {
    if (!cancelBtn) return;
    cancelBtn.disabled = true;
    cancelBtn.setAttribute('disabled', 'disabled');
    cancelBtn.setAttribute('aria-disabled', 'true');
    cancelBtn.classList.add('disabled', 'opacity-50');
    refreshCancelButtonTextForType(typeSelect?.value);
    delete cancelBtn.dataset.reservaId;
  };

  const enableCancelButton = (reservaId) => {
    if (!cancelBtn) return;
    const id = String(reservaId ?? '').trim();
    if (!id) {
      disableCancelButton();
      return;
    }
    cancelBtn.disabled = false;
    cancelBtn.removeAttribute('disabled');
    cancelBtn.removeAttribute('aria-disabled');
    cancelBtn.classList.remove('disabled', 'opacity-50');
    refreshCancelButtonTextForType(typeSelect?.value);
    cancelBtn.dataset.reservaId = id;
  };

  const updateCancelButtonVisibility = () => {
    if (!cancelBtn) return;
    const reservaId = resolveReservaId();
    if (reservaId) {
      enableCancelButton(reservaId);
    } else {
      disableCancelButton();
    }
  };

  disableCancelButton();
  modalEl.addEventListener('hidden.bs.modal', () => {
    disableCancelButton();
    if (eventIdInput) {
      eventIdInput.value = '';
    }
    if (methodIn) {
      methodIn.value = 'POST';
    }
    if (form && TYPE_MAP?.Reserva?.url) {
      form.setAttribute('action', TYPE_MAP.Reserva.url);
    }
  });

  modalEl.addEventListener('show.bs.modal', updateCancelButtonVisibility);
  modalEl.addEventListener('shown.bs.modal', updateCancelButtonVisibility);

  if (eventIdInput) {
    eventIdInput.addEventListener('input', updateCancelButtonVisibility);
    eventIdInput.addEventListener('change', updateCancelButtonVisibility);
  }

  if (methodIn) {
    methodIn.addEventListener('change', updateCancelButtonVisibility);
  }

  // Campos específicos
   // ===== Campos específicos =====
  
  const clientesField     = form.querySelector('#fieldClientes');
  const entrenadorField   = form.querySelector('#fieldEntrenador');
  const responsableField  = form.querySelector('#fieldResponsable');
  const inicioInput       = document.getElementById('reservaFecha');
  const clienteSelect     = form.querySelector('#clientes');
  const entrenadorSelect  = form.querySelector('#entrenador');
  const responsableInput  = form.querySelector('#responsable');

  // Listener para cambio de tipo en el select del modal
  if (typeSelect) {
    typeSelect.addEventListener('change', e => {
      const newType = e.target.value;
      switchFields(newType);
      refreshCancelButtonTextForType(newType);
    });
  } else {
    console.warn('⚠️  No se encontró el selector de tipo de evento en el formulario de reservas.');
  }
  
  (() => {
  const fecha  = document.getElementById('reservaFecha');
  const hora   = document.getElementById('reservaHora');
  const start  = document.getElementById('start');
  const form   = fecha.closest('form');          // asumiendo que ambos están dentro

  function fusionar() {
    if (!fecha.value || !hora.value) { start.value = ''; return; }
    // → "2025-06-17T08:30:00"
    start.value = `${fecha.value}T${hora.value}:00`;
  }

  fecha.addEventListener('change', fusionar);
  hora .addEventListener('change', fusionar);

  // Validación extra: evita enviar si falta algo
  form.addEventListener('submit', e => {
    fusionar();
    if (!start.value) {
      e.preventDefault();
      alert('Selecciona fecha y hora.');
    }
  });
})();

  
  new TomSelect('#responsable', {
  valueField: 'id',
  labelField: 'nombre',
  searchField: ['nombre'],
  loadingClass: 'is-loading',
  placeholder: 'Escribe para buscar…',
  load(query, callback) {
    // evita disparar la llamada si no hay texto
    if (!query.length) return callback();

   fetch(`/clientesb?q=${encodeURIComponent(query)}`)
  .then(r => r.json())
  .then(json => callback(json))
  .catch(() => callback());
  }
});



   // Inicializar TomSelect en el select de “Cliente”
  const clientesSelect = document.querySelector('#clientes');
  if (clientesSelect) {
    new TomSelect(clientesSelect, {
      maxItems: 1,
      valueField: 'value',
      labelField: 'text',
      searchField: 'text',
      placeholder: 'Selecciona un cliente',
      create: false
    });
  }

  // Mostrar/ocultar campos según tipo
  function switchFields(type) {
    if (type === 'Reserva' || type === 'Clase') {
      clientesField.classList.remove('d-none');
      entrenadorField.classList.remove('d-none');
      responsableField.classList.add('d-none');
    } else if (type === 'Torneo') {
      clientesField.classList.add('d-none');
      entrenadorField.classList.add('d-none');
      responsableField.classList.remove('d-none');
    }
  }
  
  
    
  

  // Inicializar FullCalendar
  let calendar = new Calendar(calendarEl, {
	   
          
	   plugins: [
      interactionPlugin,
      dayGridPlugin,
      timeGridPlugin,
      listPlugin                          // 👈 AÑADIDO
    ],
    locales: [ esLocale ],
    locale: 'es',
	timeZone: 'UTC',
    headerToolbar: { left: 'prev,next today', center: 'title', right: 'listDay,timeGridWeek,dayGridMonth' },
    buttonText: { today: 'Hoy', month: 'Mes', week: 'Semana'},
    initialView: 'dayGridMonth',
	listDayFormat: { weekday: 'long', day: '2-digit', month: 'short' },

    selectable: true,
    selectMirror: true,
	
    eventDisplay: 'block',
	 displayEventTime: true, // Es true por defecto, pero lo ponemos explícito

    // 3) Formato de hora que quieres mostrar (por defecto FullCalendar usa algo como “13:30” en 24h)
    eventTimeFormat: {
      hour:   '2-digit',   // muestra 2 dígitos de la hora
      minute: '2-digit',   // muestra 2 dígitos de los minutos
      hour12: false        // o `true` si prefieres mostrar en formato AM/PM
    },
	
	
	

    select: info => {
      if (eventIdInput) {
        eventIdInput.value = '';
      }
      disableCancelButton();
      typeSelect.value     = 'Reserva';
      refreshCancelButtonTextForType('Reserva');
      switchFields('Reserva');
      methodIn.value       = 'POST';
      form.action          = TYPE_MAP['Reserva'].url;
      form.method          = 'POST';

    //  inicioInput.value    = dt.toISOString().slice(0,16);
      durationSelect.value = '60';
      if (clienteSelect.tomselect) {
        clienteSelect.tomselect.clear(true);
      } else {
        clienteSelect.value = '';
      }
      entrenadorSelect.value = '';
      responsableInput.value = '';
          fechaInput.value = info.startStr.split('T')[0];
      // dispara la recarga de slots
      cargarSlots();

      modal.show();
    },
	
           // Captura el click sobre un día
    dateClick: info => {
      // info.dateStr viene en formato "YYYY-MM-DD"
      if (eventIdInput) {
        eventIdInput.value = '';
      }
      disableCancelButton();
      fechaInput.value = info.dateStr
      // opcional: abrir tu modal de reserva aquí
      modal.show()
    },

      eventClick: info => {
                 fechaInput.removeEventListener('change', cargarSlots);
        const ev    = info.event;
      const props = ev.extendedProps;
      const type  = props.type;
      if (eventIdInput) {
        eventIdInput.value = ev.id;
      }
      enableCancelButton(ev.id);
  // extraemos horas y minutos en local:
  
 
  const hrs   = String(ev.start.getUTCHours()).padStart(2,'0');
  const mins  = String(ev.start.getUTCMinutes()).padStart(2,'0');
  const time  = `${hrs}:${mins}`;    // "07:00"
  const date  = ev.start.toISOString().split('T')[0];
	  
              console.log('[DEBUG] extendedProps:', props);
      typeSelect.value                     = type;
      refreshCancelButtonTextForType(type);
      switchFields(type);
      methodIn.value                       = 'PUT';
form.action                          = '/reservas/' + ev.id;
      form.method                          = 'POST';
                 // 1) Rellenar el input de fecha (YYYY-MM-DD)
  //    ev.start.toISOString() === "2025-06-12T14:30:00.000Z"
  fechaInput.value = ev.start.toISOString().split('T')[0];

      if (estadoSelect) {
        const estadoActual = props.status || props.estado || ev.extendedProps?.estado;
        if (estadoActual) {
          estadoSelect.value = estadoActual;
        }
      }
 
         

		
     // inicioInput.value                    = ev.start.toISOString().slice(0,16);
      durationSelect.value                 = props.duration;
      entrenadorSelect.value              = props.entrenador_id || '';
      if (clienteSelect.tomselect) {
        const ts = clienteSelect.tomselect;
        ts.clear(true);
        if (props.cliente_id) {
          const nombre = props.title || ev.title || '';
          ts.addOption({ value: String(props.cliente_id), text: nombre });
          ts.setValue(String(props.cliente_id), true);
        }

                if (estado) {
                  const badge = document.createElement('span');
                  badge.classList.add('badge', 'align-self-start', 'fs-8');
                  estadoClasses.forEach((cls) => badge.classList.add(cls));
                  badge.innerText = estado;
                  cont.appendChild(badge);
                }

                return { domNodes: [cont] };
              }

              const container = document.createElement('div');
              container.classList.add('d-flex', 'flex-column', 'align-items-start', 'position-relative');

              if (timeText) {
                const timeBadge = document.createElement('span');
                timeBadge.classList.add('badge', 'bg-primary', 'mb-1', 'fs-7');
                timeBadge.innerText = timeText;
                container.appendChild(timeBadge);
              }

              if (estado) {
                const badge = document.createElement('span');
                badge.classList.add('badge', 'ms-auto', 'position-absolute', 'top-0', 'end-0', 'me-1', 'mt-1', 'fs-8');
                estadoClasses.forEach((cls) => badge.classList.add(cls));
                badge.innerText = estado;
                container.appendChild(badge);
              }

              lineas.forEach((linea, idx) => {
                const span = document.createElement('span');
                span.innerText = linea;
                span.classList.add(idx === 0 ? 'fw-bold' : 'text-muted', 'fs-7');
                container.appendChild(span);
              });

              return { domNodes: [container] };
            },
          });

          calendar.render();

          if (entrenadorFilter) {
            entrenadorFilter.addEventListener('change', () => {
              calendar.refetchEvents();
            });
          }

          if (fechaInput) {
            fechaInput.addEventListener('change', handleFechaChange);
          }

          if (horaSelect) {
            horaSelect.addEventListener('change', updateStartField);
          }

          if (form) {
            form.addEventListener('submit', (event) => {
              updateStartField();
              if (startInput && (!startInput.value || !fechaInput?.value || !horaSelect?.value)) {
                event.preventDefault();
                window.alert('Selecciona fecha y hora.');
              }
            });
          }

          if (cancelBtn) {
            cancelBtn.addEventListener('click', async () => {
              const reservaId = cancelBtn.dataset.reservaId || eventIdInput?.value || '';
              if (!reservaId) {
                return;
              }

              if (!window.confirm('¿Deseas cancelar esta cita?')) {
                return;
              }

              disableCancelButton();
              setCancelButtonText('Cancelando…');
              if (estadoSelect) {
                estadoSelect.value = 'Cancelada';
              }

              try {
                const { data } = await axios.post(`/reservas/${reservaId}/cancelar`, { estado: 'Cancelada' });

                const calendarEvent = calendar.getEventById(String(reservaId));
                if (calendarEvent) {
                  calendarEvent.remove();
                }
                await calendar.refetchEvents();

                document.dispatchEvent(new CustomEvent('reserva:cancelada', { detail: { id: reservaId } }));

                hideCancelButton();
                if (estadoSelect) {
                  const estadoFinal = data?.reserva?.estado || 'Cancelada';
                  estadoSelect.value = estadoFinal;
                }
                if (methodInput) {
                  methodInput.value = 'POST';
                }
                form.setAttribute('action', defaultReservaAction);
                modal.hide();
                window.alert(data?.message ?? 'La cita ha sido cancelada correctamente.');
                if (eventIdInput) {
                  eventIdInput.value = '';
                }
                updateCancelButtonVisibility();
              } catch (error) {
                console.error('Error al cancelar la cita', error);
                window.alert('No se pudo cancelar la cita. Inténtalo nuevamente.');
                enableCancelButton(reservaId);
              } finally {
                refreshCancelButtonTextForType(typeSelect?.value);
              }
            });
          }

          modalEl.addEventListener('hidden.bs.modal', () => {
            disableCancelButton();
            hideCancelButton();
            if (eventIdInput) {
              eventIdInput.value = '';
            }
            if (methodInput) {
              methodInput.value = 'POST';
            }
            form.setAttribute('action', defaultReservaAction);
            if (typeSelect) {
              typeSelect.value = 'Reserva';
              refreshCancelButtonTextForType('Reserva');
            }
            switchFields('Reserva');
          });

          modalEl.addEventListener('show.bs.modal', updateCancelButtonVisibility);
          modalEl.addEventListener('shown.bs.modal', updateCancelButtonVisibility);

          if (eventIdInput) {
            eventIdInput.addEventListener('input', updateCancelButtonVisibility);
            eventIdInput.addEventListener('change', updateCancelButtonVisibility);
          }

          if (methodInput) {
            methodInput.addEventListener('change', updateCancelButtonVisibility);
          }
        }
      }
    }
  }

  const modalPago = document.getElementById('modalPagarFactura');
  if (modalPago) {
    let triggerButton = null;

    modalPago.addEventListener('show.bs.modal', (event) => {
      triggerButton = event.relatedTarget || null;
    });

  if (cancelBtn) {
    cancelBtn.addEventListener('click', async () => {
      const reservaId = cancelBtn.dataset.reservaId || (eventIdInput ? eventIdInput.value : '');
      if (!reservaId) {
        return;
      }

      const ordenId = triggerButton.getAttribute('data-cuenta');
      if (!ordenId) {
        return;
      }

      cancelBtn.disabled = true;
      cancelBtn.setAttribute('disabled', 'disabled');
      cancelBtn.setAttribute('aria-disabled', 'true');
      setCancelButtonText('Cancelando…');
      if (estadoSelect) {
        estadoSelect.value = 'Cancelada';
      }

      try {
        const { data } = await axios.post(`/reservas/${reservaId}/cancelar`, {
          estado: 'Cancelada'
        });

        const calendarEvent = calendar.getEventById(String(reservaId));
        if (calendarEvent) {
          calendarEvent.remove();
        }
        await calendar.refetchEvents();

        document.dispatchEvent(new CustomEvent('reserva:cancelada', {
          detail: { id: reservaId }
        }));

        disableCancelButton();
        if (estadoSelect) {
          const estadoFinal = data?.reserva?.estado || 'Cancelada';
          estadoSelect.value = estadoFinal;
        }
        if (methodIn) {
          methodIn.value = 'POST';
        }
        if (form && TYPE_MAP?.Reserva?.url) {
          form.setAttribute('action', TYPE_MAP.Reserva.url);
        }
        modal.hide();
        window.alert(data?.message ?? 'La cita ha sido cancelada correctamente.');
        if (eventIdInput) {
          eventIdInput.value = '';
        }
        updateCancelButtonVisibility();
      } catch (error) {
        console.error('Error al cancelar la cita', error);
        window.alert('No se pudo cancelar la cita. Inténtalo nuevamente.');
        enableCancelButton(reservaId);
      } finally {
        refreshCancelButtonTextForType(typeSelect?.value);
      }
    });
  }

          const totalDisplay = document.querySelector('#totalInvoiceDisplay');
          if (totalDisplay) {
            totalDisplay.textContent = data.resta.toLocaleString('es-CO', {
              style: 'currency',
              currency: 'COP',
            });
          }
        })
        .catch((error) => {
          console.error('No se pudieron actualizar los totales de la orden.', error);
        });
    };

    const confirmarPagoBtn = modalPago.querySelector('.btn-confirmar-pago');
    if (confirmarPagoBtn) {
      confirmarPagoBtn.addEventListener('click', actualizarTotales);
    }
  }
});


// ...tu código JS existente (Bootstrap, intl-tel-input, etc.)...

        
class Components {
    initBootstrapComponents() {
        [...document.querySelectorAll('[data-bs-toggle="popover"]')].map(
            (e) => new bootstrap.Popover(e)
        ),
            [...document.querySelectorAll('[data-bs-toggle="tooltip"]')].map(
                (e) => new bootstrap.Tooltip(e)
            ),
            [...document.querySelectorAll(".offcanvas")].map(
                (e) => new bootstrap.Offcanvas(e)
            );
        var e = document.getElementById("toastPlacement"),
            t = document.getElementById("selectToastPlacement"),
            n =
                (e &&
                    t &&
                    t.addEventListener("change", function () {
                        e.dataset.originalClass ||
                            (e.dataset.originalClass = e.className),
                            (e.className =
                                e.dataset.originalClass + " " + this.value);
                    }),
                [].slice
                    .call(document.querySelectorAll(".toast"))
                    .map(function (e) {
                        return new bootstrap.Toast(e);
                    }),
                document.getElementById("liveAlertBtn"));
        n &&
            n.addEventListener("click", () => {
                alert("Nice, you triggered this alert message!", "success");
            });
			document.addEventListener('DOMContentLoaded', () => {
  const inputs = document.querySelectorAll('.phone-input');
  inputs.forEach(input => {
    intlTelInput(input, {
      initialCountry: input.dataset.country || 'co',
      separateDialCode: true,
      utilsScript: 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js',
    });
  });
});

    }
    initfullScreenListener() {
        var e = document.querySelector('[data-toggle="fullscreen"]');
        e &&
            e.addEventListener("click", function (e) {
                e.preventDefault(),
                    document.body.classList.toggle("fullscreen-enable"),
                    document.fullscreenElement ||
                    document.mozFullScreenElement ||
                    document.webkitFullscreenElement
                        ? document.cancelFullScreen
                            ? document.cancelFullScreen()
                            : document.mozCancelFullScreen
                            ? document.mozCancelFullScreen()
                            : document.webkitCancelFullScreen &&
                              document.webkitCancelFullScreen()
                        : document.documentElement.requestFullscreen
                        ? document.documentElement.requestFullscreen()
                        : document.documentElement.mozRequestFullScreen
                        ? document.documentElement.mozRequestFullScreen()
                        : document.documentElement.webkitRequestFullscreen &&
                          document.documentElement.webkitRequestFullscreen(
                              Element.ALLOW_KEYBOARD_INPUT
                          );
						  
            });
    }
    initCounter() {
        var e = document.querySelectorAll(".counter-value");
        function a(e) {
            return e.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
        e &&
            e.forEach(function (i) {
                !(function e() {
                    var t = +i.getAttribute("data-target"),
                        n = +i.innerText,
                        o = t / 250;
                    o < 1 && (o = 1),
                        n < t
                            ? ((i.innerText = (n + o).toFixed(0)),
                              setTimeout(e, 1))
                            : (i.innerText = a(t)),
                        a(i.innerText);
                })();
            });
    }
    init() {
        this.initBootstrapComponents(),
            this.initfullScreenListener(),
            this.initCounter();
    }
}
class FormValidation {
    initFormValidation() {
        document.querySelectorAll(".needs-validation").forEach((t) => {
            t.addEventListener(
                "submit",
                (e) => {
                    t.checkValidity() ||
                        (e.preventDefault(), e.stopPropagation()),
                        t.classList.add("was-validated");
                },
                !1
            );
        });
    }
    init() {
        this.initFormValidation();
    }
}


const FALLBACK_LAYOUT_CONFIG = {
    theme: "light",
    topbar: { color: "light" },
    menu: { size: "default", color: "light" },
    color: { primary: "#0d6efd" },
};

class ThemeLayout {
    constructor() {
        (this.html = document.getElementsByTagName("html")[0]),
            (this.config = this._buildSafeConfig(window.config)),
            (this.defaultConfig = this._buildSafeConfig(window.defaultConfig)),
            (this._sidebarBackdrop = null),
            (this._lastNonHiddenMenuSize =
                this.html.getAttribute("data-sidebar-size") ||
                (this.config &&
                    this.config.menu &&
                    this.config.menu.size) ||
                FALLBACK_LAYOUT_CONFIG.menu.size),
            "hidden" === this._lastNonHiddenMenuSize &&
                (this._lastNonHiddenMenuSize =
                    (this.config &&
                        this.config.menu &&
                        this.config.menu.size) ||
                    FALLBACK_LAYOUT_CONFIG.menu.size);
    }
    _cloneConfig(e) {
        if (!e || "object" != typeof e) return {};
        try {
            return JSON.parse(JSON.stringify(e));
        } catch (t) {
            return Object.assign({}, e);
        }
    }
    _buildSafeConfig(e) {
        var t = this._cloneConfig(FALLBACK_LAYOUT_CONFIG),
            n = this._cloneConfig(e);
        (n.topbar = Object.assign({}, t.topbar, n.topbar || {})),
            (n.menu = Object.assign({}, t.menu, n.menu || {})),
            (n.color = Object.assign({}, t.color, n.color || {})),
            (n.theme = n.theme || t.theme),
            (n.topbar.color = n.topbar.color || t.topbar.color),
            (n.menu.size = n.menu.size || t.menu.size),
            (n.menu.color = n.menu.color || t.menu.color),
            (n.color.primary = n.color.primary || t.color.primary);
        return Object.assign({}, t, n);
    }
    initVerticalMenu() {
        var e = document.querySelectorAll(".navbar-nav li .collapse");
        document
            .querySelectorAll(".navbar-nav li [data-bs-toggle='collapse']")
            .forEach((e) => {
                e.addEventListener("click", function (e) {
                    e.preventDefault();
                });
            }),
            e.forEach((e) => {
                e.addEventListener("show.bs.collapse", function (t) {
                    let n = t.target.closest(".collapse.show");
                    document
                        .querySelectorAll(".navbar-nav .collapse.show")
                        .forEach((e) => {
                            e !== t.target &&
                                e !== n &&
                                new bootstrap.Collapse(e).hide();
                        });
                });
            }),
            document.querySelector(".navbar-nav") &&
                (document
                    .querySelectorAll(".navbar-nav a")
                    .forEach(function (t) {
                        var e = window.location.href.split(/[?#]/)[0];
                        if (t.href === e) {
                            t.classList.add("active"),
                                t.parentNode.classList.add("active");
                            let e = t.closest(".collapse");
                            for (; e; )
                                e.classList.add("show"),
                                    e.parentElement.children[0].classList.add(
                                        "active"
                                    ),
                                    e.parentElement.children[0].setAttribute(
                                        "aria-expanded",
                                        "true"
                                    ),
                                    (e = e.parentElement.closest(".collapse"));
                        }
                    }),
                setTimeout(function () {
                    var e,
                        n,
                        o,
                        i,
                        a,
                        t = document.querySelector(".nav-item li a.active");
                    null != t &&
                        ((e = document.querySelector(
                            ".app-sidebar .simplebar-content-wrapper"
                        )),
                        (t = t.offsetTop - 300),
                        e) &&
                        100 < t &&
                        ((o = (n = e).scrollTop),
                        (i = t - o),
                        (a = 0),
                        (function e() {
                            var t = (a += 20),
                                t =
                                    (t /= 300) < 1
                                        ? (i / 2) * t * t + o
                                        : (-i / 2) * (--t * (t - 2) - 1) + o;
                            (n.scrollTop = t), a < 600 && setTimeout(e, 20);
                        })());
                }, 200));
    }
    initConfig() {
        (this.defaultConfig = this._buildSafeConfig(window.defaultConfig)),
            (this.config = this._buildSafeConfig(window.config)),
            this.setSwitchFromConfig();
    }
    changeMenuColor(e) {
        (this.config.menu = this.config.menu || {}),
            (this.config.menu.color =
                e || this.config.menu.color || FALLBACK_LAYOUT_CONFIG.menu.color),
            this.html.setAttribute("data-sidebar-color", this.config.menu.color),
            this.setSwitchFromConfig();
    }
    changeMenuSize(e, t = !0) {
        (this.config.menu = this.config.menu || {}),
            this.html.setAttribute("data-sidebar-size", e),
            "hidden" !== e && (this._lastNonHiddenMenuSize = e),
            t &&
                ((this.config.menu.size =
                    e || this.config.menu.size || FALLBACK_LAYOUT_CONFIG.menu.size),
                this.setSwitchFromConfig());
    }
    changeThemeMode(e) {
        (this.config.theme =
            e || this.config.theme || FALLBACK_LAYOUT_CONFIG.theme),
            this.html.setAttribute("data-bs-theme", this.config.theme),
            this.setSwitchFromConfig();
    }
    changeTopbarColor(e) {
        (this.config.topbar = this.config.topbar || {}),
            (this.config.topbar.color =
                e ||
                this.config.topbar.color ||
                FALLBACK_LAYOUT_CONFIG.topbar.color),
            this.html.setAttribute("data-topbar-color", this.config.topbar.color),
            this.setSwitchFromConfig();
    }
    resetTheme() {
        (this.config = this._buildSafeConfig(window.defaultConfig)),
            this.changeMenuColor(this.config.menu.color),
            this.changeMenuSize(this.config.menu.size),
            this.changeThemeMode(this.config.theme),
            this.changeTopbarColor(this.config.topbar.color),
            this._adjustLayout();
    }
    initSwitchListener() {
        var n = this,
            e =
                (document
                    .querySelectorAll("input[name=data-sidebar-color]")
                    .forEach(function (t) {
                        t.addEventListener("change", function (e) {
                            n.changeMenuColor(t.value);
                        });
                    }),
                document
                    .querySelectorAll("input[name=data-sidebar-size]")
                    .forEach(function (t) {
                        t.addEventListener("change", function (e) {
                            n.changeMenuSize(t.value);
                        });
                    }),
                document
                    .querySelectorAll("input[name=data-bs-theme]")
                    .forEach(function (t) {
                        t.addEventListener("change", function (e) {
                            n.changeThemeMode(t.value);
                        });
                    }),
                document
                    .querySelectorAll("input[name=data-topbar-color]")
                    .forEach(function (t) {
                        t.addEventListener("change", function (e) {
                            n.changeTopbarColor(t.value);
                        });
                    }),
                document.getElementById("light-dark-mode"));
        e &&
            e.addEventListener("click", function (e) {
                "light" === n.config.theme
                    ? n.changeThemeMode("dark")
                    : n.changeThemeMode("light");
            });
        if ((e = document.querySelector("#reset-layout")))
            e.addEventListener("click", function (e) {
                n.resetTheme();
            });
        if ((e = document.querySelector(".button-toggle-menu"))) {
            if ("1" === e.dataset.manualSidebarToggle) {
                e.dataset.themeLayoutBound = "manual";
            } else if ("1" !== e.dataset.themeLayoutBound) {
                e.addEventListener("click", function () {
                    var t =
                            n.html.getAttribute("data-sidebar-size") ||
                            n._lastNonHiddenMenuSize ||
                            (n.config &&
                                n.config.menu &&
                                n.config.menu.size) ||
                            FALLBACK_LAYOUT_CONFIG.menu.size,
                        o =
                            n._lastNonHiddenMenuSize ||
                            (n.config &&
                                n.config.menu &&
                                n.config.menu.size) ||
                            FALLBACK_LAYOUT_CONFIG.menu.size,
                        i = window.innerWidth <= 1140;
                    i
                        ? (n.changeMenuSize("hidden", !1),
                          n.html.classList.contains("sidebar-enable")
                              ? (n.html.classList.remove("sidebar-enable"),
                                n.removeBackdrop())
                              : (n.html.classList.add("sidebar-enable"),
                                n.showBackdrop()))
                        : "hidden" === t
                        ? (n.changeMenuSize(o, !1),
                          n.html.classList.add("sidebar-enable"),
                          n.removeBackdrop())
                        : ((n._lastNonHiddenMenuSize =
                              "hidden" !== t ? t : n._lastNonHiddenMenuSize),
                          n.changeMenuSize("hidden", !1),
                          n.html.classList.remove("sidebar-enable"),
                          n.removeBackdrop()),
                        recordLayoutDiagnostics({ toggleBound: !0 });
                });
                e.dataset.themeLayoutBound = "1";
            }
            recordLayoutDiagnostics({ toggleBound: !0 });
        }
    }
    showBackdrop() {
        if (this._sidebarBackdrop) return this._sidebarBackdrop;
        let t = document.createElement("div"),
            n =
                ((t.classList = "offcanvas-backdrop fade show"),
                document.body.appendChild(t),
                (document.body.style.overflow = "hidden"),
                1040 < window.innerWidth &&
                    (document.body.style.paddingRight = "15px"),
                this);
        return (
            t.addEventListener("click", function () {
                n.html.classList.remove("sidebar-enable"),
                    n.changeMenuSize("hidden", !1),
                    n.removeBackdrop();
            }),
            (this._sidebarBackdrop = t)
        );
    }
    removeBackdrop() {
        this._sidebarBackdrop &&
            (this._sidebarBackdrop.parentNode &&
                this._sidebarBackdrop.parentNode.removeChild(
                    this._sidebarBackdrop
                ),
            (this._sidebarBackdrop = null),
            (document.body.style.overflow = null),
            (document.body.style.paddingRight = null));
    }
    initWindowSize() {
        var t = this;
        window.addEventListener("resize", function (e) {
            t._adjustLayout();
        });
    }
    _adjustLayout() {
        var e =
            (this.config &&
                this.config.menu &&
                this.config.menu.size) ||
            FALLBACK_LAYOUT_CONFIG.menu.size;
        window.innerWidth <= 1140
            ? this.changeMenuSize("hidden", !1)
            : this.changeMenuSize(e);
    }
    setSwitchFromConfig() {
        try {
            sessionStorage.setItem(
                "__DARKONE_CONFIG__",
                JSON.stringify(this.config)
            );
        } catch (err) {
            console.warn(
                "No se pudo persistir la configuración del layout en sessionStorage.",
                err
            );
        }
        document
            .querySelectorAll(".settings-bar input[type=radio]")
            .forEach(function (e) {
                e.checked = !1;
            });
        var e,
            t,
            n,
            o,
            i = this.config || {},
            a = i.theme,
            r = i.topbar && i.topbar.color,
            s = i.menu && i.menu.size,
            c = i.menu && i.menu.color;
        a &&
            (e = document.querySelector(
                "input[type=radio][name=data-bs-theme][value=" + a + "]"
            )) &&
            (e.checked = !0);
        r &&
            (t = document.querySelector(
                "input[type=radio][name=data-topbar-color][value=" +
                    r +
                    "]"
            )) &&
            (t.checked = !0);
        s &&
            (n = document.querySelector(
                "input[type=radio][name=data-sidebar-size][value=" +
                    s +
                    "]"
            )) &&
            (n.checked = !0);
        c &&
            (o = document.querySelector(
                "input[type=radio][name=data-sidebar-color][value=" +
                    c +
                    "]"
            )) &&
            (o.checked = !0);
    }
    init() {
        this.initVerticalMenu(),
            this.initConfig(),
            this.initSwitchListener(),
            this.initWindowSize(),
            this._adjustLayout(),
            this.setSwitchFromConfig();
    }
}
const recordLayoutDiagnostics = (e = {}, t = {}) => {
    if ("undefined" == typeof window || "undefined" == typeof document)
        return window && window.__themeLayoutDiagnostics;
    const n = document.documentElement,
        o = document.querySelector(".button-toggle-menu"),
        i = window.__themeLayoutDiagnostics || {},
        a = Object.assign(
            {
                bootedAt: i.bootedAt || null,
                configReady: !!window.__layoutConfigReady,
                hasInstance: !!window.__themeLayoutInstance,
                sidebarSize: n ? n.getAttribute("data-sidebar-size") : null,
                togglePresent: !!o,
                toggleBound:
                    (o && o.dataset && "1" === o.dataset.themeLayoutBound) ||
                    !!i.toggleBound,
            },
            i,
            e
        );
    return (
        (a.bootedAt = e.bootedAt || a.bootedAt || new Date().toISOString()),
        (window.__themeLayoutDiagnostics = a),
        t.broadcast &&
            window.console &&
            "function" == typeof window.console.debug &&
            window.console.debug("[ThemeLayout] Boot diagnostics", a),
        t.broadcast &&
            (function () {
                try {
                    window.dispatchEvent(
                        new CustomEvent("themeLayout:boot", { detail: a })
                    );
                } catch (e) {
                    if (window.dispatchEvent && document.createEvent) {
                        const t = document.createEvent("Event");
                        t.initEvent("themeLayout:boot", !0, !0),
                            (t.detail = a),
                            window.dispatchEvent(t);
                    }
                }
            })(),
        a
    );
};
const ensureThemeLayout = (e) => {
    var t = window.__themeLayoutInstance;
    if (t)
        return (
            e && e.syncConfig && (t.initConfig(), t._adjustLayout()),
            t
        );
    var n = new ThemeLayout();
    return n.init(), (window.__themeLayoutInstance = n), n;
};
let domReadyFired =
        "complete" === document.readyState ||
        "interactive" === document.readyState,
    needsConfigSync = !!window.__layoutConfigReady;
const bootThemeLayout = () => ensureThemeLayout();
const syncThemeLayoutConfig = () => ensureThemeLayout({ syncConfig: !0 });
"undefined" != typeof window &&
    ((window.bootThemeLayout = bootThemeLayout),
    (window.ensureThemeLayout = ensureThemeLayout),
    (window.syncThemeLayoutConfig = syncThemeLayoutConfig));
const handleDomReady = () => {
    domReadyFired = !0;
    const e = new Date().toISOString();
    bootThemeLayout();
    needsConfigSync && (syncThemeLayoutConfig(), (needsConfigSync = !1));
    recordLayoutDiagnostics({ bootedAt: e }, { broadcast: !0 });
};
domReadyFired
    ? handleDomReady()
    : document.addEventListener("DOMContentLoaded", handleDomReady, {
          once: !0,
      });
window.addEventListener("layout:config-ready", function () {
    domReadyFired
        ? (syncThemeLayoutConfig(), recordLayoutDiagnostics({}, { broadcast: !0 }))
        : (needsConfigSync = !0);
});
