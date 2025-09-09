<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $page ?></title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    <!-- Font Awesome -->
    <!-- <link rel="stylesheet" href="<?= base_url() ?>assets/fontawesome/css/all.min.css"> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">

    <!-- char js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="<?= base_url() ?>assets/styles.css">

    <!-- DataTables -->
    <link rel="stylesheet" href="<?= base_url() ?>assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">

    <!-- select2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

    <!-- sweetalert -->
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- animate -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <!-- Select2 js -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.full.min.js"></script>

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

<body>
    <?php
    $kode_user = $this->session->userdata('kode_user');
    $menu = $this->db->query("
        SELECT m.* 
        FROM m_menu m 
        WHERE m.id IN (
            SELECT id_menu 
            FROM akses_menu 
            WHERE kode_role IN (
                SELECT kode_role FROM user WHERE kode_user = ?
            )
        ) 
        ORDER BY m.id
    ", [$kode_user])->result();
    ?>

    <input type="hidden" id="ubahIdMenu">

    <nav class="navbar">
        <div class="navbar-container">
            <a href="#" class="navbar-brand">Company</a>
            <div class="hamburger">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </div>
            <ul class="nav-menu"></ul>

            <div class="user-profile">
                <a href="#" class="profile-link">
                    <img src="<?= base_url('assets/user/') . $this->data["foto"] ?>" class="img-circle" alt="User Image" style="width: 20px; border-radius: 50%"><span class="text-white" onclick="getUrl('Profile')"><?= $this->data["nama"] ?></span>
                    <i class="fa-solid fa-right-from-bracket text-white logout" onclick="exit()"></i></span>
                </a>
            </div>
        </div>
    </nav>

    <script>
        const hamburger = document.querySelector(".hamburger");
        const navMenu = document.querySelector(".nav-menu");
        const navLinks = document.querySelectorAll(".nav-link");
        const body = document.querySelector("body");

        hamburger.addEventListener("click", () => {
            hamburger.classList.toggle("active");
            navMenu.classList.toggle("active");
            body.classList.toggle("no-scroll");
        });

        function renderNavbar(menuData) {
            const navMenu = document.querySelector('.nav-menu');
            navMenu.innerHTML = '';
            let menuItemHTML = '';

            if (menuData.sub_menu && menuData.sub_menu.length > 0) {
                menuData.sub_menu.forEach(sm => {
                    if (sm.sub_menu2 && sm.sub_menu2.length > 0) {
                        menuItemHTML += `
                            <li class="nav-item dropdown">
                                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" role="button" aria-expanded="false">${sm.submenu}</a>
                                <ul class="dropdown-menu">
`;
                        sm.sub_menu2.forEach(sm2 => {
                            menuItemHTML += `<li><a href="<?= site_url() ?>${sm2.url}" class="dropdown-item">${sm2.nama}</a></li>`;
                        });
                        menuItemHTML += `</ul></li>`;
                    } else {
                        menuItemHTML += `
                            <li class="nav-item">
                                <a href="<?= site_url() ?>${sm.url}" class="nav-link">${sm.submenu}</a>
                            </li>`;
                    }
                });
            } else {
                menuItemHTML = `
                    <li class="nav-item">
                        <a href="<?= site_url() ?>${menuData.url}" class="nav-link">${menuData.nama}</a>
                    </li>`;
            }

            navMenu.innerHTML = menuItemHTML;

            // Initialize Bootstrap dropdowns
            var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'))
            var dropdownList = dropdownElementList.map(function(dropdownToggleEl) {
                return new bootstrap.Dropdown(dropdownToggleEl)
            })
        }

        const dockItems = document.querySelectorAll(".dock-item");

        dockItems.forEach(item => {
            item.addEventListener("click", (e) => {
                e.preventDefault();
                // Remove active class from all dock-items
                dockItems.forEach(i => {
                    i.classList.remove("active");
                });

                // Add active class to the clicked dock-item
                item.classList.add("active");
            });
        });
    </script>

    <div class="container mt-5 mb-5">
        <div class="row">
            <div class="col-md-12">
                <?= $content ?>
            </div>
        </div>
    </div>

    <footer class="footer-dock">
        <div class="dock-container">

            <?php
            // Get active menu based on current URL segment
            $masterMenu = $this->M_global->getData('m_menu', ['url' => $this->uri->segment(1)]);
            $idMenuAktif = $masterMenu ? $masterMenu->id : 1; // Default to menu ID 1 if no match found

            foreach ($menu as $m) :

                if ($m->url == $this->uri->segment(1)) { // jika url menu sama dengan segment 1 dari url
                    // aktifkan
                    $aktifUrl = 'active';
                } else { // selain itu
                    // nonaktifkan
                    $aktifUrl = '';
                }

                $cekSM = $this->M_global->getDataResult('sub_menu', ['id_menu' => $m->id]);

                if (count($cekSM) > 0) {
                    $href = '#';
                } else {
                    $href = site_url() . $m->url;
                }
            ?>

                <div class="dock-item <?= $aktifUrl ?>">
                    <a href="<?= $href ?>" type="button" onclick="ubahIdMenu('<?= $m->id ?>')">
                        <?= $m->icon ?>
                    </a>
                </div>

            <?php endforeach; ?>
        </div>
    </footer>

    <span id="countdownNotif" style="position: fixed; font-size: 12px; left: 20px; color: white; z-index: 1000; bottom: 3vh;">
        <span id="count_notif"></span>
        <span id="notf_live"></span>
    </span>
    <span id="time" style="position: fixed; font-size: 12px; right: 20px; color: white; z-index: 1000; bottom: 3vh;"></span>

    <!-- ionicon -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <!-- Bootstrap JS (bundle = JS + Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- myscript -->
    <script>
        $(document).ready(function() {
            // Initial render
            ubahIdMenu(<?= (empty($idMenuAktif) ? (!empty($menu) ? $menu[0]->id : 'null') : $idMenuAktif) ?>);
        });

        function ubahIdMenu(idMenu) {
            if (!idMenu) return;
            $.ajax({
                url: `<?= site_url('ajax/get_menu/') ?>${idMenu}`,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    renderNavbar(data);
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", status, error);
                }
            });
        }

        function renderNavbar(menuData) {
            const navMenu = document.querySelector('.nav-menu');
            navMenu.innerHTML = '';
            let menuItemHTML = '';

            if (menuData.sub_menu && menuData.sub_menu.length > 0) {
                menuData.sub_menu.forEach(sm => {
                    if (sm.sub_menu2 && sm.sub_menu2.length > 0) {
                        menuItemHTML += `
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" role="button" aria-expanded="false">${sm.submenu}</a>
                        <ul class="dropdown-menu">`;
                        sm.sub_menu2.forEach(sm2 => {
                            menuItemHTML += `<li><a href="<?= site_url() ?>${sm2.url}" class="dropdown-item">${sm2.nama}</a></li>`;
                        });
                        menuItemHTML += `</ul></li>`;
                    } else {
                        menuItemHTML += `
                    <li class="nav-item">
                        <a href="<?= site_url() ?>${sm.url}" class="nav-link">${sm.submenu}</a>
                    </li>`;
                    }
                });
            } else {
                menuItemHTML = `
            <li class="nav-item">
                <a href="<?= site_url() ?>${menuData.url}" class="nav-link">${menuData.nama}</a>
            </li>`;
            }

            navMenu.innerHTML = menuItemHTML;

            // Inisialisasi Bootstrap Dropdown setelah isi HTML dimasukkan
            const dropdownToggleList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
            dropdownToggleList.forEach(function(dropdownToggleEl) {
                new bootstrap.Dropdown(dropdownToggleEl);
            });
        }


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
            var x1 = hours + ":" + minutes + ":" + seconds + " | " + dayName + ", " + day + " " + month + " " + year;

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
    </script>


    <!-- AdminLTE App -->
    <script src="<?= base_url() ?>assets/dist/js/adminlte.min.js"></script>

    <!-- AdminLTE for demo purposes -->
    <script src="<?= base_url() ?>assets/dist/js/demo.js"></script>
</body>

</html>