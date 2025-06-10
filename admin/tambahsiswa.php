<?php
session_start();
include "koneksi.php";
$db = new database();

// Cek apakah user sudah login
if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit();
}

// Cek role user
if ($_SESSION['role'] != 'Admin') {
    header("Location: ../unauthorized.php");
    exit();
}

if (isset($_POST['simpan'])){
    $db->tambah_siswa(
        $_POST['nisn'],
        $_POST['nama'],
        $_POST['jeniskelamin'],
        $_POST['kodejurusan'],
        $_POST['kelas'],
        $_POST['alamat'],
        $_POST['agama'],
        $_POST['nohp']
    );
    header("location:datasiswa.php");
}
?>

<!doctype html>
<html lang="en">
<head>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.min.css">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AdminSekolah | Form Tambah Data Siswa</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="title" content="AdminLTE 4 | General Form Elements" />
    <meta name="author" content="ColorlibHQ" />
    <meta name="description" content="AdminLTE is a Free Bootstrap 5 Admin Dashboard" />
    <meta name="keywords" content="bootstrap 5, bootstrap, bootstrap 5 admin dashboard" />
    
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
      integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q="
      crossorigin="anonymous"
    />
    <!--end::Fonts-->
    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/styles/overlayscrollbars.min.css"
      integrity="sha256-tZHrRjVqNSRyWg2wbppGnT833E/Ys0DHWGwT04GiqQg="
      crossorigin="anonymous"
    />
    <!--end::Third Party Plugin(OverlayScrollbars)-->
    <!--begin::Third Party Plugin(Bootstrap Icons)-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
      integrity="sha256-9kPW/n5nn53j4WMRYAxe9c1rCY96Oogo/MKSVdKzPmI="
      crossorigin="anonymous"
    />
    <!--end::Third Party Plugin(Bootstrap Icons)-->
    <!--begin::Required Plugin(AdminLTE)-->
    <link rel="stylesheet" href="../dist/css/adminlte.css" />
    <!--end::Required Plugin(AdminLTE)-->
    
    <style>
        /* Responsive Form Styles */
        .form-container {
            max-width: 100%;
            padding: 20px;
        }
        
        .form-label {
            font-weight: 500;
            margin-bottom: 8px;
        }
        
        .form-control {
            padding: 10px 15px;
            border-radius: 6px;
            border: 1px solid #ced4da;
            width: 100%;
        }
        
        .form-control:focus {
            border-color: #4a6bff;
            box-shadow: 0 0 0 0.25rem rgba(74, 107, 255, 0.25);
        }
        
        .radio-group {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }
        
        .form-check {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .form-check-input {
            margin-top: 0;
        }
        
        .card-footer {
            background-color: transparent;
            border-top: none;
            padding: 20px 0 0;
        }
        
        .btn-primary {
            background-color: #4a6bff;
            border-color: #4a6bff;
            padding: 10px 20px;
            font-weight: 500;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .form-container {
                padding: 15px;
            }
            
            .radio-group {
                flex-direction: column;
                gap: 10px;
            }
            
            .form-check {
                margin-bottom: 5px;
            }
            
            .card-body {
                padding: 15px;
            }
        }
        
        @media (max-width: 576px) {
            .form-container {
                padding: 10px;
            }
            
            .form-label {
                font-size: 14px;
            }
            
            .form-control {
                padding: 8px 12px;
                font-size: 14px;
            }
            
            .btn-primary {
                padding: 8px 16px;
                font-size: 14px;
            }
        }

    </style>
</head>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">
        <!-- Header -->
        <nav class="app-header navbar navbar-expand bg-body sticky-top">
            <div class="container-fluid">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                            <i class="bi bi-list"></i>
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-lte-toggle="fullscreen">
                            <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i>
                            <i data-lte-icon="minimize" class="bi bi-fullscreen-exit" style="display: none"></i>
                        </a>
                    </li>
                    <li class="nav-item dropdown user-menu">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <img src="dist/assets/img/user.png" class="user-image rounded-circle shadow" alt="User Image" />
                            <span class="d-none d-md-inline"><?php echo $_SESSION['nama']; ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                            <li class="user-header text-bg-primary">
                                <img src="dist/assets/img/user.png" class="rounded-circle shadow" alt="User Image" />
                                <p>
                                    <?php echo $_SESSION['nama']; ?>
                                    <small><?php echo $_SESSION['role']; ?></small>
                                </p>
                            </li>
                            <li class="user-footer">
                                <a href="profile.php" class="btn btn-default btn-flat">Profile</a>
                                <a href="../logout.php" class="btn btn-default btn-flat float-end">Sign Out</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
        
        <?php include "sidebar.php"; ?>
        
        <main class="app-main">
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0">Form Tambah Data Siswa</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">Form Tambah Data Siswa</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="app-content">
                <div class="container-fluid">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card card-primary card-outline mb-4">
                                <div class="card-body form-container">
                                    <form action="" method="post">
                                        <div class="mb-3">
                                            <label for="nisn" class="form-label">NISN</label>
                                            <input type="text" class="form-control" id="nisn" name="nisn" required>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="nama" class="form-label">Nama Lengkap</label>
                                            <input type="text" class="form-control" id="nama" name="nama" required>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Jenis Kelamin</label>
                                            <div class="radio-group">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="jeniskelamin" id="gridRadios1" value="L" checked>
                                                    <label class="form-check-label" for="gridRadios1">Laki-laki</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="jeniskelamin" id="gridRadios2" value="P">
                                                    <label class="form-check-label" for="gridRadios2">Perempuan</label>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="kodejurusan" class="form-label">Jurusan</label>
                                            <select class="form-select" id="kodejurusan" name="kodejurusan" required>
                                                <option value="">Pilih Jurusan</option>
                                                <option value="1">Akutansi</option>
                                                <option value="2">Perkantoran</option>
                                                <option value="3">Pariwisata</option>
                                                <option value="4">DKV</option>
                                                <option value="5">RPL</option>
                                                <option value="6">Broadcasting</option>
                                            </select>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="kelas" class="form-label">Kelas</label>
                                            <select class="form-select" id="kelas" name="kelas" required>
                                                <option value="">Pilih Kelas</option>
                                                <option value="X">X</option>
                                                <option value="XI">XI</option>
                                                <option value="XII">XII</option>
                                            </select>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="alamat" class="form-label">Alamat</label>
                                            <input type="text" class="form-control" id="alamat" name="alamat" required>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="agama" class="form-label">Agama</label>
                                            <select class="form-select" id="agama" name="agama" required>
                                                <option value="">Pilih Agama</option>
                                                <option value="1">Islam</option>
                                                <option value="2">Kristen</option>
                                                <option value="3">Katholik</option>
                                                <option value="4">Hindu</option>
                                                <option value="5">Buddha</option>
                                                <option value="6">Konghucu</option>
                                            </select>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="nohp" class="form-label">Nomor Handphone</label>
                                            <input type="text" class="form-control" id="nohp" name="nohp" required>
                                        </div>
                                        
                                        <div class="card-footer text-end">
                                            <button type="submit" class="btn btn-primary" name="simpan">Simpan Data</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        
<footer class="app-footer">
        <!--begin::To the end-->
        <div class="float-end d-none d-sm-inline">Anything you want</div>
        <!--end::To the end-->
        <!--begin::Copyright-->
        <strong>
          Copyright &copy; 2014-2024&nbsp;
          <a href="https://adminlte.io" class="text-decoration-none">AdminLTE.io</a>.
        </strong>
        All rights reserved.
        <!--end::Copyright-->
      </footer>
    </div>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    <script src="dist/js/adminlte.js"></script>
    
    <script>
        // Form validation
        (function() {
            'use strict';
            
            const forms = document.querySelectorAll('.needs-validation');
            
            Array.from(forms).forEach((form) => {
                form.addEventListener('submit', (event) => {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();

    </script>
</body>
</html>