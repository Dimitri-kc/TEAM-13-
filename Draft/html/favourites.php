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
        body { margin: 0; background: #f5f1eb; color: #1f1a17; }
        .favourites-main { padding: 56px 28px 72px; min-height: 600px; }
        .wrap { max-width: 1180px; margin: 0 auto; }
        .page-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 24px;
            margin-bottom: 28px;
        }
        .page-head-left { max-width: 620px; }
        h1 {
            font-family: 'ivybodoni', serif;
            font-size: 40px;
            font-weight: 500;
            letter-spacing: 0.03em;
            line-height: 1.05;
            margin: 0 0 10px;
            color: #1f1a17;
        }
        .sub {
            font-family: 'mr-eaves-modern', Arial, sans-serif;
            font-size: 18px;
            color: #6b6157;
            margin: 0;
            line-height: 1.5;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 24px;
        }

        .fav-card {
            position: relative;
            border: 1px solid rgba(111, 103, 92, 0.16);
            border-radius: 24px;
            padding: 18px;
            display: flex;
            flex-direction: column;
            align-items: stretch;
            background: rgba(255, 255, 255, 0.92);
            box-shadow: 0 16px 40px rgba(50, 41, 35, 0.08);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }

        .thumb {
            width: 100%;
            aspect-ratio: 1 / 1;
            border-radius: 18px;
            background: #ece6df;
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
            padding: 12px 22px;
            border: 1.5px solid #8C8376;
            border-radius: 999px;
            background: #8C8376;
            color: #fff;
            font-family: 'mr-eaves-modern', Arial, sans-serif;
            font-size: 16px;
            font-weight: 500;
            letter-spacing: 0.03em;
            white-space: nowrap;
            cursor: pointer;
            transition: background 0.18s ease, border-color 0.18s ease, transform 0.18s ease;
        }

        .btn:hover {
            background: #6F675C;
            border-color: #6F675C;
            transform: translateY(-1px);
        }

        .removeBtn {
            position: absolute;
            top: 16px;
            right: 16px;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            border: 1px solid rgba(111, 103, 92, 0.16);
            background: rgba(255, 255, 255, 0.94);
            cursor: pointer;
            color: #4f443b;
            font-size: 20px;
            line-height: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.18s ease, color 0.18s ease;
        }

        .removeBtn:hover {
            background: #6f675c;
            color: #fff;
        }

        .card-meta {
            display: flex;
            flex-direction: column;
            gap: 6px;
            padding: 18px 4px 0;
            flex: 1;
        }

        .card-title {
            font-family: 'ivybodoni', serif;
            font-size: 20px;
            font-weight: 500;
            line-height: 1.2;
            color: #1f1a17;
            margin: 0;
        }

        .card-price {
            font-family: 'mr-eaves-modern', Arial, sans-serif;
            font-size: 18px;
            color: #6b6157;
            margin: 0;
        }

        .card-actions {
            margin-top: 18px;
            display: flex;
            justify-content: flex-end;
        }

        .clear-form {
            margin: 0;
            flex-shrink: 0;
        }

        .clear-btn {
            background: #8C8376;
            color: #fff;
            border-color: #8C8376;
        }

        .clear-btn:hover {
            background: #6F675C;
            color: #fff;
            border-color: #6F675C;
        }

        .empty {
            padding: 26px 24px;
            border: 1px dashed rgba(111, 103, 92, 0.26);
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.82);
            color: #6b6157;
            font-family: 'mr-eaves-modern', Arial, sans-serif;
            font-size: 18px;
            line-height: 1.5;
        }

        @media (max-width: 900px) { .grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 720px) {
            .page-head {
                flex-direction: column;
                align-items: stretch;
            }
            .clear-form {
                align-self: flex-start;
            }
        }
        @media (max-width: 580px) {
            .favourites-main { padding: 42px 18px 56px; }
            .grid { grid-template-columns: 1fr; }
            h1 { font-size: 34px; }
        }

.back-home {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 18px;
    padding: 12px 20px;
    border: 1.5px solid rgba(111, 103, 92, 0.26);
    border-radius: 999px;
    background: transparent;
    font-family: 'mr-eaves-modern', Arial, sans-serif;
    font-size: 17px;
    text-decoration: none;
    color: #6f675c;
    font-weight: 500;
    transition: background 0.18s ease, border-color 0.18s ease, transform 0.18s ease, color 0.18s ease;
}

.back-home:hover {
    background: rgba(111, 103, 92, 0.08);
    border-color: rgba(111, 103, 92, 0.4);
    color: #4d463d;
    text-decoration: none;
    transform: translateY(-1px);
}
    </style>
HTML;

include 'header.php';
?>

<main class="favourites-main">
    <div class="wrap">
<a href="#" onclick="goBack(event)" class="back-home">← Go Back</a>

<div class="page-head">
    <div class="page-head-left">
        <h1>My Favourites</h1>
        <p class="sub">See an item you like? Come back to it later at any time.</p>
    </div>

    <form method="post" class="clear-form">
        <button type="submit" name="clear_favs" class="btn clear-btn" onclick="return confirm('Clear all favourites?')">
            Clear All Favourites
        </button>
    </form>
</div>
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

    <div class="card-meta">
        <h2 class="card-title"><?= htmlspecialchars($p["name"]) ?></h2>
        <p class="card-price">£<?= number_format($p["price"],2) ?></p>
    </div>

    <div class="card-actions">
        <button
            class="btn js-add-to-bag"
            type="button"
            data-product-id="<?= $pid ?>"
            >Add to Bag
        </button>
    </div>

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
