// =======================
// Retailer.js — Stock Catalogue
// =======================

const grid = document.getElementById("retailerGrid");
const msgBox = document.getElementById("retailerMsg");

// ✅ Show messages
function showMsg(msg, ok = true) {
  msgBox.textContent = msg;
  msgBox.style.color = ok ? "green" : "red";
  setTimeout(() => (msgBox.textContent = ""), 3000);
}

// ✅ Load clothes from backend
async function loadClothes() {
  try {
    const res = await fetch("backend/get_clothes.php");
    const data = await res.json();
    if (data.ok) {
      grid.innerHTML = "";
      data.clothes.forEach((c) => {
        const card = document.createElement("div");
        card.className = "card lift";
        card.innerHTML = `
          <img src="${c.image || 'images/arrival3.jpg'}" alt="${c.name}" 
               class="card-img" 
               style="width:100%;height:200px;object-fit:cover;border-radius:6px;">
          <h3>${c.name}</h3>
          <p class="muted">${c.brand || ""} ${c.size || ""} ${c.color || ""}</p>
          <p><b>₹${c.price}</b> | Qty: ${c.quantity}</p>
          <button class="btn-primary" onclick="addToCart(${c.id})">Add to Cart</button>
        `;
        grid.appendChild(card);
      });
    } else {
      showMsg("No stock available", false);
    }
  } catch (err) {
    console.error("Error loading clothes", err);
    showMsg("Failed to load stock", false);
  }
}

// ✅ Add to cart
async function addToCart(id) {
  try {
    const res = await fetch("backend/add_to_cart.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ cloth_id: id, qty: 1 }),
    });
    const data = await res.json();
    if (data.ok) {
      showMsg("Item added to cart ✅");
    } else {
      showMsg(data.error || "Could not add to cart", false);
    }
  } catch (err) {
    console.error("Cart error", err);
    showMsg("Error adding to cart", false);
  }
}

// ✅ Init
document.addEventListener("DOMContentLoaded", loadClothes);
