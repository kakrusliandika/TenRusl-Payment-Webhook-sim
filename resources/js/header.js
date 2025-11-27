// resources/js/header.js
// Header behavior: mobile primary nav + language dropdown

function initHeaderNav() {
  const header = document.querySelector(".app-header");
  if (!header) return;

  const nav = header.querySelector("#primary-nav");
  const navToggle = header.querySelector(".nav-toggle");

  if (nav && navToggle) {
    const openNav = () => {
      nav.classList.add("open");
      navToggle.setAttribute("aria-expanded", "true");
    };

    const closeNav = () => {
      nav.classList.remove("open");
      navToggle.setAttribute("aria-expanded", "false");
    };

    const toggleNav = () => {
      if (nav.classList.contains("open")) closeNav();
      else openNav();
    };

    navToggle.addEventListener("click", (e) => {
      e.stopPropagation();
      toggleNav();
    });

    document.addEventListener("click", (e) => {
      if (!header.contains(e.target)) closeNav();
    });

    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape") closeNav();
    });
  }

  // Language dropdown
  const langButton = header.querySelector("#lang-menu-button");
  const langMenu = header.querySelector("#lang-menu");

  if (langButton && langMenu) {
    const openLang = () => {
      langMenu.hidden = false;
      langButton.setAttribute("aria-expanded", "true");
    };

    const closeLang = () => {
      langMenu.hidden = true;
      langButton.setAttribute("aria-expanded", "false");
    };

    const toggleLang = () => {
      if (langMenu.hidden) openLang();
      else closeLang();
    };

    langButton.addEventListener("click", (e) => {
      e.stopPropagation();
      toggleLang();
    });

    document.addEventListener("click", (e) => {
      if (!header.contains(e.target)) closeLang();
    });

    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape") closeLang();
    });
  }
}

export function initHeader() {
  initHeaderNav();
}
