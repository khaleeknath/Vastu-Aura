// assets/js/admin-orders.js
// Talks to admin-orders-backend.php. Handles filtering, search (debounced),
// pagination, and inline order status updates.

(function () {
  const BACKEND_URL = 'assets/api/admin-orders.php';

  const el = {
    tableBody: document.getElementById('ordersTableBody'),
    message: document.getElementById('orderMessage'),
    statusFilter: document.getElementById('orderStatusFilter'),
    dateFrom: document.getElementById('orderDateFrom'),
    dateTo: document.getElementById('orderDateTo'),
    productFilter: document.getElementById('orderProductFilter'),
    search: document.getElementById('orderSearch'),
    pageSize: document.getElementById('orderPageSize'),
    clearBtn: document.getElementById('orderFilterClear'),
    pagination: document.getElementById('ordersPagination'),
    resultInfo: document.getElementById('ordersResultInfo'),
  };

  const STATUS_OPTIONS = ['Pending', 'On Hold', 'Dispatched', 'Cancelled'];

  const state = {
    page: 1,
    pageSize: parseInt(el.pageSize.value, 10) || 25,
    status: '',
    dateFrom: '',
    dateTo: '',
    product: '',
    search: '',
  };

  let searchDebounce = null;

  function showMessage(text, type = 'info') {
    el.message.textContent = text;
    el.message.className = `message-box message-${type}`;
    el.message.classList.remove('d-none');
    if (type !== 'error') {
      setTimeout(() => el.message.classList.add('d-none'), 3000);
    }
  }

  function hideMessage() {
    el.message.classList.add('d-none');
  }

  function statusBadgeClass(status) {
    switch (status) {
      case 'Dispatched': return 'badge-status badge-success';
      case 'Cancelled': return 'badge-status badge-danger';
      case 'On Hold': return 'badge-status badge-warning';
      default: return 'badge-status badge-neutral';
    }
  }

  function formatDate(dateStr) {
    const d = new Date(dateStr);
    if (isNaN(d.getTime())) return dateStr;
    return d.toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' });
  }

  function formatAmount(amount) {
    const n = Number(amount);
    return isNaN(n) ? amount : `₹${n.toLocaleString('en-IN', { minimumFractionDigits: 2 })}`;
  }

  function buildQuery() {
    const params = new URLSearchParams({
      action: 'list',
      page: state.page,
      per_page: state.pageSize,
    });
    if (state.status) params.set('status', state.status);
    if (state.dateFrom) params.set('date_from', state.dateFrom);
    if (state.dateTo) params.set('date_to', state.dateTo);
    if (state.product) params.set('product', state.product);
    if (state.search) params.set('search', state.search);
    return params.toString();
  }

  async function fetchOrders() {
    el.tableBody.innerHTML = `<tr><td colspan="7" class="text-center py-4">Loading orders…</td></tr>`;
    try {
      const res = await fetch(`${BACKEND_URL}?${buildQuery()}`, {
        method: 'GET',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
      });
      const data = await res.json();

      if (!res.ok || !data.success) {
        throw new Error(data.message || 'Failed to load orders.');
      }

      renderTable(data.orders);
      renderPagination(data.pagination);
      renderResultInfo(data.pagination);

      if (Array.isArray(data.products) && el.productFilter.dataset.populated !== '1') {
        populateProductFilter(data.products);
      }
    } catch (err) {
      el.tableBody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-danger">${escapeHtml(err.message)}</td></tr>`;
    }
  }

  function populateProductFilter(products) {
    products.forEach((p) => {
      const opt = document.createElement('option');
      opt.value = p;
      opt.textContent = p;
      el.productFilter.appendChild(opt);
    });
    el.productFilter.dataset.populated = '1';
  }

  function renderTable(orders) {
    if (!orders || orders.length === 0) {
      el.tableBody.innerHTML = `<tr><td colspan="7" class="text-center py-4">No orders found.</td></tr>`;
      return;
    }

    el.tableBody.innerHTML = orders.map((order) => {
      const statusOptions = STATUS_OPTIONS.map((s) =>
        `<option value="${s}" ${s === order.status ? 'selected' : ''}>${s}</option>`
      ).join('');

      return `
        <tr data-order-id="${order.id}">
          <td>#${order.id}</td>
          <td>
            <div class="fw-semibold">${escapeHtml(order.customer_name || 'N/A')}</div>
            <div class="text-muted small">${escapeHtml(order.email || '')}</div>
          </td>
          <td>${escapeHtml(order.products || '—')}</td>
          <td>${formatAmount(order.total_amount)}</td>
          <td>${formatDate(order.order_date)}</td>
          <td><span class="${statusBadgeClass(order.status)}">${escapeHtml(order.status)}</span></td>
          <td>
            <div class="d-flex gap-2 align-items-center">
              <select class="form-select form-select-sm order-status-select" data-order-id="${order.id}">
                ${statusOptions}
              </select>
              <button class="btn btn-sm btn-brand order-status-save" data-order-id="${order.id}">Update</button>
            </div>
          </td>
        </tr>
      `;
    }).join('');
  }

  function renderPagination(pagination) {
    if (!pagination) {
      el.pagination.innerHTML = '';
      return;
    }
    const { current_page, total_pages } = pagination;
    if (total_pages <= 1) {
      el.pagination.innerHTML = '';
      return;
    }

    let html = '';
    html += pageItem(current_page - 1, '«', current_page === 1);

    const windowSize = 2;
    for (let p = 1; p <= total_pages; p++) {
      if (p === 1 || p === total_pages || (p >= current_page - windowSize && p <= current_page + windowSize)) {
        html += pageItem(p, p, false, p === current_page);
      } else if (p === current_page - windowSize - 1 || p === current_page + windowSize + 1) {
        html += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
      }
    }

    html += pageItem(current_page + 1, '»', current_page === total_pages);
    el.pagination.innerHTML = html;
  }

  function pageItem(page, label, disabled, active = false) {
    return `
      <li class="page-item ${disabled ? 'disabled' : ''} ${active ? 'active' : ''}">
        <a class="page-link" href="#" data-page="${page}">${label}</a>
      </li>
    `;
  }

  function renderResultInfo(pagination) {
    if (!pagination) {
      el.resultInfo.textContent = '';
      return;
    }
    const { total, current_page, per_page } = pagination;
    if (total === 0) {
      el.resultInfo.textContent = 'No results';
      return;
    }
    const start = (current_page - 1) * per_page + 1;
    const end = Math.min(current_page * per_page, total);
    el.resultInfo.textContent = `Showing ${start}–${end} of ${total} orders`;
  }

  function escapeHtml(str) {
    if (str === null || str === undefined) return '';
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }

  async function updateOrderStatus(orderId, newStatus, triggerBtn) {
    const originalLabel = triggerBtn.textContent;
    triggerBtn.disabled = true;
    triggerBtn.textContent = 'Saving…';
    try {
      const res = await fetch(BACKEND_URL, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
          'X-Requested-With': 'XMLHttpRequest',
        },
        body: new URLSearchParams({ action: 'update_status', order_id: orderId, status: newStatus }),
      });
      const data = await res.json();
      if (!res.ok || !data.success) {
        throw new Error(data.message || 'Could not update order status.');
      }
      showMessage(`Order #${orderId} updated to "${newStatus}".`, 'success');
      fetchOrders();
    } catch (err) {
      showMessage(err.message, 'error');
    } finally {
      triggerBtn.disabled = false;
      triggerBtn.textContent = originalLabel;
    }
  }

  // --- Event wiring ---

  el.tableBody.addEventListener('click', (e) => {
    const btn = e.target.closest('.order-status-save');
    if (!btn) return;
    const orderId = btn.dataset.orderId;
    const select = el.tableBody.querySelector(`.order-status-select[data-order-id="${orderId}"]`);
    updateOrderStatus(orderId, select.value, btn);
  });

  el.statusFilter.addEventListener('change', () => {
    state.status = el.statusFilter.value;
    state.page = 1;
    fetchOrders();
  });

  el.dateFrom.addEventListener('change', () => {
    state.dateFrom = el.dateFrom.value;
    state.page = 1;
    fetchOrders();
  });

  el.dateTo.addEventListener('change', () => {
    state.dateTo = el.dateTo.value;
    state.page = 1;
    fetchOrders();
  });

  el.productFilter.addEventListener('change', () => {
    state.product = el.productFilter.value;
    state.page = 1;
    fetchOrders();
  });

  el.pageSize.addEventListener('change', () => {
    state.pageSize = parseInt(el.pageSize.value, 10) || 25;
    state.page = 1;
    fetchOrders();
  });

  el.search.addEventListener('input', () => {
    clearTimeout(searchDebounce);
    searchDebounce = setTimeout(() => {
      state.search = el.search.value.trim();
      state.page = 1;
      fetchOrders();
    }, 400);
  });

  el.clearBtn.addEventListener('click', () => {
    el.statusFilter.value = '';
    el.dateFrom.value = '';
    el.dateTo.value = '';
    el.productFilter.value = '';
    el.search.value = '';
    Object.assign(state, { status: '', dateFrom: '', dateTo: '', product: '', search: '', page: 1 });
    fetchOrders();
  });

  el.pagination.addEventListener('click', (e) => {
    e.preventDefault();
    const link = e.target.closest('a[data-page]');
    if (!link || link.closest('.disabled')) return;
    const page = parseInt(link.dataset.page, 10);
    if (!page || page === state.page) return;
    state.page = page;
    fetchOrders();
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });

  document.addEventListener('DOMContentLoaded', fetchOrders);
  if (document.readyState !== 'loading') fetchOrders();

  hideMessage();
})();