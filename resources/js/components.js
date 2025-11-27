// Component-level JS: language menu, dsb.

// ===== Language menu dropdown (layout/nav.blade.php) =====
function initLangMenu() {
  const btn = document.getElementById("lang-menu-button");
  const menu = document.getElementById("lang-menu");
  if (!btn || !menu) return;

  const open = () => {
    btn.setAttribute("aria-expanded", "true");
    menu.hidden = false;
  };

  const close = () => {
    btn.setAttribute("aria-expanded", "false");
    menu.hidden = true;
  };

  const toggle = () => {
    const expanded = btn.getAttribute("aria-expanded") === "true";
    expanded ? close() : open();
  };

  btn.addEventListener("click", (e) => {
    e.stopPropagation();
    toggle();
  });

  // Tutup ketika klik di luar
  document.addEventListener("click", (e) => {
    if (!menu.contains(e.target) && !btn.contains(e.target)) {
      close();
    }
  });

  // Tutup dengan Escape
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
      close();
      btn.focus();
    }
  });
}

export function initComponents() {
  initLangMenu();
}
