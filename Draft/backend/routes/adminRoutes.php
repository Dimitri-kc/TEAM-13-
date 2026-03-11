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
    case 'customers_list': //gets list of all customer in DB and their details (name, email, phone, address etc)
        $adminController->getCustomersList($data);
        break;

    case 'customer_details': //gets details of a specific customer
        $adminController->getCustomerDetails($data);
        break;

    case 'update_customer': //update customer personal details (name, email, phone etc)
        $adminController->updateCustomer($data);
        break;

    case 'deactivate_customer': //deactivate customer acc (soft delete as data retained but account deactivated)
        $adminController->deactivateCustomer($data);
        break;

    case 'update_customer_role': //update customer role (user>admin or vice versa)
        $adminController->updateCustomerRole($data);
        break;

    case 'customer_orders': //get list of order for a specific customer
        $adminController->getCustomerOrders($data);
        break;

/*     //inventory
    case 'inventory_list':
        $adminController->getInventoryList($data);
        break;

    case 'add_inventory':
        $adminController->addInventory($data);
        break;

    case 'update_inventory':
        $adminController->updateInventory($data);
        break;

    case 'delete_inventory':
        $adminController->deleteInventory($data);
        break;
    
    case 'adjust_inventory_stock':
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

    case 'report_customer_signups': //report total customers
        $adminController->reportCustomerSignups($data);
        break;

    case 'report_total_revenue':
        $adminController->reportTotalRevenue($data);
        break;

    //orders
    case 'orders_list':
        $adminController->getOrdersList($data);
        break;
    
    case 'update_order_status':
        $adminController->updateOrderStatus($data);
        break;

    default:
        echo json_encode(["success" => false, "message" => "Invalid action."]);
        break;
}

?>