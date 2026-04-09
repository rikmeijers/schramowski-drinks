document.addEventListener('DOMContentLoaded', () => {
  if (typeof window.flatpickr !== 'function') return;

  // Apply flatpickr to all date inputs site-wide.
  // Reservation pages use a specialized script that must keep control for
  // start_date/end_date (minDate + disabled ranges via trailer).
  const dateInputs = document.querySelectorAll('input[type="date"]');

  dateInputs.forEach((input) => {
    if (input.name === 'start_date' || input.name === 'end_date') return;
    // Keep native datepicker inside the print dropdown.
    if (input.closest('.dropdown-menu')) return;

    window.flatpickr(input, {
      dateFormat: 'Y-m-d',
      allowInput: true,
      disableMobile: true,
    });

    // Make it consistent with other flatpickr instances
    input.type = 'text';
  });
});
