
  
document.addEventListener('DOMContentLoaded', loadProducts);

function loadProducts() {

    fetch('assets/api/productList.php')
        .then(response => {
            
            return response.json();
        })
        .then(result => {
            
            document.getElementById('cartCount').innerText = result.cartCount;

            let html = '';

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
            
                            <p>
                                ${product.description || ''}
                            </p>
            
                            <div class="product-meta">
                                <strong>₹${product.price}</strong>
            
                                <a href="product-detail.php?id=${product.id}">
                                    Details
                                </a>
                            </div>
            
                          
            
                        </article>
                    </div>
                `;
            });
            document.getElementById('productGrid').innerHTML = html;

        })
        .catch(error => {
            console.error("ERROR:", error);
        });
}




