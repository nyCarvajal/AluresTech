import bootstrap from 'bootstrap/dist/js/bootstrap'
window.bootstrap = bootstrap;
import 'iconify-icon';
import 'simplebar/dist/simplebar'
// resources/js/app.js


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
  typeSelect.addEventListener('change', e => {
    switchFields(e.target.value);
  });
  
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



  // Mapeo de tipos a URL base
  const TYPE_MAP = {
    Reserva: { url: '/reservas' },
    Clase:   { 
	
	url: '/clases'   },
    Torneo:  { url: '/torneos'  },
  };
  
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
      typeSelect.value     = 'Reserva';
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
      fechaInput.value = info.dateStr
      // opcional: abrir tu modal de reserva aquí
      const miModal = new bootstrap.Modal(document.getElementById('reservaModal'))
      miModal.show()
    },

      eventClick: info => {
                 fechaInput.removeEventListener('change', cargarSlots);
        const ev    = info.event;
      const props = ev.extendedProps;
      const type  = props.type; 
  // extraemos horas y minutos en local:
  
 
  const hrs   = String(ev.start.getUTCHours()).padStart(2,'0');
  const mins  = String(ev.start.getUTCMinutes()).padStart(2,'0');
  const time  = `${hrs}:${mins}`;    // "07:00"
  const date  = ev.start.toISOString().split('T')[0];
	  
              console.log('[DEBUG] extendedProps:', props);
      typeSelect.value                     = type;
      switchFields(type);
      methodIn.value                       = 'PUT';
form.action                          = '/reservas/' + ev.id;
      form.method                          = 'POST';
                 // 1) Rellenar el input de fecha (YYYY-MM-DD)
  //    ev.start.toISOString() === "2025-06-12T14:30:00.000Z"
  fechaInput.value = ev.start.toISOString().split('T')[0];
 
         

		
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

      } else {
        clienteSelect.value = props.cliente_id || '';
      }

        
      
	  
	  // 2) Rellenar el select de hora (HH:mm)
  //    Usamos substring de la parte de hora
  
   cargarSlots().then(() => {
    // Inyectar opción extra si hace falta
    const exists = [...horaSelect.options].some(o => o.value === time);
    if (!exists) {
      const extra = document.createElement('option');
      extra.value = time;
      extra.text  = time;
      horaSelect.insertBefore(extra, horaSelect.options[1] || null);
    }
    // Desmarcamos la blank y seleccionamos tu hora
    horaSelect.options[0].selected = false;
    horaSelect.value = time;

    // 5) Volvemos a colocar los listeners
    fechaInput.addEventListener('change',   cargarSlots);

    // 6) Abrimos el modal
    modal.show();
  });
    },

      events: {
        url: cfg.eventsUrl,   // p.ej. '/reservas.json'
        method: 'GET',
        extraParams: () => ({
          entrenador_id: entrenadorFilter ? entrenadorFilter.value : ''
        })
      },
	   // 2) Permite seleccionar rangos
    
    
    eventDataTransform: raw => ({
      id:              raw.id,
      title:           raw.title,
      start:           raw.start,
      end:             raw.end,
      backgroundColor: raw.backgroundColor,
      borderColor:     raw.borderColor,
      display:         'block',
      extendedProps:   raw,
	   
    }),
	
        datesSet: info => {
      // cada vez que cambias de día, recarga disponibilidad
      const date = info.startStr.split('T')[0];
      axios.get('/reserva/availability', { params: { date } })
        .then(res => {
          const { minTime, maxTime } = res.data;
          calendar.setOption('slotMinTime', minTime);
          calendar.setOption('slotMaxTime', maxTime);
        });
    },

	
	 // — AÑADE ESTE CALLBACK PARA INTERPRETAR SALTOS DE LÍNEA —
   

      eventContent: function(arg) {
		  
		  
  /* ---- 0)  Detectamos si estamos en una vista de LISTA ---- */
  const esLista = arg.view.type.startsWith('list');

  /* ---- 1)  Preprocesamos el título (tu lógica actual) ---- */
  let rawTitle = arg.event.title || '';
  rawTitle = rawTitle.replace(/\\n/g, '\n');
  const lineas = rawTitle.split('\n');

  /* ---- 2)  COMMON data: extended props ---- */
  const estado = arg.event.extendedProps.status;      // Confirmada / Pendiente…
  const time   = arg.timeText;

   


  /* ********************************************************************* *
   *  A)  RENDER PARA VISTAS LIST*  (listDay, listWeek, listMonth, …)      *
   * ********************************************************************* */
  if (esLista) {
    /*  FullCalendar envuelve cada evento de lista así:
          <tr class="fc-list-event">  <td class="fc-list-event-time"> … </td>
                                       <td class="fc-list-event-title"> … </td> </tr>
        Lo que devuelvas aquí se inserta dentro de <td class="fc-list-event-title">
    */
    const cont = document.createElement('div');
    cont.classList.add('d-flex', 'flex-column', 'gap-1');

    // línea 1: título principal
    const fila1 = document.createElement('div');
    fila1.innerHTML = `<span class="fw-bold">${lineas[0]}</span>`;
    cont.appendChild(fila1);

    // líneas extra del título, si las hubiera
    lineas.slice(1).forEach(t => {
      const s = document.createElement('span');
      s.classList.add('text-muted', 'fs-7');
      s.innerText = t;
      cont.appendChild(s);
    });

    // badge de estado (Confirmada / Pendiente…)
    if (estado) {
      const badge = document.createElement('span');
      badge.classList.add('badge', 'align-self-start', 'fs-8');
      if (estado === 'Confirmada') badge.classList.add('bg-success');
      else                         badge.classList.add('bg-warning', 'text-dark');
      badge.innerText = estado;
      cont.appendChild(badge);
    }

    // devolvemos sólo la parte del título; la hora ya la pinta FC en la 1ª columna
    return { domNodes: [cont] };
  }

  /* ********************************************************************* *
   *  B)  RENDER PARA dayGrid / timeGrid  (tu código original, retocado)   *
   * ********************************************************************* */
  const container = document.createElement('div');
  container.classList.add('d-flex', 'flex-column', 'align-items-start');

  /*  Hora en badge */
 if (time) {
  const timeEl = document.createElement('span');
  timeEl.classList.add('badge', 'bg-primary', 'mb-1', 'fs-7');
  timeEl.innerText = time;
  container.appendChild(timeEl);
}

  /*  Estado en la esquina superior derecha */
  if (estado) {
    const badge = document.createElement('span');
    badge.classList.add('badge', 'ms-auto', 'position-absolute', 'top-0', 'end-0', 'me-1', 'mt-1', 'fs-8');
    if (estado === 'Confirmada') {
      badge.classList.add('bg-success');
    } else {
      badge.classList.add('bg-warning', 'text-dark');
    }
    badge.innerText = estado;
    container.appendChild(badge);
  }

  /*  Líneas de título */
  lineas.forEach((l, idx) => {
    const span = document.createElement('span');
    span.innerText = l;
    span.classList.add(idx === 0 ? 'fw-bold' : 'text-muted', 'fs-7');
    container.appendChild(span);
  });
		  
		  
		

      return { domNodes: [ container ] };
	  
    }

  
  });
  
   


  calendar.render();

  if (entrenadorFilter) {
    entrenadorFilter.addEventListener('change', () => calendar.refetchEvents());
  }
  
 
  form.addEventListener('submit', () => {
 /**   const dt     = new Date(inicioInput.value);
    const durMin = parseInt(durationSelect.value, 10);
    const endDt  = new Date(dt.getTime() + durMin*60000);

    let endInput = form.querySelector('input[name="end"]');
    if (!endInput) {
      endInput = document.createElement('input');
      endInput.type = 'hidden';
      endInput.name = 'end';
      form.appendChild(endInput);
    }
    endInput.value = endDt.toISOString().slice(0,16); */
  });
  
  
   const modalPago = document.getElementById('modalPagarFactura');
  // ... toda la inicialización del modal aquí ...
  
  modalPago.addEventListener('show.bs.modal', function(event) {
    triggerButton = event.relatedTarget;
    // ... resto de lógica de show.bs.modal ...
  });

  // Aquí defines actualizarTotales justo después de capturar triggerButton:
  function actualizarTotales() {
    const ordenId = triggerButton.getAttribute('data-cuenta');
    fetch(`/orden/${ordenId}/totales`)
      .then(res => res.json())
      .then(data => {
        document.querySelector('#cardTotalFactura').textContent =
          data.totalVentas.toLocaleString('es-CO', { style: 'currency', currency: 'COP' });
        document.querySelector('#totalInvoiceDisplay').textContent =
          data.resta.toLocaleString('es-CO', { style: 'currency', currency: 'COP' });
      });
  }

  // Y luego, tras la llamada que crea la venta o el pago (por AJAX o submit),
  // llamas a actualizarTotales():
  document.querySelector('.btn-confirmar-pago').addEventListener('click', function() {
    // ... lógica que envía el formulario (o AJAX) ...
    actualizarTotales();
  });
  
  
  
});


const fechaInput  = document.getElementById('reservaFecha');
const horaSelect  = document.getElementById('reservaHora');

function cargarSlots() {
  const date = fechaInput.value;

  if (!date) {
    horaSelect.innerHTML = '<option value="">-- Elige hora --</option>';
    return Promise.resolve();
  }

  return axios.get('/reserva/availability', {
    params: { date }
  })
  .then(res => {
    horaSelect.innerHTML = '<option value="">-- Elige hora --</option>';
    res.data.slots.forEach(h => {
      // `h` ya viene como "HH:mm"
      const opt = document.createElement('option');
      opt.value   = h;    // <— aquí solo la parte de la hora
      opt.textContent = h;
      horaSelect.appendChild(opt);
    });
  })
  .catch(err => console.error(err.response?.data || err));
}

// Cuando cambie la fecha..

fechaInput.addEventListener('change', cargarSlots);









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
            t =
                (e &&
                    document
                        .getElementById("selectToastPlacement")
                        .addEventListener("change", function () {
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
        t &&
            t.addEventListener("click", () => {
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


class ThemeLayout {
    constructor() {
        (this.html = document.getElementsByTagName("html")[0]),
            (this.config = {}),
            (this.defaultConfig = window.config);
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
        (this.defaultConfig = JSON.parse(JSON.stringify(window.defaultConfig))),
            (this.config = JSON.parse(JSON.stringify(window.config))),
            this.setSwitchFromConfig();
    }
    changeMenuColor(e) {
        (this.config.menu.color = e),
            this.html.setAttribute("data-sidebar-color", e),
            this.setSwitchFromConfig();
    }
    changeMenuSize(e, t = !0) {
        this.html.setAttribute("data-sidebar-size", e),
            t && ((this.config.menu.size = e), this.setSwitchFromConfig());
    }
    changeThemeMode(e) {
        (this.config.theme = e),
            this.html.setAttribute("data-bs-theme", e),
            this.setSwitchFromConfig();
    }
    changeTopbarColor(e) {
        (this.config.topbar.color = e),
            this.html.setAttribute("data-topbar-color", e),
            this.setSwitchFromConfig();
    }
    resetTheme() {
        (this.config = JSON.parse(JSON.stringify(window.defaultConfig))),
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
            }),
            (e = document.querySelector("#reset-layout")) &&
                e.addEventListener("click", function (e) {
                    n.resetTheme();
                }),
            (e = document.querySelector(".button-toggle-menu")) &&
                e.addEventListener("click", function () {
                    var e = n.config.menu.size,
                        t = n.html.getAttribute("data-sidebar-size", e);
                    "hidden" !== t
                        ? "condensed" === t
                            ? n.changeMenuSize(
                                  "condensed" == e ? "default" : e,
                                  !1
                              )
                            : n.changeMenuSize("condensed", !1)
                        : n.showBackdrop(),
                        n.html.classList.toggle("sidebar-enable");
                });
    }
    showBackdrop() {
        let t = document.createElement("div"),
            n =
                ((t.classList = "offcanvas-backdrop fade show"),
                document.body.appendChild(t),
                (document.body.style.overflow = "hidden"),
                1040 < window.innerWidth &&
                    (document.body.style.paddingRight = "15px"),
                this);
        t.addEventListener("click", function (e) {
            n.html.classList.remove("sidebar-enable"),
                document.body.removeChild(t),
                (document.body.style.overflow = null),
                (document.body.style.paddingRight = null);
        });
    }
    initWindowSize() {
        var t = this;
        window.addEventListener("resize", function (e) {
            t._adjustLayout();
        });
    }
    _adjustLayout() {
        window.innerWidth <= 1140
            ? this.changeMenuSize("hidden", !1)
            : this.changeMenuSize(this.config.menu.size);
    }
    setSwitchFromConfig() {
        sessionStorage.setItem(
            "__DARKONE_CONFIG__",
            JSON.stringify(this.config)
        ),
            document
                .querySelectorAll(".settings-bar input[type=radio]")
                .forEach(function (e) {
                    e.checked = !1;
                });
        var e,
            t,
            n,
            o = this.config;
        o &&
            ((e = document.querySelector(
                "input[type=radio][name=data-bs-theme][value=" + o.theme + "]"
            )),
            (t = document.querySelector(
                "input[type=radio][name=data-topbar-color][value=" +
                    o.topbar.color +
                    "]"
            )),
            (n = document.querySelector(
                "input[type=radio][name=data-sidebar-size][value=" +
                    o.menu.size +
                    "]"
            )),
            (o = document.querySelector(
                "input[type=radio][name=data-sidebar-color][value=" +
                    o.menu.color +
                    "]"
            )),
            e && (e.checked = !0),
            t && (t.checked = !0),
            n && (n.checked = !0),
            o) &&
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
new ThemeLayout().init();