

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


//calendario



// Gestión de inicialización del calendario
const calendarInitState = {
  initialized: false,
  waitingDom: false,
  warnedMissingContainer: false,
  warnedMissingModal: false,
  warnedMissingForm: false,
};

const initializeCalendar = () => {
  if (calendarInitState.initialized) {
    return;
  }

  if (document.readyState === 'loading') {
    if (!calendarInitState.waitingDom) {
      calendarInitState.waitingDom = true;
      document.addEventListener(
        'DOMContentLoaded',
        () => {
          calendarInitState.waitingDom = false;
          initializeCalendar();
        },
        { once: true },
      );
    }
    return;
  }

  const cfg = window.CalendarConfig;
  if (!cfg) {
    return;
  }

  const calendarEl = cfg.selector ? document.querySelector(cfg.selector) : null;
  if (!calendarEl) {
    if (!calendarInitState.warnedMissingContainer) {
      calendarInitState.warnedMissingContainer = true;
      console.warn('No se encontró el contenedor del calendario, se omite la inicialización.');
    }
    return;
  }

  const modalEl = cfg.modalSelector ? document.querySelector(cfg.modalSelector) : null;
  if (!modalEl) {
    if (!calendarInitState.warnedMissingModal) {
      calendarInitState.warnedMissingModal = true;
      console.warn('No se encontró el modal configurado para el calendario, se omite la inicialización.');
    }
    return;
  }

  const form = modalEl.querySelector('form');
  if (!form) {
    if (!calendarInitState.warnedMissingForm) {
      calendarInitState.warnedMissingForm = true;
      console.warn('No se encontró el formulario del calendario, se omite la inicialización.');
    }
    return;
  }

  console.log('🚀 app.js arrancó, intentando FullCalendar…');

  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
  if (csrfToken) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
  }
  axios.defaults.headers.common['Accept'] = 'application/json';

  const modal = new bootstrap.Modal(modalEl);
  form.setAttribute('method', 'POST');
  const entrenadorFilter = cfg.filterSelector ? document.querySelector(cfg.filterSelector) : null;

  const methodInput = form.querySelector('#reservationMethod');
  const typeSelect = form.querySelector('#eventType');
  const durationSelect = form.querySelector('#reservaDuracion');
  const fechaInput = form.querySelector('#reservaFecha');
  const horaSelect = form.querySelector('#reservaHora');
  const startInput = form.querySelector('#start');
  const eventIdInput = form.querySelector('#eventId');
  const cancelBtn = form.querySelector('#reservationCancel');
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

  const entrenadorField = form.querySelector('#fieldEntrenador');
  const servicioField = form.querySelector('#fieldServicio');
  const clienteInput = form.querySelector('#clienteId');
  const entrenadorSelect = form.querySelector('#entrenador');
  const servicioSelect = form.querySelector('#servicio');
  const cuentaInfo = form.querySelector('#fieldCuenta');
  const cuentaLink = form.querySelector('#reservationCuentaLink');
  const cuentaLabel = form.querySelector('#reservationCuentaLabel');

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
    Clase: { url: '/clases' },
    Torneo: { url: '/torneos' },
  };
  const defaultReservaAction = TYPE_MAP.Reserva?.url || form.getAttribute('action') || '/reservas';

  const showCancelButton = () => {
    if (!cancelBtn) return;
    cancelBtn.classList.remove('d-none');
  };

  const hideCancelButton = () => {
    if (!cancelBtn) return;
    cancelBtn.classList.add('d-none');
    delete cancelBtn.dataset.reservaId;
  };

  const disableCancelButton = () => {
    if (!cancelBtn) return;
    cancelBtn.disabled = true;
    cancelBtn.setAttribute('disabled', 'disabled');
    cancelBtn.setAttribute('aria-disabled', 'true');
    cancelBtn.classList.add('disabled', 'opacity-50');
    refreshCancelButtonTextForType(typeSelect?.value);
  };

  const enableCancelButton = (reservaId) => {
    if (!cancelBtn) return;
    const id = String(reservaId ?? '').trim();
    if (!id) {
      disableCancelButton();
      hideCancelButton();
      return;
    }
    showCancelButton();
    cancelBtn.disabled = false;
    cancelBtn.removeAttribute('disabled');
    cancelBtn.removeAttribute('aria-disabled');
    cancelBtn.classList.remove('disabled', 'opacity-50');
    cancelBtn.dataset.reservaId = id;
    refreshCancelButtonTextForType(typeSelect?.value);
  };

  const resolveReservaId = () => {
    if (eventIdInput?.value && eventIdInput.value.trim()) {
      return eventIdInput.value.trim();
    }

    const datasetId = cancelBtn?.dataset?.reservaId;
    if (datasetId && datasetId.trim()) {
      return datasetId.trim();
    }

    const action = form.getAttribute('action') ?? '';
    const match = action.match(/\/reservas\/(\d+)/);
    if (match && match[1]) {
      return match[1];
    }

    return '';
  };

  const updateCancelButtonVisibility = () => {
    if (!cancelBtn) return;
    const reservaId = resolveReservaId();
    if (reservaId) {
      enableCancelButton(reservaId);
    } else {
      disableCancelButton();
      hideCancelButton();
    }
  };

  const hideCuentaInfo = () => {
    if (cuentaInfo) {
      cuentaInfo.classList.add('d-none');
    }
    if (cuentaLink) {
      cuentaLink.setAttribute('href', '#');
    }
    if (cuentaLabel) {
      cuentaLabel.textContent = '';
    }
  };

  const showCuentaInfo = (label, url) => {
    if (!cuentaInfo) {
      return;
    }
    if (!label || !url) {
      hideCuentaInfo();
      return;
    }

    cuentaInfo.classList.remove('d-none');
    if (cuentaLabel) {
      cuentaLabel.textContent = label;
    }
    if (cuentaLink) {
      cuentaLink.setAttribute('href', url);
    }
  };

  const setRequiredAttribute = (input, enabled) => {
    if (!input) {
      return;
    }

    if (enabled) {
      input.setAttribute('required', 'required');
    } else {
      input.removeAttribute('required');
    }
  };

  const switchFields = (type) => {
    const currentType = (type || '').trim();

    if (entrenadorField) {
      entrenadorField.classList.remove('d-none');
    }

    if (servicioField) {
      servicioField.classList.remove('d-none');
    }

    const requiresServicio = currentType === 'Reserva' || currentType === 'Clase';
    setRequiredAttribute(servicioSelect, requiresServicio);
    setRequiredAttribute(entrenadorSelect, currentType === 'Clase');
    setRequiredAttribute(clienteInput, currentType === 'Reserva');
  };

  const cargarSlots = () => {
    if (!fechaInput || !horaSelect) {
      return Promise.resolve();
    }

    const dateValue = fechaInput.value;
    if (!dateValue) {
      horaSelect.innerHTML = '<option value="">-- Elige hora --</option>';
      return Promise.resolve();
    }

    return axios
      .get('/reserva/availability', { params: { date: dateValue } })
      .then((res) => {
        horaSelect.innerHTML = '<option value="">-- Elige hora --</option>';
        res.data.slots.forEach((slot) => {
          const option = document.createElement('option');
          option.value = slot;
          option.textContent = slot;
          horaSelect.appendChild(option);
        });
      })
      .catch((error) => {
        console.error(error.response?.data || error);
      });
  };

  const updateStartField = () => {
    if (!startInput || !fechaInput || !horaSelect) {
      return;
    }

    if (!fechaInput.value || !horaSelect.value) {
      startInput.value = '';
      return;
    }

    startInput.value = `${fechaInput.value}T${horaSelect.value}:00`;
  };

  const handleFechaChange = () => {
    cargarSlots();
    updateStartField();
  };

  if (typeSelect) {
    typeSelect.addEventListener('change', (event) => {
      const newType = event.target.value;
      switchFields(newType);
      refreshCancelButtonTextForType(newType);
    });
  }

  switchFields(typeSelect?.value || 'Reserva');
  refreshCancelButtonTextForType(typeSelect?.value);
  disableCancelButton();
  hideCancelButton();
  hideCuentaInfo();

  const calendar = new Calendar(calendarEl, {
    plugins: [interactionPlugin, dayGridPlugin, timeGridPlugin, listPlugin],
    locales: [esLocale],
    locale: 'es',
    timeZone: 'UTC',
    headerToolbar: { left: 'prev,next today', center: 'title', right: 'listDay,timeGridWeek,dayGridMonth' },
    buttonText: { today: 'Hoy', month: 'Mes', week: 'Semana' },
    initialView: 'dayGridMonth',
    listDayFormat: { weekday: 'long', day: '2-digit', month: 'short' },
    selectable: true,
    selectMirror: true,
    eventDisplay: 'block',
    displayEventTime: true,
    eventTimeFormat: {
      hour: '2-digit',
      minute: '2-digit',
      hour12: false,
    },
    select: (info) => {
      if (eventIdInput) {
        eventIdInput.value = '';
      }
      disableCancelButton();
      hideCancelButton();
      hideCuentaInfo();

      if (typeSelect) {
        typeSelect.value = 'Reserva';
        refreshCancelButtonTextForType('Reserva');
        switchFields('Reserva');
      }

      if (methodInput) {
        methodInput.value = 'POST';
      }
      form.setAttribute('action', defaultReservaAction);

      if (durationSelect) {
        durationSelect.value = '60';
      }

      if (clienteInput) {
        clienteInput.value = '';
      }

      if (servicioSelect) {
        servicioSelect.value = '';
      }

      if (entrenadorSelect) {
        entrenadorSelect.value = '';
      }

      if (fechaInput) {
        fechaInput.value = info.startStr.split('T')[0];
      }

      cargarSlots().then(() => {
        if (horaSelect) {
          horaSelect.value = '';
        }
        updateStartField();
      });

      modal.show();
    },
    dateClick: (info) => {
      if (eventIdInput) {
        eventIdInput.value = '';
      }
      disableCancelButton();
      hideCancelButton();
      hideCuentaInfo();

      if (fechaInput) {
        fechaInput.value = info.dateStr;
      }

      if (typeSelect) {
        typeSelect.value = 'Reserva';
        refreshCancelButtonTextForType('Reserva');
        switchFields('Reserva');
      }

      if (methodInput) {
        methodInput.value = 'POST';
      }
      form.setAttribute('action', defaultReservaAction);

      cargarSlots().then(() => {
        updateStartField();
      });

      modal.show();
    },
    eventClick: (info) => {
      const ev = info.event;
      const props = ev.extendedProps || {};
      const type = props.type || 'Reserva';
      hideCuentaInfo();

      if (eventIdInput) {
        eventIdInput.value = ev.id;
      }

      if (typeSelect) {
        typeSelect.value = type;
        refreshCancelButtonTextForType(type);
        switchFields(type);
      }

      if (servicioSelect) {
        const servicioId = props.servicio_id ? String(props.servicio_id) : '';
        servicioSelect.value = servicioId;
      }

      if (methodInput) {
        methodInput.value = 'PUT';
      }
      form.setAttribute('action', `/reservas/${ev.id}`);

      if (estadoSelect) {
        const estadoActual = props.status || props.estado || ev.extendedProps?.estado;
        if (estadoActual) {
          estadoSelect.value = estadoActual;
        }
      }

      if (durationSelect && props.duration) {
        durationSelect.value = props.duration;
      }

      if (entrenadorSelect) {
        entrenadorSelect.value = props.entrenador_id || '';
      }

      if (clienteInput) {
        clienteInput.value = props.cliente_id ? String(props.cliente_id) : '';
      }

      if (props.cuenta_label && props.cuenta_url) {
        showCuentaInfo(props.cuenta_label, props.cuenta_url);
      }

      if (props.cuenta_label && props.cuenta_url) {
        showCuentaInfo(props.cuenta_label, props.cuenta_url);
      }

      const eventStart = ev.start;
      let time = '';
      if (eventStart) {
        const hrs = String(eventStart.getUTCHours()).padStart(2, '0');
        const mins = String(eventStart.getUTCMinutes()).padStart(2, '0');
        time = `${hrs}:${mins}`;
        if (fechaInput) {
          fechaInput.value = eventStart.toISOString().split('T')[0];
        }
      }

      cargarSlots().then(() => {
        if (horaSelect && time) {
          const exists = Array.from(horaSelect.options).some((option) => option.value === time);
          if (!exists) {
            const extra = document.createElement('option');
            extra.value = time;
            extra.text = time;
            horaSelect.insertBefore(extra, horaSelect.options[1] || null);
          }

          horaSelect.options[0]?.classList?.remove('selected');
          horaSelect.value = time;
        }
        updateStartField();
      });

      enableCancelButton(ev.id);
      modal.show();
    },
    events: {
      url: cfg.eventsUrl,
      method: 'GET',
      extraParams: () => ({ entrenador_id: entrenadorFilter ? entrenadorFilter.value : '' }),
    },
    eventDataTransform: (raw) => {
      const accentColor = raw.accentColor || raw.borderColor || raw.backgroundColor || '#6366f1';

      return {
        id: raw.id,
        title: raw.title,
        start: raw.start,
        end: raw.end,
        backgroundColor: '#ffffff',
        borderColor: '#e5e7eb',
        display: 'block',
        textColor: '#111827',
        classNames: ['fc-event--card'],
        extendedProps: { ...raw, accentColor },
      };
    },
    datesSet: (info) => {
      const date = info.startStr.split('T')[0];
      axios
        .get('/reserva/availability', { params: { date } })
        .then((res) => {
          const { minTime, maxTime } = res.data;
          calendar.setOption('slotMinTime', minTime);
          calendar.setOption('slotMaxTime', maxTime);
        })
        .catch((error) => {
          console.error('No se pudo actualizar la disponibilidad del calendario.', error);
        });
    },
    eventContent: (arg) => {
      const esLista = arg.view.type.startsWith('list');
      const esMes = arg.view.type === 'dayGridMonth';

      let rawTitle = arg.event.title || '';
      rawTitle = rawTitle.replace(/\n/g, "\n");
      const lineas = rawTitle.split("\n");

      const estado = arg.event.extendedProps.status;
      const timeText = arg.timeText;
      const estadoBadgeClasses = {
        Confirmada: ['bg-success'],
        Pendiente: ['bg-warning', 'text-dark'],
        Cancelada: ['bg-danger'],
        'No Asistida': ['bg-primary'],
      };
      const estadoClasses = estadoBadgeClasses[estado] || ['bg-secondary'];

      const accentColor = arg.event.extendedProps.accentColor || '#6366f1';

      if (esLista) {
        const wrapper = document.createElement('div');
        wrapper.classList.add('fc-event-card', 'd-flex', 'align-items-start', 'gap-2');

        const accentBar = document.createElement('span');
        accentBar.classList.add('fc-event-accent', 'mt-1');
        accentBar.style.backgroundColor = accentColor;

        const cont = document.createElement('div');
        cont.classList.add('d-flex', 'flex-column', 'gap-1', 'flex-grow-1');

        const fila1 = document.createElement('div');
        fila1.innerHTML = `<span class="fw-bold">${lineas[0]}</span>`;
        cont.appendChild(fila1);

        lineas.slice(1).forEach((texto) => {
          const span = document.createElement('span');
          span.classList.add('text-muted', 'fs-7');
          span.innerText = texto;
          cont.appendChild(span);
        });

        if (estado) {
          const badge = document.createElement('span');
          badge.classList.add('badge', 'align-self-start', 'fs-8');
          estadoClasses.forEach((cls) => badge.classList.add(cls));
          badge.innerText = estado;
          cont.appendChild(badge);
        }

        wrapper.appendChild(accentBar);
        wrapper.appendChild(cont);

        return { domNodes: [wrapper] };
      }

      const wrapper = document.createElement('div');
      wrapper.classList.add('fc-event-card', 'd-flex', 'align-items-start', 'gap-2', 'position-relative');
      if (esMes) wrapper.classList.add('fc-event-card--month');

      const accentBar = document.createElement('span');
      accentBar.classList.add('fc-event-accent');
      accentBar.style.backgroundColor = accentColor;

      const container = document.createElement('div');
      container.classList.add('d-flex', 'flex-column', 'align-items-start', 'position-relative', 'flex-grow-1');
      if (esMes) container.classList.add('fc-event-card__body--month');

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

      wrapper.appendChild(accentBar);
      wrapper.appendChild(container);

      return { domNodes: [wrapper] };
    },
  });

    calendar.render();
  calendarInitState.initialized = true;

  const scheduleCalendarResize = () => {
    try {
      calendar.updateSize();
    } catch (error) {
      console.warn('No se pudo actualizar el tamaño del calendario', error);
    }
  };

  let resizeTimeoutId;
  const queueCalendarResize = () => {
    window.clearTimeout(resizeTimeoutId);
    resizeTimeoutId = window.setTimeout(() => {
      scheduleCalendarResize();
    }, 150);
  };

  scheduleCalendarResize();
  window.setTimeout(scheduleCalendarResize, 250);
  window.addEventListener('orientationchange', queueCalendarResize);
  window.addEventListener('resize', queueCalendarResize);

  if (typeof ResizeObserver !== 'undefined') {
    const resizeObserver = new ResizeObserver(() => {
      queueCalendarResize();
    });
    resizeObserver.observe(calendarEl);
  }

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

    if (servicioSelect) {
      servicioSelect.value = '';
    }
    if (typeSelect) {
      typeSelect.value = 'Reserva';
      refreshCancelButtonTextForType('Reserva');
    }
    if (clienteInput) {
      clienteInput.value = '';
    }
    switchFields('Reserva');
  });

  modalEl.addEventListener('show.bs.modal', updateCancelButtonVisibility);
  modalEl.addEventListener('shown.bs.modal', updateCancelButtonVisibility);

  if (eventIdInput) {
    eventIdInput.addEventListener('input', updateCancelButtonVisibility);
    eventIdInput.addEventListener('change', updateCancelButtonVisibility);
  }
};

// ==== SOLO UNA VEZ: config del calendario desde eventos globales ====

window.addEventListener('alures:calendar-config-ready', (event) => {
  if (event?.detail && typeof event.detail === 'object') {
    window.CalendarConfig = { ...window.CalendarConfig, ...event.detail };
  }
  initializeCalendar();
});

if (typeof window !== 'undefined') {
  window.bootstrapCalendar = (config) => {
    if (config && typeof config === 'object') {
      window.CalendarConfig = config;
    }
    initializeCalendar();
  };
}

// ==== UI extra: modal de pago y boot general ====

const bootReservationUi = () => {
  // Aseguramos que el calendario se intente inicializar
  initializeCalendar();

  const modalPago = document.getElementById('modalPagarFactura');
  if (modalPago) {
    let triggerButton = null;

    modalPago.addEventListener('show.bs.modal', (event) => {
      triggerButton = event.relatedTarget || null;
    });

    const actualizarTotales = () => {
      if (!triggerButton) {
        return;
      }

      const ordenId = triggerButton.getAttribute('data-cuenta');
      if (!ordenId) {
        return;
      }

      fetch(`/orden/${ordenId}/totales`)
        .then((res) => res.json())
        .then((data) => {
          const totalFactura = document.querySelector('#cardTotalFactura');
          if (totalFactura) {
            totalFactura.textContent = data.totalVentas.toLocaleString('es-CO', {
              style: 'currency',
              currency: 'COP',
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
};

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', bootReservationUi, { once: true });
} else {
  bootReservationUi();
}
