<?php

/**
 * Created by PhpStorm.
 * User: efindiongso
 * Date: 07/08/18
 * Time: 11.15
 */
class TransaksiPembayaran extends WebService
{

    public function listPembayaranSPPMonthlyTC()
    {

        $bln = isset($_GET['bln']) ? addslashes($_GET['bln']) : date("n");
        $thn = isset($_GET['thn']) ? addslashes($_GET['thn']) : date("Y");
        $tc_id = isset($_GET['tc_id']) ? addslashes($_GET['tc_id']) : AccessRight::getMyOrgID();

        //date("Y-m-d", mktime(0, 0, 0, *YOUR MONTH PARAM*+1,0,date("Y")));
        $logStatusMurid = new LogStatusMurid();
        $logStatusMurid = $logStatusMurid->getWhere("log_tc_id=$tc_id AND log_bln = $bln AND log_thn=$thn  AND log_status= 'A' Order by log_tgl DESC");

        $arrMurid = array();
        $arrMuridName = array();
        foreach ($logStatusMurid as $var) {

            if (!array_key_exists($var->log_id_murid, $arrMurid)) {
                $arrMurid[$var->log_id_murid] = $var->log_status;
                $arrMuridName[$var->log_id_murid] = false;
            }

        }

        $bedingung = "";
        foreach ($arrMuridName as $key => $val) {
            if (end($arrMuridName) != $key) {
                if ($bedingung == "") {
                    $bedingung = $bedingung . " bln_murid_id = $key";
                } else {
                    $bedingung = $bedingung . " OR bln_murid_id = $key";
                }
            }


        }
        $arrBulan = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12);

        $arrSTatus = array("<b>Unpaid</b>", "Paid");

        $t = time();
        ?>

        <section class="content-header">
            <h1>
                <div class="pull-right" style="font-size: 13px;">
                    Bulan :<select id="bulan_<?= $t; ?>">
                        <?
                        foreach ($arrBulan as $bln2) {
                            $sel = "";
                            if ($bln2 == $bln) {
                                $sel = "selected";
                            }
                            ?>
                            <option value="<?= $bln2; ?>" <?= $sel; ?>><?= $bln2; ?></option>
                            <?
                        }
                        ?>
                    </select>
                    Tahun :<select id="tahun_<?= $t; ?>">
                        <?
                        for ($x = date("Y") - 2; $x < date("Y") + 2; $x++) {
                            $sel = "";
                            if ($x == $thn) {
                                $sel = "selected";
                            }
                            ?>
                            <option value="<?= $x; ?>" <?= $sel; ?>><?= $x; ?></option>

                            <?
                        }
                        ?>
                        }
                        ?>
                    </select>

                    <button id="submit_bln_<?= $t; ?>">submit</button>
                </div>
                Iuran Bulanan
            </h1>
            <script>
                $('#submit_bln_<?= $t; ?>').click(function () {
                    var bln = $('#bulan_<?= $t; ?>').val();
                    var thn = $('#tahun_<?= $t; ?>').val();
                    var tc_id = '<?= $tc_id ?>';
                    openLw('listPembayaranSPPMonthlyTC', '<?=_SPPATH;?>TransaksiPembayaran/listPembayaranSPPMonthlyTC' + '?now=' + $.now() + '&bln=' + bln + "&thn=" + thn + "&tc_id=" + tc_id, 'fade');
                });
            </script>
        </section>


        <section class="content">
            <div id="summary_holder" style="text-align: left; font-size: 16px;"></div>
            <div class="table-responsive" style="background-color: #FFFFFF; margin-top: 20px;">
                <table class="table table-striped ">
                    <thead>
                    <tr>
                        <th>
                            Nama Murid
                        </th>
                        <th>
                            Tanggal
                        </th>
                        <th>
                            Kupon
                        </th>
                        <th>
                            Status
                        </th>
                    </tr>
                    </thead>

                    <tbody id="container_iuran_<?= $t; ?>">
                    <?
                    $blndate = $bln . "-" . $thn;
                    $iuranBulanan = new IuranBulanan();
                    $arrIB = $iuranBulanan->getWhere("bln_tc_id=$tc_id AND bln_date='$blndate' AND ( $bedingung)");
                    $arrNameHlp = array();
                    $sdhbayar = 0;
                    foreach ($arrIB as $val) {
                        $murid = new MuridModel();
                        $murid->getByID($val->bln_murid_id);
                        $arrNameHlp[$murid->getNameMurid()] = $val;
                        $arrMuridName[$val->bln_murid_id] = true;
                        $sdhbayar++;
                    }
                    $blmbayar = 0;
                    foreach ($arrMuridName as $id_murid => $val) {
                        if (!$val) {
                            $murid = new MuridModel();
                            $murid->getByID($id_murid);
                            $arrNameHlp[$murid->getNameMurid()] = null;
                            $blmbayar++;
                        }
                    }
                    ?>
                    <div id="summary_bayar">
                <span style="cursor: pointer;"
                      onclick="$('.sudahbayar').show();$('.belumbayar').hide();">Sudah Bayar</span> :
                        <b><? echo $sdhbayar; ?></b>
                        <br>
                <span style="cursor: pointer;"
                      onclick="$('.sudahbayar').hide();$('.belumbayar').show();">Belum Bayar</span> : <b
                            style="color: red;"><?= $blmbayar; ?></b>
                    </div>

                    <?

                    ksort($arrNameHlp);
                    $arrIuran = explode(",", $iuranBulanan->coloumlist);
                    foreach ($arrNameHlp as $nama => $iuran) {
                        ?>
                        <tr>
                        <td>
                            <?= $nama; ?>
                        </td>
                        <td>
                            <?= $iuran->bln_date_pembayaran; ?>
                        </td>
                        <td>
                            <?= $iuran->bln_kupon_id; ?>
                        </td>
                        <td>
                            <? if ($iuran->bln_status == "") {
                                $iuran->bln_status = 0;
                            }
                            echo $arrSTatus[$iuran->bln_status]; ?>
                        </td>
                        </tr><?
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </section>

        <?
    }


    public function listPembayaranSPPMonthlyTCByTime()
    {


        $bln = isset($_GET['bln']) ? addslashes($_GET['bln']) : date("n");
        $thn = isset($_GET['thn']) ? addslashes($_GET['thn']) : date("Y");
        $tc_id = isset($_GET['tc_id']) ? addslashes($_GET['tc_id']) : AccessRight::getMyOrgID();
        $tc_id = 5;
        $bln = 7;
        //date("Y-m-d", mktime(0, 0, 0, *YOUR MONTH PARAM*+1,0,date("Y")));
        $logStatusMurid = new LogStatusMurid();
        $logStatusMurid = $logStatusMurid->getWhere("log_tc_id=$tc_id AND log_bln = $bln AND log_thn=$thn  AND log_status= 'A' Order by log_tgl DESC");

        $arrMurid = array();
        $arrMuridName = array();
        foreach ($logStatusMurid as $var) {

            if (array_key_exists($var->log_id_murid, $arrMurid)) {
//                pr($var->log_id_murid . " -> " . $var->log_tgl . " -> " . $var->log_status);
            } else {
                $arrMurid[$var->log_id_murid] = $var->log_status;
                $arrMuridName[$var->log_id_murid] = false;
            }

        }

        $bedingung = "";
        foreach ($arrMuridName as $key => $val) {
            if (end($arrMuridName) != $key) {
                if ($bedingung == "") {
                    $bedingung = $bedingung . " bln_murid_id = $key";
                } else {
                    $bedingung = $bedingung . " OR bln_murid_id = $key";
                }
            }


        }

        ?>

        <section class="content-header">
            <h1>
                <div class="pull-right" style="font-size: 13px;">
                    Bulan :<select id="bulan_<?= $t; ?>">
                        <?
                        foreach ($arrBulan as $bln2) {
                            $sel = "";
                            if ($bln2 == $bln) {
                                $sel = "selected";
                            }
                            ?>
                            <option value="<?= $bln2; ?>" <?= $sel; ?>><?= $bln2; ?></option>
                            <?
                        }
                        ?>
                    </select>
                    Tahun :<select id="tahun_<?= $t; ?>">
                        <?
                        for ($x = date("Y") - 2; $x < date("Y") + 2; $x++) {
                            $sel = "";
                            if ($x == $thn) {
                                $sel = "selected";
                            }
                            ?>
                            <option value="<?= $x; ?>" <?= $sel; ?>><?= $x; ?></option>

                            <?
                        }
                        ?>
                        }
                        ?>
                    </select>

                    <button id="submit_bln_<?= $t; ?>">submit</button>
                </div>
                Iuran Bulanan
            </h1>
            <script>
                $('#submit_bln_<?= $t; ?>').click(function () {
                    var bln = $('#bulan_<?= $t; ?>').val();
                    var thn = $('#tahun_<?= $t; ?>').val();
                    var tc_id = '<?= $tc_id ?>';
                    openLw('create_operasional_pembayaran_iuran_bulanan_tc', '<?=_SPPATH;?>LaporanWeb/create_operasional_pembayaran_iuran_bulanan_tc' + '?now=' + $.now() + '&bln=' + bln + "&thn=" + thn + "&tc_id=" + tc_id, 'fade');
                });
            </script>
        </section>


        <section class="content">
            <div id="summary_holder" style="text-align: left; font-size: 16px;"></div>
            <div class="table-responsive" style="background-color: #FFFFFF; margin-top: 20px;">
                <table class="table table-striped ">
                    <thead>
                    <tr>
                        <th>
                            Nama Murid
                        </th>
                        <th>
                            Tanggal
                        </th>
                        <th>
                            Kupon
                        </th>
                    </tr>
                    </thead>

                    <tbody id="container_iuran_<?= $t; ?>">
                    <?
                    $blndate = $bln . "-" . $thn;
                    $iuranBulanan = new IuranBulanan();
                    $arrIB = $iuranBulanan->getWhere("bln_tc_id=$tc_id AND bln_date='$blndate' AND ( $bedingung)");
                    $arrNameHlp = array();
                    $sdhbayar = 0;
                    foreach ($arrIB as $val) {
                        $murid = new MuridModel();
                        $murid->getByID($val->bln_murid_id);
                        $arrNameHlp[$murid->getNameMurid()] = $val;
                        $arrMuridName[$val->bln_murid_id] = true;
                        $sdhbayar++;
                    }
                    $blmbayar = 0;
                    foreach ($arrMuridName as $id_murid => $val) {
                        if (!$val) {
                            $murid = new MuridModel();
                            $murid->getByID($id_murid);
                            $arrNameHlp[$murid->getNameMurid()] = null;
                            $blmbayar++;
                        }
                    }
                    ?>
                    Jumlah yang sudah bayar iuran: <?= $sdhbayar; ?>
                    <br>Jumlah yang belum bayar iuran: <?= $blmbayar; ?>
                    <?

                    ksort($arrNameHlp);
                    $arrIuran = explode(",", $iuranBulanan->coloumlist);
                    foreach ($arrNameHlp as $nama => $iuran) {
                        ?>
                        <tr>
                        <td>
                            <?= $nama; ?>
                        </td>
                        <td>
                            <?= $iuran->bln_date_pembayaran; ?>
                        </td>
                        <td>
                            <?= $iuran->bln_kupon_id; ?>
                        </td>
                        </tr><?
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </section>

        <?


//        sempoa__log_status_murid
    }


    public function listPembayaranSPPMonthly()
    {

        $bln = isset($_GET['bln']) ? addslashes($_GET['bln']) : date("n");
        $thn = isset($_GET['thn']) ? addslashes($_GET['thn']) : date("Y");
        $tc_id = isset($_GET['tc_id']) ? addslashes($_GET['tc_id']) : AccessRight::getMyOrgID();

        $blog = false;
        if ($bln < date("n")) {
            $tgl = date("d", mktime(0, 0, 0, $bln + 1, 0, $thn));
            $blog = true;
        } elseif ($bln == date("n")) {
            $tgl = date("d") - 1;
            $blog = true;
        } elseif ($bln > date("n")) {

        }

        $arrMurid = array();
        $arrMuridName = array();
        $arrLogDataMurid = array();

        if ($blog) {
            $logStatusMurid = new LogStatusMurid();
            $arrLogStatusMurid = $logStatusMurid->getWhere("log_tc_id=$tc_id AND log_tgl =$tgl AND log_bln = $bln AND log_thn=$thn  AND log_status= 'A' Order by log_tgl DESC");
            $varLogDataMurid = explode(",", $logStatusMurid->coloumlist);
            foreach ($arrLogStatusMurid as $var) {
                if (!array_key_exists($var->log_id_murid, $arrMurid)) {
                    $arrMurid[$var->log_id_murid] = $var->log_status;
                    $arrMuridName[$var->log_id_murid] = false;
                }
                foreach ($varLogDataMurid as $logDataMurid) {
                    $arrLogDataMurid[$var->log_id_murid][$logDataMurid] = $var->$logDataMurid;

                }

            }
        }






        $bedingung = "";
        foreach ($arrMuridName as $key => $val) {
            if (end($arrMuridName) != $key) {
                if ($bedingung == "") {
                    $bedingung = $bedingung . " bln_murid_id = $key";
                } else {
                    $bedingung = $bedingung . " OR bln_murid_id = $key";
                }
            }


        }
        $arrBulan = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12);
        $arrSTatus = array("<b>Unpaid</b>", "Paid");
        $t = time();
        ?>
        <section class="content-header">
            <h1>
                <div class="pull-right" style="font-size: 13px;">
                    Bulan :<select id="bulan_spp_<?= $t; ?>">
                        <?
                        foreach ($arrBulan as $bln2) {
                            $sel = "";
                            if ($bln2 == $bln) {
                                $sel = "selected";
                            }
                            ?>
                            <option value="<?= $bln2; ?>" <?= $sel; ?>><?= $bln2; ?></option>
                            <?
                        }
                        ?>
                    </select>
                    Tahun :<select id="tahun_spp_<?= $t; ?>">
                        <?
                        for ($x = date("Y") - 2; $x < date("Y") + 2; $x++) {
                            $sel = "";
                            if ($x == $thn) {
                                $sel = "selected";
                            }
                            ?>
                            <option value="<?= $x; ?>" <?= $sel; ?>><?= $x; ?></option>

                            <?
                        }
                        ?>
                        }
                        ?>
                    </select>

                    <button id="submit_bln_spp_<?= $t; ?>">submit</button>
                </div>
                Iuran Bulanan
            </h1>
            <script>
                $('#submit_bln_spp_<?= $t; ?>').click(function () {
                    var bln = $('#bulan_spp_<?= $t; ?>').val();
                    var thn = $('#tahun_spp_<?= $t; ?>').val();
                    var tc_id = '<?= $tc_id ?>';
                    openLw('listPembayaranSPPMonthlyTC', '<?=_SPPATH;?>TransaksiPembayaran/listPembayaranSPPMonthly' + '?now=' + $.now() + '&bln=' + bln + "&thn=" + thn + "&tc_id=" + tc_id, 'fade');
                });
            </script>
        </section>


        <section class="content">
            <div class="table-responsive" style="background-color: #FFFFFF; margin-top: 20px;">
                <table class="table table-striped ">
                    <thead>
                    <tr>
                        <th>
                            Nama Murid
                        </th>
                        <th>
                            Level
                        </th>
                        <th>
                            Tanggal
                        </th>
                        <th>
                            Kupon
                        </th>
                        <th>
                            Status
                        </th>
                    </tr>
                    </thead>

                    <tbody id="container_iuran_spp_<?= $t; ?>">
                    <?
                    $blndate = $bln . "-" . $thn;
                    $iuranBulanan = new IuranBulanan();
                    $arrIB = $iuranBulanan->getWhere("bln_tc_id=$tc_id AND bln_date='$blndate' AND ( $bedingung)");
                    $arrNameHlp = array();
                    $sdhbayar = 0;
                    foreach ($arrIB as $val) {
                        $murid = new MuridModel();
                        $murid->getByID($val->bln_murid_id);
                        $arrNameHlp[$murid->getNameMurid()] = $val;
//                        $arrNameHlp[$murid->getNameMurid()]->level = Generic::getLevelNameByID($murid->id_level_sekarang);
                        $arrMuridName[$val->bln_murid_id] = true;
                        $sdhbayar++;
                    }
                    $blmbayar = 0;
                    foreach ($arrMuridName as $id_murid => $val) {
                        if (!$val) {
                            $murid = new MuridModel();
                            $murid->getByID($id_murid);
                            $arrNameHlp[$murid->getNameMurid()] = null;
                            $arrNameHlp[$murid->getNameMurid()]['level'] = Generic::getLevelNameByID($murid->id_level_sekarang);
                            $arrNameHlp[$murid->getNameMurid()]['bln_murid_id'] = $id_murid;
                            $blmbayar++;
                        }
                    }


                    ksort($arrNameHlp);

                    foreach ($arrNameHlp as $nama => $iuran) {

                        if (is_object($iuran)) {
                            $id_murid = $iuran->bln_murid_id;
                        } else {
                            $id_murid = $arrNameHlp[$nama]['bln_murid_id'];
                        }
                        ?>
                        <tr>
                        <td>
                            <?= $nama; ?>
                        </td>
                        <td>
                            <?
                            if (is_object($iuran)) {
                                echo $arrLogDataMurid[$id_murid]['log_level'];
                            } else {
                                echo $arrNameHlp[$nama]['level'];
                            }
                            ?>
                        </td>
                        <td>
                            <?= $iuran->bln_date_pembayaran; ?>
                        </td>
                        <td>
                            <?= $iuran->bln_kupon_id; ?>
                        </td>
                        <td>

                            <?
                            if (is_object($iuran)) {
                                if ($iuran->bln_status == "") {
                                    $iuran->bln_status = 0;
                                }
                                echo $arrSTatus[$iuran->bln_status];
                            } else {
                                ?>
                                <button id='pay_now_<?= $id_murid . "_" . $t; ?>' class="btn btn-default">Pay Now
                                </button>
                                <?
                            }
                            ?>

                        </td>
                        <script>

                            $('#pay_now_<?=$id_murid . "_" . $t;?>').click(function () {
                                openLw('murid_Invoices_<?= $id_murid . "_" . $t; ?>', '<?= _SPPATH; ?>MuridWebHelper/murid_invoices?id=<?=$id_murid; ?>', 'fade');
                            })
                        </script>
                        </tr><?
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </section>

        <?
    }
}