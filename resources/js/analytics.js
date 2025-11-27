// Analytics placeholder — muat secara deferred (non-blocking, aman).
// Prinsip: tunda skrip pihak ketiga agar tidak mengganggu critical path.
function init() {
  // Di sini kamu bisa menambahkan integrasi analytics sungguhan,
  // misal gtag.js, Plausible, dsb., menggunakan <script defer> atau dynamic import.
  // Contoh event bus sederhana:
  window.addEventListener("track", (e) => {
    const { name, ...detail } = e.detail || {};
    // console.log("[analytics]", name, detail);
  });
}

// Muat saat idle (fallback ke setTimeout)
try {
  if ("requestIdleCallback" in window) {
    window.requestIdleCallback(() => init(), { timeout: 1500 });
  } else {
    setTimeout(() => init(), 0);
  }
} catch {
  setTimeout(() => init(), 0);
}
