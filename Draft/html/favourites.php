<?php 
include '../backend/config/db_connect.php'; 
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Require login
if (!isset($_SESSION['user_ID'])) {
    header("Location: signin.php");
    exit;
}

$user_id = $_SESSION['user_ID'];

$favs = [];
$dbError = null;
// CLEAR ALL FAVOURITES
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clear_favs'])) {

    $stmt = $conn->prepare("DELETE FROM favourites WHERE user_ID = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    // reload page so favourites disappear
    header("Location: favourites.php");
    exit;
}
// Load favourites from database
$sql = "SELECT p.product_ID, p.name, p.price, p.image
        FROM favourites f
        JOIN products p ON f.product_ID = p.product_ID
        WHERE f.user_ID = ?";

try {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result();

    while ($row = $res->fetch_assoc()) {
        $favs[] = $row;
    }

    $stmt->close();
} catch (mysqli_sql_exception $e) {
    // If the favourites table doesn't exist (or any SQL error), don't crash the page.
    $dbError = $e->getMessage();
    $favs = [];
}
?>
<?php
$pageTitle = 'My Favourites | LOFT &amp; LIVING';
$extraHeadContent = <<<'HTML'
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background:#fff; color:#111; }
        .favourites-main { padding: 50px; min-height: 600px; }
        .wrap { max-width: 980px; margin: 0 auto; padding: 26px 18px 40px; }
        h1 { font-size: 18px; margin: 0 0 4px; font-weight: 700; }
        .sub { font-size: 12px; color: #666; margin-bottom: 18px; }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 22px;
        }

        .fav-card {
            position: relative;
            border: 1px solid #eaeaea;
            border-radius: 6px;
            padding: 16px;
            display: flex;
            flex-direction: column;
            align-items: center;
            background: #ffffff;
            text-align: center;
        }

        .thumb {
            width: 200px;
            height: 200px;
            border-radius: 4px;
            background: #e9e9e9;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .btn {
            padding: 7px 10px;
            border: none;
            border-radius: 6px;
            background: #111;
            color: #fff;
            font-size: 11px;
            white-space: nowrap;
            margin-left: 10px;
        }

        .removeBtn {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 26px;
            height: 26px;
            border-radius: 7px;
            border: 1px solid #e1e1e1;
            background: #fff;
            cursor: pointer;
            font-size: 16px;
            line-height: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .empty { padding: 18px; border: 1px dashed #ddd; border-radius: 8px; color:#666; }
        .actions { margin-top: 14px; font-size: 12px; }
        .actions a { color:#111; text-decoration: none; border-bottom: 1px solid #ddd; }

        @media (max-width: 900px) { .grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 580px) { .grid { grid-template-columns: 1fr; } }

.back-home {
    display: inline-block;
    margin-bottom: 15px;
    font-family: 'Jost', sans-serif;
    font-size: 14px;
    text-decoration: none;
    color: var(--accent);
    font-weight: 500;
    transition: 0.2s ease;
}

.back-home:hover {
    text-decoration: underline;
}
    </style>
HTML;

include 'header.php';
?>

<main class="favourites-main">
    <div class="wrap"><div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:10px;">
   <a href="#" onclick="goBack(event)" class="back-home">← Go Back</a>
</div>

<h1>My Favourites</h1>
<div class="sub">See an item you like? Come back to it later at any time</div>


      <form method="post" style="margin-bottom:15px;">
    <button type="submit" name="clear_favs" class="btn" onclick="return confirm('Clear all favourites?')">
        Clear All Favourites
    </button>
</form>
        <?php if (empty($favs)): ?>
            <div class="empty">No favourites yet. Go to categories and like a product.</div>
   
        <?php else: ?>

            <div class="grid">
                <?php foreach ($favs as $p): ?>
                    <?php 
                        $pid = (int)$p["product_ID"]; 
                        $imagePath = "../images/livingroom-images/" . $p["image"];
                    ?>
                    
                   <div class="fav-card">

    <form method="post" action="favourite_remove.php" style="margin:0;">
        <input type="hidden" name="product_id" value="<?= $pid ?>">
        <input type="hidden" name="redirect" value="favourites.php">
        <button class="removeBtn" type="submit" title="Remove">×</button>
    </form>

    <div class="thumb">
        <?php if (!empty($p["image"])): ?>
            <img src="../images/<?= htmlspecialchars($p["image"]) ?>" alt="">
        <?php else: ?>
            <span style="color:#cfcfcf; font-size:12px;">IMG</span>
        <?php endif; ?>
    </div>

    <div style="font-size:13px; font-weight:600; margin-top:10px;">
        <?= htmlspecialchars($p["name"]) ?>
    </div>


    <div style="font-size:12px; color:#666;">
        £<?= number_format($p["price"],2) ?>
    </div>
            
    <button
        class="btn js-add-to-bag"
        type="button"
        data-product-id="<?= $pid ?>"
        style="margin-top:10px;"
        >Add to bag
    </button>

</div>

                <?php endforeach; ?>
            </div>

        <?php endif; ?>
    </div>
</main>

<?php include 'footer.php'; ?>

<script> 
//Go back button
function goBack(e) {
    e.preventDefault();
    if (document.referrer && document.referrer !== window.location.href) {
        history.back();
    } else {
        window.location.href = "homepage.php"; // fallback
    }
}
//add to bag functionality 
document.addEventListener('click', async (e) => {
    const btn = e.target.closest('.js-add-to-bag');
    if (!btn) return;

    const productId = parseInt(btn.dataset.productId, 10);
    if (!productId || typeof window.addToBasket !== 'function') return;

    btn.disabled = true;
    const ok = await window.addToBasket(productId, 1, btn);
    if (ok) btn.textContent = 'Added';
    btn.disabled = false;
});



</script>