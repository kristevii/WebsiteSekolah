<?php

include "koneksi.php";
$db = new database();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Agama</title>
    <link rel="stylesheet" href="styledata.css">
</head>
<body>
    <h2>Data Agama</h2>
    <table border="1">
        <tr>
            <th>ID Agama</th>
            <th>Nama Agama</th>
            <th>Option</th>
        </tr>
        <?php
        $no = 1;
        foreach($db-> tampil_data_agama() as $x){
        ?>
        <tr>
            <td><?php echo $x['kodeagama'] ?></td>
            <td><?php echo $x['namaagama'] ?></td>
            <td>
                <a href="edit_agama.php?kodeagama=<?php echo $x['kodeagama']; ?>&aksi=edit">Edit</a>
                <a href="proses.php?kodeagama=<?php echo $x['kodeagama']; ?>&aksi=hapus">Hapus</a>
            </td>    
        </tr>
        <?php
        }
        ?>
    </table>
    <a href="tambah_agama.php?kodeagama=<?php echo $x['kodeagama']; ?>&aksi=tambah">Tambah Data</a>
</body>
</html>