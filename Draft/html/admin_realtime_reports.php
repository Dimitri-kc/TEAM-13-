<?php
require_once '../backend/services/userFunctions.php';
require_admin_page('/TEAM-13-/Draft/html/signin.php');

require_once 'db_connect.php'; 
include "header.php";


// product stock query
$query1 = "
    SELECT p.name, p.stock, c.name AS category
    FROM products p
    JOIN categories c ON p.category_id = c.category_id
    ORDER BY p.category_id, p.product_id
";

$result1 = $conn->query($query1);

$productLabels = [];
$productStock = [];
$productColors = [];
$productCategories = [];

$categoryColors = [
    "Living room" => "#0d436e",
    "Kitchen"     => "#0d436e",
    "Office"      => "#0d436e",
    "Bathroom"    => "#0d436e",
    "Bedroom"     => "#0d436e"
];

while ($row = $result1->fetch_assoc()) {
    $productLabels[] = $row['name'];
    $productStock[] = (int)$row['stock'];
    $productCategories[] = $row['category'];

    // low stock alert, change to red if stock is below 5
    if ((int)$row['stock'] < 5) {
        $productColors[] = "#d9534f";
    } else {
        $productColors[] = $categoryColors[$row['category']];
    }
}


// daily customer sign up query

$query2 = "
    SELECT DATE(created_at) AS signup_date, COUNT(*) AS signups
    FROM users
    GROUP BY DATE(created_at)
    ORDER BY signup_date
";

$result2 = $conn->query($query2);

$dayLabels = [];
$signupCounts = [];

while ($row = $result2->fetch_assoc()) {
    $dayLabels[] = $row['signup_date'];
    $signupCounts[] = (int)$row['signups'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Real-Time Reports | Loft & Living</title>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        
        body {
            margin: 90px 0 0;
            padding: 0;
            background-color: #F4F1EC;
            color: #2B2B2B;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            min-width: 1024px;
        }

        .content-wrapper {
            width: calc(100% - 48px);
            max-width: 1050px;
            margin: 0 auto;
            padding: 40px 0 60px;
        }


        
        .chart-container {
            background: #fff;
            max-width: 1075px;
            margin: 0 auto;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 4px 18px rgba(0,0,0,0.08);
            margin-bottom: 60px;
        }

        h1 {
            font-family: "Ibarra Real Nova", serif;
            font-size: 20px;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin-bottom: 25px;
        }

        
        #categoryFilter {
            padding: 10px 15px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 14px;
            margin-bottom: 25px;
            font-family: "Ibarra Real Nova", serif;
        }
    </style>
</head>

<body>

<div class="content-wrapper">

    <label for="categoryFilter">Filter by Category:</label>
    <select id="categoryFilter">
        <option value="all">All Products</option>
        <option value="Living room">Living Room</option>
        <option value="Kitchen">Kitchen</option>
        <option value="Office">Office</option>
        <option value="Bathroom">Bathroom</option>
        <option value="Bedroom">Bedroom</option>
    </select>

    <h1>Real-Time Stock Levels (Per Product)</h1>
    <div class="chart-container">
        <canvas id="stockChart"></canvas>
    </div>

    <h1>Daily Customer Sign-Ups</h1>
    <div class="chart-container">
        <canvas id="signupChart"></canvas>
    </div>

</div>

<script>
// profile dropdown
document.getElementById("profile-toggle-btn").addEventListener("click", () => {
    document.getElementById("profile-dropdown").classList.toggle("open");
});

// product stock chart data
const productLabels = <?php echo json_encode($productLabels); ?>;
const productStock = <?php echo json_encode($productStock); ?>;
const productColors = <?php echo json_encode($productColors); ?>;
const productCategories = <?php echo json_encode($productCategories); ?>;

const stockChart = new Chart(document.getElementById('stockChart'), {
    type: 'bar',
    data: {
        labels: productLabels,
        datasets: [{
            label: 'Stock Level',
            data: productStock,
            backgroundColor: productColors,
            borderColor: '#333',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: { beginAtZero: true }
        },
        plugins: { legend: { display: false } }
    }
});

// category filters
document.getElementById("categoryFilter").addEventListener("change", function () {
    const selected = this.value;

    const filteredLabels = [];
    const filteredStock = [];
    const filteredColors = [];

    for (let i = 0; i < productLabels.length; i++) {
        if (selected === "all" || productCategories[i] === selected) {
            filteredLabels.push(productLabels[i]);
            filteredStock.push(productStock[i]);
            filteredColors.push(productColors[i]);
        }
    }

    stockChart.data.labels = filteredLabels;
    stockChart.data.datasets[0].data = filteredStock;
    stockChart.data.datasets[0].backgroundColor = filteredColors;

    stockChart.update();
});

// daily customer sign up chart 
const dayLabels = <?php echo json_encode($dayLabels); ?>;
const signupCounts = <?php echo json_encode($signupCounts); ?>;

new Chart(document.getElementById('signupChart'), {
    type: 'line',
    data: {
        labels: dayLabels,
        datasets: [{
            label: 'Daily Sign-Ups',
            data: signupCounts,
            borderColor: '#4e79a7',
            backgroundColor: 'rgba(78, 121, 167, 0.2)',
            borderWidth: 2,
            tension: 0.3,
            pointRadius: 4,
            pointBackgroundColor: '#4e79a7'
        }]
    },
    options: {
        responsive: true,
        scales: { y: { beginAtZero: true } }
    }
});
</script>

</body>
</html>
<?php include 'footer.php'; ?>