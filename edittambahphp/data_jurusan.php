<?php

include "koneksi.php";
$db = new database();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Jurusan</title>
    <link rel="stylesheet" href="styledata.css">
</head>
<body>
    <h2>Data Jurusan</h2>
    <table border="1">
        <tr>
            <th>Kode Jurusan</th>
            <th>Nama Jurusan</th>
            <th>Option</th>
        </tr>
        <?php
        $no = 1;
        foreach($db-> tampil_data_jurusan() as $x){
        ?>
        <tr>
            <td><?php echo $x['kodejurusan'] ?></td>
            <td><?php echo $x['namajurusan'] ?></td>
            <td>
                <a href="edit_jurusan.php?kodejurusan=<?php echo $x['kodejurusan']; ?>&aksi=edit">Edit</a>
                <a href="proses.php?kodejurusan=<?php echo $x['kodejurusan']; ?>&aksi=hapus">Hapus</a>
            </td>    
        </tr>
        <?php
        }
        ?>
    </table>
    <a href="tambah_jurusan.php?kodejurusan=<?php echo $x['kodejurusan']; ?>&aksi=tambah">Tambah Data Jurusan</a>
</body>
</html>