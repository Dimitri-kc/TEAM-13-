<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Website Page</title>

<link rel="stylesheet" href="../css/header_footer_style.css">
<link rel="stylesheet" href="../css/category-css/livingroom-base.css">
<link rel="stylesheet" href="../css/category-css/livingroom-structure.css">
<link rel="stylesheet" href="../css/category-css/livingroom-reusable.css">
<link rel="stylesheet" href="../css/category-css/livingroom-page.css">

<style>
:root{
  --bg:#ffffff;
  --text:#1a1a1a;
  --muted:#666666;
  --card:#f5f5f5;
  --border:#e0e0e0;
  --header:#ffffff;
  --footer:#ffffff;
  --input-bg:#ffffff;
  --input-border:#cfcfcf;
  --btn-bg:#2C2C2C;
  --btn-text:#ffffff;
  --link:#1a1a1a;
}

body{
  margin:0;
  font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background:var(--bg);
  color:var(--text);
}

a{ color:var(--link); }

.site-header{
  background:var(--header);
  border-bottom:1px solid var(--border);
}

.site-footer{
  background:var(--footer);
  border-top:1px solid var(--border);
}

.left-section,
.right-section,
.card,
.box{
  background:var(--card);
  border:1px solid var(--border);
}

input, select, textarea{
  background:var(--input-bg);
  color:var(--text);
  border:1px solid var(--input-border);
}

button{
  background:var(--btn-bg);
  color:var(--btn-text);
}

p, small, .muted{
  color:var(--muted);
}

.dark-toggle{
  background:none;
  border:none;
  cursor:pointer;
  padding:0;
}

.ui-icon{
  width:24px;
  height:24px;
  display:block;
}

.dark-mode{
  --bg:#ccc;
  --text:#f2f2f2;
  --muted:#cfcfcf;
  --card:#3a3a3a;
  --border:#4a4a4a;
  --header:#3a3a3a;
  --footer:#3a3a3a;
  --input-bg:#404040;
  --input-border:#555555;
  --btn-bg:#e6e6e6;
  --btn-text:#1a1a1a;
  --link:#ffffff;
}

body.dark-mode .site-footer ul li a{
  color:#c2c2c2;
}

body.dark-mode .site-footer ul li a:hover{
  color:#ffffff;
}

.dark-mode .ui-icon{
  filter: invert(1);
}
</style>
</head>

<body>

<header class="site-header">
  <div class="header-inner">
    <button class="menu-btn" id="menu-toggle-btn" type="button">
      <img src="../images/header_footer_images/icon-menu.png" alt="Menu" class="ui-icon" id="menu-icon-img" />
    </button>

    <div class="logo-wrapper">
      <a href="homepage.php">
        <img src="../images/header_footer_images/logo.png" alt="LOFT & LIVING" class="main-logo" />
      </a>
    </div>

    <div class="header-actions">
      <button id="dark-mode-toggle" class="dark-toggle" type="button">
        <img src="../images/header_footer_images/lightmoon.png" id="dark-icon" class="ui-icon" alt="Toggle dark mode">
      </button>

      <a href="favourites.php">
        <img src="../images/header_footer_images/icon-heart.png" alt="Favourites" class="ui-icon" />
      </a>
      <a href="signin.php">
        <img src="../images/header_footer_images/icon-user.png" alt="My Account" class="ui-icon" />
      </a>
      <a href="basket.php" class="basket-icon">
        <img src="../images/header_footer_images/icon-basket.png" alt="Basket" class="ui-icon" />
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
      <li class="nav-divider"><a href="signin.php">My Account</a></li>
    </ul>
  </nav>
</header>

<main style="max-width:1000px;margin:40px auto;padding:0 20px;">
  <div class="left-section" style="padding:22px;border-radius:14px;margin-bottom:20px;">
    <h2>Example Content</h2>
    <p class="muted">All site areas will follow the theme.</p>
    <label class="muted">Example input</label><br>
    <input type="text" value="Test" style="padding:10px 12px;border-radius:8px;width:100%;max-width:420px;">
  </div>

  <div class="right-section" style="padding:22px;border-radius:14px;">
    <button style="padding:12px 16px;border-radius:8px;border:none;cursor:pointer;">Example Button</button>
  </div>
</main>

<footer class="site-footer">
  <div class="footer-inner">
    <div class="footer-section social-links">
      <a href="#">
        <img src="../images/header_footer_images/icon-twitter.png" alt="Twitter" class="social-icon ui-icon" />
      </a>
      <a href="#">
        <img src="../images/header_footer_images/icon-instagram.png" alt="Instagram" class="social-icon ui-icon" />
      </a>
    </div>

    <div class="footer-section">
      <h4>Navigation</h4>
      <ul>
        <li><a href="homepage.php">Homepage</a></li>
        <li><a href="signin.php">My Account</a></li>
        <li><a href="favourites.php">Favourites</a></li>
        <li><a href="basket.php">Basket</a></li>
        <li><a href="darkmode.php">Dark Mode</a></li>
      </ul>
    </div>

    <div class="footer-section">
      <h4>Categories</h4>
      <ul>
        <li><a href="livingroom.php">Living Room</a></li>
        <li><a href="office.php">Offices</a></li>
        <li><a href="kitchen.php">Kitchen</a></li>
        <li><a href="bathroom.php">Bathrooms</a></li>
        <li><a href="bedroom.php">Bedrooms</a></li>
      </ul>
    </div>

    <div class="footer-section">
      <h4>More...</h4>
      <ul>
        <li><a href="contact.php">Contact Us</a></li>
        <li><a href="about.php">About Us</a></li>
      </ul>
    </div>
  </div>
</footer>

<script>
const toggle=document.getElementById("dark-mode-toggle");
const body=document.body;
const icon=document.getElementById("dark-icon");
const lightIcon="../images/header_footer_images/lightmoon.png";
const darkIcon="../images/header_footer_images/darkmoon.png";

function syncIcon(){
  icon.src = body.classList.contains("dark-mode") ? darkIcon : lightIcon;
}

if(localStorage.getItem("theme")==="dark"){
  body.classList.add("dark-mode");
}
syncIcon();

toggle.addEventListener("click",()=>{
  body.classList.toggle("dark-mode");
  localStorage.setItem("theme", body.classList.contains("dark-mode") ? "dark" : "light");
  syncIcon();
});
</script>

</body>
</html>