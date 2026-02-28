<?php
require_once 'config/config.php';
requireRole(['admin', 'petugas']);

require_once 'models/Peminjaman.php';
require_once 'models/Buku.php';

$database = new Database();
$db = $database->getConnection();

$peminjaman = new Peminjaman($db);
$buku = new Buku($db);

$message = '';
$message_type = '';

if ($_POST) {
    if (isset($_POST['action']) && $_POST['action'] === 'return') {
        $id = $_POST['id'];
        $tanggal_dikembalikan = $_POST['tanggal_dikembalikan'];
        $tanggal_kembali = $_POST['tanggal_kembali'];
        
        // Hitung denda
        $denda = hitungDenda($tanggal_kembali, $tanggal_dikembalikan);
        
        // Update status peminjaman
        if ($peminjaman->pengembalian($id, $tanggal_dikembalikan, $denda)) {
            // Tambah stok buku
            $buku->updateKetersediaan($_POST['buku_id'], 1);
            
            $message = 'Pengembalian berhasil! Denda: ' . formatCurrency($denda);
            $message_type = 'success';
        } else {
            $message = 'Gagal memproses pengembalian!';
            $message_type = 'error';
        }
    }
}

// Get peminjaman yang belum dikembalikan
$query = "SELECT p.*, a.nama_lengkap as nama_anggota, a.no_anggota,
                 b.judul as judul_buku, b.kode_buku, b.id as buku_id
          FROM peminjaman p
          LEFT JOIN anggota a ON p.anggota_id = a.id
          LEFT JOIN buku b ON p.buku_id = b.id
          WHERE p.status = 'dipinjam'
          ORDER BY p.tanggal_pinjam DESC";

$stmt = $db->prepare($query);
$stmt->execute();
$peminjaman_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengembalian - <?php echo APP_NAME; ?></title>
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
                    <a href="pengembalian.php" class="nav-link active">
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
                
                <?php if ($_SESSION['user_role'] === 'admin'): ?>
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

        <main class="main-content">
            <header class="top-nav">
                <h1>Pengembalian Buku</h1>
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
                <h2>Peminjaman Aktif</h2>

                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $message_type; ?>">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>

                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>No. Peminjaman</th>
                                <th>Anggota</th>
                                <th>Buku</th>
                                <th>Tgl Pinjam</th>
                                <th>Tgl Harus Kembali</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($peminjaman_list as $row): ?>
                            <?php
                                $today = new DateTime();
                                $tgl_kembali = new DateTime($row['tanggal_kembali']);
                                $is_late = $today > $tgl_kembali;
                            ?>
                            <tr>
                                <td><?php echo $row['no_peminjaman']; ?></td>
                                <td><?php echo $row['nama_anggota']; ?></td>
                                <td><?php echo $row['judul_buku']; ?></td>
                                <td><?php echo date('d/m/Y', strtotime($row['tanggal_pinjam'])); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($row['tanggal_kembali'])); ?></td>
                                <td>
                                    <?php if ($is_late): ?>
                                        <span class="badge badge-danger">Terlambat</span>
                                    <?php else: ?>
                                        <span class="badge badge-success">Normal</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-success" onclick='prosesKembali(<?php echo json_encode($row); ?>)'>
                                        Proses Pengembalian
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal Pengembalian -->
    <div id="returnModal" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="close" onclick="hideModal()">&times;</span>
            <h2>Proses Pengembalian</h2>
            <form method="POST">
                <input type="hidden" name="action" value="return">
                <input type="hidden" name="id" id="return_id">
                <input type="hidden" name="buku_id" id="return_buku_id">
                <input type="hidden" name="tanggal_kembali" id="return_tanggal_kembali">
                
                <div class="form-group">
                    <label>No. Peminjaman</label>
                    <input type="text" id="return_no_peminjaman" readonly>
                </div>

                <div class="form-group">
                    <label>Anggota</label>
                    <input type="text" id="return_anggota" readonly>
                </div>

                <div class="form-group">
                    <label>Buku</label>
                    <input type="text" id="return_buku" readonly>
                </div>

                <div class="form-group">
                    <label>Tanggal Dikembalikan</label>
                    <input type="date" name="tanggal_dikembalikan" id="return_tanggal_dikembalikan" value="<?php echo date('Y-m-d'); ?>" required>
                </div>

                <div class="form-group">
                    <label>Estimasi Denda</label>
                    <input type="text" id="return_denda" readonly>
                </div>

                <button type="submit" class="btn btn-primary">Proses Pengembalian</button>
            </form>
        </div>
    </div>

    <script>
        function prosesKembali(data) {
            document.getElementById('return_id').value = data.id;
            document.getElementById('return_buku_id').value = data.buku_id;
            document.getElementById('return_no_peminjaman').value = data.no_peminjaman;
            document.getElementById('return_anggota').value = data.nama_anggota;
            document.getElementById('return_buku').value = data.judul_buku;
            document.getElementById('return_tanggal_kembali').value = data.tanggal_kembali;
            
            hitungDenda();
            document.getElementById('returnModal').style.display = 'block';
        }

        function hideModal() {
            document.getElementById('returnModal').style.display = 'none';
        }

        function hitungDenda() {
            const tglKembali = new Date(document.getElementById('return_tanggal_kembali').value);
            const tglDikembalikan = new Date(document.getElementById('return_tanggal_dikembalikan').value);
            
            if (tglDikembalikan > tglKembali) {
                const diffTime = Math.abs(tglDikembalikan - tglKembali);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                const denda = diffDays * <?php echo DENDA_PER_HARI; ?>;
                document.getElementById('return_denda').value = 'Rp ' + denda.toLocaleString('id-ID');
            } else {
                document.getElementById('return_denda').value = 'Rp 0';
            }
        }

        document.getElementById('return_tanggal_dikembalikan').addEventListener('change', hitungDenda);

        window.onclick = function(event) {
            const modal = document.getElementById('returnModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>
</html>
