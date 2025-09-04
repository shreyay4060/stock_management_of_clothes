// js/app.js
// Put all logic inside DOMContentLoaded so we don't accidentally redeclare globals
document.addEventListener("DOMContentLoaded", () => {
  // ======= Helpers =======
  const API_BASE = "http://localhost/stock_management_of_clothes/backend";

  const $id = id => document.getElementById(id);
  const qs = sel => document.querySelector(sel);

  // ======= Elements (lookups are tolerant to missing ids in header.php) =======
  const yearEl = $id("year");
  // badge may or may not have an id in your header; find it reliably
  const navActions = qs(".nav-actions");
  function getOrCreateBadge() {
    let b = $id("profileBadge") || (navActions && navActions.querySelector(".badge"));
    if (!b && navActions) {
      b = document.createElement("span");
      b.className = "badge";
      b.id = "profileBadge"; // give it an id so future lookups are easy
      navActions.insertBefore(b, navActions.firstChild);
    }
    return b;
  }
  const profileBadge = getOrCreateBadge();

  // admin link - header may or may not have an id; find anchor to admin
  const adminLink = $id("adminLink") || qs('nav.navlinks a[href="admin.php"]');

  // buttons that are present in header
  const btnShowLogin = $id("btnShowLogin");
  const btnShowSignup = $id("btnShowSignup");

  // modals (may be created in header)
  const loginModal = $id("loginModal");
  const signupModal = $id("signupModal");

  // forms
  const loginForm = $id("loginForm");
  const signupForm = $id("signupForm");

  // login/signup submit buttons (not strictly needed if we use form submit)
  const btnLogin = $id("btnLogin");
  const btnSignup = $id("btnSignup");

  // ======= Modal functions =======
  function showModal(modal) {
    if (!modal) return;
    modal.classList.remove("hidden");
    // header CSS uses .modal[open] or .modal.show -> set display explicitly for robustness
    modal.style.display = "flex";
  }
  function hideModal(modal) {
    if (!modal) return;
    modal.classList.add("hidden");
    modal.style.display = "none";
  }

  // Close button handlers (any element with data-close)
  document.querySelectorAll("[data-close]").forEach(btn => {
    btn.addEventListener("click", (e) => {
      const id = btn.getAttribute("data-close");
      const m = document.getElementById(id);
      if (m) hideModal(m);
    });
  });

  // Close when clicking outside (click on backdrop)
  window.addEventListener("click", (e) => {
    // e.target could be modal backdrop (has class 'modal')
    if (e.target && e.target.classList && e.target.classList.contains("modal")) {
      hideModal(e.target);
    }
  });

  // Switch links inside modals: openSignup / openLogin
  document.getElementById("openSignup")?.addEventListener("click", (e) => {
    e.preventDefault();
    hideModal(loginModal);
    showModal(signupModal);
  });
  document.getElementById("openLogin")?.addEventListener("click", (e) => {
    e.preventDefault();
    hideModal(signupModal);
    showModal(loginModal);
  });

  // Show modals from header buttons, but only if no user present in localStorage
  btnShowLogin?.addEventListener("click", () => {
    const saved = JSON.parse(localStorage.getItem("user") || "null");
    if (!saved) showModal(loginModal);
  });
  btnShowSignup?.addEventListener("click", () => {
    const saved = JSON.parse(localStorage.getItem("user") || "null");
    if (!saved) showModal(signupModal);
  });

  // ======= API helper =======
  async function apiCall(endpoint, data = {}) {
    try {
      const res = await fetch(`${API_BASE}/${endpoint}.php`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        credentials: "include",
        body: JSON.stringify(data)
      });
      return await res.json();
    } catch (err) {
      console.error("API error:", err);
      return { ok: false, error: "Network error" };
    }
  }

  // ======= Navbar update helper =======
  function updateNavbar(user) {
    const loginBtn = btnShowLogin;
    const signupBtn = btnShowSignup;

    // ensure we have a badge element
    const badge = getOrCreateBadge();
    if (!badge) return;

    if (user) {
      // show first name + role in badge
      const first = (user.name || "").split(" ")[0] || user.email || "User";
      badge.textContent = `${first} (${user.role || "retailer"})`;

      // show admin link only for admins
      if (adminLink) adminLink.style.display = (user.role === "admin") ? "inline-block" : "none";

      // hide login/signup header buttons
      if (loginBtn) loginBtn.style.display = "none";
      if (signupBtn) signupBtn.style.display = "none";

      // replace any existing logout anchor with a JS-handled logout to avoid navigation issues
      const existingLogout = navActions && navActions.querySelector('a[href="logout.php"]');
      if (existingLogout) {
        existingLogout.classList.add("logout-raw"); // mark to avoid removing by accident
        // convert to button-like behavior: intercept click
        existingLogout.addEventListener("click", async (ev) => {
          ev.preventDefault();
          await doLogout();
        });
      } else {
        // if there is no logout anchor (rare), create one
        if (navActions && !navActions.querySelector(".logout-btn")) {
          const a = document.createElement("a");
          a.href = "#";
          a.className = "btn-primary logout-btn";
          a.textContent = "Logout";
          a.addEventListener("click", async (ev) => {
            ev.preventDefault();
            await doLogout();
          });
          navActions.appendChild(a);
        }
      }
    } else {
      // not logged in
      badge.textContent = "Guest";
      if (adminLink) adminLink.style.display = "none";
      if (loginBtn) loginBtn.style.display = "inline-block";
      if (signupBtn) signupBtn.style.display = "inline-block";

      // if a logout-btn was created by script earlier, remove it
      const createdLogout = navActions && navActions.querySelector(".logout-btn");
      if (createdLogout) createdLogout.remove();
    }
  }

  // ======= Login / Signup form handlers =======
  // Use form submit events (safer than button click only)
  loginForm?.addEventListener("submit", async (e) => {
    e.preventDefault();
    const form = loginForm;
    const email = form.querySelector('[name="email"]')?.value?.trim() || "";
    const password = form.querySelector('[name="password"]')?.value || "";

    if (!email || !password) {
      alert("Both fields are required!");
      return;
    }

    const res = await apiCall("login", { email, password });
    if (res.ok && res.user) {
      // store in localStorage for fast UI updates
      localStorage.setItem("user", JSON.stringify(res.user));
      updateNavbar(res.user);
      hideModal(loginModal);
      // redirect to home (show home screen)
      window.location.href = "index.php";
    } else {
      const msg = res.error || "Login failed";
      alert(msg);
    }
  });

  signupForm?.addEventListener("submit", async (e) => {
    e.preventDefault();
    const form = signupForm;
    const name = form.querySelector('[name="name"]')?.value?.trim() || "";
    const email = form.querySelector('[name="email"]')?.value?.trim() || "";
    const password = form.querySelector('[name="password"]')?.value || "";

    if (!name || !email || !password) {
      alert("All fields are required!");
      return;
    }

    const res = await apiCall("signup", { name, email, password });
    if (res.ok && res.user) {
      localStorage.setItem("user", JSON.stringify(res.user));
      updateNavbar(res.user);
      hideModal(signupModal);
      window.location.href = "index.php";
    } else {
      const msg = res.error || "Signup failed";
      alert(msg);
    }
  });

  // ======= Logout routine used by several places =======
  async function doLogout() {
    const res = await apiCall("logout", {});
    // even if backend fails, remove local info
    localStorage.removeItem("user");
    updateNavbar(null);
    if (res && res.ok) {
      // good
    }
    // reload to reflect server-side session status
    window.location.href = "index.php";
  }

  // Intercept clicks on any logout anchor that directly points to logout.php (in header markup)
  document.body.addEventListener("click", (ev) => {
    const a = ev.target.closest && ev.target.closest('a[href$="logout.php"]');
    if (a) {
      ev.preventDefault();
      doLogout();
    }
  });

  // ======= Session check on load (localStorage first) =======
  (function init() {
    if (yearEl) yearEl.textContent = new Date().getFullYear();

    const saved = JSON.parse(localStorage.getItem("user") || "null");
    if (saved) {
      updateNavbar(saved);
      return;
    }

    // fallback to server session
    apiCall("whoami", {}).then(res => {
      if (res && res.ok && res.user) {
        localStorage.setItem("user", JSON.stringify(res.user));
        updateNavbar(res.user);
      } else {
        updateNavbar(null);
      }
    }).catch(() => updateNavbar(null));
  })();

}); // DOMContentLoaded
