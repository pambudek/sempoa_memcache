<?php

/**
 * Created by PhpStorm.
 * User: efindiongso
 * Date: 1/24/18
 * Time: 2:18 PM
 */
class ChallangeModel extends SempoaModel
{

    var $table_name = "sempoa__app_challange";
    var $main_id = "challange_id";

    //Default Coloms for read
    public $default_read_coloms = "challange_id,chllange_type,challange_title,challange_level,challange_date,challange_status,challange_soal_id,challange_murid_ikut,challange_created_date,challange_updated,challange_active,challange_ak,challange_kpo,challange_ibo,challange_tc";
//allowed colom in CRUD filter
    public $coloumlist = "challange_id,chllange_type,challange_title,challange_level,challange_date,challange_status,challange_soal_id,challange_murid_ikut,challange_created_date,challange_updated,challange_active,challange_ak,challange_kpo,challange_ibo,challange_tc";
    public $challange_id;
    public $chllange_type;
    public $challange_title;
    public $challange_level;
    public $challange_date;
    public $challange_status;
    public $challange_soal_id;
    public $challange_murid_ikut;
    public $challange_created_date;
    public $challange_updated;
    public $challange_active;
    public $challange_ak;
    public $challange_kpo;
    public $challange_ibo;
    public $challange_tc;
    public $hideColoums = array("challange_ak","challange_kpo","challange_ibo","challange_tc");
    public $crud_setting = array("add" => 0, "search" => 1, "viewall" => 0, "export" => 1, "toggle" => 1, "import" => 0, "webservice" => 0);
    public $crud_webservice_allowed = "challange_id,chllange_type,challange_title,challange_level,challange_date,challange_status,challange_murid_ikut,challange_created_date,challange_updated,challange_active,challange_ak,challange_kpo,challange_ibo,challange_tc";

    public function overwriteForm($return, $returnfull)
    {
        $t = time();
        $arrChallangeType = array('0' => "Weekly", "1" => "Regional");
        $arrLevelMurid = Generic::getAllLevel();

        $arrStatus = array('0' => "Tidak Aktif", "1" => "Aktif");
        $return = parent::overwriteForm($return, $returnfull);
        $return['challange_level'] = new Leap\View\InputSelect($arrLevelMurid, "challange_level", "challange_level", $this->challange_level);
        $return['chllange_type'] = new Leap\View\InputSelect($arrChallangeType, "chllange_type", "chllange_type", $this->chllange_type);
        $return['challange_murid_ikut'] = new Leap\View\InputText("hidden", 'challange_murid_ikut', 'challange_murid_ikut', $this->challange_murid_ikut);

        $return['challange_tc'] = new Leap\View\InputText("hidden", 'challange_tc', 'challange_tc', $this->challange_tc);
        $return['challange_ibo'] = new Leap\View\InputText("hidden", 'challange_ibo', 'challange_ibo', AccessRight::getMyOrgID());
        $return['challange_kpo'] = new Leap\View\InputText("hidden", 'challange_kpo', 'challange_kpo', Generic::getMyParentID(AccessRight::getMyOrgID()));
        $return['challange_ak'] = new Leap\View\InputText("hidden", 'challange_ak', 'challange_ak', Generic::getMyParentID(Generic::getMyParentID(AccessRight::getMyOrgID())));
        $return['challange_active'] = new Leap\View\InputSelect($arrStatus, "challange_active", "challange_active", $this->challange_active);

        if ($this->challange_date == "") {

            $return['challange_date'] = new Leap\View\InputText("date", "challange_date", "challange_date", leap_mysqldate());
        } else {
            $return['challange_date'] = new Leap\View\InputText("date", "challange_date", "challange_date", $this->challange_date);

        }
        if ($this->challange_created_date == "") {

            $return['challange_created_date'] = new Leap\View\InputText("date", "challange_created_date", "challange_created_date", leap_mysqldate());
        } else {
            $return['challange_created_date'] = new Leap\View\InputText("date", "challange_created_date", "challange_created_date", $this->challange_created_date);

        }
        if ($this->challange_updated == "") {
            $return['challange_updated'] = new Leap\View\InputText("date", "challange_updated", "challange_updated", leap_mysqldate());
        } else {
            $return['challange_updated'] = new Leap\View\InputText("date", "challange_updated", "challange_updated", $this->challange_updated);

        }

        $return['challange_status'] = new Leap\View\InputSelect($arrStatus, "challange_status", "challange_status", $this->challange_status);
        $soal = new SoalChallangeModel();
        $arrChallangeSoal = $soal->getSoalChallangeByLevel($this->challange_level);
        foreach($arrChallangeSoal as $val){

        }
        $label = implode(".", $arrChallangeSoal);
        $value = implode(",", array_keys($arrChallangeSoal));
        $return['challange_soal_id'] = new Leap\View\InputFieldToken("text", 'challange_soal_id', 'challange_soal_id', $value, $label, $this->challange_soal_id);

//        pr($label);
//        $soalChallange = new SoalChallangeModel();
//        $arrChallangeSoal = $soalChallange->getSoalChallangeByLevel($this->challange_level);
////        pr($arrChallangeSoal);
//        $label = implode(",", $arrChallangeSoal);
//        $value = implode(",", array_keys($arrChallangeSoal));
//
        return $return;
    }

    public function overwriteRead($return)
    {
        $objs = $return['objs'];

        $arrChallangeType = array('0' => "Weekly", "1" => "Regional");
        $arrStatus = array('0' => "Tidak Aktif", "1" => "Aktif");
        $arrLevel = Generic::getAllLevel();
        foreach ($objs as $obj) {
            if (isset($obj->chllange_type)) {
                $obj->chllange_type = $arrChallangeType[$obj->chllange_type];
            }
            if (isset($obj->challange_status)) {
                $obj->challange_status = $arrStatus[$obj->challange_status];
            }
            if (isset($obj->challange_active)) {
                $obj->challange_active = $arrStatus[$obj->challange_active];
            }

            if (isset($obj->challange_level)) {
                $obj->challange_level = $arrLevel[$obj->challange_level];
            }
            if (isset($obj->challange_created_date)) {
                $a = new DateTime($obj->challange_created_date);
                $obj->challange_created_date = $a->format("d-m-Y");
            }

            if (isset($obj->challange_updated)) {
                $a = new DateTime($obj->challange_updated);
                $obj->challange_updated = $a->format("d-m-Y");
            }

            if (isset($obj->challange_date)) {
                $a = new DateTime($obj->challange_date);
                $obj->challange_date = $a->format("d-m-Y");
            }

        }


        return $return;
    }
}