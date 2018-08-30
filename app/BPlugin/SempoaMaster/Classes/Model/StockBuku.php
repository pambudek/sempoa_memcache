<?php

/**
 * Created by PhpStorm.
 * User: efindiongso
 * Date: 8/3/17
 * Time: 10:27 AM
 */
class StockBuku extends Model
{
    var $table_name = "sempoa__stock_buku";
    var $main_id = "stock_buku_id";
    public $crud_setting = array("add" => 1, "search" => 1, "viewall" => 1, "export" => 1, "toggle" => 1, "import" => 1, "webservice" => 1);

//Default Coloms for read
    public $default_read_coloms = "stock_buku_id,stock_buku_no,stock_name_buku,stock_id_buku,stock_grup_level,stock_buku_status_kpo,stock_status_ibo,stock_status_tc,stock_murid,stock_buku_tgl_masuk_kpo,stock_buku_tgl_keluar_kpo,stock_buku_tgl_masuk_ibo,stock_buku_tgl_keluar_ibo,stock_buku_tgl_masuk_tc,stock_buku_tgl_keluar_tc,stock_buku_tgl_status_rusak,stock_po_pesanan_ibo,stock_po_pesanan_tc,stock_invoice_murid,stock_buku_kpo,stock_buku_ibo,stock_buku_tc,stock_murid_id";

//allowed colom in CRUD filter
    public $coloumlist = "stock_buku_id,stock_buku_no,stock_name_buku,stock_id_buku,stock_grup_level,stock_buku_status_kpo,stock_status_ibo,stock_status_tc,stock_murid,stock_buku_tgl_masuk_kpo,stock_buku_tgl_keluar_kpo,stock_buku_tgl_masuk_ibo,stock_buku_tgl_keluar_ibo,stock_buku_tgl_masuk_tc,stock_buku_tgl_keluar_tc,stock_buku_tgl_status_rusak,stock_po_pesanan_ibo,stock_po_pesanan_tc,stock_invoice_murid,stock_buku_kpo,stock_buku_ibo,stock_buku_tc,stock_murid_id";
    public $stock_buku_id;
    public $stock_buku_no;
    public $stock_name_buku;
    public $stock_id_buku;
    public $stock_grup_level;
    public $stock_buku_status_kpo;
    public $stock_status_ibo;
    public $stock_status_tc;
    public $stock_murid;
    public $stock_buku_tgl_masuk_kpo;
    public $stock_buku_tgl_keluar_kpo;
    public $stock_buku_tgl_masuk_ibo;
    public $stock_buku_tgl_keluar_ibo;
    public $stock_buku_tgl_masuk_tc;
    public $stock_buku_tgl_keluar_tc;
    public $stock_buku_tgl_status_rusak;
    public $stock_po_pesanan_ibo;
    public $stock_po_pesanan_tc;
    public $stock_invoice_murid;
    public $stock_buku_kpo;
    public $stock_buku_ibo;
    public $stock_buku_tc;
    public $stock_murid_id;

    public function getMyLastNummer($id_barang)
    {
        $this->getWhereOne("stock_id_buku=$id_barang ORDER by stock_buku_id DESC");
        if ((is_null($this->stock_buku_id))) {
            // ambil 3digit pertama
            $barang = new BarangWebModel();
            $barang->getByID($id_barang);
            if (!(is_null($barang->id_barang_harga))) {
                if ($barang->no_buku == "") {
                    return "0";
                } else {
                    return $barang->no_buku . "00001";
                }

            } else {
                return "0";
            }

        } else {
            $lastnummer = Generic::getLastNomorBuku($this->stock_buku_no);
            return $lastnummer;
        }

    }

    public function createNoBuku($id_barang, $no_buku, $kpo, $name_barang)
    {
        $level = Generic::getLevelIdByIdBarang($id_barang);
        $a = new $this();
        $a->stock_id_buku = $id_barang;
        $a->stock_buku_no = $no_buku;
        $a->stock_buku_status_kpo = 1;
        $a->stock_buku_kpo = $kpo;
        $a->stock_buku_tgl_masuk_kpo = leap_mysqldate();
        $a->stock_grup_level = $level;
        $a->stock_name_buku = $name_barang;
        $a->save();

    }

    public function getBukuYgdReservMurid($level, $org_id_pemilik, $id_murid, $kurikulum, $jenis_biaya)
    {

        $arrIdBarang = Generic::getIdBarangByLevelDanJenisBiaya($level, $kurikulum, $jenis_biaya);
        $res = array();
        foreach ($arrIdBarang as $val) {
            $buno = new StockBuku();
//            $buno->getWhereOne("stock_id_buku=$val AND stock_status_tc=1 AND stock_buku_tc=$org_id_pemilik ORDER BY stock_buku_id ASC");
            $buno->getWhereOne("stock_id_buku=$val AND stock_status_tc=1 AND stock_buku_tc=$org_id_pemilik ORDER BY stock_buku_no ASC");

            if (is_null($buno->stock_buku_id)) {

            } else {
                $res[] = $buno->stock_buku_id;
            }
        }
        return $res;
    }

    public function setStatusBuku($res, $id_murid, $inv_bln_id)
    {

        if (count($res) == 0) {
            return false;
        } else {
            foreach ($res as $val) {
                $buno = new $this();
                $buno->getbyid($val);
                $buno->stock_status_tc = 0;
                $buno->stock_buku_tgl_keluar_tc = leap_mysqldate();
                $buno->stock_murid_id = $id_murid;
                $buno->stock_murid = 1;
                $buno->stock_invoice_murid = $inv_bln_id;
                $buno->save(1);

            }
            return true;
        }

    }

    public function getBarangIDbyPk($stock_buku_id)
    {
        $buno = new $this();
        $buno->getbyid($stock_buku_id);
        return $buno->stock_id_buku;
    }

    public function checkJumlahBarangByLevel($level, $org_id, $orgType)
    {
        $buno = new StockBuku();
        if ($orgType == KEY::$KPO) {

        } elseif ($orgType == KEY::$IBO) {

        } elseif ($orgType == KEY::$TC) {
            $buno->getWhere("stock_status_tc=1 AND stock_grup_level=$level AND stock_buku_tc=$org_id");
        }
    }

    public function getNoBukuById($id)
    {
        $this->getByID($id);
        return $this->stock_buku_no;

    }

    public function getBukuNoByStudentID($student_id)
    {
        $buno = new $this();
        $arrStudent = $buno->getwhere("stock_murid_id=$student_id ORDER by stock_grup_level DESC");
        return $arrStudent;
    }

    public function getBukuNoByInvoiceID($stock_invoice_murid)
    {
        $res = $this->getWhere("stock_invoice_murid='$stock_invoice_murid'");
        $erg = array();
        foreach($res as $val){
            $erg[$val->stock_buku_no] = $val->stock_name_buku;
        }

        return $erg;
    }

    public function retourBukuMurid($invoice_id)
    {
        $this->getWhereOne("stock_murid=1 AND stock_invoice_murid='$invoice_id'");

        if (!is_null($this->stock_buku_id)) {
            $this->stock_status_tc = 1;
            $this->stock_murid = 0;
            $this->stock_invoice_murid = "";
            $this->stock_murid_id = "";
            $this->save(1);

        }
    }

    public function getNamaBukuByNoBuku($nobuku)
    {
        $this->getWhereOne("stock_buku_no='$nobuku'");
        return $this->stock_name_buku;
    }

    public function getNoBukuTerkecilByLevelYgAvail($org_type, $org_id_claim, $level, $id_buku)
    {
        if ($org_type == KEY::$KPO) {
            $this->getWhereOne("stock_buku_kpo= $org_id_claim AND stock_id_buku=$id_buku AND stock_grup_level = $level AND stock_buku_status_kpo=1 ORDER BY stock_buku_id ASC ");
            return $this->stock_buku_no;
        } elseif ($org_type == KEY::$IBO) {
            $this->getWhereOne("stock_buku_ibo= $org_id_claim AND stock_id_buku=$id_buku AND stock_grup_level = $level AND stock_status_ibo=1 ORDER BY stock_buku_id ASC ");
            return $this->stock_buku_no;

        } elseif ($org_type == KEY::$TC) {
            $this->getWhereOne("stock_buku_tc= $org_id_claim AND stock_id_buku=$id_buku AND stock_grup_level = $level AND stock_status_tc=1 ORDER BY stock_buku_id ASC ");
            return $this->stock_buku_no;

        }
    }

    public function retourBukuKePeminta($org_type, $org_id_pengclaim, $org_id_claim, $no_buku)
    {

        if ($org_type == KEY::$KPO) {
            $this->getWhereOne("stock_buku_kpo= $org_id_claim AND stock_buku_no=$no_buku  AND stock_status_kpo=1 ORDER BY stock_buku_id ASC ");
            $this->stock_buku_ibo = $org_id_pengclaim;
            $this->stock_buku_tgl_masuk_ibo = leap_mysqldate();
            $this->stock_buku_status_kpo = 0;
            $this->stock_status_ibo = 1;
            $this->stock_status_tc = 0;
            $this->save(1);
        } elseif ($org_type == KEY::$IBO) {
            $this->getWhereOne("stock_buku_ibo= $org_id_claim AND stock_buku_no=$no_buku  AND stock_status_ibo=1 ORDER BY stock_buku_id ASC ");
            $this->stock_buku_tc = $org_id_pengclaim;
            $this->stock_buku_tgl_masuk_tc = leap_mysqldate();
            $this->stock_buku_tgl_keluar_ibo = leap_mysqldate();
            $this->stock_buku_tgl_masuk_kpo = leap_mysqldate();
            $this->stock_status_kpo = 0;
            $this->stock_status_ibo = 0;
            $this->stock_status_tc = 1;
            $this->save(1);
        } elseif ($org_type == KEY::$TC) {
            $this->getWhereOne("stock_buku_tc= $org_id_claim AND stock_buku_no=$no_buku  AND stock_status_tc=1 ORDER BY stock_buku_id ASC ");
            $this->stock_murid_id = $org_id_pengclaim;
            $this->stock_buku_tgl_keluar_tc = leap_mysqldate();
            $this->stock_buku_tgl_masuk_murid = leap_mysqldate();
            $this->stock_status_kpo = 0;
            $this->stock_status_ibo = 0;
            $this->stock_status_tc = 0;
            $this->stock_murid = 1;
            $this->save(1);
        }
    }


    public function retourBukuKePeminta_($org_type, $org_id_pengclaim, $org_id_claim, $level, $id_buku)
    {

        if ($org_type == KEY::$KPO) {

        } elseif ($org_type == KEY::$IBO) {
//            public $stock_id_buku;
//            public $stock_grup_level;
            $this->getWhereOne("stock_buku_ibo= $org_id_claim AND stock_id_buku=$id_buku AND stock_grup_level = $level AND stock_status_ibo=0 ORDER BY stock_buku_id ASC ");
            $this->stock_buku_tc = $org_id_pengclaim;
            $this->stock_buku_tgl_keluar_ibo = leap_mysqldate();
            $this->stock_buku_tgl_masuk_tc = leap_mysqldate();
            $this->stock_status_ibo = 0;
            $this->stock_status_tc = 1;
            $this->save(1);
        } elseif ($org_type == KEY::$TC) {

        }
    }

    public function getAllMyNoBukuFreiTCByInvoiceId($level,$tc_id, $kurikulum){

        $brg = new BarangWebModel();
//        $id_buku =$brg->getIdBarangByLevelKurikulum($level,$kurikulum);
//        $arrNoBuku = $this->getWhere("stock_buku_tc='$tc_id' AND stock_status_tc='1' AND stock_grup_level='$level' AND stock_id_buku='$id_buku'");
        $id_buku =$brg->getIdBarangByLevelKurikulum($level,$kurikulum);
        $arrNoBuku = $this->getWhere("stock_buku_tc='$tc_id' AND stock_status_tc='1' AND stock_grup_level='$level'");

        $arrResult = array();
        foreach($arrNoBuku as $val){
            $arrResult[] = $val->stock_buku_no;
        }
        return $arrResult;
    }
}