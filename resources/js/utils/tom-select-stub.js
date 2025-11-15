const noop = () => {};

const resolveNode = (target) => {
  if (!target) return null;
  if (target instanceof Element) return target;
  if (typeof target === 'string') {
    try {
      return document.querySelector(target);
    } catch (error) {
      console.warn('TomSelect stub no pudo buscar el selector proporcionado:', target, error);
      return null;
    }
  }
  if (Array.isArray(target)) {
    return resolveNode(target[0]);
  }
  if (typeof target === 'object' && 'length' in target) {
    return resolveNode(target[0]);
  }
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
      console.warn('TomSelectStub: se intentó inicializar sin un elemento válido.');
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
