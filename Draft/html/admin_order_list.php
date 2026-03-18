<?php
require_once '../backend/services/userFunctions.php';
require_admin_page('/TEAM-13-/Draft/html/signin.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Admin - Orders & Shipments</title>

<link rel="stylesheet" href="../css/header_footer_style.css?v=14">
<link rel="stylesheet" href="../css/category-css/livingroom-base.css">
<link rel="stylesheet" href="../css/category-css/livingroom-structure.css">
<link rel="stylesheet" href="../css/category-css/livingroom-reusable.css">
<link rel="stylesheet" href="../css/category-css/livingroom-page.css">

<style>

body{
font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;
background:#fff;
margin:0;
padding:0;
color:#1a1a1a;
}

.admin-container{
max-width:900px;
margin:20px auto 0 auto;
}

h1{
font-weight:700;
text-align:left;
font-size:30px;
margin-bottom:4px;
}

.subheader{
color:#6c6c6c;
font-weight:400;
font-size:14px;
margin-top:0;
margin-bottom:24px;
}

.orders-grid{
display:grid;
grid-template-columns:repeat(2,1fr);
gap:16px;
margin-bottom:50px;
}

.order-card{
border:1px solid #e2e2e2;
border-radius:6px;
padding:12px 16px;
display:flex;
align-items:center;
gap:12px;
}

.order-card img{
width:80px;
height:80px;
object-fit:cover;
border-radius:4px;
background:#eee;
flex-shrink:0;
}

.order-details{
flex-grow:1;
font-size:14px;
}

.order-status{
font-weight:600;
font-size:12px;
color:#333;
margin-bottom:4px;
}

.order-number{
font-weight:700;
font-size:16px;
margin:0 0 4px 0;
}

.customer-name{
margin:0;
font-weight:500;
color:#555;
}

.order-actions{
display:flex;
gap:10px;
}

button{
border-radius:6px;
border:none;
padding:6px 14px;
font-size:13px;
font-weight:600;
cursor:pointer;
white-space:nowrap;
}

.btn-view-edit{
background:#ddd;
color:#333;
}

.btn-view-edit:hover{
background:#ccc;
}

.btn-cancel{
background:#2C2C2C;
color:white;
}

.btn-cancel:hover{
background:#1a1a1a;
}

/* HEADER SPACING FIX */

.site-header{
padding:10px 0;
}

.main-logo{
height:48px;
}

/* MODAL */

.edit-panel-overlay{
display:none;
position:fixed;
inset:0;
background:rgba(0,0,0,0.45);
z-index:999;
align-items:center;
justify-content:center;
}

.edit-panel{
background:#fff;
width:100%;
max-width:500px;
border-radius:12px;
padding:24px;
box-shadow:0 10px 30px rgba(0,0,0,0.18);
}

.edit-panel h2{
margin-top:0;
margin-bottom:18px;
font-size:24px;
}

.edit-field{
margin-bottom:16px;
}

.edit-field label{
display:block;
font-weight:600;
margin-bottom:6px;
}

.edit-field input,
.edit-field select{
width:100%;
padding:10px 12px;
border:1px solid #d8d8d8;
border-radius:8px;
}

.edit-panel-actions{
display:flex;
gap:10px;
margin-top:18px;
}

.btn-save{
background:#2C2C2C;
color:#fff;
}

.btn-close{
background:#ddd;
}

@media (max-width:600px){

.orders-grid{
grid-template-columns:1fr;
}

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

<div class="admin-container">

<h1>Orders and Shipments</h1>
<p class="subheader">View recent customer orders and make edits or cancel</p>

<div class="orders-grid" id="ordersContainer"></div>

</div>

<!-- MODAL -->

<div class="edit-panel-overlay" id="editPanelOverlay">

<div class="edit-panel">

<h2>View & Edit Order</h2>

<div class="edit-field">
<label for="editOrderId">Order Number</label>
<input type="text" id="editOrderId" readonly>
</div>

<div class="edit-field">
<label for="editCustomerName">Customer Name</label>
<input type="text" id="editCustomerName" readonly>
</div>

<div class="edit-field">
<label for="editOrderStatus">Order Status</label>

<select id="editOrderStatus">
<option value="Pending">Pending</option>
<option value="Processing">Processing</option>
<option value="Shipped">Shipped</option>
<option value="Delivered">Delivered</option>
<option value="Cancelled">Cancelled</option>
</select>

</div>

<div class="edit-panel-actions">

<button class="btn-save" id="saveOrderChanges">Save Changes</button>
<button class="btn-close" id="closeEditPanel">Close</button>

</div>

</div>

</div>

<script>

document.addEventListener("DOMContentLoaded",loadOrders);

let currentOrderCard=null;

async function loadOrders(){

const response=await fetch("/TEAM-13-/Draft/backend/routes/orderRoutes.php?action=fetchAll");
const result=await response.json();

const container=document.getElementById("ordersContainer");
container.innerHTML="";

result.data.forEach(order=>{

const image = order.image
? "/TEAM-13-/Draft/" + order.image.replace("../","")
: "https://via.placeholder.com/80";

const customerName = order.customer_name ?? ("Customer ID: "+order.user_ID);

const card=`

<div class="order-card" data-order-id="${order.order_ID}">

<img src="${image}" alt="Product Image">

<div class="order-details">

<p class="order-status">Order Status: ${order.order_status}</p>

<p class="order-number">Order #${order.order_ID}</p>

<p class="customer-name">${customerName}</p>

</div>

<div class="order-actions">

<button class="btn-view-edit">View & Edit</button>

<button class="btn-cancel">Cancel</button>

</div>

</div>

`;

container.insertAdjacentHTML("beforeend",card);

});

}

document.getElementById("ordersContainer").addEventListener("click",function(e){

const orderCard=e.target.closest(".order-card");
if(!orderCard) return;

const orderId=orderCard.dataset.orderId;

if(e.target.classList.contains("btn-view-edit")){

currentOrderCard=orderCard;

const status = orderCard.querySelector(".order-status").innerText.replace("Order Status: ","");
const name = orderCard.querySelector(".customer-name").innerText;

document.getElementById("editOrderId").value = orderId;
document.getElementById("editCustomerName").value = name;
document.getElementById("editOrderStatus").value = status;

document.getElementById("editPanelOverlay").style.display="flex";

}

if(e.target.classList.contains("btn-cancel")){
updateOrderStatus(orderId,"Cancelled",orderCard);
}

});

document.getElementById("saveOrderChanges").addEventListener("click",async function(){

const orderId=document.getElementById("editOrderId").value;
const status=document.getElementById("editOrderStatus").value;

await updateOrderStatus(orderId,status,currentOrderCard);

document.getElementById("editPanelOverlay").style.display="none";

});

document.getElementById("closeEditPanel").addEventListener("click",function(){
document.getElementById("editPanelOverlay").style.display="none";
});

async function updateOrderStatus(orderId,status,card){

const formData = new FormData();

formData.append("action","updateStatus");
formData.append("order_ID",orderId);
formData.append("order_status",status);

await fetch("/TEAM-13-/Draft/backend/routes/orderRoutes.php",{
method:"POST",
body:formData
});

if(card){
card.querySelector(".order-status").innerText="Order Status: "+status;
}

}

</script>

<!-- Footer -->

<?php $footerPartialOnly = true; include 'footer.php'; ?>

</body>
</html>


