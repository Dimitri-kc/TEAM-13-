<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Website Page</title>

<link rel="stylesheet" href="../css/header_footer_style.css?v=16">
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
    <link rel="stylesheet" href="https://use.typekit.net/lll5xwi.css">
    <link rel="stylesheet" href="https://use.typekit.net/ehd2wqk.css">
    <link rel="stylesheet" href="../css/dark-mode.css?v=12">
    <link rel="stylesheet" href="../css/reusable_header.css?v=4">
    <script src="../javascript/dark-mode.js"></script>
</head>

<body>

<?php $headerPartialOnly = true; include 'header.php'; ?>

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

<?php $footerPartialOnly = true; include 'footer.php'; ?>

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