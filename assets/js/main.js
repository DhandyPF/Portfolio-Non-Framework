const menuToggle = document.querySelector(".menu-toggle");
const primaryNav = document.querySelector("#primary-nav");
const siteHeader = document.querySelector(".site-header");
const certificateTrack = document.querySelector(".certificate-grid");
const profileCard = document.querySelector(".profile-card");
const themeToggle = document.querySelector(".theme-toggle");
const colorSchemeMeta = document.querySelector('meta[name="color-scheme"]');
const reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)");

const systemPrefersDark = () => window.matchMedia("(prefers-color-scheme: dark)").matches;

const getRenderedTheme = () => {
  const storedTheme = colorSchemeMeta?.content;
  return storedTheme === "light" || storedTheme === "dark"
    ? storedTheme
    : systemPrefersDark()
      ? "dark"
      : "light";
};

const updateThemeToggle = () => {
  if (!themeToggle) {
    return;
  }

  const isDark = getRenderedTheme() === "dark";
  themeToggle.textContent = isDark ? "Mode terang" : "Mode gelap";
  themeToggle.setAttribute(
    "aria-label",
    isDark ? "Aktifkan mode terang" : "Aktifkan mode gelap",
  );
};

const applyTheme = (theme) => {
  if (!colorSchemeMeta) {
    return;
  }

  if (theme === "light" || theme === "dark") {
    document.documentElement.dataset.theme = theme;
    colorSchemeMeta.content = theme;
  } else {
    delete document.documentElement.dataset.theme;
    colorSchemeMeta.content = "light dark";
  }

  updateThemeToggle();
};

themeToggle?.addEventListener("click", () => {
  const currentTheme = getRenderedTheme();
  const nextTheme = currentTheme === "dark" ? "light" : "dark";
  const systemTheme = systemPrefersDark() ? "dark" : "light";

  try {
    if (nextTheme === systemTheme) {
      localStorage.removeItem("color-scheme");
      applyTheme(null);
    } else {
      localStorage.setItem("color-scheme", nextTheme);
      applyTheme(nextTheme);
    }
  } catch (error) {
    applyTheme(nextTheme);
  }
});

window.addEventListener("storage", (event) => {
  if (event.key === "color-scheme") {
    applyTheme(event.newValue === "light" || event.newValue === "dark" ? event.newValue : null);
  }
});

updateThemeToggle();

// Buka dan tutup navigasi pada layar kecil.
menuToggle?.addEventListener("click", () => {
  const isOpen = primaryNav.classList.toggle("is-open");
  menuToggle.setAttribute("aria-expanded", String(isOpen));
});

primaryNav?.querySelectorAll("a").forEach((link) => {
  link.addEventListener("click", () => {
    primaryNav.classList.remove("is-open");
    menuToggle?.setAttribute("aria-expanded", "false");
  });
});

document.addEventListener("keydown", (event) => {
  if (event.key === "Escape" && primaryNav?.classList.contains("is-open")) {
    primaryNav.classList.remove("is-open");
    menuToggle?.setAttribute("aria-expanded", "false");
    menuToggle?.focus();
  }
});

document.addEventListener("pointerdown", (event) => {
  if (
    primaryNav?.classList.contains("is-open")
    && !siteHeader?.contains(event.target)
  ) {
    primaryNav.classList.remove("is-open");
    menuToggle?.setAttribute("aria-expanded", "false");
  }
});

if ("IntersectionObserver" in window && primaryNav) {
  const navLinks = Array.from(primaryNav.querySelectorAll("a[href^='#']"));
  const sections = navLinks
    .map((link) => document.querySelector(link.getAttribute("href")))
    .filter(Boolean);

  const sectionObserver = new IntersectionObserver(
    (entries) => {
      const visibleEntry = entries
        .filter((entry) => entry.isIntersecting)
        .sort((a, b) => b.intersectionRatio - a.intersectionRatio)[0];

      if (!visibleEntry) {
        return;
      }

      navLinks.forEach((link) => {
        if (link.getAttribute("href") === `#${visibleEntry.target.id}`) {
          link.setAttribute("aria-current", "page");
        } else {
          link.removeAttribute("aria-current");
        }
      });
    },
    { rootMargin: "-20% 0px -60%", threshold: [0, 0.1, 0.5] },
  );

  sections.forEach((section) => sectionObserver.observe(section));
}

const updateNavbarLayout = () => {
  if (!siteHeader || !primaryNav) {
    return;
  }

  siteHeader.classList.add("is-wide");
  const navShell = siteHeader.querySelector(".nav-shell");
  const fits = navShell
    ? navShell.scrollWidth <= navShell.clientWidth
    : false;

  siteHeader.classList.toggle("is-wide", fits);
  if (!fits) {
    primaryNav.classList.remove("is-open");
    menuToggle?.setAttribute("aria-expanded", "false");
  }
};

updateNavbarLayout();
window.addEventListener("resize", updateNavbarLayout, { passive: true });

if ("ResizeObserver" in window && siteHeader) {
  const navbarObserver = new ResizeObserver(updateNavbarLayout);
  const navShell = siteHeader.querySelector(".nav-shell");
  if (navShell) {
    navbarObserver.observe(navShell);
  }
  if (primaryNav) {
    navbarObserver.observe(primaryNav);
  }
}

// Tambahkan bayangan pada navbar setelah halaman mulai digulir.
const updateHeaderState = () => {
  siteHeader?.classList.toggle("is-scrolled", window.scrollY > 12);
};

updateHeaderState();
window.addEventListener("scroll", updateHeaderState, { passive: true });

// Putar ulang tirai foto saat kartu profil diklik.
profileCard?.addEventListener("click", () => {
  if (reduceMotion.matches) {
    return;
  }

  profileCard.classList.remove("is-curtain-active");
  window.requestAnimationFrame(() => {
    profileCard.classList.add("is-curtain-active");
  });
  window.setTimeout(() => {
    profileCard.classList.remove("is-curtain-active");
  }, 1300);
});

// Geser sertifikat perlahan ke arah kanan visual, lalu ulangi dari ujung awal.
if (certificateTrack && !reduceMotion.matches) {
  let animationFrame = 0;
  let lastTime = 0;
  let paused = false;
  let hasStarted = false;
  const speed = 0.035;

  const moveCertificates = (time) => {
    if (!lastTime) {
      lastTime = time;
    }

    if (!paused && certificateTrack.scrollWidth > certificateTrack.clientWidth) {
      if (!hasStarted) {
        certificateTrack.scrollLeft = certificateTrack.scrollWidth - certificateTrack.clientWidth;
        hasStarted = true;
      }
      const elapsed = Math.min(time - lastTime, 50);
      certificateTrack.scrollLeft -= elapsed * speed;
      if (certificateTrack.scrollLeft <= 0) {
        certificateTrack.scrollLeft = certificateTrack.scrollWidth - certificateTrack.clientWidth;
      }
    }

    lastTime = time;
    animationFrame = window.requestAnimationFrame(moveCertificates);
  };

  const pauseCertificates = () => {
    paused = true;
  };

  const resumeCertificates = () => {
    paused = false;
  };

  certificateTrack.addEventListener("pointerenter", pauseCertificates);
  certificateTrack.addEventListener("pointerleave", resumeCertificates);
  certificateTrack.addEventListener("focusin", pauseCertificates);
  certificateTrack.addEventListener("focusout", resumeCertificates);
  certificateTrack.addEventListener("touchstart", pauseCertificates, { passive: true });
  certificateTrack.addEventListener("touchend", resumeCertificates, { passive: true });
  animationFrame = window.requestAnimationFrame(moveCertificates);

  window.addEventListener("pagehide", () => {
    window.cancelAnimationFrame(animationFrame);
  }, { once: true });
}

// Tampilkan elemen dari arah yang sesuai dengan arah scroll.
let lastScrollY = window.scrollY;
let scrollDirection = "down";

window.addEventListener("scroll", () => {
  const currentScrollY = window.scrollY;
  if (currentScrollY > lastScrollY) {
    scrollDirection = "down";
  } else if (currentScrollY < lastScrollY) {
    scrollDirection = "up";
  }
  lastScrollY = currentScrollY;
}, { passive: true });

if ("IntersectionObserver" in window && !reduceMotion.matches) {
  const revealObserver = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.remove("reveal-from-down", "reveal-from-up");
          entry.target.classList.add(
            scrollDirection === "up" ? "reveal-from-up" : "reveal-from-down",
          );
          window.requestAnimationFrame(() => {
            entry.target.classList.add("is-visible");
          });
        } else {
          entry.target.classList.remove("is-visible");
        }
      });
    },
    { threshold: 0.12 },
  );

  document
    .querySelectorAll(".reveal")
    .forEach((element) => revealObserver.observe(element));
} else {
  document.querySelectorAll(".reveal").forEach((element) => {
    element.classList.add("is-visible");
  });
}
