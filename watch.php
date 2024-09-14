<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'merapyareusers');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shoes Products</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Shoes Category</h1>
    </header>

    <section class="product_section">
        <div class="container">
            <div class="product_wrapper">
                <?php
                // Fetch products from the "shoes" category
                $result = $conn->query("SELECT * FROM products WHERE category='watches'");

                while ($row = $result->fetch_assoc()) {
                    echo '
                    <div class="product">
                        <img src="uploads/'.$row['image'].'" alt="'.$row['name'].'" class="product_image">
                        <div class="description">
                            <span class="brand_name">'.$row['name'].'</span>
                            <p class="product_description">'.$row['description'].'</p>
                            <h4 class="product_price">'.$row['price'].'rs</h4>
                            <div class="fea_pro_add_to_cart">
                                <a href="#"><i class="fa-solid fa-cart-plus"></i> Add to Cart</a>
                            </div>
                        </div>
                    </div>';
                }

                $conn->close();
                ?>
            </div>
        </div>
    </section>

    <footer>
        <p>&copy; 2024 ShopSAGE. All Rights Reserved.</p>
    </footer>
</body>
</html>
