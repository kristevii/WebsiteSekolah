<?php

class database {
    var $host = "localhost";
    var $username = "root";
    var $password = "";
    var $db = "sekolah";
    public $koneksi; // Deklarasikan properti koneksi

    function __construct(){
        // Cek koneksi ke MySQL server
        $this->koneksi = mysqli_connect($this->host, $this->username, $this->password);

        if (mysqli_connect_errno()) {
            die("Koneksi database GAGAL: " . mysqli_connect_error());
        }

        // Cek pemilihan database
        $cekdb = mysqli_select_db($this->koneksi, $this->db);
        if (!$cekdb) {
            die("Database '{$this->db}' tidak ditemukan atau gagal dipilih.");
        }
    }

    // Metode untuk login dengan password plain text
    function login($username, $password) {
        // Lindungi input dari SQL Injection
        $username = mysqli_real_escape_string($this->koneksi, $username);
        $password = mysqli_real_escape_string($this->koneksi, $password);

        $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
        $result = mysqli_query($this->koneksi, $query); // Gunakan $this->koneksi

        if (!$result) {
            error_log("Login Query Error: " . mysqli_error($this->koneksi) . " Query: " . $query);
            return false;
        }

        if (mysqli_num_rows($result) == 1) {
            return mysqli_fetch_assoc($result); // Login berhasil, kembalikan data user
        }
        return false; // User tidak ditemukan atau password salah
    }
    function get_user_by_id($id_user) {
        $query = "SELECT * FROM users WHERE id_user = ?";
        $stmt = $this->koneksi->prepare($query);
        $stmt->bind_param("i", $id_user);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return null;
        }
    }

    // --- Function Tampil Data ---
    function tampil_data_siswa(){
        $hasil = [];
        $query = "SELECT
                    s.nisn as nisn,
                    s.nama as nama,
                    IF(s.jeniskelamin = 'L', 'Laki-laki', 'Perempuan') AS jeniskelamin,
                    j.namajurusan as kodejurusan,
                    s.kelas as kelas,
                    s.alamat as alamat,
                    a.namaagama as agama,
                    s.nohp as nohp
                FROM siswa s
                LEFT JOIN jurusan j ON s.kodejurusan = j.kodejurusan
                LEFT JOIN agama a ON s.agama = a.idagama";

        $data = mysqli_query($this->koneksi, $query);

        if (!$data) {
            error_log("Query error tampil_data_siswa: " . mysqli_error($this->koneksi));
            return []; // Kembalikan array kosong jika query gagal
        }

        while ($row = mysqli_fetch_array($data)){
            $hasil[] = $row;
        }
        return $hasil;
    }

    function tampil_data_jurusan(){
        $hasil = [];
        $data = mysqli_query($this->koneksi, "select * from jurusan");
        if (!$data) {
            error_log("Query error tampil_data_jurusan: " . mysqli_error($this->koneksi));
            return [];
        }
        while ($row = mysqli_fetch_array($data)){
            $hasil[] = $row;
        }
        return $hasil;
    }

    function tampil_data_agama(){
        $hasil = [];
        $data = mysqli_query($this->koneksi, "select * from agama");
        if (!$data) {
            error_log("Query error tampil_data_agama: " . mysqli_error($this->koneksi));
            return [];
        }
        while ($row = mysqli_fetch_array($data)){
            $hasil[] = $row;
        }
        return $hasil;
    }

    function tampil_data_user(){
        $hasil = [];
        $data = mysqli_query($this->koneksi, "select id_user, username, password, nama, role from users");
        if (!$data) {
            error_log("Query error tampil_data_user: " . mysqli_error($this->koneksi));
            return [];
        }
        while ($row = mysqli_fetch_array($data)){
            $hasil[] = $row;
        }
        return $hasil;
    }

    // --- Function Tambah Data ---
    function tambah_siswa($nisn,$nama,$jeniskelamin,$kodejurusan,$kelas,$alamat,$agama,$nohp) {
        // Lindungi dari SQL Injection
        $nisn = mysqli_real_escape_string($this->koneksi, $nisn);
        $nama = mysqli_real_escape_string($this->koneksi, $nama);
        $jeniskelamin = mysqli_real_escape_string($this->koneksi, $jeniskelamin);
        $kodejurusan = mysqli_real_escape_string($this->koneksi, $kodejurusan);
        $kelas = mysqli_real_escape_string($this->koneksi, $kelas);
        $alamat = mysqli_real_escape_string($this->koneksi, $alamat);
        $agama = mysqli_real_escape_string($this->koneksi, $agama);
        $nohp = mysqli_real_escape_string($this->koneksi, $nohp);

        $sql = "INSERT INTO siswa(nisn,nama,jeniskelamin,kodejurusan,kelas,alamat,agama,nohp) VALUES (
        '$nisn','$nama','$jeniskelamin','$kodejurusan','$kelas','$alamat','$agama','$nohp')";
        $result = mysqli_query($this->koneksi, $sql);

        if ($result) {
            return true;
        } else {
            error_log("Error tambah_siswa: " . mysqli_error($this->koneksi) . " Query: " . $sql);
            return false;
        }
    }

    function tambah_jurusan($namajurusan) {
        // Lindungi dari SQL Injection
        $namajurusan = mysqli_real_escape_string($this->koneksi, $namajurusan);

        $sql = "INSERT INTO jurusan(namajurusan) VALUES ('$namajurusan')";
        $result = mysqli_query($this->koneksi, $sql);

        if ($result) {
            return true;
        } else {
            error_log("Error tambah_jurusan: " . mysqli_error($this->koneksi) . " Query: " . $sql);
            return false;
        }
    }

    function tambah_agama($namaagama) {
        // Lindungi dari SQL Injection
        $namaagama = mysqli_real_escape_string($this->koneksi, $namaagama);

        $sql = "INSERT INTO agama(namaagama) VALUES ('$namaagama')";
        $result = mysqli_query($this->koneksi, $sql);

        if ($result) {
            return true;
        } else {
            error_log("Error tambah_agama: " . mysqli_error($this->koneksi) . " Query: " . $sql);
            return false;
        }
    }

    function tambah_user($username, $password_plain, $nama, $role) {
        $username = mysqli_real_escape_string($this->koneksi, $username);
        $nama = mysqli_real_escape_string($this->koneksi, $nama);
        $role = mysqli_real_escape_string($this->koneksi, $role);
        // Simpan password sebagai plain text
        $password = mysqli_real_escape_string($this->koneksi, $password_plain);

        $sql = "INSERT INTO users(username, password, nama, role) VALUES (
        '$username', '$password', '$nama', '$role')";
        $result = mysqli_query($this->koneksi, $sql);

        if ($result) {
            return true;
        } else {
            error_log("Error tambah_user: " . mysqli_error($this->koneksi) . " Query: " . $sql);
            return false;
        }
    }
    // --- Function Edit Data ---
    function edit_data_siswa($nisn,$nama,$jeniskelamin,$kodejurusan,$kelas,$alamat,$agama,$nohp) {
        // Lindungi dari SQL Injection
        $nisn = mysqli_real_escape_string($this->koneksi, $nisn);
        $nama = mysqli_real_escape_string($this->koneksi, $nama);
        $jeniskelamin = mysqli_real_escape_string($this->koneksi, $jeniskelamin);
        $kodejurusan = mysqli_real_escape_string($this->koneksi, $kodejurusan);
        $kelas = mysqli_real_escape_string($this->koneksi, $kelas);
        $alamat = mysqli_real_escape_string($this->koneksi, $alamat);
        $agama = mysqli_real_escape_string($this->koneksi, $agama);
        $nohp = mysqli_real_escape_string($this->koneksi, $nohp);

        $sql = "UPDATE siswa SET nama = '$nama', jeniskelamin = '$jeniskelamin',
        kodejurusan = '$kodejurusan', kelas = '$kelas', alamat = '$alamat', agama = '$agama',
        nohp = '$nohp' WHERE nisn = '$nisn'";
        $result = mysqli_query($this->koneksi, $sql);

        if ($result) {
            return true;
        } else {
            error_log("Error edit_data_siswa: " . mysqli_error($this->koneksi) . " Query: " . $sql);
            return false;
        }
    }

    function edit_data_jurusan($kodejurusan, $namajurusan) {
        // Lindungi dari SQL Injection
        $kodejurusan = mysqli_real_escape_string($this->koneksi, $kodejurusan);
        $namajurusan = mysqli_real_escape_string($this->koneksi, $namajurusan);

        $sql = "UPDATE jurusan SET namajurusan = '$namajurusan' WHERE kodejurusan = '$kodejurusan'";
        $result = mysqli_query($this->koneksi, $sql);

        if ($result) {
            return true;
        } else {
            error_log("Error edit_data_jurusan: " . mysqli_error($this->koneksi) . " Query: " . $sql);
            return false;
        }
    }

    function edit_data_agama($idagama, $namaagama) {
        // Lindungi dari SQL Injection
        $idagama = mysqli_real_escape_string($this->koneksi, $idagama);
        $namaagama = mysqli_real_escape_string($this->koneksi, $namaagama);

        $sql = "UPDATE agama SET namaagama = '$namaagama' WHERE idagama = '$idagama'";
        $result = mysqli_query($this->koneksi, $sql);

        if ($result) {
            return true;
        } else {
            error_log("Error edit_data_agama: " . mysqli_error($this->koneksi) . " Query: " . $sql);
            return false;
        }
    }

    function edit_data_user($id_user, $username, $password, $nama, $role) {
        // Lindungi dari SQL Injection
        $id_user = mysqli_real_escape_string($this->koneksi, $id_user);
        $username = mysqli_real_escape_string($this->koneksi, $username);
        $nama = mysqli_real_escape_string($this->koneksi, $nama);
        $role = mysqli_real_escape_string($this->koneksi, $role);

        $sql = "UPDATE users SET username = '$username', nama = '$nama', role = '$role'";
        if ($password !== null && !empty($password)) {
            $password = mysqli_real_escape_string($this->koneksi, $password);
            $sql .= ", password = '$password'"; // Tambahkan update password jika ada password baru
        }
        $sql .= " WHERE id_user = '$id_user'";

        $result = mysqli_query($this->koneksi, $sql);

        if ($result) {
            return true;
        } else {
            error_log("Error edit_data_user: " . mysqli_error($this->koneksi) . " Query: " . $sql);
            return false;
        }
    }

    // --- Function Hapus Data ---
    function hapus_data_siswa($nisn) {
        // Lindungi dari SQL Injection
        $nisn = mysqli_real_escape_string($this->koneksi, $nisn);

        $sql = "DELETE FROM siswa WHERE nisn = '$nisn'";
        $result = mysqli_query($this->koneksi, $sql);

        if ($result) {
            return true;
        } else {
            error_log("Error hapus_data_siswa: " . mysqli_error($this->koneksi) . " Query: " . $sql);
            return false;
        }
    }

    function hapus_data_agama($idagama) {
        // Lindungi dari SQL Injection
        $idagama = mysqli_real_escape_string($this->koneksi, $idagama);

        $sql = "DELETE FROM agama WHERE idagama = '$idagama'";
        $result = mysqli_query($this->koneksi, $sql);

        if ($result) {
            return true;
        } else {
            error_log("Error hapus_data_agama: " . mysqli_error($this->koneksi) . " Query: " . $sql);
            return false;
        }
    }

    function hapus_data_jurusan($kodejurusan) {
        // Lindungi dari SQL Injection
        $kodejurusan = mysqli_real_escape_string($this->koneksi, $kodejurusan);

        $sql = "DELETE FROM jurusan WHERE kodejurusan = '$kodejurusan'";
        $result = mysqli_query($this->koneksi, $sql);

        if ($result) {
            return true;
        } else {
            error_log("Error hapus_data_jurusan: " . mysqli_error($this->koneksi) . " Query: " . $sql);
            return false;
        }
    }

    function hapus_data_user($username) {
        // Lindungi dari SQL Injection
        $username = mysqli_real_escape_string($this->koneksi, $username);

        $sql = "DELETE FROM users WHERE username = '$username'";
        $result = mysqli_query($this->koneksi, $sql);

        if ($result) {
            return true;
        } else {
            error_log("Error hapus_data_user: " . mysqli_error($this->koneksi) . " Query: " . $sql);
            return false;
        }
    }

    // --- Function Jumlah Data di Dashboard ---
    function jumlahdata_siswa(){
        $data = mysqli_query($this->koneksi, "SELECT COUNT(*) as total from siswa");
        if (!$data) {
            error_log("Query error jumlahdata_siswa: " . mysqli_error($this->koneksi));
            return 0;
        }
        $hasil = mysqli_fetch_assoc($data);
        return $hasil['total'];
    }

    function jumlahdata_agama(){
        $data = mysqli_query($this->koneksi, "SELECT COUNT(*) as total from agama");
        if (!$data) {
            error_log("Query error jumlahdata_agama: " . mysqli_error($this->koneksi));
            return 0;
        }
        $hasil = mysqli_fetch_assoc($data);
        return $hasil['total'];
    }

    function jumlahdata_jurusan(){
        $data = mysqli_query($this->koneksi, "SELECT COUNT(*) as total from jurusan");
        if (!$data) {
            error_log("Query error jumlahdata_jurusan: " . mysqli_error($this->koneksi));
            return 0;
        }
        $hasil = mysqli_fetch_assoc($data);
        return $hasil['total'];
    }

    function jumlahdata_user(){
        $data = mysqli_query($this->koneksi, "SELECT COUNT(*) as total from users");
        if (!$data) {
            error_log("Query error jumlahdata_user: " . mysqli_error($this->koneksi));
            return 0;
        }
        $hasil = mysqli_fetch_assoc($data);
        return $hasil['total'];
    }
}

?>