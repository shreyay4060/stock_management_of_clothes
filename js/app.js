// js/app.js

// ========== Elements ==========
const yearEl = document.getElementById("year");
const profileBadge = document.getElementById("profileBadge");
const adminLink = document.getElementById("adminLink");

// Modals
const loginModal = document.getElementById("loginModal");
const signupModal = document.getElementById("signupModal");

// Buttons
const btnShowLogin = document.getElementById("btnShowLogin");
const btnShowSignup = document.getElementById("btnShowSignup");
const btnLogin = document.getElementById("btnLogin");
const btnSignup = document.getElementById("btnSignup");

// ========== Helpers ==========
function openModal(modal) {
  modal.classList.remove("hidden");
}
function closeModal(modal) {
  modal.classList.add("hidden");
}

// Close buttons
document.querySelectorAll(".close").forEach(btn => {
  btn.addEventListener("click", e => {
    const target = e.target.getAttribute("data-close");
    closeModal(document.getElementById(target));
  });
});

// Outside click close
window.addEventListener("click", e => {
  if (e.target.classList.contains("modal")) {
    closeModal(e.target);
  }
});

// ========== Show modals ==========
btnShowLogin?.addEventListener("click", () => openModal(loginModal));
btnShowSignup?.addEventListener("click", () => openModal(signupModal));

// ========== Backend Calls ==========
const API_BASE = "http://localhost/stock_management_of_clothes/backend";

async function apiCall(endpoint, data) {
  try {
    const res = await fetch(`${API_BASE}/${endpoint}.php`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(data),
      credentials: "include"
    });

    const jsonRes = await res.json();
    console.log(`📡 [${endpoint}] response:`, jsonRes); // Debug log
    return jsonRes;
  } catch (err) {
    console.error(`❌ [${endpoint}] API error`, err);
    return { ok: false, error: "Network error" };
  }
}

// ========== Signup ==========
btnSignup?.addEventListener("click", async (e) => {
  e.preventDefault();

  const name = document.getElementById("signupName").value.trim();
  const email = document.getElementById("signupEmail").value.trim();
  const password = document.getElementById("signupPass").value.trim();

  if (!name || !email || !password) {
    alert("All fields are required!");
    return;
  }

  const res = await apiCall("signup", { name, email, password });
  if (res.ok) {
    alert("Signup successful! You can now log in.");
    closeModal(signupModal);
    openModal(loginModal);
  } else {
    alert("Signup failed: " + res.error);
  }
});

// ========== Login ==========
btnLogin?.addEventListener("click", async (e) => {
  e.preventDefault();

  const email = document.getElementById("loginEmail").value.trim();
  const password = document.getElementById("loginPass").value.trim();

  if (!email || !password) {
    alert("Both fields are required!");
    return;
  }

  const res = await apiCall("login", { email, password });
  if (res.ok) {
    profileBadge.textContent = res.user.name;
    if (res.user.role === "admin") adminLink.style.display = "inline-block";
    closeModal(loginModal);
  } else {
    alert("Login failed: " + res.error);
  }
});

// ========== Logout ==========
profileBadge?.addEventListener("click", async () => {
  if (profileBadge.textContent === "Guest") return;
  const res = await apiCall("logout", {});
  if (res.ok) {
    profileBadge.textContent = "Guest";
    adminLink.style.display = "none";
    alert("Logged out!");
  }
});

// ========== Auto check session ==========
async function checkSession() {
  const res = await apiCall("whoami", {});
  if (res.ok && res.user) {
    profileBadge.textContent = res.user.name;
    if (res.user.role === "admin") adminLink.style.display = "inline-block";
  }
}
checkSession();

// ========== UI extras ==========
if (yearEl) yearEl.textContent = new Date().getFullYear();

// Switch between login/signup modals
document.getElementById("openSignup")?.addEventListener("click", (e) => {
  e.preventDefault();
  loginModal.classList.add("hidden");
  signupModal.classList.remove("hidden");
});

document.getElementById("openLogin")?.addEventListener("click", (e) => {
  e.preventDefault();
  signupModal.classList.add("hidden");
  loginModal.classList.remove("hidden");
});
