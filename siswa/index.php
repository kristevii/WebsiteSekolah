<?php
session_start();
// Cek apakah user sudah login
if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit();
}

// Cek role user
if ($_SESSION['role'] != 'Siswa') {
    header("Location: ../unauthorized.php");
    exit();
}

include "../koneksi.php";
$db = new database();
$jumlahdata_siswa = $db->jumlahdata_siswa();
$jumlahdata_jurusan = $db->jumlahdata_jurusan();
?>

<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Sekolah | Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="title" content="Sekolah | Dashboard" />
    <meta name="author" content="ColorlibHQ" />
    <meta name="description" content="AdminLTE is a Free Bootstrap 5 Admin Dashboard, 30 example pages using Vanilla JS." />
    <meta name="keywords" content="bootstrap 5, bootstrap, bootstrap 5 admin dashboard, bootstrap 5 dashboard, bootstrap 5 charts, bootstrap 5 calendar, bootstrap 5 datepicker, bootstrap 5 tables, bootstrap 5 datatable, vanilla js datatable, colorlibhq, colorlibhq dashboard, colorlibhq admin dashboard" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css" integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q=" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/styles/overlayscrollbars.min.css" integrity="sha256-tZHrRjVqNSRyWg2wbppGnT833E/Ys0DHWGwT04GiqQg=" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" integrity="sha256-9kPW/n5nn53j4WMRYAxe9c1rCY96Oogo/MKSVdKzPmI=" crossorigin="anonymous" />
    <link rel="stylesheet" href="dist/css/adminlte.css" />
    </head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">
        <nav class="app-header navbar navbar-expand bg-body stiky-top">
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
                        <div class="col-sm-6">
                            <h3 class="mb-0">Dashboard</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                            </ol>
                        </div>
                    </div>
                    </div>
                </div>
            <div class="app-content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-3 col-6">
                            <div class="small-box text-bg-primary">
                                <div class="inner">
                                    <h3><?php echo $jumlahdata_siswa; ?></h3>
                                    <p>Data Siswa</p>
                                </div>
                                <svg class="small-box-icon" fill="currentColor" viewBox="0 0 19 19" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M4 14.5V11.165L8.00843 13.4663C9.24174 14.1743 10.7583 14.1743 11.9916 13.4663L16 11.165V14.5C16 14.6326 15.9473 14.7598 15.8536 14.8536L15.852 14.8551L15.8496 14.8574L15.8428 14.8642L15.8201 14.8859C15.801 14.9039 15.7741 14.9288 15.7394 14.9596C15.6701 15.0213 15.5696 15.1067 15.4389 15.2079C15.1777 15.41 14.7948 15.6761 14.2978 15.9412C13.3033 16.4716 11.8479 17 10 17C8.15211 17 6.69675 16.4716 5.70221 15.9412C5.20518 15.6761 4.82226 15.41 4.5611 15.2079C4.43043 15.1067 4.32994 15.0213 4.26059 14.9596C4.22591 14.9288 4.19898 14.9039 4.17992 14.8859L4.15724 14.8642C4.05938 14.7684 4 14.6378 4 14.5Z" />
                                    <path d="M18.7489 8.43369L11.4937 12.599C10.5687 13.1301 9.4313 13.1301 8.50632 12.599L2 8.86367L2 13.5C2 13.7761 1.77614 14 1.5 14C1.22386 14 1 13.7761 1 13.5V8.00008C1 7.81007 1.10598 7.64474 1.26206 7.56014L8.5063 3.40104C8.85317 3.20189 9.22992 3.07743 9.61413 3.02764C9.73586 3.01187 9.85834 3.00359 9.98086 3.00281C10.3739 3.0003 10.7674 3.07496 11.1377 3.22679C11.2591 3.27658 11.3781 3.33466 11.4937 3.40104L18.749 7.56646C18.9042 7.65561 19 7.82101 19 8.00008C19 8.17914 18.9042 8.34454 18.7489 8.43369Z" />
                                </svg>
                                <a href="datasiswa.php" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                                    View details <i class="bi bi-link-45deg"></i>
                                </a>
                            </div>
                            </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box text-bg-warning">
                                <div class="inner">
                                    <h3><?php echo $jumlahdata_jurusan; ?></h3>
                                    <p>Data Jurusan</p>
                                </div>
                                <svg class="small-box-icon" fill="currentColor" viewBox="0 0 448 512" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M96 0C43 0 0 43 0 96L0 416c0 53 43 96 96 96l288 0 32 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l0-64c17.7 0 32-14.3 32-32l0-320c0-17.7-14.3-32-32-32L384 0 96 0zm0 384l256 0 0 64L96 448c-17.7 0-32-14.3-32-32s14.3-32 32-32zm32-240c0-8.8 7.2-16 16-16l192 0c8.8 0 16 7.2 16 16s-7.2 16-16 16l-192 0c-8.8 0-16-7.2-16-16zm16 48l192 0c8.8 0 16 7.2 16 16s-7.2 16-16 16l-192 0c-8.8 0-16-7.2-16-16s7.2-16 16-16z"></path>
                                </svg>
                                <a href="datajurusan.php" class="small-box-footer link-dark link-underline-opacity-0 link-underline-opacity-50-hover">
                                    View details <i class="bi bi-link-45deg"></i>
                                </a>
                            </div>
                        </div>
                        </div>
                        <div class="col-md-12">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title">Statistik Data</h5>
                                    <div class="card-tools">
                                    </div>
                                </div>
                                <div class="card-body">
                                    <!--begin::Row-->
                                    <div class="row">
                                    <div class="col-md-8">
                                        <div id="sales-chart"></div>
                                    </div>
                                    <!-- /.col -->
                                    <div class="col-md-4">
                                        <div class="progress-group">
                                        Data Siswa
                                        <span class="float-end"><b><?php echo $jumlahdata_siswa; ?></b>/100</span>
                                        <div class="progress progress-sm">
                                            <div class="progress-bar text-bg-primary" style="width: <?php echo ($jumlahdata_siswa / 100) * 100; ?>%"></div>
                                        </div>
                                        </div>
                                        <div class="progress-group">
                                        Data Jurusan
                                        <span class="float-end"><b><?php echo $jumlahdata_jurusan; ?></b>/100</span>
                                        <div class="progress progress-sm">
                                            <div class="progress-bar text-bg-warning" style="width: <?php echo ($jumlahdata_jurusan / 100) * 100; ?>%"></div>
                                        </div>
                                        </div>
                                    </div>
                                    <!-- /.col -->
                                    </div>
                                    <!--end::Row-->
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
    <!-- Tambahkan script ApexCharts sebelum script lainnya -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/browser/overlayscrollbars.browser.es6.min.js" integrity="sha256-dghWARbRe2eLlIJ56wNB+b760ywulqK3DzZYEpsg2fQ=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    <script src="dist/js/adminlte.js"></script>
    <script>
        const SELECTOR_SIDEBAR_WRAPPER = '.sidebar-wrapper';
        const Default = {
            scrollbarTheme: 'os-theme-light',
            scrollbarAutoHide: 'leave',
            scrollbarClickScroll: true,
        };
        document.addEventListener('DOMContentLoaded', function() {
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
    <script>
        const jumlahdata_siswa = <?php echo $jumlahdata_siswa; ?>;
        const jumlahdata_jurusan = <?php echo $jumlahdata_jurusan; ?>;
        const sales_chart_options = {
            series: [{
                name: 'Jumlah Data',
                data: [jumlahdata_siswa, jumlahdata_jurusan],
            },],
            chart: {
                height: 180,
                type: 'area',
                toolbar: {
                    show: false,
                },
            },
            legend: {
                show: false,
            },
            colors: ['#0d6efd', '#dc3545', '#20c997', '#ffc107'],
            dataLabels: {
                enabled: false,
            },
            stroke: {
                curve: 'smooth',
            },
            xaxis: {
                categories: [
                    'Data Siswa',
                    'Data Jurusan',
                ],
            },
            yaxis: {
                min: 0,
            },
            tooltip: {
                x: {
                    format: 'MMMM yyyy',
                },
            },
        };

        const sales_chart = new ApexCharts(
            document.querySelector('#sales-chart'),
            sales_chart_options,
        );
        sales_chart.render();

    </script>
    </body>
</html>