<?php include '../backend/config/db_connect.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact | LOFT & LIVING</title>

    <!-- Header CSS -->
    <link rel="stylesheet" href="../css/header_footer_style.css?v=14">

    <!-- Page-specific CSS -->
    <link rel="stylesheet" href="../css/contact-css/contact-base.css">
    <link rel="stylesheet" href="../css/contact-css/contact-structure.css">
    <link rel="stylesheet" href="../css/contact-css/contact-reusable.css">
    <link rel="stylesheet" href="../css/contact-css/contact-page.css">
    <link rel="stylesheet" href="https://use.typekit.net/lll5xwi.css">
    <link rel="stylesheet" href="https://use.typekit.net/ehd2wqk.css">
    <link rel="stylesheet" href="../css/dark-mode.css?v=9">
    <link rel="stylesheet" href="../css/reusable_header.css?v=4">
    <script src="../javascript/dark-mode.js"></script>
</head>
<body>

<?php $headerPartialOnly = true; include 'header.php'; ?>  <!-- Include site header here -->

<main>
    <header id="main-header">
        <h1 class="title">CONTACT US</h1>
    </header>

    <section>
        <div class="form-container">
            <form id="contact-form" action="https://formspree.io/f/xzzlerol" method="POST">
                <input type="text" name="_gotcha" style="display: none;" />

                <label for="first">Name<span class="required">*</span> </label>
                <input type="text" id="first" name="first" placeholder="First Name" required>

                <label for="last">Surname<span class="required">*</span></label>
                <input type="text" id="last" name="last" placeholder="Last Name" required>

                <label for="email">Email<span class="required">*</span></label>
                <input type="email" id="email" name="email" placeholder="Email Address" required>

                <label for="order">Order Number (If Applicable)</label>
                <input type="text" id="order" name="order" placeholder="Enter Order Number">

                <label for="message">Message<span class="required">*</span></label>
                <textarea id="message" name="message" placeholder="Enter message or enquiry" required></textarea>

                <button type="submit">Submit</button>
            </form>
        </div>
    </section>
</main>

<?php $footerPartialOnly = true; include 'footer.php'; ?>  <!-- Include site footer here -->

<!-- JS Scripts -->

</body>
</html>