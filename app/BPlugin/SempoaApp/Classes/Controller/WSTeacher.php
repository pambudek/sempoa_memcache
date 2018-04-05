<?php

/**
 * Created by PhpStorm.
 * User: efindiongso
 * Date: 1/24/18
 * Time: 3:11 PM
 */
class WSTeacher extends WebService
{

    public function loginTeacher()
    {
        if (Efiwebsetting::getData('checkOAuth') == 'yes')
            IMBAuth::checkOAuth();

        $kode_guru = addslashes($_POST['kode_guru']);
        Generic::checkFieldKosong($kode_guru, KEYAPP::$GURU_ID_KOSONG);
        $teache_pwd = addslashes($_POST['teache_pwd']);
        Generic::checkFieldKosong($teache_pwd, KEYAPP::$PASSWORD_SALAH);

        $objGuru = new SempoaGuruModel();
        $objGuru->getWhereOne("kode_guru='$kode_guru' AND guru_app_pwd='$teache_pwd'");

        if (is_null($objGuru->guru_id)) {
            Generic::errorMsg(KEYAPP::$TEACHER_TDK_BS_LOGIN);
        }
        $arrWS = explode(",",$objGuru->APPWS);
        $arrHlp = array();
        foreach($arrWS as $val){
            $arrHlp[$val] = $objGuru->$val;

        }

        $tc = new SempoaOrg();
        $tc->getByID($objGuru->guru_tc_id);
        $arrWS = explode(",",$tc->ContactTCAPP);
        $arrHlpTC = array();
        foreach($arrWS as $val){
            $arrHlpTC[$val] = $tc->$val;
        }
        $arrHlp['TC'] = $arrHlpTC;

        $ibo = new SempoaOrg();
        $ibo->getByID($objGuru->guru_ibo_id);
        foreach($arrWS as $val){
            $arrHlpTC[$val] = $ibo->$val;
        }
        $arrHlp['IBO'] = $arrHlpTC;
        $json = array();
        $json['status_code'] = 1;
        $json['results'] = $arrHlp;
        $json['status_message'] = KEYAPP::$LOGIN_BERHASIL;
        echo json_encode($json);
        die();
        // nama
        // kode
        // nama tc
        // kelas
        // add Murid

    }

    public function homeTeacher()
    {
        if (Efiwebsetting::getData('checkOAuth') == 'yes')
            IMBAuth::checkOAuth();

        $kode_guru = addslashes($_POST['kode_guru']);
        Generic::checkFieldKosong($kode_guru, KEYAPP::$GURU_ID_KOSONG);

        $objGuru = new SempoaGuruModel();
        $objGuru->getWhereOne("kode_guru='$kode_guru'");

        if (is_null($objGuru->guru_id)) {
            Generic::errorMsg(KEYAPP::$TEACHER_TDK_BS_LOGIN);
        }

        $arrGuruWS = explode(",", $objGuru->APPWS);
        $jsonGuru = array();
        foreach ($arrGuruWS as $val) {
            $jsonGuru[$val] = $objGuru->$val;
        }
        $jsonGuru['TC'] = Generic::getTCNamebyID($objGuru->guru_tc_id);
        $json = array();
        $json['status_code'] = 1;

        $date = new DateTime('today');
        $hari = $date->format("w");

        $kls = new KelasWebModel();
        $arrKelas = $kls->getGuruKelasByDay($jsonGuru['guru_id'], $hari);
        $arrKelasWS = explode(",", $kls->APPWS);
        $arrKelasHelp = array();
        $jsonKelas = array();

//        $jsonKelas['Date'] = $date->format("d M Y");
        foreach ($arrKelas as $kls) {
            unset($arrKelasHelp);
            foreach ($arrKelasWS as $val) {
                $arrKelasHelp[$val] = $kls->$val;
            }
            $jsonKelas[] = $arrKelasHelp;
        }

        $json['result']['Guru'] = $jsonGuru;
        $json['result']['Date'] = $date->format("d M Y");
        $json['result']['Kelas'] = $jsonKelas;
        $json['status_message'] = KEYAPP::$SUCCESS;
        echo json_encode($json);
        die();
    }


    public function getMuridsByKelasID()
    {

        if (Efiwebsetting::getData('checkOAuth') == 'yes')
            IMBAuth::checkOAuth();

//        $guru_id = addslashes($_POST['guru_id']);
//        Generic::checkFieldKosong($guru_id, KEYAPP::$GURU_ID_KOSONG);

        $kelas_id = addslashes($_POST['kelas_id']);
        Generic::checkFieldKosong($kelas_id, "Kelas ID Kosong");
        $objKelasMatrix = new MuridKelasMatrix();
        $arrMurids = $objKelasMatrix->getAllMuridAktivByKelas($kelas_id);

        $arrKelasWS = explode(",", $objKelasMatrix->crud_webservice_allowed);
        $arrMurid = array();
        $jsonMurid = array();
//        $today = new Dat
        foreach ($arrMurids as $murid) {
            unset($arrMurid);
            foreach ($arrKelasWS as $val) {
                if ($val == "murid_id") {
                    $arrMurid["nama_murid"] = Generic::getMuridNamebyID($murid->$val);
//                    $kode_siswa = Generic2::getKodeSiswaByMuridId($murid->$val);
                    $arrMurid["kode_siswa"] =  Generic2::getKodeSiswaByMuridId($murid->$val);


                }
                if ($val == "level_murid") {
                    $arrMurid["Level"] = Generic::getLevelNameByID($murid->$val);
                }

                $arrMurid[$val] = $murid->$val;
            }
            $hariini = new DateTime('now');
            $tgl = $hariini->format("Y-m-d");
            $progress = new ProgressModel();
            $lastProgress = $progress->getMuridProgressByDate($arrMurid["kode_siswa"],$arrMurid["level_murid"],$tgl);
            $arrMurid['prog'] = $lastProgress;
            // jika tdk ada last progress, create new
            if($lastProgress == null){
                $progress = new ProgressModel();
                $progress->createProgress($arrMurid["kode_siswa"], $arrMurid["level_murid"], $arrMurid["guru_id"], $tgl);
                $lastProgress = $progress->getMuridProgressByDate($arrMurid["kode_siswa"],$arrMurid["level_murid"],$tgl);
                $arrMurid['prog'] = $lastProgress;
            }
            $jsonMurid[] = $arrMurid;
        }

        $json['result']['Murids'] = $jsonMurid;
        $json['status_message'] = KEYAPP::$SUCCESS;
        echo json_encode($json);
        die();
    }


    function getListStudentygblmadakelas()
    {
        $kelas_id = addslashes($_POST['kelas_id']);
        Generic::checkFieldKosong($kelas_id, KEYAPP::$PARENT_ID_KOSONG);
        $guru_id = addslashes($_POST['guru_id']);
        Generic::checkFieldKosong($kelas_id, KEYAPP::$GURU_ID_KOSONG_LOGOUT);

        $tc_id = addslashes($_POST['tc_id']);
        Generic::checkFieldKosong($tc_id, KEYAPP::$PARENT_ID_KOSONG);
        // Cek, guru ini ngajar di kelas id ini atau tidak
        $kelas = new KelasWebModel();
        $kelas->getWhereOne("id_kelas='$kelas_id' AND guru_id='$guru_id'");

        if (is_null($kelas->id_kelas)) {
            Generic::errorMsg(KEYAPP::$GURU_TDK_NGAJAR_DIKELAS_INI);
        }


        $kelas = new KelasWebModel();
        $kelas->getByID($kelas_id);


        $mk = new MuridKelasMatrix();
        $arrMuriddiKelas = $mk->getWhere(" kelas_id = '$kelas_id' AND active_status = 1");

        $ids = array();
        foreach ($arrMuriddiKelas as $mur) {
            $ids[] = "id_murid != '" . $mur->murid_id . "'";
        }
        $imp = implode(" AND ", $ids);
        if (count($ids) > 0)
            $imp = $imp . " AND ";

        $mur = new MuridModel();
        $arrMuridYangBlomKursus = $mur->getWhere($imp . " id_level_sekarang <= '{$kelas->level}' AND murid_tc_id = '$tc_id' AND status=1 ORDER BY nama_siswa ASC");


        $arrWS = explode(",", $mur->APPWS);
        $arrMurid = array();
        $jsonMurid = array();
        foreach ($arrMuridYangBlomKursus as $murid) {

            unset($arrMurid);
            foreach ($arrWS as $val) {
                $arrMurid[$val] = $murid->$val;
                if ($val == "id_level_sekarang") {
                    $arrMurid["Level"] = Generic::getLevelNameByID($murid->$val);
                }
                if ($val == "nama_siswa") {
                    $arrMurid["Huruf_pertama"] = strtoupper(substr($murid->$val, 0, 1));
                }
            }
            $jsonMurid[] = $arrMurid;
        }

        $json = array();
        $json['status_code'] = 1;
        $json['result'] = $jsonMurid;
        $json['status_message'] = KEYAPP::$SUCCESS;
        echo json_encode($json);
        die();

    }


    function addStudentdiKelasByIDKls()
    {

        $id_murid = addslashes($_POST['id_murid']);
        Generic::checkFieldKosong($id_murid, KEYAPP::$ADD_MURID_ID_KOSONG);
        $kelas_id = addslashes($_POST['kelas_id']);
        Generic::checkFieldKosong($kelas_id, KEYAPP::$PARENT_ID_KOSONG);
        $guru_id = addslashes($_POST['guru_id']);
        Generic::checkFieldKosong($kelas_id, KEYAPP::$GURU_ID_KOSONG_LOGOUT);
        $tc_id = addslashes($_POST['tc_id']);
        Generic::checkFieldKosong($tc_id, KEYAPP::$PARENT_ID_KOSONG);


        // Cek, guru ini ngajar di kelas id ini atau tidak
        $kelas = new KelasWebModel();
        $kelas->getWhereOne("id_kelas='$kelas_id' AND guru_id='$guru_id'");

        if (is_null($kelas->id_kelas)) {
            Generic::errorMsg(KEYAPP::$GURU_TDK_NGAJAR_DIKELAS_INI);
        }

        if($tc_id != $kelas->tc_id){
            Generic::errorMsg(KEYAPP::$PARENT_ID_KOSONG . " TC berbeda");
        }

        $objMurid = new MuridModel();
        $objMurid->getByID($id_murid);
        if($tc_id != $objMurid->murid_tc_id){
            Generic::errorMsg(KEYAPP::$PARENT_ID_KOSONG . " TC berbeda");
        }

        // cek murid sdh di add
        $mk = new MuridKelasMatrix();
        $mk->getWhereOne("murid_id='$id_murid' AND kelas_id='$kelas_id' AND active_status=1");


        if (!is_null($mk->mk_id)) {
            Generic::errorMsg(KEYAPP::$MURID_SUDAH_DI_ADD_DALAM_KELAS);
        }


        $mk = new MuridKelasMatrix();
        $mk->kelas_id = $kelas_id;
        $mk->murid_id = $id_murid;
        $mk->active_status = 1;
        $mk->tc_id = AccessRight::getMyOrgID();
        $mk->active_date = leap_mysqldate();
        $mk->guru_id = $guru_id;
        $mk->nama_guru = Generic::getGuruNamebyID($guru_id);
        $mk->level_kelas = $kelas->level;
        $mk->level_murid = $objMurid->id_level_sekarang;

        if ($mk->save()) {


            // cek progress sdh ada belum
            $json = array();
            $json['status_code'] = 1;
            $json['status_message'] = KEYAPP::$SUCCESS;
            echo json_encode($json);
        } else {
            Generic::errorMsg(KEYAPP::$MURID_GAGAL_DIMASUKAN_KELAS);
        }
    }


    // Notification
    // Guru dan Murid, KODE GURU
    public function getNotificationByID()
    {
        if (Efiwebsetting::getData('checkOAuth') == 'yes')
            IMBAuth::checkOAuth();

        $kode_guru = addslashes($_POST['kode_guru']);
        Generic::checkFieldKosong($kode_guru, KEYAPP::$PARENT_ID_KOSONG);

        $objNotif = new SempoaNotification();

        $arrNotif = $objNotif->getMyNotif($kode_guru);
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
        $json['status_message'] = "!";
        echo json_encode($json);
        die();

    }


    public function readNotificationByID()
    {
        if (Efiwebsetting::getData('checkOAuth') == 'yes')
            IMBAuth::checkOAuth();
        $kode_guru = addslashes($_POST['kode_guru']);
        Generic::checkFieldKosong($kode_guru, KEYAPP::$PARENT_ID_KOSONG);

        $notif_id = addslashes($_POST['notif_id']);
        Generic::checkFieldKosong($notif_id, "Notif ID kosong");

        $objNotif = new SempoaNotification();

        $objNotif->getWhereOne("notification_belongs_id='$kode_guru' AND notification_id='$notif_id'");
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


    public function absensiGuru()
    {

        $guru_id = addslashes($_GET['guru_id']);
        Generic::checkFieldKosong($guru_id, KEYAPP::$GURU_ID_KOSONG_LOGOUT);

        $kelas_id = addslashes($_GET['kelas_id']);
        Generic::checkFieldKosong($guru_id, KEYAPP::$KELAS_ID_KOSONG_ABSENSI);

        $kelas = new KelasWebModel();
        $kelas->getByID($kelas_id);
        $hari_ini = new DateTime();
        $dayweek = date('w', strtotime($hari_ini->format("Y-m-d")));
        if ($guru_id != $kelas->guru_id) {
            Generic::errorMsg(KEYAPP::$GURU_TDK_NGAJAR_DI_KELAS_INI);
        }
        if ($dayweek != $kelas->hari_kelas) {
            Generic::errorMsg(KEYAPP::$GURU_NGAJAR_TP_WAKTU_BERBEDA);
        }

        $jam_mulai = $kelas->jam_mulai_kelas;
        $jam_akhir = $kelas->jam_akhir_kelas;
        $jam_mulai_start = substr($jam_mulai, 0, 2);
        $jam_mulai_akhir = substr($jam_mulai, 3, 2);
        $jam_akhir_mulai = substr($jam_akhir, 0, 2);
        $jam_akhir_akhir = substr($jam_akhir, 3, 2);

        $now = time();
        $timeStart = mktime($jam_mulai_start, $jam_mulai_akhir);
        $timeStop = mktime($jam_akhir_mulai, $jam_akhir_akhir);


        if ($now > $timeStart && $now < $timeStop) {

            $hari_ini = date("Y-m-d");
            $abs = new AbsenGuruModel();
            //apa perlu cek apakah hari ini sdh absen ??
            $cnt = $abs->getJumlah("absen_date = '$hari_ini' AND absen_guru_id = '$guru_id' AND absen_kelas_id = '$kelas_id'");
            if ($cnt > 0) {
                $json['status_code'] = 0;
                $json['status_message'] = "Hari ini sudah absen";
                echo json_encode($json);
                die();
            }

            $abs->absen_date = date("Y-m-d");
            $abs->absen_pengabsen_id = Account::getMyID();
            $abs->absen_kelas_id = $kelas_id;
            $abs->absen_guru_id = $guru_id;
            $abs->absen_reg_date = leap_mysqldate();
//        $abs->absen_mk_id = $mk_id;

            $guru = new SempoaGuruModel();
            $guru->getByID($guru_id);
            $abs->absen_ak_id = $guru->guru_ak_id;
            $abs->absen_kpo_id = $guru->guru_kpo_id;
            $abs->absen_ibo_id = $guru->guru_ibo_id;
            $abs->absen_tc_id = $guru->guru_tc_id;

            if ($abs->save()) {

                $rekapAbsenGuru = new RekapAbsenCoach();
                $date = new DateTime('today');
                $week = $date->format("W");
                $id_absen_coach = $guru_id . "_" . $week;

                $count = $rekapAbsenGuru->searchMuridSdhAbsen($id_absen_coach, $date);
                if ($count > 0) {
                    $rekapAbsenGuru->updateGuruName($id_absen_coach, $guru_id);
                } else {
                    $rekapAbsenGuru->addAbsenCouchFromGuru($guru_id, $guru->guru_ak_id, $guru->guru_kpo_id, $guru->guru_ibo_id, $guru->guru_tc_id, $date);
                }

                $json['status_code'] = 1;
                $json['status_message'] = "Guru berhasil diabsen!";
                echo json_encode($json);
                die();
            }

            $json['status_code'] = 0;
            $json['status_message'] = "Save Failed";
            echo json_encode($json);
            die();

        } else {
            Generic::errorMsg(KEYAPP::$GURU_TIDAK_BOLEH_ABSEN);
        }
    }

    // Setting Guru

    // Change Teacher's name
    public function changeTeacherName()
    {

        if (Efiwebsetting::getData('checkOAuth') == 'yes')
            IMBAuth::checkOAuth();


        $kode_guru = addslashes($_POST['kode_guru']);
        $guru_fullname = addslashes($_POST['guru_fullname']);
        Generic::checkFieldKosong($kode_guru, KEYAPP::$PARENT_ID_KOSONG);
        Generic::checkFieldKosong($guru_fullname, KEYAPP::$PARENT_ID_KOSONG);

        // cek username
        $guru_newname = addslashes($_POST['guru_newname']);
        $guru_pwd = addslashes($_POST['guru_pwd']);
        Generic::checkFieldKosong($guru_newname, KEYAPP::$MASUKAN_NAMA_PARENT);
        Generic::checkFieldKosong($guru_pwd, KEYAPP::$MASUKAN_PASSWORD_PARENT);

        // Cek nama lama dan pwd
        $objGuru = new SempoaGuruModel();
        $objGuru->getWhereOne("kode_guru='$kode_guru' AND nama_guru='$guru_fullname' AND guru_app_pwd='$guru_pwd'");
        if (is_null($objGuru->guru_id)) {
            Generic::errorMsg(KEYAPP::$ID_GURU_TIDAK_DITEMUKAN);
        } else {
            $objGuru->setFieldModel("nama_guru", $guru_newname);
            if ($objGuru->save(1)) {
                $json = array();
                $json['status_code'] = 1;
                $json['status_message'] = KEYAPP::$PARENT_GANTI_NAMA_SUKSES;
                echo json_encode($json);
                die();
            }
        }
    }

    // Change Teacher's Email
    public function changeTeacherEmail()
    {

        if (Efiwebsetting::getData('checkOAuth') == 'yes')
            IMBAuth::checkOAuth();


        $kode_guru = addslashes($_POST['kode_guru']);
        $guru_email = addslashes($_POST['guru_email']);
        Generic::checkFieldKosong($kode_guru, KEYAPP::$PARENT_ID_KOSONG);
//        Generic::checkFieldKosong($guru_email, KEYAPP::$PARENT_ID_KOSONG);

        // cek username
        $guru_newemail = addslashes($_POST['guru_newemail']);
        if(!Generic::isEmailValid($guru_newemail)){
            Generic::errorMsg("Email tidak valid");
        }
        $guru_pwd = addslashes($_POST['guru_pwd']);
        Generic::checkFieldKosong($guru_newemail, KEYAPP::$MASUKAN_EMAIL_BARU_ANDA);
        Generic::checkFieldKosong($guru_pwd, KEYAPP::$MASUKAN_PASSWORD_PARENT);

        // Cek nama lama dan pwd
        $objGuru = new SempoaGuruModel();
        $objGuru->getWhereOne("kode_guru='$kode_guru' AND email_guru='$guru_email' AND guru_app_pwd='$guru_pwd'");
        if (is_null($objGuru->guru_id)) {
            Generic::errorMsg(KEYAPP::$ID_GURU_TIDAK_DITEMUKAN);
        } else {
            $objGuru->setFieldModel("email_guru", $guru_newemail);
            if ($objGuru->save(1)) {
                $json = array();
                $json['status_code'] = 1;
                $json['status_message'] = KEYAPP::$EMAIL_ANDA_SUKSES_DIGANTI;
                echo json_encode($json);
                die();
            }
        }
    }

    public function changeTeacherHP()
    {

        if (Efiwebsetting::getData('checkOAuth') == 'yes')
            IMBAuth::checkOAuth();


        $kode_guru = addslashes($_POST['kode_guru']);
        $nomor_hp = addslashes($_POST['nomor_hp']);
        Generic::checkFieldKosong($kode_guru, KEYAPP::$PARENT_ID_KOSONG);

        // cek username
        $guru_newhp = addslashes($_POST['guru_newhp']);
        $guru_pwd = addslashes($_POST['guru_pwd']);
        Generic::checkFieldKosong($guru_newhp, KEYAPP::$NO_HP_BARU_TIDAK_ADA);
        Generic::checkFieldKosong($guru_pwd, KEYAPP::$MASUKAN_PASSWORD_PARENT);

        // Cek nama lama dan pwd
        $objGuru = new SempoaGuruModel();
        $objGuru->getWhereOne("kode_guru='$kode_guru' AND nomor_hp='$nomor_hp' AND guru_app_pwd='$guru_pwd'");
        if (is_null($objGuru->guru_id)) {
            Generic::errorMsg(KEYAPP::$ID_GURU_TIDAK_DITEMUKAN);
        } else {
            $objGuru->setFieldModel("nomor_hp", $guru_newhp);
            if ($objGuru->save(1)) {
                $json = array();
                $json['status_code'] = 1;
                $json['status_message'] = KEYAPP::$HP_ANDA_SUKSES_DIGANTI;
                echo json_encode($json);
                die();
            }
        }
    }

    public function changeTeacherPassword()
    {

        if (Efiwebsetting::getData('checkOAuth') == 'yes')
            IMBAuth::checkOAuth();


        $kode_guru = addslashes($_POST['kode_guru']);
        Generic::checkFieldKosong($kode_guru, KEYAPP::$PARENT_ID_KOSONG);

        $guru_pwd = addslashes($_POST['guru_pwd']);
        Generic::checkFieldKosong($guru_pwd, KEYAPP::$MASUKAN_PASSWORD_PARENT);

        $guru_new_pwd = addslashes($_POST['guru_new_pwd']);
        Generic::checkFieldKosong($guru_new_pwd, KEYAPP::$PWD_BARU_KOSONG);

        if(strlen($guru_new_pwd) != KEYAPP::$PANJANG_PWD){
            Generic::errorMsg(KEYAPP::$PANJANG_PWD_HRS_6);
        }

        // Cek nama lama dan pwd
        $objGuru = new SempoaGuruModel();
        $objGuru->getWhereOne("kode_guru='$kode_guru'  AND guru_app_pwd='$guru_pwd'");
        if (is_null($objGuru->guru_id)) {
            Generic::errorMsg(KEYAPP::$ID_GURU_TIDAK_DITEMUKAN);
        } else {
            $objGuru->setFieldModel("guru_app_pwd", $guru_new_pwd);
            if ($objGuru->save(1)) {
                $json = array();
                $json['status_code'] = 1;
                $json['status_message'] = KEYAPP::$PARENT_GANTI_PASSWORD_SUKSES;
                echo json_encode($json);
                die();
            }
        }
    }


    public function refresh(){
        if (Efiwebsetting::getData('checkOAuth') == 'yes')
            IMBAuth::checkOAuth();

        $kode_guru = addslashes($_POST['kode_guru']);
        Generic::checkFieldKosong($kode_guru, KEYAPP::$GURU_ID_KOSONG);

        $objGuru = new SempoaGuruModel();
        $objGuru->getWhereOne("kode_guru='$kode_guru'");

        if (is_null($objGuru->guru_id)) {
            Generic::errorMsg(KEYAPP::$TEACHER_TDK_BS_LOGIN);
        }
        $arrWS = explode(",",$objGuru->APPWS);
        $arrHlp = array();
        foreach($arrWS as $val){
            $arrHlp[$val] = $objGuru->$val;

        }

        $tc = new SempoaOrg();
        $tc->getByID($objGuru->guru_tc_id);
        $arrWS = explode(",",$tc->ContactTCAPP);
        $arrHlpTC = array();
        foreach($arrWS as $val){
            $arrHlpTC[$val] = $tc->$val;
        }
        $arrHlp['TC'] = $arrHlpTC;

        $ibo = new SempoaOrg();
        $ibo->getByID($objGuru->guru_ibo_id);
        foreach($arrWS as $val){
            $arrHlpTC[$val] = $ibo->$val;
        }
        $arrHlp['IBO'] = $arrHlpTC;
        $json = array();
        $json['status_code'] = 1;
        $json['results'] = $arrHlp;
        $json['status_message'] = KEYAPP::$LOGIN_BERHASIL;
        echo json_encode($json);
        die();
    }
    public function getProgress(){


        $progress = new ProgressModel();
        $dateToday = new DateTime('Now');
        $date = $dateToday->format("Y-m-d");
        $kode_siswa = "1406030001";
        $progress_level = 10;
        $progress_guru_id = 12;
//        pr($progress->createProgress($kode_siswa, $progress_level, $progress_guru_id, $date));

        pr($progress->listProgressByDate($kode_siswa, $progress_level));
    }

    /*
     *  public function getMuridsByKelasID()
    {

        if (Efiwebsetting::getData('checkOAuth') == 'yes')
            IMBAuth::checkOAuth();

//        $guru_id = addslashes($_POST['guru_id']);
//        Generic::checkFieldKosong($guru_id, KEYAPP::$GURU_ID_KOSONG);

        $kelas_id = addslashes($_POST['kelas_id']);
        Generic::checkFieldKosong($kelas_id, "Kelas ID Kosong");
        $objKelasMatrix = new MuridKelasMatrix();
        $arrMurids = $objKelasMatrix->getAllMuridAktivByKelas($kelas_id);

        $arrKelasWS = explode(",", $objKelasMatrix->crud_webservice_allowed);
        $arrMurid = array();
        $jsonMurid = array();
        foreach ($arrMurids as $murid) {
            unset($arrMurid);
            foreach ($arrKelasWS as $val) {
                if ($val == "murid_id") {
                    $arrMurid["nama_murid"] = Generic::getMuridNamebyID($murid->$val);
                }
                if ($val == "level_murid") {
                    $arrMurid["Level"] = Generic::getLevelNameByID($murid->$val);
                }

                $arrMurid[$val] = $murid->$val;
            }
            $jsonMurid[] = $arrMurid;
        }

        $json['result']['Murids'] = $jsonMurid;
        $json['status_message'] = KEYAPP::$SUCCESS;
        echo json_encode($json);
        die();
    }
     */
}