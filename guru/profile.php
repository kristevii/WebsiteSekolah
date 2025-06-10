<?php
session_start();
include "koneksi.php";
$db = new database();

// Authentication check
if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SESSION['role'] != 'Guru') {
    header("Location: ../unauthorized.php");
    exit();
}

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

// Proses Update Data User
if (isset($_POST['update'])) {
    $password_to_update = null;
    if (isset($_POST['password']) && !empty(trim($_POST['password']))) {
        $password_to_update = trim($_POST['password']);
    }
    if ($db->edit_data_user(
        $_POST['id_user'],
        $_POST['username'],
        $password_to_update,
        $_SESSION['nama'], // Keep the original name from session
        $_SESSION['role']  // Keep the original role from session
    )) {
        $_SESSION['notif_status'] = 'success';
        $_SESSION['notif_message'] = 'Data berhasil diperbarui!';
        $_SESSION['username'] = $_POST['username'];
    } else {
        $_SESSION['notif_status'] = 'danger';
        $_SESSION['notif_message'] = 'Gagal memperbarui data!';
    }
    header("location:profile.php");
    exit;
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Profile | Sekolah</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="title" content="Profile | Sekolah" />
    <meta name="author" content="ColorlibHQ" />
    <meta name="description" content="AdminLTE is a Free Bootstrap 5 Admin Dashboard" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css" integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q=" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/styles/overlayscrollbars.min.css" integrity="sha256-tZHrRjVqNSRyWg2wbppGnT833E/Ys0DHWGwT04GiqQg=" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" integrity="sha256-9kPW/n5nn53j4WMRYAxe9c1rCY96Oogo/MKSVdKzPmI=" crossorigin="anonymous" />
    <link rel="stylesheet" href="dist/css/adminlte.css" />
    <style>
        :root {
            --primary-color: #4a6bff;
            --secondary-color: #6c757d;
            --dark-color: #343a40;
            --light-color: #f8f9fa;
            --danger-color: #dc3545;
            --success-color: #28a745;
        }
        
        .profile-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .profile-header {
            text-align: center;
            margin-bottom: 30px;
            position: relative;
        }
        
        .profile-header h1 {
            color: var(--dark-color);
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .profile-header .breadcrumb {
            justify-content: center;
        }
        
        .profile-card {
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            margin-bottom: 30px;
            border: none;
        }
        
        .profile-card-header {
            background: linear-gradient(135deg, var(--primary-color), #6a5acd);
            color: white;
            padding: 20px;
            text-align: center;
            position: relative;
        }
        
        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 4px solid white;
            margin: 0 auto 15px;
            background-color: var(--light-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            color: var(--primary-color);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }
        
        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .profile-name {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .profile-role {
            font-size: 16px;
            opacity: 0.9;
            margin-bottom: 15px;
        }
        
        .profile-card-body {
            padding: 15px;
            background-color: white;
        }
        
        .profile-section {
            margin-bottom: 30px;
        }
        
        .profile-section-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
        }
        
        .profile-section-title i {
            margin-right: 10px;
        }
        
        .info-item {
            display: flex;
            margin-bottom: 15px;
        }
        
        .info-label {
            width: 150px;
            font-weight: 600;
            color: var(--secondary-color);
        }
        
        .info-value {
            flex: 1;
            color: var(--dark-color);
        }
        
        .action-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }
        
        .stats-card {
            border-radius: 10px;
            padding: 10px;
            margin-bottom: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            border-left: 4px solid var(--primary-color);
            background-color: white;
        }
        
        .stats-card .stats-title {
            font-size: 14px;
            color: var(--secondary-color);
            margin-bottom: 5px;
        }
        
        .stats-card .stats-value {
            font-size: 24px;
            font-weight: 600;
            color: var(--dark-color);
        }
        
        .activity-item {
            display: flex;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px dashed rgba(0, 0, 0, 0.1);
        }
        
        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: rgba(74, 107, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: var(--primary-color);
        }
        
        .activity-content {
            flex: 1;
        }
        
        .activity-time {
            font-size: 12px;
            color: var(--secondary-color);
        }

        /* Custom Modal Styles */
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
            max-width: 600px;
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

        .form-control:disabled, .form-select:disabled {
            background-color: #e9ecef;
            opacity: 1;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(74, 107, 255, 0.25);
        }
        
        @media (max-width: 768px) {
            .profile-card-header {
                padding: 20px;
            }
            
            .profile-avatar {
                width: 100px;
                height: 100px;
                font-size: 36px;
            }
            
            .info-item {
                flex-direction: column;
            }
            
            .info-label {
                width: 100%;
                margin-bottom: 5px;
            }
            
            .action-buttons {
                flex-direction: column;
                gap: 10px;
            }
            
            .action-buttons .btn {
                width: 100%;
            }

            .custom-modal-content {
                margin: 10% auto;
                width: 95%;
                padding: 15px;
            }
        }

        @media (max-width: 576px) {
            .custom-modal-content {
                margin: 15% auto;
                width: 98%;
                padding: 10px;
            }
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
                            <span class="d-none d-md-inline"><?php echo htmlspecialchars($_SESSION['nama'] ?? 'Pengguna'); ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                            <li class="user-header text-bg-primary">
                                <img src="dist/assets/img/user.png" class="rounded-circle shadow" alt="User Image" />
                                <p>
                                    <?php echo htmlspecialchars($_SESSION['nama'] ?? 'Pengguna'); ?>
                                    <small><?php echo htmlspecialchars($_SESSION['role'] ?? 'Role'); ?></small>
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
        
        <?php include "sidebar.php"; ?>
        
        <main class="app-main">
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0">Profil Pengguna</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">Profile</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="app-content">
                <div class="container-fluid profile-container">
                    <?php display_session_alert(); ?>
                    
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="profile-card">
                                <div class="profile-card-header">
                                    <div class="profile-avatar">
                                        <img src="dist/assets/img/user.png" class="user-image rounded-circle shadow" alt="User Image" />
                                    </div>
                                    <div class="profile-name"><?php echo htmlspecialchars($_SESSION['nama'] ?? 'Nama Pengguna'); ?></div>
                                    <div class="profile-role">
                                        <span class="badge bg-light text-dark"><?php echo htmlspecialchars($_SESSION['role'] ?? 'Role'); ?></span>
                                    </div>
                                </div>
                                <div class="profile-card-body">
                                    <div class="stats-card">
                                        <div class="stats-title">Status</div>
                                        <div class="stats-value">
                                            <span class="badge bg-success">Aktif</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-8">
                            <div class="profile-card">
                                <div class="profile-card-body">
                                    <div class="profile-section">
                                        <div class="profile-section-title">
                                            <i class="bi bi-person-circle"></i> Informasi Pribadi
                                        </div>
                                        <div class="info-item">
                                            <div class="info-label">Nama Lengkap</div>
                                            <div class="info-value"><?php echo htmlspecialchars($_SESSION['nama'] ?? 'N/A'); ?></div>
                                        </div>
                                        <div class="info-item">
                                            <div class="info-label">Username</div>
                                            <div class="info-value"><?php echo htmlspecialchars($_SESSION['username'] ?? 'N/A'); ?></div>
                                        </div>
                                        <div class="info-item">
                                            <div class="info-label">Role</div>
                                            <div class="info-value">
                                                <span class="badge bg-primary"><?php echo htmlspecialchars($_SESSION['role'] ?? 'N/A'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="profile-section">
                                        <div class="profile-section-title">
                                            <i class="bi bi-shield-lock"></i> Keamanan
                                        </div>
                                        
                                        <div class="info-item">
                                            <div class="info-label">Password</div>
                                            <div class="info-value">
                                              <?php
                                                // Dapatkan panjang password dari database
                                                $user_data = $db->get_user_by_id($_SESSION['id_user']);
                                                $password_length = isset($user_data['password']) ? strlen($user_data['password']) : 0;
                                                
                                                // Tampilkan bintang sesuai panjang password
                                                echo str_repeat('*', $password_length);
                                                ?>
                                                <a class="btn btn-sm btn-outline-primary ms-3" onclick="openEditModal()">Ganti</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="action-buttons">
                                        <div>
                                            <button type="button" onclick="openEditModal()" class="btn btn-primary">
                                                <i class="bi bi-pencil-square"></i> Edit Profile
                                            </button>
                                        </div>
                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#logoutModal">
                                            <i class="bi bi-box-arrow-right"></i> Sign Out
                                        </button>
                                    </div>
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
    
    <!-- Logout Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="logoutModalLabel">Konfirmasi Sign Out</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Yakin ingin keluar dari akun?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="../logout.php" class="btn btn-danger">Sign Out</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Profile Modal -->
    <div id="modalEditProfile" class="custom-modal">
        <div class="custom-modal-content">
            <span onclick="closeModal('modalEditProfile')" class="custom-modal-close">&times;</span>
            <h3>Edit Profil Pengguna</h3>
            <form action="profile.php" method="post" id="formEditProfile">
                <input type="hidden" name="id_user" value="<?php echo htmlspecialchars($_SESSION['id_user'] ?? ''); ?>">
                <div class="mb-3">
                    <label for="edit_nama" class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" id="edit_nama" name="nama" 
                           value="<?php echo htmlspecialchars($_SESSION['nama'] ?? ''); ?>" disabled>
                </div>
                <div class="mb-3">
                    <label for="edit_role" class="form-label">Role</label>
                    <input type="text" class="form-control" id="edit_role" name="role" 
                           value="<?php echo htmlspecialchars($_SESSION['role'] ?? ''); ?>" disabled>
                </div>
                <div class="mb-3">
                    <label for="edit_username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="edit_username" name="username" 
                           value="<?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="edit_password" class="form-label">Password Baru</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="edit_password" name="password" 
                               placeholder="Biarkan kosong jika tidak ingin mengubah">
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    <small class="form-text text-muted">Isi hanya jika ingin mengubah password</small>
                </div>
                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary" name="update">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/browser/overlayscrollbars.browser.es6.min.js" integrity="sha256-dghWARbRe2eLlIJ56wNB+b760ywulqK3DzZYEpsg2fQ=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    <script src="dist/js/adminlte.js"></script>
    <script>
        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function() {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
            
            // Initialize OverlayScrollbars for sidebar
            const SELECTOR_SIDEBAR_WRAPPER = '.sidebar-wrapper';
            const sidebarWrapper = document.querySelector(SELECTOR_SIDEBAR_WRAPPER);
            if (sidebarWrapper && typeof OverlayScrollbarsGlobal?.OverlayScrollbars !== 'undefined') {
                OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
                    scrollbars: {
                        theme: 'os-theme-light',
                        autoHide: 'leave',
                        clickScroll: true,
                    },
                });
            }

            // Password toggle
            const togglePassword = document.querySelector('#togglePassword');
            if (togglePassword) {
                togglePassword.addEventListener('click', function() {
                    const password = document.querySelector('#edit_password');
                    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                    password.setAttribute('type', type);
                    this.innerHTML = type === 'password' ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>';
                });
            }
        });

        // Modal functions
        function openEditModal() {
            const modal = document.getElementById('modalEditProfile');
            if (modal) {
                modal.style.display = 'block';
                adjustModalSize(modal.querySelector('.custom-modal-content'));
            }
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = 'none';
            }
        }

        function adjustModalSize(modalElement) {
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

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modals = document.querySelectorAll('.custom-modal');
            modals.forEach(modal => {
                if (event.target == modal) {
                    modal.style.display = 'none';
                }
            });
        }

        // Adjust modal on resize
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