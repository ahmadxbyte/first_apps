<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $judul ?></title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= base_url() ?>assets/fontawesome/css/all.min.css">

    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">

    <!-- char js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <!-- Bootstrap 5 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- DataTables -->
    <link rel="stylesheet" href="<?= base_url() ?>assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">

    <!-- select2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

    <!-- Theme style -->
    <link rel="stylesheet" href="<?= base_url() ?>assets/dist/css/adminlte.min.css">

    <!-- sweetalert -->
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- animate -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <!-- Select2 js -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.full.min.js"></script>

    <!-- Bootstrap 4 -->
    <script src="<?= base_url() ?>assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- DataTables  & Plugins -->
    <script src="<?= base_url() ?>assets/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="<?= base_url() ?>assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="<?= base_url() ?>assets/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="<?= base_url() ?>assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="<?= base_url() ?>assets/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="<?= base_url() ?>assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="<?= base_url() ?>assets/plugins/jszip/jszip.min.js"></script>
    <script src="<?= base_url() ?>assets/plugins/pdfmake/pdfmake.min.js"></script>
    <script src="<?= base_url() ?>assets/plugins/pdfmake/vfs_fonts.js"></script>
    <script src="<?= base_url() ?>assets/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="<?= base_url() ?>assets/plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="<?= base_url() ?>assets/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
    <link rel="icon" href="<?= base_url('assets/img/web/') . $web->logo ?>" type="image/ico">

    <!-- full calendar -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>
</head>

<body style="background-image: url(<?= site_url('assets/img/web/') . $web->bg ?>); background-size: cover; background-repeat: no-repeat; background-position: center; backdrop-filter: blur(8px);">

    <!-- responsive -->
    <style>
        /* select2 */
        .select2-selection__rendered {
            line-height: 31px !important;
        }

        .select2-container .select2-selection--single {
            height: 37px !important;
        }

        .select2-selection__arrow {
            height: 37px !important;
        }

        .border-primary {
            border: 1px solid #007bff;
        }

        .border-danger {
            border: 1px solid #c82333;
        }

        /* For mobile phones: */
        [class*="col-"] {
            width: 100%;
        }

        @media only screen and (min-width: 768px) {

            /* For desktop: */
            .col-1 {
                width: 8.33%;
            }

            .col-2 {
                width: 16.66%;
            }

            .col-3 {
                width: 25%;
            }

            .col-4 {
                width: 33.33%;
            }

            .col-5 {
                width: 41.66%;
            }

            .col-6 {
                width: 50%;
            }

            .col-7 {
                width: 58.33%;
            }

            .col-8 {
                width: 66.66%;
            }

            .col-9 {
                width: 75%;
            }

            .col-10 {
                width: 83.33%;
            }

            .col-11 {
                width: 91.66%;
            }

            .col-12 {
                width: 100%;
            }
        }

        .btn-circle {
            width: 30px;
            height: 30px;
            padding: 6px 0px;
            border-radius: 15px;
            text-align: center;
            font-size: 12px;
            line-height: 1.42857;
        }

        .floating {
            position: fixed;
            width: 70px;
            height: 70px;
            bottom: 10px;
            right: 10px;
            background-color: #14a651;
            color: white;
            border-radius: 50%;
            text-align: center;
            box-shadow: 2px 2px 4px #999;
        }
    </style>

    <?php if ($web->ct_theme == 1) { ?>
        <style>
            .swal2-modal {
                background-color: rgba(255, 255, 255, 0.4);
                -webkit-backdrop-filter: blur(4px);
                backdrop-filter: blur(4px);
            }
        </style>
    <?php } else if ($web->ct_theme == 2) { ?>
        <style>
            .swal2-modal {
                background-color: rgba(30, 30, 30, 0.6);
                -webkit-backdrop-filter: blur(5px);
                backdrop-filter: blur(5px);
                color: white;
            }
        </style>
    <?php } ?>

    <div class="d-flex justify-content-center align-items-center" style="min-height: 100vh;">
        <?php echo $content ?>
    </div>

    <footer class="fixed-bottom pb-5" style="width: 100vw; left: 0; right: 0; max-width: 100vw;">
        <div class="row m-0">
            <div class="col-md-12 text-center">
                <button type="button" class="btn" onclick="exit()" data-toggle="tooltip" data-placement="top" title="Keluar" style="background-color: transparent !important; border: none;">
                    <i class="fa fa-power-off fa-2x text-white"></i>
                </button>
            </div>
        </div>
    </footer>

    <!-- ionicon -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <script>
        $(function() {
            $('[data-toggle="tooltip"]').tooltip()
        })

        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
        // fungsi keluar sistem
        function exit() {
            Swal.fire({
                title: "Kamu yakin?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, keluar!"
            }).then((result) => {
                if (result.isConfirmed) { // jika di konfirmasi "Ya"
                    // arahkan ke fungsi logout di controller Auth
                    getUrl('Auth/logout')
                }
            });
        }



        // darkmode/lightmode
        // Theme toggle logic
        function setTheme(theme) {
            if (theme === 'dark') {
                document.body.classList.add('dark-mode');
                document.getElementById('theme-icon').className = 'fa-solid fa-sun';
                document.getElementById('titleMode').textContent = 'Light';
            } else {
                document.body.classList.remove('dark-mode');
                document.getElementById('theme-icon').className = 'fa-solid fa-moon';
                document.getElementById('titleMode').textContent = 'Dark';
            }
            localStorage.setItem('theme', theme);
        }

        // On load, set theme from cache
        document.addEventListener('DOMContentLoaded', function() {
            var theme = localStorage.getItem('theme') || 'light';
            setTheme(theme);

            document.getElementById('toggle-theme').addEventListener('click', function() {
                var currentTheme = localStorage.getItem('theme') || 'light';
                setTheme(currentTheme === 'light' ? 'dark' : 'light');
            });
        });

        // Optional: Add dark-mode CSS if not already present
        (function() {
            if (!document.getElementById('dark-mode-style')) {
                var style = document.createElement('style');
                style.id = 'dark-mode-style';
                style.innerHTML = `
                body.dark-mode {
                background-color: #222 !important;
                color: #fff !important;
                }
                body.dark-mode .main-header,
                body.dark-mode .main-footer,
                body.dark-mode .main-sidebar,
                body.dark-mode .sidebar,
                body.dark-mode .content-wrapper {
                background-color: #222 !important;
                color: #fff !important;
                }
                body.dark-mode .main-header,
                body.dark-mode .main-header .navbar-nav .nav-link,
                body.dark-mode .main-header .navbar-nav .nav-link span,
                body.dark-mode .main-header .navbar-nav .btn,
                body.dark-mode .main-header .navbar-nav .fa,
                body.dark-mode .main-header .navbar-nav .fa-solid,
                body.dark-mode .main-header .navbar-nav .fa-regular,
                body.dark-mode .main-header .navbar-nav .fa-circle-question,
                body.dark-mode .main-header .navbar-nav .fa-bell,
                body.dark-mode .main-header .navbar-nav .fa-right-from-bracket {
                color: #fff !important;
                }
                body.dark-mode .main-header .navbar-nav .nav-link .badge,
                body.dark-mode .main-header .navbar-nav .btn .badge {
                color: #fff !important;
                }
                body.dark-mode .card,
                body.dark-mode .modal-content {
                background-color: #333 !important;
                color: #fff !important;
                }
                body.dark-mode .table {
                color: #fff !important;
                }
                body.dark-mode .btn,
                body.dark-mode .form-control {
                background-color: #333 !important;
                color: #fff !important;
                border-color: #444 !important;
                }
                body.dark-mode .select2-container--default .select2-selection--single,
                body.dark-mode .select2-container--default .select2-selection--multiple {
                background-color: #333 !important;
                color: #fff !important;
                border-color: #444 !important;
                }
                body.dark-mode .small-box {
                background-color: #333 !important;
                color: #fff !important;
                }
                body.dark-mode .small-box .icon > i {
                color: #fff !important;
                }
                /* Dropdowns */
                body.dark-mode .dropdown-menu,
                body.dark-mode .dropdown-menu-right,
                body.dark-mode .dropdown-menu-lg {
                background-color: #333 !important;
                color: #fff !important;
                border-color: #444 !important;
                }
                body.dark-mode .dropdown-item {
                color: #fff !important;
                }
                body.dark-mode .dropdown-item:hover,
                body.dark-mode .dropdown-item:focus {
                background-color: #444 !important;
                color: #fff !important;
                }
            `;
                document.head.appendChild(style);
            }
        })();
    </script>

    <!-- AdminLTE App -->
    <script src="<?= base_url() ?>assets/dist/js/adminlte.min.js"></script>

    <!-- AdminLTE for demo purposes -->
    <script src="<?= base_url() ?>assets/dist/js/demo.js"></script>
</body>

</html>