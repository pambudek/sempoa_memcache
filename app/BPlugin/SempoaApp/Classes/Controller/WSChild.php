<?php

/**
 * Created by PhpStorm.
 * User: efindiongso
 * Date: 13/02/18
 * Time: 09.32
 */
class WSChild extends WebService
{
    public function homeChild()
    {

        if (Efiwebsetting::getData('checkOAuth') == 'yes')
            IMBAuth::checkOAuth();

        $kode_siswa = addslashes($_POST['kode_siswa']);
        Generic::checkFieldKosong($kode_siswa, KEYAPP::$ID_MURID_KOSONG);

        $objChild = new MuridModel();
        $objChild->getWhereOne("kode_siswa='$kode_siswa'");

        if (is_null($objChild->kode_siswa)) {
            Generic::errorMsg(KEYAPP::$MURID_SALAH_LOGIN);
        }

        $json = array();

        $arrWS = explode(",", $objChild->APPWS);

        $arrchildHlp = array();
        foreach ($arrWS as $val) {
            $arrchildHlp[$val] = $objChild->$val;
        }
        $arrchildHlp['level'] = Generic::getLevelNameByID($objChild->id_level_sekarang);
        // Progress
        $progress = new ProgressModel();
        $progress->getWhereOne("kode_siswa='$kode_siswa' ORDER BY progress_updated DESC");
        $arrWS = explode(",", $progress->WSHalBuku);
        $arrHalBukuHlp = array();
        foreach ($arrWS as $val) {
            if ($progress->$val != "")
                $arrHalBukuHlp[$val] = $progress->$val;
        }
        $arrchildHlp['progress'] = $arrHalBukuHlp;

        $tc = new SempoaOrg();
        $tc->getByID($objChild->murid_tc_id);
        $arrWS = explode(",", $tc->ContactTCAPP);
        $arrHlpTC = array();
        foreach ($arrWS as $val) {
            $arrHlpTC[$val] = $tc->$val;
        }
        $arrchildHlp['contact_tc'] = $arrHlpTC;

        $ibo = new SempoaOrg();
        $ibo->getByID($objChild->murid_ibo_id);
        foreach ($arrWS as $val) {
            $arrHlpTC[$val] = $ibo->$val;
        }
        $arrchildHlp['contact_ibo'] = $arrHlpTC;
        $id_murid = $objChild->id_murid;
        // last attandance
        $absen = new AbsenEntryModel();
        $absen->getWhereOne("absen_murid_id=$id_murid ORDER BY absen_reg_date DESC");
        //jumlah coin, jumlah rewards
        // progress buku
        $date = new DateTime($absen->absen_date);
        $arrchildHlp['absen'] = $date->format("d-M-Y");
        $arrchildHlp['coin'] = "100.000 Coins";
        $arrchildHlp['rewards'] = "Rp. 200.000,- Rewards";
        $arrchildHlp['TC'] = Generic::getTCNamebyID($objChild->murid_tc_id);
        $arrchildAll[] = $arrchildHlp;


        $json['status_code'] = 1;
        $json['result'] = $arrchildAll;
        $json['status_message'] = KEYAPP::$SUCCESS;
        echo json_encode($json);
        die();

    }

    public function AbsenMurid()
    {
        $murid_id = addslashes($_POST['murid_id']);
        Generic::checkFieldKosong($murid_id, KEYAPP::$ID_MURID_KOSONG);

        $kelas_id = addslashes($_POST['kelas_id']);
        Generic::checkFieldKosong($kelas_id, KEYAPP::$KELAS_ID_KOSONG_ABSENSI);

        $kelas = new MuridKelasMatrix();
        $kelas->getWhereOne("murid_id='$murid_id' AND kelas_id='$kelas_id'");
        if (is_null($kelas->murid_id)) {
            Generic::errorMsg(KEYAPP::$KELAS_SALAH_MURID);
        }

        $hari_ini = date("Y-m-d");

        $abs = new AbsenEntryModel();

        $cnt = $abs->getJumlah("absen_date = '$hari_ini' AND absen_murid_id = '$murid_id' AND absen_kelas_id = '$kelas_id'");


        if ($cnt > 0) {
            $json['status_code'] = 0;
            $json['status_message'] = KEYAPP::$HARI_INI_SUDAH_ABSEN;
            echo json_encode($json);
            die();
        }


        $abs->absen_date = date("Y-m-d");
        $abs->absen_pengabsen_id = Account::getMyID();
        $abs->absen_kelas_id = $kelas_id;
        $abs->absen_murid_id = $murid_id;
        $abs->absen_reg_date = leap_mysqldate();
        $abs->absen_tc_id = AccessRight::getMyOrgID();
        $abs->absen_guru_id = $kelas->guru_id;
        $murid = new MuridModel();
        $murid->getByID($murid_id);
        $abs->absen_ibo_id = $murid->murid_ibo_id;
        $abs->absen_kpo_id = $murid->murid_kpo_id;
        $abs->absen_ak_id = $murid->murid_ak_id;
        if ($abs->save()) {

            $rekapAbsenGuru = new RekapAbsenCoach();
            $date = new DateTime('today');
            $rekapAbsenGuru->addAbsenCouchFromMurid($kelas->guru_id, $murid->murid_ak_id, $murid->murid_kpo_id, $murid->murid_ibo_id, $murid->murid_tc_id, $date, $murid->id_level_sekarang);
            $json['status_code'] = 1;
            $json['status_message'] = KEYAPP::$BERHASIL_ABSEN;
            echo json_encode($json);
            die();
        }

        $json['status_code'] = 0;
        $json['status_message'] = "Save Failed";
        echo json_encode($json);
        die();
    }


//wallet
    public function getWalletMurid()
    {

        if (Efiwebsetting::getData('checkOAuth') == 'yes')
            IMBAuth::checkOAuth();
        $kode_siswa = addslashes($_POST['kode_siswa']);
        Generic::checkFieldKosong($kode_siswa, KEYAPP::$ID_MURID_KOSONG);

        $topup_jumlah = addslashes($_POST['topup_jumlah']);
        Generic::checkFieldKosong($topup_jumlah, KEYAPP::$JUMLAH_COIN_KOSOSNG);

        $objwallet = new SempoaTopup();

        $objwallet->getWhereOne("topup_kodesiswa = '$kode_siswa' AND topup_jumlah='$topup_jumlah'");
        if (is_null($objwallet->topup_id)) {
            Generic::errorMsg(KEYAPP::$JUMLAH_COIN_KOSOSNG);
        }

        $json = array();

        $arrWS = explode(",", $objwallet->crud_webservice_allowed);

        $arrwalletHlp = array();
        foreach ($arrWS as $val) {
            $arrwalletHlp[$val] = $objwallet->$val;
        }
        $arrwalletAll[] = $arrwalletHlp;

        $json['status_code'] = 1;
        $json['result'] = $arrwalletAll;
        $json['status_message'] = KEYAPP::$SUCCESS;
        echo json_encode($json);
        die();
    }

    public function getSettingCoin()
    {
        if (Efiwebsetting::getData('checkOAuth') == 'yes')
            IMBAuth::checkOAuth();
        $objSettingCoin = new SettingCoinModel();
        $arrCoins = $objSettingCoin->getAll();

        $arrWs = explode(",", $objSettingCoin->crud_webservice_allowed);
        $arrHlp[] = array();
        foreach ($arrCoins as $coins) {
            unset($arrHlp);
            foreach ($arrWs as $val) {
                $arrHlp[$val] = $coins->$val;
            }
            $arrJsonHlp[] = $arrHlp;
        }

        $json['status_code'] = 1;
        $json['result'] = $arrJsonHlp;
        $json['status_message'] = KEYAPP::$SUCCESS;
        echo json_encode($json);
        die();

    }

    public function requestCoinMuridtoParent()
    {

        if (Efiwebsetting::getData('checkOAuth') == 'yes')
            IMBAuth::checkOAuth();

        $kode_siswa = addslashes($_POST['kode_siswa']);
        Generic::checkFieldKosong($kode_siswa, KEYAPP::$ID_MURID_KOSONG);

        $jumlah_coin = addslashes($_POST['jumlah_coin']);
        Generic::checkFieldKosong($jumlah_coin, KEYAPP::$JUMLAH_COIN_KOSOSNG);


        $objParent = new ParentSempoa();
        $objParent->getWhereOne("FIND_IN_SET('$kode_siswa',parent_kode_anak)");

        $requestcoin = new SempoaTopup();

        $requestcoin->topup_kodesiswa = $kode_siswa;
        $requestcoin->topup_parent_id = $objParent->parent_id;
        $requestcoin->topup_jumlah = $jumlah_coin;
        $requestcoin->topup_status = KEYAPP::$STATUS_TOP_UP_PENDING;
        $requestcoin->topup_created_date = leap_mysqldate();
        $requestcoin->topup_pending_date = leap_mysqldate();
        $requestcoin->save();

        $notif = new SempoaNotification();
        $notif->createNotif($objParent->parent_id, KEYAPP::$NOTIF_TYPE_TOP_UP, KEYAPP::$NOTIF_TITLE_TOP_UP, KEYAPP::$NOTIF_CONTENT_TOP_UP);

        $json = array();
        $json['status_code'] = 1;
        $json['status_message'] = "Berhasil Request";
        echo json_encode($json);
        die();

    }
// ok
    public function coinsHistory()
    {
        if (Efiwebsetting::getData('checkOAuth') == 'yes')
            IMBAuth::checkOAuth();

        $kode_siswa = addslashes($_POST['kode_siswa']);
        Generic::checkFieldKosong($kode_siswa, KEYAPP::$PARENT_ID_KOSONG);

        $topUp = new SempoaTopup();
        $arrMyTopUP = $topUp->getStudentTopUp($kode_siswa);

        $arrWS = explode(",", $topUp->crud_webservice_allowed);
        $arrHlp = array();
        $arrJsonHlp = array();
        foreach ($arrMyTopUP as $historyTopup) {
            unset($arrHlp);
            foreach ($arrWS as $val) {
                $arrHlp[$val] = $historyTopup->$val;
            }
            $arrJsonHlp[] = $arrHlp;
        }
        $json = array();
        $json['status_code'] = 1;
        $json['result'] = $arrJsonHlp;
        $json['status_message'] = KEYAPP::$SUCCESS;
        echo json_encode($json);
        die();
    }
    // ok!
    public function withdrawMurid()
    {

        if (Efiwebsetting::getData('checkOAuth') == 'yes')
            IMBAuth::checkOAuth();

        $kode_siswa = addslashes($_POST['kode_siswa']);
        Generic::checkFieldKosong($kode_siswa, KEYAPP::$ID_MURID_KOSONG);


        $objParent = new ParentSempoa();
        $objParent->getWhereOne("FIND_IN_SET('$kode_siswa',parent_kode_anak)");

        $parent_id = $objParent->parent_id;
        Generic::checkFieldKosong($parent_id, KEYAPP::$PARENT_ID_KOSONG);

        $jumlah_with_draw = addslashes($_POST['jumlah_with_draw']);
        Generic::checkFieldKosong($jumlah_with_draw, KEYAPP::$JUMLAH_DIBELI_KOSONG);

        $nomor_bank = addslashes($_POST['nomor_bank']);
        Generic::checkFieldKosong($nomor_bank, KEYAPP::$NOMOR_BANK_KOSONG);

        $parent_pwd = addslashes($_POST['parent_pwd']);
        Generic::checkFieldKosong($parent_pwd, KEYAPP::$PANJANG_PWD_HRS_6);

        // cek id_parent = pwd;

        $objParent = new ParentSempoa();
        $objParent->getWhereOne("parent_id='$parent_id' AND parent_pwd='$parent_pwd'");
        if(is_null($objParent->parent_id)){
            Generic::errorMsg(KEYAPP::$PASSWORD_SALAH);
        }

        $withDraw = new WithdrawModel();
        $withDraw->withdraw_kode_siswa = $kode_siswa;
        $withDraw->withdraw_parent_id = $parent_id;
        $withDraw->withdraw_nomor_bank = $nomor_bank;
        $withDraw->withdraw_jumlah = $jumlah_with_draw;
        $withDraw->withdraw_created_date = leap_mysqldate();
        $withDraw->withdraw_updated = leap_mysqldate();
        $withDraw->withdraw_active = 1;
        $withDraw->withdraw_status = KEYAPP::$STATUS_TOP_UP_PENDING;
        $withDraw->save();

        $json = array();
        $json['status_code'] = 1;
        $json['status_message'] = KEYAPP::$TOP_UP_MSG;
        echo json_encode($json);
        die();

    }



    public function withdrawHistory()
    {
        if (Efiwebsetting::getData('checkOAuth') == 'yes')
            IMBAuth::checkOAuth();


        $kode_siswa = addslashes($_POST['kode_siswa']);
        Generic::checkFieldKosong($kode_siswa, KEYAPP::$PARENT_ID_KOSONG);

        $withDraw = new WithdrawModel();
        $arrMyWithDraw = $withDraw->getMyWithDraw($kode_siswa);

        $arrWS = explode(",", $withDraw->crud_webservice_allowed);
        $arrHlp = array();
        $arrJsonHlp = array();
        foreach ($arrMyWithDraw as $historyWithDraw) {
            unset($arrHlp);
            foreach ($arrWS as $val) {
                $arrHlp[$val] = $historyWithDraw->$val;
            }
            $arrJsonHlp[] = $arrHlp;
        }
        $json = array();
        $json['status_code'] = 1;
        $json['result'] = $arrJsonHlp;
        $json['status_message'] = KEYAPP::$SUCCESS;
        echo json_encode($json);
        die();
    }

//    Profil Murid

    public function getProfilMurid()
    {

        if (Efiwebsetting::getData('checkOAuth') == 'yes')
            IMBAuth::checkOAuth();

        $kode_siswa = addslashes($_POST['kode_siswa']);
        Generic::checkFieldKosong($kode_siswa, KEYAPP::$ID_MURID_KOSONG);

        $objChild = new MuridModel();
        $objChild->getWhereOne("kode_siswa='$kode_siswa'");

        if (is_null($objChild->kode_siswa)) {
            Generic::errorMsg(KEYAPP::$MURID_SALAH_LOGIN);
        }

        $json = array();

        $arrWS = explode(",", $objChild->APPWS);

        $arrchildHlp = array();
        foreach ($arrWS as $val) {
            $arrchildHlp[$val] = $objChild->$val;

        }
        $arrchildAll[] = $arrchildHlp;


        $json['status_code'] = 1;
        $json['result'] = $arrchildAll;
        $json['status_message'] = KEYAPP::$SUCCESS;
        echo json_encode($json);
        die();


    }

    // Notification
    //  Murid, KODE MURID
    public function getNotificationByID()
    {
        if (Efiwebsetting::getData('checkOAuth') == 'yes')
            IMBAuth::checkOAuth();


        $kode_siswa = addslashes($_POST['kode_siswa']);
        Generic::checkFieldKosong($kode_siswa, KEYAPP::$PARENT_ID_KOSONG);

        $objNotif = new SempoaNotification();

        $arrNotif = $objNotif->getMyNotif($kode_siswa);
        $json = array();

        $arrWS = explode(",", $objNotif->crud_webservice_allowed);
        $arrNotifAll = array();
        foreach ($arrNotif as $notif) {
            $arrNotifHlp = array();
            foreach ($arrWS as $val) {
                $arrNotifHlp[$val] = $notif->$val;
            }
            $arrNotifAll[] = $arrNotifHlp;
        }

        $json['status_code'] = 1;
        $json['result'] = $arrNotifAll;
        $json['status_message'] = KEYAPP::$SUCCESS;
        echo json_encode($json);
        die();

    }


    public function readNotificationByID()
    {
        if (Efiwebsetting::getData('checkOAuth') == 'yes')
            IMBAuth::checkOAuth();
        $kode_siswa = addslashes($_POST['kode_siswa']);
        Generic::checkFieldKosong($kode_siswa, KEYAPP::$ID_MURID_KOSONG);

        $notif_id = addslashes($_POST['notif_id']);
        Generic::checkFieldKosong($notif_id, "Notif ID kosong");

        $objNotif = new SempoaNotification();

        $objNotif->getWhereOne("notification_belongs_id='$kode_siswa' AND notification_id='$notif_id'");
        if (is_null($objNotif->notification_id)) {
            Generic::errorMsg("Silahkan pilih Notif sekali lagi");
        }
        $json = array();

        $arrWS = explode(",", $objNotif->crud_webservice_allowed);

        $arrNotifHlp = array();
        foreach ($arrWS as $val) {
            $arrNotifHlp[$val] = $objNotif->$val;
        }
        $arrNotifAll[] = $arrNotifHlp;


        $json['status_code'] = 1;
        $json['result'] = $arrNotifAll;
        $json['status_message'] = KEYAPP::$SUCCESS;
        echo json_encode($json);
        die();
    }


    public function getMyChildTCInfo()
    {
        if (Efiwebsetting::getData('checkOAuth') == 'yes')
            IMBAuth::checkOAuth();

        $kode_siswa = addslashes($_POST['kode_siswa']);
        Generic::checkFieldKosong($kode_siswa, KEYAPP::$PARENT_ID_KOSONG);


        $objMurid = new MuridModel();
        $objMurid->getWhereOne("kode_siswa='$kode_siswa'");
        $objTC = new SempoaOrg();
        $objTC->getByID($objMurid->murid_tc_id);
        $arrTCData = explode(",", $objTC->ContactTCAPP);
        unset($arrDataTC);
        foreach ($arrTCData as $fieldTC) {
            $arrDataTC[$fieldTC] = $objTC->$fieldTC;
        }
        $jsonHelp[] = $arrDataTC;

        $json['status_code'] = 1;
        $json['result'] = $jsonHelp;
        $json['status_message'] = KEYAPP::$SUCCESS;

        echo json_encode($json);
        die();


    }

    public function getMyChildIBOInfo()
    {
        if (Efiwebsetting::getData('checkOAuth') == 'yes')
            IMBAuth::checkOAuth();
        $kode_siswa = addslashes($_POST['kode_siswa']);
        Generic::checkFieldKosong($kode_siswa, KEYAPP::$PARENT_ID_KOSONG);


        $objMurid = new MuridModel();
        $objMurid->getWhereOne("kode_siswa='$kode_siswa'");
        $objTC = new SempoaOrg();
        $objTC->getByID($objMurid->murid_ibo_id);
        $arrTCData = explode(",", $objTC->ContactTCAPP);
        unset($arrDataTC);
        foreach ($arrTCData as $fieldTC) {
            $arrDataTC[$fieldTC] = $objTC->$fieldTC;
        }
        $jsonHelp[] = $arrDataTC;


        $json['status_code'] = 1;
        $json['result'] = $jsonHelp;
        $json['status_message'] = "";
        echo json_encode($json);
        die();


    }

    public function ChallangeSoal()
    {

        if (Efiwebsetting::getData('checkOAuth') == 'yes')
            IMBAuth::checkOAuth();

        $soal_challange_level = addslashes($_POST['soal_challange_level']);
        Generic::checkFieldKosong($soal_challange_level, "level challange anda kosong");

        $soal_challange_soal = addslashes($_POST['soal_challange_soal']);
        Generic::checkFieldKosong($soal_challange_soal, "soal challange anda kosong");

        $soal_challange_jawaban = addslashes($_POST['soal_challange_jawaban']);
        Generic::checkFieldKosong($soal_challange_jawaban, "jawaban challange anda kosong");

        $objChallange = new SoalChallangeModel();
        $objChallange->soal_challange_jawaban = $soal_challange_jawaban;
        $objChallange->soal_challange_soal = $soal_challange_soal;
        $objChallange->soal_challange_level = $soal_challange_level;
        $objChallange->soal_challange_created_date = leap_mysqldate();
        $objChallange->soal_challange_update = leap_mysqldate();
        $objChallange->soal_challange_status = "";
        $objChallange->save();

        $json = array();
        $json['status_code'] = 1;
        $json['status_message'] = KEYAPP::$SUCCESS;
        echo json_encode($json);
        die();

    }

    public function AddChallange()
    {

        if (Efiwebsetting::getData('checkOAuth') == 'yes')
            IMBAuth::checkOAuth();

        $challange_type = addslashes($_POST['challange_type']);
        Generic::checkFieldKosong($challange_type, "type challange anda kosong");

        $challange_title = addslashes($_POST['challange_title']);
        Generic::checkFieldKosong($challange_title, "title challange anda kosong");

        $challange_level = addslashes($_POST['challange_level']);
        Generic::checkFieldKosong($challange_level, "level challange anda kosong");

        $challange_date = addslashes($_POST['challange_date']);
        Generic::checkFieldKosong($challange_date, "date challange anda kosong");

        $kode_siswa = addslashes($_POST['kode_siswa']);

        $objChallange = new ChallangeModel();
        $objChallange->challange_type = $challange_type;
        $objChallange->challange_title = $challange_title;
        $objChallange->challange_level = $challange_level;
        $objChallange->challange_date = $challange_date;
        $objChallange->challange_created_date = leap_mysqldate();
        $objChallange->challange_updated = leap_mysqldate();
        $objMurid = new MuridModel();
        $objMurid->getByID($kode_siswa);
        $objChallange->challange_ak = $objMurid->murid_ak_id;
        $objChallange->challange_kpo = $objMurid->murid_kpo_id;
        $objChallange->challange_ibo = $objMurid->murid_ibo_id;
        $objChallange->challange_tc = $objMurid->murid_tc_id;
        $objChallange->save();

        $json = array();
        $json['status_code'] = 1;
        $json['status_message'] = KEYAPP::$SUCCESS;
        echo json_encode($json);
        die();


    }

    public function ViewChallange()
    {

        if (Efiwebsetting::getData('checkOAuth') == 'yes')
            IMBAuth::checkOAuth();

        $challange_type = addslashes($_POST['challange_type']);
        Generic::checkFieldKosong($challange_type, "type challange anda kosng");

        $challange_title = addslashes($_POST['challange_title']);
        Generic::checkFieldKosong($challange_title, "title challange anda kosng");

        $challange_level = addslashes($_POST['challange_level']);
        Generic::checkFieldKosong($challange_level, "level challange anda kosng");

        $challange_date = addslashes($_POST['challange_date']);
        Generic::checkFieldKosong($challange_date, "date challange anda kosng");

        $objChallange = new ChallangeModel();

    }

    public function joinChallange()
    {

        if (Efiwebsetting::getData('checkOAuth') == 'yes')
            IMBAuth::checkOAuth();

        $kode_siswa = addslashes($_POST['kode_siswa']);
        Generic::checkFieldKosong($kode_siswa, "ID anda kosong");

        $challange_id = addslashes($_POST['challange_id']);
        Generic::checkFieldKosong($challange_id, "ID anda kosong");

        $objMurid = new MuridModel();
        $objMurid->getByID($kode_siswa);

        $today = date("Y-m-d");
        $join = new ChallangeModel();
        $count = $join->getJumlah("challange_date='$today' AND join_kode_siswa='$kode_siswa' AND join_challange_id='$challange_id'");
        if ($count > 0) {
            $json['status_code'] = 0;
            $json['status_message'] = KEYAPP::$MURID_SUDAH_JOIN;
            echo json_encode($json);
            die();
        }

        $join->challange_date = date("Y-m-d");
        $join->challange_murid_ikut = $kode_siswa;
        $join->challange_id = $challange_id;

        $objJoin = new JoinChallangeModel();
        $objJoin->join_challange_id = $challange_id;
        $objJoin->join_kode_siswa = $kode_siswa;

        $objJoin->save();

        $json = array();
        $json['status_code'] = 1;
        $json['status_message'] = "Berhasil Join";
        echo json_encode($json);
        die();


    }


    public function HistoryChallange()
    {

        if (Efiwebsetting::getData('checkOAuth') == 'yes')
            IMBAuth::checkOAuth();

        $kode_siswa = addslashes($_POST['kode_siswa']);
        Generic::checkFieldKosong($kode_siswa, "ID anda kosong");

        $Challange = new JoinChallangeModel();
        $arrayChallange = $Challange->getMuridChallange($kode_siswa);

        $arrWS = explode(",", $Challange->crud_webservice_allowed);
        $arrHlp = array();
        $arrJsonHlp = array();
        foreach ($arrayChallange as $historyChallange) {
            unset($arrHlp);
            foreach ($arrWS as $val) {
                $arrHlp[$val] = $historyChallange->$val;
            }
            $arrJsonHlp[] = $arrHlp;
        }
        $json = array();
        $json['status_code'] = 1;
        $json['result'] = $arrJsonHlp;
        $json['status_message'] = KEYAPP::$SUCCESS;
        echo json_encode($json);
        die();


    }
}