<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Migrasi
 *
 * @author efindiongso
 */
class Migrasi extends WebService
{

    // Utk setiap kali Migrasi Murid dr system Billy ke kita, ada beberapa hal yang harus di migrasi
    // 1. Migrasi Status
    // 2. Migrasi Level

    public function migrationTC()
    {

    }

    public function migrationMurid()
    {
        $serverpath = "localhost";
        $db_username = "root";
        $db_password = "root";
        $db_name = "sempoa_migrasi_3";
        $db_prefix = '';
//        $db_name_migrasi = "c1nt466__sempoa_asli";

        $db_M = $this->getDB($serverpath, $db_username, $db_password, $db_name);
        $q = "SELECT * FROM siswa";
        $arrMuridAsli = $this->query($q, $db_M);
        $arrsudah = array();
        $arrMurid = array();
        foreach ($arrMuridAsli as $murid) {
            $rest = substr($murid->kode_siswa, 1);
            $ibo = substr($rest, 1, 2);
            $tc = substr($rest, 2);
            $tc = substr($tc, 1, 3);
            $org_ibo = $ibo . $tc;
            $arrMurid[$org_ibo][] = $murid;

        }

        pr("mulai");
        $this->insertMuridToDBAsli($arrMurid);
        pr("akhir");
    }

    function insertMuridToDBAsli($arrMurid)
    {
//pr($arrMurid);
        $jenisKelamin = array();
        $jenisKelamin['perempuan'] = 'f';
        $jenisKelamin['laki-laki'] = 'm';
        $agama = array();
        $agama[''] = '';
        $agama['Kristen'] = 'kr';
        $agama['Katolik'] = 'kh';
        $agama['Islam'] = 'i';
        $agama['Hindu'] = 'h';
        $agama['Buddha'] = 'b';

        $level = array();
        $level['junior 1'] = 1;
        $level['junior 2'] = 2;
        $level['foundation 1'] = 3;
        $level['foundation 2'] = 4;
        $level['foundation 3'] = 6;
        $level['foundation 4'] = 7;
        $level['F1'] = 3;
        $level['F2'] = 4;
        $level['intermediate 1'] = 8;
        $level['intermediate 2'] = 9;
        $level['intermediate 3'] = 10;
        $level['intermediate 4'] = 11;
        $level['advance 1'] = 12;
        $level['advance 2'] = 13;
        $level['advance 3'] = 14;


        $status = array();
        $status['keluar'] = KEY::$STATUSMURIDNKELUAR;
        $status['aktif'] = KEY::$STATUSMURIDAKTIV;
        $status['cuti'] = KEY::$STATUSMURIDCUTI;
        //nis_awal

        $jenis_pembayaran = array();
        $jenis_pembayaran['EDC'] = 3;
        $jenis_pembayaran['TRANSFER'] = 5;

        $serverpath = "localhost";
        $db_username = "root";
        $db_password = "root";
//        sempoa_live
        $db_name = "sempoa_live_11";
        $db_prefix = '';
        $mysql = $this->getDB($serverpath, $db_username, $db_password, $db_name);
        $count = 1;
        $dataMurid = array();
        $arrMigrasi = array();
//        pr(count($arrMurid));
        foreach ($arrMurid as $keyorg_id => $muridObj) {
//            pr($keyorg_id);
            foreach ($muridObj as $murid) {
                unset($dataMurid);
                $objTC = $this->getIBOData($keyorg_id);
                if (!is_null($objTC->org_id)) {
                    if ($objTC->tc_migrasi != 1) {
                        pr($objTC->nama);
                        unset($dataMurid);
                        unset($arrDatenType);
                        $strKey = "";
                        $strval = "";
                        $tc_id = $objTC->org_id;
//                    pr($keyorg_id);

                        $ibo_id = Generic::getMyParentID($tc_id);
                        $kpo_id = Generic::getMyParentID($ibo_id);
                        $ak_id = Generic::getMyParentID($kpo_id);
                        $dataMurid['kode_siswa'] = $murid->kode_siswa;
                        $arrDatenType['kode_siswa'] = "String";
                        $dataMurid['nama_siswa'] = $murid->nama_siswa;
                        $arrDatenType['nama_siswa'] = "String";
                        $dataMurid['jenis_kelamin'] = $jenisKelamin[$murid->jenis_kelamin];
                        $arrDatenType['jenis_kelamin'] = "String";
                        $dataMurid['alamat'] = $murid->alamat;
                        $arrDatenType['alamat'] = "String";
                        $dataMurid['agama'] = $agama[$murid->agama];
                        $arrDatenType['agama'] = "String";
                        $dataMurid['tempat_lahir'] = $murid->tempat_lahir;
                        $arrDatenType['tempat_lahir'] = "String";
                        $dataMurid['tanggal_lahir'] = $murid->tanggal_lahir;
                        $arrDatenType['tanggal_lahir'] = "String";
                        $dataMurid['telepon'] = $murid->telepon;
                        $arrDatenType['telepon'] = "String";
                        $dataMurid['nama_sekolah'] = $murid->nama_sekolah;
                        $arrDatenType['nama_sekolah'] = "String";
                        $dataMurid['nama_ortu'] = $murid->nama_ortu;
                        $arrDatenType['nama_ortu'] = "String";
                        $dataMurid['gambar'] = $murid->gambar;
                        $arrDatenType['gambar'] = "String";
                        $dataMurid['tanggal_masuk'] = $murid->tanggal_masuk;
                        $arrDatenType['tanggal_masuk'] = "String";
                        $dataMurid['email_ortu'] = $murid->email_ortu;
                        $arrDatenType['email_ortu'] = "String";
                        $dataMurid['id_level_masuk'] = $level[$murid->level_masuk];
                        $arrDatenType['id_level_masuk'] = "int";
                        $dataMurid['id_level_sekarang'] = $level[$murid->level_sekarang];
                        $arrDatenType['id_level_sekarang'] = "int";
                        $dataMurid['nomor_pendaftaran'] = $murid->nomor_pendaftaran;
                        $arrDatenType['nomor_pendaftaran'] = "String";
                        $dataMurid['status'] = $status[$murid->status];
                        $arrDatenType['status'] = "int";
                        $dataMurid['murid_ak_id'] = $ak_id;
                        $dataMurid['murid_kpo_id'] = $kpo_id;
                        $dataMurid['murid_ibo_id'] = $ibo_id;
                        $dataMurid['murid_tc_id'] = $tc_id;
                        $dataMurid['pay_firsttime'] = 1;

                        $arrKey = array();
                        $arrVal = array();
                        foreach ($dataMurid as $key => $val) {
                            $arrKey[] = $key;
                            if ($val == "") {
//                            if ($arrDatenType[$key] == "String") {
//                                $val = "\"\"";
//                            }
                                if ($arrDatenType[$key] == "int") {
                                    $val = 0;
                                }
                            } elseif ($val == "0000-00-00") {
                                $val = KEY::$TGL_KOSONG;

                            }
                            if ($arrDatenType[$key] == "String") {
                                $val = "\"" . $val . "\"";
                            }
//                       x

                            $arrVal[] = $val;
                        }
                        $strKey = implode(",", $arrKey);
                        $strval = implode(",", $arrVal);
                        $q = "INSERT INTO sempoa__siswa (" . $strKey . ") VALUES (" . $strval . ")";
                        $arrMigrasi[$objTC->name] = $tc_id;
                        $count++;
                        $q2 = mysql_query($q);
                        if (!$q2) {
//                        pr($q);
                        } else {

                            pr("Sukses");
                            pr($count);
                        }
                    }
                }


            }
        }
        pr($arrMigrasi);
        pr($count);
    }

//id_murid,kode_siswa,nama_siswa,jenis_kelamin,alamat,agama,tempat_lahir,tanggal_lahir,telepon,nama_sekolah,nama_ortu,gambar,tanggal_masuk,email_ortu,id_level_masuk,id_level_sekarang,nomor_pendaftaran,kode_guru,status,murid_ak_id,murid_kpo_id,murid_ibo_id,murid_tc_id,pay_firsttime
    function getIBOData($data_tc)
    {
//        $data_tc = '06030';

        $serverpath = "localhost";
        $db_username = "root";
        $db_password = "root";
        $db_name = "sempoa_live_11";
        $db_prefix = '';

        $db = $this->getDB($serverpath, $db_username, $db_password, $db_name);

        $q = "SELECT * FROM sempoa__tc WHERE org_kode='$data_tc' ";
        $arrTC = $this->query($q, $db);
        return $arrTC[0];

//        return $orgObj;
//        pr($orgObj);
//        $arrResult['tc_id'] = $orgObj->org_id;
//        $arrResult['ibo_id'] = Generic::getMyParentID($orgObj->org_id);
//        $arrResult['kpo_id'] = Generic::getMyParentID(Generic::getMyParentID($orgObj->org_id));
//        $arrResult['ak_id'] = Generic::getMyParentID(Generic::getMyParentID(Generic::getMyParentID($orgObj->org_id)));
//        pr($arrResult);
    }

    //put your code here
    public function testConnection()
    {


        $serverpath = "localhost";
        $db_username = "root";
        $db_password = "root";
        $db_name = "sempoa_migrasi_3";
        $db_prefix = '';
        $db_name_migrasi = "c1nt466__sempoa_asli";

        $db = $this->getDB($serverpath, $db_username, $db_password, $db_name);
        $q = "SELECT * FROM siswa";
        $arrMuridAsli = $this->query($q, $db);
        foreach ($arrMuridAsli as $murid) {
            pr($murid);
        }

        $db_migrasi = $this->getDB($serverpath, $db_username, $db_password, $db_name_migrasi);
        $qm = "SELECT * FROM 	acc_akun";
        $arrMig = $this->query($qm, $db_migrasi);

        pr($arrMig);
    }

    public function getDB($serverpath, $db_username, $db_password, $db_name)
    {
        pr($db_name);
        $mysql = mysql_connect($serverpath, $db_username, $db_password);
        if (!$mysql) {

        }
        $db = mysql_select_db($db_name, $mysql);

        if (!$db) {
            echo "Cannot Select Database";
        } else {
            return $mysql;
        }
    }

    public function query($q, $db)
    {
        $resultSource = mysql_query($q, $db);
        $arr = array();
        while ($res = mysql_fetch_object($resultSource)) {
            $arr[] = $res;
        }
        return $arr;
    }

    public function migrationGuru()
    {
        $serverpath = "localhost";
        $db_username = "root";
        $db_password = "root";
        $db_name = "sempoa_migrasi_sunshine_3";
        $db_prefix = '';
//        $db_name_migrasi = "c1nt466__sempoa_asli";

        $db_M = $this->getDB($serverpath, $db_username, $db_password, $db_name);
        $q = "SELECT * FROM table_guru";
        $arrGuruAsli = $this->query($q, $db_M);
        $arrsudah = array();
        $arrGuru = array();
        foreach ($arrGuruAsli as $guru) {
//            pr($murid);
            $rest = substr($guru->kode_guru, 2);
            $org_ibo = substr($rest, 0, 5);
//            pr($org_ibo);
            $levelhlp = $this->getLevelGuru($db_M, $guru->kode_guru);
            $guru->idlevel = $levelhlp[0]->level;
            $arrGuru[$org_ibo][] = $guru;
        }

        echo "jumlah: " . count($arrGuruAsli);

        pr("mulai");
        $this->insertGuruToDBAsli($arrGuru);
        pr("akhir");
    }

    public function insertGuruToDBAsli($arrGuru)
    {
        $jenisKelamin = array();
        $jenisKelamin['perempuan'] = 'f';
        $jenisKelamin['laki-laki'] = 'm';
        $level = array();
        // Level lama
        $level['junior 1'] = 1;
        $level['junior 2'] = 2;
        $level['foundation 1'] = 3;
        $level['foundation 2'] = 4;
        $level['foundation 3'] = 6;
        $level['foundation 4'] = 6;
        $level['F1'] = 3;
        $level['F2'] = 4;
        $level['intermediate 1'] = 6;
        $level['intermediate 2'] = 7;
        $level['intermediate 3'] = 8;
        $level['intermediate 4'] = 9;
        $level['advance 1'] = 9;
        $level['advance 2'] = 10;
        $level['advance 3'] = 11;


        $levelBaru = array();


        $status = array();
        $status[''] = KEY::$STATUSGURUQUALIFIED;
        $status['aktif'] = KEY::$STATUSGURUQUALIFIED;
        $status['keluar'] = KEY::$STATUSGURURESIGN;


        $agama = array();
        $agama[''] = '';
        $agama['kristen'] = 'kr';
        $agama['katolik'] = 'kh';
        $agama['islam'] = 'i';
        $agama['hindu'] = 'h';
        $agama['buddha'] = 'b';

        $serverpath = "localhost";
        $db_username = "root";
        $db_password = "root";
        $db_name = "sempoa_live_11";
        $db_prefix = '';
        $mysql = $this->getDB($serverpath, $db_username, $db_password, $db_name);
        $count = 1;
        $dataGuru = array();
        $arrDatenType = array();
        $arrTC = array();
        $arrTCKosong = array();
        foreach ($arrGuru as $keyorg_id => $guruObj) {
//            pr($keyorg_id);
            foreach ($guruObj as $guru) {

                if (!array_key_exists($keyorg_id, $arrTC)) {
                    $objTC = $this->getIBOData($keyorg_id);
                    if (!is_null($objTC->org_id)) {
                        if ($objTC->tc_migrasi != 1) {
                            $arrTC[$keyorg_id] = $objTC;
                        }

                    } else {
                        pr($guru);
                        $arrTCKosong[] = $keyorg_id . " => tc tidak ada";
                    }
                }
            }
        }

        foreach ($arrTC as $key_org => $objTC) {
//            pr($key_org);
            $tc_id = $objTC->org_id;

            $ibo_id = Generic::getMyParentID($tc_id);
            $kpo_id = Generic::getMyParentID($ibo_id);
            $ak_id = Generic::getMyParentID($kpo_id);
            foreach ($arrGuru[$key_org] as $guru) {
//                pr($guru);
                $guruBaru = new SempoaGuruModel();
                unset($dataGuru);

                $dataGuru['kode_guru'] = $guru->kode_guru;
                $arrDatenType['kode_guru'] = "String";
                $dataGuru['tanggal_masuk'] = $guru->tanggal_masuk;
                $arrDatenType['tanggal_masuk'] = "String";
                $dataGuru['nama_guru'] = $guru->nama_guru;
                $arrDatenType['nama_guru'] = "String";
                $dataGuru['nama_panggilan'] = $guru->nama_panggilan;
                $arrDatenType['nama_panggilan'] = "String";
                $dataGuru['jenis_kelamin'] = $jenisKelamin[$guru->jenis_kelamin];
                $arrDatenType['jenis_kelamin'] = "String";
                $dataGuru['alamat'] = $guru->alamat;
                $arrDatenType['alamat'] = "String";
                $dataGuru['tempat_lahir'] = $guru->tempat_lahir;
                $arrDatenType['tempat_lahir'] = "String";
                $dataGuru['agama'] = $agama[$guru->agama];
                $arrDatenType['agama'] = "String";
                $dataGuru['tanggal_lahir'] = $guru->tanggal_lahir;
                $arrDatenType['tanggal_lahir'] = "String";
                $dataGuru['nomor_hp'] = $guru->nomor_hp;
                $arrDatenType['nomor_hp'] = "String";
                $dataGuru['pendidikan_terakhir'] = $guru->pendidikan_terakhir;
                $arrDatenType['pendidikan_terakhir'] = "String";
                $dataGuru['kode_tc'] = $guru->kode_tc;
                $arrDatenType['kode_tc'] = "String";
                $dataGuru['email_guru'] = $guru->email_guru;
                $arrDatenType['email_guru'] = "String";
                $dataGuru['status'] = $status[$guru->status];
                $arrDatenType['status'] = "int";
                $dataGuru['gambar'] = $guru->gambar;
                $arrDatenType['gambar'] = "String";
                $dataGuru['guru_ak_id'] = $ak_id;
                $arrDatenType['guru_ak_id'] = "int";
                $dataGuru['guru_kpo_id'] = $kpo_id;
                $arrDatenType['guru_kpo_id'] = "int";
                $dataGuru['guru_ibo_id'] = $ibo_id;
                $arrDatenType['guru_ibo_id'] = "int";
                $dataGuru['guru_tc_id'] = $tc_id;
                $arrDatenType['guru_tc_id'] = "int";
                $dataGuru['guru_first_register'] = 1;
                $arrDatenType['guru_first_register'] = "int";
                $dataGuru['id_level_training_guru'] = $level[$guru->idlevel];
                $arrDatenType['id_level_training_guru'] = "int";


                $arrKey = array();
                $arrVal = array();
                foreach ($dataGuru as $key => $val) {
                    $arrKey[] = $key;
                    if ($val == "") {
                        if ($arrDatenType[$key] == "int") {
                            $val = 0;
                        }
                    }
//                    elseif($val == "0000-00-00"){
//                        $val = KEY::$TGL_KOSONG;
//                    }
                    if ($arrDatenType[$key] == "String") {
                        $val = "\"" . $val . "\"";
                    }


                    $arrVal[] = $val;
                }
                $strKey = implode(",", $arrKey);
                $strval = implode(",", $arrVal);
                $q = "INSERT INTO sempoa__guru (" . $strKey . ") VALUES (" . $strval . ")";

                $q2 = mysql_query($q);
                if (!$q2) {
//                        pr($q);
                } else {
                    $count++;
                    pr($q . ";");
//                    pr("Sukses");

                }
            }
        }

        if (count($arrTCKosong) > 0) {
            pr($arrTCKosong);
        }
        pr($count);

    }

    public function insertGuruToDBAsli_backup($arrGuru)
    {
        $jenisKelamin = array();
        $jenisKelamin['perempuan'] = 'f';
        $jenisKelamin['laki-laki'] = 'm';
        $level = array();
        $level['junior 1'] = 1;
        $level['junior 2'] = 2;
        $level['foundation 1'] = 3;
        $level['foundation 2'] = 4;
        $level['foundation 3'] = 6;
        $level['foundation 4'] = 7;
        $level['F1'] = 3;
        $level['F2'] = 4;
        $level['intermediate 1'] = 8;
        $level['intermediate 2'] = 9;
        $level['intermediate 3'] = 10;
        $level['intermediate 4'] = 11;
        $level['advance 1'] = 12;
        $level['advance 2'] = 13;
        $level['advance 3'] = 14;

        $status = array();
        $status[''] = KEY::$STATUSGURUQUALIFIED;
        $status['aktif'] = KEY::$STATUSGURUQUALIFIED;
        $status['keluar'] = KEY::$STATUSGURURESIGN;


        $agama = array();
        $agama[''] = '';
        $agama['kristen'] = 'kr';
        $agama['katolik'] = 'kh';
        $agama['islam'] = 'i';
        $agama['hindu'] = 'h';
        $agama['buddha'] = 'b';

        $serverpath = "localhost";
        $db_username = "root";
        $db_password = "root";
        $db_name = "sempoa_live_11";
        $db_prefix = '';
        $mysql = $this->getDB($serverpath, $db_username, $db_password, $db_name);
        $count = 1;
        $dataGuru = array();
        $arrDatenType = array();
        $arrTC = array();
        foreach ($arrGuru as $keyorg_id => $guruObj) {
//            pr($keyorg_id);
            foreach ($guruObj as $guru) {
                if (!array_key_exists($keyorg_id, $arrTC)) {
                    $objTC = $this->getIBOData($keyorg_id);
                    if (!is_null($objTC->org_id)) {
                        if ($objTC->tc_migrasi != 1) {
                            $arrTC[$keyorg_id] = $objTC;
                        }

                    }
                }
            }
        }

        foreach ($arrTC as $key_org => $objTC) {
            pr($key_org);
            $tc_id = $objTC->org_id;
            $ibo_id = Generic::getMyParentID($tc_id);
            $kpo_id = Generic::getMyParentID($ibo_id);
            $ak_id = Generic::getMyParentID($kpo_id);
            foreach ($arrGuru[$key_org] as $guru) {
//                pr($guru);
                $guruBaru = new SempoaGuruModel();
                unset($dataGuru);

                $dataGuru['kode_guru'] = $guru->kode_guru;
                $arrDatenType['kode_guru'] = "int";
                $dataGuru['tanggal_masuk'] = $guru->tanggal_masuk;
                $arrDatenType['tanggal_masuk'] = "String";
                $dataGuru['nama_guru'] = $guru->nama_guru;
                $arrDatenType['nama_guru'] = "String";
                $dataGuru['nama_panggilan'] = $guru->nama_panggilan;
                $arrDatenType['nama_panggilan'] = "String";
                $dataGuru['jenis_kelamin'] = $jenisKelamin[$guru->jenis_kelamin];
                $arrDatenType['jenis_kelamin'] = "String";
                $dataGuru['alamat'] = $guru->alamat;
                $arrDatenType['alamat'] = "String";
                $dataGuru['tempat_lahir'] = $guru->tempat_lahir;
                $arrDatenType['tempat_lahir'] = "String";
                $dataGuru['agama'] = $agama[$guru->agama];
                $arrDatenType['agama'] = "String";
                $dataGuru['tanggal_lahir'] = $guru->tanggal_lahir;
                $arrDatenType['tanggal_lahir'] = "String";
                $dataGuru['nomor_hp'] = $guru->nomor_hp;
                $arrDatenType['nomor_hp'] = "String";
                $dataGuru['pendidikan_terakhir'] = $guru->pendidikan_terakhir;
                $arrDatenType['pendidikan_terakhir'] = "String";
                $dataGuru['kode_tc'] = $guru->kode_tc;
                $arrDatenType['kode_tc'] = "String";
                $dataGuru['email_guru'] = $guru->email_guru;
                $arrDatenType['email_guru'] = "String";
                $dataGuru['status'] = $status[$guru->status];
                $arrDatenType['status'] = "int";
                $dataGuru['gambar'] = $guru->gambar;
                $arrDatenType['gambar'] = "String";
                $dataGuru['guru_ak_id'] = $ak_id;
                $arrDatenType['guru_ak_id'] = "int";
                $dataGuru['guru_kpo_id'] = $kpo_id;
                $arrDatenType['guru_kpo_id'] = "int";
                $dataGuru['guru_ibo_id'] = $ibo_id;
                $arrDatenType['guru_ibo_id'] = "int";
                $dataGuru['guru_tc_id'] = $tc_id;
                $arrDatenType['guru_tc_id'] = "int";
                $dataGuru['guru_first_register'] = 1;
                $arrDatenType['guru_first_register'] = "int";
                $dataGuru['id_level_training_guru'] = $level[$guru->idlevel];
                $arrDatenType['id_level_training_guru'] = "int";


                $arrKey = array();
                $arrVal = array();
                foreach ($dataGuru as $key => $val) {
                    $arrKey[] = $key;
                    if ($val == "") {
                        if ($arrDatenType[$key] == "int") {
                            $val = 0;
                        }
                    }
//                    elseif($val == "0000-00-00"){
//                        $val = KEY::$TGL_KOSONG;
//                    }
                    if ($arrDatenType[$key] == "String") {
                        $val = "\"" . $val . "\"";
                    }


                    $arrVal[] = $val;
                }
                $strKey = implode(",", $arrKey);
                $strval = implode(",", $arrVal);
                $q = "INSERT INTO sempoa__guru (" . $strKey . ") VALUES (" . $strval . ")";
                pr($q);
                $count++;
                $q2 = mysql_query($q);
            }
        }
        pr($count);
    }

    public function getLevelGuru($db, $guru_kode)
    {
        $q = "SELECT level FROM table_level_guru WHERE kode_guru=$guru_kode ORDER BY tanggal DESC LIMIT 0,1";
        $result = $this->query($q, $db);
        return $result;
    }

    // Trainer di IBO
    public function migrationTrainerIBO()
    {
        $serverpath = "localhost";
        $db_username = "root";
        $db_password = "root";
        $db_name = "sempoa_migrasi_3";

        $db_M = $this->getDB($serverpath, $db_username, $db_password, $db_name);
        $q = "SELECT * FROM table_trainer";
        $arrTrainerAsli = $this->query($q, $db_M);
        $arrsudah = array();
        $arrGuru = array();
        foreach ($arrTrainerAsli as $trainer) {
            $rest = substr($trainer->kode_trainer, 2);
            $org_ibo = substr($rest, 0, 2);
//            $levelhlp = $this->getLevelGuru($db_M, $guru->kode_guru);
//            $guru->idlevel = $levelhlp[0]->level;
            $arrTrainer[$org_ibo][] = $trainer;
        }

        pr("mulai");
        $this->insertTrainerToDBAsli($arrTrainer);
        die();
        pr("akhir");
    }

    public function insertTrainerToDBAsli($arrTrainer)
    {
        $jenisKelamin = array();
        $jenisKelamin['perempuan'] = 'f';
        $jenisKelamin['laki-laki'] = 'm';


        $level = array();
        $level['junior 1'] = 1;
        $level['junior 2'] = 2;
        $level['foundation 1'] = 3;
        $level['foundation 2'] = 4;
        $level['foundation 3'] = 6;
        $level['foundation 4'] = 7;
        $level['F1'] = 3;
        $level['F2'] = 4;
        $level['intermediate 1'] = 8;
        $level['intermediate 2'] = 9;
        $level['intermediate 3'] = 10;
        $level['intermediate 4'] = 11;
        $level['advance 1'] = 12;
        $level['advance 2'] = 13;
        $level['advance 3'] = 14;

        $status = array();
        $status['aktif'] = KEY::$STATUSGURUQUALIFIED;
        $status['keluar'] = KEY::$STATUSGURURESIGN;

        $agama = array();
        $agama[''] = '';
        $agama['kristen'] = 'kr';
        $agama['katolik'] = 'kh';
        $agama['islam'] = 'i';
        $agama['hindu'] = 'h';
        $agama['buddha'] = 'b';

        $serverpath = "localhost";
        $db_username = "root";
        $db_password = "root";
        $db_name = "sempoa_live_11";
        $db_prefix = '';
        $mysql = $this->getDB($serverpath, $db_username, $db_password, $db_name);
        $count = 1;
        $dataTrainer = array();
        $arrDatenType = array();
        $arrIBO = array();
        foreach ($arrTrainer as $keyorg_id => $trainerObj) {
            foreach ($trainerObj as $trainer) {
                if (!array_key_exists($keyorg_id, $arrIBO)) {
                    $objTC = $this->getIBOData($keyorg_id);
                    if (!is_null($objTC->org_id)) {
                        if ($objTC->tc_migrasi != 1) {
                            $arrIBO[$keyorg_id] = $objTC;
                        }

                    }
                }
            }
        }
        foreach ($arrIBO as $key_org => $objIBO) {
            pr($key_org);
            $ibo_id = $objIBO->org_id;
            $kpo_id = Generic::getMyParentID($ibo_id);
            $ak_id = Generic::getMyParentID($kpo_id);
            foreach ($arrTrainer[$key_org] as $trainer) {
//                pr($guru);
                $trainerBaru = new TrainerModel();
                unset($dataTrainer);
                $dataTrainer['kode_trainer'] = $trainer->kode_trainer;
                $arrDatenType['kode_trainer'] = "int";
                $dataTrainer['tanggal_masuk'] = $trainer->tanggal_masuk;
                $arrDatenType['tanggal_masuk'] = "String";
                $dataTrainer['nama_trainer'] = $trainer->nama_trainer;
                $arrDatenType['nama_trainer'] = "String";
                $dataTrainer['nama_panggilan'] = $trainer->nama_panggilan;
                $arrDatenType['nama_panggilan'] = "String";
                $dataTrainer['jenis_kelamin'] = $jenisKelamin[$trainer->jenis_kelamin];
                $arrDatenType['jenis_kelamin'] = "String";
                $dataTrainer['alamat'] = $trainer->alamat;
                $arrDatenType['alamat'] = "String";
                $dataTrainer['tempat_lahir'] = $trainer->tempat_lahir;
                $arrDatenType['tempat_lahir'] = "String";
                $dataTrainer['agama'] = $agama[$trainer->agama];
                $arrDatenType['agama'] = "String";
                $dataTrainer['tanggal_lahir'] = $trainer->tanggal_lahir;
                $arrDatenType['tanggal_lahir'] = "String";
                $dataTrainer['nomor_hp'] = $trainer->nomor_hp;
                $arrDatenType['nomor_hp'] = "String";
                $dataTrainer['pendidikan_terakhir'] = $trainer->pendidikan_terakhir;
                $arrDatenType['pendidikan_terakhir'] = "String";
                $dataTrainer['email'] = $trainer->email;
                $arrDatenType['email'] = "String";
                $dataTrainer['status'] = $status[$trainer->status];
                $arrDatenType['status'] = "int";
                $dataTrainer['gambar'] = $trainer->gambar;
                $arrDatenType['gambar'] = "String";
                $dataTrainer['tr_ak_id'] = $ak_id;
                $arrDatenType['tr_ak_id'] = "int";
                $dataTrainer['tr_kpo_id'] = $kpo_id;
                $arrDatenType['tr_kpo_id'] = "int";
                $dataTrainer['tr_ibo_id'] = $ibo_id;
                $arrDatenType['tr_ibo_id'] = "int";
                $dataTrainer['id_level_trainer'] = $level[$trainer->idlevel];
                $arrDatenType['id_level_trainer'] = "int";

                $arrKey = array();
                $arrVal = array();
                foreach ($dataTrainer as $key => $val) {
                    $arrKey[] = $key;
                    if ($val == "") {
                        if ($arrDatenType[$key] == "int") {
                            $val = 0;
                        }
                    } elseif ($val == "0000-00-00") {
                        $val = KEY::$TGL_KOSONG;
                    }
                    if ($arrDatenType[$key] == "String") {
                        $val = "\"" . $val . "\"";
                    }


                    $arrVal[] = $val;
                }
                $strKey = implode(",", $arrKey);
                $strval = implode(",", $arrVal);
                $q = "INSERT INTO sempoa__trainer (" . $strKey . ") VALUES (" . $strval . ")";
                pr($q);
                $count++;
                $q2 = mysql_query($q);
            }
        }
        pr($count);
    }

    // Migration Kupon yg sdh kepake di TC
    public function migrationKuponTC()
    {
        $serverpath = "localhost";
        $db_username = "root";
        $db_password = "root";
        $db_name = "sempoa_migrasi_3";

        $bln = isset($_GET['bln']) ? addslashes($_GET['bln']) : date("n");
        $thn = isset($_GET['thn']) ? addslashes($_GET['thn']) : date("Y");

        $arrKuponTransaksi = array();
        $arrTCinTarget = $this->getAllTCinDB();
        $db_Quelle = $this->getDB($serverpath, $db_username, $db_password, $db_name);
        foreach ($arrTCinTarget as $keyorg_id => $tc) {
            $qQuelle = "SELECT * FROM table_transaksi WHERE kode_tc='$keyorg_id' AND jenis_transaksi = 'MONTHLYFEE' AND MONTH(tanggal_pakai)= $bln AND YEAR(tanggal_pakai)=$thn AND harga !=0";

            $arrKuponTransaksi[$keyorg_id] = $this->query($qQuelle, $db_Quelle);
            $arrKuponTransaksi[$keyorg_id]['tc_id'] = $tc->org_id;

        }
        pr("start");
        $this->insertKuponTerpakaiToDBAsli($arrKuponTransaksi);
        pr("Akhir");
    }

    public function getAllTCinDB()
    {
        $serverpath = "localhost";
        $db_username = "root";
        $db_password = "root";
        $db_name = "sempoa_live_11";
        $db_prefix = '';
        $db_Quelle = $this->getDB($serverpath, $db_username, $db_password, $db_name);
        $qQuelle = "SELECT * FROM sempoa__tc WHERE org_type='tc' AND tc_migrasi !=1";
        $arrTC = $this->query($qQuelle, $db_Quelle);

        $arrTCinDB = array();
        foreach ($arrTC as $val) {
            $arrTCinDB[$val->org_kode] = $val;
        }
        return $arrTCinDB;
    }

    public function insertKuponTerpakaiToDBAsli($arrKuponTransaksi)
    {

        $jenis_pembayaran = array();
        $jenis_pembayaran['EDC'] = 3;
        $jenis_pembayaran['TRANSFER'] = 5;

        $serverpath = "localhost";
        $db_username = "root";
        $db_password = "root";
        $db_name = "sempoa_live_11";
        $db_prefix = '';
        $mysql = $this->getDB($serverpath, $db_username, $db_password, $db_name);
        $count = 0;

        $arrTC = array();
        foreach ($arrKuponTransaksi as $keyorg_id => $kuponObj) {

            $tc_id = $kuponObj['tc_id'];
            $ibo_id = Generic::getMyParentID($tc_id);
            $kpo_id = Generic::getMyParentID($ibo_id);
            $ak_id = Generic::getMyParentID($kpo_id);
            foreach ($kuponObj as $kupon) {
                $iuranBulanan = new IuranBulanan();
                $iuranBulanan->bln_murid_id = $this->getMuridIdByKodeSiswa($kupon->kode_siswa);
                $date = $kupon->tanggal_pakai;
                $bln = date("m", strtotime($date));
                $thn = date("Y", strtotime($date));
                //11-2016
                $iuranBulanan->bln_date = $bln . "-" . $thn;
                $iuranBulanan->bln_date_pembayaran = $kupon->tanggal_transaksi;

                // bulan
                $iuranBulanan->bln_mon = $bln;
                $iuranBulanan->bln_tahun = $thn;
                $iuranBulanan->bln_status = 1;
                $iuranBulanan->bln_kupon_id = $kupon->barang;
                $iuranBulanan->bln_cara_bayar = $jenis_pembayaran[$kupon->jenis_pembayaran];
                $iuranBulanan->bln_ak_id = $ak_id;
                $iuranBulanan->bln_kpo_id = $kpo_id;
                $iuranBulanan->bln_ibo_id = $ibo_id;
                $iuranBulanan->bln_tc_id = $tc_id;
                $iuranBulanan->bln_no_urut_inv = $iuranBulanan->getLastNoUrutInvoice($thn, $bln, $tc_id);
                $iuranBulanan->bln_no_invoice = "SPP/" . $thn . "/" . $bln . "/" . $iuranBulanan->bln_no_urut_inv;
                if ($iuranBulanan->save()) {
                    $count++;
                }
            }

            pr($count);


//            pr($kuponObj->tanggal_pakai);
//
//            pr($date);
//
//            $iuranBulanan->save();
        }

    }

    public function getMuridIdByKodeSiswa($kode_siswa)
    {
        $objMurid = new MuridModel();
        $objMurid->getWhereOne("kode_siswa='$kode_siswa'");
        return $objMurid->id_murid;
    }

    public function coba()
    {
        $a = new IuranBulanan();
        $b = $a->getLastNoUrutInvoice(2016, 11, AccessRight::getMyOrgID());
        pr($b);
        $a = "Coba sekali, sesame";
//        $ab
    }

    public function migrasiStatusMurid()
    {
        $murid = new MuridModel();
        $arrMurids = $murid->getWhere("status=1");
        $i = 0;
        foreach ($arrMurids as $mur) {
            $statusMurid = new StatusHisMuridModel();
            $statusMurid->getWhereOne("status_murid_id=$mur->id_murid");
            if (is_null($statusMurid->status_id)) {
                $i++;
                $statusMurid = new StatusHisMuridModel();
                $statusMurid->status_murid_id = $mur->id_murid;
                $statusMurid->status_tanggal_mulai = leap_mysqldate();
                $statusMurid->status_level_murid = $mur->id_level_sekarang;
                $statusMurid->status = $mur->status;
                $statusMurid->status_ak_id = $mur->murid_ak_id;
                $statusMurid->status_kpo_id = $mur->murid_kpo_id;
                $statusMurid->status_ibo_id = $mur->murid_ibo_id;
                $statusMurid->status_tc_id = $mur->murid_tc_id;
                $statusMurid->save();
                $logMurid = new LogStatusMurid();
                $logMurid->createLogMurid($mur->id_murid);

            }
        }
        echo "jumlah yg termigrasi: " + $i;

    }

    public function migrasiLevelMurid()
    {
        $murid = new MuridModel();
        $arrMurid = $murid->getWhere("status=1");
        $count = 0;

        foreach ($arrMurid as $mur) {
            $mj = new MuridJourney();
            $id_murid = $mur->id_murid;
            $mj->getWhereOne("journey_murid_id=$id_murid");
            if (!is_null($mj->journey_id)) {
//            pr($id_murid);
            } else {

                $mk = new MuridJourney();
                $mk->createJourney($id_murid, $mur->id_level_sekarang, "2017-05-31 14:20:27", $mur->murid_tc_id);
                $count++;
            }
        }
        echo "tercreate " . $count . " level ";
    }

    function MigraOwnerIDKuponSatuan()
    {
        global $db;

        $sql = "SELECT satuan.kupon_id, bundle.tc_id FROM transaksi__kupon_satuan satuan INNER JOIN transaksi__kupon_bundle bundle on satuan.kupon_bundle_id = bundle.bundle_id WHERE satuan.kupon_status = 1 AND satuan.kupon_owner_id=3";
        $arr = $db->query($sql, 2);
//pr($sql);
//        die();
        $count = 0;
        foreach ($arr as $val) {
            $kupon = new KuponSatuan();
            $kupon->getWhereOne("kupon_id=$val->kupon_id");
            $kupon->bck_owner_id = $kupon->kupon_owner_id;
            $kupon->kupon_owner_id = $val->tc_id;
//            die();
            $kupon->save(1);
            $count++;
        }

        echo $count;

    }


    public function createIuranBulananFirstPayment()
    {
        $fp = new PaymentFirstTimeLog();
        $arr = $fp->getWhere("Date(murid_pay_date) BETWEEN '2017-02-23' AND '2017-03-07' ORDER by murid_pay_date DESC");
        $arrdouble = array();
        foreach ($arr as $val) {
            pr($val->murid_id . ", " . Generic::getTCNamebyID($val->murid_tc_id));

            $ser = unserialize($val->murid_biaya_serial);
            $bln_date_pembayaran = $val->murid_pay_date;
            $datePembayaran = new DateTime($bln_date_pembayaran);

            $pilih_kapan = $ser['kupon']['kapan'];
            $bln_kupon_id = $ser['kupon']['nomor'];

            $tc_id = $val->murid_tc_id;
            $ibo_id = $val->murid_ibo_id;
            $kpo_id = $val->murid_kpo_id;
            $ak_id = $val->murid_ak_id;
            $jenis_pmbr = $val->murid_cara_bayar;
            $bln_create_date = $val->murid_pay_date;
            pr($bln_create_date);
//            $bln_create_date->save();
            // check di Iuran bulanan sdh ada belum

            $murid_id = $val->murid_id;
            $bln_skrg = $datePembayaran->format("n");
            $thn_skrg = $datePembayaran->format("Y");

            $i = 1;
            // cari di iuran bulanan
            $si = new IuranBulanan();
            $iuranBulanan = new IuranBulanan();
            $iuranBulanan->bln_tc_id = $tc_id;
            $iuranBulanan->bln_murid_id = $murid_id;
            $iuranBulanan->bln_date = $pilih_kapan;
            $iuranBulanan->bln_mon = $bln_skrg;
            $iuranBulanan->bln_tahun = $thn_skrg;
            $iuranBulanan->bln_kupon_id = $bln_kupon_id;
            $iuranBulanan->bln_status = 1;
            $iuranBulanan->bln_ibo_id = $ibo_id;
            $iuranBulanan->bln_kpo_id = $kpo_id;
            $iuranBulanan->bln_ak_id = $ak_id;
            $iuranBulanan->bln_cara_bayar = $jenis_pmbr;
            $iuranBulanan->bln_date_pembayaran = $val->murid_pay_date;

            $arr = $si->getWhere("bln_murid_id='$murid_id' AND bln_date = '$pilih_kapan'");
            if (count($arr) >= 1) {
                $arrdouble[] = "murid ID: " . $murid_id . ", Nama: " . Generic::getMuridNamebyID($murid_id) . ", TC: " . Generic::getTCNamebyID($tc_id);
                $iuranBulanan->bln_id = $murid_id . "_" . $bln_skrg . "_" . $thn_skrg . "_" . $i;
                $i++;
            } else {
                $iuranBulanan->bln_id = $murid_id . "_" . $bln_skrg . "_" . $thn_skrg;
            }


            $iuranBulanan->bln_create_date = $val->murid_pay_date;
            $iuranBulanan->save();


        }
        pr($arrdouble);
    }


    public function MigrasiGpLion()
    {
        $serverpath = "localhost";
        $db_username = "root";
        $db_password = "root";
        $db_name = "sempoa_migrasi_tiger";
        $db_prefix = '';
        $mysql = $this->getDB($serverpath, $db_username, $db_password, $db_name);
        $q = "SELECT * FROM siswa";
        $arrMuridAsli = $this->query($q, $mysql);
        $arrMurid = array();
        foreach ($arrMuridAsli as $murid) {
            $rest = substr($murid->kode_siswa, 1);
            $ibo = substr($rest, 1, 2);

            $tc = substr($rest, 2);
            $tc = substr($tc, 1, 3);
            $org_ibo = $ibo . $tc;
            $arrMurid[$org_ibo][] = $murid;

        }
        pr($arrMurid);
        $this->insertMuridToDBAsliLion($arrMurid);
//
//
//        pr($arrMuridAsli);
    }


    function insertMuridToDBAsliLion($arrMurid)
    {

        $level = array();
        $level['junior 1'] = 1;
        $level['junior 2'] = 2;
        $level['foundation 1'] = 3;
        $level['foundation 2'] = 4;
        $level['intermediate 1'] = 6;
        $level['intermediate 2'] = 7;
        $level['intermediate 3'] = 8;
        $level['advance 1'] = 9;
        $level['advance 2'] = 10;
        $level['advance 3'] = 11;
        $level['Grand Module 1'] = 12;
        $level['Grand Module 2'] = 13;
        $level['Grand Module 3'] = 14;

        $status = array();
        $status['keluar'] = KEY::$STATUSMURIDNKELUAR;
        $status['aktif'] = KEY::$STATUSMURIDAKTIV;
        $status['cuti'] = KEY::$STATUSMURIDCUTI;
        //nis_awal

        $serverpath = "localhost";
        $db_username = "root";
        $db_password = "root";
//        sempoa_live
        $db_name = "sempoa_live_11";
        $db_prefix = '';
        $mysql = $this->getDB($serverpath, $db_username, $db_password, $db_name);
        $count = 1;
        $dataMurid = array();
        $arrMigrasi = array();
        pr(count($arrMurid));

        $arrTcTdkada = array();
        foreach ($arrMurid as $keyorg_id => $muridObj) {

//            pr($muridObj);
            $objTC = $this->getIBOData($keyorg_id);
            if (is_null($objTC->org_id)) {
                $arrTcTdkada[] = $keyorg_id . " =>tc tdk ditemukan";
                pr("key" . $keyorg_id);
                pr("<b>" . $keyorg_id . " =>tc tdk ditemukan</b>");
            } else {

                foreach ($muridObj as $murid) {
                    unset($dataMurid);
                    if ($objTC->tc_migrasi != 1) {

                        unset($dataMurid);
                        unset($arrDatenType);
                        $strKey = "";
                        $strval = "";
                        $tc_id = $objTC->org_id;

                        $ibo_id = Generic::getMyParentID($tc_id);
                        $kpo_id = Generic::getMyParentID($ibo_id);
                        $ak_id = Generic::getMyParentID($kpo_id);
                        $dataMurid['kode_siswa'] = $murid->kode_siswa;
                        $arrDatenType['kode_siswa'] = "String";
                        $dataMurid['nama_siswa'] = $murid->nama_siswa;
                        $arrDatenType['nama_siswa'] = "String";
                        $arrDatenType['jenis_kelamin'] = "String";
                        $dataMurid['alamat'] = $murid->alamat;
                        $arrDatenType['alamat'] = "String";
                        $dataMurid['tempat_lahir'] = $murid->tempat_lahir;
                        $arrDatenType['tempat_lahir'] = "String";
                        $dataMurid['tanggal_lahir'] = $murid->tanggal_lahir;
                        $arrDatenType['tanggal_lahir'] = "String";
                        $dataMurid['telepon'] = $murid->telepon;
                        $arrDatenType['telepon'] = "String";
                        $dataMurid['nama_sekolah'] = $murid->nama_sekolah;
                        $arrDatenType['nama_sekolah'] = "String";
                        $dataMurid['nama_ortu'] = $murid->nama_ortu;
                        $arrDatenType['nama_ortu'] = "String";
                        $dataMurid['gambar'] = $murid->gambar;
                        $arrDatenType['gambar'] = "String";
                        $dataMurid['tanggal_masuk'] = $murid->tanggal_masuk;
                        $arrDatenType['tanggal_masuk'] = "String";
                        $dataMurid['email_ortu'] = $murid->email_ortu;
                        $arrDatenType['email_ortu'] = "String";
                        $dataMurid['id_level_masuk'] = $level[$murid->level_sekarang];
                        $arrDatenType['id_level_masuk'] = "int";
                        $dataMurid['id_level_sekarang'] = $level[$murid->level_sekarang];
                        $arrDatenType['id_level_sekarang'] = "int";
                        $dataMurid['nomor_pendaftaran'] = $murid->nomor_pendaftaran;
                        $arrDatenType['nomor_pendaftaran'] = "String";
                        $dataMurid['status'] = $status[$murid->status];
                        $arrDatenType['status'] = "int";
                        $dataMurid['murid_ak_id'] = $ak_id;
                        $dataMurid['murid_kpo_id'] = $kpo_id;
                        $dataMurid['murid_ibo_id'] = $ibo_id;
                        $dataMurid['murid_tc_id'] = $tc_id;
                        $dataMurid['pay_firsttime'] = 1;

//                        pr($arrDatenType);
                        $arrKey = array();
                        $arrVal = array();
                        foreach ($dataMurid as $key => $val) {
                            $arrKey[] = $key;
                            if ($val == "") {
//                            if ($arrDatenType[$key] == "String") {
//                                $val = "\"\"";
//                            }
                                if ($arrDatenType[$key] == "int") {
                                    $val = 0;
                                }
                            } elseif ($val == "0000-00-00") {
                                $val = KEY::$TGL_KOSONG;

                            }
                            if ($arrDatenType[$key] == "String") {
                                $val = "\"" . $val . "\"";
                            }

                            $arrVal[] = $val;
                        }
                        $strKey = implode(",", $arrKey);
                        $strval = implode(",", $arrVal);
                        $q = "INSERT INTO sempoa__siswa (" . $strKey . ") VALUES (" . $strval . ")";
//                        pr($q);
                        $arrMigrasi[$objTC->name] = $tc_id;
                        $count++;
                        $q2 = mysql_query($q);
                        if (!$q2) {
                            pr($q);
                            pr("gagal");
                        } else {

                            pr("Sukses");
                            pr($count);
                        }
                    }

                }
            }


        }

        if (count($arrTcTdkada) != 0) {
            pr($arrTcTdkada);
        }
        pr($arrMigrasi);
        pr($count);
    }


    public function migrasiStatusMuridTiger()
    {
        $murid = new MuridModel();
        $arrMurids = $murid->getWhere("status=1  AND id_murid >=9183");
        $i = 0;
        foreach ($arrMurids as $mur) {
            $statusMurid = new StatusHisMuridModel();
            $statusMurid->getWhereOne("status_murid_id=$mur->id_murid");
            if (is_null($statusMurid->status_id)) {
                $i++;
                $statusMurid = new StatusHisMuridModel();
                $statusMurid->status_murid_id = $mur->id_murid;
                $statusMurid->status_tanggal_mulai = "2017-07-30 22:00:00";
                $statusMurid->status_level_murid = $mur->id_level_sekarang;
                $statusMurid->status = $mur->status;
                $statusMurid->status_ak_id = $mur->murid_ak_id;
                $statusMurid->status_kpo_id = $mur->murid_kpo_id;
                $statusMurid->status_ibo_id = $mur->murid_ibo_id;
                $statusMurid->status_tc_id = $mur->murid_tc_id;
                $statusMurid->save();
                $logMurid = new LogStatusMurid();
                $logMurid->createLogMurid($mur->id_murid);

            }
        }
        echo "jumlah yg termigrasi: " + $i;

    }

    public function migrasiLevelMuridTiger()
    {
        $murid = new MuridModel();
        $arrMurid = $murid->getWhere("(status=1 OR status= 2) AND id_murid >=9183");
        $count = 0;

        foreach ($arrMurid as $mur) {
            $mj = new MuridJourney();
            $id_murid = $mur->id_murid;
            $mj->getWhereOne("journey_murid_id=$id_murid");
            if (!is_null($mj->journey_id)) {
//            pr($id_murid);
            } else {

                $mk = new MuridJourney();
                $mk->createJourney($id_murid, $mur->id_level_sekarang, "2017-07-30 22:00:00", $mur->murid_tc_id);
                $count++;
            }
        }
        echo "tercreate " . $count . " level ";
    }

    public function MigrasiGpSunshine()
    {
        $serverpath = "localhost";
        $db_username = "root";
        $db_password = "root";
        $db_name = "sempoa_migrasi_sunshine_3";
        $db_prefix = '';
        $mysql = $this->getDB($serverpath, $db_username, $db_password, $db_name);
        $q = "SELECT * FROM siswa";
        $arrMuridAsli = $this->query($q, $mysql);
        $arrMurid = array();
        foreach ($arrMuridAsli as $murid) {
            $rest = substr($murid->kode_siswa, 1);
            $ibo = substr($rest, 1, 2);

            $tc = substr($rest, 2);
            $tc = substr($tc, 1, 3);
            $org_ibo = $ibo . $tc;
            $arrMurid[$org_ibo][] = $murid;

        }
        $count = 0;
        foreach ($arrMurid as $val) {
            $count += count($val);
        }
        pr($count);
//die();
        $this->insertMuridToDBAsliLion($arrMurid);
//        pr($arrMurid);
//
//        pr($arrMuridAsli);
    }

    public function hitungUlangJumlaStock()
    {
        $arrTc = Generic::getAllMyTC(3);
        $arrTc[3] = "ibo";
        foreach ($arrTc as $key => $tc) {
            pr($key . "=>" . $tc);
            $stockNobuku = new StockBuku();
            $arrStockNoBukuGroup = $stockNobuku->getWhere("stock_buku_tc=$key  GROUP BY stock_id_buku ORDER BY stock_grup_level");

            foreach ($arrStockNoBukuGroup as $val) {
                $stockNobukuHlp = new StockBuku();
//                pr($stockNobukuHlp->getJumlah("stock_buku_tc=$key AND stock_id_buku=$val->stock_id_buku"));
                $stockTc = new StockModel();
                $stockTc->org_id = $key;
                $stockTc->id_barang = $val->stock_id_buku;
                $stockTc->jumlah_stock = $stockNobukuHlp->getJumlah("stock_buku_tc=$key AND stock_id_buku=$val->stock_id_buku AND stock_status_tc=1");
                pr($val->stock_id_buku . " - " . $stockTc->jumlah_stock);
                $stockTc->save();
            }
//            pr($arrStockNoBukuGroup);

//            die();
//            $stockTc = new StockModel();
//            $arrStock = $stockTc->getWhere("org_id=$key");
////            pr($arrStock);
//            foreach($arrStock as $val){
//                pr($val->id_barang);
//                $stockNobuku = new StockBuku();
//                pr("Jumlah: " . $stockNobuku->getJumlah("stock_id_buku=$val->id_barang AND stock_buku_tc=$key"));
//                //$stock_id_buku
//            }
        }

    }

    public function hitungUlangJumlaStockIBO()
    {

        $arrTc[3] = "ibo";
        foreach ($arrTc as $key => $tc) {
            pr($key . "=>" . $tc);
            $stockNobuku = new StockBuku();
            $arrStockNoBukuGroup = $stockNobuku->getWhere("stock_buku_ibo=$key  GROUP BY stock_id_buku ORDER BY stock_grup_level");
            foreach ($arrStockNoBukuGroup as $val) {
                $stockNobukuHlp = new StockBuku();
//                pr($stockNobukuHlp->getJumlah("stock_buku_tc=$key AND stock_id_buku=$val->stock_id_buku"));
                $stockTc = new StockModel();
                $stockTc->org_id = $key;
                $stockTc->id_barang = $val->stock_id_buku;
                $stockTc->jumlah_stock = $stockNobukuHlp->getJumlah("stock_buku_ibo=$key AND stock_id_buku=$val->stock_id_buku AND stock_status_ibo=1");
                pr($val->stock_id_buku . " - " . $stockTc->jumlah_stock);
                $stockTc->save();
            }
//            pr($arrStockNoBukuGroup);

//            die();
//            $stockTc = new StockModel();
//            $arrStock = $stockTc->getWhere("org_id=$key");
////            pr($arrStock);
//            foreach($arrStock as $val){
//                pr($val->id_barang);
//                $stockNobuku = new StockBuku();
//                pr("Jumlah: " . $stockNobuku->getJumlah("stock_id_buku=$val->id_barang AND stock_buku_tc=$key"));
//                //$stock_id_buku
//            }
        }

    }

    public function migrasiIuranSPP()
    {

        $iuranBulanan = new IuranBulanan();

        $arrIuran = $iuranBulanan->getAll();
        echo "sasa";
        echo count($arrIuran);
    }
}
