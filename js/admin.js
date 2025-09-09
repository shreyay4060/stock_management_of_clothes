// js/admin.js
// =======================
// Admin — Manage Clothes
// =======================

const addClothForm = document.getElementById("addClothForm");
const clothesTable = document.getElementById("clothesTable");

async function apiCall(endpoint, dataObj = {}, isFile = false) {
  try {
    let options = { method: "POST", credentials: "include" };

    if (isFile) {
      options.body = dataObj;
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

function escapeHtml(s) {
  return String(s || "").replace(/[&<>"']/g, (m) =>
    ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[m])
  );
}

function resolveImage(img, name = "no image") {
  if (img) {
    if (!img.startsWith("backend/uploads/") && !img.startsWith("http") && !img.startsWith("images/")) {
      img = "backend/uploads/" + img;
    }
    return `<img src="${img}" alt="${escapeHtml(name)}"
             style="width:60px;height:60px;object-fit:cover;border-radius:4px;">`;
  }
  return `<img src="images/arrival3.jpg" alt="${escapeHtml(name)}"
           style="width:60px;height:60px;object-fit:cover;border-radius:4px;">`;
}

addClothForm?.addEventListener("submit", async (e) => {
  e.preventDefault();
  const fd = new FormData(addClothForm);
  const res = await apiCall("add_cloth", fd, true);

  if (res.ok) {
    alert("Cloth added successfully!");
    addClothForm.reset();
    loadClothes();
  } else {
    alert("Failed to add cloth: " + (res.error || "unknown"));
  }
});

async function loadClothes() {
  const res = await apiCall("get_clothes", {});
  clothesTable.innerHTML = "";

  if (res.ok && res.clothes?.length > 0) {
    res.clothes.forEach((cloth) => {
      const tr = document.createElement("tr");
      tr.innerHTML = `
        <td>${cloth.id}</td>
        <td><input type="text" value="${escapeHtml(cloth.name || "")}" data-field="name" data-id="${cloth.id}" disabled></td>
        <td><input type="text" value="${escapeHtml(cloth.brand || "")}" data-field="brand" data-id="${cloth.id}" disabled></td>
        <td><input type="text" value="${escapeHtml(cloth.size || "")}" data-field="size" data-id="${cloth.id}" disabled></td>
        <td><input type="text" value="${escapeHtml(cloth.color || "")}" data-field="color" data-id="${cloth.id}" disabled></td>
        <td><input type="number" step="0.01" value="${Number(cloth.price || 0).toFixed(2)}" data-field="price" data-id="${cloth.id}" disabled></td>
        <td><input type="number" value="${parseInt(cloth.quantity || 0)}" data-field="quantity" data-id="${cloth.id}" disabled></td>
        <td>${resolveImage(cloth.image, cloth.name)}</td>
        <td>
          <button class="edit-btn btn-warning" data-id="${cloth.id}">Edit</button>
          <button class="save-btn btn-success" data-id="${cloth.id}" style="display:none;">Save</button>
          <button class="delete-btn btn-danger" data-id="${cloth.id}">Delete</button>
        </td>
      `;
      clothesTable.appendChild(tr);
    });

    bindActions();
  } else {
    clothesTable.innerHTML = `<tr><td colspan="9">No clothes available.</td></tr>`;
  }
}

function bindActions() {
  document.querySelectorAll(".edit-btn").forEach((btn) => {
    btn.addEventListener("click", () => {
      toggleEditMode(btn.dataset.id, true);
    });
  });

  document.querySelectorAll(".save-btn").forEach((btn) => {
    btn.addEventListener("click", async () => {
      const id = btn.dataset.id;
      const fields = document.querySelectorAll(`[data-id="${id}"]`);
      let updateData = { id };
      fields.forEach((f) => {
        const fld = f.dataset.field;
        if (fld) updateData[fld] = f.value;
      });

      const res = await apiCall("update_cloth", updateData);
      if (res.ok) {
        alert("Updated successfully!");
        toggleEditMode(id, false);
        loadClothes();
      } else {
        alert("Update failed: " + (res.error || "unknown"));
      }
    });
  });

  document.querySelectorAll(".delete-btn").forEach((btn) => {
    btn.addEventListener("click", async () => {
      if (!confirm("Delete this cloth?")) return;
      const id = btn.dataset.id;
      const res = await apiCall("delete_cloth", { id });
      if (res.ok) {
        alert("Deleted successfully!");
        loadClothes();
      } else {
        alert("Delete failed: " + (res.error || "unknown"));
      }
    });
  });
}

function toggleEditMode(id, enable) {
  const fields = document.querySelectorAll(`[data-id="${id}"]`);
  fields.forEach((f) => (f.disabled = !enable));

  const editBtn = document.querySelector(`.edit-btn[data-id="${id}"]`);
  const saveBtn = document.querySelector(`.save-btn[data-id="${id}"]`);

  if (enable) {
    if (editBtn) editBtn.style.display = "none";
    if (saveBtn) saveBtn.style.display = "inline-block";
  } else {
    if (editBtn) editBtn.style.display = "inline-block";
    if (saveBtn) saveBtn.style.display = "none";
  }
}

loadClothes();
