<?php

include "koneksi.php";
$db = new database();

if (isset($_POST['simpan'])){
    $db->tambah_agama(
        $_POST['namaagama']
    ) ;
    header("location:data_agama.php");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Agama</title>
    <link rel="stylesheet" href="styletambah.css">
</head>
<body>
    <h2>Form Tambah Agama</h2>
    <form action="" method="post">
        <label for="namaagama">Nama Agama :</label><br>
        <input type="text" id="namaagama" name="namaagama" required><br><br>

        <input type="submit" name="simpan" value="Tambah Agama">
    </form>
</body>
</html>