<?php include '../backend/config/db_connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guest Checkout page</title>

    <link rel="stylesheet" href="../css/header_footer_style.css?v=15">

    <link rel="stylesheet" href="../css/checkout.css" />
    <link rel="stylesheet" href="https://use.typekit.net/lll5xwi.css">
    <link rel="stylesheet" href="https://use.typekit.net/ehd2wqk.css">
    <link rel="stylesheet" href="../css/dark-mode.css?v=12">
    <link rel="stylesheet" href="../css/reusable_header.css?v=4">
    <script src="../javascript/dark-mode.js"></script>
</head>
<body>

    <?php $headerPartialOnly = true; include 'header.php'; ?>
    <header class="checkout-header">
        <h1>GUEST CHECKOUT</h1>
    </header>

    <main class="checkout-layout">

        <aside class="product-column">
            <div class="product-item">
                <img src="../images/basket-images/sofa.jpg" alt="Venice Cream Sofa"/>
                <div class="product-text">
                    <p>Venice Cream Sofa</p>
                    <p class="price">£295</p>
                    <p class="quantity">Quantity: 1</p>
                </div>
            </div>
        </aside>

        <section class="details-column">
            
     <section class="review-box">
 
</section>


                    <div class="card-fields">
                         <h2>Email</h2>
    <input type="email" placeholder="Email Address" required />
    <h2>Full Name</h2>
    <input type="text" placeholder="Full Name" required />
    <h2>Address (Using Google Lookup)</h2>
    <input type="text" placeholder="Address Line 1" required />


                        <h2>Card Details</h2>

                        <input type="text" placeholder="Card Number (16 Digits)" maxlength="16" required />
                        <input type="text" placeholder="Expiry Date (MM/YY)" required />
                        <input type="text" placeholder="CVV (3 Digits)" maxlength="3" required />
                        <button type="submit">Submit</button>

                        <div class="pay-buttons">
                            <img src="../images/basket-images/applepay.png" alt="Apple Pay" class="pay-btn">
                            <img src="../images/basket-images/googlepay.png" alt="Google Pay" class="pay-btn">
                        </div>
                    </div>

            <div class="delivery-section">
                <p>Ready for Loft & Living in Your Home?</p>
                <p>Your Order will be dispatched using Standard Delivery </p>
                <p>Estimated Delivery: 9th December 2025</p>

                <button class="checkout-btn" onclick="window.location.href='orderconfirmation.php'">Checkout</button>
            </div>

        </section>
        
    </main>
    <?php $footerPartialOnly = true; include 'footer.php'; ?>
</body>
</html>