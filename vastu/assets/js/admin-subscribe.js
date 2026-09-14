document.addEventListener('DOMContentLoaded', () => {
    const tableBody    = document.getElementById('subscribersTableBody');
    const messageBox   = document.getElementById('subMessage');
    const resultInfo   = document.getElementById('subResultInfo');
    const paginationEl = document.getElementById('subPagination');
  
    const statusFilter    = document.getElementById('subStatusFilter');
    const dateFromFilter  = document.getElementById('subDateFrom');
    const dateToFilter    = document.getElementById('subDateTo');
    const searchFilter    = document.getElementById('subSearch');
    const pageSizeFilter  = document.getElementById('subPageSize');
    const clearFiltersBtn = document.getElementById('subFilterClear');
  
    if (!tableBody) return;
  
    let currentPage = 1;
    let searchDebounce = null;
  
    function showMessage(text, isError) {
      if (!messageBox) return;
      messageBox.textContent = text;
      messageBox.classList.remove('d-none');
      messageBox.classList.toggle('is-error', !!isError);
      setTimeout(() => messageBox.classList.add('d-none'), 4000);
    }
  
    function escapeHtml(str) {
      if (str == null) return '';
      return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
    }
  
    function formatDate(dateStr) {
      const d = new Date(dateStr.replace(' ', 'T'));
      if (isNaN(d)) return dateStr;
      return d.toLocaleString('en-IN', {
        day: '2-digit', month: 'short', year: 'numeric',
        hour: '2-digit', minute: '2-digit'
      });
    }
  
    function buildQuery() {
      const params = new URLSearchParams();
      if (statusFilter.value) params.set('status', statusFilter.value);
      if (dateFromFilter.value) params.set('date_from', dateFromFilter.value);
      if (dateToFilter.value) params.set('date_to', dateToFilter.value);
      if (searchFilter.value.trim()) params.set('search', searchFilter.value.trim());
      params.set('page', currentPage);
      params.set('page_size', pageSizeFilter.value);
      return params.toString();
    }
  
    function renderRows(rows) {
      if (!rows.length) {
        tableBody.innerHTML = `
          <tr><td colspan="5" class="text-center text-muted py-4">No subscribers found.</td></tr>`;
        return;
      }
  
      tableBody.innerHTML = rows.map((row) => `
        <tr data-id="${row.id}">
          <td>#${row.id}</td>
          <td>${escapeHtml(row.name || '—')}</td>
          <td>${escapeHtml(row.email)}</td>
          <td>${formatDate(row.created_at)}</td>
          <td>
            <select class="form-select form-select-sm sub-status-select" data-id="${row.id}">
              <option value="Active" ${row.status === 'Active' ? 'selected' : ''}>Active</option>
              <option value="Inactive" ${row.status === 'Inactive' ? 'selected' : ''}>Inactive</option>
            </select>
          </td>
        </tr>
      `).join('');
    }
  
    function renderPagination(page, totalPages) {
      if (!paginationEl) return;
      paginationEl.innerHTML = '';
      if (totalPages <= 1) return;
  
      const makeItem = (label, targetPage, disabled, active) => {
        const li = document.createElement('li');
        li.className = `page-item ${disabled ? 'disabled' : ''} ${active ? 'active' : ''}`;
        const a = document.createElement('a');
        a.className = 'page-link';
        a.href = '#';
        a.textContent = label;
        if (!disabled && !active) {
          a.addEventListener('click', (e) => {
            e.preventDefault();
            currentPage = targetPage;
            loadSubscribers();
          });
        }
        li.appendChild(a);
        return li;
      };
  
      paginationEl.appendChild(makeItem('«', page - 1, page <= 1, false));
  
      const start = Math.max(1, page - 2);
      const end = Math.min(totalPages, page + 2);
      for (let p = start; p <= end; p++) {
        paginationEl.appendChild(makeItem(String(p), p, false, p === page));
      }
  
      paginationEl.appendChild(makeItem('»', page + 1, page >= totalPages, false));
    }
  
    function loadSubscribers() {
      tableBody.innerHTML = `
        <tr><td colspan="5" class="text-center text-muted py-4">Loading subscribers…</td></tr>`;
  
      fetch(`assets/api/admin-subscribe-list.php?${buildQuery()}`, {
        credentials: 'same-origin'
      })
        .then((res) => res.json())
        .then((data) => {
          if (!data.success) {
            showMessage(data.message || 'Could not load subscribers.', true);
            tableBody.innerHTML = `
              <tr><td colspan="5" class="text-center text-muted py-4">Could not load subscribers.</td></tr>`;
            return;
          }
  
          renderRows(data.data);
          renderPagination(data.page, data.total_pages);
  
          if (resultInfo) {
            const shown = data.data.length;
            const from = shown ? (data.page - 1) * data.page_size + 1 : 0;
            const to = from + shown - 1;
            resultInfo.textContent = `Showing ${from}-${to} of ${data.total} subscribers`;
          }
        })
        .catch(() => {
          showMessage('Something went wrong while loading subscribers.', true);
          tableBody.innerHTML = `
            <tr><td colspan="5" class="text-center text-muted py-4">Something went wrong.</td></tr>`;
        });
    }
  
    // ---- Status change handler (event delegation) ----
    tableBody.addEventListener('change', (e) => {
      const select = e.target.closest('.sub-status-select');
      if (!select) return;
  
      const id = select.dataset.id;
      const status = select.value;
  
      select.disabled = true;
  
      const formData = new FormData();
      formData.append('id', id);
      formData.append('status', status);
  
      fetch('assets/api/admin-subscribe-update-status.php', {
        method: 'POST',
        body: formData,
        credentials: 'same-origin'
      })
        .then((res) => res.json())
        .then((data) => {
          showMessage(data.message, !data.success);
        })
        .catch(() => {
          showMessage('Could not update status. Please try again.', true);
        })
        .finally(() => {
          select.disabled = false;
        });
    });
  
    // ---- Filter listeners ----
    [statusFilter, dateFromFilter, dateToFilter, pageSizeFilter].forEach((el) => {
      el.addEventListener('change', () => {
        currentPage = 1;
        loadSubscribers();
      });
    });
  
    searchFilter.addEventListener('input', () => {
      clearTimeout(searchDebounce);
      searchDebounce = setTimeout(() => {
        currentPage = 1;
        loadSubscribers();
      }, 350);
    });
  
    clearFiltersBtn.addEventListener('click', () => {
      statusFilter.value = '';
      dateFromFilter.value = '';
      dateToFilter.value = '';
      searchFilter.value = '';
      pageSizeFilter.value = '25';
      currentPage = 1;
      loadSubscribers();
    });
  
    // Initial load
    loadSubscribers();
  });