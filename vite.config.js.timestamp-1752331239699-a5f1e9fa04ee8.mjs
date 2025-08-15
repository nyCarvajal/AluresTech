// vite.config.js
import { defineConfig } from "file:///C:/xampp/htdocs/smashtechlv/node_modules/vite/dist/node/index.js";
import laravel from "file:///C:/xampp/htdocs/smashtechlv/node_modules/laravel-vite-plugin/dist/index.js";
var vite_config_default = defineConfig({
  plugins: [
    laravel({
      input: [
        //css
        "resources/scss/icons.scss",
        "resources/scss/style.scss",
        "node_modules/quill/dist/quill.snow.css",
        "node_modules/quill/dist/quill.bubble.css",
        "node_modules/flatpickr/dist/flatpickr.min.css",
        "node_modules/flatpickr/dist/themes/dark.css",
        "node_modules/gridjs/dist/theme/mermaid.css",
        "node_modules/flatpickr/dist/themes/dark.css",
        "node_modules/gridjs/dist/theme/mermaid.min.css",
        //js
        "resources/js/app.js",
        "resources/js/config.js",
        "resources/js/pages/dashboard.js",
        "resources/js/pages/chart.js",
        "resources/js/pages/form-quilljs.js",
        "resources/js/pages/form-fileupload.js",
        "resources/js/pages/form-flatepicker.js",
        "resources/js/pages/table-gridjs.js",
        "resources/js/pages/maps-google.js",
        "resources/js/pages/maps-vector.js",
        "resources/js/pages/maps-spain.js",
        "resources/js/pages/maps-russia.js",
        "resources/js/pages/maps-iraq.js",
        "resources/js/pages/maps-canada.js"
      ],
      refresh: true
    })
  ]
});
export {
  vite_config_default as default
};
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsidml0ZS5jb25maWcuanMiXSwKICAic291cmNlc0NvbnRlbnQiOiBbImNvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9kaXJuYW1lID0gXCJDOlxcXFx4YW1wcFxcXFxodGRvY3NcXFxcc21hc2h0ZWNobHZcIjtjb25zdCBfX3ZpdGVfaW5qZWN0ZWRfb3JpZ2luYWxfZmlsZW5hbWUgPSBcIkM6XFxcXHhhbXBwXFxcXGh0ZG9jc1xcXFxzbWFzaHRlY2hsdlxcXFx2aXRlLmNvbmZpZy5qc1wiO2NvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9pbXBvcnRfbWV0YV91cmwgPSBcImZpbGU6Ly8vQzoveGFtcHAvaHRkb2NzL3NtYXNodGVjaGx2L3ZpdGUuY29uZmlnLmpzXCI7aW1wb3J0IHsgZGVmaW5lQ29uZmlnIH0gZnJvbSAndml0ZSc7XG5pbXBvcnQgbGFyYXZlbCBmcm9tICdsYXJhdmVsLXZpdGUtcGx1Z2luJztcblxuZXhwb3J0IGRlZmF1bHQgZGVmaW5lQ29uZmlnKHtcblx0XG4gICAgcGx1Z2luczogW1xuICAgICAgICBsYXJhdmVsKHtcbiAgICAgICAgICAgIGlucHV0OiBbXG4gICAgICAgICAgICAgICAgLy9jc3NcbiAgICAgICAgICAgICAgICBcInJlc291cmNlcy9zY3NzL2ljb25zLnNjc3NcIixcbiAgICAgICAgICAgICAgICBcInJlc291cmNlcy9zY3NzL3N0eWxlLnNjc3NcIixcbiAgICAgICAgICAgICAgICBcIm5vZGVfbW9kdWxlcy9xdWlsbC9kaXN0L3F1aWxsLnNub3cuY3NzXCIsXG4gICAgICAgICAgICAgICAgXCJub2RlX21vZHVsZXMvcXVpbGwvZGlzdC9xdWlsbC5idWJibGUuY3NzXCIsXG4gICAgICAgICAgICAgICAgXCJub2RlX21vZHVsZXMvZmxhdHBpY2tyL2Rpc3QvZmxhdHBpY2tyLm1pbi5jc3NcIixcbiAgICAgICAgICAgICAgICBcIm5vZGVfbW9kdWxlcy9mbGF0cGlja3IvZGlzdC90aGVtZXMvZGFyay5jc3NcIixcbiAgICAgICAgICAgICAgICBcIm5vZGVfbW9kdWxlcy9ncmlkanMvZGlzdC90aGVtZS9tZXJtYWlkLmNzc1wiLFxuICAgICAgICAgICAgICAgIFwibm9kZV9tb2R1bGVzL2ZsYXRwaWNrci9kaXN0L3RoZW1lcy9kYXJrLmNzc1wiLFxuICAgICAgICAgICAgICAgIFwibm9kZV9tb2R1bGVzL2dyaWRqcy9kaXN0L3RoZW1lL21lcm1haWQubWluLmNzc1wiLFxuXG5cbiAgICAgICAgICAgICAgICAvL2pzXG4gICAgICAgICAgICAgICAgXCJyZXNvdXJjZXMvanMvYXBwLmpzXCIsXG4gICAgICAgICAgICAgICAgXCJyZXNvdXJjZXMvanMvY29uZmlnLmpzXCIsXG4gICAgICAgICAgICAgICAgXCJyZXNvdXJjZXMvanMvcGFnZXMvZGFzaGJvYXJkLmpzXCIsXG4gICAgICAgICAgICAgICAgXCJyZXNvdXJjZXMvanMvcGFnZXMvY2hhcnQuanNcIixcbiAgICAgICAgICAgICAgICBcInJlc291cmNlcy9qcy9wYWdlcy9mb3JtLXF1aWxsanMuanNcIixcbiAgICAgICAgICAgICAgICBcInJlc291cmNlcy9qcy9wYWdlcy9mb3JtLWZpbGV1cGxvYWQuanNcIixcbiAgICAgICAgICAgICAgICBcInJlc291cmNlcy9qcy9wYWdlcy9mb3JtLWZsYXRlcGlja2VyLmpzXCIsXG4gICAgICAgICAgICAgICAgXCJyZXNvdXJjZXMvanMvcGFnZXMvdGFibGUtZ3JpZGpzLmpzXCIsXG4gICAgICAgICAgICAgICAgXCJyZXNvdXJjZXMvanMvcGFnZXMvbWFwcy1nb29nbGUuanNcIixcbiAgICAgICAgICAgICAgICBcInJlc291cmNlcy9qcy9wYWdlcy9tYXBzLXZlY3Rvci5qc1wiLFxuICAgICAgICAgICAgICAgIFwicmVzb3VyY2VzL2pzL3BhZ2VzL21hcHMtc3BhaW4uanNcIixcbiAgICAgICAgICAgICAgICBcInJlc291cmNlcy9qcy9wYWdlcy9tYXBzLXJ1c3NpYS5qc1wiLFxuICAgICAgICAgICAgICAgIFwicmVzb3VyY2VzL2pzL3BhZ2VzL21hcHMtaXJhcS5qc1wiLFxuICAgICAgICAgICAgICAgIFwicmVzb3VyY2VzL2pzL3BhZ2VzL21hcHMtY2FuYWRhLmpzXCJcblxuICAgICAgICAgICAgXSxcbiAgICAgICAgICAgIHJlZnJlc2g6IHRydWUsXG4gICAgICAgIH0pLFxuICAgIF0sXG59KTtcbiJdLAogICJtYXBwaW5ncyI6ICI7QUFBMlEsU0FBUyxvQkFBb0I7QUFDeFMsT0FBTyxhQUFhO0FBRXBCLElBQU8sc0JBQVEsYUFBYTtBQUFBLEVBRXhCLFNBQVM7QUFBQSxJQUNMLFFBQVE7QUFBQSxNQUNKLE9BQU87QUFBQTtBQUFBLFFBRUg7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBO0FBQUEsUUFJQTtBQUFBLFFBQ0E7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxNQUVKO0FBQUEsTUFDQSxTQUFTO0FBQUEsSUFDYixDQUFDO0FBQUEsRUFDTDtBQUNKLENBQUM7IiwKICAibmFtZXMiOiBbXQp9Cg==
