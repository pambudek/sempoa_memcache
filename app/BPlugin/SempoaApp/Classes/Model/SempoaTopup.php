<?php

/**
 * Created by PhpStorm.
 * User: efindiongso
 * Date: 1/23/18
 * Time: 2:59 PM
 */
class SempoaTopup extends Model
{
    var $table_name = "Sempoa__app_topup";
    var $main_id = "topup_id";

    //Default Coloms for read
    public $default_read_coloms = "topup_id,topup_parent_id,topup_kodesiswa,topup_status,topup_created_date,topup_pending_date,topup_approved_date,topup_canceled_date,topup_jumlah,topup_carapembayaran,topup_changed_status_by";

//allowed colom in CRUD filter
    public $coloumlist = "topup_id,topup_parent_id,topup_kodesiswa,topup_status,topup_created_date,topup_pending_date,topup_approved_date,topup_canceled_date,topup_jumlah,topup_carapembayaran,topup_changed_status_by";
    public $topup_id;
    public $topup_parent_id;
    public $topup_kodesiswa;
    public $topup_status;
    public $topup_created_date;
    public $topup_pending_date;
    public $topup_approved_date;
    public $topup_canceled_date;
    public $topup_jumlah;
    public $topup_carapembayaran;
    public $topup_changed_status_by;

    public $crud_webservice_allowed = "topup_id,topup_parent_id,topup_kodesiswa,topup_status,topup_created_date,topup_pending_date,topup_approved_date,topup_canceled_date,topup_jumlah,topup_carapembayaran,topup_changed_status_by";

    public function getParentTopUp($parent_id)
    {
        $arrMyTopUp = $this->getWhere("topup_parent_id='$parent_id' ORDER BY topup_id DESC");
        pr($arrMyTopUp);
        return $arrMyTopUp;
    }

    public function getStudentTopUp($kode_siswa)
    {
        $arrMyTopUp = $this->getWhere("topup_kodesiswa='$kode_siswa' ORDER BY topup_id DESC");
        return $arrMyTopUp;
    }
}