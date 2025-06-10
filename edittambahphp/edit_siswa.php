<?php
include "koneksi.php";
$db = new database();

// Cek apakah NISN tersedia di URL
if (isset($_GET['nisn'])) {
    $nisn = $_GET['nisn'];

    // Ambil data siswa berdasarkan NISN
    $query = "SELECT * FROM siswa WHERE nisn = '$nisn'";
    $result = mysqli_query($db->koneksi, $query);

    if (!$result) {
        die("Query Error: " . mysqli_error($db->koneksi));
    }

    $data = mysqli_fetch_assoc($result);
}

// Jika tombol "Simpan Perubahan" ditekan
if (isset($_POST['update'])) {
    $db->edit_data_siswa(
        $_POST['nisn'],
        $_POST['nama'],
        $_POST['jeniskelamin'],
        $_POST['kodejurusan'],
        $_POST['kelas'],
        $_POST['alamat'],
        $_POST['agama'],
        $_POST['nohp']
    );

    echo "<script>alert('Data berhasil diperbarui!'); window.location='data_siswa.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Siswa</title>
</head>
<body>
    <h2>Form Edit Data Siswa</h2>
    <form action="" method="post">
        <input type="hidden" name="nisn" value="<?php echo $data['nisn']; ?>">

        <label for="nama">Nama:</label><br>
        <input type="text" id="nama" name="nama" value="<?php echo $data['nama']; ?>" required><br><br>

        <label for="jeniskelamin">Jenis Kelamin:</label><br>
        <input type="radio" id="laki-laki" name="jeniskelamin" value="L" <?php echo ($data['jeniskelamin'] == 'L') ? 'checked' : ''; ?>> Laki-laki
        <input type="radio" id="perempuan" name="jeniskelamin" value="P" <?php echo ($data['jeniskelamin'] == 'P') ? 'checked' : ''; ?>> Perempuan
        <br><br>

        <label for="kodejurusan">Jurusan:</label><br>
        <input type="text" id="kodejurusan" name="kodejurusan" value="<?php echo $data['kodejurusan']; ?>" required><br><br>

        <label for="kelas">Kelas:</label><br>
        <input type="text" id="kelas" name="kelas" value="<?php echo $data['kelas']; ?>" required><br><br>

        <label for="alamat">Alamat:</label><br>
        <input type="text" id="alamat" name="alamat" value="<?php echo $data['alamat']; ?>" required><br><br>

        <label for="agama">Agama:</label><br>
        <input type="text" id="agama" name="agama" value="<?php echo $data['agama']; ?>" required><br><br>

        <label for="nohp">Nomor Telepon:</label><br>
        <input type="text" id="nohp" name="nohp" value="<?php echo $data['nohp']; ?>" required><br><br>

        <input type="submit" name="update" value="Simpan Perubahan">
    </form>
</body>
</html>