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
    <!-- <link rel="stylesheet" href="shiv.css"> -->
    <style>
        .brand_name {
            position: relative;
            top: 2px;
            display: flex;
        }
        
      
  /* Global Styles */
  body {
    font-family: 'League Spartan', 'Roboto', sans-serif;
    background-color: #f4f4f9;
    margin: 0;
    padding: 0;
    color: #333;
  }

  h1, h2, h3, h4, p {
    margin: 0 0 20px 0;
  }

  /* Featured Product Section */
  .Feature_prod_area {
    padding: 60px 0;
    background-color: #fff;
    text-align: center;
  }

  .Feature_prod_heading {
    font-size: 2.5rem;
    font-weight: bold;
    color: #333;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 10px;
  }

  .Feature_prod_para {
    font-size: 1.2rem;
    color: #777;
  }

  .wrapper_fea_prod {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
    padding: 40px 0;
  }

  .product {
    background-color: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    position: relative;
    transition: transform 0.3s ease;
  }

  .product:hover {
    transform: translateY(-10px);
  }

  .product img {
    width: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
  }

  .product:hover img {
    transform: scale(1.05);
  }

  .description {
    padding: 20px;
    text-align: left;
  }

  .brand_name {
    font-size: 1rem;
    color: #555;
    text-transform: uppercase;
    letter-spacing: 1px;
  }

  .product_name1 {
    font-size: 1.2rem;
    font-weight: bold;
    color: #333;
    margin: 5px 0;
  }

  .product_pricing {
    font-size: 1.5rem;
    color: #00adb5;
    font-weight: bold;
  }

  .fea_pro_add_to_cart {
    position: absolute;
    bottom: 20px;
    right: 20px;
  }

  .fea_pro_add_to_cart a {
    background-color: #FF6F3C;
    color: #fff;
    padding: 10px 15px;
    border-radius: 50%;
    transition: background-color 0.3s ease, transform 0.3s ease;
  }

  .fea_pro_add_to_cart a:hover {
    background-color: #007b83;
    transform: scale(1.1);
  }

  /* Size and Color Selectors */
  .size-options, .color-options {
    margin: 10px 0;
  }

  .size-select, .color-select {
    padding: 10px;
    border-radius: 8px;
    border: 1px solid #ddd;
    background-color: #f9f9f9;
    transition: border-color 0.3s ease;
  }

  .size-select:hover, .color-select:hover {
    border-color: #00adb5;
  }

  /* Buttons */
  .add-to-cart {
    display: inline-block;
    padding: 12px 20px;
    /* background-color: #00adb5; */
    color: #fff;
    border-radius: 30px;
    text-decoration: none;
    font-weight: bold;
    transition: background-color 0.3s ease, transform 0.3s ease;
  }

  .add-to-cart:hover {
    background-color: #007b83;
    transform: translateY(-5px);
  }

  /* Smooth Fade-in Animation */
  .wrapper_fea_prod > .product {
    opacity: 0;
    animation: fadeIn 0.6s ease forwards;
  }

  @keyframes fadeIn {
    to {
      opacity: 1;
    }
  }

  /* Cart Section Styling */
  .cart-section {
    background-color: #f9f9f9;
    padding: 40px;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  }

  .cart-item {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
  }

  .cart-item-image {
    width: 80px;
    height: 80px;
    object-fit: cover;
    margin-right: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  }

  .cart-item-details {
    flex-grow: 1;
  }

  .cart-item-price {
    font-size: 1.2rem;
    color: #00adb5;
  }

  .quantity-controls {
    display: flex;
    align-items: center;
  }

  .quantity-input {
    width: 50px;
    text-align: center;
    padding: 5px;
    margin: 0 10px;
  }

  .remove-from-cart {
    background-color: #ff4747;
    color: #fff;
    padding: 8px 16px;
    border-radius: 8px;
    margin-left: 20px;
    transition: background-color 0.3s ease;
  }

  .remove-from-cart:hover {
    background-color: #d63c3c;
  }

  /* Smooth Scroll for Section Navigation */
  html {
    scroll-behavior: smooth;
  }
  .Feature_prod_area{
    position: relative;
    top: 10em;
}

.Feature_prod_heading {
    display: flex;
    justify-content: center;
    font-family: "League Spartan", sans-serif;
    font-size: -webkit-xxx-large;
}

.Feature_prod_area{
    text-align: center;
    font-family: "League Spartan", sans-serif;
}

.Featured_product_con{
    display: flex;
    position: absolute;
}
.product_image{
    max-width: 16em;
    height: 14em;
    width: 17em;
    height: 22em;
    justify-content: center;
    border-radius: 10px;
    
    
}


.Feature_flexcol{
    margin: 20px;
    padding: 20px 20px 0 20px;
    font-family: "League Spartan", sans-serif;
    border: 1px solid #e7ebe7;
    box-shadow: 20px 20px 34px rgba(0, 0, 0, 0.07);
    border-radius: 10px;
}
  
  
   

.description{
    display: flex;
    flex-direction: column;
    text-align: left;
    margin-top: 12px;
}
.brand_name{
    position: relative;
    top: 2px;
}
.product_name1{
    margin-top: 11px;
    margin-bottom: 11px;
}
.product_pricing{
    font-size: large;
   
    margin: 11px 0 0 0;
    padding: 0;
 
    display: flex;
}
.fea_pro_add_to_cart{
    justify-content: center;
    width: 40px;
    height: 40px;
    left: 9em;
    font-size: x-large;
    position: absolute;
   
}
.stars{
    display: flex;
    position: relative;
}
.wrapper_fea_prod{
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    margin-top: 2em;
}
.Featured_product_con :where(.Feature_flexcol, .product) img {
    transition: transform 0.3s;
}

.Featured_product_con :where(.Feature_flexcol, .product):hover img {
    transform: scale(1.1);
}
.Feature_prod_section, .Feature_prod_area, .Featured_product_con, .wrapper_fea_prod, .Feature_flexcol{
    position: relative;
}
/* Slider Container */
.slider-container {
    position: relative;
    width: 100%;
    overflow: hidden;
    max-width: 220px; /* Adjust as per your requirement */
    height: 300px; /* Set a fixed height for consistency */
    margin: auto;
}

.slider-wrapper {
    display: flex;
    transition: transform 0.4s ease;
    height: 100%;
}

.slider-wrapper img {
    min-width: 100%;
    height: 100%;
    object-fit: cover; /* Ensures the image scales and fits without stretching */
    border-radius: 8px;
}

/* Navigation Buttons */
.slider-prev {
    position: absolute;
    top: 55%;
    transform: translateY(-50%);
    background-color: rgba(0, 0, 0, 0.5);
    color: white;
    border: none;
    padding: 8px;
    cursor: pointer;
    border-radius: 50%;
    font-size: 18px;
    z-index: 1; /* Ensures arrows are above the images */
}

.slider-next{
    position: absolute;
    top: 53%;
    transform: translateY(-50%);
    background-color: rgba(0, 0, 0, 0.5);
    color: white;
    border: none;
    padding: 8px;
    cursor: pointer;
    border-radius: 50%;
    font-size: 18px;
    z-index: 1; /* Ensures arrows are above the images */
}
.slider-prev {
    left: -23px;
    padding: 28px;
    background: url(https://images-static.nykaa.com/uploads/3df01759-af0c-4d81-b2f7-f61577ac3807.svg) no-repeat;
    padding: 28px;

    

}

.slider-next {
    right: -23px;
    padding: 23px;
    background: url(https://images-static.nykaa.com/uploads/45d5a7b6-86eb-4850-9568-4d6ed91731fe.svg);
    background-repeat: no-repeat;
    
}



/* Minimal Add to Cart Button */
.fea_pro_add_to_cart a {
    color: black; /* Classy Orange Text */
    border: 2px solid #FF6F3C; /* Outline style with orange border */
    padding: 10px 20px;
    border-radius: 50px; /* Rounded button shape */
    display: inline-block;
    text-align: center;
    font-size: 16px; /* Slightly reduced size for modern minimalism */
    font-weight: bold;
    transition: background-color 0.3s ease, color 0.3s ease, transform 0.3s ease;
    text-decoration: none; /* Remove underline */
}

.fea_pro_add_to_cart a:hover {
    background-color: #FF6F3C; /* Fill with classy orange on hover */
    color: white; /* Change text color to white on hover */
    transform: scale(1.05); /* Slight zoom effect */
}

/* Professional Pricing Style */
.product_pricing {
    color: black; /* Classy Orange for price */
    font-size: 22px; /* Maintain readability */
    font-weight: 600; /* Semi-bold for professional look */
    text-align: center;
    margin-top: 10px;
    font-family: 'League Spartan', sans-serif; /* Stylish font for pricing */
}

.product_pricing span {
    font-size: 18px; /* Subtle reduction for currency symbol */
    font-weight: 400; /* Lighter weight for a more balanced look */
    vertical-align: middle; /* Align currency with price */
}

/* Add Currency Symbol for Professional Look */
.product_pricing::before {
    content: '₹'; /* Adjust as per the currency */
    font-size: 18px;
    font-weight: 400; /* Lighter font-weight for symbol */
    margin-right: 5px;
    vertical-align: middle;
}


.view-more {    
    display: inline-block;
    padding: 10px 20px;
    color: #000000;
    background-color: #FF6F3C; /* Button color */
    border-radius: 25px; /* Rounded corners */
    text-decoration: none;
    font-weight: bold;
    transition: background-color 0.3s ease, transform 0.3s ease;
    margin-top: 10px; /* Space above the button */
}

.view-more:hover {
    color: #fff; /* Darker orange on hover */
    transform: translateY(-3px); /* Slight lift effect */
}

.size_space{
    padding-right: 6px;
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
                include 'partials/_dbconnect.php';

                // Check connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Fetch products from the database
                $result = $conn->query("SELECT * FROM product_details WHERE category='Power Bank'");

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
                    // Slider Images (assuming you have multiple images stored in the database)
                    $slider_images = '';
                    if (!empty($row['image_url_1'])) {
                        $slider_images .= '<img src="' . htmlspecialchars($row['image_url_1'], ENT_QUOTES, 'UTF-8') . '" alt="' . htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') . '" class="product_image">';
                    }
                    if (!empty($row['image_url_2'])) {
                        $slider_images .= '<img src="' . htmlspecialchars($row['image_url_2'], ENT_QUOTES, 'UTF-8') . '" alt="' . htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') . '" class="product_image">';
                    }
                    if (!empty($row['image_url_3'])) {
                        $slider_images .= '<img src="' . htmlspecialchars($row['image_url_3'], ENT_QUOTES, 'UTF-8') . '" alt="' . htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') . '" class="product_image">';
                    }
                    if (!empty($row['image_url_4'])) {
                        $slider_images .= '<img src="' . htmlspecialchars($row['image_url_4'], ENT_QUOTES, 'UTF-8') . '" alt="' . htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') . '" class="product_image">';
                    }
                    echo '
                    <div class="Feature_flexcol product" data-id="' . $row['id'] . '">
                        <div class="product">
                            
                               <!-- Image Slider Container -->
            <div class="slider-container">
                <div class="slider-wrapper">
                    ' . $slider_images . '
                </div>
                <!-- Navigation Arrows -->
                <button class="slider-prev">&lt;</button>
                <button class="slider-next">&gt;</button>
            </div>
                        
                        </div>
                        <div class="description">
                            <span class="brand_name">' . htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') . '</span>
                           
                            <div class="stars">
                              
                                <div class="size-options size_space">

                                <label for="size-select">Size:</label>
                                <select id="size-select" class="size-select">
                                    ' . $size_options . '
                                </select>
                                </div>
                                <div class="color-options">

                                <label for="color-select">Color:</label>
                                <select id="color-select" class="color-select">
                                    ' . $color_options . '
                                </select>
                                </div>

                            </div>
                            <div class="fea_pro_add_to_cart">
                                <a href="#" class="add-to-cart" data-id="' . $row['id'] . '" data-name="' . htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') . '" data-price="' . htmlspecialchars($row['price'], ENT_QUOTES, 'UTF-8') . '" data-image="' . htmlspecialchars($row['image_url_1'], ENT_QUOTES, 'UTF-8') . '">
                                    <i class="fa-solid fa-cart-plus" style="color: #000000;"></i>
                                </a>
                            </div>
                            <div>
                                <h4 class="product_pricing">' . htmlspecialchars($row['price'], ENT_QUOTES, 'UTF-8') . 'rs</h4>
                                <a href="product detals/product_detail.html?id=' . $row['id'] . '" class="view-more">View More</a>
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
    document.querySelectorAll('.slider-container').forEach((container) => {
    const sliderWrapper = container.querySelector('.slider-wrapper');
    const sliderImages = sliderWrapper.querySelectorAll('img');
    let currentIndex = 0;

    const prevButton = container.querySelector('.slider-prev');
    const nextButton = container.querySelector('.slider-next');

    // Update the slider position
    function updateSliderPosition() {
        const offset = -currentIndex * 100; // Each image takes 100% width
        sliderWrapper.style.transform = `translateX(${offset}%)`;
    }

    // Next Slide
    nextButton.addEventListener('click', () => {
        currentIndex = (currentIndex + 1) % sliderImages.length; // Loop back to the first image
        updateSliderPosition();
    });

    // Previous Slide
    prevButton.addEventListener('click', () => {
        currentIndex = (currentIndex - 1 + sliderImages.length) % sliderImages.length; // Loop back to the last image
        updateSliderPosition();
    });
});

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
