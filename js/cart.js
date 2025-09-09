// js/cart.js
// =======================
// Cart — Table Version with Checkout
// =======================

document.addEventListener("DOMContentLoaded", () => {
  const tbody = document.getElementById("cartTableBody");
  const totalBox = document.getElementById("cartTotal");
  const checkoutBtn = document.getElementById("checkoutBtn");

  function escapeHtml(s) {
    return String(s || "").replace(/[&<>"']/g, (m) => ({
      "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;"
    }[m]));
  }

  // ✅ Always resolve to backend/uploads
  function normalizeImagePath(imgSrc) {
    if (!imgSrc) return "images/arrival3.jpg";
    if (imgSrc.startsWith("http") || imgSrc.startsWith("images/") || imgSrc.startsWith("backend/uploads/")) {
      return imgSrc;
    }
    return `backend/uploads/${imgSrc}`;
  }

  function loadCart() {
    let cart = JSON.parse(localStorage.getItem("cart") || "[]");

    cart = cart.map(item => ({
      ...item,
      image: normalizeImagePath(item.image),
      qty: Number(item.qty) || 1,
      price: Number(item.price) || 0
    }));

    localStorage.setItem("cart", JSON.stringify(cart));
    renderCart(cart);
  }

  function renderCart(cart) {
    if (!tbody) return console.warn("Missing cart table body (#cartTableBody)");
    tbody.innerHTML = "";
    let total = 0;

    if (!cart.length) {
      tbody.innerHTML = `<tr><td colspan="6" class="muted">Your cart is empty.</td></tr>`;
      if (totalBox) totalBox.textContent = "₹0.00";
      return;
    }

    cart.forEach((item, idx) => {
      const subtotal = (item.price * item.qty).toFixed(2);
      total += item.price * item.qty;

      const tr = document.createElement("tr");
      tr.innerHTML = `
        <td><img src="${escapeHtml(item.image)}" alt="${escapeHtml(item.name)}"
                 style="width:64px;height:64px;object-fit:cover;border-radius:6px"></td>
        <td>${escapeHtml(item.name)}</td>
        <td>₹${Number(item.price).toFixed(2)}</td>
        <td>
          <input type="number" min="1" max="100" value="${item.qty}"
                 onchange="setQty(${idx}, this.value)" style="width:60px;text-align:center;">
        </td>
        <td>₹${subtotal}</td>
        <td><button onclick="removeItem(${idx})">×</button></td>
      `;
      tbody.appendChild(tr);
    });

    if (totalBox) totalBox.textContent = "₹" + total.toFixed(2);
  }

  // global handlers
  window.setQty = function(idx, val) {
    const cart = JSON.parse(localStorage.getItem("cart") || "[]");
    if (!cart[idx]) return;
    let qty = parseInt(val) || 1;
    qty = Math.max(1, Math.min(100, qty));
    cart[idx].qty = qty;
    localStorage.setItem("cart", JSON.stringify(cart));
    loadCart();
  };

  window.removeItem = function(idx) {
    const cart = JSON.parse(localStorage.getItem("cart") || "[]");
    if (!cart[idx]) return;
    cart.splice(idx, 1);
    localStorage.setItem("cart", JSON.stringify(cart));
    loadCart();
  };

  // ✅ Checkout with credentials
  async function checkout() {
    const cart = JSON.parse(localStorage.getItem("cart") || "[]");
    if (!cart.length) return alert("Cart is empty!");

    try {
      const res = await fetch("backend/place_order.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ items: cart.map(it => ({ id: it.id, qty: it.qty })) }),
        credentials: "include"
      });

      let data;
      try {
        data = await res.json();
      } catch (e) {
        const text = await res.text();
        console.error("Server raw response:", text);
        alert("Server error. Check console for details.");
        return;
      }

      if (data.ok) {
        alert(`Order #${data.order_id} placed! Total ₹${Number(data.total).toFixed(2)}`);
        localStorage.removeItem("cart");
        window.location.href = "profile.php";
      } else {
        alert("Checkout failed: " + (data.error || "Unknown error"));
      }
    } catch (err) {
      console.error("Checkout error", err);
      alert("Network error during checkout");
    }
  }

  if (checkoutBtn) checkoutBtn.addEventListener("click", checkout);

  loadCart();
});
