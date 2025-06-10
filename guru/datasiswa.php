<?php
session_start();
include "koneksi.php"; // Pastikan file koneksi.php sudah benar dan ada kelas database
$db = new database();

// Cek apakah user sudah login
if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit();
}

// Cek role user
if ($_SESSION['role'] != 'Guru') {
    header("Location: ../unauthorized.php");
    exit();
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
    <meta
        name="description"
        content="AdminLTE is a Free Bootstrap 5 Admin Dashboard, 30 example pages using Vanilla JS."
    />
    <meta
        name="keywords"
        content="bootstrap 5, bootstrap, bootstrap 5 admin dashboard, bootstrap 5 dashboard, bootstrap 5 charts, bootstrap 5 calendar, bootstrap 5 datepicker, bootstrap 5 tables, bootstrap 5 datatable, vanilla js datatable, colorlibhq, colorlibhq dashboard, colorlibhq admin dashboard"
    />
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
        integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q="
        crossorigin="anonymous"
    />
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/styles/overlayscrollbars.min.css"
        integrity="sha256-tZHrRjVqNSRyWg2wbppGnT833E/Ys0DHWGwT04GiqQg="
        crossorigin="anonymous"
    />
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        integrity="sha256-9kPW/n5nn53j4WMRYAxe9c1rCY96Oogo/MKSVdKzPmI="
        crossorigin="anonymous"
    />
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
    <script>
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
            dom: '<"top-container"<"top-left"l><"top-right"f>>rt<"bottom-container"ip><"clear">'
        });
    });
    </script>

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
                            <img
                                src="dist/assets/img/user.png"
                                class="user-image rounded-circle shadow"
                                alt="User Image"
                            />
                            <span class="d-none d-md-inline"><?php echo htmlspecialchars($_SESSION['nama']); ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                            <li class="user-header text-bg-primary">
                                <img
                                    src="dist/assets/img/user.png"
                                    class="rounded-circle shadow"
                                    alt="User Image"
                                />
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
                    </div>
                    </div>
                </div>
            <div class="app-content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card mb-4">
                                <div class="card-body p-0 table-responsif">
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
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $no = 1;
                                        // Array mapping untuk menampilkan nama Jurusan dan Agama
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
    <script>
        $(document).ready(function () {
            if (!$.fn.DataTable.isDataTable('#dataSiswa')) {
                $('#dataSiswa').DataTable({
                    "paging": true,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "lengthMenu": [5, 10, 25, 50, 100]
                });
            }
        });
    </script>
    <script
        src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/browser/overlayscrollbars.browser.es6.min.js"
        integrity="sha256-dghWARbRe2eLlIJ56wNB+b760ywulqK3DzZYEpsg2fQ="
        crossorigin="anonymous"
    ></script>
    <script
        src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
        crossorigin="anonymous"
    ></script>
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
        integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy"
        crossorigin="anonymous"
    ></script>
    <script src="dist/js/adminlte.js"></script>
    <script>
        const SELECTOR_SIDEBAR_WRAPPER = '.sidebar-wrapper';
        const Default = {
            scrollbarTheme: 'os-theme-light',
            scrollbarAutoHide: 'leave',
            scrollbarClickScroll: true,
        };
        document.addEventListener('DOMContentLoaded', function () {
            const sidebarWrapper = document.querySelector(SELECTOR_SIDEBAR_WRAPPER);
            if (sidebarWrapper && typeof OverlayScrollbarsGlobal?.OverlayScrollbars !== 'undefined') {
                OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
                    scrollbars: {
                        theme: Default.scrollbarTheme,
                        autoHide: Default.scrollbarAutoHide,
                        clickScroll: Default.scrollbarClickScroll,
                    },
                });
            }
        });
    </script>
</body>
</html>