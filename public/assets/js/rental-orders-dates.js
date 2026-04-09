document.addEventListener('DOMContentLoaded', () => {
  if (typeof window.flatpickr !== 'function') return;

  const rental = document.querySelector('input[name="rental_date"]');
  const ret = document.querySelector('input[name="return_date"]');
  if (!rental || !ret) return;

  // Detect edit mode: the form has data-edit-mode attribute
  const isEditMode = !!document.querySelector('[data-edit-mode]');

  const today = new Date();
  today.setHours(0, 0, 0, 0);

  function addDays(date, days) {
    const d = new Date(date);
    d.setDate(d.getDate() + days);
    d.setHours(0, 0, 0, 0);
    return d;
  }

  if (isEditMode) {
    // Edit mode: rental_date has no minDate restriction (all dates selectable).
    // Return_date must be after the selected rental_date.
    const rentalPicker = window.flatpickr(rental, {
      dateFormat: 'Y-m-d',
      allowInput: true,
      disableMobile: true,
      onChange: () => syncEditReturnConstraints(),
      onValueUpdate: () => syncEditReturnConstraints(),
    });

    const returnPicker = window.flatpickr(ret, {
      dateFormat: 'Y-m-d',
      allowInput: true,
      disableMobile: true,
    });

    rental.type = 'text';
    ret.type = 'text';

    function syncEditReturnConstraints() {
      const selectedRental = rentalPicker.selectedDates?.[0];
      if (selectedRental) {
        const minReturn = addDays(selectedRental, 1);
        returnPicker.set('minDate', minReturn);

        // If current return_date is before the new minimum, clear it
        const current = returnPicker.selectedDates?.[0];
        if (current && current < minReturn) {
          returnPicker.clear();
        }
      } else {
        returnPicker.set('minDate', null);
      }
    }

    // Initial sync so return_date respects the existing rental_date value
    syncEditReturnConstraints();

    return;
  }

  // Create mode: enforce minDate and return date depends on rental date

  // Initialize rental_date
  const rentalPicker = window.flatpickr(rental, {
    dateFormat: 'Y-m-d',
    allowInput: true,
    disableMobile: true,
    minDate: today,
    onChange: () => syncReturnConstraints(),
    onValueUpdate: () => syncReturnConstraints(),
  });

  // Initialize return_date; start disabled until rental_date is set
  const returnPicker = window.flatpickr(ret, {
    dateFormat: 'Y-m-d',
    allowInput: true,
    disableMobile: true,
    clickOpens: false,
  });

  // Make consistent with other flatpickr instances
  rental.type = 'text';
  ret.type = 'text';

  function disableReturnCompletely() {
    ret.value = '';
    returnPicker.set('minDate', null);
    returnPicker.set('maxDate', null);
    returnPicker.set('clickOpens', false);
    ret.setAttribute('disabled', 'disabled');
    ret.classList.add('disabled');
  }

  function enableReturn(minDate) {
    ret.removeAttribute('disabled');
    ret.classList.remove('disabled');
    returnPicker.set('minDate', minDate);
    returnPicker.set('clickOpens', true);

    // If current return_date is invalid, clear it
    const current = returnPicker.selectedDates?.[0];
    if (current && current < minDate) {
      returnPicker.clear();
    }
  }

  function syncReturnConstraints() {
    const selectedRental = rentalPicker.selectedDates?.[0];

    if (!selectedRental) {
      disableReturnCompletely();
      return;
    }

    const minReturn = addDays(selectedRental, 1);
    enableReturn(minReturn);
  }

  // Initial sync for create screen
  syncReturnConstraints();
});

