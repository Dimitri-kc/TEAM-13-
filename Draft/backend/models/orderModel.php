<?php

require_once __DIR__ . '/../config/db_connect.php';

class OrderModel {

    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    /* DASHBOARD STATS*/

    public function getTotalOrders() {

        $sql = "SELECT COUNT(*) AS total_orders FROM orders";
        $result = $this->conn->query($sql);

        return $result->fetch_assoc()['total_orders'];
    }

    public function getMonthlyOrders() {

        $sql = "SELECT COUNT(*) AS monthly_orders
                FROM orders
                WHERE MONTH(order_date) = MONTH(CURRENT_DATE())
                AND YEAR(order_date) = YEAR(CURRENT_DATE())";

        $result = $this->conn->query($sql);

        return $result->fetch_assoc()['monthly_orders'];
    }

    public function getPendingOrders() {

        $sql = "SELECT COUNT(*) AS pending_orders
                FROM orders
                WHERE order_status = 'Pending'";

        $result = $this->conn->query($sql);

        return $result->fetch_assoc()['pending_orders'];
    }

    public function getOrdersPerMonth() {

        $sql = "SELECT MONTH(order_date) AS label,
                COUNT(*) AS value
                FROM orders
                GROUP BY label
                ORDER BY label";

        $result = $this->conn->query($sql);

        $chart = [];

        while ($row = $result->fetch_assoc()) {

            $chart[] = [
                "label" => $row['label'],
                "value" => $row['value']
            ];

        }

        return $chart;
    }

    /* CREATE ORDER*/

    public function createOrder($user_ID, $total_price, $address) {

        $stmt = $this->conn->prepare(
            "INSERT INTO orders (user_ID, total_price, address)
             VALUES (?, ?, ?)"
        );

        if (!$stmt) return false;

        $stmt->bind_param("ids", $user_ID, $total_price, $address);

        if ($stmt->execute()) {

            return $this->conn->insert_id;

        }

        return false;
    }

    /* USER ORDERS */

    public function getOrdersByUser($user_ID) {

        $stmt = $this->conn->prepare(
            "SELECT *
             FROM orders
             WHERE user_ID = ?
             ORDER BY order_date DESC"
        );

        if (!$stmt) return [];

        $stmt->bind_param("i", $user_ID);

        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /* ADMIN - GET ALL ORDERS */

public function getAllOrders() {

$sql = "

SELECT 
    o.order_ID,
    o.user_ID,
    o.order_date,
    o.total_price,
    o.order_status,
    o.address,

    CONCAT(u.name,' ',u.surname) AS customer_name,

    p.image,
    r.status AS return_status

FROM orders o

LEFT JOIN users u 
ON o.user_ID = u.user_ID

LEFT JOIN order_items oi 
ON o.order_ID = oi.order_ID

LEFT JOIN products p 
ON oi.product_ID = p.product_ID

LEFT JOIN (
    SELECT order_ID, status
    FROM returns
    WHERE return_ID IN (
        SELECT MAX(return_ID)
        FROM returns
        GROUP BY order_ID
    )
) r
ON o.order_ID = r.order_ID

ORDER BY o.order_date DESC

";

$result = $this->conn->query($sql);

$orders = [];

if($result){

while ($row = $result->fetch_assoc()){

$orders[] = $row;

}

}

return $orders;

}

    /*UPDATE ORDER STATUS */

    public function updateOrderStatus($order_ID, $status) {

        $stmt = $this->conn->prepare(
            "UPDATE orders
             SET order_status = ?
             WHERE order_ID = ?"
        );

        if (!$stmt) return false;

        $stmt->bind_param("si", $status, $order_ID);

        return $stmt->execute();
    }

    public function updateReturnStatusForOrder($order_ID, $status) {

        $stmt = $this->conn->prepare(
            "UPDATE returns
             SET status = ?
             WHERE order_ID = ?
             ORDER BY return_ID DESC
             LIMIT 1"
        );

        if (!$stmt) return false;

        $stmt->bind_param("si", $status, $order_ID);

        return $stmt->execute();
    }

} 
