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
    reason || 'TomSelect recibió un objetivo inválido y se omitió la inicialización.';

  if (error) {
    console.warn(message, target, error);
  } else {
    console.warn(message, target);
  }
};

const resolveElement = (raw) => {
  if (isDomLikeElement(raw)) {
    return raw;
  }

  if (typeof raw === 'string') {
    if (typeof document === 'undefined' || !raw.trim()) {
      return null;
    }

    const selector = raw.trim();
    try {
      const element = document.querySelector(selector);
      if (!element) {
        warnInvalidTarget(
          selector,
          'TomSelect omitido: no se encontró ningún elemento para el selector proporcionado.',
        );
      }
      return element;
    } catch (error) {
      warnInvalidTarget(
        selector,
        'TomSelect omitido: el selector proporcionado no es válido.',
        error,
      );
      return null;
    }
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

    warnInvalidTarget(
      raw,
      'TomSelect omitido: no se encontró ningún elemento en el arreglo proporcionado.',
    );
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

  warnInvalidTarget(raw, 'TomSelect omitido: no se pudo resolver el objetivo proporcionado.');
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

let warnedConstructorFailure = false;

class GuardedTomSelect extends BaseTomSelect {
  constructor(elementLike, ...rest) {
    const element = resolveElement(elementLike);

    if (!isDomLikeElement(element)) {
      warnInvalidTarget(
        elementLike,
        'TomSelect recibió un objetivo inválido y se omitió la inicialización.',
      );
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
