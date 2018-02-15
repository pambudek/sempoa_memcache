<?php

/**
 * Created by PhpStorm.
 * User: elroy
 * Date: 9/11/16
 * Time: 3:29 PM
 */
class MuridWebHelper extends WebService
{

    function firsttime_payment()
    {
        $id = addslashes($_GET['id_murid']);
//        pr($id);

        $murid = new MuridModel();
        $murid->getByID($id);

        $org = AccessRight::getMyOrgID();
        $myParentID = Generic::getMyParentID($org);
        $myGrandParentID = Generic::getMyParentID($myParentID);
        $lanjut = true;
        $kupon = new KuponSatuan();
        $arrkupon = $kupon->getWhere("kupon_owner_id = '$org' AND kupon_status = 0 ORDER BY kupon_id ASC");
        if (count($arrkupon) == 0) {
            $lanjut = false;
        }

        $tc = new SempoaOrg();
        $tc->getByID(AccessRight::getMyOrgID());
        $id_buku = "";
        $id_perlengkapan = "";
        $t = time();
        $arrjenisBiayaSPP = Generic::getJenisBiayaType();
        $jenisBiayaSPP = $arrjenisBiayaSPP[$murid->id_level_sekarang];
        if ($murid->id_level_sekarang == 1) {

            $arrIDs = Generic::firstPaymentJunior();
        } elseif ($murid->id_level_sekarang == 2) {

            $arrIDs = Generic::firstPaymentJunior2();
        } elseif (($murid->id_level_sekarang >= 3)) {
            $arrIDs = Generic::firstPaymentFoundation();
        }
        ?>

        <div style="background-color: #FFFFFF; padding: 20px; margin-top: 20px;" xmlns="http://www.w3.org/1999/html"
             xmlns="http://www.w3.org/1999/html">


            <section class="content-header">

                <h1 style="text-align: center; margin-bottom: 20px;">Pembayaran Registrasi Murid</h1>
            </section>
            <style>
                .data-siswa {
                    margin-top: 20px;
                    margin-bottom: 20px;
                    font-size: 18px;
                }

                .pilih-kupon {
                    margin-top: 10px;
                    font-style: italic;
                }
            </style>

            <div class="data-siswa">
                <div class="data-siswa-item">Nama : <b><?= $murid->nama_siswa; ?></b></div>
                <div class="data-siswa-item">Kode Siswa : <b><?= $murid->kode_siswa; ?></b></div>

            </div>
            <? //pr($murid);
            ?>

            <div style="background-color: #EEEEEE; font-size: 18px; text-align: center; padding: 5px;">Daftar
                Pembayaran
            </div>
            <table width="100%" class="table table-hover" style="font-size: 16px;">
                <?
                $total = 0;
                //            pr($arrIDs);

                foreach ($arrIDs as $num => $id_biaya) {
                    $biaya = new SettingJenisBiayaModel();
                    $biaya->getByID($id_biaya);
                    $jenisbm = new JenisBiayaModel();
                    $jenisbm->getByID(AccessRight::getMyOrgID() . "_" . $id_biaya);
                    $total += $jenisbm->harga;
                    ?>
                    <tr>
                        <td>
                            <?= $num + 1; ?>. <?= $biaya->jenis_biaya;
                            ?>
                            <?
                            if ($id_biaya == 4) {
                                //TODO check stok buku
//                            echo "<b>Check stok buku ya..</b>";
//                                pr($murid->id_level_masuk);
                                $arrBukuYgDiperlukan = Generic::getIdBarangByLevelDanJenisBiaya($murid->id_level_masuk, 0, KEY::$JENIS_BUKU);
//                                pr($arrBukuYgDiperlukan);
                                $myBuku = new BarangWebModel();
                                $arrMyBuku = $myBuku->getWhere("level=$murid->id_level_sekarang  AND jenis_biaya = 1 AND kpo_id = $myGrandParentID LIMIT 0,1");
                                $stockBarang = new StockModel();
                                $buku_active = array_pop($arrMyBuku);
                                $stockBarang->getWhereOne("id_barang='$buku_active->id_barang_harga' AND org_id='$org'");


                                foreach ($arrBukuYgDiperlukan as $val) {
                                    $stockBarang->getWhereOne("id_barang='$val' AND org_id='$org'");
//                                    pr($stockBarang);
                                    if ($stockBarang->jumlah_stock <= 0) {
                                        $lanjut = $lanjut & false;
                                        echo "<b> Stock Habis!</b>";
                                    } else {
                                        $lanjut = $lanjut & true;


                                    }
                                }

                                if ($lanjut) {
                                    echo Generic::getLevelNameByID($murid->id_level_masuk);
                                    $id_buku = implode(",", $arrBukuYgDiperlukan);
                                }


                            }
                            ?>
                            <?
                            // Perlengkapan
                            if ($id_biaya == 7) {

                                $myBuku = new BarangWebModel();
                                $id_barang = $myBuku->getPerlengkapanJunior($myGrandParentID);
                                $myBuku->getWhereOne("level='1'  AND jenis_biaya = 2 AND kpo_id = $myGrandParentID LIMIT 0,1");
                                $id_perlengkapan = $myBuku->id_barang_harga;
                                $stockBarang = new StockModel();
                                $stockBarang->getWhereOne("org_id=$org  AND id_barang=$id_barang");
                                $jmlhStock = $stockBarang->jumlah_stock;

                                if ($jmlhStock > 0) {
                                    $id_perlengkapan = $myBuku->id_barang_harga;
                                    $lanjut = $lanjut & true;
                                } else {
                                    $lanjut = $lanjut & false;
                                    echo "<b> Stock Habis!</b>";
                                }

                            }
                            ?>
                            <?
                            if ($id_biaya == 8) {

                                $myBuku = new BarangWebModel();
                                $id_barang = $myBuku->getPerlengkapanFoundation($myGrandParentID);
                                $stockBarang = new StockModel();

                                $stockBarang->getWhereOne("org_id=$org  AND id_barang=$id_barang");
                                $jmlhStock = $stockBarang->jumlah_stock;


                                if ($jmlhStock > 0) {
                                    $id_perlengkapan = $myBuku->id_barang_harga;
                                    $lanjut = $lanjut & true;
                                } else {
                                    $lanjut = $lanjut & false;
                                    echo "<b> Stock Habis!</b>";
                                }

                            }
                            ?>
                            <? if (($id_biaya == $jenisBiayaSPP)) {
                            $jenisbm->getByID(AccessRight::getMyOrgID() . "_" . $jenisBiayaSPP);
                            $kuponSatuan = new KuponSatuan();
                            $arrkupon = $kuponSatuan->getWhere("kupon_owner_id = '$org' AND kupon_status = 0 ORDER BY kupon_id ASC");
                            $arrkuponHlp = array();
                            foreach ($arrkupon as $kpn) {
                                $arrkuponHlp[] = $kpn->kupon_id;
                            }
                            ?>
                            <div class="pilih-kupon">
                                Pilih Kupon
                                <input type="text" id="pilih_kupon_<?= $t; ?>"/>
                                <script>
                                    $(function () {
                                        var availableKupons = <? echo json_encode($arrkuponHlp);?>;
                                        $("#pilih_kupon_<?= $t; ?>").autocomplete({
                                            source: availableKupons
                                        });

                                    });
                                </script>

                                &nbsp; Pilih Bulan
                                <?
                                $arrBulan = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12);
                                ?>

                                <select id="pilih_kapan">
                                    <?
                                    for ($x = date("Y"); $x < date("Y") + 2; $x++) {
                                        foreach ($arrBulan as $bln) {
                                            $sel = "";
                                            if ($bln == date("n") && $x == date("Y")) {
                                                $sel = "selected";
                                            }
                                            ?>
                                            <?
                                            if ($x == date("Y")) {
                                                if ($bln >= date("n")) {
                                                    ?>
                                                    <option value="<?= $bln; ?>-<?= $x; ?>" <?= $sel; ?>><?= $bln; ?>
                                                        -<?= $x; ?></option>
                                                    <?
                                                }
                                            } else {
                                                ?>
                                                <option value="<?= $bln; ?>-<?= $x; ?>" <?= $sel; ?>><?= $bln; ?>
                                                    -<?= $x; ?></option>
                                                <?
                                            }


                                        }
                                    }
                                    ?>
                                </select>
                                <? }
                                ?>
                            </div>
                        </td>
                        <td style="text-align: right;">
                            IDR <?= idr($jenisbm->harga); ?>
                        </td>
                    </tr>
                <? } ?>
                <tr style="font-weight: bold;">
                    <td>
                        TOTAL
                    </td>
                    <td style="text-align: right;">
                        IDR <?= idr($total); ?>
                    </td>
                </tr>

                <tr style="font-weight: bold;">
                    <td>
                        Jenis Pembayaran
                    </td>
                    <td style="text-align: right;">
                        <select id="jenis_pmbr">
                            <?
                            $pb = new PembayaranWeb2Model();
                            //                    $arrp = $pb->getAll();
                            $jenispby = $pb->getAll();
                            foreach ($jenispby as $by) {
                                ?>
                                <option value="<?= $by->id_jenis_pembayaran; ?>"><?= $by->jenis_pembayaran; ?></option>
                                <?
                            }
                            ?>
                        </select>
                    </td>
                </tr>
            </table>

            <div style="text-align: center;">
                <button id="payfirst_button" class="btn btn-lg btn-default" style="width: 50%;">SUBMIT PEMBAYARAN
                </button>
            </div>
            <script>
                $('#payfirst_button').click(function () {
                    if (confirm("Anda yakin akan melakukan pembayaran?")) {
                        if (<?= $lanjut; ?>) {
                            var post = {};
                            post.murid_id = '<?= $id; ?>';
                            post.jenis_pmbr = $('#jenis_pmbr').val();
                            post.pilih_kupon = $('#pilih_kupon_<?= $t; ?>').val();
                            post.pilih_kapan = $('#pilih_kapan').val();
                            post.id_buku = '<?= $id_buku; ?>';
                            post.id_perlengkapan = '<?= $id_perlengkapan; ?>';
                            $.post("<?= _SPPATH; ?>MuridWebHelper/process_firstpayment",
                                post,
                                function (data) {
                                    console.log(data);
                                    if (data.status_code) {
                                        alert(data.status_message);

                                        openLw('create_operasional_kelas', '<?=_SPPATH;?>KelasWeb/create_operasional_kelas' + '?now=' + $.now(), 'fade');
                                        activkanMenuKiri('create_operasional_kelas');
                                        //                                    alert(data.buku);
//                                    openLw('read_murid_tc', '<?//= _SPPATH; ?>//MuridWeb/read_murid_tc?t=' + $.now(), 'fade');
                                    } else {
                                        alert(data.status_message);
                                    }
                                }, 'json');
                        } else {
                            alert("Stock Buku atau perlengkapan habis!");
                        }
                    }
                    else {

                    }

                });
            </script>

        </div>
        <?
    }

    function process_firstpayment()
    {
        $murid_id = addslashes($_POST['murid_id']);
        $jenis_pmbr = addslashes($_POST['jenis_pmbr']);
        $pilih_kupon = addslashes($_POST['pilih_kupon']);
        $pilih_kapan = addslashes($_POST['pilih_kapan']);
        $id_perlengkapan = addslashes($_POST['id_perlengkapan']);
        $id_buku = addslashes($_POST['id_buku']);


        $myOrgID = AccessRight::getMyOrgID();
        $myParentID = Generic::getMyParentID($myOrgID);
        // KPO
        $myGrandParentID = Generic::getMyParentID($myParentID);
        $myGrandGrandParentID = Generic::getMyParentID($myGrandParentID);
        /*
         * YANG HARUS DIBUAT
         * Ada 2 jenis pembayaran
         * 1. Pembayaran First Time
         * isinya Registrasi,  , dan perlengkapan
         * perlengkapan tergantung dari stok perlengkapan
         *
         * 2.Pembayaran Bulanan/Rutin
         * pembayaran SPP dihitung masuk ke pembayaran rutin, sekaligus mengurasi kupon
         * akan dibuatkan invoice tiap bulannya scr otomatis, yang blm bayar kelihatan dari status invoicenya
         *
         * 3.Pembayaran Buku / Naik Level
         * isinya Iuran Buku
         * dimana iuran buku tergantung dari stok buku
         *
         * Setelah sukses membayar..murid diplot ke level yang dituju di log nya
         * Jurnal Umum Diisi
         *
         */
        //HITUNG VALUE
// Check Kupon existiert di DB TC


        $obKuponOwner = new KuponSatuan();
        $obKuponOwner->getWhereOne("kupon_id=$pilih_kupon AND kupon_owner_id=$myOrgID AND kupon_status!=1");

        if ($obKuponOwner->kupon_id == null) {
            $json['status_code'] = 0;
            $json['status_message'] = "Kupon tidak ada di database! ";
            echo json_encode($json);
            die();
        }

        $murid = new MuridModel();
        $murid->getByID($murid_id);
        $org = AccessRight::getMyOrgID();
        $myParentID = Generic::getMyParentID($org);
        $myGrandParentID = Generic::getMyParentID($myParentID);
        $akID = Generic::getMyParentID($myGrandParentID);
        $tc = new SempoaOrg();
        $tc->getByID(AccessRight::getMyOrgID());
        $total = 0;


        if ($murid->id_level_sekarang == 1) {

            $arrIDs = Generic::firstPaymentJunior();
        } elseif ($murid->id_level_sekarang == 2) {

            $arrIDs = Generic::firstPaymentJunior2();
        } elseif (($murid->id_level_sekarang >= 3)) {
            $arrIDs = Generic::firstPaymentFoundation();
        }


//        $arrIDs = array(1, 4, 7, 2); //jenis biaya
        $arrSerial = array();
        foreach ($arrIDs as $num => $id_biaya) {
            $biaya = new SettingJenisBiayaModel();
            $biaya->getByID($id_biaya);
            $jenisbm = new JenisBiayaModel();
            $jenisbm->getByID(AccessRight::getMyOrgID() . "_" . $id_biaya);
            $total += $jenisbm->harga;
            $obj = array();
            $obj['harga'] = $jenisbm->harga;
            $obj['id_biaya'] = $biaya->id_biaya;
            $obj['jenis_biaya'] = $biaya->jenis_biaya;
            $arrSerial[] = $obj;
        }
        $arrSerial['kupon'] = array("nomor" => $pilih_kupon, "kapan" => $pilih_kapan);
        //First Time
        $first = new PaymentFirstTimeLog();
        $first->getByID($murid_id);
        $json['$first'] = $first;
        $succ = 0;
        $noInvoiceFP = "";

        if (is_null($first->murid_id)) {
            $first = new PaymentFirstTimeLog();
            $first->murid_id = $murid_id;
            $first->murid_pay_date = leap_mysqldate();
            $first->murid_cara_bayar = $jenis_pmbr;
            $first->murid_pay_value = $total;
            $first->murid_ak_id = $myGrandGrandParentID;
            $first->murid_kpo_id = $myGrandParentID;
            $first->murid_ibo_id = $myParentID;
            $first->murid_tc_id = $myOrgID;
            $thn_skrg = date("Y");
            $bln_skrg = date("n");
            $first->bln_no_urut_inv = $first->getLastNoUrutInvoice($thn_skrg, $bln_skrg, AccessRight::getMyOrgID());
            $noInvoiceFP = "FP/" . $thn_skrg . "/" . $bln_skrg . "/" . $first->bln_no_urut_inv;
            $first->bln_no_invoice = $noInvoiceFP;
            $first->murid_biaya_serial = serialize($arrSerial);
            $succ = $first->save();
        } else {
            if ($first->murid_pay_value > 0) {
                $json['murid_pay_value'] = $first->murid_pay_value;
                $json['status_code'] = 0;
                $json['status_message'] = "Sudah melakukan pembayaran pertama";
                echo json_encode($json);
                die();
            } else {
                $first->murid_pay_date = leap_mysqldate();
                $first->murid_cara_bayar = $jenis_pmbr;
                $first->murid_pay_value = $total;
                $first->murid_ak_id = $myGrandGrandParentID;
                $first->murid_kpo_id = $myGrandParentID;
                $first->murid_ibo_id = $myParentID;
                $first->murid_tc_id = $myOrgID;
                $thn_skrg = date("Y");
                $bln_skrg = date("n");
                $first->bln_no_urut_inv = $first->getLastNoUrutInvoice($thn_skrg, $bln_skrg, AccessRight::getMyOrgID());
                $noInvoiceFP = "FP/" . $thn_skrg . "/" . $bln_skrg . "/" . $first->bln_no_urut_inv;
                $first->bln_no_invoice = $noInvoiceFP;
                $first->murid_biaya_serial = serialize($arrSerial);
                $succ = $first->save(1);
                $json['masuk'] = $succ;
            }
        }

        $json['succ'] = $succ;
        if ($succ > 0) {
            $json['masuk2'] = $succ;
        }
        if ($succ > 0) {
            $json['masuk3'] = $succ;
            $murid = new MuridModel();
            $murid->getByID($murid_id);
            $murid->pay_firsttime = 1;
            //bayar pakai kupon
            $iu = new IuranBulanan();

            $succ2 = $iu->createIuranBulananFirstPayment($murid_id, $pilih_kapan, $pilih_kupon, $myParentID, $myGrandParentID, $myGrandGrandParentID, AccessRight::getMyOrgID(), $jenis_pmbr);

            if ($succ2) {
                $ksatuan = new KuponSatuan();
                $ksatuan->getByID($pilih_kupon);
                $ksatuan->kupon_pemakaian_date = leap_mysqldate();
                $ksatuan->kupon_status = 1;
                $ksatuan->kupon_pemakaian_id = $succ2;
                $ksatuan->save();
                //activkan murid
//                $murid->id_level_masuk =
                $murid->status = 1;
                $murid->save(1);
                $j = new MuridJourney();
                $j->journey_murid_id = $murid_id;
                $j->journey_level_mulai = $murid->id_level_masuk;
                $j->journey_mulai_date = leap_mysqldate();
                $j->journey_tc_id = AccessRight::getMyOrgID();
                $j->save();


                // StockBarang

                // StockBarang
                $stockBarangBuku = new StockModel();
                $stockBarangBuku->getWhereOne("id_barang = '$id_buku' AND org_id='$org'");
                $stockBarangBuku->jumlah_stock = $stockBarangBuku->jumlah_stock - 1;
                $stockBarangBuku->save();
                $stockBarang = new StockModel();
                $stockBarang->getWhereOne("id_barang = $id_perlengkapan AND org_id=$org");
                $stockBarang->jumlah_stock = $stockBarang->jumlah_stock - 1;
                $stockBarang->save();

//                $arrIDBuku = explode(",", $id_buku);
//                foreach ($arrIDBuku as $val) {
//                    $stockBarangBuku = new StockModel();
//                    $stockBarangBuku->getWhereOne("id_barang = '$val' AND org_id='$org'");
//                    $stockBarangBuku->jumlah_stock = $stockBarangBuku->jumlah_stock - 1;
//                    $stockBarangBuku->save(1);
//                }

                $objIuranBuku = new IuranBuku();
                $bln = date("n");
                $thn = date("Y");
                $objIuranBuku->bln_murid_id = $murid_id;
                $objIuranBuku->bln_date_pembayaran = leap_mysqldate();
                $objIuranBuku->bln_date = $bln . "-" . $thn;
                $objIuranBuku->bln_mon = $bln;
                $objIuranBuku->bln_tahun = $thn;
                $objIuranBuku->bln_tc_id = $murid->murid_tc_id;
                $objIuranBuku->bln_kpo_id = $murid->murid_kpo_id;
                $objIuranBuku->bln_ibo_id = $murid->murid_ibo_id;
                $objIuranBuku->bln_ak_id = $murid->murid_ak_id;
                $objIuranBuku->bln_buku_level = $murid->id_level_sekarang;
                $objIuranBuku->bln_status = 1;
                $objIuranBuku->bln_cara_bayar = $jenis_pmbr;
                $objIuranBuku->bln_no_invoice = $noInvoiceFP;
                $id_iuranbuku = $objIuranBuku->save();
                $json['noinvice'] = $noInvoiceFP;
                $setNoBuku = new StockBuku();
                $resBuNo = $setNoBuku->getBukuYgdReservMurid($murid->id_level_sekarang, $murid->murid_tc_id, $murid_id, 0, KEY::$JENIS_BUKU);
                $setNoBuku->setStatusBuku($resBuNo, $murid_id, $id_iuranbuku);
                // Stock Buku No


                $myID = AccessRight::getMyOrgID();
                Generic::createLaporanDebet($myID, $myID, KEY::$DEBET_IURAN_BUKU_TC, KEY::$BIAYA_IURAN_BUKU, "Iuran Buku: Siswa: " . Generic::getMuridNamebyID($murid_id), 1, 0, "Utama");
                $arrjenisBiayaSPP = Generic::getJenisBiayaType();
                $jenisBiayaSPP = $arrjenisBiayaSPP[$murid->id_level_sekarang];
                Generic::createLaporanDebet($myID, $myID, KEY::$DEBET_IURAN_BULANAN_TC, $jenisBiayaSPP, "Iuran Bulanan: Siswa: " . Generic::getMuridNamebyID($murid_id) . ", Bulan: " . $iu->bln_date . " dgn Kode Kupon: " . $iu->bln_kupon_id, 1, 0, "Utama");
                Generic::createLaporanDebet($myID, $myID, KEY::$DEBET_REGISTRASI_TC, KEY::$BIAYA_REGISTRASI, "Registrasi: Siswa: " . Generic::getMuridNamebyID($murid_id), 1, 0, "Utama");


                if (($murid->id_level_sekarang < 3)) {
                    Generic::createLaporanDebet($myID, $myID, KEY::$DEBET_PERLENGKAPAN_TC, KEY::$BIAYA_PERLENGKAPAN_JUNIOR, "Perlengkapan: Siswa: " . Generic::getMuridNamebyID($murid_id), 1, 0, "Utama");
                } else {
                    Generic::createLaporanDebet($myID, $myID, KEY::$DEBET_PERLENGKAPAN_TC, KEY::$BIAYA_PERLENGKAPAN_FOUNDATION, "Perlengkapan: Siswa: " . Generic::getMuridNamebyID($murid_id), 1, 0, "Utama");
                }

                // Create Nilai

                $nilaiMurid = new NilaiModel();
                $arrNilai = $nilaiMurid->getWhere("nilai_murid_id ='$murid_id' AND nilai_level='$murid->id_level_sekarang'");
                if (count($arrNilai) == 0) {
                    $nilaiMurid->nilai_murid_id = $murid_id;
                    $nilaiMurid->nilai_level = $murid->id_level_sekarang;
                    $nilaiMurid->nilai_create_date = leap_mysqldate();
                    $nilaiMurid->nilai_org_id = $myID;
                    $nilaiMurid->save();
                }
//                $json['buku'] = pr($stockBarangBuku);
                $json['status_code'] = 1;
                $json['status_message'] = "Payment Entry Success\nSilahkan Masukan Murid Ke Kelas Yang Diinginkan";
                echo json_encode($json);
                die();
            }
        }
        $json['status_code'] = 0;
        $json['status_message'] = "Gagal";
        echo json_encode($json);
        die();
    }

    function undo_process_firstpayment()
    {
        //murid_biaya_serial
        $murid_id = addslashes($_GET['murid_id']);
        $id_level = addslashes($_GET['level_murid']);

        $murid = new MuridModel();
        $murid->getByID($murid_id);


        $myID = $murid->murid_tc_id;
        $myOrgID = $murid->murid_tc_id;
        $myParentID = Generic::getMyParentID($myOrgID);
        // KPO

        $myGrandParentID = Generic::getMyParentID($myParentID);
        $myGrandGrandParentID = Generic::getMyParentID($myGrandParentID);


        $fp = new PaymentFirstTimeLog();
        $fp->getWhereOne("murid_id=$murid_id");
        if (is_null($fp->murid_id)) {
            $json['status_code'] = 0;
            $json['status_message'] = "Data Murid tidak ditemukan!";
            echo json_encode($json);
            die();
        }
        $murid->pay_firsttime = 0;
        $murid->status = 0;
        $murid->save(1);

        $arrRetourBiaya = unserialize($fp->murid_biaya_serial);
        $json['obj'] = $arrRetourBiaya;
        $fp->murid_pay_value = "";
        $fp->murid_biaya_serial = "";
        $noInvoice = $fp->bln_no_invoice;
        $fp->save(1);
        foreach ($arrRetourBiaya as $val) {
            foreach ($val as $key => $valhlp) {
                if (($key == "jenis_biaya") AND ($valhlp == "Registrasi")) {
                    Generic::createLaporanDebet($myID, $myID, KEY::$DEBET_REGISTRASI_TC, KEY::$BIAYA_REGISTRASI, "Registrasi: Siswa: " . Generic::getMuridNamebyID($murid_id), -1, 0, "Utama");


                }
                if (($key == "jenis_biaya") AND ($valhlp == "Iuran Buku")) {
                    $myBuku = new BarangWebModel();
                    $arrMyBuku = $myBuku->getWhere("level=$murid->id_level_sekarang  AND jenis_biaya = 1 AND kpo_id = $myGrandParentID LIMIT 0,1");
                    $stockBarang = new StockModel();
                    $buku_active = array_pop($arrMyBuku);
                    $stockBarang->getWhereOne("id_barang='$buku_active->id_barang_harga' AND org_id='$myID'");
                    $id_buku = $buku_active->id_barang_harga;


//                    $stockBarang->retourStock($id_buku, $myID);
                    Generic::createLaporanDebet($myID, $myID, KEY::$DEBET_IURAN_BUKU_TC, KEY::$BIAYA_IURAN_BUKU, "Iuran Buku: Siswa: " . Generic::getMuridNamebyID($murid_id), -1, 0, "Utama");


                }

                if (($key == "jenis_biaya") AND ($valhlp == "Perlengkapan Junior")) {

                    $myBuku = new BarangWebModel();
                    $id_perlengkapan = $myBuku->getPerlengkapanJunior($myGrandParentID);
                    $stockBarang = new StockModel();
                    $stockBarang->retourStock($id_perlengkapan, $myID);
                    Generic::createLaporanDebet($myID, $myID, KEY::$DEBET_PERLENGKAPAN_TC, KEY::$BIAYA_PERLENGKAPAN_JUNIOR, "Perlengkapan: Siswa: " . Generic::getMuridNamebyID($murid_id), -1, 0, "Utama");

                }

                if (($key == "jenis_biaya") AND ($valhlp == "Perlengkapan Fondation ")) {
                    $myBuku = new BarangWebModel();
                    $id_perlengkapan = $myBuku->getPerlengkapanFoundation($myGrandParentID);
                    $stockBarang = new StockModel();
                    $stockBarang->retourStock($id_perlengkapan, $myID);
                    Generic::createLaporanDebet($myID, $myID, KEY::$DEBET_PERLENGKAPAN_TC, KEY::$BIAYA_PERLENGKAPAN_FOUNDATION, "Perlengkapan: Siswa: " . Generic::getMuridNamebyID($murid_id), -1, 0, "Utama");

                }
                if (($key == "nomor")) {
                    $kuponSatuan = new KuponSatuan();
                    $noKupon = $val['nomor'];
                    $kuponSatuan->retourKupon($val['nomor']);
                    $json['nokupon'] = $val['nomor'];
                    $arrjenisBiayaSPP = Generic::getJenisBiayaType();
                    $jenisBiayaSPP = $arrjenisBiayaSPP[$id_level];
                    $m = new DateTime($fp->murid_pay_date);
                    Generic::createLaporanDebet($myID, $myID, KEY::$DEBET_IURAN_BULANAN_TC, $jenisBiayaSPP, "Iuran Bulanan: Siswa: " . Generic::getMuridNamebyID($murid_id) . ", Bulan: " . $m->format("m-Y") . " dgn Kode Kupon: " . $val['nomor'], -1, 0, "Utama");
                    $iuranBulanan = new IuranBulanan();
                    $iuranBulanan->getWhereOne("bln_kupon_id='$noKupon'");
                    $iuranBulanan->delete($iuranBulanan->bln_id);
                }
            }

        }


        $iuranBuku = new IuranBuku();
        $iuranBuku->getWhereOne("bln_no_invoice='$noInvoice' AND bln_murid_id=$murid_id");
        $iuranBuku->bln_status = 0;
        $iuranBuku->delete($iuranBuku->bln_id);
        $id_invoice = $iuranBuku->bln_id;
        $stockBuku = new StockBuku();
        $arrStockBuku = $stockBuku->getWhere("stock_invoice_murid='$id_invoice' AND stock_murid_id=$murid_id");
        foreach ($arrStockBuku as $buku) {
            $stockBuku = new StockBuku();
            $stockBuku->retourBukuMurid($id_invoice);
            $stockBarang = new StockModel();
            $stockBarang->retourStock($buku->stock_id_buku, $myID);
        }
        $nilaiMurid = new NilaiModel();
        $nilaiMurid->getWhereOne("nilai_murid_id=$murid_id AND nilai_level=$id_level");
        if (!is_null($nilaiMurid->nilai_id)) {
            $nilaiMurid->nilai_delete = 1;
            $nilaiMurid->save(1);
        }
        $json['status_code'] = 1;
        $json['status_message'] = "Transaksi Firstpayment berhasil dibatalkan!";


        echo json_encode($json);
        die();


        $j = new MuridJourney();
        $j->journey_murid_id = $murid_id;
        $j->journey_level_mulai = $murid->id_level_masuk;
        $j->journey_mulai_date = leap_mysqldate();
        $j->journey_tc_id = AccessRight::getMyOrgID();
        $j->save();


        $nilaiMurid = new NilaiModel();
        $arrNilai = $nilaiMurid->getWhere("nilai_murid_id ='$murid_id' AND nilai_level='$murid->id_level_sekarang'");
        if (count($arrNilai) == 0) {
            $nilaiMurid->nilai_murid_id = $murid_id;
            $nilaiMurid->nilai_level = $murid->id_level_sekarang;
            $nilaiMurid->nilai_create_date = leap_mysqldate();
            $nilaiMurid->nilai_org_id = $myID;
            $nilaiMurid->save();
        }


        $json['status_code'] = 0;
        $json['status_message'] = "Gagal";
        echo json_encode($json);
        die();
    }

    function profile()
    {
        $myType = AccessRight::getMyOrgType();
        $id = addslashes($_GET['id_murid']);
        $murid = new MuridModel();
        $murid->getByID($id);
        $arrStatusMurid = Generic::getAllStatusMurid();
        $arrLevelMurid = Generic::getAllLevel();
        $html = "\"<select id='select_status_$murid->id_murid'>";
        foreach ($arrStatusMurid as $key => $value) {
            if ($key == $murid->status) {
                $html = $html . "<option value='$key' selected>$value</option>";
            } else {
                $html = $html . "<option value='$key'>$value</option>";
            }
        }
        $html = $html . "</select>\"";


        $htmlLevel = "\"<select id='select_lvl_$murid->id_murid'>";
        foreach ($arrLevelMurid as $key => $value) {
            if ($key == $murid->id_level_sekarang) {
                $htmlLevel = $htmlLevel . "<option value='$key' selected>$value</option>";
            } else {
                $htmlLevel = $htmlLevel . "<option value='$key'>$value</option>";
            }
        }
        $htmlLevel = $htmlLevel . "</select>\"";


        $t = time();
        ?>

        <style>
            .glyphicon-pencil {
                cursor: pointer;
            }
        </style>
        <section class="content-header">
            <?
            if (AccessRight::getMyOrgType() == KEY::$KPO) {
                ?>
                <span id="edit_<?= $id; ?>" class="pull-right  glyphicon glyphicon-pencil"
                      onclick="openLw('MuridWebView', '<?= _SPPATH; ?>MuridWeb3/read_murid_kpo?cmd=edit&id=<?= $id; ?>&parent_page=' + window.selected_page + '&loadagain=' + $.now(), 'fade');"></span>
                <?
            } elseif (AccessRight::getMyOrgType() == KEY::$IBO) {
                ?>
                <span id="edit_<?= $id; ?>" class="pull-right  glyphicon glyphicon-pencil"
                      onclick="openLw('MuridWebView', '<?= _SPPATH; ?>MuridWebHelper/readmuridibo?cmd=edit&id=<?= $id; ?>&parent_page=' + window.selected_page + '&loadagain=' + $.now(), 'fade');"></span>
                <?
            } elseif (AccessRight::getMyOrgType() == KEY::$TC) {
                ?>
                <span id="edit_<?= $id; ?>" class="pull-right  glyphicon glyphicon-pencil"
                      onclick="openLw('MuridWebView', '<?= _SPPATH; ?>MuridWeb/read_murid_tc?cmd=edit&id=<?= $id; ?>&parent_page=' + window.selected_page + '&loadagain=' + $.now(), 'fade');"></span>
                <?
            }
            ?>


        </section>

        <style>
            .murid_prof table {
                background-color: #fff;
            }

            .murid_prof table td {
                line-height: 34px;
                vertical-align: middle;
            }

            .murid_prof .col-md-4 h3 {
                background-color: #f6f6f6;
                text-align: center;
                margin-bottom: 0px;
                border-bottom: 2px solid #005384;
                padding-bottom: 20px;
                padding-top: 20px;
            }

            .murid_prof .btn-default {
                background-color: #67a8ce;
                color: white;
                border-color: #67a8ce;
                font-weight: bold;
            }
        </style>
        <div class="row2 murid_prof" style="padding-top: 20px;">
            <!-- left column -->
            <div class="col-md-12">
                <div class="text-center">
                    <img width="100px" src="<?= _BPATH . _PHOTOURL . $murid->gambar; ?>" onerror="imgError(this);"
                         class="img-circle" id="foto_<?= $murid->id_murid . "_" . $t; ?>">
                    <h1><?= $murid->nama_siswa; ?>
                    </h1>
                </div>
            </div>

            <!-- edit form column -->
            <div class="col-md-12">
                <div class="col-md-4">
                    <h3><i class="fa fa-info-circle"></i> Basic Info</h3>
                    <table class="table table-striped ">
                        <tr>
                            <td>
                                Level
                            </td>
                            <td id='<?= $murid->id_level_sekarang . "_" . $murid->id_murid; ?>'>

                                <b><?= Generic::getLevelNameByID($murid->id_level_sekarang); ?></b>
                            </td>
                            <td>
                                <?
                                if ($murid->status == KEY::$STATUSMURIDAKTIV) {
                                    if (AccessRight::getMyOrgType() == 'tc') {
                                        ?>
                                        <button class='btn btn-default' id='naiklevel_<?= $murid->id_murid; ?>'>Naik
                                            Level
                                        </button>
                                        <?
                                    }

                                }
                                ?>


                            </td>
                        </tr>

                        <tr>
                            <td>
                                Status
                            </td>
                            <td id="status_<?= $murid->id_murid; ?>_<?= $t; ?>" colspan="2" style="font-weight: bold;">
                                <?= $arrStatusMurid[$murid->status]; ?>
                            </td>
                        </tr>


                        <tr>
                            <td>
                                TC
                            </td>
                            <td colspan="2">
                                <b><?= Generic::getTCNamebyID($murid->murid_tc_id); ?></b>
                            </td>
                        </tr>
                        <? if ($murid->status != 0) { ?>
                            <tr>
                                <td>
                                    History
                                </td>
                                <td colspan="2">
                                    <button
                                        onclick="openLw('murid_history_<?= $id; ?>', '<?= _SPPATH; ?>MuridWebHelper/murid_history?id=<?= $id; ?>', 'fade');"
                                        class="btn btn-default">View History
                                    </button>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    Nilai
                                </td>
                                <td colspan="2">
                                    <button
                                        onclick="openLw('murid_nilai_<?= $id; ?>', '<?= _SPPATH; ?>MuridWebHelper/murid_nilai?id=<?= $id; ?>', 'fade');"
                                        class="btn btn-default">Nilai
                                    </button>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    Ujian
                                </td>
                                <td colspan="2">
                                    <button
                                        onclick="openLw('murid_lomba_<?= $id; ?>', '<?= _SPPATH; ?>MuridWebHelper/murid_lomba?id=<?= $id; ?>', 'fade');"
                                        class="btn btn-default">Ujian
                                    </button>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    Kurikulum
                                </td>
                                <td colspan="2">
                                    <button id="murid_ganti_kur_<?= $id; ?>"
                                            class="btn btn-default"><? if ($murid->murid_kurikulum == KEY::$KURIKULUM_BARU) {
                                            echo "Baru";
                                        } else {
                                            echo "Lama";
                                        }
                                        ?>
                                    </button>
                                </td>
                            </tr>

                            <?
                            if (AccessRight::getMyOrgType() == KEY::$IBO) {
                                ?>
                                <tr>
                                    <td>
                                        <b>Adjust Level ke Kurikulum Baru</b>
                                    </td>
                                    <td id="adjust_level_kurikulum_<?= $murid->id_murid . "_" . $t; ?>" colspan="2"
                                        style="font-weight: bold;">
                                        <?= Generic::getLevelNameByID($murid->id_level_sekarang); ?>
                                    </td>
                                </tr>
                                <?

                            }


                            ?>


                        <? } ?>
                    </table>
                </div>


                <div class="col-md-4 ">
                    <h3><i class=" fa fa-dollar"></i> Pembayaran</h3>
                    <table class="table table-striped ">
                        <? if ($murid->status != 0) { ?>
                            <tr>
                                <td>
                                    Invoices
                                </td>
                                <td colspan="2">
                                    <button class="btn btn-default"
                                            onclick="openLw('murid_Invoices_<?= $id; ?>', '<?= _SPPATH; ?>MuridWebHelper/murid_invoices?id=<?= $id; ?>', 'fade');">
                                        View Invoices
                                    </button>
                                </td>
                            </tr>
                        <? } ?>
                        <tr>
                            <td>
                                Pendaftaran Pertama
                            </td>
                            <td>
                                <?
                                if ($murid->pay_firsttime == '0') {
//                $obj->removeAutoCrudClick = array("pay_firsttime");
                                    echo "<button class='btn btn-default' onclick=\"openLw('Payment_Murid','" . _SPPATH . "MuridWebHelper/firsttime_payment?id_murid=" . $murid->id_murid . "','fade');\">Payment First Time</button>";
                                } else {
                                    echo "<a target=\"_blank\" href=" . _SPPATH . "MuridWebHelper/printRegister2?id_murid=" . $murid->id_murid . "><span  style=\"vertical-align:middle\" class=\"glyphicon glyphicon-print\"  aria-hidden=\"true\"></span>
                                            </a>";
                                }
                                ?>
                            </td>


                            <td>
                                <?


                                if (($murid->pay_firsttime == 1) AND (AccessRight::getMyOrgType() == KEY::$IBO)) {
                                    $fp = new PaymentFirstTimeLog();
                                    $fp->getWhereOne("murid_id=$murid->id_murid");
//                                    pr($fp);
                                    if (!is_null($fp->murid_id)) {
                                        $now = time();
                                        $your_date = strtotime($fp->murid_pay_date);
                                        $datediff = Generic::diffTwoDaysInDay($now, $your_date);
//                                        echo $murid->id_murid . " - " . $fp->murid_pay_date . " - " . Generic::getMuridNamebyID($fp->murid_id) . " - ".$datediff;
                                        if ($datediff <= KEY::$MAX_UNDO_FIRST_PAYMENT) {
                                            ?>
                                            <span id="undo_first_payment_<?= $murid->id_murid; ?>" class="fa fa-undo"
                                                  aria-hidden="true"></span>
                                            <?

                                            ;
                                        }
                                    }

                                } else {

                                }
                                ?>
                            </td>

                        </tr>

                        <?
                        if (AccessRight::getMyOrgType() == KEY::$TC) {
                            ?>
                            <tr>
                                <td>
                                    Beli Buku pengganti
                                </td>
                                <td colspan="2">
                                    <button id="beli_buku_<?= $t; ?>"
                                            class="btn btn-default"><?= Generic::getLevelNameByID($murid->id_level_sekarang); ?>
                                    </button>
                                </td>
                            </tr>
                            <?
                        }

                        ?>


                    </table>
                </div>

                <div class="col-md-4">
                    <h3><i class="fa fa-home"></i> Kelas</h3>
                    <table class="table table-striped ">
                        <? if ($murid->status != 0) { ?>
                            <tr>
                                <td>
                                    Lihat Absensi
                                </td>
                                <td colspan="2">
                                    <button
                                        onclick="openLw('murid_absensi_<?= $id; ?>', '<?= _SPPATH; ?>MuridWebHelper/murid_absensi?id=<?= $id; ?>', 'fade');"
                                        class="btn btn-default">Absensi Murid
                                    </button>
                                </td>
                            </tr>
                            <? if ($myType == KEY::$TC) {
                                ?>
                                <tr>

                                    <td>
                                        Absensi Murid
                                    </td>
                                    <td colspan="2">
                                        <button
                                            onclick="openLw('absen_profile_<?= $id; ?>', '<?= _SPPATH; ?>KelasWebHelper/absen_profile?id_murid=<?= $id; ?>', 'fade');"
                                            class="btn btn-default">Absen
                                        </button>
                                    </td>
                                </tr>
                                <?

                            } ?>


                            <tr>

                                <td>
                                    Kelas
                                </td>
                                <td>
                                    <button
                                        onclick="openLw('murid_kelas_<?= $id; ?>', '<?= _SPPATH; ?>MuridWebHelper/murid_kelas?id=<?= $id; ?>', 'fade');"
                                        class="btn btn-default">View Kelas
                                    </button>
                                </td>
                                <?
                                if ($myType == KEY::$TC) {
                                    ?>
                                    <td>
                                        <button
                                            onclick="openLw('create_operasional_kelas_<?= $id; ?>', '<?= _SPPATH; ?>KelasWeb/create_operasional_kelas', 'fade');"
                                            class="btn btn-default">Pindah Kelas
                                        </button>
                                    </td>
                                    <?
                                }
                                ?>

                            </tr>
                        <? } ?>
                    </table>
                </div>


                <div class="clearfix"></div>
            </div>
        </div>
        <script>

            $("#foto_<?=$murid->id_murid . "_" . $t;?>").click(function () {

            });
            $("#beli_buku_<?= $t; ?>").click(function () {
                if (confirm("Anda yakin akan membeli buku level " + "<?=Generic::getLevelNameByID($murid->id_level_sekarang);?>")) {

                    var kur =<?= $murid->murid_kurikulum; ?>;
                    var id_level =<?= $murid->id_level_sekarang; ?>;
                    var id_murid = <?= $murid->id_murid; ?>;

                    if (kur == 0) {
                        $.get("<?= _SPPATH; ?>MuridWebHelper/create_invoice_buku_manual?id_murid=" + id_murid + "&id_level=" + id_level + "&kur=" + kur, function (data2) {
                            console.log(data2);
                            if (data2.status_code) {
                                alert(data2.status_message);
                                openLw('murid_Invoices_1', '<?=_SPPATH;?>MuridWebHelper/murid_invoices?active_tab=buku&id=' + id_murid, 'fade');
                                lwrefresh("murid_Invoices_<?= $murid->id_murid; ?>");
                            } else {
                                alert(data2.status_message);
                                openLw('murid_Invoices_1', '<?=_SPPATH;?>MuridWebHelper/murid_invoices?active_tab=buku&id=' + id_murid, 'fade');
                                lwrefresh("murid_Invoices_<?= $murid->id_murid; ?>");

                            }
                        }, 'json');

                    }


                    else {
                        $.get("<?= _SPPATH; ?>MuridWebHelper/create_invoice_buku_manual?id_murid=" + id_murid + "&id_level=" + id_level + "&kur=" + kur, function (data2) {
                            console.log(data2);
                            lwrefresh("murid_Invoices_<?= $murid->id_murid; ?>");
                            if (data2.status_code) {
                                alert(data2.status_message);

                                openLw('murid_Invoices_1', '<?=_SPPATH;?>MuridWebHelper/murid_invoices?active_tab=buku&id=' + id_murid, 'fade');
                                lwrefresh("murid_Invoices_<?= $murid->id_murid; ?>");
                            } else {
                                alert(data2.status_message);
                                openLw('murid_Invoices_1', '<?=_SPPATH;?>MuridWebHelper/murid_invoices?active_tab=buku&id=' + id_murid, 'fade');
                                lwrefresh("murid_Invoices_<?= $murid->id_murid; ?>");

                            }
                        }, 'json');
                    }
                }
            });
            $("#murid_ganti_kur_<?= $id; ?>").click(function () {

                if (confirm("Anda yakin akan mengantikan Kurikulum?")) {
                    $.get("<?= _SPPATH; ?>MuridWebHelper/murid_ganti_kur?id=<?= $id; ?>", function (data) {
                        alert(data.status_message);
                        console.log(data);
                        if (data.status_code) {

                            lwrefresh(selected_page);
                        }
//                        console.log(data.murid);

                    }, 'json');
                }
            });
            $('#undo_first_payment_<?=$murid->id_murid; ?>').click(function () {
                if (confirm("Anda yakin akan membatalkan transaksi Firstpayment?")) {
                    $.get("<?= _SPPATH; ?>MuridWebHelper/undo_process_firstpayment?murid_id=<?= $murid->id_murid; ?>" + "&level_murid=<?= $murid->id_level_masuk; ?>", function (data) {
                        alert(data.status_message);
                        console.log(data);
                        if (data.status_code) {

                            lwrefresh(selected_page);
                        }
//                        console.log(data.murid);

                    }, 'json');
                }
            });
            $('#status_<?= $murid->id_murid; ?>_<?=$t;?>').dblclick(function () {
                <?
                if($murid->pay_firsttime == 1){
                ?>
                $('#status_<?= $murid->id_murid; ?>_<?=$t;?>').html(<?= $html ?>);
                $('#select_status_<?= $murid->id_murid; ?>').change(function () {
                    var id_status = $('#select_status_<?= $murid->id_murid; ?>').val();

                    $.get("<?= _SPPATH; ?>MuridWebHelper/setStatusMurid?id_murid=<?= $murid->id_murid; ?>" + "&id_status=" + id_status, function (data) {
                        alert(data.status_message);
                        if (data.status_code) {

                            lwrefresh(selected_page);
                        }
                        console.log(data.murid);

                    }, 'json');

                });
                <?
                }
                else{
                ?>
                alert("Murid belum melakukan pembayaran pertama");
                <?
                }
                ?>

            });

            $('#adjust_level_kurikulum_<?= $murid->id_murid . "_" . $t; ?>').dblclick(function () {
                $('#adjust_level_kurikulum_<?= $murid->id_murid . "_" . $t; ?>').html(<?= $htmlLevel ?>);

                $('#select_lvl_<?=$murid->id_murid; ?>').change(function () {

                    var id_level = $('#select_lvl_<?=$murid->id_murid; ?>').val();
                    $.get("<?= _SPPATH; ?>MuridWebHelper/changeLevelKurikulum?id_murid=<?= $murid->id_murid; ?>" + "&id_level_murid=" + id_level, function (data) {
                        console.log(data);
                        alert(data.status_message);
                        if (data.status_code) {
                            lwrefresh(selected_page);
                        }
                        console.log(data.murid);

                    }, 'json');
                })
//
            });


            $('#naiklevel_<?= $murid->id_murid; ?>').click(function () {


                <?
                $arrKur = Generic::getJenisKurikulum();
                ?>

                if (confirm("Anda yakin akan menaikan kelas ke level selanjutnya?")) {

                    var kur =<?= $murid->murid_kurikulum; ?>;
                    var id_level =<?= $murid->id_level_sekarang; ?>;
                    var id_murid = <?= $murid->id_murid; ?>;

                    if (kur == 0) {
                        $.get("<?= _SPPATH; ?>MuridWebHelper/naik_kelas?id_murid=" + id_murid + "&id_level=" + id_level + "&kur=" + kur, function (data2) {
                            console.log(data2);
                            if (data2.status_code) {
                                alert(data2.status_message);
//                            lwrefresh("murid_Invoices_<?//= $murid->id_murid; ?>//");
                                openLw('murid_Invoices_1', '<?=_SPPATH;?>MuridWebHelper/murid_invoices?active_tab=buku&id=' + id_murid, 'fade');
                            } else {
                                alert(data2.status_message);
                                openLw('murid_Invoices_1', '<?=_SPPATH;?>MuridWebHelper/murid_invoices?active_tab=buku&id=' + id_murid, 'fade');

                            }
                        }, 'json');

                    }

                    else {

                        // kur = 1 lama
                        // Kurikulum lama dan buku lama
                        if (confirm("Sekarang Murid ini masih memakai Kurikulum <?=$arrKur[$murid->murid_kurikulum];?>.\n\nApakah Anda akan membeli buku kurikulum lama? \n(Jika Anda memilih Cancel, berarti Anda memilih buku Kurikulum baru dan kurikulum akan disesuaikan)")) {

                            if (confirm("Anda akan membeli buku lama?")) {
                                $.get("<?= _SPPATH; ?>MuridWebHelper/naik_kelas?id_murid=" + id_murid + "&id_level=" + id_level + "&kur=" + kur, function (data2) {
                                    console.log(data2);
                                    if (data2.status_code) {
                                        alert(data2.status_message);
//                            lwrefresh("murid_Invoices_<?//= $murid->id_murid; ?>//");
                                        openLw('murid_Invoices_1', '<?=_SPPATH;?>MuridWebHelper/murid_invoices?active_tab=buku&id=' + id_murid, 'fade');
                                    } else {
                                        alert(data2.status_message);
                                        openLw('murid_Invoices_1', '<?=_SPPATH;?>MuridWebHelper/murid_invoices?active_tab=buku&id=' + id_murid, 'fade');

                                    }
                                }, 'json');

                            }


                        }
                        else {
//                            alert("level: " + id_level );
                            // Cancel ganti ke kurikulum baru
                            if (confirm("Anda akan membeli buku baru dan kurikulum Anda akan diganti ke kurikulum baru?")) {
                                $.get("<?= _SPPATH; ?>MuridWebHelper/naik_kelas?id_murid=" + id_murid + "&id_level=" + id_level + "&kur=" + kur + "&gantiKur=1", function (data2) {
                                    console.log(data2);
                                    if (data2.status_code) {
                                        alert(data2.status_message);
//                            lwrefresh("murid_Invoices_<?//= $murid->id_murid; ?>//");
                                        openLw('murid_Invoices_1', '<?=_SPPATH;?>MuridWebHelper/murid_invoices?active_tab=buku&id=' + id_murid, 'fade');
                                    } else {
                                        alert(data2.status_message);
                                        openLw('murid_Invoices_1', '<?=_SPPATH;?>MuridWebHelper/murid_invoices?active_tab=buku&id=' + id_murid, 'fade');

                                    }
                                }, 'json');

                            }

                        }
                    }


                }
            });
        </script>
        <?
    }

    function murid_absensi()
    {
        $page = isset($_GET['page']) ? addslashes($_GET['page']) : 1;
        $limit = KEY::$LIMIT_PROFILE;
        $begin = ($page - 1) * $limit;
        $id = addslashes($_GET['id']);
        $objAbsen = new AbsenEntryModel();
        $murid = new MuridModel();
        $murid->getByID($id);
//          $objAbsen->absen_murid_id
        $arrAbsen = $objAbsen->getWhere("absen_murid_id='$id' ORDER BY absen_date DESC LIMIT $begin,$limit");
        $jumlahTotal = $objAbsen->getJumlah("absen_murid_id='$id'");
        $jumlahHalamanTotal = ceil($jumlahTotal / $limit);
        $t = time();
        ?>

        <section class="content-header">

            <h1>
                <div class="pull-right">
                    <button class="btn btn-default" onclick="back_to_profile_murid('<?= $id; ?>');">back to profile
                    </button>
                </div>
                Absensi <?= $murid->nama_siswa; ?>


            </h1>

        </section>

        <section class="content">
            <div class="table-responsive">
                <table class="table table-striped ">
                    <thead>
                    <tr>
                        <th>
                            Tanggal
                        </th>
                        <th>
                            Kelas
                        </th>
                        <th>
                            Guru
                        </th>
                        <th>
                            Pengabsen
                        </th>
                    </tr>
                    </thead>
                    <tbody id="container_load_absen">
                    <?
                    //                        pr($arrAbsen);
                    foreach ($arrAbsen as $absen) {
//                    $kelas = new KelasWebModel();
//                    $kelas->getByID($absen->absen_kelas_id)
                        ?>
                        <tr>
                            <td><?= $absen->absen_reg_date; ?></td>
                            <td><?= Generic::printerKelas($absen->absen_kelas_id); ?></td>
                            <td><?= Generic::getGuruNamebyID($absen->absen_guru_id); ?></td>
                            <td><?
                                $acc = new Account();
                                $acc->getByID($absen->absen_pengabsen_id);
                                echo $acc->admin_nama_depan;
                                ?></td>
                        </tr>
                        <?
                    }
                    ?>

                    </tbody>
                    <script>
                    </script>
                </table>
            </div>
            <div class="text-center">
                <button class="btn btn-default" id="loadmore_absen_<?= $t; ?>">Load more</button>
            </div>
            <script>
                var page_absen = <?= $page; ?>;
                var total_page_absen = <?= $jumlahHalamanTotal; ?>;
                $('#loadmore_absen_<?= $t; ?>').click(function () {
                    if (page_absen < total_page_absen) {
                        page_absen++;
                        $.get("<?= _SPPATH; ?>MuridWebHelper/murid_absensi_load?id=<?= $id; ?>&page=" + page_absen, function (data) {
                            $("#container_load_absen").append(data);
                        });
                        if (page_absen > total_page_absen)
                            $('#loadmore_absen_<?= $t; ?>').hide();
                    } else {
                        $('#loadmore_absen_<?= $t; ?>').hide();
                    }
                });
            </script>
        </section>
        <?
//          pr($arrAbsen);
    }

    function murid_absensi_load()
    {
        $page = isset($_GET['page']) ? addslashes($_GET['page']) : 1;
        $limit = KEY::$LIMIT_PROFILE;
        $begin = ($page - 1) * $limit;
        $id = addslashes($_GET['id']);
        $objAbsen = new AbsenEntryModel();
        $murid = new MuridModel();
        $murid->getByID($id);
//          $objAbsen->absen_murid_id
        $arrAbsen = $objAbsen->getWhere("absen_murid_id='$id' ORDER BY absen_date DESC LIMIT $begin,$limit");
        $jumlahTotal = $objAbsen->getJumlah("absen_murid_id='$id'");
        $jumlahHalamanTotal = ceil($jumlahTotal / $limit);
        foreach ($arrAbsen as $absen) {
            ?>
            <tr>
                <td><?= $absen->absen_reg_date; ?></td>
                <td><?= Generic::printerKelas($absen->absen_kelas_id); ?></td>
                <td><?= Generic::getGuruNamebyID($absen->absen_guru_id); ?></td>
                <td><?
                    $acc = new Account();
                    $acc->getByID($absen->absen_pengabsen_id);
                    echo $acc->admin_nama_depan;
                    ?></td>

            </tr>
            <?
        }
    }

    function murid_kelas()
    {
        $page = isset($_GET['page']) ? addslashes($_GET['page']) : 1;
        $limit = KEY::$LIMIT_PROFILE;
        $begin = ($page - 1) * $limit;
        $id = addslashes($_GET['id']);
        $mk = new MuridKelasMatrix();
        $arrMK = $mk->getWhere("murid_id='$id' AND active_status = 1 LIMIT $begin,$limit");
        $jumlahTotal = $mk->getJumlah("murid_id='$id'");
        $jumlahHalamanTotal = ceil($jumlahTotal / $limit);
        $kelas = new KelasWebModel();
        $t = time();
        ?>
        <section class="content-header">
            <h1>
                <div class="pull-right">
                    <button class="btn btn-default" onclick="back_to_profile_murid('<?= $id; ?>');">back to profile
                    </button>
                </div>
                Kelas <?= Generic::getMuridNamebyID($id); ?>
            </h1>

        </section>

        <section class="content">
            <table class="table table-striped table-responsive">
                <thead>
                <tr>
                    <th>
                        Hari dan Jam
                    </th>
                    <th>
                        Ruang
                    </th>
                    <th>
                        Level
                    </th>
                    <th>
                        Guru
                    </th>

                </tr>
                </thead>

                <tbody id='container_load_kelas'>
                <?
                foreach ($arrMK as $mk) {
                    $arrKelas = $kelas->getWhere("id_kelas='$mk->kelas_id'");
                    foreach ($arrKelas as $kelas) {
                        ?>
                        <tr>
                            <td>
                                <b><?= Generic::getWeekDay()[$kelas->hari_kelas]; ?>
                                    <?= date("h:i", strtotime($kelas->jam_mulai_kelas)); ?>
                                    - <?= date("h:i", strtotime($kelas->jam_akhir_kelas)); ?>
                                </b>
                            </td>
                            <td>
                                <?= $kelas->id_room; ?>
                            </td>
                            <td>
                                <?= Generic::getLevelNameByID($kelas->level); ?>
                            </td>
                            <td>
                                <?= Generic::getGuruNamebyID($kelas->guru_id); ?>
                            </td>
                        </tr>

                        <?
                    }
                }
                ?>
                </tbody>
            </table>
            <div class="text-center">
                <button class="btn btn-default" id="loadmore_kelas_<?= $t; ?>">Load more</button>
            </div>
            <script>
                var page_kelas = <?= $page; ?>;
                var total_page_kelas = <?= $jumlahHalamanTotal; ?>;
                $('#loadmore_kelas_<?= $t; ?>').click(function () {
                    if (page_kelas < total_page_kelas) {
                        page_kelas++;
                        $.get("<?= _SPPATH; ?>MuridWebHelper/murid_kelas_load?id=<?= $id; ?>&page=" + page_kelas, function (data) {
                            $("#container_load_kelas").append(data);
                        });
                        if (page_kelas > total_page_kelas)
                            $('#loadmore_kelas_<?= $t; ?>').hide();
                    } else {
                        $('#loadmore_kelas_<?= $t; ?>').hide();
                    }
                });
            </script>
        </section>
        <?
    }

    function murid_kelas_load()
    {
        $page = isset($_GET['page']) ? addslashes($_GET['page']) : 1;
        $limit = KEY::$LIMIT_PROFILE;
        $begin = ($page - 1) * $limit;
        $id = addslashes($_GET['id']);
        $mk = new MuridKelasMatrix();
        $arrMK = $mk->getWhere("murid_id='$id' AND active_status = 1 LIMIT $begin,$limit");
        $jumlahTotal = $mk->getJumlah("murid_id='$id'");
        $jumlahHalamanTotal = ceil($jumlahTotal / $limit);
        $kelas = new KelasWebModel();
        foreach ($arrMK as $mk) {
            $arrKelas = $kelas->getWhere("id_kelas='$mk->kelas_id'");
            foreach ($arrKelas as $kelas) {
                ?>
                <tr>
                    <td>
                        <b><?= Generic::getWeekDay()[$kelas->hari_kelas]; ?>
                            <?= date("h:i", strtotime($kelas->jam_mulai_kelas)); ?>
                            - <?= date("h:i", strtotime($kelas->jam_akhir_kelas)); ?>
                        </b>
                    </td>
                    <td>
                        <?= $kelas->id_room; ?>
                    </td>
                    <td>
                        <?= Generic::getLevelNameByID($kelas->level); ?>
                    </td>
                    <td>
                        <?= Generic::getGuruNamebyID($kelas->guru_id); ?>
                    </td>
                </tr>

                <?
            }
        }
    }

    function murid_history()
    {
        $id = addslashes($_GET['id']);
        $murid = new MuridModel();
        $murid->getByID($id);
        $page = isset($_GET['page']) ? addslashes($_GET['page']) : 1;
        $limit = KEY::$LIMIT_PROFILE;
        $begin = ($page - 1) * $limit;
        $mk = new MuridKelasMatrix();
        $arrKelasHistory = $mk->getWhere("murid_id = '$id' ORDER BY active_date DESC LIMIT $begin,$limit");
        $mj = new MuridJourney();
        $arrMuridJourney = $mj->getWhere("journey_murid_id = '$id' ORDER BY journey_mulai_date DESC LIMIT $begin,$limit");
        $jumlahTotal = $mk->getJumlah("murid_id='$id'");
        $jumlahHalamanTotal = ceil($jumlahTotal / $limit);
        $jumlahTotalMJ = $mj->getJumlah("journey_murid_id='$id'");
        $jumlahHalamanTotalMJ = ceil($jumlahTotalMJ / $limit);

        $statusMurid = new StatusHisMuridModel();
        $arrStatusMurid = $statusMurid->getWhere("status_murid_id='$id' ORDER by status_tanggal_mulai DESC");
        $jumlahTotalStatus = $statusMurid->getJumlah("journey_murid_id='$id'");
        $jumlahHalamanTotalStatus = ceil($jumlahTotalStatus / $limit);


        $t = time();
        ?>
        <section class="content-header">
            <h1>
                <div class="pull-right">
                    <button class="btn btn-default" onclick="back_to_profile_murid('<?= $id; ?>');">back to profile
                    </button>
                </div>
                History <?= $murid->nama_siswa; ?>
            </h1>

        </section>

        <section class="content">
            <div id="history_<?= $id; ?>">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a style="font-weight: bold; font-size: 15px;" href="#HKelas_<?= $id; ?>" data-toggle="tab">History
                            Kelas</a>
                    </li>
                    <li><a style="font-weight: bold; font-size: 15px;" href="#HLevel_<?= $id; ?>" data-toggle="tab">History
                            Level</a>
                    </li>
                    <li><a style="font-weight: bold; font-size: 15px;" href="#Status_<?= $id; ?>" data-toggle="tab">History
                            Status</a>
                    </li>
                </ul>

                <div class="tab-content ">
                    <div class="tab-pane active" id="HKelas_<?= $id; ?>"
                         style="background-color: #FFFFFF; padding:10px;     border: 1px solid #ddd; border-top: 0px;">
                        <div class="table-responsive" style="padding-top: 10px;">
                            <table class=" table table-striped table-bordered">
                                <thead>
                                <tr>
                                    <th>Hari/Jam</th>
                                    <th>Guru</th>
                                    <th>Level</th>
                                    <th>Status</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                </tr>
                                </thead>
                                <tbody id="container_load_history_kelas">
                                <?
                                foreach ($arrKelasHistory as $kls) {
                                    $kelas = new KelasWebModel();
                                    $kelas->getByID($kls->kelas_id);
                                    $guru = new SempoaGuruModel();
                                    $guru->getByID($kelas->guru_id);
                                    $level = new SempoaLevel();
                                    $level->getByID($kelas->level);
                                    ?>
                                    <tr>
                                        <td>
                                            <b><?= Generic::getWeekDay()[$kelas->hari_kelas]; ?>
                                                <?= date("h:i", strtotime($kelas->jam_mulai_kelas)); ?>
                                                - <?= date("h:i", strtotime($kelas->jam_akhir_kelas)); ?>
                                            </b>
                                        </td>
                                        <td>
                                            <?= $guru->nama_guru; ?>
                                        </td>
                                        <td>
                                            <?= $level->level; ?>
                                        </td>
                                        <td>
                                            <?
                                            if ($kls->active_status == 1)
                                                echo KEY::$AKTIV;
                                            else
                                                echo KEY::$NON_AKTIV;
                                            ?>
                                        </td>
                                        <td>
                                            <?= $kls->active_date; ?>
                                        </td>
                                        <td>
                                            <?
                                            if ($kls->nonactive_date == "1970-01-01 07:00:00")
                                                echo KEY::$SEKARANG;
                                            else
                                                echo $kls->nonactive_date;
                                            ?>
                                        </td>
                                    </tr>
                                    <?
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center">
                            <button class="btn btn-default" id="loadmore_History_kelas_<?= $t; ?>">Load more</button>
                        </div>
                        <script>
                            var page_history = <?= $page; ?>;
                            var total_page_history = <?= $jumlahHalamanTotal; ?>;
                            $('#loadmore_History_kelas_<?= $t; ?>').click(function () {
                                if (page_history < total_page_history) {
                                    page_history++;
                                    $.get("<?= _SPPATH; ?>MuridWebHelper/murid_history_kelas_load?id=<?= $id; ?>&page=" + page_history, function (data) {
                                        $("#container_load_history_kelas").append(data);
                                    });
                                    if (page_history > total_page_history)
                                        $('#loadmore_History_kelas_<?= $t; ?>').hide();
                                } else {
                                    $('#loadmore_History_kelas_<?= $t; ?>').hide();
                                }
                            });
                        </script>


                    </div>

                    <div class="tab-pane" id="HLevel_<?= $id; ?>"
                         style="background-color: #FFFFFF; padding:10px;border: 1px solid #ddd; border-top: 0px;">
                        <div class="table-responsive" style="padding-top: 10px;">
                            <table class="table table-striped table-bordered">
                                <thead>
                                <tr>
                                    <th>
                                        Level
                                    </th>
                                    <th>
                                        Mulai
                                    </th>
                                    <th>
                                        Sampai
                                    </th>
                                    <th>
                                        TC
                                    </th>
                                </tr>
                                </thead>
                                <tbody id='container_load_history_level'>
                                <?
                                foreach ($arrMuridJourney as $jour) {
                                    $level = new SempoaLevel();
                                    $level->getByID($jour->journey_level_mulai);
                                    ?>
                                    <tr>
                                        <td>
                                            <?= $level->level; ?>
                                        </td>
                                        <td>
                                            <?= $jour->journey_mulai_date; ?>
                                        </td>
                                        <td>
                                            <?
                                            if ($jour->journey_end_date == "1970-01-01 07:00:00")
                                                echo KEY::$SEKARANG;
                                            else
                                                echo $jour->journey_end_date;
                                            ?>
                                        </td>
                                        <td>
                                            <?= Generic::getTCNamebyID($jour->journey_tc_id); ?>
                                        </td>
                                    </tr>
                                <? } ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="text-center">
                            <button class="btn btn-default" id="loadmore_History_Level_<?= $t; ?>">Load more</button>
                        </div>
                        <script>
                            var page_level = <?= $page; ?>;
                            var total_page_level = <?= $jumlahHalamanTotalMJ; ?>;
                            $('#loadmore_History_Level_<?= $t; ?>').click(function () {
                                //                                alert("asas");
                                if (page_level < total_page_level) {
                                    page_level++;
                                    $.get("<?= _SPPATH; ?>MuridWebHelper/murid_history_kelas_level?id=<?= $id; ?>&page=" + page_level, function (data) {
                                        $("#container_load_history_level").append(data);
                                    });
                                    if (page_level > total_page_level)
                                        $('#loadmore_History_Level_<?= $t; ?>').hide();
                                } else {
                                    $('#loadmore_History_Level_<?= $t; ?>').hide();
                                }
                            });
                        </script>


                    </div>

                    <div class="tab-pane" id="Status_<?= $id; ?>"
                         style="background-color: #FFFFFF; padding:10px;border: 1px solid #ddd; border-top: 0px;">
                        <div class="table-responsive" style="padding-top: 10px;">
                            <table class="table table-striped table-bordered">
                                <thead>
                                <tr>

                                    <th>
                                        Status
                                    </th>
                                    <th>
                                        Tanggal Mulai
                                    </th>
                                    <th>
                                        Tanggal Akhir
                                    </th>
                                </tr>
                                </thead>
                                <tbody id='container_load_history_status'>
                                <?
                                $arrStatus = Generic::getAllStatusMurid();
                                foreach ($arrStatusMurid as $status) {
                                    $level = new SempoaLevel();
                                    $level->getByID($jour->journey_level_mulai);
                                    ?>
                                    <tr>

                                        <td>
                                            <?= $arrStatus[$status->status]; ?>
                                        </td>
                                        <td>
                                            <?= $status->status_tanggal_mulai; ?>
                                        </td>
                                        <td>

                                            <?
                                            if ($status->status_tanggal_akhir == "1970-01-01 07:00:00")
                                                echo "Sekarang";
                                            else
                                                echo $status->status_tanggal_akhir;
                                            ?>
                                        </td>

                                    </tr>
                                <? } ?>
                                </tbody>
                            </table>
                            <div class="text-center">
                                <button class="btn btn-default" id="loadmore_History_status_<?= $t; ?>">Load more
                                </button>
                            </div>
                            <script>
                                var page_status = <?= $page; ?>;
                                var total_status = <?= $jumlahHalamanTotalStatus; ?>;
                                $('#loadmore_History_status_<?= $t; ?>').click(function () {
                                    if (page_status < total_status) {
                                        page_status++;
                                        $.get("<?= _SPPATH; ?>MuridWebHelper/murid_history_status_load?id=<?= $id; ?>&page=" + page_status, function (data) {
                                            $("#container_load_history_status").append(data);
                                        });
                                        if (page_status > total_status)
                                            $('#loadmore_History_status_<?= $t; ?>').hide();
                                    } else {
                                        $('#loadmore_History_status_<?= $t; ?>').hide();
                                    }
                                });
                            </script>
                        </div>

                    </div>
                </div>

            </div>
        </section>
        <?
    }

    function murid_history_status_load()
    {
        $id = addslashes($_GET['id']);
        $murid = new MuridModel();
        $murid->getByID($id);
        $page = isset($_GET['page']) ? addslashes($_GET['page']) : 1;
        $limit = KEY::$LIMIT_PROFILE;
        $begin = ($page - 1) * $limit;

        $statusMurid = new StatusHisMuridModel();
        $arrStatusMurid = $statusMurid->getWhere("status_murid_id='$id' ORDER by status_tanggal_mulai DESC");
        $jumlahTotalStatus = $statusMurid->getJumlah("journey_murid_id='$id'");
        $jumlahHalamanTotalStatus = ceil($jumlahTotalStatus / $limit);

        $arrStatus = Generic::getAllStatusMurid();
        foreach ($arrStatusMurid as $status) {
            $level = new SempoaLevel();
            $level->getByID($jour->journey_level_mulai);
            ?>
            <tr>

                <td>
                    <?= $arrStatus[$status->status]; ?>
                </td>
                <td>
                    <?= $status->status_tanggal_mulai; ?>
                </td>
                <td>
                    <?
                    if ($status->status_tanggal_akhir == "1970-01-01 07:00:00")
                        echo "Sekarang";
                    else
                        echo $status->status_tanggal_akhir;
                    ?>
                </td>

            </tr>
            <?
        }
    }

    function murid_history_kelas_level()
    {
        $id = addslashes($_GET['id']);
        $murid = new MuridModel();
        $murid->getByID($id);
        $page = isset($_GET['page']) ? addslashes($_GET['page']) : 1;
        $limit = KEY::$LIMIT_PROFILE;
        $begin = ($page - 1) * $limit;
        $mj = new MuridJourney();
        $arrMuridJourney = $mj->getWhere("journey_murid_id = '$id' ORDER BY journey_mulai_date DESC LIMIT $begin,$limit");
        $jumlahTotalMJ = $mj->getJumlah("journey_murid_id='$id'");
        $jumlahHalamanTotalMJ = ceil($jumlahTotalMJ / $limit);
        $t = time();
//pr($jumlahHalamanTotalMJ);
//        pr($arrMuridJourney);
        foreach ($arrMuridJourney as $jour) {
            $level = new SempoaLevel();
            $level->getByID($jour->journey_level_mulai);
            ?>
            <tr>
                <td>
                    <?= $level->level; ?>
                </td>
                <td>
                    <?= $jour->journey_mulai_date; ?>
                </td>
                <td>
                    <?
                    if ($jour->journey_end_date == "1970-01-01 07:00:00")
                        echo KEY::$AKTIV;
                    else
                        echo $jour->journey_end_date;
                    ?>
                </td>
                <td>
                    <?= Generic::getTCNamebyID($jour->journey_tc_id); ?>
                </td>
            </tr>
            <?
        }
    }

    function murid_history_kelas_load()
    {
        $id = addslashes($_GET['id']);
        $murid = new MuridModel();
        $murid->getByID($id);
        $page = isset($_GET['page']) ? addslashes($_GET['page']) : 1;
        $limit = KEY::$LIMIT_PROFILE;
        $begin = ($page - 1) * $limit;
        $mk = new MuridKelasMatrix();
        $arrKelasHistory = $mk->getWhere("murid_id = '$id' ORDER BY active_date DESC LIMIT $begin,$limit");
        $jumlahTotal = $mk->getJumlah("murid_id='$id'");
        $jumlahHalamanTotal = ceil($jumlahTotal / $limit);
//        pr($arrKelasHistory);
        foreach ($arrKelasHistory as $kls) {
            $kelas = new KelasWebModel();
            $kelas->getByID($kls->kelas_id);
            $guru = new SempoaGuruModel();
            $guru->getByID($kelas->guru_id);
            $level = new SempoaLevel();
            $level->getByID($kelas->level);
            ?>
            <tr>
                <td>
                    <b><?= Generic::getWeekDay()[$kelas->hari_kelas]; ?>
                        <?= date("h:i", strtotime($kelas->jam_mulai_kelas)); ?>
                        - <?= date("h:i", strtotime($kelas->jam_akhir_kelas)); ?>
                    </b>
                </td>
                <td>
                    <?= $guru->nama_guru; ?>
                </td>
                <td>
                    <?= $level->level; ?>
                </td>
                <td>
                    <?
                    if ($kls->active_status == 1)
                        echo "Active";
                    else
                        echo "Non Active";
                    ?>
                </td>
                <td>
                    <?= $kls->active_date; ?>
                </td>
                <td>
                    <?
                    if ($kls->nonactive_date == "1970-01-01 07:00:00")
                        echo KEY::$SEKARANG;
                    else
                        echo $kls->nonactive_date;
                    ?>
                </td>
            </tr>
            <?
        }
    }

    function murid_invoices()
    {
        $page = isset($_GET['page']) ? addslashes($_GET['page']) : 1;
        $limit = KEY::$LIMIT_PROFILE;
        $begin = ($page - 1) * $limit;
        $id = addslashes($_GET['id']);
        $murid = new MuridModel();
        $murid->getByID($id);
//        $tc_id = AccessRight::getMyOrgID();
        $tc_id = AccessRight::getMyOrgID();
        $mk = new IuranBulanan();
        $arrMK = $mk->getWhere("bln_murid_id='$id' ORDER BY bln_tahun DESC,bln_mon DESC LIMIT $begin,$limit");
        $jumlahTotal = $mk->getJumlah("bln_murid_id='$id'");
        $jumlahHalamanTotal = ceil($jumlahTotal / $limit);
        $arrSTatus = array("<b>Unpaid</b>", "Paid");
        $kuponSatuan = new KuponSatuan();
        $arrkupon = $kuponSatuan->getWhere("kupon_owner_id = '$tc_id' AND kupon_status = 0 ORDER BY kupon_id ASC");

        $now = new DateTime($murid->tanggal_masuk);
        $month = $now->format('m');
        $year = $now->format('Y');
        $arrPembayaran = Generic::getJenisPembayaran();
        $t = time();

        if ($_GET['active_tab'] == "") $active_tab = "bulanan";
        else $active_tab = "buku";
        ?>
        <section class="content-header">
            <h1>
                <div class="pull-right">
                    <?
                    if (($murid->status == KEY::$STATUSMURIDAKTIV) AND (AccessRight::getMyOrgType() == KEY::$TC)) {
                        ?>
                        <button class="btn btn-default" id="create_invoice_bulanan_<?= $t; ?>">Buat Invoice Iuran
                            Bulanan
                        </button>
                        <?

                    }

                    if (($murid->status == KEY::$STATUSMURIDAKTIV) AND (AccessRight::getMyOrgType() == KEY::$IBO)) {
                        ?>
                        <button class="btn btn-default" id="create_invoice_bulanan_ibo_<?= $t; ?>">Buat Invoice Iuran
                            Bulanan
                        </button>
                        <?

                    }

                    ?>

                    <button class="btn btn-default" onclick="back_to_profile_murid('<?= $id; ?>');">back to profile
                    </button>

                </div>
                Invoices <?= Generic::getMuridNamebyID($id); ?>
            </h1>
            <script>

                $('#create_invoice_bulanan_ibo_<?= $t; ?>').click(function () {
                    var id_murid = <?= $id; ?>;
                    $('#modal_ibo .modal-body').load("<?= _SPPATH; ?>KelasWebHelper/create_invoice_ibo?id_murid=" + id_murid);
                    $('#modal_ibo').modal('show');

//                    $('#modal_bundle_kupon_selector').modal("show");
                });


                $('#create_invoice_bulanan_<?=$t;?>').click(function () {
                    if (confirm("Anda yakin akan mencetak Invoice untuk bulan selanjutnya?")) {
                        var id_murid = <?= $id; ?>;
                        $.get("<?= _SPPATH; ?>MuridWebHelper/create_invoice?id=" + id_murid, function (data2) {
                            console.log(data2);
                            alert(data2.status_message);
                            lwrefresh('murid_Invoices_<?=$id;?>');
                        }, 'json');
                    }
                });


            </script>

        </section>

        <section class="content">

            <div id="invoices_<?= $id; ?>">
                <ul class="nav nav-tabs">
                    <li class="<? if ($active_tab == "bulanan") {
                        ?>active<?
                    } ?>">
                        <a style="font-weight: bold; font-size: 15px;" href="#IuranSPP_<?= $id . $t; ?>"
                           data-toggle="tab">Iuran
                            Bulanan</a>
                    </li>
                    <li class="<? if ($active_tab == "buku") {
                        ?>active<?
                    } ?>"><a style="font-weight: bold; font-size: 15px;" href="#IuranBuku_<?= $id . $t; ?>"
                             data-toggle="tab">Iuran
                            Buku</a>
                    </li>
                </ul>

                <div class="tab-content ">

                    <div class="tab-pane <? if ($active_tab == "bulanan") {
                        ?>active<?
                    } ?>" id="IuranSPP_<?= $id . $t; ?>"
                         style="background-color: #FFFFFF; padding:10px;border: 1px solid #ddd; border-top: 0px;">
                        <div class="table-responsive">
                            <table class="table table-striped ">
                                <thead>
                                <tr>
                                    <th>
                                        Bulan
                                    </th>
                                    <th>
                                        Status
                                    </th>
                                    <th>
                                        Kupon
                                    </th>
                                    <th>
                                        Tanggal
                                    </th>
                                    <th>
                                        Jenis Pembayaran
                                    </th>
                                    <?
                                    if ((AccessRight::getMyOrgType() == KEY::$TC)) {
                                        ?>
                                        <th>
                                            Print
                                        </th>
                                        <?

                                    }

                                    ?>

                                    <?
                                    if (AccessRight::getMyOrgType() == KEY::$IBO) {
                                        ?>
                                        <th>
                                            Undo
                                        </th>
                                        <th>
                                            Delete
                                        </th>
                                        <?
                                    }
                                    ?>

                                </tr>
                                </thead>

                                <tbody id='container_load_invoices_<?= $t; ?>'>

                                <?
                                foreach ($arrMK as $mk) {
                                    $kuponSatuan->getByID($mk->bln_kupon_id);
                                    ?>
                                    <tr>
                                        <td><?= $mk->bln_date; ?></td>
                                        <td id="status_<?= $mk->bln_id; ?>">
                                            <?
                                            if ($mk->bln_status) {
                                                echo $arrSTatus[$mk->bln_status];
                                            } else {
                                                if (AccessRight::getMyOrgType() == "tc") {
                                                    ?>
                                                    <button id="payNow_<?= $mk->bln_id; ?>"
                                                            class="btn btn-default bBayar_<?= $mk->bln_id; ?>">Pay Now
                                                    </button>
                                                    <?
                                                } else {
                                                    echo $arrSTatus[$mk->bln_status];
                                                }
                                            }
                                            ?>

                                        </td>
                                        <style>
                                            .ui-autocomplete {
                                                z-index: = 10000000;
                                            }
                                        </style>
                                        <td class='kupon' id="no_kupon_<?= $mk->bln_id; ?>">
                                            <?
                                            if ($mk->bln_kupon_id != 0) {
                                                echo $mk->bln_kupon_id;
                                            } else {
                                                if (AccessRight::getMyOrgType() == 'tc') {
                                                    $arrkuponHlp = array();
                                                    foreach ($arrkupon as $kpn) {
                                                        $arrkuponHlp[] = $kpn->kupon_id;
                                                    }
//
                                                    ?>
                                                    <input type="text" id="kupon_name_t_<?= $mk->bln_id; ?>"/>
                                                    <script>
                                                        $(function () {
                                                            var availableTags = <? echo json_encode($arrkuponHlp);?>;
                                                            $("#kupon_name_t_<?= $mk->bln_id; ?>").autocomplete({
                                                                source: availableTags
                                                            });

                                                        });
                                                    </script>

                                                    <?
                                                }

                                            }
                                            ?>

                                        </td>
                                        <td id="tglpembayaran_<?= $mk->bln_id; ?>"><?
                                            //                                        pr($mk);
                                            if (($mk->bln_date_pembayaran != KEY::$TGL_KOSONG)) {
                                                echo $mk->bln_date_pembayaran;
                                            }
                                            ?></td>

                                        <td><?
                                            if ($mk->bln_status)
                                                echo $arrPembayaran[$mk->bln_cara_bayar];
                                            else {
                                                if (AccessRight::getMyOrgType() == "tc") {
                                                    ?>
                                                    <select id="jenis_pmbr_invoice_spp_<?= $mk->bln_id ?>">
                                                        <?
                                                        foreach ($arrPembayaran as $key => $by) {
                                                            ?>
                                                            <option value="<?= $key; ?>"><?= $by; ?></option>
                                                            <?
                                                        }
                                                        ?>
                                                    </select>
                                                    <?
                                                }
                                            }
                                            ?></td>
                                        <?
                                        if ((AccessRight::getMyOrgType() == KEY::$TC)) {
                                            ?>
                                            <td>
                                                <?
                                                if ($mk->bln_kupon_id == 0) {
//                                                echo $mk->bln_kupon_id;
                                                } else {


                                                    if (($mk->bln_date == $month . "-" . $year)) {
                                                        echo "<a target=\"_blank\" href=" . _SPPATH . "MuridWebHelper/printRegister2?id_murid=" . $id . "><span  style=\"vertical-align:middle\" class=\"glyphicon glyphicon-print\"  aria-hidden=\"true\"></span>
                                            </a>";
                                                    } else {
                                                        ?>
                                                        <a target="_blank"
                                                           href="<?= _SPPATH; ?>MuridWebHelper/printSPP2?nama=<?= Generic::getMuridNamebyID($id); ?>&id_murid=<?= $id; ?>&bln=<?= $mk->bln_date; ?>&id=<?= $mk->bln_kupon_id; ?>">

                                                        <span class="glyphicon glyphicon-print"
                                                              aria-hidden="true"></span>
                                                        </a>
                                                        <?

                                                    }


                                                }
                                                ?>
                                            </td>
                                            <?
                                        }
                                        ?>

                                        <td>
                                            <?
                                            if ((AccessRight::getMyOrgType() == KEY::$IBO) AND ($mk->bln_date_pembayaran != KEY::$TGL_KOSONG)) {
                                                $now = time();
                                                $your_date = strtotime($mk->bln_date_pembayaran);
                                                $datediff = $now - $your_date;
                                                $datediff = floor($datediff / (60 * 60 * 24));
                                                if ($datediff <= KEY::$MAX_UNDO_SPP) {
                                                    ?>
                                                    <span id="undo_<?= $mk->bln_id; ?>" class="fa fa-undo"
                                                          aria-hidden="true"></span>
                                                    <?
                                                }
                                            }
                                            ?>
                                        <td>
                                            <?
                                            if ((AccessRight::getMyOrgType() == KEY::$IBO)) {
                                            ?>
                                            <span id="delete_iuran_bulanan_<?= $mk->bln_id . "_" . $t; ?>"
                                                  class="fa fa-times"
                                                  aria-hidden="true"></span>
                                        </td>
                                    <?

                                    }

                                    ?>

                                        </td>
                                    </tr>
                                    <script>

                                        $('#delete_iuran_bulanan_<?= $mk->bln_id . "_" . $t; ?>').click(function () {
                                            var bln_id = '<?= $mk->bln_id; ?>';
                                            if (confirm("Apakah Anda Yakin akan menghapus transaksi Iuran Bulanan?")) {
                                                $.post("<?= _SPPATH; ?>LaporanWebHelper/hapusIuranBulanan", {
                                                        bln_id: bln_id
                                                    },
                                                    function (data) {
                                                        console.log(data);
                                                        alert(data.status_message);
                                                        if (data.status_code) {
                                                            lwrefresh(selected_page);
                                                            lwrefresh("Profile_Murid");
                                                        }
                                                    }, 'json');
                                            }
                                        });

                                        $('#undo_<?= $mk->bln_id; ?>').click(function () {
                                            var bln_id = '<?= $mk->bln_id; ?>';
                                            var kupon = $('#no_kupon_<?= $mk->bln_id; ?>').text();
//                                            alert(kupon);
                                            if (kupon != null) {
                                                if (confirm("Apakah Anda Yakin akan membatalkan transaksi Iuran Bulanan?"))
                                                    $.post("<?= _SPPATH; ?>LaporanWebHelper/undo_iuran_bulanan", {
                                                        lvl_murid:<?=$murid->id_level_sekarang;?>,
                                                        bln_id: bln_id,
                                                        kupon_id: kupon,
                                                        kupon_owner:<?=$murid->murid_tc_id;?>
                                                    }, function (data) {
                                                        console.log(data);
                                                        alert(data.status_message);
                                                        if (data.status_code) {
//                                                        $('.bBayar_<?//= $mk->bln_id; ?>//').hide();
//                                                        $('#pilih_kupon_<?//= $t; ?>//').attr("disabled", "true");
//                                                        $('#status_<?//= $mk->bln_id; ?>//').html("Paid");
//                                                        $('#tglpembayaran_<?//= $mk->bln_id; ?>//').html('<?//= leap_mysqldate(); ?>//');
                                                            lwrefresh(selected_page);
                                                            lwrefresh("Profile_Murid");
                                                        }
                                                    }, 'json');
                                            }
                                            else {
                                                alert("Kupon tidak tersedia!");
                                            }
                                        });

                                        <? if ($mk->bln_status) { ?>
                                        $('.bBayar_<?= $mk->bln_id; ?>').hide();
                                        <? } else {
                                        ?>
                                        $('.bBayar_<?= $mk->bln_id; ?>').show();
                                        <? }
                                        ?>
                                        $('#payNow_<?= $mk->bln_id; ?>').click(function () {
                                            $('#payNow_<?= $mk->bln_id; ?>').prop('disabled', true);
                                            var bln_id = '<?= $mk->bln_id; ?>';
                                            var jpb = $('#jenis_pmbr_invoice_spp_<?= $mk->bln_id ?>').val();
                                            var kupon = $('#kupon_name_t_<?= $mk->bln_id; ?>').val();
                                            if (kupon != null) {
                                                $.post("<?= _SPPATH; ?>LaporanWebHelper/update_iuran_bulanan", {
                                                    lvl_murid:<?=$murid->id_level_sekarang;?>,
                                                    bln_id: bln_id,
                                                    kupon_id: kupon,
                                                    jpb: jpb,
                                                    kupon_owner:<?=AccessRight::getMyOrgID();?>
                                                }, function (data) {
                                                    console.log(data);
                                                    alert(data.status_message);
                                                    if (data.status_code) {
                                                        $('.bBayar_<?= $mk->bln_id; ?>').hide();
                                                        $('#pilih_kupon_<?= $t; ?>').attr("disabled", "true");
                                                        $('#status_<?= $mk->bln_id; ?>').html("Paid");
                                                        $('#tglpembayaran_<?= $mk->bln_id; ?>').html('<?= leap_mysqldate(); ?>');
                                                        lwrefresh(selected_page);
                                                        lwrefresh("Profile_Murid");
                                                    }
                                                }, 'json');
                                            }
                                            else {
                                                alert("Kupon tidak tersedia!");
                                            }

                                        });
                                    </script>
                                    <?
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center">
                            <button class="btn btn-default" id="loadmore_invoices_<?= $t; ?>">Load more</button>
                        </div>
                        <script>
                            var page_invoices = <?= $page; ?>;
                            var total_page_invoices = <?= $jumlahHalamanTotal; ?>;
                            $('#loadmore_invoices_<?= $t; ?>').click(function () {
                                if (page_invoices < total_page_invoices) {
                                    page_invoices++;
                                    $.get("<?= _SPPATH; ?>MuridWebHelper/murid_invoices_load?id=<?= $id; ?>&page=" + page_invoices, function (data) {
                                        $("#container_load_invoices_<?=$t;?>").append(data);
                                    });
                                    if (page_invoices > total_page_invoices)
                                        $('#loadmore_invoices_<?= $t; ?>').hide();
                                } else {
                                    $('#loadmore_invoices_<?= $t; ?>').hide();
                                }
                            });
                        </script>
                    </div>


                    <!--// Tab ke 2-->
                    <?
                    $iuranBuku = new IuranBuku();
                    $arrIuranBuku = $iuranBuku->getWhere("bln_murid_id='$id' ORDER by bln_id DESC LIMIT $begin,$limit");
                    //                    pr($arrIuranBuku);
                    $jumlahTotal = $iuranBuku->getJumlah("bln_murid_id='$id'");
                    $jumlahHalamanTotalBuku = ceil($jumlahTotal / $limit);
                    $arrPembayaran = Generic::getJenisPembayaran();
                    ?>
                    <div class="tab-pane <? if ($active_tab == "buku") {
                        ?>active<?
                    } ?>" id="IuranBuku_<?= $id . $t; ?>"
                         style="background-color: #FFFFFF; padding:10px;border: 1px solid #ddd; border-top: 0px;">
                        <table class="table table-striped table-responsive">
                            <thead>
                            <tr>
                                <th>
                                    Tanggal
                                </th>
                                <th>
                                    Level
                                </th>
                                <th>
                                    Status
                                </th>
                                <th>
                                    No Buku
                                </th>
                                <th>
                                    Jenis Pembayaran
                                </th>
                                <?
                                if ((AccessRight::getMyOrgType() == KEY::$TC)) {
                                    ?>
                                    <th>
                                        Print
                                    </th>
                                    <?

                                }
                                ?>
                                <?
                                if (AccessRight::getMyOrgType() == KEY::$IBO) {
                                    ?>
                                    <th>
                                        Undo
                                    </th>
                                    <?
                                }
                                ?>
                                <?
                                if ((AccessRight::getMyOrgType() == KEY::$IBO)) {
                                    ?>
                                    <th>
                                        Delete
                                    </th>
                                    <?

                                }

                                ?>
                            </tr>
                            </thead>
                            <tbody id='container_load_history_iuranBuku_<?= $t; ?>'>
                            <?
                            foreach ($arrIuranBuku as $key => $val) {
                                ?>
                                <tr>
                                    <td><?= $val->bln_date_pembayaran; ?></td>
                                    <td><?= Generic::getLevelNameByID($val->bln_buku_level); ?></td>
                                    <td><?
                                        if ($val->bln_status)
                                            echo $arrSTatus[$val->bln_status];
                                        else {
                                            if (AccessRight::getMyOrgType() == "tc") {
                                                ?>
                                                <button class="btn btn-default belumbayar_<?= $val->bln_id; ?>"
                                                        id='pay_now_bulanan_<?= $val->bln_id . $t; ?>'>Pay Now
                                                </button>

                                                <?
                                            } else {
                                                echo "<b>Unpaid</b>";
                                            }
                                        }
                                        ?></td>
                                    <td>
                                        <?
                                        $stock = new StockBuku();
                                        $res = $stock->getBukuNoByInvoiceID($val->bln_id);
                                        if (count($res) > 0) {
                                            foreach ($res as $key => $nobuku) {
                                                echo $nobuku . "/" . $key . "<br>";
                                            }
                                        }

                                        ?>

                                    </td>
                                    <td><?
                                        if ($val->bln_status)
                                            echo $arrPembayaran[$val->bln_cara_bayar];
                                        else {
                                            if (AccessRight::getMyOrgType() == "tc") {
                                                ?>
                                                <select id="jenis_pmbr_invoice_<?= $val->bln_id ?>">
                                                    <?
                                                    foreach ($arrPembayaran as $key => $by) {
                                                        ?>
                                                        <option value="<?= $key; ?>"><?= $by; ?></option>
                                                        <?
                                                    }
                                                    ?>
                                                </select>
                                                <?
                                            }
                                        }
                                        ?></td>

                                    <?
                                    if ((AccessRight::getMyOrgType() == KEY::$TC)) {
                                        ?>
                                        <td>
                                            <?
                                            if ($val->bln_status == 0) {
//                                                echo $mk->bln_kupon_id;
                                            } else {
                                                if ($murid->id_level_masuk == $val->bln_buku_level) {
                                                    echo "<a target=\"_blank\" href=" . _SPPATH . "MuridWebHelper/printRegister2?id_murid=" . $id . "><span  style=\"vertical-align:middle\" class=\"glyphicon glyphicon-print\"  aria-hidden=\"true\"></span>
                                            </a>";
                                                } else {
                                                    ?>
                                                    <a target="_blank"
                                                       href="<?= _SPPATH; ?>MuridWebHelper/printBuku2?id_invoice=<?= $val->bln_id; ?>&nama=<?= Generic::getMuridNamebyID($id); ?>&id_murid=<?= $id; ?>&tgl=<?= $val->bln_date_pembayaran; ?>&level=<?= Generic::getLevelNameByID($val->bln_buku_level); ?>">

                                                        <span class="glyphicon glyphicon-print"
                                                              aria-hidden="true"></span>
                                                    </a>
                                                    <?
                                                }


                                            }
                                            ?>

                                        </td>
                                        <?

                                    }
                                    ?>
                                    <?
                                    if (AccessRight::getMyOrgType() == KEY::$IBO) {
                                        ?>
                                        <td>
                                            <?
                                            if (substr($val->bln_no_invoice, 0, 2) != KEY::$AWALAN_FIRST_PAYMENT) {
                                                $now = time();
                                                $your_date = strtotime($val->bln_date_pembayaran);
                                                $datediff = Generic::diffTwoDaysInDay($now, $your_date);
                                                if ($datediff <= KEY::$MAX_UNDO_IURAN_BUKU) {
                                                    ?>
                                                    <span id="undo_iuran_buku_<?= $val->bln_id; ?>" class="fa fa-undo"
                                                          aria-hidden="true"></span>
                                                    <?
                                                }
                                            }

                                            ?>
                                        </td>
                                        <?
                                    }
                                    ?>

                                    <?
                                    if (AccessRight::getMyOrgType() == KEY::$IBO) {
                                        ?>
                                        <td>

                                    <span id="delete_invoice_buku_<?= $val->bln_id . "_" . $t; ?>" class="fa fa-times"
                                          aria-hidden="true"></span>
                                        </td>
                                        <?
                                    } ?>

                                    <script>
                                        var bln_id = '<?= $val->bln_id; ?>';
                                        $('#delete_invoice_buku_<?= $val->bln_id . "_" . $t; ?>').click(function () {
                                            var bln_id = '<?= $val->bln_id; ?>';
                                            if (confirm("Apakah Anda Yakin akan menghapus transaksi Iuran Bulanan?")) {
//                                                alert(bln_id);
                                                $.post("<?= _SPPATH; ?>LaporanWebHelper/hapusIuranBuku", {
                                                    bln_id: bln_id
                                                }, function (data) {
                                                    console.log(data);
                                                    alert(data.status_message);
                                                    if (data.status_code) {
                                                        lwrefresh(selected_page);
                                                        lwrefresh("Profile_Murid");
                                                    }
                                                }, 'json');
                                            }
                                        });
                                        $("#undo_iuran_buku_<?= $val->bln_id; ?>").click(function () {
                                            var bln_id = '<?= $val->bln_id; ?>';
                                            if (confirm("Apakah Anda Yakin akan membatalkan transaksi Iuran Bulanan?" + bln_id)) {
                                                $.post("<?= _SPPATH; ?>LaporanWebHelper/undo_iuran_buku_2", {
                                                    bln_id: bln_id
                                                }, function (data) {
                                                    console.log(data);
                                                    alert(data.status_message);
                                                    if (data.status_code) {
                                                        lwrefresh(selected_page);
                                                        lwrefresh("Profile_Murid");
                                                    }
                                                }, 'json');
                                            }
                                            //kosongkan status di iuran bulanan
                                            //
                                        });
                                        $('#undo_<?= $val->bln_id . $t; ?>').click(function () {
                                        });

                                        <?
                                        if ($val->bln_status) {
                                        ?>
                                        $('#belumbayar_<?= $val->bln_id; ?>').hide();
                                        <?
                                        } else {
                                        ?>
                                        $('#belumbayar_<?= $val->bln_id; ?>').show();
                                        <?
                                        }
                                        ?>
                                        $('#pay_now_bulanan_<?= $val->bln_id . $t; ?>').click(function () {
                                            var jpb = $('#jenis_pmbr_invoice_<?= $val->bln_id ?>').val();
                                            var bln_id = <?= $val->bln_id; ?>;
                                            $.post("<?= _SPPATH; ?>LaporanWebHelper/pay_iuran_buku_roy", {
                                                    bln_id: bln_id,
                                                    cara_pby: jpb
                                                },
                                                function (data) {
                                                    alert(data.status_message);
                                                    if (data.status_code) {
                                                        $('.td .belumbayar_<?= $val->bln_id; ?>').hide();
                                                        $('.sudahbayar').show();
                                                        $('#jenis_pmbr_invoice_<?= $val->bln_id ?>').attr("disabled", "true");
                                                        lwrefresh(selected_page);
                                                        // Refresh profile muridnya
                                                        lwrefresh("Profile_Murid");
                                                    } else {
                                                    }
                                                    console.log(data);
                                                    //                                                                $('#balikan_<? //= $val->bln_id;                                                                                                                          ?>//').html(data);
                                                }, 'json');
                                        });
                                        //


                                    </script>
                                </tr>
                                <?
                            }
                            ?>
                            </tbody>
                        </table>
                        <div class="text-center">
                            <button class="btn btn-default" id="loadmore_iuranBuku_<?= $val->bln_murid_id; ?>">Load
                                more
                            </button>
                        </div>
                        <script>
                            var page_buku = <?= $page; ?>;
                            var total_page_buku = <?= $jumlahHalamanTotalBuku; ?>;
                            $('#loadmore_iuranBuku_<?= $val->bln_murid_id; ?>').click(function () {
                                if (page_buku < total_page_buku) {
                                    page_buku++;
                                    $.get("<?= _SPPATH; ?>MuridWebHelper/murid_iuranBuku_load?id=<?= $id; ?>&page=" + page_buku, function (data) {
                                        $("#container_load_history_iuranBuku_<?=$t;?>").append(data);
                                    });
                                    if (page_buku > total_page_buku)
                                        $('#loadmore_iuranBuku_<?= $val->bln_murid_id; ?>').hide();
                                } else {
                                    $('#loadmore_iuranBuku_<?= $val->bln_murid_id; ?>').hide();
                                }
                            });
                        </script>
                    </div>

                </div>

            </div>

            </div>


        </section>
        <?
    }


    function murid_invoices_load()
    {
        $page = isset($_GET['page']) ? addslashes($_GET['page']) : 1;
        $tc_id = AccessRight::getMyOrgID();
        $limit = KEY::$LIMIT_PROFILE;
        $begin = ($page - 1) * $limit;
        $id = addslashes($_GET['id']);
        $mk = new IuranBulanan();
        $arrMK = $mk->getWhere("bln_murid_id='$id' ORDER BY bln_tahun DESC,bln_mon DESC LIMIT $begin,$limit");
        $jumlahTotal = $mk->getJumlah("bln_murid_id='$id'");
        $jumlahHalamanTotal = ceil($jumlahTotal / $limit);
        $arrSTatus = array("<b>Unpaid</b>", "Paid");
        $kuponSatuan = new KuponSatuan();
        $arrkupon = $kuponSatuan->getWhere("kupon_owner_id = '$tc_id' AND kupon_status = 0 ORDER BY kupon_id ASC");
        $t = time();
        $murid = new MuridModel();
        $murid->getByID($id);
        $now = new \DateTime($murid->tanggal_masuk);
        $month = $now->format('m');
        $year = $now->format('Y');
        $arrPembayaran = Generic::getJenisPembayaran();
        foreach ($arrMK as $mk) {
            $kuponSatuan->getByID($mk->bln_kupon_id);
            ?>
            <tr>
                <td><?= $mk->bln_date; ?></td>
                <td id="status_<?= $mk->bln_id; ?>">
                    <?
                    if ($mk->bln_status) {
                        echo $arrSTatus[$mk->bln_status];
                    } else {
                        if (AccessRight::getMyOrgType() == "tc") {
                            ?>
                            <button id="payNow_<?= $mk->bln_id; ?>" class="btn btn-default bBayar">Pay Now</button>
                            <?
                        } else {
                            echo "<b>Unpaid</b>";
                        }

                    }
                    ?>

                </td>
                <style>
                    .ui-autocomplete {
                        z-index: = 10000000;
                    }
                </style>
                <td class='kupon' id="no_kupon_<?= $mk->bln_id; ?>">
                    <?
                    if ($mk->bln_status) {
                        echo $mk->bln_kupon_id;
                    } else {
                        if (AccessRight::getMyOrgType() == "tc") {
                            $arrkuponHlp = array();
                            foreach ($arrkupon as $kpn) {
                                $arrkuponHlp[] = $kpn->kupon_id;
                            }
                            ?>
                            <input type="text" id="kupon_name_t_<?= $mk->bln_id; ?>"/>
                            <script>
                                $(function () {
                                    var availableTags = <? echo json_encode($arrkuponHlp);?>;
                                    $("#kupon_name_t_<?= $mk->bln_id; ?>").autocomplete({
                                        source: availableTags
                                    });

                                });
                            </script>
                            <?
                        }

                    }
                    ?>

                </td>

                <td id="tglpembayaran_<?= $mk->bln_id; ?>"><?
                    //                                        pr($mk);
                    if (($mk->bln_date_pembayaran != KEY::$TGL_KOSONG)) {
                        echo $mk->bln_date_pembayaran;
                    }
                    ?></td>

                <td><?
                    if ($mk->bln_status)
                        echo $arrPembayaran[$mk->bln_cara_bayar];
                    else {
                        if (AccessRight::getMyOrgType() == "tc") {
                            ?>
                            <select id="jenis_pmbr_invoice_spp_<?= $mk->bln_id; ?>">
                                <?
                                foreach ($arrPembayaran as $key => $by) {
                                    ?>
                                    <option value="<?= $key; ?>"><?= $by; ?></option>
                                    <?
                                }
                                ?>
                            </select>
                            <?
                        }
                    }
                    ?></td>

                <?
                if ((AccessRight::getMyOrgType() == KEY::$TC)) {
                    ?>

                    <td>
                        <?
                        if ($mk->bln_kupon_id == 0) {
//                                                echo $mk->bln_kupon_id;
                        } else {

                            if (($mk->bln_date == $month . "-" . $year)) {
                                echo "<a target=\"_blank\" href=" . _SPPATH . "MuridWebHelper/printRegister2?id_murid=" . $id . "><span  style=\"vertical-align:middle\" class=\"glyphicon glyphicon-print\"  aria-hidden=\"true\"></span>
                                            </a>";
                            } else {
                                ?>
                                <a target="_blank"
                                   href="<?= _SPPATH; ?>MuridWebHelper/printSPP2?nama=<?= Generic::getMuridNamebyID($id); ?>&id_murid=<?= $id; ?>&bln=<?= $mk->bln_date; ?>&id=<?= $mk->bln_kupon_id; ?>">

                                    <span class="glyphicon glyphicon-print" aria-hidden="true"></span>
                                </a>
                                <?

                            }


                        }
                        ?>
                    </td>
                    <?

                }
                ?>


                <td>
                    <?
                    if ((AccessRight::getMyOrgType() == KEY::$IBO) AND ($mk->bln_date_pembayaran != KEY::$TGL_KOSONG)) {
                        $now = time();
                        $your_date = strtotime($mk->bln_date_pembayaran);
                        $datediff = $now - $your_date;
                        $datediff = floor($datediff / (60 * 60 * 24));
                        if ($datediff <= KEY::$MAX_UNDO_SPP) {
                            ?>
                            <span id="undo_<?= $mk->bln_id; ?>" class="fa fa-undo"
                                  aria-hidden="true"></span>
                            <?
                        }


                    }

                    ?>


                </td>
            </tr>
            <script>


                $('#undo_<?= $mk->bln_id; ?>').click(function () {
                    var bln_id = '<?= $mk->bln_id; ?>';
                    var kupon = $('#no_kupon_<?= $mk->bln_id; ?>').text();
                    if (kupon != null) {
                        if (confirm("yakin?"))
                            $.post("<?= _SPPATH; ?>LaporanWebHelper/undo_iuran_bulanan", {
                                lvl_murid:<?=$murid->id_level_sekarang;?>,
                                bln_id: bln_id,
                                kupon_id: kupon,
                                kupon_owner:<?=$murid->murid_tc_id;?>
                            }, function (data) {
                                console.log(data);
                                alert(data.status_message);
                                if (data.status_code) {
                                    lwrefresh(selected_page);
                                    lwrefresh("Profile_Murid");
                                }
                            }, 'json');
                    }
                    else {
                        alert("Kupon tidak tersedia!");
                    }
                });
                <? if ($mk->bln_status) { ?>
                $('.td .bBayar').hide();
                <? } else {
                ?>
                $('.td .bBayar').show();
                <? }
                ?>
                $('#payNow_<?= $mk->bln_id; ?>').click(function () {
                    var bln_id = '<?= $mk->bln_id; ?>';
                    var kupon = $('#kupon_name_t_<?= $mk->bln_id; ?>').val();
                    var jpb = $('#jenis_pmbr_invoice_spp_<?= $mk->bln_id ?>').val();
                    $.post("<?= _SPPATH; ?>LaporanWebHelper/update_iuran_bulanan", {
                        lvl_murid:<?=$murid->id_level_sekarang;?>,
                        bln_id: bln_id,
                        kupon_id: kupon,
                        jpb: jpb,
                        kupon_owner:<?=AccessRight::getMyOrgID();?>

                    }, function (data) {
                        console.log(data);
                        alert(data.status_message);
                        if (data.status_code) {
                            $('.bBayar').hide();
                            $('#pilih_kupon_<?= $t; ?>').attr("disabled", "true");
                            $('#status_<?= $mk->bln_id; ?>').html("Paid");
                            $('#tglpembayaran_<?= $mk->bln_id; ?>').html('<?= leap_mysqldate(); ?>');
                            lwrefresh(selected_page);
                            lwrefresh("Profile_Murid");
                        }
                    }, 'json');
                });

            </script>
            <?
        }
    }

    public function create_invoice_cronjobAllTC()
    {
        $tc_id = isset($_GET['tc_id']) ? addslashes($_GET['tc_id']) : die("please insert tc id");
        $bln = isset($_GET['bln']) ? addslashes($_GET['bln']) : date("n");
        $thn = isset($_GET['thn']) ? addslashes($_GET['thn']) : date("Y");
        $ibo_id = Generic::getMyParentID($tc_id);
        $kpo = Generic::getMyParentID($ibo_id);
        $ak = Generic::getMyParentID($kpo);
        $murid = new MuridModel();
        $arrMurid = $murid->getWhere("status = 1 OR status = 2");
        $total = 0;
        foreach ($arrMurid as $mur) {
//            pr($mur);
            $iur = new IuranBulanan();
            $cnt = $iur->getJumlah("bln_murid_id = '{$mur->id_murid}' AND bln_mon = '$bln' AND bln_tahun = '$thn'");
            if ($cnt > 0)
                continue;
            $iur->bln_murid_id = $mur->id_murid;
            $iur->bln_date = $bln . "-" . $thn;
            $iur->bln_mon = $bln;
            $iur->bln_tahun = $thn;
            $iur->bln_tc_id = $mur->murid_tc_id;
            $iur->bln_kpo_id = $mur->murid_kpo_id;
            $iur->bln_ibo_id = $mur->murid_ibo_id;
            $iur->bln_ak_id = $mur->murid_ak_id;
            if ($iur->save()) {
                $total++;
            }
        }
        echo $total;
    }

    function create_invoice_cronjob()
    {
        $tc_id = isset($_GET['tc_id']) ? addslashes($_GET['tc_id']) : die("please insert tc id");
        $bln = isset($_GET['bln']) ? addslashes($_GET['bln']) : date("n");
        $thn = isset($_GET['thn']) ? addslashes($_GET['thn']) : date("Y");
        $ibo_id = Generic::getMyParentID($tc_id);
        $kpo = Generic::getMyParentID($ibo_id);
        $ak = Generic::getMyParentID($kpo);
        $murid = new MuridModel();
        $arrMurid = $murid->getWhere("status = 1 OR status = 2");
        $total = 0;
        foreach ($arrMurid as $mur) {
            $iur = new IuranBulanan();
            $cnt = $iur->getJumlah("bln_murid_id = '{$mur->id_murid}' AND bln_mon = '$bln' AND bln_tahun = '$thn'");
            if ($cnt > 0)
                continue;
            $iur->bln_murid_id = $mur->id_murid;
            $iur->bln_date = $bln . "-" . $thn;
            $iur->bln_mon = $bln;
            $iur->bln_tahun = $thn;
            $iur->bln_tc_id = $tc_id;
            $iur->bln_kpo_id = $kpo;
            $iur->bln_ibo_id = $ibo_id;
            $iur->bln_ak_id = $ak;
            if ($iur->save()) {
                $total++;
            }
        }
        echo $total;
    }

    function viewMuridTC()
    {
        $status = isset($_GET['status']) ? addslashes($_GET['status']) : 1;
        $page = isset($_GET['page']) ? addslashes($_GET['page']) : 1;
        $limit = KEY::$LIMIT_PAGE;
        $begin = ($page - 1) * $limit;
        $IBOid = AccessRight::getMyOrgID();
        $arrMyTC = Generic::getAllMyTC($IBOid);
        $tcid = $_GET['tc_id'];
        $obj = new MuridModel();
        $arrMurid = $obj->getWhere("murid_tc_id=$tcid AND status=$status ORDER BY nama_siswa ASC  LIMIT $begin, $limit");
        $jumlahTotal = $obj->getJumlah("murid_tc_id=$tcid AND status=$status ");
        $jumlahHalamanTotal = ceil($jumlahTotal / $limit);

        $arrStatusMurid = Generic::getAllStatusMurid();
        $arrJenisKelamin = Generic::getJeniskelamin();
        $arrLevel = Generic::getAllLevel();
        $t = time();
        $return['classname'] = __FUNCTION__;
        $return['page'] = $page;
        $return['totalpage'] = $jumlahHalamanTotal;
        $return['perpage'] = $limit;
        $return['status'] = $status;
        $return['tc_id'] = $tcid;

        $index = (($page - 1) * 20) + 1;
        ?>
        <div id='container_tc_<?= $t; ?>'">
        <section class="content-header">
            <div class="pull-right" style="font-size: 13px;">
                <label for="exampleInputName2">Pilih TC:</label>
                <select id="pilih_tc_<?= $t; ?>">

                    <?

                    foreach ($arrMyTC as $key => $val) {
                        if ($tcid == $key) {
                            $selected = "selected";
                        } else {
                            $selected = "";
                        }
                        ?>
                        <option value="<?= $key; ?>" <?= $selected; ?>><?= $val; ?></option>
                        <?
                    }
                    ?>
                </select>
                <label for="exampleInputName2">Status:</label>
                <select id="pilih_status_<?= $t; ?>">

                    <?
                    foreach ($arrStatusMurid as $key => $val) {
                        if ($key == $status) {
                            $selected = "selected";
                        } else {
                            $selected = "";
                        }

                        ?>
                        <option value="<?= $key; ?>" <?= $selected; ?>><?= $val; ?></option>
                        <?
                    }
                    ?>
                </select>
                <button id="submit_pilih_tc_<?= $t; ?>">Submit</button>
                <script>
                    $('#submit_pilih_tc_<?= $t; ?>').click(function () {
                        var slc = $('#pilih_tc_<?= $t; ?>').val();

                        var status = $('#pilih_status_<?= $t; ?>').val();
//                        alert(slc  + "-" + status);
                        $('#container_tc_<?= $t; ?>').load("<?= _SPPATH; ?>MuridWebHelper/viewMuridTC?tc_id=" + slc + "&status=" + status, function () {
                        }, 'json');
                    });

                </script>
            </div>


        </section>
        <div class="clearfix"></div>
        <section class="content">
            <table class='table table-bordered table-striped '>
                <thead>
                <tr>
                    <th>No.</th>
                    <th>Kode Siswa</th>
                    <th>Nama Siswa</th>
                    <th>Status</th>
                    <th>Jenis Kelamin</th>
                    <th>Level</th>
                    <th>Profile</th>
                </tr>
                </thead>
                <tbody>
                <?
                foreach ($arrMurid as $key => $murid) {
                    ?>
                    <tr id='<?= $murid->id_murid; ?>'>
                        <td>
                            <?= $index; ?>
                        </td>
                        <td>
                            <?= $murid->kode_siswa ?>
                        </td>
                        <td>
                            <?= $murid->nama_siswa ?>
                        </td>
                        <td><?= $arrStatusMurid[$murid->status]; ?></td>
                        <td>
                            <?= $arrJenisKelamin[$murid->jenis_kelamin];
                            ?>
                        </td>
                        <td>
                            <?= Generic::getLevelNameByID($murid->id_level_sekarang); ?>
                        </td>
                        <td>
                            <button
                                onclick="openLw('Profile_Murid', '<?= _SPPATH; ?>MuridWebHelper/profile?id_murid=<?= $murid->id_murid; ?>', 'fade');">
                                Profile
                            </button>
                        </td>
                    </tr>
                    <?
                    $index++;
                }
                ?>
                </tbody>
            </table>
            <?
            $webClass = __CLASS__;
            Generic::pagination($return, $webClass);

            ?>
        </section>>
        </div>
        <?
    }

    function viewMuridTC_tmp()
    {
        $page = isset($_GET['page']) ? addslashes($_GET['page']) : 1;
        $limit = KEY::$LIMIT_PROFILE;
        $begin = ($page - 1) * $limit;
        $tcid = (isset($_GET['tc_id']) ? addslashes($_GET['tc_id']) : key($arrMyTC));
        $obj = new MuridModel();
        $arrMurid = $obj->getWhere("murid_tc_id='$tcid' LIMIT $begin, $limit");
        $jumlahTotal = $obj->getJumlah("murid_tc_id='$tcid'");
        $jumlahHalamanTotal = ceil($jumlahTotal / $limit);
        $t = time();
        ?>

        <table class='table table-bordered table-striped '>
            <thead>
            <tr>
                <th>ID</th>
                <th>Kode Siswa</th>
                <th>Nama Siswa</th>
                <th>Jenis Kelamin</th>
                <th>Level</th>
                <th>Profile</th>
            </tr>
            </thead>
            <tbody id="container_load_murid">
            <?
            foreach ($arrMurid as $key => $murid) {
                ?>
                <tr id='<?= $murid->id_murid; ?>'>
                    <td>
                        <?= $murid->id_murid ?>
                    </td>
                    <td>
                        <?= $murid->kode_siswa ?>
                    </td>
                    <td>
                        <?= $murid->nama_siswa ?>
                    </td>
                    <td>
                        <?
                        if ($murid->jenis_kelamin == 'm')
                            echo "Male";
                        else
                            echo "Female";
                        ?>
                    </td>
                    <td>
                        <?= Generic::getLevelNameByID($murid->id_level_sekarang); ?>
                    </td>
                    <td>
                        <button
                            onclick="openLw('Profile_Murid', '<?= _SPPATH; ?>MuridWebHelper/profile?id_murid=<?= $murid->id_murid; ?>', 'fade');">
                            Profile
                        </button>
                    </td>
                </tr>
                <?
            }
            ?>
            </tbody>
        </table>
        <div class="text-center">
            <button class="btn btn-default" id="loadmore_murid_<?= $t; ?>">Load more</button>
        </div>

        <script>
            var page_murid = <?= $page; ?>;
            var total_murid = <?= $jumlahHalamanTotal; ?>;
            $('#loadmore_murid_<?= $t; ?>').click(function () {
                if (page_murid < total_murid) {
                    page_murid++;
                    $.get("<?= _SPPATH; ?>MuridWebHelper/murid_tc_load?tc_id=<?= $tcid; ?>&page=" + page_murid, function (data) {
                        $("#container_load_murid").append(data);
                    });
                    if (page_murid > total_murid)
                        $('#loadmore_murid_<?= $t; ?>').hide();
                } else {
                    $('#loadmore_murid_<?= $t; ?>').hide();
                }
            });
        </script>
        <?
    }

    function murid_tc_load()
    {
        $page = isset($_GET['page']) ? addslashes($_GET['page']) : 1;
        $limit = KEY::$LIMIT_PROFILE;
        $begin = ($page - 1) * $limit;
        $tcid = (isset($_GET['tc_id']) ? addslashes($_GET['tc_id']) : key($arrMyTC));
        $obj = new MuridModel();
        $arrMurid = $obj->getWhere("murid_tc_id='$tcid' LIMIT $begin, $limit");
        $jumlahTotal = $obj->getJumlah("murid_tc_id='$tcid'");
        $jumlahHalamanTotal = ceil($jumlahTotal / $limit);
        $arrJenisKelamin = Generic::getJeniskelamin();
        $t = time();
        foreach ($arrMurid as $key => $murid) {
            ?>
            <tr id='<?= $murid->id_murid; ?>'>
                <td>
                    <?= $murid->id_murid ?>
                </td>
                <td>
                    <?= $murid->kode_siswa ?>
                </td>
                <td>
                    <?= $murid->nama_siswa ?>
                </td>
                <td>
                    <?= $arrJenisKelamin[$murid->jenis_kelamin];

                    ?>
                </td>
                <td>
                    <?= Generic::getLevelNameByID($murid->id_level_sekarang); ?>
                </td>
                <td>
                    <button
                        onclick="openLw('Profile_Murid', '<?= _SPPATH; ?>/MuridWebHelper/profile?id_murid=<?= $murid->id_murid; ?>', 'fade');">
                        Profile
                    </button>
                </td>
            </tr>
            <?
        }
    }

    function guru_insentif()
    {
        $id = addslashes($_GET['id']);
        $guru = new SempoaGuruModel();
        $guru->getByID($id);
        $level = new SempoaLevel();
        $arrlevel = $level->getAll();
        $bln = isset($_GET['bln']) ? addslashes($_GET['bln']) : date("n");
        $thn = isset($_GET['thn']) ? addslashes($_GET['thn']) : date("Y");
//        echo $_GET['bln']." ".$_GET['thn'];
        $begin_date = $thn . "-" . $bln . "-02";
        $end_date = $thn . "-" . $bln . "-" . cal_days_in_month(CAL_GREGORIAN, $bln, $thn);
        $t = time();
        $arrBulan = Generic::getAllMonths();
        ?>
        <section class="content-header">
            <h1>

                <div class="pull-right" style="font-size: 13px;">
                    Bulan :<select id="guru_insentif_bulan_<?= $t; ?>">
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

                    Tahun :<select id="guru_insentif_tahun_<?= $t; ?>">
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
                    <button id="guru_insentif_button_<?= $t; ?>">submit</button>
                    <button class="btn btn-default" onclick="back_to_profile_guru('<?= $id; ?>');">back to profile
                    </button>
                </div>
                Kelas <?= $guru->nama_guru; ?>
                <script>
                    $('#guru_insentif_button_<?= $t; ?>').click(function () {
                        var bln = $('#guru_insentif_bulan_<?= $t; ?>').val();
                        var thn = $('#guru_insentif_tahun_<?= $t; ?>').val();
                        openLw('guru_insentif_<?= $id; ?>', '<?= _SPPATH; ?>MuridWebHelper/guru_insentif?id=<?= $id; ?>&bln=' + bln + '&thn=' + thn + "&now=" + $.now(), 'fade');
                    });
                </script>
                </div>
                Murid dari <?= $guru->nama_guru; ?>
            </h1>

        </section>

        <section class="content">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>Level</th>
                        <th>Jumlah Murid</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?
                    global $db;
                    //ambil murid dari history yang masi aktif dalam bulan x
                    foreach ($arrlevel as $lvl) {
                        $mk = new MuridKelasMatrix();
                        $q = "SELECT COUNT(DISTINCT murid_id) AS cnt FROM {$mk->table_name} WHERE guru_id = '$id' AND level_murid = '{$lvl->id_level}' AND ( active_date <= '$begin_date' AND (nonactive_date >= '$end_date' OR nonactive_date = '1970-01-01 07:00:00'))";
                        $cnt = $db->query($q, 1);
//                        $jml = $mk->getJumlah("guru_id = '$id' AND level_murid = '{$lvl->id_level}' AND ( active_date <= '$begin_date' AND (nonactive_date >= '$end_date' OR nonactive_date = '1970-01-01 07:00:00'))");
                        if ($cnt->cnt < 1)
                            $cnt->cnt = 0;
                        ?>
                        <tr>

                            <td><?= $lvl->level; ?></td>
                            <td><?= $cnt->cnt; ?> </td>
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

    public function naik_kelas()
    {

        $id_murid = addslashes($_GET['id_murid']);
        $id_level = addslashes($_GET['id_level']);
        $kur = addslashes($_GET['kur']);
        $gantiKur = addslashes($_GET['gantiKur']);

        $json = array();

        // Cek, apakah masih ada invoice buku yang belum di bayar
        $objIuranBuku = new IuranBuku();
        $jumlahIuran = $objIuranBuku->getJumlah("bln_murid_id='$id_murid'  AND bln_status=0");

        if ($jumlahIuran > 0) {
            $json['status_code'] = 0;
            $json['status_message'] = "Masih ada taggihan buku yang belum dibayar!";
            echo json_encode($json);
            die();
        }


        // Check Kurikulum
        if ($kur == KEY::$KURIKULUM_LAMA) {
            $objIuranBuku = new IuranBuku();


            // Ganti Kurikulum lama ke Kurikulum Baru
            if ($gantiKur == 1) {
//                $next_level = Generic::getMyNextLevel($id_level);
                $next_level = Generic::getMyNextLevelKurLamaSpezial($id_level);
                if (is_null($next_level->id_level)) {
                    $json['status_code'] = 0;
                    $json['status_message'] = "Tidak bisa naik ke level!";
                    echo json_encode($json);
                    die();
                }
                $cnt = $objIuranBuku->getJumlah("bln_murid_id='$id_murid' AND bln_buku_level= '$next_level->id_level'");
            }

            // Tidak ganti kurikulum
            // Ikut level Kurikulum baru
            // Buku level lama
            else {
                $next_level = Generic::getMyNextLevel($id_level);
                $id_level_lama = Generic::convertLevelBaruKeLama($id_level);
                $id_next_level_lama = Generic::getMyNextLevelLama($id_level_lama);
//                pr($id_level_lama . " " . $id_next_level_lama->id_level_lama);
                // hitung buku lama
                $cnt = $objIuranBuku->getJumlah("bln_murid_id='$id_murid' AND bln_buku_level= '$next_level->id_level'");

            }


        } // Kurikulum Baru
        else {
            $next_level = Generic::getMyNextLevel($id_level);
            $objIuranBuku = new IuranBuku();
            $cnt = $objIuranBuku->getJumlah("bln_murid_id='$id_murid' AND bln_buku_level= '$next_level->id_level'");
        }


        if ($cnt > 0) {
//             continue;
            $json['status_code'] = 0;
            $json['status_message'] = "Invoice sdh tercetak";
            echo json_encode($json);
            die();
        } else {

            $objMurid = new MuridModel();
            $objMurid->getByID($id_murid);
            $bln = date("n");
            $thn = date("Y");
            $objIuranBuku->bln_murid_id = $objMurid->id_murid;
            $objIuranBuku->bln_date_pembayaran = leap_mysqldate();
            $objIuranBuku->bln_date = $bln . "-" . $thn;
            $objIuranBuku->bln_mon = $bln;
            $objIuranBuku->bln_tahun = $thn;
            $objIuranBuku->bln_tc_id = $objMurid->murid_tc_id;
            $objIuranBuku->bln_kpo_id = $objMurid->murid_kpo_id;
            $objIuranBuku->bln_ibo_id = $objMurid->murid_ibo_id;
            $objIuranBuku->bln_ak_id = $objMurid->murid_ak_id;

            // Kurikulum lama

            if ($kur == KEY::$KURIKULUM_LAMA) {
                //berarti Anda memilih buku Kurikulum baru dan kurikulum akan disesuaikan
                if ($gantiKur == 1) {
                    $objIuranBuku->bln_buku_level = $next_level->id_level;
                } else {

                    $objIuranBuku->bln_buku_level = $next_level->id_level;
                }

            } else {
                $objIuranBuku->bln_buku_level = $next_level->id_level;
            }

            $objIuranBuku->bln_kur = $kur;
            if ($kur == 1) {
                $objIuranBuku->bln_ganti_kur = $gantiKur;
            }

            $succ = $objIuranBuku->save();
            if ($succ) {
                // Create Nilai
                $nilaiMurid = new NilaiModel();
                $arrNilai = $nilaiMurid->getWhere("nilai_murid_id ='$id_murid' AND nilai_level='$objMurid->id_level_sekarang'");
                if (count($arrNilai) == 0) {
                    $nilaiMurid->nilai_murid_id = $id_murid;
                    $nilaiMurid->nilai_level = $objMurid->id_level_sekarang;
                    $nilaiMurid->nilai_create_date = leap_mysqldate();
                    $nilaiMurid->nilai_org_id = AccessRight::getMyOrgID();
                    $nilaiMurid->save();
                }
                $json['status_code'] = 1;
                $json['status_message'] = "Invoice tercetak!";
                echo json_encode($json);
                die();
            }
            $json['status_code'] = 0;
            $json['status_message'] = "Invoice sdh tercetak";
            echo json_encode($json);
            die();
        }
        //1 . buat log naik level
        // 2. ganti level di table murid
        //3. buat invoice iuran buku
    }

    public function naik_kelas_lama_ke_baru()
    {

        $id_murid = addslashes($_GET['id_murid']);
        $id_level = addslashes($_GET['id_level']);
        $kur = addslashes($_GET['kur']);
        $gantiKur = addslashes($_GET['gantiKur']);

        $json = array();

        // Cek, apakah masih ada invoice buku yang belum di bayar
        $objIuranBuku = new IuranBuku();
        $jumlahIuran = $objIuranBuku->getJumlah("bln_murid_id='$id_murid'  AND bln_status=0");

        if ($jumlahIuran > 0) {
            $json['status_code'] = 0;
            $json['status_message'] = "Masih ada taggihan buku yang belum dibayar!";
            echo json_encode($json);
            die();
        }


        // Check Kurikulum
        if ($kur == KEY::$KURIKULUM_LAMA) {
            $objIuranBuku = new IuranBuku();


            // Ganti Kurikulum lama ke Kurikulum Baru
            if ($gantiKur == 1) {
                $next_level = Generic::getMyNextLevel($id_level);
                $cnt = $objIuranBuku->getJumlah("bln_murid_id='$id_murid' AND bln_buku_level= '$next_level->id_level'");
            }

            // Tidak ganti kurikulum
            // Ikut level Kurikulum baru
            // Buku level lama
            else {
                $next_level = Generic::getMyNextLevel($id_level);
                $id_level_lama = Generic::convertLevelBaruKeLama($id_level);
                $id_next_level_lama = Generic::getMyNextLevelLama($id_level_lama);
//                pr($id_level_lama . " " . $id_next_level_lama->id_level_lama);
                // hitung buku lama
                $cnt = $objIuranBuku->getJumlah("bln_murid_id='$id_murid' AND bln_buku_level= '$next_level->id_level'");

            }


        } // Kurikulum Baru
        else {
            $next_level = Generic::getMyNextLevel($id_level);
            $objIuranBuku = new IuranBuku();
            $cnt = $objIuranBuku->getJumlah("bln_murid_id='$id_murid' AND bln_buku_level= '$next_level->id_level'");
        }


        if ($cnt > 0) {
//             continue;
            $json['status_code'] = 0;
            $json['status_message'] = "Invoice sdh tercetak";
            echo json_encode($json);
            die();
        } else {

            $objMurid = new MuridModel();
            $objMurid->getByID($id_murid);
            $bln = date("n");
            $thn = date("Y");
            $objIuranBuku->bln_murid_id = $objMurid->id_murid;
            $objIuranBuku->bln_date_pembayaran = leap_mysqldate();
            $objIuranBuku->bln_date = $bln . "-" . $thn;
            $objIuranBuku->bln_mon = $bln;
            $objIuranBuku->bln_tahun = $thn;
            $objIuranBuku->bln_tc_id = $objMurid->murid_tc_id;
            $objIuranBuku->bln_kpo_id = $objMurid->murid_kpo_id;
            $objIuranBuku->bln_ibo_id = $objMurid->murid_ibo_id;
            $objIuranBuku->bln_ak_id = $objMurid->murid_ak_id;

            // Kurikulum lama

            if ($kur == KEY::$KURIKULUM_LAMA) {
                //berarti Anda memilih buku Kurikulum baru dan kurikulum akan disesuaikan
                if ($gantiKur == 1) {
                    $objIuranBuku->bln_buku_level = $next_level->id_level;
                } else {

                    $objIuranBuku->bln_buku_level = $next_level->id_level;
                }

            } else {
                $objIuranBuku->bln_buku_level = $next_level->id_level;
            }

            $objIuranBuku->bln_kur = $kur;
            if ($kur == 1) {
                $objIuranBuku->bln_ganti_kur = $gantiKur;
            }

            $succ = $objIuranBuku->save();
            if ($succ) {
                // Create Nilai
                $nilaiMurid = new NilaiModel();
                $arrNilai = $nilaiMurid->getWhere("nilai_murid_id ='$id_murid' AND nilai_level='$objMurid->id_level_sekarang'");
                if (count($arrNilai) == 0) {
                    $nilaiMurid->nilai_murid_id = $id_murid;
                    $nilaiMurid->nilai_level = $objMurid->id_level_sekarang;
                    $nilaiMurid->nilai_create_date = leap_mysqldate();
                    $nilaiMurid->nilai_org_id = AccessRight::getMyOrgID();
                    $nilaiMurid->save();
                }
                $json['status_code'] = 1;
                $json['status_message'] = "Invoice tercetak!";
                echo json_encode($json);
                die();
            }
            $json['status_code'] = 0;
            $json['status_message'] = "Invoice sdh tercetak";
            echo json_encode($json);
            die();
        }
        //1 . buat log naik level
        // 2. ganti level di table murid
        //3. buat invoice iuran buku
    }

    public function create_invoice_buku_manual()
    {
        $id_murid = addslashes($_GET['id_murid']);
        $id_level = addslashes($_GET['id_level']);
        $kur = addslashes($_GET['kur']);
        $gantiKur = 0;
        $json = array();
        $objIuranBuku = new IuranBuku();

        $jumlahIuran = $objIuranBuku->getJumlah("bln_murid_id='$id_murid'  AND bln_status=0");

        if ($jumlahIuran > 0) {
            $json['status_code'] = 1;
            $json['status_message'] = "Masih ada taggihan buku yang belum dibayar!";
            echo json_encode($json);
            die();
        }

//        bln_status
        // Check Kurikulum
        if ($kur == KEY::$KURIKULUM_LAMA) {
            $objIuranBuku = new IuranBuku();


            // Ganti Kurikulum lama ke Kurikulum Baru
            if ($gantiKur == 1) {
                $cnt = $objIuranBuku->getJumlah("bln_murid_id='$id_murid' AND bln_buku_level= '$id_level'");
            } else {
                $cnt = $objIuranBuku->getJumlah("bln_murid_id='$id_murid' AND bln_buku_level= '$id_level'");
            }

        } // Kurikulum Baru
        else {
            $objIuranBuku = new IuranBuku();
            $cnt = $objIuranBuku->getJumlah("bln_murid_id='$id_murid' AND bln_buku_level= '$id_level'");
        }


        $objMurid = new MuridModel();
        $objMurid->getByID($id_murid);
        $bln = date("n");
        $thn = date("Y");
        $objIuranBuku->bln_murid_id = $objMurid->id_murid;
        $objIuranBuku->bln_date_pembayaran = leap_mysqldate();
        $objIuranBuku->bln_date = $bln . "-" . $thn;
        $objIuranBuku->bln_mon = $bln;
        $objIuranBuku->bln_tahun = $thn;
        $objIuranBuku->bln_tc_id = $objMurid->murid_tc_id;
        $objIuranBuku->bln_kpo_id = $objMurid->murid_kpo_id;
        $objIuranBuku->bln_ibo_id = $objMurid->murid_ibo_id;
        $objIuranBuku->bln_ak_id = $objMurid->murid_ak_id;


        // Kurikulum lama

        if ($kur == KEY::$KURIKULUM_LAMA) {
            //berarti Anda memilih buku Kurikulum baru dan kurikulum akan disesuaikan
            if ($gantiKur == 1) {
                $objIuranBuku->bln_buku_level = $id_level;
            } else {

                $objIuranBuku->bln_buku_level = $id_level;
            }

        } else {
            $objIuranBuku->bln_buku_level = $id_level;
        }

        $objIuranBuku->bln_kur = $kur;
        if ($kur == 1) {
            $objIuranBuku->bln_ganti_kur = $gantiKur;
        }

        $succ = $objIuranBuku->save();
        if ($succ) {
            // Create Nilai
            $json['status_code'] = 1;
            $json['status_message'] = "Invoice tercetak!";
            echo json_encode($json);
            die();
        }
        $json['status_code'] = 0;
        $json['status_message'] = "Invoice sdh tercetak";
        echo json_encode($json);
        die();

        //1 . buat log naik level
        // 2. ganti level di table murid
        //3. buat invoice iuran buku
    }

    public function setStatusMurid()
    {
        $id_murid = addslashes($_GET['id_murid']);
        $id_status = addslashes($_GET['id_status']);

        $objMurid = new MuridModel();
        $objMurid->getByID($id_murid);
        $objMurid->status = $id_status;
        $succ = $objMurid->save(1);
        $json['murid'] = $objMurid;
        if ($succ) {
//
            $json['status_code'] = 1;
            $json['status_message'] = "Update success";
            echo json_encode($json);
            die();
        }
        $json['status_code'] = 0;
        $json['status_message'] = "Update failed";
        echo json_encode($json);
        die();
    }

    public function read_request_certificate()
    {

        $page = isset($_GET['page']) ? addslashes($_GET['page']) : 1;
        $limit = KEY::$LIMIT_PROFILE;
        $begin = ($page - 1) * $limit;
        $ibo_id = AccessRight::getMyOrgID();
        $objCertificate = new SertifikatModel();
        $arrCertificate = $objCertificate->getWhere("sertifikat_ibo_id='$ibo_id' AND sertifikat_status = 0 LIMIT $begin, $limit");
        $arrStatus = array("<b>Belum di cetak</b>", "Sdh di cetak");
        $jumlahTotal = $objCertificate->getJumlah("sertifikat_ibo_id='$ibo_id' AND sertifikat_status = 0");
        $jumlahHalamanTotal = ceil($jumlahTotal / $limit);
        $t = time();
        $jumlahTotalBlmTercetak = $objCertificate->getJumlah("sertifikat_status=0 AND sertifikat_ibo_id='$ibo_id'");

        ?>
        <section class="content-header">
            <h1><?= KEY::$TITLEPERMINTAANSERTIFIKAT; ?></h1>
            <div class="box-tools pull-right">
                <button id="sertifikat_<?= $t; ?>">Sertifikat tercetak</button>
                <script>
                    $('#sertifikat_<?= $t; ?>').click(function () {
                        openLw('get_sertifikat_tercetak', '<?=_SPPATH;?>MuridWeb/get_sertifikat_tercetak', 'fade');
                    });
                </script>
            </div>
        </section>
        <div class="clearfix">
        </div>
        <section class="content">
            <div class="table-responsive">
                <h4><i>Belum tercetak: </i><b style="color: red"><?= $jumlahTotalBlmTercetak; ?></b></h4>
                <table class="table table-striped">
                    <thead>
                    <tr>

                        <th>
                            Nama
                        </th>
                        <th>
                            Level
                        </th>
                        <th>
                            TC
                        </th>
                        <th>
                            Status
                        </th>
                        <th>
                            Tanggal Request
                        </th>
                        <th>
                            Tanggal Cetak
                        </th>

                    </tr>
                    </thead>
                    <tbody id="container_load_certificate">
                    <?
                    foreach ($arrCertificate as $key => $val) {
                        ?>
                        <tr>

                            <td><?= Generic::getMuridNamebyID($val->sertifikat_murid_id); ?></td>
                            <td><?= Generic::getLevelNameByID($val->sertifikat_murid_level); ?></td>
                            <td><?= Generic::getTCNamebyID($val->sertifikat_tc_id); ?></td>

                            <td id="status_<?= $val->sertifikat_id; ?>"><?
                                if ($val->sertifikat_status) {
                                    echo $arrStatus[$val->sertifikat_status];
                                } else {
                                    ?>
                                    <button id="cetak_<?= $val->sertifikat_id . $t; ?>"
                                            class="btn btn-default sertifikat_<?= $val->sertifikat_id; ?>"><?= $arrStatus[$val->sertifikat_status]; ?></button>

                                    <?
                                }
                                ?></td>

                            <td><?= $val->sertifikat_req_date; ?></td>
                            <td id="sertifikat_kirim_date_<?= $val->sertifikat_id; ?>"> <?
                                if ($val->sertifikat_kirim_date != "1970-01-01 07:00:00") {
                                    echo $val->sertifikat_kirim_date;
                                }
                                ?></td>
                        </tr>
                        <script>

                            $('#cetak_<?= $val->sertifikat_id . $t; ?>').click(function () {
                                $.get("<?= _SPPATH; ?>MuridWebHelper/updateCertificate?id_certificate=<?= $val->sertifikat_id; ?>", function (data) {

                                    alert(data.status_message);
                                    if (data.status_code) {
                                        lwrefresh(selected_page);
                                        $('.sertifikat_<?= $val->sertifikat_id; ?>').hide();
                                        $('#status_<?= $val->sertifikat_id; ?>').html('<?= $arrStatus[1]; ?>');
                                        $('#sertifikat_kirim_date_<?= $val->sertifikat_id; ?>').html('<?= leap_mysqldate(); ?>');
                                    }
                                }, 'json');


                            });
                        </script>
                        <?
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <div class="text-center">
                <button class="btn btn-default" id="loadmore_certificate_<?= $t; ?>">Load more</button>
            </div>
            <script>
                var page_cert = <?= $page; ?>;
                var total_cert = <?= $jumlahHalamanTotal; ?>;
                $('#loadmore_certificate_<?= $t; ?>').click(function () {
                    if (page_cert < total_cert) {
                        page_cert++;
                        $.get("<?= _SPPATH; ?>MuridWebHelper/certificateload?page=" + page_cert, function (data) {
                            $("#container_load_certificate").append(data);
                        });
                        if (page_cert > total_cert)
                            $('#loadmore_certificate_<?= $t; ?>').hide();
                    } else {
                        $('#loadmore_certificate_<?= $t; ?>').hide();
                    }
                });
            </script>
        </section>

        <?
    }

    function certificateload()
    {
        $page = isset($_GET['page']) ? addslashes($_GET['page']) : 1;
        $limit = KEY::$LIMIT_PROFILE;
        $begin = ($page - 1) * $limit;
        $ibo_id = AccessRight::getMyOrgID();
        $objCertificate = new SertifikatModel();
        $arrCertificate = $objCertificate->getWhere(" sertifikat_ibo_id='$ibo_id' AND sertifikat_status = 0  LIMIT $begin, $limit");
        $arrStatus = array("<b>Belum di cetak</b>", "Sdh di cetak");

        $jumlahTotal = $objCertificate->getJumlah("sertifikat_ibo_id='$ibo_id' AND sertifikat_status = 0");
        $jumlahHalamanTotal = ceil($jumlahTotal / $limit);
        $t = time();
        foreach ($arrCertificate as $key => $val) {
            ?>
            <tr>

                <td><?= Generic::getMuridNamebyID($val->sertifikat_murid_id); ?></td>
                <td><?= Generic::getLevelNameByID($val->sertifikat_murid_level); ?></td>
                <td><?= Generic::getTCNamebyID($val->sertifikat_tc_id); ?></td>

                <td id="status_<?= $val->sertifikat_id; ?>"><?
                    if ($val->sertifikat_status) {
                        echo $arrStatus[$val->sertifikat_status];
                    } else {
                        ?>
                        <button id="cetak_<?= $val->sertifikat_id . $t; ?>"
                                class="btn btn-default sertifikat_<?= $val->sertifikat_id; ?>"><?= $arrStatus[$val->sertifikat_status]; ?></button>

                        <?
                    }
                    ?></td>

                <td><?= $val->sertifikat_req_date; ?></td>
                <td id="sertifikat_kirim_date_<?= $val->sertifikat_id; ?>"> <?
                    if ($val->sertifikat_kirim_date != "1970-01-01 07:00:00") {
                        echo $val->sertifikat_kirim_date;
                    }
                    ?></td>
            </tr>
            <script>

                $('#cetak_<?= $val->sertifikat_id . $t; ?>').click(function () {
                    $.get("<?= _SPPATH; ?>MuridWebHelper/updateCertificate?id_certificate=<?= $val->sertifikat_id; ?>", function (data) {

                        alert(data.status_message);
                        if (data.status_code) {
                            lwrefresh(selected_page);
                            $('.sertifikat_<?= $val->sertifikat_id; ?>').hide();
                            $('#status_<?= $val->sertifikat_id; ?>').html('<?= $arrStatus[1]; ?>');
                            $('#sertifikat_kirim_date_<?= $val->sertifikat_id; ?>').html('<?= leap_mysqldate(); ?>');
                        }
                    }, 'json');


                });
            </script>
            <?
        }
    }

    function certificatetercetak_load()
    {
        $tc_id = $_GET['tc_id'];
        $page = isset($_GET['page']) ? addslashes($_GET['page']) : 1;
        $limit = KEY::$LIMIT_PROFILE;
        $begin = ($page - 1) * $limit;
        $ibo_id = AccessRight::getMyOrgID();
        $objCertificate = new SertifikatModel();
        $arrCertificate = $objCertificate->getWhere(" sertifikat_tc_id=$tc_id AND sertifikat_status = 1 LIMIT $begin, $limit");
        $arrStatus = array("<b>Belum di cetak</b>", "Sdh di cetak");

        $jumlahTotal = $objCertificate->getJumlah("sertifikat_tc_id=$tc_id AND sertifikat_status = 1 ");
        $jumlahHalamanTotal = ceil($jumlahTotal / $limit);
        $t = time();
        foreach ($arrCertificate as $key => $val) {
            ?>
            <tr>

                <td><?= Generic::getMuridNamebyID($val->sertifikat_murid_id); ?></td>
                <td><?= Generic::getLevelNameByID($val->sertifikat_murid_level); ?></td>
                <td><?= Generic::getTCNamebyID($val->sertifikat_tc_id); ?></td>

                <td id="status_<?= $val->sertifikat_id; ?>"><?
                    if ($val->sertifikat_status) {
                        echo $arrStatus[$val->sertifikat_status];
                    } else {
                        ?>
                        <button id="cetak_<?= $val->sertifikat_id . $t; ?>"
                                class="btn btn-default sertifikat_<?= $val->sertifikat_id; ?>"><?= $arrStatus[$val->sertifikat_status]; ?></button>

                        <?
                    }
                    ?></td>

                <td><?= $val->sertifikat_req_date; ?></td>
                <td id="sertifikat_kirim_date_<?= $val->sertifikat_id; ?>"> <?
                    if ($val->sertifikat_kirim_date != "1970-01-01 07:00:00") {
                        echo $val->sertifikat_kirim_date;
                    }
                    ?></td>
            </tr>
            <script>

                $('#cetak_<?= $val->sertifikat_id . $t; ?>').click(function () {
                    $.get("<?= _SPPATH; ?>MuridWebHelper/updateCertificate?id_certificate=<?= $val->sertifikat_id; ?>", function (data) {

                        alert(data.status_message);
                        if (data.status_code) {
                            lwrefresh(selected_page);
                            $('.sertifikat_<?= $val->sertifikat_id; ?>').hide();
                            $('#status_<?= $val->sertifikat_id; ?>').html('<?= $arrStatus[1]; ?>');
                            $('#sertifikat_kirim_date_<?= $val->sertifikat_id; ?>').html('<?= leap_mysqldate(); ?>');
                        }
                    }, 'json');


                });
            </script>
            <?
        }
    }

    function updateCertificate()
    {
        $certificate_id = addslashes($_GET['id_certificate']);

        $objCertificate = new SertifikatModel();
        $objCertificate->getByID($certificate_id);
        $objCertificate->sertifikat_status = 1;
        $objCertificate->sertifikat_kirim_date = leap_mysqldate();
        $update = $objCertificate->save(1);
        if ($update) {
            $json['status_code'] = 1;
            $json['status_message'] = "Sertifikat sudah di cetak!";
            echo json_encode($json);
            die();
        }

        $json['status_code'] = 0;
        $json['status_message'] = "Update failed!";
        echo json_encode($json);
        die();
    }

    function loadCertTC()
    {
        $tc_id = $_GET['tc_id'];
        $status = $_GET['status'];
        $page = isset($_GET['page']) ? addslashes($_GET['page']) : 1;
        $limit = KEY::$LIMIT_PROFILE;
        $begin = ($page - 1) * $limit;
        $objCertificate = new SertifikatModel();
        $arrCertificate = $objCertificate->getWhere("sertifikat_tc_id=$tc_id AND sertifikat_status = $status LIMIT $begin, $limit");
        $jumlahTotal = $objCertificate->getJumlah("sertifikat_tc_id='$tc_id AND sertifikat_status = $status");
        $jumlahHalamanTotal = ceil($jumlahTotal / $limit);
        $arrStatus = array("<b>Belum di cetak</b>", "Sdh di cetak");


        foreach ($arrCertificate as $key => $val) {
            ?>
            <tr>

                <td><?= Generic::getMuridNamebyID($val->sertifikat_murid_id); ?></td>
                <td><?= Generic::getLevelNameByID($val->sertifikat_murid_level); ?></td>
                <td><?= Generic::getTCNamebyID($val->sertifikat_tc_id); ?></td>

                <td id="status_<?= $val->sertifikat_id; ?>"><?
                    if ($val->sertifikat_status) {
                        echo $arrStatus[$val->sertifikat_status];
                    } else {
                        ?>
                        <button id="cetak_<?= $val->sertifikat_id . $t; ?>"
                                class="btn btn-default sertifikat_<?= $val->sertifikat_id; ?>"><?= $arrStatus[$val->sertifikat_status]; ?></button>

                        <?
                    }
                    ?></td>

                <td><?= $val->sertifikat_req_date; ?></td>
                <td id="sertifikat_kirim_date_<?= $val->sertifikat_id; ?>"> <?
                    if ($val->sertifikat_kirim_date != "1970-01-01 07:00:00") {
                        echo $val->sertifikat_kirim_date;
                    }
                    ?></td>
            </tr>
            <script>

                $('#cetak_<?= $val->sertifikat_id . $t; ?>').click(function () {
                    $.get("<?= _SPPATH; ?>MuridWebHelper/updateCertificate?id_certificate=<?= $val->sertifikat_id; ?>", function (data) {

                        alert(data.status_message);
                        if (data.status_code) {
                            lwrefresh(selected_page);
                            $('.sertifikat_<?= $val->sertifikat_id; ?>').hide();
                            $('#status_<?= $val->sertifikat_id; ?>').html('<?= $arrStatus[1]; ?>');
                            $('#sertifikat_kirim_date_<?= $val->sertifikat_id; ?>').html('<?= leap_mysqldate(); ?>');
                        }
                    }, 'json');


                });
            </script>
            <?
        }

    }


    public function getTC()
    {
        $ibo_id = addslashes($_POST['ibo_id']);
        $arrMyTC = Generic::getAllMyTC($ibo_id);
        $t = time();
        ?>
        <select id="pilih_tc_<?= $t; ?>">
            <?
            foreach ($arrMyTC as $key => $val) {
                ?>
                <option value="<?= $key; ?>"><?= $val; ?></option>
                <?
            }
            ?>
        </select>
        <?
    }

    function getAbsensiMuridByMonth()
    {
        $bln = isset($_GET['bln']) ? addslashes($_GET['bln']) : date("n");
        $thn = isset($_GET['thn']) ? addslashes($_GET['thn']) : date("Y");
        $org = addslashes($_GET['org']);
        $date_help = "";
        if ($org == "tc") {
            $tc_id = isset($_GET['tc_id']) ? addslashes($_GET['tc_id']) : AccessRight::getMyOrgID();
        } elseif ($org == "ibo") {
            $arrMyTC = Generic::getAllMyTC(AccessRight::getMyOrgID());
            $tc_id = isset($_GET['tc_id']) ? addslashes($_GET['tc_id']) : key($arrMyTC);
        }
//        pr($tc_id);
        $t = time();
        $arrBulan = Generic::getAllMonths();
        $absen = new AbsenEntryModel();
        $arrAbsen = $absen->getWhere("absen_tc_id='$tc_id' AND (Month(absen_date)= $bln) AND (YEAR(absen_date)=$thn)  Group BY absen_date ");
        foreach ($arrAbsen as $abs) {
            $arrAbsen2 = $absen->getWhere("absen_tc_id='$tc_id' AND absen_date = '$abs->absen_date'");
            $once = false;
            foreach ($arrAbsen2 as $abs2) {
                if (!($once)) {

                    if ($abs2->absen_date != $date_help) {
                        $date_help = $abs2->absen_date;
                        $once = true;
                        ?>
                        <tr>
                            <td onclick="buka('<?= $abs2->absen_date; ?>');">
                                <?= $abs2->absen_date; ?>
                                <span class="caret" style="cursor: pointer;"></span>
                            </td>
                            <td>

                            </td>
                            <td>

                            </td>
                            <td>

                            </td>
                        </tr>
                        <? ?>

                        <?
                    }
                }
                ?>

                <tr>
                    <td class="bawah<?= $abs2->absen_date; ?>" style="visibility:hidden;display: none">

                    </td>
                    <td class="bawah<?= $abs2->absen_date; ?>" style="visibility:hidden;display: none">
                        <?= Generic::getMuridNamebyID($abs2->absen_murid_id); ?>
                    </td>
                    <td class="bawah<?= $abs2->absen_date; ?>" style="visibility:hidden;display: none">
                        <?
                        $kelas = new KelasWebModel();
                        $kelas->getWhereOne("id_kelas= '$abs2->absen_kelas_id'");
                        //                        pr($kelas);
                        echo Generic::getWeekDay()[$kelas->hari_kelas] . ", " . date("h:i", strtotime($kelas->jam_mulai_kelas)) . " - " . date("h:i", strtotime($kelas->jam_akhir_kelas));
                        ?>
                    </td>
                    <td class="bawah<?= $abs2->absen_date; ?>" style="visibility:hidden;display: none">
                        <?= Generic::getGuruNamebyID($abs2->absen_guru_id); ?>
                    </td>
                </tr>
                <script>
                    var openPO_id = 0;
                    var listOpen = [];
                    function buka(date) {
                        var pos = jQuery.inArray(date, listOpen);
                        console.log(pos);
                        if (pos == -1) {
                            $(".bawah" + date).removeAttr("style");

                            listOpen.push(date);
                        } else {
                            console.log("masuk");
                            $(".bawah" + date).css("visibility", "hidden");
                            $(".bawah" + date).css("display", "none");


                            listOpen.pop(date);
                            //
                        }


                    }
                </script>
                <?
            }
        }
    }


    function getAbsensiGuruByMonth()
    {

        $arrMyTC = Generic::getAllMyTC(AccessRight::getMyOrgID());
        $bln = isset($_GET['bln']) ? addslashes($_GET['bln']) : date("n");
        $thn = isset($_GET['thn']) ? addslashes($_GET['thn']) : date("Y");
        $tc_id = isset($_GET['tc_id']) ? addslashes($_GET['tc_id']) : key($arrMyTC);
        $t = time();
        $arrBulan = Generic::getAllMonths();
        $absen = new AbsenGuruModel();
        $arrAbsen = $absen->getWhere("absen_tc_id='$tc_id' AND (Month(absen_date)= $bln) AND (YEAR(absen_date)=$thn)  Group BY absen_date ");

        GuruWebHelper::tableAbsensiGuru($absen, $arrAbsen, $tc_id, $t);

    }

    static function tableAbsensi($objAbsen, $arrAbsen, $tc_id, $t)
    {
        $date_help = "";
        ?>
        <section class="content">
            <div class="table-responsive">
                <table class="table table-striped ">
                    <thead>
                    <tr>

                        <th>Tanggal</th>
                        <th>Nama</th>
                        <th>Kelas</th>
                        <th>Guru</th>
                    </tr>
                    </thead>

                    <tbody id="container_absensi_<?= $t; ?>">
                    <?
                    foreach ($arrAbsen as $abs) {
                        $arrAbsen2 = $objAbsen->getWhere("absen_tc_id='$tc_id' AND absen_date = '$abs->absen_date'");
                        $once = false;
                        foreach ($arrAbsen2 as $abs2) {
                        if (!($once)) {

                        if ($abs2->absen_date != $date_help) {
                            $date_help = $abs2->absen_date;
                            $once = true;
                            ?>
                            <tr>
                                <td onclick="buka('<?= $abs2->absen_date; ?>');">
                                    <?= $abs2->absen_date; ?>
                                    <span class="caret" style="cursor: pointer;"></span>
                                </td>
                                <td>

                                </td>
                                <td>

                                </td>
                                <td>

                                </td>
                            </tr>
                        <? ?>

                        <?
                        }
                        }
                        ?>

                            <tr>
                                <td class="bawah<?= $abs2->absen_date; ?>" style="visibility:hidden;display: none">

                                </td>
                                <td class="bawah<?= $abs2->absen_date; ?>" style="visibility:hidden;display: none">
                                    <?= Generic::getMuridNamebyID($abs2->absen_murid_id); ?>
                                </td>
                                <td class="bawah<?= $abs2->absen_date; ?>" style="visibility:hidden;display: none">
                                    <?
                                    $kelas = new KelasWebModel();
                                    $kelas->getWhereOne("id_kelas= '$abs2->absen_kelas_id'");
                                    echo Generic::getWeekDay()[$kelas->hari_kelas] . ", " . date("h:i", strtotime($kelas->jam_mulai_kelas)) . " - " . date("h:i", strtotime($kelas->jam_akhir_kelas));
                                    ?>
                                </td>
                                <td class="bawah<?= $abs2->absen_date; ?>" style="visibility:hidden;display: none">
                                    <?= Generic::getGuruNamebyID($abs2->absen_guru_id); ?>
                                </td>
                            </tr>
                            <script>
                                var openPO_id = 0;
                                var listOpen = [];
                                function buka(date) {
                                    var pos = jQuery.inArray(date, listOpen);
                                    console.log(pos);
                                    if (pos == -1) {
                                        $(".bawah" + date).removeAttr("style");

                                        listOpen.push(date);
                                    } else {
                                        console.log("masuk");
                                        $(".bawah" + date).css("visibility", "hidden");
                                        $(".bawah" + date).css("display", "none");


                                        listOpen.pop(date);
                                        //
                                    }


                                }
                            </script>
                            <?
                        }
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </section>

        <style>

            .caret {
                display: inline-block;
                width: 0;
                height: 0;
                margin-left: 2px;
                vertical-align: middle;
                border-top: 4px solid;
                border-right: 4px solid transparent;
                border-left: 4px solid transparent;
            }
        </style>
        <?
    }

    public function readmuridibo()
    {
        $help = new MuridWeb3();
        $help->read_murid_ibo();
    }

    public function setnilai()
    {
//        setnilai?id_murid=" + id_murid + "&level=" + level + "&nilai="
        $id_murid = addslashes($_GET['id_murid']);
        $level = addslashes($_GET['level']);
        $nilai_murid = addslashes($_GET['nilai']);
        $nilai = new NilaiModel();
        $nilai->getWhereOne("nilai_murid_id=$id_murid AND nilai_level=$level");
        $nilai->nilai_result = $nilai_murid;
        $update = $nilai->save(1);
        if ($update) {
            $json['status_code'] = 1;
            $json['status_message'] = "Pengisian nilai Berhasil!";
            echo json_encode($json);
            die();
        }
        $json['status_code'] = 0;
        $json['status_message'] = "Pengisian nilai gagal!";
        echo json_encode($json);
        die();
    }

    function murid_nilai()
    {
        $id = addslashes($_GET['id']);
        $nilai = new NilaiModel();
        $arrSettingNilai = Generic::getSettingNilai();
        $arrNilai = $nilai->getWhere("nilai_murid_id='$id' ORDER BY nilai_level DESC");
//        $nilai->nilai_murid_id
        $t = time();
        ?>
        <section class="content-header">
            <h1>Nilai <?= Generic::getMuridNamebyID($id); ?>
                <div class="pull-right">
                    <button class="btn btn-default" onclick="back_to_profile_murid('<?= $id; ?>');">back to profile
                    </button>
                </div>
            </h1>
        </section>
        <section class="content">

            <table class="table table-striped table-responsive">
                <thead>
                <tr>
                    <th>Level</th>
                    <th>Nilai</th>
                    <th>Tanggal</th>
                </tr>
                </thead>
                <tbody>
                <?
                foreach ($arrNilai as $val) {
                    ?>
                    <tr>
                        <td><?= Generic::getLevelNameByID($val->nilai_level); ?></td>
                        <td id='result_<?= $val->nilai_murid_id . "_" . $val->nilai_level . $t; ?>'><?= $val->nilai_result; ?></td>
                        <td><?= ($val->nilai_create_date); ?></td>
                    </tr>
                    <script>
                        $('#result_<?= $val->nilai_murid_id . "_" . $val->nilai_level . $t; ?>').dblclick(function () {

                            var current = $("#result_<?= $val->nilai_murid_id . "_" . $val->nilai_level; ?>").html();
                            var html = "<input value=\"<?=$val->nilai_result;?>\" type=\"number\" name=\"nilai\" id='select_nilai_<?= $val->nilai_murid_id . "_" . $val->nilai_level . $t; ?>'>";
//
                            $("#result_<?= $val->nilai_murid_id . "_" . $val->nilai_level . $t; ?>").html(html);
                            $('#select_nilai_<?= $val->nilai_murid_id . "_" . $val->nilai_level . $t; ?>').change(function () {
                                var slc = $('#select_nilai_<?= $val->nilai_murid_id . "_" . $val->nilai_level . $t; ?>').val();
                                var id_murid = '<?= $val->nilai_murid_id; ?>';
                                var level = '<?= $val->nilai_level; ?>';
                                if (confirm("Nilai akan diinput?")) {
                                    $.get("<?= _SPPATH; ?>MuridWebHelper/setnilai?id_murid=" + id_murid + "&level=" + level + "&nilai=" + slc, function (data) {
                                            if (data.status_code) {
                                                alert(data.status_message);
                                                lwrefresh(selected_page);
                                            } else {
                                                alert(data.status_message);
                                            }
                                        }, 'json'
                                    );
                                }

                            });
                        });
                    </script>
                    <?
                }
                ?>
                </tbody>
            </table>
        </section>
        <?
    }

    public function getNilaiMurid()
    {
        $tc_id = addslashes($_GET['tc_id']);
        $id_murid = addslashes($_GET['id_murid']);
        $arrSettingNilai = Generic::getSettingNilai();
        $nilai = new NilaiModel();
//        public $default_read_coloms = "nilai_id,nilai_murid_id,nilai_level,nilai_result,nilai_create_date,nilai_org_id";

        $arrNilai = $nilai->getWhere("nilai_murid_id='$id_murid' AND nilai_org_id = '$tc_id' ORDER BY nilai_level DESC");
        $t = time();
        ?>


        <?
        foreach ($arrNilai as $val) {
            ?>
            <tr>
                <td><?= Generic::getLevelNameByID($val->nilai_level); ?></td>
                <td><?= $arrSettingNilai[$val->nilai_result]; ?></td>
                <td><?= ($val->nilai_create_date) ?></td>
            </tr>
            <?
        }
        ?>

        <?
    }

    function getMuridByTC()
    {
        $ibo_id = AccessRight::getMyOrgID();
        $tc_id = addslashes($_POST['tc_id']);
        $arrMyTC = Generic::getAllMyTC($ibo_id);
        $t = time();
        ?>
        <select id="pilih_tc_<?= $t; ?>">
            <?
            foreach ($arrMyTC as $key => $val) {
                ?>
                <option value="<?= $key; ?>"><?= $val; ?></option>
                <?
            }
            ?>
        </select>
        <?
    }

    function murid_lomba()
    {
        $id_murid = addslashes($_GET['id']);
        $nilaiMurid = new MatrixNilaiModel();
        $arrNilaiMurid = $nilaiMurid->getWhere("mu_murid_id='$id_murid' ORDER by mu_date DESC");
//        pr($arrNilaiMurid);
        ?>
        <section class="content-header">
            <h1>History Perlombaan <?= Generic::getMuridNamebyID($id_murid); ?>
                <div class="pull-right">
                    <button class="btn btn-default" onclick="back_to_profile_murid('<?= $id_murid; ?>');">back to
                        profile
                    </button>
                </div>
            </h1>
        </section>
        <section class="content">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Nama Perlombaan</th>
                        <th>Nilai</th>

                    </tr>
                    </thead>
                    <tbody>
                    <?
                    $i = 1;
                    foreach ($arrNilaiMurid as $lomba) {
                        $ujian = new UjianModel();
                        $ujian->getWhereOne("ujian_id='$lomba->mu_ujian_id'");
                        ?>
                        <tr>
                            <td><?= $i; ?></td>
                            <td><?= date("d-m-Y", strtotime($ujian->ujian_date)); ?></td>
                            <td><?= $ujian->ujian_jenis; ?></td>
                            <td><?= $lomba->mu_nilai; ?></td>

                        </tr>
                        <?
                        $i++;
                    }
                    ?>
                    <tr></tr>
                    </tbody>

                </table>
            </div>
        </section>
        <?
    }

    function murid_ganti_kur()
    {
        $id_murid = addslashes($_GET['id']);
        $murid = new MuridModel();
        $murid->getByID($id_murid);
        $json = array();
        if (!is_null($murid->id_murid)) {
            if ($murid->murid_kurikulum == 0) {
                $murid->murid_kurikulum = 1;

            } else {
                $murid->murid_kurikulum = 0;

            }

            $murid->save(1);
            $json['status_code'] = 1;
            $json['status_message'] = "Kurikulum berhasil diubah";

        } else {
            $json['status_code'] = 0;
            $json['status_message'] = "Kurikulum gagla diubah";

        }

        echo json_encode($json);
        die();

    }

    function printSPP()
    {
        $kuponSatuan = new KuponSatuan();
        $id = addslashes($_GET['id']);
        $nama = addslashes($_GET['nama']);
        $bln = addslashes($_GET['bln']);
        $id_murid = addslashes($_GET['id_murid']);
        $kuponSatuan->getByID($id);
        $murid = new MuridModel();
        $murid->getByID($id_murid);
        $arrjenisBiayaSPP = Generic::getJenisBiayaType();
        $jenisBiayaSPP = $arrjenisBiayaSPP[$murid->id_level_sekarang];
        $jenisbm = new JenisBiayaModel();
        $jenisbm->getByID(AccessRight::getMyOrgID() . "_" . $jenisBiayaSPP);
        $iuranBulanan = new IuranBulanan();
        $iuranBulanan->getWhereOne("bln_kupon_id=$kuponSatuan->kupon_id");
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <title>Invoice Iuran Bulanan</title>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
            <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        </head>
        <style>
            th {
                text-align: center;
            }


        </style>
        <style type="text/css" media="print">
            #headerPrint {
                display: none;
            }
        </style>
        <div id="headerPrint" style="font-family:Verdana, Arial, Helvetica, sans-serif;">
            Click Ctrl+P untuk print.
        </div>
        <body>

        <div class="container">
            <h2 style="text-align:center">Invoices Iuran Bulanan</h2>
            <br>
            <br>
            <br>
            <div class="info_invoices">
                <b>No. Invoice : <?= $iuranBulanan->bln_no_invoice; ?></b> <br>
                <b>Tanggal : <?= $kuponSatuan->kupon_pemakaian_date; ?></b>
            </div>
            <br>
            <br>
            <p>Telah diterima pembayaran dari :<br>
                <b>Nama Murid : <?= $nama; ?></b><br>
                <b>No Murid : <?= $murid->kode_siswa; ?></b><br>
            </p>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th> Bulan</th>
                        <th> Kupon Yang Digunakan</th>
                        <th> Harga</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td><?= $bln; ?></td>
                        <td><?= $kuponSatuan->kupon_id ?></td>
                        <td style="text-align:right; "><?= idr($jenisbm->harga); ?></td>
                    </tr>


                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="2" style="text-align:right; padding-right:50px">Jumlah Total</td>
                        <td style="text-align:right;"><?= idr($jenisbm->harga); ?></td>

                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        </body>
        </html>


        <?
    }

    function printSPP2()
    {

        $kuponSatuan = new KuponSatuan();
        $id = addslashes($_GET['id']);
        $nama = addslashes($_GET['nama']);
        $id_murid = addslashes($_GET['id_murid']);
        $kuponSatuan->getByID($id);
        $murid = new MuridModel();
        $murid->getByID($id_murid);
        $arrjenisBiayaSPP = Generic::getJenisBiayaType();
        $jenisBiayaSPP = $arrjenisBiayaSPP[$murid->id_level_sekarang];
        $jenisbm = new JenisBiayaModel();
        $jenisbm->getByID(AccessRight::getMyOrgID() . "_" . $jenisBiayaSPP);
        $iuranBulanan = new IuranBulanan();
        $iuranBulanan->getWhereOne("bln_kupon_id=$kuponSatuan->kupon_id");
        $tc = new SempoaOrg();
        $tc->getWhereOne("org_id=$murid->murid_tc_id");
        $date = new DateTime($iuranBulanan->bln_date_pembayaran);
        $tanggal = $date->format("d-m-Y");
        ?>
        <html>

        <head>
            <meta charset="utf-8">
            <style>
                @page {
                    size: A6;
                    margin: 0;
                }

                @media print {
                    html, body {
                        width: 105mm;
                        height: 148mm;
                    }
                }

                #data_tc {
                    text-align: center;
                }

                div.info_invoices {
                    font-size: 12px;
                }

                div.nama_siswa {
                    font-size: 12px;
                }

                table {
                    font-family: arial, sans-serif;
                    border-collapse: collapse;
                    width: 100%;
                    font-size: 12px;
                }

                td, th {

                    border: 1px solid #414141;
                    text-align: center;
                    padding: 8px;
                    font-size: 12px;
                }

                th {
                    background-color: #dddddd;
                }

                #sempoasip_pusat {
                    text-align: center;
                }

                #logo_sempoa {
                    display: block;
                    margin: auto;
                }

                div.penutup_invoices {
                    text-align: center;
                    margin-left: 450px;
                    margin-right: 450px;
                }

                .container {
                    font-size: 12px;
                }
            </style>

        </head>

        <body>
        <section class="sheet padding-10mm">
            <!-- Write HTML just like a web page -->
            <article>

                <div class="container" style="margin-left: 20px; margin-right: 20px;">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="invoice_orang_tua">
                                <div class="kop_invoices">
                                    <h4 id="data_tc">
                                        <?= $tc->nama; ?><br>
                                        <?= $tc->alamat; ?><br>
                                        Telp. <?= $tc->nomor_telp; ?>, Fax. <?= $tc->tc_no_fax_office; ?>,
                                        HP. <?= $tc->tc_no_hp_office; ?>
                                    </h4>
                                    <div class="info_invoices">
                                        <b>No. Invoice : <?= $iuranBulanan->bln_no_invoice; ?></b> <br>
                                        <b>Tanggal : <?= $tanggal; ?></b>
                                    </div>
                                    <div class="nama_siswa">
                                        <p>
                                            Telah diterima pembayaran oleh Murid :<br>
                                            <b>Nama Murid : <?= $nama; ?></b><br>
                                            <b>No Murid : <?= $murid->kode_siswa; ?></b><br>
                                        </p>
                                    </div>
                                    <table style="border-right: 20px;">
                                        <tr>
                                            <th>No Kupon</th>
                                            <th>Keterangan</th>
                                            <th>Harga</th>
                                        </tr>
                                        <tr>
                                            <td><?= $iuranBulanan->bln_kupon_id; ?></td>
                                            <td>Iuran Bulanan : <?= $iuranBulanan->bln_date; ?></td>
                                            <td><?= idr($jenisbm->harga); ?></td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td style="text-align:right;padding-right:15px;font-style:bold;">Jumlah
                                                Total
                                            </td>
                                            <td><?= idr($jenisbm->harga); ?></td>
                                        </tr>

                                    </table>

                    <span>
	<p>Pembayaran melalui mesin EDC atau via transfer ke :</p>
	<h4 id="sempoasip_pusat">SEMPOA SIP<br><?= $tc->tc_nama_bank; ?> <?= $tc->tc_cabang_bank; ?>
        <br><?= $tc->tc_acc_bank; ?></h4>

</span>
                                    <div class="clearfix"></div>

                                    <div class="col-md-3" style="text-align: right;">
                                        ....................., <?= $tanggal; ?><!--</div>-->

                                    </div>
                                    <div class="clearfix"></div>
                                    <br><br>
                                    <div>
                                        <p style="border-right: 20px; float: left;">Catatan :
                                            Setiap Training
                                            Centre
                                            beroperasional dan
                                            memiliki kepemilikan secara mandiri</p>

                                    </div>
                                    <br><br><br>

                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </article>
        </section>
        </body>
        </html>
        <?

    }

    function printBuku2()
    {

        $invoice_id = addslashes($_GET['id_invoice']);
        $nama = addslashes($_GET['nama']);
        $bln = addslashes($_GET['bln']);
        $id_murid = addslashes($_GET['id_murid']);
        $tgl = addslashes($_GET['tgl']);
        $level = addslashes($_GET['level']);
        $jenisbm = new JenisBiayaModel();
        $jenisbm->getByID(AccessRight::getMyOrgID() . "_" . KEY::$BIAYA_IURAN_BUKU);
        $murid = new MuridModel();
        $murid->getByID($id_murid);
        $iuranBuku = new IuranBuku();
        $arrLevel = Generic::getAllLevel();
        $a = array_keys($arrLevel, $level);
        $iuranBuku->getWhereOne("bln_murid_id=$id_murid AND bln_buku_level=$a[0]");
        $tc = new SempoaOrg();
        $tc->getWhereOne("org_id=$murid->murid_tc_id");
        $bukuDgnNo = new StockBuku();
        $arbukuDgnNo = $bukuDgnNo->getWhere("stock_invoice_murid=$iuranBuku->bln_id");
        $arbukuDgnNo = $bukuDgnNo->getWhere("stock_invoice_murid=$invoice_id");

        ?>

        <html>

        <head>
            <title>Invoice <?= Lang::t("Iuran Buku") ?></title>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">

            <style>
                @page {
                    size: A6;
                    margin: 0;
                }

                @media print {
                    html, body {
                        width: 105mm;
                        height: 148mm;
                    }
                }

                #data_tc {
                    text-align: center;
                }

                div.info_invoices {
                    padding: 10px;
                    font-size: 12px;
                }

                div.nama_siswa {
                    padding: 10px;
                    font-size: 12px;
                }

                table {
                    font-family: arial, sans-serif;
                    border-collapse: collapse;
                    width: 100%;
                    margin-right: 12px;
                }

                td, th {
                    border: 1px solid #414141;
                    text-align: center;
                    padding: 8px;
                    font-size: 12px;
                }

                th {
                    background-color: #dddddd;
                }

                #sempoasip_pusat {
                    text-align: center;
                }

                #logo_sempoa {
                    display: block;
                    margin: auto;
                }

                div.penutup_invoices {
                    text-align: center;
                    margin-left: 450px;
                    margin-right: 450px;
                }

                .nama_siswa {
                    font-size: 12px;
                }
            </style>
        </head>

        <body>
        <section class="sheet padding-10mm">

            <!-- Write HTML just like a web page -->
            <article>

                <div class="container">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="invoice_orang_tua">
                                <div class="kop_invoices">
                                    <h4 id="data_tc">
                                        <?= $tc->nama; ?><br>
                                        <?= $tc->alamat; ?><br>
                                        Telp. <?= $tc->nomor_telp; ?>, Fax. <?= $tc->tc_no_fax_office; ?>,
                                        HP. <?= $tc->tc_no_hp_office; ?>
                                    </h4>
                                    <div class="info_invoices">
                                        <b>No. Invoice : <?= $iuranBuku->bln_no_invoice; ?></b> <br>
                                        <b>Tanggal : <?= $tgl; ?></b>
                                    </div>
                                    <div class="nama_siswa">
                                        Telah diterima pembayaran oleh Murid :<br>
                                        <b>Nama Murid : <?= $nama; ?></b><br>
                                        <b>No Murid : <?= $murid->kode_siswa; ?></b><br><br>
                                        <table style="border-right: 20px;">
                                            <thead>
                                            <th>No Buku</th>
                                            <th>Keterangan</th>
                                            <th>Harga</th>
                                            </thead>
                                            <tbody>
                                            <?
                                            foreach ($arbukuDgnNo as $val) {
                                                $date = new DateTime($val->stock_buku_tgl_keluar_tc);
                                                $tanggal = $date->format("d-m-Y");
                                                ?>
                                                <tr>
                                                    <td><?= $val->stock_buku_no; ?></td>
                                                    <td><?= $val->stock_name_buku; ?></td>
                                                    <td><?= idr($jenisbm->harga); ?></td>
                                                </tr>
                                                <?
                                            }
                                            ?>

                                            <tr>
                                                <td></td>
                                                <td style="text-align:right;padding-right:15px;font-style:bold;">Jumlah
                                                    Total
                                                </td>
                                                <td><?= idr($jenisbm->harga); ?> </td>
                                            </tr>
                                            </tbody>
                                        </table>
                    <span>
	<p>Pembayaran melalui mesin EDC atau via transfer ke :</p>
	<h4 id="sempoasip_pusat">SEMPOA SIP<br><?= $tc->tc_nama_bank; ?> <?= $tc->tc_cabang_bank; ?>
        <br><?= $tc->tc_acc_bank; ?></h4>

</span>
                                        <div class="clearfix"></div>

                                        <div class="col-md-3" style="text-align: right;">
                                            ....................., <?= $tanggal; ?><!--</div>-->

                                        </div>
                                        <div class="clearfix"></div>
                                        <br><br>
                                        <div>
                                            <p style="border-right: 20px; float: left;margin-left: 20px;">Catatan :
                                                Setiap Training
                                                Centre
                                                beroperasional dan
                                                memiliki kepemilikan secara mandiri</p>

                                        </div>
                                        <br><br><br>

                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
        </body>
        </article>
        </section>
        </html>
        <?

    }

    function printBuku_3()
    {

        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <title>Invoice <?= Lang::t("Iuran Buku") ?></title>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
            <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        </head>
        <style>
            .invoice-title h2, .invoice-title h3 {
                display: inline-block;
            }

            .table > tbody > tr > .no-line {
                border-top: none;
            }

            .table > thead > tr > .no-line {
                border-bottom: none;
            }

            .table > tbody > tr > .thick-line {
                border-top: 2px solid;
            }
        </style>
        <body>
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <div class="invoice-title">
                        <h2>Invoice</h2>
                        <h3 class="pull-right">Order # 12345</h3>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-xs-6">
                            <address>
                                <strong>Billed To:</strong><br>
                                John Smith<br>
                                1234 Main<br>
                                Apt. 4B<br>
                                Springfield, ST 54321
                            </address>
                        </div>
                        <div class="col-xs-6 text-right">
                            <address>
                                <strong>Shipped To:</strong><br>
                                Jane Smith<br>
                                1234 Main<br>
                                Apt. 4B<br>
                                Springfield, ST 54321
                            </address>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-6">
                            <address>
                                <strong>Payment Method:</strong><br>
                                Visa ending **** 4242<br>
                                jsmith@email.com
                            </address>
                        </div>
                        <div class="col-xs-6 text-right">
                            <address>
                                <strong>Order Date:</strong><br>
                                March 7, 2014<br><br>
                            </address>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title"><strong>Order summary</strong></h3>
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-condensed">
                                    <thead>
                                    <tr>
                                        <td><strong>Item</strong></td>
                                        <td class="text-center"><strong>Price</strong></td>
                                        <td class="text-center"><strong>Quantity</strong></td>
                                        <td class="text-right"><strong>Totals</strong></td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <!-- foreach ($order->lineItems as $line) or some such thing here -->
                                    <tr>
                                        <td>BS-200</td>
                                        <td class="text-center">$10.99</td>
                                        <td class="text-center">1</td>
                                        <td class="text-right">$10.99</td>
                                    </tr>
                                    <tr>
                                        <td>BS-400</td>
                                        <td class="text-center">$20.00</td>
                                        <td class="text-center">3</td>
                                        <td class="text-right">$60.00</td>
                                    </tr>
                                    <tr>
                                        <td>BS-1000</td>
                                        <td class="text-center">$600.00</td>
                                        <td class="text-center">1</td>
                                        <td class="text-right">$600.00</td>
                                    </tr>
                                    <tr>
                                        <td class="thick-line"></td>
                                        <td class="thick-line"></td>
                                        <td class="thick-line text-center"><strong>Subtotal</strong></td>
                                        <td class="thick-line text-right">$670.99</td>
                                    </tr>
                                    <tr>
                                        <td class="no-line"></td>
                                        <td class="no-line"></td>
                                        <td class="no-line text-center"><strong>Shipping</strong></td>
                                        <td class="no-line text-right">$15</td>
                                    </tr>
                                    <tr>
                                        <td class="no-line"></td>
                                        <td class="no-line"></td>
                                        <td class="no-line text-center"><strong>Total</strong></td>
                                        <td class="no-line text-right">$685.99</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </body>
        </html>
        <?
    }

    function printBuku()
    {

        $nama = addslashes($_GET['nama']);
        $bln = addslashes($_GET['bln']);
        $id_murid = addslashes($_GET['id_murid']);
        $tgl = addslashes($_GET['tgl']);
        $level = addslashes($_GET['level']);
        $jenisbm = new JenisBiayaModel();
        $jenisbm->getByID(AccessRight::getMyOrgID() . "_" . KEY::$BIAYA_IURAN_BUKU);
        $murid = new MuridModel();
        $murid->getByID($id_murid);
        $iuranBuku = new IuranBuku();
        $arrLevel = Generic::getAllLevel();
        $a = array_keys($arrLevel, $level);
        $iuranBuku->getWhereOne("bln_murid_id=$id_murid AND bln_buku_level=$a[0]");
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <title>Invoice <?= Lang::t("Iuran Buku") ?></title>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
            <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        </head>
        <style>
            th {
                text-align: center;
            }

        </style>
        <style type="text/css" media="print">
            #headerPrint {
                display: none;
            }
        </style>
        <div id="headerPrint" style="font-family:Verdana, Arial, Helvetica, sans-serif;">
            Click Ctrl+P untuk print.
        </div>
        <body>

        <div class="container">
            <h2 style="text-align:center">Invoices <?= Lang::t("Iuran Buku") ?></h2>
            <br>
            <br>
            <br>
            <div class="info_invoices">
                <b>No. Invoice :<?= $iuranBuku->bln_no_invoice; ?></b> <br>
                <b>Tanggal : <?= $tgl; ?></b>
            </div>
            <br>
            <br>
            <p>Telah diterima pembayaran dari :<br>
                <b>Nama Murid : <?= $nama; ?></b><br>
                <b>No Murid :<?= $murid->kode_siswa; ?></b><br>
            </p>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th> Level Buku</th>
                        <th> Harga</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td><?= $level; ?></td>
                        <td style="text-align:right; "><?= idr($jenisbm->harga); ?></td>
                    </tr>


                    </tbody>
                    <tfoot>
                    <tr>
                        <td style="text-align:right; padding-right:50px">Jumlah Total</td>
                        <td style="text-align:right;"><?= idr($jenisbm->harga); ?></td>

                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        </body>
        </html>


        <?
    }

    function printRegister()
    {
        $id_murid = addslashes($_GET['id_murid']);
        $murid = new MuridModel();
        $murid->getByID($id_murid);
        $arrjenisBiayaSPP = Generic::getJenisBiayaType();
        $jenisBiayaSPP = $arrjenisBiayaSPP[$murid->id_level_sekarang];
        $pay = new PaymentFirstTimeLog();
        $pay->getByID($id_murid);
        $arrPay = unserialize($pay->murid_biaya_serial);
        foreach ($arrPay as $key => $val) {

            if ($val['id_biaya'] == KEY::$BIAYA_REGISTRASI) {
                $Registrasi = $val['harga'];
            }
            if ($val['id_biaya'] == KEY::$BIAYA_IURAN_BUKU) {
                $ibuku = $val['harga'];
            }
            if ($val['id_biaya'] == $jenisBiayaSPP) {
                $SPP = $val['harga'];
            }
            if ($val['id_biaya'] == KEY::$BIAYA_PERLENGKAPAN_JUNIOR) {
                $harga = $val['harga'];
                $level = "Junior";
            }
            if ($val['id_biaya'] == KEY::$BIAYA_PERLENGKAPAN_FOUNDATION) {
                $harga = $val['harga'];
                $level = "Foundation";
            }

            if ($key == "kupon") {
                $nomor = $val['nomor'];
                $bln = $val['kapan'];
            }
        }

        $date = new DateTime($pay->murid_pay_date);
        $tanggal = $date->format("d-m-Y");
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <title>Invoice Pendaftaran</title>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
            <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        </head>
        <style>
            th {
                text-align: center;
            }

        </style>
        <style type="text/css" media="print">
            #headerPrint {
                display: none;
            }
        </style>
        <div id="headerPrint" style="font-family:Verdana, Arial, Helvetica, sans-serif;">
            Click Ctrl+P untuk print.
        </div>
        <body>

        <div class="container">
            <h2 style="text-align:center">Invoices Pendaftaran</h2>
            <br>
            <br>
            <br>
            <div class="info_invoices">
                <b>No. Invoice :<?= $pay->bln_no_invoice; ?></b> <br>
                <b>Tanggal : <?= $tanggal; ?></b>
            </div>
            <br>
            <br>
            <p>Telah diterima pembayaran dari :<br>
                <b>Nama Murid : <?= Generic::getMuridNamebyID($pay->murid_id); ?></b><br>
                <b>No Murid : <?= ($murid->kode_siswa); ?></b><br>
            </p>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Jenis Biaya</th>
                        <th>Jumlah</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>Biaya Registrasi</td>
                        <td style="text-align:right;"><?= idr($Registrasi); ?></td>
                    </tr>
                    <tr>
                        <td>Iuran Bulanan <?= $bln; ?> dengan nomor kupon <?= $nomor; ?></td>
                        <td style="text-align:right;"><?= idr($SPP); ?></td>
                    </tr>
                    <tr>
                        <td><?= Lang::t("Iuran Buku") ?>
                            level <?= Generic::getLevelNameByID($murid->id_level_masuk); ?></td>
                        <td style="text-align:right;"><?= idr($ibuku); ?></td>
                    </tr>
                    <tr>
                        <td>Biaya Perlengkapan <?= $level; ?></td>
                        <td style="text-align:right;"><?= idr($harga); ?></td>
                    </tr>

                    </tbody>
                    <tfoot>
                    <tr>
                        <td style="text-align:right; padding-right:50px">Jumlah Total</td>
                        <td style="text-align:right;"><?= idr($pay->murid_pay_value); ?></td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        </body>
        </html>
        <?
    }


    function printRegister2()
    {

        $id_murid = addslashes($_GET['id_murid']);
        $murid = new MuridModel();
        $murid->getByID($id_murid);
        $arrjenisBiayaSPP = Generic::getJenisBiayaType();
        $jenisBiayaSPP = $arrjenisBiayaSPP[$murid->id_level_sekarang];
        $pay = new PaymentFirstTimeLog();
        $pay->getByID($id_murid);
        $arrPay = unserialize($pay->murid_biaya_serial);
        foreach ($arrPay as $key => $val) {

            if ($val['id_biaya'] == KEY::$BIAYA_REGISTRASI) {
                $Registrasi = $val['harga'];
            }
            if ($val['id_biaya'] == KEY::$BIAYA_IURAN_BUKU) {
                $ibuku = $val['harga'];
            }
            if ($val['id_biaya'] == $jenisBiayaSPP) {
                $SPP = $val['harga'];
            }
            if ($val['id_biaya'] == KEY::$BIAYA_PERLENGKAPAN_JUNIOR) {
                $harga = $val['harga'];
                $level = "Junior";
            }
            if ($val['id_biaya'] == KEY::$BIAYA_PERLENGKAPAN_FOUNDATION) {
                $harga = $val['harga'];
                $level = "Foundation";
            }

            if ($key == "kupon") {
                $nomor = $val['nomor'];
                $bln = $val['kapan'];
            }
        }
        $tc = new SempoaOrg();
        $tc->getWhereOne("org_id=$murid->murid_tc_id");
        $date = new DateTime($pay->murid_pay_date);
        $tanggal = $date->format("d-m-Y");
        ?>

        <html>

        <head>
            <style>

                @page {
                    size: A6;
                    margin: 0;
                }

                @media print {
                    html, body {
                        width: 105mm;
                        height: 148mm;
                    }
                }

                #data_tc {
                    text-align: center;
                }

                div.info_invoices {

                }

                div.nama_siswa {

                }

                table {
                    font-family: arial, sans-serif;
                    border-collapse: collapse;
                    width: 100%;
                    margin-right: 20px;
                    font-size: 12px;
                }

                td, th {
                    border: 1px solid #414141;
                    text-align: center;
                    padding: 8px;
                }

                th {
                    background-color: #dddddd;
                }

                #sempoasip_pusat {
                    text-align: center;
                }

                .invoice_orang_tua {
                    margin-left: 20px;
                    margin-right: 20px;
                    font-size: 12px;
                }

                div.penutup_invoices {
                    text-align: center;
                    margin-left: 450px;
                    margin-right: 450px;
                }
            </style>
        </head>

        <body>
        <section class="sheet padding-10mm">
            <article>
                <div class="invoice_orang_tua">
                    <div class="kop_invoices">

                        <h4 id="data_tc">
                            <?= $tc->nama; ?><br>
                            <?= $tc->alamat; ?><br>
                            Telp. <?= $tc->nomor_telp; ?>, Fax. <?= $tc->tc_no_fax_office; ?>,
                            HP. <?= $tc->tc_no_hp_office; ?>
                        </h4>
                        <div class="info_invoices">
                            <b>No. Invoice : <?= $pay->bln_no_invoice; ?></b> <br>
                            <b>Tanggal : <?= $tanggal; ?></b>
                        </div>
                        <div class="nama_siswa">
                            <p>
                                Telah diterima pembayaran oleh Murid :<br>
                                <b>Nama Murid : <?= Generic::getMuridNamebyID($pay->murid_id); ?></b><br>
                                <b>No Murid : <?= ($murid->kode_siswa); ?></b><br>
                            </p>
                        </div>
                        <table>
                            <tr>
                                <th>No</th>
                                <th>Keterangan</th>
                                <th>Harga</th>
                            </tr>
                            <tr>
                                <td></td>
                                <td>Biaya Registrasi</td>
                                <td style="text-align:right;"><?= idr($Registrasi); ?></td>
                            </tr>
                            <tr>
                                <td><?= $nomor; ?></td>
                                <td>Iuran Bulanan : <?= $bln; ?> </td>
                                <td style="text-align:right;"><?= idr($SPP); ?></td>
                            </tr>
                            <tr>
                                <td><?= Generic:: getNoBukuByIuranBulananIDWithTC($pay->bln_no_invoice, $pay->murid_tc_id); ?></td>
                                <td>Uang Buku <?= Generic::getLevelNameByID($murid->id_level_masuk); ?></td>
                                <td style="text-align:right;"><?= idr($ibuku); ?></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>Biaya Perlengkapan <?= $level; ?></td>
                                <td style="text-align:right;"><?= idr($harga); ?></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td style="text-align:right;padding-right:15px;font-style:bold;">Jumlah Total</td>
                                <td style="text-align:right;"><?= idr($pay->murid_pay_value); ?></td>
                            </tr>

                        </table>

<span>
	<p>Pembayaran melalui mesin EDC atau via transfer ke :</p>
	<h4 id="sempoasip_pusat">SEMPOA SIP<br><?= $tc->tc_nama_bank; ?> <?= $tc->tc_cabang_bank; ?>
        <br><?= $tc->tc_acc_bank; ?></h4>

	<p style="float: right; margin-right: 20px;">....................., <?= $tanggal; ?></p>
</span>
                        <br><br>
                        <div>
                            <p style="float: left;">Catatan : Setiap Training Centre beroperasional
                                dan
                                memiliki kepemilikan secara mandiri</p>
                            <p style="float: right;">Training Center</p>
                        </div>
                        <br><br>
                    </div>

                </div>
            </article>
        </section>

        </body>
        </html>
        <?
    }


    public static function read_murid_tc_page()
    {

        $myOrgID = (AccessRight::getMyOrgID());
        $t = time();
        $obj = new MuridModel();
        $arrStatusMurid = Generic::getAllStatusMurid();
        $arrStatusMurid[KEY::$STATUSINDEXALL] = KEY::$STATUSALL;
        $arrJenisKelamin = Generic::getJeniskelamin();
        $arrLevel = Generic::getAllLevel();

        $page = isset($_GET['page']) ? addslashes($_GET['page']) : 1;
        $limit = KEY::$LIMIT_PAGE;
        $begin = ($page - 1) * $limit;
        $status = isset($_GET['status']) ? addslashes($_GET['status']) : 1;
        if ($status == KEY::$STATUSINDEXALL) {
            $arrMurid = $obj->getWhere("murid_tc_id=$myOrgID ORDER BY nama_siswa ASC  LIMIT $begin, $limit");
            $jumlahTotal = $obj->getJumlah("murid_tc_id=$myOrgID ");
        } else {
            $arrMurid = $obj->getWhere("murid_tc_id=$myOrgID AND status=$status ORDER BY nama_siswa ASC  LIMIT $begin, $limit");
            $jumlahTotal = $obj->getJumlah("murid_tc_id=$myOrgID AND status=$status");
        }

        $index = (($page - 1) * 20) + 1;
        $jumlahHalamanTotal = ceil($jumlahTotal / $limit);
        $return['classname'] = __FUNCTION__;
        $return['page'] = $page;
        $return['totalpage'] = $jumlahHalamanTotal;
        $return['perpage'] = $limit;
        $return['search_triger'] = $status;
        $return['status'] = $status;
        ?>
        <div id="content_load_murid_<?= $t; ?>">
            <section class="content-header">
                <div class="box-tools pull-right">

                    <label for="exampleInputName2">Status:</label>
                    <select id="pilih_status_<?= $t; ?>">

                        <?
                        foreach ($arrStatusMurid as $key => $val) {
                            if ($key == $status) {
                                $selected = "selected";
                            } else {
                                $selected = "";
                            }

                            ?>
                            <option value="<?= $key; ?>" <?= $selected; ?>><?= $val; ?></option>
                            <?
                        }
                        ?>
                    </select>
                    <button id="submit_status_<?= $t; ?>">submit</button>
                    <script>
                        $('#submit_status_<?= $t; ?>').click(function () {
                            var status = $('#pilih_status_<?= $t; ?>').val();
                            $('#content_load_murid_<?=$t;?>').load('<?= _SPPATH; ?>MuridWebHelper/read_murid_tc_page?status=' + status, function () {

                            }, 'json');
                        });

                    </script>
                </div>
            </section>
            <div class="clearfix"></div>
            <section class="content">
                <div class="row hidden-print" style="margin-bottom: 10px;">
                    <div class="col-md-4 col-xs-12">

                        <div class="input-group">
                            <input type="text" class="form-control" value=""
                                   id="search_word_<?= __FUNCTION__ . "_" . $t; ?>">
      <span class="input-group-btn">
        <button class="btn btn-default" id="submit_<?= __FUNCTION__ . "_" . $t; ?>" type="button">search</button>
      </span>
                            <script>
                                $('#submit_<?= __FUNCTION__ . "_" . $t; ?>').click(function () {
                                    var words = $('#search_word_<?= __FUNCTION__ . "_" . $t; ?>').val();
                                    if (words != "") {
                                        var status = $('#pilih_status_<?= $t; ?>').val();
                                        var clms = "nama_siswa";

                                        $('#content_load_murid_<?=$t;?>').load('<?= _SPPATH; ?>MuridWebHelper/read_murid_tc_search?status=' + status + "&clms=" + clms + "&words=" + words, function () {

                                        }, 'json');
                                    }
                                });
                            </script>
                        </div>

                    </div>
                </div>
                <div>
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>No.</th>
                            <th>Kode Siswa</th>
                            <th>Nama Siswa</th>
                            <th>Status</th>
                            <th>Jenis Kelamin</th>
                            <th>Level Sekarang</th>
                            <th>Firsttime payment</th>
                            <th>Profile</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?
                        foreach ($arrMurid as $key => $valMurid) {
                            $k = $key + 1;
                            ?>
                            <tr>
                                <td><?= $index; ?></td>
                                <td><?= $valMurid->kode_siswa; ?></td>
                                <td><?= $valMurid->nama_siswa; ?></td>
                                <td><?= $arrStatusMurid[$valMurid->status]; ?></td>
                                <td><?= $arrJenisKelamin[$valMurid->jenis_kelamin]; ?></td>
                                <td><?= $arrLevel[$valMurid->id_level_sekarang]; ?></td>
                                <td><?

                                    if ($valMurid->pay_firsttime == '0') {
                                        echo "<button onclick=\"openLw('Payment_Murid','" . _SPPATH . "MuridWebHelper/firsttime_payment?id_murid=" . $valMurid->id_murid . "','fade');\">Payment First Time</button>";
                                    } else {
                                        echo "<a target=\"_blank\" href=" . _SPPATH . "MuridWebHelper/printRegister2?id_murid=" . $valMurid->id_murid . "><span  style=\"vertical-align:middle\" class=\"glyphicon glyphicon-print\"  aria-hidden=\"true\"></span>
                                            </a>";
                                    }

                                    ?></td>
                                <td><?
                                    echo "<button onclick=\"openLw('Profile_Murid','" . _SPPATH . "MuridWebHelper/profile?id_murid=" . $valMurid->id_murid . "','fade');\">Profile</button>";
                                    ?></td>
                            </tr>
                            <?
                            $index++;
                        }
                        ?>
                        </tbody>
                        <tfoot>

                        </tfoot>

                    </table>
                    <?
                    $webClass = __CLASS__;
                    Generic::pagination($return, $webClass);

                    ?>
                </div>
            </section>
        </div>
        <?

    }

    public function viewMuridKPO()
    {
        $kpoID = AccessRight::getMyOrgID();
        $arrMyIBO = Generic::getAllMyIBO($kpoID);
        $iboID = (isset($_GET['ibo_id']) ? addslashes($_GET['ibo_id']) : key($arrMyIBO));

        $arrMyTC = Generic::getAllMyTC($iboID);
        $tcid = (isset($_GET['tc_id']) ? addslashes($_GET['tc_id']) : key($arrMyTC));
        $t = time();
        $arrStatusMurid = Generic::getAllStatusMurid();
        $arrJenisKelamin = Generic::getJeniskelamin();
        $arrLevel = Generic::getAllLevel();

        $obj = new MuridModel();
        $page = isset($_GET['page']) ? addslashes($_GET['page']) : 1;
        $limit = KEY::$LIMIT_PAGE;
        $begin = ($page - 1) * $limit;
        $status = isset($_GET['status']) ? addslashes($_GET['status']) : 1;
        $arrMurid = $obj->getWhere("murid_tc_id=$tcid AND status=$status ORDER BY nama_siswa ASC  LIMIT $begin, $limit");
        $jumlahTotal = $obj->getJumlah("murid_tc_id=$tcid AND status=$status");
        $jumlahHalamanTotal = ceil($jumlahTotal / $limit);
        $index = (($page - 1) * 20) + 1;
        $return['classname'] = __FUNCTION__;
        $return['page'] = $page;
        $return['totalpage'] = $jumlahHalamanTotal;
        $return['perpage'] = $limit;
        $return['status'] = $status;
        $return['tc_id'] = $tcid;

        ?>
        <div id="container_tc_<?= $tcid; ?>">
            <section class="content-header">
                <h1>
                    <div class="pull-right" style="font-size: 13px;">
                        <label for="exampleInputName2">Pilih IBO:</label>
                        <select id="pilih_IBO_<?= $t; ?>">

                            <?
                            foreach ($arrMyIBO as $key => $val) {
                                if ($iboID == $key) {
                                    $selected = "selected";
                                } else {
                                    $selected = "";
                                }

                                ?>
                                <option value="<?= $key; ?>" <?= $selected ?>><?= $val; ?></option>
                                <?
                            }
                            ?>
                        </select>

                        <label for="exampleInputName2">Pilih TC:</label>
                        <select id="pilih_tc_<?= $t; ?>">

                            <?
                            foreach ($arrMyTC as $key => $val) {
                                if ($tcid == $key) {
                                    $selected = "selected";
                                } else {
                                    $selected = "";
                                }
                                ?>
                                <option value="<?= $key; ?>" <?= $selected ?>><?= $val; ?></option>
                                <?
                            }
                            ?>
                        </select>
                        <label for="exampleInputName2">Status:</label>
                        <select id="pilih_status_<?= $t; ?>">

                            <?
                            foreach ($arrStatusMurid as $key => $val) {
                                if ($key == $status) {
                                    $selected = "selected";
                                } else {
                                    $selected = "";
                                }

                                ?>
                                <option value="<?= $key; ?>" <?= $selected; ?>><?= $val; ?></option>
                                <?
                            }
                            ?>
                        </select>
                        <button class="btn btn-default" id="submit_pilih_tc_<?= $t; ?>">Submit</button>
                    </div>
                </h1>
            </section>


            <div class="clearfix"></div>
            <section class="content">

                <table class='table table-bordered table-striped '>
                    <thead>
                    <tr>
                        <th>No.</th>
                        <th>Kode Siswa</th>
                        <th>Nama Siswa</th>
                        <th>Status</th>
                        <th>Jenis Kelamin</th>
                        <th>Level</th>
                        <th>Profile</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?
                    foreach ($arrMurid as $key => $murid) {
                        ?>
                        <tr id='<?= $murid->id_murid; ?>'>
                            <td>
                                <?= $index; ?>
                            </td>
                            <td>
                                <?= $murid->kode_siswa ?>
                            </td>
                            <td>
                                <?= $murid->nama_siswa ?>
                            </td>
                            <td><?= $arrStatusMurid[$murid->status]; ?></td>
                            <td>
                                <?= $arrJenisKelamin[$murid->jenis_kelamin];
                                ?>
                            </td>
                            <td>
                                <?= Generic::getLevelNameByID($murid->id_level_sekarang); ?>
                            </td>
                            <td>
                                <button
                                    onclick="openLw('Profile_Murid', '<?= _SPPATH; ?>MuridWebHelper/profile?id_murid=<?= $murid->id_murid; ?>', 'fade');">
                                    Profile
                                </button>
                            </td>
                        </tr>
                        <?
                        $index++;
                    }
                    ?>
                    </tbody>
                </table>
                <?
                $webClass = __CLASS__;
                Generic::pagination($return, $webClass);

                ?>

            </section>
            <script>
                $('#pilih_IBO_<?= $t; ?>').change(function () {
                    var slc = $('#pilih_IBO_<?= $t; ?>').val();
                    getTC(slc);
                });

                function getTC(slc) {
                    $.ajax({
                        type: "POST",
                        url: "<?= _SPPATH; ?>MuridWebHelper/getTC?",
                        data: 'ibo_id=' + slc,
                        success: function (data) {
                            console.log(data);
                            $("#pilih_tc_<?= $t; ?>").html(data);
                        }
                    });
                }
                $('#submit_pilih_tc_<?= $t; ?>').click(function () {
                    var slc = $('#pilih_tc_<?= $t; ?>').val();
                    var status = $('#pilih_status_<?= $t; ?>').val();
                    $('#container_tc_<?= $tcid; ?>').load("<?= _SPPATH; ?>MuridWebHelper/viewMuridKPO?tc_id=" + slc + "&status=" + status, function () {
                    }, 'json');
                });

            </script>
        </div>
        <?
    }

    public function read_murid_tc_search()
    {

        $myOrgID = (AccessRight::getMyOrgID());
        $t = time();
        $obj = new MuridModel();
        $arrStatusMurid = Generic::getAllStatusMurid();
        $arrStatusMurid[KEY::$STATUSINDEXALL] = KEY::$STATUSALL;
        $arrJenisKelamin = Generic::getJeniskelamin();
        $arrLevel = Generic::getAllLevel();

        $page = isset($_GET['page']) ? addslashes($_GET['page']) : 1;
        $limit = KEY::$LIMIT_PAGE;
        $begin = ($page - 1) * $limit;
        $status = isset($_GET['status']) ? addslashes($_GET['status']) : 1;
        $index = (($page - 1) * 20) + 1;

        $words = $_GET['words'];
//        pr($words);
        $clms = $_GET['clms'];
        $arrClms = array();
        $clm = "";
        if ($clms != "") {
            $clm = " AND  ";
            //nama_siswa Like '%bil%' OR alamat LIKE '%bi%'
            $arrClms = explode(",", $clms);
            for ($i = 0; $i < count($arrClms); $i++) {
                if ($i == count($arrClms) - 1) {
                    $clm = $clm . $arrClms[$i] . " LIKE " . "'%" . $words . "%'";
                } else {
                    $clm = $clm . $arrClms[$i] . " LIKE " . "'%" . $words . "%'" . " OR ";
                }
            }
        }
//        pr($clm);
        if ($status == KEY::$STATUSINDEXALL) {
            $arrMurid = $obj->getWhere("murid_tc_id=$myOrgID $clm ORDER BY nama_siswa ASC  LIMIT $begin, $limit");
            $jumlahTotal = $obj->getJumlah("murid_tc_id=$myOrgID $clm");
        } else {
            $arrMurid = $obj->getWhere("murid_tc_id=$myOrgID AND status=$status $clm ORDER BY nama_siswa ASC  LIMIT $begin, $limit");
            $jumlahTotal = $obj->getJumlah("murid_tc_id=$myOrgID AND status=$status $clm");
        }


        $jumlahHalamanTotal = ceil($jumlahTotal / $limit);

        $return['classname'] = __FUNCTION__;
        $return['page'] = $page;
        $return['totalpage'] = $jumlahHalamanTotal;
        $return['perpage'] = $limit;
        $return['search_triger'] = $status;
        $return['status'] = $status;
        ?>
        <div id="content_load_murid_<?= $t; ?>">
            <section class="content-header">
                <div class="box-tools pull-right">

                    <label for="exampleInputName2">Status:</label>
                    <select id="pilih_status_<?= $t; ?>">

                        <?
                        foreach ($arrStatusMurid as $key => $val) {
                            if ($key == $status) {
                                $selected = "selected";
                            } else {
                                $selected = "";
                            }

                            ?>
                            <option value="<?= $key; ?>" <?= $selected; ?>><?= $val; ?></option>
                            <?
                        }
                        ?>
                    </select>
                    <button id="submit_status_<?= $t; ?>">submit</button>
                    <script>
                        $('#submit_status_<?= $t; ?>').click(function () {
                            var status = $('#pilih_status_<?= $t; ?>').val();
                            $('#content_load_murid_<?=$t;?>').load('<?= _SPPATH; ?>MuridWebHelper/read_murid_tc_page?status=' + status, function () {

                            }, 'json');
                        });

                    </script>
                </div>
            </section>
            <div class="clearfix"></div>
            <section class="content">
                <div class="row hidden-print" style="margin-bottom: 10px;">
                    <div class="col-md-4 col-xs-12">

                        <div class="input-group">
                            <input type="text" class="form-control" value=""
                                   id="search_word_<?= __FUNCTION__ . "_" . $t; ?>">
      <span class="input-group-btn">
        <button class="btn btn-default" id="submit_<?= __FUNCTION__ . "_" . $t; ?>" type="button">search</button>
      </span>
                            <script>
                                $('#submit_<?= __FUNCTION__ . "_" . $t; ?>').click(function () {
                                    var words = $('#search_word_<?= __FUNCTION__ . "_" . $t; ?>').val();
                                    if (words != "") {
                                        var status = $('#pilih_status_<?= $t; ?>').val();
                                        var clms = "nama_siswa";
                                        $('#content_load_murid_<?=$t;?>').load('<?= _SPPATH; ?>MuridWebHelper/read_murid_tc_search?status=' + status + "&clms=" + clms + "&words=" + words, function () {

                                        }, 'json');
                                    }
                                });
                            </script>
                        </div>

                    </div>
                </div>
                <div>
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>No.</th>
                            <th>Kode Siswa</th>
                            <th>Nama Siswa</th>
                            <th>Status</th>
                            <th>Jenis Kelamin</th>
                            <th>Level Sekarang</th>
                            <th>Firsttime payment</th>
                            <th>Profile</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?
                        foreach ($arrMurid as $key => $valMurid) {
                            $k = $key + 1;
                            ?>
                            <tr>
                                <td><?= $index; ?></td>
                                <td><?= $valMurid->kode_siswa; ?></td>
                                <td><?= $valMurid->nama_siswa; ?></td>
                                <td><?= $arrStatusMurid[$valMurid->status]; ?></td>
                                <td><?= $arrJenisKelamin[$valMurid->jenis_kelamin]; ?></td>
                                <td><?= $arrLevel[$valMurid->id_level_sekarang]; ?></td>
                                <td><?

                                    if ($valMurid->pay_firsttime == '0') {
                                        echo "<button onclick=\"openLw('Payment_Murid','" . _SPPATH . "MuridWebHelper/firsttime_payment?id_murid=" . $valMurid->id_murid . "','fade');\">Payment First Time</button>";
                                    } else {
                                        echo "<a target=\"_blank\" href=" . _SPPATH . "MuridWebHelper/printRegister2?id_murid=" . $valMurid->id_murid . "><span  style=\"vertical-align:middle\" class=\"glyphicon glyphicon-print\"  aria-hidden=\"true\"></span>
                                            </a>";
                                    }

                                    ?></td>
                                <td><?
                                    echo "<button onclick=\"openLw('Profile_Murid','" . _SPPATH . "MuridWebHelper/profile?id_murid=" . $valMurid->id_murid . "','fade');\">Profile</button>";
                                    ?></td>
                            </tr>
                            <?
                            $index++;
                        }
                        ?>
                        </tbody>
                        <tfoot>

                        </tfoot>

                    </table>
                    <?
                    $webClass = __CLASS__;
                    Generic::pagination($return, $webClass);

                    ?>
                </div>
            </section>
        </div>
        <?

    }


    function murid_iuranBuku_load()
    {
        $page = isset($_GET['page']) ? addslashes($_GET['page']) : 1;
        $limit = KEY::$LIMIT_PROFILE;
        $begin = ($page - 1) * $limit;
        $id = addslashes($_GET['id']);
        $iuranBuku = new IuranBuku();
        $arrIuranBuku = $iuranBuku->getWhere("bln_murid_id='$id' ORDER by bln_id DESC LIMIT $begin,$limit");
        $jumlahTotal = $iuranBuku->getJumlah("bln_murid_id='$id'");
//        pr($arrIuranBuku);
        $jumlahHalamanTotal = ceil($jumlahTotal / $limit);
        $arrPembayaran = Generic::getJenisPembayaran();
        $arrSTatus = array("<b>Unpaid</b>", "Paid");
        $murid = new MuridModel();
        $murid->getByID($id);
        foreach ($arrIuranBuku as $key => $val) {
            ?>
            <tr>
                <td><?= $val->bln_date_pembayaran; ?></td>
                <td><?= Generic::getLevelNameByID($val->bln_buku_level); ?></td>
                <td><?
                    if ($val->bln_status)
                        echo $arrSTatus[$val->bln_status];
                    else {
                        if (AccessRight::getMyOrgType() == "tc") {
                            ?>
                            <button class="btn btn-default belumbayar_<?= $val->bln_id; ?>"
                                    id='pay_now_bulanan_<?= $val->bln_id; ?>'>Pay Now
                            </button>

                            <?
                        } else {
                            echo "<b>Unpaid</b>";
                        }
                    }
                    ?></td>
                <td>
                    <?
                    $stock = new StockBuku();
                    $res = $stock->getBukuNoByInvoiceID($val->bln_id);
                    if (count($res) > 0) {
                        foreach ($res as $key => $nobuku) {
                            echo $nobuku . "/" . $key . "<br>";
                        }
                    }

                    ?>

                </td>
                <td><?
                    if ($val->bln_status)
                        echo $arrPembayaran[$val->bln_cara_bayar];
                    else {
                        if (AccessRight::getMyOrgType() == "tc") {
                            ?>
                            <select id="jenis_pmbr_invoice_<?= $val->bln_id ?>">
                                <?
                                foreach ($arrPembayaran as $key => $by) {
                                    ?>
                                    <option value="<?= $key; ?>"><?= $by; ?></option>
                                    <?
                                }
                                ?>
                            </select>
                            <?
                        }
                    }
                    ?></td>

                <?
                if ((AccessRight::getMyOrgType() == KEY::$TC)) {
                    ?>
                    <td>
                        <?
                        if ($val->bln_status == 0) {
//                                                echo $mk->bln_kupon_id;
                        } else {
                            if ($murid->id_level_masuk == $val->bln_buku_level) {
                                echo "<a target=\"_blank\" href=" . _SPPATH . "MuridWebHelper/printRegister2?id_murid=" . $id . "><span  style=\"vertical-align:middle\" class=\"glyphicon glyphicon-print\"  aria-hidden=\"true\"></span>
                                            </a>";
                            } else {
                                ?>
                                <a target="_blank"
                                   href="<?= _SPPATH; ?>MuridWebHelper/printBuku2?id_invoice=<?= $val->bln_id; ?>&nama=<?= Generic::getMuridNamebyID($id); ?>&id_murid=<?= $id; ?>&tgl=<?= $val->bln_date_pembayaran; ?>&level=<?= Generic::getLevelNameByID($val->bln_buku_level); ?>">

                                                        <span class="glyphicon glyphicon-print"
                                                              aria-hidden="true"></span>
                                </a>
                                <?
                            }


                        }
                        ?>

                    </td>
                    <?

                }
                ?>
                <?
                if (AccessRight::getMyOrgType() == KEY::$IBO) {
                    ?>
                    <td>
                        <?
                        if (substr($val->bln_no_invoice, 0, 2) != KEY::$AWALAN_FIRST_PAYMENT) {
                            $now = time();
                            $your_date = strtotime($val->bln_date_pembayaran);
                            $datediff = Generic::diffTwoDaysInDay($now, $your_date);
                            if ($datediff <= KEY::$MAX_UNDO_IURAN_BUKU) {
                                ?>
                                <span id="undo_iuran_buku_<?= $val->bln_id; ?>" class="fa fa-undo"
                                      aria-hidden="true"></span>
                                <?
                            }
                        }

                        ?>
                    </td>
                    <?
                }
                ?>
                <?
                if (AccessRight::getMyOrgType() == KEY::$IBO) {
                    ?>
                    <td>
                        <span id="delete_invoice_buku_<?= $val->bln_id . "_" . $t; ?>" class="fa fa-times"
                              aria-hidden="true"></span>
                    </td>
                    <?
                } ?>
                <script>
                    var bln_id = '<?= $val->bln_id; ?>';
                    $('#delete_invoice_buku_<?= $val->bln_id . "_" . $t; ?>').click(function () {
                        if (confirm("Apakah Anda Yakin akan menghapus transaksi Iuran Bulanan?")) {
//                            alert(bln_id);
                            $.post("<?= _SPPATH; ?>LaporanWebHelper/hapusIuranBuku", {
                                bln_id: bln_id
                            }, function (data) {
                                console.log(data);
                                alert(data.status_message);
                                if (data.status_code) {
                                    lwrefresh(selected_page);
                                    lwrefresh("Profile_Murid");
                                }
                            }, 'json');
                        }
                    });

                    $("#undo_iuran_buku_<?= $val->bln_id; ?>").click(function () {
                        if (confirm("Apakah Anda Yakin akan membatalkan transaksi Iuran Bulanan?")) {
                            $.post("<?= _SPPATH; ?>LaporanWebHelper/undo_iuran_buku_2", {
                                bln_id: bln_id
                            }, function (data) {
                                console.log(data);
                                alert(data.status_message);
                                if (data.status_code) {
                                    lwrefresh(selected_page);
                                    lwrefresh("Profile_Murid");
                                }
                            }, 'json');
                        }
                        //kosongkan status di iuran bulanan
                        //
                    });
                    $('#undo_<?= $val->bln_id . $t; ?>').click(function () {
                    });

                    <?
                    if ($val->bln_status) {
                    ?>
                    $('#belumbayar_<?= $val->bln_id; ?>').hide();
                    <?
                    } else {
                    ?>
                    $('#belumbayar_<?= $val->bln_id; ?>').show();
                    <?
                    }
                    ?>
                    $('#pay_now_bulanan_<?= $val->bln_id; ?>').click(function () {
//                                            alert("asas");
                        var jpb = $('#jenis_pmbr_invoice_<?= $val->bln_id ?>').val();
                        var bln_id = <?= $val->bln_id; ?>;
                        $.post("<?= _SPPATH; ?>LaporanWebHelper/pay_iuran_buku_roy", {
                                bln_id: bln_id,
                                cara_pby: jpb
                            },
                            function (data) {
                                alert(data.status_message);
                                if (data.status_code) {
                                    $('.td .belumbayar_<?= $val->bln_id; ?>').hide();
                                    $('.sudahbayar').show();
                                    $('#jenis_pmbr_invoice_<?= $val->bln_id ?>').attr("disabled", "true");
                                    lwrefresh(selected_page);
                                    // Refresh profile muridnya
                                    lwrefresh("Profile_Murid");
                                } else {
                                }
                                console.log(data);
                                //                                                                $('#balikan_<? //= $val->bln_id;                                                                                                                          ?>//').html(data);
                            }, 'json');
                    });
                    //


                </script>
            </tr>
            <?
        }


    }

    public function read_murid_tc_ibo()
    {
        if ($_GET['tc_id'] != "") {
            $_SESSION['selected_murid_tc_id'] = $_GET['tc_id'];
        }
        $myOrgID = $_SESSION['selected_murid_tc_id'];
        if ($myOrgID == "") die("No TC ID");
        $obj = new MuridModel();
        if ($_GET['cmd'] == 'edit' && $_GET['id'] != "")
            $obj->getByID($_GET['id']);
        if ($obj->pay_firsttime == '0')
            $obj->onAjaxSuccess = "openLw('Payment_Murid','" . _SPPATH . "MuridWebHelper/firsttime_payment?id_murid='+data.bool,'fade');";
        $obj->removeAutoCrudClick = array("pay_firsttime", "profile");
        $obj->read_filter_array = array("murid_tc_id" => $myOrgID);
        $obj->hideColoums = array("murid_tc_id", "murid_ak_id", "murid_kpo_id", "murid_ibo_id");
        $crud = new CrudCustom();
        $crud->ar_add = AccessRight::hasRight("create_murid_tc");
        $crud->ar_edit = AccessRight::hasRight("update_murid_tc");
        $crud->ar_delete = AccessRight::hasRight("delete_murid_tc");
        $crud->filter_extra = "status_murid";
        if ($_GET['status_murid'] != "") {
            $_SESSION['filter_status_murid'] = $_GET['status_murid'];
        }
        if (isset($_SESSION['filter_status_murid']) && $_SESSION['filter_status_murid'] != "99") {
            $obj->read_filter_array["status"] = $_SESSION['filter_status_murid'];
        }
        $crud->run_custom($obj, "MuridWebHelper", "read_murid_tc_ibo");
    }

    public function create_invoice_ibo()
    {

        $id_murid = addslashes($_POST['id_murid']);
        $bln = addslashes($_POST['bln']);
        $thn = addslashes($_POST['thn']);

        $weiter = true;
        $id_hlp = $id_murid . "_" . $bln . "_" . $thn;
        $iuranbulanan = new IuranBulanan();
        $iuranbulanan->getWhereOne("bln_murid_id=$id_murid  AND bln_mon=$bln AND  bln_tahun=$thn  AND bln_id='$id_hlp'");

        $murid = new MuridModel();
        $murid->getByID($id_murid);
        $iuranbulanan = new IuranBulanan();
        $pilih_kapan = $bln . "-" . $thn;
        $idInvoice = $iuranbulanan->createIuranBulananManual($id_murid, $pilih_kapan, $bln, $thn, $murid->murid_ak_id, $murid->murid_kpo_id, $murid->murid_ibo_id, $murid->murid_tc_id);
        if ($idInvoice > 0) {
            $json['status_code'] = 1;
            $json['status_message'] = "Invoice sudah tercetak";

        } else {

            $jumlah = $iuranbulanan->getJumlah("bln_mon=$bln AND bln_tahun=$thn AND bln_murid_id=$id_murid");
            $idInvoice = $iuranbulanan->createIuranBulananManualSecond($id_murid, $pilih_kapan, $bln, $thn, $jumlah+1, $murid->murid_ak_id, $murid->murid_kpo_id, $murid->murid_ibo_id, $murid->murid_tc_id);

            if ($idInvoice > 0) {
                $json['status_code'] = 1;
                $json['status_message'] = "Invoice sudah tercetak";

            } else {
                $json['status_code'] = 0;
                $json['status_message'] = "jumllah : " .  $jumlah . "Invoice gagal tercetak";
            }


        }


        echo json_encode($json);
        die();

    }


    public function create_invoice()
    {

        $id = addslashes($_GET['id']);
        $ib = new IuranBulanan();
        $bln = date("n");
        $thn = date("Y");

        $weiter = true;


        while ($weiter) {
            $id_hlp = $id . "_" . $bln . "_" . $thn;
            $iuranbulanan = new IuranBulanan();
            $iuranbulanan->getWhereOne("bln_murid_id=$id  AND bln_mon=$bln AND  bln_tahun=$thn  AND bln_id='$id_hlp'");
//            $json['bln'] = $bln . " " . $thn;
            if (is_null($iuranbulanan->bln_id)) {
                $weiter = false;
            } else {
                if ($bln == 12) {
                    $bln = 1;
                    $thn = $thn + 1;
                } else {
                    $bln = $bln + 1;

                }
            }
        }


        $murid = new MuridModel();
        $murid->getByID($id);

        $iuranbulanan = new IuranBulanan();
        $pilih_kapan = $bln . "-" . $thn;

        $idInvoice = $iuranbulanan->createIuranBulananManual($id, $pilih_kapan, $bln, $thn, $murid->murid_ak_id, $murid->murid_kpo_id, $murid->murid_ibo_id, $murid->murid_tc_id);


        if ($idInvoice > 0) {
            $json['status_code'] = 1;
            $json['status_message'] = "Invoice sudah tercetak";

        } else {
            $json['status_code'] = 0;
            $json['status_message'] = "Invoice gagal tercetak";

        }

        echo json_encode($json);
        die();

    }

    // tgl 26.04 krn tc harapan indah ibrahim
    public function create_invoice_backup()
    {

        $id = addslashes($_GET['id']);
        $ib = new IuranBulanan();
        $ib->getWhereOne("bln_murid_id=$id ORDER BY bln_tahun DESC");

        if (is_null($ib->bln_id)) {
            $bln = date("n");
            $thn = date("Y");
        } else {
            $thn = $ib->bln_tahun;
            $ib->getWhereOne("bln_murid_id=$id AND bln_tahun=$thn ORDER BY bln_mon DESC");
            $bln = $ib->bln_mon;
            $bln_skrg = date("n");
            $thn_skrg = date("Y");
            if ($thn_skrg > $thn) {
                $bln = $bln_skrg - 1;
            } else {
                if ($bln_skrg - $bln > 1) {
                    $bln = $bln_skrg - 1;
                }
            }
        }


        $iuranbulanan = new IuranBulanan();
        $iuranbulanan->getWhereOne("bln_murid_id=$id  AND bln_mon=$bln AND  bln_tahun=$thn");


        $weiter = true;
        if (is_null($iuranbulanan->bln_id)) {
            $weiter = false;
            if ($bln == 12) {
                $bln = 1;
                $thn = $thn + 1;
            } else {
//                $bln = $bln + 1;
                $bln = $bln;
                $json['bln'] = $bln . " " . $thn;
            }
        }


        while ($weiter) {
            $id_hlp = $id . "_" . $bln . "_" . $thn;
            $iuranbulanan = new IuranBulanan();
            $iuranbulanan->getWhereOne("bln_murid_id=$id  AND bln_mon=$bln AND  bln_tahun=$thn  AND bln_id='$id_hlp'");
            $json['bln'] = $bln . " " . $thn;
            if (is_null($iuranbulanan->bln_id)) {
                $weiter = false;
            } else {
                if ($bln == 12) {
                    $bln = 1;
                    $thn = $thn + 1;
                } else {


                    $bln = $bln + 1;

                }
            }
        }

        $murid = new MuridModel();
        $murid->getByID($id);

        $iuranbulanan = new IuranBulanan();
        $pilih_kapan = $bln . "-" . $thn;

        $idInvoice = $iuranbulanan->createIuranBulananManual($id, $pilih_kapan, $bln, $thn, $murid->murid_ak_id, $murid->murid_kpo_id, $murid->murid_ibo_id, $murid->murid_tc_id);

        if ($idInvoice > 0) {
            $json['status_code'] = 1;
            $json['status_message'] = "Invoice sudah tercetak";

        } else {
            $json['status_code'] = 0;
            $json['status_message'] = "Invoice gagal tercetak";

        }

        echo json_encode($json);
        die();


    }

    function changeLevelKurikulum()
    {
        // id_murid
        // Level_murid
        $id_murid = $_GET['id_murid'];
        $id_level = $_GET['id_level_murid'];

        $murid = new MuridModel();
        $murid->getByID($id_murid);

        if (is_null($murid->id_murid)) {
            $json['status_code'] = 0;
            $json['status_message'] = "Murid tidak ditemukan";
            echo json_encode($json);
            die();
        }

        $id_lvl_hlp = $murid->id_level_sekarang;
        $murid->id_level_sekarang = $id_level;
        $murid->save(1);
        $json['murid'] = $murid;

        $mj = new MuridJourney();
        $mj->getWhereOne("journey_murid_id=$id_murid AND journey_level_mulai=$id_lvl_hlp");
        if (!is_null($mj->journey_id)) {
            $mj->journey_level_mulai = $id_level;
            $mj->journey_mulai_date = leap_mysqldate();
            $mj->save(1);
            $json['status_code'] = 1;
            $json['status_message'] = "Level Murid berhasil di set ke Level Kurikulum Baru";
            echo json_encode($json);
            die();
        }
        // Journey diganti level murid sebelumnya ke yg skrg


    }
}