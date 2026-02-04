//added for fetch API
export function loadProducts(category) {
fetch(`../../routes/productRoutes.php?action=byCategory&category=${category}`)
.then(response => response.json())

 .then(products => {

            const grid = document.querySelector(".product-grid");
            const noResults = document.getElementById("no-results");
             grid.innerHTML = "";

            if (!products || products.length === 0) {
                if (noResults) noResults.style.display = "block";
                return;
            }

            if (noResults) noResults.style.display = "none";
            products.forEach(product => {

                const item = document.createElement("div");
                item.className = "item";
                item.dataset.keywords = product.keywords || "";
                item.dataset.category = product.category || "";
                item.dataset.colour = product.colour || "";
                item.dataset.price = product.price || 0;
                item.dataset.rating = product.rating || 0;
                item.dataset.new = product.is_new || false;

  
                item.innerHTML = `
                    <img src="../images/livingroom-images/${product.image}" alt="${product.name}">
                    <div class="product-text">
                        <h2>${product.name}</h2>
                        <p>£${product.price}</p>
                    </div>
                `;
                item.onclick = () => {
                    window.location.href = `product.html?id=${product.id}`;
                };
                grid.appendChild(item);
            });
        })

        .catch(error => console.error("Whoops! Failed to load products:", error));
}
