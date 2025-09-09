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

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <!-- <script src="<?= base_url() ?>assets/plugins/jquery/jquery.min.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.full.min.js"></script>

    <!-- DataTables -->
    <link rel="stylesheet" href="<?= base_url() ?>assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">

    <!-- select2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.full.min.js"></script>

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
</head>

<body class="hold-transition login-page" style="background-image: url(<?= site_url('assets/img/web/') . $web->bg ?>); background-size: cover; background-repeat: no-repeat; background-position: center; backdrop-filter: blur(8px);">


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
            width: 50px;
            height: 50px;
            bottom: 10px;
            right: 10px;
            background-color: #14a651;
            color: white;
            border-radius: 50%;
            text-align: center;
        }
    </style>

    <?= $content ?>

    <!-- modal loading proses -->
    <div class="modal fade" id="loading">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <img src="<?= base_url() ?>assets/img/loading_2.gif" style="width: 100%;">
                </div>
            </div>
        </div>
    </div>

    <!-- ionicon -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <!-- myscript -->
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>

    <script>
        // nonaktif inspect element
        // Menonaktifkan klik kanan
        // document.addEventListener('contextmenu', function(e) {
        //     e.preventDefault();
        // });

        // Menonaktifkan F12 (DevTools)
        // document.addEventListener('keydown', function(e) {
        //     if (e.keyCode === 123) { // F12
        //         e.preventDefault();
        //     }
        // });


        // variable
        const siteUrl = '<?= site_url() ?>';

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
        });

        // fungsi select2 global
        // inisial
        initailizeSelect2_cabang(param = '');
        initailizeSelect2_cabang_member(param = '');

        function select2_default(param) {
            var mymessage = "Data kosong";
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

        function initailizeSelect2_cabang(param) {
            if (param == '' || param == null || param == 'null') { // jika parameter kosong/ null
                // jalankan fungsi select2_default
                select2_default('select2_cabang');
            } else { // selain itu
                // jalan fungsi select2 asli
                $(".select2_cabang").select2({
                    allowClear: true,
                    multiple: false,
                    placeholder: '~ Pilih Cabang',
                    dropdownAutoWidth: true,
                    width: '100%',
                    language: {
                        inputTooShort: function() {
                            return 'Ketikan Nomor minimal 1 huruf';
                        }
                    },
                    ajax: {
                        url: siteUrl + 'Select2_master/dataCabang/?email=' + param,
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

        function initailizeSelect2_cabang_member(param) {
            if (param == '' || param == null || param == 'null') { // jika parameter kosong/ null
                // jalankan fungsi select2_default
                select2_default('select2_cabang');
            } else { // selain itu
                // jalan fungsi select2 asli
                $(".select2_cabang").select2({
                    allowClear: true,
                    multiple: false,
                    placeholder: '~ Pilih Cabang',
                    dropdownAutoWidth: true,
                    width: '100%',
                    language: {
                        inputTooShort: function() {
                            return 'Ketikan Nomor minimal 1 huruf';
                        }
                    },
                    ajax: {
                        url: siteUrl + 'Select2_master/dataCabangMember/?email=' + param,
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

        // cek email berdsasarkan email
        function cekEmailLog(mail) {
            if (validateEmail(mail)) {
                if (mail == null || mail == "") { // jika email null/ tidak ada
                    Swal.fire({
                        title: "Email",
                        text: "Form sudah diisi?",
                        icon: "question"
                    });
                    return;
                } else { // selain itu
                    $.ajax({
                        url: siteUrl + 'Auth/cek_email?email=' + mail,
                        type: "POST",
                        dataType: "JSON",
                        success: function(result) { // jika fungsi berjalan
                            if (result.status == 1) { // jika mendapatkan hasil status 1
                                $('#email').val('');
                                Swal.fire({
                                    title: "Email",
                                    text: "Tidak ditemukan!, silahkan masuk daftarkan email",
                                    icon: "error"
                                });
                                return;
                            }
                        },
                        error: function(result) { // jika fungsi gagal berjalan
                            // tampilkan notifikasi error
                            error_proccess()
                        }
                    });
                }
            }
        }

        // fungsi kirimkan kode
        function getCode(param, email) {
            // tampilkan loading

            // jalankan fungsi
            $.ajax({
                url: siteUrl + 'Auth/sendCode/' + param + '/?email=' + email,
                type: "POST",
                dataType: "JSON",
                success: function(result) { // jika fungsi berjalan
                    // sembunyikan loading

                    if (result.status == 1) { // jika mendapatkan hasil status 1
                        Swal.fire({
                            title: "Kode Validasi",
                            text: "Berhasil dikirim!, silahkan cek email anda",
                            icon: "success"
                        });
                    } else if (result.status == 2) { // jika mendapatkan hasil status 2
                        Swal.fire({
                            title: "Kode Validasi",
                            text: "Gagal dikirim!, silahkan coba lagi",
                            icon: "info"
                        });
                    } else { // selain itu
                        Swal.fire({
                            title: "Kode Validasi",
                            text: "Gagal dikirim!, email sudah digunakan",
                            icon: "info"
                        });
                    }
                },
                error: function(result) { // jika fungsi gagal berjalan
                    // sembunyikan loaing

                    // tampilkan notifikasi error
                    error_proccess()
                }
            });
        }

        // fungsi hyperlink dengan js
        function getUrl(url) {
            location.href = siteUrl + url;
        }

        // fungsi cek value harus berupa email
        function validateEmail(email) {
            var re = /\S+@\S+\.\S+/;
            return re.test(email);
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

        // fungsi notifikasi error
        function error_proccess() {
            Swal.fire({
                title: "Error",
                text: "Error dalam pemrosesan!",
                icon: "error"
            });
            return;
        }
    </script>

    <!-- AdminLTE App -->
    <script src="<?= base_url() ?>assets/dist/js/adminlte.min.js"></script>

    <!-- AdminLTE for demo purposes -->
    <script src="<?= base_url() ?>assets/dist/js/demo.js"></script>
</body>

</html>