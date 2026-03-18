<?php include '../backend/config/db_connect.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy | LOFT & LIVING</title>
    <link rel="stylesheet" href="../css/header_footer_style.css?v=15">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            line-height: 1.6;
            background: #f9f9f9;
            color: #333;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        main.container {
            max-width: 900px;
            margin: 40px auto 40px;
            padding: 30px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.1);
        }
        h1 {
            font-size: 28px;
            margin-bottom: 15px;
            color: #222;
        }
        p {
            margin-bottom: 15px;
        }
        ul {
            margin: 10px 0 20px 20px;
        }
        .faq-item {
            border-bottom: 1px solid #ddd;
            margin-bottom: 10px;
        }
        .faq-question {
            padding: 12px;
            cursor: pointer;
            background: #f0f0f0;
            font-weight: 600;
            border-radius: 6px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease, padding 0.3s ease;
            padding: 0 12px;
            background: #fafafa;
        }
        .faq-answer.open {
            padding: 12px;
        }
        .faq-question::after {
            content: "▼";
            font-size: 12px;
            transition: transform 0.3s ease;
        }
        .faq-question.active::after {
            transform: rotate(-180deg);
        }
        .back-dashboard {
    display: inline-block;
    margin-bottom: 20px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    color: #2C6E49;
    transition: 0.2s ease;
}

.back-dashboard:hover {
    text-decoration: underline;
}
    </style>
    <link rel="stylesheet" href="https://use.typekit.net/lll5xwi.css">
    <link rel="stylesheet" href="https://use.typekit.net/ehd2wqk.css">
    <link rel="stylesheet" href="../css/dark-mode.css?v=9">
    <link rel="stylesheet" href="../css/reusable_header.css?v=4">
    <script src="../javascript/dark-mode.js"></script>
</head>
<body>

<?php $headerPartialOnly = true; include 'header.php'; ?>
    <main class="container">
    <a href="user_dash.php" class="back-dashboard">← Back to Dashboard</a>
    <h1>Privacy Policy</h1>

    <p>We are committed to handling your personal information responsibly and securely. We will:</p>
    <ul>
        <li>Use and maintain your data in accordance with applicable laws and regulations.</li>
        <li>Inform you about what data we collect and how we use it.</li>
        <li>Ensure the privacy of your data is protected.</li>
        <li>Respect your legal rights regarding the personal data we hold.</li>
    </ul>

    <p>We do not sell your data to third parties or transfer it outside our organization except as described in this policy.</p>

    <p>This Privacy Policy explains what data we collect about you, why and how we use it, who we share it with, and how we protect your information.</p>

    <p>The Privacy Policy is provided in a layered format so you can expand each section for more details.</p>

    <div class="faq">
        <div class="faq-item">
            <div class="faq-question">1. What personal data do we collect?</div>
            <div class="faq-answer">
                <p>We collect information such as your name, email address, phone number, and any data you provide when using our website or services.</p>
            </div>
        </div>
        <div class="faq-item">
            <div class="faq-question">2. How do we use your personal data?</div>
            <div class="faq-answer">
                <p>Your data is used to provide our services, process orders, communicate with you, improve our offerings, and comply with legal obligations.</p>
            </div>
        </div>
        <div class="faq-item">
            <div class="faq-question">3. Who do we share your data with?</div>
            <div class="faq-answer">
                <p>We may share your data with trusted service providers, partners, or as required by law. We do not sell your personal information.</p>
            </div>
        </div>
    </div>
</main>


<script>
    // FAQ toggle
    const questions = document.querySelectorAll('.faq-question');
    questions.forEach(q => {
        q.addEventListener('click', () => {
            const answer = q.nextElementSibling;
            const isOpen = answer.classList.contains('open');
            answer.classList.toggle('open', !isOpen);
            q.classList.toggle('active', !isOpen);
            answer.style.maxHeight = !isOpen ? answer.scrollHeight + "px" : "0";
        });
    });
</script>

<?php $footerPartialOnly = true; include 'footer.php'; ?>
</body>
</html>