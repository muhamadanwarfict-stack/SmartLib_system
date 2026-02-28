<?php
// Konfigurasi umum aplikasi
define('BASE_URL', 'http://localhost/perpustakaan/');
define('APP_NAME', 'Sistem Informasi Perpustakaan');
define('DENDA_PER_HARI', 1000); // Denda keterlambatan per hari dalam Rupiah

// Konfigurasi session
session_start();

// Autoload classes
spl_autoload_register(function ($class_name) {
    $directories = [
        'classes/',
        'models/',
        'controllers/'
    ];
    
    foreach ($directories as $directory) {
        $file = $directory . $class_name . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Include database connection
require_once 'config/database.php';

// Helper functions
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_role']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

function requireRole($allowedRoles) {
    requireLogin();
    if (!in_array($_SESSION['user_role'], $allowedRoles)) {
        header('Location: unauthorized.php');
        exit();
    }
}

function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function formatCurrency($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

function generateNoPeminjaman() {
    return 'PJM' . date('Ymd') . rand(1000, 9999);
}

function hitungDenda($tanggal_kembali, $tanggal_dikembalikan) {
    $tgl_kembali = new DateTime($tanggal_kembali);
    $tgl_dikembalikan = new DateTime($tanggal_dikembalikan);
    
    if ($tgl_dikembalikan > $tgl_kembali) {
        $selisih = $tgl_kembali->diff($tgl_dikembalikan);
        $hari_terlambat = $selisih->days;
        return $hari_terlambat * DENDA_PER_HARI;
    }
    
    return 0;
}
?>
