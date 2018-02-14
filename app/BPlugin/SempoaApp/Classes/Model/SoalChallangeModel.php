<?php

/**
 * Created by PhpStorm.
 * User: efindiongso
 * Date: 1/17/18
 * Time: 3:09 PM
 */
class SoalChallangeModel extends Model
{
    var $table_name = "sempoa__app_soal_challange";
    var $main_id = "soal_challange_id";

    //Default Coloms for read
    public $default_read_coloms = "soal_challange_id,soal_challange_level,soal_challange_soal,soal_challange_jawaban,soal_challange_created_date,soal_challange_update,soal_challange_status";

//allowed colom in CRUD filter
    public $coloumlist = "soal_challange_id,soal_challange_level,soal_challange_soal,soal_challange_jawaban,soal_challange_created_date,soal_challange_update,soal_challange_status";
    public $soal_challange_id;
    public $soal_challange_level;
    public $soal_challange_soal;
    public $soal_challange_jawaban;
    public $soal_challange_created_date;
    public $soal_challange_update;
    public $soal_challange_status;
    public $crud_setting = array("add" => 0, "search" => 1, "viewall" => 0, "export" => 1, "toggle" => 1, "import" => 0, "webservice" => 0);


    public function overwriteForm($return, $returnfull)
    {
        $t = time();
        $arrLevelMurid = Generic::getAllLevel();

        $arrStatus = array('0'=>"Tidak Aktif", "1"=>"Aktif");
        $return = parent::overwriteForm($return, $returnfull);

        $return['soal_challange_level'] = new Leap\View\InputSelect($arrLevelMurid, "soal_challange_level", "soal_challange_level", $this->soal_challange_level);
        if ($this->soal_challange_created_date == "") {

            $return['soal_challange_created_date'] = new Leap\View\InputText("date", "soal_challange_created_date", "soal_challange_created_date", leap_mysqldate());
        } else {
            $return['soal_challange_created_date'] = new Leap\View\InputText("date", "soal_challange_created_date", "soal_challange_created_date", $this->soal_challange_created_date);

        }
        if ($this->soal_challange_update == "") {

            $return['soal_challange_update'] = new Leap\View\InputText("date", "soal_challange_update", "soal_challange_update", leap_mysqldate());
        } else {
            $return['soal_challange_update'] = new Leap\View\InputText("date", "soal_challange_update", "soal_challange_update", $this->soal_challange_update);

        }

        $return['soal_challange_status'] = new Leap\View\InputSelect($arrStatus, "soal_challange_status", "soal_challange_status", $this->soal_challange_status);

        return $return;
    }

    public function overwriteRead($return)
    {
        $objs = $return['objs'];
        $arrStatus = array('0'=>"Tidak Aktif", "1"=>"Aktif");
        $arrLevel = Generic::getAllLevel();
        foreach ($objs as $obj) {
            if (isset($obj->soal_challange_status)) {
                $obj->soal_challange_status = $arrStatus[$obj->soal_challange_status];
            }
            if (isset($obj->soal_challange_level)) {
                $obj->soal_challange_level = $arrLevel[$obj->soal_challange_level];
            }
            if (isset($obj->soal_challange_created_date)) {
                $a = new DateTime($obj->soal_challange_created_date);
                $obj->soal_challange_created_date = $a->format("d-m-Y");
            }

            if (isset($obj->soal_challange_update)) {
                $a = new DateTime($obj->soal_challange_update);
                $obj->soal_challange_update = $a->format("d-m-Y");
            }
        }


        return $return;
    }

}