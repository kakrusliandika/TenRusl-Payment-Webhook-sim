// Orchestrator untuk JS global: header, utilities, components, footer
import { initHeader } from "./header.js";
import { initFooter } from "./footer.js";
import { initUtilities, toast as utilitiesToast } from "./utilities.js";
import { initComponents } from "./components.js";

// Re-export toast agar kompatibel bila ada yang import dari './main.js'
export function toast(message, opts = {}) {
  return utilitiesToast(message, opts);
}

// Init global
export function initMain() {
  initHeader();
  initUtilities();
  initComponents();
  initFooter();
}
