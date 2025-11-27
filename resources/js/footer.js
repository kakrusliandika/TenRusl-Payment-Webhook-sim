// resources/js/footer.js
// Behavior footer: tracking klik link/footer untuk analytics, dsb.

function trackFooterClick(link, type) {
  try {
    window.dispatchEvent(
      new CustomEvent("track", {
        detail: {
          name: "footer_link_click",
          type,
          href: link.href,
          label:
            link.textContent?.trim() || link.getAttribute("aria-label") || null,
        },
      })
    );
  } catch {
    // swallow
  }
}

export function initFooter() {
  // Semua link di baris text
  const textLinks = document.querySelectorAll(".footer-links a");
  textLinks.forEach((link) => {
    link.addEventListener("click", () => trackFooterClick(link, "text"));
  });

  // Semua icon social
  const socialLinks = document.querySelectorAll(".footer-social a");
  socialLinks.forEach((link) => {
    link.addEventListener("click", () => trackFooterClick(link, "social"));
  });
}
