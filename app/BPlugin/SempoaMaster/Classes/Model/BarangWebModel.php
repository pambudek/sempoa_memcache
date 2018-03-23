<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BarangWebModel
 *
 * @author efindiongso
 */
class BarangWebModel extends SempoaModel
{

    //put your code here
    var $table_name = "sempoa__barang_harga";
    var $main_id = "id_barang_harga";
//Default Coloms for read
    public $default_read_coloms = "id_barang_harga,kode_barang,nama_barang,jenis_biaya,jenis_kurikulum,level,no_buku,halaman_buku,foto_barang,barang_desc,ak_id,kpo_id,ak_rule,kpo_rule,ibo_rule,tc_rule";
//allowed colom in CRUD filter
    public $coloumlist = "id_barang_harga,kode_barang,nama_barang,jenis_biaya,jenis_kurikulum,level,no_buku,halaman_buku,foto_barang,barang_desc,ak_id,kpo_id,ak_rule,kpo_rule,ibo_rule,tc_rule";
    public $id_barang_harga;
    public $kode_barang;
    public $nama_barang;
    public $jenis_biaya;
    public $jenis_kurikulum;
    public $level;
    public $no_buku;
    public $halaman_buku;
    public $foto_barang;
    public $barang_desc;
    public $ak_id;
    public $kpo_id;
    public $ak_rule;
    public $kpo_rule;
    public $ibo_rule;
    public $tc_rule;
    public $hideColoums = array("ak_id", "kpo_id");

    public function overwriteForm($return, $returnfull)
    {
        $return = parent::overwriteForm($return, $returnfull);
        $arrKurikulum = Generic::getJenisKurikulum();

        if ($this->foto_barang == "") {
            $this->foto_barang = "noimageperson.png";
        }
        $return['jenis_biaya'] = new Leap\View\InputSelect(Generic::getJenisBiaya(), "jenis_biaya", "jenis_biaya", $this->jenis_biaya);
        $webservice = _SPPATH . "BarangWebHelper2/getLevel";
//        $return['jenis_kurikulum'] = new Leap\View\InputSelect($arrKurikulum, "jenis_kurikulum", "jenis_kurikulum", $this->jenis_kurikulum);
        $return['jenis_kurikulum'] = new Leap\View\InputSelectOnChange($arrKurikulum, "jenis_kurikulum", "jenis_kurikulum", $this->jenis_kurikulum, "level", $webservice);


        if ($this->jenis_kurikulum == 0) {
            $arrLevelKurBaru = Generic::getAllLevel();
            $return['level'] = new Leap\View\InputSelect($arrLevelKurBaru, "level", "level", $this->level);
        } else {

            $arrLevelKurLama = Generic::getLevelKurikulumLama();
            $return['level'] = new Leap\View\InputSelect($arrLevelKurLama, "level", "level", $this->level);
        }


        $return['foto_barang'] = new \Leap\View\InputFoto("foto", "foto_barang", $this->foto_barang);

        $return['ak_id'] = new \Leap\View\InputText("hidden", "ak_id", "ak_id", Generic::getMyParentID(AccessRight::getMyOrgID()));
        $return['kpo_id'] = new \Leap\View\InputText("hidden", "kpo_id", "kpo_id", AccessRight::getMyOrgID());


        return $return;
    }

    public function overwriteRead($return)
    {
        $objs = $return['objs'];
        $arrJenisBarang = Generic::getJenisBarang();
        $arrLevel = Generic::getAllLevel();
        $arrKurikulum = Generic::getJenisKurikulum();
        $arrLevelKurLama = Generic::getLevelKurikulumLama();
        foreach ($objs as $obj) {
            if (isset($obj->foto_barang)) {

                if ($obj->foto_barang == "") {
                    $obj->foto_barang = "noimageperson.png";
                }
                $obj->foto_barang = \Leap\View\InputFoto::getAndMakeFoto($obj->foto_barang, "car_image_" . $obj->foto_barang);
            }
            if (isset($obj->jenis_biaya)) {
                $obj->jenis_biaya = $arrJenisBarang[$obj->jenis_biaya];
            }

            if (isset($obj->jenis_kurikulum)) {
                if ($obj->jenis_kurikulum == 0) {
                    $obj->level = $arrLevel[$obj->level];
                } elseif ($obj->jenis_kurikulum == 1) {
                    $obj->level = $arrLevelKurLama[$obj->level];
                }
            }

            if (isset($obj->jenis_kurikulum)) {
                $obj->jenis_kurikulum = $arrKurikulum[$obj->jenis_kurikulum];


            }


        }
        return $return;
    }

    public function getPerlengkapanJunior($kpo_id)
    {
        $this->getWhereOne("kpo_id=$kpo_id AND jenis_biaya = 2 AND (level=1 OR level=2)");
        return $this->id_barang_harga;
    }

    public function getPerlengkapanFoundation($kpo_id)
    {
        $this->getWhereOne("kpo_id=$kpo_id AND jenis_biaya = 2 AND level=3");
        return $this->id_barang_harga;
    }

    public function getMyBookNo($id_barang)
    {
        $this->getWhereOne("id_barang_harga=$id_barang");
        if (!is_null($this->id_barang_harga)) {
            return $this->no_buku;
        } else {
            return "";
        }
    }

    public function getNamaBukuByID($id)
    {
        $this->getWhereOne("id_barang_harga=$id");
        return $this->nama_barang;
    }

    public function getNamaBukuByNoBuku($awalannobuku)
    {
        $this->getWhereOne("no_buku=$awalannobuku");
        return $this->nama_barang;
    }

    public function getStockByIdJenisBarang($idJenisBarang)
    {
        $arrStock = $this->getWhere("jenis_biaya=$idJenisBarang");
        $res = array();
        foreach ($arrStock as $val) {
            $res[$val->id_barang_harga] = $val->nama_barang;
        }
        return $res;
    }

    public function getHalBuku($level, $kurikulum)
    {
        $this->getWhereOne("level=$level AND jenis_kurikulum=$kurikulum AND jenis_biaya=1");
        if (!is_null($this->id_barang_harga)) {
            $halBukuTotal = $this->halaman_buku;
            $halBuku = \GuzzleHttp\json_decode($halBukuTotal);

            return $halBuku;
        }
        return null;

    }

    public function getIdBarangByLevelKurikulum($level, $kurikulum)
    {
        $this->getWhereOne("jenis_kurikulum='$kurikulum' AND level='$level'");
        return $this->id_barang_harga;
    }
}
