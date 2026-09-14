document.addEventListener('DOMContentLoaded', () => {
    const tableBody   = document.getElementById('enquiriesTableBody');
    const messageBox  = document.getElementById('enquiryMessage');
    const resultInfo  = document.getElementById('enquiriesResultInfo');
    const paginationEl= document.getElementById('enquiriesPagination');
  
    const statusFilter   = document.getElementById('enquiryStatusFilter');
    const typeFilter      = document.getElementById('enquiryTypeFilter');
    const dateFromFilter  = document.getElementById('enquiryDateFrom');
    const dateToFilter    = document.getElementById('enquiryDateTo');
    const searchFilter    = document.getElementById('enquirySearch');
    const pageSizeFilter  = document.getElementById('enquiryPageSize');
    const clearFiltersBtn = document.getElementById('enquiryFilterClear');
  
    const viewModalEl = document.getElementById('enquiryViewModal');
    const viewModalBody = document.getElementById('enquiryViewBody');
    const viewModal = viewModalEl ? new bootstrap.Modal(viewModalEl) : null;
  
    if (!tableBody) return;
  
    let currentPage = 1;
    let searchDebounce = null;
  
    const STATUS_LABELS = {
      new: 'New',
      in_progress: 'In Progress',
      resolved: 'Resolved'
    };
  
    const STATUS_BADGE_CLASS = {
      new: 'badge-status-new',
      in_progress: 'badge-status-progress',
      resolved: 'badge-status-resolved'
    };
  
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
  
    function truncate(str, len) {
      if (!str) return '';
      return str.length > len ? str.slice(0, len).trim() + '…' : str;
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
      if (typeFilter.value) params.set('enquiry_type', typeFilter.value);
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
          <tr>
            <td colspan="8" class="text-center text-muted py-4">No enquiries found.</td>
          </tr>`;
        return;
      }
  
      tableBody.innerHTML = rows.map((row) => `
        <tr data-id="${row.id}">
          <td>#${row.id}</td>
          <td>
            <div class="fw-semibold">${escapeHtml(row.full_name)}</div>
          </td>
          <td>
            <div>${escapeHtml(row.email)}</div>
            <div class="text-muted small">${escapeHtml(row.mobile || '—')}</div>
          </td>
          <td>${escapeHtml(row.enquiry_type)}</td>
          <td>
            <span class="enquiry-msg-preview">${escapeHtml(truncate(row.message, 60))}</span>
          </td>
          <td>${formatDate(row.created_at)}</td>
          <td>
            <select class="form-select form-select-sm enquiry-status-select ${STATUS_BADGE_CLASS[row.status] || ''}" data-id="${row.id}">
              <option value="new" ${row.status === 'new' ? 'selected' : ''}>New</option>
              <option value="in_progress" ${row.status === 'in_progress' ? 'selected' : ''}>In Progress</option>
              <option value="resolved" ${row.status === 'resolved' ? 'selected' : ''}>Resolved</option>
            </select>
          </td>
          <td>
            <button type="button" class="btn btn-sm btn-outline-brand btn-view-enquiry"
                    data-id="${row.id}"
                    data-name="${escapeHtml(row.full_name)}"
                    data-email="${escapeHtml(row.email)}"
                    data-mobile="${escapeHtml(row.mobile || '—')}"
                    data-type="${escapeHtml(row.enquiry_type)}"
                    data-date="${formatDate(row.created_at)}"
                    data-message="${escapeHtml(row.message)}">
              View
            </button>
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
            loadEnquiries();
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
  
    function loadEnquiries() {
      tableBody.innerHTML = `
        <tr><td colspan="8" class="text-center text-muted py-4">Loading enquiries…</td></tr>`;
  
      fetch(`assets/api/admin-enquiries.php?${buildQuery()}`, {
        credentials: 'same-origin'
      })
        .then((res) => res.json())
        .then((data) => {
          if (!data.success) {
            showMessage(data.message || 'Could not load enquiries.', true);
            tableBody.innerHTML = `
              <tr><td colspan="8" class="text-center text-muted py-4">Could not load enquiries.</td></tr>`;
            return;
          }
  
          renderRows(data.data);
          renderPagination(data.page, data.total_pages);
  
          if (resultInfo) {
            const shown = data.data.length;
            const from = shown ? (data.page - 1) * data.page_size + 1 : 0;
            const to = from + shown - 1;
            resultInfo.textContent = `Showing ${from}-${to} of ${data.total} enquiries`;
          }
        })
        .catch(() => {
          showMessage('Something went wrong while loading enquiries.', true);
          tableBody.innerHTML = `
            <tr><td colspan="8" class="text-center text-muted py-4">Something went wrong.</td></tr>`;
        });
    }
  
    // ---- Status change handler (event delegation) ----
    tableBody.addEventListener('change', (e) => {
      const select = e.target.closest('.enquiry-status-select');
      if (!select) return;
  
      const id = select.dataset.id;
      const status = select.value;
  
      select.disabled = true;
  
      const formData = new FormData();
      formData.append('id', id);
      formData.append('status', status);
  
      fetch('assets/api/admin-enquiry-update-status.php', {
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
  
    // ---- View button handler (event delegation) ----
    tableBody.addEventListener('click', (e) => {
      const btn = e.target.closest('.btn-view-enquiry');
      if (!btn || !viewModal || !viewModalBody) return;
  
      viewModalBody.innerHTML = `
        <p class="mb-1"><strong>Name:</strong> ${btn.dataset.name}</p>
        <p class="mb-1"><strong>Email:</strong> ${btn.dataset.email}</p>
        <p class="mb-1"><strong>Mobile:</strong> ${btn.dataset.mobile}</p>
        <p class="mb-1"><strong>Type:</strong> ${btn.dataset.type}</p>
        <p class="mb-3"><strong>Received:</strong> ${btn.dataset.date}</p>
        <hr>
        <p class="mb-0" style="white-space: pre-wrap;">${btn.dataset.message}</p>
      `;
      viewModal.show();
    });
  
    // ---- Filter listeners ----
    [statusFilter, typeFilter, dateFromFilter, dateToFilter, pageSizeFilter].forEach((el) => {
      el.addEventListener('change', () => {
        currentPage = 1;
        loadEnquiries();
      });
    });
  
    searchFilter.addEventListener('input', () => {
      clearTimeout(searchDebounce);
      searchDebounce = setTimeout(() => {
        currentPage = 1;
        loadEnquiries();
      }, 350);
    });
  
    clearFiltersBtn.addEventListener('click', () => {
      statusFilter.value = '';
      typeFilter.value = '';
      dateFromFilter.value = '';
      dateToFilter.value = '';
      searchFilter.value = '';
      pageSizeFilter.value = '25';
      currentPage = 1;
      loadEnquiries();
    });
  
    // Initial load
    loadEnquiries();
  });