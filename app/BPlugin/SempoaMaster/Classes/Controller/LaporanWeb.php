<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of LaporanWeb
 *
 * @author efindiongso
 */
class LaporanWeb extends WebService
{

    // AK

    public function get_laporan_pembayaran_iuran_bulanan_ak()
    {
        echo " LaporanWeb - get_laporan_pembayaran_iuran_bulanan_ak";
    }

    public function get_laporan_iuran_buku_ak()
    {
        echo " LaporanWeb - get_laporan_iuran_buku_ak";
    }

    function get_laporan_penjualan_abn_kpo()
    {
        $this->get_laporan_tc(KEY::$DEBET_ALAT_BANTU_MENGAJAR_KPO, "Biaya Alat Bantu Mengajar");
    }

    function get_laporan_penjualan_kupon_kpo()
    {
        $this->get_laporan_tc(KEY::$DEBET_ROYALTI_KPO, "Pendapatan Royalti");
    }

    function get_laporan_penjualan_buku_kpo()
    {
        $this->get_laporan_tc(KEY::$DEBET_BUKU_KPO, "Penjualan Buku");
    }

    function get_laporan_penjualan_barang_kpo()
    {

        $this->get_laporan_tc(KEY::$DEBET_BARANG_KPO, "Pembelian Barang");
    }

    function get_laporan_penjualan_perlengkapan_kpo()
    {
        $this->get_laporan_tc(KEY::$DEBET_PERLENGKAPAN_KPO, "Penjualan Perlengkapan");
    }

    public function get_laporan_pembelian_buku_ibo()
    {
        $this->get_laporan_tc(KEY::$KREDIT_BUKU_IBO, "Pembelian Buku");
    }

    public function get_laporan_pembayaran_iuran_bulanan_ibo()
    {
        $this->get_laporan_tc(KEY::$KREDIT_ROYALTI_IBO, "Iuran Bulanan");
    }

    public function get_laporan_penjualan_kupon_ibo()
    {
        $this->get_laporan_tc(KEY::$DEBET_ROYALTI_IBO, "Pendapatan Royalti");
    }

    public function get_laporan_penjualan_barang_dan_buku()
    {
        $this->get_laporan_tc(KEY::$DEBET_BARANG_IBO, "Barang dan Buku");
    }

    public function get_laporan_pembelian_abn_ibo()
    {
        $this->get_laporan_tc(KEY::$KREDIT_ALAT_BANTU_MENGAJAR_IBO, "Pembelian Alat Bantu Mengajar");
    }

    public function get_laporan_penjualan_abn_ibo()
    {
        $this->get_laporan_tc(KEY::$DEBET_ALAT_BANTU_MENGAJAR_IBO, "Penjualan Alat Bantu Mengajar");
    }

    public function get_laporan_pembelian_abn_tc()
    {
        $this->get_laporan_tc(KEY::$KREDIT_ALAT_BANTU_MENGAJAR_TC, "Pembelian Alat Bantu Mengajar");
    }

    //TC
    public function create_operasional_pembayaran_iuran_bulanan_tc()
    {

        $bln = isset($_GET['bln']) ? addslashes($_GET['bln']) : date("n");
        $thn = isset($_GET['thn']) ? addslashes($_GET['thn']) : date("Y");
        $tc_id = isset($_GET['tc_id']) ? addslashes($_GET['tc_id']) : AccessRight::getMyOrgID();


        // cek iuran bulanan dr bulan dan tahun yg dipilih sdh ada belum
        $iuranBulanan = new IuranBulanan();
        $arrIuranBulanan = $iuranBulanan->getWhere("bln_mon = '$bln' AND bln_tahun = '$thn' AND bln_tc_id='$tc_id' ORDER BY bln_murid_id ASC");

        $jumlahMuridAktiv = Generic::getJumlahMuridAktivByTC($tc_id);

        if ((count($arrIuranBulanan) == 0) or count($arrIuranBulanan) < $jumlahMuridAktiv) {

            $murid = new MuridModel();
            // Status cuti dikeluarkan
            $lastday = date('t', strtotime($bln . "/1/" . $thn));
            $dateTerakhir = new DateTime($thn . "-" . $bln . "-" . $lastday);
            $dateTerakhir_tmp = $dateTerakhir->format("Y-m-d");
//            $arrMurid = $murid->getWhere("(status = 1) AND murid_tc_id = '$tc_id' AND tanggal_masuk <='$dateTerakhir_tmp' ORDER BY nama_siswa ASC");
            $arrMurid = $murid->getWhere("murid_tc_id = '$tc_id' AND tanggal_masuk <='$dateTerakhir_tmp' ORDER BY nama_siswa ASC");

            $invoiceCreated = false;
        } else {

            $invoiceCreated = true;
        }

        $status = new MuridWeb2Model();
        $arrs = $status->getAll();

        $arrSTatus = array();
        foreach ($arrs as $st) {
            $arrSTatus[$st->id_status_murid] = $st->status;

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

                    <tbody id="container_iuran_<?= $t; ?>">
                    <?
                    $sudahbayar = 0;
                    $belumbayar = 0;

                    if ($invoiceCreated) {
//                        echo "invoiceCreated = " . $invoiceCreated . "<br>";
                    foreach ($arrIuranBulanan as $iuran) {

                    if ($iuran->bln_tc_id == $tc_id) {


                        $muridTmp = new MuridModel();
                        $muridTmp->getByID($iuran->bln_murid_id);
                        ?>
                        <tr id='payment_<?= $iuran->bln_id; ?>'
                            class="<? if ($iuran->bln_status) { ?>sudahbayar <?
                            } else { ?> belumbayar<?
                            } ?>">
                            <td><a style="cursor: pointer;"
                                   onclick="back_to_profile_murid('<?= $iuran->bln_murid_id; ?>');"><?= Generic::getMuridNamebyID($iuran->bln_murid_id); ?></a>
                            </td>
                            <td><?= Generic::getLevelNameByID($muridTmp->id_level_sekarang); ?></td>
                            <td><?
                                $kuponSatuan = new KuponSatuan();
                                $kuponSatuan->getWhereOne("kupon_id=$iuran->bln_kupon_id");
                                //                            echo $kuponSatuan->kupon_pemakaian_date;
                                if ($iuran->bln_status) {
                                    echo $iuran->bln_date_pembayaran;
                                }

                                ?></td>

                            <td class='kupon'>
                                <?
                                if ($iuran->bln_status) {
                                    echo $iuran->bln_kupon_id;
                                    $sudahbayar++;
                                } else {
                                    $belumbayar++;
                                    ?>
                                    <button id='pay_now_<?= $iuran->bln_id; ?>' class="btn btn-default">Pay Now
                                    </button>
                                    <script>
                                        $('#pay_now_<?= $iuran->bln_id; ?>').click(function () {
                                            openLw('murid_Invoices_<?= $iuran->bln_murid_id; ?>', '<?= _SPPATH; ?>MuridWebHelper/murid_invoices?id=<?= $iuran->bln_murid_id; ?>', 'fade');
                                        })
                                    </script>
                                    <?
                                }
                                ?>
                            </td>
                            <td><?= $arrSTatus[$iuran->bln_status]; ?></td>
                        </tr>

                    <?
                    }
                    }
                    } else {
//                    echo "invoiceCreated = " . $invoiceCreated . "<br>";
//                    foreach ($arrMurid as $mk){
//                        echo $mk->nama_siswa . "<br>";
//                    }
//                    die();
                    foreach ($arrMurid as $mk) {

                    $iuranBulanan = new IuranBulanan();
                    //                    $iuranBulanan->getWhereOne("bln_murid_id = '$mk->id_murid' AND bln_mon = '$bln' AND bln_tahun = '$thn' AND bln_tc_id='$tc_id'");
                    $iuranBulanan->getWhereOne("bln_murid_id = '$mk->id_murid' AND bln_mon = '$bln' AND bln_tahun = '$thn'");

                    //                    if (!is_null($iuranBulanan->bln_id)) {

                    if ((!is_null($iuranBulanan->bln_id) && ($iuranBulanan->bln_tc_id == $tc_id) || ((is_null($iuranBulanan->bln_id)&&($mk->status==1))))) {
                    ?>
                        <tr id='payment_<?= $iuranBulanan->bln_id; ?>'
                            class="<? if ($iuranBulanan->bln_status) { ?>sudahbayar <?
                            } else { ?> belumbayar<?
                            } ?>">
                            <td><a style="cursor: pointer;"
                                   onclick="back_to_profile_murid('<?= $mk->id_murid; ?>');"><?= $mk->nama_siswa; ?></a>
                            </td>
                            <td><?= Generic::getLevelNameByID($mk->id_level_sekarang); ?></td>
                            <td><?
                                $kuponSatuan = new KuponSatuan();
                                $kuponSatuan->getWhereOne("kupon_id=$iuranBulanan->bln_kupon_id");
                                //                            echo $kuponSatuan->kupon_pemakaian_date;
                                if ($iuranBulanan->bln_status) {
                                    echo $iuranBulanan->bln_date_pembayaran;
                                }

                                ?></td>

                            <td class='kupon'>
                                <?
                                if ($iuranBulanan->bln_status) {
                                    echo $iuranBulanan->bln_kupon_id;
                                    $sudahbayar++;
                                } else {
//                                echo $iuranBulanan->bln_id . " saas";
                                    $belumbayar++;
                                    ?>
                                    <button id='pay_now_<?= $mk->id_murid; ?>' class="btn btn-default">Pay Now
                                    </button>
                                    <?
                                }
                                ?>
                            </td>
                            <td><?= $arrSTatus[$iuranBulanan->bln_status]; ?></td>
                        </tr>
                        <script>

                            $('#pay_now_<?= $mk->id_murid; ?>').click(function () {
                                openLw('murid_Invoices_<?= $mk->id_murid; ?>', '<?= _SPPATH; ?>MuridWebHelper/murid_invoices?id=<?= $mk->id_murid; ?>', 'fade');
                            })
                        </script>
                        <?

                    }

                    }

                    }


                    ?>
                    </tbody>
                </table>
            </div>
            <div id="summary_bayar" style="display: none;">
                <span style="cursor: pointer;"
                      onclick="$('.sudahbayar').show();$('.belumbayar').hide();">Sudah Bayar</span> :
                <b><? echo $sudahbayar; ?></b>
                <br>
                <span style="cursor: pointer;"
                      onclick="$('.sudahbayar').hide();$('.belumbayar').show();">Belum Bayar</span> : <b
                    style="color: red;"><?= $belumbayar; ?></b>
            </div>
            <script>
                $(document).ready(function () {
                    $('#summary_holder').html($('#summary_bayar').html());
                });
            </script>
        </section>

        <?
        die();
    }

    public function create_operasional_pembayaran_iuran_bulanan_tc_1()
    {
        $myorg = AccessRight::getMyOrgID();
        $bln = isset($_GET['bln']) ? addslashes($_GET['bln']) : date("n");
        $thn = isset($_GET['thn']) ? addslashes($_GET['thn']) : date("Y");
        $tc_id = isset($_GET['tc_id']) ? addslashes($_GET['tc_id']) : AccessRight::getMyOrgID();
        $murid = new MuridModel();
        // Status cuti dikeluarkan
        $bln_sekarang = "" . $bln . $thn;
//        $tanggal_masuk
        $arrMurid = $murid->getWhere("(status = 1) AND murid_tc_id = '$tc_id' ORDER BY nama_siswa ASC");
        $status = new MuridWeb2Model();
        $arrs = $status->getAll();

        $arrSTatus = array();
        foreach ($arrs as $st) {
            $arrSTatus[$st->id_status_murid] = $st->status;

        }
//        echo "2<br>";
        $arrBulan = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12);

        // Kupon
        $kupon = new KuponSatuan();
        $arrkupon = $kupon->getWhere("kupon_owner_id = '$tc_id' AND kupon_status = 0 ORDER BY kupon_id ASC");
        $checkKupon = count($arrkupon);
//        $checkKupon = 0;
        $arrSTatus = array("<b>Unpaid</b>", "Paid");
        $t = time();;
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

                    <tbody id="container_iuran_<?= $t; ?>">
                    <?
                    $sudahbayar = 0;
                    $belumbayar = 0;
                    foreach ($arrMurid as $mk) {

                        $iuranBulanan = new IuranBulanan();
                        $iuranBulanan->getWhereOne("bln_murid_id = '$mk->id_murid' AND bln_mon = '$bln' AND bln_tahun = '$thn' AND bln_tc_id='$tc_id'");
//                        pr($iuranBulanan);
                        if ($mk->id_murid == '3202') {
                            pr($mk->id_murid . " - " . $bln);
                        }
                        ?>

                        <tr id='payment_<?= $iuranBulanan->bln_id; ?>'
                            class="<? if ($iuranBulanan->bln_status) { ?>sudahbayar <?
                            } else { ?> belumbayar<?
                            } ?>">
                            <td><a style="cursor: pointer;"
                                   onclick="back_to_profile_murid('<?= $mk->id_murid; ?>');"><?= $mk->nama_siswa; ?></a>
                            </td>
                            <td><?= Generic::getLevelNameByID($mk->id_level_sekarang); ?></td>
                            <td><?
                                $kuponSatuan = new KuponSatuan();
                                $kuponSatuan->getWhereOne("kupon_id=$iuranBulanan->bln_kupon_id");
                                //                            echo $kuponSatuan->kupon_pemakaian_date;
                                if ($iuranBulanan->bln_status) {
                                    echo $iuranBulanan->bln_date_pembayaran;
                                }

                                ?></td>

                            <td class='kupon'>
                                <?
                                if ($iuranBulanan->bln_status) {
                                    echo $iuranBulanan->bln_kupon_id;
                                    $sudahbayar++;
                                } else {
//                                echo $iuranBulanan->bln_id . " saas";
                                    $belumbayar++;
                                    ?>
                                    <button id='pay_now_<?= $mk->id_murid; ?>' class="btn btn-default">Pay Now
                                    </button>
                                    <?
                                }
                                ?>
                            </td>
                            <td><?= $arrSTatus[$iuranBulanan->bln_status]; ?></td>
                        </tr>
                        <script>
                            $('#pay_now_<?= $mk->id_murid; ?>').click(function () {
                                openLw('murid_Invoices_<?= $mk->id_murid; ?>', '<?= _SPPATH; ?>MuridWebHelper/murid_invoices?id=<?= $mk->id_murid; ?>', 'fade');
                            })
                        </script>
                        <?
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <div id="summary_bayar" style="display: none;">
                <span style="cursor: pointer;"
                      onclick="$('.sudahbayar').show();$('.belumbayar').hide();">Sudah Bayar</span> :
                <b><? echo $sudahbayar; ?></b>
                <br>
                <span style="cursor: pointer;"
                      onclick="$('.sudahbayar').hide();$('.belumbayar').show();">Belum Bayar</span> : <b
                    style="color: red;"><?= $belumbayar; ?></b>
            </div>
            <script>
                $(document).ready(function () {
                    $('#summary_holder').html($('#summary_bayar').html());
                });
            </script>
        </section>

        <?
        die();
    }

    public function create_operasional_pembayaran_iuran_bulanan_tc_jalan()
    {
        $myorg = AccessRight::getMyOrgID();
        $bln = isset($_GET['bln']) ? addslashes($_GET['bln']) : date("n");
        $thn = isset($_GET['thn']) ? addslashes($_GET['thn']) : date("Y");
        $tc_id = isset($_GET['tc_id']) ? addslashes($_GET['tc_id']) : AccessRight::getMyOrgID();
        $murid = new MuridModel();
        // Status cuti dikeluarkan
        $arrMurid = $murid->getWhere("(status = 1) AND murid_tc_id = '$tc_id' ORDER BY nama_siswa ASC");
        $status = new MuridWeb2Model();
        $arrs = $status->getAll();

        $arrSTatus = array();
        foreach ($arrs as $st) {
            $arrSTatus[$st->id_status_murid] = $st->status;

        }
//        echo "2<br>";
        $arrBulan = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12);

        // Kupon
        $kupon = new KuponSatuan();
        $arrkupon = $kupon->getWhere("kupon_owner_id = '$tc_id' AND kupon_status = 0 ORDER BY kupon_id ASC");
        $checkKupon = count($arrkupon);
//        $checkKupon = 0;
        $arrSTatus = array("<b>Unpaid</b>", "Paid");
        $t = time();;
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

                    <tbody id="container_iuran_<?= $t; ?>">
                    <?
                    $sudahbayar = 0;
                    $belumbayar = 0;
                    foreach ($arrMurid as $mk) {

                        $iuranBulanan = new IuranBulanan();
                        $iuranBulanan->getWhereOne("bln_murid_id = '$mk->id_murid' AND bln_mon = '$bln' AND bln_tahun = '$thn' AND bln_tc_id='$tc_id'");
//                        pr($iuranBulanan);
                        if ($mk->id_murid == '3202') {
                            pr($mk->id_murid . " - " . $bln);
                        }
                        ?>

                        <tr id='payment_<?= $iuranBulanan->bln_id; ?>'
                            class="<? if ($iuranBulanan->bln_status) { ?>sudahbayar <?
                            } else { ?> belumbayar<?
                            } ?>">
                            <td><a style="cursor: pointer;"
                                   onclick="back_to_profile_murid('<?= $mk->id_murid; ?>');"><?= $mk->nama_siswa; ?></a>
                            </td>
                            <td><?= Generic::getLevelNameByID($mk->id_level_sekarang); ?></td>
                            <td><?
                                $kuponSatuan = new KuponSatuan();
                                $kuponSatuan->getWhereOne("kupon_id=$iuranBulanan->bln_kupon_id");
                                //                            echo $kuponSatuan->kupon_pemakaian_date;
                                if ($iuranBulanan->bln_status) {
                                    echo $iuranBulanan->bln_date_pembayaran;
                                }

                                ?></td>

                            <td class='kupon'>
                                <?
                                if ($iuranBulanan->bln_status) {
                                    echo $iuranBulanan->bln_kupon_id;
                                    $sudahbayar++;
                                } else {
//                                echo $iuranBulanan->bln_id . " saas";
                                    $belumbayar++;
                                    ?>
                                    <button id='pay_now_<?= $mk->id_murid; ?>' class="btn btn-default">Pay Now
                                    </button>
                                    <?
                                }
                                ?>
                            </td>
                            <td><?= $arrSTatus[$iuranBulanan->bln_status]; ?></td>
                        </tr>
                        <script>
                            $('#pay_now_<?= $mk->id_murid; ?>').click(function () {
                                openLw('murid_Invoices_<?= $mk->id_murid; ?>', '<?= _SPPATH; ?>MuridWebHelper/murid_invoices?id=<?= $mk->id_murid; ?>', 'fade');
                            })
                        </script>
                        <?
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <div id="summary_bayar" style="display: none;">
                <span style="cursor: pointer;"
                      onclick="$('.sudahbayar').show();$('.belumbayar').hide();">Sudah Bayar</span> :
                <b><? echo $sudahbayar; ?></b>
                <br>
                <span style="cursor: pointer;"
                      onclick="$('.sudahbayar').hide();$('.belumbayar').show();">Belum Bayar</span> : <b
                    style="color: red;"><?= $belumbayar; ?></b>
            </div>
            <script>
                $(document).ready(function () {
                    $('#summary_holder').html($('#summary_bayar').html());
                });
            </script>
        </section>

        <?
        die();
    }

    public function create_operasional_pembayaran_iuran_bulanan_tc_tmp()
    {

        $myorg = AccessRight::getMyOrgID();

        $murid = new MuridModel();
        $arrMurid = $murid->getWhere("(status = 1  OR status = 2) AND murid_tc_id = '$myorg' ORDER BY nama_siswa ASC");

        $bln = isset($_GET['bln']) ? addslashes($_GET['bln']) : date("n");
        $thn = isset($_GET['thn']) ? addslashes($_GET['thn']) : date("Y");

        $status = new MuridWeb2Model();
        $arrs = $status->getAll();

        $arrSTatus = array();
        foreach ($arrs as $st) {
            $arrSTatus[$st->id_status_murid] = $st->status;
        }
        $arrBulan = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12);

        // Kupon
        $kupon = new KuponSatuan();
        $arrkupon = $kupon->getWhere("kupon_owner_id = '$myorg' AND kupon_status = 0 ORDER BY kupon_id ASC");
        $checkKupon = count($arrkupon);
//        $checkKupon = 0;
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
                            if ($bln2 == date("n")) {
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
                            if ($x == date("Y")) {
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
                    var tc_id = '<?= $myorg ?>';
                    $('#container_iuran_<?= $t; ?>').load("<?= _SPPATH; ?>LaporanWebHelper/loadIuranBulanan?bln=" + bln + "&thn=" + thn + "&tc_id=" + tc_id, function () {
                    }, 'json');
                });
            </script>
        </section>

        <section class="content">
            <table class="table table-striped table-responsive">
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

                    <th>
                        Action
                    </th>
                </tr>
                </thead>

                <tbody id="container_iuran_<?= $t; ?>">
                <?
                $objMurid = new MuridModel();

                foreach ($arrMurid as $mk) {

                    $iuranBulanan = new IuranBulanan();
                    $iuranBulanan->getWhereOne("bln_murid_id = '{$mk->id_murid}' AND bln_mon = '$bln' AND bln_tahun = '$thn' AND bln_tc_id='$myorg'");
                    pr($iuranBulanan);
                    ?>

                    <tr id='payment_<?= $iuranBulanan->bln_id; ?>'>
                        <td><a style="cursor: pointer;"
                               onclick="back_to_profile_murid('<?= $mk->id_murid; ?>');"><?= $mk->nama_siswa; ?></a>
                        </td>
                        <td><?= Generic::getLevelNameByID($mk->id_level_sekarang); ?></td>
                        <td><?= $iuranBulanan->bln_status; ?></td>

                        <td class='kupon'>
                            <?
                            if ($iuranBulanan->bln_status) {
                                echo $iuranBulanan->bln_kupon_id;
                            } else {
                                ?>

                                <?
                            }
                            ?>


                        </td>
                        <td><?= $arrSTatus[$iuranBulanan->bln_status]; ?></td>
                        <td id='status_payment_<?= $iuranBulanan->bln_id; ?>'>
                            <?
                            if ($iuranBulanan->bln_status) {
                                ?>
                                <button id='pay_detail_<?= $iuranBulanan->bln_id; ?>' class="btn btn-default">Payment
                                    Detail
                                </button>
                                <?
                            } else {
                                ?>
                                <button id='pay_now_<?= $iuranBulanan->bln_id; ?>' class="btn btn-default">Pay Now
                                </button>
                                <?
                            }
                            ?>
                        </td>
                    </tr>

                    <script>


                        $('#pay_now_<?= $iuranBulanan->bln_id; ?>').click(function () {
                            if (<?= $checkKupon; ?> ===
                            0
                            )
                            {
                                alert("Kupon telah habis!");
                                return false;
                            }
                            var pilihKupon = $('#pilih_kupon_<?= $t; ?>').val();
                            $('#status_payment_<?= $iuranBulanan->bln_id; ?>').load("<?= _SPPATH; ?>LaporanWebHelper/update_iuran_bulanan?bln_id=<?= $iuranBulanan->bln_id; ?>" + "&kupon_id=" + pilihKupon, function (data) {
                                var b = "<td><a style=\"cursor: pointer;\" onclick=\"back_to_profile_murid(" + <?= $iuranBulanan->bln_murid_id; ?> +");\">" + '<?= $iuranBulanan->nama_siswa; ?>' +
                                    "</a> </td>" +
                                    "<td><?= $arrSTatus[$iuranBulanan->status]; ?></td>" +
                                    "<td>" + pilihKupon + "</td>" +
                                    "<td id='status_payment_<?= $iuranBulanan->bln_id; ?>'>" +
                                    " <button id = 'pay_detail_<?= $iuranBulanan->bln_id; ?>' class=\"btn btn-default\">Payment Detail</button>" +
                                    "  </td>";
                                $('#payment_<?= $iuranBulanan->bln_id; ?>').html(" ");
                                $('#payment_<?= $iuranBulanan->bln_id; ?>').append(b);
                                console.log(b);
                                lwrefresh(selected_page);
                            }, 'json');
                        })


                        $('#pay_detail_<?= $iuranBulanan->bln_id; ?>').click(function () {
                            openLw("paymentDetails", '<?= _SPPATH; ?>LaporanWebHelper/paymentDetails?bln_id=<?= $iuranBulanan->bln_id; ?>', 'fade');
                        })
                    </script>
                    <?
                }
                ?>
                </tbody>
            </table>
        </section>

        <?
    }

    function create_operasional_pembayaran_iuran_buku_tc_tmp()
    {
        $myorg = AccessRight::getMyOrgID();
        $objMurid = new MuridModel();
        $iuranBuku = new IuranBuku();
        global $db;
        $q = "SELECT siswa.nama_siswa, iuran.* FROM {$iuranBuku->table_name} iuran INNER JOIN {$objMurid->table_name} siswa ON iuran.bln_murid_id = siswa.id_murid WHERE iuran.bln_tc_id='$myorg'";
        $arrIuranBuku = $db->query($q, 2);
//        pr($arrIuranBuku);
        $arrSTatus = array("<b>Unpaid</b>", "Paid");
        $t = time();
        $pb = new PembayaranWeb2Model();

        $jenispby = $pb->getAll();
        $arrBulan = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12);
        ?>
        <section class="content-header">
            <h1>
                <div class="pull-right" style="font-size: 13px;">
                    Bulan :<select id="bulan_<?= $t; ?>">
                        <?
                        foreach ($arrBulan as $bln2) {
                            $sel = "";
                            if ($bln2 == date("n")) {
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
                            if ($x == date("Y")) {
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
                Iuran Buku
            </h1>
            <script>
                $('#submit_bln_<?= $t; ?>').click(function () {
                    var bln = $('#bulan_<?= $t; ?>').val();
                    var thn = $('#tahun_<?= $t; ?>').val();
                    var tc_id = '<?= $myorg ?>';
                    $('#container_iuran_<?= $t; ?>').load("<?= _SPPATH; ?>LaporanWebHelper/loadIuranBulanan?bln=" + bln + "&thn=" + thn + "&tc_id=" + tc_id, function () {
                    }, 'json');
                });
            </script>
        </section>

        <section class="content">sss
            <div class="table-responsive" style="background-color: #FFFFFF;">
                <table class="table table-striped ">
                    <thead>
                    <tr>
                        <th>Nama Murid</th>
                        <th>Level</th>
                        <th>Status</th>
                        <th>Cara Pembayaran</th>
                        <th>Action</th>
                        <th>Keterangan</th>
                    </tr>
                    </thead>
                    <tbody id='container_iuran_<?= $t; ?>'>
                    <?
                    foreach ($arrIuranBuku as $key => $val) {
                        ?>
                        <tr>

                            <td id='<?= $val->bln_murid_id ?>'><a style="cursor: pointer;"
                                                                  onclick="back_to_profile_murid('<?= $val->bln_murid_id; ?>');"><?= $val->nama_siswa; ?></a>
                            </td>
                            <td id='<?= $val->bln_murid_id ?>'><?= Generic::getLevelNameByID($val->bln_buku_level) ?></td>
                            <td id='Status<?= $val->bln_murid_id ?>'><?= $arrSTatus[$val->bln_status]; ?></td>
                            <td id='<?= $val->bln_murid_id ?>'>
                                <select id="jenis_pmbr_<?= $val->bln_murid_id ?>">
                                    <?
                                    foreach ($jenispby as $by) {
                                        ?>
                                        <option
                                            value="<?= $by->id_jenis_pembayaran; ?>"><?= $by->jenis_pembayaran; ?></option>
                                        <?
                                    }
                                    ?>
                                </select>
                            </td>

                            <td id='status_payment_<?= $val->bln_id; ?>'>
                                <?
                                if ($val->bln_status) {
                                    ?>
                                    <button id='pay_detail_<?= $val->bln_murid_id; ?>' class="btn btn-default">Payment
                                        Detail
                                    </button>
                                    <?
                                } else {
                                    ?>
                                    <button id='pay_now_<?= $val->bln_murid_id; ?>' class="btn btn-default">Pay Now
                                    </button>
                                    <?
                                }
                                ?>

                            </td>
                            <td></td>
                        </tr>
                        <script>
                            $('#pay_now_<?= $val->bln_murid_id; ?>').click(function () {
                                var jpb = $('#jenis_pmbr_<?= $val->bln_murid_id ?>').val();
                                var bln_id = <?= $val->bln_id; ?>;
                                $('#status_payment_<?= $val->bln_id; ?>').load('<?= _SPPATH ?>LaporanWebHelper/pay_iuran_buku?bln_id=' + bln_id + "&cara_pby=" + jpb, function (data) {

                                    //                                var b = "<td id='status_payment_<?= $iuranBulanan->bln_id; ?>'>" +

                                    var b = "<button id = 'pay_detail_<?= $val->bln_murid_id; ?>' class=\"btn btn-default\">Payment Detail</button>";
                                    //                                        "</td>";
                                    $('#status_payment_<?= $val->bln_id; ?>').html("");
                                    $('#status_payment_<?= $val->bln_id; ?>').append(b);
                                    lwrefresh(selected_page);
                                    //                                if (data.status_code) {
                                    //                                    alert(data.status_message);
                                    //                                } else {
                                    //                                    alert(data.status_message);
                                    //                                }
                                }, 'json');
                            });
                            $('#pay_detail_<?= $val->bln_murid_id; ?>').click(function () {


                                openLw("paymentDetailsBuku", '<?= _SPPATH; ?>LaporanWebHelper/paymentDetailsBuku?bln_id=<?= $val->bln_id; ?>', 'fade');
                            })
                        </script>
                        <?
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </section>
        <?
    }

    function create_operasional_pembayaran_iuran_buku_tc()
    {
        $bln = isset($_GET['bln']) ? addslashes($_GET['bln']) : date("n");
        $thn = isset($_GET['thn']) ? addslashes($_GET['thn']) : date("Y");
        $tc_id = isset($_GET['tc_id']) ? addslashes($_GET['tc_id']) : AccessRight::getMyOrgID();

        $objMurid = new MuridModel();
        $iuranBuku = new IuranBuku();
        global $db;
        $q = "SELECT siswa.nama_siswa, iuran.* FROM {$iuranBuku->table_name} iuran INNER JOIN {$objMurid->table_name} siswa ON iuran.bln_murid_id = siswa.id_murid WHERE iuran.bln_tc_id='$tc_id' AND iuran.bln_mon='$bln' AND iuran.bln_tahun='$thn' ";
        $arrIuranBuku = $db->query($q, 2);
        $arrSTatus = Generic::getStatus();
        $arrPembayaran = Generic::getJenisPembayaran();

        $arrBulan = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12);
        $t = time();
        ?>
        <section class="content-header">
            <h1>
                <div class="pull-right" style="font-size: 13px;">
                    Bulan :<select id="bulan_<?= $t; ?>">
                        <?
                        foreach ($arrBulan as $bln2) {
                            $sel = "";
                            if ($bln2 == date("n")) {
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
                            if ($x == date("Y")) {
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
                Iuran Buku
            </h1>
            <script>
                $('#submit_bln_<?= $t; ?>').click(function () {
                    var bln = $('#bulan_<?= $t; ?>').val();
                    var thn = $('#tahun_<?= $t; ?>').val();
                    var tc_id = '<?= $tc_id ?>';
                    $('#container_load_history_iuranBuku_<?= $t; ?>').load("<?= _SPPATH; ?>LaporanWebHelper/pay_iuran_buku?bln=" + bln + "&thn=" + thn + "&tc_id=" + tc_id, function () {
                    }, 'json');
                });
            </script>
        </section>

        <section class="content">
            <div class="table-responsive" style="background-color: #FFFFFF;">
                <table class="table table-striped ">
                    <thead>
                    <tr>
                        <th>Nama Murid</th>
                        <th>Level</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Cara Pembayaran</th>
                    </tr>
                    </thead>
                    <tbody id='container_load_history_iuranBuku_<?= $t; ?>'>
                    <?
                    foreach ($arrIuranBuku as $key => $val) {
                        ?>
                        <tr>
                            <td>
                                <a style="cursor: pointer;"
                                   onclick="back_to_profile_murid('<?= $val->bln_murid_id; ?>');"><?= $val->nama_siswa; ?></a>
                            </td>

                            <td><?= Generic::getLevelNameByID($val->bln_buku_level); ?></td>
                            <td><?= $val->bln_date_pembayaran; ?></td>

                            <td><?= $arrSTatus[$val->bln_status]; ?></td>
                            <td><?
                                if ($val->bln_status)
                                    echo $arrPembayaran[$val->bln_cara_bayar];
                                else {

                                }
                                ?></td>

                        </tr>

                        <?
                    }
                    ?>
                    </tbody>

                </table>
            </div>
        </section>
        <?
    }

    function get_laporan_pembayaran_iuran_bulanan_tc()
    {

        $this->get_laporan_tc(KEY::$DEBET_IURAN_BULANAN_TC, "Iuran Bulanan");
    }

    function get_laporan_pembelian_kupon_ibo()
    {
        $this->get_laporan_tc(KEY::$KREDIT_ROYALTI_IBO, "Pembelian Kupon IBO ke KPO");
    }

    // Buku
    function get_laporan_pembelian_barang_dan_buku_ibo()
    {
        $this->get_laporan_tc(KEY::$KREDIT_BUKU_IBO, "Pembelian Buku IBO ke KPO");
    }

    function get_laporan_registrasi_guru_tc()
    {
        $this->get_laporan_tc(KEY::$KREDIT_REGISTRASI_GURU_TC, "Pembayaran Registrasi Guru");
    }

    function get_laporan_registrasi_guru_ibo()
    {
        $this->get_laporan_tc(KEY::$DEBET_REGISTRASI_GURU_TC, "Pendapatan Registrasi Guru");
    }

    function get_laporan_penjualan_buku_ibo()
    {
        $help = new LaporanWeb();
        $help->get_laporan_tc(KEY::$DEBET_BUKU_IBO, "Penjualan Buku IBO ke TC");
    }

    function get_laporan_iuran_buku_tc()
    {
        $this->get_laporan_tc(KEY::$DEBET_IURAN_BUKU_TC, "Iuran Buku");
    }

    function get_laporan_pembelian_kupon_tc()
    {
        $this->get_laporan_tc(KEY::$KREDIT_ROYALTI_TC, "Pembayaran Royalti");
    }

    public function get_laporan_pembelian_buku_tc()
    {
        $this->get_laporan_tc(KEY::$KREDIT_BUKU_TC, "Pembelian Buku");
    }

    static function get_laporan_tc($type, $jenis_pembayaran)
    {
        $bln = isset($_GET['bln']) ? addslashes($_GET['bln']) : date("n");
        $thn = isset($_GET['thn']) ? addslashes($_GET['thn']) : date("Y");
        $myOrgID = AccessRight::getMyOrgID();
        $transaksi = new TransaksiModel();
        $arrTransaksi = $transaksi->getWhere("entry_org_id = '$myOrgID'  AND entry_akun_id = " . $type . "  AND (MONTH(entry_date)=$bln) AND (YEAR(entry_date)=$thn) ORDER BY entry_date DESC");
//        pr($arrTransaksi);
        $t = time();
        $arrBulan = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12);
        ?>

        <section class="content-header">
            <h1>
                <div class="pull-right" style="font-size: 13px;">
                    Bulan :<select id="bulan_<?= $t; ?>">
                        <?
                        foreach ($arrBulan as $bln2) {
                            $sel = "";
                            if ($bln2 == date("n")) {
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
                            if ($x == date("Y")) {
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
                Laporan <?= $jenis_pembayaran; ?>
            </h1>
            <script>
                $('#submit_bln_<?= $t; ?>').click(function () {
                    var bln = $('#bulan_<?= $t; ?>').val();
                    var thn = $('#tahun_<?= $t; ?>').val();
                    var tc_id = '<?= $myOrgID ?>';
                    $('#container_laporan_<?= $t; ?>').load("<?= _SPPATH; ?>LaporanWebHelper/loadLaporantc?bln=" + bln + "&thn=" + thn + "&tc_id=" + <?= $myOrgID; ?> +"&type=" +<?= $type; ?>, function () {
                    }, 'json');
                });
            </script>
        </section>


        <section class="content">
            <div class="table-responsive">
                <table class="table table-striped ">
                    <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Keterangan</th>
                        <th>Total</th>
                    </tr>
                    </thead>
                    <tbody id="container_laporan_<?= $t; ?>">
                    <?
                    $debet = 0;
                    $kredit = 0;
                    foreach ($arrTransaksi as $tr) {
                        ?>
                        <tr>
                            <td><?= $tr->entry_date; ?></td>
                            <td><?= $tr->entry_keterangan; ?></td>
                            <td class="angka"><?
                                if ($tr->entry_debit == "") {
                                    $debet += $tr->entry_kredit;
                                    echo idr($tr->entry_kredit);
                                } else {
                                    $debet += $tr->entry_debit;
                                    echo idr($tr->entry_debit);
                                }
                                ?></td>


                        </tr>

                        <?
                    }
                    ?>
                    <tr style="font-weight: bold;">
                        <td>Total</td>
                        <td></td>

                        <td class="angka"><?= idr($debet); ?></td>

                    </tr>
                    </tbody>
                    <tfoot>

                    </tfoot>
                </table>
            </div>
        </section>
        <style>
            .angka {
                text-align: right;
            }
        </style>
        <?
    }

}
