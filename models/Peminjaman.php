<?php
class Peminjaman {
    private $conn;
    private $table_name = "peminjaman";

    public $id;
    public $no_peminjaman;
    public $anggota_id;
    public $buku_id;
    public $tanggal_pinjam;
    public $tanggal_kembali;
    public $tanggal_dikembalikan;
    public $denda;
    public $status;
    public $petugas_id;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                  SET no_peminjaman=:no_peminjaman, anggota_id=:anggota_id, buku_id=:buku_id,
                      tanggal_pinjam=:tanggal_pinjam, tanggal_kembali=:tanggal_kembali,
                      status=:status, petugas_id=:petugas_id";

        $stmt = $this->conn->prepare($query);

        $this->no_peminjaman = htmlspecialchars(strip_tags($this->no_peminjaman));
        $this->anggota_id = htmlspecialchars(strip_tags($this->anggota_id));
        $this->buku_id = htmlspecialchars(strip_tags($this->buku_id));
        $this->tanggal_pinjam = htmlspecialchars(strip_tags($this->tanggal_pinjam));
        $this->tanggal_kembali = htmlspecialchars(strip_tags($this->tanggal_kembali));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->petugas_id = htmlspecialchars(strip_tags($this->petugas_id));

        $stmt->bindParam(':no_peminjaman', $this->no_peminjaman);
        $stmt->bindParam(':anggota_id', $this->anggota_id);
        $stmt->bindParam(':buku_id', $this->buku_id);
        $stmt->bindParam(':tanggal_pinjam', $this->tanggal_pinjam);
        $stmt->bindParam(':tanggal_kembali', $this->tanggal_kembali);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':petugas_id', $this->petugas_id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function readAll() {
        $query = "SELECT p.*, a.nama_lengkap as nama_anggota, a.no_anggota,
                         b.judul as judul_buku, b.kode_buku,
                         u.nama_lengkap as nama_petugas
                  FROM " . $this->table_name . " p
                  LEFT JOIN anggota a ON p.anggota_id = a.id
                  LEFT JOIN buku b ON p.buku_id = b.id
                  LEFT JOIN users u ON p.petugas_id = u.id
                  ORDER BY p.tanggal_pinjam DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    public function readRecent($limit = 5) {
        $query = "SELECT p.*, a.nama_lengkap as nama_anggota, b.judul as judul_buku
                  FROM " . $this->table_name . " p
                  LEFT JOIN anggota a ON p.anggota_id = a.id
                  LEFT JOIN buku b ON p.buku_id = b.id
                  ORDER BY p.tanggal_pinjam DESC
                  LIMIT :limit";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt;
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function getTotalPeminjamanAktif() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " 
                  WHERE status = 'dipinjam'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    public function getTerlambat() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " 
                  WHERE status = 'dipinjam' AND tanggal_kembali < CURDATE()";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    public function getTotalPeminjamanHariIni() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " 
                  WHERE DATE(tanggal_pinjam) = CURDATE()";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    public function getTotalPeminjamanBulanIni() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " 
                  WHERE MONTH(tanggal_pinjam) = MONTH(CURDATE()) 
                  AND YEAR(tanggal_pinjam) = YEAR(CURDATE())";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    public function pengembalian($id, $tanggal_dikembalikan, $denda) {
        $query = "UPDATE " . $this->table_name . "
                  SET tanggal_dikembalikan=:tanggal_dikembalikan, denda=:denda, status='dikembalikan'
                  WHERE id=:id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':tanggal_dikembalikan', $tanggal_dikembalikan);
        $stmt->bindParam(':denda', $denda);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function readOne() {
        $query = "SELECT p.*, a.nama_lengkap as nama_anggota, a.no_anggota,
                         b.judul as judul_buku, b.kode_buku,
                         u.nama_lengkap as nama_petugas
                  FROM " . $this->table_name . " p
                  LEFT JOIN anggota a ON p.anggota_id = a.id
                  LEFT JOIN buku b ON p.buku_id = b.id
                  LEFT JOIN users u ON p.petugas_id = u.id
                  WHERE p.id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    public function getTotalDenda() {
        $query = "SELECT SUM(denda) as total FROM " . $this->table_name . " 
                  WHERE status = 'dikembalikan' AND denda > 0";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] ?? 0;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . "
                  SET anggota_id=:anggota_id, buku_id=:buku_id,
                      tanggal_pinjam=:tanggal_pinjam, tanggal_kembali=:tanggal_kembali
                  WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        $this->anggota_id = htmlspecialchars(strip_tags($this->anggota_id));
        $this->buku_id = htmlspecialchars(strip_tags($this->buku_id));
        $this->tanggal_pinjam = htmlspecialchars(strip_tags($this->tanggal_pinjam));
        $this->tanggal_kembali = htmlspecialchars(strip_tags($this->tanggal_kembali));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':anggota_id', $this->anggota_id);
        $stmt->bindParam(':buku_id', $this->buku_id);
        $stmt->bindParam(':tanggal_pinjam', $this->tanggal_pinjam);
        $stmt->bindParam(':tanggal_kembali', $this->tanggal_kembali);
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
