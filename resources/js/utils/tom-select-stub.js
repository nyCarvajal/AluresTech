// resources/js/utils/tom-select-stub.js

function resolveElement(target) {
  if (target instanceof Element) return target;
  if (typeof target === 'string') return document.querySelector(target);
  return null;
}

class TomSelectStub {
  constructor(target, settings = {}) {
    const el = resolveElement(target);

    if (!el) {
      console.warn('[TomSelectStub] No se encontró el elemento para', target);
      // Importante: NO tocamos el ".tomselect" de nada si el es null
      return;
    }

    // Si ya hay una instancia previa, simplemente la reutilizamos
    if (!el.tomselect) {
      el.tomselect = {
        destroy() {},
        clear() {},
        setValue() {},
        getValue() {
          return el.value;
        },
        addOption() {},
        addItem() {},
        removeItem() {},
        // Si necesitas más métodos de la API, los agregas aquí
      };
    }

    // El constructor de TomSelect suele devolver la instancia que se guarda en el elemento
    return el.tomselect;
  }
}

// Solo registramos el stub si no hay nada todavía
if (typeof window !== 'undefined' && typeof window.TomSelect === 'undefined') {
  window.TomSelect = TomSelectStub;
}

export default TomSelectStub;