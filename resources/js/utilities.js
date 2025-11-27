// Utilities: skip-link focus, toast, dll.

// ===== Fokus manajemen ringan =====
// Pastikan anchor skip-link memindahkan fokus ke <main>
function initSkipLinkFocus() {
  const main = document.getElementById("main-content");
  if (!main) return;

  // Bila user mendarat via #main-content, tempatkan fokus
  if (location.hash === "#main-content") {
    main.focus({ preventScroll: true });
  }

  window.addEventListener("hashchange", () => {
    if (location.hash === "#main-content") {
      main.focus({ preventScroll: true });
    }
  });
}

// ===== Toast util =====
function ensureToastHost() {
  let host = document.getElementById("toast-host");
  if (!host) {
    host = document.createElement("div");
    host.id = "toast-host";
    host.style.position = "fixed";
    host.style.right = "1rem";
    host.style.bottom = "1rem";
    host.style.zIndex = "1000";
    document.body.appendChild(host);
  }
  return host;
}

export function toast(message, opts = {}) {
  const host = ensureToastHost();
  const el = document.createElement("div");
  el.className = "toast";
  el.setAttribute("role", "status");
  el.setAttribute("aria-live", "polite");
  el.textContent = message;
  host.appendChild(el);

  const ttl = typeof opts.ttl === "number" ? opts.ttl : 3000;
  window.setTimeout(() => el.remove(), ttl);
}

// Init semua utilities global
export function initUtilities() {
  initSkipLinkFocus();
}
