<?php
class Anggota {
    private $conn;
    private $table_name = "anggota";

    public $id;
    public $no_anggota;
    public $nama_lengkap;
    public $alamat;
    public $telepon;
    public $email;
    public $tanggal_daftar;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                  SET no_anggota=:no_anggota, nama_lengkap=:nama_lengkap, alamat=:alamat, 
                      telepon=:telepon, email=:email, tanggal_daftar=:tanggal_daftar";

        $stmt = $this->conn->prepare($query);

        $this->no_anggota = htmlspecialchars(strip_tags($this->no_anggota));
        $this->nama_lengkap = htmlspecialchars(strip_tags($this->nama_lengkap));
        $this->alamat = htmlspecialchars(strip_tags($this->alamat));
        $this->telepon = htmlspecialchars(strip_tags($this->telepon));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->tanggal_daftar = htmlspecialchars(strip_tags($this->tanggal_daftar));

        $stmt->bindParam(':no_anggota', $this->no_anggota);
        $stmt->bindParam(':nama_lengkap', $this->nama_lengkap);
        $stmt->bindParam(':alamat', $this->alamat);
        $stmt->bindParam(':telepon', $this->telepon);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':tanggal_daftar', $this->tanggal_daftar);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY nama_lengkap";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->no_anggota = $row['no_anggota'];
            $this->nama_lengkap = $row['nama_lengkap'];
            $this->alamat = $row['alamat'];
            $this->telepon = $row['telepon'];
            $this->email = $row['email'];
            $this->tanggal_daftar = $row['tanggal_daftar'];
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . "
                  SET no_anggota=:no_anggota, nama_lengkap=:nama_lengkap, alamat=:alamat, 
                      telepon=:telepon, email=:email
                  WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        $this->no_anggota = htmlspecialchars(strip_tags($this->no_anggota));
        $this->nama_lengkap = htmlspecialchars(strip_tags($this->nama_lengkap));
        $this->alamat = htmlspecialchars(strip_tags($this->alamat));
        $this->telepon = htmlspecialchars(strip_tags($this->telepon));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':no_anggota', $this->no_anggota);
        $stmt->bindParam(':nama_lengkap', $this->nama_lengkap);
        $stmt->bindParam(':alamat', $this->alamat);
        $stmt->bindParam(':telepon', $this->telepon);
        $stmt->bindParam(':email', $this->email);
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

    public function getTotalAnggota() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    public function getAnggotaBaru($days = 30) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " 
                  WHERE tanggal_daftar >= DATE_SUB(CURDATE(), INTERVAL :days DAY)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':days', $days, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    public function search($keyword) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE nama_lengkap LIKE :keyword OR no_anggota LIKE :keyword
                  ORDER BY nama_lengkap";

        $stmt = $this->conn->prepare($query);
        $keyword = "%{$keyword}%";
        $stmt->bindParam(':keyword', $keyword);
        $stmt->execute();

        return $stmt;
    }
}
?>
