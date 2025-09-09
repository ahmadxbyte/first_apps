<?php
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


<form method="post" id="form_coa">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary" <?= $style ?>>
                <div class="card-header">
                    <span class="font-weight-bold h4"><i class="fa-solid fa-bookmark text-primary"></i> Jurnal Pembelian</span>
                    <div class="float-right">
                        <button type="button" class="btn btn-primary" onclick="reloadTable()"><i class="fa-solid fa-rotate-right"></i>&nbsp;&nbsp;Refresh</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="tableJurnalPembelian" width="100%" style="border-radius: 10px;">
                            <thead>
                                <tr class="text-center">
                                    <th width="5%" style="border-radius: 10px 0px 0px 0px;">#</th>
                                    <th width="15%">ID</th>
                                    <th width="10%">Waktu</th>
                                    <th width="20%">COA</th>
                                    <th width="15%">Debit</th>
                                    <th width="15%">Credit</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    var table = $('#tableJurnalPembelian');
</script>