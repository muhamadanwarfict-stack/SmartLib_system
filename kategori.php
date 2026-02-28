<?php
require_once 'config/config.php';
requireRole(['admin']);

require_once 'models/KategoriBuku.php';

$database = new Database();
$db = $database->getConnection();

$kategori = new KategoriBuku($db);
$role = $_SESSION['user_role'];

$message = '';
$message_type = '';

if ($_POST) {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $kategori->kode_kategori = $_POST['kode_kategori'];
                $kategori->nama_kategori = $_POST['nama_kategori'];
                $kategori->deskripsi = $_POST['deskripsi'];
                
                if ($kategori->create()) {
                    $message = 'Kategori berhasil ditambahkan!';
                    $message_type = 'success';
                } else {
                    $message = 'Gagal menambahkan kategori!';
                    $message_type = 'error';
                }
                break;
                
            case 'update':
                $kategori->id = $_POST['id'];
                $kategori->kode_kategori = $_POST['kode_kategori'];
                $kategori->nama_kategori = $_POST['nama_kategori'];
                $kategori->deskripsi = $_POST['deskripsi'];
                
                if ($kategori->update()) {
                    $message = 'Kategori berhasil diupdate!';
                    $message_type = 'success';
                } else {
                    $message = 'Gagal mengupdate kategori!';
                    $message_type = 'error';
                }
                break;
                
            case 'delete':
                $kategori->id = $_POST['id'];
                if ($kategori->delete()) {
                    $message = 'Kategori berhasil dihapus!';
                    $message_type = 'success';
                } else {
                    $message = 'Gagal menghapus kategori!';
                    $message_type = 'error';
                }
                break;
        }
    }
}

$stmt = $kategori->readAll();
$kategori_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori Buku - <?php echo APP_NAME; ?></title>
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
                    <a href="kategori.php" class="nav-link active">
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
                
                <li class="nav-item">
                    <a href="logout.php" class="nav-link">
                        <i>🚪</i> Logout
                    </a>
                </li>
            </ul>
        </nav>

        <main class="main-content">
            <header class="top-nav">
                <h1>Kategori Buku</h1>
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
                    <h2>Kategori Buku</h2>
                    <button class="btn btn-primary" onclick="showAddModal()">
                        <i>➕</i> Tambah Kategori
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
                                <th>Nama Kategori</th>
                                <th>Deskripsi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($kategori_list as $row): ?>
                            <tr>
                                <td><?php echo $row['kode_kategori']; ?></td>
                                <td><?php echo $row['nama_kategori']; ?></td>
                                <td><?php echo $row['deskripsi']; ?></td>
                                <td>
                                    <button class="btn btn-sm btn-info" onclick='editKategori(<?php echo json_encode($row); ?>)'>Edit</button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteKategori(<?php echo $row['id']; ?>)">Hapus</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal Tambah -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="hideModal('addModal')">&times;</span>
            <h2>Tambah Kategori</h2>
            <form method="POST">
                <input type="hidden" name="action" value="create">
                
                <div class="form-group">
                    <label>Kode Kategori *</label>
                    <input type="text" name="kode_kategori" required>
                </div>

                <div class="form-group">
                    <label>Nama Kategori *</label>
                    <input type="text" name="nama_kategori" required>
                </div>

                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="deskripsi" rows="3"></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Simpan</button>
                <button type="button" class="btn btn-secondary" onclick="hideModal('addModal')">Batal</button>
            </form>
        </div>
    </div>

    <!-- Modal Edit -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="hideModal('editModal')">&times;</span>
            <h2>Edit Kategori</h2>
            <form method="POST">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" id="edit_id">
                
                <div class="form-group">
                    <label>Kode Kategori *</label>
                    <input type="text" name="kode_kategori" id="edit_kode_kategori" required>
                </div>

                <div class="form-group">
                    <label>Nama Kategori *</label>
                    <input type="text" name="nama_kategori" id="edit_nama_kategori" required>
                </div>

                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="deskripsi" id="edit_deskripsi" rows="3"></textarea>
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

        function editKategori(data) {
            document.getElementById('edit_id').value = data.id;
            document.getElementById('edit_kode_kategori').value = data.kode_kategori;
            document.getElementById('edit_nama_kategori').value = data.nama_kategori;
            document.getElementById('edit_deskripsi').value = data.deskripsi;
            document.getElementById('editModal').style.display = 'block';
        }

        function deleteKategori(id) {
            if (confirm('Yakin ingin menghapus kategori ini?')) {
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
