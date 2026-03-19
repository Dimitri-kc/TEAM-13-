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
$bodyClass = 'admin-dashboard-page';
$extraHeadContent = <<<'HTML'
    <style>
        .dashboard-wrap {
            background: #ffffff;
            padding: clamp(36px, 5vw, 60px) clamp(16px, 3vw, 24px) 80px;
        }

        .dashboard-container {
            max-width: 1040px;
            margin: 0 auto;
        }

        .dashboard-heading {
            margin-bottom: 26px;
            max-width: 100%;
        }

        .dashboard-heading-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            margin-bottom: 8px;
            flex-wrap: wrap;
        }

        .dashboard-heading h2 {
            font-size: clamp(28px, 3vw, 34px);
            font-weight: 500;
            font-family: 'ivybodoni', serif;
            margin: 0;
            line-height: 1.08;
            min-width: 0;
        }

        .dashboard-heading p {
            margin: 0;
            color: #777;
            font-size: 17px;
        }

        .return-home-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 18px;
            border-radius: 999px;
            background: #afa595;
            color: #fff;
            text-decoration: none;
            font-size: 16px;
            font-weight: 500;
            white-space: nowrap;
            flex-shrink: 0;
            transition: background-color 120ms ease, transform 120ms ease, box-shadow 120ms ease;
        }

        .return-home-btn:hover {
            background: #978b79;
            box-shadow: 0 14px 24px rgba(98, 84, 67, 0.16);
            transform: translateY(-1px);
        }

        .dash-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 22px;
        }

        .dash-card {
            border: 1px solid #e9e9e9;
            border-radius: 10px;
            background: #fff;
            overflow: hidden;
            text-decoration: none;
            color: inherit;
            min-width: 0;
            transition: transform 120ms ease, box-shadow 120ms ease;
        }

        .dash-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.06);
        }

        .dash-card-inner {
            padding: 22px;
            height: 100%;
        }

        .card-top {
            display: grid;
            grid-template-columns: minmax(84px, 104px) minmax(0, 1fr);
            gap: 18px;
            align-items: center;
        }

        .card-media img {
            width: 100%;
            max-width: 104px;
            aspect-ratio: 1 / 1;
            object-fit: cover;
            border-radius: 8px;
            display: block;
        }

        .dash-card h3 {
            margin: 6px 0;
            font-size: 21px;
            font-weight: 500;
            font-family: 'ivybodoni', serif;
            line-height: 1.18;
        }

        .dash-card p {
            margin: 0;
            font-size: 16px;
            color: #777;
            line-height: 1.5;
            max-width: 100%;
        }

        @media (max-width: 860px) {
            .dashboard-wrap {
                padding-top: 34px;
            }

            .card-top {
                grid-template-columns: 88px minmax(0, 1fr);
                gap: 16px;
            }
        }

        @media (max-width: 640px) {
            .dash-grid { grid-template-columns: 1fr; }

            .dashboard-heading-top {
                flex-direction: column;
                align-items: flex-start;
            }

            .return-home-btn {
                width: 100%;
            }

            .card-top {
                grid-template-columns: 1fr;
                gap: 14px;
            }

            .card-media img {
                max-width: 92px;
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
                    <h2>Welcome to the Admin Dashboard, <?php echo htmlspecialchars($userName); ?></h2>
                    <a class="return-home-btn" href="homepage.php">Return to Homepage</a>
                </div>
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
