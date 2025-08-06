// Dynamic filtering functionality
document.addEventListener('DOMContentLoaded', function() {
    // Initialize filters
    fetchCategories();
    fetchSizes();
    fetchColors();
    fetchPriceRange();
    loadProducts();

    // Filter form submission
    document.getElementById('filterForm').addEventListener('submit', function(e) {
        e.preventDefault();
        loadProducts();
    });

    // Category change event
    document.addEventListener('change', function(e) {
        if (e.target.name === 'category') {
            fetchColors(e.target.value);
        }
    });

    // Clear filters
    document.getElementById('clearFilters').addEventListener('click', function() {
        document.getElementById('filterForm').reset();
        loadProducts();
    });

    function fetchCategories() {
        fetch('../api/filter-handler.php?type=categories')
        .then(response => response.json())
        .then(categories => {
            let html = '';
            categories.forEach(category => {
                html += `
                    <div class="filter-checkbox">
                        <input type="radio" name="category" value="${category.id}" id="cat${category.id}" class="form-check-input">
                        <label for="cat${category.id}" class="form-check-label">${category.name}</label>
                    </div>
                `;
            });
            document.getElementById('categories').innerHTML = html;
        })
        .catch(error => console.error('Error fetching categories:', error));
    }

    function fetchSizes() {
        fetch('../api/filter-handler.php?type=sizes')
        .then(response => response.json())
        .then(sizes => {
            let html = '';
            sizes.forEach(size => {
                html += `
                    <div class="filter-checkbox">
                        <input type="checkbox" name="size" value="${size}" id="size${size}" class="form-check-input">
                        <label for="size${size}" class="form-check-label">${size}</label>
                    </div>
                `;
            });
            document.getElementById('sizes').innerHTML = html;
        })
        .catch(error => console.error('Error fetching sizes:', error));
    }

    function fetchColors(categoryId = null) {
        let url = '../api/filter-handler.php?type=colors';
        if (categoryId) {
            url += `&category=${categoryId}`;
        }

        fetch(url)
        .then(response => response.json())
        .then(colors => {
            let html = '';
            colors.forEach(color => {
                html += `
                    <div class="filter-checkbox">
                        <input type="checkbox" name="color" value="${color}" id="color${color}" class="form-check-input">
                        <label for="color${color}" class="form-check-label text-capitalize">${color}</label>
                    </div>
                `;
            });
            document.getElementById('colors').innerHTML = html;
        })
        .catch(error => console.error('Error fetching colors:', error));
    }

    function fetchPriceRange() {
        fetch('../api/filter-handler.php?type=price-range')
        .then(response => response.json())
        .then(range => {
            document.getElementById('minPrice').setAttribute('min', range.min_price);
            document.getElementById('maxPrice').setAttribute('max', range.max_price);
            document.getElementById('minPrice').setAttribute('placeholder', `Min: ₹${range.min_price}`);
            document.getElementById('maxPrice').setAttribute('placeholder', `Max: ₹${range.max_price}`);
        })
        .catch(error => console.error('Error fetching price range:', error));
    }

    function loadProducts() {
        const form = document.getElementById('filterForm');
        const formData = new FormData(form);

        // Show loading
        document.getElementById('products').innerHTML = '<div class="text-center"><div class="loading"></div></div>';

        fetch('../api/filter-handler.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(products => {
            const grid = document.getElementById('products');
            const noMsg = document.getElementById('noProductsMsg');

            if (products.length === 0) {
                grid.innerHTML = '';
                noMsg.classList.remove('d-none');
                return;
            }

            noMsg.classList.add('d-none');
            let html = '';

            products.forEach(product => {
                let images = product.image_url ? product.image_url.split(',') : ['assets/images/placeholder.jpg'];
images = images.map(img => {
    img = img.trim().replace(/^\.+\/?/, ''); // Remove leading ./ or ../
    if (!img.startsWith('/rtwrs_web/')) {
        img = '/rtwrs_web/' + img.replace(/^\/+/, '');
    }
    return img;
});
                const isNew = (Date.now() - Date.parse(product.created_at)) < (7 * 24 * 60 * 60 * 1000);
                const isLowStock = product.quantity_available <= 2;

                html += `
                    <div class="col-md-4 col-lg-3 mb-4">
                        <div class="card product-card h-100" data-product='${JSON.stringify(product)}'>
                            <img src="${images[0]}" class="card-img-top" alt="${product.product_name}" style="width:100%; height:auto; max-height:280px; object-fit:contain; border-bottom:1px solid #ddd; cursor:pointer;" onclick="showProductModal(${product.id})">
                            <div class="card-body d-flex flex-column">
                                <h6 class="card-title">${product.product_name}</h6>
                                ${isNew ? '<span class="badge badge-new mb-2">New</span>' : ''}
                                ${isLowStock ? `<span class="badge badge-stock mb-2">Only ${product.quantity_available} Left</span>` : ''}
                                <div class="price-tag mb-2">₹${product.price_per_day}/day</div>
                                <div class="mb-2">
                                    <small class="text-muted">Sizes: ${product.size}</small>
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted">Colors: ${product.color}</small>
                                </div>
                                <div class="mt-auto">
                                    <a href="../user/rent.php?id=${product.id}" class="btn btn-primary w-100">Book Now</a>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });

            grid.innerHTML = html;
        })
        .catch(error => {
            console.error('Error loading products:', error);
            document.getElementById('products').innerHTML = '<div class="alert alert-danger">Error loading products. Please try again.</div>';
        });
    }

    // Add form data to POST request
    FormData.prototype.append = function(name, value) {
        if (this.has(name)) {
            this.delete(name);
        }
        this.set(name, value);
    };
});
