<!-- THIS FILE ISN'T BEING CALLED ANYWHERE IN THE CURRENT FLOW, FROM MVP1
 <?php
session_start();
require __DIR__ . "/../backend/config/db_connect.php";

if (!isset($_SESSION["user_id"])) $_SESSION["user_id"] = 1; // demo
$userId = (int)$_SESSION["user_id"];

// Fetch products
$products = $pdo->query("SELECT id, name, image, price FROM products ORDER BY id DESC")->fetchAll();

// Fetch favourites for this user into a set
$stmt = $pdo->prepare("SELECT product_id FROM favourites WHERE user_id = ?");
$stmt->execute([$userId]);
$favSet = [];
foreach ($stmt->fetchAll() as $row) $favSet[(int)$row["product_id"]] = true;
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Categories</title>
  <style>
    body { font-family: Arial, sans-serif; padding: 24px; }
    .top { display:flex; justify-content:space-between; align-items:center; max-width:980px; }
    .grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; max-width: 980px; margin-top: 16px; }
    .card { border: 1px solid #e8e8e8; border-radius: 10px; padding: 14px; }
    .img { width: 100%; aspect-ratio: 4/3; background: #eee; border-radius: 8px; overflow:hidden; display:flex; align-items:center; justify-content:center; }
    .img img { width:100%; height:100%; object-fit:cover; display:block; }
    .row { display:flex; align-items:center; justify-content:space-between; margin-top: 12px; gap: 10px; }
    .name { font-weight:600; }
    .price { color:#666; font-size: 13px; }
    .heartBtn {
      width: 36px; height: 36px; border-radius: 10px;
      border: 1px solid #ddd; background:#fff; cursor:pointer;
      display:flex; align-items:center; justify-content:center;
      font-size: 18px;
    }
    .heartBtn.liked { border-color:#111; }
    a { color:#111; text-decoration:none; border-bottom:1px solid #ddd; }
    @media (max-width: 900px) { .grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 580px) { .grid { grid-template-columns: 1fr; } }
  </style>
</head>
<body>
  <div class="top">
    <h2 style="margin:0;">Categories</h2>
    <a href="favorites.php">Go to Favorites</a>
  </div>

  <div class="grid">
    <?php foreach ($products as $p): $pid = (int)$p["id"]; $liked = isset($favSet[$pid]); ?>
      <div class="card">
        <div class="img">
          <?php if (!empty($p["image"]) && file_exists($p["image"])): ?>
            <img src="<?= htmlspecialchars($p["image"]) ?>" alt="">
          <?php else: ?>
            <span style="color:#aaa;">No Image</span>
          <?php endif; ?>
        </div>

        <div class="row">
          <div>
            <div class="name"><?= htmlspecialchars($p["name"]) ?></div>
            <div class="price">$<?= number_format((float)$p["price"], 2) ?></div>
          </div>

          <?php if (!$liked): ?>
            <form method="post" action="favourite_add.php" style="margin:0;">
              <input type="hidden" name="product_id" value="<?= $pid ?>">
              <input type="hidden" name="redirect" value="categories.php">
              <button class="heartBtn" type="submit" title="Like">♡</button>
            </form>
          <?php else: ?>
            <form method="post" action="favourite_remove.php" style="margin:0;">
              <input type="hidden" name="product_id" value="<?= $pid ?>">
              <input type="hidden" name="redirect" value="categories.php">
              <button class="heartBtn liked" type="submit" title="Unlike">♥</button>
            </form>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</body>
</html>
 -->