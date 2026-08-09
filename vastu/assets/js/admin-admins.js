const defaultAdmins = [
  { name: "Admin Anaya", email: "admin@vastuaura.com", role: "Operations Admin" },
  { name: "Rahul Sethi", email: "rahul@vastuaura.com", role: "Store Admin" },
  { name: "Nisha Rao", email: "nisha@vastuaura.com", role: "Support Admin" }
];

const adminsInfo = JSON.parse(localStorage.getItem("vastuAdmin") || '{"name":"Admin Anaya","email":"admin@vastuaura.com"}');
document.getElementById("adminGreeting").textContent = adminsInfo.name;
const adminAdminsBackToTop = document.getElementById("backToTop");

const getAdmins = () => JSON.parse(localStorage.getItem("vastuAdminList") || "null") || defaultAdmins;
const setAdmins = (admins) => localStorage.setItem("vastuAdminList", JSON.stringify(admins));
const adminsTableBody = document.getElementById("adminsTableBody");
const adminMessage = document.getElementById("adminMessage");

const renderAdmins = () => {
  adminsTableBody.innerHTML = getAdmins().map((admin, index) => `
    <tr>
      <td>${index + 1}</td>
      <td>${admin.name}</td>
      <td>${admin.email}</td>
      <td>${admin.role}</td>
      <td><button class="action-btn" data-index="${index}" ${index === 0 ? "disabled" : ""}>Delete</button></td>
    </tr>
  `).join("");
};

const showAdminMessage = (text) => {
  adminMessage.textContent = text;
  adminMessage.classList.remove("d-none");
};

document.getElementById("toggleAdminForm")?.addEventListener("click", () => {
  document.getElementById("addAdminPanel").classList.toggle("d-none");
});

document.getElementById("addAdminForm")?.addEventListener("submit", (event) => {
  event.preventDefault();
  const admins = getAdmins();
  admins.push({
    name: document.getElementById("newAdminName").value.trim(),
    email: document.getElementById("newAdminEmail").value.trim(),
    role: document.getElementById("newAdminRole").value
  });
  setAdmins(admins);
  showAdminMessage(`New admin added by ${adminsInfo.name} at ${new Date().toLocaleString("en-IN")}`);
  event.target.reset();
  document.getElementById("addAdminPanel").classList.add("d-none");
  renderAdmins();
});

adminsTableBody?.addEventListener("click", (event) => {
  const target = event.target;
  if (!(target instanceof HTMLElement)) return;
  const index = target.dataset.index;
  if (index === undefined) return;

  const admins = getAdmins();
  admins.splice(Number(index), 1);
  setAdmins(admins);
  showAdminMessage(`Admin removed by ${adminsInfo.name} at ${new Date().toLocaleString("en-IN")}`);
  renderAdmins();
});

const logoutAdminsPanel = () => {
  localStorage.removeItem("vastuAdmin");
  window.location.href = "admin-login.php";
};

document.getElementById("sidebarLogout")?.addEventListener("click", (event) => {
  event.preventDefault();
  logoutAdminsPanel();
});

document.getElementById("topbarLogout")?.addEventListener("click", logoutAdminsPanel);

setAdmins(getAdmins());
renderAdmins();

window.addEventListener("scroll", () => {
  adminAdminsBackToTop.classList.toggle("show", window.scrollY > 220);
});

adminAdminsBackToTop?.addEventListener("click", () => window.scrollTo({ top: 0, behavior: "smooth" }));

if (window.anime) {
  anime({
    targets: ".admin-sidebar, .admin-topbar, .page-head, .form-card, .table-card",
    translateY: [24, 0],
    opacity: [0, 1],
    delay: anime.stagger(70),
    duration: 840,
    easing: "easeOutQuad"
  });
}
