import BaseTomSelect from '@alures/tom-select-source';

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
  if (!value) {
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

const resolveElement = (raw) => {
  if (isDomLikeElement(raw)) {
    return raw;
  }

  if (typeof raw === 'string') {
    if (typeof document === 'undefined' || !raw.trim()) {
      return null;
    }
    return document.querySelector(raw.trim());
  }

  if (!raw || typeof raw !== 'object') {
    return null;
  }

  if (Array.isArray(raw)) {
    for (const candidate of raw) {
      const element = resolveElement(candidate);
      if (element) {
        return element;
      }
    }
    return null;
  }

  if (typeof raw.jquery === 'string' && typeof raw.length === 'number') {
    return resolveElement(raw[0]);
  }

  if (typeof raw.length === 'number' && raw.length > 0) {
    return resolveElement(raw[0]);
  }

  if (typeof raw.el === 'object' && raw.el) {
    return resolveElement(raw.el);
  }

  return null;
};

const createFallbackInstance = (target) => {
  const prototype = (target && target.prototype) || Object.prototype;
  const fallback = Object.create(prototype);

  Object.defineProperty(fallback, 'constructor', {
    value: target || function GuardedTomSelectStub() {},
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

class GuardedTomSelect extends BaseTomSelect {
  constructor(elementLike, ...rest) {
    const element = resolveElement(elementLike);

    if (!isDomLikeElement(element)) {
      if (!warnedInvalidElement) {
        warnedInvalidElement = true;
        console.warn(
          'TomSelect recibió un objetivo inválido y se omitió la inicialización.',
          elementLike,
        );
      }
      return createFallbackInstance(GuardedTomSelect);
    }

    try {
      super(element, ...rest);
    } catch (error) {
      if (!warnedConstructorFailure) {
        warnedConstructorFailure = true;
        console.error('TomSelect lanzó una excepción al inicializar.', error);
      }
      return createFallbackInstance(GuardedTomSelect);
    }
  }
}

const SafeTomSelect = GuardedTomSelect;

if (typeof window !== 'undefined') {
  window.TomSelect = SafeTomSelect;
}

export const OriginalTomSelect = BaseTomSelect;
export default SafeTomSelect;
export * from '@alures/tom-select-source';
