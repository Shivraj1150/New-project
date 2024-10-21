<?php
session_start(); // Start the session

// Assuming user_id is set during login
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page or show an error if user is not logged in
    header("Location: _login.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
    <title>My Cart</title>
    <style>
    * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f9f9f9;
            color: #333;
            display: flex;
            font-family: "League Spartan", sans-serif;

            justify-content: center;
            align-items: center;
            height: 100vh;
            padding: 20px;
        }

        .cart-container {
            background-color: #ffffff;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border-radius: 15px;
            width: 100%;
            max-width: 900px;
            padding: 30px;
            animation: fadeInUp 0.5s ease-in-out;
        }

        h1 {
            text-align: center;
            color: #f97316;
            margin-bottom: 20px;
            font-weight: bold;
            font-size: 2.5rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            padding: 15px;
            text-align: left;
            font-size: 1rem;
        }

        th {
            background-color: #f9f9f9;
            text-transform: uppercase;
            color: #555;
        }

        td {
            border-bottom: 1px solid #e2e8f0;
        }

        .cart-item-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 10px;
        }

        .quantity-controls button {
            background-color: #f97316;
            border: none;
            padding: 8px 12px;
            color: white;
            cursor: pointer;
            border-radius: 8px;
            transition: background-color 0.3s;
        }

        .quantity-controls button:hover {
            background-color: #ea580c;
        }

        .quantity-input {
            width: 50px;
            text-align: center;
            border: 1px solid #cbd5e0;
            border-radius: 8px;
            padding: 8px;
            background-color: #edf2f7;
            color: #2d3748;
        }

        .cart-summary {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }

        .apply-discount {
            display: flex;
            gap: 10px;
        }

        .apply-discount input {
            padding: 12px;
            border: 1px solid #cbd5e0;
            border-radius: 8px;
            font-size: 1rem;
            background-color: #edf2f7;
            color: #2d3748;
        }

        .apply-discount button {
            background-color: #f97316;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .apply-discount button:hover {
            background-color: #ea580c;
        }

        .total-container {
            font-size: 1.2rem;
            text-align: right;
            color: #f97316;
        }

        .checkout-button {
            background-color: #f97316;
            color: white;
            border: none;
            padding: 15px 30px;
            font-size: 1.2rem;
            border-radius: 12px;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: background-color 0.3s, transform 0.3s;
        }

        .checkout-button:hover {
            background-color: #ea580c;
            transform: scale(1.05);
        }

        .checkout-button::after {
            content: '';
            position: absolute;
            top: 50%;
            left: -100%;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.3);
            transform: skewX(-20deg);
            transition: left 0.5s ease-in-out;
        }

        .checkout-button:hover::after {
            left: 150%;
        }

        /* Delete icon */
        .delete-icon {
            background-color: transparent;
            border: none;
            cursor: pointer;
            color: #f97316;
            font-size: 1.5rem;
            transition: color 0.3s;
        }

        .delete-icon:hover {
            color: #ea580c;
        }

        /* Animations */
        @keyframes fadeInUp {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive */
   /* Adjustments for smaller screens */
@media (max-width: 768px) {
    .cart-container {
        padding: 15px;
    }

    h1 {
        font-size: 2rem;
    }

    table {
        font-size: 0.9rem;
    }

    th, td {
        padding: 10px;
        font-size: 0.8rem;
    }

    .cart-item-image {
        width: 50px;
        height: 50px;
    }

    .quantity-controls button {
        padding: 6px 10px;
        font-size: 0.8rem;
    }

    .quantity-input {
        width: 40px;
        padding: 6px;
        font-size: 0.9rem;
    }

    .apply-discount input {
        padding: 10px;
        font-size: 0.9rem;
    }

    .apply-discount button {
        padding: 10px 18px;
        font-size: 0.9rem;
    }

    .total-container p {
        font-size: 1rem;
    }

    .checkout-button {
        font-size: 1rem;
        padding: 12px 25px;
    }

    /* Make table responsive by adding horizontal scroll */
    .cart-container {
        overflow-x: auto;
    }

    table {
        width: 100%; /* Add width so the table scrolls horizontally */
    }

    .cart-summary {
        flex-direction: column;
        align-items: flex-end;
        gap: 10px;
    }
}

.notification {
            position: fixed;
         
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            padding: 10px;
            z-index: 1000000;
            transition: opacity 0.5s ease;
            opacity: 0;
            display: none; /* Ensure it starts as hidden */
        }

        .notification.show {
            display: block; /* Show when triggered */
            opacity: 1; /* Fade in effect */
        }
        .container {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
    padding: 20px 40px;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    max-width: 500px;
    width: 100%;
}

h1 {
    font-size: 2em;
    text-align: center;
    color: #333;
    margin-bottom: 20px;
}

.progress-bar {
    position: relative;
    height: 8px;
    background: #e0e0e0;
    border-radius: 4px;
    margin-bottom: 20px;
}

.progress {
    height: 100%;
    width: 33%;
    background: linear-gradient(to right, #ff9800, #ffcc80);
    border-radius: 4px;
    transition: width 0.4s ease;
}

.form-group {
    margin-bottom: 20px;
    position: relative;
}

label {
    font-size: 0.9em;
    color: #666;
    display: block;
    margin-bottom: 5px;
    transition: all 0.3s ease;
}

input, textarea, select {
    width: 100%;
    padding: 10px;
    font-size: 1em;
    border: 1px solid #ccc;
    border-radius: 6px;
    outline: none;
    transition: all 0.3s ease;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

input:focus, textarea:focus, select:focus {
    border-color: #ff9800;
    box-shadow: 0 4px 10px rgba(255, 152, 0, 0.4);
}

textarea {
    resize: none;
    min-height: 80px;
}

.form-group1 textarea{
    resize: none;
    min-height: 20px;
}

.form-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 20px;
}

.primary-button {
    background: linear-gradient(to right, #ff9800, #ffcc80);
    border: none;
    color: #fff;
    padding: 10px 20px;
    border-radius: 6px;
    font-size: 1em;
    cursor: pointer;
    transition: all 0.3s ease;
}

.primary-button:hover {
    box-shadow: 0 8px 20px rgba(255, 152, 0, 0.4);
}

.secondary-button {
    background: none;
    border: 1px solid #ccc;
    color: #666;
    padding: 10px 20px;
    border-radius: 6px;
    font-size: 1em;
    cursor: pointer;
    transition: all 0.3s ease;
}

.secondary-button:hover {
    background: #f3f3f3;
}

input:focus + label, textarea:focus + label, select:focus + label {
    color: #ff9800;
    transform: translateY(-20px);
    font-size: 0.8em;
}

input:not(:placeholder-shown) + label, textarea:not(:placeholder-shown) + label, select:not(:placeholder-shown) + label {
    color: #ff9800;
    transform: translateY(-20px);
    font-size: 0.8em;
}

.modal-overlay {
            display: none; /* Initially hidden */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5); /* Semi-transparent background */
            justify-content: center; /* Center the modal */
            align-items: center; /* Center the modal */
            z-index: 1000; /* Sit on top of other elements */
            transition: opacity 0.3s ease; /* Smooth fade in/out */
    opacity: 1; /* Start with full opacity */
        }

        .container {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            padding: 20px 40px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
            display: block; /* Make it visible within the modal */
        }


    </style>
</head>
<body>
    <div id="notification" class="notification" style="display: none;"></div>
    <div class="cart-container">
        <h1>Shopping Cart</h1>
        
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Description</th>
                    <th>Quantity</th>
                  
                    
                
                    

                </tr>
            </thead>
            <tbody class="cart-section">
                <!-- Cart items will be dynamically injected here -->
            </tbody>
        </table>

        <div class="cart-summary">
            <div class="apply-discount">
                <input type="text" id="discount-code" placeholder="Discount Code">
                <button onclick="applyDiscount()">Apply</button>
            </div>

            <div class="total-container">
                <p>Total Items: <span id="total-items">0</span></p>
                <p>Total Price: <span id="total-price">₹0.00</span></p>
            </div>
            
            <button class="checkout-button"  >Proceed to Checkout</button>
            

        </div>
    </div>
    <div class="modal-overlay" id="modal-overlay">

    <div class="container">
        <div class="progress-bar">
            <div class="progress"></div>
        </div>
        <h1>Shipping Details</h1>
        <form id="shipping-form" action="process_order.php" method="POST">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
                <input type="text" id="name" name="shipping_name" placeholder="Enter your full name" required>
            </div>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="shipping_email" placeholder="Enter your email address" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="text" id="phone" name="shipping_phone" placeholder="e.g., +91 98765 43210" required>
            </div>
            <div class="form-group">
                <label for="shipping-address">Shipping Address</label>
                <textarea id="shipping-address" name="shipping_address" placeholder="Street, City, State, Pincode" required></textarea>
            </div>
            <div class="form-group1">
                <label for="country">Country</label>
                <textarea id="country" name="shipping_country" placeholder="Country" required></textarea>
            </div>
            <div class="form-group1">
                <label for="state">State/Province</label>
                <textarea id="state" name="shipping_state" placeholder="State and Pincode" required></textarea>
            </div>
            <div class="form-actions">
                <button type="button" class="secondary-button" onclick="closeModal()">Back</button>
              <!-- Cart/Product Details (Dynamically populated from cart) -->
    <input type="hidden" name="product_description" value="${item.product_name}">
    <input type="hidden" name="product_size" value="${item.size}">
    <input type="hidden" name="product_color" value="${item.color}">
    <input type="hidden" name="product_quantity" value="${item.quantity}">
    <input type="hidden" name="product_image" value="${item.product_image}">
    <input type="hidden" name="total_price" id="hidden-total-price" value="">
           <button type="submit" class="primary-button" id="continue-to-payment">Continue to Payment</button>
            </div>
        </form>
    </div>
</div>

 




    <script>
     
function showNotification(message) {
    const notification = document.getElementById('notification');
    notification.textContent = message;
    notification.classList.add('show');
    notification.style.display = 'block'; // Ensure it is displayed

    // Automatically hide the notification after 3 seconds
    setTimeout(() => {
        notification.classList.remove('show');
        notification.style.display = 'none';
    }, 3000000);
}
function closeModal() {
    const modalOverlay = document.getElementById("modal-overlay");
    modalOverlay.style.opacity = "0"; // Start fade-out
    setTimeout(() => {
        modalOverlay.style.display = "none"; // Hide after fade-out
    }, 300); // Match the duration with the CSS transition time
}




function proceedToCheckout() {
            const totalPrice = document.getElementById('total-price').textContent.replace('₹', '');
            const totalItems = document.getElementById('total-items').textContent;

            if (parseInt(totalItems) > 0 && parseFloat(totalPrice) > 0) {
                document.getElementById('modal-overlay').style.display = 'flex'; // Show the modal overlay
                document.querySelector('.cart-container').style.opacity = '0.3'; // Dim the cart background
            } else {
                showNotification('Your cart is empty or you are not logged In. Please add items or login to your account before proceeding to checkout.');
            }
        }

        function closeModal() {
            document.getElementById('modal-overlay').style.display = 'none'; // Hide the modal overlay
            document.querySelector('.cart-container').style.opacity = '1'; // Restore the cart background opacity
        }

        // On form submission, trigger payment initiation progress
        const form = document.getElementById('shipping-form');
        form.addEventListener('submit', function(event) {
            event.preventDefault();  // Prevent default form submission
              // Update form action to point to phonepe_initiate_payment.php
    // form.action = 'phonepe_initiate_payment.php';

            const totalPrice = document.getElementById('hidden-total-price').value;
    console.log("Submitting total price: ", totalPrice); // Check if total price is correct

            const progress = document.querySelector('.progress');
            progress.style.width = '66%';

            setTimeout(() => {
                progress.style.width = '100%';

                // Submit the form to phonepe_initiate_payment.php
                form.submit();  // Submit the form after the progress animation

            }, 500);  // Simulate a short delay for the progress bar
        });

// Add event listener to the checkout button
document.addEventListener('DOMContentLoaded', () => {
    document.querySelector('.checkout-button').addEventListener('click', proceedToCheckout);
    renderCart(); // Call renderCart to display cart items on page load
});


     
        // JavaScript remains the same, but change the 'Remove' button to use the delete icon
        function renderCart() {
            fetch('cart.php')
                .then(response => response.json())
                .then(cartItems => {
                    console.log(cartItems); // Check if cart items are being returned properly
            if (!cartItems || cartItems.length === 0) {
                console.log('Cart is empty');
                return;  // Return early if the cart is empty
            }


                    const cartSection = document.querySelector('.cart-section');
                    cartSection.innerHTML = '';

                    let totalPrice = 0;
                    let totalItems = 0;

        // Clear any existing product hidden fields
        const shippingForm = document.getElementById('shipping-form');
            shippingForm.querySelectorAll('.product-input-group').forEach(el => el.remove());


                        cartItems.forEach((item, index) => {
                        const cartRow = document.createElement('tr');
                        cartRow.classList.add('cart-item');
                        cartRow.innerHTML = 
                            `<td><img src="${item.product_image}" alt="${item.product_name}" class="cart-item-image">    <button class="delete-icon" data-id="${item.product_id}" data-size="${item.size}" data-color="${item.color}"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="Black" class="bi bi-trash3-fill" viewBox="0 0 16 16">
  <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
</svg></button>
</td>
                            <td>${item.product_name}  <br>Size: ${item.size}  <br>Color:${item.color} <br> ₹${item.product_price} </td>
                          
                            <td>
                                <div class="quantity-controls">
                                    <button class="decrease-quantity" data-id="${item.product_id}" data-size="${item.size}">-</button>
<input type="number" value="${item.quantity}" min="1" class="quantity-input" data-id="${item.product_id}" data-size="${item.size}" data-color="${item.color}">
                                    <button class="increase-quantity" data-id="${item.product_id}" data-size="${item.size}">+</button>
                                </div>
                            </td>
                            
                            
 

                           `;
                        
                            cartSection.appendChild(cartRow);
            
             


                        totalPrice += parseFloat(item.product_price) * item.quantity;

                        totalItems += item.quantity;
                         // Add hidden input fields for each product to the form
                const productInputGroup = document.createElement('div');
                productInputGroup.classList.add('product-input-group');
                productInputGroup.innerHTML = `
                    <input type="hidden" name="products[${index}][description]" value="${item.product_name}">
                    <input type="hidden" name="products[${index}][size]" value="${item.size}">
                    <input type="hidden" name="products[${index}][color]" value="${item.color}">
                    <input type="hidden" name="products[${index}][quantity]" value="${item.quantity}">
                    <input type="hidden" name="products[${index}][image]" value="${item.product_image}">
                    <input type="hidden" name="products[${index}][price]" value="${item.product_price}">
                `;
                shippingForm.appendChild(productInputGroup);
            });

                        
                   

                    document.getElementById('total-price').textContent = `₹${totalPrice.toFixed(2)}`;
                    document.getElementById('total-items').textContent = totalItems;
                 // Update the hidden total price field for form submission
            document.getElementById('hidden-total-price').value = totalPrice.toFixed(2);

                    addRemoveFunctionality();
                    addQuantityFunctionality();
                });
        }

        function addRemoveFunctionality() {
            document.querySelectorAll('.delete-icon').forEach(button => {
                button.addEventListener('click', () => {
                    const productId = button.getAttribute('data-id');
                    const size = button.getAttribute('data-size');
                    removeFromCart(productId, size);
                });
            });
        }

        function removeFromCart(product_id, size) {
            const color = document.querySelector(`.delete-icon[data-id="${product_id}"][data-size="${size}"]`).getAttribute('data-color');

            fetch('cart.php', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `product_id=${product_id}&size=${size}&color=${color}`
            })
            .then(() => {
                renderCart();
            });
        }

        function addQuantityFunctionality() {
    document.querySelectorAll('.increase-quantity').forEach(button => {
        button.addEventListener('click', () => {
            const productId = button.getAttribute('data-id');
            const size = button.getAttribute('data-size');
            const color = button.closest('tr').querySelector('.quantity-input').getAttribute('data-color'); // Get the color

            const input = document.querySelector(`.quantity-input[data-id="${productId}"][data-size="${size}"][data-color="${color}"]`);
            let quantity = parseInt(input.value);
            quantity += 1;
            updateQuantity(productId, size, quantity, color); // Pass color
        });
    });

    document.querySelectorAll('.decrease-quantity').forEach(button => {
        button.addEventListener('click', () => {
            const productId = button.getAttribute('data-id');
            const size = button.getAttribute('data-size');
            const color = button.closest('tr').querySelector('.quantity-input').getAttribute('data-color'); // Get the color

            const input = document.querySelector(`.quantity-input[data-id="${productId}"][data-size="${size}"][data-color="${color}"]`);
            let quantity = parseInt(input.value);
            if (quantity > 1) {
                quantity -= 1;
                updateQuantity(productId, size, quantity, color); // Pass color
            }
        });
    });

    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', () => {
            const productId = input.getAttribute('data-id');
            const size = input.getAttribute('data-size');
            const color = input.getAttribute('data-color'); // Get color

            let quantity = parseInt(input.value);
            if (quantity < 1) {
                quantity = 1;
                input.value = 1;
            }
            updateQuantity(productId, size, quantity, color); // Pass color
        });
    });
}

function updateQuantity(product_id, size, quantity, color) {
    fetch('cart.php', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `product_id=${product_id}&size=${size}&quantity=${quantity}&color=${color}`
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

        function applyDiscount() {
            const discountCode = document.getElementById('discount-code').value;
            fetch('discount.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `discount_code=${discountCode}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert('Discount applied successfully!');
                    renderCart();
                } else {
                    alert(data.message);
                }
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            renderCart();
        });

    </script>
</body>
</html>
