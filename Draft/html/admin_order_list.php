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

<link rel="stylesheet" href="../css/header_footer_style.css?v=21">
<link rel="stylesheet" href="../css/admin_order_list.css?v=2">
    <link rel="stylesheet" href="https://use.typekit.net/lll5xwi.css">
    <link rel="stylesheet" href="https://use.typekit.net/ehd2wqk.css">
    <link rel="stylesheet" href="../css/dark-mode.css?v=13">
    <link rel="stylesheet" href="../css/reusable_header.css?v=11">
    <script src="../javascript/dark-mode.js"></script>
</head>

<body class="admin-order-list-page">

<?php $headerPartialOnly = true; include 'header.php'; ?>

<div class="admin-container">

<div class="page-topbar">
<div class="page-topbar-copy">

<h1 class="page-title">Orders and Shipments</h1>
<p class="subheader">View recent customer orders and make edits or cancel</p>

</div>

<button
type="button"
class="return-btn"
onclick="if (window.history.length > 1) { window.history.back(); } else { window.location.href='admin_dash.php'; }"
>
Return to Previous Page
</button>

</div>

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

function resolveProductImage(imagePath) {
    if (!imagePath) {
        return "../images/basket-images/sofa.jpg";
    }

    if (/^(https?:)?\/\//.test(imagePath)) {
        return imagePath;
    }

    let cleaned = imagePath
        .replace(/^(\.\.\/)+/, '')
        .replace(/^\/+/, '');

    if (!cleaned.startsWith('images/')) {
        cleaned = `images/${cleaned}`;
    }

    return `../${cleaned}`;
}

async function loadOrders(){

const response=await fetch("/TEAM-13-/Draft/backend/routes/orderRoutes.php?action=fetchAll");
const result=await response.json();

const container=document.getElementById("ordersContainer");
container.innerHTML="";

result.data.forEach(order=>{

const image = resolveProductImage(order.image);

const customerName = order.customer_name ?? ("Customer ID: "+order.user_ID);

const card=`

<div class="order-card" data-order-id="${order.order_ID}">

<img src="${image}" alt="Product Image" onerror="this.onerror=null;this.src='../images/basket-images/sofa.jpg';">

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
