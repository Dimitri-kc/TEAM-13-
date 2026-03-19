<?php
require_once '../backend/services/userFunctions.php';
require_admin_page('/TEAM-13-/Draft/html/signin.php');

require_once '../backend/config/db_connect.php'; 

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
    <link rel="stylesheet" href="https://use.typekit.net/lll5xwi.css">
    <link rel="stylesheet" href="https://use.typekit.net/ehd2wqk.css">
    <link rel="stylesheet" href="../css/dark-mode.css?v=13">
    <link rel="stylesheet" href="../css/header_footer_style.css?v=16">
    <link rel="stylesheet" href="../css/reusable_header.css?v=6">
    <link rel="stylesheet" href="../css/admin_realtime_reports.css?v=1">
    <script src="../javascript/dark-mode.js"></script>
    
</head>

<body class="admin-reports-page">

<?php $headerPartialOnly = true; include 'header.php'; ?>

<div class="reports-wrapper">

    <div class="reports-header">
        <h1>Real-Time Reports</h1>
        <p>Track stock performance and customer sign-up activity across the store in one place.</p>
    </div>

    <section class="report-card report-card-wide">
        <div class="report-card-head">
            <div>
                <h2>Real-Time Stock Levels</h2>
                <p>View current stock by product and filter by category.</p>
            </div>
            <div class="report-filter">
                <label for="categoryFilter">Filter by Category</label>
                <select id="categoryFilter">
                    <option value="all">All Products</option>
                    <option value="Living room">Living Room</option>
                    <option value="Kitchen">Kitchen</option>
                    <option value="Office">Office</option>
                    <option value="Bathroom">Bathroom</option>
                    <option value="Bedroom">Bedroom</option>
                </select>
            </div>
        </div>
        <div class="chart-container">
            <canvas id="stockChart"></canvas>
        </div>
    </section>

    <section class="report-card">
        <div class="report-card-head">
            <div>
                <h2>Daily Customer Sign-Ups</h2>
                <p>Monitor sign-up activity over time.</p>
            </div>
        </div>
        <div class="chart-container">
            <canvas id="signupChart"></canvas>
        </div>
    </section>

    <section class="report-card report-summary-card">
        <div class="report-card-head">
            <div>
                <h2>Quick Notes</h2>
                <p>At-a-glance guidance for interpreting the report data.</p>
            </div>
        </div>
        <div class="report-summary-list">
            <div class="report-summary-item">
                <span class="report-summary-label">Stock alert</span>
                <span class="report-summary-value">Products under 5 units are highlighted in red.</span>
            </div>
            <div class="report-summary-item">
                <span class="report-summary-label">Sign-up trend</span>
                <span class="report-summary-value">Use the line chart to spot recent growth or slow periods.</span>
            </div>
            <div class="report-summary-item">
                <span class="report-summary-label">Next step</span>
                <span class="report-summary-value">Prioritize low-stock products before they affect active orders.</span>
            </div>
        </div>
    </section>

</div>

<script>
// product stock chart data
const productLabels = <?php echo json_encode($productLabels); ?>;
const productStock = <?php echo json_encode($productStock); ?>;
const productColors = <?php echo json_encode($productColors); ?>;
const productCategories = <?php echo json_encode($productCategories); ?>;

function getChartTheme() {
    const isDark = document.documentElement.classList.contains('dark-mode');
    return {
        tickColor: isDark ? '#d9d1c7' : '#3e3833',
        gridColor: isDark ? 'rgba(255,255,255,0.12)' : 'rgba(31,26,23,0.1)',
        borderColor: isDark ? 'rgba(255,255,255,0.18)' : 'rgba(31,26,23,0.14)',
        lineColor: isDark ? '#8ab4f8' : '#4e79a7',
        lineFill: isDark ? 'rgba(138, 180, 248, 0.18)' : 'rgba(78, 121, 167, 0.2)',
        pointColor: isDark ? '#8ab4f8' : '#4e79a7',
        barBorder: isDark ? 'rgba(255,255,255,0.12)' : '#333'
    };
}

function applyChartTheme(chart, overrides = {}) {
    const theme = { ...getChartTheme(), ...overrides };
    chart.options.scales.x = {
        ticks: {
            color: theme.tickColor
        },
        grid: {
            color: theme.gridColor,
            borderColor: theme.borderColor
        }
    };
    chart.options.scales.y = {
        beginAtZero: true,
        ticks: {
            color: theme.tickColor
        },
        grid: {
            color: theme.gridColor,
            borderColor: theme.borderColor
        }
    };
    chart.update();
}

const stockChart = new Chart(document.getElementById('stockChart'), {
    type: 'bar',
    data: {
        labels: productLabels,
        datasets: [{
            label: 'Stock Level',
            data: productStock,
            backgroundColor: productColors,
            borderColor: getChartTheme().barBorder,
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {},
        plugins: { legend: { display: false } }
    }
});
applyChartTheme(stockChart);

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

const signupChart = new Chart(document.getElementById('signupChart'), {
    type: 'line',
    data: {
        labels: dayLabels,
        datasets: [{
            label: 'Daily Sign-Ups',
            data: signupCounts,
            borderColor: getChartTheme().lineColor,
            backgroundColor: getChartTheme().lineFill,
            borderWidth: 2,
            tension: 0.3,
            pointRadius: 4,
            pointBackgroundColor: getChartTheme().pointColor
        }]
    },
    options: {
        responsive: true,
        scales: {}
    }
});
applyChartTheme(signupChart);

const darkModeObserver = new MutationObserver(() => {
    const theme = getChartTheme();
    stockChart.data.datasets[0].borderColor = theme.barBorder;
    signupChart.data.datasets[0].borderColor = theme.lineColor;
    signupChart.data.datasets[0].backgroundColor = theme.lineFill;
    signupChart.data.datasets[0].pointBackgroundColor = theme.pointColor;
    applyChartTheme(stockChart);
    applyChartTheme(signupChart);
});

darkModeObserver.observe(document.documentElement, {
    attributes: true,
    attributeFilter: ['class']
});
</script>

<?php include 'footer.php'; ?>
