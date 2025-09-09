<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $page ?></title>

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

<body class="hold-transition sidebar-mini layout-fixed">

    <style>
        <?php if ($this->uri->segment(1) === 'Emr'): ?><?php foreach ($hist_member as $hm): ?>#popup<?= $hm->id ?>,
        #popup2<?= $hm->id ?> {
            position: fixed;
            display: none;
            width: 30vw;
            top: 10%;
            right: 67%;
            z-index: 9998;
        }

        .card-draggable<?= $hm->id ?> {
            cursor: move;
        }

        .card-draggable2<?= $hm->id ?> {
            cursor: move;
        }

        <?php endforeach; ?><?php endif; ?>
    </style>

    <script>
        // Dynamic z-index for popups: bring last clicked to front
        <?php if ($this->uri->segment(1) == 'Emr'): ?>
            <?php foreach ($hist_member as $hm): ?>
                // Define popup IDs for this member
                const popupIds<?= $hm->id ?> = ['popup<?= $hm->id ?>', 'popup2<?= $hm->id ?>'];

                // Track highest z-index used
                let highestZIndex<?= $hm->id ?> = 9998;

                // Function to handle bringing popup to front
                function bringToFront<?= $hm->id ?>(popupId) {
                    // Increment highest z-index
                    highestZIndex<?= $hm->id ?>++;

                    // Set clicked popup to new highest z-index
                    const selectedPopup = document.getElementById(popupId);
                    if (selectedPopup) {
                        selectedPopup.style.zIndex = highestZIndex<?= $hm->id ?>;
                    }
                }

                // Initialize event listeners when DOM is ready
                document.addEventListener('DOMContentLoaded', () => {
                    // Add mousedown handlers to each popup
                    popupIds<?= $hm->id ?>.forEach(id => {
                        const popup = document.getElementById(id);
                        if (popup) {
                            popup.addEventListener('mousedown', () => bringToFront<?= $hm->id ?>(id));
                        }
                    });
                });
            <?php endforeach; ?>
        <?php else: ?>

            function bringToFront(popupId) {
                // temukan popup
                const popups = ['popup_psn'];
                // Set base z-index
                let baseZ = 9998;
                // Set semua popups ke base z-index
                popups.forEach(id => {
                    const el = document.getElementById(id);
                    if (el) el.style.zIndex = baseZ;
                });
                // Set popup yg dipilih ke yg terbesar dari z-index
                const topEl = document.getElementById(popupId);
                if (topEl) topEl.style.zIndex = baseZ + 2;
            }

            // ketika popups di klik, jadikan dipaling depan
            document.addEventListener('DOMContentLoaded', function() {
                ['popup_psn'].forEach(function(id) {
                    const el = document.getElementById(id);
                    if (el) {
                        el.addEventListener('mousedown', function() {
                            bringToFront(id);
                        });
                    }
                });
            });
        <?php endif; ?>
    </script>

    <!-- responsive -->
    <style>
        /* select2 */
        .select2-selection__rendered {
            line-height: 27px !important;
        }

        .select2-container .select2-selection--single {
            height: 39px !important;
        }

        .select2-selection__arrow {
            height: 39px !important;
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

        <?php
        $color_bg = $this->M_global->getData('web_setting', ['id' => 1])->bg_theme;
        ?>.active {
            background-color: #f2f2f4 !important;
            color: <?= $color_bg ?> !important;
        }

        .letter {
            background: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            margin: 26px auto 0;
            min-height: 300px;
            padding: 24px;
            position: relative;
            width: 80%;
        }

        .letter:before,
        .letter:after {
            content: "";
            height: 98%;
            position: absolute;
            width: 100%;
            z-index: -1;
        }

        .letter:before {
            background: #fafafa;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.2);
            left: -5px;
            top: 4px;
            transform: rotate(-2.5deg);
        }

        .letter:after {
            background: #f6f6f6;
            box-shadow: 0 0 3px rgba(0, 0, 0, 0.2);
            right: -3px;
            top: 1px;
            transform: rotate(1.4deg);
        }

        #popup_psn {
            position: fixed;
            z-index: 9999;
            display: none;
            width: 30vw;
            top: 10%;
            right: 67%;
        }

        .pesanku-no-border {
            margin-top: auto;
            border-bottom: 0 !important;
        }

        .paper-plane-no-border {
            margin-top: auto;
            border-bottom: 0 !important;
        }

        .collapse {
            transition: max-height 0.3s ease-in-out;
            overflow: hidden;
            max-height: 0;
        }

        .collapse.show {
            max-height: 500px;
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

            /* Semua text di seluruh halaman menjadi putih */
            html,
            body,
            body,
            body *:not(.main-sidebar):not(.main-sidebar *):not(.sidebar):not(.sidebar *):not(.main-footer):not(.main-footer *)[style*="color: #000"],
            body *:not(.main-sidebar):not(.main-sidebar *):not(.sidebar):not(.sidebar *):not(.main-footer):not(.main-footer *)[style*="color:#000"],
            body *:not(.main-sidebar):not(.main-sidebar *):not(.sidebar):not(.sidebar *):not(.main-footer):not(.main-footer *)[style*="color: rgb(0, 0, 0)"],
            body *:not(.main-sidebar):not(.main-sidebar *):not(.sidebar):not(.sidebar *):not(.main-footer):not(.main-footer *)[style*="color:rgba(0,0,0"],
            body *:not(.main-sidebar):not(.main-sidebar *):not(.sidebar):not(.sidebar *):not(.main-footer):not(.main-footer *)[style*="color: black"],
            body *:not(.main-sidebar):not(.main-sidebar *):not(.sidebar):not(.sidebar *):not(.main-footer):not(.main-footer *)[style*="color:black"] {
                color: white !important;
            }

            /* Tambahan untuk select2, input, textarea agar text tidak hitam di dark mode */
            .select2-container--default .select2-selection--single,
            .select2-container--default .select2-selection--multiple,
            .select2-container--default .select2-selection--single .select2-selection__rendered,
            .select2-container--default .select2-selection--multiple .select2-selection__rendered,
            .select2-container--default .select2-search--dropdown .select2-search__field,
            .select2-container--default .select2-results__option {
                color: #222 !important;
                background-color: white !important;
                border-color: #222 !important;
            }

            /* Placeholder color untuk input dan textarea */
            input::placeholder,
            textarea::placeholder {
                color: #bbb !important;
                opacity: 1 !important;
            }

            /* Tombol yang tidak active (bukan .active) text putih */
            .btn:not(.active),
            button:not(.active),
            .nav-link:not(.active) {
                color: #fff !important;
            }

            /* Placeholder color untuk select2 search */
            .select2-search__field::placeholder {
                color: #bbb !important;
                opacity: 1 !important;
            }

            .main-header,
            .main-header .navbar-nav .nav-link,
            .main-header .navbar-nav .nav-link span,
            .main-header .navbar-nav .btn,
            .main-header .navbar-nav .fa,
            .main-header .navbar-nav .fa-solid,
            .main-header .navbar-nav .fa-regular,
            .main-header .navbar-nav .fa-circle-question,
            .main-header .navbar-nav .fa-bell,
            .main-header .navbar-nav .fa-right-from-bracket {
                color: white !important;
            }

            .main-header .navbar-nav .nav-link .badge,
            .main-header .navbar-nav .btn .badge {
                color: white !important;
            }

            /* table-hover */
            .table-hover tbody tr:hover td,
            .table-hover tbody tr:hover th {
                color: rgb(0, 123, 255) !important;
            }
        </style>
    <?php }
    $master_cabang = $this->M_global->getData('cabang', ["kode_cabang" => $this->session->userdata('cabang')]);
    $version_web = $this->M_global->getData('web_version', ["id_web" => $this->session->userdata('web_id')]);

    if ($web->ct_theme == 1) {
        $style = 'style="background: rgba(255, 255, 255, 0.6); backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px);"';
        $style2 = 'style="backdrop-filter: blur(10px);"';
        $style3 = 'style="background: transparent;"';
        $style4 = 'style="background-color: rgba(255, 255, 255, 0.8); backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px); width: 10vw; border-radius: 8px;"';
        $style5 = 'style="background-color: rgba(255, 255, 255, 0.8); backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px); width: 30vw; border-radius: 8px;"';
        $style6 = 'style="background-color: rgba(255, 255, 255, 0.8); backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px); width: 30vw; border-radius: 8px;"';
        $style_modal = 'style="background-color: rgba(255, 255, 255, 0.6); -webkit-backdrop-filter: blur(10px); backdrop-filter: blur(4px);"';
    } else if ($web->ct_theme == 2) {
        $style = 'style="background: rgba(30, 30, 30, 0.8); backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px); color: white !important;"';
        $style2 = 'style="backdrop-filter: blur(10px);"';
        $style3 = 'style="background: transparent;"';
        $style4 = 'style="background-color: rgba(30, 30, 30, 0.8); backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px); width: 10vw; border-radius: 8px;"';
        $style5 = 'style="background-color: rgba(30, 30, 30, 0.8); backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px); width: 30vw; border-radius: 8px;"';
        $style6 = 'style="background-color: rgba(30, 30, 30, 0.8); backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px); width: 30vw; border-radius: 8px; color: white !important;"';
        $style_modal = 'style="background-color: rgba(30, 30, 30, 0.9); -webkit-backdrop-filter: blur(30px); backdrop-filter: blur(5px); color: white !important;"';
    } else {
        $style = '';
        $style2 = '';
        $style3 = '';
        $style4 = '';
        $style5 = '';
        $style6 = 'style="width: 30vw;"';
        $style_modal = '';
    }

    // Set session update time in minutes (auto_lock is in minutes)
    $config['sess_time_to_update'] = 60000 * $web->auto_lock; // waktu dalam detik (menit x 60)

    $tgl1 = strtotime(date('Y-m-d'));
    $tgl2 = strtotime($master_cabang->aktif_sampai);

    $jarak = ($tgl2 - $tgl1);

    $aktif_cabang = $jarak / 60 / 60 / 24;

    cek_so();

    // Hapus sampah otomatis
    $web_setting = $this->M_global->getData('web_setting', ['id' => $this->session->userdata('web_id')]); //ambil setting web

    $limit_trash = $web_setting->limit_trash_web; // ambil limit trash

    $sampah = $this->M_global->getDataSampah(); // ambil data sampah

    if (count($sampah) > 0) { // cek jika ada data sampah
        foreach ($sampah as $s) { // lakukan loop
            // Get trash date + limit days
            $date_trash = date('Y-m-d', strtotime($s->tgl . ' + ' . $limit_trash . ' days'));

            // Delete trash data where delete date is less than or equal to today
            if (strtotime(date('Y-m-d')) >= strtotime($date_trash)) {
                $this->M_global->delData($s->tabel, ['tgl_hapus <= ' => date('Y-m-d')]);
            }
        }
    }
    ?>

    <div id="popup_psn">
        <div class="card shadow card-lg" style="border: 1px solid grey;">
            <div class="card-header card-draggable_psn">
                <span class="h4">
                    Pesan
                    <i type="button" class="fa fa-times float-right" onclick="close_popup_psn()"></i>
                </span>
            </div>
            <div id="body_psn" style="overflow-y: scroll; overflow-x: hidden; height: 70vh; width: 100%;"></div>
        </div>
    </div>

    <script>
        const popup_psn = document.getElementById('popup_psn');
        const header_psn = document.querySelector('.card-draggable_psn');
        let offsetX_ps, offsetY_psn, isDragging_psn = false;

        header_psn.addEventListener('mousedown', (e) => {
            isDragging_psn = true;
            offsetX_ps = e.clientX - popup_psn.offsetLeft;
            offsetY_psn = e.clientY - popup_psn.offsetTop;
        });

        document.addEventListener('mousemove', (e) => {
            if (!isDragging_psn) return;
            popup_psn.style.left = e.clientX - offsetX_ps + 'px';
            popup_psn.style.top = e.clientY - offsetY_psn + 'px';
        });

        document.addEventListener('mouseup', () => {
            isDragging_psn = false;
        });

        function close_popup_psn() {
            popup_psn.style.display = 'none';
        }

        function pop_psn() {
            $('#body_psn').text('');

            // Initial load
            let xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("body_psn").innerHTML = this.responseText;
                }
            };
            xhttp.open("GET", "<?= base_url('Auth/body_psn'); ?>", true);
            xhttp.send();

            popup_psn.style.display = 'block';
        }
    </script>


    <div class="wrapper">

        <!-- Preloader -->
        <div class="preloader flex-column justify-content-center align-items-center" id="loadering" <?= $style; ?>>
            <img class="animation__shake" src="<?= base_url('assets/img/web/') . $web->loading ?>" alt="AdminLTELogo">
        </div>

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light shadow-sm fixed-top" <?= $style; ?>>
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" type="button" role="button"><i class="fa-solid fa-caret-left"></i></a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <span type="button" class="nav-link">Cabang: <?= $master_cabang->cabang ?></span>
                    <span id="countdownNotif"><?= $web->auto_reload ?> Detik</span>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <span class="nav-link" style="border-left: 1px solid #ccc; height: 100%;"></span>
                </li>
                <?php if ($this->uri->segment(1) == 'Emr') : ?>
                    <li class="nav-item d-none d-sm-inline-block" style="margin-left: -25px;">
                        <span class="nav-link">Refresh Otomatis <span id="countdown"><?= $web->auto_reload ?> Detik</span></span>
                    </li>
                <?php endif; ?>
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">

                <!-- Messages Dropdown Menu -->
                <li class="nav-item dropdown">
                    <?= ($this->uri->segment(1) == 'Home' ? '' : '<button class="btn" onclick="showGuide()"><i class="fa-solid fa-circle-question"></i>&nbsp;&nbsp;Manual Guide</button>') ?>
                </li>
                <?php if ($this->data["kode_role"] <> 'R0005') : ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link" data-toggle="dropdown" type="button" readonly>
                            <span class="badge badge-info" type="button" onclick="ganti_shift()"><?= 'Shift ~ ke: ' . $this->data["shift"] ?></span>
                        </a>
                    </li>
                <?php endif ?>
                <!-- <li class="nav-item">
                    <a href="" class="nav-link text-info" data-toggle="dropdown" type="button" onclick="pop_openai()" style="font-weight: bold;">
                        <i class="fa-solid fa-chalkboard-user"></i> AIDEN
                    </a>
                </li> -->
                <!-- <li class="nav-item dropdown">
                    <a href="" class="nav-link" data-toggle="dropdown" type="button" onclick="pop_psn()">
                        <i class="fa-solid fa-comments"></i> Pesan
                    </a>
                </li> -->
                <!-- <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" type="button">
                        <ion-icon name="chatbubbles-outline" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Tooltip on bottom" title="Perpesanan"></ion-icon>
                        <span class="badge badge-danger navbar-badge">3</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <a type="button" class="dropdown-item">
                            <div class="media">
                                <img src="<?= base_url() ?>assets/dist/img/user8-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
                                <div class="media-body">
                                    <h3 class="dropdown-item-title">
                                        John Pierce
                                        <span class="float-right text-sm text-muted"><i class="fas fa-star"></i></span>
                                    </h3>
                                    <p class="text-sm">I got your message bro</p>
                                    <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
                                </div>
                            </div>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a type="button" class="dropdown-item dropdown-footer">See All Messages</a>
                    </div>
                </li> -->

                <!-- Notifications Dropdown Menu -->
                <?php
                $membering = $this->M_global->getData('member', ['kode_member' => $this->session->userdata('kode_user')]);
                ?>

                <?php if (!$membering) : ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link" data-toggle="dropdown" type="button">
                            <i class="fa-regular fa-solid fa-bell"></i>&nbsp;&nbsp;Notifikasi&nbsp;&nbsp;
                            <div id="count_notif"></div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" <?= $style6 ?>>
                            <div id="notf_live" <?= $style6 ?>></div>
                        </div>
                    </li>
                <?php endif ?>
                <li class="nav-item d-none d-sm-inline-block">
                    <span class="nav-link" style="border-left: 1px solid #ccc;"></span>
                </li>
                <li class="nav-item dropdown" style="margin-left: -25px;">
                    <div class="user-panel d-flex" data-toggle="dropdown" type="button" readonly>
                        <div class="info ms-auto">
                            <span class="font-weight-bold text-primary">
                                <?= $this->data["nama"] ?>
                            </span>
                        </div>
                        <div class="image my-auto">
                            <img src="<?= (($this->session->userdata('kode_role') == 'R0005') ? base_url('assets/member/') . $this->data["foto"] : base_url('assets/user/') . $this->data["foto"]) ?>" class="img-circle elevation-2 shadow" alt="User Image">
                        </div>
                    </div>
                    <div class="dropdown-menu dropdown-menu-right p-2" <?= $style4 ?>>
                        <a type="button" class="btn w-100 text-left" href="<?= (($this->session->userdata('kode_role') == 'R0005') ? site_url('Profile/profile_member') : site_url('Profile')) ?>">
                            <i class="fa-solid fa-fw fa-user"></i>&nbsp;&nbsp;Akun
                        </a>
                        <button id="toggle-theme" class="btn w-100 text-left" type="button">
                            <i class="fa-solid fa-fw fa-moon" id="theme-icon"></i>&nbsp;&nbsp;<span id="titleMode">Dark</span> Mode
                        </button>
                        <button type="button" class="btn w-100 text-left text-danger" onclick="exit()">
                            <i class="fa-solid fa-fw fa-door-open"></i>&nbsp;&nbsp;Keluar
                        </button>
                    </div>
                </li>
            </ul>

            <script>
                let recognition;
                let recognizing = false;

                // Cek apakah browser mendukung
                if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
                    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
                    recognition = new SpeechRecognition();
                    recognition.lang = 'id-ID'; // Atur ke bahasa Indonesia
                    recognition.interimResults = false;
                    recognition.continuous = false;

                    recognition.onstart = function() {
                        recognizing = true;
                        $('#voiceBtn').html('<i class="fa fa-circle-notch fa-spin"></i>');
                    };

                    recognition.onend = function() {
                        recognizing = false;
                        $('#voiceBtn').html('<i class="fa fa-microphone"></i>');
                    };

                    recognition.onresult = function(event) {
                        const transcript = event.results[0][0].transcript;
                        $('#prompt').val(transcript);
                        tanya_ai(); // Jalankan AI setelah suara dikonversi
                    };

                    $('#voiceBtn').on('click', function() {
                        if (recognizing) {
                            recognition.stop();
                        } else {
                            recognition.start();
                        }
                    });

                } else {
                    $('#voiceBtn').hide(); // Sembunyikan tombol voice

                    $('#tanyaBtn').css('width', '100%'); // Full width untuk tombol tanya
                }
            </script>

        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4" style="background-color: <?= $web->bg_theme ?>;">

            <!-- Brand Logo -->
            <a type="button" href="<?= site_url('Home') ?>" class="brand-link" <?= $style2 ?>>
                <img src="<?= base_url('assets/img/web/') . $web->logo ?>" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8;">
                <span class="brand-text font-weight-light"><?= $nama_apps ?></span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar" <?= $style2 ?>>

                <!-- SidebarSearch Form -->
                <div class="form-inline mt-3">
                    <div class="input-group" data-widget="sidebar-search">
                        <input class="form-control form-control-sidebar" type="search" placeholder="Cari Menu..." aria-label="Search">
                    </div>
                </div>

                <!-- Sidebar -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        <?php
                        // ambil menu dari table m_menu kemudian tampung ke variable $menu
                        $menu = $this->db->query("SELECT m.* FROM m_menu m WHERE m.id IN (SELECT id_menu FROM akses_menu WHERE kode_role IN (SELECT kode_role FROM user WHERE kode_user = '" . $this->session->userdata('kode_user') . "')) ORDER BY m.id")->result();

                        if ($menu) {
                            $menu = $menu;
                        } else {
                            $menu = $this->db->query("SELECT m.* FROM m_menu m WHERE m.id IN (SELECT id_menu FROM akses_menu WHERE kode_role IN (SELECT kode_role FROM member WHERE kode_member = '" . $this->session->userdata('kode_user') . "')) ORDER BY m.id")->result();
                        }

                        // loop $menu
                        foreach ($menu as $m) :
                            if ($m->url == $this->uri->segment(1)) { // jika url menu sama dengan segment 1 dari url
                                // aktifkan
                                $aktifUrl = 'active';
                            } else { // selain itu
                                // nonaktifkan
                                $aktifUrl = '';
                            }
                        ?>
                            <?php
                            $cek_sm = $this->db->query('SELECT * FROM sub_menu WHERE id_menu = "' . $m->id . '"')->num_rows();
                            if ($cek_sm < 1) :
                            ?>
                                <li class="nav-item">
                                    <a type="button" class="nav-link <?= $aktifUrl ?>" onclick="getUrl('<?= $m->url ?>')">
                                        &nbsp;<?= $m->icon ?>
                                        <p class="<?= ($m->nama == 'Sampah Master') ? 'text-danger font-weight-bold' : '' ?>">
                                            <?php
                                            $data_sampah = $this->M_global->getDataSampah();

                                            if (count($data_sampah) > 0) {
                                                $count_sampah = ' <sup class="badge badge-primary">' . count($data_sampah) . '</sup>';
                                            } else {
                                                $count_sampah = '';
                                            }
                                            ?>
                                            <?= ($m->nama == 'Sampah Master') ? $m->nama . $count_sampah : $m->nama ?>
                                        </p>
                                    </a>
                                </li>
                            <?php else : ?>
                                <li class="nav-item">
                                    <a type="button" class="nav-link <?= $aktifUrl ?>">
                                        &nbsp;<?= $m->icon ?>
                                        <p class="<?= ($m->nama == 'Sampah') ? 'text-danger font-weight-bold' : '' ?>">
                                            <?= $m->nama ?>
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <?php
                                        $sub_menu = $this->db->get_where("sub_menu", ["id_menu" => $m->id])->result();

                                        foreach ($sub_menu as $sm) :
                                            $cek_submenu2 = $this->db->query("SELECT sm2.* FROM sub_menu2 sm2 JOIN sub_menu sm ON sm2.id_submenu = sm.id WHERE sm.url_submenu IS NULL AND sm2.id_submenu = '$sm->id'")->num_rows();
                                        ?>
                                            <li class="nav-item">
                                                <?php if ($cek_submenu2 > 0) :
                                                    $cek_sub_menu = $this->db->query("SELECT s.*, m.nama, m.url, sm2.url_submenu2 FROM sub_menu s JOIN m_menu m ON s.id_menu = m.id JOIN sub_menu2 sm2 ON sm2.id_submenu = s.id WHERE sm2.url_submenu2 = '" . $this->uri->segment('2') . "'")->row();

                                                    if (!empty($cek_sub_menu->submenu)) {
                                                        if ($cek_sub_menu->submenu == $sm->submenu) {
                                                            $aktifUrl2 = 'active';
                                                        } else {
                                                            $aktifUrl2 = '';
                                                        }
                                                    } else {
                                                        $aktifUrl2 = '';
                                                    }

                                                ?>
                                                    <a type="button" class="nav-link <?= $aktifUrl2 ?>">
                                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= $sm->icon ?>
                                                        <p>
                                                            <?= $sm->submenu; ?>
                                                            <?php if ($cek_submenu2 > 0) : ?>
                                                                <i class="right fas fa-angle-left"></i>
                                                            <?php endif; ?>
                                                        </p>
                                                    </a>
                                                    <ul class="nav nav-treeview">
                                                        <?php
                                                        $sub_menu2 = $this->db->get_where("sub_menu2", ["id_submenu" => $sm->id])->result();
                                                        foreach ($sub_menu2 as $sm2) :
                                                            $aktifUrl3 = ($sm2->url_submenu2 == $this->uri->segment(2)) ? 'active' : '';
                                                        ?>
                                                            <li class="nav-item">
                                                                <a type="button" class="nav-link <?= $aktifUrl3 ?>" href="<?= site_url($m->url . '/' . $sm2->url_submenu2) ?>">
                                                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= $sm2->icon ?>
                                                                    <p><?= $sm2->nama ?></p>
                                                                </a>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                <?php else :
                                                    $cek_sub_menu = $this->db->query("SELECT s.*, m.nama, m.url FROM sub_menu s JOIN m_menu m ON s.id_menu = m.id WHERE s.id = '$sm->id'")->row();

                                                    if (($this->uri->segment(1) == $cek_sub_menu->url) && ($this->uri->segment(2) == $cek_sub_menu->url_submenu)) {
                                                        $aktifUrl2 = 'active';
                                                    } else {
                                                        $aktifUrl2 = '';
                                                    }
                                                ?>
                                                    <a type="button" class="nav-link <?= $aktifUrl2 ?>" href="<?= site_url($m->url . '/' . $sm->url_submenu) ?>">
                                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= $sm->icon ?>
                                                        <p><?= $sm->submenu; ?></p>
                                                    </a>
                                                <?php endif; ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                    <hr style="border-color: white !important; background-color: white !important;">
                    <div class="text-center">
                        <div style="bottom: 10px; text-align: center;">
                            <a href="<?= $web->ig ?>" target="_blank" type="button" style="margin: 2px;" class="btn btn-danger"><i class="fa-brands fa-instagram"></i></a>
                            <a href="<?= $web->git ?>" target="_blank" type="button" style="margin: 2px;" class="btn btn-dark"><i class="fa-brands fa-github"></i></a>
                            <a href="https://wa.me/<?= $web->nohp ?>" target="_blank" type="button" style="margin: 2px;" class="btn btn-success"><i class="fa-brands fa-whatsapp"></i></a>
                            <a href="mailto:<?= $web->email ?>" target="_blank" type="button" style="margin: 2px;" class="btn btn-info"><i class="fa-solid fa-envelope-open-text"></i></a>
                        </div>
                    </div>
                </nav>
            </div>
        </aside>

        <!-- wrapper -->
        <div class="content-wrapper" style="margin-top: 50px; position: relative;">
            <!-- Background image container with blur --><!-- Background with blur -->
            <?php if ($web->bg_0 == 0) : ?>
                <div style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: url('<?= base_url('assets/img/web/') . $web->bg ?>') no-repeat center center fixed; background-size: cover; z-index: 0;"></div>
            <?php else : ?>
                <div style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: <?= $web->solid_bg ?>; background-size: cover; z-index: 0;"></div>
            <?php endif ?>

            <!-- modal loading proses -->
            <div class="modal fade" id="loading" data-bs-backdrop="static" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body text-center">
                            <img src="<?= base_url('assets/img/web/') . $web->loading ?>" style="width: 100%;">
                        </div>
                    </div>
                </div>
            </div>

            <!-- body -->
            <section class="content p-1">
                <div class="container-fluid pl-3 pr-3">
                    <br>
                    <?= $content ?>
                </div>
            </section>
        </div>

        <br>
        <br>
        <br>

        <!-- modal manual guide -->
        <div class="modal fade" id="modal_mg" tabindex="-1" aria-labelledby="modal_mgLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content" <?= $style_modal ?>>
                    <!-- class letter di hapus -->
                    <div class="modal-header text-primary">
                        <h5 class="modal-title" style="font-weight: bold;" id="modal_mgLabel"></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="md_close()">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="modal-isi"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- footer -->
        <footer class="main-footer fixed-bottom shadow-lg" <?= $style; ?>>
            <strong>Masa Aktif Cabang: <span class="text-danger"><?= number_format($aktif_cabang) ?></span> Hari</strong>
            <div class="float-right">
                <span type="button" class="nav-link" id="time"></span>
            </div>
        </footer>
    </div>

    <!-- ionicon -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <!-- myscript -->
    <script>
        // lockscreen
        setTimeout(function() {
            // Redirect otomatis ke halaman lockscreen setelah 10 detik
            // ambil last_url dan simpan ke session
            var lastUrl = window.location.href;
            // simpan last_url ke cache (localStorage)
            try {
                localStorage.setItem('last_url', lastUrl);
            } catch (e) {
                // handle jika localStorage tidak tersedia
            }
            window.location.href = "<?= site_url() ?>Auth/lockscreen";
        }, '<?= 60000 * $web->auto_lock ?>');

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

        // load pertama kali
        var siteUrl = '<?= site_url() ?>';
        var table;

        $(function() {
            $('[data-toggle="tooltip"]').tooltip()
        })

        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))

        // load pertama kali saat sistem berjalan
        $("#open_pass").hide();

        AOS.init();

        $(".select2_global").select2({
            placeholder: $(this).data('placeholder'),
            width: '100%',
            allowClear: true,
        });

        $(document).ready(function() {
            // Memeriksa ketika pushmenu diaktifkan
            $('[data-widget="pushmenu"]').click(function() {
                var icon = $(this).find('i'); // Menyimpan elemen <i> yang ada di dalam link

                // Memeriksa apakah ikon kiri aktif (sebelum pushmenu dibuka)
                if (icon.hasClass('fa-caret-left')) {
                    icon.removeClass('fa-caret-left').addClass('fa-caret-right'); // Ganti ke fa-caret-right
                } else {
                    icon.removeClass('fa-caret-right').addClass('fa-caret-left'); // Ganti ke fa-caret-left
                }
            });
        });


        $('#countdownNotif').hide();

        var timeNotif = '<?= $web->auto_reload ?>';
        var countdownNotif = setInterval(function() {
            if (timeNotif <= 0) {
                timeNotif = '<?= $web->auto_reload ?>';
                notif_live();
                count_notif_live();
            }
            document.getElementById("countdownNotif").innerHTML = timeNotif + " Detik";
            timeNotif -= 1;
        }, 500);

        notif_live();
        count_notif_live();

        function notif_live() {
            xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("notf_live").innerHTML = this.responseText;
                }
            };
            xhttp.open("GET", "<?= base_url('Auth/notif_live'); ?>", true);
            xhttp.send();
        }

        function count_notif_live() {
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    // Get the first element with the class 'count_notif' and update its innerHTML
                    document.getElementById("count_notif").innerHTML = this.responseText;
                }
            };
            xhttp.open("GET", "<?= base_url('Auth/count_notif'); ?>", true);
            xhttp.send();
        }

        display_ct();

        function close_popup(uid) {
            document.getElementById('popup' + uid).style.display = 'none';
        }

        function close_popup2(uid) {
            document.getElementById('popup2' + uid).style.display = 'none';
        }

        const hariIndo = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        // Fungsi untuk memformat tanggal dengan nama hari
        function formatDateWithDay(date) {
            const yyyy = date.getFullYear();
            let mm = date.getMonth() + 1; // Bulan dimulai dari 0, jadi tambahkan 1
            let dd = date.getDate();
            const dayName = hariIndo[date.getDay()]; // Ambil nama hari sesuai index getDay()

            if (dd < 10) dd = '0' + dd;
            if (mm < 10) mm = '0' + mm;

            // Format tanggal menjadi "Hari, dd-mm-yyyy"
            return `${dayName}`;
        }

        function formatTime(timeStr) {
            if (!timeStr) return '';
            // If it's a Date object, extract hours and minutes
            if (typeof timeStr === 'object' && typeof timeStr.getHours === 'function') {
                const hours = timeStr.getHours().toString().padStart(2, '0');
                const minutes = timeStr.getMinutes().toString().padStart(2, '0');
                return `${hours}:${minutes}`;
            }
            // If it's an ISO string or similar, try to parse as Date
            if (typeof timeStr === 'string' && timeStr.includes('T')) {
                const d = new Date(timeStr);
                if (!isNaN(d.getTime())) {
                    const hours = d.getHours().toString().padStart(2, '0');
                    const minutes = d.getMinutes().toString().padStart(2, '0');
                    return `${hours}:${minutes}`;
                }
            }
            // Otherwise, assume it's "HH:mm" string
            if (typeof timeStr === 'string' && timeStr.includes(':')) {
                const [hours, minutes] = timeStr.split(':');
                return `${hours.padStart(2, '0')}:${minutes.padStart(2, '0')}`;
            }
            return '';
        }

        function ganti_shift() {
            $('#modal_mgLabel').text(``);
            $('#modal-isi').text(``);

            $('#modal_mg').modal('show');
            $('#modal_mgLabel').html('Ganti Shift');
            $('#modal-isi').append(`
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Shift</label>
                        <div class="form-group">
                            <select class="form-control select2_new_shift" id="new_shift" name="new_shift" placeholder="Pilih Shift">
                                <option value="1" <?= ($this->session->userdata('shift') == 1) ? 'selected' : '' ?>>Shift 1</option>
                                <option value="2" <?= ($this->session->userdata('shift') == 2) ? 'selected' : '' ?>>Shift 2</option>
                                <option value="3" <?= ($this->session->userdata('shift') == 3) ? 'selected' : '' ?>>Shift 3</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label>Password</label>
                        <input type="password" class="form-control" id="shift_password" name="shift_password" placeholder="Password" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button type="button" class="btn btn-primary float-right" onclick="simpan_shift()">Update Shift</button>
                    </div>
                </div>
                `);

            $(".select2_new_shift").select2({
                placeholder: $(this).data('placeholder'),
                width: '100%',
                allowClear: true,
                dropdownParent: $("#modal_mg")
            });
        }

        // close modal
        function md_close() {
            $('#modal_mg').modal('hide');
        }

        function simpan_shift() {
            $('#modal_mg').modal('hide');

            var new_shift = $('#new_shift').val();
            var shift_password = $('#shift_password').val();

            $.ajax({
                url: siteUrl + 'Auth/ganti_shift?shift=' + new_shift + '&password=' + shift_password,
                type: 'POST',
                dataType: 'JSON',
                success: function(result) {
                    if (result.status == 1) {
                        Swal.fire({
                            title: "Shift",
                            text: "Berhasil di ganti!",
                            icon: "success"
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: "Shift",
                            text: "Gagal di ganti!",
                            icon: "info"
                        });
                    }
                },
                error: function(result) { // jika fungsi error
                    // jalankan fungsi error
                    error_proccess();
                }
            })
        }

        // fungsi clean db
        function clean_db() {
            Swal.fire({
                title: "Kamu yakin?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, kosongkan!"
            }).then((result) => {
                if (result.isConfirmed) { // jika di konfirmasi "Ya"
                    // arahkan ke fungsi logout di controller Auth
                    $.ajax({
                        url: siteUrl + 'Auth/clean_db',
                        type: 'POST',
                        dataType: 'JSON',
                        success: function(result) {
                            if (result.status == 1) {
                                Swal.fire({
                                    title: "Database",
                                    text: "Berhasil di reset!",
                                    icon: "success"
                                });
                            } else {
                                Swal.fire({
                                    title: "Database",
                                    text: "Gagal di reset!",
                                    icon: "info"
                                });
                            }
                        },
                        error: function(result) { // jika fungsi error
                            // jalankan fungsi error
                            error_proccess();
                        }
                    });
                }
            });
        }

        function display_c() {
            var refresh = 1000; // Refresh rate in milli seconds
            mytime = setTimeout('display_ct()', refresh)
        }

        function display_ct() {
            var x = new Date();

            // Array nama hari dalam Bahasa Indonesia
            var days = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];

            // Array nama bulan dalam Bahasa Indonesia
            var months = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

            // Mendapatkan nama hari
            var dayName = days[x.getDay()];

            // Mendapatkan tanggal, bulan, dan tahun
            var day = x.getDate();
            var month = months[x.getMonth()];
            var year = x.getFullYear();

            // Mendapatkan jam, menit, dan detik
            var hours = x.getHours();
            var minutes = x.getMinutes();
            var seconds = x.getSeconds();

            // Format waktu
            var x1 = hours + ":" + minutes + ":" + seconds + " / " + dayName + ", " + day + " " + month + " " + year;

            // Menampilkan waktu pada elemen dengan id 'time'
            document.getElementById('time').innerHTML = x1;
            setTimeout(display_ct, 1000); // Memperbarui setiap detik
        }


        // fungsi hyperlink js
        function getUrl(url) {
            location.href = siteUrl + url;
        }

        // fungsi tampil/sembunyi password
        function pass() {
            if (document.getElementById("password").type == "password") { // jika icon password gembok di klik
                // ubah tipe password menjadi text
                document.getElementById("password").type = "text";

                // tampilkan icon buka
                $("#open_pass").show();

                // sembunyikan icon gembok
                $("#lock_pass").hide();
            } else { // selain itu
                // ubah tipe password menjadi passwword
                document.getElementById("password").type = "password";
                // sembunyikan icon buka
                $("#open_pass").hide();

                // tampilkan icon gembok
                $("#lock_pass").show();
            }
        }

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

        // notifikasi error
        function error_proccess() {
            Swal.fire({
                title: "Error",
                text: "Error dalam pemrosesan!",
                icon: "error"
            });
            return;
        }

        // uppercase
        function upperCase(params, forid) {
            $('#' + forid).val(params.toUpperCase())
        }

        // huruf besar diawal kata
        function ubah_nama(nama, forid) {
            // var nama_barang = nama.charAt(0).toUpperCase() + nama.slice(1);
            str = nama.toLowerCase().replace(/\b[a-z]/g, function(letter) {
                return letter.toUpperCase();
            });
            $("#" + forid).val(str);
        }

        // fungsi cek value harus berupa email
        function validateEmail(email) {
            var re = /\S+@\S+\.\S+/;
            return re.test(email);
        }

        // cek email berdsasarkan email
        function cekEmail(forid) {
            if (validateEmail($('#' + forid).val()) == false) {

                Swal.fire("Email", "Format sudah valid?", "question");
                return;
            }
        }

        // kirim data via email
        function send_data_mail(param) {
            Swal.fire({
                title: "Masukan Email",
                input: "text",
                inputAttributes: {
                    autocapitalize: "off"
                },
                showCancelButton: true,
                confirmButtonText: "Kirim",
                cancelButtonText: "Tutup",
                showLoaderOnConfirm: true,
                preConfirm: async (email) => {
                    try {
                        const githubUrl = `${siteUrl}Auth/email/?param=${param}&email=${email}`;
                        const response = await fetch(githubUrl);
                        if (!response.ok) {
                            return Swal.showValidationMessage(`${JSON.stringify(await response.json())}`);
                        }
                        return response.json();
                    } catch (error) {
                        Swal.showValidationMessage(`Request failed: ${error}`);
                    }
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    if (result.value.status == 1) {
                        Swal.fire("Data", "Berhasil dikirim via Email!, silahkan cek email", "success");
                    } else {
                        Swal.fire("Data", "Gagal dikirim via Email!, silahkan coba lagi", "info");
                    }
                }
            });
        }

        // cek panjang karakter
        function cekLength(param, forid) {
            if (forid == 'kodepos' || forid == 'kode_pos') { // jika id nya kodepos

                // jalankan fungsi
                if (param.length > 5) { // jika panjang karakter lebih dari 5
                    // munculkan notif
                    Swal.fire('Kode Pos', "Maksimal 5 digit", "question");
                }

                // ambil 5 karakter dari depan lalu lempar ke id-nya
                $('#' + forid).val(param.slice(0, 5));
            } else if (forid == 'nik') { // jika id nya nik

                // jalankan fungsi
                if (param.length != 16) { // jika panjang karakter lebih dari 5
                    // munculkan notif
                    Swal.fire('NIK', "Harus 16 digit", "question");
                }

                // ambil 5 karakter dari depan lalu lempar ke id-nya
                $('#' + forid).val(param.slice(0, 16));

            } else if (forid == 'npwp') { // jika id nya npwp

                // jalankan fungsi
                if (param.length != 16) { // jika panjang karakter lebih dari 5
                    // munculkan notif
                    Swal.fire('NPWP', "Harus 16 digit", "question");
                }

                // ambil 5 karakter dari depan lalu lempar ke id-nya
                $('#' + forid).val(param.slice(0, 16));

            } else if (forid == 'sip') { // jika id nya sip

                // jalankan fungsi
                if (param.length != 15) { // jika panjang karakter lebih dari 5
                    // munculkan notif
                    Swal.fire('SIP', "Harus 15 digit", "question");
                }

                // ambil 5 karakter dari depan lalu lempar ke id-nya
                $('#' + forid).val(param.slice(0, 15));

            }
        }

        // fungsi ambil alamat
        function getAddress(param, forid) {
            // ambil karakter by forid (nik)
            var prov = param.slice(0, 2);
            var kot = param.slice(0, 4);
            var kec = param.slice(0, 6);

            // jalankan fungsi
            showAddress(prov, 'provinsi');
            showAddress(kot, 'kabupaten');
            showAddress(kec, 'kecamatan');
        }

        // fungsi menampilkan isi address
        function showAddress(param, forid) {

            if (param == '' || param == null || forid == '' || forid == null) {
                return Swal.fire('Kesalahan', "Terdapat kesalahan saat memuat!, coba lagi", "question");
            }

            if (forid == 'provinsi') { // jika forid = provinsi
                // isi table menjadi m_provinsi
                forid2 = 'm_provinsi';
            } else { // selain itu
                // isi table berdasarkan lemparan
                forid2 = forid;
            }

            // jalankan fungsi
            $.ajax({
                url: siteUrl + 'Master_show/getInfo/' + forid2 + '/' + param,
                type: 'POST',
                dataType: 'JSON',
                success: function(result) { // jika fungsi berjalan
                    $('#' + forid).html(`<option value="${result.id}">${result.text}</option>`);
                },
                error: function(result) { // jika fungsi error
                    // jalankan fungsi error
                    error_proccess();
                }
            });
        }

        // fungsi format Rupiah
        function formatRp(num, forid) {
            num = num.toString().replace(/\$|\,/g, '');

            num = Math.ceil(num);

            if (isNaN(num)) num = "0";

            sign = (num == (num = Math.abs(num)));
            num = Math.floor(num * 100 + 0.50000000001);
            cents = num % 100;
            num = Math.floor(num / 100).toString();

            if (cents < 10) cents = "0" + cents;

            for (var i = 0; i < Math.floor((num.length - (1 + i)) / 3); i++) {
                num = num.substring(0, num.length - (4 * i + 3)) + ',' +
                    num.substring(num.length - (4 * i + 3));
            }

            var result = (((sign) ? '' : '-') + '' + num);
            $('#' + forid).val(result);
        }

        // fungsi format Rupiah NoId
        function formatRpNoId(num) {
            num = num.toString().replace(/\$|\,/g, '');

            num = Math.ceil(num);

            if (isNaN(num)) num = "0";

            sign = (num == (num = Math.abs(num)));
            num = Math.floor(num * 100 + 0.50000000001);
            cents = num % 100;
            num = Math.floor(num / 100).toString();

            if (cents < 10) cents = "0" + cents;

            for (var i = 0; i < Math.floor((num.length - (1 + i)) / 3); i++) {
                num = num.substring(0, num.length - (4 * i + 3)) + ',' +
                    num.substring(num.length - (4 * i + 3));
            }

            return (((sign) ? '' : '-') + '' + num);
        }

        // fungsi preview
        function preview(url) {
            if (url == 'pendaftaran') {
                var poli_pendaftaran = $('#kode_poli').val()
                var tgl_dari_pendaftaran = $('#dari').val()
                var tgl_sampai_pendaftaran = $('#sampai').val()
            } else if ((url == 'kasir') || (url == 'barang_po_in') || (url == 'barang_in') || (url == 'barang_in_retur') || (url == 'barang_out') || (url == 'barang_out_retur') || (url == 'penyesuaian_stok') || (url == 'mutasi_po') || (url == 'mutasi')) {
                var poli_pendaftaran = ''
                var tgl_dari_pendaftaran = $('#dari').val()
                var tgl_sampai_pendaftaran = $('#sampai').val()
            } else {
                var poli_pendaftaran = ''
                var tgl_dari_pendaftaran = ''
                var tgl_sampai_pendaftaran = ''
            }

            var param = `?poli=${poli_pendaftaran}&dari=${tgl_dari_pendaftaran}&sampai=${tgl_sampai_pendaftaran}`
            window.open(`${siteUrl}Report/${url}/0${param}`, '_blank');
        }

        // fungsi print
        function print(url) {
            if (url == 'pendaftaran') {
                var poli_pendaftaran = $('#kode_poli').val()
                var tgl_dari_pendaftaran = $('#dari').val()
                var tgl_sampai_pendaftaran = $('#sampai').val()
            } else if ((url == 'kasir') || (url == 'barang_po_in') || (url == 'barang_in') || (url == 'barang_in_retur') || (url == 'barang_out') || (url == 'barang_out_retur') || (url == 'penyesuaian_stok') || (url == 'mutasi_po') || (url == 'mutasi')) {
                var poli_pendaftaran = ''
                var tgl_dari_pendaftaran = $('#dari').val()
                var tgl_sampai_pendaftaran = $('#sampai').val()
            } else {
                var poli_pendaftaran = ''
                var tgl_dari_pendaftaran = ''
                var tgl_sampai_pendaftaran = ''
            }

            var param = `?poli=${poli_pendaftaran}&dari=${tgl_dari_pendaftaran}&sampai=${tgl_sampai_pendaftaran}`
            window.open(`${siteUrl}Report/${url}/1${param}`, '_blank');
        }

        // fungsi export excel
        function excel(url) {
            if (url == 'pendaftaran') {
                var poli_pendaftaran = $('#kode_poli').val()
                var tgl_dari_pendaftaran = $('#dari').val()
                var tgl_sampai_pendaftaran = $('#sampai').val()
            } else if ((url == 'kasir') || (url == 'barang_po_in') || (url == 'barang_in') || (url == 'barang_in_retur') || (url == 'barang_out') || (url == 'barang_out_retur') || (url == 'penyesuaian_stok') || (url == 'mutasi_po') || (url == 'mutasi')) {
                var poli_pendaftaran = ''
                var tgl_dari_pendaftaran = $('#dari').val()
                var tgl_sampai_pendaftaran = $('#sampai').val()
            } else {
                var poli_pendaftaran = ''
                var tgl_dari_pendaftaran = ''
                var tgl_sampai_pendaftaran = ''
            }

            var param = `?poli=${poli_pendaftaran}&dari=${tgl_dari_pendaftaran}&sampai=${tgl_sampai_pendaftaran}`
            window.open(`${siteUrl}Report/${url}/2${param}`, '_blank');
        }

        function printsingle(url) {
            window.open(`${siteUrl}${url}/1`, '_blank');
        }

        // datatable
        $('#tableSederhana').DataTable({
            "destroy": true,
            "processing": true,
            "responsive": true,
            "serverSide": false,
            "scrollCollapse": false,
            "paging": true,
            "oLanguage": {
                "sEmptyTable": "<div class='text-center'>Data Kosong</div>",
                "sInfoEmpty": "",
                "sInfoFiltered": "",
                "sSearch": "",
                "sSearchPlaceholder": "Cari data...",
                "sInfo": " Jumlah _TOTAL_ Data (_START_ - _END_)",
                "sLengthMenu": "_MENU_ Baris",
                "sZeroRecords": "<div class='text-center'>Data Kosong</div>",
                "oPaginate": {
                    "sPrevious": "Sebelumnya",
                    "sNext": "Berikutnya"
                }
            },
            "aLengthMenu": [
                [10, 25, 50, 75, 100, -1],
                [10, 25, 50, 75, 100, "Semua"]
            ],
            "columnDefs": [{
                "targets": [-1],
                "orderable": false,
            }, ],
        });

        $('#tableNonSearch').DataTable({
            "destroy": true,
            "processing": true,
            "responsive": true,
            "serverSide": false,
            "scrollCollapse": false,
            "paging": true,
            "searching": false,
            "oLanguage": {
                "sEmptyTable": "<div class='text-center'>Data Kosong</div>",
                "sInfoEmpty": "",
                "sInfoFiltered": "",
                "sSearch": "",
                "sInfo": " Jumlah _TOTAL_ Data (_START_ - _END_)",
                "sLengthMenu": "_MENU_ Baris",
                "sZeroRecords": "<div class='text-center'>Data Kosong</div>",
                "oPaginate": {
                    "sPrevious": "Sebelumnya",
                    "sNext": "Berikutnya"
                }
            },
            "aLengthMenu": [
                [5, 20, 50, 75, 100, -1],
                [5, 20, 50, 75, 100, "Semua"]
            ],
            "columnDefs": [{
                "targets": [-1],
                "orderable": false,
            }, ],
        });

        <?php if (!empty($list_data)) : ?>
            if (table) {
                table.DataTable({
                    "destroy": true,
                    "processing": true,
                    "responsive": true,
                    "serverSide": true,
                    "order": [],
                    "ajax": {
                        "url": `${siteUrl}${'<?= $list_data ?>/' + '<?= $param1 ?>'}`,
                        "type": "POST",
                    },
                    "scrollCollapse": false,
                    <?php if ($this->uri->segment(1) !== 'Accounting') : ?> "paging": true,
                    <?php else : ?> "paging": false,
                    <?php endif; ?> "language": {
                        "emptyTable": "<div class='text-center'>Data Kosong</div>",
                        "infoEmpty": "",
                        "infoFiltered": "",
                        "search": "",
                        "searchPlaceholder": "Cari data...",
                        "info": " Jumlah _TOTAL_ Data (_START_ - _END_)",
                        "lengthMenu": "_MENU_ Baris",
                        "zeroRecords": "<div class='text-center'>Data Kosong</div>",
                        "paginate": {
                            "previous": "Sebelumnya",
                            "next": "Berikutnya"
                        }
                    },
                    "lengthMenu": [
                        [10, 25, 50, 75, 100, -1],
                        [10, 25, 50, 75, 100, "Semua"]
                    ],
                    "columnDefs": [{
                        "targets": [-1],
                        "orderable": false,
                    }],
                });
            }

            // fungsi filter tanggal dan parameter jika ada (jika tidak ada di kosongkan)
            function filter(x = '', y = '') {
                var dari = $('#dari').val();
                var sampai = $('#sampai').val();

                if (x == '' || x == null) {
                    var parameterString = `2~${dari}~${sampai}`;
                } else {
                    var parameterString = `2~${dari}~${sampai}/${x}/${y}`;
                }

                table.DataTable().ajax.url(siteUrl + '<?= $list_data ?>' + parameterString).load();
            }
        <?php endif; ?>

        function reloadTable() {
            // if ($.fn.DataTable.isDataTable(table)) {
            table.DataTable().ajax.reload(null, false);
            // }
        }

        // fungsi select2 global
        // inisial
        initailizeSelect2_prefix();
        initailizeSelect2_pajak();
        initailizeSelect2_provinsi();
        initailizeSelect2_kabupaten(param = '');
        initailizeSelect2_kecamatan(param = '');
        initailizeSelect2_member("<?= (($this->uri->segment(1) == 'Health') ? 'Health' : 'Transaksi') ?>");
        initailizeSelect2_user();
        initailizeSelect2_user_all();
        initailizeSelect2_poli();
        initailizeSelect2_jenis_bayar();
        initailizeSelect2_tindakan();
        initailizeSelect2_kelas();
        initailizeSelect2_dokter_poli(param = 'POL0000001');
        initailizeSelect2_poli_dokter(param = '');
        initailizeSelect2_dokter_all();
        initailizeSelect2_ruang();
        initailizeSelect2_ruang_jd(kode_poli = '', hari = '', kode_cabang = '');
        initailizeSelect2_bed(param = '');
        initailizeSelect2_supplier();
        initailizeSelect2_gudang_int();
        initailizeSelect2_gudang_log();
        initailizeSelect2_pekerjaan();
        initailizeSelect2_agama();
        initailizeSelect2_pendidikan();
        initailizeSelect2_pendaftaran('');
        initailizeSelect2_penjualan();
        initailizeSelect2_penjualan_retur();
        initailizeSelect2_bank();
        initailizeSelect2_tipe_bank();
        initailizeSelect2_jual_for_retur();
        initailizeSelect2_promo(min_buy = '0');
        initailizeSelect2_barang();
        initailizeSelect2_kas_bank();
        initailizeSelect2_kategori_tarif();
        initailizeSelect2_all_cabang();
        initailizeSelect2_tarif_paket();
        initailizeSelect2_tarif_single();
        initailizeSelect2_tarif_singlex();
        initailizeSelect2_tarif_paketx();
        initailizeSelect2_tindakan_single_master();
        initailizeSelect2_paket_tindakan(bayar = 'JB00000001', kelas = 'Umum', poli = 'POL0000001');
        initailizeSelect2_tindakan_single(bayar = 'JB00000001', kelas = 'Umum', poli = 'POL0000001');
        initailizeSelect2_tindakan_single_lab(bayar = 'JB00000001', kelas = 'Umum', poli = 'POL0000001');
        initailizeSelect2_tindakan_single_rad(bayar = 'JB00000001', kelas = 'Umum', poli = 'POL0000001');
        initailizeSelect2_terdaftar();
        initailizeSelect2_klasifikasi_akun();
        initailizeSelect2_akun_sel(param = '');
        initailizeSelect2_barang_stok();
        initailizeSelect2_icd9();
        initailizeSelect2_icd10();
        initailizeSelect2_cara_masuk();
        initailizeSelect2_group_coa();
        initailizeSelect2_master_coa();

        // fungsi
        function initailizeSelect2_icd9() {
            // jalan fungsi select2 asli
            $(".select2_icd9").select2({
                allowClear: true,
                multiple: false,
                placeholder: '~ Pilih ICD 9',
                dropdownAutoWidth: true,
                width: '100%',
                language: {
                    inputTooShort: function() {
                        return 'Ketikan Nomor minimal 1 huruf';
                    },
                    noResults: function() {
                        return 'Data Tidak Ditemukan';
                    }
                },
                ajax: {
                    url: siteUrl + 'Select2_master/dataIcd9/',
                    type: 'POST',
                    dataType: 'JSON',
                    delay: 100,
                    data: function(result) {
                        return {
                            searchTerm: result.term
                        };
                    },

                    processResults: function(result) {
                        return {
                            results: result
                        };
                    },
                    cache: true
                }
            });
        }

        function initailizeSelect2_icd10() {
            // jalan fungsi select2 asli
            $(".select2_icd10").select2({
                allowClear: true,
                multiple: false,
                placeholder: '~ Pilih ICD 10',
                dropdownAutoWidth: true,
                width: '100%',
                language: {
                    inputTooShort: function() {
                        return 'Ketikan Nomor minimal 1 huruf';
                    },
                    noResults: function() {
                        return 'Data Tidak Ditemukan';
                    }
                },
                ajax: {
                    url: siteUrl + 'Select2_master/dataIcd10/',
                    type: 'POST',
                    dataType: 'JSON',
                    delay: 100,
                    data: function(result) {
                        return {
                            searchTerm: result.term
                        };
                    },

                    processResults: function(result) {
                        return {
                            results: result
                        };
                    },
                    cache: true
                }
            });
        }

        function initailizeSelect2_cara_masuk() {
            // jalan fungsi select2 asli
            $(".select2_cara_masuk").select2({
                allowClear: true,
                multiple: false,
                placeholder: '~ Pilih Cara Masuk',
                dropdownAutoWidth: true,
                width: '100%',
                language: {
                    inputTooShort: function() {
                        return 'Ketikan Nomor minimal 1 huruf';
                    },
                    noResults: function() {
                        return 'Data Tidak Ditemukan';
                    }
                },
                ajax: {
                    url: siteUrl + 'Select2_master/dataCaraMasuk/',
                    type: 'POST',
                    dataType: 'JSON',
                    delay: 100,
                    data: function(result) {
                        return {
                            searchTerm: result.term
                        };
                    },

                    processResults: function(result) {
                        return {
                            results: result
                        };
                    },
                    cache: true
                }
            });
        }

        function initailizeSelect2_group_coa() {
            // jalan fungsi select2 asli
            $(".select2_group_coa").select2({
                allowClear: true,
                multiple: false,
                placeholder: '~ Pilih Group Coa',
                dropdownAutoWidth: true,
                width: '100%',
                language: {
                    inputTooShort: function() {
                        return 'Ketikan Nomor minimal 1 huruf';
                    },
                    noResults: function() {
                        return 'Data Tidak Ditemukan';
                    }
                },
                ajax: {
                    url: siteUrl + 'Select2_master/dataGroupCoa/',
                    type: 'POST',
                    dataType: 'JSON',
                    delay: 100,
                    data: function(result) {
                        return {
                            searchTerm: result.term
                        };
                    },

                    processResults: function(result) {
                        return {
                            results: result
                        };
                    },
                    cache: true
                }
            });
        }

        function initailizeSelect2_master_coa() {
            // jalan fungsi select2 asli
            $(".select2_master_coa").select2({
                allowClear: true,
                multiple: false,
                placeholder: '~ Pilih Master Coa',
                dropdownAutoWidth: true,
                width: '100%',
                language: {
                    inputTooShort: function() {
                        return 'Ketikan Nomor minimal 1 huruf';
                    },
                    noResults: function() {
                        return 'Data Tidak Ditemukan';
                    }
                },
                ajax: {
                    url: siteUrl + 'Select2_master/dataMasterCoa/',
                    type: 'POST',
                    dataType: 'JSON',
                    delay: 100,
                    data: function(result) {
                        return {
                            searchTerm: result.term
                        };
                    },

                    processResults: function(result) {
                        return {
                            results: result
                        };
                    },
                    cache: true
                }
            });
        }

        function initailizeSelect2_barang_stok() {
            // jalan fungsi select2 asli
            $(".select2_barang_stok").select2({
                allowClear: true,
                multiple: false,
                placeholder: '~ Pilih Barang',
                dropdownAutoWidth: true,
                width: '100%',
                language: {
                    inputTooShort: function() {
                        return 'Ketikan Nomor minimal 1 huruf';
                    },
                    noResults: function() {
                        return 'Data Tidak Ditemukan';
                    }
                },
                ajax: {
                    url: siteUrl + 'Select2_master/dataBarangStok/',
                    type: 'POST',
                    dataType: 'JSON',
                    delay: 100,
                    data: function(result) {
                        return {
                            searchTerm: result.term
                        };
                    },

                    processResults: function(result) {
                        return {
                            results: result
                        };
                    },
                    cache: true
                }
            });
        }

        function initailizeSelect2_akun_sel(param) {
            if (param == '' || param == null || param == 'null') { // jika parameter kosong/ null
                // jalankan fungsi select2_default
                return select2_default('select2_akun_sel');
            }
            // jalan fungsi select2 asli
            $(".select2_akun_sel").select2({
                allowClear: true,
                multiple: false,
                placeholder: '~ Pilih Akun',
                dropdownAutoWidth: true,
                width: '100%',
                language: {
                    inputTooShort: function() {
                        return 'Ketikan Nomor minimal 1 huruf';
                    },
                    noResults: function() {
                        return 'Data Tidak Ditemukan';
                    }
                },
                ajax: {
                    url: siteUrl + 'Select2_master/dataAkunSel/' + param,
                    type: 'POST',
                    dataType: 'JSON',
                    delay: 100,
                    data: function(result) {
                        return {
                            searchTerm: result.term
                        };
                    },

                    processResults: function(result) {
                        return {
                            results: result
                        };
                    },
                    cache: true
                }
            });
        }

        function initailizeSelect2_klasifikasi_akun() {
            // jalan fungsi select2 asli
            $(".select2_klasifikasi_akun").select2({
                allowClear: true,
                multiple: false,
                placeholder: '~ Pilih Klasifikasi',
                dropdownAutoWidth: true,
                width: '100%',
                language: {
                    inputTooShort: function() {
                        return 'Ketikan Nomor minimal 1 huruf';
                    },
                    noResults: function() {
                        return 'Data Tidak Ditemukan';
                    }
                },
                ajax: {
                    url: siteUrl + 'Select2_master/dataKlasifikasiAkun/',
                    type: 'POST',
                    dataType: 'JSON',
                    delay: 100,
                    data: function(result) {
                        return {
                            searchTerm: result.term
                        };
                    },

                    processResults: function(result) {
                        return {
                            results: result
                        };
                    },
                    cache: true
                }
            });
        }

        function initailizeSelect2_terdaftar() {
            // jalan fungsi select2 asli
            $(".select2_terdaftar").select2({
                allowClear: true,
                multiple: false,
                placeholder: '~ Pilih Cabang',
                dropdownAutoWidth: true,
                width: '100%',
                language: {
                    inputTooShort: function() {
                        return 'Ketikan Nomor minimal 1 huruf';
                    },
                    noResults: function() {
                        return 'Data Tidak Ditemukan';
                    }
                },
                ajax: {
                    url: siteUrl + 'Select2_master/dataTerdaftar/',
                    type: 'POST',
                    dataType: 'JSON',
                    delay: 100,
                    data: function(result) {
                        return {
                            searchTerm: result.term
                        };
                    },

                    processResults: function(result) {
                        return {
                            results: result
                        };
                    },
                    cache: true
                }
            });
        }

        function initailizeSelect2_tarif_single() {
            // jalan fungsi select2 asli
            $(".select2_tarif_single").select2({
                allowClear: true,
                multiple: false,
                placeholder: '~ Pilih Tarif Single',
                dropdownAutoWidth: true,
                width: '100%',
                language: {
                    inputTooShort: function() {
                        return 'Ketikan Nomor minimal 1 huruf';
                    },
                    noResults: function() {
                        return 'Data Tidak Ditemukan';
                    }
                },
                ajax: {
                    url: siteUrl + 'Select2_master/dataTarifSingle/',
                    type: 'POST',
                    dataType: 'JSON',
                    delay: 100,
                    data: function(result) {
                        return {
                            searchTerm: result.term
                        };
                    },

                    processResults: function(result) {
                        return {
                            results: result
                        };
                    },
                    cache: true
                }
            });
        }

        function initailizeSelect2_tarif_singlex() {
            // jalan fungsi select2 asli
            $(".select2_tarif_singlex").select2({
                allowClear: true,
                multiple: false,
                placeholder: '~ Pilih Tarif Single',
                dropdownAutoWidth: true,
                width: '100%',
                language: {
                    inputTooShort: function() {
                        return 'Ketikan Nomor minimal 1 huruf';
                    },
                    noResults: function() {
                        return 'Data Tidak Ditemukan';
                    }
                },
                ajax: {
                    url: siteUrl + 'Select2_master/dataTarifSinglex/',
                    type: 'POST',
                    dataType: 'JSON',
                    delay: 100,
                    data: function(result) {
                        return {
                            searchTerm: result.term
                        };
                    },

                    processResults: function(result) {
                        return {
                            results: result
                        };
                    },
                    cache: true
                }
            });
        }

        function initailizeSelect2_tindakan_single_master() {
            // jalan fungsi select2 asli
            $(".select2_tindakan_single_master").select2({
                allowClear: true,
                multiple: false,
                placeholder: '~ Pilih Tindakan',
                dropdownAutoWidth: true,
                width: '100%',
                language: {
                    inputTooShort: function() {
                        return 'Ketikan Nomor minimal 1 huruf';
                    },
                    noResults: function() {
                        return 'Data Tidak Ditemukan';
                    }
                },
                ajax: {
                    url: siteUrl + 'Select2_master/dataTindakanMasterx',
                    type: 'POST',
                    dataType: 'JSON',
                    delay: 100,
                    data: function(result) {
                        return {
                            searchTerm: result.term
                        };
                    },

                    processResults: function(result) {
                        return {
                            results: result
                        };
                    },
                    cache: true
                }
            });
        }

        function initailizeSelect2_paket_tindakan(bayar, kelas, poli) {
            // jalan fungsi select2 asli
            $(".select2_paket_tindakan").select2({
                allowClear: true,
                multiple: false,
                placeholder: '~ Pilih Paket Tindakan',
                dropdownAutoWidth: true,
                width: '100%',
                language: {
                    inputTooShort: function() {
                        return 'Ketikan Nomor minimal 1 huruf';
                    },
                    noResults: function() {
                        return 'Data Tidak Ditemukan';
                    }
                },
                ajax: {
                    url: siteUrl + 'Select2_master/dataPaketTindakan/' + bayar + '/' + kelas + '/' + poli,
                    type: 'POST',
                    dataType: 'JSON',
                    delay: 100,
                    data: function(result) {
                        return {
                            searchTerm: result.term
                        };
                    },

                    processResults: function(result) {
                        return {
                            results: result
                        };
                    },
                    cache: true
                }
            });
        }

        function initailizeSelect2_tindakan_single(bayar, kelas, poli) {
            // jalan fungsi select2 asli
            $(".select2_tindakan_single").select2({
                allowClear: true,
                multiple: false,
                placeholder: '~ Pilih Tindakan Single',
                dropdownAutoWidth: true,
                width: '100%',
                language: {
                    inputTooShort: function() {
                        return 'Ketikan Nomor minimal 1 huruf';
                    },
                    noResults: function() {
                        return 'Data Tidak Ditemukan';
                    }
                },
                ajax: {
                    url: siteUrl + 'Select2_master/dataTindakanMaster/' + bayar + '/' + kelas + '/' + poli + '/1/',
                    type: 'POST',
                    dataType: 'JSON',
                    delay: 100,
                    data: function(result) {
                        return {
                            searchTerm: result.term
                        };
                    },

                    processResults: function(result) {
                        return {
                            results: result
                        };
                    },
                    cache: true
                }
            });
        }

        function initailizeSelect2_tindakan_single_lab(bayar, kelas, poli) {
            // jalan fungsi select2 asli
            $(".select2_tindakan_single_lab").select2({
                allowClear: true,
                multiple: false,
                placeholder: '~ Pilih Tindakan Single Lab',
                dropdownAutoWidth: true,
                width: '100%',
                language: {
                    inputTooShort: function() {
                        return 'Ketikan Nomor minimal 1 huruf';
                    },
                    noResults: function() {
                        return 'Data Tidak Ditemukan';
                    }
                },
                ajax: {
                    url: siteUrl + 'Select2_master/dataTindakanMasterLab/' + bayar + '/' + kelas + '/' + poli + '/1/',
                    type: 'POST',
                    dataType: 'JSON',
                    delay: 100,
                    data: function(result) {
                        return {
                            searchTerm: result.term
                        };
                    },

                    processResults: function(result) {
                        return {
                            results: result
                        };
                    },
                    cache: true
                }
            });
        }

        function initailizeSelect2_tindakan_single_rad(bayar, kelas, poli) {
            // jalan fungsi select2 asli
            $(".select2_tindakan_single_rad").select2({
                allowClear: true,
                multiple: false,
                placeholder: '~ Pilih Tindakan Single Rad',
                dropdownAutoWidth: true,
                width: '100%',
                language: {
                    inputTooShort: function() {
                        return 'Ketikan Nomor minimal 1 huruf';
                    },
                    noResults: function() {
                        return 'Data Tidak Ditemukan';
                    }
                },
                ajax: {
                    url: siteUrl + 'Select2_master/dataTindakanMasterRad/' + bayar + '/' + kelas + '/' + poli + '/1/',
                    type: 'POST',
                    dataType: 'JSON',
                    delay: 100,
                    data: function(result) {
                        return {
                            searchTerm: result.term
                        };
                    },

                    processResults: function(result) {
                        return {
                            results: result
                        };
                    },
                    cache: true
                }
            });
        }

        function initailizeSelect2_tarif_paketx() {
            // jalan fungsi select2 asli
            $(".select2_tarif_paketx").select2({
                allowClear: true,
                multiple: false,
                placeholder: '~ Pilih Tarif Paket',
                dropdownAutoWidth: true,
                width: '100%',
                language: {
                    inputTooShort: function() {
                        return 'Ketikan Nomor minimal 1 huruf';
                    },
                    noResults: function() {
                        return 'Data Tidak Ditemukan';
                    }
                },
                ajax: {
                    url: siteUrl + 'Select2_master/dataTarifPaketx/',
                    type: 'POST',
                    dataType: 'JSON',
                    delay: 100,
                    data: function(result) {
                        return {
                            searchTerm: result.term
                        };
                    },

                    processResults: function(result) {
                        return {
                            results: result
                        };
                    },
                    cache: true
                }
            });
        }

        function initailizeSelect2_tarif_paket() {
            // jalan fungsi select2 asli
            $(".select2_tarif_paket").select2({
                allowClear: true,
                multiple: false,
                placeholder: '~ Pilih Cabang',
                dropdownAutoWidth: true,
                width: '100%',
                language: {
                    inputTooShort: function() {
                        return 'Ketikan Nomor minimal 1 huruf';
                    },
                    noResults: function() {
                        return 'Data Tidak Ditemukan';
                    }
                },
                ajax: {
                    url: siteUrl + 'Select2_master/dataTarifPaket/',
                    type: 'POST',
                    dataType: 'JSON',
                    delay: 100,
                    data: function(result) {
                        return {
                            searchTerm: result.term
                        };
                    },

                    processResults: function(result) {
                        return {
                            results: result
                        };
                    },
                    cache: true
                }
            });
        }

        function initailizeSelect2_all_cabang() {
            $(".select2_all_cabang").select2({
                allowClear: true,
                multiple: false,
                placeholder: '~ Pilih Cabang',
                dropdownAutoWidth: true,
                width: '100%',
                language: {
                    inputTooShort: function() {
                        return 'Ketikan Nomor minimal 1 huruf';
                    },
                    noResults: function() {
                        return 'Data Tidak Ditemukan';
                    }
                },
                ajax: {
                    url: siteUrl + 'Select2_master/dataAllCabang/',
                    type: 'POST',
                    dataType: 'JSON',
                    delay: 100,
                    data: function(result) {
                        return {
                            searchTerm: result.term
                        };
                    },

                    processResults: function(result) {
                        return {
                            results: result
                        };
                    },
                    cache: true
                }
            });
        }

        function initailizeSelect2_kategori_tarif() {
            $(".select2_kategori_tarif").select2({
                allowClear: true,
                multiple: false,
                placeholder: '~ Pilih Kategori',
                //minimumInputLength: 2,
                dropdownAutoWidth: true,
                width: '100%',
                language: {
                    inputTooShort: function() {
                        return 'Ketikan Nomor minimal 2 huruf';
                    },
                    noResults: function() {
                        return 'Data Tidak Ditemukan';
                    }
                },
                ajax: {
                    url: siteUrl + 'Select2_master/dataKatTarif',
                    type: 'POST',
                    dataType: 'JSON',
                    delay: 100,
                    data: function(result) {
                        return {
                            searchTerm: result.term
                        };
                    },

                    processResults: function(result) {
                        return {
                            results: result
                        };
                    },
                    cache: true
                }
            });
        }

        function initailizeSelect2_kas_bank() {
            $(".select2_kas_bank").select2({
                allowClear: true,
                multiple: false,
                placeholder: '~ Pilih Barang',
                //minimumInputLength: 2,
                dropdownAutoWidth: true,
                width: '100%',
                language: {
                    inputTooShort: function() {
                        return 'Ketikan Nomor minimal 2 huruf';
                    },
                    noResults: function() {
                        return 'Data Tidak Ditemukan';
                    }
                },
                ajax: {
                    url: siteUrl + 'Select2_master/dataKasBank',
                    type: 'POST',
                    dataType: 'JSON',
                    delay: 100,
                    data: function(result) {
                        return {
                            searchTerm: result.term
                        };
                    },

                    processResults: function(result) {
                        return {
                            results: result
                        };
                    },
                    cache: true
                }
            });
        }

        function initailizeSelect2_barang() {
            $(".select2_barang").select2({
                allowClear: true,
                multiple: false,
                placeholder: '~ Pilih Barang',
                //minimumInputLength: 2,
                dropdownAutoWidth: true,
                width: '100%',
                language: {
                    inputTooShort: function() {
                        return 'Ketikan Nomor minimal 2 huruf';
                    },
                    noResults: function() {
                        return 'Data Tidak Ditemukan';
                    }
                },
                ajax: {
                    url: siteUrl + 'Select2_master/dataBarang',
                    type: 'POST',
                    dataType: 'JSON',
                    delay: 100,
                    data: function(result) {
                        return {
                            searchTerm: result.term
                        };
                    },

                    processResults: function(result) {
                        return {
                            results: result
                        };
                    },
                    cache: true
                }
            });
        }

        function initailizeSelect2_prefix() {
            $(".select2-prefix").select2({
                allowClear: true,
                multiple: false,
                placeholder: '~ Pilih Prefix',
                //minimumInputLength: 2,
                dropdownAutoWidth: true,
                width: '100%',
                language: {
                    inputTooShort: function() {
                        return 'Ketikan Nomor minimal 2 huruf';
                    },
                    noResults: function() {
                        return 'Data Tidak Ditemukan';
                    }
                },
                ajax: {
                    url: siteUrl + 'Select2_master/dataPrefix',
                    type: 'POST',
                    dataType: 'JSON',
                    delay: 100,
                    data: function(result) {
                        return {
                            searchTerm: result.term
                        };
                    },

                    processResults: function(result) {
                        return {
                            results: result
                        };
                    },
                    cache: true
                }
            });
        }

        function initailizeSelect2_pajak() {
            $(".select2_pajak").select2({
                allowClear: true,
                multiple: false,
                placeholder: '~ Pilih Pajak',
                //minimumInputLength: 2,
                dropdownAutoWidth: true,
                width: '100%',
                language: {
                    inputTooShort: function() {
                        return 'Ketikan Nomor minimal 2 huruf';
                    },
                    noResults: function() {
                        return 'Data Tidak Ditemukan';
                    }
                },
                ajax: {
                    url: siteUrl + 'Select2_master/dataPajak',
                    type: 'POST',
                    dataType: 'JSON',
                    delay: 100,
                    data: function(result) {
                        return {
                            searchTerm: result.term
                        };
                    },

                    processResults: function(result) {
                        return {
                            results: result
                        };
                    },
                    cache: true
                }
            });
        }

        function select2_default(param) {
            var mymessage = "Data tidak ditemukan";
            $("." + param).select2({
                placeholder: $(this).data('placeholder'),
                width: '100%',
                language: {
                    noResults: function() {
                        return mymessage;
                    }
                },
            });
        }

        function initailizeSelect2_provinsi() {
            $(".select2_provinsi").select2({
                allowClear: true,
                multiple: false,
                placeholder: '~ Pilih Provinsi',
                //minimumInputLength: 2,
                dropdownAutoWidth: true,
                width: '100%',
                language: {
                    inputTooShort: function() {
                        return 'Ketikan Nomor minimal 2 huruf';
                    },
                    noResults: function() {
                        return 'Data Tidak Ditemukan';
                    }
                },
                ajax: {
                    url: siteUrl + 'Select2_master/dataProvinsi',
                    type: 'POST',
                    dataType: 'JSON',
                    delay: 100,
                    data: function(result) {
                        return {
                            searchTerm: result.term
                        };
                    },

                    processResults: function(result) {
                        return {
                            results: result
                        };
                    },
                    cache: true
                }
            });
        }

        function initailizeSelect2_kabupaten(param) {
            if (param == '' || param == null || param == 'null') { // jika parameter kosong/ null
                // jalankan fungsi select2_default
                select2_default('select2_kabupaten');
            } else { // selain itu
                // jalan fungsi select2 asli
                $(".select2_kabupaten").select2({
                    allowClear: true,
                    multiple: false,
                    placeholder: '~ Pilih Kabupaten',
                    dropdownAutoWidth: true,
                    width: '100%',
                    language: {
                        inputTooShort: function() {
                            return 'Ketikan Nomor minimal 1 huruf';
                        },
                        noResults: function() {
                            return 'Data Tidak Ditemukan';
                        }
                    },
                    ajax: {
                        url: siteUrl + 'Select2_master/dataKabupaten/' + param,
                        type: 'POST',
                        dataType: 'JSON',
                        delay: 100,
                        data: function(result) {
                            return {
                                searchTerm: result.term
                            };
                        },
                        processResults: function(result) {
                            return {
                                results: result
                            };
                        },
                        cache: true
                    }
                });
            }
        }

        function initailizeSelect2_kecamatan(param) {
            if (param == '' || param == null || param == 'null') { // jika parameter kosong/ null
                // jalankan fungsi select2_default
                select2_default('select2_kecamatan');
            } else { // selain itu
                // jalan fungsi select2 asli
                $(".select2_kecamatan").select2({
                    allowClear: true,
                    multiple: false,
                    placeholder: '~ Pilih Kecamatan',
                    dropdownAutoWidth: true,
                    width: '100%',
                    language: {
                        inputTooShort: function() {
                            return 'Ketikan Nomor minimal 1 huruf';
                        },
                        noResults: function() {
                            return 'Data Tidak Ditemukan';
                        }
                    },
                    ajax: {
                        url: siteUrl + 'Select2_master/dataKecamatan/' + param,
                        type: 'POST',
                        dataType: 'JSON',
                        delay: 100,
                        data: function(result) {
                            return {
                                searchTerm: result.term
                            };
                        },

                        processResults: function(result) {
                            return {
                                results: result
                            };
                        },
                        cache: true
                    }
                });
            }
        }

        function initailizeSelect2_member(param) {
            $(".select2_member").select2({
                allowClear: true,
                multiple: false,
                placeholder: '~ Pilih Member',
                //minimumInputLength: 2,
                dropdownAutoWidth: true,
                width: '100%',
                language: {
                    inputTooShort: function() {
                        return 'Ketikan Nomor minimal 2 huruf';
                    },
                    noResults: function() {
                        return 'Data Tidak Ditemukan';
                    }
                },
                ajax: {
                    url: siteUrl + 'Select2_master/dataMember/' + param,
                    type: 'POST',
                    dataType: 'JSON',
                    delay: 100,
                    data: function(result) {
                        return {
                            searchTerm: result.term
                        };
                    },

                    processResults: function(result) {
                        return {
                            results: result
                        };
                    },
                    cache: true
                }
            });
        }

        function initailizeSelect2_user() {
            $(".select2_user").select2({
                allowClear: true,
                multiple: false,
                placeholder: '~ Pilih User',
                //minimumInputLength: 2,
                dropdownAutoWidth: true,
                width: '100%',
                language: {
                    inputTooShort: function() {
                        return 'Ketikan Nomor minimal 2 huruf';
                    },
                    noResults: function() {
                        return 'Data Tidak Ditemukan';
                    }
                },
                ajax: {
                    url: siteUrl + 'Select2_master/dataUser',
                    type: 'POST',
                    dataType: 'JSON',
                    delay: 100,
                    data: function(result) {
                        return {
                            searchTerm: result.term
                        };
                    },

                    processResults: function(result) {
                        return {
                            results: result
                        };
                    },
                    cache: true
                }
            });
        }

        function initailizeSelect2_user_all() {
            $(".select2_user_all").select2({
                allowClear: true,
                multiple: false,
                placeholder: '~ Pilih User',
                //minimumInputLength: 2,
                dropdownAutoWidth: true,
                width: '100%',
                language: {
                    inputTooShort: function() {
                        return 'Ketikan Nomor minimal 2 huruf';
                    },
                    noResults: function() {
                        return 'Data Tidak Ditemukan';
                    }
                },
                ajax: {
                    url: siteUrl + 'Select2_master/dataUserAll',
                    type: 'POST',
                    dataType: 'JSON',
                    delay: 100,
                    data: function(result) {
                        return {
                            searchTerm: result.term
                        };
                    },

                    processResults: function(result) {
                        return {
                            results: result
                        };
                    },
                    cache: true
                }
            });
        }

        function initailizeSelect2_jenis_bayar() {
            $(".select2_jenis_bayar").select2({
                allowClear: true,
                multiple: false,
                placeholder: '~ Pilih Jenis Bayar',
                //minimumInputLength: 2,
                dropdownAutoWidth: true,
                width: '100%',
                language: {
                    inputTooShort: function() {
                        return 'Ketikan Nomor minimal 2 huruf';
                    },
                    noResults: function() {
                        return 'Data Tidak Ditemukan';
                    }
                },
                ajax: {
                    url: siteUrl + 'Select2_master/dataJenisBayar',
                    type: 'POST',
                    dataType: 'JSON',
                    delay: 100,
                    data: function(result) {
                        return {
                            searchTerm: result.term
                        };
                    },

                    processResults: function(result) {
                        return {
                            results: result
                        };
                    },
                    cache: true
                }
            });
        }

        function initailizeSelect2_tindakan() {
            $(".select2_tindakan").select2({
                allowClear: true,
                multiple: false,
                placeholder: '~ Pilih Tindakan',
                //minimumInputLength: 2,
                dropdownAutoWidth: true,
                width: '100%',
                language: {
                    inputTooShort: function() {
                        return 'Ketikan Nomor minimal 2 huruf';
                    },
                    noResults: function() {
                        return 'Data Tidak Ditemukan';
                    }
                },
                ajax: {
                    url: siteUrl + 'Select2_master/dataTindakan',
                    type: 'POST',
                    dataType: 'JSON',
                    delay: 100,
                    data: function(result) {
                        return {
                            searchTerm: result.term
                        };
                    },

                    processResults: function(result) {
                        return {
                            results: result
                        };
                    },
                    cache: true
                }
            });
        }

        function initailizeSelect2_kelas() {
            $(".select2_kelas").select2({
                allowClear: true,
                multiple: false,
                placeholder: '~ Pilih Kelas',
                //minimumInputLength: 2,
                dropdownAutoWidth: true,
                width: '100%',
                language: {
                    inputTooShort: function() {
                        return 'Ketikan Nomor minimal 2 huruf';
                    },
                    noResults: function() {
                        return 'Data Tidak Ditemukan';
                    }
                },
                ajax: {
                    url: siteUrl + 'Select2_master/dataKelas',
                    type: 'POST',
                    dataType: 'JSON',
                    delay: 100,
                    data: function(result) {
                        return {
                            searchTerm: result.term
                        };
                    },

                    processResults: function(result) {
                        return {
                            results: result
                        };
                    },
                    cache: true
                }
            });
        }

        function initailizeSelect2_poli() {
            $(".select2_poli").select2({
                allowClear: true,
                multiple: false,
                placeholder: '~ Pilih Poli',
                //minimumInputLength: 2,
                dropdownAutoWidth: true,
                width: '100%',
                language: {
                    inputTooShort: function() {
                        return 'Ketikan Nomor minimal 2 huruf';
                    },
                    noResults: function() {
                        return 'Data Tidak Ditemukan';
                    }
                },
                ajax: {
                    url: siteUrl + 'Select2_master/dataPoli',
                    type: 'POST',
                    dataType: 'JSON',
                    delay: 100,
                    data: function(result) {
                        return {
                            searchTerm: result.term
                        };
                    },

                    processResults: function(result) {
                        return {
                            results: result
                        };
                    },
                    cache: true
                }
            });
        }

        function initailizeSelect2_poli_dokter(param) {
            if (param == '' || param == null || param == 'null') { // jika parameter kosong/ null
                // jalankan fungsi select2_default
                select2_default('select2_poli_dokter');
            } else { // selain itu
                // jalan fungsi select2 asli
                $(".select2_poli_dokter").select2({
                    allowClear: true,
                    multiple: false,
                    placeholder: '~ Pilih Poli',
                    dropdownAutoWidth: true,
                    width: '100%',
                    language: {
                        inputTooShort: function() {
                            return 'Ketikan Nomor minimal 1 huruf';
                        },
                        noResults: function() {
                            return 'Data Tidak Ditemukan';
                        }
                    },
                    ajax: {
                        url: siteUrl + 'Select2_master/dataPoliDokter/' + param,
                        type: 'POST',
                        dataType: 'JSON',
                        delay: 100,
                        data: function(result) {
                            return {
                                searchTerm: result.term
                            };
                        },

                        processResults: function(result) {
                            return {
                                results: result
                            };
                        },
                        cache: true
                    }
                });
            }
        }

        function initailizeSelect2_dokter_poli(param) {
            if (param == '' || param == null || param == 'null') { // jika parameter kosong/ null
                // jalankan fungsi select2_default
                select2_default('select2_dokter_poli');
            } else { // selain itu
                // jalan fungsi select2 asli
                $(".select2_dokter_poli").select2({
                    allowClear: true,
                    multiple: false,
                    placeholder: '~ Pilih Dokter',
                    dropdownAutoWidth: true,
                    width: '100%',
                    language: {
                        inputTooShort: function() {
                            return 'Ketikan Nomor minimal 1 huruf';
                        },
                        noResults: function() {
                            return 'Data Tidak Ditemukan';
                        }
                    },
                    ajax: {
                        url: siteUrl + 'Select2_master/dataDokterPoli/' + param,
                        type: 'POST',
                        dataType: 'JSON',
                        delay: 100,
                        data: function(result) {
                            return {
                                searchTerm: result.term
                            };
                        },

                        processResults: function(result) {
                            return {
                                results: result
                            };
                        },
                        cache: true
                    }
                });
            }
        }

        function initailizeSelect2_dokter_all() {
            $(".select2_dokter_all").select2({
                allowClear: true,
                multiple: false,
                placeholder: '~ Pilih Dokter',
                dropdownAutoWidth: true,
                width: '100%',
                language: {
                    inputTooShort: function() {
                        return 'Ketikan Nomor minimal 1 huruf';
                    },
                    noResults: function() {
                        return 'Data Tidak Ditemukan';
                    }
                },
                ajax: {
                    url: siteUrl + 'Select2_master/dataDokterAll',
                    type: 'POST',
                    dataType: 'JSON',
                    delay: 100,
                    data: function(result) {
                        return {
                            searchTerm: result.term
                        };
                    },

                    processResults: function(result) {
                        return {
                            results: result
                        };
                    },
                    cache: true
                }
            });
        }

        function initailizeSelect2_ruang_jd(kode_poli, hari, kode_cabang) {
            $(".select2_ruang_jd").select2({
                allowClear: true,
                multiple: false,
                placeholder: '~ Pilih Ruang',
                //minimumInputLength: 2,
                dropdownAutoWidth: true,
                width: '100%',
                language: {
                    inputTooShort: function() {
                        return 'Ketikan Nomor minimal 2 huruf';
                    },
                    noResults: function() {
                        return 'Data Tidak Ditemukan';
                    }
                },
                ajax: {
                    url: siteUrl + 'Select2_master/dataRuangJd/' + kode_poli + '/' + hari + '/' + kode_cabang,
                    type: 'POST',
                    dataType: 'JSON',
                    delay: 100,
                    data: function(result) {
                        return {
                            searchTerm: result.term
                        };
                    },

                    processResults: function(result) {
                        return {
                            results: result
                        };
                    },
                    cache: true
                }
            });
        }

        function initailizeSelect2_ruang() {
            $(".select2_ruang").select2({
                allowClear: true,
                multiple: false,
                placeholder: '~ Pilih Ruang',
                //minimumInputLength: 2,
                dropdownAutoWidth: true,
                width: '100%',
                language: {
                    inputTooShort: function() {
                        return 'Ketikan Nomor minimal 2 huruf';
                    },
                    noResults: function() {
                        return 'Data Tidak Ditemukan';
                    }
                },
                ajax: {
                    url: siteUrl + 'Select2_master/dataRuang',
                    type: 'POST',
                    dataType: 'JSON',
                    delay: 100,
                    data: function(result) {
                        return {
                            searchTerm: result.term
                        };
                    },

                    processResults: function(result) {
                        return {
                            results: result
                        };
                    },
                    cache: true
                }
            });
        }

        function initailizeSelect2_bed(param) {
            if (param == '' || param == null || param == 'null') { // jika parameter kosong/ null
                // jalankan fungsi select2_default
                select2_default('select2_bed');
            } else {
                $(".select2_bed").select2({
                    allowClear: true,
                    multiple: false,
                    placeholder: '~ Pilih Bed',
                    //minimumInputLength: 2,
                    dropdownAutoWidth: true,
                    width: '100%',
                    language: {
                        inputTooShort: function() {
                            return 'Ketikan Nomor minimal 2 huruf';
                        },
                        noResults: function() {
                            return 'Data Tidak Ditemukan';
                        }
                    },
                    ajax: {
                        url: siteUrl + 'Select2_master/dataBed/' + param,
                        type: 'POST',
                        dataType: 'JSON',
                        delay: 100,
                        data: function(result) {
                            return {
                                searchTerm: result.term
                            };
                        },

                        processResults: function(result) {
                            return {
                                results: result
                            };
                        },
                        cache: true
                    }
                });
            }
        }

        function initailizeSelect2_supplier() {
            $(".select2_supplier").select2({
                allowClear: true,
                multiple: false,
                placeholder: '~ Pilih Supplier',
                //minimumInputLength: 2,
                dropdownAutoWidth: true,
                width: '100%',
                language: {
                    inputTooShort: function() {
                        return 'Ketikan Nomor minimal 2 huruf';
                    },
                    noResults: function() {
                        return 'Data Tidak Ditemukan';
                    }
                },
                ajax: {
                    url: siteUrl + 'Select2_master/dataSupplier',
                    type: 'POST',
                    dataType: 'JSON',
                    delay: 100,
                    data: function(result) {
                        return {
                            searchTerm: result.term
                        };
                    },

                    processResults: function(result) {
                        return {
                            results: result
                        };
                    },
                    cache: true
                }
            });
        }

        function initailizeSelect2_gudang_int() {
            $(".select2_gudang_int").select2({
                allowClear: true,
                multiple: false,
                placeholder: '~ Pilih Gudang',
                //minimumInputLength: 2,
                dropdownAutoWidth: true,
                width: '100%',
                language: {
                    inputTooShort: function() {
                        return 'Ketikan Nomor minimal 2 huruf';
                    },
                    noResults: function() {
                        return 'Data Tidak Ditemukan';
                    }
                },
                ajax: {
                    url: siteUrl + 'Select2_master/dataGudangInt',
                    type: 'POST',
                    dataType: 'JSON',
                    delay: 100,
                    data: function(result) {
                        return {
                            searchTerm: result.term
                        };
                    },

                    processResults: function(result) {
                        return {
                            results: result
                        };
                    },
                    cache: true
                }
            });
        }

        function initailizeSelect2_gudang_log() {
            $(".select2_gudang_log").select2({
                allowClear: true,
                multiple: false,
                placeholder: '~ Pilih Gudang',
                //minimumInputLength: 2,
                dropdownAutoWidth: true,
                width: '100%',
                language: {
                    inputTooShort: function() {
                        return 'Ketikan Nomor minimal 2 huruf';
                    },
                    noResults: function() {
                        return 'Data Tidak Ditemukan';
                    }
                },
                ajax: {
                    url: siteUrl + 'Select2_master/dataGudangLog',
                    type: 'POST',
                    dataType: 'JSON',
                    delay: 100,
                    data: function(result) {
                        return {
                            searchTerm: result.term
                        };
                    },

                    processResults: function(result) {
                        return {
                            results: result
                        };
                    },
                    cache: true
                }
            });
        }

        function initailizeSelect2_pekerjaan() {
            $(".select2_pekerjaan").select2({
                allowClear: true,
                multiple: false,
                placeholder: '~ Pilih Pekerjaan',
                //minimumInputLength: 2,
                dropdownAutoWidth: true,
                width: '100%',
                language: {
                    inputTooShort: function() {
                        return 'Ketikan Nomor minimal 2 huruf';
                    },
                    noResults: function() {
                        return 'Data Tidak Ditemukan';
                    }
                },
                ajax: {
                    url: siteUrl + 'Select2_master/dataPekerjaan',
                    type: 'POST',
                    dataType: 'JSON',
                    delay: 100,
                    data: function(result) {
                        return {
                            searchTerm: result.term
                        };
                    },

                    processResults: function(result) {
                        return {
                            results: result
                        };
                    },
                    cache: true
                }
            });
        }

        function initailizeSelect2_agama() {
            $(".select2_agama").select2({
                allowClear: true,
                multiple: false,
                placeholder: '~ Pilih Agama',
                //minimumInputLength: 2,
                dropdownAutoWidth: true,
                width: '100%',
                language: {
                    inputTooShort: function() {
                        return 'Ketikan Nomor minimal 2 huruf';
                    },
                    noResults: function() {
                        return 'Data Tidak Ditemukan';
                    }
                },
                ajax: {
                    url: siteUrl + 'Select2_master/dataAgama',
                    type: 'POST',
                    dataType: 'JSON',
                    delay: 100,
                    data: function(result) {
                        return {
                            searchTerm: result.term
                        };
                    },

                    processResults: function(result) {
                        return {
                            results: result
                        };
                    },
                    cache: true
                }
            });
        }

        function initailizeSelect2_pendidikan() {
            $(".select2_pendidikan").select2({
                allowClear: true,
                multiple: false,
                placeholder: '~ Pilih Pendidikan',
                //minimumInputLength: 2,
                dropdownAutoWidth: true,
                width: '100%',
                language: {
                    inputTooShort: function() {
                        return 'Ketikan Nomor minimal 2 huruf';
                    },
                    noResults: function() {
                        return 'Data Tidak Ditemukan';
                    }
                },
                ajax: {
                    url: siteUrl + 'Select2_master/dataPendidikan',
                    type: 'POST',
                    dataType: 'JSON',
                    delay: 100,
                    data: function(result) {
                        return {
                            searchTerm: result.term
                        };
                    },

                    processResults: function(result) {
                        return {
                            results: result
                        };
                    },
                    cache: true
                }
            });
        }

        function initailizeSelect2_pendaftaran(param) {
            $(".select2_pendaftaran").select2({
                allowClear: true,
                multiple: false,
                placeholder: '~ Pilih Pendaftaran',
                //minimumInputLength: 2,
                dropdownAutoWidth: true,
                width: '100%',
                language: {
                    inputTooShort: function() {
                        return 'Ketikan Nomor minimal 2 huruf';
                    },
                    noResults: function() {
                        return 'Data Tidak Ditemukan';
                    }
                },
                ajax: {
                    url: siteUrl + 'Select2_master/dataPendaftaran/' + param,
                    type: 'POST',
                    dataType: 'JSON',
                    delay: 100,
                    data: function(result) {
                        return {
                            searchTerm: result.term
                        };
                    },

                    processResults: function(result) {
                        return {
                            results: result
                        };
                    },
                    cache: true
                }
            });
        }

        function initailizeSelect2_penjualan() {
            $(".select2_penjualan").select2({
                allowClear: true,
                multiple: false,
                placeholder: '~ Pilih Penjualan',
                //minimumInputLength: 2,
                dropdownAutoWidth: true,
                width: '100%',
                language: {
                    inputTooShort: function() {
                        return 'Ketikan Nomor minimal 2 huruf';
                    },
                    noResults: function() {
                        return 'Data Tidak Ditemukan';
                    }
                },
                ajax: {
                    url: siteUrl + 'Select2_master/dataPenjualan',
                    type: 'POST',
                    dataType: 'JSON',
                    delay: 100,
                    data: function(result) {
                        return {
                            searchTerm: result.term
                        };
                    },

                    processResults: function(result) {
                        return {
                            results: result
                        };
                    },
                    cache: true
                }
            });
        }

        function initailizeSelect2_penjualan_retur() {
            $(".select2_penjualan_retur").select2({
                allowClear: true,
                multiple: false,
                placeholder: '~ Pilih Retur Jual',
                //minimumInputLength: 2,
                dropdownAutoWidth: true,
                width: '100%',
                language: {
                    inputTooShort: function() {
                        return 'Ketikan Nomor minimal 2 huruf';
                    },
                    noResults: function() {
                        return 'Data Tidak Ditemukan';
                    }
                },
                ajax: {
                    url: siteUrl + 'Select2_master/dataReturJual',
                    type: 'POST',
                    dataType: 'JSON',
                    delay: 100,
                    data: function(result) {
                        return {
                            searchTerm: result.term
                        };
                    },

                    processResults: function(result) {
                        return {
                            results: result
                        };
                    },
                    cache: true
                }
            });
        }

        function initailizeSelect2_bank() {
            $(".select2_bank").select2({
                allowClear: true,
                multiple: false,
                placeholder: '~ Pilih Bank',
                //minimumInputLength: 2,
                dropdownAutoWidth: true,
                width: '100%',
                language: {
                    inputTooShort: function() {
                        return 'Ketikan Nomor minimal 2 huruf';
                    },
                    noResults: function() {
                        return 'Data Tidak Ditemukan';
                    }
                },
                ajax: {
                    url: siteUrl + 'Select2_master/dataBank',
                    type: 'POST',
                    dataType: 'JSON',
                    delay: 100,
                    data: function(result) {
                        return {
                            searchTerm: result.term
                        };
                    },

                    processResults: function(result) {
                        return {
                            results: result
                        };
                    },
                    cache: true
                }
            });
        }

        function initailizeSelect2_tipe_bank() {
            $(".select2_tipe_bank").select2({
                allowClear: true,
                multiple: false,
                placeholder: '~ Pilih Tipe Bank',
                //minimumInputLength: 2,
                dropdownAutoWidth: true,
                width: '100%',
                language: {
                    inputTooShort: function() {
                        return 'Ketikan Nomor minimal 2 huruf';
                    },
                    noResults: function() {
                        return 'Data Tidak Ditemukan';
                    }
                },
                ajax: {
                    url: siteUrl + 'Select2_master/dataTipeBank',
                    type: 'POST',
                    dataType: 'JSON',
                    delay: 100,
                    data: function(result) {
                        return {
                            searchTerm: result.term
                        };
                    },

                    processResults: function(result) {
                        return {
                            results: result
                        };
                    },
                    cache: true
                }
            });
        }

        function initailizeSelect2_jual_for_retur() {
            $(".select2_jual_for_retur").select2({
                allowClear: true,
                multiple: false,
                placeholder: '~ Pilih Penjualan Untuk Di Retur',
                //minimumInputLength: 2,
                dropdownAutoWidth: true,
                width: '100%',
                language: {
                    inputTooShort: function() {
                        return 'Ketikan Nomor minimal 2 huruf';
                    },
                    noResults: function() {
                        return 'Data Tidak Ditemukan';
                    }
                },
                ajax: {
                    url: siteUrl + 'Select2_master/dataJualForRetur',
                    type: 'POST',
                    dataType: 'JSON',
                    delay: 100,
                    data: function(result) {
                        return {
                            searchTerm: result.term
                        };
                    },

                    processResults: function(result) {
                        return {
                            results: result
                        };
                    },
                    cache: true
                }
            });
        }

        function initailizeSelect2_promo(min_buy) {
            $(".select2_promo").select2({
                allowClear: true,
                multiple: false,
                placeholder: '~ Pilih Promo',
                //minimumInputLength: 2,
                dropdownAutoWidth: true,
                width: '100%',
                language: {
                    inputTooShort: function() {
                        return 'Ketikan Nomor minimal 2 huruf';
                    },
                    noResults: function() {
                        return 'Data Tidak Ditemukan';
                    }
                },
                ajax: {
                    url: siteUrl + 'Select2_master/dataPromo/' + min_buy,
                    type: 'POST',
                    dataType: 'JSON',
                    delay: 100,
                    data: function(result) {
                        return {
                            searchTerm: result.term
                        };
                    },

                    processResults: function(result) {
                        return {
                            results: result
                        };
                    },
                    cache: true
                }
            });
        }

        // Run migration in background using AJAX
        //$.ajax({
        //url: '<?= site_url() ?>Backdoor/auto_migrasi',
        //method: 'GET',
        //success: function(response) {
        //console.log('Background migration completed');
        //},
        //error: function(xhr, status, error) {
        //console.error('Background migration failed:', error);
        //}
        //});
    </script>

    <!-- AdminLTE App -->
    <script src="<?= base_url() ?>assets/dist/js/adminlte.min.js"></script>

    <!-- AdminLTE for demo purposes -->
    <script src="<?= base_url() ?>assets/dist/js/demo.js"></script>
</body>

</html>