// js/admin.js

const addClothForm = document.getElementById("addClothForm");
const clothesList = document.getElementById("clothes-list");

// ========== Helpers ==========
async function apiCall(endpoint, dataObj = {}, isFile = false) {
  try {
    let options = { method: "POST", credentials: "include" };

    if (isFile) {
      options.body = dataObj; // already FormData
    } else {
      const fd = new FormData();
      for (let k in dataObj) fd.append(k, dataObj[k]);
      options.body = fd;
    }

    const res = await fetch(`backend/${endpoint}.php`, options);
    return await res.json();
  } catch (err) {
    console.error("API error", err);
    return { ok: false, error: "Network error" };
  }
}

// ========== Add Cloth ==========
addClothForm?.addEventListener("submit", async (e) => {
  e.preventDefault();

  const fd = new FormData(addClothForm);
  const res = await apiCall("add_cloth", fd, true);

  if (res.ok) {
    alert("Cloth added successfully!");
    addClothForm.reset();
    loadClothes();
  } else {
    alert("Failed to add cloth: " + res.error);
  }
});

// ========== Load Clothes ==========
async function loadClothes() {
  const res = await apiCall("get_clothes", {});
  clothesList.innerHTML = "";

  if (res.ok && res.clothes.length > 0) {
    res.clothes.forEach(cloth => {
      const card = document.createElement("div");
      card.className = "cloth-card";

      card.innerHTML = `
        <img src="backend/uploads/${cloth.image || "no-image.png"}" alt="${cloth.name}" class="cloth-img">
        <h3>${cloth.name}</h3>
        <p><b>Brand:</b> ${cloth.brand || "-"}</p>
        <p><b>Size:</b> ${cloth.size || "-"}</p>
        <p><b>Color:</b> ${cloth.color || "-"}</p>
        <p><b>Price:</b> ₹${cloth.price}</p>
        <p><b>Quantity:</b> ${cloth.quantity}</p>
        <button class="delete-btn" data-id="${cloth.id}">Delete</button>
      `;
      clothesList.appendChild(card);
    });

    // Bind delete events
    document.querySelectorAll(".delete-btn").forEach(btn => {
      btn.addEventListener("click", async () => {
        if (!confirm("Delete this cloth?")) return;
        const id = btn.getAttribute("data-id");
        const res = await apiCall("delete_cloth", { id });
        if (res.ok) {
          alert("Deleted successfully!");
          loadClothes();
        } else {
          alert("Delete failed: " + res.error);
        }
      });
    });

  } else {
    clothesList.innerHTML = "<p>No clothes available.</p>";
  }
}

// Initial load
loadClothes();
