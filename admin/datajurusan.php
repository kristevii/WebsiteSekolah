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
        // Hapus notifikasi dari session setelah ditampilkan
        unset($_SESSION['notif_status']);
        unset($_SESSION['notif_message']);
    }
}

// Proses Hapus Data User
if (isset($_GET['hapus'])) {
    $jurusan_to_delete = $_GET['hapus'];

    if ($db->hapus_data_jurusan($jurusan_to_delete)) { // Pastikan method ini ada di class database Anda
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
    header("location:datajurusan.php"); // Redirect setelah proses selesai
    exit;
}

// Proses Tambah Data User
if (isset($_POST['simpan'])) {
    if ($db->tambah_jurusan( // Pastikan method ini ada di class database Anda
        $_POST['namajurusan']
    )) {
        $_SESSION['notif_status'] = 'success';
        $_SESSION['notif_message'] = 'Data berhasil ditambahkan!';
    } else {
        $_SESSION['notif_status'] = 'danger';
        $_SESSION['notif_message'] = 'Gagal menambah data jurusan!';
    }
    header("location:datajurusan.php"); // Redirect setelah proses selesai
    exit;
}

// Proses Update Data User
if (isset($_POST['update'])) {
    if ($db->edit_data_jurusan( // Pastikan method ini ada di class database Anda
        $_POST['kodejurusan'],
        $_POST['namajurusan']
    )) {
        $_SESSION['notif_status'] = 'success';
        $_SESSION['notif_message'] = 'Data berhasil diperbarui!';
    } else {
        $_SESSION['notif_status'] = 'danger';
        $_SESSION['notif_message'] = 'Gagal memperbarui data!';
    }
    header("location:datajurusan.php"); // Redirect setelah proses selesai
    exit;
}
?>

<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Data Jurusan | Sekolah</title>
    <!--begin::Primary Meta Tags-->
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
    <!--end::Primary Meta Tags-->
    <!--begin::Fonts-->
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
    <link rel="stylesheet" href="dist/css/adminlte.css" />
    <!--end::Required Plugin(AdminLTE)-->

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
            #dataJurusan td .btn {
                width: auto; /* Make them fit their content */
                display: inline-block; /* Ensure they can be side-by-side */
                margin-right: 5px; /* Space between buttons in the same cell */
                margin-bottom: 5px; /* Adjusted margin if they stack in very narrow cells */
            }
            /* Remove right margin from the last button in a table cell to prevent extra space */
            #dataJurusan td .btn:last-child {
                margin-right: 0;
            }
            /* Optional: Prevent buttons in table cells from wrapping too aggressively */
            #dataJurusan td {
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
  <!--end::Head-->
  <!--begin::Body-->
  <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
      <!--begin::Header-->
      <nav class="app-header navbar navbar-expand bg-body sticky-top">
        <!--begin::Container-->
        <div class="container-fluid">
          <!--begin::Start Navbar Links-->
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                <i class="bi bi-list"></i>
              </a>
            </li>
          </ul>
          <!--end::Start Navbar Links-->
          <!--begin::End Navbar Links-->
          <ul class="navbar-nav ms-auto">
            <!--begin::Fullscreen Toggle-->
            <li class="nav-item">
              <a class="nav-link" href="#" data-lte-toggle="fullscreen">
                <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i>
                <i data-lte-icon="minimize" class="bi bi-fullscreen-exit" style="display: none"></i>
              </a>
            </li>
            <!--end::Fullscreen Toggle-->
            <!--begin::User Menu Dropdown-->
            <li class="nav-item dropdown user-menu">
              <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                <img
                  src="dist/assets/img/user.png"
                  class="user-image rounded-circle shadow"
                  alt="User Image"
                />
                <span class="d-none d-md-inline"><?php echo $_SESSION['nama']; ?></span>
              </a>
              <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                <!--begin::User Image-->
                <li class="user-header text-bg-primary">
                  <img
                    src="dist/assets/img/user.png"
                    class="rounded-circle shadow"
                    alt="User Image"
                  />
                  <p>
                    <?php echo $_SESSION['nama']; ?>
                    <small><?php echo $_SESSION['role']; ?></small>
                  </p>
                </li>
                <!--end::User Image-->
                <!--begin::Menu Footer-->
                <li class="user-footer">
                  <a href="profile.php" class="btn btn-default btn-flat">Profile</a>
                  <a href="../logout.php" class="btn btn-default btn-flat float-end">Sign out</a>
                </li>
                <!--end::Menu Footer-->
              </ul>
            </li>
            <!--end::User Menu Dropdown-->
          </ul>
          <!--end::End Navbar Links-->
        </div>
        <!--end::Container-->
      </nav>
      <!--end::Header-->
      <?php include "sidebar.php"; ?>
      <!--begin::App Main-->
      <main class="app-main">
        <!--begin::App Content Header-->
        <div class="app-content-header">
          <!--begin::Container-->
          <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
              <div class="col-sm-6"><h3 class="mb-0">Data Jurusan</h3></div>
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                  <li class="breadcrumb-item"><a href="#">Home</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Data Jurusan</li>
                </ol>
              </div>
              <div class="text-start">
                <a onclick="openModal()" class="btn btn-success btn-sm">Tambah Data</a>
              </div>
            </div>
            <!--end::Row-->
          </div>
          <!--end::Container-->
        </div>
        <!--end::App Content Header-->
        <!--begin::App Content-->
        <div class="app-content">
          <!--begin::Container-->
          <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
              <!-- /.col -->
              <div class="col-md-12">
                <?php display_session_alert(); // Tampilkan notifikasi di sini ?>
                <!-- /.card -->
                <div class="card mb-4">
                  <!-- /.card-header -->
                  <div class="card-body p-0 table-responsif">
                    <table id="dataJurusan" class="display nowrap" style="width:100%">
                      <thead>
                        <tr>
                          <th>Kode Jurusan</th>
                          <th>Nama Jurusan</th>
                          <th>Option</th>
                        </tr>
                      </thead>
                      <tbody>
                      <?php
                      $no = 1;
                      foreach($db-> tampil_data_jurusan() as $x){
                      ?>
                      <tr>
                          <td><?php echo $x['kodejurusan'] ?></td>
                          <td><?php echo $x['namajurusan'] ?></td>
                          <td>
                            <button type="button" onclick="openModalEdit('<?php echo htmlspecialchars($x['kodejurusan']); ?>', '<?php echo htmlspecialchars($x['namajurusan']); ?>')" class="btn btn-primary btn-sm"><i class="bi bi-pencil-square"></i> Edit</button>
                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#hapusModal" data-id="<?php echo htmlspecialchars($x['kodejurusan']); ?>"><i class="bi bi-trash"></i> Hapus</button>
                          </td>    
                      </tr>
                      <?php
                      }
                      ?>
                      </tbody>
                    </table>
                  </div>
                  <!-- /.card-body -->
                </div>
                <!-- /.card -->
              </div>
              <!-- /.col -->
            </div>
            <!--end::Row-->
          </div>
          <!--end::Container-->
        </div>
        <!--end::App Content-->
      </main>
      <!--end::App Main-->
      <!--begin::Footer-->
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
      <!--end::Footer-->
    </div>
    <div id="modalTambahJurusan" class="custom-modal">
      <div class="custom-modal-content">
        <span onclick="closeModal('modalTambahJurusan')" class="custom-modal-close">&times;</span>
        <h3>Form Tambah Data Jurusan</h3>
        <form action="datajurusan.php" method="post"> 
          <div class="card-body">
            <div class="mb-3">
              <label for="tambah_namajurusan" class="form-label">Nama Jurusan</label> 
              <input type="text" class="form-control" id="tambah_namajurusan" name="namajurusan" required/>
            </div>
          </div>
          <div class="card-footer">
            <button type="submit" class="btn btn-primary" name="simpan">Simpan</button> 
          </div>
        </form>
      </div>
    </div>
    <div id="modalEditJurusan" class="custom-modal">
      <div class="custom-modal-content">
        <span onclick="closeModal('modalEditJurusan')" class="custom-modal-close">&times;</span>
        <h3>Form Edit Data Jurusan</h3>
        <form action="datajurusan.php" method="post"> <div class="card-body">
            <input type="hidden" id="edit_kodejurusan" name="kodejurusan" value=""> <div class="mb-3">
              <label for="edit_namajurusan" class="form-label">Nama Jurusan</label> 
              <input type="text" class="form-control" id="edit_namajurusan" name="namajurusan" value="" required/>
            </div>
          </div>
          <div class="card-footer">
            <button type="submit" class="btn btn-primary" name="update">Simpan Perubahan</button> </div>
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
            <form id="formHapus" method="GET" action="datajurusan.php"> 
              <input type="hidden" name="hapus" id="hapusId">
              <button type="submit" class="btn btn-danger">Hapus</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!--begin::Script-->
    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <script
      src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/browser/overlayscrollbars.browser.es6.min.js"
      integrity="sha256-dghWARbRe2eLlIJ56wNB+b760ywulqK3DzZYEpsg2fQ="
      crossorigin="anonymous"
    ></script>
    <!--end::Third Party Plugin(OverlayScrollbars)--><!--begin::Required Plugin(popperjs for Bootstrap 5)-->
    <script
      src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
      integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
      crossorigin="anonymous"
    ></script>
    <!--end::Required Plugin(popperjs for Bootstrap 5)--><!--begin::Required Plugin(Bootstrap 5)-->
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
      integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy"
      crossorigin="anonymous"
    ></script>
    <!--end::Required Plugin(Bootstrap 5)--><!--begin::Required Plugin(AdminLTE)-->
    <script src="dist/js/adminlte.js"></script>
    <!--end::Required Plugin(AdminLTE)--><!--begin::OverlayScrollbars Configure-->
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

        // Inisialisasi DataTable (hanya satu kali)
        $('#dataJurusan').DataTable({
          responsive: true,
          scrollX: true,
          scrollCollapse: true,
          paging: true,
          searching: true,
          ordering: true,
          info: true,
          lengthChange: true,
          pageLength: 10,
          lengthMenu: [5, 10, 25, 50, 100],
          dom: '<"top-container"<"top-left"l><"top-right"f>>rt<"bottom-container"ip><"clear">'
        });
      });

      function openModal() {
        document.getElementById('modalTambahJurusan').style.display = 'block';
      }

      // Fungsi openModalEdit DENGAN PARAMETER
      function openModalEdit(kode_jurusan_param, nama_jurusan_param) {
        document.getElementById('modalEditJurusan').style.display = 'block';
        // Mengisi value ke input di dalam modal edit
        document.querySelector('#modalEditJurusan input[name="kodejurusan"]').value = kode_jurusan_param;
        document.querySelector('#modalEditJurusan input[name="namajurusan"]').value = nama_jurusan_param;
      }

      function closeModal(modalId) { // Terima ID modal yang akan ditutup
        if (document.getElementById(modalId)) {
            document.getElementById(modalId).style.display = 'none';
        }
      }

      // Event listener untuk Bootstrap modal konfirmasi hapus
      var hapusModal = document.getElementById('hapusModal');
      if (hapusModal) {
        hapusModal.addEventListener('show.bs.modal', function (event) {
          var button = event.relatedTarget; // Tombol yang memicu modal
          var id = button.getAttribute('data-id'); // Ambil id dari atribut data-id
          var inputId = hapusModal.querySelector('#hapusId'); // Input hidden di dalam form hapus
          inputId.value = id;
        });
      }
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
    <!--end::OverlayScrollbars Configure-->
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>