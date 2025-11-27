// Global entry point: bootstrap + utilities for all pages.
import "./bootstrap.js";
import "./i18n.js";

import { initThemeToggle } from "./theme.js";
import { initMain } from "./main.js";
import { initProvidersSearch } from "./pages.js";
import { initHeader } from "./header.js";

// Hilangkan .no-js (CSS fallback)
document.documentElement.classList.remove("no-js");

// Inisialisasi saat DOM siap
document.addEventListener("DOMContentLoaded", () => {
  // Toggle tema bila ada tombol #theme-toggle
  initThemeToggle();
  initHeader();

  // Init header, utilities, components, footer (dirangkai di main.js)
  initMain();

  // Init halaman providers (search/filter)
  initProvidersSearch();

  // Beri sinyal siap
  document.dispatchEvent(new CustomEvent("app:ready"));
});

// Defer analytics ke waktu idle (non-blocking)
const loadAnalytics = () => import("./analytics.js").catch(() => {});
if ("requestIdleCallback" in window) {
  requestIdleCallback(loadAnalytics, { timeout: 2000 });
} else {
  setTimeout(loadAnalytics, 0);
}
