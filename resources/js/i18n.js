// resources/js/i18n.js
// Lightweight front-end i18n helper.
// Server-side tetap pakai Laravel lang() / __().

const DICT = {
  en: {
    toggle_menu: "Toggle menu",
    toggle_theme: "Toggle theme",
  },
  id: {
    toggle_menu: "Buka/tutup menu",
    toggle_theme: "Ganti tema",
  },
  ja: {
    toggle_menu: "メニューを開閉",
    toggle_theme: "テーマを変更",
  },
};

let CURRENT = (document.documentElement.lang || "en").slice(0, 2) || "en";

export function getCurrentLocale() {
  return CURRENT;
}

export function setLocale(lc) {
  CURRENT = lc in DICT ? lc : "en";
  document.dispatchEvent(
    new CustomEvent("i18n:change", { detail: { locale: CURRENT } })
  );
}

export function t(key) {
  const dict = DICT[CURRENT] || DICT.en || {};
  if (key in dict) return dict[key];
  if (key in (DICT.en || {})) return DICT.en[key];
  return key;
}

// Contoh penggunaan di JS:
// import { t } from "./i18n.js";
// button.setAttribute("aria-label", t("toggle_menu"));
