// resources/js/lib/safe-tom-select.js

import TomSelect from '@alures/tom-select-source';

function resolveElement(target) {
  if (target instanceof Element) return target;
  if (typeof target === 'string') return document.querySelector(target);
  return null;
}

export default class SafeTomSelect extends TomSelect {
  constructor(target, settings = {}) {
    const el = resolveElement(target);

    if (!el) {
      console.warn('[SafeTomSelect] No se encontró el elemento para', target);

      // Truco: creamos un <select> "dummy" invisible para que
      // las internas de TomSelect no trabajen con null
      const dummy = document.createElement('select');
      dummy.style.display = 'none';
      document.body.appendChild(dummy);

      super(dummy, settings);
      return;
    }

    super(el, settings);
  }
}
