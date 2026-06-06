document.addEventListener("DOMContentLoaded", function () {
    const navbarToggler = document.querySelector(".navbar-toggler");
    let menuOpenIcon = document.getElementById("menu-open-icon");
    let menuCloseIcon = document.getElementById("menu-close-icon");

    if (!navbarToggler || !menuOpenIcon || !menuCloseIcon) {
        return;
    }

    navbarToggler.addEventListener("click", function () {
        if (menuOpenIcon.classList.contains("d-none")) {
            menuOpenIcon.classList.remove("d-none");
            menuCloseIcon.classList.add("d-none");
        } else {
            menuOpenIcon.classList.add("d-none");
            menuCloseIcon.classList.remove("d-none");
        }
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const lightbox = document.getElementById("lightbox");
    if (lightbox) {
        document.body.classList.add("no-scroll");
    }
});

document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll("form[data-prevent-double-submit]").forEach(function (form) {
        form.addEventListener("submit", function (e) {
            var btn = form.querySelector('button[type="submit"]');
            if (!btn) return;
            if (btn.disabled) {
                e.preventDefault();
                return;
            }
            btn.disabled = true;
            btn.dataset.originalHtml = btn.innerHTML;
            var label = btn.dataset.loadingText || 'Wird gespeichert…';
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>' + label;
        });
    });
});

// Theme: dark mode is disabled for this project. Always force Bootstrap light theme.
document.addEventListener("DOMContentLoaded", function () {
    try { localStorage.removeItem("theme"); } catch (e) {}

    document.documentElement.setAttribute("data-bs-theme", "light");

    // Some pages/scripts might set it later; enforce again after they run.
    window.setTimeout(function () {
        document.documentElement.setAttribute("data-bs-theme", "light");
    }, 0);
});
