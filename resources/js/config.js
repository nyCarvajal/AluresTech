// public/js/config.js

(function() {
  // 1) Valores por defecto
  var defaults = {
    theme: "light",
    topbar: { color: "light" },
    menu:   { size: "default", color: "light" }
  };

  // 2) Lee el HTML <html> para atributos data-*
  var htmlEl = document.documentElement;

  // 3) Arranca config con una copia profunda de defaults
  var config = JSON.parse(JSON.stringify(defaults));

  // 4) Sobrescribe con atributos HTML si están presentes
  config.theme            = htmlEl.getAttribute("data-bs-theme")      || defaults.theme;
  config.topbar.color     = htmlEl.getAttribute("data-topbar-color")  || defaults.topbar.color;
  config.menu.color       = htmlEl.getAttribute("data-sidebar-color") || defaults.menu.color;
  config.menu.size        = htmlEl.getAttribute("data-sidebar-size")  || defaults.menu.size;

  // 5) Guarda snapshot como defaultConfig
  window.defaultConfig = JSON.parse(JSON.stringify(config));

  // 6) Si hay config previa en sessionStorage, la funde sobre el config actual
  var stored = null;
  try {
    stored = sessionStorage.getItem("__DARKONE_CONFIG__");
  } catch (err) {
    console.warn(
      "sessionStorage no está disponible; se omite la configuración persistida.",
      err
    );
  }
  if (stored) {
    try {
      var saved = JSON.parse(stored);
      // Fusiona saved sobre config manteniendo estructuras necesarias
      config = Object.assign({}, JSON.parse(JSON.stringify(defaults)), config, saved);
      // Asegura que topbar y menu existen
      config.topbar = config.topbar || { color: defaults.topbar.color };
      config.menu   = config.menu   || { size: defaults.menu.size, color: defaults.menu.color };
    } catch (err) {
      console.error("Error parsing __DARKONE_CONFIG__", err);
    }
  }

  // 7) Asegura que existe config.color.primary (ThemeLayout lo necesita)
  try {
    var css = getComputedStyle(document.documentElement);
    config.color = config.color || {};
    config.color.primary = css.getPropertyValue('--bs-primary').trim() || '#0d6efd';
  } catch (err) {
    config.color = config.color || {};
    config.color.primary = '#0d6efd';
    console.error("Error setting config.color.primary", err);
  }

  // 8) Asigna la configuración final
  window.config = config;

  // 9) Aplica atributos de vuelta al <html> para reflejar cambios
  htmlEl.setAttribute("data-bs-theme",      config.theme);
  htmlEl.setAttribute("data-topbar-color",  config.topbar.color);
  htmlEl.setAttribute("data-sidebar-color", config.menu.color);
  if (window.innerWidth <= 1140) {
    htmlEl.setAttribute("data-sidebar-size", "hidden");
  } else {
    htmlEl.setAttribute("data-sidebar-size", config.menu.size);
  }
})();
