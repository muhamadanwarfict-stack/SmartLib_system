<?php
class Buku {
    private $conn;
    private $table_name = "buku";

    public $id;
    public $kode_buku;
    public $judul;
    public $pengarang;
    public $penerbit;
    public $tahun_terbit;
    public $kategori_id;
    public $isbn;
    public $jumlah_total;
    public $jumlah_tersedia;
    public $lokasi_rak;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                  SET kode_buku=:kode_buku, judul=:judul, pengarang=:pengarang, 
                      penerbit=:penerbit, tahun_terbit=:tahun_terbit, kategori_id=:kategori_id,
                      isbn=:isbn, jumlah_total=:jumlah_total, jumlah_tersedia=:jumlah_tersedia,
                      lokasi_rak=:lokasi_rak";

        $stmt = $this->conn->prepare($query);

        $this->kode_buku = htmlspecialchars(strip_tags($this->kode_buku));
        $this->judul = htmlspecialchars(strip_tags($this->judul));
        $this->pengarang = htmlspecialchars(strip_tags($this->pengarang));
        $this->penerbit = htmlspecialchars(strip_tags($this->penerbit));
        $this->tahun_terbit = htmlspecialchars(strip_tags($this->tahun_terbit));
        $this->kategori_id = htmlspecialchars(strip_tags($this->kategori_id));
        $this->isbn = htmlspecialchars(strip_tags($this->isbn));
        $this->jumlah_total = htmlspecialchars(strip_tags($this->jumlah_total));
        $this->jumlah_tersedia = htmlspecialchars(strip_tags($this->jumlah_tersedia));
        $this->lokasi_rak = htmlspecialchars(strip_tags($this->lokasi_rak));

        $stmt->bindParam(':kode_buku', $this->kode_buku);
        $stmt->bindParam(':judul', $this->judul);
        $stmt->bindParam(':pengarang', $this->pengarang);
        $stmt->bindParam(':penerbit', $this->penerbit);
        $stmt->bindParam(':tahun_terbit', $this->tahun_terbit);
        $stmt->bindParam(':kategori_id', $this->kategori_id);
        $stmt->bindParam(':isbn', $this->isbn);
        $stmt->bindParam(':jumlah_total', $this->jumlah_total);
        $stmt->bindParam(':jumlah_tersedia', $this->jumlah_tersedia);
        $stmt->bindParam(':lokasi_rak', $this->lokasi_rak);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function readAll() {
        $query = "SELECT b.*, k.nama_kategori 
                  FROM " . $this->table_name . " b
                  LEFT JOIN kategori_buku k ON b.kategori_id = k.id
                  ORDER BY b.judul";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    public function readOne() {
        $query = "SELECT b.*, k.nama_kategori 
                  FROM " . $this->table_name . " b
                  LEFT JOIN kategori_buku k ON b.kategori_id = k.id
                  WHERE b.id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->kode_buku = $row['kode_buku'];
            $this->judul = $row['judul'];
            $this->pengarang = $row['pengarang'];
            $this->penerbit = $row['penerbit'];
            $this->tahun_terbit = $row['tahun_terbit'];
            $this->kategori_id = $row['kategori_id'];
            $this->isbn = $row['isbn'];
            $this->jumlah_total = $row['jumlah_total'];
            $this->jumlah_tersedia = $row['jumlah_tersedia'];
            $this->lokasi_rak = $row['lokasi_rak'];
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . "
                  SET kode_buku=:kode_buku, judul=:judul, pengarang=:pengarang, 
                      penerbit=:penerbit, tahun_terbit=:tahun_terbit, kategori_id=:kategori_id,
                      isbn=:isbn, jumlah_total=:jumlah_total, jumlah_tersedia=:jumlah_tersedia,
                      lokasi_rak=:lokasi_rak
                  WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        $this->kode_buku = htmlspecialchars(strip_tags($this->kode_buku));
        $this->judul = htmlspecialchars(strip_tags($this->judul));
        $this->pengarang = htmlspecialchars(strip_tags($this->pengarang));
        $this->penerbit = htmlspecialchars(strip_tags($this->penerbit));
        $this->tahun_terbit = htmlspecialchars(strip_tags($this->tahun_terbit));
        $this->kategori_id = htmlspecialchars(strip_tags($this->kategori_id));
        $this->isbn = htmlspecialchars(strip_tags($this->isbn));
        $this->jumlah_total = htmlspecialchars(strip_tags($this->jumlah_total));
        $this->jumlah_tersedia = htmlspecialchars(strip_tags($this->jumlah_tersedia));
        $this->lokasi_rak = htmlspecialchars(strip_tags($this->lokasi_rak));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':kode_buku', $this->kode_buku);
        $stmt->bindParam(':judul', $this->judul);
        $stmt->bindParam(':pengarang', $this->pengarang);
        $stmt->bindParam(':penerbit', $this->penerbit);
        $stmt->bindParam(':tahun_terbit', $this->tahun_terbit);
        $stmt->bindParam(':kategori_id', $this->kategori_id);
        $stmt->bindParam(':isbn', $this->isbn);
        $stmt->bindParam(':jumlah_total', $this->jumlah_total);
        $stmt->bindParam(':jumlah_tersedia', $this->jumlah_tersedia);
        $stmt->bindParam(':lokasi_rak', $this->lokasi_rak);
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
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

    public function updateKetersediaan($buku_id, $jumlah) {
        $query = "UPDATE " . $this->table_name . " 
                  SET jumlah_tersedia = jumlah_tersedia + :jumlah 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':jumlah', $jumlah);
        $stmt->bindParam(':id', $buku_id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function getTotalBuku() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    public function getBukuTersedia() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " 
                  WHERE jumlah_tersedia > 0";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    public function getBukuHabis() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " 
                  WHERE jumlah_tersedia = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    public function search($keyword) {
        $query = "SELECT b.*, k.nama_kategori 
                  FROM " . $this->table_name . " b
                  LEFT JOIN kategori_buku k ON b.kategori_id = k.id
                  WHERE b.judul LIKE :keyword OR b.pengarang LIKE :keyword OR b.kode_buku LIKE :keyword
                  ORDER BY b.judul";

        $stmt = $this->conn->prepare($query);
        $keyword = "%{$keyword}%";
        $stmt->bindParam(':keyword', $keyword);
        $stmt->execute();

        return $stmt;
    }
}
?>
