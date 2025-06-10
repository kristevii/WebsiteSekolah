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

// Fungsi untuk menampilkan notifikasi dari session
function display_session_alert() {
    if (isset($_SESSION['notif_status']) && isset($_SESSION['notif_message'])) {
        $alert_class = ($_SESSION['notif_status'] == 'success') ? 'alert-success' : 'alert-danger';
        $message = htmlspecialchars($_SESSION['notif_message'], ENT_QUOTES, 'UTF-8');
        echo "<div class='alert {$alert_class} alert-dismissible fade show' role='alert'>";
        echo $message;
        echo "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>";
        echo "</div>";
        unset($_SESSION['notif_status']);
        unset($_SESSION['notif_message']);
    }
}

// Proses Hapus Data User
if (isset($_GET['hapus'])) {
    $nisn_to_delete = $_GET['hapus'];

    if ($db->hapus_data_siswa($nisn_to_delete)) {
        $_SESSION['notif_status'] = 'success';
        $_SESSION['notif_message'] = 'Data berhasil dihapus!';
    } else {
        $_SESSION['notif_status'] = 'danger';
        $error_message = 'Gagal menghapus data!';
        if (isset($db->koneksi) && is_object($db->koneksi) && method_exists($db->koneksi, 'error') && !empty($db->koneksi->error)) {
            $error_message .= " Error: " . $db->koneksi->error;
        }
        $_SESSION['notif_message'] = $error_message;
    }
    header("location:datasiswa.php");
    exit;
}

// Proses Tambah Data User
if (isset($_POST['simpan'])) {
    if ($db->tambah_siswa(
        $_POST['nisn'],
        $_POST['nama'],
        $_POST['jeniskelamin'],
        $_POST['kodejurusan'],
        $_POST['kelas'],
        $_POST['alamat'],
        $_POST['agama'],
        $_POST['nohp']
    )) {
        $_SESSION['notif_status'] = 'success';
        $_SESSION['notif_message'] = 'Data berhasil ditambahkan!';
    } else {
        $_SESSION['notif_status'] = 'danger';
        $_SESSION['notif_message'] = 'Gagal menambah data pengguna!';
    }
    header("location:datasiswa.php");
    exit;
}

// Proses Update Data User
if (isset($_POST['update'])) {
    $jeniskelamin = isset($_POST['jeniskelamin']) ? $_POST['jeniskelamin'] : '';
    if ($db->edit_data_siswa(
        $_POST['nisn'],
        $_POST['nama'],
        $jeniskelamin,
        $_POST['kodejurusan'],
        $_POST['kelas'],
        $_POST['alamat'],
        $_POST['agama'],
        $_POST['nohp']
    )) {
        $_SESSION['notif_status'] = 'success';
        $_SESSION['notif_message'] = 'Data berhasil diperbarui!';
    } else {
        $_SESSION['notif_status'] = 'danger';
        $_SESSION['notif_message'] = 'Gagal memperbarui data!';
    }
    header("location:datasiswa.php");
    exit;
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Data Siswa | Sekolah</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="title" content="Data Siswa" />
    <meta name="author" content="ColorlibHQ" />
    <meta name="description" content="AdminLTE is a Free Bootstrap 5 Admin Dashboard, 30 example pages using Vanilla JS." />
    <meta name="keywords" content="bootstrap 5, bootstrap, bootstrap 5 admin dashboard, bootstrap 5 dashboard, bootstrap 5 charts, bootstrap 5 calendar, bootstrap 5 datepicker, bootstrap 5 tables, bootstrap 5 datatable, vanilla js datatable, colorlibhq, colorlibhq dashboard, colorlibhq admin dashboard" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css" integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q=" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/styles/overlayscrollbars.min.css" integrity="sha256-tZHrRjVqNSRyWg2wbppGnT833E/Ys0DHWGwT04GiqQg=" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" integrity="sha256-9kPW/n5nn53j4WMRYAxe9c1rCY96Oogo/MKSVdKzPmI=" crossorigin="anonymous" />
    <link rel="stylesheet" href="dist/css/adminlte.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <style>
        /* Responsive Table Styles */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        #dataSiswa {
            width: 100% !important;
        }

        /* Responsive Modal Styles */
        .custom-modal {
            display: none;
            position: fixed;
            z-index: 1050;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.5);
        }

        .custom-modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 90%;
            max-width: 800px;
            border-radius: 8px;
            position: relative;
            box-sizing: border-box;
        }

        .custom-modal-close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            position: absolute;
            top: 10px;
            right: 15px;
            cursor: pointer;
        }

        .custom-modal-close:hover,
        .custom-modal-close:focus {
            color: black;
            text-decoration: none;
        }

        /* Responsive form elements & General Mobile Styles */
        @media (max-width: 768px) {
            /* Modal adjustments */
            .custom-modal-content {
                margin: 10% auto;
                width: 95%;
                padding: 15px;
            }
            
            /* Form elements */
            .form-check, .form-switch {
                margin-bottom: 0.5rem;
            }
            fieldset.row {
                margin-bottom: 1rem;
            }
            .col-form-label {
                padding-bottom: 0.5rem;
            }
            .col-sm-10 { /* Typically for form input container */
                width: 100%;
            }
            .form-select, .form-control {
                width: 100%;
            }
            .card-footer { /* General card footer, might affect modals too */
                text-align: center;
            }

            /* Table & general page font adjustments */
            body {
                font-size: 14px;
            }
            .table {
                font-size: 13px;
            }
            .card-body { /* General card body padding */
                padding: 0.5rem;
            }
            .dataTables_wrapper .dataTables_info,
            .dataTables_wrapper .dataTables_filter input {
                font-size: 13px;
            }
            .dataTables_wrapper .dataTables_length select {
                padding: 0.2rem 0.5rem;
            }
            
            /* === GENERAL BUTTON STYLING ON SMALL SCREENS === */
            /* This rule makes buttons smaller (padding/font) and by default full-width */
            .btn {
                width: 100%; /* Default to full-width, overridden below for specific buttons */
                margin-bottom: 10px;
                padding: 0.25rem 0.5rem;
                font-size: 0.875rem;
                line-height: 1.5;
            }

            /* === REVISED: OVERRIDES FOR SPECIFIC BUTTONS TO KEEP THEIR ORIGINAL SMALL SIZE === */
            /* For "Tambah Data" button */
            .app-content-header .text-start > .btn,
            .app-content-header .text-start > a.btn { /* Targets <a> tag if it's used as a button */
                width: auto; /* Make it fit its content, not full-width */
                /* margin-bottom: 10px; /* Inherits from general .btn rule or can be adjusted */
            }

            /* For "Edit" and "Hapus" buttons in the table */
            #dataSiswa td .btn {
                width: auto; /* Make them fit their content */
                display: inline-block; /* Ensure they can be side-by-side */
                margin-right: 5px; /* Space between buttons in the same cell */
                margin-bottom: 5px; /* Adjusted margin if they stack in very narrow cells */
            }
            /* Remove right margin from the last button in a table cell to prevent extra space */
            #dataSiswa td .btn:last-child {
                margin-right: 0;
            }
            /* Optional: Prevent buttons in table cells from wrapping too aggressively */
            #dataSiswa td {
                white-space: nowrap;
            }
            /* === END OF REVISED BUTTON STYLES === */
        }

        @media (max-width: 576px) {
            .custom-modal-content {
                margin: 15% auto;
                width: 98%;
                padding: 10px;
            }
            
            .mb-3 { /* Bootstrap margin bottom class */
                margin-bottom: 0.75rem !important;
            }
            
            .form-label {
                font-size: 0.9rem;
            }
            
            .form-control, .form-select {
                font-size: 0.9rem;
                padding: 0.375rem 0.75rem;
            }
            
            h3 { /* Page title heading */
                font-size: 1.25rem;
            }
        }

        /* Table controls (DataTables specific) */
        .top-container {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            margin-bottom: 15px;
            margin-top: 15px;
            width: 100%;
        }

        .bottom-container {
            margin-top: 15px;
            width: 100%;
        }
    </style>
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">
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
                            <span class="d-none d-md-inline"><?php echo htmlspecialchars($_SESSION['nama']); ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                            <li class="user-header text-bg-primary">
                                <img src="dist/assets/img/user.png" class="rounded-circle shadow" alt="User Image" />
                                <p>
                                    <?php echo htmlspecialchars($_SESSION['nama']); ?>
                                    <small><?php echo htmlspecialchars($_SESSION['role']); ?></small>
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
                        <div class="col-sm-6"><h3 class="mb-0">Data Siswa</h3></div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Data Siswa</li>
                            </ol>
                        </div>
                        <div class="col-12 mt-2 text-start"> <a onclick="openModalTambah()" class="btn btn-success btn-sm">Tambah Data</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="app-content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <?php display_session_alert(); ?>
                            <div class="card mb-4">
                                <div class="card-body p-0 table-responsive">
                                    <table id="dataSiswa" class="display nowrap" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>NISN</th>
                                                <th>Nama</th>
                                                <th>Jenis Kelamin</th>
                                                <th>Jurusan</th>
                                                <th>Kelas</th>
                                                <th>Alamat</th>
                                                <th>Agama</th>
                                                <th>No HP</th>
                                                <th>Option</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $no = 1;
                                        $jurusanMap = [
                                            '1' => 'Akutansi',
                                            '2' => 'Perkantoran',
                                            '3' => 'Pariwisata',
                                            '4' => 'DKV',
                                            '5' => 'RPL',
                                            '6' => 'Broadcasting'
                                        ];
                                        $agamaMap = [
                                            '1' => 'Islam',
                                            '2' => 'Kristen',
                                            '3' => 'Katholik'
                                            // Lengkapi dengan agama lain jika ada di form
                                        ];

                                        foreach($db->tampil_data_siswa() as $x){
                                        ?>
                                            <tr>
                                                <td><?php echo $no++; ?></td>
                                                <td><?php echo htmlspecialchars($x['nisn']); ?></td>
                                                <td><?php echo htmlspecialchars($x['nama']); ?></td>
                                                <td><?php echo htmlspecialchars($x['jeniskelamin']); ?></td>
                                                <td><?php echo htmlspecialchars($jurusanMap[$x['kodejurusan']] ?? $x['kodejurusan']); ?></td>
                                                <td><?php echo htmlspecialchars($x['kelas']); ?></td>
                                                <td><?php echo htmlspecialchars($x['alamat']); ?></td>
                                                <td><?php echo htmlspecialchars($agamaMap[$x['agama']] ?? $x['agama']); ?></td>
                                                <td><?php echo htmlspecialchars($x['nohp']); ?></td>
                                                <td>
                                                    <button type="button" onclick="openModalEdit(
                                                            '<?php echo htmlspecialchars($x['nisn'], ENT_QUOTES); ?>',
                                                            '<?php echo htmlspecialchars($x['nama'], ENT_QUOTES); ?>',
                                                            '<?php echo htmlspecialchars($x['jeniskelamin'], ENT_QUOTES); ?>',
                                                            '<?php echo htmlspecialchars($x['kodejurusan'], ENT_QUOTES); ?>',
                                                            '<?php echo htmlspecialchars($x['kelas'], ENT_QUOTES); ?>',
                                                            '<?php echo htmlspecialchars($x['alamat'], ENT_QUOTES); ?>',
                                                            '<?php echo htmlspecialchars($x['agama'], ENT_QUOTES); ?>',
                                                            '<?php echo htmlspecialchars($x['nohp'], ENT_QUOTES); ?>')"
                                                        class="btn btn-primary btn-sm"><i class="bi bi-pencil-square"></i> Edit</button>
                                                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#hapusModal" data-id="<?php echo htmlspecialchars($x['nisn']); ?>"><i class="bi bi-trash"></i> Hapus</button>
                                                </td>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        
        <footer class="app-footer">
            <div class="float-end d-none d-sm-inline">Anything you want</div>
            <strong>
                Copyright &copy; 2014-2024&nbsp;
                <a href="https://adminlte.io" class="text-decoration-none">AdminLTE.io</a>.
            </strong>
            All rights reserved.
        </footer>
    </div>

    <div id="modalTambahSiswa" class="custom-modal">
        <div class="custom-modal-content">
            <span onclick="closeModal('modalTambahSiswa')" class="custom-modal-close">&times;</span>
            <h3>Form Tambah Data Siswa</h3>
            <form action="datasiswa.php" method="post">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nisn" class="form-label">NISN</label>
                            <input type="text" class="form-control" id="nisn" name="nisn" required/>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="nama" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="nama" name="nama" required/>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenis Kelamin</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="jeniskelamin" id="gridRadios1" value="L" checked>
                                    <label class="form-check-label" for="gridRadios1">Laki-laki</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="jeniskelamin" id="gridRadios2" value="P">
                                    <label class="form-check-label" for="gridRadios2">Perempuan</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
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
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="kelas" class="form-label">Kelas</label>
                            <select class="form-select" id="kelas" name="kelas" required>
                                <option value="">Pilih Kelas</option>
                                <option value="X">X</option>
                                <option value="XI">XI</option>
                                <option value="XII">XII</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
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
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <input type="text" class="form-control" id="alamat" name="alamat" required/>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="nohp" class="form-label">Nomor Handphone</label>
                            <input type="text" class="form-control" id="nohp" name="nohp" required/>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <input type="submit" class="btn btn-primary" name="simpan" value="Simpan">
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="modalEditSiswa" class="custom-modal">
        <div class="custom-modal-content">
            <span onclick="closeModal('modalEditSiswa')" class="custom-modal-close">&times;</span>
            <h3>Form Edit Data Siswa</h3>
            <form action="datasiswa.php" method="post">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_nisn" class="form-label">NISN</label>
                            <input type="text" class="form-control" id="edit_nisn" name="nisn" value="" readonly/>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_nama" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="edit_nama" name="nama" value="" required/>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenis Kelamin</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="jeniskelamin" id="edit_jeniskelamin_l" value="L">
                                    <label class="form-check-label" for="edit_jeniskelamin_l">Laki-laki</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="jeniskelamin" id="edit_jeniskelamin_p" value="P">
                                    <label class="form-check-label" for="edit_jeniskelamin_p">Perempuan</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="edit_kodejurusan" class="form-label">Jurusan</label>
                            <select class="form-select" id="edit_kodejurusan" name="kodejurusan" required>
                                <option value="">Pilih Jurusan</option>
                                <option value="1">Akutansi</option>
                                <option value="2">Perkantoran</option>
                                <option value="3">Pariwisata</option>
                                <option value="4">DKV</option>
                                <option value="5">RPL</option>
                                <option value="6">Broadcasting</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_kelas" class="form-label">Kelas</label>
                            <select class="form-select" id="edit_kelas" name="kelas" required>
                                <option value="">Pilih Kelas</option>
                                <option value="X">X</option>
                                <option value="XI">XI</option>
                                <option value="XII">XII</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="edit_agama" class="form-label">Agama</label>
                            <select class="form-select" id="edit_agama" name="agama" required>
                                <option value="">Pilih Agama</option>
                                <option value="1">Islam</option>
                                <option value="2">Kristen</option>
                                <option value="3">Katholik</option>
                                <option value="4">Hindu</option>
                                <option value="5">Buddha</option>
                                <option value="6">Konghucu</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_alamat" class="form-label">Alamat</label>
                            <input type="text" class="form-control" id="edit_alamat" name="alamat" value="" required/>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="edit_nohp" class="form-label">Nomor Handphone</label>
                            <input type="text" class="form-control" id="edit_nohp" name="nohp" value="" required/>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <input type="submit" class="btn btn-primary" name="update" value="Simpan Perubahan">
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="hapusModal" tabindex="-1" aria-labelledby="hapusModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="hapusModalLabel">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus data ini?
                </div>
                <div class="modal-footer">
                    <form id="formHapus" method="GET" action="datasiswa.php">
                        <input type="hidden" name="hapus" id="hapusId">
                        <button type="submit" class="btn btn-danger"><i class="bi bi-trash"></i> Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Inisialisasi DataTable
        $(document).ready(function() {
            $('#dataSiswa').DataTable({
                responsive: true,
                scrollX: true,
                scrollCollapse: true,
                paging: true,
                searching: true,
                ordering: true,
                info: true,
                lengthChange: true,
                pageLength: 10,
                // dom: '<"top-container"<"top-left"l><"top-right"f>>rt<"bottom-container"ip><"clear">'
                // Penggunaan dom default Bootstrap 5 lebih baik jika menggunakan AdminLTE/Bootstrap
            });
        });

        // Fungsi untuk menyesuaikan ukuran modal custom
        function adjustModalSize(modalElement) {
            // const modal = document.querySelector(`#${modalId} .custom-modal-content`);
            if (!modalElement) return;
            const windowHeight = window.innerHeight;
            
            if (modalElement.offsetHeight > windowHeight * 0.8) {
                modalElement.style.maxHeight = (windowHeight * 0.8) + 'px';
                modalElement.style.overflowY = 'auto';
            } else {
                modalElement.style.maxHeight = 'none';
                modalElement.style.overflowY = 'visible';
            }
        }

        // Fungsi untuk membuka modal custom
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = 'block';
                adjustModalSize(modal.querySelector('.custom-modal-content'));
            }
        }
        
        // Fungsi untuk menutup modal custom
        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = 'none';
            }
        }

        // Fungsi untuk membuka modal tambah
        function openModalTambah() {
            openModal('modalTambahSiswa');
        }

        // Fungsi untuk membuka modal edit
        function openModalEdit(nisn, nama, jenisKelamin, kodeJurusan, kelas, alamat, agama, nohp) {
            // Set nilai form
            document.getElementById('edit_nisn').value = nisn;
            document.getElementById('edit_nama').value = nama;

            // Set radio button jenis kelamin
            if (jenisKelamin === 'L') {
                document.getElementById('edit_jeniskelamin_l').checked = true;
            } else if (jenisKelamin === 'P') {
                document.getElementById('edit_jeniskelamin_p').checked = true;
            } else { // Clear selection if no match
                 document.getElementById('edit_jeniskelamin_l').checked = false;
                 document.getElementById('edit_jeniskelamin_p').checked = false;
            }

            document.getElementById('edit_kodejurusan').value = kodeJurusan;
            document.getElementById('edit_kelas').value = kelas;
            document.getElementById('edit_alamat').value = alamat;
            document.getElementById('edit_agama').value = agama;
            document.getElementById('edit_nohp').value = nohp;
            
            openModal('modalEditSiswa');
        }
        
        // Event listener untuk modal hapus Bootstrap
        var hapusModal = document.getElementById('hapusModal');
        if (hapusModal) {
            hapusModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var id = button.getAttribute('data-id');
                var inputHapusId = hapusModal.querySelector('#hapusId');
                inputHapusId.value = id;
            });
        }

        // Menyesuaikan ukuran modal custom saat window diresize
        window.addEventListener('resize', function() {
            const modals = document.querySelectorAll('.custom-modal');
            modals.forEach(modal => {
                if (modal.style.display === 'block') {
                    adjustModalSize(modal.querySelector('.custom-modal-content'));
                }
            });
        });

    </script>
    <script src="dist/js/adminlte.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/browser/overlayscrollbars.browser.es6.min.js" integrity="sha256-Kq8Dk38MpgzHkFKddmU3L0x872qI5WJwd62HqY1PwbE=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>