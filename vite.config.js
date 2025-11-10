// vite.config.js
import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
  plugins: [
    laravel({
      input: [
        "resources/css/app.css",
        "resources/css/main.css",
        "resources/js/app.js",
        "resources/js/main.js",
      ],
      refresh: true,
    }),
    tailwindcss(),
  ],
});
