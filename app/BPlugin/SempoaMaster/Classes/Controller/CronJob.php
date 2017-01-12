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
     * 2. create_rekap_siswa ->tiap akhir bulan!
     * 3. cronJobRekapKuponBulanan ->tiap akhir bulan!
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
        $arrMurid = $murid->getWhere("status = 1 OR status = 2");
        $total = 0;
        foreach ($arrMurid as $mur) {
//
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
//            }
        }
        echo "Total ter create " . $total . " Invoice(SPP)!";
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
                        $jmlhMuridAktiv = $murid->getMuridAktiv($keyTC);
                        $jmlhMuridBaru = $this->getMuridBaruByTC($keyTC, $bln, $thn);
                        $jmlhMuridCuti = $logSiswa->getCountSiswaByStatusOrgType($bln, $thn, 'C', "tc", $keyTC);
                        $jmlhMuridKeluar = $logSiswa->getCountSiswaByStatusOrgType($bln, $thn, 'K', "tc", $keyTC);
                        $jmlhMuridLulus = $logSiswa->getCountSiswaByStatusOrgType($bln, $thn, 'L', "tc", $keyTC);
                        $jmlhMuridBL = $rekap_siswa->getCountSiswaAktivBulanLalu($bln, $thn, $keyTC);
                        if ($jmlhMuridBaru > 0) {
                        }
                        if (is_null($rekap_siswa->bi_rekap_kode_tc)) {
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
                            $createBaru++;
                        } else {
                            $rekap_siswa->bi_rekap_bl = $jmlhMuridBL;
                            $rekap_siswa->bi_rekap_baru = $jmlhMuridBaru;
                            $rekap_siswa->bi_rekap_aktiv = $jmlhMuridAktiv;
                            $rekap_siswa->bi_rekap_cuti = $jmlhMuridCuti;
                            $rekap_siswa->bi_rekap_keluar = $jmlhMuridKeluar;
                            $rekap_siswa->bi_rekap_lulus = $jmlhMuridLulus;
                            $rekap_siswa->bi_rekap_kupon = $this->getPenjualanKuponByTC($keyTC, $bln, $thn);
                            $rekap_siswa->bi_rekap_jumlah_guru = $this->getGuruAktivByTC($keyTC, $bln, $thn);
                            $rekap_siswa->bi_rekap_buku = $this->getPenjualanBukuByTC($keyTC, $bln, $thn);
                            $rekap_siswa->save(1);
                            $update++;
                        }
                    }
                }
            }
        }
        if ($createBaru != 0) {
            echo "Tercreate sebanyak " . $createBaru . " rows <br>";
        }
        if ($update != 0) {
            echo "Update sebanyak " . $update . " rows <br>";
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

                        if (is_null($objRekapKupon->bi_kupon_id)) {
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
                        } else {
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
                            $objRekapKupon->save(1);
                            $update++;

                        }
                    }
                }
            }
        }

        if ($createBaru != 0) {
            echo "Tercreate sebanyak " . $createBaru . " rows <br>";
        }
        if ($update != 0) {
            echo "Update sebanyak " . $update . " rows <br>";
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

}
