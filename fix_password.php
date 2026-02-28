<?php
// Script untuk fix password di database
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

// Password yang benar
$admin_password = 'admin123';
$petugas_password = 'admin123';

// Hash password
$admin_hash = password_hash($admin_password, PASSWORD_DEFAULT);
$petugas_hash = password_hash($petugas_password, PASSWORD_DEFAULT);

echo "<h2>Password Hash Generator</h2>";
echo "<p>Admin password hash: <code>$admin_hash</code></p>";
echo "<p>Petugas password hash: <code>$petugas_hash</code></p>";

// Update password di database
try {
    // Update admin
    $query = "UPDATE users SET password = :password WHERE username = 'admin'";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':password', $admin_hash);
    $stmt->execute();
    echo "<p style='color: green;'>✓ Password admin berhasil diupdate!</p>";
    
    // Update petugas
    $query = "UPDATE users SET password = :password WHERE username = 'petugas'";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':password', $petugas_hash);
    $stmt->execute();
    echo "<p style='color: green;'>✓ Password petugas berhasil diupdate!</p>";
    
    echo "<hr>";
    echo "<h3>Silakan login dengan:</h3>";
    echo "<ul>";
    echo "<li><strong>Admin:</strong> username: admin, password: admin123</li>";
    echo "<li><strong>Petugas:</strong> username: petugas, password: admin123</li>";
    echo "</ul>";
    echo "<p><a href='login.php' style='background: #6C63FF; color: white; padding: 10px 20px; text-decoration: none; border-radius: 8px;'>Ke Halaman Login</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>
