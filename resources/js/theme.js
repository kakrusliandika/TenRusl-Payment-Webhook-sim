// Toggle tema: 'light' | 'dark'
// Hormati prefers-color-scheme untuk default pertama, simpan di localStorage
const STORAGE_KEY = "theme"; // nilai disimpan: 'light' | 'dark'

export function getSystemPrefersDark() {
  return (
    window.matchMedia &&
    window.matchMedia("(prefers-color-scheme: dark)").matches
  );
}

// Ambil theme tersimpan; kalau belum ada, ikut sistem (auto, tapi tidak disimpan)
export function getStoredTheme() {
  const stored = localStorage.getItem(STORAGE_KEY);

  if (stored === "light" || stored === "dark") {
    return stored;
  }

  // default pertama kali: ikut sistem
  return getSystemPrefersDark() ? "dark" : "light";
}

export function applyTheme(theme = getStoredTheme()) {
  const html = document.documentElement;
  const isDark = theme === "dark";

  // Terapkan class "dark" untuk token CSS
  html.classList.toggle("dark", isDark);

  // (opsional) expose info mode ke data-theme
  html.dataset.theme = theme;

  // Update ARIA tombol toggle (kalau ada)
  const btn = document.getElementById("theme-toggle");
  if (btn) {
    btn.setAttribute("aria-pressed", String(isDark));
    btn.dataset.theme = theme;
  }
}

export function setTheme(theme) {
  // Guard: pastikan cuma 'light' atau 'dark'
  if (theme !== "light" && theme !== "dark") {
    theme = getSystemPrefersDark() ? "dark" : "light";
  }

  localStorage.setItem(STORAGE_KEY, theme);
  applyTheme(theme);
}

// Toggle sederhana: light <-> dark (TANPA state 'auto' lagi)
export function toggleTheme() {
  const current = getStoredTheme();
  const next = current === "dark" ? "light" : "dark";

  setTheme(next);
  return next;
}

export function initThemeToggle(onChange = setTheme) {
  // Inisialisasi awal berdasarkan localStorage / sistem
  applyTheme();

  // Kalau user belum pernah pilih apa pun (belum ada STORAGE_KEY),
  // baru kita dengar perubahan sistem.
  if (!localStorage.getItem(STORAGE_KEY) && window.matchMedia) {
    const mq = window.matchMedia("(prefers-color-scheme: dark)");
    mq.addEventListener?.("change", () => {
      // getStoredTheme() di sini akan ikut sistem lagi
      applyTheme();
    });
  }

  // Kaitkan ke tombol
  const btn = document.getElementById("theme-toggle");
  if (btn) {
    btn.addEventListener("click", () => {
      const next = toggleTheme();
      onChange(next);
    });
  }
}
