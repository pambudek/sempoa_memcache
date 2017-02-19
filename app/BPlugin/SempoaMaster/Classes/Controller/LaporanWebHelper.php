<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of LaporanWebHelper
 *
 * @author efindiongso
 */
class LaporanWebHelper extends WebService {

    //put your code here
    function loadIuranBulanan() {

        $status = new MuridWeb2Model();
        $arrs = $status->getAll();

        $arrSTatus = array();
        foreach ($arrs as $st) {
            $arrSTatus[$st->id_status_murid] = $st->status;
        }
        $bln = isset($_GET['bln']) ? addslashes($_GET['bln']) : date("n");
        $thn = isset($_GET['thn']) ? addslashes($_GET['thn']) : date("Y");
        $tc_id = isset($_GET['tc_id']) ? addslashes($_GET['tc_id']) : AccessRight::getMyOrgID();
        $murid = new MuridModel();
//        $arrMurid = $murid->getWhere("status = 1 OR status = 2 AND murid_tc_id = '$myorg' ORDER BY nama_siswa ASC");
        $iuranBulanan = new IuranBulanan();
        global $db;
        $q = "SELECT siswa.nama_siswa, siswa.status, siswa.id_level_sekarang, iuran.* FROM {$iuranBulanan->table_name} iuran INNER JOIN {$murid->table_name} siswa ON iuran.bln_murid_id = siswa.id_murid WHERE siswa.murid_tc_id='$tc_id' AND iuran.bln_mon = '$bln' AND iuran.bln_tahun = '$thn' AND (siswa.status = 1 or siswa.status = 2) ORDER by siswa.nama_siswa ASC";
//        pr($q);
        $arrIuranMurid = $db->query($q, 2);
//        pr($arrIuranMurid);

        $arrSTatus = array("<b>Unpaid</b>", "Paid");
        foreach ($arrIuranMurid as $iuran) {
            ?>
            <tr id='payment_<?= $iuran->bln_id; ?>'>
                <td><a style="cursor: pointer;" onclick="back_to_profile_murid('<?= $iuran->bln_murid_id; ?>');"><?= $iuran->nama_siswa; ?></a> </td>

                <td><?= Generic::getLevelNameByID($iuran->id_level_sekarang); ?></td>
                <td><?= $iuran->bln_date_pembayaran; ?></td>

                <td class ='kupon'>
                    <?
                    if ($iuran->bln_status) {
                        echo $iuran->bln_kupon_id;
                    } else {
                        ?>
                        <button id = 'pay_now_<?= $iuran->bln_id; ?>'class="btn btn-default">Pay Now</button>
                        <?
                    }
                    ?>
                </td>
                <td><?= $arrSTatus[$iuran->bln_status]; ?></td>

            </tr>
            <script>
                $('#pay_now_<?= $iuran->bln_id; ?>').click(function () {
                    openLw('murid_Invoices_<?= $iuran->bln_murid_id; ?>', '<?= _SPPATH; ?>MuridWebHelper/murid_invoices?id=<?= $iuran->bln_murid_id; ?>', 'fade');
                        })
            </script>
            <?
        }
    }

    function update_iuran_bulanan() {
        $bln_id = addslashes($_POST['bln_id']);
        $kupon_id = addslashes($_POST['kupon_id']);
        $kupon_owner = addslashes($_POST['kupon_owner']);
        $level_murid = addslashes($_POST['lvl_murid']);
        $jpb = addslashes($_POST['jpb']);
        // cari kupon ist vorhanden!

        $json['myid'] = $kupon_owner;
        $obKuponOwner = new KuponSatuan();
        $obKuponOwner->getWhereOne("kupon_id=$kupon_id AND kupon_owner_id=$kupon_owner AND kupon_status!=1");

        if($obKuponOwner->kupon_id == null){
            $json['status_code'] = 0;
            $json['status_message'] = "Kupon tidak ada di database! ";
            echo json_encode($json);
            die();
        }

        $thn_skrg = date("Y");
        $bln_skrg = date("n");
        $iuranBulanan = new IuranBulanan();
        $iuranBulanan->getByID($bln_id);
        $iuranBulanan->bln_status = 1;
        $iuranBulanan->bln_kupon_id = $kupon_id;
        $iuranBulanan->bln_date_pembayaran = date("Y-m-d H:i:s");
        $iuranBulanan->bln_no_urut_inv = $iuranBulanan->getLastNoUrutInvoice($thn_skrg, $bln_skrg, AccessRight::getMyOrgID());
        $iuranBulanan->bln_no_invoice = "SPP/" . $thn_skrg . "/".$bln_skrg."/". $iuranBulanan->bln_no_urut_inv;
        $iuranBulanan->bln_cara_bayar = $jpb;
        $iuranBulanan->save(1);

        $json['kuponsblm'] = $obKuponOwner;
        $kupon = new KuponSatuan();
        $kupon->getByID($kupon_id);
        $json['kupon'] = $kupon;
        $obKuponOwner->kupon_status = 1;
        $obKuponOwner->kupon_pemakaian_date = leap_mysqldate();

        $succ = $obKuponOwner->save(1);

//        bln_murid_id
        if ($succ) {
            //($buku_id,$keterangan,$debit,$credit,$org_id)
            $myID = AccessRight::getMyOrgID();
            $arrjenisBiayaSPP = Generic::getJenisBiayaType();
            $jenisBiayaSPP = $arrjenisBiayaSPP[$level_murid];

            Generic::createLaporanDebet($myID, $myID,KEY::$DEBET_IURAN_BULANAN_TC, $jenisBiayaSPP, "Iuran Bulanan: Siswa: " . Generic::getMuridNamebyID($iuranBulanan->bln_murid_id) . ", Bulan: " . $iuranBulanan->bln_date . " dgn Kode Kupon: " . $kupon_id, 1, 0,"Utama");
            $json['get'] = $_GET;
            $json['status_code'] = 1;
            $json['status_message'] = "Success!";
            echo json_encode($json);
            die();
        }
        $json['status_code'] = 0;
        $json['status_message'] = "Update failed!";
        echo json_encode($json);
        die();
    }

    function undo_iuran_bulanan() {
        $bln_id = addslashes($_POST['bln_id']);
        $kupon_id = addslashes($_POST['kupon_id']);
        $kupon_owner = addslashes($_POST['kupon_owner']);
        $level_murid = addslashes($_POST['lvl_murid']);

        // cari kupon ist vorhanden!

        $obKuponOwner = new KuponSatuan();
        $obKuponOwner->getWhereOne("kupon_id=$kupon_id AND kupon_owner_id=$kupon_owner AND kupon_status=1");

        if($obKuponOwner->kupon_id == null){
            $json['status_code'] = 0;
            $json['status_message'] = "Kupon tidak ada di database! ";
            echo json_encode($json);
            die();
        }

        $iuranBulanan = new IuranBulanan();
        $iuranBulanan->getByID($bln_id);
        $iuranBulanan->bln_status = 0;
        $iuranBulanan->bln_kupon_id = 0;
        $iuranBulanan->bln_date_pembayaran =  KEY::$TGL_KOSONG;
        $iuranBulanan->bln_no_urut_inv = "";
        $iuranBulanan->bln_no_invoice = "";
        $iuranBulanan->bln_cara_bayar = 0;
        $iuranBulanan->save(1);

        $kupon = new KuponSatuan();
        $kupon->getByID($kupon_id);
        $obKuponOwner->kupon_status = 0;
        $obKuponOwner->kupon_pemakaian_date = KEY::$TGL_KOSONG;
        $succ = $obKuponOwner->save(1);
        $json['kupon'] = $kupon;
        if ($succ) {
            //($buku_id,$keterangan,$debit,$credit,$org_id)
            $arrjenisBiayaSPP = Generic::getJenisBiayaType();
            $jenisBiayaSPP = $arrjenisBiayaSPP[$level_murid];

            Generic::createLaporanDebet($kupon_owner, $kupon_owner,KEY::$DEBET_IURAN_BULANAN_TC, $jenisBiayaSPP, "Iuran Bulanan: Siswa: " . Generic::getMuridNamebyID($iuranBulanan->bln_murid_id) . ", Bulan: " . $iuranBulanan->bln_date . " dgn Kode Kupon: " . $kupon_id, -1, 0,"Utama");
            $json['get'] = $_GET;
            $json['status_code'] = 1;
            $json['status_message'] = "Success!";
            echo json_encode($json);
            die();
        }
        $json['status_code'] = 0;
        $json['status_message'] = "Update failed!";
        echo json_encode($json);
        die();
    }


    function paymentDetails() {
        $bln_id = addslashes($_GET['bln_id']);
//        $bln_id = 29;
        $iuranBulanan = new IuranBulanan();
        $iuranBulanan->getByID($bln_id);
        $kupon = new KuponSatuan();
        $kupon->getByID($iuranBulanan->bln_kupon_id);
//        pr($iuranBulanan);
        ?>
        <section class="content-header" >
            <h1>
                <div class="pull-right"><button class="btn btn-default" onclick="back_to_profile_murid('<?= $iuranBulanan->bln_murid_id; ?>');">back to profile</button></div>
                Payment Details: <?= (Generic::getMuridNamebyID($iuranBulanan->bln_murid_id)) ?>


            </h1>

        </section>
        <div class="row2" style="padding-top: 20px;">
            <div class="col-md-9 col-sm-6 col-xs-12">
                <table class="table table-striped table-responsive">
                    <tr>
                        <td>
                            Iuran Bulan:
                        </td>
                        <td>
                            <?= $iuranBulanan->bln_date; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Kode Kupon:
                        </td>
                        <td>
                            <?= $iuranBulanan->bln_kupon_id; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Tanggal pembayaran:
                        </td>
                        <td>
                            <?= $kupon->kupon_pemakaian_date; ?>
                        </td>
                    </tr>


                </table>
            </div>



        </div>
        <?
    }

    function paymentDetailsBuku() {
        $bln_id = addslashes($_GET['bln_id']);
//        $bln_id = 29;
        $iuranBulanan = new IuranBuku();
        $iuranBulanan->getByID($bln_id);
        $arrjp = Generic::getJenisPembayaran();
        ?>
        <section class="content-header" >
            <h1>
                <div class="pull-right"><button class="btn btn-default" onclick="back_to_profile_murid('<?= $iuranBulanan->bln_murid_id; ?>');">back to profile</button></div>

                Payment Details: <?= (Generic::getMuridNamebyID($iuranBulanan->bln_murid_id)) ?>
            </h1>

        </section>
        <div class="row2" style="padding-top: 20px;">
            <div class="col-md-9 col-sm-6 col-xs-12">
                <table class="table table-striped table-responsive">

                    <tr>
                        <td>
                            Level:
                        </td>
                        <td>
                            <?= Generic::getLevelNameByID($iuranBulanan->bln_buku_level); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Tanggal pembayaran:
                        </td>
                        <td>
                            <?= $iuranBulanan->bln_date_pembayaran; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Cara Pembayaran:
                        </td>
                        <td>
                            <?= $arrjp[$iuranBulanan->bln_cara_bayar]; ?>
                        </td>
                    </tr>

                </table>
            </div>



        </div>
        <?
    }

    public function pay_iuran_buku_tmp() {
        $bln_id = addslashes($_GET['bln_id']);
        $cara_pby = addslashes($_GET['cara_pby']);
        $iuranBuku = new IuranBuku();
        $iuranBuku->getByID($bln_id);

        $myOrg = AccessRight::getMyOrgID();
        // update stock buku
        $myBuku = new BarangWebModel();
        $arrMyBuku = $myBuku->getWhere("level=' $iuranBuku->bln_buku_level'");
        if (count($arrMyBuku) > 0) {
            // Check Stock
//            pr($arrMyBuku);
            $stockBarang = new StockModel();
            foreach ($arrMyBuku as $buku) {
                $arrStock = $stockBarang->getWhere("id_barang='$buku->id_barang_harga' AND org_id='$myOrg'");

                if (count($arrStock) == 0) {
                    $json['status_code'] = 0;
                    $json['status_message'] = "Jumlah stock buku habis!";
                    echo json_encode($json);
                    die();
                }
                if (($arrStock[0]->jumlah_stock) > 0) {
                    $arrStock[0]->jumlah_stock = $arrStock[0]->jumlah_stock - 1;
                    $arrStock[0]->save(1);
                    $mj = new MuridJourney();
                    $arrJourney = $mj->getWhere("journey_murid_id='$iuranBuku->bln_murid_id'");
                    $arrJourney[0]->journey_level_end = $objMurid->id_level_sekarang;
                    $arrJourney[0]->journey_end_date = leap_mysqldate();
                    $arrJourney[0]->save(1);
                    $objMurid = new MuridModel();
                    $objMurid->getByID($iuranBuku->bln_murid_id);
                    $objMurid->id_level_sekarang = $iuranBuku->bln_buku_level;
                    $objMurid->save(1);
                    $iuranBuku->bln_date_pembayaran = leap_mysqldate();
                    $iuranBuku->bln_status = 1;
                    $iuranBuku->bln_cara_bayar = $cara_pby;
                    $iuranBuku->save(1);
                    $mj_new = new MuridJourney();
                    $mj_new->journey_murid_id = $iuranBuku->bln_murid_id;
                    $mj_new->journey_level_mulai = $iuranBuku->bln_buku_level;
                    $mj_new->journey_mulai_date = leap_mysqldate();
                    $mj_new->journey_tc_id = AccessRight::getMyOrgID();
                    $mj_new->save();
                    $json['status_code'] = 1;
                    $json['status_message'] = "Pembayaran Berhasil!";
                    echo json_encode($json);
                    die();
                } else {
                    $json['status_code'] = 0;
                    $json['status_message'] = "Jumlah stock buku habis!";
                    echo json_encode($json);
                    die();
                }
            }
        }
        $json['status_code'] = 0;
        $json['status_message'] = "Pembayaran gagal!";
        echo json_encode($json);
        die();
        // update level siswa
    }

    public function pay_iuran_buku() {

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

        foreach ($arrIuranBuku as $key => $val) {
            ?>
            <tr>
                <td>
                    <a style="cursor: pointer;" onclick="back_to_profile_murid('<?= $val->bln_murid_id; ?>');"><?= $val->nama_siswa; ?></a> 
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

        <?
    }

    public function pay_iuran_buku_roy() {
        $bln = isset($_GET['bln']) ? addslashes($_GET['bln']) : date("n");
        $thn = isset($_GET['thn']) ? addslashes($_GET['thn']) : date("Y");
        $tc_id = isset($_GET['tc_id']) ? addslashes($_GET['tc_id']) : AccessRight::getMyOrgID();

        $bln_id = addslashes($_POST['bln_id']);
        $cara_pby = addslashes($_POST['cara_pby']);

        $iuranBuku = new IuranBuku();
        $iuranBuku->getByID($bln_id);
//        pr($iuranBuku);
        $myOrg = AccessRight::getMyOrgID();
        
        $myParentID = Generic::getMyParentID($myOrg);
        $myGrandParentID = Generic::getMyParentID($myParentID);
        // update stock buku
//        $myBuku = new BarangWebModel();
//        $arrMyBuku = $myBuku->getWhere("level=' $iuranBuku->bln_buku_level' AND jenis_biaya = 1 LIMIT 0,1");
//
//
//        
         $myBuku = new BarangWebModel();
         $arrMyBuku = $myBuku->getWhere("level=$iuranBuku->bln_buku_level  AND jenis_biaya = 1 AND kpo_id = $myGrandParentID LIMIT 0,1");
                            
        if (count($arrMyBuku) > 0) {
            // Check Stock

            $stockBarang = new StockModel();
            $buku_active = array_pop($arrMyBuku);

            $stockBarang->getWhereOne("id_barang='$buku_active->id_barang_harga' AND org_id='$myOrg'");


            if ($stockBarang->jumlah_stock > 0) {
                //ada stok
                //kurangi stok
                $stockBarang->jumlah_stock--;
                $stockBarang->save();

                //iuran buku dibayar
                $iuranBuku->bln_status = 1;
                $iuranBuku->bln_cara_bayar = $cara_pby;
                $iuranBuku->bln_date_pembayaran = leap_mysqldate();
                $thn_skrg = date("Y");
                $bln_skrg = date("n");
                $iuranBuku->bln_no_urut_inv = $iuranBuku->getLastNoUrutInvoice($thn_skrg, $bln_skrg, AccessRight::getMyOrgID());
                $iuranBuku->bln_no_invoice = "IB/" . $thn_skrg . "/".$bln_skrg."/". $iuranBuku->bln_no_urut_inv;

                if ($iuranBuku->save()) {
                    //murid update level
                    $objMurid = new MuridModel();
                    $objMurid->getByID($iuranBuku->bln_murid_id);
                    $level_sebelumnya = $objMurid->id_level_sekarang;
                    $objMurid->id_level_sekarang = $iuranBuku->bln_buku_level;

                    if ($objMurid->save()) {

                        //journey
                        //journey lama diupdate
                        $mj = new MuridJourney();
                        $mj->getWhereOne("journey_murid_id='$iuranBuku->bln_murid_id' AND journey_level_mulai = '$level_sebelumnya'");
                        $mj->journey_level_end = $objMurid->id_level_sekarang;
                        $mj->journey_end_date = leap_mysqldate();
                        $mj->save(1);


                        //journey baru ditambah
                        $mj_new = new MuridJourney();
                        $mj_new->journey_murid_id = $iuranBuku->bln_murid_id;
                        $mj_new->journey_level_mulai = $iuranBuku->bln_buku_level;
                        $mj_new->journey_mulai_date = leap_mysqldate();
                        $mj_new->journey_tc_id = AccessRight::getMyOrgID();
                        $mj_new->save();

                        // Laporan
                        $myID = AccessRight::getMyOrgID();
                        Generic::createLaporanDebet($myID, $myID,KEY::$DEBET_IURAN_BUKU_TC, KEY::$BIAYA_IURAN_BUKU, "Iuran Buku: Siswa: " . Generic::getMuridNamebyID($iuranBuku->bln_murid_id), 1, 0,"Utama");

                        // Check apakah butuh sertifikat
                        $needCertificate = Generic::istLevelNeedCertificate($level_sebelumnya);
                        if ($needCertificate) {
                            $parent_id = Generic::getMyParentID(AccessRight::getMyOrgID());
                            $certificate = new SertifikatModel();
                            $certificate->createSertifikatTC($tc_id, $iuranBuku->bln_murid_id, $level_sebelumnya);
                             SempoaInboxModel::sendMsg($parent_id,AccessRight::getMyOrgID(),"Permintaan Sertifikat", Generic::getTCNamebyID($tc_id) . " request Sertifikat untuk murid: " . Generic::getMuridNamebyID( $iuranBuku->bln_murid_id));

                        }







                        $json['status_code'] = 1;
                        $json['status_message'] = "Pembayaran Berhasil! Silahkan sesuaikan Kelasnya";
                        echo json_encode($json);
                        die();
                    } else {
                        $json['status_code'] = 0;
                        $json['status_message'] = "Gagal Update Murid";
                        echo json_encode($json);
                        die();
                    }
                } else {
                    $json['status_code'] = 0;
                    $json['status_message'] = "Gagal Save Iuran Buku";
                    echo json_encode($json);
                    die();
                }
            }


            //tidak ada stok
            $json['status_code'] = 0;
            $json['status_message'] = "Jumlah stock buku habis!";
            echo json_encode($json);
            die();
        }
        $json['status_code'] = 0;
        $json['status_message'] = "Pembayaran gagal!";
        echo json_encode($json);
        die();
        // update level siswa
    }

    public function undo_iuran_buku() {
        $bln = isset($_GET['bln']) ? addslashes($_GET['bln']) : date("n");
        $thn = isset($_GET['thn']) ? addslashes($_GET['thn']) : date("Y");
        $tc_id = isset($_GET['tc_id']) ? addslashes($_GET['tc_id']) : die();

        $bln_id = addslashes($_POST['bln_id']);

        $iuranBuku = new IuranBuku();
        $iuranBuku->getByID($bln_id);
//        pr($iuranBuku);
        $myOrg = $tc_id;

        $myParentID = Generic::getMyParentID($myOrg);
        $myGrandParentID = Generic::getMyParentID($myParentID);
        // update stock buku

        $myBuku = new BarangWebModel();
        $arrMyBuku = $myBuku->getWhere("level=$iuranBuku->bln_buku_level  AND jenis_biaya = 1 AND kpo_id = $myGrandParentID LIMIT 0,1");

        if (count($arrMyBuku) > 0) {
            // Check Stock

            $stockBarang = new StockModel();
            $buku_active = array_pop($arrMyBuku);

            $stockBarang->getWhereOne("id_barang='$buku_active->id_barang_harga' AND org_id='$myOrg'");


            if ($stockBarang->jumlah_stock > 0) {
                //ada stok
                //kurangi stok
                $stockBarang->jumlah_stock ++;
                $stockBarang->save();

                //iuran buku dibayar
                $iuranBuku->bln_status = 0;
                $iuranBuku->bln_cara_bayar = 0;
                $iuranBuku->bln_date_pembayaran = KEY::$TGL_KOSONG;
                $iuranBuku->bln_no_urut_inv = "";
                $iuranBuku->bln_no_invoice = "";

                if ($iuranBuku->save()) {
                    //murid update level
                    $objMurid = new MuridModel();
                    $objMurid->getByID($iuranBuku->bln_murid_id);
                    $level_sebelumnya = $objMurid->id_level_sekarang;
                    $objMurid->id_level_sekarang = $iuranBuku->bln_buku_level;

                    if ($objMurid->save()) {

                        //journey
                        //journey lama diupdate
                        $mj = new MuridJourney();
                        $mj->getWhereOne("journey_murid_id='$iuranBuku->bln_murid_id' AND journey_level_mulai = '$level_sebelumnya'");
                        $mj->journey_level_end = $objMurid->id_level_sekarang;
                        $mj->journey_end_date = leap_mysqldate();
                        $mj->save(1);


                        //journey baru ditambah
                        $mj_new = new MuridJourney();
                        $mj_new->journey_murid_id = $iuranBuku->bln_murid_id;
                        $mj_new->journey_level_mulai = $iuranBuku->bln_buku_level;
                        $mj_new->journey_mulai_date = leap_mysqldate();
                        $mj_new->journey_tc_id = AccessRight::getMyOrgID();
                        $mj_new->save();

                        // Laporan
                        $myID = AccessRight::getMyOrgID();
                        Generic::createLaporanDebet($myID, $myID,KEY::$DEBET_IURAN_BUKU_TC, KEY::$BIAYA_IURAN_BUKU, "Iuran Buku: Siswa: " . Generic::getMuridNamebyID($iuranBuku->bln_murid_id), 1, 0,"Utama");

                        // Check apakah butuh sertifikat
                        $needCertificate = Generic::istLevelNeedCertificate($level_sebelumnya);
                        if ($needCertificate) {
                            $parent_id = Generic::getMyParentID(AccessRight::getMyOrgID());
                            $certificate = new SertifikatModel();
                            $certificate->createSertifikatTC($tc_id, $iuranBuku->bln_murid_id, $level_sebelumnya);
                            SempoaInboxModel::sendMsg($parent_id,AccessRight::getMyOrgID(),"Permintaan Sertifikat", Generic::getTCNamebyID($tc_id) . " request Sertifikat untuk murid: " . Generic::getMuridNamebyID( $iuranBuku->bln_murid_id));

                        }







                        $json['status_code'] = 1;
                        $json['status_message'] = "Pembayaran Berhasil! Silahkan sesuaikan Kelasnya";
                        echo json_encode($json);
                        die();
                    } else {
                        $json['status_code'] = 0;
                        $json['status_message'] = "Gagal Update Murid";
                        echo json_encode($json);
                        die();
                    }
                } else {
                    $json['status_code'] = 0;
                    $json['status_message'] = "Gagal Save Iuran Buku";
                    echo json_encode($json);
                    die();
                }
            }


            //tidak ada stok
            $json['status_code'] = 0;
            $json['status_message'] = "Jumlah stock buku habis!";
            echo json_encode($json);
            die();
        }
        $json['status_code'] = 0;
        $json['status_message'] = "Pembayaran gagal!";
        echo json_encode($json);
        die();
        // update level siswa
    }

    function loadLaporantc() {
        $bln = isset($_GET['bln']) ? addslashes($_GET['bln']) : date("n");
        $thn = isset($_GET['thn']) ? addslashes($_GET['thn']) : date("Y");
        $type = addslashes($_GET['type']);
        $tc_id = addslashes($_GET['tc_id']);
        $transaksi = new TransaksiModel();
        $arrTransaksi = $transaksi->getWhere("entry_org_id = '$tc_id'  AND entry_akun_id = " . $type . "  AND (MONTH(entry_date)=$bln) AND (YEAR(entry_date)=$thn) ORDER BY entry_date DESC");
//        pr($arrTransaksi);
        $debet = 0;
        foreach ($arrTransaksi as $tr) {
            ?>
            <tr>
                <td><?= $tr->entry_date; ?></td>
                <td><?= $tr->entry_keterangan; ?></td>
                <td class="angka"><?
                    if ($tr->entry_debit == 0) {
                        $debet +=$tr->entry_credit;
                        echo idr($tr->entry_credit);
                    } else {
                        $debet +=$tr->entry_debit;
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
        <?
    }

    public  function getWeekInYear(){
        $year = $_POST['thn'];
        $arrWeek = Generic::getDateRangeByWeek($year);
//        krsort($arrWeek);
        $date = new DateTime('today');
        $todayweek = $date->format("W");
        $t = time();
        ?>
        <select id="minggu_<?= $t; ?>">
            <?
            foreach ($arrWeek as $key => $week) {
                $sel = "";
                if (($key) == ($todayweek)) {

                    $sel = "selected";
                }
                ?>
                <option value="<?= $key; ?>" <?= $sel; ?>><?= $week; ?></option>
                <?
            }
            ?>
        </select>
        <?
    }

    
    function testCodeGuru() {
         $o = new BiayaTrainingModel();
         $o->printColumlistAsAttributes();
        $tc_id = AccessRight::getMyOrgID();
        $tc_id = 96;
        $org_code = Generic::getOrgCode($tc_id);
        pr("org_code: " . $org_code);
        pr(Generic::fCreateKodeSiswa());
//         pr(Generic::getLastKodeGuru($tc_id));
        pr("Kode Guru: " . Generic::fCreateKode(KEY::$GURU, $tc_id));
        pr("Kode Murid: " . Generic::fCreateKode(KEY::$MURID, $tc_id));
        pr("Kode Trainer: " . Generic::fCreateKode(KEY::$TRAINER, 200));
    }

}
