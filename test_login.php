<?php
// Script untuk test login dan debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/database.php';
require_once 'models/User.php';

$database = new Database();
$db = $database->getConnection();

echo "<h2>Test Login Debug</h2>";

// Test 1: Cek koneksi database
echo "<h3>1. Test Koneksi Database</h3>";
if ($db) {
    echo "<p style='color: green;'>✓ Koneksi database berhasil</p>";
} else {
    echo "<p style='color: red;'>✗ Koneksi database gagal</p>";
    exit;
}

// Test 2: Cek apakah user ada di database
echo "<h3>2. Cek User di Database</h3>";
$query = "SELECT id, username, password, nama_lengkap, email, user_role FROM users WHERE username = 'admin'";
$stmt = $db->prepare($query);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<p style='color: green;'>✓ User 'admin' ditemukan</p>";
    echo "<pre>";
    echo "ID: " . $user_data['id'] . "\n";
    echo "Username: " . $user_data['username'] . "\n";
    echo "Nama: " . $user_data['nama_lengkap'] . "\n";
    echo "Email: " . $user_data['email'] . "\n";
    echo "Role: " . $user_data['user_role'] . "\n";
    echo "Password Hash: " . substr($user_data['password'], 0, 30) . "...\n";
    echo "</pre>";
    
    // Test 3: Test password verify
    echo "<h3>3. Test Password Verification</h3>";
    $test_password = 'admin123';
    echo "<p>Testing password: <strong>$test_password</strong></p>";
    
    if (password_verify($test_password, $user_data['password'])) {
        echo "<p style='color: green;'>✓ Password COCOK!</p>";
    } else {
        echo "<p style='color: red;'>✗ Password TIDAK COCOK!</p>";
        echo "<p>Generating new hash...</p>";
        
        $new_hash = password_hash($test_password, PASSWORD_DEFAULT);
        echo "<p>New hash: <code>$new_hash</code></p>";
        
        // Update password
        $update_query = "UPDATE users SET password = :password WHERE username = 'admin'";
        $update_stmt = $db->prepare($update_query);
        $update_stmt->bindParam(':password', $new_hash);
        
        if ($update_stmt->execute()) {
            echo "<p style='color: green;'>✓ Password berhasil diupdate! Silakan coba login lagi.</p>";
        } else {
            echo "<p style='color: red;'>✗ Gagal update password</p>";
        }
    }
    
} else {
    echo "<p style='color: red;'>✗ User 'admin' tidak ditemukan di database</p>";
    echo "<p>Membuat user admin baru...</p>";
    
    $password_hash = password_hash('admin123', PASSWORD_DEFAULT);
    $insert_query = "INSERT INTO users (username, password, nama_lengkap, email, user_role) 
                     VALUES ('admin', :password, 'Administrator', 'admin@perpustakaan.com', 'admin')";
    $insert_stmt = $db->prepare($insert_query);
    $insert_stmt->bindParam(':password', $password_hash);
    
    if ($insert_stmt->execute()) {
        echo "<p style='color: green;'>✓ User admin berhasil dibuat!</p>";
    } else {
        echo "<p style='color: red;'>✗ Gagal membuat user admin</p>";
    }
}

// Test 4: Test login function
echo "<h3>4. Test Login Function</h3>";
$user = new User($db);
if ($user->login('admin', 'admin123')) {
    echo "<p style='color: green;'>✓ Login function berhasil!</p>";
    echo "<pre>";
    echo "User ID: " . $user->id . "\n";
    echo "Username: " . $user->username . "\n";
    echo "Nama: " . $user->nama_lengkap . "\n";
    echo "Role: " . $user->role . "\n";
    echo "</pre>";
} else {
    echo "<p style='color: red;'>✗ Login function gagal!</p>";
}

echo "<hr>";
echo "<h3>Kesimpulan</h3>";
echo "<p>Jika semua test di atas berhasil (✓), silakan coba login di <a href='login.php'>halaman login</a></p>";
echo "<p><strong>Username:</strong> admin</p>";
echo "<p><strong>Password:</strong> admin123</p>";
?>
