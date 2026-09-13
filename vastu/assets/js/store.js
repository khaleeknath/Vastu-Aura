document.addEventListener('DOMContentLoaded', () => {
    loadCategories();
    loadProducts(0);

    document.getElementById('categoryFilterGroup').addEventListener('click', (e) => {
        const btn = e.target.closest('.filter-btn');
        if (!btn) return;

        document.querySelectorAll('#categoryFilterGroup .filter-btn')
            .forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        const categoryId = parseInt(btn.dataset.categoryId, 10) || 0;
        loadProducts(categoryId);
    });
});

function loadCategories() {
    fetch('assets/api/categoryList.php')
        .then(res => res.json())
        .then(result => {
            const group = document.getElementById('categoryFilterGroup');
            result.data.forEach(cat => {
                const btn = document.createElement('button');
                btn.className = 'filter-btn';
                btn.dataset.categoryId = cat.id;
                btn.textContent = cat.name;
                group.appendChild(btn);
            });
        })
        .catch(error => console.error('ERROR loading categories:', error));
}

function loadProducts(categoryId = 0) {
    const url = categoryId > 0
        ? `assets/api/productList.php?category_id=${categoryId}`
        : 'assets/api/productList.php';
        
    fetch(url)
        .then(response => response.json())
        .then(result => {
            console.log("cate",result )
            document.getElementById('cartCount').innerText = result.cartCount;

            let html = '';

            if (result.data.length === 0) {
                html = '<p class="text-muted">No products found in this category.</p>';
            }

            result.data.forEach(product => {
                html += `
                    <div class="col-md-6 col-xl-4 product-item">
                        <article class="product-card">
                            <div class="product-art">
                                ${product.category_name}
                            </div>
                            <span class="product-tag">
                                ${product.category_name}
                            </span>
                            <h3>${product.name}</h3>
                            <p>${product.description || ''}</p>
                            <div class="product-meta">
                                <strong>₹${product.price}</strong>
                                <a href="product-detail.php?id=${product.id}">Details</a>
                            </div>
                        </article>
                    </div>
                `;
            });
            document.getElementById('productGrid').innerHTML = html;
        })
        .catch(error => console.error('ERROR:', error));
}