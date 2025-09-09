<?php
if ($web->ct_theme == 1) {
    $style = 'style="background: rgba(255, 255, 255, 0.6); backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px);"';
    $style2 = 'style="background-color: rgba(255, 255, 255, 0.2); backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px); border-radius: 30px;"';
} else if ($web->ct_theme == 2) {
    $style = 'style="background: rgba(30, 30, 30, 0.8); backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px); color: white !important;"';
    $style2 = 'style="background-color: rgba(30, 30, 30, 0.2); backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px); border-radius: 30px;"';
} else {
    $style = '';
    $style2 = 'style="background-color: rgba(255, 255, 255, 0.2); backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px); border-radius: 30px;"';
}
?>

<form id="form_lockscreen">
    <div class="login-box">
        <div class="card shadow" <?= $style2 ?>>
            <div class="card-body p-5">
                <img src="<?= base_url('assets/user/') . $data_user->foto ?>" class="img-circle elevation-2 shadow" alt="User Image" style="width: 100px; height: 100px; border-radius: 50%; margin: 0 auto; display: block; margin-bottom: 10px">
                <div class="row align-items-center justify-content-center mb-3 text-center">
                    <div class="col-md-12">
                        <span class="font-weight-bold text-white" style="font-size: 25px;"><?= $nama ?></span>
                        <br>
                        <span class="text-white" style="font-size: 12px;"><?= $this->M_global->getData('m_role', ['kode_role' => $data_user->kode_role])->keterangan ?></span>
                        <br>
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="input-group" style="border-radius: 30px; overflow: hidden;">
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="basic-addon2" style="border-radius: 30px 0 0 30px;"><?= $this->M_global->getData('cabang', ['kode_cabang' => $this->session->userdata('cabang')])->cabang ?></span>
                                    </div>
                                    <input type="password" class="form-control" placeholder="Sandi" id="password" name="password" <?= $style ?> autofocus style="border-radius: 0;">
                                    <div class="input-group-append" onclick="unlock()" data-toggle="tooltip" data-placement="top" title="Masuk" type="submit">
                                        <span class="input-group-text" id="basic-addon2" style="border-radius: 0 30px 30px 0;"><i class="fa-solid fa-right-to-bracket"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    // Menangkap event tombol Enter pada input password
    document.getElementById('password').addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            unlock();
        }
    });

    function unlock() {
        var password = document.getElementById('password').value;
        if (password === '') {
            Swal.fire({
                position: "center",
                icon: "error",
                title: "Sandi tidak boleh kosong!",
                showConfirmButton: false,
                timer: 1000
            });

            return false;
        }

        $.ajax({
            url: '<?= base_url('Auth/unlock') ?>',
            type: 'POST',
            data: $('#form_lockscreen').serialize(),
            dataType: 'JSON',
            success: function(response) {
                if (response.status == 1) {
                    var lastUrlStored = localStorage.getItem('last_url');
                    if (lastUrlStored) {
                        window.location.href = lastUrlStored;
                    }
                } else {
                    $('#password').val('')

                    Swal.fire({
                        position: "center",
                        icon: "warning",
                        title: "Sandi salah!, silahkan coba lagi.",
                        showConfirmButton: false,
                        timer: 1000
                    });
                }
            },
            error: function(error) {
                $('#password').val('')

                error_proccess();
            }
        });
    }
</script>