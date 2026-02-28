<?php
require_once 'config/config.php';
requireRole(['admin', 'petugas']);

require_once 'models/Peminjaman.php';
require_once 'models/Buku.php';
require_once 'models/Anggota.php';

$database = new Database();
$db = $database->getConnection();

$peminjaman = new Peminjaman($db);
$buku = new Buku($db);
$anggota = new Anggota($db);

$message = '';
$message_type = '';

if ($_POST) {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                // Generate nomor peminjaman
                $peminjaman->no_peminjaman = generateNoPeminjaman();
                $peminjaman->anggota_id = $_POST['anggota_id'];
                $peminjaman->buku_id = $_POST['buku_id'];
                $peminjaman->tanggal_pinjam = $_POST['tanggal_pinjam'];
                $peminjaman->tanggal_kembali = $_POST['tanggal_kembali'];
                $peminjaman->status = 'dipinjam';
                $peminjaman->petugas_id = $_SESSION['user_id'];
                
                if ($peminjaman->create()) {
                    // Kurangi stok buku
                    $buku->updateKetersediaan($_POST['buku_id'], -1);
                    $message = 'Peminjaman berhasil ditambahkan!';
                    $message_type = 'success';
                } else {
                    $message = 'Gagal menambahkan peminjaman!';
                    $message_type = 'error';
                }
                break;
                
            case 'update':
                // Get old data untuk restore stok
                $peminjaman->id = $_POST['id'];
                $old_data = $peminjaman->readOne();
                
                // Restore stok buku lama
                if ($old_data['buku_id'] != $_POST['buku_id']) {
                    $buku->updateKetersediaan($old_data['buku_id'], 1);
                    $buku->updateKetersediaan($_POST['buku_id'], -1);
                }
                
                $peminjaman->anggota_id = $_POST['anggota_id'];
                $peminjaman->buku_id = $_POST['buku_id'];
                $peminjaman->tanggal_pinjam = $_POST['tanggal_pinjam'];
                $peminjaman->tanggal_kembali = $_POST['tanggal_kembali'];
                
                if ($peminjaman->update()) {
                    $message = 'Peminjaman berhasil diupdate!';
                    $message_type = 'success';
                } else {
                    $message = 'Gagal mengupdate peminjaman!';
                    $message_type = 'error';
                }
                break;
                
            case 'delete':
                // Get data untuk restore stok
                $peminjaman->id = $_POST['id'];
                $data = $peminjaman->readOne();
                
                if ($peminjaman->delete()) {
                    // Restore stok buku jika masih dipinjam
                    if ($data['status'] == 'dipinjam') {
                        $buku->updateKetersediaan($data['buku_id'], 1);
                    }
                    $message = 'Peminjaman berhasil dihapus!';
                    $message_type = 'success';
                } else {
                    $message = 'Gagal menghapus peminjaman!';
                    $message_type = 'error';
                }
                break;
        }
    }
}

$stmt = $peminjaman->readAll();
$peminjaman_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt_buku = $buku->readAll();
$buku_list = $stmt_buku->fetchAll(PDO::FETCH_ASSOC);

$stmt_anggota = $anggota->readAll();
$anggota_list = $stmt_anggota->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peminjaman - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css?v=2.0">
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            border-radius: 8px;
            width: 80%;
            max-width: 600px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: #000;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
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
                    <a href="peminjaman.php" class="nav-link active">
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
                <h1>Peminjaman Buku</h1>
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
                <div class="page-header">
                    <h2>Data Peminjaman</h2>
                    <button class="btn btn-primary" onclick="showAddModal()">
                        <i>➕</i> Tambah Peminjaman
                    </button>
                </div>

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
                                <th>Tgl Kembali</th>
                                <th>Status</th>
                                <th>Petugas</th>
                                <th>Aksi</th>
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
                                <td>
                                    <span class="badge badge-<?php echo $row['status'] == 'dipinjam' ? 'warning' : 'success'; ?>">
                                        <?php echo ucfirst($row['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo $row['nama_petugas']; ?></td>
                                <td>
                                    <?php if ($row['status'] == 'dipinjam'): ?>
                                    <button class="btn btn-sm btn-info" onclick='editPeminjaman(<?php echo json_encode($row); ?>)'>Edit</button>
                                    <button class="btn btn-sm btn-danger" onclick="deletePeminjaman(<?php echo $row['id']; ?>)">Hapus</button>
                                    <?php else: ?>
                                    <span style="color: #999;">Selesai</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal Tambah Peminjaman -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="hideModal('addModal')">&times;</span>
            <h2>Tambah Peminjaman</h2>
            <form method="POST">
                <input type="hidden" name="action" value="create">
                
                <div class="form-group">
                    <label>Anggota *</label>
                    <select name="anggota_id" required>
                        <option value="">Pilih Anggota</option>
                        <?php foreach ($anggota_list as $a): ?>
                        <option value="<?php echo $a['id']; ?>"><?php echo $a['no_anggota']; ?> - <?php echo $a['nama_lengkap']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Buku *</label>
                    <select name="buku_id" required>
                        <option value="">Pilih Buku</option>
                        <?php foreach ($buku_list as $b): ?>
                        <?php if ($b['jumlah_tersedia'] > 0): ?>
                        <option value="<?php echo $b['id']; ?>"><?php echo $b['kode_buku']; ?> - <?php echo $b['judul']; ?> (Tersedia: <?php echo $b['jumlah_tersedia']; ?>)</option>
                        <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Tanggal Pinjam *</label>
                    <input type="date" name="tanggal_pinjam" value="<?php echo date('Y-m-d'); ?>" required>
                </div>

                <div class="form-group">
                    <label>Tanggal Kembali *</label>
                    <input type="date" name="tanggal_kembali" value="<?php echo date('Y-m-d', strtotime('+7 days')); ?>" required>
                </div>

                <button type="submit" class="btn btn-primary">Simpan</button>
                <button type="button" class="btn btn-secondary" onclick="hideModal('addModal')">Batal</button>
            </form>
        </div>
    </div>

    <!-- Modal Edit Peminjaman -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="hideModal('editModal')">&times;</span>
            <h2>Edit Peminjaman</h2>
            <form method="POST">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" id="edit_id">
                
                <div class="form-group">
                    <label>No. Peminjaman</label>
                    <input type="text" id="edit_no_peminjaman" readonly>
                </div>

                <div class="form-group">
                    <label>Anggota *</label>
                    <select name="anggota_id" id="edit_anggota_id" required>
                        <option value="">Pilih Anggota</option>
                        <?php foreach ($anggota_list as $a): ?>
                        <option value="<?php echo $a['id']; ?>"><?php echo $a['no_anggota']; ?> - <?php echo $a['nama_lengkap']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Buku *</label>
                    <select name="buku_id" id="edit_buku_id" required>
                        <option value="">Pilih Buku</option>
                        <?php foreach ($buku_list as $b): ?>
                        <option value="<?php echo $b['id']; ?>"><?php echo $b['kode_buku']; ?> - <?php echo $b['judul']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Tanggal Pinjam *</label>
                    <input type="date" name="tanggal_pinjam" id="edit_tanggal_pinjam" required>
                </div>

                <div class="form-group">
                    <label>Tanggal Kembali *</label>
                    <input type="date" name="tanggal_kembali" id="edit_tanggal_kembali" required>
                </div>

                <button type="submit" class="btn btn-primary">Update</button>
                <button type="button" class="btn btn-secondary" onclick="hideModal('editModal')">Batal</button>
            </form>
        </div>
    </div>

    <script>
        function showAddModal() {
            document.getElementById('addModal').style.display = 'block';
        }

        function hideModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        function editPeminjaman(data) {
            document.getElementById('edit_id').value = data.id;
            document.getElementById('edit_no_peminjaman').value = data.no_peminjaman;
            document.getElementById('edit_anggota_id').value = data.anggota_id;
            document.getElementById('edit_buku_id').value = data.buku_id;
            document.getElementById('edit_tanggal_pinjam').value = data.tanggal_pinjam;
            document.getElementById('edit_tanggal_kembali').value = data.tanggal_kembali;
            document.getElementById('editModal').style.display = 'block';
        }

        function deletePeminjaman(id) {
            if (confirm('Yakin ingin menghapus peminjaman ini? Stok buku akan dikembalikan.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = '<input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="' + id + '">';
                document.body.appendChild(form);
                form.submit();
            }
        }

        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }
    </script>
</body>
</html>
