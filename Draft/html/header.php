<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = !empty($_SESSION['user_ID']);
$isAdmin = (($_SESSION['role'] ?? '') === 'admin');
$userName = $_SESSION['name'] ?? '';
$headerName = ($userName !== '') ? $userName : 'Guest';
$lastSearchQuery = trim((string)($_SESSION['last_search_query'] ?? ''));
$searchHref = 'search.php';

if ($lastSearchQuery !== '') {
    $searchHref .= '?q=' . urlencode($lastSearchQuery);
}

$headerPartialOnly = $headerPartialOnly ?? false;
$pageTitle = $pageTitle ?? 'LOFT &amp; LIVING BIRMINGHAM';
$extraHeadContent = $extraHeadContent ?? '';

if (!$headerPartialOnly):
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="https://use.typekit.net/lll5xwi.css">
    <link rel="stylesheet" href="https://use.typekit.net/ehd2wqk.css">
    <link rel="stylesheet" href="../css/header_footer_style.css?v=15">
    <link rel="stylesheet" href="../css/dark-mode.css?v=9">
    <link rel="stylesheet" href="../css/reusable_header.css?v=4">
    <?php echo $extraHeadContent; ?>
    <script src="../javascript/dark-mode.js"></script>
</head>
<body>
<?php endif; ?>

<header class="site-header">
    <div class="header-inner">
        <div class="header-left-tools">
            <button class="menu-btn" id="menu-toggle-btn" type="button" aria-label="Open menu">
                <img src="../images/header_footer_images/icon-menu.png" alt="Menu" class="ui-icon" id="menu-icon-img">
            </button>
            <img src="../images/header_footer_images/icon-moon.png" alt="Dark Mode" class="ui-icon" id="moon-icon" data-light-src="../images/header_footer_images/icon-moon.png" data-dark-src="../images/header_footer_images/icon-moon2.png" style="margin-left: 8px; margin-right: 8px; vertical-align: middle; cursor: pointer;">
            <a class="mini-search" href="<?php echo htmlspecialchars($searchHref); ?>" aria-label="Search" data-search-trigger="modal">
                <img src="../images/header_footer_images/icon-search.png" alt="Search" class="ui-icon" id="search-icon" style="vertical-align: middle;">
            </a>
        </div>

        <div class="logo-wrapper">
            <a href="homepage.php">
                <img src="../images/header_footer_images/logo1.png" alt="LOFT &amp; LIVING" class="main-logo">
            </a>
        </div>

        <div class="header-actions">
            <a href="favourites.php">
                <img src="../images/header_footer_images/icon-heart.png" alt="Favourites" class="ui-icon">
            </a>

            <div class="profile-wrapper" id="profile-wrapper">
                <button class="profile-btn" id="profile-toggle-btn" type="button" aria-haspopup="true" aria-expanded="false">
                    <img src="../images/header_footer_images/icon-user.png" alt="My Account" class="ui-icon">
                </button>

                <div class="profile-dropdown" id="profile-dropdown">
                    <?php if ($isLoggedIn): ?>
                        <div class="profile-welcome">Welcome, <?php echo htmlspecialchars($headerName); ?></div>
                    <?php else: ?>
                        <div class="profile-welcome">Welcome to Loft & Living</div>
                    <?php endif; ?>

                    <?php if (!$isLoggedIn): ?>
                        <a class="profile-link" href="signin.php">Sign in</a>
                        <a class="profile-link" href="signup.php">Sign Up</a>
                    <?php endif; ?>

                    <?php if ($isLoggedIn): ?>
                        <a class="profile-link" href="user_dash.php">My Account</a>
                        <a class="profile-link" href="user_order_history.php">My Orders</a>
                        <?php if ($isAdmin): ?>
                            <a class="profile-link" href="admin_dash.php">Admin Dashboard</a>
                        <?php endif; ?>
                        <a class="profile-link" href="signout.php">Sign Out</a>
                    <?php endif; ?>
                </div>
            </div>

            <a href="basket.php" class="basket-icon">
                <img src="../images/header_footer_images/icon-basket.png" alt="Basket" class="ui-icon">
                <span id="basket-count">0</span>
            </a>
        </div>
    </div>

    <nav class="dropdown-panel" id="dropdown-nav">
        <ul class="nav-links">
            <li><a href="livingroom.php">Living Room</a></li>
            <li><a href="bathroom.php">Bathroom</a></li>
            <li><a href="bedroom.php">Bedroom</a></li>
            <li><a href="office.php">Office</a></li>
            <li><a href="kitchen.php">Kitchen</a></li>
        </ul>
    </nav>
</header>
