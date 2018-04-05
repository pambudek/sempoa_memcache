<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of StatusHisMuridModel
 *
 * @author efindiongso
 */
class StatusHisMuridModel extends Model
{

    public $table_name = "sempoa__history_status";
    var $main_id = "status_id";
    //put your code here
    //Default Coloms for read
    public $default_read_coloms = "status_id,status_murid_id,status,status_level_murid,status_tanggal_mulai,status_tanggal_akhir,status_ak_id,status_kpo_id,status_ibo_id,status_tc_id";
//allowed colom in CRUD filter
    public $coloumlist = "status_id,status_murid_id,status,status_level_murid,status_tanggal_mulai,status_tanggal_akhir,status_ak_id,status_kpo_id,status_ibo_id,status_tc_id";
    public $status_id;
    public $status_murid_id;
    public $status;
    public $status_level_murid;
    public $status_tanggal_mulai;
    public $status_tanggal_akhir;
    public $status_ak_id;
    public $status_kpo_id;
    public $status_ibo_id;
    public $status_tc_id;

    public function getJumlahMuridAktivByMonth($ibo_id, $level, $bln, $thn)
    {
//         $q = "SELECT * FROM {$murid->table_name} murid INNER JOIN {$statusMurid->table_name} status ON status.status_murid_id = murid.id_murid WHERE murid.murid_tc_id='$keytc' AND YEAR(status.status_tanggal_mulai ) = $thn AND MONTH(status.status_tanggal_mulai ) = $bln  AND status.status_tanggal_akhir ='1970-01-01 07:00:00'  AND status.status=$status GROUP BY murid.id_murid";
//      
        $arrJumlahMurid = $this->getWhere("status_ibo_id=$ibo_id AND status=1 AND status_level_murid=$level AND status_tanggal_akhir='1970-01-01 07:00:00' AND MONTH(status_tanggal_mulai)<=$bln AND YEAR(status_tanggal_mulai)<=$thn");
//        pr($arrJumlahMurid);
        return count($arrJumlahMurid);
    }

    public function getJumlahMuridCutiByMonth($ibo_id, $level, $bln, $thn)
    {

        $arrJumlahMurid = $this->getWhere("status_ibo_id=$ibo_id AND status=2 AND status_level_murid=$level AND YEAR(status_tanggal_mulai)=$thn AND MONTH(status_tanggal_mulai)=$bln");
        return count($arrJumlahMurid);
    }

    public function getJumlahMuridKeluarByMonth($ibo_id, $level, $bln, $thn)
    {

        $arrJumlahMurid = $this->getWhere("status_ibo_id=$ibo_id AND status=3 AND status_level_murid=$level AND YEAR(status_tanggal_mulai)=$thn AND MONTH(status_tanggal_mulai)=$bln");
        return count($arrJumlahMurid);
    }

    public function getJumlahMuridAktivByMonthTC($tc_id, $bln, $thn)
    {
        $arrJumlahMurid = $this->getWhere("status_tc_id=$tc_id AND status=1  AND status_tanggal_akhir='1970-01-01 07:00:00'");
//        pr($arrJumlahMurid);
//        return $tc_id;
        return count($arrJumlahMurid);
    }


    public function createHistory($id_murid)
    {
        $objMurid = new MuridModel();
        $objMurid->getByID($id_murid);
        $this->status_murid_id = $objMurid->id_murid;
        $this->status_tanggal_mulai = leap_mysqldate();
        $this->status_level_murid = $objMurid->id_level_sekarang;
        $this->status = $objMurid->status;
        $this->status_ak_id = $objMurid->murid_ak_id;
        $this->status_kpo_id = $objMurid->murid_kpo_id;
        $this->status_ibo_id = $objMurid->murid_ibo_id;
        $this->status_tc_id = $objMurid->murid_tc_id;
        $this->save();
//        $logMurid = new LogStatusMurid();
//        $logMurid->createLogMurid($id_murid);
    }

    public function updateHistory($id_murid)
    {
        $objStatus = new StatusHisMuridModel();
        $objStatus = $this->getLastDataMurid($id_murid);
        $objStatus->status_tanggal_akhir = leap_mysqldate();
        $objStatus->save(1);
        $objMurid = new MuridModel();
        $objMurid->getByID($id_murid);

        $this->status_murid_id = $objMurid->id_murid;
        $this->status_tanggal_mulai = leap_mysqldate();
        $this->status_level_murid = $objMurid->id_level_sekarang;
        $this->status = $objMurid->status;
        $this->status_ak_id = $objMurid->murid_ak_id;
        $this->status_kpo_id = $objMurid->murid_kpo_id;
        $this->status_ibo_id = $objMurid->murid_ibo_id;
        $this->status_tc_id = $objMurid->murid_tc_id;
        $this->save();
    }

    public function getLastDataMurid($id_murid)
    {
        $this->getWhereOne("status_murid_id=$id_murid  ORDER BY status_tanggal_mulai DESC");
        return $this;
    }

    public function updateHistoryMurid($id_murid)
    {
        $this->getWhereOne("status_murid_id='$id_murid'  ORDER BY status_tanggal_mulai DESC");
        $this->status_tanggal_akhir = leap_mysqldate();
        $this->save(1);
    }

}
