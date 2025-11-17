const noop = () => {};

const warnedObjectTargets = new WeakSet();
const warnedPrimitiveTargets = new Set();

const shouldWarnForTarget = (target) => {
  if (target && (typeof target === 'object' || typeof target === 'function')) {
    if (warnedObjectTargets.has(target)) {
      return false;
    }
    warnedObjectTargets.add(target);
    return true;
  }

  const key = typeof target === 'string' ? target : String(target);
  if (warnedPrimitiveTargets.has(key)) {
    return false;
  }
  warnedPrimitiveTargets.add(key);
  return true;
};

const warnInvalidTarget = (target, reason, error) => {
  if (!shouldWarnForTarget(target || reason || 'invalid-target')) {
    return;
  }
  const message =
    reason || 'TomSelectStub: se intentó inicializar sin un elemento válido. Se omitirá.';
  if (error) {
    console.warn(message, target, error);
  } else {
    console.warn(message, target);
  }
};

const resolveNode = (target) => {
  if (!target) {
    warnInvalidTarget(target, 'TomSelectStub: se recibió un objetivo vacío.');
    return null;
  }
  if (typeof Element !== 'undefined' && target instanceof Element) return target;
  if (typeof target === 'string') {
    try {
      const node = document.querySelector(target);
      if (!node) {
        warnInvalidTarget(
          target,
          'TomSelectStub: no se encontró ningún elemento para el selector proporcionado.',
        );
      }
      return node;
    } catch (error) {
      warnInvalidTarget(
        target,
        'TomSelect stub no pudo buscar el selector proporcionado.',
        error,
      );
      return null;
    }
  }
  if (Array.isArray(target)) {
    return resolveNode(target[0]);
  }
  if (typeof target === 'object' && 'length' in target) {
    return resolveNode(target[0]);
  }
  warnInvalidTarget(target, 'TomSelectStub: no se pudo resolver el objetivo proporcionado.');
  return null;
};

class TomSelectStub {
  constructor(target, options = {}) {
    this.__isTomSelectStub = true;
    this.input = resolveNode(target);
    this.control = this.input;
    this.dropdown = null;
    this.settings = options;

    if (!this.input) {
      warnInvalidTarget(
        target,
        'TomSelectStub: se intentó inicializar sin un elemento válido. Se usará un stub.',
      );
    }
  }

  destroy() {
    noop();
  }
  sync() { noop(); }
  on() { return noop; }
  off() { return noop; }
  addOption() { noop(); }
  addItem() { noop(); }
  clear() { noop(); }
  clearOptions() { noop(); }
  setValue() { noop(); }
  setTextboxValue() { noop(); }
  refreshOptions() { noop(); }
  focus() { noop(); }
  blur() { noop(); }
  open() { noop(); }
  close() { noop(); }
  load(callback) {
    if (typeof callback === 'function') {
      callback([]);
    }
  }
}

if (typeof window !== 'undefined') {
  const existing = window.TomSelect;
  if (!existing || existing.__isTomSelectStub) {
    window.TomSelect = TomSelectStub;
  }
}

export default TomSelectStub;
