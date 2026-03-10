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

    /* ORDER SYSTEM*/

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

    public function getAllOrders() {

        $stmt = $this->conn->prepare(
            "SELECT *
             FROM orders
             ORDER BY order_date DESC"
        );

        if (!$stmt) return [];

        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

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
}