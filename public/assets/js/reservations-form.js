document.addEventListener('DOMContentLoaded', () => {
  const trailerSelect = document.querySelector('select[name="trailer_id"]');
  const startInput = document.querySelector('input[name="start_date"]');
  const endInput = document.querySelector('input[name="end_date"]');
  const form = trailerSelect?.closest('form');
  const reservationId = form?.dataset?.reservationId ? Number(form.dataset.reservationId) : null;
  const isEdit = Number.isFinite(reservationId) && reservationId > 0;

  if (!trailerSelect || !startInput || !endInput) return;

  const hasFlatpickr = typeof window.flatpickr === 'function';
  const debug = window.__RESERVATION_DEBUG__ === true;

  function logDebug(...args) {
    if (!debug) return;
    // eslint-disable-next-line no-console
    console.log('[reservations-form]', ...args);
  }

  const today = new Date();
  today.setHours(0, 0, 0, 0);

  function toYmd(d) {
    const yyyy = d.getFullYear();
    const mm = String(d.getMonth() + 1).padStart(2, '0');
    const dd = String(d.getDate()).padStart(2, '0');
    return `${yyyy}-${mm}-${dd}`;
  }

  function parseYmd(s) {
    if (!s) return null;
    const [y, m, d] = s.split('-').map(Number);
    if (!y || !m || !d) return null;
    const dt = new Date(y, m - 1, d);
    dt.setHours(0, 0, 0, 0);
    return dt;
  }

  function overlaps(aStart, aEndInclusive, bStart, bEndInclusive) {
    return aStart <= bEndInclusive && bStart <= aEndInclusive;
  }

  let blocked = [];

  function startOfDay(d) {
    const x = new Date(d);
    x.setHours(0, 0, 0, 0);
    return x;
  }

  function addDays(date, days) {
    const d = new Date(date);
    d.setDate(d.getDate() + days);
    d.setHours(0, 0, 0, 0);
    return d;
  }

  function getBufferedRange(r) {
    // Treat the incoming range as the already-buffered blocked window.
    const from = parseYmd(r.start);
    const to = parseYmd(r.end);
    if (!from || !to) return null;

    return { from, to };
  }

  function expandBlockedToDates() {
    // We want *inclusive* blocking.
    // Buffer rule: 1 day BEFORE start and 1 day AFTER end are also blocked.
    const out = [];
    for (const r of blocked) {
      const buffered = getBufferedRange(r);
      if (!buffered) continue;

      for (let d = startOfDay(buffered.from); d <= buffered.to; d = addDays(d, 1)) {
        out.push(new Date(d));
      }
    }
    return out;
  }

  function nextBlockedDateOnOrAfter(date) {
    const target = startOfDay(date);

    let best = null;
    for (const r of blocked) {
      const buffered = getBufferedRange(r);
      if (!buffered) continue;

      // If the target is before/inside this buffered blocked window,
      // the earliest blocked date we can hit is max(target, buffered.from).
      if (target <= buffered.to) {
        const candidate = target < buffered.from ? buffered.from : target;
        if (!best || candidate < best) best = candidate;
      }
    }
    return best;
  }

  function updateEndMaxByFirstBlockedAfterStart() {
    if (!endPicker) return;

    // reset default max
    endPicker.set('maxDate', null);

    const s = parseYmd(startInput.value);
    if (!s) return;

    // Find the first blocked date on/after the start day.
    // IMPORTANT: nextBlockedDateOnOrAfter() already includes the +1 day buffer
    // on the *end* of existing reservations. If we start searching from (start+1)
    // we effectively add an extra day.
    const candidate = nextBlockedDateOnOrAfter(s);
    if (candidate) {
      endPicker.set('maxDate', addDays(candidate, -1));
    }
  }

  function getDisableRanges() {
    // Use explicit disabled *dates* so inclusive appointment blocks are correct.
    // (Flatpickr range objects can be tricky with timezones; dates are safest.)
    return expandBlockedToDates();
  }

  function rangeOverlapsBlocked(startYmd, endYmd) {
    const s = parseYmd(startYmd);
    const e = parseYmd(endYmd);
    if (!s || !e) return false;

    for (const r of blocked) {
      const buffered = getBufferedRange(r);
      if (!buffered) continue;

      if (overlaps(s, e, buffered.from, buffered.to)) return true;
    }
    return false;
  }

  function clearCustomValidity() {
    startInput.setCustomValidity('');
    endInput.setCustomValidity('');
  }

  function applyValidity() {
    clearCustomValidity();

    const s = startInput.value;
    const e = endInput.value;

    // Keep end >= start
    if (s && e && e < s) {
      endInput.value = '';
    }

    if (s && e && rangeOverlapsBlocked(s, e)) {
      endInput.setCustomValidity('In deze periode is de trailer al gereserveerd. Kies andere datums.');
    }
  }

  // Fallback (no flatpickr): at least block past via native min
  const todayStr = toYmd(today);
  if (!isEdit) {
    startInput.setAttribute('min', todayStr);
    endInput.setAttribute('min', todayStr);
  } else {
    startInput.removeAttribute('min');
    endInput.removeAttribute('min');
  }

  let startPicker = null;
  let endPicker = null;

  function syncPickerDisable() {
    if (!hasFlatpickr) return;
    const disableRanges = getDisableRanges();

    if (startPicker) startPicker.set('disable', disableRanges);
    if (endPicker) endPicker.set('disable', disableRanges);

    // Force redraw (sometimes needed when updating disable dynamically)
    if (startPicker) startPicker.redraw();
    if (endPicker) endPicker.redraw();

    updateEndMaxByFirstBlockedAfterStart();

    logDebug('syncPickerDisable', { blockedRanges: blocked.length, disabledDates: disableRanges.length });
  }

  function initFlatpickr() {
    if (!hasFlatpickr) return;

    // Make inputs text so flatpickr controls formatting consistently
    startInput.type = 'text';
    endInput.type = 'text';

    const initialStart = parseYmd(startInput.value);
    const minDateForForm = isEdit ? null : today;

    const common = {
      dateFormat: 'Y-m-d',
      allowInput: true,
      minDate: minDateForForm,
      disableMobile: true,
    };

    startPicker = window.flatpickr(startInput, {
      ...common,
      onChange: function (selectedDates, dateStr) {
        // Ensure end can't be before start
        if (endPicker) {
          endPicker.set('minDate', selectedDates[0] || minDateForForm);

          // Limit end date selection to the first blocked date after start
          updateEndMaxByFirstBlockedAfterStart();

          // If end is now invalid, clear it
          if (endInput.value && dateStr && endInput.value < dateStr) {
            endPicker.clear();
          }

          // If end is now beyond maxDate (because a block starts), clear it
          if (endPicker.config.maxDate && endInput.value) {
            const max = startOfDay(endPicker.config.maxDate);
            const endValue = parseYmd(endInput.value);
            if (endValue && endValue > max) {
              endPicker.clear();
            }
          }
        }
        applyValidity();
      },
    });

    endPicker = window.flatpickr(endInput, {
      ...common,
      onChange: function () {
        applyValidity();
      },
    });

    // Initial link
    if (startInput.value && endPicker) {
      endPicker.set('minDate', initialStart || minDateForForm);
      updateEndMaxByFirstBlockedAfterStart();
    }

    syncPickerDisable();
  }

  function clearSelectedDates() {
    // Clear both the DOM input values and flatpickr internal state
    startInput.value = '';
    endInput.value = '';
    clearCustomValidity();

    if (startPicker) startPicker.clear();
    if (endPicker) endPicker.clear();

    // Reset any end constraints
    if (endPicker) {
      endPicker.set('minDate', isEdit ? null : today);
      endPicker.set('maxDate', null);
    }
  }

  async function loadBlockedDates(trailerId) {
    blocked = [];
    clearCustomValidity();

    if (!trailerId) {
      syncPickerDisable();
      applyValidity();
      return;
    }

    const template = form?.dataset?.blockedDatesUrlTemplate;
    const rawUrl = template
      ? template.replace('__TRAILER_ID__', encodeURIComponent(trailerId))
      : `/trailers/${encodeURIComponent(trailerId)}/blocked-dates`;

    const url = reservationId ? `${rawUrl}${rawUrl.includes('?') ? '&' : '?'}exclude=${encodeURIComponent(String(reservationId))}` : rawUrl;

    logDebug('fetch', url);

    try {
      const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      const data = await res.json();

      blocked = Array.isArray(data?.blocked) ? data.blocked : [];

      // No change to server data, but log that we apply a 1-day buffer after each reservation
      logDebug('blocked loaded (raw)', blocked);

      logDebug('blocked loaded', blocked);

      syncPickerDisable();
      applyValidity();
    } catch (e) {
      // Endpoint failure => don't block UI; server-side validation still protects.
      blocked = [];
      logDebug('blocked fetch failed', e);
      syncPickerDisable();
      applyValidity();
    }
  }

  // Sync selected radio option into hidden boolean inputs so backend receives expected fields.
  function syncServiceSelection() {
    const sel = form.querySelector('input[name="service_option"]:checked');
    const val = sel ? sel.value : null;
    const hSelber = document.getElementById('hidden_service_selber_beladen');
    const hLehr = document.getElementById('hidden_service_lehr');
    const hPaket = document.getElementById('hidden_service_paket');
    if (hSelber) hSelber.value = val === 'selber_beladen' ? '1' : '0';
    if (hLehr) hLehr.value = val === 'lehr' ? '1' : '0';
    if (hPaket) hPaket.value = val === 'paket' ? '1' : '0';
  }

  // Keep radios and hidden inputs in sync interactively
  form?.addEventListener('change', (e) => {
    if (e.target && e.target.name === 'service_option') {
      syncServiceSelection();
    }
  });

  // Ensure sync right before submit (covers browsers without JS or race conditions)
  form?.addEventListener('submit', () => {
    syncServiceSelection();
  });

  trailerSelect.addEventListener('change', () => {
    if (!isEdit) {
      clearSelectedDates();
    }
    loadBlockedDates(trailerSelect.value);
  });
  startInput.addEventListener('change', applyValidity);
  endInput.addEventListener('change', applyValidity);

  const paymentStatusSelect = document.querySelector('select[name="payment_status"][data-payment-status]');
  const partialPaidWrapper = document.querySelector('[data-partial-paid-wrapper]');
  const partialPaidInput = document.querySelector('input[name="partial_paid_amount"][data-partial-paid-input]');

  function syncPartialPaidUi() {
    if (!paymentStatusSelect || !partialPaidWrapper || !partialPaidInput) return;

    const isPartial = paymentStatusSelect.value === 'partial';
    partialPaidWrapper.style.display = isPartial ? '' : 'none';
    partialPaidInput.required = isPartial;

    if (!isPartial) {
      partialPaidInput.value = '';
      partialPaidInput.setCustomValidity('');
    }
  }

  if (paymentStatusSelect && partialPaidWrapper && partialPaidInput) {
    paymentStatusSelect.addEventListener('change', syncPartialPaidUi);
    syncPartialPaidUi();

    // Also ensure the right state before submit
    form?.addEventListener('submit', syncPartialPaidUi);
  }

  initFlatpickr();
  loadBlockedDates(trailerSelect.value);
  applyValidity();
  // Ensure hidden inputs reflect the initial radio selection on load
  if (typeof syncServiceSelection === 'function') syncServiceSelection();
});
