<?php
require_once 'config/config.php';

// Redirect to dashboard if logged in, otherwise show landing page
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

// Show landing page
header('Location: landing.php');
exit();
?>
