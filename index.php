<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopSAGE - Product Page</title>

    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <!-- Stylesheet -->
    <link rel="stylesheet" href="shiv.css">
    <style>
        .brand_name {
            position: relative;
            top: 2px;
            display: flex;
        }
    </style>
</head>

<body>
<section class="Feature_prod_section">
    <div class="Feature_prod_area">
        <h1 class="Feature_prod_heading">Featured Products</h1>
        <p class="Feature_prod_para">All Brand New Collection of Best Selling Products</p>
        <div class="container Featured_product_con">
            <div class="wrapper_fea_prod">
                <?php
                // Database connection
                $conn = new mysqli('localhost', 'root', '', 'merapyareusers');

                // Check connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Fetch products from the database
                $result = $conn->query("SELECT * FROM product_details");

                while ($row = $result->fetch_assoc()) {
                    // Decode sizes JSON data
                    $sizes = json_decode($row['sizes'], true);
                    $size_options = '';

                    if (is_array($sizes)) {
                        foreach ($sizes as $size) {
                            $size_options .= '<option value="' . htmlspecialchars($size, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($size, ENT_QUOTES, 'UTF-8') . '</option>';
                        }
                    } else {
                        $size_options = '<option value="">No sizes available</option>';
                    }

                    // Decode colors JSON data
                    $colors = json_decode($row['colors'], true);
                    $color_options = '';

                    if (is_array($colors)) {
                        foreach ($colors as $color) {
                            $color_options .= '<option value="' . htmlspecialchars($color, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($color, ENT_QUOTES, 'UTF-8') . '</option>';
                        }
                    } else {
                        $color_options = '<option value="">No colors available</option>';
                    }

                    echo '
                    <div class="Feature_flexcol product" data-id="' . $row['id'] . '">
                        <div class="product">
                            <a href="product detals/product_detail.html?id=' . $row['id'] . '">
                                <img src="uploads/' . htmlspecialchars($row['image_url_1'], ENT_QUOTES, 'UTF-8') . '" alt="' . htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') . '" class="product_image">
                            </a>
                        </div>
                        <div class="description">
                            <span class="brand_name">' . htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') . '</span>
                            <p class="product_name1">' . htmlspecialchars($row['description'], ENT_QUOTES, 'UTF-8') . '</p>
                            <div class="stars">
                                <i class="fa-solid fa-star" style="color: #FFD43B;"></i>
                                <i class="fa-solid fa-star" style="color: #FFD43B;"></i>
                                <i class="fa-solid fa-star" style="color: #FFD43B;"></i>
                                <i class="fa-solid fa-star" style="color: #FFD43B;"></i>
                                <i class="fa-solid fa-star" style="color: #FFD43B;"></i>
                                <label for="size-select">Size:</label>
                                <select id="size-select" class="size-select">
                                    ' . $size_options . '
                                </select>
                                <label for="color-select">Color:</label>
                                <select id="color-select" class="color-select">
                                    ' . $color_options . '
                                </select>
                            </div>
                            <div class="fea_pro_add_to_cart">
                                <a href="#" class="add-to-cart" data-id="' . $row['id'] . '" data-name="' . htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') . '" data-price="' . htmlspecialchars($row['price'], ENT_QUOTES, 'UTF-8') . '" data-image="uploads/' . htmlspecialchars($row['image_url_1'], ENT_QUOTES, 'UTF-8') . '">
                                    <i class="fa-solid fa-cart-plus" style="color: #000000;"></i>
                                </a>
                            </div>
                            <div>
                                <h4 class="product_pricing">' . htmlspecialchars($row['price'], ENT_QUOTES, 'UTF-8') . 'rs</h4>
                            </div>
                        </div>
                    </div>';
                }

                $conn->close();
                ?>
            </div>
        </div>
    </div>
</section>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>
<script>
  document.querySelectorAll('.add-to-cart').forEach(button => {
    button.addEventListener('click', (e) => {
        e.preventDefault();
        console.log('Add to Cart button clicked'); // Add this line for debugging

        let productElement = button.closest('.product'); // Find the parent element
        let sizeSelect = productElement.querySelector('.size-select'); // Get the selected size
        let colorSelect = productElement.querySelector('.color-select'); // Get the selected color

        let product = {
            product_id: button.getAttribute('data-id'),
            product_name: button.getAttribute('data-name'),
            product_price: button.getAttribute('data-price'),
            product_image: button.getAttribute('data-image'),
            quantity: 1,
            size: sizeSelect ? sizeSelect.value : '', // Get the selected size value
            color: colorSelect ? colorSelect.value : '' // Get the selected color value
        };
        
        addToCart(product);
    });
});

function addToCart(product) {
    fetch('cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `product_id=${product.product_id}&product_name=${product.product_name}&product_price=${product.product_price}&product_image=${product.product_image}&quantity=${product.quantity}&size=${product.size}&color=${product.color}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            renderCart();
        } else {
            alert(data.message);
        }
    });
}

function renderCart() {
    fetch('cart.php')
        .then(response => response.json())
        .then(cartItems => {
            const cartSection = document.querySelector('.cart-section');
            cartSection.innerHTML = '';

            let totalPrice = 0;

            cartItems.forEach((item) => {
                const cartItem = document.createElement('div');
                cartItem.classList.add('cart-item');
                cartItem.innerHTML = `
                    <img src="${item.product_image}" alt="${item.product_name}" class="cart-item-image">
                    <div class="cart-item-details">
                        <h3>${item.product_name}</h3>
                        <p>Size: ${item.size}</p> <!-- Display selected size -->
                        <p>Color: ${item.color}</p> <!-- Display selected color -->
                        <p class="cart-item-price">₹${item.product_price}</p>
                        <div class="quantity-controls">
                            <button class="decrease-quantity" data-id="${item.product_id}">-</button>
                            <input type="number" value="${item.quantity}" min="1" class="quantity-input" data-id="${item.product_id}">
                            <button class="increase-quantity" data-id="${item.product_id}">+</button>
                        </div>
                        <button class="remove-from-cart" data-id="${item.product_id}">Remove</button>
                    </div>
                `;
                cartSection.appendChild(cartItem);

                totalPrice += parseFloat(item.product_price) * item.quantity;
            });

            document.getElementById('total-price').textContent = `₹${totalPrice.toFixed(2)}`;

            addRemoveFunctionality();
            addQuantityFunctionality();
        });
}

function addRemoveFunctionality() {
  document.querySelectorAll('.remove-from-cart').forEach(button => {
      button.addEventListener('click', (e) => {
          let product_id = button.getAttribute('data-id');
          removeFromCart(product_id);
      });
  });
}

function removeFromCart(product_id) {
  fetch('cart.php', {
      method: 'DELETE',
      headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: `product_id=${product_id}`
  })
  .then(response => response.json())
  .then(data => {
      if (data.status === 'success') {
          renderCart();
      } else {
          alert(data.message);
      }
  });
}
</script>
</body>
</html>
