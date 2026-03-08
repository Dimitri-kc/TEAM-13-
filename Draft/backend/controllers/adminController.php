<?php 
//admin controller - handles admin specific operations
require_once __DIR__ . '/../config/db_connect.php'; //database connection
require_once __DIR__ . '/../models/userModel.php'; //user model for database operations related to users

class AdminController {

    private function ok($data = [], $message = 'OK') {
        echo json_encode(["success" => true, "message" => $message, "data" => $data]);
        return; 
    }
    private function fail ($message = 'Error', $code = 400) {
        echo json_encode(["success" => false, "message" => $message]);
        return;
    }

    public function dashboardSummary($data) {
        //for dashboard summary stats
        $summary = [
            "total_customers" => 150, //placeholder data for now, replace with actual database queries
            "total_orders" => 1200,
            "total_revenue" => 50000,
            "low_stock_items" => 5
        ];
        $this->ok($summary, "Dashboard summary retrieved successfully.");
    }

//Customer Management Operations
    public function getCustomersList($data) {
        //for retrieving list of customers
        $this->fail('Not implemented yet', 501);
    }

    public function updateCustomer($data) {
        //for updating customer details
        $this->fail('Not implemented yet', 501);
    }

    public function deactivateCustomer($data) {
        //for deactivating/deleting customer account
        $this->fail('Not implemented yet', 501);
    }

    public function updateCustomerRole($data) {
        //for updating customer role (e.g. user to admin)
        $this->fail('Not implemented yet', 501);
    }

    public function getCustomerOrders($data) {
        //for retrieving orders of a specific customer
        $this->fail('Not implemented yet', 501);
    }

//Report Analytics
    public function reportStockLevel($data) {
        //for generating stock level report
        $this->fail('Not implemented yet', 501);
    }

    public function reportCustomerSignups($data) {
        //for retrieveing total customers 
        $this->fail('Not implemented yet', 501);
    }

    public function reportTotalRevenue($data) {
        //generate total revenue report
        $this->fail('Not implemented yet', 501);
    }
    
}

?>