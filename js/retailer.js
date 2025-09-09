// js/retailer.js
// =======================
// Retailer.js — Stock Catalogue
// =======================

document.addEventListener("DOMContentLoaded", () => {
  const grid = document.getElementById("retailerGrid");
  const msgBox = document.getElementById("retailerMsg");

  function showMsg(msg, ok = true) {
    if (!msgBox) return;
    msgBox.textContent = msg;
    msgBox.style.color = ok ? "green" : "red";
    setTimeout(() => (msgBox.textContent = ""), 3000);
  }

  let CLOTHES_CACHE = [];

  function escapeHtml(s) {
    return String(s || "").replace(/[&<>"']/g, (m) => ({
      "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;"
    }[m]));
  }

  function resolveImagePath(img) {
    if (!img) return "images/arrival3.jpg";
    if (img.startsWith("http") || img.startsWith("backend/uploads/") || img.startsWith("images/")) return img;
    return `backend/uploads/${img}`;
  }

  async function loadClothes() {
    try {
      const res = await fetch("backend/get_clothes.php");
      const data = await res.json();
      if (!data.ok) {
        showMsg("No stock available", false);
        return;
      }

      CLOTHES_CACHE = data.clothes || [];
      if (!grid) return;
      grid.innerHTML = "";

      CLOTHES_CACHE.forEach((c) => {
        const imgSrc = resolveImagePath(c.image);
        const card = document.createElement("div");
        card.className = "card lift";
        card.innerHTML = `
          <img src="${imgSrc}" alt="${escapeHtml(c.name)}" 
               class="card-img" 
               style="width:100%;height:200px;object-fit:cover;border-radius:6px;">
          <h3>${escapeHtml(c.name)}</h3>
          <p class="muted">${escapeHtml(c.brand || "")} ${escapeHtml(c.size || "")} ${escapeHtml(c.color || "")}</p>
          <p><b>₹${Number(c.price).toFixed(2)}</b> | Qty: ${c.quantity}</p>
          <button class="btn-primary add-to-cart" data-id="${c.id}">Add to Cart</button>
        `;
        grid.appendChild(card);
      });

      document.querySelectorAll(".add-to-cart").forEach(btn => {
        btn.addEventListener("click", () => {
          const id = Number(btn.getAttribute("data-id"));
          addToCart(id);
        });
      });

    } catch (err) {
      console.error("Error loading clothes", err);
      showMsg("Failed to load stock", false);
    }
  }

  // ✅ Add to cart with default qty = 100
  window.addToCart = function(id) {
    const item = CLOTHES_CACHE.find(x => Number(x.id) === Number(id));
    if (!item) {
      showMsg("Item not found", false);
      return;
    }

    const cart = JSON.parse(localStorage.getItem("cart") || "[]");
    const existing = cart.find(c => Number(c.id) === Number(item.id));

    if (existing) {
      existing.qty = Math.min((existing.qty || 100) + 100, Number(item.quantity || 99999));
    } else {
      cart.push({
        id: Number(item.id),
        name: item.name,
        price: Number(item.price) || 0,
        qty: 100,
        image: resolveImagePath(item.image)
      });
    }

    localStorage.setItem("cart", JSON.stringify(cart));
    showMsg("Item added to cart ✅");
  };

  loadClothes();
});
