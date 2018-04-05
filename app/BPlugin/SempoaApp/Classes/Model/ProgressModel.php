<?php

/**
 * Created by PhpStorm.
 * User: efindiongso
 * Date: 1/29/18
 * Time: 9:25 AM
 */
class ProgressModel extends Model
{
    var $table_name = "sempoa__app_progress";
    var $main_id = "progress_id";

    //Default Coloms for read
    public $default_read_coloms = "progress_id,kode_siswa,progress_level,progress_nama_buku_1,progress_nama_buku_2,progress_nama_buku_3,progress_nama_buku_4,progress_hal_buku_1,progress_hal_buku_2,progress_hal_buku_3,progress_hal_buku_4,progress_total_hal_1,progress_total_hal_2,progress_total_hal_3,progress_total_hal_4,progress_guru_id,progress_created,progress_updated,progress_active";

//allowed colom in CRUD filter
    public $coloumlist = "progress_id,kode_siswa,progress_level,progress_nama_buku_1,progress_nama_buku_2,progress_nama_buku_3,progress_nama_buku_4,progress_hal_buku_1,progress_hal_buku_2,progress_hal_buku_3,progress_hal_buku_4,progress_total_hal_1,progress_total_hal_2,progress_total_hal_3,progress_total_hal_4,progress_guru_id,progress_created,progress_updated,progress_active";
    public $progress_id;
    public $kode_siswa;
    public $progress_level;
    public $progress_nama_buku_1;
    public $progress_nama_buku_2;
    public $progress_nama_buku_3;
    public $progress_nama_buku_4;
    public $progress_hal_buku_1;
    public $progress_hal_buku_2;
    public $progress_hal_buku_3;
    public $progress_hal_buku_4;
    public $progress_total_hal_1;
    public $progress_total_hal_2;
    public $progress_total_hal_3;
    public $progress_total_hal_4;
    public $progress_guru_id;
    public $progress_created;
    public $progress_updated;
    public $progress_active;

    public $crud_webservice_allowed = "progress_id,kode_siswa,progress_level,progress_nama_buku_1,progress_nama_buku_2,progress_nama_buku_3,progress_nama_buku_4,progress_hal_buku_1,progress_hal_buku_2,progress_hal_buku_3,progress_hal_buku_4,progress_total_hal_1,progress_total_hal_2,progress_total_hal_3,progress_total_hal_4,progress_guru_id,progress_created,progress_updated,progress_active";
    public $WSHalBuku = "progress_nama_buku_1,progress_nama_buku_2,progress_nama_buku_3,progress_nama_buku_4,progress_hal_buku_1,progress_hal_buku_2,progress_hal_buku_3,progress_hal_buku_4,progress_total_hal_1,progress_total_hal_2,progress_total_hal_3,progress_total_hal_4";

    public function isProgressCreated($kode_siswa, $progress_level, $date)
    {
        $this->getWhereOne("kode_siswa='$kode_siswa' AND progress_level=$progress_level AND cast(`progress_created`as date)='$date'");
        if (!is_null($this->progress_id)) {
            return true;
        }
        return false;
    }

    public function getProgressByDate($kode_siswa, $progress_level, $date)
    {
        $this->getWhereOne("kode_siswa='$kode_siswa' AND progress_level=$progress_level AND cast(`progress_created`as date)='$date' ");
        if (!is_null($this->progress_id)) {
            return $this;
        }
        return null;
    }


    public function createProgress($kode_siswa, $progress_level, $progress_guru_id)
    {
        $this->kode_siswa = $kode_siswa;
        $this->progress_level = $progress_level;
        $this->progress_guru_id = $progress_guru_id;
        $this->progress_created = leap_mysqldate();
        $this->progress_updated = leap_mysqldate();
        $this->progress_active = 1;

        $objMurid = new MuridModel();
        $objMurid->getWhereOne("kode_siswa='$kode_siswa' AND id_level_sekarang='$progress_level'");
        pr($progress_level);
        $buku = new BarangWebModel();
        $arrBuku = $buku->getHalBuku($progress_level, $objMurid->murid_kurikulum);
        pr($kode_siswa . " - " . $arrBuku);
        if ($arrBuku != null) {
            foreach ($arrBuku as $val) {
                $i = 1;
                foreach ($val as $jenisBuku => $hal) {
                    $b = "progress_total_hal_" . $i;
                    $c = "progress_nama_buku_" . $i;
                    $this->$c = $jenisBuku;
                    $this->$b = $hal;
                    $i++;
                }

            }
            $this->save();
        }


    }


    public function listProgressByDate($kode_siswa, $progress_level)
    {
        $this->getWhereOne("kode_siswa='$kode_siswa' AND progress_level=$progress_level ORDER BY progress_created DESC");
        $arrWS = explode(",", $this->crud_webservice_allowed);
        $arrHlp = array();
        foreach ($arrWS as $val) {
            $arrHlp[$val] = $this->$val;
        }
        return $arrHlp;
    }

    /*
     * return true => punya progress
     * return false => tidak punya progress
     */
    public function hasMuridProgress($kode_siswa, $level)
    {
        $this->getWhereOne("kode_siswa='$kode_siswa' AND progress_level=$level");
        if (is_null($this->progress_id)) {
            return false;
        }
        return true;
    }

    public function getMuridProgressByDate($kode_siswa, $level, $date)
    {
        //select DATE_FORMAT(`progress_updated`,"%Y-%m-%d") as a From sempoa__app_progress
        $this->getWhereOne("kode_siswa='$kode_siswa' AND progress_level='$level' AND DATE_FORMAT(`progress_updated`,\"%Y-%m-%d\") = '$date'");
        if (is_null($this->progress_id)) {
            return null;
        }
        return $this;
    }

    public function getMuridLastProgress($kode_siswa, $level)
    {
        //
        $this->getWhereOne("kode_siswa='$kode_siswa' AND progress_level='$level' ORDER BY progress_updated DESC");
        if (is_null($this->progress_id)) {
            return null;
        }
        return $this;
    }

    public function createProgress_backup($kode_siswa, $progress_level, $progress_guru_id, $date)
    {
        if (!$this->isProgressCreated($kode_siswa, $progress_level, $date)) {

            $this->kode_siswa = $kode_siswa;
            $this->progress_level = $progress_level;
            $this->progress_guru_id = $progress_guru_id;
            $this->progress_created = leap_mysqldate();
            $this->progress_updated = leap_mysqldate();
            $this->progress_active = 1;

            $objMurid = new MuridModel();
            $objMurid->getWhereOne("kode_siswa='$kode_siswa' AND id_level_sekarang='$progress_level'");

            $buku = new BarangWebModel();
            $arrBuku = $buku->getHalBuku($progress_level, $objMurid->murid_kurikulum);
//            pr($arrBuku);
            if ($arrBuku != null) {
                foreach ($arrBuku as $val) {
                    $i = 1;
                    foreach ($val as $jenisBuku => $hal) {
                        $b = "progress_total_hal_" . $i;
                        $c = "progress_nama_buku_" . $i;
                        $this->$c = $jenisBuku;
                        $this->$b = $hal;
                        $i++;
                    }

                }
                $this->save();
                return $this;
            }

        } else {

            return $this->getProgressByDate($kode_siswa, $progress_level, $date);
        }

    }

}