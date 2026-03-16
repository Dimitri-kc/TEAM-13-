<?php
// user_dash.php
// User dashboard page - shows account shortcuts once a user is logged in

// Start session to access logged-in user information (set during login)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If the user isn't logged in, redirect them to sign in
if (empty($_SESSION['user_ID'])) {
    header("Location: signin.php");
    exit;
}

// Pull user details from session (already set in UserController login method)
$userName = $_SESSION['name'] ?? 'User';
$pageTitle = 'My Account | LOFT &amp; LIVING BIRMINGHAM';
$extraHeadContent = <<<'HTML'
    <style>
        .dashboard-wrap {
            background: #ffffff;
            padding: 60px 24px 80px;
        }

        .dashboard-container {
            max-width: 1100px;
            margin: 0 auto;
        }

        .dashboard-heading {
            margin-bottom: 26px;
        }

        .dashboard-heading-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 6px;
        }

        .dashboard-heading h2 {
            font-size: 20px;
            font-weight: 700;
            margin: 0;
        }

        .dashboard-heading p {
            margin: 0;
            color: #777;
            font-size: 14px;
        }

        .return-home-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 18px;
            border-radius: 999px;
            background: #2B2B2B;
            color: #fff;
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
            white-space: nowrap;
            transition: background-color 120ms ease, transform 120ms ease;
        }

        .return-home-btn:hover {
            background: #1f8438;
            transform: translateY(-1px);
        }

        .dash-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 22px;
        }

        .dash-card {
            border: 1px solid #e9e9e9;
            border-radius: 10px;
            background: #fff;
            overflow: hidden;
            text-decoration: none;
            color: inherit;
            transition: transform 120ms ease, box-shadow 120ms ease;
        }

        .dash-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.06);
        }

        .dash-card-inner {
            padding: 18px;
        }

        .card-top {
            display: grid;
            grid-template-columns: 92px 1fr;
            gap: 16px;
            align-items: center;
        }

        .card-media img {
            width: 92px;
            height: 92px;
            object-fit: cover;
            border-radius: 8px;
            display: block;
        }

        .dash-card h3 {
            margin: 6px 0;
            font-size: 16px;
            font-weight: 700;
        }

        .dash-card p {
            margin: 0;
            font-size: 13px;
            color: #777;
            line-height: 1.35;
            max-width: 320px;
        }

        .span-2 { grid-column: span 2; }
        .span-3 { grid-column: span 3; }

        @media (max-width: 980px) {
            .dash-grid { grid-template-columns: repeat(2, 1fr); }
            .span-2, .span-3 { grid-column: span 1; }
        }

        @media (max-width: 560px) {
            .dash-grid { grid-template-columns: 1fr; }

            .dashboard-heading-top {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
HTML;

include 'header.php';
?>

    <main class="dashboard-wrap">
        <div class="dashboard-container">

            <div class="dashboard-heading">
                <div class="dashboard-heading-top">
                    <h2>Welcome, <?php echo htmlspecialchars($userName); ?></h2>
                    <a class="return-home-btn" href="homepage.php">Return to Homepage</a>
                </div>
                <p>My Account</p>
            </div>

            <div class="dash-grid">
                <a class="dash-card span-2" href="favourites.php">
                    <div class="dash-card-inner">
                        <div class="card-top">
                            <div class="card-media">
                                <img src="../images/dashboard/dash1.png" alt="My Favourites">
                            </div>
                            <div>
                                <h3>My Favourites</h3>
                                <p>See an item you like? Come back to it later at any time</p>
                            </div>
                        </div>
                    </div>
                </a>

                <a class="dash-card span-2" href="user_order_history.php">
                    <div class="dash-card-inner">
                        <div class="card-top">
                            <div class="card-media">
                                <img src="../images/dashboard/dash2.png" alt="My Recent Orders">
                            </div>
                            <div>
                                <h3>My Recent Orders</h3>
                                <p>Take a look at previous orders you've made</p>
                            </div>
                        </div>
                    </div>
                </a>

                <a class="dash-card span-2" href="billings.php">
                    <div class="dash-card-inner">
                        <div class="card-top">
                            <div class="card-media">
                                <img src="../images/dashboard/dash3.png" alt="My Addresses">
                            </div>
                            <div>
                                <h3>My Addresses</h3>
                                <p>View any saved addresses and make any changes</p>
                            </div>
                        </div>
                    </div>
                </a>

                <a class="dash-card span-3" href="account_settings.php">
                    <div class="dash-card-inner">
                        <div class="card-top">
                            <div class="card-media">
                                <img src="../images/dashboard/dash4.png" alt="My Account Settings">
                            </div>
                            <div>
                                <h3>My Account Settings</h3>
                                <p>Make any changes to your account name, email address or password</p>
                            </div>
                        </div>
                    </div>
                </a>

                <a class="dash-card span-3" href="privacy.php">
                    <div class="dash-card-inner">
                        <div class="card-top">
                            <div class="card-media">
                                <img src="../images/dashboard/dash5.png" alt="Privacy">
                            </div>
                            <div>
                                <h3>Privacy</h3>
                                <p>View our privacy policies here</p>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

        </div>
    </main>

<?php include 'footer.php'; ?>