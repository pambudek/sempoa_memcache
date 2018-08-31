<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of KelasWebModel
 *
 * @author efindiongso
 */
class KelasWebModel extends Model {

    //put your code here
    var $table_name = "sempoa__kelas";
    var $main_id = "id_kelas";
    //Default Coloms for read
    public $default_read_coloms = "id_kelas,hari_kelas,jam_mulai_kelas,jam_akhir_kelas,id_room,level,guru_id,aktiv,ak_id,kpo_id,ibo_id,tc_id";
//allowed colom in CRUD filter
    public $coloumlist = "id_kelas,hari_kelas,jam_mulai_kelas,jam_akhir_kelas,id_room,murid_id,guru_id,level,aktiv,ak_id,kpo_id,ibo_id,tc_id";
    public $customColumnList = "id_kelas,hari_kelas,jam_mulai_kelas,jam_akhir_kelas,id_room,murid_id,level,guru_id,aktiv";
    public $id_kelas;
    public $hari_kelas;
    public $jam_mulai_kelas;
    public $jam_akhir_kelas;
    public $id_room;
    public $murid_id;
    public $level;
    public $guru_id;
    public $aktiv;
    public $ak_id;
    public $kpo_id;
    public $ibo_id;
    public $tc_id;
    public $hideColoums = array("ak_id", "kpo_id", "ibo_id", "tc_id");
    public $APPWS = "id_kelas,jam_mulai_kelas,jam_akhir_kelas,id_room";
    public function overwriteForm($return, $returnfull) {
//        echo $day_of_week = date('N', now()));
        parent::overwriteForm($return, $returnfull);
        $arrDays = Generic::getWeekDay();
        $return['hari_kelas'] = new \Leap\View\InputSelect($arrDays, 'hari_kelas', 'hari_kelas', $this->hari_kelas);
        $return['jam_mulai_kelas'] = new Leap\View\InputText("time", "jam_mulai_kelas", "jam_mulai_kelas", $this->jam_mulai_kelas);
        $return['jam_akhir_kelas'] = new Leap\View\InputText("time", "jam_akhir_kelas", "jam_akhir_kelas", $this->jam_akhir_kelas);

        if (AccessRight::getMyOrgType() == "tc") {
            $myID = AccessRight::getMyOrgID();
            $myParentID = Generic::getMyParentID($myID);
            $myGrandParentID = Generic::getMyParentID($myParentID);
            $myGrandGrandParentID = Generic::getMyParentID($myGrandParentID);
            $return['ak_id'] = new Leap\View\InputText("hidden", "ak_id", "ak_id", $myGrandGrandParentID);
            $return['kpo_id'] = new Leap\View\InputText("hidden", "kpo_id", "kpo_id", $myGrandParentID);
            $return['ibo_id'] = new Leap\View\InputText("hidden", "ibo_id", "ibo_id", $myParentID);
            $return['tc_id'] = new Leap\View\InputText("hidden", "tc_id", "tc_id", $myID);
            $return['aktiv'] = new \Leap\View\InputSelect($this->arrayYesNO, "aktiv", "aktiv", $this->aktiv);

        }

        $guru = new SempoaGuruModel();
        $new = array();
        $arrs = $guru->getWhere("status =1  AND guru_tc_id = '".AccessRight::getMyOrgID()."' ORDER BY nama_guru ASC");
//        pr($arrs);
        foreach($arrs as $gg){
            $level = new SempoaLevel();
            $level->getByID($gg->id_level_training_guru);
            $new[$gg->guru_id] = $gg->nama_guru." (".$level->level.")";
        }

        $return['guru_id'] = new \Leap\View\InputSelect($new, 'guru_id', 'guru_id', $this->guru_id);
        $return['level'] = new Leap\View\InputText("hidden", "level", "level", $this->level);

        $return['murid_id'] = new Leap\View\InputText("hidden", "murid_id", "murid_id", $this->murid_id);

        return $return;
    }

    public function overwriteRead($return) {
       
        parent::overwriteRead($return);
        $arrAktiv[0] = "Nonaktiv";
        $arrAktiv[1] = "Aktiv";
        $objs = $return['objs'];
        $arrDays = Generic::getWeekDay();
        foreach ($objs as $obj) {
            $obj->hari_kelas = $arrDays[$obj->hari_kelas];
            if (isset($obj->level)) {
                $obj->level = Generic::getLevelNameByID($obj->level);
            }
             if (isset($obj->guru_id)) {
                $obj->guru_id = Generic::getGuruNamebyID($obj->guru_id);
            }
            if (isset($obj->aktiv)) {
                $obj->aktiv = $arrAktiv[$obj->aktiv];
            }
//            if ($obj->murid_id != "") {
//                $obj->murid_id = "<button onclick='window.location.href = \"mailto:" . $obj->murid_id . "\";'>Email</button>";
//            }
//
//            if ($obj->guru_id != "") {
//                $obj->guru_id = "<button onclick='window.location.href = \"mailto:" . $obj->guru_id . "\";'>Email</button>";
//            }
        }
        return $return;
    }

    public function constraints(){

        $err = array();

        if($this->guru_id>0){
            $guru = new SempoaGuruModel();
            $guru->getByID($this->guru_id);
            $level = new SempoaLevel();
            $level->getByID($guru->id_level_training_guru);

            $this->level = $level->id_level;
        }

        if (!isset($this->hari_kelas)) {
            $err['hari_kelas'] = Lang::t('Masukan Hari untuk kelas!');
        }

        if (!isset($this->jam_mulai_kelas)) {
            $err['jam_mulai_kelas'] = Lang::t('Masukan Jam Mulai Kelas!');
        }

        if (!isset($this->jam_akhir_kelas)) {
            $err['jam_akhir_kelas'] = Lang::t('Masukan Jam Akhir Kelas!');
        }


        return $err;
    }


    public function getGuruKelasByDay($guru_id, $day){
        $arrKelas = $this->getWhere("guru_id='$guru_id' AND hari_kelas='$day' ORDER BY jam_mulai_kelas ASC");
        return $arrKelas;
    }
}
