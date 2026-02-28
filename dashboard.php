<?php
require_once 'config/config.php';
requireLogin();

// Include models
require_once 'models/User.php';
require_once 'models/Buku.php';
require_once 'models/Peminjaman.php';
require_once 'models/Anggota.php';

$database = new Database();
$db = $database->getConnection();

// Get dashboard data based on user role
$role = $_SESSION['user_role'];
$stats = [];

if ($role === 'admin' || $role === 'petugas') {
    // Get peminjaman stats
    $peminjaman = new Peminjaman($db);
    $total_peminjaman_aktif = $peminjaman->getTotalPeminjamanAktif();
    $total_terlambat = $peminjaman->getTerlambat();
    
    // Get buku stats
    $buku = new Buku($db);
    $total_buku = $buku->getTotalBuku();
    $buku_tersedia = $buku->getBukuTersedia();
    
    // Get anggota stats
    $anggota = new Anggota($db);
    $total_anggota = $anggota->getTotalAnggota();
}

// Get recent activities
$recent_peminjaman = [];

if ($role === 'admin' || $role === 'petugas') {
    $stmt = $peminjaman->readRecent(5);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $recent_peminjaman[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css?v=2.0">
</head>
<body>
    <div class="main-container">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="sidebar-header">
                <h2><?php echo APP_NAME; ?></h2>
            </div>
            
            <ul class="sidebar-nav">
                <li class="nav-item">
                    <a href="dashboard.php" class="nav-link active">
                        <i>📊</i> Dashboard
                    </a>
                </li>
                
                <?php if ($role === 'admin' || $role === 'petugas'): ?>
                <li class="nav-item">
                    <a href="peminjaman.php" class="nav-link">
                        <i>📚</i> Peminjaman
                    </a>
                </li>
                <li class="nav-item">
                    <a href="pengembalian.php" class="nav-link">
                        <i>↩️</i> Pengembalian
                    </a>
                </li>
                <li class="nav-item">
                    <a href="buku.php" class="nav-link">
                        <i>📖</i> Data Buku
                    </a>
                </li>
                <li class="nav-item">
                    <a href="anggota.php" class="nav-link">
                        <i>👥</i> Data Anggota
                    </a>
                </li>
                <?php endif; ?>
                
                <?php if ($role === 'admin'): ?>
                <li class="nav-item">
                    <a href="kategori.php" class="nav-link">
                        <i>🏷️</i> Kategori Buku
                    </a>
                </li>
                <li class="nav-item">
                    <a href="laporan.php" class="nav-link">
                        <i>📈</i> Laporan
                    </a>
                </li>
                <li class="nav-item">
                    <a href="users.php" class="nav-link">
                        <i>⚙️</i> Manajemen User
                    </a>
                </li>
                <?php endif; ?>
                
                <li class="nav-item">
                    <a href="logout.php" class="nav-link">
                        <i>🚪</i> Logout
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Top Navigation -->
            <header class="top-nav">
                <h1>Dashboard</h1>
                <div class="user-info">
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($_SESSION['nama_lengkap'], 0, 1)); ?>
                    </div>
                    <div class="user-details">
                        <div class="user-name"><?php echo $_SESSION['nama_lengkap']; ?></div>
                        <div class="user-role"><?php echo ucfirst($_SESSION['user_role']); ?></div>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <div class="content">
                <!-- Welcome Message -->
                <div class="alert alert-info">
                    Selamat datang, <strong><?php echo $_SESSION['nama_lengkap']; ?></strong>! 
                    Anda login sebagai <strong><?php echo ucfirst($_SESSION['user_role']); ?></strong>.
                </div>

                <!-- Dashboard Cards -->
                <div class="dashboard-grid">
                    <?php if ($role === 'admin' || $role === 'petugas'): ?>
                    <div class="dashboard-card">
                        <div class="card-header">
                            <div class="card-icon primary">
                                <i>📚</i>
                            </div>
                            <div class="card-title">Total Buku</div>
                        </div>
                        <div class="card-value"><?php echo $total_buku ?? 0; ?></div>
                        <div class="card-subtitle">Jumlah koleksi buku</div>
                    </div>

                    <div class="dashboard-card">
                        <div class="card-header">
                            <div class="card-icon success">
                                <i>✅</i>
                            </div>
                            <div class="card-title">Buku Tersedia</div>
                        </div>
                        <div class="card-value"><?php echo $buku_tersedia ?? 0; ?></div>
                        <div class="card-subtitle">Buku yang dapat dipinjam</div>
                    </div>

                    <div class="dashboard-card">
                        <div class="card-header">
                            <div class="card-icon warning">
                                <i>📖</i>
                            </div>
                            <div class="card-title">Sedang Dipinjam</div>
                        </div>
                        <div class="card-value"><?php echo $total_peminjaman_aktif ?? 0; ?></div>
                        <div class="card-subtitle">Peminjaman aktif</div>
                    </div>

                    <div class="dashboard-card">
                        <div class="card-header">
                            <div class="card-icon danger">
                                <i>⚠️</i>
                            </div>
                            <div class="card-title">Terlambat</div>
                        </div>
                        <div class="card-value"><?php echo $total_terlambat ?? 0; ?></div>
                        <div class="card-subtitle">Peminjaman terlambat</div>
                    </div>

                    <?php if ($role === 'admin'): ?>
                    <div class="dashboard-card">
                        <div class="card-header">
                            <div class="card-icon info">
                                <i>👥</i>
                            </div>
                            <div class="card-title">Total Anggota</div>
                        </div>
                        <div class="card-value"><?php echo $total_anggota ?? 0; ?></div>
                        <div class="card-subtitle">Anggota terdaftar</div>
                    </div>
                    <?php endif; ?>
                    <?php endif; ?>
                </div>

                <!-- Recent Activities -->
                <?php if ($role === 'admin' || $role === 'petugas'): ?>
                <div class="table-container">
                    <div class="table-header">
                        <h3 class="table-title">Peminjaman Terbaru</h3>
                    </div>
                    <div style="padding: 0;">
                        <?php if (empty($recent_peminjaman)): ?>
                            <p style="padding: 20px; text-align: center; color: #666;">Tidak ada data peminjaman</p>
                        <?php else: ?>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>No. Peminjaman</th>
                                        <th>Anggota</th>
                                        <th>Buku</th>
                                        <th>Tanggal Pinjam</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_peminjaman as $row): ?>
                                    <tr>
                                        <td><?php echo $row['no_peminjaman']; ?></td>
                                        <td><?php echo $row['nama_anggota']; ?></td>
                                        <td><?php echo $row['judul_buku']; ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($row['tanggal_pinjam'])); ?></td>
                                        <td>
                                            <span class="badge badge-<?php echo $row['status'] == 'dipinjam' ? 'warning' : 'success'; ?>">
                                                <?php echo ucfirst($row['status']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
