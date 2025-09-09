<?php
$created    = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->created;

if ($web->ct_theme == 1) {
    $style = 'style="background: rgba(255, 255, 255, 0.6); backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px);"';
    $style2 = 'style="backdrop-filter: blur(10px);"';
    $style3 = 'style="background: transparent;"';
    $style_modal = 'style="background-color: rgba(255, 255, 255, 0.4); -webkit-backdrop-filter: blur(10px); backdrop-filter: blur(4px);"';
} else if ($web->ct_theme == 2) {
    $style = 'style="background: rgba(30, 30, 30, 0.8); backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px); color: white !important;"';
    $style2 = 'style="backdrop-filter: blur(10px);"';
    $style3 = 'style="background: transparent;"';
    $style_modal = 'style="background-color: rgba(30, 30, 30, 0.9); -webkit-backdrop-filter: blur(30px); backdrop-filter: blur(5px); color: white !important;"';
} else {
    $style = '';
    $style2 = '';
    $style3 = '';
    $style_modal = '';
}
?>

<div class="row">
    <div class="col-lg-6 col-6">
        <div class="small-box" style="background: rgb(200, 35, 51, 1); backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px);">
            <div class="inner">
                <h3><span id="member_count">0</span></h3>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const targetValue = <?= count($this->M_global->getResult('member')) ?>;
                        const duration = 2000; // Animation duration in milliseconds
                        const counterElement = document.getElementById('member_count');
                        let startValue = 0;
                        const increment = targetValue / (duration / 10);

                        const counterInterval = setInterval(() => {
                            startValue += increment;
                            if (startValue >= targetValue) {
                                startValue = targetValue;
                                clearInterval(counterInterval);
                            }
                            counterElement.textContent = new Intl.NumberFormat().format(Math.floor(startValue));
                        }, 10);
                    });
                </script>
                <p>Jumlah member</p>
            </div>
            <div class="icon">
                <i class="fa fa-fw fa-users"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-6 col-6">
        <div class="small-box" style="background: rgb(0, 123, 255, 1); backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px);">
            <div class="inner">
                <h3><span id="member_count_act">0</span></h3>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const targetValue = <?= count($this->M_global->getDataResult('member', ['actived' => 1])) ?>;
                        const duration = 2000; // Animation duration in milliseconds
                        const counterElement = document.getElementById('member_count_act');
                        let startValue = 0;
                        const increment = targetValue / (duration / 10);

                        const counterInterval = setInterval(() => {
                            startValue += increment;
                            if (startValue >= targetValue) {
                                startValue = targetValue;
                                clearInterval(counterInterval);
                            }
                            counterElement.textContent = new Intl.NumberFormat().format(Math.floor(startValue));
                        }, 10);
                    });
                </script>
                <p>Jumlah member</p>
            </div>
            <div class="icon">
                <i class="fa fa-fw fa-user"></i>
            </div>
        </div>
    </div>
</div>

<form method="post" id="form_daftar">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary" <?= $style ?>>
                <div class="card-header">
                    <span class="font-weight-bold h4"><i class="fa-solid fa-bookmark text-primary"></i> Daftar Member</span>
                    <div class="float-right">
                        <div class="btn-group">
                            <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-circle-down"></i>&nbsp;&nbsp;Unduh
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="preview('member')"><i class="fa-solid fa-fw fa-tv"></i>&nbsp;&nbsp;Preview</a></li>
                                <li><a class="dropdown-item" href="#" onclick="print('member')"><i class="fa-regular fa-fw fa-file-pdf"></i>&nbsp;&nbsp;Pdf</a></li>
                                <li><a class="dropdown-item" href="#" onclick="excel('member')"><i class="fa-regular fa-fw fa-file-excel"></i>&nbsp;&nbsp;Excel</a></li>
                            </ul>
                        </div>
                        <button type="button" class="btn btn-primary" onclick="reloadTable()"><i class="fa-solid fa-rotate-right"></i>&nbsp;&nbsp;Refresh</button>
                        <button type="button" class="btn btn-success" onclick="getUrl('Health/form_daftar/0')" <?= (($created > 0) ? '' : 'disabled') ?>><i class="fa-solid fa-circle-plus"></i>&nbsp;&nbsp;Tambah</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table shadow-sm table-hover table-bordered" id="tableDaftar" width="100%" style="border-radius: 10px;">
                                    <thead>
                                        <tr class="text-center">
                                            <th width="5%" style="border-radius: 10px 0px 0px 0px;">#</th>
                                            <th width="10%">RM</th>
                                            <th width="10%">NIK</th>
                                            <th width="25%">Nama</th>
                                            <th width="20%">Alamat</th>
                                            <th width="15%">Trx Terakhir</th>
                                            <th width="15%" style="border-radius: 0px 10px 0px 0px;">Aksi</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- modal info member -->
<div class="modal" tabindex="-1" id="modal_member">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" <?= $style_modal ?>>
            <div class="modal-header">
                <h5 class="modal-title"><span id="kodeMember"></span> - <span class="badge badge-info" id="on_off"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="tutup()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 m-auto">
                        <img id="preview_img" class="rounded mx-auto d-block" style="border: 2px solid grey; width: 100%;" src="" alt="User profile picture">
                    </div>
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" placeholder="Email" id="email" name="email" value="" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" placeholder="No. Hp" id="nohp" name="nohp" value="" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" placeholder="Gender" id="jkel" name="jkel" value="" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" placeholder="Joined" id="joined" name="joined" value="" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" placeholder="Pendidikan" id="pendidikan" name="pendidikan" value="" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" placeholder="Agama" id="agama" name="agama" value="" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" placeholder="Pekerjaan" id="pekerjaan" name="pekerjaan" value="" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // variable
    var table = $('#tableDaftar');
    var kodeMember = $('#kodeMember');
    const modal_member = $("#modal_member");
    var email = $("#email");
    var nohp = $("#nohp");
    var jkel = $("#jkel");
    var joined = $("#joined");
    var on_off = $("#on_off");
    var pendidikan = $("#pendidikan");
    var pekerjaan = $("#pekerjaan");
    var agama = $("#agama");

    //fungsi ubah berdasarkan lemparan kode
    function ubah(kode_daftar) {
        // jalankan fungsi
        getUrl('Health/form_daftar/' + kode_daftar);
    }

    // fungsi hapus berdasarkan kode_member
    function hapus(kode_member) {
        // ajukan pertanyaaan
        Swal.fire({
            title: "Kamu yakin?",
            text: "Data yang dihapus tidak bisa dikembalikan!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, hapus!",
            cancelButtonText: "Tidak!"
        }).then((result) => {
            if (result.isConfirmed) { // jika yakin

                // jalankan fungsi
                $.ajax({
                    url: siteUrl + 'Health/delMember/' + kode_member,
                    type: 'POST',
                    dataType: 'JSON',
                    success: function(result) { // jika fungsi berjalan dengan baik

                        if (result.status == 1) { // jika mendapatkan hasil 1
                            Swal.fire("Member", "Berhasil di hapus!", "success").then(() => {
                                reloadTable();
                            });
                        } else { // selain itu

                            Swal.fire("Member", "Gagal di hapus!, silahkan dicoba kembali", "info");
                        }
                    },
                    error: function(result) { // jika fungsi error

                        error_proccess();
                    }
                });
            }
        });
    }

    // fungsi aktif/non-aktif akun
    function actived(kode_daftar, param) {
        if (param == 1) {
            var pesan = "Akun ini akan diaktifkan!";
            var pesan2 = "diaktifkan!";
        } else {
            var pesan = "Akun ini akan dinonaktifkan!";
            var pesan2 = "dinonaktifkan!";
        }
        // ajukan pertanyaaan
        Swal.fire({
            title: "Kamu yakin?",
            text: pesan,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, " + pesan2,
            cancelButtonText: "Tidak!"
        }).then((result) => {
            if (result.isConfirmed) { // jika yakin

                // jalankan fungsi
                $.ajax({
                    url: siteUrl + 'Health/activeddaftar/' + kode_daftar + '/' + param,
                    type: 'POST',
                    dataType: 'JSON',
                    success: function(result) { // jika fungsi berjalan dengan baik

                        if (result.status == 1) { // jika mendapatkan hasil 1
                            Swal.fire("Pengguna", "Berhasil " + pesan2, "success").then(() => {
                                reloadTable();
                            });
                        } else { // selain itu

                            Swal.fire("Pengguna", "Gagal " + pesan2 + ", silahkan dicoba kembali", "info");
                        }
                    },
                    error: function(result) { // jika fungsi error

                        error_proccess();
                    }
                });
            }
        });
    }

    // fungsi cek informasi lanjutan
    function info(kode_member) {
        modal_member.modal('show');
        kodeMember.text(kode_member);
        $.ajax({
            url: siteUrl + 'Health/getInfoMember/' + kode_member,
            type: 'POST',
            dataType: 'JSON',
            success: function(result) {
                if (result.on_off == 1) {
                    on_off.text('Online');
                } else {
                    on_off.text('Offline');
                }

                if (result.jkel == 'P') {
                    jkel.val('Gender: Laki-laki');
                    var foto = 'pria.png';
                } else {
                    jkel.val('Gender: Wanita');
                    var foto = 'wanita.png';
                }

                nohp.val('No. Hp: ' + result.nohp);
                email.val('Email: ' + result.email);
                pendidikan.val('Pendidikan: ' + result.pendidikan);
                pekerjaan.val('Pekerjaan: ' + result.pekerjaan);
                agama.val('Agama: ' + result.agama);

                var x = new Date(result.joined)
                var x1 = x.getMonth() + 1 + "/" + x.getDate() + "/" + x.getFullYear();

                joined.val('Gabung: ' + x1);

                $('#preview_img').attr('src', siteUrl + 'assets/member/' + foto);
            },
            error: function(result) {}
        });
    }

    // modal tutup;
    function tutup() {
        modal_member.modal('hide');
    }

    function showGuide() {
        // clean text
        $('#modal_mgLabel').text(``);
        $('#modal-isi').text(``);

        $('#modal_mg').modal('show'); // show modal

        // isi text
        $('#modal_mgLabel').append(`Manual Guide Pendaftaran Member`);
        $('#modal-isi').append(`
            <ol>
                <li style="font-weight: bold;">Tambah Data</li>
                <p>
                    <ul>
                        <li>Klik tombol Tambah</li>
                        <li>Selanjutnya isikan Form yang tersedia<br><span style='color: red;'>Teks berawarna merah</span> mengartikan wajib terisi</li>
                        <li>Klik tombol Proses</li>
                    </ul>
                </p>
                <li style="font-weight: bold;">Ubah Data</li>
                <p>
                    <ul>
                        <li>Klik tombol Ubah pada list data yang ingin di ubah</li>
                        <li>Ubah isi Form yang akan di ubah<br><span style='color: red;'>Teks berawarna merah</span> mengartikan wajib terisi</li>
                        <li>Klik tombol Proses</li>
                    </ul>
                </p>
                <li style="font-weight: bold;">Hapus Data</li>
                <p>
                    <ul>
                        <li>Klik tombol Hapus pada list data yang ingin di hapus</li>
                        <li>Saat Muncul Pop Up, klik "Ya, Hapus"</li>
                    </ul>
                </p>
            </ol>
        `);
    }
</script>