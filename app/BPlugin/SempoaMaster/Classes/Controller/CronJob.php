<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CronJob
 *
 * @author efindiongso
 */
class CronJob extends WebService
{

    /*
     *  14.12.2016, total ada 3 Cronjob yg aktiv dari Class ini
     * 1. create_invoice_spp_cronjobAllTC -> tiap awal bulan jam 00:10!
     * 2. create_rekap_siswa ->tiap Malam Weekdays
     * 3. cronJobRekapKuponBulanan ->tiap Malam Weekdays
     * 4. setMuridKeluar -> tiap malam
     */


//put your code here

    /*
     * Cron Job Uang SPP
     * Cronjob ini diaktivkan/ dijalankan setiap awal bulan tgl 1, jam 00:00
     * Aktiv!
     */

    public function create_invoice_spp_cronjobAllTC()
    {
        $bln = isset($_GET['bln']) ? addslashes($_GET['bln']) : date("n");
        $thn = isset($_GET['thn']) ? addslashes($_GET['thn']) : date("Y");
        $murid = new MuridModel();
        $arrMurid = $murid->getWhere("status = 1");
        $total = 0;
        foreach ($arrMurid as $mur) {
//


            $iur = new IuranBulanan();
            $idKey = $mur->id_murid . "_" . $bln . "_" . $thn;
//            $cnt = $iur->getJumlah("bln_murid_id = '{$mur->id_murid}' AND bln_mon = '$bln' AND bln_tahun = '$thn'");
            $cnt = $iur->getJumlah("bln_id = '$idKey'");
            if ($cnt > 0)
                continue;
            $iur->bln_id = $idKey;
            $iur->bln_murid_id = $mur->id_murid;
            $iur->bln_date = $bln . "-" . $thn;
            $iur->bln_mon = $bln;
            $iur->bln_tahun = $thn;
            $iur->bln_tc_id = $mur->murid_tc_id;
            $iur->bln_kpo_id = $mur->murid_kpo_id;
            $iur->bln_ibo_id = $mur->murid_ibo_id;
            $iur->bln_ak_id = $mur->murid_ak_id;
            $iur->bln_create_date = leap_mysqldate();

            $iuranbulanan = new IuranBulanan();
            $iuranbulanan->getWhereOne("bln_murid_id='$idKey' ORDER by  bln_urutan_invoice_murid  DESC");
            if (is_null($iuranbulanan->bln_id)) {
                $iur->bln_urutan_invoice_murid = 1;
            } else {
                $iur->bln_urutan_invoice_murid = $iuranbulanan->bln_urutan_invoice_murid + 1;
            }

            if ($iur->save()) {
                $total++;
            }
//            }
        }
        echo "Total ter create " . $total . " Invoice(SPP)!";
    }


    public function create_invoice_spp_cronjobAllTC2()
    {
        $bln = isset($_GET['bln']) ? addslashes($_GET['bln']) : date("n");
        $thn = isset($_GET['thn']) ? addslashes($_GET['thn']) : date("Y");
        $murid = new MuridModel();
        $arrMurid = $murid->getWhere("id_murid >= 6957 AND id_murid <= 8479 AND status = 1");
        pr(count($arrMurid));
        $total = 0;
        $totaltdksave = 0;
        foreach ($arrMurid as $mur) {
//


            $iur = new IuranBulanan();
            $idKey = $mur->id_murid . "_" . $bln . "_" . $thn;
//            $cnt = $iur->getJumlah("bln_murid_id = '{$mur->id_murid}' AND bln_mon = '$bln' AND bln_tahun = '$thn'");
            $cnt = $iur->getJumlah("bln_id = '$idKey'");
            if ($cnt > 0)
                continue;
            $iur->bln_id = $idKey;
            $iur->bln_murid_id = $mur->id_murid;
            $iur->bln_date = $bln . "-" . $thn;
            $iur->bln_mon = $bln;
            $iur->bln_tahun = $thn;
            $iur->bln_tc_id = $mur->murid_tc_id;
            $iur->bln_kpo_id = $mur->murid_kpo_id;
            $iur->bln_ibo_id = $mur->murid_ibo_id;
            $iur->bln_ak_id = $mur->murid_ak_id;
            $iur->bln_create_date = leap_mysqldate();

            $iuranbulanan = new IuranBulanan();
            $iuranbulanan->getWhereOne("bln_murid_id='$idKey' ORDER by  bln_urutan_invoice_murid  DESC");
            if (is_null($iuranbulanan->bln_id)) {
                $iur->bln_urutan_invoice_murid = 1;
            } else {
                $iur->bln_urutan_invoice_murid = $iuranbulanan->bln_urutan_invoice_murid + 1;
            }

            if ($iur->save()) {
                $total++;
            } else {
                $totaltdksave++;
            }
//            }
        }
        echo "Total ter create " . $total . " Invoice(SPP)! <br>";
        echo "Total tdk create " . $totaltdksave . " Invoice(SPP)!";
    }

    /*
     * Cronjob ini dijalankan spesifikasi tc
     */

    function create_invoice_cronjob_perTC()
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
            $cnt = $iur->getJumlah("bln_murid_id = '{$mur->id_murid}' AND bln_mon = '$bln' AND bln_tahun = '$thn' AND bln_tc_id=$tc_id");
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
            $iur->bln_create_date = leap_mysqldate();
            if ($iur->save()) {
                $total++;
            }
        }
        echo $total;
    }

    /*
     * Aktiv! akhir bulan
     */

    public function create_rekap_siswa()
    {

        $bln = isset($_GET['bln']) ? addslashes($_GET['bln']) : date("n");
        $thn = isset($_GET['thn']) ? addslashes($_GET['thn']) : date("Y");

        $ak = new SempoaOrg();
        $arrAK = $ak->getWhere("org_type='ak'");
        $allAK = array();
        foreach ($arrAK as $val) {
            $allAK[$val->org_id] = $val->nama;
        }

        $waktu = $bln . "-" . $thn;
        $createBaru = 0;
        $update = 0;
        foreach ($allAK as $keyak => $valAK) {
            $arrKPO = Generic::getAllMyKPO($keyak);

            foreach ($arrKPO as $keyKPO => $valKPO) {
                $arrIBO = Generic::getAllMyIBO($keyKPO);
                foreach ($arrIBO as $keyIBO => $valIBO) {
                    $arrTC = Generic::getAllMyTC($keyIBO);

                    foreach ($arrTC as $keyTC => $valTC) {
                        $rekap_siswa = new RekapSiswaIBOModel();
                        $rekap_siswa->getWhereOne("bi_rekap_tc_id = '$keyTC' AND bi_rekap_ibo_id='$keyIBO' AND bi_rekap_kpo_id='$keyKPO' AND bi_rekap_ak_id='$keyak' AND bi_rekap_siswa_waktu = '$waktu' ");
                        $logSiswa = new LogStatusMurid();
                        $murid = new MuridModel();
//                        $jmlhMuridAktiv = $murid->getMuridAktiv($keyTC);
                        $jmlhMuridAktiv = $logSiswa->getCountSiswaByStatusOrgType($bln, $thn, 'A', "tc", $keyTC);
                        $jmlhMuridBaru = $this->getMuridBaruByTC($keyTC, $bln, $thn);
                        $jmlhMuridCuti = $logSiswa->getCountSiswaByStatusOrgType($bln, $thn, 'C', "tc", $keyTC);
                        $jmlhMuridKeluar = $logSiswa->getCountSiswaByStatusOrgType($bln, $thn, 'K', "tc", $keyTC);
                        $jmlhMuridLulus = $logSiswa->getCountSiswaByStatusOrgType($bln, $thn, 'L', "tc", $keyTC);
                        $jmlhMuridBL = $rekap_siswa->getCountSiswaAktivBulanLalu($bln, $thn, $keyTC);
                        if ($jmlhMuridBaru > 0) {
                        }

                        if (!is_null($rekap_siswa->bi_rekap_kode_tc)) {
                            $rekap_siswa->delete($rekap_siswa->bi_rekap_siswa_id);
                            echo "TC: " . $rekap_siswa->bi_rekap_kode_tc . " " . Generic::getTCNamebyID($rekap_siswa->bi_rekap_kode_tc) . " kedelete untuk bulan " . $waktu . " nama Murid: " . Generic::getMuridNamebyID($rekap_siswa->bi_rekap_siswa_id) . "<br>";
                        }

                        $rekap_siswa = new RekapSiswaIBOModel();

                        $tc = new SempoaOrg();
                        $tc->getByID($keyTC);
                        $rekap_siswa->bi_rekap_tc_id = $keyTC;
                        $rekap_siswa->bi_rekap_ibo_id = $keyIBO;
                        $rekap_siswa->bi_rekap_kpo_id = $keyKPO;
                        $rekap_siswa->bi_rekap_ak_id = $keyak;
                        $rekap_siswa->bi_rekap_siswa_waktu = $waktu;
                        $rekap_siswa->bi_rekap_kode_tc = $tc->org_kode;
                        $rekap_siswa->bi_rekap_nama_tc = $tc->nama;
                        $rekap_siswa->bi_rekap_nama_director = $tc->nama_pemilik;
                        $rekap_siswa->bi_rekap_bl = $jmlhMuridBL;
                        $rekap_siswa->bi_rekap_baru = $jmlhMuridBaru;
                        $rekap_siswa->bi_rekap_aktiv = $jmlhMuridAktiv;
                        $rekap_siswa->bi_rekap_cuti = $jmlhMuridCuti;
                        $rekap_siswa->bi_rekap_keluar = $jmlhMuridKeluar;
                        $rekap_siswa->bi_rekap_lulus = $jmlhMuridLulus;
                        $rekap_siswa->bi_rekap_kupon = $this->getPenjualanKuponByTC($keyTC, $bln, $thn);
                        $rekap_siswa->bi_rekap_jumlah_guru = $this->getGuruAktivByTC($keyTC, $bln, $thn);
                        $rekap_siswa->bi_rekap_bln = $bln;
                        $rekap_siswa->bi_rekap_tahun = $thn;
                        $rekap_siswa->bi_rekap_buku = $this->getPenjualanBukuByTC($keyTC, $bln, $thn);
                        $rekap_siswa->save();
                        echo Generic::getTCNamebyID($rekap_siswa->bi_rekap_kode_tc) . " create untuk bulan " . $waktu . "<br>";
                        $createBaru++;


//                        if (is_null($rekap_siswa->bi_rekap_kode_tc)) {
//                            $rekap_siswa = new RekapSiswaIBOModel();
//
//                            $tc = new SempoaOrg();
//                            $tc->getByID($keyTC);
//                            $rekap_siswa->bi_rekap_tc_id = $keyTC;
//                            $rekap_siswa->bi_rekap_ibo_id = $keyIBO;
//                            $rekap_siswa->bi_rekap_kpo_id = $keyKPO;
//                            $rekap_siswa->bi_rekap_ak_id = $keyak;
//                            $rekap_siswa->bi_rekap_siswa_waktu = $waktu;
//                            $rekap_siswa->bi_rekap_kode_tc = $tc->org_kode;
//                            $rekap_siswa->bi_rekap_nama_tc = $tc->nama;
//                            $rekap_siswa->bi_rekap_nama_director = $tc->nama_pemilik;
//                            $rekap_siswa->bi_rekap_bl = $jmlhMuridBL;
//                            $rekap_siswa->bi_rekap_baru = $jmlhMuridBaru;
//                            $rekap_siswa->bi_rekap_aktiv = $jmlhMuridAktiv;
//                            $rekap_siswa->bi_rekap_cuti = $jmlhMuridCuti;
//                            $rekap_siswa->bi_rekap_keluar = $jmlhMuridKeluar;
//                            $rekap_siswa->bi_rekap_lulus = $jmlhMuridLulus;
//                            $rekap_siswa->bi_rekap_kupon = $this->getPenjualanKuponByTC($keyTC, $bln, $thn);
//                            $rekap_siswa->bi_rekap_jumlah_guru = $this->getGuruAktivByTC($keyTC, $bln, $thn);
//                            $rekap_siswa->bi_rekap_bln = $bln;
//                            $rekap_siswa->bi_rekap_tahun = $thn;
//                            $rekap_siswa->bi_rekap_buku = $this->getPenjualanBukuByTC($keyTC, $bln, $thn);
//                            $rekap_siswa->save();
//                            $createBaru++;
//                        } else {
//                            $rekap_siswa->bi_rekap_bl = $jmlhMuridBL;
//                            $rekap_siswa->bi_rekap_baru = $jmlhMuridBaru;
//                            $rekap_siswa->bi_rekap_aktiv = $jmlhMuridAktiv;
//                            $rekap_siswa->bi_rekap_cuti = $jmlhMuridCuti;
//                            $rekap_siswa->bi_rekap_keluar = $jmlhMuridKeluar;
//                            $rekap_siswa->bi_rekap_lulus = $jmlhMuridLulus;
//                            $rekap_siswa->bi_rekap_kupon = $this->getPenjualanKuponByTC($keyTC, $bln, $thn);
//                            $rekap_siswa->bi_rekap_jumlah_guru = $this->getGuruAktivByTC($keyTC, $bln, $thn);
//                            $rekap_siswa->bi_rekap_buku = $this->getPenjualanBukuByTC($keyTC, $bln, $thn);
//                            $rekap_siswa->save(1);
//                            $update++;
//                        }
                    }
                }
            }
        }
        if ($createBaru != 0) {
            echo "Tercreate sebanyak " . $createBaru . " rows <br>";
            $mail = new Leapmail2();
            $mail->sendHTMLEmail("efindi.ongso@gmail.com","Cron Job create_rekap_siswa","",$createBaru . " rows " );
        }
        if ($update != 0) {
            echo "Update sebanyak " . $update . " rows <br>";
            $mail = new Leapmail2();
            $mail->sendHTMLEmail("efindi.ongso@gmail.com","Cron Job create_rekap_siswa","", $update . " rows" );
        }
    }


    /*
     * Cronjob Rekap Kupon
     * cronJobRekapKuponAll -> tdk di pake, di buat utk test aja
     */

    public function cronJobRekapKuponAll()
    {
        $i = 0;
        $arrmonth = Generic::getAllMonths();
        $thn = date("Y");
        foreach ($arrmonth as $bln) {

            $arrAllAK = Generic::getAllAK();
            foreach ($arrAllAK as $ak_id => $namaAK) {
                $arrKPO = Generic::getAllMyKPO($ak_id);
                foreach ($arrKPO as $kpo_id => $namaKPO) {
                    $arrIBO = Generic::getAllMyIBO($kpo_id);
                    foreach ($arrIBO as $ibo_id => $namaIBO) {
                        $arrTC = Generic::getAllMyTC($ibo_id);

                        foreach ($arrTC as $tc_id => $namaTC) {

                            $objRekapKupon = new BIRekapKuponModel();
//                                bi_kupon_ak_id, bi_kupon_kpo_id, bi_kupon_ibo_id, bi_kupon_tc_id, bi_kupon_bln,bi_kupon_thn
                            $arrResult = $objRekapKupon->getWhere("bi_kupon_ak_id=$ak_id AND bi_kupon_kpo_id=$kpo_id AND bi_kupon_ibo_id=$ibo_id AND bi_kupon_tc_id=$tc_id AND bi_kupon_bln=$bln AND bi_kupon_thn=$thn");

                            if (count($arrResult) == 0) {
                                echo "masuk";
                                $objRekapKupon = new BIRekapKuponModel();
                                $objRekapKupon->bi_kupon_ak_id = $ak_id;
                                $objRekapKupon->bi_kupon_kpo_id = $kpo_id;
                                $objRekapKupon->bi_kupon_ibo_id = $ibo_id;
                                $objRekapKupon->bi_kupon_tc_id = $tc_id;
                                $objRekapKupon->bi_kupon_bln = $bln;
                                $objRekapKupon->bi_kupon_thn = $thn;
                                $objRekapKupon->bi_kupon_waktu = $bln . "-" . $thn;
                                $jumlahKupon = 0;
                                $kupon = new KuponBundle();
                                $jumlahKupon = $kupon->getJumlahKuponByTC($bln, $thn, $tc_id);

                                $kuponSatuan = new KuponSatuan();
                                $jmlhIuaran = $kuponSatuan->getJumlahKupgitonTerpakaiByTC($bln, $thn, $tc_id);
                                $biRekapModel = new BIRekapKuponModel();
                                $jmlhStock = $biRekapModel->getDatenPrevMonth($bln, $thn, $ak_id, $kpo_id, $ibo_id, $tc_id);

                                $objRekapKupon->bi_kupon_stock = $jmlhStock;
                                $objRekapKupon->bi_kupon_kupon_masuk = $jumlahKupon;
                                $objRekapKupon->bi_kupon_trs_bln = $jmlhIuaran;
                                $objRekapKupon->bi_kupon_stock_akhir = ($jmlhStock + $jumlahKupon) - $jmlhIuaran;
                                $murid = new MuridModel();
                                $objRekapKupon->bi_kupon_murid_aktiv = $murid->getMuridAktiv($tc_id);
                                $objRekapKupon->save();
                                $i++;
                            }
                        }
                    }
                }
            }
        }
    }


    // Aktiv!
    public function cronJobRekapKuponBulanan()
    {
        $bln = isset($_GET['bln']) ? addslashes($_GET['bln']) : date("n");
        $thn = isset($_GET['thn']) ? addslashes($_GET['thn']) : date("Y");
        $createBaru = 0;
        $update = 0;
        $arrAllAK = Generic::getAllAK();
        foreach ($arrAllAK as $ak_id => $namaAK) {
            $arrKPO = Generic::getAllMyKPO($ak_id);
            foreach ($arrKPO as $kpo_id => $namaKPO) {
                $arrIBO = Generic::getAllMyIBO($kpo_id);
                foreach ($arrIBO as $ibo_id => $namaIBO) {
                    $arrTC = Generic::getAllMyTC($ibo_id);

                    foreach ($arrTC as $tc_id => $namaTC) {

                        $objRekapKupon = new BIRekapKuponModel();
                        $objRekapKupon->getWhereOne("bi_kupon_ak_id=$ak_id AND bi_kupon_kpo_id=$kpo_id AND bi_kupon_ibo_id=$ibo_id AND bi_kupon_tc_id=$tc_id AND bi_kupon_bln=$bln AND bi_kupon_thn=$thn");

                        if (!is_null($objRekapKupon->bi_kupon_id)) {
                            $objRekapKupon->delete($objRekapKupon->bi_kupon_id);
                            echo Generic::getTCNamebyID($tc_id) . " kedelete untuk bulan " . $bln . $thn . "<br>";
                        }

                        $objRekapKupon = new BIRekapKuponModel();
                        $objRekapKupon->bi_kupon_ak_id = $ak_id;
                        $objRekapKupon->bi_kupon_kpo_id = $kpo_id;
                        $objRekapKupon->bi_kupon_ibo_id = $ibo_id;
                        $objRekapKupon->bi_kupon_tc_id = $tc_id;
                        $objRekapKupon->bi_kupon_bln = $bln;
                        $objRekapKupon->bi_kupon_thn = $thn;
                        $objRekapKupon->bi_kupon_waktu = $bln . "-" . $thn;
                        $jumlahKupon = 0;
                        $kupon = new KuponBundle();
                        $jumlahKupon = $kupon->getJumlahKuponByTC($bln, $thn, $tc_id);

                        $kuponSatuan = new KuponSatuan();
                        $jmlhIuaran = $kuponSatuan->getJumlahKuponTerpakaiByTC($bln, $thn, $tc_id);
                        $biRekapModel = new BIRekapKuponModel();
                        $jmlhStock = $biRekapModel->getDatenPrevMonth($bln, $thn, $ak_id, $kpo_id, $ibo_id, $tc_id);

                        $objRekapKupon->bi_kupon_stock = $jmlhStock;
                        $objRekapKupon->bi_kupon_kupon_masuk = $jumlahKupon;
                        $objRekapKupon->bi_kupon_trs_bln = $jmlhIuaran;
                        $objRekapKupon->bi_kupon_stock_akhir = ($jmlhStock + $jumlahKupon) - $jmlhIuaran;
                        $murid = new MuridModel();
                        $objRekapKupon->bi_kupon_murid_aktiv = $murid->getMuridAktiv($tc_id);
                        $objRekapKupon->save();
                        $createBaru++;
                        echo Generic::getTCNamebyID($tc_id) . " keCreate untuk bulan " . $bln . $thn . "<br>";

//                        if (is_null($objRekapKupon->bi_kupon_id)) {
//                            $objRekapKupon = new BIRekapKuponModel();
//                            $objRekapKupon->bi_kupon_ak_id = $ak_id;
//                            $objRekapKupon->bi_kupon_kpo_id = $kpo_id;
//                            $objRekapKupon->bi_kupon_ibo_id = $ibo_id;
//                            $objRekapKupon->bi_kupon_tc_id = $tc_id;
//                            $objRekapKupon->bi_kupon_bln = $bln;
//                            $objRekapKupon->bi_kupon_thn = $thn;
//                            $objRekapKupon->bi_kupon_waktu = $bln . "-" . $thn;
//                            $jumlahKupon = 0;
//                            $kupon = new KuponBundle();
//                            $jumlahKupon = $kupon->getJumlahKuponByTC($bln, $thn, $tc_id);
//
//                            $kuponSatuan = new KuponSatuan();
//                            $jmlhIuaran = $kuponSatuan->getJumlahKuponTerpakaiByTC($bln, $thn, $tc_id);
//                            $biRekapModel = new BIRekapKuponModel();
//                            $jmlhStock = $biRekapModel->getDatenPrevMonth($bln, $thn, $ak_id, $kpo_id, $ibo_id, $tc_id);
//
//                            $objRekapKupon->bi_kupon_stock = $jmlhStock;
//                            $objRekapKupon->bi_kupon_kupon_masuk = $jumlahKupon;
//                            $objRekapKupon->bi_kupon_trs_bln = $jmlhIuaran;
//                            $objRekapKupon->bi_kupon_stock_akhir = ($jmlhStock + $jumlahKupon) - $jmlhIuaran;
//                            $murid = new MuridModel();
//                            $objRekapKupon->bi_kupon_murid_aktiv = $murid->getMuridAktiv($tc_id);
//                            $objRekapKupon->save();
//                            $createBaru++;
//                        } else {
//                            $kupon = new KuponBundle();
//                            $jumlahKupon = $kupon->getJumlahKuponByTC($bln, $thn, $tc_id);
//
//                            $kuponSatuan = new KuponSatuan();
//                            $jmlhIuaran = $kuponSatuan->getJumlahKuponTerpakaiByTC($bln, $thn, $tc_id);
//                            $biRekapModel = new BIRekapKuponModel();
//                            $jmlhStock = $biRekapModel->getDatenPrevMonth($bln, $thn, $ak_id, $kpo_id, $ibo_id, $tc_id);
//
//                            $objRekapKupon->bi_kupon_stock = $jmlhStock;
//                            $objRekapKupon->bi_kupon_kupon_masuk = $jumlahKupon;
//                            $objRekapKupon->bi_kupon_trs_bln = $jmlhIuaran;
//                            $objRekapKupon->bi_kupon_stock_akhir = ($jmlhStock + $jumlahKupon) - $jmlhIuaran;
//                            $murid = new MuridModel();
//                            $objRekapKupon->bi_kupon_murid_aktiv = $murid->getMuridAktiv($tc_id);
//                            $objRekapKupon->save(1);
//                            $update++;
//
//                        }
                    }
                }
            }
        }

        if ($createBaru != 0) {
            echo "Tercreate sebanyak " . $createBaru . " rows <br>";
            $mail = new Leapmail2();
            $mail->sendHTMLEmail("efindi.ongso@gmail.com","Cron Job cronJobRekapKuponBulanan","",$createBaru . " rows " );
        }
        if ($update != 0) {
            echo "Update sebanyak " . $update . " rows <br>";
            $mail = new Leapmail2();
            $mail->sendHTMLEmail("efindi.ongso@gmail.com","Cron Job cronJobRekapKuponBulanan","",$createBaru . " rows " );
        }
    }


    public function getMuridStatusByTC($status, $keytc, $bln, $thn)
    {
        $murid = new MuridModel();
        $statusMurid = new StatusHisMuridModel();
        $q = "SELECT * FROM {$murid->table_name} murid INNER JOIN {$statusMurid->table_name} status ON status.status_murid_id = murid.id_murid WHERE murid.murid_tc_id='$keytc' AND YEAR(status.status_tanggal_mulai ) = $thn AND MONTH(status.status_tanggal_mulai ) = $bln  AND status.status_tanggal_akhir ='1970-01-01 07:00:00'  AND status.status=$status GROUP BY murid.id_murid";
//        $q = "SELECT * FROM {$murid->table_name} murid INNER JOIN {$statusMurid->table_name} status ON status.status_murid_id = murid.id_murid WHERE murid.murid_tc_id='$keytc' AND YEAR(status.status_tanggal_mulai ) = $thn AND MONTH(status.status_tanggal_mulai ) = $bln  AND status.status=$status GROUP BY murid.id_murid";

        global $db;
        $arrMurid = $db->query($q, 2);
        return count($arrMurid);
    }

    public function getMuridBaruByTC($keytc, $bln, $thn)
    {
        $logPayment = new PaymentFirstTimeLog();
        $count = $logPayment->getJumlah("Year(murid_pay_date)=$thn AND MONTH(murid_pay_date)=$bln AND murid_tc_id=$keytc");
//        $count = $murid->getJumlah("murid_tc_id=$keytc AND YEAR(tanggal_masuk) = $thn AND MONTH(tanggal_masuk) = $bln");

        return $count;
    }

    public function getMuridBaruByTC_tmp($keytc, $bln, $thn)
    {
        $murid = new MuridModel();
        $count = $murid->getJumlah("murid_tc_id=$keytc AND YEAR(tanggal_masuk) = $thn AND MONTH(tanggal_masuk) = $bln");

        return $count;
    }

    function getPenjualanKuponByTC($keytc, $bln, $thn)
    {
//        transaksi__kupon_satuan
        $kupon = new KuponSatuan();
        $waktu = $bln . "-" . $thn;
//        $count = $kupon->getJumlah("kupon_owner_id='$keytc' AND kupon_status = 1 AND YEAR(kupon_pemakaian_date) = $thn AND MONTH(kupon_pemakaian_date) = $bln");
        $count = $kupon->getJumlah("kupon_owner_id='$keytc' AND kupon_status = 1 AND MONTH(kupon_pemakaian_date) = $bln AND YEAR(kupon_pemakaian_date) = $thn");
        return $count;
    }

    function getGuruAktivByTC($keytc, $bln, $thn)
    {
        $objGuru = new SempoaGuruModel();
        $nonAktiv = KEY::$STATUSGURURESIGN;
        $arrGuru = $objGuru->getWhere("status !='$nonAktiv' AND guru_tc_id='$keytc'");
        return count($arrGuru);
    }

    function getPenjualanBukuByTC($keytc, $bln, $thn)
    {

        $waktu = $bln . "-" . $thn;
        $iuranBuku = new IuranBuku();
        $count = $iuranBuku->getJumlah("bln_tc_id='$keytc' AND bln_date='$waktu' AND bln_status=1");
        return $count;
    }

    function getBL($keytc, $keyibo, $bln, $thn)
    {
        if ($bln == 1) {
            $bln = 12;
            $thn = $thn - 1;
        } else {
            $bln = $bln - 1;
        }
        $waktu = $bln . "-" . $thn;
        $rekap_siswa = new RekapSiswaIBOModel();
        $rekap_siswa->getWhereOne("bi_rekap_tc_id = '$keytc' AND bi_rekap_ibo_id='$keyibo' AND bi_rekap_siswa_waktu = '$waktu' ");
        return $rekap_siswa->bi_rekap_aktiv;
    }

    /*
     * Cron Job set  status ke keluar setelah lebih dr 2 bulan cuti
     */

    public function setMuridKeluar()
    {

        $muridStatus = new StatusHisMuridModel();
        $arrMuridsCuti = $muridStatus->getWhere("status=2 AND status_tanggal_akhir = 1970-01-01 07:00:00 AND TIMESTAMPDIFF(MONTH, status_tanggal_mulai, now())>2");

        $jumlahMurid = 0;
        foreach ($arrMuridsCuti as $mur) {
            $jumlahMurid++;
            $mur->status_tanggal_akhir = leap_mysqldate();
            $mur->save(1);


            $murid = new MuridModel();
            $murid->getByID($mur->status_murid_id);
            $murid->status = KEY::$STATUSMURIDNKELUAR;
            $murid->save(1);
            // Ganti status murid di model murid

            // Create History Murid dgn status keluar

            $statusMurid = new StatusHisMuridModel();
            $statusMurid->status_murid_id = $mur->status_murid_id;
            $statusMurid->status_tanggal_mulai = leap_mysqldate();
            $statusMurid->status_level_murid = $murid->id_level_sekarang;
            $statusMurid->status = KEY::$STATUSMURIDNKELUAR;
            $statusMurid->status_ak_id = $murid->murid_ak_id;
            $statusMurid->status_kpo_id = $murid->murid_kpo_id;
            $statusMurid->status_ibo_id = $murid->murid_ibo_id;
            $statusMurid->status_tc_id = $murid->murid_ibo_id;
            $statusMurid->save();

            $logMurid = new LogStatusMurid();
            $logMurid->createLogMurid($mur->status_murid_id);

        }
        echo "Sebanyak " . $jumlahMurid . " ganti status dari Cuti ke keluar";

    }

    public function setStatusCutiAktiv()
    {
        $murid = new MuridModel();
        $arrMurid = $murid->getWhere("status=2 AND murid_date_cuti_aktiv = DATE_FORMAT(CURDATE(),'%Y-%m-05')");
        foreach ($arrMurid as $murid) {
            $murid->murid_date_cuti_aktiv = "0000-00-00";
            $murid->status = 1;
            $murid->save(1);
        }
    }

    function cobaCuti()
    {
        $log = new LogStatusMurid();
        $log->getCountSiswaCutiGroup(1901, 'K', 3, 2017, 26);
    }

    public function createAktivLog()
    {

        $murid = new MuridModel();
        $arrMurid = $murid->getWhere("status = 1");
        pr(count($arrMurid));
        $count=0;
        foreach ($arrMurid as $val) {
            $log = new LogStatusMurid();
            $tgl = date("d");
            $bln = date("n");
            $thn = date("Y");
            $murid_id = $val->id_murid;
            $log->getWhereOne("log_tgl=$tgl AND log_bln=$bln AND log_thn=$thn AND log_id_murid=$murid_id");

            if (is_null($log->log_id)) {
                $log = new LogStatusMurid();
                $log->log_id_murid = $val->id_murid;
                $log->log_tgl = $tgl;
                $log->log_bln = $bln;
                $log->log_thn = $thn;
                $log->log_ak_id = $val->murid_ak_id;
                $log->log_kpo_id = $val->murid_kpo_id;
                $log->log_ibo_id = $val->murid_ibo_id;
                $log->log_tc_id = $val->murid_tc_id;
                $log->log_status = "A";
                $murid = new MuridModel();
                $murid->getByID($val->id_murid);
                if ($murid->murid_kurikulum == KEY::$KURIKULUM_BARU) {
                    $log->log_level = Generic::getLevelNameByID($murid->id_level_sekarang);
                    $log->log_kurikulum = KEY::$KURIKULUM_BARU_TEXT;
                } else {
                    $log->log_level = Generic::getLevelNameLamaByID($murid->id_level_sekarang);
                    $log->log_kurikulum = KEY::$KURIKULUM_LAMA_TEXT;
                }
                $log->save();
                $count++;
//                die();
            }

        }
        $mail = new Leapmail2();
        $mail->sendHTMLEmail("efindi.ongso@gmail.com","Cron Job createAktivLog","",$count . " tercreated" );
    }

    public function createAktivLogDate()
    {

        $bln = isset($_GET['bln']) ? addslashes($_GET['bln']) : date("n");
        $thn = isset($_GET['thn']) ? addslashes($_GET['thn']) : date("Y");
        $tgl =  isset($_GET['tgl']) ? addslashes($_GET['tgl']) : date("d");
        $murid = new MuridModel();
        $arrMurid = $murid->getWhere("status = 1");
        pr(count($arrMurid));
        $count=0;
        foreach ($arrMurid as $val) {
            $log = new LogStatusMurid();

            $murid_id = $val->id_murid;
            $log->getWhereOne("log_tgl=$tgl AND log_bln=$bln AND log_thn=$thn AND log_id_murid=$murid_id");


            if (is_null($log->log_id)) {
                $log = new LogStatusMurid();
                $log->log_id_murid = $val->id_murid;
                $log->log_tgl = $tgl;
                $log->log_bln = $bln;
                $log->log_thn = $thn;
                $log->log_ak_id = $val->murid_ak_id;
                $log->log_kpo_id = $val->murid_kpo_id;
                $log->log_ibo_id = $val->murid_ibo_id;
                $log->log_tc_id = $val->murid_tc_id;
                $log->log_status = "A";
                $murid = new MuridModel();
                $murid->getByID($val->id_murid);
                if ($murid->murid_kurikulum == KEY::$KURIKULUM_BARU) {
                    $log->log_level = Generic::getLevelNameByID($murid->id_level_sekarang);
                    $log->log_kurikulum = KEY::$KURIKULUM_BARU_TEXT;
                } else {
                    $log->log_level = Generic::getLevelNameLamaByID($murid->id_level_sekarang);
                    $log->log_kurikulum = KEY::$KURIKULUM_LAMA_TEXT;
                }
                $log->save();
                $count++;
//                die();
            }

        }
        pr($count . " tercreated");
    }

    public function kirimEmail(){

        $mail = new Leapmail2();
        $mail->sendHTMLEmail("efindi.ongso@gmail.com","cron","cron","cronisi");

    }
}
