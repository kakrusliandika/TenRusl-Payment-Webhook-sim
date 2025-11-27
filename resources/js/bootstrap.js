// Baseline HTTP & small init (lean & safe)
import axios from "axios";

// Expose (konvensi Laravel)
window.axios = axios;

// AJAX marker
window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

// CSRF dari <meta name="csrf-token">
(() => {
  const el = document.querySelector('meta[name="csrf-token"]');
  if (el?.content) {
    window.axios.defaults.headers.common["X-CSRF-TOKEN"] = el.content;
  }
})();

// Tanpa cross-site credentials secara default
window.axios.defaults.withCredentials = false;

// Timeout wajar
window.axios.defaults.timeout = 10000; // 10s

// Error interceptor minimal (tidak bocorkan sensitif)
const isProd =
  typeof import.meta !== "undefined" && import.meta.env && import.meta.env.PROD;

window.axios.interceptors.response.use(
  (r) => r,
  (err) => {
    if (!isProd) {
      const cfg = err?.config || {};
      console.warn(
        "[HTTP]",
        err?.response?.status || "ERR",
        cfg.method?.toUpperCase?.(),
        cfg.url || "(unknown)"
      );
    }
    return Promise.reject(err);
  }
);
