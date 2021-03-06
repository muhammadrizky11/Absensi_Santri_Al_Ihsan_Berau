<?php
// tampilkan data mengajar
$kelasMengajar = mysqli_query($con, "SELECT * FROM tb_mengajar 

INNER JOIN tb_master_mapel ON tb_mengajar.id_mapel=tb_master_mapel.id_mapel
INNER JOIN tb_mkelas ON tb_mengajar.id_mkelas=tb_mkelas.id_mkelas

INNER JOIN tb_semester ON tb_mengajar.id_semester=tb_semester.id_semester
INNER JOIN tb_thajaran ON tb_mengajar.id_thajaran=tb_thajaran.id_thajaran
WHERE tb_mengajar.id_guru='$data[id_guru]' AND tb_mengajar.id_mengajar='$_GET[pelajaran]'  AND tb_thajaran.status=1  ");

foreach ($kelasMengajar as $d)
?>
<div class="page-inner">
    <div class="page-header">
        <h4 class="page-title">KELAS (<?= strtoupper($d['nama_kelas']) ?> )</h4>
        <ul class="breadcrumbs" style="font-weight: bold;">
            <li class="nav-home">
                <a href="#">
                    <i class="flaticon-home"></i>
                </a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">KELAS (<?= strtoupper($d['nama_kelas']) ?> )</a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#"><?= strtoupper($d['mapel']) ?></a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-8 col-xs-12">
            <?php
            // dapatkan pertemuan terakhir di tb izin
            $last_pertemuan = mysqli_query($con, "SELECT * FROM _logabsensi WHERE id_mengajar='$_GET[pelajaran]'ORDER BY pertemuan_ke DESC LIMIT 1  ");
            $cekPertemuan = mysqli_num_rows($last_pertemuan);
            $jml = mysqli_fetch_array($last_pertemuan);

            if ($cekPertemuan > 0) {
                $pertemuan = $jml['pertemuan_ke'];
            } else {
                $pertemuan = 1;
            }

            ?>

            <hr>
            <div class="card col-8">
                <div class="card-body">
                    <h2>Update Absen Santri</h2>
                    <hr>
                    <form action="" method="post">
                        <input type="hidden" name="pertemuan" class="form-control" value="<?= $pertemuan; ?>">
                        <input type="hidden" name="pelajaran" value="<?= $_GET['pelajaran'] ?>">
                        <table>
                            <?php

                            // tampilakan data santri berdasarkan kelas yang dipilih
                            $tgl_hari_ini = date('Y-m-d');

                            $santri = mysqli_query($con, "SELECT * FROM _logabsensi INNER JOIN tb_santri ON _logabsensi.id_santri=tb_santri.id_santri WHERE  _logabsensi.tgl_absen='$tgl_hari_ini' AND _logabsensi.id_mengajar='$_GET[pelajaran]' ORDER BY _logabsensi.id_santri ASC  ");
                            $jumlahsantri = mysqli_num_rows($santri);

                            foreach ($santri as $i => $s) { ?>

                                <tr>
                                    <div class="col-lg">
                                        <b class="text-success"><?= $s['nama_santri']; ?></b>
                                        <?php if ($s['ket'] == '') {
                                            echo "<span class='text-danger'>Belum Absen</span>";
                                        } ?>
                                        <input type="hidden" name="id_santri-<?= $i; ?>" value="<?= $s['id_santri'] ?>">
                                        <div class="form-check form-check-inline">


                                            <label class="form-check-label">
                                                <input name="ket-<?= $i; ?>" class="form-check-input" type="radio" value="H" <?php if ($s['ket'] == 'H') {
                                                                                                                                    echo "checked";
                                                                                                                                } ?>>
                                                <span class="form-check-sign">H</span>
                                            </label>

                                            <label class="form-check-label">
                                                <input name="ket-<?= $i; ?>" class="form-check-input" type="radio" value="I" <?php if ($s['ket'] == 'I') {
                                                                                                                                    echo "checked";
                                                                                                                                } ?>>
                                                <span class="form-check-sign">I</span>
                                            </label>

                                            <label class="form-check-label">
                                                <input name="ket-<?= $i; ?>" class="form-check-input" type="radio" value="S" <?php if ($s['ket'] == 'S') {
                                                                                                                                    echo "checked";
                                                                                                                                } ?>>
                                                <span class="form-check-sign">S</span>
                                            </label>

                                            <label class="form-check-label">
                                                <input name="ket-<?= $i; ?>" class="form-check-input" type="radio" value="A" <?php if ($s['ket'] == 'A') {
                                                                                                                                    echo "checked";
                                                                                                                                } ?>>
                                                <span class="form-check-sign">A</span>
                                            </label>

                                        </div>
                                        </td>
                                </tr>
                            <?php } ?>
                        </table>
                </div>

                <center>

                    <button type="submit" name="update" class="btn btn-success mb-1">
                        <i class="fa fa-check"></i> Update Absensi
                    </button>

                    <a href="javascript:history.back()" class="btn btn-default"><i class="fas fa-arrow-circle-left"></i>
                        Kembali</a>
                    <hr>
                </center>
            </div>
            </form>

            <?php
            if (isset($_POST['update'])) {

                $total = $jumlahsantri - 1;


                for ($i = 0; $i <= $total; $i++) {

                    $pertemuan = $_POST['pertemuan'];
                    $hari_sekarang = date('Y-m-d');

                    $id_santri = $_POST['id_santri-' . $i];
                    $pelajaran = $_POST['pelajaran'];
                    $ket = $_POST['ket-' . $i];

                    echo "<pre>";
                    print_r($_POST);
                    echo "</pre>";

                    $updte_absen = mysqli_query($con, "UPDATE _logabsensi SET ket='$ket' WHERE id_mengajar='$pelajaran' AND id_santri='$id_santri' AND tgl_absen='$hari_sekarang' ");

                    if ($updte_absen) {


                        echo "
											<script type='text/javascript'>
											setTimeout(function () { 

											swal('Berhasil', 'Absensi Telah berubah', {
											icon : 'success',
											buttons: {        			
											confirm: {
											className : 'btn btn-success'
											}
											},
											});    
											},10);  
											window.setTimeout(function(){ 
											window.location.replace('?page=absen&act=update&pelajaran=$_GET[pelajaran]');
											} ,3000);   
											</script>";
                    }
                }
            } ?>
        </div>
    </div>
</div>
</div>