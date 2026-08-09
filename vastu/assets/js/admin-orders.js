const defaultOrders = [
  { id: "VA-102341", customer: "Aarav Sharma", product: "Brass Entrance Harmony Set", date: "2026-04-18", status: "Pending" },
  { id: "VA-102342", customer: "Rhea Mehta", product: "Tranquil Lamp Diffuser", date: "2026-04-19", status: "Dispatched" },
  { id: "VA-102343", customer: "Keshav Batra", product: "Compass Desk Tile", date: "2026-04-20", status: "On Hold" },
  { id: "VA-102344", customer: "Naina Khanna", product: "Brass Entrance Harmony Set", date: "2026-04-21", status: "Cancelled" }
];

const adminInfo = JSON.parse(localStorage.getItem("vastuAdmin") || '{"name":"Admin Anaya","email":"admin@vastuaura.com"}');
document.getElementById("adminGreeting").textContent = adminInfo.name;
const adminOrdersBackToTop = document.getElementById("backToTop");

const getOrders = () => JSON.parse(localStorage.getItem("vastuOrders") || "null") || defaultOrders;
const setOrders = (orders) => localStorage.setItem("vastuOrders", JSON.stringify(orders));
const ordersTableBody = document.getElementById("ordersTableBody");
const orderMessage = document.getElementById("orderMessage");

const badgeClass = (status) => {
  if (status === "Pending") return "status-pending";
  if (status === "On Hold") return "status-hold";
  if (status === "Dispatched") return "status-dispatched";
  return "status-cancelled";
};

const renderOrders = () => {
  const statusFilter = document.getElementById("orderStatusFilter").value;
  const from = document.getElementById("orderDateFrom").value;
  const to = document.getElementById("orderDateTo").value;
  const productFilter = document.getElementById("orderProductFilter").value;
  const search = document.getElementById("orderSearch").value.trim().toLowerCase();

  const filtered = getOrders().filter((order) => {
    const statusMatch = !statusFilter || order.status === statusFilter;
    const productMatch = !productFilter || order.product === productFilter;
    const searchMatch = !search || `${order.id} ${order.customer} ${order.product}`.toLowerCase().includes(search);
    const fromMatch = !from || order.date >= from;
    const toMatch = !to || order.date <= to;
    return statusMatch && productMatch && searchMatch && fromMatch && toMatch;
  });

  ordersTableBody.innerHTML = filtered.map((order) => `
    <tr>
      <td>${order.id}</td>
      <td>${order.customer}</td>
      <td>${order.product}</td>
      <td>${order.date}</td>
      <td><span class="status-pill ${badgeClass(order.status)}">${order.status}</span></td>
      <td>
        <div class="action-group">
          <button class="action-btn" data-id="${order.id}" data-action="Delete">Delete</button>
          <button class="action-btn" data-id="${order.id}" data-action="Cancelled">Cancel</button>
          <button class="action-btn" data-id="${order.id}" data-action="Dispatched">Dispatched</button>
          <button class="action-btn" data-id="${order.id}" data-action="On Hold">On Hold</button>
        </div>
      </td>
    </tr>
  `).join("");
};

const showOrderMessage = (text) => {
  orderMessage.textContent = text;
  orderMessage.classList.remove("d-none");
};

ordersTableBody?.addEventListener("click", (event) => {
  const target = event.target;
  if (!(target instanceof HTMLElement)) return;
  const id = target.dataset.id;
  const action = target.dataset.action;
  if (!id || !action) return;

  let orders = getOrders();

  if (action === "Delete") {
    orders = orders.filter((order) => order.id !== id);
    setOrders(orders);
    showOrderMessage(`Order ${id} deleted by ${adminInfo.name} at ${new Date().toLocaleString("en-IN")}`);
    renderOrders();
    return;
  }

  orders = orders.map((order) => order.id === id ? { ...order, status: action } : order);
  setOrders(orders);
  showOrderMessage(`Status updated to ${action} by ${adminInfo.name} at ${new Date().toLocaleString("en-IN")}`);
  renderOrders();
});

["orderStatusFilter", "orderDateFrom", "orderDateTo", "orderProductFilter", "orderSearch"].forEach((id) => {
  document.getElementById(id)?.addEventListener("input", renderOrders);
  document.getElementById(id)?.addEventListener("change", renderOrders);
});

const logoutAdmin = () => {
  localStorage.removeItem("vastuAdmin");
  window.location.href = "admin-login.php";
};

document.getElementById("sidebarLogout")?.addEventListener("click", (event) => {
  event.preventDefault();
  logoutAdmin();
});

document.getElementById("topbarLogout")?.addEventListener("click", logoutAdmin);

setOrders(getOrders());
renderOrders();

window.addEventListener("scroll", () => {
  adminOrdersBackToTop.classList.toggle("show", window.scrollY > 220);
});

adminOrdersBackToTop?.addEventListener("click", () => window.scrollTo({ top: 0, behavior: "smooth" }));

if (window.anime) {
  anime({
    targets: ".admin-sidebar, .admin-topbar, .page-head, .filter-card, .table-card",
    translateY: [24, 0],
    opacity: [0, 1],
    delay: anime.stagger(70),
    duration: 840,
    easing: "easeOutQuad"
  });
}
