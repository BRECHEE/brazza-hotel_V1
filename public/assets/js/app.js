/* =============================================================================
   BRAZZA HOTEL — interface behaviour
   Progressive enhancement only. Every page remains usable without JavaScript.
   ========================================================================== */
(() => {
    "use strict";

    document.documentElement.classList.add("js");

    const reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

    /* Page loader ------------------------------------------------------------ */
    const siteLoader = document.querySelector("[data-site-loader]");
    const loaderStartedAt = performance.now();
    let loaderHidden = false;

    const hideSiteLoader = () => {
        if (loaderHidden || !siteLoader) return;
        loaderHidden = true;
        document.documentElement.classList.remove("is-loading");
        siteLoader.classList.add("is-hidden");
        siteLoader.setAttribute("aria-hidden", "true");
        window.setTimeout(() => siteLoader.remove(), 700);
    };

    const finishSiteLoader = () => {
        if (!siteLoader) {
            document.documentElement.classList.remove("is-loading");
            return;
        }
        const elapsed = performance.now() - loaderStartedAt;
        const delay = reducedMotion ? 0 : Math.max(0, 850 - elapsed);
        window.setTimeout(hideSiteLoader, delay);
    };

    if (document.readyState === "complete") {
        finishSiteLoader();
    } else {
        window.addEventListener("load", finishSiteLoader, { once: true });
    }
    window.setTimeout(hideSiteLoader, 3500);

    /* Theme ------------------------------------------------------------------ */
    const root = document.documentElement;
    const themeButtons = document.querySelectorAll("[data-theme-toggle]");
    const themeColor = document.querySelector("[data-theme-color]");

    const currentTheme = () => (root.dataset.theme === "light" ? "light" : "dark");

    const applyTheme = (theme, persist = true) => {
        const isLight = theme === "light";
        root.dataset.theme = theme;
        root.style.colorScheme = theme;

        if (themeColor) {
            themeColor.setAttribute("content", isLight ? "#f5efe5" : "#101010");
        }

        themeButtons.forEach((button) => {
            button.setAttribute("aria-pressed", isLight ? "true" : "false");
            button.setAttribute("aria-label", isLight ? "Activer le mode sombre" : "Activer le mode clair");
            button.setAttribute("title", isLight ? "Activer le mode sombre" : "Activer le mode clair");
        });

        if (persist) {
            try {
                localStorage.setItem("brazza-theme", theme);
            } catch (error) {
                /* Ignore unavailable storage. */
            }
        }
    };

    applyTheme(currentTheme(), false);

    themeButtons.forEach((button) => {
        button.addEventListener("click", () => {
            applyTheme(currentTheme() === "light" ? "dark" : "light");
        });
    });

    window.addEventListener("storage", (event) => {
        if (event.key === "brazza-theme" && (event.newValue === "light" || event.newValue === "dark")) {
            applyTheme(event.newValue, false);
        }
    });

    /* Header ----------------------------------------------------------------- */
    const header = document.querySelector("[data-header]");
    let lastScroll = 0;

    const syncHeader = () => {
        if (!header) return;
        const y = window.scrollY || 0;
        header.classList.toggle("is-scrolled", y > 24);
        lastScroll = y;
    };
    syncHeader();
    window.addEventListener("scroll", syncHeader, { passive: true });

    /* Mobile navigation ------------------------------------------------------ */
    const menu = document.querySelector("[data-mobile-menu]");
    const toggle = document.querySelector("[data-menu-toggle]");
    const closeButton = document.querySelector("[data-menu-close]");
    let previousFocus = null;

    const openMenu = () => {
        if (!menu || !toggle) return;
        previousFocus = document.activeElement;
        menu.removeAttribute("inert");
        menu.setAttribute("aria-hidden", "false");
        menu.classList.add("is-open");
        toggle.setAttribute("aria-expanded", "true");
        header?.classList.add("is-menu-open");
        document.body.classList.add("menu-open");
        window.setTimeout(() => closeButton?.focus(), 120);
    };

    const closeMenu = (restoreFocus = true) => {
        if (!menu || !toggle) return;
        menu.setAttribute("aria-hidden", "true");
        menu.classList.remove("is-open");
        toggle.setAttribute("aria-expanded", "false");
        header?.classList.remove("is-menu-open");
        document.body.classList.remove("menu-open");
        window.setTimeout(() => menu.setAttribute("inert", ""), 420);
        if (restoreFocus && previousFocus instanceof HTMLElement) {
            previousFocus.focus();
        }
    };

    toggle?.addEventListener("click", () => {
        const isOpen = toggle.getAttribute("aria-expanded") === "true";
        isOpen ? closeMenu() : openMenu();
    });
    closeButton?.addEventListener("click", () => closeMenu());

    menu?.querySelectorAll("a").forEach((link) => {
        link.addEventListener("click", () => closeMenu(false));
    });

    document.addEventListener("keydown", (event) => {
        if (event.key === "Escape" && menu?.classList.contains("is-open")) {
            closeMenu();
            return;
        }

        if (event.key !== "Tab" || !menu?.classList.contains("is-open")) return;
        const focusable = menu.querySelectorAll(
            'a[href], button:not([disabled]), input, select, textarea, [tabindex]:not([tabindex="-1"])'
        );
        if (!focusable.length) return;
        const first = focusable[0];
        const last = focusable[focusable.length - 1];

        if (event.shiftKey && document.activeElement === first) {
            event.preventDefault();
            last.focus();
        } else if (!event.shiftKey && document.activeElement === last) {
            event.preventDefault();
            first.focus();
        }
    });

    window.addEventListener("resize", () => {
        if (window.innerWidth > 980 && menu?.classList.contains("is-open")) {
            closeMenu(false);
        }
    });

    /* Scroll reveals --------------------------------------------------------- */
    const revealElements = document.querySelectorAll("[data-reveal]");
    if (revealElements.length) {
        if ("IntersectionObserver" in window && !reducedMotion) {
            const observer = new IntersectionObserver(
                (entries) => {
                    entries.forEach((entry) => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add("is-revealed");
                            observer.unobserve(entry.target);
                        }
                    });
                },
                { rootMargin: "0px 0px -8% 0px", threshold: 0.12 }
            );
            revealElements.forEach((element) => observer.observe(element));

            // Safety net: content must never remain invisible if an observer
            // callback is missed (very tall layout, fast jump, unusual browser).
            window.setTimeout(() => {
                revealElements.forEach((element) => {
                    element.classList.add("is-revealed");
                    observer.unobserve(element);
                });
            }, 2600);
        } else {
            revealElements.forEach((element) => element.classList.add("is-revealed"));
        }
    }

    /* Date fields ------------------------------------------------------------ */
    document.querySelectorAll('input[name="check_in"]').forEach((checkIn) => {
        const form = checkIn.closest("form");
        const checkOut = form?.querySelector('input[name="check_out"]');
        if (!checkOut) return;

        const isoPlusOne = (value) => {
            const date = new Date(`${value}T12:00:00`);
            if (Number.isNaN(date.getTime())) return "";
            date.setDate(date.getDate() + 1);
            return date.toISOString().slice(0, 10);
        };

        const sync = () => {
            if (!checkIn.value) return;
            const minimum = isoPlusOne(checkIn.value);
            checkOut.min = minimum;
            if (!checkOut.value || checkOut.value <= checkIn.value) {
                checkOut.value = minimum;
            }
        };

        checkIn.addEventListener("change", sync);
        sync();
    });

    /* GSAP hero -------------------------------------------------------------- */
    const runHeroAnimation = () => {
        if (reducedMotion || typeof window.gsap === "undefined") return;
        const hero = document.querySelector(".hero");
        if (!hero) return;

        const timeline = window.gsap.timeline({ defaults: { ease: "power3.out" } });
        timeline
            .from(".hero__media picture", { scale: 1.07, duration: 2.2, ease: "power2.out" }, 0)
            .from(".hero__kicker", { y: 18, opacity: 0, duration: 0.9 }, 0.15)
            .from(".hero h1", { y: 42, opacity: 0, duration: 1.15 }, 0.3)
            .from(".hero__lead", { y: 24, opacity: 0, duration: 0.9 }, 0.55)
            .from(".hero__actions", { y: 18, opacity: 0, duration: 0.8 }, 0.72)
            .from(".hero__booking-wrap", { y: 30, opacity: 0, duration: 0.9 }, 0.86);

        if (typeof window.ScrollTrigger !== "undefined") {
            window.gsap.registerPlugin(window.ScrollTrigger);
            window.gsap.to(".hero__image", {
                yPercent: 7,
                ease: "none",
                scrollTrigger: {
                    trigger: ".hero",
                    start: "top top",
                    end: "bottom top",
                    scrub: true,
                },
            });
        }
    };

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", runHeroAnimation, { once: true });
    } else {
        runHeroAnimation();
    }

    /* Lightbox --------------------------------------------------------------- */
    const dialog = document.getElementById("lightbox");
    const dialogImage = document.getElementById("lightbox-image");
    const dialogCaption = document.getElementById("lightbox-caption");
    const dialogCount = document.getElementById("lightbox-count");
    const dialogClose = document.getElementById("lightbox-close");
    let lightboxTrigger = null;

    if (dialog && dialogImage && typeof dialog.showModal === "function") {
        document.querySelectorAll("[data-lightbox]").forEach((trigger) => {
            trigger.addEventListener("click", (event) => {
                event.preventDefault();
                lightboxTrigger = trigger;
                dialogImage.src = trigger.getAttribute("data-full") || trigger.getAttribute("href") || "";
                dialogImage.alt = trigger.getAttribute("data-alt") || "";
                if (dialogCaption) {
                    dialogCaption.textContent = trigger.getAttribute("data-caption") || "";
                }
                if (dialogCount) {
                    const total = document.querySelectorAll("[data-lightbox]").length;
                    const index = Array.from(document.querySelectorAll("[data-lightbox]")).indexOf(trigger) + 1;
                    dialogCount.textContent = `${String(index).padStart(2, "0")} / ${String(total).padStart(2, "0")}`;
                }
                dialog.showModal();
                document.body.classList.add("lightbox-open");
                dialogClose?.focus();
            });
        });

        const closeLightbox = () => {
            dialog.close();
            document.body.classList.remove("lightbox-open");
            if (lightboxTrigger instanceof HTMLElement) lightboxTrigger.focus();
        };

        dialogClose?.addEventListener("click", closeLightbox);

        dialog.addEventListener("click", (event) => {
            if (event.target === dialog) closeLightbox();
        });

        dialog.addEventListener("cancel", (event) => {
            event.preventDefault();
            closeLightbox();
        });

        dialog.addEventListener("close", () => {
            document.body.classList.remove("lightbox-open");
            dialogImage.removeAttribute("src");
        });
    }

    /* External links: safe new tab hints ------------------------------------- */
    document.querySelectorAll('a[target="_blank"]').forEach((link) => {
        if (!link.rel.includes("noopener")) {
            link.rel = `${link.rel} noopener noreferrer`.trim();
        }
    });
})();
