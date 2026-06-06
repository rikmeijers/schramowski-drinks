document.addEventListener('DOMContentLoaded', () => {
  const triggers = document.querySelectorAll('[data-items-modal-trigger]');
  const modalEl = document.getElementById('itemsModal');
  if (!modalEl || !triggers.length) return;

  function getLabels(btn) {
    const json = btn.getAttribute('data-item-labels') || '{}';
    try { return JSON.parse(json) || {}; } catch { return {}; }
  }

  triggers.forEach(btn => {
    btn.addEventListener('click', () => {
      const json = btn.getAttribute('data-items') || '{}';
      let items;
      try { items = JSON.parse(json); } catch { items = {}; }

      const labels = getLabels(btn);

      const body = modalEl.querySelector('[data-items-modal-body]');
      body.innerHTML = '';

      const rows = Object.entries(items)
        .filter(([,v]) => parseInt(v || 0, 10) > 0)
        .sort(([a],[b]) => {
          const la = (labels[a] || a).toString();
          const lb = (labels[b] || b).toString();
          return la.localeCompare(lb);
        });

      if (!rows.length) {
        body.innerHTML = '<div class="text-body-secondary">Keine Artikel erfasst.</div>';
        return;
      }

      const table = document.createElement('table');
      table.className = 'table ui-table align-middle mb-0';
      table.innerHTML = `
        <thead>
          <tr>
            <th>Artikel</th>
            <th style="width:140px;">Anzahl</th>
          </tr>
        </thead>
        <tbody></tbody>
      `;

      const tbody = table.querySelector('tbody');
      rows.forEach(([k,v]) => {
        const tr = document.createElement('tr');
        const labelTd = document.createElement('td');
        labelTd.textContent = labels[k] || k;
        const qtyTd = document.createElement('td');
        qtyTd.className = 'fw-semibold';
        qtyTd.textContent = parseInt(v, 10);
        tr.appendChild(labelTd);
        tr.appendChild(qtyTd);
        tbody.appendChild(tr);
      });

      body.appendChild(table);
    });
  });
});
