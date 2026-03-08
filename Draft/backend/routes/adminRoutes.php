<?php //admin routes - handle admin only operations (dashboard, inventory, customer management etc.)

header('Content-Type: application/json');
session_start();

require_once __DIR__ . '/../controllers/adminController.php'; //path to adminController.php
require_once __DIR__ . '/../services/userFunctions.php'; //path to userFunctions.php for access control

require_admin(); //only admin can access these routes - hard gate

//POST > prevent unintended data exposure via GET even if JSON input expected (also prevents CSRF attacks)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { 
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
    exit;
}   

$raw = file_get_contents("php://input"); //get raw POST data (JSON expected fron fetch)
$data = json_decode($raw, true); //get JSON input data and decode

if (!is_array($data) || empty($data['action'])) { //if no data/action provided then return error
    echo json_encode(["success" => false, "message" => "No action specified"]); 
    exit;
}
$adminController = new AdminController(); //instance of admin controller

switch ($data['action'] ?? '') {

    case 'dashboard_summary':
        $adminController->dashboardSummary($data);
        break;

    //customers
    case 'customers_list':
        $adminController->getCustomersList($data);
        break;

    case 'customer_update':
        $adminController->updateCustomer($data);
        break;

    case 'customer_dectivate':
        $adminController->deactivateCustomer($data);
        break;

    case 'customer_role_update':
        $adminController->updateCustomerRole($data);
        break;

    case 'customer_orders':
        $adminController->getCustomerOrders($data);
        break;

/*     //inventory
    case 'inventory_list':
        $adminController->getInventoryList($data);
        break;

    case 'inventory_add':
        $adminController->addInventory($data);
        break;

    case 'inventory_update':
        $adminController->updateInventory($data);
        break;

    case 'inventory_delete':
        $adminController->deleteInventory($data);
        break;
    
    case 'inventory_adjust_stock':
        $adminController->adjustInventoryStock($data);
        break;

    case 'inventory_set_stock_level':
        $adminController->setInventoryStockLevel($data);
        break;
 */
    //reports
    case 'report_stock_level':
        $adminController->reportStockLevel($data);
        break;

    case 'report_customer_signups':
        $adminController->reportCustomerSignups($data);
        break;

    case 'report_total_revenue':
        $adminController->reportTotalRevenue($data);
        break;

/*     //orders
    case 'orders_list':
        $adminController->ordersList($data);
        break;
    
    case 'order_update_status':
        $adminController->updateOrderStatus($data);
        break; */

    default:
        echo json_encode(["success" => false, "message" => "Invalid action."]);
        break;
}

?>