<?php
require_once 'vendor/autoload.php';

// If no page parameter is set, default to home
if (!isset($_GET['page'])) {
    $_GET['page'] = 'home';
}

// Use action.php for routing
include 'action.php';