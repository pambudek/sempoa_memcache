<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Generic
 *
 * @author efindiongso
 */
class Generic
{

    public static function getStatus()
    {
        $arrSTatus = array("<b>Unpaid</b>", "Paid");
        return $arrSTatus;
    }

    public static function getStatusTrainer()
    {
        $arrSTatus = array("<b>Unpaid</b>", "Paid", "<b>Discount 100%</b>");
        return $arrSTatus;
    }

    public static function getKeteranganTraining()
    {
        $arrSTatus = array("<i>Select</i>", "<b>Lulus</b>", "<b>Tidak Lulus</b>", "<b>Sakit</b>", "<b>Absen</b>");
        return $arrSTatus;
    }

    public static function firstPaymentJunior()
    {
        $arrIDs = array(1, 4, 7, 2);
        return $arrIDs;
    }

    public static function firstPaymentJunior2()
    {
        $arrIDs = array(1, 4, 7, 11);
        return $arrIDs;
    }

    public static function firstPaymentFoundation()
    {
        $arrIDs = array(1, 4, 8, 11);
        return $arrIDs;
    }

    public static function getPerlengkapanLevel()
    {
        $objBarang = new BarangWebModel();
        $arrPerlengkapan = $objBarang->getWhere("jenis_biaya=2");
        $arr = array();
        foreach ($arrPerlengkapan as $val) {
            $arr[$val->id_barang_harga] = $val->nama_barang;
        }
        return $arr;
    }

    public static function getJenisPembayaran()
    {
        $objPembayaran = new PembayaranWeb2Model();
        $arr = $objPembayaran->getAll();
        foreach ($arr as $pb) {
            $arrPembayaran[$pb->id_jenis_pembayaran] = $pb->jenis_pembayaran;
        }

        return $arrPembayaran;
    }

    public static function getJenisBiaya()
    {
//        $arrBiaya = array("Barang", "Buku", "Perlengkapan");
        $arrBiaya[KEY::$JENIS_BARANG] = KEY::$JENIS_BARANG_TEXT;
        $arrBiaya[KEY::$JENIS_BUKU] = KEY::$JENIS_BUKU_TEXT;
        $arrBiaya[KEY::$JENIS_PERLENGKAPAN] = KEY::$JENIS_PERLENGKAPAN_TEXT;
        return $arrBiaya;
    }

    public static function getChildren($parent_id)
    {
        $obj = new SempoaOrg();
        $arr = $obj->getWhere("org_parent_id='$parent_id'");
        return $arr;
    }

    public static function getMyOrg($orgid)
    {
        $obj = new SempoaOrg();
        $arr = $obj->getWhere("org_id='$orgid'");
        return $arr;
    }

    public static function getMyParentID($myOrgID)
    {
        $obj = new SempoaOrg();
        $arr = $obj->getWhere("org_id='$myOrgID'");
        if (count($arr) > 0) {
            return $arr[0]->org_parent_id;
        } else
            return "";
    }

    public static function getAllAK()
    {
        $ak = new SempoaOrg();
        $arrAK = $ak->getWhere("org_type='ak'");
        $allAK = array();
        foreach ($arrAK as $val) {
            $allAK[$val->org_id] = $val->nama;
        }
        return $allAK;
    }

    public static function getAllMyKPO($ak_id)
    {
        $obj = new SempoaOrg();
        $arr = $obj->getWhere("org_type='kpo' AND org_parent_id = '$ak_id'");
        if (count($arr) > 0) {
            foreach ($arr as $val) {
                $arrKPO[$val->org_id] = $val->nama;
            }
        }

        return $arrKPO;
    }

    public static function getAllIBO()
    {
        $ibo = new SempoaOrg();
        $arrIbo = $ibo->getWhere("org_type='ibo'");
        $allIbo = array();
        foreach ($arrIbo as $val) {
            $allIbo[$val->org_id] = $val->nama;
        }
        return $allIbo;
    }

    public static function getAllMyIBO($KPOid)
    {
        $obj = new SempoaOrg();
        $arr = $obj->getWhere("org_type='ibo' AND org_parent_id = '$KPOid'");
        if (count($arr) > 0) {
            foreach ($arr as $val) {
                $arrIBO[$val->org_id] = $val->nama;
            }
        }

        return $arrIBO;
    }

    public static function getAllMyTC($IBOid)
    {
        $obj = new SempoaOrg();
//        pr($IBOid);
        $arr = $obj->getWhere("org_type='tc' AND org_parent_id='$IBOid' ORDER BY nama ASC");
//        pr($arr);
        if (count($arr) > 0) {
            foreach ($arr as $val) {
                $arrTC[$val->org_id] = $val->nama;
            }
        }

        return $arrTC;
    }

    public static function getGroup($id, $all = '0')
    {
        $obj = new GroupsModel();
        $arr = $obj->getWhere("parent_id='$id'");
        if (count($arr) > 0) {
            foreach ($arr as $val) {
                $arrGroup[$val->id_group] = $val->nama;
            }
        }

        return $arrGroup;
    }

    public static function fgetAllGroupMember($id_group)
    {
        $obj = new GroupsModel();
        $arr = $obj->getWhere("id_group='$id_group'");

        if (count($arr) > 0) {
            foreach ($arr as $val) {
                if ($res == "") {
                    $res = $val->groups;
                } else
                    $res = $res . "," . $val->groups;
            }
            return $res;
        }
    }

    public static function getOrgNamebyID($id)
    {
        $obj = new SempoaOrg();
        $obj->getByID($id);
        return $obj->nama;
    }

    public static function getMyGroupID($myOrgID)
    {
        $ibo_id = Generic::getMyParentID($myOrgID);
        $arrGroup = self::getGroup($ibo_id);
        foreach ($arrGroup as $key => $val) {
            $arrTC = self::fgetAllGroupMember($key);
//            pr($arrTC);
            $arrTCHlp = explode(",", "$arrTC");

            if (in_array($myOrgID, $arrTCHlp)) {
                return $key;
            }
        }
    }

    public static function fgetGroupMember($id_group)
    {
        $obj = new GroupsModel();
        $arr = $obj->getWhere("id_group='$id_group'");

        if (count($arr) > 0) {
            foreach ($arr as $val) {
                $arrGroup[$val->id_group] = $val->groups;
            }
            return $arrGroup;
        }
        return "";
    }

    public function getSession()
    {
        pr($_SESSION);
    }

    //put your code here
    public static function getClassSetting()
    {
        $classSetting = new SettingWeb5Model();
        $arr = $classSetting->getAll();
        foreach ($arr as $val) {
            $arrIsi[$val->id_sk] = $val->sk_name;
        }
        return $arrIsi;
    }

    public static function getClassSettingByKPOID($kpo_id)
    {
        $classSetting = new SettingWeb5Model();
        $arr = $classSetting->getWhere("kpo_id='$kpo_id'");
        foreach ($arr as $val) {
            $arrIsi[$val->id_level_murid] = $val->level_murid;
        }
        return $arrIsi;
    }

    /*
     * @parameter, $tc_id
     * @return semua guru dari current tc_id
     */

    public static function getAllGuruByTcID($tc_id)
    {
        $objGuru = new SempoaGuruModel();
        $arrObjGuru = $objGuru->getWhere("guru_tc_id='$tc_id'");
        self::checkCountArray($arrObjGuru);
        return $arrObjGuru;
    }

    /*
     * Menghitung tc_id yang pertama
     * @parameter, $iboid
     * @return, id dari tc pertama
     */

    public function getFirstTCID($IBOid)
    {
        $arrFirstTC = self::getAllMyTC($IBOid);
        self::checkCountArray($arrFirstTC);
        return key($arrFirstTC);
    }

    public static function checkCountArray($arr)
    {
        if (count($arr) == 0) {
            die("Array is null");
        }
    }

    public static function getAllLevelTraining()
    {
        $objLevelTraining = new TrainerWebModel();
        $arrLevelTraining = $objLevelTraining->getAll();
        Generic::checkCountArray($arrLevelTraining);

        foreach ($arrLevelTraining as $val) {
            $arrLevel[$val->id_level_trainer] = $val->level_trainer;
        }
        return $arrLevel;
    }

    public static function getAllLevelTrainingByKPOId($kpo_id)
    {
        $objLevelTraining = new GuruWebModel();
        $arrLevelTraining = $objLevelTraining->getWhere("kpo_id='$kpo_id'");
        foreach ($arrLevelTraining as $val) {
            $arrLevel[$val->id_level_guru] = $val->level_guru;
        }
        return $arrLevel;
    }

    public static function getAllStatusGuru()
    {
        $objStatus = new GuruWeb4Model();
        $arrStatus = $objStatus->getAll();
        Generic::checkCountArray($arrStatus);

        foreach ($arrStatus as $val) {
            $arrStatusErg[$val->id_status_guru] = $val->status_guru;
        }
        return $arrStatusErg;
    }

    public static function getAllStatusGuruByKPOId($kpo_id)
    {
        $objStatus = new GuruWeb4Model();
        $arrStatus = $objStatus->getWhere("kpo_id='$kpo_id'");
        Generic::checkCountArray($arrStatus);

        foreach ($arrStatus as $val) {
            $arrStatusErg[$val->id_status_guru] = $val->status_guru;
        }
        return $arrStatusErg;
    }

    public static function getAllStatusMurid()
    {

        $objStatus = new MuridWeb2Model();
        $arrStatus = $objStatus->getAll();
        Generic::checkCountArray($arrStatus);

        foreach ($arrStatus as $val) {
            $arrStatusErg[$val->id_status_murid] = $val->status;
        }
        return $arrStatusErg;
    }

    public static function getAllStatusMuridByKPO($kpo_id)
    {

        $objStatus = new MuridWeb2Model();
        $arrStatus = $objStatus->getWhere("kpo_id='$kpo_id'");

//        Generic::checkCountArra/y($arrStatus);

        foreach ($arrStatus as $val) {
            $arrStatusErg[$val->id_status_murid] = $val->status;
        }
        return $arrStatusErg;
    }

    public static function getAllAgama()
    {
        $arrAgama[''] = "";
        $arrAgama['b'] = "Buddha";
        $arrAgama['h'] = "Hindu";
        $arrAgama['i'] = "Islam";
        $arrAgama['kh'] = "Khatolik";
        $arrAgama['kr'] = "Kristen";
        return $arrAgama;
    }

    public static function getArrValueByIndex($index, $arr)
    {
        foreach ($arr as $key => $val) {
            if ($index == $key) {
                return $val;
            }
        }
    }

    public static function getWeekDay()
    {
        $arrWeekday[1] = "Monday";
        $arrWeekday[2] = "Tuesday";
        $arrWeekday[3] = "Wednesday";
        $arrWeekday[4] = "Thursday";
        $arrWeekday[5] = "Friday";
        $arrWeekday[6] = "Saturday";
        $arrWeekday[0] = "Sunday";
        return $arrWeekday;
    }

    public static function WeekMap()
    {
        $dowMap = array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
        return $dowMap;
    }

    /*
     * 
     */

    public static function getTypeGroup()
    {
        $arrType[0] = KEY::$AK;
        $arrType[1] = KEY::$KPO;
        $arrType[2] = KEY::$IBO;
        $arrType[3] = KEY::$TC;

        return $arrType;
    }

    public static function getTypeGroupinInt($arrGroup, $search)
    {
        foreach ($arrGroup as $key => $val) {
//            pr($val);
            if ($val == $search) {
//                pr($val . " " . $search . " " . $key);
                return $key;
            }
        }
        return "";
    }

    /*
     * 
     */

    public static function getRuanganByTC($tc_id)
    {
        $objRuangan = new RuanganModel();
        $arrObjRuangan = $objRuangan->getWhere("tc_id = '$tc_id'");
        Generic::checkCountArray($arrObjRuangan);
        foreach ($arrObjRuangan as $val) {
            $arrRuangan[$val->id_ruangan] = $val->nama_ruangan;
        }
        return $arrRuangan;
    }

    /*
     * 
     */

    public static function isCurrentAccountAK()
    {
        if (AccessRight::getMyOrgType() == KEY::$AK) {
            return true;
        } else {
            return false;
        }
    }

    public static function isCurrentAccountKPO()
    {
        if (AccessRight::getMyOrgType() == KEY::$KPO) {
            return true;
        } else {
            return false;
        }
    }

    public static function isCurrentAccountIBO()
    {
        if (AccessRight::getMyOrgType() == KEY::$IBO) {
            return true;
        } else {
            return false;
        }
    }

    public static function isCurrentAccountTC()
    {
        if (AccessRight::getMyOrgType() == KEY::$TC) {
            return true;
        } else {
            return false;
        }
    }

    public static function fCreateKode($who, $org_id)
    {
        $year = strval(date("Y"));
        $year = substr($year, 2);
        $org_code = self::getOrgCode($org_id);

        $kode_tahun = $year;
        $kode_org = "";
        $kode_urutan = "";
        if ($who == "Murid") {
            $last_code = self::getLastKodeSiswa($org_id);
            if ($last_code == "") {
                $last_code = $kode_tahun . strval($org_code) . "001";
            } else {
                if (substr($last_code, 0, -8) != $year) {
                    $last_code = $kode_tahun . strval($org_code) . "001";
                } else {
                    $last_code = $last_code + 1;
                }

            }
        } elseif ($who == "Guru") {
            $last_code = self::getLastKodeGuru($org_id);
            if ($last_code == "") {
                $last_code = $kode_tahun . strval($org_code) . "01";
            } else {
                if (substr($last_code, 0, -7) != $year) {
                    $last_code = $kode_tahun . strval($org_code) . "01";
                } else {
                    $last_code = $last_code + 1;
                }

            }
        } elseif ($who == KEY::$TRAINER) {
            $last_code = self::getLastKodeTrainer($org_id);
            if ($last_code == "") {
                $last_code = $kode_tahun . strval($org_code) . "01";
            } else {
                if (substr($last_code, 0, -4) != $year) {
                    $last_code = $kode_tahun . strval($org_code) . "01";
                } else {
                    $last_code = $last_code + 1;
                }
            }
        }

        return $last_code;
    }

    public static function fCreateKodeSiswa()
    {
        $year = strval(date("Y"));
        $year = substr($year, 2);
        $myTCNo = AccessRight::getMyOrgID();
        $myIBONo = self::getMyParentID($myTCNo);
        $myTC = AccessRight::getMyOrgType();

        $TC = new SempoaOrg();
        $TC->getByID($myTCNo);
        $TCID = ($TC->org_kode);

//        echo $year  . $myIBONo . $myTCNo;
        $kode_siswa = self::getLastKodeSiswa($myTCNo);
        if ($kode_siswa == "") {
//            $kode_siswa = $year . strval($myIBONo) . strval($myTCNo) . "001";
            $kode_siswa = $year . strval($TCID) . "001";
        } else {
            if (substr($kode_siswa, 0, -8) != $year) {
                $kode_siswa = $year . strval($TCID) . "001";
            } else {
                $kode_siswa = $kode_siswa + 1;
            }

        }
        return $kode_siswa;
    }

    public static function getOrgCode($id)
    {
        $org = new SempoaOrg();
        $org->getByID($id);
        return strval($org->org_kode);
    }

    public static function getLastKodeSiswa($tc_id)
    {
        $objMurid = new MuridModel();
        $objMurid->getWhereOne("murid_tc_id='$tc_id'  AND kode_siswa != '' ORDER BY id_murid DESC");

        if(is_null($objMurid->id_murid)){
            return "";
        }
        else{
            return $objMurid->kode_siswa;
        }

        global $db;
        $q = "SELECT * FROM {$objMurid->table_name} WHERE murid_tc_id='$tc_id' ORDER BY id_murid DESC Limit 1";
        $arrMurid = $db->query($q, 2);
        if (count($arrMurid) == 0) {
            return "";
        } else {
            foreach ($arrMurid as $val) {
                return $val->kode_siswa;
            }
        }
    }

    public static function getLastKodeGuru($tc_id)
    {
        $objGuru = new SempoaGuruModel();
        global $db;
        $q = "SELECT * FROM {$objGuru->table_name} WHERE guru_tc_id='$tc_id' ORDER BY guru_id DESC Limit 1";

        $arrGuru = $db->query($q, 2);
        if (count($arrGuru) == 0) {
            return "";
        } else {
            foreach ($arrGuru as $val) {
                return $val->kode_guru;
            }
        }
    }

    public static function getLastKodeTrainer($ibo_id)
    {
        $objTrainer = new TrainerModel();
        global $db;
        $q = "SELECT * FROM {$objTrainer->table_name} WHERE tr_ibo_id='$ibo_id' ORDER BY id_trainer DESC Limit 1";

        $arrTrainer = $db->query($q, 2);
        if (count($arrTrainer) == 0) {
            return "";
        } else {
            foreach ($arrTrainer as $val) {
                return $val->kode_trainer;
            }
        }
    }

    public static function getAllsMyIBO($id_KPO)
    {
        $objOrg = new SempoaOrg();
        $arrOrg = $objOrg->getWhere("org_parent_id=$id_KPO AND org_type='ibo'");
        Generic::checkCountArray($arrOrg);

        foreach ($arrOrg as $val) {
            $arrAllIBO[$val->org_id] = $val->nama;
        }
        return $arrAllIBO;
    }

    public static function getAllsMyTC($id_IBO)
    {
        $objOrg = new SempoaOrg();
        $arrOrg = $objOrg->getWhere("org_parent_id=$id_IBO AND org_type='tc'");
        Generic::checkCountArray($arrOrg);

        foreach ($arrOrg as $val) {
            $arrAllTC[$val->org_id] = $val->nama;
        }
        return $arrAllTC;
    }

    public static function getAllBiaya()
    {
        $obj = new JenisPembayaranModel();
        return $obj->getAll();
    }

    public static function getAllJenisBiaya($kpo_id)
    {
        $obj = new SettingJenisBiayaModel();
        $arrObj = $obj->getWhere("kpo_id='$kpo_id'");
        $arrColumn = explode(",", $obj->coloumlist);
        $arrBiaya = array();
        foreach ($arrObj as $val) {
//            foreach($arrColumn as $value){
            $sem[$val->id_biaya] = $val->jenis_biaya;
//            }
//            $arrBiaya[] = $sem;
        }

        return $sem;
    }

    public static function getLastIDBiaya($kpo_id)
    {
        $obj = new SettingJenisBiayaModel();
        global $db;
        $q = "SELECT * FROM {$obj->table_name} WHERE kpo_id='$kpo_id' ORDER BY id_jenis_biaya DESC Limit 1";
        $arr = $db->query($q, 2);
//        pr($arr);
        if (count($arr) == 0) {
            return "1";
        } else {
            foreach ($arr as $val) {
                return ($val->id_biaya) + 1;
            }
        }
    }

    public static function getMyRoot()
    {
        $myOrgID = AccessRight::getMyOrgID();
        $myParentID = Generic::getMyParentID($myOrgID);
        $myGrandParentID = Generic::getMyParentID($myParentID);
        $myGrandGrandParentID = Generic::getMyParentID($myGrandParentID);
        pr('myOrgID: ' . $myOrgID);
        pr('myParentID: ' . $myParentID);
        pr('myGrandParentID: ' . $myGrandParentID);
        pr('myGrandGrandParentID: ' . $myGrandGrandParentID);
    }

    public static function getAllLevelMurid($id)
    {
        $obj = new SettingWeb5Model();
        $arrObj = $obj->getWhere("kpo_id='$id'");
        $arrColumn = explode(",", $obj->coloumlist);
        $arrBiaya = array();
        foreach ($arrObj as $val) {
            $sem[$val->id_level_murid] = $val->level_murid;
        }

        return $sem;
    }

    public static function getAllLevel()
    {
        $obj = new SempoaLevel();
        $arrObj = $obj->getAll();
        $arrColumn = explode(",", $obj->coloumlist);
        $arrBiaya = array();
        foreach ($arrObj as $val) {
            $sem[$val->id_level] = $val->level;
        }

        return $sem;
    }

    public static function getLastIDTable($pKey, $obj)
    {
        global $db;
        $q = "SELECT * FROM {$obj->table_name} ORDER BY $pKey DESC Limit 1";
        $arr = $db->query($q, 2);
        if (count($arr) == 0) {
            return "1";
        } else {
            foreach ($arr as $val) {
                return ($val->$pKey) + 1;
            }
        }
    }

    public static function fCheckMasterRow($refID, $jenis_biaya, $ak_id, $kpo_id, $ibo_id, $table)
    {

        global $db;
        $q = "SELECT * FROM $table WHERE (refID='$refID' AND refID != 0) AND jenis_biaya='$jenis_biaya' AND ak_id='$ak_id' AND kpo_id ='$kpo_id' AND ibo_id=$ibo_id";
//        pr($q);
        $arr = $db->query($q, 2);
//        pr($arr);
//        pr(count($arr));
        if (count($arr) > 0) {
//            echo "gefunden!";
            return 1;
        } else {
//            echo "nicht gefunden!";
            return 0;
        }
    }

    public static function fCheckMasterRowTC($refID, $jenis_biaya, $ak_id, $kpo_id, $ibo_id, $tc_id, $table)
    {

        global $db;
        $q = "SELECT * FROM $table WHERE (refID='$refID' AND refID != 0) AND jenis_biaya='$jenis_biaya' AND ak_id='$ak_id' AND kpo_id ='$kpo_id' AND ibo_id='$ibo_id' AND  tc_id='$tc_id'";
//        pr($q);
        $arr = $db->query($q, 2);
//        pr($arr);
//        pr(count($arr));
        if (count($arr) > 0) {
//            echo "gefunden!";
            return 1;
        } else {
//            echo "nicht gefunden!";
            return 0;
        }
    }

    public static function removeIBO($refID, $table)
    {
        global $db;
        $q = "SELECT * FROM $table WHERE (refID='$refID')";
        $arr = $db->query($q, 2);
        if (count($arr) > 0) {
            foreach ($arr as $val) {
                $res[$val->id_setting_biaya] = $val->ibo_id;
            }
            return $res;
        }
    }

    public static function removeTC($refID, $table)
    {
        global $db;
        $q = "SELECT * FROM $table WHERE (refID='$refID')";
        $arr = $db->query($q, 2);
        if (count($arr) > 0) {
            foreach ($arr as $val) {
                $res[$val->id_setting_biaya] = $val->tc_id;
            }
            return $res;
        }
    }

    public static function getAllmemberTC($refID, $table)
    {
        global $db;
        $q = "SELECT * FROM $table WHERE (refID='$refID')";
        $arr = $db->query($q, 2);
        if (count($arr) > 0) {
            foreach ($arr as $val) {
                $res[$val->id_setting_biaya] = $val->tc_id;
            }
            return $res;
        }
    }

    public static function getMasterSPP($kpoid, $iboid, $level)
    {
        $obj = new BiayaBulananModel();
        $arrObj = $obj->getWhere(" kpo_id = '$kpoid' AND ibo_id='$iboid' AND level='$level' AND type = 'MASTER'");

//        return $arrObj;=z
    }

    public static function getTextSettingBiaya($IDSetting)
    {
        $obj = new SettingJenisBiayaModel();

        $arr = $obj->getWhere("id_biaya='$IDSetting'");
        foreach ($arr as $key => $val) {
            return $val->jenis_biaya;
        }
    }

    public static function getTCMember($parent_id)
    {

        $arrTC = Generic::getAllsMyTC($parent_id);
        $a = "";
        $objGroupTC = new GroupsModel();
        $arrGroupTC = $objGroupTC->getWhere("parent_id='$parent_id'");
        foreach ($arrGroupTC as $val) {
            $arrVergebenTC[] = $val->groups;
        }
        $value = implode(",", array_keys($arrTC));
        $arrVal = explode(',', $value);
        foreach ($arrVergebenTC as $val) {
            if ($a == "") {
                $a = str_replace(" ", "", $val);
            } else {
                $a = $a . "," . str_replace(" ", "", $val);
            }
        }
        $arrhelp = explode(",", $a);
        $arrhelp = array_unique($arrhelp);
//        pr($arrhelp);
        $arr = array();
        foreach ($arrTC as $key => $val) {
            $bfind = false;
            foreach ($arrhelp as $valHelp) {
                if ($key == $valHelp) {
                    $bfind = true;
                }
            }
            if ($bfind == false) {
                $arr[$key] = $val;
            }
        }
        return $arr;
    }

    public static function getTCMemberRoy($parent_id, $id_group)
    {

        $arrTC = Generic::getAllsMyTC($parent_id);

        $objGroupTC = new GroupsModel();
        $arrGroupTC = $objGroupTC->getWhere("parent_id='$parent_id' AND id_group != '$id_group'");
        foreach ($arrGroupTC as $val) {


            $arrVergebenTC[] = $val->groups;
        }
        $a = "";
        $value = implode(",", array_keys($arrTC));
        $arrVal = explode(',', $value);
        foreach ($arrVergebenTC as $val) {
            if ($a == "") {
                $a = str_replace(" ", "", $val);
            } else {
                $a = $a . "," . str_replace(" ", "", $val);
            }
        }
        $arrhelp = explode(",", $a);
        $arrhelp = array_unique($arrhelp);
//        pr($arrhelp);
        $arr = array();
        foreach ($arrTC as $key => $val) {
            $bfind = false;
            foreach ($arrhelp as $valHelp) {
                if ($key == $valHelp) {
                    $bfind = true;
                }
            }
            if ($bfind == false) {
                $arr[$key] = $val;
            }
        }
        return $arr;
    }

    public static function getNamaBarangByKPOID($kpo_id)
    {
        $objBarang = new BarangWebModel();
        $arrBarang = $objBarang->getWhere("kpo_id='$kpo_id'");
        if (count($arrBarang) == 0) {
            return "";
        } else {
            foreach ($arrBarang as $val) {
                $barang[$val->id_barang_harga] = $val->nama_barang;
            }
        }
        return $barang;
    }

    public static function getQtyBarangByKPOID($kpo_id)
    {
        $objBarang = new StockModel();
        $arrBarang = $objBarang->getWhere("org_id='$kpo_id'");
        if (count($arrBarang) == 0) {
            return "";
        } else {
            foreach ($arrBarang as $val) {
                $barang[$val->id_barang] = $val->jumlah_stock;
            }
        }
//        pr($barang);
        return $barang;
    }

    public static function getNamaBarangByIDKPOID($id_barang, $kpo_id)
    {
        $objBarang = new BarangWebModel();
        $arrBarang = $objBarang->getWhere("kpo_id='$kpo_id' AND id_barang_harga='$id_barang'");
        if (count($arrBarang) == 0) {
            return "";
        } else {
            return $arrBarang[0]->nama_barang;
        }
    }

    public static function getStockByOrgID($id_barang, $org_id)
    {
        $objStock = new StockModel();
        $arrStock = $objStock->getWhere("org_id='$org_id' AND id_barang='$id_barang'");
        if (count($arrStock) == 0) {
            return "";
        } else {
            return $arrStock[0]->jumlah_stock;
        }
    }


    public static function getNamaBarangByIDBarang($id_barang)
    {
        $objBarang = new BarangWebModel();
        $arrBarang = $objBarang->getWhere("id_barang_harga='$id_barang'");
        if (count($arrBarang) == 0) {
            return "";
        } else {
            return $arrBarang[0]->nama_barang;
        }
    }

    public static function getNamaBarang()
    {
        $objBarang = new BarangWebModel();
        $arrBarang = $objBarang->getAll();
        $namaBarang = array();
        foreach ($arrBarang as $val) {
            $namaBarang[$val->id_barang_harga] = $val->nama_barang;
        }

        return $namaBarang;
    }

    public static function getJumlahStockByID($id_barang, $kpo_id)
    {
        $objStok = new StockModel();
        $arrStock = $objStok->getWhere("id_barang = '$id_barang' AND org_id='$kpo_id'");
        return $arrStock;
    }

    public static function getHargaBarang($id_barang, $atasan_id)
    {
        $hargaBarang = new SettingHargaBarang();
        $key = $atasan_id . "_" . $id_barang;
        $arrHargaBarang = $hargaBarang->getWhere("id_setting_biaya='$key'");
//        pr($arrHargaBarang);
        if (count($arrHargaBarang) == 0) {
            return 0;
        } else {
            return $arrHargaBarang[0]->harga;
        }
    }

    public static function getHargaBarangByGroup($id_barang, $group_id)
    {
        $hargaBarang = new SettingHargaBarang();
        $key = $group_id . "_" . $id_barang;
//        pr($key);
        $arrHargaBarang = $hargaBarang->getWhere("id_setting_biaya='$key'");
//        pr($arrHargaBarang);
        if (count($arrHargaBarang) == 0) {
            return 0;
        } else {
            return $arrHargaBarang[0]->harga;
        }
    }

    public static function number($number)
    {
        return number_format($number, 0, '', '.');
    }

    public static function getMyStock($myID)
    {
        $objStock = new StockModel();
        $arrStock = $objStock->getWhere("org_id='$myID'");
        return $arrStock;
    }

    public static function getAdminNameByID($id)
    {
        $acc = new SempoaAccount();
        $name = $acc->getWhere("admin_org_id='$id'");
        return $name[0]->admin_nama_depan;
    }

    public static function getAdminUsernameByID($id)
    {
        $acc = new SempoaAccount();
        $acc->getWhereOne("admin_id='$id'");
        return $acc->admin_username;
    }


    public static function getTCNamebyID($tc_id)
    {
        $objTC = new SempoaOrg();
        $objTC->getByID($tc_id);
        return $objTC->nama;
    }


    public static function getOrgIDByName($orgName)
    {
        $org = new SempoaOrg();
        $org->getWhereOne("nama='$orgName'");
        return $org->org_id;
    }

    public static function getIBONamebyID($ibo_id)
    {
        $objTC = new SempoaOrg();
        $objTC->getByID($ibo_id);
        return $objTC->nama;
    }

    public static function getLevelNameByID($id)
    {

        if ($id == "" || $id == 0) return "<i>Not Set</i>";
        $obj = new SempoaLevel();
        $obj->getByID($id);
        return $obj->level;
    }

    public static function printerKelas($kelas_id)
    {

        $kelas = new KelasWebModel();
        $kelas->getByID($kelas_id);

        $level = new SempoaLevel();
        $level->getByID($kelas->level);

        return $level->level . ", " . Generic::getWeekDay()[$kelas->hari_kelas] . " " . $kelas->jam_mulai_kelas . "-" . $kelas->jam_akhir_kelas;
    }

    public static function getGuruNamebyID($id)
    {
        $obj = new SempoaGuruModel();
        $arrGuru = $obj->getWhere("guru_id='$id'");
        return $arrGuru[0]->nama_guru;
    }

    public static function getTrainerNamebyID($id)
    {
        $obj = new TrainerModel();
        $obj->getByID($id);
        return $obj->nama_trainer;
    }

    public static function getMuridNamebyID($id)
    {
        $obj = new MuridModel;
        $arrMurid = $obj->getWhere("id_murid='$id'");
        return $arrMurid[0]->nama_siswa;
    }

    public static function getMyNextLevel($myLevel)
    {
        $objLevel = new SempoaLevel();
        $arrAll = $objLevel->getAll();
        $keymylevel = "";
        foreach ($arrAll as $key => $level) {
            if ($level->id_level == $myLevel) {
                $keymylevel = $key;
                break;
            }
        }
        $help = $arrAll[$key + 1];
        return $help;
    }

    public static function getMyNextLevelKurLamaSpezial($myLevel)
    {
        $objLevel = new SempoaLevel();
        $arrAll = $objLevel->getAll();
        $keymylevel = "";

        if ($myLevel == 7 || $myLevel == 8) {
            // 7
            echo "masuk 1";
            foreach ($arrAll as $key => $level) {
                if ($level->id_level == $myLevel) {
                    $keymylevel = $key;
                    break;
                }
            }
            $help = $arrAll[$key];

        } elseif ($myLevel >= 1 && $myLevel < 7) {
            echo "masuk 3";
            foreach ($arrAll as $key => $level) {
                if ($level->id_level == $myLevel) {
                    $keymylevel = $key;
                    break;
                }
            }
            $help = $arrAll[$key + 1];

        } else {
            return "";
        }
        return $help;


    }

    public static function getMyNextLevelLama($myLevel)
    {
        $objLevel = new SempoaLevelLama();
        $arrAll = $objLevel->getAll();
        $keymylevel = "";
        foreach ($arrAll as $key => $level) {
            if ($level->id_level_lama == $myLevel) {
                $keymylevel = $key;
                break;
            }
        }
        $help = $arrAll[$key + 1];
        return $help;
    }

    public static function istLevelNeedCertificate($level_id)
    {
        $level = new SempoaLevel();
        $level->getByID($level_id);
        if ($level->level_sertifikat) {
            return true;
        } else {
            return false;
        }
    }

    public static function getPOByID($po_id)
    {
        $po_object = new POModel();
        $po_object->getByID($po_id);
        return $po_object;
    }

    public static function createLaporanDebet($orgID, $orgID_Biaya, $kode_jenis_biaya, $jenis_biaya, $keterangan, $jmlh_item, $debet, $type)
    {
        if ($type == "Utama") {
            $jenisbm = new JenisBiayaModel();
            $jenisbm->getByID($orgID . "_" . $jenis_biaya); //bahaya krn di hardcode .... //test coba2 dulu myorgid
//            TransaksiModel::entry($kode_jenis_biaya, $keterangan, $jenisbm->harga * $jmlh_item, $debet, $orgID);
            TransaksiModel::entry($kode_jenis_biaya, $keterangan, $jenisbm->harga * $jmlh_item, $debet, $orgID_Biaya);
        } elseif ($type == "Training") {

            TransaksiModel::entry($kode_jenis_biaya, $keterangan, $jmlh_item, $debet, $orgID);
        } elseif ($type == "Discount100") {

            TransaksiModel::entry($kode_jenis_biaya, $keterangan, $jmlh_item, $debet, $orgID);
        } elseif ($type == "") {
            $jenisbm = new SettingHargaBarang();
            $jenisbm->getByID($orgID_Biaya . "_" . $jenis_biaya); //bahaya krn di hardcode .... //test coba2 dulu myorgid
            TransaksiModel::entry($kode_jenis_biaya, $keterangan, $jenisbm->harga * $jmlh_item, $debet, $orgID);
        }
    }

    public static function createLaporanKredit($orgID, $orgID_Biaya = '0', $kode_jenis_biaya, $jenis_biaya, $keterangan, $jmlh_item, $credit, $type)
    {
        if ($type == "Utama") {
            $jenisbm = new JenisBiayaModel();
            $jenisbm->getByID($orgID . "_" . $jenis_biaya); //bahaya krn di hardcode .... //test coba2 dulu myorgid
            TransaksiModel::entry($kode_jenis_biaya, $keterangan, $credit, $jenisbm->harga * $jmlh_item, $orgID);
        } elseif ($type == "Training") {
            TransaksiModel::entry($kode_jenis_biaya, $keterangan, $credit, $jmlh_item, $orgID);
        } elseif ($type == "Discount100") {
            TransaksiModel::entry($kode_jenis_biaya, $keterangan, $jmlh_item, $credit, $orgID);

        } elseif ($type == "") {
            $jenisbm = new SettingHargaBarang();
            $jenisbm->getByID($orgID_Biaya . "_" . $jenis_biaya); //bahaya krn di hardcode .... //test coba2 dulu myorgid
            TransaksiModel::entry($kode_jenis_biaya, $keterangan, $credit, $jenisbm->harga * $jmlh_item, $orgID);
        }
    }


    public static function getAllMonths()
    {
        $arrBulan = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12);
        return $arrBulan;
    }

    public static function getAllMonthsWithAll()
    {
        $arrBulan = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12);
        $arrBulan[count($arrBulan) + 1] = "All";
        return $arrBulan;
    }

    public static function getMonthName($bln)
    {
        if ($bln == 1) {
            return "Januari";
        } elseif ($bln == 2) {
            return "Febuari";
        } elseif ($bln == 3) {
            return "Maret";
        } elseif ($bln == 4) {
            return "April";
        } elseif ($bln == 5) {
            return "Mei";
        } elseif ($bln == 6) {
            return "Juni";
        } elseif ($bln == 7) {
            return "Juli";
        } elseif ($bln == 8) {
            return "Agustus";
        } elseif ($bln == 9) {
            return "September";
        } elseif ($bln == 10) {
            return "Oktober";
        } elseif ($bln == 11) {
            return "November";
        } elseif ($bln == 12) {
            return "Desember";
        }
    }

    public static function myOrgData()
    {
        $myOrgId = AccessRight::getMyOrgID();
        $myIBO = new SempoaOrg();
        $myIBO->getByID($myOrgId);
        ?>
        <section class="content-header">

            <h1>
                <?= $myIBO->nama; ?>
            </h1>

        </section>

        <section class="content">
            <div class="table-responsive">
                <table class="table table-striped ">
                    <tr>
                        <td>
                            Kode
                        </td>
                        <td>

                            <?= $myIBO->org_kode; ?>
                        </td>
                        <td>
                    </tr>

                    <tr>
                        <td>
                            Nama Pemilik
                        </td>
                        <td>

                            <?= $myIBO->nama_pemilik; ?>
                        </td>
                        <td>
                    </tr>

                    <tr>
                        <td>
                            Alamat
                        </td>
                        <td>

                            <?= $myIBO->alamat; ?>
                        </td>
                        <td>
                    </tr>

                    <tr>
                        <td>
                            No Telp
                        </td>
                        <td>

                            <?= $myIBO->nomor_telp; ?>
                        </td>
                        <td>
                    </tr>

                    <tr>
                        <td>
                            Email
                        </td>
                        <td>

                            <?= $myIBO->email; ?>

                        </td>
                        <td>
                    </tr>
                </table>
            </div>
        </section>
        <?
    }

    public static function getAllGuruQualifiedByTC($tcID)
    {
        $guru = new SempoaGuruModel();
        $arrGuru = $guru->getWhere("guru_tc_id='$tcID' AND status =1");
        $arrAllGuru = array();
        foreach ($arrGuru as $gr) {
            $arrAllGuru[$gr->guru_id] = $gr->nama_guru;
        }

        return $arrAllGuru;
    }

    public static function getAllGuruAktivByTC($tcID)
    {
        $guru = new SempoaGuruModel();
        $arrGuru = $guru->getWhere("guru_tc_id='$tcID'");
        $arrAllGuru = array();
        foreach ($arrGuru as $gr) {
            $arrAllGuru[$gr->guru_id] = $gr->nama_guru;
        }

        return $arrAllGuru;
    }

    public static function getJenisTraining()
    {
        $arrJenis = array();
        $arrJenis[] = KEY::$JENIS_TRAINING_MATERI;
        $arrJenis[] = KEY::$JENIS_TRAINING_EVALUASI;

        return $arrJenis;
    }

    public static function getAllTrainerByIBO($ibo_id)
    {
        $trainer = new TrainerModel();
        $arrTrainer = $trainer->getWhere("tr_ibo_id='$ibo_id'");
        return $arrTrainer;
    }

    public static function getAllTrainerWithIDByIBO($ibo_id)
    {
        $trainer = new TrainerModel();
        $arrTrainer = $trainer->getWhere("tr_ibo_id='$ibo_id'");
        $arrTrainerHelp = array();

        foreach ($arrTrainer as $val) {
            $arrTrainerHelp[$val->id_trainer] = $val->nama_trainer;
        }
        return $arrTrainerHelp;
    }

    public static function getJenisBarangType()
    {
        $barang = new BarangWebModel();
        $arrBarang = $barang->getAll();
        $arrBrgHlp = array();
        foreach ($arrBarang as $brg) {
            $arrBrgHlp[$brg->id_barang_harga] = $brg->jenis_biaya;
        }
        return $arrBrgHlp;
    }

    public static function getJenisBarang()
    {
        $arrJenisBarang = array();
        $arrJenisBarang[KEY::$JENIS_BIAYA_BARANG] = KEY::$JB_BARANG;
        $arrJenisBarang[KEY::$JENIS_BIAYA_BUKU] = KEY::$JB_BUKU;
        $arrJenisBarang[KEY::$JENIS_BIAYA_PERLENGKAPAN] = KEY::$JB_PERLENGKAPAN;
        return $arrJenisBarang;
    }

    public static function getJenisBiayaTraining()
    {
        $arrBiayaTraining = array();
        $arrBiayaTraining[KEY::$INDEX_TRAINING_SATUAN] = KEY::$TRAINING_SATUAN;
        $arrBiayaTraining[KEY::$INDEX_TRAINING_PAKET] = KEY::$TRAINING_PAKET;

        return $arrBiayaTraining;
    }

    public static function getTrainingLevel($training_id)
    {
        $training = new JadwalTrainingModel();
        $training->getByID($training_id);
        return $training->jt_level_from;
    }

    public static function getLevelGroup()
    {
        $arrGroupLevel = array();
        $arrGroupLevel[KEY::$LEVEL_GROUP_JUNIOR] = KEY::$GROUP_JUNIOR;
        $arrGroupLevel[KEY::$LEVEL_GROUP_FOUNDATION] = KEY::$GROUP_FOUNDATION;
        $arrGroupLevel[KEY::$LEVEL_GROUP_INTERMEDIATE] = KEY::$GROUP_INTERMEDIATE;
        $arrGroupLevel[KEY::$LEVEL_GROUP_ADVANCE] = KEY::$GROUP_ADVANCE;
        $arrGroupLevel[KEY::$LEVEL_GROUP_GRAND_MODULE] = KEY::$GROUP_GRAND_MODULE;
        return $arrGroupLevel;
    }

    public static function getSettingNilai()
    {
        $arrNilai = array();
        $arrNilai[0] = "Belum diisi";
        $arrNilai[1] = "A";
        $arrNilai[2] = "B";
        $arrNilai[3] = "C";
        $arrNilai[4] = "D";
        $arrNilai[5] = "E";
        return $arrNilai;
    }


    public static function getJenisKurikulum()
    {
        $arrKur = array();
        $arrKur[KEY::$KURIKULUM_BARU] = KEY::$KURIKULUM_BARU_TEXT;
        $arrKur[KEY::$KURIKULUM_LAMA] = KEY::$KURIKULUM_LAMA_TEXT;

        return $arrKur;
    }


    public static function getLevelKurikulumLama()
    {
        $obj = new SempoaLevelLama();
        $arrObj = $obj->getAll();
        $arrColumn = explode(",", $obj->coloumlist);
        $arrBiaya = array();
        foreach ($arrObj as $val) {
            $sem[$val->id_level_lama] = $val->level_lama;
        }

        return $sem;
    }

    public static function getAktivMuridByTcID($tc_id)
    {
        $objMurid = new MuridModel();
        $arrMurid = $objMurid->getWhere("status !=0 AND murid_tc_id='$tc_id' ORDER BY nama_siswa ASC");
        $arrResult = array();
        foreach ($arrMurid as $murid) {
            $arrResult[$murid->id_murid] = $murid->nama_siswa;
        }

        return $arrResult;
    }

    public static function getJenisUjian()
    {
        $arrJenisUjian = array();
        $arrJenisUjian[KEY::$KEY_UJIAN_LAIN] = KEY::$UJIAN_LAIN;
        $arrJenisUjian[KEY::$KEY_UJIAN_SPT] = KEY::$UJIAN_SPT;
        $arrJenisUjian[KEY::$KEY_UJIAN_EGT] = KEY::$UJIAN_EGT;
        return $arrJenisUjian;
    }

    public static function getStatusAktiv()
    {
        $arrStatusAktif = array();
        $arrStatusAktif[KEY::$KEY_STATUS_TIDAK_AKTIV] = KEY::$STATUS_TIDAK_AKTIV;
        $arrStatusAktif[KEY::$KEY_STATUS_AKTIV] = KEY::$STATUS_AKTIV;
        return $arrStatusAktif;
    }

    static function printsempoa()
    {
        $t = time();
        ?>
        <span id="print_<?= $t; ?>" class="glyphicon glyphicon-print" aria-hidden="true"></span>
        <style type="text/css" media="print">
            @page {
                size: landscape;
            }
        </style>
        <script>

            $('#print_<?= $t; ?>').click(function () {
                window.print();
                return false;
            });
        </script>
        <?
    }

    static function isPast($date)
    {
        $today = date("Y-m-d H:i:s");
        $date = $ujian->ujian_date;

        if ($date >= $today) {
            return true;
        }
        return false;
    }

    static function getBiayaByJenis($jenis_biaya, $org_id)
    {
        $jenisBiaya = new JenisBiayaModel();
        $jenisBiaya->getWhereOne("jenis_biaya=$jenis_biaya AND setting_org_id=$org_id");
        return $jenisBiaya->harga;
    }

    public static function exportLogo()
    {
        $t = time();
        ?>
        <span id="export_<?= $t; ?>" class="glyphicon glyphicon-export" aria-hidden="true"></span>
        <?
    }

    public function exportIt($return)
    {
//        $return = $this->overwriteReadExcel($return);

        $filename = "test.xls";

        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");
        $flag = false;


        $guru = new SempoaGuruModel();
        $all = $guru->getAll();

        //guru_id
//                $kode_guru;
//    public $tanggal_masuk;
//    public $nama_guru;
//		foreach ($return['objs'] as $key => $obj) {
//
//			foreach ($obj as $name => $value) {
//                if(in_array($name,$this->hideColoums))continue;
//				echo Lang::t($name) . "\t";
//			}
//			break;
//		}
//		
//		
//		print("\n");

        echo "No\tNama TC\tNama Guru\tJadwalMengajar\n";
        echo "\t\t\tSenin\tLevel\tSelasa\tLevel\tRabu\tLevel\n";

        foreach ($all as $key => $obj) {
            echo $obj->guru_id . "\t" . $obj->nama_guru . "\t" . $obj->kode_guru . "\n";
        }
        exit;
    }

    public static function getDateRangeByWeek($year)
    {
        $firstDayOfYear = mktime(0, 0, 0, 1, 1, $year);
        $nextMonday = strtotime('monday', $firstDayOfYear);
        $nextSunday = strtotime('sunday', $nextMonday);
        $arrWeek = array();
        $i = 1;
        while (date('Y', $nextMonday) == $year) {
            $arrWeek[$i] = date("d-m", $nextMonday) . ' - ' . date("d-m", $nextSunday);
            $nextMonday = strtotime('+1 week', $nextMonday);
            $nextSunday = strtotime('+1 week', $nextSunday);
            $i++;
        }

        return $arrWeek;
    }

    public static function getLevelAbsenCoach($arrLevel)
    {
        sort($arrLevel);
        foreach ($arrLevel as $lvl) {
            $arrLevelHlp[$lvl] = Generic::getLevelNameByID($lvl);
        }
        $strLevel = implode(",", $arrLevelHlp);
        if ($strLevel == "")
            $strLevel = "-";
        return $strLevel;
    }

    public static function getJeniskelamin()
    {
        $arrJeniskelamin = array();
        $arrJeniskelamin['f'] = 'Female';
        $arrJeniskelamin['m'] = 'Male';
        return $arrJeniskelamin;
    }

    public static function pagination($return, $webClass)
    {
        $c = $return['classname'];
        $page = $return['page'];
        $sort = urlencode($return['sort']);
        $w = (isset($return['search_keyword']) ? $return['search_keyword'] : "");
        $search = $return['search_triger'];
        $totalpage = $return['totalpage'];
        $perpage = $return['perpage'];
        $clms = $return['coloms'];

        $t = time() - 10000000;
        ?>
    <div class="CrudViewPagination <?= $c; ?>_Pagination" id="<?= $c; ?>_Pagination">
        <? if (!($page <= 1)) { ?>
        <span class="CrudViewPagebutton"
              id="<?= $webClass; ?>firstpagepat_<?= $page; ?><?= $t; ?>"><?= Lang::t('first'); ?></span>
        <span class="CrudViewPagebutton"
              id="<?= $webClass; ?>prevpat_<?= $page; ?><?= $t; ?>"><?= Lang::t('prev'); ?></span>
        <script type="text/javascript">
            $("#<?=$webClass;?>firstpagepat_<?=$page;?><?=$t;?>").click(function () {
                openLw(window.selected_page, '<?=_SPPATH;?><?=$webClass;?>/<?=$c;?>?page=1&clms=<?=$clms;?>&sort=<?=$sort;?>&search=<?=$search;?>&word=<?=$w;?>&status=<?=$return['status'];?>&tc_id=<?=$return['tc_id'];?>', 'fade');
            });
            $("#<?=$webClass;?>prevpat_<?=$page;?><?=$t;?>").click(function () {
                openLw(window.selected_page, '<?=_SPPATH;?><?=$webClass;?>/<?=$c;?>?page=<?=($page - 1);?>&clms=<?=$clms;?>&sort=<?=$sort;?>&search=<?=$search;?>&word=<?=$w;?>&status=<?=$return['status'];?>&tc_id=<?=$return['tc_id'];?>', 'fade');
            });
        </script>
        <?
    }
        //handle next pages
        $showpagination = 2;
        if ($page > ($totalpage - $showpagination)) {

            $endpage = $totalpage;
        } else {
            $endpage = $page + $showpagination;
        }

        if ($page >= $showpagination) {
            $beginpage = $page - $showpagination;
        } else {
            $beginpage = 1;
        }
        if ($beginpage < 1) {
            $beginpage = 1;
        }
        if ($endpage > $totalpage) {
            $endpage = $totalpage;
        }
        for ($x = $beginpage; $x <= $endpage; $x++) {
            if ($x == $page) {
                $selected = "selpage";
            } else {
                $selected = "";
            }
            ?>
            <span class="CrudViewPagebutton <?= $selected; ?>"
                  id="<?= $webClass; ?>mppage_<?= $x; ?><?= $t; ?>"><?= $x; ?></span>
            <script type="text/javascript">
                $("#<?=$webClass;?>mppage_<?=$x;?><?=$t;?>").click(function () {
                    openLw(window.selected_page, '<?=_SPPATH;?><?=$webClass;?>/<?=$c;?>?page=<?=$x;?>&sort=<?=$sort;?>&clms=<?=$clms;?>&search=<?=$search;?>&word=<?=$w;?>&status=<?=$return['status'];?>&tc_id=<?=$return['tc_id'];?>', 'fade');
                });
            </script>
            <?
        }
        if (!($page >= $totalpage)) {
            ?><span class="CrudViewPagebutton"
                    id="<?= $webClass; ?>nextpat_<?= $page; ?><?= $t; ?>"><?= Lang::t('next'); ?></span>
            <span class="CrudViewPagebutton"
                  id="<?= $webClass; ?>lastpagepat_<?= $page; ?><?= $t; ?>"><?= Lang::t('last'); ?></span>
            <script type="text/javascript">
                $("#<?=$webClass;?>lastpagepat_<?=$page;?><?=$t;?>").click(function () {
                    openLw(window.selected_page, '<?=_SPPATH;?><?=$webClass;?>/<?=$c;?>?page=<?=$totalpage;?>&clms=<?=$clms;?>&sort=<?=$sort;?>&search=<?=$search;?>&word=<?=$w;?>&status=<?=$return['status'];?>&tc_id=<?=$return['tc_id'];?>', 'fade');
                });
                $("#<?=$webClass;?>nextpat_<?=$page;?><?=$t;?>").click(function () {
                    openLw(window.selected_page, '<?=_SPPATH;?><?=$webClass;?>/<?=$c;?>?page=<?=($page + 1);?>&clms=<?=$clms;?>&sort=<?=$sort;?>&search=<?=$search;?>&word=<?=$w;?>&status=<?=$return['status'];?>&tc_id=<?=$return['tc_id'];?>', 'fade');
                });
            </script><?

        }
        ?></div><?

    }

    public static function pagination_baru()
    {
        $webClass = $_GET['webClass'];
        $c = $_GET['classname'];
        $page = $_GET['page'];
        $w = (isset($_GET['search_keyword']) ? $_GET['search_keyword'] : "");
        $search = $_GET['search_triger'];
        $totalpage = $_GET['totalpage'];
        $perpage = $_GET['perpage'];
        $clms = $_GET['coloms'];
        $sort = "";
//        $sort = urlencode($return['sort']);
        $t = time() - 10000000;
        ?>
    <div class="CrudViewPagination <?= $c; ?>_Pagination" id="<?= $c; ?>_Pagination">
        <? if (!($page <= 1)) { ?>
        <span class="CrudViewPagebutton"
              id="<?= $webClass; ?>firstpagepat_<?= $page; ?><?= $t; ?>"><?= Lang::t('first'); ?></span>
        <span class="CrudViewPagebutton"
              id="<?= $webClass; ?>prevpat_<?= $page; ?><?= $t; ?>"><?= Lang::t('prev'); ?></span>
        <script type="text/javascript">
            $("#<?=$webClass;?>firstpagepat_<?=$page;?><?=$t;?>").click(function () {
                openLw(window.selected_page, '<?=_SPPATH;?><?=$webClass;?>/<?=$c;?>?page=1&clms=<?=$clms;?>&sort=<?=$sort;?>&search=<?=$search;?>&word=<?=$w;?>&status=<?=$return['status'];?>&tc_id=<?=$return['tc_id'];?>', 'fade');
            });
            $("#<?=$webClass;?>prevpat_<?=$page;?><?=$t;?>").click(function () {
                openLw(window.selected_page, '<?=_SPPATH;?><?=$webClass;?>/<?=$c;?>?page=<?=($page - 1);?>&clms=<?=$clms;?>&sort=<?=$sort;?>&search=<?=$search;?>&word=<?=$w;?>&status=<?=$return['status'];?>&tc_id=<?=$return['tc_id'];?>', 'fade');
            });
        </script>
        <?
    }
        //handle next pages
        $showpagination = 2;
        if ($page > ($totalpage - $showpagination)) {

            $endpage = $totalpage;
        } else {
            $endpage = $page + $showpagination;
        }

        if ($page >= $showpagination) {
            $beginpage = $page - $showpagination;
        } else {
            $beginpage = 1;
        }
        if ($beginpage < 1) {
            $beginpage = 1;
        }
        if ($endpage > $totalpage) {
            $endpage = $totalpage;
        }
        for ($x = $beginpage; $x <= $endpage; $x++) {
            if ($x == $page) {
                $selected = "selpage";
            } else {
                $selected = "";
            }
            ?>
            <span class="CrudViewPagebutton <?= $selected; ?>"
                  id="<?= $webClass; ?>mppage_<?= $x; ?><?= $t; ?>"><?= $x; ?></span>
            <script type="text/javascript">
                $("#<?=$webClass;?>mppage_<?=$x;?><?=$t;?>").click(function () {
                    openLw(window.selected_page, '<?=_SPPATH;?><?=$webClass;?>/<?=$c;?>?page=<?=$x;?>&sort=<?=$sort;?>&clms=<?=$clms;?>&search=<?=$search;?>&word=<?=$w;?>&status=<?=$return['status'];?>&tc_id=<?=$return['tc_id'];?>', 'fade');
                });
            </script>
            <?
        }
        if (!($page >= $totalpage)) {
            ?><span class="CrudViewPagebutton"
                    id="<?= $webClass; ?>nextpat_<?= $page; ?><?= $t; ?>"><?= Lang::t('next'); ?></span>
            <span class="CrudViewPagebutton"
                  id="<?= $webClass; ?>lastpagepat_<?= $page; ?><?= $t; ?>"><?= Lang::t('last'); ?></span>
            <script type="text/javascript">
                $("#<?=$webClass;?>lastpagepat_<?=$page;?><?=$t;?>").click(function () {
                    openLw(window.selected_page, '<?=_SPPATH;?><?=$webClass;?>/<?=$c;?>?page=<?=$totalpage;?>&clms=<?=$clms;?>&sort=<?=$sort;?>&search=<?=$search;?>&word=<?=$w;?>&status=<?=$return['status'];?>&tc_id=<?=$return['tc_id'];?>', 'fade');
                });
                $("#<?=$webClass;?>nextpat_<?=$page;?><?=$t;?>").click(function () {
                    openLw(window.selected_page, '<?=_SPPATH;?><?=$webClass;?>/<?=$c;?>?page=<?=($page + 1);?>&clms=<?=$clms;?>&sort=<?=$sort;?>&search=<?=$search;?>&word=<?=$w;?>&status=<?=$return['status'];?>&tc_id=<?=$return['tc_id'];?>', 'fade');
                });
            </script><?

        }
        ?></div><?

    }


    public static function getJenisBiayaType()
    {
        $arrLevel = Generic::getAllLevel();
        $arrJenisBiaya = array();
        foreach ($arrLevel as $key => $level) {
            if ($key == KEY::$LEVEL_JUNIOR1) {
                $arrJenisBiaya[$key] = KEY::$BIAYA_SPP_TYPE_1;
            } else {
                $arrJenisBiaya[$key] = KEY::$BIAYA_SPP_TYPE_2;
            }
        }
        return $arrJenisBiaya;
    }

    public static function getNamaHari()
    {
        $dowMap = array('Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu');
        return $dowMap;
    }

    public static function errorMsg($strMsg)
    {
        $json['status_code'] = 0;
        $json['status_message'] = $strMsg;
        echo json_encode($json);
        die();
    }


    public static function diffTwoDaysInDay($date1, $date2)
    {
        $datediff = $date1 - $date2;
        $datediff = floor($datediff / (60 * 60 * 24));
        return $datediff;
    }


    public static function diffTwoDays($date1, $date2){
        $interval = $date1->diff($date2);

        if($interval->invert == 1){
            return -1* $interval->days;
        }
        return $interval->days;

    }
    public static function getLevelAndKurByIdBarang($id_barang)
    {
        $res = array();
        $obj = new BarangWebModel();
        $obj->getByID($id_barang);
        if (!is_null($obj->id_barang_harga)) {
            $res[KEY::$TEXT_LEVEL] = $obj->level;
            $res[KEY::$TEXT_KURIKULUM] = $obj->jenis_kurikulum;
        }
        return $res;

    }

    public static function getLastNomorBuku($no_buku)
    {

        $awalan = substr($no_buku, 0, 3);
        $help = substr($no_buku, 3, strlen($no_buku));
        $c = ((int)$help);
        $c++;
        if (strlen($c) == 1) {
            // 0 ada 4
            $res = $awalan . "0000" . $c;
        } else if (strlen($c) == 2) {
            // 0 ada 3
            $res = $awalan . "000" . $c;
        } else if (strlen($c) == 3) {
            // 0 ada 2
            $res = $awalan . "00" . $c;
        } else if (strlen($c) == 4) {
            // 0 ada 1
            $res = $awalan . "0" . $c;
        } else {
            pr($awalan . $c);
            $res = $awalan . $c;
        }

        return $res;
    }

    public static function getLevelByBarangID()
    {

        $arrBarangIds = Generic::getAllBuku();
        $arrKur = Generic::returnKurikulum();
        $res = array();
        foreach ($arrBarangIds as $val) {
            $obj = new BarangWebModel();
            $obj->getWhereOne("id_barang_harga=$val");
            if (!(is_null($obj->id_barang_harga))) {
                $kur = $obj->jenis_kurikulum;

                $res[$val] = ($obj->nama_barang) . " - " . $arrKur[$kur];
            }
        }
        return $res;
    }

    public static function returnKurikulum()
    {
        $res[KEY::$KURIKULUM_LAMA] = KEY::$KURIKULUM_LAMA_TEXT;
        $res[KEY::$KURIKULUM_BARU] = KEY::$KURIKULUM_BARU_TEXT;
        return $res;
    }

    public static function getAllBuku()
    {
        $stockNoBuku = new StockBuku();
        $arrBuku = $stockNoBuku->getWhere("stock_buku_id >=1 GROUP BY stock_id_buku");
        $res = array();
        foreach ($arrBuku as $val) {
            $res[] = $val->stock_id_buku;
        }
        return $res;
    }


    public static function getStatusBuku()
    {

        $res[KEY::$BUKU_NON_AVAILABLE] = KEY::$BUKU_NON_AVAILABLE_TEXT;
        $res[KEY::$BUKU_AVAILABLE] = KEY::$BUKU_AVAILABLE_TEXT;
        return $res;
    }

    public static function getLevelIdByIdBarang($id_barang)
    {
        $objBarang = new BarangWebModel();
        $objBarang->getWhereOne("id_barang_harga=$id_barang");
        return $objBarang->level;
    }

    public static function getIdBarangByLevel($level, $kurikulum)
    {
        $barang = new BarangWebModel();
        $arrBarang = $barang->getWhere("level=$level AND jenis_kurikulum=$kurikulum");

        $res = array();
        foreach ($arrBarang as $val) {
            $res[] = $val->id_barang_harga;
        }
        return $res;
    }


    public static function getIdBarangByLevelDanJenisBiaya($level, $kurikulum, $jenisBiaya)
    {
        $barang = new BarangWebModel();
        $arrBarang = $barang->getWhere("level=$level AND jenis_kurikulum=$kurikulum AND jenis_biaya=$jenisBiaya");

        $res = array();
        foreach ($arrBarang as $val) {
            $res[] = $val->id_barang_harga;
        }
        return $res;
    }

    public static function recalculationStock()
    {

    }


    public static function convertLevelLamaKeBaru($levelLama)
    {
        if ($levelLama == 1) {
            return 1;
        }
        if ($levelLama == 2) {
            return 2;
        }
        if ($levelLama == 3) {
            return 3;
        }
        if ($levelLama == 4) {
            return 4;
        }
        if ($levelLama == 5) {
            return 6;
        } elseif ($levelLama == 6) {
            return 7;
        } elseif ($levelLama == 7) {
            return 8;
        } elseif ($levelLama == 8) {
            return 9;
        } elseif ($levelLama == 9) {
            return 10;
        } elseif ($levelLama == 10) {
            return 11;
        } elseif ($levelLama == 11) {
            return 12;
        } elseif ($levelLama == 12) {
            return 13;
        } elseif ($levelLama == 13) {
            return 14;
        }

    }

    public static function convertLevelBaruKeLama($levelBaru)
    {
        if ($levelBaru == 1) {
            return 1;
        }
        if ($levelBaru == 2) {
            return 2;
        }
        if ($levelBaru == 3) {
            return 3;
        }
        if ($levelBaru == 4) {
            return 4;
        }
        if ($levelBaru == 6) {
            return 5;
        } elseif ($levelBaru == 7) {
            return 6;
        } elseif ($levelBaru == 8) {
            return 7;
        } elseif ($levelBaru == 9) {
            return 8;
        } elseif ($levelBaru == 10) {
            return 9;
        } elseif ($levelBaru == 11) {
            return 10;
        } elseif ($levelBaru == 12) {
            return 11;
        } elseif ($levelBaru == 13) {
            return 12;
        } elseif ($levelBaru == 14) {
            return 13;
        }

    }

    public static function getJenisBarang_()
    {
        $barang = new BarangWebModel();
        $arr = $barang->getWhere("1 GROUP BY  jenis_biaya");
        foreach ($arr as $val) {
            $res[] = $val->jenis_biaya;
        }
    }

    public static function getAllStatusBuku()
    {
        $res = array();
        $res[KEY::$BUKU_NON_AVAILABLE_ALIAS] = KEY::$BUKU_NON_AVAILABLE_TEXT;
        $res[KEY::$BUKU_AVAILABLE_ALIAS] = KEY::$BUKU_AVAILABLE_TEXT;
        $res[KEY::$BUKU_RUSAK_ALIAS] = KEY::$BUKU_RUSAK_TEXT;
        return $res;
    }

    public static function getAllStatusRetour()
    {
        $res = array();
        $res[KEY::$RETOUR_STATUS_CLAIM_ALIAS] = KEY::$RETOUR_STATUS_CLAIM_TEXT;
        $res[KEY::$RETOUR_STATUS_CLAIMED_ALIAS] = KEY::$RETOUR_STATUS_CLAIMED_TEXT;
        return $res;
    }

    public static function getAllMuridByTC($tc_id)
    {
        $murid = new MuridModel();
        $arrMurid = $murid->getWhere("murid_tc_id=$tc_id");
        $res = array();
        foreach ($arrMurid as $val) {
            $res[$val->id_murid] = $val->nama_siswa;
        }
        return $res;

    }

    public static function getNoBukuByIuranBulananID($id_invoice)
    {
        $ib = new IuranBuku();
        $ib->getWhereOne("bln_no_invoice='$id_invoice'");
        $id_ib = $ib->bln_id;
        $stockBuku = new StockBuku();
        $stockBuku->getWhereOne("stock_invoice_murid=$id_ib");
        return $stockBuku->stock_buku_no;
    }

    public static function getNoBukuByIuranBulananIDWithTC($id_invoice, $tc_id)
    {
        $ib = new IuranBuku();
        $ib->getWhereOne("bln_no_invoice='$id_invoice' AND bln_tc_id = $tc_id");
        $id_ib = $ib->bln_id;
        $stockBuku = new StockBuku();
        $stockBuku->getWhereOne("stock_invoice_murid=$id_ib");
        return $stockBuku->stock_buku_no;
    }

    public static function getSisaKuponTC($tc_id)
    {
        $kuponSatuan = new KuponSatuan();
        $jumlah = $kuponSatuan->getJumlah("kupon_owner_id='$tc_id' AND kupon_status=0 ORDER by kupon_id ASC");
        return $jumlah;
    }

    public static function getJumlahKuponYangDibeliByBulanTahun($tc_id, $bln, $thn)
    {
        $kuponRequest = new RequestModel();
        $arrKuponRequest = $kuponRequest->getWhere("req_pengirim_org_id='$tc_id' AND req_status = 1 AND MONTH(req_date)=$bln AND YEAR(req_date) =$thn ");
        $jumlah = 0;
        foreach ($arrKuponRequest as $val) {
            $jumlah += $val->req_jumlah;
        }
        return $jumlah;
    }

    public static function getJumlahMuridAktivByTC($tc_id)
    {
        $murid = new MuridModel();
        $jumlah = $murid->getJumlah("murid_tc_id=$tc_id AND status =1");
        return $jumlah;
    }

    public static function isEmailValid($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        } else {
            return false;
        }
    }

    public static function checkFieldKosong($strField, $strErrMsg){
        if($strField == ""){
            self::errorMsg($strErrMsg);
        }
    }

    static function generateRandomString($length = 5)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }


    static function generatePassword($length = 8)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $count = mb_strlen($chars);

        for ($i = 0, $result = ''; $i < $length; $i++) {
            $index = rand(0, $count - 1);
            $result .= mb_substr($chars, $index, 1);
        }

        return $result;
    }

    static function get_client_ip() {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

    static function getNamaSiswaByKodeSiswa($kode_siswa){
        $murid = new MuridModel();
        $murid->getWhereOne("kode_siswa='$kode_siswa'");
        return $murid->nama_siswa;
    }
}
