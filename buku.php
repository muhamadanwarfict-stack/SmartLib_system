Tesssss
<?php
require_once 'config/config.php';
requireRole(['admin', 'petugas']);

require_once 'models/Buku.php';
require_once 'models/KategoriBuku.php';

$database = new Database();
$db = $database->getConnection();

$buku = new Buku($db);
$kategori = new KategoriBuku($db);
$role = $_SESSION['user_role'];

$message = '';
$message_type = '';

// Handle form submission
if ($_POST) {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $buku->kode_buku = $_POST['kode_buku'];
                $buku->judul = $_POST['judul'];
                $buku->pengarang = $_POST['pengarang'];
                $buku->penerbit = $_POST['penerbit'];
                $buku->tahun_terbit = $_POST['tahun_terbit'];
                $buku->kategori_id = $_POST['kategori_id'];
                $buku->isbn = $_POST['isbn'];
                $buku->jumlah_total = $_POST['jumlah_total'];
                $buku->jumlah_tersedia = $_POST['jumlah_total'];
                $buku->lokasi_rak = $_POST['lokasi_rak'];
                
                if ($buku->create()) {
                    $message = 'Buku berhasil ditambahkan!';
                    $message_type = 'success';
                } else {
                    $message = 'Gagal menambahkan buku!';
                    $message_type = 'error';
                }
                break;
                
            case 'update':
                $buku->id = $_POST['id'];
                $buku->kode_buku = $_POST['kode_buku'];
                $buku->judul = $_POST['judul'];
                $buku->pengarang = $_POST['pengarang'];
                $buku->penerbit = $_POST['penerbit'];
                $buku->tahun_terbit = $_POST['tahun_terbit'];
                $buku->kategori_id = $_POST['kategori_id'];
                $buku->isbn = $_POST['isbn'];
                $buku->jumlah_total = $_POST['jumlah_total'];
                $buku->jumlah_tersedia = $_POST['jumlah_tersedia'];
                $buku->lokasi_rak = $_POST['lokasi_rak'];
                
                if ($buku->update()) {
                    $message = 'Buku berhasil diupdate!';
                    $message_type = 'success';
                } else {
                    $message = 'Gagal mengupdate buku!';
                    $message_type = 'error';
                }
                break;
                
            case 'delete':
                $buku->id = $_POST['id'];
                if ($buku->delete()) {
                    $message = 'Buku berhasil dihapus!';
                    $message_type = 'success';
                } else {
                    $message = 'Gagal menghapus buku!';
                    $message_type = 'error';
                }
                break;
        }
    }
}

// Get all buku
$stmt = $buku->readAll();
$buku_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all kategori for dropdown
$stmt_kategori = $kategori->readAll();
$kategori_list = $stmt_kategori->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Buku - <?php echo APP_NAME; ?></title>
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
        .form-group select,
        .form-group textarea {
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
                    <a href="buku.php" class="nav-link active">
                        <i>📖</i> Data Buku
                    </a>
                </li>
                <li class="nav-item">
                    <a href="anggota.php" class="nav-link">
                        <i>👥</i> Data Anggota
                    </a>
                </li>
                
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

        <main class="main-content">
            <header class="top-nav">
                <h1>Data Buku</h1>
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
                    <h2>Data Buku</h2>
                    <button class="btn btn-primary" onclick="showAddModal()">
                        <i>➕</i> Tambah Buku
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
                                <th>Kode</th>
                                <th>Judul</th>
                                <th>Pengarang</th>
                                <th>Kategori</th>
                                <th>Tahun</th>
                                <th>Stok</th>
                                <th>Tersedia</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($buku_list as $row): ?>
                            <tr>
                                <td><?php echo $row['kode_buku']; ?></td>
                                <td><?php echo $row['judul']; ?></td>
                                <td><?php echo $row['pengarang']; ?></td>
                                <td><?php echo $row['nama_kategori']; ?></td>
                                <td><?php echo $row['tahun_terbit']; ?></td>
                                <td><?php echo $row['jumlah_total']; ?></td>
                                <td><?php echo $row['jumlah_tersedia']; ?></td>
                                <td>
                                    <button class="btn btn-sm btn-info" onclick='editBuku(<?php echo json_encode($row); ?>)'>Edit</button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteBuku(<?php echo $row['id']; ?>)">Hapus</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal Tambah Buku -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="hideModal('addModal')">&times;</span>
            <h2>Tambah Buku</h2>
            <form method="POST">
                <input type="hidden" name="action" value="create">
                
                <div class="form-group">
                    <label>Kode Buku *</label>
                    <input type="text" name="kode_buku" required>
                </div>

                <div class="form-group">
                    <label>Judul *</label>
                    <input type="text" name="judul" required>
                </div>

                <div class="form-group">
                    <label>Pengarang *</label>
                    <input type="text" name="pengarang" required>
                </div>

                <div class="form-group">
                    <label>Penerbit</label>
                    <input type="text" name="penerbit">
                </div>

                <div class="form-group">
                    <label>Tahun Terbit</label>
                    <input type="number" name="tahun_terbit" min="1900" max="<?php echo date('Y'); ?>">
                </div>

                <div class="form-group">
                    <label>Kategori</label>
                    <select name="kategori_id">
                        <option value="">Pilih Kategori</option>
                        <?php foreach ($kategori_list as $k): ?>
                        <option value="<?php echo $k['id']; ?>"><?php echo $k['nama_kategori']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>ISBN</label>
                    <input type="text" name="isbn">
                </div>

                <div class="form-group">
                    <label>Jumlah Total *</label>
                    <input type="number" name="jumlah_total" value="1" min="1" required>
                </div>

                <div class="form-group">
                    <label>Lokasi Rak</label>
                    <input type="text" name="lokasi_rak">
                </div>

                <button type="submit" class="btn btn-primary">Simpan</button>
                <button type="button" class="btn btn-secondary" onclick="hideModal('addModal')">Batal</button>
            </form>
        </div>
    </div>

    <!-- Modal Edit Buku -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="hideModal('editModal')">&times;</span>
            <h2>Edit Buku</h2>
            <form method="POST">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" id="edit_id">
                
                <div class="form-group">
                    <label>Kode Buku *</label>
                    <input type="text" name="kode_buku" id="edit_kode_buku" required>
                </div>

                <div class="form-group">
                    <label>Judul *</label>
                    <input type="text" name="judul" id="edit_judul" required>
                </div>

                <div class="form-group">
                    <label>Pengarang *</label>
                    <input type="text" name="pengarang" id="edit_pengarang" required>
                </div>

                <div class="form-group">
                    <label>Penerbit</label>
                    <input type="text" name="penerbit" id="edit_penerbit">
                </div>

                <div class="form-group">
                    <label>Tahun Terbit</label>
                    <input type="number" name="tahun_terbit" id="edit_tahun_terbit" min="1900" max="<?php echo date('Y'); ?>">
                </div>

                <div class="form-group">
                    <label>Kategori</label>
                    <select name="kategori_id" id="edit_kategori_id">
                        <option value="">Pilih Kategori</option>
                        <?php foreach ($kategori_list as $k): ?>
                        <option value="<?php echo $k['id']; ?>"><?php echo $k['nama_kategori']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>ISBN</label>
                    <input type="text" name="isbn" id="edit_isbn">
                </div>

                <div class="form-group">
                    <label>Jumlah Total *</label>
                    <input type="number" name="jumlah_total" id="edit_jumlah_total" min="1" required>
                </div>

                <div class="form-group">
                    <label>Jumlah Tersedia *</label>
                    <input type="number" name="jumlah_tersedia" id="edit_jumlah_tersedia" min="0" required>
                </div>

                <div class="form-group">
                    <label>Lokasi Rak</label>
                    <input type="text" name="lokasi_rak" id="edit_lokasi_rak">
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

        function editBuku(data) {
            document.getElementById('edit_id').value = data.id;
            document.getElementById('edit_kode_buku').value = data.kode_buku;
            document.getElementById('edit_judul').value = data.judul;
            document.getElementById('edit_pengarang').value = data.pengarang;
            document.getElementById('edit_penerbit').value = data.penerbit;
            document.getElementById('edit_tahun_terbit').value = data.tahun_terbit;
            document.getElementById('edit_kategori_id').value = data.kategori_id;
            document.getElementById('edit_isbn').value = data.isbn;
            document.getElementById('edit_jumlah_total').value = data.jumlah_total;
            document.getElementById('edit_jumlah_tersedia').value = data.jumlah_tersedia;
            document.getElementById('edit_lokasi_rak').value = data.lokasi_rak;
            document.getElementById('editModal').style.display = 'block';
        }

        function deleteBuku(id) {
            if (confirm('Yakin ingin menghapus buku ini?')) {
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
