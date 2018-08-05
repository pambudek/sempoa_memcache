<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BIWebHelper
 *
 * @author efindiongso
 */
class BIWebHelper extends WebService
{

    public function loadRekapSiswa()
    {
        $myOrgID = AccessRight::getMyOrgID();
        $arrMyIBO = Generic::getAllMyIBO(AccessRight::getMyOrgID());
        $bln = isset($_GET['bln']) ? addslashes($_GET['bln']) : date("n");
        $thn = isset($_GET['thn']) ? addslashes($_GET['thn']) : date("Y");
        $ibo_id = isset($_GET['ibo_id']) ? addslashes($_GET['ibo_id']) : key($arrMyIBO);
        $arrMyTC = Generic::getAllMyTC($ibo_id);
        $waktu = $bln . "-" . $thn;
        $dataArray = array();
        $i = 0;
        foreach ($arrMyTC as $keytc => $tc) {
            $rekap_siswa = new RekapSiswaIBOModel();
            $arrRekapSiswa = $rekap_siswa->getWhere("bi_rekap_tc_id = '$keytc' AND bi_rekap_ibo_id='$ibo_id' AND bi_rekap_siswa_waktu = '$waktu' ");

            if (count($arrRekapSiswa) > 0) {
                $hasil[$i]['tc'] = $tc;
                $hasil[$i]['bln'] = $bln;
                $hasil[$i]['thn'] = $thn;
                $hasil[$i]['baru'] = $arrRekapSiswa[0]->bi_rekap_baru;
                $hasil[$i]['keluar'] = $arrRekapSiswa[0]->bi_rekap_keluar;
                $hasil[$i]['cuti'] = $arrRekapSiswa[0]->bi_rekap_cuti;
                $hasil[$i]['lulus'] = $arrRekapSiswa[0]->bi_rekap_lulus;
                $hasil[$i]['aktiv'] = $arrRekapSiswa[0]->bi_rekap_aktiv;
                $hasil[$i]['kupon'] = $arrRekapSiswa[0]->bi_rekap_kupon;
                $data = new DataBI();
                $data->bln = $bln;
                $data->thn = $thn;
                $data->baru = $arrRekapSiswa[0]->bi_rekap_baru;
                $data->keluar = $arrRekapSiswa[0]->bi_rekap_keluar;
                $data->cuti = $arrRekapSiswa[0]->bi_rekap_cuti;
                $data->lulus = $arrRekapSiswa[0]->bi_rekap_lulus;
                $data->aktiv = $arrRekapSiswa[0]->bi_rekap_aktiv;
                $data->kupon = $arrRekapSiswa[0]->bi_rekap_kupon;
                $dataArray[] = $data;
                $i++;
            }
        }
// 
        $ibo = new SempoaOrg();
        $arrIbo = $ibo->getWhere("org_type='ibo' AND org_parent_id='$myOrgID'");
        $allIbo = array();
        foreach ($arrIbo as $val) {
            $allIbo[$val->org_id] = $val->nama;
        }

//        pr($allIbo);
//        pr($allIbo);
        $hasilIBO = array();
        $i = 0;
        foreach ($allIbo as $keyibo => $ibo) {
            $rekap_siswa = new RekapSiswaIBOModel();
            $arrRekapSiswa = $rekap_siswa->getWhere("bi_rekap_ibo_id='$keyibo' AND bi_rekap_siswa_waktu = '$waktu' ");

//            if (count($arrRekapSiswa) > 0) {
            $hasilIBO[$i]['ibo'] = $ibo;
            $hasilIBO[$i]['bln'] = $bln;
            $hasilIBO[$i]['thn'] = $thn;
            if (count($arrRekapSiswa) == 0) {
                $hasilIBO[$i]['baru'] = 0;
                $hasilIBO[$i]['keluar'] = 0;
                $hasilIBO[$i]['cuti'] = 0;
                $hasilIBO[$i]['lulus'] = 0;
                $hasilIBO[$i]['aktiv'] = 0;
                $hasilIBO[$i]['kupon'] = 0;
            } else {
                foreach ($arrRekapSiswa as $val) {
                    $hasilIBO[$i]['baru'] += $val->bi_rekap_baru;
                    $hasilIBO[$i]['keluar'] += $val->bi_rekap_keluar;
                    $hasilIBO[$i]['cuti'] += $val->bi_rekap_cuti;
                    $hasilIBO[$i]['lulus'] += $val->bi_rekap_lulus;
                    $hasilIBO[$i]['aktiv'] += $val->bi_rekap_aktiv;
                    $hasilIBO[$i]['kupon'] += $val->bi_rekap_kupon;
                }
                $i++;
            }
//            }
        }
//        pr($hasilIBO);
// Jumlah Siswa dan Coach
        $i = 0;
        $hasilIBOCouch = array();
        foreach ($allIbo as $keyibo => $ibo) {
            $rekap_siswa = new RekapSiswaIBOModel();
            $arrRekapSiswa = $rekap_siswa->getWhere("bi_rekap_ibo_id='$keyibo' AND bi_rekap_siswa_waktu = '$waktu' ");

            $arrMyTC = Generic::getAllMyTC($keyibo);
            if (count($arrRekapSiswa) > 0) {
                foreach ($arrMyTC as $keytc => $tc) {
                    $rekap_siswa_tmp = new RekapSiswaIBOModel();
                    $rekap_siswa_tmp->getWhereOne("bi_rekap_ibo_id='$keyibo' AND bi_rekap_tc_id='$keytc' AND bi_rekap_siswa_waktu = '$waktu' ");
                    $hasilIBOCouch[$i]['bln'] = $bln;
                    $hasilIBOCouch[$i]['thn'] = $thn;
                    $hasilIBOCouch[$i]['tc'] = $rekap_siswa_tmp->bi_rekap_nama_tc;
                    $hasilIBOCouch[$i]['Coach'] = $rekap_siswa_tmp->bi_rekap_jumlah_guru;
                    $hasilIBOCouch[$i]['Siswa'] = $rekap_siswa_tmp->bi_rekap_aktiv;
                    $i++;
                }
            }
        }

        $i = 0;
        $hasilIBOBuku = array();
        $rekap_siswa = new RekapSiswaIBOModel();
        $arrRekapSiswa = $rekap_siswa->getWhere("bi_rekap_ibo_id='$ibo_id' AND bi_rekap_tahun = $thn ");
        foreach ($arrRekapSiswa as $key => $val) {
            $hasilIBOBuku[$i]['Ibo'] = Generic::getTCNamebyID($val->bi_rekap_ibo_id);
            $hasilIBOBuku[$i]['Ibo_id'] = ($val->bi_rekap_ibo_id);
            $hasilIBOBuku[$i]['Siswa'] += $val->bi_rekap_aktiv;
            $hasilIBOBuku[$i]['Buku'] += $val->bi_rekap_buku;
            $hasilIBOBuku[$i]['Kupon'] += $val->bi_rekap_kupon;
        }
        ?>

        <div class="row box-body chart-responsive">
            <div class="col-xs-12">
                <h3>Rekapitulasi Siswa IBO per <?= $bln . "/" . $thn; ?> </h3>
                <div class="chart" id="bar-chart-ibo" style="height: 300px; background-color: white;">
                </div>
            </div>

            <div class="col-xs-12">
                <h3>Rekapitulasi Siswa TC per <?= $bln . "/" . $thn; ?> </h3>
                <div class="chart" id="bar-chart" style="height: 300px; background-color: white;">

                </div>
            </div>
            <div class="col-xs-12">
                <h3>Laporan Perkembangan IBO Berdasarkan Jumlah Siswa dan Coach <?= $bln . "/" . $thn; ?> </h3>
                <div class="chart" id="bar-chart-coach" style="height: 300px; background-color: white;">

                </div>
            </div>
            <div class="col-xs-12">
                <h3>Laporan Siswa, Buku dan Kupon per IBO <?= $bln . "/" . $thn; ?> </h3>
                <div class="chart" id="bar-chart-buku" style="height: 300px; background-color: white;">

                </div>
            </div>

        </div>


        <script>

            Morris.Bar({
                element: 'bar-chart',
                data: <? echo json_encode($hasil); ?>,
                xkey: 'tc',
                ykeys: ['aktiv', 'baru', 'cuti', 'keluar', 'lulus', 'kupon'],
                labels: ['Aktiv', 'Baru', 'Cuti', 'Keluar', 'Lulus', "Kupon"],
            });

            Morris.Bar({
                element: 'bar-chart-ibo',
                data: <? echo json_encode($hasilIBO); ?>,
                xkey: 'ibo',
                ykeys: ['aktiv', 'baru', 'cuti', 'keluar', 'lulus', 'kupon'],
                labels: ['Aktiv', 'Baru', 'Cuti', 'Keluar', 'Lulus', "Kupon"],
            });
            Morris.Bar({
                element: 'bar-chart-coach',
                data: <? echo json_encode($hasilIBOCouch); ?>,
                xkey: 'tc',
                ykeys: ['Coach', 'Siswa'],
                labels: ['Coach', 'Siswa'],
            });

            Morris.Bar({
                element: 'bar-chart-buku',
                data: <? echo json_encode($hasilIBOBuku); ?>,
                xkey: 'Ibo',
                ykeys: ['Siswa', 'Buku', 'Kupon'],
                labels: ['Siswa', 'Buku', 'Kupon']
            });
        </script>
        <?
    }

    function loadRekapSiswaTable()
    {
        $kpo_id = AccessRight::getMyOrgID();
        $arrMyIBO = Generic::getAllMyIBO($kpo_id);
        $t = time();
        $bln = isset($_GET['bln']) ? addslashes($_GET['bln']) : date("n");
        $thn = isset($_GET['thn']) ? addslashes($_GET['thn']) : date("Y");
        $ibo_id = isset($_GET['ibo_id']) ? addslashes($_GET['ibo_id']) : key($arrMyIBO);

        $arrBulan = Generic::getAllMonths();
        $arrBulan[count($arrBulan) + 1] = "All";
        $arrMyTC = Generic::getAllMyTC($ibo_id);
        $i = 1;
        ?>
        <table class="table table-bordered table-striped table-sempoa-border" style='background-color: white'>
            <thead>
            <tr>
                <th class="tengah" rowspan="2">No.</th>
                <th class="tengah" rowspan="2">Kode TC</th>
                <th class="tengah" rowspan="2">Nama TC</th>
                <th class="tengah" rowspan="2">Nama Director</th>

                <?
                if ($bln != "All") {
                    ?>
                    <th id="<?= $bln . "_" . $thn; ?>" class="total"
                        colspan="7"><?= Generic::getMonthName($bln); ?></th>

                    <?
                } else {
                    foreach ($arrBulan as $val) {
                        if ($val != "All") {
                            ?>
                            <th id="<?= $val . "_" . $thn; ?>" class="total"
                                colspan="7"><?= Generic::getMonthName($val); ?></th>

                            <?
                        }
                    }
                }
                ?>

            </tr>
            <?
            if ($bln != "All") {
                ?>
                <tr>
                    <th>BL</th>
                    <th>B</th>
                    <th>K</th>
                    <th>C</th>
                    <th>L</th>
                    <th>A</th>
                    <th>KPN</th>

                </tr>
                <?
            } else {
                ?>
                <tr>
                    <?
                    foreach ($arrBulan as $val) {
                        if ($val != "All") {
                            ?>

                            <th>BL</th>
                            <th>B</th>
                            <th>K</th>
                            <th>C</th>
                            <th>L</th>
                            <th>A</th>
                            <th>KPN</th>


                            <?
                        }
                    }
                    ?>
                </tr>
                <?
            }
            ?>

            </thead>
            <tbody id="load_<?= $bln . "_" . $thn; ?>">
            <?
            $i = 1;
            $total_bl = 0;
            $total_baru = 0;
            $total_keluar = 0;
            $total_cuti = 0;
            $total_lulus = 0;
            $total_aktiv = 0;
            $total_kupon = 0;
            $arrTotal_bl = array();
            $arrTotal_baru = array();
            $arrTotal_keluar = array();
            $arrTotal_cuti = array();
            $arrTotal_lulus = array();
            $arrTotal_aktiv = array();
            $arrTotal_kupon = array();
            foreach ($arrMyTC as $id_tc => $tc) {
                $orgTC = new SempoaOrg();
                $orgTC->getByID($id_tc);
                ?>
                <tr>
                    <td><?= $i; ?></td>
                    <td><?= $orgTC->org_kode; ?></td>
                    <td><?= $orgTC->nama; ?></td>
                    <td><?= $orgTC->nama_pemilik; ?></td>
                    <?
                    if ($bln != "All") {
                        $org_tc = new RekapSiswaIBOModel();
                        $arrDaten = $org_tc->getDaten($bln, $thn, $orgTC->nama);
                        $total_bl += $arrDaten[0]->bi_rekap_bl;
                        $total_baru += $arrDaten[0]->bi_rekap_baru;
                        $total_keluar += $arrDaten[0]->bi_rekap_keluar;
                        $total_cuti += $arrDaten[0]->bi_rekap_cuti;
                        $total_lulus += $arrDaten[0]->bi_rekap_lulus;
                        $total_aktiv += $arrDaten[0]->bi_rekap_aktiv;
                        $total_kupon += $arrDaten[0]->bi_rekap_kupon;

                        if (is_null($arrDaten[0]->bi_rekap_bl)) {
                            $bl = 0;
                        } else {
                            $bl = $arrDaten[0]->bi_rekap_bl;
                        }

                        if (is_null($arrDaten[0]->bi_rekap_baru)) {
                            $baru = 0;
                        } else {
                            $baru = $arrDaten[0]->bi_rekap_baru;
                        }

                        if (is_null($arrDaten[0]->bi_rekap_keluar)) {
                            $keluar = 0;
                        } else {
                            $keluar = $arrDaten[0]->bi_rekap_keluar;
                        }

                        if (is_null($arrDaten[0]->bi_rekap_cuti)) {
                            $cuti = 0;
                        } else {
                            $cuti = $arrDaten[0]->bi_rekap_cuti;
                        }


                        if (is_null($arrDaten[0]->bi_rekap_lulus)) {
                            $lulus = 0;
                        } else {
                            $lulus = $arrDaten[0]->bi_rekap_lulus;
                        }
                        if (is_null($arrDaten[0]->bi_rekap_aktiv)) {
                            $aktiv = 0;
                        } else {
                            $aktiv = $arrDaten[0]->bi_rekap_aktiv;
                        }

                        if (is_null($arrDaten[0]->bi_rekap_kupon)) {
                            $kpn = 0;
                        } else {
                            $kpn = $arrDaten[0]->bi_rekap_kupon;
                        }
                        ?>
                        <td><?= $bl; ?></td>
                        <td><?= $baru; ?></td>
                        <td><?= $keluar; ?></td>
                        <td><?= $cuti; ?></td>
                        <td><?= $lulus; ?></td>
                        <td><?= $aktiv; ?></td>
                        <td><?= $kpn; ?></td>
                        <?
                    } else {

                        foreach ($arrBulan as $val) {
                            if ($val != "All") {
                                $org_tc = new RekapSiswaIBOModel();
                                $arrDaten = $org_tc->getDaten($val, $thn, $orgTC->nama);
                                $arrTotal_bl[$val] += $arrDaten[0]->bi_rekap_bl;
                                $arrTotal_baru[$val] += $arrDaten[0]->bi_rekap_baru;
                                $arrTotal_keluar[$val] += $arrDaten[0]->bi_rekap_keluar;
                                $arrTotal_cuti[$val] += $arrDaten[0]->bi_rekap_cuti;
                                $arrTotal_lulus[$val] += $arrDaten[0]->bi_rekap_lulus;
                                $arrTotal_aktiv[$val] += $arrDaten[0]->bi_rekap_aktiv;
                                $arrTotal_kupon[$val] += $arrDaten[0]->bi_rekap_kupon;

                                if (is_null($arrDaten[0]->bi_rekap_bl)) {
                                    $bl = 0;
                                } else {
                                    $bl = $arrDaten[0]->bi_rekap_bl;
                                }

                                if (is_null($arrDaten[0]->bi_rekap_baru)) {
                                    $baru = 0;
                                } else {
                                    $baru = $arrDaten[0]->bi_rekap_baru;
                                }

                                if (is_null($arrDaten[0]->bi_rekap_keluar)) {
                                    $keluar = 0;
                                } else {
                                    $keluar = $arrDaten[0]->bi_rekap_keluar;
                                }

                                if (is_null($arrDaten[0]->bi_rekap_cuti)) {
                                    $cuti = 0;
                                } else {
                                    $cuti = $arrDaten[0]->bi_rekap_cuti;
                                }


                                if (is_null($arrDaten[0]->bi_rekap_lulus)) {
                                    $lulus = 0;
                                } else {
                                    $lulus = $arrDaten[0]->bi_rekap_lulus;
                                }
                                if (is_null($arrDaten[0]->bi_rekap_aktiv)) {
                                    $aktiv = 0;
                                } else {
                                    $aktiv = $arrDaten[0]->bi_rekap_aktiv;
                                }

                                if (is_null($arrDaten[0]->bi_rekap_kupon)) {
                                    $kpn = 0;
                                } else {
                                    $kpn = $arrDaten[0]->bi_rekap_kupon;
                                }
                                ?>
                                <td><?= $bl; ?></td>
                                <td><?= $baru; ?></td>
                                <td><?= $keluar; ?></td>
                                <td><?= $cuti; ?></td>
                                <td><?= $lulus; ?></td>
                                <td><?= $aktiv; ?></td>
                                <td><?= $kpn; ?></td>
                                <?
                            }
                        }
//                            pr($arrTotal_kupon);
                    }
                    ?>

                </tr>
                <?
                $i++;
            }
            ?>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="4" class="total">Total</td>
                <?
                if ($bln != "All") {
                    ?>
                    <td><?= $total_bl; ?></td>
                    <td><?= $total_baru; ?></td>
                    <td><?= $total_keluar; ?></td>
                    <td><?= $total_cuti; ?></td>
                    <td><?= $total_lulus; ?></td>
                    <td><?= $total_aktiv; ?></td>
                    <td><?= $total_kupon; ?></td>
                    <?
                } else {
                    foreach ($arrBulan as $val) {
                        if ($val != "All") {
                            ?>
                            <td><?= $arrTotal_bl[$val]; ?></td>
                            <td><?= $arrTotal_baru[$val]; ?></td>
                            <td><?= $arrTotal_keluar[$val]; ?></td>
                            <td><?= $arrTotal_cuti[$val]; ?></td>
                            <td><?= $arrTotal_lulus[$val]; ?></td>
                            <td><?= $arrTotal_aktiv[$val]; ?></td>
                            <td><?= $arrTotal_kupon[$val]; ?></td>
                            <?
                        }
                    }
                }
                ?>

            </tr>
            </tfoot>
        </table>


        <?
        $hasilTC = array();
        $waktu = $bln . "-" . $thn;
        $t = time();
        if ($bln != "All") {
            $i = 0;
            foreach ($arrMyTC as $keytc => $tc) {
                $rekap_siswa = new RekapSiswaIBOModel();
                $arrRekapSiswa = $rekap_siswa->getWhere("bi_rekap_tc_id = '$keytc' AND bi_rekap_ibo_id='$ibo_id' AND bi_rekap_siswa_waktu = '$waktu' ");

                if (count($arrRekapSiswa) > 0) {
                    $hasil[$i]['tc'] = $tc;
                    $hasil[$i]['bln'] = $bln;
                    $hasil[$i]['thn'] = $thn;
                    $hasil[$i]['bl'] = $arrRekapSiswa[0]->bi_rekap_bl;
                    $hasil[$i]['baru'] = $arrRekapSiswa[0]->bi_rekap_baru;
                    $hasil[$i]['keluar'] = $arrRekapSiswa[0]->bi_rekap_keluar;
                    $hasil[$i]['cuti'] = $arrRekapSiswa[0]->bi_rekap_cuti;
                    $hasil[$i]['lulus'] = $arrRekapSiswa[0]->bi_rekap_lulus;
                    $hasil[$i]['aktiv'] = $arrRekapSiswa[0]->bi_rekap_aktiv;
                    $hasil[$i]['kupon'] = $arrRekapSiswa[0]->bi_rekap_kupon;

                    $i++;
                }
            }
        } else {
            foreach ($arrBulan as $val) {
                if ($val != "All") {
                    $i = 0;
                    foreach ($arrMyTC as $keytc => $tc) {
                        $waktu = $val . "-" . $thn;
                        $rekap_siswa = new RekapSiswaIBOModel();
                        $arrRekapSiswa = $rekap_siswa->getWhere("bi_rekap_tc_id = '$keytc' AND bi_rekap_ibo_id='$ibo_id' AND bi_rekap_siswa_waktu = '$waktu' ");

                        if (count($arrRekapSiswa) > 0) {
                            $hasil[$i]['tc'] = $tc;
                            $hasil[$i]['bln'] = $bln;
                            $hasil[$i]['thn'] = $thn;
                            $hasil[$i]['waktu'] = $waktu;
                            $hasil[$i]['bl'] = $arrRekapSiswa[0]->bi_rekap_bl;
                            $hasil[$i]['baru'] = $arrRekapSiswa[0]->bi_rekap_baru;
                            $hasil[$i]['keluar'] = $arrRekapSiswa[0]->bi_rekap_keluar;
                            $hasil[$i]['cuti'] = $arrRekapSiswa[0]->bi_rekap_cuti;
                            $hasil[$i]['lulus'] = $arrRekapSiswa[0]->bi_rekap_lulus;
                            $hasil[$i]['aktiv'] = $arrRekapSiswa[0]->bi_rekap_aktiv;
                            $hasil[$i]['kupon'] = $arrRekapSiswa[0]->bi_rekap_kupon;

                            $i++;
                        }
                    }
                }
            }
        }
        ?>
        <div class="clearfix"></div>
        <div class="row box-body chart-responsive">
            <div class="col-xs-12">
                <h3>Rekapitulasi Siswa TC per <?= $bln . "/" . $thn; ?> </h3>
                <div class="chart" id="bar-chart-<?= $t; ?>" style="height: 300px; background-color: white">

                </div>
            </div>
        </div>

        <script>

            Morris.Bar({
                element: 'bar-chart-<?= $t; ?>',
                data: <? echo json_encode($hasil); ?>,
                xkey: 'tc',
                ykeys: ['bl', 'aktiv', 'baru', 'cuti', 'keluar', 'lulus', 'kupon'],
                labels: ['BL', 'Aktiv', 'Baru', 'Cuti', 'Keluar', 'Lulus', "Kupon"],
            });
        </script>
        <?
    }

    function loadRekapAllSiswaTable()
    {
        $bln = isset($_GET['bln']) ? addslashes($_GET['bln']) : date("n");
        $thn = isset($_GET['thn']) ? addslashes($_GET['thn']) : date("Y");
        $arrAllIBO = Generic::getAllMyIBO(AccessRight::getMyOrgID());

        $t = time();
        $arrBulan = Generic::getAllMonths();
        $arrBulan[count($arrBulan) + 1] = "All";
        ?>
        <div class="table-responsive">
            <table class="table table-bordered table-sempoa-border table-striped" style="background-color: white;">
                <thead>
                <tr>
                    <th class="tengah" rowspan="2">No.</th>
                    <th class="tengah" rowspan="2">Wilayah</th>
                    <th class="tengah" rowspan="2">Nama IBO</th>

                    <?
                    if ($bln != "All") {
                        ?>
                        <th id="<?= $bln . "_" . $thn; ?>" class="total"
                            colspan="7"><?= Generic::getMonthName($bln); ?></th>

                        <?
                    } else {
                        foreach ($arrBulan as $val) {
                            if ($val != "All") {
                                ?>
                                <th id="<?= $val . "_" . $thn; ?>" class="total"
                                    colspan="7"><?= Generic::getMonthName($val); ?></th>

                                <?
                            }
                        }
                    }
                    ?>

                </tr>
                <?
                if ($bln != "All") {
                    ?>
                    <tr>
                        <th>BL</th>
                        <th>B</th>
                        <th>K</th>
                        <th>C</th>
                        <th>L</th>
                        <th>A</th>
                        <th>KPN</th>

                    </tr>
                    <?
                } else {
                    ?>
                    <tr>
                        <?
                        foreach ($arrBulan as $val) {
                            if ($val != "All") {
                                ?>

                                <th>BL</th>
                                <th>B</th>
                                <th>K</th>
                                <th>C</th>
                                <th>L</th>
                                <th>A</th>
                                <th>KPN</th>


                                <?
                            }
                        }
                        ?>
                    </tr>
                    <?
                }
                ?>

                </thead>
                <tbody>
                <?
                if ($bln != "All") {
                    $index = 1;
                    $i = 0;
                    $hasilIBO = array();
                    foreach ($arrAllIBO as $key => $ibo) {
                        $ibo_org = new SempoaOrg;
                        $ibo_org->getByID($key);
                        $waktu = $bln . "-" . $thn;
                        $rekap_siswa = new RekapSiswaIBOModel();
                        $arrRekapSiswa = $rekap_siswa->getWhere("bi_rekap_ibo_id='$key' AND bi_rekap_siswa_waktu = '$waktu' ");
                        ?>
                        <tr>
                            <td><?= $index; ?></td>
                            <td><?= $ibo_org->propinsi; ?></td>
                            <td><?= $ibo_org->nama; ?></td>
                            <?
                            $hasilIBO[$i]['ibo'] = $ibo;
                            $hasilIBO[$i]['bln'] = $bln;
                            $hasilIBO[$i]['thn'] = $thn;
                            if (count($arrRekapSiswa) == 0) {
                                $hasilIBO[$i]['bl'] = 0;
                                $hasilIBO[$i]['baru'] = 0;
                                $hasilIBO[$i]['keluar'] = 0;
                                $hasilIBO[$i]['cuti'] = 0;
                                $hasilIBO[$i]['lulus'] = 0;
                                $hasilIBO[$i]['aktiv'] = 0;
                                $hasilIBO[$i]['kupon'] = 0;
                            } else {
                                foreach ($arrRekapSiswa as $val) {
                                    $hasilIBO[$i]['bl'] += $val->bi_rekap_bl;
                                    $hasilIBO[$i]['baru'] += $val->bi_rekap_baru;
                                    $hasilIBO[$i]['keluar'] += $val->bi_rekap_keluar;
                                    $hasilIBO[$i]['cuti'] += $val->bi_rekap_cuti;
                                    $hasilIBO[$i]['lulus'] += $val->bi_rekap_lulus;
                                    $hasilIBO[$i]['aktiv'] += $val->bi_rekap_aktiv;
                                    $hasilIBO[$i]['kupon'] += $val->bi_rekap_kupon;
                                }
                            }
                            ?>
                            <td><?= $hasilIBO[$i]['bl']; ?></td>
                            <td><?= $hasilIBO[$i]['baru']; ?></td>
                            <td><?= $hasilIBO[$i]['keluar']; ?></td>
                            <td><?= $hasilIBO[$i]['cuti']; ?></td>
                            <td><?= $hasilIBO[$i]['lulus']; ?></td>
                            <td><?= $hasilIBO[$i]['aktiv']; ?></td>
                            <td><?= $hasilIBO[$i]['kupon']; ?></td>
                        </tr>
                        <?
                        $i++;
                        $index++;
                        ?>
                        <?
                    }
                } else {
                    $index = 1;
                    $i = 0;
                    $hasilIBO = array();
                    foreach ($arrAllIBO as $key => $ibo) {
                        $ibo_org = new SempoaOrg;
                        $ibo_org->getByID($key);
                        ?>
                        <tr>
                            <td><?= $index; ?></td>
                            <td><?= $ibo_org->propinsi; ?></td>
                            <td><?= $ibo_org->nama; ?></td>
                            <?
                            foreach ($arrBulan as $valThn) {
                                if ($valThn != "All") {
                                    $waktu = $valThn . "-" . $thn;
                                    $rekap_siswa = new RekapSiswaIBOModel();
                                    $arrRekapSiswa = $rekap_siswa->getWhere("bi_rekap_ibo_id='$key' AND bi_rekap_siswa_waktu = '$waktu' ");


                                    if (count($arrRekapSiswa) == 0) {
                                        $hasilIBO[$valThn]['bl'] = 0;
                                        $hasilIBO[$valThn]['baru'] = 0;
                                        $hasilIBO[$valThn]['keluar'] = 0;
                                        $hasilIBO[$valThn]['cuti'] = 0;
                                        $hasilIBO[$valThn]['lulus'] = 0;
                                        $hasilIBO[$valThn]['aktiv'] = 0;
                                        $hasilIBO[$valThn]['kupon'] = 0;
                                    } else {
                                        foreach ($arrRekapSiswa as $val) {
                                            $hasilIBO[$valThn]['bl'] += $val->bi_rekap_bl;
                                            $hasilIBO[$valThn]['baru'] += $val->bi_rekap_baru;
                                            $hasilIBO[$valThn]['keluar'] += $val->bi_rekap_keluar;
                                            $hasilIBO[$valThn]['cuti'] += $val->bi_rekap_cuti;
                                            $hasilIBO[$valThn]['lulus'] += $val->bi_rekap_lulus;
                                            $hasilIBO[$valThn]['aktiv'] += $val->bi_rekap_aktiv;
                                            $hasilIBO[$valThn]['kupon'] += $val->bi_rekap_kupon;
                                        }
                                    }
                                    ?>
                                    <td><?= $hasilIBO[$valThn]['bl']; ?></td>
                                    <td><?= $hasilIBO[$valThn]['baru']; ?></td>
                                    <td><?= $hasilIBO[$valThn]['keluar']; ?></td>
                                    <td><?= $hasilIBO[$valThn]['cuti']; ?></td>
                                    <td><?= $hasilIBO[$valThn]['lulus']; ?></td>
                                    <td><?= $hasilIBO[$valThn]['aktiv']; ?></td>
                                    <td><?= $hasilIBO[$valThn]['kupon']; ?></td>
                                    <?
                                }
                            }
                            ?>
                        </tr>
                        <?
                        $index++;
                    }
                }
                ?>
                </tbody>
            </table>
        </div>
        <div class="clearfix"></div>
        <div class="row box-body chart-responsive">
            <div class="col-xs-12">
                <h3> <?//                = $bln . "/" . $thn;
                    ?> </h3>
                <div class="chart" id="bar-chart-ibo-<?= $t; ?>" style="height: 300px;">

                </div>
            </div>
        </div>

        <script>

            //            Morris.Bar({
            //                element: 'bar-chart-ibo-<?= $t; ?>',
            //                data: <? // echo json_encode($hasilIBO);    ?>,
            //                xkey: 'ibo',
            //                ykeys: ['aktiv', 'baru', 'cuti', 'keluar', 'lulus', 'kupon'],
            //                labels: ['Aktiv', 'Baru', 'Cuti', 'Keluar', 'Lulus', "Kupon"],
            //            });
        </script>

        <?
    }

    function loadRekapSiswaTableTC()
    {
        $arrMyTC = Generic::getAllMyTC(AccessRight::getMyOrgID());
        $bln = isset($_GET['bln']) ? addslashes($_GET['bln']) : date("n");
        $thn = isset($_GET['thn']) ? addslashes($_GET['thn']) : date("Y");
        $t = time();
        $arrBulan = Generic::getAllMonths();
        $arrBulan[count($arrBulan) + 1] = "All";
        $i = 1;
        if ($bln == KEY::$KEY_MONTH_ALL) {
            $j = 1;
            for ($j = 1; $j <= 3; $j++) {
                ?>
                <section class="content">
                    <table class="table table-bordered  table-striped table-sempoa-border"
                           style="background-color: white;">
                        <thead>
                        <tr>
                            <th class="tengah" rowspan="2">No.</th>
                            <th class="tengah" rowspan="2">Kode TC</th>
                            <th class="tengah" rowspan="2">Nama TC</th>
                            <th class="tengah" rowspan="2">Nama Director</th>


                            <?
                            foreach ($arrBulan as $val) {

                                if ($j == 1) {
                                    if ($val <= 4 && $val != KEY::$KEY_MONTH_ALL) {
                                        ?>
                                        <th id="<?= $val . "_" . $thn; ?>" class="tengahcolumn"
                                            colspan="7"><?= Generic::getMonthName($val); ?></th>

                                        <?
                                    }
                                } elseif ($j == 2) {
                                    if ($val >= 5 && $val <= 8) {
                                        ?>
                                        <th id="<?= $val . "_" . $thn; ?>" class="tengah"
                                            colspan="7"><?= Generic::getMonthName($val); ?></th>

                                        <?
                                    }
                                } elseif ($j == 3) {
                                    if ($val >= 9 && $val <= 12) {
                                        ?>
                                        <th id="<?= $val . "_" . $thn; ?>" class="tengah"
                                            colspan="7"><?= Generic::getMonthName($val); ?></th>

                                        <?
                                    }
                                }
                            }
                            ?>
                        </tr>
                        <tr>
                            <?
                            foreach ($arrBulan as $val) {
                                if ($val != KEY::$KEY_MONTH_ALL) {


                                    if ($j == 1) {
                                        if ($val <= 4 && $val != KEY::$KEY_MONTH_ALL) {
                                            ?>
                                            <th>BL</th>
                                            <th>B</th>
                                            <th>K</th>
                                            <th>C</th>
                                            <th>L</th>
                                            <th>A</th>
                                            <th>KPN</th>
                                            <?
                                        }
                                    } elseif ($j == 2) {
                                        if ($val >= 5 && $val <= 8) {
                                            ?>
                                            <th>BL</th>
                                            <th>B</th>
                                            <th>K</th>
                                            <th>C</th>
                                            <th>L</th>
                                            <th>A</th>
                                            <th>KPN</th>
                                            <?
                                        }
                                    } elseif ($j == 3) {
                                        if ($val >= 9 && $val <= 12) {
                                            ?>
                                            <th>BL</th>
                                            <th>B</th>
                                            <th>K</th>
                                            <th>C</th>
                                            <th>L</th>
                                            <th>A</th>
                                            <th>KPN</th>
                                            <?
                                        }
                                    }
                                }
                            }
                            ?>
                        </tr>
                        </thead>
                        <? ?>
                        <tbody id="load_<?= $j . "_" . $t . "_" . $thn; ?>">

                        <?
                        $i = 1;
                        $arrTotal_bl = array();
                        $arrTotal_baru = array();
                        $arrTotal_keluar = array();
                        $arrTotal_cuti = array();
                        $arrTotal_lulus = array();
                        $arrTotal_aktiv = array();
                        $arrTotal_kupon = array();
                        foreach ($arrMyTC as $id_tc => $tc) {
                            $orgTC = new SempoaOrg();
                            $orgTC->getByID($id_tc);
                            ?>
                            <tr>
                                <td><?= $i; ?></td>
                                <td><?= $orgTC->org_kode; ?></td>
                                <td><?= $orgTC->nama; ?></td>
                                <td><?= $orgTC->nama_pemilik; ?></td>
                                <?
                                foreach ($arrBulan as $val) {
                                    if ($val != KEY::$KEY_MONTH_ALL) {
                                        $org_tc = new RekapSiswaIBOModel();
                                        $arrDaten = $org_tc->getDaten($val, $thn, $orgTC->nama);
                                        $arrTotal_bl[$val] += $arrDaten[0]->bi_rekap_bl;
                                        $arrTotal_baru[$val] += $arrDaten[0]->bi_rekap_baru;
                                        $arrTotal_keluar[$val] += $arrDaten[0]->bi_rekap_keluar;
                                        $arrTotal_cuti[$val] += $arrDaten[0]->bi_rekap_cuti;
                                        $arrTotal_lulus[$val] += $arrDaten[0]->bi_rekap_lulus;
                                        $arrTotal_aktiv[$val] += $arrDaten[0]->bi_rekap_aktiv;
                                        $arrTotal_kupon [$val] += $arrDaten[0]->bi_rekap_kupon;

                                        if (is_null($arrDaten[0]->bi_rekap_bl)) {
                                            $bl = 0;
                                        } else {
                                            $bl = $arrDaten[0]->bi_rekap_bl;
                                        }

                                        if (is_null($arrDaten[0]->bi_rekap_baru)) {
                                            $baru = 0;
                                        } else {
                                            $baru = $arrDaten[0]->bi_rekap_baru;
                                        }

                                        if (is_null($arrDaten[0]->bi_rekap_keluar)) {
                                            $keluar = 0;
                                        } else {
                                            $keluar = $arrDaten[0]->bi_rekap_keluar;
                                        }

                                        if (is_null($arrDaten[0]->bi_rekap_cuti)) {
                                            $cuti = 0;
                                        } else {
                                            $cuti = $arrDaten[0]->bi_rekap_cuti;
                                        }


                                        if (is_null($arrDaten[0]->bi_rekap_lulus)) {
                                            $lulus = 0;
                                        } else {
                                            $lulus = $arrDaten[0]->bi_rekap_lulus;
                                        }
                                        if (is_null($arrDaten[0]->bi_rekap_aktiv)) {
                                            $aktiv = 0;
                                        } else {
                                            $aktiv = $arrDaten[0]->bi_rekap_aktiv;
                                        }

                                        if (is_null($arrDaten[0]->bi_rekap_kupon)) {
                                            $kpn = 0;
                                        } else {
                                            $kpn = $arrDaten[0]->bi_rekap_kupon;
                                        }

                                        if ($j == 1) {
                                            if ($val <= 4 && $val != KEY::$KEY_MONTH_ALL) {
                                                ?>
                                                <td><?= $bl; ?></td>
                                                <td><?= $baru; ?></td>
                                                <td><?= $keluar; ?></td>
                                                <td><?= $cuti; ?></td>
                                                <td><?= $lulus; ?></td>
                                                <td><?= $aktiv; ?></td>
                                                <td><?= $kpn; ?></td>
                                                <?
                                            }
                                        } elseif ($j == 2) {
                                            if ($val >= 5 && $val <= 8) {
                                                ?>
                                                <td><?= $bl; ?></td>
                                                <td><?= $baru; ?></td>
                                                <td><?= $keluar; ?></td>
                                                <td><?= $cuti; ?></td>
                                                <td><?= $lulus; ?></td>
                                                <td><?= $aktiv; ?></td>
                                                <td><?= $kpn; ?></td>
                                                <?
                                            }
                                        } elseif ($j == 3) {
                                            if ($val >= 9 && $val <= 12) {
                                                ?>
                                                <td><?= $bl; ?></td>
                                                <td><?= $baru; ?></td>
                                                <td><?= $keluar; ?></td>
                                                <td><?= $cuti; ?></td>
                                                <td><?= $lulus; ?></td>
                                                <td><?= $aktiv; ?></td>
                                                <td><?= $kpn; ?></td>
                                                <?
                                            }
                                        }
                                    }
                                }
                                ?>
                            </tr>
                            <?
                            $i++;
                        }
                        ?>

                        </tbody>
                        <tfoot id="foot_<?= $j . "_" . $t . "_" . $thn; ?>">
                        <tr>
                            <td colspan="4" style="text-align:center; font-weight: bold">Total</td>
                            <?
                            foreach ($arrBulan as $val) {
                                if ($val != KEY::$KEY_MONTH_ALL) {
                                    if ($j == 1) {
                                        if ($val <= 4 && $val != KEY::$KEY_MONTH_ALL) {
                                            ?>
                                            <td><?= $arrTotal_bl[$val]; ?></td>
                                            <td><?= $arrTotal_baru[$val]; ?></td>
                                            <td><?= $arrTotal_keluar[$val]; ?></td>
                                            <td><?= $arrTotal_cuti[$val]; ?></td>
                                            <td><?= $arrTotal_lulus[$val]; ?></td>
                                            <td><?= $arrTotal_aktiv[$val]; ?></td>
                                            <td><?= $arrTotal_kupon[$val]; ?></td>
                                            <?
                                        }
                                    } elseif ($j == 2) {
                                        if ($val >= 5 && $val <= 8) {
                                            ?>
                                            <td><?= $arrTotal_bl[$val]; ?></td>
                                            <td><?= $arrTotal_baru[$val]; ?></td>
                                            <td><?= $arrTotal_keluar[$val]; ?></td>
                                            <td><?= $arrTotal_cuti[$val]; ?></td>
                                            <td><?= $arrTotal_lulus[$val]; ?></td>
                                            <td><?= $arrTotal_aktiv[$val]; ?></td>
                                            <td><?= $arrTotal_kupon[$val]; ?></td>
                                            <?
                                        }
                                    } elseif ($j == 3) {
                                        if ($val >= 9 && $val <= 12) {
                                            ?>
                                            <td><?= $arrTotal_bl[$val]; ?></td>
                                            <td><?= $arrTotal_baru[$val]; ?></td>
                                            <td><?= $arrTotal_keluar[$val]; ?></td>
                                            <td><?= $arrTotal_cuti[$val]; ?></td>
                                            <td><?= $arrTotal_lulus[$val]; ?></td>
                                            <td><?= $arrTotal_aktiv[$val]; ?></td>
                                            <td><?= $arrTotal_kupon[$val]; ?></td>
                                            <?
                                        }
                                    }
                                }
                            }
                            ?>
                        </tr>
                        </tfoot>
                    </table>
                </section>

                <?
            }
        } // Jika bulan yg di pilih selain All
        else {
            ?>
            <table class="table table-bordered table-striped table-sempoa-border" style="background-color: white;">
                <thead>
                <tr>
                    <th class="tengah" rowspan="2">No.</th>
                    <th class="tengah" rowspan="2">Kode TC</th>
                    <th class="tengah" rowspan="2">Nama TC</th>
                    <th class="tengah" rowspan="2">Nama Director</th>
                    <th id="<?= $bln . "_" . $thn; ?>" class="tengah"
                        colspan="7"><?= Generic::getMonthName($bln); ?></th>
                </tr>
                <tr>
                    <th>BL</th>
                    <th>B</th>
                    <th>K</th>
                    <th>C</th>
                    <th>L</th>
                    <th>A</th>
                    <th>KPN</th>
                </tr>
                </thead>
                <tbody id="load_<?= $bln . "_" . $thn; ?>">
                <?
                $i = 1;
                $total_bl = 0;
                $total_baru = 0;
                $total_keluar = 0;
                $total_cuti = 0;
                $total_lulus = 0;
                $total_aktiv = 0;
                $total_kupon = 0;
                $arrTotal_bl = array();
                $arrTotal_baru = array();
                $arrTotal_keluar = array();
                $arrTotal_cuti = array();
                $arrTotal_lulus = array();
                $arrTotal_aktiv = array();
                $arrTotal_kupon = array();
                foreach ($arrMyTC as $id_tc => $tc) {
                    $orgTC = new SempoaOrg();
                    $orgTC->getByID($id_tc);
                    ?>
                    <tr>
                        <td><?= $i; ?></td>
                        <td><?= $orgTC->org_kode; ?></td>
                        <td><?= $orgTC->nama; ?></td>
                        <td><?= $orgTC->nama_pemilik; ?></td>
                        <?
                        $org_tc = new RekapSiswaIBOModel();
                        $arrDaten = $org_tc->getDaten($bln, $thn, $orgTC->nama);
                        $total_bl += $arrDaten[0]->bi_rekap_bl;
                        $total_baru += $arrDaten[0]->bi_rekap_baru;
                        $total_keluar += $arrDaten[0]->bi_rekap_keluar;
                        $total_cuti += $arrDaten[0]->bi_rekap_cuti;
                        $total_lulus += $arrDaten[0]->bi_rekap_lulus;
                        $total_aktiv += $arrDaten[0]->bi_rekap_aktiv;
                        $total_kupon += $arrDaten[0]->bi_rekap_kupon;
                        ?>
                        <td><?= $arrDaten[0]->bi_rekap_bl; ?></td>
                        <td><?= $arrDaten[0]->bi_rekap_baru; ?></td>
                        <td><?= $arrDaten[0]->bi_rekap_keluar; ?></td>
                        <td><?= $arrDaten[0]->bi_rekap_cuti; ?></td>
                        <td><?= $arrDaten[0]->bi_rekap_lulus; ?></td>
                        <td><?= $arrDaten[0]->bi_rekap_aktiv; ?></td>
                        <td><?= $arrDaten[0]->bi_rekap_kupon; ?></td>

                    </tr>
                    <?
                    $i++;
                }
                ?>
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="4" style="text-align:center; font-weight: bold">Total</td>
                    <td><?= $total_bl; ?></td>
                    <td><?= $total_baru; ?></td>
                    <td><?= $total_keluar; ?></td>
                    <td><?= $total_cuti; ?></td>
                    <td><?= $total_lulus; ?></td>
                    <td><?= $total_aktiv; ?></td>
                    <td><?= $total_kupon; ?></td>
                </tr>
                </tfoot>
            </table>


            <?
        }

        $hasil = array();
        $waktu = $bln . "-" . $thn;
        $t = time();

        if ($bln != "All") {
            $i = 0;
            foreach ($arrMyTC as $keytc => $tc) {
                $rekap_siswa = new RekapSiswaIBOModel();
                $arrRekapSiswa = $rekap_siswa->getWhere("bi_rekap_tc_id = $keytc AND bi_rekap_siswa_waktu = '$waktu' ");

                if (count($arrRekapSiswa) > 0) {
                    $hasil[$i]['tc'] = $tc;
                    $hasil[$i]['bln'] = $bln;
                    $hasil[$i]['thn'] = $thn;
                    $hasil[$i]['baru'] = $arrRekapSiswa[0]->bi_rekap_baru;
                    $hasil[$i]['keluar'] = $arrRekapSiswa[0]->bi_rekap_keluar;
                    $hasil[$i]['cuti'] = $arrRekapSiswa[0]->bi_rekap_cuti;
                    $hasil[$i]['lulus'] = $arrRekapSiswa[0]->bi_rekap_lulus;
                    $hasil[$i]['aktiv'] = $arrRekapSiswa[0]->bi_rekap_aktiv;
                    $hasil[$i]['kupon'] = $arrRekapSiswa[0]->bi_rekap_kupon;
                } else {
                    $hasil[$i]['tc'] = $tc;
                    $hasil[$i]['bln'] = $bln;
                    $hasil[$i]['thn'] = $thn;
                    $hasil[$i]['waktu'] = $waktu;
                    $hasil[$i]['baru'] = 0;
                    $hasil[$i]['keluar'] = 0;
                    $hasil[$i]['cuti'] = 0;
                    $hasil[$i]['lulus'] = 0;
                    $hasil[$i]['aktiv'] = 0;
                    $hasil[$i]['kupon'] = 0;
                }
                $i++;
            }
        } else {
            foreach ($arrBulan as $val) {
                if ($val != "All") {
                    $i = 0;
                    foreach ($arrMyTC as $keytc => $tc) {
                        $waktu = $val . "-" . $thn;
                        $rekap_siswa = new RekapSiswaIBOModel();
                        $arrRekapSiswa = $rekap_siswa->getWhere("bi_rekap_tc_id = $keytc  AND bi_rekap_siswa_waktu = '$waktu' ");

                        $hasil[$i]['tc'] = $tc;
                        $hasil[$i]['bln'] = $val;
                        $hasil[$i]['thn'] = $thn;
                        $hasil[$i]['waktu'] = $waktu;
                        $hasil[$i]['baru'] = 0;
                        $hasil[$i]['keluar'] = 0;
                        $hasil[$i]['cuti'] = 0;
                        $hasil[$i]['lulus'] = 0;
                        $hasil[$i]['aktiv'] = 0;
                        $hasil[$i]['kupon'] = 0;

                        if (count($arrRekapSiswa) > 0) {
                            $hasil[$i]['tc'] = $tc;
                            $hasil[$i]['bln'] = $val;
                            $hasil[$i]['thn'] = $thn;
                            $hasil[$i]['waktu'] = $waktu;
                            $hasil[$i]['baru'] = $arrRekapSiswa[0]->bi_rekap_baru;
                            $hasil[$i]['keluar'] = $arrRekapSiswa[0]->bi_rekap_keluar;
                            $hasil[$i]['cuti'] = $arrRekapSiswa[0]->bi_rekap_cuti;
                            $hasil[$i]['lulus'] = $arrRekapSiswa[0]->bi_rekap_lulus;
                            $hasil[$i]['aktiv'] = $arrRekapSiswa[0]->bi_rekap_aktiv;
                            $hasil[$i]['kupon'] = $arrRekapSiswa[0]->bi_rekap_kupon;

                            $i++;
                        }
                    }
                }
            }
        }
        ?>
        <div class="clearfix"></div>
        <!--        <div class="row box-body chart-responsive">
            <div class="col-xs-12">
                <h3>Rekapitulasi Siswa TC per 
//                = Generic::getMonthName($bln) . "/" . $thn; 
                ?> </h3>
                <div class="chart putih" id="bar-chart-<?= $t; ?>" style="height: 300px;">

                </div>
            </div>
        </div>-->

        <script>

            //            Morris.Bar({
            //                element: 'bar-chart-<?= $t; ?>',
            //                data: <? echo json_encode($hasil); ?>,
            //                xkey: 'tc',
            //                ykeys: ['aktiv', 'baru', 'cuti', 'keluar', 'lulus', 'kupon'],
            //                labels: ['Aktiv', 'Baru', 'Cuti', 'Keluar', 'Lulus', "Kupon"],
            //            });
        </script>
        <style>
            .thead .table-sempoa-border {
                border: 4px;
                border-style: solid;
                border-color: burlywood;

            }

            .tengah {
                vertical-align: middle !important;
            }

            .tengahcolumn {
                vertical-align: middle !important;
                font-weight: bold;
            }

            .putih {
                background-color: white;
            }
        </style>
        <?
    }

    function loadRekapSiswaTableOneTC()
    {

        $bln = isset($_GET['bln']) ? addslashes($_GET['bln']) : date("n");
        $thn = isset($_GET['thn']) ? addslashes($_GET['thn']) : date("Y");
        $t = time();
        $arrBulan = Generic::getAllMonths();
        $arrBulan[count($arrBulan) + 1] = "All";
        $i = 1;
        $id_tc = AccessRight::getMyOrgID();
        if ($bln == KEY::$KEY_MONTH_ALL) {
            $j = 1;
            for ($j = 1; $j <= 3; $j++) {
                ?>
                <section class="content">
                    <table class="table table-bordered  table-striped table-sempoa-border"
                           style="background-color: white;">
                        <thead>
                        <tr>
                            <th class="tengah" rowspan="2">No.</th>
                            <th class="tengah" rowspan="2">Kode TC</th>
                            <th class="tengah" rowspan="2">Nama TC</th>
                            <th class="tengah" rowspan="2">Nama Director</th>


                            <?
                            foreach ($arrBulan as $val) {

                                if ($j == 1) {
                                    if ($val <= 4 && $val != KEY::$KEY_MONTH_ALL) {
                                        ?>
                                        <th id="<?= $val . "_" . $thn; ?>" class="tengahcolumn"
                                            colspan="7"><?= Generic::getMonthName($val); ?></th>

                                        <?
                                    }
                                } elseif ($j == 2) {
                                    if ($val >= 5 && $val <= 8) {
                                        ?>
                                        <th id="<?= $val . "_" . $thn; ?>" class="tengah"
                                            colspan="7"><?= Generic::getMonthName($val); ?></th>

                                        <?
                                    }
                                } elseif ($j == 3) {
                                    if ($val >= 9 && $val <= 12) {
                                        ?>
                                        <th id="<?= $val . "_" . $thn; ?>" class="tengah"
                                            colspan="7"><?= Generic::getMonthName($val); ?></th>

                                        <?
                                    }
                                }
                            }
                            ?>
                        </tr>
                        <tr>
                            <?
                            foreach ($arrBulan as $val) {
                                if ($val != KEY::$KEY_MONTH_ALL) {


                                    if ($j == 1) {
                                        if ($val <= 4 && $val != KEY::$KEY_MONTH_ALL) {
                                            ?>
                                            <th>BL</th>
                                            <th>B</th>
                                            <th>K</th>
                                            <th>C</th>
                                            <th>L</th>
                                            <th>A</th>
                                            <th>KPN</th>
                                            <?
                                        }
                                    } elseif ($j == 2) {
                                        if ($val >= 5 && $val <= 8) {
                                            ?>
                                            <th>BL</th>
                                            <th>B</th>
                                            <th>K</th>
                                            <th>C</th>
                                            <th>L</th>
                                            <th>A</th>
                                            <th>KPN</th>
                                            <?
                                        }
                                    } elseif ($j == 3) {
                                        if ($val >= 9 && $val <= 12) {
                                            ?>
                                            <th>BL</th>
                                            <th>B</th>
                                            <th>K</th>
                                            <th>C</th>
                                            <th>L</th>
                                            <th>A</th>
                                            <th>KPN</th>
                                            <?
                                        }
                                    }
                                }
                            }
                            ?>
                        </tr>
                        </thead>
                        <? ?>
                        <tbody id="load_<?= $j . "_" . $t . "_" . $thn; ?>">

                        <?
                        $i = 1;
                        $arrTotal_bl = array();
                        $arrTotal_baru = array();
                        $arrTotal_keluar = array();
                        $arrTotal_cuti = array();
                        $arrTotal_lulus = array();
                        $arrTotal_aktiv = array();
                        $arrTotal_kupon = array();
                        $orgTC = new SempoaOrg();
                        $orgTC->getByID($id_tc);
                        ?>
                        <tr>
                            <td><?= $i; ?></td>
                            <td><?= $orgTC->org_kode; ?></td>
                            <td><?= $orgTC->nama; ?></td>
                            <td><?= $orgTC->nama_pemilik; ?></td>
                            <?
                            foreach ($arrBulan as $val) {
                                if ($val != KEY::$KEY_MONTH_ALL) {
                                    $org_tc = new RekapSiswaIBOModel();
                                    $arrDaten = $org_tc->getDaten($val, $thn, $orgTC->nama);
                                    $arrTotal_bl[$val] += $arrDaten[0]->bi_rekap_bl;
                                    $arrTotal_baru[$val] += $arrDaten[0]->bi_rekap_baru;
                                    $arrTotal_keluar[$val] += $arrDaten[0]->bi_rekap_keluar;
                                    $arrTotal_cuti[$val] += $arrDaten[0]->bi_rekap_cuti;
                                    $arrTotal_lulus[$val] += $arrDaten[0]->bi_rekap_lulus;
                                    $arrTotal_aktiv[$val] += $arrDaten[0]->bi_rekap_aktiv;
                                    $arrTotal_kupon [$val] += $arrDaten[0]->bi_rekap_kupon;

                                    if (is_null($arrDaten[0]->bi_rekap_bl)) {
                                        $bl = 0;
                                    } else {
                                        $bl = $arrDaten[0]->bi_rekap_bl;
                                    }

                                    if (is_null($arrDaten[0]->bi_rekap_baru)) {
                                        $baru = 0;
                                    } else {
                                        $baru = $arrDaten[0]->bi_rekap_baru;
                                    }

                                    if (is_null($arrDaten[0]->bi_rekap_keluar)) {
                                        $keluar = 0;
                                    } else {
                                        $keluar = $arrDaten[0]->bi_rekap_keluar;
                                    }

                                    if (is_null($arrDaten[0]->bi_rekap_cuti)) {
                                        $cuti = 0;
                                    } else {
                                        $cuti = $arrDaten[0]->bi_rekap_cuti;
                                    }


                                    if (is_null($arrDaten[0]->bi_rekap_lulus)) {
                                        $lulus = 0;
                                    } else {
                                        $lulus = $arrDaten[0]->bi_rekap_lulus;
                                    }
                                    if (is_null($arrDaten[0]->bi_rekap_aktiv)) {
                                        $aktiv = 0;
                                    } else {
                                        $aktiv = $arrDaten[0]->bi_rekap_aktiv;
                                    }

                                    if (is_null($arrDaten[0]->bi_rekap_kupon)) {
                                        $kpn = 0;
                                    } else {
                                        $kpn = $arrDaten[0]->bi_rekap_kupon;
                                    }

                                    if ($j == 1) {
                                        if ($val <= 4 && $val != KEY::$KEY_MONTH_ALL) {
                                            ?>
                                            <td><?= $bl; ?></td>
                                            <td><?= $baru; ?></td>
                                            <td><?= $keluar; ?></td>
                                            <td><?= $cuti; ?></td>
                                            <td><?= $lulus; ?></td>
                                            <td><?= $aktiv; ?></td>
                                            <td><?= $kpn; ?></td>
                                            <?
                                        }
                                    } elseif ($j == 2) {
                                        if ($val >= 5 && $val <= 8) {
                                            ?>
                                            <td><?= $bl; ?></td>
                                            <td><?= $baru; ?></td>
                                            <td><?= $keluar; ?></td>
                                            <td><?= $cuti; ?></td>
                                            <td><?= $lulus; ?></td>
                                            <td><?= $aktiv; ?></td>
                                            <td><?= $kpn; ?></td>
                                            <?
                                        }
                                    } elseif ($j == 3) {
                                        if ($val >= 9 && $val <= 12) {
                                            ?>
                                            <td><?= $bl; ?></td>
                                            <td><?= $baru; ?></td>
                                            <td><?= $keluar; ?></td>
                                            <td><?= $cuti; ?></td>
                                            <td><?= $lulus; ?></td>
                                            <td><?= $aktiv; ?></td>
                                            <td><?= $kpn; ?></td>
                                            <?
                                        }
                                    }
                                }
                            }
                            ?>
                        </tr>
                        <?
                        $i++;
                        ?>

                        </tbody>
                        <tfoot id="foot_<?= $j . "_" . $t . "_" . $thn; ?>">
                        <tr>
                            <td colspan="4" style="text-align:center; font-weight: bold">Total</td>
                            <?
                            foreach ($arrBulan as $val) {
                                if ($val != KEY::$KEY_MONTH_ALL) {
                                    if ($j == 1) {
                                        if ($val <= 4 && $val != KEY::$KEY_MONTH_ALL) {
                                            ?>
                                            <td><?= $arrTotal_bl[$val]; ?></td>
                                            <td><?= $arrTotal_baru[$val]; ?></td>
                                            <td><?= $arrTotal_keluar[$val]; ?></td>
                                            <td><?= $arrTotal_cuti[$val]; ?></td>
                                            <td><?= $arrTotal_lulus[$val]; ?></td>
                                            <td><?= $arrTotal_aktiv[$val]; ?></td>
                                            <td><?= $arrTotal_kupon[$val]; ?></td>
                                            <?
                                        }
                                    } elseif ($j == 2) {
                                        if ($val >= 5 && $val <= 8) {
                                            ?>
                                            <td><?= $arrTotal_bl[$val]; ?></td>
                                            <td><?= $arrTotal_baru[$val]; ?></td>
                                            <td><?= $arrTotal_keluar[$val]; ?></td>
                                            <td><?= $arrTotal_cuti[$val]; ?></td>
                                            <td><?= $arrTotal_lulus[$val]; ?></td>
                                            <td><?= $arrTotal_aktiv[$val]; ?></td>
                                            <td><?= $arrTotal_kupon[$val]; ?></td>
                                            <?
                                        }
                                    } elseif ($j == 3) {
                                        if ($val >= 9 && $val <= 12) {
                                            ?>
                                            <td><?= $arrTotal_bl[$val]; ?></td>
                                            <td><?= $arrTotal_baru[$val]; ?></td>
                                            <td><?= $arrTotal_keluar[$val]; ?></td>
                                            <td><?= $arrTotal_cuti[$val]; ?></td>
                                            <td><?= $arrTotal_lulus[$val]; ?></td>
                                            <td><?= $arrTotal_aktiv[$val]; ?></td>
                                            <td><?= $arrTotal_kupon[$val]; ?></td>
                                            <?
                                        }
                                    }
                                }
                            }
                            ?>
                        </tr>
                        </tfoot>
                    </table>
                </section>

                <?
            }
        } // Jika bulan yg di pilih selain All
        else {
            ?>
            <table class="table table-bordered table-striped table-sempoa-border" style="background-color: white;">
                <thead>
                <tr>
                    <th class="tengah" rowspan="2">No.</th>
                    <th class="tengah" rowspan="2">Kode TC</th>
                    <th class="tengah" rowspan="2">Nama TC</th>
                    <th class="tengah" rowspan="2">Nama Director</th>
                    <th id="<?= $bln . "_" . $thn; ?>" class="tengah"
                        colspan="7"><?= Generic::getMonthName($bln); ?></th>
                </tr>
                <tr>
                    <th>BL</th>
                    <th>B</th>
                    <th>K</th>
                    <th>C</th>
                    <th>L</th>
                    <th>A</th>
                    <th>KPN</th>
                </tr>
                </thead>
                <tbody id="load_<?= $bln . "_" . $thn; ?>">
                <?
                $i = 1;
                $total_bl = 0;
                $total_baru = 0;
                $total_keluar = 0;
                $total_cuti = 0;
                $total_lulus = 0;
                $total_aktiv = 0;
                $total_kupon = 0;
                $arrTotal_bl = array();
                $arrTotal_baru = array();
                $arrTotal_keluar = array();
                $arrTotal_cuti = array();
                $arrTotal_lulus = array();
                $arrTotal_aktiv = array();
                $arrTotal_kupon = array();
                $orgTC = new SempoaOrg();
                $orgTC->getByID($id_tc);
                ?>
                <tr>
                    <td><?= $i; ?></td>
                    <td><?= $orgTC->org_kode; ?></td>
                    <td><?= $orgTC->nama; ?></td>
                    <td><?= $orgTC->nama_pemilik; ?></td>
                    <?
                    $org_tc = new RekapSiswaIBOModel();
                    $arrDaten = $org_tc->getDaten($bln, $thn, $orgTC->nama);
                    $total_bl += $arrDaten[0]->bi_rekap_bl;
                    $total_baru += $arrDaten[0]->bi_rekap_baru;
                    $total_keluar += $arrDaten[0]->bi_rekap_keluar;
                    $total_cuti += $arrDaten[0]->bi_rekap_cuti;
                    $total_lulus += $arrDaten[0]->bi_rekap_lulus;
                    $total_aktiv += $arrDaten[0]->bi_rekap_aktiv;
                    $total_kupon += $arrDaten[0]->bi_rekap_kupon;
                    ?>
                    <td><?= $arrDaten[0]->bi_rekap_bl; ?></td>
                    <td><?= $arrDaten[0]->bi_rekap_baru; ?></td>
                    <td><?= $arrDaten[0]->bi_rekap_keluar; ?></td>
                    <td><?= $arrDaten[0]->bi_rekap_cuti; ?></td>
                    <td><?= $arrDaten[0]->bi_rekap_lulus; ?></td>
                    <td><?= $arrDaten[0]->bi_rekap_aktiv; ?></td>
                    <td><?= $arrDaten[0]->bi_rekap_kupon; ?></td>

                </tr>
                <?
                $i++;
                ?>
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="4" style="text-align:center; font-weight: bold">Total</td>
                    <td><?= $total_bl; ?></td>
                    <td><?= $total_baru; ?></td>
                    <td><?= $total_keluar; ?></td>
                    <td><?= $total_cuti; ?></td>
                    <td><?= $total_lulus; ?></td>
                    <td><?= $total_aktiv; ?></td>
                    <td><?= $total_kupon; ?></td>
                </tr>
                </tfoot>
            </table>


            <?
        }

        $hasil = array();
        $waktu = $bln . "-" . $thn;
        $t = time();

        if ($bln != "All") {
            $i = 0;
            foreach ($arrMyTC as $keytc => $tc) {
                $rekap_siswa = new RekapSiswaIBOModel();
                $arrRekapSiswa = $rekap_siswa->getWhere("bi_rekap_tc_id = $keytc AND bi_rekap_siswa_waktu = '$waktu' ");

                if (count($arrRekapSiswa) > 0) {
                    $hasil[$i]['tc'] = $tc;
                    $hasil[$i]['bln'] = $bln;
                    $hasil[$i]['thn'] = $thn;
                    $hasil[$i]['baru'] = $arrRekapSiswa[0]->bi_rekap_baru;
                    $hasil[$i]['keluar'] = $arrRekapSiswa[0]->bi_rekap_keluar;
                    $hasil[$i]['cuti'] = $arrRekapSiswa[0]->bi_rekap_cuti;
                    $hasil[$i]['lulus'] = $arrRekapSiswa[0]->bi_rekap_lulus;
                    $hasil[$i]['aktiv'] = $arrRekapSiswa[0]->bi_rekap_aktiv;
                    $hasil[$i]['kupon'] = $arrRekapSiswa[0]->bi_rekap_kupon;
                } else {
                    $hasil[$i]['tc'] = $tc;
                    $hasil[$i]['bln'] = $bln;
                    $hasil[$i]['thn'] = $thn;
                    $hasil[$i]['waktu'] = $waktu;
                    $hasil[$i]['baru'] = 0;
                    $hasil[$i]['keluar'] = 0;
                    $hasil[$i]['cuti'] = 0;
                    $hasil[$i]['lulus'] = 0;
                    $hasil[$i]['aktiv'] = 0;
                    $hasil[$i]['kupon'] = 0;
                }
                $i++;
            }
        } else {
            foreach ($arrBulan as $val) {
                if ($val != "All") {
                    $i = 0;
                    foreach ($arrMyTC as $keytc => $tc) {
                        $waktu = $val . "-" . $thn;
                        $rekap_siswa = new RekapSiswaIBOModel();
                        $arrRekapSiswa = $rekap_siswa->getWhere("bi_rekap_tc_id = $keytc  AND bi_rekap_siswa_waktu = '$waktu' ");

                        $hasil[$i]['tc'] = $tc;
                        $hasil[$i]['bln'] = $val;
                        $hasil[$i]['thn'] = $thn;
                        $hasil[$i]['waktu'] = $waktu;
                        $hasil[$i]['baru'] = 0;
                        $hasil[$i]['keluar'] = 0;
                        $hasil[$i]['cuti'] = 0;
                        $hasil[$i]['lulus'] = 0;
                        $hasil[$i]['aktiv'] = 0;
                        $hasil[$i]['kupon'] = 0;

                        if (count($arrRekapSiswa) > 0) {
                            $hasil[$i]['tc'] = $tc;
                            $hasil[$i]['bln'] = $val;
                            $hasil[$i]['thn'] = $thn;
                            $hasil[$i]['waktu'] = $waktu;
                            $hasil[$i]['baru'] = $arrRekapSiswa[0]->bi_rekap_baru;
                            $hasil[$i]['keluar'] = $arrRekapSiswa[0]->bi_rekap_keluar;
                            $hasil[$i]['cuti'] = $arrRekapSiswa[0]->bi_rekap_cuti;
                            $hasil[$i]['lulus'] = $arrRekapSiswa[0]->bi_rekap_lulus;
                            $hasil[$i]['aktiv'] = $arrRekapSiswa[0]->bi_rekap_aktiv;
                            $hasil[$i]['kupon'] = $arrRekapSiswa[0]->bi_rekap_kupon;

                            $i++;
                        }
                    }
                }
            }
        }
        ?>
        <div class="clearfix"></div>
        <!--        <div class="row box-body chart-responsive">
            <div class="col-xs-12">
                <h3>Rekapitulasi Siswa TC per
//                = Generic::getMonthName($bln) . "/" . $thn;
                ?> </h3>
                <div class="chart putih" id="bar-chart-<?= $t; ?>" style="height: 300px;">

                </div>
            </div>
        </div>-->

        <script>

            //            Morris.Bar({
            //                element: 'bar-chart-<?= $t; ?>',
            //                data: <? echo json_encode($hasil); ?>,
            //                xkey: 'tc',
            //                ykeys: ['aktiv', 'baru', 'cuti', 'keluar', 'lulus', 'kupon'],
            //                labels: ['Aktiv', 'Baru', 'Cuti', 'Keluar', 'Lulus', "Kupon"],
            //            });
        </script>
        <style>
            .thead .table-sempoa-border {
                border: 4px;
                border-style: solid;
                border-color: burlywood;

            }

            .tengah {
                vertical-align: middle !important;
            }

            .tengahcolumn {
                vertical-align: middle !important;
                font-weight: bold;
            }

            .putih {
                background-color: white;
            }
        </style>
        <?
    }

    function loadRekapKuponTC()
    {
        $bln = isset($_GET['bln']) ? addslashes($_GET['bln']) : date("n");
        $thn = isset($_GET['thn']) ? addslashes($_GET['thn']) : date("Y");
        $t = time();
        $arrBulan = Generic::getAllMonthsWithAll();
        $arrMyTC = Generic::getAllMyTC(AccessRight::getMyOrgID());
        $tc_id = isset($_GET['tc_id']) ? addslashes($_GET['tc_id']) : Key($arrMyTC);
        $objRekapKupon = new BIRekapKuponModel();
        $arrRekap = $objRekapKupon->getWhere("bi_kupon_tc_id=$tc_id AND bi_kupon_bln=$bln AND bi_kupon_thn=$thn");


        ?>
        <table class="table table-bordered table-striped content_kupon_<?= $tc_id . $bln . $thn; ?>"
               style="background-color: white;">
            <thead>
            <tr>
                <th>No.</th>
                <th>Bulan</th>
                <th>Stock</th>
                <th>Kupon Masuk</th>
                <th>Transaksi Bulan ini</th>
                <th>Stock Akhir</th>
            </tr>
            </thead>
            <tbody>
            <?
            $i = 1;
            $arrKuponStock = 0;
            $arrKuponmasuk = 0;
            $arrKuponTraBln = 0;
            $arrKuponStockAkhir = 0;
            if ($bln != KEY::$KEY_MONTH_ALL) {
                $objRekapKupon = new BIRekapKuponModel();
                $arrRekap = $objRekapKupon->getWhere("bi_kupon_tc_id=$tc_id AND bi_kupon_bln=$bln AND bi_kupon_thn=$thn");
                $arrKuponStock += $arrRekap[0]->bi_kupon_stock;
                $arrKuponmasuk += $arrRekap[0]->bi_kupon_kupon_masuk;
                $arrKuponTraBln += $arrRekap[0]->bi_kupon_trs_bln;
                $arrKuponStockAkhir += $arrRekap[0]->bi_kupon_stock_akhir;
                ?>
                <tr>
                    <td><?= $i; ?></td>
                    <td><?= Generic::getMonthName($bln); ?></td>
                    <td><?
                        if ($arrRekap[0]->bi_kupon_stock == "") {
                            echo 0;
                        } else {
                            echo $arrRekap[0]->bi_kupon_stock;
                        }
                        ?></td>

                    <td><?
                        if ($arrRekap[0]->bi_kupon_kupon_masuk == "") {
                            echo 0;
                        } else {
                            echo $arrRekap[0]->bi_kupon_kupon_masuk;
                        }
                        ?></td>

                    <td><?
                        if ($arrRekap[0]->bi_kupon_trs_bln == "") {
                            echo 0;
                        } else {
                            echo $arrRekap[0]->bi_kupon_trs_bln;
                        }
                        ?></td>

                    <td><?
                        if ($arrRekap[0]->bi_kupon_stock_akhir == "") {
                            echo 0;
                        } else {
                            echo $arrRekap[0]->bi_kupon_stock_akhir;
                        }
                        ?></td>


                </tr>

                <?
            } else {
                $i = 1;

                foreach ($arrBulan as $key => $valBln) {
                    if ($valBln != KEY::$KEY_MONTH_ALL) {
                        $objRekapKupon = new BIRekapKuponModel();
                        $arrRekap = $objRekapKupon->getWhere("bi_kupon_tc_id=$tc_id AND bi_kupon_bln=$valBln AND bi_kupon_thn=$thn");
                        $arrKuponStock += $arrRekap[0]->bi_kupon_stock;
                        $arrKuponmasuk += $arrRekap[0]->bi_kupon_kupon_masuk;
                        $arrKuponTraBln += $arrRekap[0]->bi_kupon_trs_bln;
                        $arrKuponStockAkhir += $arrRekap[0]->bi_kupon_stock_akhir;
                        ?>
                        <tr>
                            <td><?= $i; ?></td>
                            <td><?= Generic::getMonthName($valBln); ?></td>
                            <td><?
                                if ($arrRekap[0]->bi_kupon_stock == "") {
                                    echo 0;
                                } else {
                                    echo $arrRekap[0]->bi_kupon_stock;
                                }
                                ?></td>

                            <td><?
                                if ($arrRekap[0]->bi_kupon_kupon_masuk == "") {
                                    echo 0;
                                } else {
                                    echo $arrRekap[0]->bi_kupon_kupon_masuk;
                                }
                                ?></td>

                            <td><?
                                if ($arrRekap[0]->bi_kupon_trs_bln == "") {
                                    echo 0;
                                } else {
                                    echo $arrRekap[0]->bi_kupon_trs_bln;
                                }
                                ?></td>

                            <td><?
                                if ($arrRekap[0]->bi_kupon_stock_akhir == "") {
                                    echo 0;
                                } else {
                                    echo $arrRekap[0]->bi_kupon_stock_akhir;
                                }
                                ?></td>

                        </tr>
                        <?
                        $i++;
                    }
                }
            }
            ?>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="2" style="text-align:center; font-weight: bold">Total</td>
                <td><?= $arrKuponStock; ?></td>
                <td><?= $arrKuponmasuk; ?></td>
                <td><?= $arrKuponTraBln; ?></td>
                <td><?= $arrKuponStockAkhir; ?></td>
            </tr>
            </tfoot>
        </table>
        <?
        die();
    }

    function loadRekapKuponOneTC()
    {
        $bln = isset($_GET['bln']) ? addslashes($_GET['bln']) : date("n");
        $thn = isset($_GET['thn']) ? addslashes($_GET['thn']) : date("Y");
        $t = time();
        $arrBulan = Generic::getAllMonthsWithAll();
//        $arrMyTC = Generic::getAllMyTC(AccessRight::getMyOrgID());
        $tc_id = AccessRight::getMyOrgID();
        $objRekapKupon = new BIRekapKuponModel();
        $arrRekap = $objRekapKupon->getWhere("bi_kupon_tc_id=$tc_id AND bi_kupon_bln=$bln AND bi_kupon_thn=$thn");

        ?>
        <table class="table table-bordered table-striped content_kupon_<?= $tc_id . $bln . $thn; ?>"
               style="background-color: white;">
            <thead>
            <tr>
                <th>No.</th>
                <th>Bulan</th>
                <th>Stock</th>
                <th>Kupon Masuk</th>
                <th>Transaksi Bulan ini</th>
                <th>Stock Akhir</th>
            </tr>
            </thead>
            <tbody>
            <?
            $i = 1;
            $arrKuponStock = 0;
            $arrKuponmasuk = 0;
            $arrKuponTraBln = 0;
            $arrKuponStockAkhir = 0;
            if ($bln != KEY::$KEY_MONTH_ALL) {
                $objRekapKupon = new BIRekapKuponModel();
                $arrRekap = $objRekapKupon->getWhere("bi_kupon_tc_id=$tc_id AND bi_kupon_bln=$bln AND bi_kupon_thn=$thn");
                $arrKuponStock += $arrRekap[0]->bi_kupon_stock;
                $arrKuponmasuk += $arrRekap[0]->bi_kupon_kupon_masuk;
                $arrKuponTraBln += $arrRekap[0]->bi_kupon_trs_bln;
                $arrKuponStockAkhir += $arrRekap[0]->bi_kupon_stock_akhir;
                ?>
                <tr>
                    <td><?= $i; ?></td>
                    <td><?= Generic::getMonthName($bln); ?></td>
                    <td><?
                        if ($arrRekap[0]->bi_kupon_stock == "") {
                            echo 0;
                        } else {
                            echo $arrRekap[0]->bi_kupon_stock;
                        }
                        ?></td>

                    <td><?
                        if ($arrRekap[0]->bi_kupon_kupon_masuk == "") {
                            echo 0;
                        } else {
                            echo $arrRekap[0]->bi_kupon_kupon_masuk;
                        }
                        ?></td>

                    <td><?
                        if ($arrRekap[0]->bi_kupon_trs_bln == "") {
                            echo 0;
                        } else {
                            echo $arrRekap[0]->bi_kupon_trs_bln;
                        }
                        ?></td>

                    <td><?
                        if ($arrRekap[0]->bi_kupon_stock_akhir == "") {
                            echo 0;
                        } else {
                            echo $arrRekap[0]->bi_kupon_stock_akhir;
                        }
                        ?></td>


                </tr>

                <?
            } else {
                $i = 1;

                foreach ($arrBulan as $key => $valBln) {
                    if ($valBln != KEY::$KEY_MONTH_ALL) {
                        $objRekapKupon = new BIRekapKuponModel();
                        $arrRekap = $objRekapKupon->getWhere("bi_kupon_tc_id=$tc_id AND bi_kupon_bln=$valBln AND bi_kupon_thn=$thn");
                        $arrKuponStock += $arrRekap[0]->bi_kupon_stock;
                        $arrKuponmasuk += $arrRekap[0]->bi_kupon_kupon_masuk;
                        $arrKuponTraBln += $arrRekap[0]->bi_kupon_trs_bln;
                        $arrKuponStockAkhir += $arrRekap[0]->bi_kupon_stock_akhir;
                        ?>
                        <tr>
                            <td><?= $i; ?></td>
                            <td><?= Generic::getMonthName($valBln); ?></td>
                            <td><?
                                if ($arrRekap[0]->bi_kupon_stock == "") {
                                    echo 0;
                                } else {
                                    echo $arrRekap[0]->bi_kupon_stock;
                                }
                                ?></td>

                            <td><?
                                if ($arrRekap[0]->bi_kupon_kupon_masuk == "") {
                                    echo 0;
                                } else {
                                    echo $arrRekap[0]->bi_kupon_kupon_masuk;
                                }
                                ?></td>

                            <td><?
                                if ($arrRekap[0]->bi_kupon_trs_bln == "") {
                                    echo 0;
                                } else {
                                    echo $arrRekap[0]->bi_kupon_trs_bln;
                                }
                                ?></td>

                            <td><?
                                if ($arrRekap[0]->bi_kupon_stock_akhir == "") {
                                    echo 0;
                                } else {
                                    echo $arrRekap[0]->bi_kupon_stock_akhir;
                                }
                                ?></td>

                        </tr>
                        <?
                        $i++;
                    }
                }
            }
            ?>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="2" style="text-align:center; font-weight: bold">Total</td>
                <td><?= $arrKuponStock; ?></td>
                <td><?= $arrKuponmasuk; ?></td>
                <td><?= $arrKuponTraBln; ?></td>
                <td><?= $arrKuponStockAkhir; ?></td>
            </tr>
            </tfoot>
        </table>
        <?
        die();
    }

    function loadRekapKuponTerjualTC()
    {
        $arrMyTC = Generic::getAllMyTC(AccessRight::getMyOrgID());
        $bln = isset($_GET['bln']) ? addslashes($_GET['bln']) : date("n");
        $thn = isset($_GET['thn']) ? addslashes($_GET['thn']) : date("Y");
        $t = time();
        $arrBulan = Generic::getAllMonths();
        $arrBulan[count($arrBulan) + 1] = "All";

        $columns[] = "No.";
        $columns[] = "t";
        $columns[] = "Nama TC";
        $columns[] = "t";
        $columns[] = Generic::getMonthName($bln);
        $columns[] = "n";
        $columns[] = "t";
        $columns[] = "t";
        $columns[] = "Kupon Terjual";
        $columns[] = "t";
        $columns[] = "ABB";
        $columns[] = "n";
        $str = serialize($columns);
        $column = urlencode($str);
        ?>

        <style>
            .tengahcolumn {
                vertical-align: middle !important;
                font-weight: bold;
            }
        </style>
        <table class="table table-bordered table-striped table-sempoa-border" style="background-color: white;">
            <thead>
            <tr>
                <td class="tengahcolumn" rowspan="2">No.</td>
                <td class="tengahcolumn" rowspan="2">Nama TC</td>
                <?
                if ($bln != KEY::$KEY_MONTH_ALL) {
                    ?>
                    <th id="<?= $bln . "_" . $thn; ?>" style="text-align:center; font-weight: bold;"
                        colspan="4"><?= Generic::getMonthName($bln); ?></th>

                    <?
                } else {
                    foreach ($arrBulan as $valbln) {
                        if ($valbln != KEY::$KEY_MONTH_ALL) {
                            ?>
                            <th id="<?= $valbln . "_" . $thn; ?>" class="tengahcolumn"
                                colspan="4"><?= Generic::getMonthName($valbln); ?></th>

                            <?
                        }
                    }
                }
                ?>
            </tr>
            <tr>
                <?
                if ($bln != KEY::$KEY_MONTH_ALL) {
                    ?>
                    <th>Kupon Terjual</th>
                    <th>Sisa Kupon</th>
                    <th>Kupon yang dibeli</th>
                    <th>A</th>
                    <?
                } else {
                    foreach ($arrBulan as $valbln) {
                        if ($valbln != KEY::$KEY_MONTH_ALL) {
                            ?>
                            <th>Kupon Terjual</th>
                            <th>Sisa Kupon</th>
                            <th>Kupon yang dibeli</th>
                            <th>A</th>
                            <?
                        }
                    }
                }
                ?>

            </tr>
            </thead>
            <tbody>
            <?
            $totalTerjual = 0;
            $totalAktiv = 0;
            if ($bln != KEY::$KEY_MONTH_ALL) {
                $i = 1;
                $totalTerjual = 0;
                $totalAktiv = 0;
                $totalSisaKupon = 0;
                $totalKuponYgDbeli = 0;
                $content = array();
//                        pr($arrMyTC);
                foreach ($arrMyTC as $keyTC => $tc) {
                    $birekap = new RekapSiswaIBOModel();
                    $birekap->getWhereOne("bi_rekap_tc_id=$keyTC AND bi_rekap_bln=$bln AND bi_rekap_tahun=$thn");

                    $totalTerjual += $birekap->bi_rekap_kupon;
                    $totalAktiv += $birekap->bi_rekap_aktiv;
                    ?>
                    <tr>
                        <td><?= $i; ?></td>
                        <td><?= $tc; ?></td>
                        <td><?
                            if ($birekap->bi_rekap_kupon == "") {
                                echo 0;
                            } else {
                                echo $birekap->bi_rekap_kupon;
                            }
                            ?></td>
                        <td><?
                            $a = Generic::getSisaKuponTC($keyTC);
                            $totalSisaKupon +=$a;
                            echo $a;?></td>
                        <td><?
                            $b= Generic::getJumlahKuponYangDibeliByBulanTahun($keyTC, $bln, $thn);
                            $totalKuponYgDbeli +=$b;
                            echo $b;?></td>
                        <td><?
                            if ($birekap->bi_rekap_aktiv == "") {
                                echo 0;
                            } else {
                                echo $birekap->bi_rekap_aktiv;
                            }
                            ?></td>
                    </tr>
                    <?
                    $i++;
                }
            } else {
                $i = 1;
                $totalTerjual = array();
                $totalAktiv = array();
                $totalSisaKuponArr =  array();
                $totalKuponYgDbeliArr =  array();
                foreach ($arrMyTC as $keyTC => $tc) {
                    ?>
                    <tr>
                        <td><?= $i; ?></td>
                        <td><?= $tc; ?></td>
                        <?
                        //
                        foreach ($arrBulan as $valbln) {
                            if ($valbln != KEY::$KEY_MONTH_ALL) {

                                $birekap = new RekapSiswaIBOModel();
                                $birekap->getWhereOne("bi_rekap_tc_id=$keyTC AND bi_rekap_bln=$valbln AND bi_rekap_tahun=$thn");
                                $totalTerjual[$valbln] = $totalTerjual[$valbln] + $birekap->bi_rekap_kupon;
                                $totalAktiv[$valbln] = $totalAktiv[$valbln] + $birekap->bi_rekap_aktiv;
//                                    pr($valbln . " - " . $totalAktiv[$valbln]);
                                ?>
                                <td><?
                                    if ($birekap->bi_rekap_kupon == "") {
                                        echo 0;
                                    } else {
                                        echo $birekap->bi_rekap_kupon;
                                    }
                                    ?></td>
                                <td><?
                                    $a=  Generic::getSisaKuponTC($keyTC);
                                    $totalSisaKuponArr[$valbln]+=$a;
                                    echo $a;
                                   ?></td>
                                <td><?
                                    $b = Generic::getJumlahKuponYangDibeliByBulanTahun($keyTC, $valbln, $thn);
                                    $totalKuponYgDbeliArr[$valbln] += $b;
                                    echo $b;

                                    ?></td>
                                <td><?
                                    if ($birekap->bi_rekap_aktiv == "") {
                                        echo 0;
                                    } else {
                                        echo $birekap->bi_rekap_aktiv;
                                    }
                                    ?></td>
                                <?
                            }
                        }
                        ?>
                    </tr>
                    <?
                    $i++;
                }
            }
            //                pr($totalTerjual);
            ?>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="2" style="text-align:center; font-weight: bold">Total</td>
                <?
                if ($bln != KEY::$KEY_MONTH_ALL) {
                    ?>
                    <td><?= $totalTerjual ?></td>
                    <td><?= $totalSisaKupon ?></td>
                    <td><?= $totalKuponYgDbeli ?></td>
                    <td><?= $totalAktiv ?></td>
                    <?
                } else {
                    foreach ($arrBulan as $valbln) {
                        if ($valbln != KEY::$KEY_MONTH_ALL) {
                            ?>
                            <td><?= $totalTerjual[$valbln] ?></td>
                            <td><?= $totalSisaKuponArr[$valbln] ?></td>
                            <td><?= $totalKuponYgDbeliArr[$valbln] ?></td>
                            <td><?= $totalAktiv[$valbln] ?></td>
                            <?
                        }
                    }
                }
                ?>

            </tr>
            </tfoot>
        </table>

        <script>
            $('#export_<?= $t; ?>').click(function () {
                //                var column = <?= $column; ?>;

                window.open('<?= _SPPATH; ?>BIWebHelper/exporttoexcel?column=<?= $column; ?>&jenis_rekap=Rekapitulasi Kupon per TC', "_blank ");

            });
        </script>

        <?
    }

    function loadRekapKuponTerjualOneTC()
    {
        $arrMyTC = Generic::getAllMyTC(AccessRight::getMyOrgID());
        $bln = isset($_GET['bln']) ? addslashes($_GET['bln']) : date("n");
        $thn = isset($_GET['thn']) ? addslashes($_GET['thn']) : date("Y");
        $t = time();
        $arrBulan = Generic::getAllMonths();
        $arrBulan[count($arrBulan) + 1] = "All";

        $columns[] = "No.";
        $columns[] = "t";
        $columns[] = "Nama TC";
        $columns[] = "t";
        $columns[] = Generic::getMonthName($bln);
        $columns[] = "n";
        $columns[] = "t";
        $columns[] = "t";
        $columns[] = "Kupon Terjual";
        $columns[] = "t";
        $columns[] = "ABB";
        $columns[] = "n";
        $str = serialize($columns);
        $column = urlencode($str);

        $keyTC = AccessRight::getMyOrgID();
        ?>

        <style>
            .tengahcolumn {
                vertical-align: middle !important;
                font-weight: bold;
            }
        </style>
        <table class="table table-bordered table-striped table-sempoa-border" style="background-color: white;">
            <thead>
            <tr>
                <td class="tengahcolumn" rowspan="2">No.</td>
                <td class="tengahcolumn" rowspan="2">Nama TC</td>
                <?
                if ($bln != KEY::$KEY_MONTH_ALL) {
                    ?>
                    <th id="<?= $bln . "_" . $thn; ?>" style="text-align:center; font-weight: bold;"
                        colspan="2"><?= Generic::getMonthName($bln); ?></th>

                    <?
                } else {
                    foreach ($arrBulan as $valbln) {
                        if ($valbln != KEY::$KEY_MONTH_ALL) {
                            ?>
                            <th id="<?= $valbln . "_" . $thn; ?>" class="tengahcolumn"
                                colspan="2"><?= Generic::getMonthName($valbln); ?></th>

                            <?
                        }
                    }
                }
                ?>
            </tr>
            <tr>
                <?
                if ($bln != KEY::$KEY_MONTH_ALL) {
                    ?>
                    <th>Kupon Terjual</th>
                    <th>A</th>
                    <?
                } else {
                    foreach ($arrBulan as $valbln) {
                        if ($valbln != KEY::$KEY_MONTH_ALL) {
                            ?>
                            <th>Kupon Terjual</th>
                            <th>A</th>
                            <?
                        }
                    }
                }
                ?>

            </tr>
            </thead>
            <tbody>
            <?
            $totalTerjual = 0;
            $totalAktiv = 0;
            if ($bln != KEY::$KEY_MONTH_ALL) {
                $i = 1;
                $totalTerjual = 0;
                $totalAktiv = 0;

                $birekap = new RekapSiswaIBOModel();
                $birekap->getWhereOne("bi_rekap_tc_id=$keyTC AND bi_rekap_bln=$bln AND bi_rekap_tahun=$thn");

                $totalTerjual += $birekap->bi_rekap_kupon;
                $totalAktiv += $birekap->bi_rekap_aktiv;
                ?>
                <tr>
                    <td><?= $i; ?></td>
                    <td><?= Generic::getTCNamebyID($keyTC); ?></td>
                    <td><?
                        if ($birekap->bi_rekap_kupon == "") {
                            echo 0;
                        } else {
                            echo $birekap->bi_rekap_kupon;
                        }
                        ?></td>

                    <td><?
                        if ($birekap->bi_rekap_aktiv == "") {
                            echo 0;
                        } else {
                            echo $birekap->bi_rekap_aktiv;
                        }
                        ?></td>
                </tr>
                <?
                $i++;

            } else {
                $i = 1;
                $totalTerjual = array();
                $totalAktiv = array();

                ?>
                <tr>
                    <td><?= $i; ?></td>
                    <td><?= $tc; ?></td>
                    <?
                    //
                    foreach ($arrBulan as $valbln) {
                        if ($valbln != KEY::$KEY_MONTH_ALL) {

                            $birekap = new RekapSiswaIBOModel();
                            $birekap->getWhereOne("bi_rekap_tc_id=$keyTC AND bi_rekap_bln=$valbln AND bi_rekap_tahun=$thn");
                            $totalTerjual[$valbln] = $totalTerjual[$valbln] + $birekap->bi_rekap_kupon;
                            $totalAktiv[$valbln] = $totalAktiv[$valbln] + $birekap->bi_rekap_aktiv;
//                                    pr($valbln . " - " . $totalAktiv[$valbln]);
                            ?>
                            <td><?
                                if ($birekap->bi_rekap_kupon == "") {
                                    echo 0;
                                } else {
                                    echo $birekap->bi_rekap_kupon;
                                }
                                ?></td>

                            <td><?
                                if ($birekap->bi_rekap_aktiv == "") {
                                    echo 0;
                                } else {
                                    echo $birekap->bi_rekap_aktiv;
                                }
                                ?></td>
                            <?
                        }
                    }
                    ?>
                </tr>
                <?
                $i++;

            }
            //                pr($totalTerjual);
            ?>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="2" style="text-align:center; font-weight: bold">Total</td>
                <?
                if ($bln != KEY::$KEY_MONTH_ALL) {
                    ?>
                    <td><?= $totalTerjual ?></td>
                    <td><?= $totalAktiv ?></td>
                    <?
                } else {
                    foreach ($arrBulan as $valbln) {
                        if ($valbln != KEY::$KEY_MONTH_ALL) {
                            ?>
                            <td><?= $totalTerjual[$valbln] ?></td>
                            <td><?= $totalAktiv[$valbln] ?></td>
                            <?
                        }
                    }
                }
                ?>

            </tr>
            </tfoot>
        </table>

        <script>
            $('#export_<?= $t; ?>').click(function () {
                //                var column = <?= $column; ?>;

                window.open('<?= _SPPATH; ?>BIWebHelper/exporttoexcel?column=<?= $column; ?>&jenis_rekap=Rekapitulasi Kupon per TC', "_blank ");

            });
        </script>

        <?
    }

    function loadLaporanBelajarMurid()
    {
        $arrMyTC = Generic::getAllMyTC(AccessRight::getMyOrgID());
        $tc_id = isset($_GET['tc_id']) ? addslashes($_GET['tc_id']) : Key($arrMyTC);
        $t = time();

        $i = 1;
        $sudah = array();
        $muridMatrix = new MuridKelasMatrix();
        $arrGuru = $muridMatrix->getWhere("tc_id=$tc_id AND active_status = 1 ORDER BY level_murid ASC");
        $firstime = true;
        $now = new DateTime();
        $kelasByGuru = array();
        foreach ($arrGuru as $mm) {
            $kelasByGuru[$mm->guru_id][] = $mm;
        }

        $num = 1;
        foreach ($kelasByGuru as $guru_id => $arrmm) {

            $guru = new SempoaGuruModel();
            $guru->getByID($guru_id);
            ?>
            <tr class="guru_coach">
                <td colspan="4">
                    <?= $guru->nama_guru; ?>
                </td>
            </tr>

            <?
            foreach ($arrmm as $mm) {
                $datetime2 = new DateTime($mm->active_date);
                $datetime1 = new DateTime(date('Y-m-d H:i:s'));
                $interval = $datetime2->diff($datetime1);

                if (in_array($mm->murid_id . $guru_id, $sudah)) {
                    $datetime2 = new DateTime($mm->active_date);
                    $datetime1 = new DateTime(date('Y-m-d H:i:s'));
                    $interval2 = $datetime2->diff($datetime1);
                    if ($interval2 > $interval) {
                        $interval = $interval2;
                    }
                    continue;
                }
                $sudah[] = $mm->murid_id . $guru_id;
                $murid = new MuridModel();
                $murid->getByID($mm->murid_id);
                ?>
                <tr>
                    <td>
                        <?=
                        $num;
                        $num++;
                        ?>
                    </td>
                    <td>
                        <?= Generic::getMuridNamebyID($mm->murid_id); ?>
                    </td>
                    <td>
                        <?= Generic::getLevelNameByID($murid->id_level_sekarang); ?>
                    </td>
                    <td>
                        <?= floor($interval->format('%R%a ') / 30); ?> bulan
                    </td>
                </tr>

            <? } ?>

            <?
        }
    }

    public function load_laporan_jumlah_siswa_by_status()
    {
        $t = time();
        $arrBulan = Generic::getAllMonthsWithAll();
        $bln = isset($_GET['bln']) ? addslashes($_GET['bln']) : date("n");
        $thn = isset($_GET['thn']) ? addslashes($_GET['thn']) : date("Y");
        $ibo_id = isset($_GET['ibo_id']) ? addslashes($_GET['ibo_id']) : key($arrMyIBO);
        $arrLevel = Generic::getAllLevel();

        if ($bln != KEY::$KEY_MONTH_ALL) {
            ?>
            <table class="table table-bordered table-striped" style="background-color: white;">
                <thead>
                <tr>
                    <th rowspan="2" class="tengah">Level</th>
                    <?
                    if ($bln != "All") {
                        ?>
                        <th id="<?= $bln . "_" . $thn; ?>" class="total"
                            colspan="3"><?= Generic::getMonthName($bln); ?></th>

                        <?
                    }
                    ?>
                    <th colspan="1">Total</th>
                </tr>
                <tr>
                    <th>Aktif</th>
                    <th>Cuti</th>
                    <th>Keluar</th>
                    <th>Keluar</th>
                </tr>
                </thead>
                <tbody>
                <?
                $aktiv = 0;
                $cuti = 0;
                $keluar = 0;
                $aktivTotal = 0;
                $cutiTotal = 0;
                $keluarTotal = 0;
                foreach ($arrLevel as $key => $level) {
                    $hlp = new StatusHisMuridModel();
                    $aktiv = $hlp->getJumlahMuridAktivByMonth($ibo_id, $key, $bln, $thn);
                    $aktivTotal += $aktiv;
                    $cuti = $hlp->getJumlahMuridCutiByMonth($ibo_id, $key, $bln, $thn);
                    $cutiTotal += $cuti;
                    $keluar = $hlp->getJumlahMuridKeluarByMonth($ibo_id, $key, $bln, $thn);
                    $keluarTotal += $keluar;
                    ?>
                    <tr>
                        <td><?= $level; ?></td>
                        <td><?= $aktiv; ?></td>
                        <td><?= $cuti; ?></td>
                        <td><?= $keluar; ?></td>
                        <td><?= $keluar; ?></td>
                    </tr>
                    <?
                }
                ?>
                </tbody>
                <tfoot>
                <tr>
                    <td class="total">Total</td>
                    <td><?= $aktivTotal; ?></td>
                    <td><?= $cutiTotal; ?></td>
                    <td><?= $keluarTotal; ?></td>
                    <td><?= $keluarTotal; ?></td>
                </tr>
                </tfoot>
            </table>
            <?
        } else {
            ?>
            <table class="table table-bordered table-striped" style="background-color: white;">
                <thead>
                <tr>
                    <th rowspan="2" style="text-align:center; font-weight: bold">Level</th>
                    <?
                    foreach ($arrBulan as $bln) {
                        if ($bln != "All") {
                            ?>
                            <th id="<?= $bln . "_" . $thn; ?>" class="total"
                                colspan="3"><?= Generic::getMonthName($bln); ?></th>

                            <?
                        }
                    }
                    ?>
                    <th>Total</th>
                </tr>
                <tr>
                    <?
                    foreach ($arrBulan as $bln) {
                        if ($bln != "All") {
                            ?>
                            <th>Aktif</th>
                            <th>Cuti</th>
                            <th>Keluar</th>
                            <?
                        }
                    }
                    ?>
                    <th>Keluar</th>


                </tr>
                </thead>
                <tbody>
                <?
                $aktiv = 0;
                $cuti = 0;
                $keluar = 0;
                $aktivTotal = array();
                $cutiTotal = array();
                $keluarTotal = array();


                foreach ($arrLevel as $key => $level) {
                    $keluarRow = 0;
                    ?>
                    <tr>
                        <td><?= $level; ?></td>
                        <?
                        foreach ($arrBulan as $bln) {
                            if ($bln != "All") {
                                $hlp = new StatusHisMuridModel();
                                $aktiv = $hlp->getJumlahMuridAktivByMonth($ibo_id, $key, $bln, $thn);
                                $aktivTotal[$bln] += $aktiv;
                                $cuti = $hlp->getJumlahMuridCutiByMonth($ibo_id, $key, $bln, $thn);
                                $cutiTotal[$bln] += $cuti;
                                $keluar = $hlp->getJumlahMuridKeluarByMonth($ibo_id, $key, $bln, $thn);
                                $keluarTotal[$bln] += $keluar;
                                $keluarRow += $keluar;
                                ?>

                                <td><?= $aktiv; ?></td>
                                <td><?= $cuti; ?></td>
                                <td><?= $keluar; ?></td>

                                <?
                            }
                        }
                        ?>
                        <td><?= $keluarRow; ?></td>
                        <? ?>
                    </tr>
                    <?
                }
                ?>
                </tbody>
                <tfoot>
                <tr>
                    <td class="total">Total</td>
                    <?
                    $keluarRow = 0;
                    foreach ($arrBulan as $bln) {
                        $keluarRow += $keluarTotal[$bln];
                        if ($bln != "All") {
                            ?>
                            <td><?= $aktivTotal[$bln]; ?></td>
                            <td><?= $cutiTotal[$bln]; ?></td>
                            <td><?= $keluarTotal[$bln]; ?></td>

                            <?
                        }
                    }
                    ?>
                    <td><?= $keluarRow; ?></td>
                </tr>
                </tfoot>
            </table>
            <?
        }
    }

    public function load_tc_table()
    {
        $ibo_id = addslashes($_GET['ibo_id']);
        $objIBO = new SempoaOrg();
        $arrMyTC = $objIBO->getWhere("org_parent_id=$ibo_id AND org_type='tc'");
//        pr($ibo_id);
        foreach ($arrMyTC as $key => $val) {
            $murid = new MuridModel();
            $guru = new SempoaGuruModel();
            ?>
            <tr>
                <td><?= $key + 1; ?></td>
                <td><?= $val->nama; ?></td>
                <td><?= $val->nama_pemilik; ?></td>
                <td><?= $val->tanggal_lahir; ?></td>
                <td><?= $val->alamat_rmh_priv; ?></td>
                <td><?= $val->telp_priv; ?></td>
                <td><?= $val->hp_priv; ?></td>
                <td><?= $val->email_priv; ?></td>
                <td><?= $val->tgl_kontrak; ?></td>
                <td><?= $val->alamat; ?></td>
                <td><?= $val->nomor_telp; ?></td>
                <td><?= $val->email; ?></td>
                <td><?= $guru->getCountAktivGuruByTC($val->org_id); ?></td>
                <td><?= $murid->getMuridAktiv($val->org_id) ?></td>

            </tr>
            <?
        }
    }

    public function load_coach_tc_table()
    {
        $ibo_id = addslashes($_GET['ibo_id']);
        $objIBO = new SempoaOrg();
        $arrMyTC = $objIBO->getWhere("org_parent_id=$ibo_id AND org_type='tc'");
//        pr($ibo_id);
        $i = 1;
        global $db;
        foreach ($arrMyTC as $key => $val) {
            $guru = new SempoaGuruModel();
            $arrGuru = $guru->getAllGuruAktivByTC($val->org_id);
            $sempoa = new SempoaOrg();
            $sempoa->getByID($val->org_id);
//                        pr($sempoa);
            foreach ($arrGuru as $valGuru) {

                $mk = new MuridKelasMatrix();
                $cnt = $mk->getJumlahSiswaByGuru($valGuru->guru_id, $val->org_id);
                ?>
                <tr>
                    <td><?= $i; ?></td>
                    <td><?= $valGuru->nama_guru; ?></td>
                    <td><?= $sempoa->nama; ?></td>
                    <td><?= $sempoa->nama_pemilik; ?></td>
                    <td><?= Generic::getLevelNameByID($valGuru->id_level_training_guru); ?></td>
                    <td><?= $valGuru->tanggal_lahir; ?></td>
                    <td><?= $valGuru->alamat; ?></td>
                    <td><?= $valGuru->nomor_hp; ?></td>
                    <td><?= $valGuru->email_guru; ?></td>
                    <td><?= $cnt; ?></td>
                </tr>
                <?
                $i++;
            }
        }
    }

    public function testPrev()
    {
        pr(Generic::getMyRoot());
        $r = new BIRekapKuponModel();
        pr(Generic::getTCNamebyID(231));
        pr($r->getDatenPrevMonth(10, 2016, 1, 198, 225, 231));
    }

    public function exportSempoa()
    {
        $type = $_GET['type'];
        $bln = $_GET['bln'];
        $thn = $_GET['thn'];
        switch ($type) {
            case KEY::$REPORT_REKAP_KUPON_TC:
                $ibo_id = $_GET['ibo_id'];
                $export = new SempoaExport();
                $columnname = $export->header_rekap_kupon_tc($bln);
                $columnContent = $export->content_rekap_kupon_tc($ibo_id, $bln, $thn);
                $judulTengah = KEY::$JUDUL_REPORT_REKAP_KUPON_TC . Generic::getTCNamebyID($ibo_id);
                $filename = KEY::$REPORT_REKAP_KUPON_TC . Generic::getTCNamebyID($ibo_id);
                $this->export($judulTengah, $filename, $columnname, $columnContent);
                break;
            case KEY::$REPORT_REKAP_SISWA_IBO:
                $ibo_id = $_GET['ibo_id'];
                $export = new SempoaExport();
                $header = $export->header_rekap_siswa_ibo($bln);
                $columnContent = $export->content_rekap_siswa_ibo($ibo_id, $bln, $thn);
                $judulTengah = KEY::$JUDUL_REPORT_REKAP_SISWA_IBO . Generic::getTCNamebyID($ibo_id);
                $filename = KEY::$REPORT_REKAP_SISWA_IBO . Generic::getTCNamebyID($ibo_id);
                $this->export($judulTengah, $filename, $header, $columnContent);
                break;
            case KEY::$REPORT_REKAP_BULANAN_KUPON:
                $tc_id = $_GET['tc_id'];
                $bln = $_GET['bln'];
                $thn = $_GET['thn'];
                $export = new SempoaExport();
                $header = $export->header_rekap_bulanan_kupon($bln);
                $columnContent = $export->content_rekap_bulanan_kupon($tc_id, $bln, $thn);
                $judulTengah = KEY::$JUDUL_REKAP_BULANAN_KUPON . Generic::getTCNamebyID($tc_id);
                $filename = KEY::$REPORT_REKAP_BULANAN_KUPON . Generic::getTCNamebyID($tc_id);
                $this->export($judulTengah, $filename, $header, $columnContent);
                break;

            case KEY::$REPORT_REKAP_JUMLAH_SISWA_STATUS_KPO:
                $ibo_id = $_GET['ibo_id'];
                $export = new SempoaExport();
                $header = $export->header_rekap_jumlah_siswa_kpo($bln);
                $columnContent = $export->content_rekap_jumlah_siswa_kpo($ibo_id, $bln, $thn);
                $this->export(KEY::$JUDUL_REPORT_REKAP_JUMLAH_SISWA_STATUS_KPO . " " . Generic::getTCNamebyID($ibo_id), KEY::$REPORT_REKAP_JUMLAH_SISWA_STATUS_KPO . " " . Generic::getTCNamebyID($ibo_id), $header, $columnContent);
                break;

            case KEY::$REPORT_REKAP_ALL_SISWA_KPO:
                $export = new SempoaExport();
                $header = $export->header_rekap_all_siswa($bln);
                $columnContent = $export->content_rekap_all_siswa($bln, $thn);
                $judulTengah = KEY::$JUDUL_REPORT_REKAP_ALL_SISWA_KPO;
                $filename = KEY::$REPORT_REKAP_ALL_SISWA_KPO;
                $this->export($judulTengah, $filename, $header, $columnContent);
                break;

            case KEY::$REPORT_REKAP_SISWA_KPO:
                $ibo_id = $_GET['ibo_id'];
                $export = new SempoaExport();
                $header = $export->header_rekap_siswa_kpo($bln);
                $columnContent = $export->content_rekap_siswa_kpo($ibo_id, $bln, $thn);
                $judulTengah = KEY::$JUDUL_REPORT_REKAP_SISWA_KPO . " " . Generic::getTCNamebyID($ibo_id);
                $filename = KEY::$REPORT_REKAP_SISWA_KPO . " " . Generic::getTCNamebyID($ibo_id);
                $this->export($judulTengah, $filename, $header, $columnContent);
                break;

            case KEY::$REPORT_REKAP_ABSEN_GURU:
                $tc_id = $_GET['tc_id'];
                $week = $_GET['week'];
                $tglWeek = $_GET['tglWeek'];
                $export = new SempoaExport();
                $header = $export->header_rekap_absen_guru($week, $tglWeek);
                $columnContent = $export->content_rekap_absen_guru($week, $thn, $tc_id);
                $judulTengah = KEY::$JUDUL_REPORT_REKAP_ABSEN_GURU;
                $filename = KEY::$REPORT_REKAP_ABSEN_GURU . " " . Generic::getTCNamebyID($tc_id);
                $this->export($judulTengah, $filename, $header, $columnContent);

                break;

            case KEY::$REPORT_REKAP_LAMA_BELAJAR_MURID_TC:
                $tc_id = $_GET['tc_id'];
                $export = new SempoaExport();
                $header = $export->header_rekap_lama_belajar($week, $tglWeek);
                $columnContent = $export->content_rekap_lama_belajar($tc_id);
                $judulTengah = KEY::$JUDUL_REKAP_LAMA_BELAJAR_MURID_TC;
                $filename = KEY::$REPORT_REKAP_LAMA_BELAJAR_MURID_TC . " " . Generic::getTCNamebyID($tc_id);
                $this->export($judulTengah, $filename, $header, $columnContent);

                break;

            case KEY::$REPORT_REKAP_PERKEMBANGAN_IBO:
                $thn = $_GET['thn'];
                $export = new SempoaExport();
                $header = $export->header_rekap_perkembanganIBO($thn);
                $columnContent = $export->content_rekap_perkembanganIBO(AccessRight::getMyOrgID(), $thn);
                $judulTengah = KEY::$JUDUL_REKAP_PERKEMBANGAN_IBO;
                $filename = KEY::$REPORT_REKAP_PERKEMBANGAN_IBO;
                $this->export($judulTengah, $filename, $header, $columnContent);

                break;

            case KEY::$REPORT_REKAP_PENJUALAN_B_K_S:
                $thn = $_GET['thn'];
                $export = new SempoaExport();
                $header = $export->header_rekap_jumlah_siswa_buku_kupon($thn);
                $columnContent = $export->content_rekap_jumlah_siswa_buku_kupon(AccessRight::getMyOrgID(), $thn);
                $judulTengah = KEY::$JUDUL_REPORT_REKAP_PENJUALAN_B_K_S;
                $filename = KEY::$REPORT_REKAP_PENJUALAN_B_K_S;
                $this->export($judulTengah, $filename, $header, $columnContent);

                break;

            case KEY::$REPORT_REKAP_KUPON_TC_LVL:
                $tc_id = $_GET['tc_id'];
                $export = new SempoaExport();
                $columnname = $export->header_rekap_kupon_tc($bln);
                $columnContent = $export->content_rekap_kupon_tc_lvl($tc_id, $bln, $thn);
                $judulTengah = KEY::$JUDUL_REPORT_REKAP_KUPON_TC . Generic::getTCNamebyID($tc_id);
                $filename = KEY::$REPORT_REKAP_KUPON_TC . Generic::getTCNamebyID($tc_id);
                $this->export($judulTengah, $filename, $columnname, $columnContent);
                break;
//
            case KEY::$REPORT_REKAP_SISWA_IBO_TC_LVL:
                $tc_id = $_GET['tc_id'];
                $export = new SempoaExport();
                $header = $export->header_rekap_siswa_ibo($bln);
                $columnContent = $export->content_rekap_siswa_tc_lvl($tc_id, $bln, $thn);
                $judulTengah = KEY::$JUDUL_REPORT_REKAP_SISWA_TC . Generic::getTCNamebyID($tc_id);
                $filename = KEY::$REPORT_REKAP_SISWA_IBO_TC_LVL . Generic::getTCNamebyID($tc_id);
                $this->export($judulTengah, $filename, $header, $columnContent);
                break;


            case KEY::$REPORT_REKAP_BULANAN_KUPON_TC_LVL:
                $tc_id = $_GET['tc_id'];
                $bln = $_GET['bln'];
                $thn = $_GET['thn'];
                $export = new SempoaExport();
                $header = $export->header_rekap_bulanan_kupon($bln);
                $columnContent = $export->content_rekap_bulanan_kupon_tc_lvl($tc_id, $bln, $thn);
                $judulTengah = KEY::$JUDUL_REKAP_BULANAN_KUPON . Generic::getTCNamebyID($tc_id);
                $filename = KEY::$REPORT_REKAP_BULANAN_KUPON_TC_LVL . Generic::getTCNamebyID($tc_id);
                $this->export($judulTengah, $filename, $header, $columnContent);
                break;
        }
        if ($type) {

        }
    }

    public function export($titleexcel, $title, $columnname, $columnContent)
    {

        $objPHPExcel = new PHPExcel();

// Set properties
        $objPHPExcel->getProperties()->setCreator("Sempoa SIP")
            ->setLastModifiedBy("Sempoa SIP")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Result file");
        $objPHPExcel->setActiveSheetIndex(0);
        $objWorkSheet = $objPHPExcel->getActiveSheet();


        $cell = $objWorkSheet->getCell("A" . 1);
        $cell->setValue($titleexcel);
        $objPHPExcel->getActiveSheet()->getStyle("A1:A1")->getFont()->setSize(16);
        // Init Header
//        $row = 2;
        $col = 0;
        $column = 'A';
        foreach ($columnname as $val) {
            $row = $val->zeile;
            if ($val->awal === 1) {
                $column = 'A';
            }
            if ($val->mulaiColumn != "") {
                $column = $val->mulaiColumn;
            }

            $cell = $objWorkSheet->getCell($column . $row);
            if ($val->anzahlcolumn > 1) {
                $columnhelp = $column;
                for ($i = 0; $i < $val->anzahlcolumn - 1; $i++) {
                    $columnhelp++;
                }
//                $objWorkSheet->mergeCells($column . "1:" . $columnhelp . "1");
                $objWorkSheet->mergeCells($column . $row . ":" . $columnhelp . $row);
                $column = $columnhelp;
            }
            if ($val->textAllign == "center") {
                $objWorkSheet->getStyle($column . "1:" . $columnhelp . "1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            }
            $cell->setValue($val->name);
            $column++;
        }


        $row = 1;
        $col = 1;
        // Init Content
//        $column = 'A';
        foreach ($columnContent as $val) {

            $row = $val->zeile + 2;
            if ($val->awal === 1) {
                $column = 'A';
            }
            if ($val->mulaiColumn != "") {
                $column = $val->mulaiColumn;
            }

            $cell = $objWorkSheet->getCell($column . $row);
            if ($val->anzahlcolumn > 1) {
                $columnhelp = $column;
                for ($i = 0; $i < $val->anzahlcolumn - 1; $i++) {
                    $columnhelp++;
                }
                $objWorkSheet->mergeCells($column . $row . ":" . $columnhelp . $row);
                $column = $columnhelp;
            }
            if ($val->textAllign == "center") {
                $objWorkSheet->getStyle($column . $row)->getAlignment()->applyFromArray(
                    array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,));
            }
            $cell->setValue($val->name);
            $column++;
            $row++;
        }

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $title . "_" . date('Ymd') . '.xlsx');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
//        $objWriter->save($title . "_" . date('Ymd') . '.xlsx');
        exit;


// Add some data
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Hello')
            ->setCellValue('B2', 'world!')
            ->setCellValue('C1', 'Hello')
            ->setCellValue('D2', 'world!');

// Miscellaneous glyphs, UTF-8
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A4', 'Miscellaneous glyphs')
            ->setCellValue('A5', 'éàèùâêîôûëïüÿäöüç');

// Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle('Simple');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);
    }

    public function sempoaexport()
    {
        $arrMyTC = Generic::getAllMyTC(AccessRight::getMyOrgID());
        $bln = isset($_GET['bln']) ? addslashes($_GET['bln']) : date("n");
        $thn = isset($_GET['thn']) ? addslashes($_GET['thn']) : date("Y");
        $t = time();
        $arrBulan = Generic::getAllMonths();
        $arrBulan[count($arrBulan) + 1] = "All";

        $columns[] = "No.";
        $columns[] = "t";
        $columns[] = "Nama TC";
        $columns[] = "t";
        $columns[] = Generic::getMonthName($bln);
        $columns[] = "n";
        $columns[] = "t";
        $columns[] = "t";
        $columns[] = "Kupon Terjual";
        $columns[] = "t";
        $columns[] = "A";
        $columns[] = "n";
        $str = serialize($columns);
        $column = urlencode($str);


        $totalTerjual = 0;
        $totalAktiv = 0;
        if ($bln != KEY::$KEY_MONTH_ALL) {
            $i = 1;
            foreach ($arrMyTC as $keyTC => $tc) {
                $birekap = new BIRekapKuponModel();
                $birekap->getWhereOne("bi_kupon_tc_id=$keyTC AND bi_kupon_bln=$bln AND bi_kupon_thn=$thn");
                $totalTerjual += $birekap->bi_kupon_trs_bln;
                $totalAktiv += $birekap->bi_kupon_stock_akhir;
                ?>
                <tr>
                    <td><?= $i; ?></td>
                    <td><?= $tc; ?></td>
                    <td><?= $birekap->bi_kupon_trs_bln; ?></td>
                    <td><?= $birekap->bi_kupon_stock_akhir; ?></td>
                </tr>
                <?
                $i++;
            }
        } else {
            $i = 1;
            $totalTerjual = array();
            $totalAktiv = array();
            foreach ($arrMyTC as $keyTC => $tc) {

                foreach ($arrBulan as $valbln) {
                    if ($valbln != KEY::$KEY_MONTH_ALL) {
                        $birekap = new BIRekapKuponModel();
                        $birekap->getWhereOne("bi_kupon_tc_id=$keyTC AND bi_kupon_bln=$valbln AND bi_kupon_thn=$thn");
                        $totalTerjual[$valbln] = $totalTerjual[$valbln] + $birekap->bi_kupon_trs_bln;
                        $totalAktiv[$valbln] = $totalAktiv[$valbln] + $birekap->bi_kupon_stock_akhir;
//                                    pr($valbln . " - " . $totalAktiv[$valbln]);
                    }
                }

                $i++;
            }
        }

        if ($bln != KEY::$KEY_MONTH_ALL) {

        } else {
            foreach ($arrBulan as $valbln) {
                if ($valbln != KEY::$KEY_MONTH_ALL) {

                }
            }
        }
        ?>
        <script>
            $(document).ready(function () {
                alert(<?= $bln ?>);
                window.open('<?= _SPPATH; ?>BIWebHelper/exporttoexcel?column=<?= $column; ?>&footer=<?= $contentfooter; ?>&content=<?= $contentcolumn; ?>&jenis_rekap=Rekapitulasi Kupon per TC', "_blank ");

            });
        </script>
        <?
    }

    public function exporttoexcel()
    {
        $columnname = unserialize(urldecode($_GET['column']));
        $jenisRekap = $_GET['jenis_rekap'];
        $bln = $_GET['bln'];
        $arrToExport = unserialize(urldecode($_GET['content']));
        $footer = unserialize(urldecode($_GET['footer']));
        $filename = $jenisRekap . "_" . date('Ymd') . ".xls";

        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");
        $flag = false;
        foreach ($columnname as $val) {
            if ($val == 't') {
                $head .= "\t";
            } elseif ($val == 'n') {
                $head .= "\n";
            } else {
                $head .= $val;
            }
        }
        echo $head;

        foreach ($arrToExport as $val) {
            if ($val == 't') {
                $content .= "\t";
            } elseif ($val == 'n') {
                $content .= "\n";
            } else {
                $content .= $val;
            }
        }
        echo $content;

        foreach ($footer as $val) {
            if ($val == 't') {
                $footerHlp .= "\t";
            } elseif ($val == 'n') {
                $footerHlp .= "\n";
            } else {
                $footerHlp .= (String)$val;
            }
        }
        echo $footerHlp;

        exit;
    }

    public function loadabsencoach()
    {
        $week = addslashes($_GET['week']);
        $thn = addslashes($_GET['thn']);
        $tc_id = addslashes($_GET['tc_id']);
        $i = 1;
        $senin = 0;
        $selasa = 0;
        $rabu = 0;
        $kamis = 0;
        $jumat = 0;
        $sabtu = 0;
        $birekap = new RekapAbsenCoach();
        $arrRekap = $birekap->getWhere("ac_tc_id=$tc_id AND ac_week=$week AND ac_tahun=$thn");
        foreach ($arrRekap as $val) {
            ?>
            <tr>
                <td><?= $i; ?></td>
                <td><?= $val->ac_nama_guru_dtg; ?></td>
                <td>
                    <?
                    $senin += $val->ac_1;
                    echo $val->ac_1;
                    ?>
                </td>
                <td><?
                    $arrLevel = explode(",", $val->ac_level_1);
                    //                        pr($arrLevel);
                    echo Generic::getLevelAbsenCoach($arrLevel);
                    ?></td>
                <td>

                    <?
                    $selasa += $val->ac_2;
                    echo $val->ac_2;
                    ?>

                </td>
                <td><?
                    $arrLevel = explode(",", $val->ac_level_2);
                    echo Generic::getLevelAbsenCoach($arrLevel);
                    ?></td>
                <td>
                    <?
                    $rabu += $val->ac_3;
                    echo $val->ac_3;
                    ?>
                </td>
                <td><?
                    $arrLevel = explode(",", $val->ac_level_3);
                    echo Generic::getLevelAbsenCoach($arrLevel);
                    ?></td>
                <td>
                    <?
                    $kamis += $val->ac_4;
                    echo $val->ac_4;
                    ?>
                </td>
                <td><?
                    $arrLevel = explode(",", $val->ac_level_4);
                    echo Generic::getLevelAbsenCoach($arrLevel);
                    ?></td>
                <td>
                    <?
                    $jumat += $val->ac_5;
                    echo $val->ac_5;
                    ?>
                </td>
                <td><?
                    $arrLevel = explode(",", $val->ac_level_5);
                    echo Generic::getLevelAbsenCoach($arrLevel);
                    ?></td>
                <td>
                    <?
                    $sabtu += $val->ac_6;
                    echo $val->ac_6;
                    ?>
                </td>
                <td><?
                    $arrLevel = explode(",", $val->ac_level_6);
                    echo Generic::getLevelAbsenCoach($arrLevel);
                    ?></td>
            </tr>
            <?
            $i++;
        }

        ?>
        <tr>
            <td colspan="2">Total</td>
            <td><?= $senin; ?></td>
            <td></td>
            <td><?= $selasa; ?></td>
            <td></td>
            <td><?= $rabu; ?></td>
            <td></td>
            <td><?= $kamis; ?></td>
            <td></td>
            <td><?= $jumat; ?></td>
            <td></td>
            <td><?= $sabtu; ?></td>
            <td></td>
        </tr>
        <?
    }

    public function loadabsencoach_tc()
    {
        $week = addslashes($_GET['week']);
        $thn = addslashes($_GET['thn']);
        $tc_id = AccessRight::getMyOrgID();
        $i = 1;
        $senin = 0;
        $selasa = 0;
        $rabu = 0;
        $kamis = 0;
        $jumat = 0;
        $sabtu = 0;
        $birekap = new RekapAbsenCoach();
        $arrRekap = $birekap->getWhere("ac_tc_id=$tc_id AND ac_week=$week AND ac_tahun=$thn");
        foreach ($arrRekap as $val) {
            ?>
            <tr>
                <td><?= $i; ?></td>
                <td><?= $val->ac_nama_guru_dtg; ?></td>
                <td>
                    <?
                    $senin += $val->ac_1;
                    echo $val->ac_1;
                    ?>
                </td>
                <td><?
                    $arrLevel = explode(",", $val->ac_level_1);
                    //                        pr($arrLevel);
                    echo Generic::getLevelAbsenCoach($arrLevel);
                    ?></td>
                <td>

                    <?
                    $selasa += $val->ac_2;
                    echo $val->ac_2;
                    ?>

                </td>
                <td><?
                    $arrLevel = explode(",", $val->ac_level_2);
                    echo Generic::getLevelAbsenCoach($arrLevel);
                    ?></td>
                <td>
                    <?
                    $rabu += $val->ac_3;
                    echo $val->ac_3;
                    ?>
                </td>
                <td><?
                    $arrLevel = explode(",", $val->ac_level_3);
                    echo Generic::getLevelAbsenCoach($arrLevel);
                    ?></td>
                <td>
                    <?
                    $kamis += $val->ac_4;
                    echo $val->ac_4;
                    ?>
                </td>
                <td><?
                    $arrLevel = explode(",", $val->ac_level_4);
                    echo Generic::getLevelAbsenCoach($arrLevel);
                    ?></td>
                <td>
                    <?
                    $jumat += $val->ac_5;
                    echo $val->ac_5;
                    ?>
                </td>
                <td><?
                    $arrLevel = explode(",", $val->ac_level_5);
                    echo Generic::getLevelAbsenCoach($arrLevel);
                    ?></td>
                <td>
                    <?
                    $sabtu += $val->ac_6;
                    echo $val->ac_6;
                    ?>
                </td>
                <td><?
                    $arrLevel = explode(",", $val->ac_level_6);
                    echo Generic::getLevelAbsenCoach($arrLevel);
                    ?></td>
            </tr>
            <?
            $i++;
        }

        ?>
        <tr>
            <td colspan="2">Total</td>
            <td><?= $senin; ?></td>
            <td></td>
            <td><?= $selasa; ?></td>
            <td></td>
            <td><?= $rabu; ?></td>
            <td></td>
            <td><?= $kamis; ?></td>
            <td></td>
            <td><?= $jumat; ?></td>
            <td></td>
            <td><?= $sabtu; ?></td>
            <td></td>
        </tr>
        <?
    }

    public function laporan_jumlah_siswa_buku_kupon()
    {
        $ibo_id = addslashes($_GET['ibo_id']);

        $arrMyIBO = Generic::getAllMyIBO($ibo_id);
        $thn = isset($_GET['thn']) ? addslashes($_GET['thn']) : date("Y");
        $thn_skrg = date("Y");
        $bln_skrg = date("n");
        $t = time();
        $title = KEY::$JUDUL_REPORT_REKAP_PENJUALAN_B_K_S;
        $arrTahun = array();
        $arrTahun[] = $thn - 3;
        $arrTahun[] = $thn - 2;
        $arrTahun[] = $thn - 1;
        $arrTahun[] = $thn;
        $arrKey[1] = "Siswa";
        $arrKey[2] = "Buku";
        $arrKey[3] = "Kupon";
        ?>
        <table class="table table-bordered table-striped" style="background-color: white;">
            <thead>
            <tr>
                <th></th>
                <?
                foreach ($arrTahun as $valthn) {
                    ?>
                    <th><?= $valthn; ?></th>
                    <?
                }
                ?>


            </tr>
            </thead>
            <tbody>
            <?
            foreach ($arrKey as $val) {
                ?>
                <tr>
                    <td><?= $val; ?></td>
                    <?
                    foreach ($arrTahun as $valthn) {
                        $rekap_siswa = new RekapSiswaIBOModel();
                        if ($valthn == $thn_skrg) {
                            $arrRekapSiswa = $rekap_siswa->getWhere(" bi_rekap_ibo_id='$ibo_id' AND bi_rekap_tahun = $valthn AND bi_rekap_bln=$bln_skrg");
                        } else {
                            $arrRekapSiswa = $rekap_siswa->getWhere(" bi_rekap_ibo_id='$ibo_id' AND bi_rekap_tahun = $valthn AND bi_rekap_bln =12");
                        }
                        $total[$val][$valthn] = 0;

                        foreach ($arrRekapSiswa as $valRekap) {
                            if ($val == "Siswa") {
                                $total[$val][$valthn] += $valRekap->bi_rekap_aktiv;
                            }
                            if ($val == "Buku") {
                                $total[$val][$valthn] += $valRekap->bi_rekap_buku;
                            }
                            if ($val == "Kupon") {
                                $total[$val][$valthn] += $valRekap->bi_rekap_kupon;
                            }
                        }
                    }

                    foreach ($arrTahun as $valthn) {
                        ?>
                        <td><?= $total[$val][$valthn]; ?></td>
                        <?
                    }
                    ?>


                </tr>
                <?
            }
            ?>
            <tr></tr>
            </tbody>
            <tfoot></tfoot>
        </table>
        <?
    }

    public function load_laporan_perkembangan_ibo()
    {
        $ibo_id = addslashes($_GET['ibo_id']);
        $thn = isset($_GET['thn']) ? addslashes($_GET['thn']) : date("Y");
        $thn_skrg = date("Y");
        $bln_skrg = date("n");
        $arrMyIBO = Generic::getAllMyIBO(AccessRight::getMyOrgID());
        $t = time();
        $arrTahun = array();
        $arrTahun[] = $thn - 3;
        $arrTahun[] = $thn - 2;
        $arrTahun[] = $thn - 1;
        $arrTahun[] = $thn;

        $i = 1;
        ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped" style="background-color: white;">

                <thead>
                <tr>
                    <th rowspan="2">No.</th>
                    <th rowspan="2">Wilayah</th>
                    <? foreach ($arrTahun as $valThn) {
                        ?>
                        <th colspan="3"><?= $valThn ?></th>
                        <?
                    }
                    ?>
                </tr>
                <tr>

                    <? foreach ($arrTahun as $valThn) {
                        ?>
                        <th>TC</th>
                        <th>Coach</th>
                        <th>Siswa</th>
                        <?
                    }
                    ?>
                </tr>
                </thead>
                <tbody>
                <?
                $i = 1;
                foreach ($arrMyIBO as $keyIBO => $iboname) {
                    ?>
                    <tr>
                        <td><?= $i; ?></td>
                        <td><?= $iboname; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <?
                    $arrmytc = Generic::getAllMyTC($keyIBO);
                    foreach ($arrmytc as $keytc => $tcname) {
                        ?>
                        <tr>
                            <td></td>
                            <td></td>

                            <?
                            foreach ($arrTahun as $keythn => $valthn) {
                                $rekap_siswa = new RekapSiswaIBOModel();
                                if ($valthn == $thn_skrg) {
                                    $arrRekapSiswa = $rekap_siswa->getWhere(" bi_rekap_tc_id=$keytc AND bi_rekap_tahun = $valthn AND bi_rekap_bln=$bln_skrg");
                                } else {
                                    $arrRekapSiswa = $rekap_siswa->getWhere(" bi_rekap_tc_id=$keytc AND bi_rekap_tahun = $valthn AND bi_rekap_bln =12");
                                }
                                $arrCoach[$keytc][$valthn] = 0;
                                $arrSiswa[$keytc][$valthn] = 0;
                                foreach ($arrRekapSiswa as $rekap) {

                                    $arrCoach[$keytc][$valthn] += $rekap->bi_rekap_jumlah_guru;
                                    $arrSiswa[$keytc][$valthn] += $rekap->bi_rekap_aktiv;
                                }
                                ?>
                                <td><?= $tcname; ?></td>
                                <td><?= $arrCoach[$keytc][$valthn]; ?></td>
                                <td><?= $arrSiswa[$keytc][$valthn]; ?></td>
                                <?
                            }
                            //                                pr($arrCoach);
                            //                                pr($arrSiswa);
                            ?>


                        </tr>
                        <?
                    }
                    ?>
                    <? ?>

                    <?
                    $i++;
                }
                ?>
                </tbody>
                <tfoot></tfoot>

            </table>
        </div>
        <?
    }

    public function loadMuridByStatusTC()
    {
        $tc_id = AccessRight::getMyOrgID();
        $bln = isset($_GET['bln']) ? addslashes($_GET['bln']) : date("n");
        $thn = isset($_GET['thn']) ? addslashes($_GET['thn']) : date("Y");
        $status_murid = isset($_GET['status']) ? addslashes($_GET['status']) : 1;
        $arrNamaMurid = Generic::getAllMuridByTC($tc_id);
        $statusHistory = new StatusHisMuridModel();

        if($status_murid != 1){
            $arrStatusHistory = $statusHistory->getWhere("MONTH(status_tanggal_mulai)=$bln AND YEAR(status_tanggal_mulai) = $thn AND status_tc_id = $tc_id AND status = $status_murid ");

        }
        else{
//            $arrStatusHistory = $statusHistory->getWhere("status_tanggal_akhir='1970-01-01 07:00:00' AND status_tc_id = $tc_id AND status = $status_murid ");
            $arrStatusHistory = $statusHistory->getWhere("(MONTH(status_tanggal_akhir)>$bln AND YEAR(status_tanggal_akhir)= $thn OR status_tanggal_akhir='1970-01-01 07:00:00' )AND status_tc_id = $tc_id AND status = $status_murid ");

        }
        $i = 1;
        foreach ($arrStatusHistory as $val) {
            ?>
            <tr>
                <td><?= $i++; ?></td>
                <td><?= $arrNamaMurid[$val->status_murid_id]; ?></td>
                <td><?= Generic::getLevelNameByID($val->status_level_murid); ?></td>
                <td><?= ($val->status_tanggal_mulai); ?></td>
            </tr>
            <?
        }
    }

}
