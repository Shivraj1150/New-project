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
        .brand_name{
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
                $result = $conn->query("SELECT * FROM products");

                while ($row = $result->fetch_assoc()) {
                    echo '
                    <div class="Feature_flexcol product" data-id="'.$row['id'].'">
                        <div class="product"> 
                            <img src="uploads/'.$row['image'].'" alt="'.$row['name'].'" class="product_image">
                        </div>
                        <div class="description">
                            <span class="brand_name">'.$row['name'].'</span>
                            <p class="product_name1">'.$row['description'].'</p>
                            <div class="stars">
                                <i class="fa-solid fa-star" style="color: #FFD43B;"></i>
                                <i class="fa-solid fa-star" style="color: #FFD43B;"></i>
                                <i class="fa-solid fa-star" style="color: #FFD43B;"></i>
                                <i class="fa-solid fa-star" style="color: #FFD43B;"></i>
                                <i class="fa-solid fa-star" style="color: #FFD43B;"></i>
                                <label for="size-select">Size:</label>
                                <select id="size-select" class="size-select">
                                    <option value="S">S</option>
                                    <option value="M">M</option>
                                    <option value="L">L</option>
                                    <option value="XL">XL</option>
                                </select>
                            </div>
                            <div class="fea_pro_add_to_cart">
                                <a href="#" class="add-to-cart" data-id="'.$row['id'].'" data-name="'.$row['name'].'" data-price="'.$row['price'].'" data-image="uploads/'.$row['image'].'">
                                    <i class="fa-solid fa-cart-plus" style="color: #000000;"></i>
                                </a>
                            </div>
                            <div>
                                <h4 class="product_pricing">'.$row['price'].'rs</h4>
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

</body>
</html>
