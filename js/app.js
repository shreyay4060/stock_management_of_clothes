document.addEventListener("DOMContentLoaded", () => {
  // ========== Elements ==========
  const yearEl = document.getElementById("year");
  const profileBadge = document.getElementById("profileBadge");
  const adminLink = document.getElementById("adminLink");

  // Buttons
  const btnLogin = document.getElementById("btnLogin");
  const btnSignup = document.getElementById("btnSignup");
  const btnShowLogin = document.getElementById("btnShowLogin");
  const btnShowSignup = document.getElementById("btnShowSignup");

  // Modals
  const loginModal = document.getElementById("loginModal");
  const signupModal = document.getElementById("signupModal");

  // ========== Modal Controls ==========
  function openModal(modal) {
    if (modal) modal.classList.remove("hidden");
  }
  function closeModal(modal) {
    if (modal) modal.classList.add("hidden");
  }

  // Close buttons
  document.querySelectorAll(".close").forEach((btn) => {
    btn.addEventListener("click", (e) => {
      const target = e.target.getAttribute("data-close");
      const modal = document.getElementById(target);
      if (modal) closeModal(modal);
    });
  });

  // Outside click close
  window.addEventListener("click", (e) => {
    if (e.target.classList.contains("modal")) {
      closeModal(e.target);
    }
  });

  // Switch between login/signup
  document.getElementById("openSignup")?.addEventListener("click", (e) => {
    e.preventDefault();
    closeModal(loginModal);
    openModal(signupModal);
  });
  document.getElementById("openLogin")?.addEventListener("click", (e) => {
    e.preventDefault();
    closeModal(signupModal);
    openModal(loginModal);
  });

  // Show modals (only if no user logged in)
  btnShowLogin?.addEventListener("click", () => {
    const savedUser = JSON.parse(localStorage.getItem("user"));
    if (!savedUser) openModal(loginModal);
  });
  btnShowSignup?.addEventListener("click", () => {
    const savedUser = JSON.parse(localStorage.getItem("user"));
    if (!savedUser) openModal(signupModal);
  });

  // ========== Backend Calls ==========
  const API_BASE = "http://localhost/stock_management_of_clothes/backend";

  async function apiCall(endpoint, data) {
    try {
      const res = await fetch(`${API_BASE}/${endpoint}.php`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data),
        credentials: "include",
      });

      return await res.json();
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
      localStorage.setItem("user", JSON.stringify(res.user));
      updateNavbar(res.user);
      closeModal(signupModal);
      window.location.href = "index.php"; // ✅ Redirect
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
      localStorage.setItem("user", JSON.stringify(res.user));
      updateNavbar(res.user);
      closeModal(loginModal);
      window.location.href = "index.php"; // ✅ Redirect
    } else {
      alert("Login failed: " + res.error);
    }
  });

  // ========== Logout ==========
  profileBadge?.addEventListener("click", async () => {
    const savedUser = JSON.parse(localStorage.getItem("user"));
    if (!savedUser) return;

    const res = await apiCall("logout", {});
    if (res.ok) {
      localStorage.removeItem("user");
      updateNavbar(null);
      alert("Logged out!");
      window.location.href = "index.php";
    }
  });

  // ========== Navbar Update Helper ==========
  function updateNavbar(user) {
    if (!profileBadge) return;

    if (user) {
      const firstName = user.name.split(" ")[0];
      profileBadge.textContent = `${firstName} (${user.role})`;

      if (user.role === "admin") adminLink.style.display = "inline-block";
      else adminLink.style.display = "none";

      if (btnShowLogin) btnShowLogin.style.display = "none";
      if (btnShowSignup) btnShowSignup.style.display = "none";
      closeModal(loginModal);
      closeModal(signupModal);
    } else {
      profileBadge.textContent = "Guest";
      adminLink.style.display = "none";
      if (btnShowLogin) btnShowLogin.style.display = "inline-block";
      if (btnShowSignup) btnShowSignup.style.display = "inline-block";
    }
  }

  // ========== Auto check session ==========
  async function checkSession() {
    const savedUser = JSON.parse(localStorage.getItem("user"));
    if (savedUser) {
      updateNavbar(savedUser);
      return;
    }

    const res = await apiCall("whoami", {});
    if (res.ok && res.user) {
      localStorage.setItem("user", JSON.stringify(res.user));
      updateNavbar(res.user);
    } else {
      updateNavbar(null);
    }
  }
  checkSession();

  // ========== UI extras ==========
  if (yearEl) yearEl.textContent = new Date().getFullYear();
});
