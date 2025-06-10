<?php
session_start();
include "koneksi.php"; // Pastikan file ini berisi class 'database' Anda
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
        // Hapus notifikasi dari session setelah ditampilkan
        unset($_SESSION['notif_status']);
        unset($_SESSION['notif_message']);
    }
}

// Proses Hapus Data User
if (isset($_GET['hapus'])) {
    $username_to_delete = $_GET['hapus'];

    if ($db->hapus_data_user($username_to_delete)) { // Pastikan method ini ada di class database Anda
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
    header("location:datauser.php"); // Redirect setelah proses selesai
    exit;
}

// Proses Tambah Data User
if (isset($_POST['simpan'])) {
    if ($db->tambah_user( // Pastikan method ini ada di class database Anda
        $_POST['username'],
        $_POST['password'], // Kirim password mentah atau yang sudah di-hash
        $_POST['nama'],
        $_POST['role']
    )) {
        $_SESSION['notif_status'] = 'success';
        $_SESSION['notif_message'] = 'Data berhasil ditambahkan!';
    } else {
        $_SESSION['notif_status'] = 'danger';
        $_SESSION['notif_message'] = 'Gagal menambah data pengguna!';
    }
    header("location:datauser.php"); // Redirect setelah proses selesai
    exit;
}

// Proses Update Data User
if (isset($_POST['update'])) {
    $password_to_update = null;
    if (isset($_POST['password']) && !empty(trim($_POST['password']))) {
        $password_to_update = trim($_POST['password']);
    }
    if ($db->edit_data_user( // Pastikan method ini ada di class database Anda
        $_POST['id_user'],
        $_POST['username'],
        $password_to_update, // Kirim password yang mungkin NULL
        $_POST['nama'],
        $_POST['role']
    )) {
        $_SESSION['notif_status'] = 'success';
        $_SESSION['notif_message'] = 'Data berhasil diperbarui!';
    } else {
        $_SESSION['notif_status'] = 'danger';
        $_SESSION['notif_message'] = 'Gagal memperbarui data!';
    }
    header("location:datauser.php"); // Redirect setelah proses selesai
    exit;
}

?>

<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Data User | Sekolah</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="title" content="Data User" />
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
            #dataUser td .btn {
                width: auto; /* Make them fit their content */
                display: inline-block; /* Ensure they can be side-by-side */
                margin-right: 5px; /* Space between buttons in the same cell */
                margin-bottom: 5px; /* Adjusted margin if they stack in very narrow cells */
            }
            /* Remove right margin from the last button in a table cell to prevent extra space */
            #dataUser td .btn:last-child {
                margin-right: 0;
            }
            /* Optional: Prevent buttons in table cells from wrapping too aggressively */
            #dataUser td {
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
                            <img
                                src="dist/assets/img/user.png" class="user-image rounded-circle shadow"
                                alt="User Image"
                            />
                            <span class="d-none d-md-inline"><?php echo htmlspecialchars($_SESSION['nama'], ENT_QUOTES, 'UTF-8'); ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                            <li class="user-header text-bg-primary">
                                <img
                                    src="dist/assets/img/user.png" class="rounded-circle shadow"
                                    alt="User Image"
                                />
                                <p>
                                    <?php echo htmlspecialchars($_SESSION['nama'], ENT_QUOTES, 'UTF-8'); ?>
                                    <small><?php echo htmlspecialchars($_SESSION['role'], ENT_QUOTES, 'UTF-8'); ?></small>
                                </p>
                            </li>
                            <li class="user-footer">
                                <a href="profile.php" class="btn btn-default btn-flat">Profile</a>
                                <a href="../logout.php" class="btn btn-default btn-flat float-end">Sign out</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
        <?php include "sidebar.php"; // Pastikan file ini ada dan path-nya benar ?>
        <main class="app-main">
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6"><h3 class="mb-0">Data User</h3></div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Data User</li>
                            </ol>
                        </div>
                        <div class="col-12 text-start mt-3">
                            <button type="button" onclick="openModalTambah()" class="btn btn-success btn-sm">Tambah Data</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="app-content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <?php display_session_alert(); // Tampilkan notifikasi di sini ?>
                            <div class="card mb-4">
                                <div class="card-body p-0 table-responsif">
                                    <table id="dataUser" class="display nowrap" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>ID User</th>
                                                <th>Nama</th>
                                                <th>Username</th>
                                                <th>Password</th>
                                                <th>Role</th>
                                                <th>Option</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        // Pastikan method tampil_data_user() ada di class database Anda
                                        $data_user = $db->tampil_data_user();
                                        if (!empty($data_user) && is_array($data_user)) {
                                            foreach ($data_user as $x) {
                                        ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($x['id_user'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                <td><?php echo htmlspecialchars($x['nama'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                <td><?php echo htmlspecialchars($x['username'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                <td><?php echo htmlspecialchars($x['password'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                <td><?php echo htmlspecialchars($x['role'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                <td>
                                                    <button type="button" class="btn btn-primary btn-sm btn-edit-user"
                                                            data-id_user="<?php echo htmlspecialchars($x['id_user'], ENT_QUOTES, 'UTF-8'); ?>"
                                                            data-username="<?php echo htmlspecialchars($x['username'], ENT_QUOTES, 'UTF-8'); ?>"
                                                            data-nama="<?php echo htmlspecialchars($x['nama'], ENT_QUOTES, 'UTF-8'); ?>"
                                                            data-role="<?php echo htmlspecialchars($x['role'], ENT_QUOTES, 'UTF-8'); ?>">
                                                        <i class="bi bi-pencil-square"></i> Edit
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#hapusModal" data-username-hapus="<?php echo htmlspecialchars($x['username'], ENT_QUOTES, 'UTF-8'); ?>">
                                                        <i class="bi bi-trash"></i> Hapus
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php
                                            } // Akhir foreach
                                        } else {
                                            echo '<tr><td colspan="5" class="text-center">Tidak ada data pengguna.</td></tr>';
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

    <div id="modalTambahUser" class="custom-modal">
        <div class="custom-modal-content">
            <span onclick="closeModal('modalTambahUser')" class="custom-modal-close" title="Tutup">&times;</span>
            <h3>Form Tambah Data User</h3>
            <form action="datauser.php" method="post" id="formTambahUser">
                <div class="mb-3">
                    <label for="tambah_nama" class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" id="tambah_nama" name="nama" required/>
                </div>
                <div class="mb-3">
                    <label for="tambah_username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="tambah_username" name="username" required/>
                </div>
                <div class="mb-3">
                    <label for="tambah_password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="tambah_password" name="password" required/>
                </div>
                <div class="mb-3">
                    <label for="tambah_role" class="form-label">Role</label>
                    <select class="form-select" id="tambah_role" name="role" required>
                        <option value="">Pilih Role</option>
                        <option value="Admin">Admin</option>
                        <option value="Guru">Guru</option>
                        <option value="Siswa">Siswa</option>
                    </select>
                </div>
                <div class="text-end mt-3">
                    <button type="submit" class="btn btn-primary" name="simpan">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div id="modalEditUser" class="custom-modal">
        <div class="custom-modal-content">
            <span onclick="closeModal('modalEditUser')" class="custom-modal-close" title="Tutup">&times;</span>
            <h3>Form Edit Data User</h3>
            <form action="datauser.php" method="post" id="formEditUser">
                <input type="hidden" id="edit_id_user" name="id_user" />
                <div class="mb-3">
                    <label for="edit_nama" class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" id="edit_nama" name="nama" required/>
                </div>
                <div class="mb-3">
                    <label for="edit_username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="edit_username" name="username" required/>
                </div>
                <div class="mb-3">
                    <label for="edit_password" class="form-label">Password Baru</label>
                    <input type="password" class="form-control" id="edit_password" name="password" placeholder="Biarkan kosong jika tidak ingin mengubah"/>
                    <small class="form-text text-muted">Isi hanya jika ingin mengubah password.</small>
                </div>
                <div class="mb-3">
                    <label for="edit_role" class="form-label">Role</label>
                    <select class="form-select" id="edit_role" name="role" required>
                        <option value="">Pilih Role</option>
                        <option value="Admin">Admin</option>
                        <option value="Guru">Guru</option>
                        <option value="Siswa">Siswa</option>
                    </select>
                </div>
                <div class="text-end mt-3">
                    <button type="submit" class="btn btn-primary" name="update">Simpan Perubahan</button>
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
                    Apakah Anda yakin ingin menghapus pengguna dengan username : <strong id="hapusUsernameText"></strong>? Tindakan ini tidak dapat dibatalkan.
                </div>
                <div class="modal-footer">
                    <form id="formHapus" method="GET" action="datauser.php">
                        <input type="hidden" name="hapus" id="hapusUsername">
                        <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
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
    <script src="dist/js/adminlte.js"></script> <script>
        // Inisialisasi OverlayScrollbars untuk sidebar jika ada
        document.addEventListener('DOMContentLoaded', function () {
            const SELECTOR_SIDEBAR_WRAPPER = '.sidebar-wrapper';
            const sidebarWrapper = document.querySelector(SELECTOR_SIDEBAR_WRAPPER);
            if (sidebarWrapper && typeof OverlayScrollbarsGlobal?.OverlayScrollbars !== 'undefined') {
                OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
                    scrollbars: {
                        theme: 'os-theme-light', // atau 'os-theme-dark'
                        autoHide: 'leave',
                        clickScroll: true,
                    },
                });
            }
        });
        // Fungsi untuk menutup modal custom
        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = 'none';
            }
        }

        // Fungsi untuk membuka modal tambah
        function openModalTambah() {
            closeModal('modalEditUser'); // Sembunyikan modal edit jika terbuka
            const modalTambah = document.getElementById('modalTambahUser');
            const formTambah = document.getElementById('formTambahUser');
            if (modalTambah && formTambah) {
                formTambah.reset(); // Reset form tambah
                modalTambah.style.display = 'block';
            }
        }

        // Fungsi untuk membuka modal edit dan mengisi datanya
        function openModalEditWithData(id_user_param, username_param, nama_param, role_param) {
            closeModal('modalTambahUser'); // Sembunyikan modal tambah jika terbuka

            const modalEdit = document.getElementById('modalEditUser');
            if (modalEdit) {
                document.getElementById('edit_id_user').value = id_user_param;
                document.getElementById('edit_username').value = username_param;
                document.getElementById('edit_password').value = ''; // Kosongkan password field
                document.getElementById('edit_nama').value = nama_param;
                document.getElementById('edit_role').value = role_param;
                modalEdit.style.display = 'block';
            }
        }
        // Event listener untuk tombol Edit
        $('#dataUser').on('click', '.btn-edit-user', function() {
            var id_user = $(this).data('id_user');
            var username = $(this).data('username');
            var nama = $(this).data('nama');
            var role = $(this).data('role');
            openModalEditWithData(id_user, username, nama, role);
        });

        // Untuk Modal Hapus Bootstrap
        var hapusModalEl = document.getElementById('hapusModal');
        if (hapusModalEl) {
            hapusModalEl.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var username = button.getAttribute('data-username-hapus');
                hapusModalEl.querySelector('#hapusUsername').value = username;
                hapusModalEl.querySelector('#hapusUsernameText').textContent = username;
            });
        }
        $(document).ready(function() {
            $('#dataUser').DataTable({
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
</body>
</html>