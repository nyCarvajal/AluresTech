import BaseTomSelect from 'tom-select/dist/js/tom-select.complete.js';

const noop = () => {};

const METHODS_TO_STUB = [
  'addItem',
  'blur',
  'clear',
  'clearCache',
  'close',
  'destroy',
  'disable',
  'enable',
  'focus',
  'getValue',
  'load',
  'lock',
  'off',
  'on',
  'open',
  'refreshItems',
  'refreshOptions',
  'removeItem',
  'setTextboxValue',
  'setValue',
  'sync',
  'unlock',
  'updateOption',
];

const isDomLikeElement = (value) => {
  if (!value || typeof value !== 'object') {
    return false;
  }

  if (typeof value.nodeType === 'number') {
    return true;
  }

  if (typeof value.tagName === 'string') {
    return true;
  }

  return false;
};

const createFallbackInstance = (target, newTarget) => {
  const prototype = (target && target.prototype) || Object.prototype;
  const fallback = Object.create(prototype);

  Object.defineProperty(fallback, 'constructor', {
    value: newTarget || target || function GuardedTomSelectStub() {},
    configurable: true,
    writable: true,
  });

  METHODS_TO_STUB.forEach((method) => {
    if (typeof fallback[method] !== 'function') {
      fallback[method] = noop;
    }
  });

  return fallback;
};

let warnedInvalidElement = false;
let warnedConstructorFailure = false;

const instantiateSafely = (target, args, newTarget) => {
  const [element] = Array.isArray(args) ? args : [];

  if (!isDomLikeElement(element)) {
    if (!warnedInvalidElement) {
      warnedInvalidElement = true;
      console.warn(
        'TomSelect recibió un elemento inválido y se omitió la inicialización.',
        element,
      );
    }
    return createFallbackInstance(target, newTarget);
  }

  try {
    return Reflect.construct(target, args, newTarget);
  } catch (error) {
    if (!warnedConstructorFailure) {
      warnedConstructorFailure = true;
      console.error('TomSelect lanzó una excepción al inicializar.', error);
    }
    return createFallbackInstance(target, newTarget);
  }
};

const buildGuardedConstructor = () => {
  if (typeof Proxy !== 'function') {
    function GuardedTomSelect(...args) {
      const newTarget = new.target || GuardedTomSelect;
      return instantiateSafely(BaseTomSelect, args, newTarget);
    }

    GuardedTomSelect.prototype = BaseTomSelect.prototype;
    Object.setPrototypeOf(GuardedTomSelect, BaseTomSelect);
    return GuardedTomSelect;
  }

  const GuardedTomSelect = new Proxy(BaseTomSelect, {
    construct(target, args, newTarget) {
      return instantiateSafely(target, args, newTarget);
    },
  });

  Object.setPrototypeOf(GuardedTomSelect, BaseTomSelect);
  GuardedTomSelect.prototype = BaseTomSelect.prototype;
  return GuardedTomSelect;
};

const SafeTomSelect = buildGuardedConstructor();

if (typeof window !== 'undefined') {
  window.TomSelect = SafeTomSelect;
}

export const OriginalTomSelect = BaseTomSelect;
export default SafeTomSelect;
export * from 'tom-select/dist/js/tom-select.complete.js';
