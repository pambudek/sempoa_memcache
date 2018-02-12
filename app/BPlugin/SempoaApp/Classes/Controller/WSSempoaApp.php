<?php

/**
 * Created by PhpStorm.
 * User: efindiongso
 * Date: 1/19/18
 * Time: 8:59 AM
 */
class WSSempoaApp extends WebService
{

    public function registerParent()
    {


        if (Efiwebsetting::getData('checkOAuth') == 'yes')
            IMBAuth::checkOAuth();

        $parent_fullname = addslashes($_POST['parent_fullname']);
        $parent_email = addslashes($_POST['parent_email']);
        $parent_hp_nr = addslashes($_POST['parent_hp_nr']);
        $parent_pwd = addslashes($_POST['parent_pwd']);

        $objParent = new ParentSempoa();
        if ($parent_fullname == "") {
            Generic::errorMsg("Fullname harus diisi");
        }

        if ($parent_email == "") {
            Generic::errorMsg("Email harus diisi");
        } else {
            // cek email valid;
            if (!Generic::isEmailValid($parent_email)) {
                Generic::errorMsg("Email tidak valid");
            } else {
                // cek email sdh pernah dipake
                if (!$objParent->isEmailUsed($parent_email)) {
                    Generic::errorMsg("Email sudah pernah dipakai!");
                }
            }
        }


        if ($parent_hp_nr == "") {
            Generic::errorMsg("No Handphone harus diisi");
        }

        if ($parent_pwd == "") {
            Generic::errorMsg("Password harus diisi");
        }

        $objParent = new ParentSempoa();
        $objParent->parent_fullname = $parent_fullname;
        $objParent->parent_email = $parent_email;
        $objParent->parent_hp_nr = $parent_hp_nr;
        $objParent->parent_pwd = $parent_pwd;
        $objParent->parent_active = 1;
        $objParent->parent_created_date = leap_mysqldate();
        $objParent->parent_last_login = leap_mysqldate();
        $objParent->parent_updated_date = leap_mysqldate();
//        $objParent->parent_last_ip =
        $objParent->save();
        $json['status_code'] = 1;
        $json['status_message'] = "Registrasi berhasil!";
        echo json_encode($json);
        die();
    }

    public function login()
    {
        if (Efiwebsetting::getData('checkOAuth') == 'yes')
            IMBAuth::checkOAuth();

        $user_id = addslashes($_POST['user_id']);
        Generic::checkFieldKosong($user_id, KEYAPP::$PARENT_ID_KOSONG);

        // cek apakah email atau bukan
        // jika email maka ortu
        // jika bukan email cek apakah kode_siswa
        //1606030020
        // Murid
        if (is_numeric($user_id)) {
            $pwd = addslashes($_POST['pwd']);
            Generic::checkFieldKosong($pwd, "Masukan Password");
            $objSiswa = new MuridModel();
            $objSiswa->getWhereOne("kode_siswa='$user_id' AND murid_app_pwd='$pwd'");

            if (is_null($objSiswa->id_murid)) {
                Generic::errorMsg("Kode Siswa atau Password salah!");
            }

            $arrWS = explode(",", $objSiswa->APPWS);

            $arrHlp = array();
            foreach ($arrWS as $val) {
                $arrHlp[$val] = $objSiswa->$val;
            }
            $arrHlp['type'] = KEYAPP::$TYPE_CHILD;
            $json['status_code'] = 1;
            $json['result'] = $arrHlp;
            $json['status_message'] = "Berhasil!";
            echo json_encode($json);
            die();
        } // ini Parent
        elseif (Generic::isEmailValid($user_id)) {
            $pwd = addslashes($_POST['pwd']);
            if ($user_id == "") {
                Generic::errorMsg("Email harus diisi");
            } else {
                // cek email valid;
                if (!Generic::isEmailValid($user_id)) {
                    Generic::errorMsg("Email tidak valid");
                }
            }

            if ($pwd == "") {
                Generic::errorMsg("Password harus diisi");
            }

            $json = array();
            $objParent = new ParentSempoa();
            $objParent->getWhereOne("parent_email='$user_id' AND parent_pwd='$pwd'");
            if (is_null($objParent->parent_id)) {
                Generic::errorMsg("Email salah atau password salah");
            }

            $objParent->setLastLogin($objParent->parent_id);
            $arrWS = explode(",", $objParent->crud_webservice_allowed);

            $arrHlp = array();
            foreach ($arrWS as $val) {
                $arrHlp[$val] = $objParent->$val;
            }
            $arrHlp['type'] = KEYAPP::$TYPE_PARENT;
            $json['status_code'] = 1;
            $json['result'] = $arrHlp;
            $json['status_message'] = "Berhasil!";
            echo json_encode($json);
            die();
        } // Bukan Murid bukan Parent
        else {
            Generic::errorMsg("Masukan user id sebagai Siswa atau Email sebagai Orang tua!");
        }


    }

// ini tidak dipake lagi
//    public function loginParent()
//    {
//        if (Efiwebsetting::getData('checkOAuth') == 'yes')
//            IMBAuth::checkOAuth();
//        $parent_email = addslashes($_POST['parent_email']);
//        $parent_pwd = addslashes($_POST['parent_pwd']);
//        if ($parent_email == "") {
//            Generic::errorMsg("Email harus diisi");
//        } else {
//            // cek email valid;
//            if (!Generic::isEmailValid($parent_email)) {
//                Generic::errorMsg("Email tidak valid");
//            }
//        }
//        if ($parent_pwd == "") {
//            Generic::errorMsg("Password harus diisi");
//        }
//        $json = array();
//        $objParent = new ParentSempoa();
//        $objParent->getWhereOne("parent_email='$parent_email' AND parent_pwd='$parent_pwd'");
//        if (is_null($objParent->parent_id)) {
//            Generic::errorMsg("Email salah atau password salah");
//        }
//        $objParent->setLastLogin($objParent->parent_id);
//        $arrWS = explode(",", $objParent->crud_webservice_allowed);
//        $arrHlp = array();
//        foreach ($arrWS as $val) {
//            $arrHlp[$val] = $objParent->$val;
//        }
//        $json['status_code'] = 1;
//        $json['result'] = $arrHlp;
//        $json['status_message'] = "Berhasil!";
//        echo json_encode($json);
//        die();
//    }

    public function resetPwdParent()
    {
        if (Efiwebsetting::getData('checkOAuth') == 'yes')
            IMBAuth::checkOAuth();
        $parent_email = addslashes($_POST['parent_email']);
        if ($parent_email == "") {
            Generic::errorMsg("Email harus diisi");
        } else {
            // cek email valid;
            if (!Generic::isEmailValid($parent_email)) {
                Generic::errorMsg("Email tidak valid");
            }
        }

        $objParent = new ParentSempoa();
        if ($objParent->isEmailUsed($parent_email)) {
            Generic::errorMsg("Email tidak terdaftar!");
        }

        // create email ganti password
        // send email ke parent
        // content
        // password
        $mail = new Leapmail2();
        $subject = KEYAPP::$subjectForgotPasswordParent;
        $content = "Content";
        $pwd = Generic::generateRandomString(6);
//        $email, $judul, $isi,$isiHTML
        $erg = $mail->sendHTMLEmail($parent_email, $subject, "", $content . " Password: " . $pwd);
        if ($erg) {

            $objParent = new ParentSempoa();
            $objParent->getWhereOne("parent_email='$parent_email'");
            $objParent->parent_pwd = $pwd;
            $objParent->save(1);
            $json['status_code'] = 1;
            $json['status_message'] = "Email berhasil dikirim!";
            echo json_encode($json);
            die();
        } else {
            Generic::errorMsg("Email gagal dikirim!");
        }
    }


    // Home

    public function loadHomeParent()
    {

        if (Efiwebsetting::getData('checkOAuth') == 'yes')
            IMBAuth::checkOAuth();
        $parent_id = addslashes($_POST['parent_id']);
        Generic::checkFieldKosong($parent_id, KEYAPP::$PARENT_ID_KOSONG);

        $objParent = new ParentSempoa();
        $objParent->getByID($parent_id);

        if (is_null($objParent->parent_id)) {
            Generic::errorMsg(KEYAPP::$PARENT_ID_KOSONG);
        }

        if ($objParent->parent_kode_anak != '') {
            $arrKodeanak = explode(",", $objParent->parent_kode_anak);
            $json = array();
            $jsonHlp = array();
            foreach ($arrKodeanak as $val) {
                // cari progress bar
                // wallet
                $objMurid = new MuridModel();
                $objMurid->getWhereOne("kode_siswa='$val'");
                $arrWS = explode(",", $objMurid->crud_webservice_allowed);
                $arrHlp = array();
                foreach ($arrWS as $field) {
                    $arrHlp[$field] = $objMurid->$field;
                }
                $objWallet = new WalletModel();
                $arrHlp['wallet'] = $objWallet->getMyCoin($val);
                $arrHlp['progress'][] = "Halam 10";
                $arrHlp['rank_Challange'] = "1";
                $jsonHlp['list murid'][] = $arrHlp;
                // Wallet

                // Progress


                // Challange

            }
            $json['status_code'] = 1;
            $json['result'] = $jsonHlp;
            $json['status_message'] = KEYAPP::$SUCCESS;
            echo json_encode($json);
            die();
        }
    }

    // Add Child

    public function loadChildDataByParent()
    {
        if (Efiwebsetting::getData('checkOAuth') == 'yes')
            IMBAuth::checkOAuth();

        $parent_id = addslashes($_POST['parent_id']);
        Generic::checkFieldKosong($parent_id, KEYAPP::$PARENT_ID_KOSONG);
        $kode_siswa = addslashes($_POST['kode_siswa']);
        Generic::checkFieldKosong($kode_siswa, KEYAPP::$MASUKAN_KODE_SISWA);
        $sempoaMurid = new MuridModel();
        $sempoaMurid->getWhereOne("kode_siswa = '$kode_siswa'");

        if (is_null($sempoaMurid->id_murid)) {
            Generic::errorMsg(KEYAPP::$KODE_SISWA_NOT_FOUND);
        }

        if ($sempoaMurid->murid_parent_id != 0) {
            Generic::errorMsg(KEYAPP::$MURID_SDH_PUNYA_PARENT);
        }

        $arrWS = explode(",", "nama_siswa,tanggal_lahir");

        $arrHlp = array();
        foreach ($arrWS as $val) {
            $arrHlp[$val] = $sempoaMurid->$val;
        }

        $json['status_code'] = 1;
        $json['result'] = $arrHlp;
        $json['status_message'] = KEYAPP::$SUCCESS;
        echo json_encode($json);
        die();
    }


    // Pas tekan save!
    public function addChildByParent()
    {
        if (Efiwebsetting::getData('checkOAuth') == 'yes')
            IMBAuth::checkOAuth();

        $parent_id = addslashes($_POST['parent_id']);
        Generic::checkFieldKosong($parent_id, KEYAPP::$PARENT_ID_KOSONG);
        $kode_siswa = addslashes($_POST['kode_siswa']);
        Generic::checkFieldKosong($kode_siswa, KEYAPP::$MASUKAN_KODE_SISWA);

        $nama_siswa = addslashes($_POST['nama_siswa']);
        Generic::checkFieldKosong($nama_siswa, KEYAPP::$NAMA_MURID_KOSONG);

        $tgl_lahir_siswa = addslashes($_POST['tgl_lahir_siswa']);
        Generic::checkFieldKosong($tgl_lahir_siswa, KEYAPP::$BIRTHDAY_MURID_KOSONG);

        $objParent = new ParentSempoa();
        $objParent->getByID($parent_id);

        if (is_null($objParent->parent_id)) {
            Generic::checkFieldKosong($parent_id, KEYAPP::$PARENT_ID_KOSONG);
        }

        $sempoaMurid = new MuridModel();
        $sempoaMurid->getWhereOne("kode_siswa = '$kode_siswa'");

        if (is_null($sempoaMurid->id_murid)) {
            Generic::errorMsg(KEYAPP::$KODE_SISWA_NOT_FOUND);
        }
        if ($sempoaMurid->murid_parent_id != 0) {
            Generic::errorMsg(KEYAPP::$MURID_SDH_PUNYA_PARENT);
        }

        // mulai isi data ortu
        if ($objParent->parent_kode_anak == "") {
            $objParent->parent_kode_anak = $kode_siswa;
        } else {
            $objParent->parent_kode_anak = $objParent->parent_kode_anak . "," . $kode_siswa;
        }
        $objParent->save(1);
        $updated = false;

        if ($nama_siswa != $sempoaMurid->nama_siswa) {
            $updated = true;
        }

        if ($tgl_lahir_siswa != $sempoaMurid->tanggal_lahir) {
            $updated = true;
        }

        if ($updated) {
            $sempoaMurid->setFieldMurid($sempoaMurid->id_murid, "nama_siswa", $nama_siswa);
            $sempoaMurid->setFieldMurid($sempoaMurid->id_murid, "tanggal_lahir", $tgl_lahir_siswa);
        }

        $sempoaMurid->setFieldMurid($sempoaMurid->id_murid, "murid_parent_id", $parent_id);

        // Kirim Email ke ortu Password anak
        $mail = new Leapmail2();
        $subject = "Password anak Anda: " . $sempoaMurid->nama_siswa;
        $pwd = Generic::generatePassword(6);
        $content = "Password anak Anda:" . $sempoaMurid->nama_siswa . " " . $pwd;

        $sendTo = $objParent->parent_email;
        $mail->sendHTMLEmail($sendTo, $subject, "", $content);
        $sempoaMurid->setFieldMurid($sempoaMurid->id_murid, "murid_app_pwd", $pwd);
        $sempoaMurid->setFieldMurid($sempoaMurid->id_murid, "murid_created_date", leap_mysqldate());
        $sempoaMurid->setFieldMurid($sempoaMurid->id_murid, "murid_updated", leap_mysqldate());
        $sempoaMurid->setFieldMurid($sempoaMurid->id_murid, "murid_active", 1);
        $json['status_code'] = 1;
        $json['status_message'] = KEYAPP::$DATA_ANAK_BERHASIL_DISIMPAN;
        echo json_encode($json);
        die();

    }


// Account Setting

    public function getParentProfil()
    {
        $parent_fullname = addslashes($_POST['parent_fullname']);

    }


    // Topup

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


    public function topUpParent()
    {
        if (Efiwebsetting::getData('checkOAuth') == 'yes')
            IMBAuth::checkOAuth();

        $parent_id = addslashes($_POST['parent_id']);
        Generic::checkFieldKosong($parent_id, KEYAPP::$PARENT_ID_KOSONG);
        $kode_siswa = addslashes($_POST['kode_siswa']);
        Generic::checkFieldKosong($kode_siswa, KEYAPP::$KODE_SISWA_KOSONG);


        // cek apakah parent ini, ortu dr anak
        $jumlah_yg_dibeli = addslashes($_POST['jumlah_yg_di_beli']);
        Generic::checkFieldKosong($jumlah_yg_dibeli, KEYAPP::$JUMLAH_DIBELI_KOSONG);

        $cara_pembayaran = addslashes($_POST['cara_pembayaran']);
        Generic::checkFieldKosong($cara_pembayaran, KEYAPP::$CARA_PEMBAYARAN_KOSONG);

        // check anaknya orang tua
        $objParent = new ParentSempoa();
        $objParent->getByID($parent_id);
//        pr($objParent);
        if(is_null($objParent->parent_id)) {
            Generic::errorMsg(KEYAPP::$PARENT_ID_KOSONG);
        }
        else{
            $arrKodeMurid = explode(",",$objParent->parent_kode_anak);
            if(!in_array($kode_siswa,$arrKodeMurid)){
                Generic::errorMsg("Kode Anak salah!");
            }
        }

        $topUp = new SempoaTopup();
        $topUp->topup_created_date = leap_mysqldate();
        $topUp->topup_kodesiswa = $kode_siswa;
        $topUp->topup_parent_id = $parent_id;
        $topUp->topup_carapembayaran = $cara_pembayaran;
        $topUp->topup_jumlah = $jumlah_yg_dibeli;
        $topUp->topup_status = KEYAPP::$STATUS_TOP_UP_PENDING;
        $topUp->topup_pending_date = leap_mysqldate();

        $topUp->topup_changed_status_by = $objParent->parent_fullname;
        $topUp->save();
        $json = array();
        $json['status_code'] = 1;
        $json['status_message'] = KEYAPP::$TOP_UP_MSG;
        echo json_encode($json);
        die();

    }

    public function topUpHistory()
    {
        if (Efiwebsetting::getData('checkOAuth') == 'yes')
            IMBAuth::checkOAuth();

        $parent_id = addslashes($_POST['parent_id']);
        Generic::checkFieldKosong($parent_id, KEYAPP::$PARENT_ID_KOSONG);

        $topUp = new SempoaTopup();
        $arrMyTopUP = $topUp->getParentTopUp($parent_id);

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

    // Notification

    public function getNotificationByID()
    {
        if (Efiwebsetting::getData('checkOAuth') == 'yes')
            IMBAuth::checkOAuth();

        $parent_id = addslashes($_POST['parent_id']);
        Generic::checkFieldKosong($parent_id, KEYAPP::$PARENT_ID_KOSONG);

        $objNotif = new SempoaNotification();

        $arrNotif = $objNotif->getMyNotif($parent_id);
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
        $parent_id = addslashes($_POST['parent_id']);
        Generic::checkFieldKosong($parent_id, KEYAPP::$PARENT_ID_KOSONG);
        $notif_id = addslashes($_POST['notif_id']);
        Generic::checkFieldKosong($notif_id, "Notif ID kosong");

        $objNotif = new SempoaNotification();

        $objNotif->getWhereOne("notification_belongs_id='$parent_id' AND notification_id='$notif_id'");
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

    // Setting
    public function changeParentName()
    {
        $parent_id = addslashes($_POST['parent_id']);
        $parent_fullname = addslashes($_POST['parent_fullname']);
        Generic::checkFieldKosong($parent_id, KEYAPP::$PARENT_ID_KOSONG);
        Generic::checkFieldKosong($parent_fullname, KEYAPP::$PARENT_ID_KOSONG);
        // cek username
        $parent_newname = addslashes($_POST['parent_newname']);
        $parent_pwd = addslashes($_POST['parent_pwd']);
        Generic::checkFieldKosong($parent_newname, KEYAPP::$MASUKAN_NAMA_PARENT);
        Generic::checkFieldKosong($parent_pwd, KEYAPP::$MASUKAN_PASSWORD_PARENT);

        // Cek nama lama dan pwd
        $objParent = new ParentSempoa();
        $objParent->getWhereOne("parent_id='$parent_id' AND parent_fullname='$parent_fullname' AND parent_pwd='$parent_pwd'");
        if (is_null($objParent->parent_id)) {
            Generic::errorMsg(KEYAPP::$PASSWORD_SALAH);
        } else {
            $objParent->setFieldParent($objParent->parent_id, "parent_fullname", $parent_newname);
            $objParent->setLastUpdate($objParent->parent_id);
            if ($objParent->save(1)) {
                $json = array();
                $json['status_code'] = 1;
                $json['status_message'] = KEYAPP::$PARENT_GANTI_NAMA_SUKSES;
                echo json_encode($json);
                die();
            }
        }

    }

    public function changeParentEmail()
    {
        $parent_id = addslashes($_POST['parent_id']);
        $parent_email = addslashes($_POST['parent_email']);
        Generic::checkFieldKosong($parent_id, KEYAPP::$PARENT_ID_KOSONG);
        Generic::checkFieldKosong($parent_email, KEYAPP::$PARENT_ID_KOSONG);
        if (!Generic::isEmailValid($parent_email)) {
            Generic::errorMsg("Email tidak valid");
        }
        // cek username
        $parent_new_email = addslashes($_POST['parent_new_email']);
        if (!Generic::isEmailValid($parent_new_email)) {
            Generic::errorMsg("Email tidak valid");
        }
        $parent_pwd = addslashes($_POST['parent_pwd']);
        Generic::checkFieldKosong($parent_new_email, KEYAPP::$MASUKAN_EMAIL_PARENT);
        Generic::checkFieldKosong($parent_pwd, KEYAPP::$MASUKAN_PASSWORD_PARENT);

        // Cek nama lama dan pwd
        $objParent = new ParentSempoa();
        $objParent->getWhereOne("parent_id='$parent_id' AND parent_email='$parent_email' AND parent_pwd='$parent_pwd'");
        if (is_null($objParent->parent_id)) {
            Generic::errorMsg(KEYAPP::$PASSWORD_SALAH);
        } else {
            $objParent->setFieldParent($objParent->parent_id, "parent_email", $parent_new_email);
            $objParent->setLastUpdate($objParent->parent_id);
            if ($objParent->save(1)) {
                $json = array();
                $json['status_code'] = 1;
                $json['status_message'] = KEYAPP::$PARENT_GANTI_EMAIL_SUKSES;
                echo json_encode($json);
                die();
            }
        }

    }

    public function changeParentPhoneNr()
    {
        $parent_id = addslashes($_POST['parent_id']);
        $parent_hp_nr = addslashes($_POST['parent_hp_nr']);
//        Generic::checkFieldKosong($parent_id, KEYAPP::$PARENT_ID_KOSONG);
        Generic::checkFieldKosong($parent_hp_nr, KEYAPP::$PARENT_ID_KOSONG);
        // cek username
        $parent_new_hp_nr = addslashes($_POST['parent_new_hp_nr']);
        $parent_pwd = addslashes($_POST['parent_pwd']);
        Generic::checkFieldKosong($parent_new_hp_nr, KEYAPP::$MASUKAN_NR_HP_PARENT);
        Generic::checkFieldKosong($parent_pwd, KEYAPP::$MASUKAN_PASSWORD_PARENT);

        // Cek nama lama dan pwd
        $objParent = new ParentSempoa();
        $objParent->getWhereOne("parent_id='$parent_id' AND parent_hp_nr='$parent_hp_nr' AND parent_pwd='$parent_pwd'");
        if (is_null($objParent->parent_id)) {
            Generic::errorMsg(KEYAPP::$PASSWORD_SALAH);
        } else {
            $objParent->setFieldParent($objParent->parent_id, "parent_hp_nr", $parent_new_hp_nr);
            $objParent->setLastUpdate($objParent->parent_id);
            if ($objParent->save(1)) {
                $json = array();
                $json['status_code'] = 1;
                $json['status_message'] = KEYAPP::$PARENT_GANTI_HP_SUKSES;
                echo json_encode($json);
                die();
            }
        }

    }

    public function changeParentPassword()
    {
        $parent_id = addslashes($_POST['parent_id']);
        Generic::checkFieldKosong($parent_id, KEYAPP::$PARENT_ID_KOSONG);
        // cek username
        $parent_new_pwd = addslashes($_POST['parent_new_pwd']);
        $parent_pwd = addslashes($_POST['parent_pwd']);
        Generic::checkFieldKosong($parent_new_pwd, KEYAPP::$MASUKAN_NEW_PASSWORD_PARENT);
        Generic::checkFieldKosong($parent_pwd, KEYAPP::$MASUKAN_PASSWORD_PARENT);

        // Cek nama lama dan pwd
        $objParent = new ParentSempoa();
        $objParent->getWhereOne("parent_id='$parent_id' AND parent_pwd='$parent_pwd'");
        if (is_null($objParent->parent_id)) {
            Generic::errorMsg(KEYAPP::$PASSWORD_SALAH);
        } else {
            $objParent->setFieldParent($objParent->parent_id, "parent_pwd", $parent_new_pwd);
            $objParent->setLastUpdate($objParent->parent_id);
            if ($objParent->save(1)) {
                $json = array();
                $json['status_code'] = 1;
                $json['status_message'] = KEYAPP::$PARENT_GANTI_PASSWORD_SUKSES;
                echo json_encode($json);
                die();
            }
        }

    }


    public function getMyChildTCInfo()
    {
        if (Efiwebsetting::getData('checkOAuth') == 'yes')
            IMBAuth::checkOAuth();
        $parent_id = addslashes($_POST['parent_id']);
        Generic::checkFieldKosong($parent_id, KEYAPP::$PARENT_ID_KOSONG);

        $objParent = new ParentSempoa();
        $objParent->getByID($parent_id);
        if ($objParent->parent_kode_anak != "") {
            $arrKode_siswa = explode(",", $objParent->parent_kode_anak);
            $arrDataTC = array();
            foreach ($arrKode_siswa as $kode_siswa) {
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
            }

            $json['status_code'] = 1;
            $json['result'] = $jsonHelp;
            $json['status_message'] = "";
            echo json_encode($json);
            die();
        }
        $json['status_code'] = 0;
        $json['status_message'] = "Belum mempunyai anak!";
        echo json_encode($json);
        die();
    }

    public function getMyChildIBOInfo()
    {
        if (Efiwebsetting::getData('checkOAuth') == 'yes')
            IMBAuth::checkOAuth();
        $parent_id = addslashes($_POST['parent_id']);
        Generic::checkFieldKosong($parent_id, KEYAPP::$PARENT_ID_KOSONG);

        $objParent = new ParentSempoa();
        $objParent->getByID($parent_id);
        if ($objParent->parent_kode_anak != "") {
            $arrKode_siswa = explode(",", $objParent->parent_kode_anak);
            $arrDataTC = array();
            foreach ($arrKode_siswa as $kode_siswa) {
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
            }

            $json['status_code'] = 1;
            $json['result'] = $jsonHelp;
            $json['status_message'] = "";
            echo json_encode($json);
            die();
        }
        $json['status_code'] = 0;
        $json['status_message'] = KEYAPP::$SUCCESS;
        echo json_encode($json);
        die();
    }

    public function getMyChildName()
    {
        if (Efiwebsetting::getData('checkOAuth') == 'yes')
            IMBAuth::checkOAuth();
        $parent_id = addslashes($_POST['parent_id']);
        Generic::checkFieldKosong($parent_id, KEYAPP::$PARENT_ID_KOSONG);

        $objParent = new ParentSempoa();
        $objParent->getWhereOne("parent_id='$parent_id'");
        $arrKodeMurid = explode(",", $objParent->parent_kode_anak);
        $arrNamaAnak = array();
        foreach ($arrKodeMurid as $kode_siswa) {
            $objMurid = new MuridModel();
            $objMurid->getWhereOne("kode_siswa='$kode_siswa'");
            if (is_null($objMurid->id_murid)) {
                $arrNamaAnak[] = "";
            } else {
                $arrNamaAnak[] = $objMurid->nama_siswa;
            }

        }
        $json = array();
        $json['status_code'] = 1;
        $json['result'] = $arrNamaAnak;
        $json['status_message'] = KEYAPP::$SUCCESS;
        echo json_encode($json);
        die();
    }

    public function dataLogin()
    {

        if (Efiwebsetting::getData('checkOAuth') == 'yes')
            IMBAuth::checkOAuth();

        $parent_id = addslashes($_POST['parent_id']);

        if ($parent_id == "") {
            Generic::errorMsg("id harus diisi");
        }

        $json = array();
        $objParent = new ParentSempoa();
        $objParent->getWhereOne("parent_id='$parent_id'");
        if (is_null($objParent->parent_id)) {
            Generic::errorMsg("!");
        }

        $objParent->setLastLogin($objParent->parent_id);
        $arrWS = explode(",", $objParent->crud_webservice_allowed);

        $arrHlp = array();
        foreach ($arrWS as $val) {
            $arrHlp[$val] = $objParent->$val;
        }

        $json['status_code'] = 1;
        $json['result'] = $arrHlp;
        $json['status_message'] = "Berhasil!";
        echo json_encode($json);
        die();
    }
}