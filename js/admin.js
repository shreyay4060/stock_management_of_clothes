// js/admin.js

const addClothForm = document.getElementById("addClothForm");
const clothesTable = document.getElementById("clothesTable");

// ========== Helpers ==========
async function apiCall(endpoint, dataObj = {}, isFile = false) {
  try {
    let options = { method: "POST", credentials: "include" };

    if (isFile) {
      options.body = dataObj; // already FormData
    } else {
      if (endpoint === "update_cloth") {
        options.headers = { "Content-Type": "application/json" };
        options.body = JSON.stringify(dataObj);
      } else {
        const fd = new FormData();
        for (let k in dataObj) fd.append(k, dataObj[k]);
        options.body = fd;
      }
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
  clothesTable.innerHTML = "";

  if (res.ok && res.clothes.length > 0) {
    res.clothes.forEach((cloth) => {
      const tr = document.createElement("tr");

      tr.innerHTML = `
        <td>${cloth.id}</td>
        <td><input type="text" value="${cloth.name}" data-field="name" data-id="${cloth.id}" disabled></td>
        <td><input type="text" value="${cloth.brand || ""}" data-field="brand" data-id="${cloth.id}" disabled></td>
        <td><input type="text" value="${cloth.size || ""}" data-field="size" data-id="${cloth.id}" disabled></td>
        <td><input type="text" value="${cloth.color || ""}" data-field="color" data-id="${cloth.id}" disabled></td>
        <td><input type="number" step="0.01" value="${cloth.price}" data-field="price" data-id="${cloth.id}" disabled></td>
        <td><input type="number" value="${cloth.quantity}" data-field="quantity" data-id="${cloth.id}" disabled></td>
        <td>
          <img src="${cloth.image || 'images/arrival3.jpg'}" 
               alt="${cloth.name}" 
               style="width:60px;height:60px;object-fit:cover;border-radius:4px;">
        </td>
        <td>
          <button class="edit-btn btn-warning" data-id="${cloth.id}">Edit</button>
          <button class="save-btn btn-success" data-id="${cloth.id}" style="display:none;">Save</button>
          <button class="delete-btn btn-danger" data-id="${cloth.id}">Delete</button>
        </td>
      `;
      clothesTable.appendChild(tr);
    });

    // Bind edit events
    document.querySelectorAll(".edit-btn").forEach((btn) => {
      btn.addEventListener("click", () => toggleEditMode(btn.dataset.id, true));
    });

    // Bind save events
    document.querySelectorAll(".save-btn").forEach((btn) => {
      btn.addEventListener("click", async () => {
        const id = btn.dataset.id;
        const fields = document.querySelectorAll(`[data-id="${id}"]`);
        let updateData = { id };
        fields.forEach((f) => (updateData[f.dataset.field] = f.value));

        const res = await apiCall("update_cloth", updateData);
        if (res.ok) {
          alert("Updated successfully!");
          toggleEditMode(id, false);
          loadClothes();
        } else {
          alert("Update failed: " + res.error);
        }
      });
    });

    // Bind delete events
    document.querySelectorAll(".delete-btn").forEach((btn) => {
      btn.addEventListener("click", async () => {
        if (!confirm("Delete this cloth?")) return;
        const res = await apiCall("delete_cloth", { id: btn.dataset.id });
        if (res.ok) {
          alert("Deleted successfully!");
          loadClothes();
        } else {
          alert("Delete failed: " + res.error);
        }
      });
    });
  } else {
    clothesTable.innerHTML = `<tr><td colspan="9">No clothes available.</td></tr>`;
  }
}

// Helper: Toggle edit mode
function toggleEditMode(id, enable) {
  const fields = document.querySelectorAll(`[data-id="${id}"]`);
  fields.forEach((f) => (f.disabled = !enable));

  const editBtn = document.querySelector(`.edit-btn[data-id="${id}"]`);
  const saveBtn = document.querySelector(`.save-btn[data-id="${id}"]`);

  editBtn.style.display = enable ? "none" : "inline-block";
  saveBtn.style.display = enable ? "inline-block" : "none";
}

// Initial load
loadClothes();
