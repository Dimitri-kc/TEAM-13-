<?php include '../backend/config/db_connect.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact | LOFT & LIVING</title>

    <!-- Header CSS -->
    <link rel="stylesheet" href="../css/header_footer_style.css?v=16">

    <!-- Page-specific CSS -->
    <link rel="stylesheet" href="../css/contact-css/contact-base.css?v=2">
    <link rel="stylesheet" href="../css/contact-css/contact-structure.css?v=2">
    <link rel="stylesheet" href="../css/contact-css/contact-reusable.css?v=2">
    <link rel="stylesheet" href="../css/contact-css/contact-page.css?v=2">
    <link rel="stylesheet" href="https://use.typekit.net/lll5xwi.css">
    <link rel="stylesheet" href="https://use.typekit.net/ehd2wqk.css">
    <link rel="stylesheet" href="../css/dark-mode.css?v=12">
    <link rel="stylesheet" href="../css/reusable_header.css?v=4">
    <script src="../javascript/dark-mode.js"></script>
</head>
<body>

<?php $headerPartialOnly = true; include 'header.php'; ?>  <!-- Include site header here -->

<main class="contact-page">
    <section class="contact-hero">
        <p class="contact-eyebrow">Customer Support</p>
        <h1 class="title">Contact Us</h1>
        <p class="contact-intro">Questions about an order, delivery, or a piece you have your eye on? Send us a message and we will get back to you as soon as possible.</p>
    </section>

    <section class="contact-layout">
        <aside class="contact-info-card">
            <h2>How can we help?</h2>
            <p>Use the form to send any enquiry and include your order number if your message relates to an existing purchase.</p>
            <div class="contact-info-list">
                <div class="contact-info-item">
                    <span class="contact-info-label">Response time</span>
                    <span class="contact-info-value">Usually within 1-2 working days</span>
                </div>
                <div class="contact-info-item">
                    <span class="contact-info-label">Support</span>
                    <span class="contact-info-value">Orders, returns, stock and delivery questions</span>
                </div>
                <div class="contact-info-item">
                    <span class="contact-info-label">Best tip</span>
                    <span class="contact-info-value">Add your order number to help us respond faster</span>
                </div>
            </div>
        </aside>

        <div class="form-container">
            <form id="contact-form" action="https://formspree.io/f/xzzlerol" method="POST">
                <input type="text" name="_gotcha" style="display: none;" />

                <div class="contact-form-grid">
                    <div class="field-group">
                        <label for="first">Name<span class="required">*</span></label>
                        <input type="text" id="first" name="first" placeholder="First Name" required>
                    </div>

                    <div class="field-group">
                        <label for="last">Surname<span class="required">*</span></label>
                        <input type="text" id="last" name="last" placeholder="Last Name" required>
                    </div>
                </div>

                <div class="field-group">
                    <label for="email">Email<span class="required">*</span></label>
                    <input type="email" id="email" name="email" placeholder="Email Address" required>
                </div>

                <div class="field-group">
                    <label for="order">Order Number (If Applicable)</label>
                    <input type="text" id="order" name="order" placeholder="Enter Order Number">
                </div>

                <div class="field-group">
                    <label for="message">Message<span class="required">*</span></label>
                    <textarea id="message" name="message" placeholder="Enter message or enquiry" required></textarea>
                </div>

                <button type="submit">Send Message</button>
            </form>
        </div>
    </section>
</main>

<?php $footerPartialOnly = true; include 'footer.php'; ?>  <!-- Include site footer here -->

<!-- JS Scripts -->

</body>
</html>
