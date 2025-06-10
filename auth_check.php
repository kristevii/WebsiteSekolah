<?php
session_start();

$username = $_POST['username'];
$password = $_POST['password'];

$query = "SELECT * FROM users WHERE username = '$username' AND password '$password'";
$result = mysqli_query($conn, $query);

if($users = mysqli_fetch_assoc($result)) {
    $_SESSION['id_user'] = $users['id_user'];
    $_SESSION['nama'] = $users['nama'];
    $_SESSION['role'] = $users['role'];

    switch($users['role']) {
        case 'Admin';
            header("Location : dasboard/admin");
        break;    
        case 'Guru';
            header("Location : dasboard/guru");
        break;    
        case 'Siswa';
            header("Location : dasboard/siswa");
        break;    
    }
    exit;
}

?>