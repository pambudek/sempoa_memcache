<?php

/**
 * Created by PhpStorm.
 * User: efindiongso
 * Date: 1/24/18
 * Time: 2:53 PM
 */
class JoinChallangeModel extends Model
{
    var $table_name = "sempoa__app_join_challange";
    var $main_id = "join_challange_id";

    //Default Coloms for read
    public $default_read_coloms = "join_id,join_challange_id,join_kode_siswa,join_hasil,join_total_nilai,join_total_waktu,join_created_date,join_updated,join_active";

//allowed colom in CRUD filter
    public $coloumlist = "join_id,join_challange_id,join_kode_siswa,join_hasil,join_total_nilai,join_total_waktu,join_created_date,join_updated,join_active";
    public $join_id;
    public $join_challange_id;
    public $join_kode_siswa;
    public $join_hasil;
    public $join_total_nilai;
    public $join_total_waktu;
    public $join_created_date;
    public $join_updated;
    public $join_active;

    public $crud_webservice_allowed = "join_id,join_challange_id,join_kode_siswa,join_hasil,join_total_nilai,join_total_waktu,join_created_date,join_updated,join_active";

    public function getMuridChallange($kode_siswa)
    {
        $arrayChallange = array();
        $arrayChallange = $this->getWhere("join_kode_siswa='$kode_siswa' ORDER BY join_created_date DESC");
//        pr($arrayChallange);
        return $arrayChallange;
    }

    public function getJoinMurid($join_total_nilai)
    {
        $obj = array();
        $obj = $this->getWhere("join_total_nilai='$join_total_nilai' ORDER BY join_created_date DESC");
//        pr($obj);
        return $obj;

    }
}