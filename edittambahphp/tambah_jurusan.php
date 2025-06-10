<?php

include "koneksi.php";
$db = new database();

if (isset($_POST['simpan'])){
    $db->tambah_jurusan(
        $_POST['namajurusan']
    ) ;
    header("location:data_jurusan.php");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Jurusan</title>
    <link rel="stylesheet" href="styletambah.css">
</head>
<body>
    <h2>Form Tambah Jurusan</h2>
    <form action="" method="post">

        <label for="namajurusan">Nama Jurusan :</label><br>
        <input type="text" id="namajurusan" name="namajurusan" required><br><br>

        <input type="submit" name="simpan" value="Tambah Jurusan">
    </form>
</body>
</html>