/* Brazza Hotel — administration interactions */
(() => {
    "use strict";

    document.documentElement.classList.add("js");

    const shell = document.querySelector("[data-admin-shell]");
    const toggle = document.querySelector("[data-admin-toggle]");
    const overlay = document.querySelector("[data-admin-overlay]");

    const closeSidebar = () => {
        shell?.classList.remove("is-sidebar-open");
        toggle?.setAttribute("aria-expanded", "false");
        document.body.classList.remove("admin-nav-open");
    };

    const openSidebar = () => {
        shell?.classList.add("is-sidebar-open");
        toggle?.setAttribute("aria-expanded", "true");
        document.body.classList.add("admin-nav-open");
    };

    toggle?.addEventListener("click", () => {
        shell?.classList.contains("is-sidebar-open") ? closeSidebar() : openSidebar();
    });
    overlay?.addEventListener("click", closeSidebar);
    window.addEventListener("resize", () => {
        if (window.innerWidth > 1024) closeSidebar();
    });
    document.addEventListener("keydown", (event) => {
        if (event.key === "Escape") closeSidebar();
    });

    document.querySelectorAll("[data-password-toggle]").forEach((button) => {
        button.addEventListener("click", () => {
            const target = document.getElementById(button.dataset.target || "");
            if (!target) return;
            const visible = target.getAttribute("type") === "text";
            target.setAttribute("type", visible ? "password" : "text");
            button.setAttribute("aria-label", visible ? "Afficher le mot de passe" : "Masquer le mot de passe");
            const icon = button.querySelector("i");
            if (icon) {
                icon.classList.toggle("fa-eye", visible);
                icon.classList.toggle("fa-eye-slash", !visible);
            }
        });
    });

    document.querySelectorAll("form[data-confirm]").forEach((form) => {
        form.addEventListener("submit", (event) => {
            const message = form.getAttribute("data-confirm") || "Confirmer cette action ?";
            if (!window.confirm(message)) {
                event.preventDefault();
            }
        });
    });

    document.querySelectorAll("[data-image-input]").forEach((input) => {
        input.addEventListener("change", () => {
            const output = document.querySelector(input.getAttribute("data-image-input"));
            if (!output) return;
            const files = Array.from(input.files || []);
            output.textContent = files.length
                ? files.map((file) => `${file.name} (${Math.round(file.size / 1024)} Ko)`).join(" · ")
                : "Aucun fichier sélectionné";
        });
    });
})();
