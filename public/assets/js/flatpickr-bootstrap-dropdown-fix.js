document.addEventListener('DOMContentLoaded', () => {
  if (typeof window.flatpickr !== 'function') return;

  // When flatpickr is used inside a Bootstrap dropdown, clicking the calendar
  // (e.g. next/prev month arrows) can close the dropdown because Bootstrap
  // closes on any inner click.
  //
  // Fix: stop click events originating from the flatpickr calendar container,
  // but ONLY when the triggering input lives inside a bootstrap dropdown.
  document.addEventListener(
    'click',
    (e) => {
      const el = e.target;
      if (!(el instanceof Element)) return;

      const calendar = el.closest('.flatpickr-calendar');
      if (!calendar) return;

      // Only apply when flatpickr is opened by an input inside an OPEN bootstrap dropdown.
      // Flatpickr stores the active instance on the input as `_flatpickr`.
      const activeInput = document.querySelector('input._flatpickr-active');
      const instance = activeInput && activeInput._flatpickr ? activeInput._flatpickr : null;
      const isInOpenDropdown = !!(activeInput && activeInput.closest('.dropdown-menu.show'));

      // If we can't confidently detect a dropdown context, do nothing.
      if (!instance || !isInOpenDropdown) return;

      // Keep the dropdown open while interacting with the calendar.
      e.stopPropagation();
    },
    true // capture so we intercept before Bootstrap
  );
});
