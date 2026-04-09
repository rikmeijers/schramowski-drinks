document.addEventListener('DOMContentLoaded', () => {
  const form = document.querySelector('form[data-signature-form]');
  if (!form) return;

  const pad = form.querySelector('[data-signature-pad]');
  const canvas = form.querySelector('canvas[data-signature-canvas]');
  const input = form.querySelector('input[data-signature-data-url]');
  const clearBtn = form.querySelector('[data-signature-clear]');

  if (!pad || !canvas || !input) return;

  const ctx = canvas.getContext('2d');
  const state = {
    drawing: false,
    hasInk: false,
    last: null,
  };

  function resizeCanvas() {
    const rect = pad.getBoundingClientRect();
    const ratio = Math.max(window.devicePixelRatio || 1, 1);

    // Preserve current signature pixels via data URL before resizing (resizing clears canvas)
    const existingDataUrl = (state.hasInk && canvas.width && canvas.height)
      ? canvas.toDataURL('image/png')
      : (input.value && input.value.startsWith('data:image/') ? input.value : '');

    canvas.width = Math.floor(rect.width * ratio);
    canvas.height = Math.floor(rect.height * ratio);
    canvas.style.width = rect.width + 'px';
    canvas.style.height = rect.height + 'px';

    ctx.setTransform(ratio, 0, 0, ratio, 0, 0);
    ctx.lineWidth = 2;
    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';
    ctx.strokeStyle = '#111';

    if (existingDataUrl) {
      const img = new Image();
      img.onload = () => {
        ctx.clearRect(0, 0, rect.width, rect.height);
        ctx.drawImage(img, 0, 0, rect.width, rect.height);
        state.hasInk = true;
        input.value = existingDataUrl;
      };
      img.src = existingDataUrl;
    }
  }

  function getPoint(e) {
    const rect = canvas.getBoundingClientRect();
    const touch = e.touches && e.touches[0];
    const clientX = touch ? touch.clientX : e.clientX;
    const clientY = touch ? touch.clientY : e.clientY;
    return { x: clientX - rect.left, y: clientY - rect.top };
  }

  function start(e) {
    e.preventDefault();
    state.drawing = true;
    state.last = getPoint(e);
  }

  function move(e) {
    if (!state.drawing) return;
    e.preventDefault();

    const p = getPoint(e);
    ctx.beginPath();
    ctx.moveTo(state.last.x, state.last.y);
    ctx.lineTo(p.x, p.y);
    ctx.stroke();

    state.last = p;
    state.hasInk = true;
  }

  function end() {
    if (!state.drawing) return;
    state.drawing = false;
    state.last = null;

    // Persist signature immediately so it survives scroll/resize/navigation before submit
    syncToInput();
  }

  function clear() {
    const rect = canvas.getBoundingClientRect();
    ctx.clearRect(0, 0, rect.width, rect.height);
    state.hasInk = false;
    input.value = '';
  }

  function syncToInput() {
    if (!state.hasInk) {
      input.value = '';
      return;
    }
    // Store as PNG data URL (server stores as file)
    input.value = canvas.toDataURL('image/png');
  }

  canvas.addEventListener('mousedown', start);
  canvas.addEventListener('mousemove', move);
  window.addEventListener('mouseup', end);

  canvas.addEventListener('touchstart', start, { passive: false });
  canvas.addEventListener('touchmove', move, { passive: false });
  canvas.addEventListener('touchend', end);
  canvas.addEventListener('touchcancel', end);

  clearBtn?.addEventListener('click', clear);

  // Update hidden input on submit
  form.addEventListener('submit', () => {
    syncToInput();
  });

  // Keep crisp on resize / orientation changes
  window.addEventListener('resize', () => {
    resizeCanvas();
  });
  window.addEventListener('orientationchange', () => {
    resizeCanvas();
  });

  // Persist also when leaving the page / losing focus (mobile browsers can snapshot/restore)
  window.addEventListener('beforeunload', syncToInput);

  resizeCanvas();
});
