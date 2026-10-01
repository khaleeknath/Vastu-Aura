document.addEventListener('DOMContentLoaded', function () {

    const messageBox = document.getElementById('productMessage');
    const IMAGE_BASE_PATH = 'assets/uploads/products/';
    let removedImageIds = [];

    function renderProductGallery(images) {
      const gallery = document.getElementById('modalImageGallery');
      gallery.innerHTML = '';
     
      images.forEach(img => {
        const wrap = document.createElement('div');
        wrap.className = 'position-relative';
        wrap.style.width = '64px';
        wrap.style.height = '64px';
        wrap.dataset.imageId = img.id;
     
        wrap.innerHTML = `
        <img src="${IMAGE_BASE_PATH}${img.image}" alt=""
             style="width:64px;height:64px;object-fit:cover;border-radius:6px;">
        <button type="button" class="btn bg-danger text-white rounded-circle remove-gallery-image p-0
                                      d-flex align-items-center justify-content-center"
                style="position:absolute;top:-6px;right:-6px;width:18px;height:18px;
                       line-height:1;font-size:14px;border:none;"
                data-id="${img.id}" aria-label="Remove">&times;</button>
      `;
        gallery.appendChild(wrap);
      });
     
      gallery.querySelectorAll('.remove-gallery-image').forEach(btn => {
        btn.addEventListener('click', () => {
          const id = Number(btn.dataset.id);
          removedImageIds.push(id);
          btn.closest('[data-image-id]')?.remove();
        });
      });
    }

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
     
        // NEW: reset removal tracking + new-file input, then render existing images
        removedImageIds = [];
        document.getElementById('modalNewImages').value = '';
        const images = JSON.parse(btn.dataset.images || '[]');
        renderProductGallery(images);
     
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
  
        // const imageFile = document.getElementById('modalImage').files[0];
        // if (imageFile) {
        //   formData.append('image', imageFile);
        // }

        removedImageIds.forEach(id => formData.append('removed_images[]', id));
 
        const newImageFiles = document.getElementById('modalNewImages').files;
        Array.from(newImageFiles).forEach(file => {
          formData.append('images[]', file);
        });
  
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
  
   
  
  });

  // ---------------- Refill Stock ----------------
const refillStockModalEl = document.getElementById('refillStockModal');
const refillStockModal = refillStockModalEl ? new bootstrap.Modal(refillStockModalEl) : null;

document.querySelectorAll('.refill-stock').forEach(btn => {
  btn.addEventListener('click', () => {
    document.getElementById('refillProductId').value = btn.dataset.id;
    document.getElementById('refillProductName').textContent = btn.dataset.name;
    document.getElementById('refillQuantity').value = '';
    refillStockModal?.show();
  });
});

document.getElementById('refillSaveBtn')?.addEventListener('click', async () => {
  const quantity = document.getElementById('refillQuantity').value;

  if (!quantity || Number(quantity) <= 0) {
    alert('Please enter a valid quantity.');
    return;
  }

  const btn = document.getElementById('refillSaveBtn');
  btn.disabled = true;
  btn.textContent = 'Saving...';

  try {
    const formData = new FormData();
    formData.append('product_id', document.getElementById('refillProductId').value);
    formData.append('quantity', quantity);

    const res = await fetch('assets/api/refill-stock.php', { method: 'POST', body: formData });
    const data = await res.json();
      console.log("Datag Success", data.success)
    if (data.success) {
      refillStockModal?.hide();
      showMessage(`${data.message} New stock: ${data.new_stock}`, false);
      reloadPage();
    } else {
      alert(data.message || 'Failed to refill stock.');
    }
  } catch (err) {
    alert('Something went wrong while refilling stock.');
  } finally {
    btn.disabled = false;
    btn.textContent = 'Add Stock';
  }
});