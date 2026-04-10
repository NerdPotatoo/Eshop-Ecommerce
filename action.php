<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'vendor/autoload.php';

use App\Controllers\CartController;

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['page'])) {
    if ($_POST['page'] === 'add-to-cart') {
        header('Content-Type: application/json');
        $cartController = new CartController();
        
        $productId = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
        $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
        
        if ($productId > 0 && $quantity > 0) {
            $result = $cartController->addToCart($productId, $quantity);
            echo json_encode($result);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid product or quantity']);
        }
        exit;
    }
}

// Handle AJAX GET requests
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['page']) && $_GET['page'] === 'cart-count') {
    header('Content-Type: application/json');
    $cartController = new CartController();
    echo json_encode(['count' => $cartController->getCartCount()]);
    exit;
}

if(isset($_GET['page'])) {
    if($_GET['page'] == 'home') {
        include 'pages/index.php';
    }
    else if($_GET['page'] == 'about') {
        include 'pages/about.php';
    } 
    else if($_GET['page'] == 'products') {
        include 'pages/products.php';
    } 
    else if($_GET['page'] == 'login') {
        include 'pages/login.php';
    } 
    else if($_GET['page'] == 'signup') {
        include 'pages/signup.php';
    } 
    else if($_GET['page'] == 'contact') {
        include 'pages/contact.php';
    } 
    else if($_GET['page'] == 'cart') {
        include 'pages/cart.php';
    } 
    else if($_GET['page'] == 'checkout') {
        include 'pages/checkout.php';
    }
    else if($_GET['page'] == 'order-confirmation') {
        include 'pages/order-confirmation.php';
    }
    else if($_GET['page'] == 'admin/dashboard') {
        include 'pages/admin/dashboard.php';
    }
    else if($_GET['page'] == 'admin/products') {
        include 'pages/admin/products.php';
    }
    else if($_GET['page'] == 'admin/add-product') {
        include 'pages/admin/add-product.php';
    }
    else if($_GET['page'] == 'admin/orders') {
        include 'pages/admin/orders.php';
    }
    else if($_GET['page'] == 'admin/customers') {
        include 'pages/admin/customers.php';
    }
    else if($_GET['page'] == 'admin/contacts') {
        include 'pages/admin/contacts.php';
    }
    else if($_GET['page'] == 'admin/login') {
        include 'pages/admin/login.php';
    }
    else if($_GET['page'] == 'admin/setup') {
        include 'pages/admin/setup.php';
    }
    else if($_GET['page'] == 'logout') {
        include 'pages/logout.php';
    }
    else {
        // Default to home page if route not found
        include 'pages/index.php';
    }
} else {
    // If no page parameter, show home page
    include 'pages/index.php';
}
