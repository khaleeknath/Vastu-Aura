document.addEventListener('DOMContentLoaded', function () {

    const messageBox = document.getElementById('productMessage');
  
    function showMessage(text, isError) {
      if (!messageBox) return;
      messageBox.textContent = text;
      messageBox.classList.remove('d-none');
      messageBox.classList.toggle('alert-danger', !!isError);
      messageBox.classList.toggle('alert-success', !isError);
    }
  
    function reloadPage() {
      setTimeout(() => window.location.reload(), 600);
    }
  
    // ---------------- Add Category ----------------
    const addCategoryModalEl = document.getElementById('addCategoryModal');
    const addCategoryModal = addCategoryModalEl ? new bootstrap.Modal(addCategoryModalEl) : null;
  
    document.getElementById('openAddCategory')?.addEventListener('click', () => {
      document.getElementById('newCategoryName').value = '';
      addCategoryModal?.show();
    });
  
    document.getElementById('saveCategoryBtn')?.addEventListener('click', async () => {
      const name = document.getElementById('newCategoryName').value.trim();
      if (!name) {
        alert('Please enter a category name.');
        return;
      }
  
      const btn = document.getElementById('saveCategoryBtn');
      btn.disabled = true;
      btn.textContent = 'Saving...';
  
      try {
        const formData = new FormData();
        formData.append('name', name);
  
        const res = await fetch('assets/api/add-category.php', {
          method: 'POST',
          body: formData
        });
        const data = await res.json();
  
        if (data.success) {
          addCategoryModal?.hide();
          showMessage(data.message, false);
          reloadPage();
        } else {
          alert(data.message || 'Failed to add category.');
        }
      } catch (err) {
        alert('Something went wrong while adding the category.');
      } finally {
        btn.disabled = false;
        btn.textContent = 'Save Category';
      }
    });
  
    // ---------------- Add Product ----------------
    const addProductModalEl = document.getElementById('addProductModal');
    const addProductModal = addProductModalEl ? new bootstrap.Modal(addProductModalEl) : null;
  
    document.getElementById('openAddProduct')?.addEventListener('click', () => {
      document.getElementById('addProductForm').reset();
      addProductModal?.show();
    });
  
    document.getElementById('saveProductBtn')?.addEventListener('click', async () => {
      const form = document.getElementById('addProductForm');
  
      if (!form.checkValidity()) {
        form.reportValidity();
        return;
      }
  
      const btn = document.getElementById('saveProductBtn');
      btn.disabled = true;
      btn.textContent = 'Saving...';
  
      try {
        const formData = new FormData(form);
  
        const res = await fetch('assets/api/add-product.php', {
          method: 'POST',
          body: formData
        });
        const data = await res.json();
  
        if (data.success) {
          addProductModal?.hide();
          showMessage(data.message, false);
          reloadPage();
        } else {
          alert(data.message || 'Failed to add product.');
        }
      } catch (err) {
        alert('Something went wrong while adding the product.');
      } finally {
        btn.disabled = false;
        btn.textContent = 'Save Product';
      }
    });
  
    // ---------------- View / Edit Product ----------------
    const productModalEl = document.getElementById('productModal');
    const productModal = productModalEl ? new bootstrap.Modal(productModalEl) : null;
  
    document.querySelectorAll('.view-product').forEach(btn => {
      btn.addEventListener('click', () => {
        document.getElementById('modalProductId').value = btn.dataset.id;
        document.getElementById('modalName').value = btn.dataset.name;
        document.getElementById('modalDescription').value = btn.dataset.description;
        document.getElementById('modalDetails').value = btn.dataset.details;
        document.getElementById('modalPrice').value = btn.dataset.price;
        document.getElementById('modalStock').value = btn.dataset.stock;
        document.getElementById('modalProductStatus').value = btn.dataset.status;
        document.getElementById('modalCategoryId').value = btn.dataset.categoryId;
        document.getElementById('modalImage').value = '';
  
        productModal?.show();
      });
    });
  
    document.getElementById('modalUpdateBtn')?.addEventListener('click', async () => {
      const btn = document.getElementById('modalUpdateBtn');
      btn.disabled = true;
      btn.textContent = 'Saving...';
  
      try {
        const formData = new FormData();
        formData.append('id', document.getElementById('modalProductId').value);
        formData.append('name', document.getElementById('modalName').value.trim());
        formData.append('description', document.getElementById('modalDescription').value.trim());
        formData.append('details', document.getElementById('modalDetails').value.trim());
        formData.append('price', document.getElementById('modalPrice').value);
        formData.append('stock', document.getElementById('modalStock').value);
        formData.append('category_id', document.getElementById('modalCategoryId').value);
        formData.append('status', document.getElementById('modalProductStatus').value);
  
        const imageFile = document.getElementById('modalImage').files[0];
        if (imageFile) {
          formData.append('image', imageFile);
        }
  
        const res = await fetch('assets/api/update-product.php', {
          method: 'POST',
          body: formData
        });
        const data = await res.json();
  
        if (data.success) {
          productModal?.hide();
          showMessage(data.message, false);
          reloadPage();
        } else {
          alert(data.message || 'Failed to update product.');
        }
      } catch (err) {
        alert('Something went wrong while updating the product.');
      } finally {
        btn.disabled = false;
        btn.textContent = 'Save Changes';
      }
    });
  
    document.getElementById('modalDeleteBtn')?.addEventListener('click', async () => {
      const id = document.getElementById('modalProductId').value;
      if (!confirm('Delete this product? This cannot be undone.')) return;
  
      const btn = document.getElementById('modalDeleteBtn');
      btn.disabled = true;
      btn.textContent = 'Deleting...';
  
      try {
        const formData = new FormData();
        formData.append('id', id);
  
        const res = await fetch('assets/api/delete-product.php', {
          method: 'POST',
          body: formData
        });
        const data = await res.json();
  
        if (data.success) {
          productModal?.hide();
          showMessage(data.message, false);
          reloadPage();
        } else {
          alert(data.message || 'Failed to delete product.');
        }
      } catch (err) {
        alert('Something went wrong while deleting the product.');
      } finally {
        btn.disabled = false;
        btn.textContent = 'Delete';
      }
    });
  
    // ---------------- Logout (kept consistent with appointments page) ----------------
    document.getElementById('sidebarLogout')?.addEventListener('click', function (e) {
      e.preventDefault();
      window.location.href = 'logout.php';
    });
    document.getElementById('topbarLogout')?.addEventListener('click', function () {
      window.location.href = 'logout.php';
    });
  
  });