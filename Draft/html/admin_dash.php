<?php
// user_dash.php
// User dashboard page - shows account shortcuts once a user is logged in

require_once '../backend/services/userFunctions.php';
require_admin_page('/TEAM-13-/Draft/html/signin.php');

// Start session to access logged-in user information (set during login)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If the user isn't logged in, redirect them to sign in
if (empty($_SESSION['user_ID'])) {
    header("Location: admin_login.php");
    exit;
}

// Pull user details from session (already set in UserController login method)
$userName = $_SESSION['name'] ?? 'User';
$pageTitle = 'Admin Dashboard | LOFT &amp; LIVING BIRMINGHAM';
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

        .dashboard-heading h2 {
            font-size: 20px;
            font-weight: 700;
            margin: 0 0 6px 0;
        }

        .dashboard-heading p {
            margin: 0;
            color: #777;
            font-size: 14px;
        }

        .dash-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
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

        @media (max-width: 980px) {
            .dash-grid { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 560px) {
            .dash-grid { grid-template-columns: 1fr; }
        }
    </style>
HTML;

include 'header.php';
?>

    <main class="dashboard-wrap">
        <div class="dashboard-container">

            <div class="dashboard-heading">
                <h2>Welcome to the Admin Dashboard, <?php echo htmlspecialchars($userName); ?></h2>
                <p>My Account</p>
            </div>

            <div class="dash-grid">
                <a class="dash-card" href="admin_order_list.php">
                    <div class="dash-card-inner">
                        <div class="card-top">
                            <div class="card-media">
                                <img src="../images/dashboard/admin_dash1.png" alt="My Favourites">
                            </div>
                            <div>
                                <h3>Orders & Shipments</h3>
                                <p>View and manage all orders and shipments</p>
                            </div>
                        </div>
                    </div>
                </a>

                <a class="dash-card" href="admin_realtime_reports.php">
                    <div class="dash-card-inner">
                        <div class="card-top">
                            <div class="card-media">
                                <img src="../images/dashboard/admin_dash2.png" alt="My Recent Orders">
                            </div>
                            <div>
                                <h3>Real-Time Reports</h3>
                                <p>View real-time reports and analytics</p>
                            </div>
                        </div>
                    </div>
                </a>

                <a class="dash-card" href="admin_customer_management.php">
                    <div class="dash-card-inner">
                        <div class="card-top">
                            <div class="card-media">
                                <img src="../images/dashboard/admin_dash4.png" alt="My Account Settings">
                            </div>
                            <div>
                                <h3>Customer Management</h3>
                                <p>Manage customer accounts and user permissions</p>
                            </div>
                        </div>
                    </div>
                </a>

                <a class="dash-card" href="admin_product_inventory.php">
                    <div class="dash-card-inner">
                        <div class="card-top">
                            <div class="card-media">
                                <img src="../images/dashboard/admin_dash5.png" alt="Privacy">
                            </div>
                            <div>
                                <h3>Product Inventory</h3>
                                <p>Manage product inventory and stock levels</p>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

        </div>
    </main>

<?php include 'footer.php'; ?>