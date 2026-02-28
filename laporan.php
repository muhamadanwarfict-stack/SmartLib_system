<?php
require_once 'config/config.php';
requireRole(['admin']);

require_once 'models/Peminjaman.php';
require_once 'models/Buku.php';
require_once 'models/Anggota.php';

$database = new Database();
$db = $database->getConnection();

$peminjaman = new Peminjaman($db);
$buku = new Buku($db);
$anggota = new Anggota($db);

// Get statistics
$total_buku = $buku->getTotalBuku();
$total_anggota = $anggota->getTotalAnggota();
$total_peminjaman_aktif = $peminjaman->getTotalPeminjamanAktif();
$total_terlambat = $peminjaman->getTerlambat();
$total_denda = $peminjaman->getTotalDenda();

// Get peminjaman data
$stmt = $peminjaman->readAll();
$peminjaman_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css?v=2.1">
</head>
<body>
    <div class="main-container">
        <nav class="sidebar">
            <div class="sidebar-header">
                <h2><?php echo APP_NAME; ?></h2>
            </div>
            
            <ul class="sidebar-nav">
                <li class="nav-item">
                    <a href="dashboard.php" class="nav-link">
                        <i>📊</i> Dashboard
                    </a>
                </li>
                
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
                
                <li class="nav-item">
                    <a href="kategori.php" class="nav-link">
                        <i>🏷️</i> Kategori Buku
                    </a>
                </li>
                <li class="nav-item">
                    <a href="laporan.php" class="nav-link active">
                        <i>📈</i> Laporan
                    </a>
                </li>
                <li class="nav-item">
                    <a href="users.php" class="nav-link">
                        <i>⚙️</i> Manajemen User
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="logout.php" class="nav-link">
                        <i>🚪</i> Logout
                    </a>
                </li>
            </ul>
        </nav>

        <main class="main-content">
            <header class="top-nav">
                <h1>Laporan Perpustakaan</h1>
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

            <div class="content">
                <h2>Ringkasan Statistik</h2>

                <div class="dashboard-grid">
                    <div class="dashboard-card">
                        <div class="card-header">
                            <div class="card-icon primary">
                                <i>📚</i>
                            </div>
                            <div class="card-title">Total Buku</div>
                        </div>
                        <div class="card-value"><?php echo $total_buku; ?></div>
                        <div class="card-subtitle">Koleksi buku</div>
                    </div>

                    <div class="dashboard-card">
                        <div class="card-header">
                            <div class="card-icon success">
                                <i>👥</i>
                            </div>
                            <div class="card-title">Total Anggota</div>
                        </div>
                        <div class="card-value"><?php echo $total_anggota; ?></div>
                        <div class="card-subtitle">Anggota terdaftar</div>
                    </div>

                    <div class="dashboard-card">
                        <div class="card-header">
                            <div class="card-icon warning">
                                <i>📖</i>
                            </div>
                            <div class="card-title">Peminjaman Aktif</div>
                        </div>
                        <div class="card-value"><?php echo $total_peminjaman_aktif; ?></div>
                        <div class="card-subtitle">Sedang dipinjam</div>
                    </div>

                    <div class="dashboard-card">
                        <div class="card-header">
                            <div class="card-icon danger">
                                <i>⚠️</i>
                            </div>
                            <div class="card-title">Terlambat</div>
                        </div>
                        <div class="card-value"><?php echo $total_terlambat; ?></div>
                        <div class="card-subtitle">Peminjaman terlambat</div>
                    </div>

                    <div class="dashboard-card">
                        <div class="card-header">
                            <div class="card-icon info">
                                <i>💰</i>
                            </div>
                            <div class="card-title">Total Denda</div>
                        </div>
                        <div class="card-value"><?php echo formatCurrency($total_denda); ?></div>
                        <div class="card-subtitle">Denda terkumpul</div>
                    </div>
                </div>

                <h2 style="margin-top: 30px;">Riwayat Peminjaman</h2>

                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>No. Peminjaman</th>
                                <th>Anggota</th>
                                <th>Buku</th>
                                <th>Tgl Pinjam</th>
                                <th>Tgl Kembali</th>
                                <th>Tgl Dikembalikan</th>
                                <th>Denda</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($peminjaman_list as $row): ?>
                            <tr>
                                <td><?php echo $row['no_peminjaman']; ?></td>
                                <td><?php echo $row['nama_anggota']; ?></td>
                                <td><?php echo $row['judul_buku']; ?></td>
                                <td><?php echo date('d/m/Y', strtotime($row['tanggal_pinjam'])); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($row['tanggal_kembali'])); ?></td>
                                <td><?php echo $row['tanggal_dikembalikan'] ? date('d/m/Y', strtotime($row['tanggal_dikembalikan'])) : '-'; ?></td>
                                <td><?php echo formatCurrency($row['denda']); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $row['status'] == 'dipinjam' ? 'warning' : 'success'; ?>">
                                        <?php echo ucfirst($row['status']); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
