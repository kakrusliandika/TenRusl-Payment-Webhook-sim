// Fungsional halaman khusus (Providers search dengan debounce)

function debounce(fn, wait = 200) {
  let t;
  return (...args) => {
    clearTimeout(t);
    t = setTimeout(() => fn(...args), wait);
  };
}

export function initProvidersSearch() {
  const input = document.getElementById("providers-search");
  if (!input) return;

  const tiles = Array.from(document.querySelectorAll(".provider-tile"));
  const run = () => {
    const q = input.value.toLowerCase().trim();
    tiles.forEach((el) => {
      const hit = el.dataset.name?.toLowerCase().includes(q);
      el.style.display = hit ? "" : "none";
    });
  };

  input.addEventListener("input", debounce(run, 120));
}
