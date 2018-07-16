<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of LogStatusMurid
 *
 * @author efindiongso
 */
class LogStatusMurid extends Model {

    //put your code here
    var $main_id = "log_id";
    var $table_name = "sempoa__log_status_murid";
    //Default Coloms for read
    public $default_read_coloms = "log_id,log_id_murid,log_tgl,log_bln,log_thn,log_status,log_ak_id,log_kpo_id,log_ibo_id,log_tc_id";
//allowed colom in CRUD filter
    public $coloumlist = "log_id,log_id_murid,log_tgl,log_bln,log_thn,log_status,log_ak_id,log_kpo_id,log_ibo_id,log_tc_id";
    public $log_id;
    public $log_id_murid;
    public $log_tgl;
    public $log_bln;
    public $log_thn;
    public $log_status;
    public $log_ak_id;
    public $log_kpo_id;
    public $log_ibo_id;
    public $log_tc_id;

    public function createLogMurid($id_murid) {
        $murid = new MuridModel();
        $murid->getByID($id_murid);
        $this->log_id_murid = $id_murid;
        $this->log_tgl = date("d");
        $this->log_bln = date("n");
        $this->log_thn = date("Y");
        $this->log_ak_id = $murid->murid_ak_id;
        $this->log_kpo_id = $murid->murid_kpo_id;
        $this->log_ibo_id = $murid->murid_ibo_id;
        $this->log_tc_id = $murid->murid_tc_id;
        if ($murid->status == KEY::$STATUSMURIDAKTIV) {
            $this->log_status = "A";
        } elseif ($murid->status == KEY::$STATUSMURIDCUTI) {
            $this->log_status = "C";
        } elseif ($murid->status == KEY::$STATUSMURIDNKELUAR) {
            $this->log_status = "K";
        } elseif ($murid->status == KEY::$STATUSMURIDNLULUS) {
            $this->log_status = "L";
        } elseif ($murid->status == KEY::$STATUSMURIDNONAKTIV) {
            $this->log_status = "NA";
        }

        $this->save();
    }

    public function createLogMuridORG($id_murid) {
        $murid = new MuridModel();
        $murid->getByID($id_murid);
        $this->log_id_murid = $id_murid;
        $this->log_tgl = date("d");
        $this->log_bln = date("n");
        $this->log_thn = date("Y");
        $this->log_ak_id = $murid->murid_ak_id;
        $this->log_kpo_id = $murid->murid_kpo_id;
        $this->log_ibo_id = $murid->murid_ibo_id;
        $this->log_tc_id = $murid->murid_tc_id;
        if ($murid->status == KEY::$STATUSMURIDAKTIV) {
            $this->log_status = "A";
        } elseif ($murid->status == KEY::$STATUSMURIDCUTI) {
            $this->log_status = "C";
        } elseif ($murid->status == KEY::$STATUSMURIDNKELUAR) {
            $this->log_status = "K";
        } elseif ($murid->status == KEY::$STATUSMURIDNLULUS) {
            $this->log_status = "L";
        } elseif ($murid->status == KEY::$STATUSMURIDNONAKTIV) {
            $this->log_status = "NA";
        }

        $this->save();
    }

    /*
     * status K = keluar
     * status A = Aktiv
     * status C = Cuti
     * status B = Baru
     * status L = Lulus
     * $orgType = ak
     * $orgType = kpo
     * $orgType = ibo
     * $orgType = tc
     */

    public function getCountSiswaByStatusOrgType_tmp($bln, $thn, $status_siswa, $orgType, $org_id) {
        $count = 0;
        if ($orgType == KEY::$AK) {
            $count = $this->getJumlah("log_status='$status_siswa' AND log_ak_id=$org_id AND log_thn=$thn AND log_bln=$bln");
        } elseif ($orgType == KEY::$KPO) {
            $count = $this->getJumlah("log_status='$status_siswa' AND log_kpo_id=$org_id AND log_thn=$thn AND log_bln=$bln");
        } elseif ($orgType == KEY::$IBO) {
            $count = $this->getJumlah("log_status='$status_siswa' AND log_ibo_id=$org_id AND log_thn=$thn AND log_bln=$bln");
        } elseif ($orgType == KEY::$TC) {

            $count = $this->getJumlah("log_status='$status_siswa' AND log_tc_id=$org_id AND log_thn=$thn AND log_bln=$bln");

        }
        return $count;
    }


    //
    public function getCountSiswaByStatusOrgType($bln, $thn, $status_siswa, $orgType, $org_id) {
        $count = 0;
        if ($orgType == KEY::$AK) {
//            $count = $this->getJumlah("log_status='$status_siswa' AND log_ak_id=$org_id AND log_thn=$thn AND log_bln=$bln");
            $arrLogMuridID = $this->getWhere("log_ak_id=$org_id AND log_thn=$thn AND log_bln=$bln ORDER BY log_id ASC");
        } elseif ($orgType == KEY::$KPO) {
//            $count = $this->getJumlah("log_status='$status_siswa' AND log_kpo_id=$org_id AND log_thn=$thn AND log_bln=$bln");
            $arrLogMuridID = $this->getWhere("log_kpo_id=$org_id AND log_thn=$thn AND log_bln=$bln ORDER BY log_id ASC");
        } elseif ($orgType == KEY::$IBO) {
            $arrLogMuridID = $this->getWhere("log_ibo_id=$org_id AND log_thn=$thn AND log_bln=$bln ORDER BY log_id ASC");

        } elseif ($orgType == KEY::$TC) {
            $arrLogMuridID = $this->getWhere("log_tc_id=$org_id AND log_thn=$thn AND log_bln=$bln ORDER BY log_id ASC");

        }
        $arrMuridID = array();
        foreach($arrLogMuridID as $muridID){
            $arrMuridID[$muridID->log_id_murid][] = $muridID->log_status;
        }
        foreach($arrMuridID as $val){
            if($val[count($val) - 1] == $status_siswa){
                $count++;
            }
        }
        return $count;
    }


    public function getCountSiswaCutiGroup($id_siswa,$status, $bln, $thn, $org_id){
        $logMurid = new LogStatusMurid();
        $arr = $logMurid->getWhere("log_id_murid=$id_siswa AND log_status='$status' AND log_tc_id='$org_id' AND log_bln=$bln AND log_thn=$thn GROUP BY log_status");

        pr(count($arr));
    }

    /*
     * status K = keluar
     * status A = Aktiv
     * status C = Cuti
     * status B = Baru
     * status L = Lulus
     * $orgType = ak
     * $orgType = kpo
     * $orgType = ibo
     * $orgType = tc
     */

    public function getCountSiswaAktivBulanLalu($bln, $thn, $status_siswa, $orgType, $org_id) {
        if ($bln == 1) {
            $bln = 12;
            $thn = $thn - 1;
        } else {
            $bln = $bln - 1;
        }
        if ($orgType == KEY::$AK) {
            $count = $this->getJumlah("log_status='$status_siswa' AND log_ak_id=$org_id AND log_thn=$thn AND log_bln=$bln");
        } elseif ($orgType == KEY::$KPO) {
            $count = $this->getJumlah("log_status='$status_siswa' AND log_kpo_id=$org_id AND log_thn=$thn AND log_bln=$bln");
        } elseif ($orgType == KEY::$IBO) {
            $count = $this->getJumlah("log_status='$status_siswa' AND log_ibo_id=$org_id AND log_thn=$thn AND log_bln=$bln");
        } elseif ($orgType == KEY::$TC) {
            $count = $this->getJumlah("log_status='$status_siswa' AND log_tc_id=$org_id AND log_thn=$thn AND log_bln=$bln");
        }

        return $count;
    }

    public function istLogExistiert($murid_id, $statusMurid, $bln, $thn){

    }
}
