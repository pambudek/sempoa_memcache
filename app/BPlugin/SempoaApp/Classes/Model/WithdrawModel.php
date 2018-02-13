<?php
/**
 * Created by PhpStorm.
 * User: Shelly
 * Date: 13/02/2018
 * Time: 11:24
 */

class WithdrawModel extends Model
{
    var $table_name = "sempoa__app_withdraw";
    var $main_id = "withdraw_id";

//Default Coloms for read
    public $default_read_coloms = "withdraw_id,withdraw_kode_siswa,withdraw_parent_id,withdraw_nama_bank,withdraw_jumlah,withdraw_nomor_bank,withdraw_created_date,withdraw_updated,withdraw_active,withdraw_status,withdraw_proces_by";

//allowed colom in CRUD filter
    public $coloumlist = "withdraw_id,withdraw_kode_siswa,withdraw_parent_id,withdraw_nama_bank,withdraw_jumlah,withdraw_nomor_bank,withdraw_created_date,withdraw_updated,withdraw_active,withdraw_status,withdraw_proces_by";
    public $withdraw_id;
    public $withdraw_kode_siswa;
    public $withdraw_parent_id;
    public $withdraw_nama_bank;
    public $withdraw_jumlah;
    public $withdraw_nomor_bank;
    public $withdraw_created_date;
    public $withdraw_updated;
    public $withdraw_active;
    public $withdraw_status;
    public $withdraw_proces_by;
    public $crud_webservice_allowed = "withdraw_id,withdraw_kode_siswa,withdraw_parent_id,withdraw_nama_bank,withdraw_jumlah,withdraw_nomor_bank,withdraw_created_date,withdraw_updated,withdraw_active,withdraw_status,withdraw_proces_by";


    public function getMyWithDraw($kode_siswa){
        $arrWithDraw = array();
        $arrWithDraw = $this->getWhere("withdraw_kode_siswa='$kode_siswa' ORDER BY withdraw_created_date DESC");
        return $arrWithDraw;
    }
}