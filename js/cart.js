// js/cart.js
document.addEventListener("DOMContentLoaded", () => {
  const tbody = document.getElementById("cartTableBody");
  const totalBox = document.getElementById("cartTotal");
  const checkoutBtn = document.getElementById("checkoutBtn");
  const addressModal = document.getElementById("addressModal");
  const addressForm = document.getElementById("addressForm");

  function escapeHtml(s) {
    return String(s || "").replace(/[&<>"']/g, (m) => ({
      "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;"
    }[m]));
  }

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
    tbody.innerHTML = "";
    let total = 0;

    if (!cart.length) {
      tbody.innerHTML = `<tr><td colspan="6" class="muted">Your cart is empty.</td></tr>`;
      totalBox.textContent = "₹0.00";
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

    totalBox.textContent = "₹" + total.toFixed(2);
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

  // ✅ Modal handlers
  window.closeAddressModal = function() {
    addressModal.classList.add("hidden");
  };
  function openAddressModal() {
    addressModal.classList.remove("hidden");
  }

  // ✅ Checkout flow
  async function checkout() {
    const cart = JSON.parse(localStorage.getItem("cart") || "[]");
    if (!cart.length) return alert("Cart is empty!");

    // open modal (instead of redirect)
    openAddressModal();

    // prefill address if exists
    try {
      const addrRes = await fetch("backend/get_address.php", { credentials: "include" });
      const addrData = await addrRes.json();
      if (addrData.ok && addrData.address) {
        for (let key in addrData.address) {
          if (addressForm.elements[key]) {
            addressForm.elements[key].value = addrData.address[key] || "";
          }
        }
      }
    } catch (e) {
      console.warn("Could not fetch saved address", e);
    }
  }

  if (checkoutBtn) checkoutBtn.addEventListener("click", checkout);

  // ✅ Save address + place order
  addressForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    const cart = JSON.parse(localStorage.getItem("cart") || "[]");
    if (!cart.length) return alert("Cart is empty!");

    const data = Object.fromEntries(new FormData(addressForm).entries());

    try {
      // save/update address
      const res = await fetch("backend/save_address.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data),
        credentials: "include"
      });
      const out = await res.json();
      if (!out.ok) return alert("Failed to save address: " + out.error);

      const address_id = out.address_id;

      // place order
      const orderRes = await fetch("backend/place_order.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          items: cart.map(it => ({ id: it.id, qty: it.qty })),
          address_id: address_id
        }),
        credentials: "include"
      });
      const orderOut = await orderRes.json();

      if (orderOut.ok) {
        alert(`Order #${orderOut.order_id} placed successfully! Total ₹${Number(orderOut.total).toFixed(2)}\nPayment Method: Cash on Delivery`);
        localStorage.removeItem("cart");
        window.location.href = "profile.php";
      } else {
        alert("Order failed: " + (orderOut.error || "Unknown error"));
      }
    } catch (err) {
      console.error("Address/order error", err);
      alert("Network error while placing order.");
    }
  });

  loadCart();
});
