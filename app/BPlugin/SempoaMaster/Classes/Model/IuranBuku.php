<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of IuranBuku
 *
 * @author efindiongso
 */
class IuranBuku extends Model
{
    var $table_name = "sempoa__iuran_buku";
    var $main_id = "bln_id";

    //Default Coloms for read
    public $default_read_coloms = "bln_id,bln_murid_id,bln_buku_level,bln_date_pembayaran,bln_date,bln_mon,bln_tahun,bln_status,bln_tc_id,bln_ibo_id,bln_kpo_id,bln_ak_id,bln_cara_bayar,bln_no_urut_inv,bln_no_invoice,bln_kur,bln_ganti_kur";

//allowed colom in CRUD filter
    public $coloumlist = "bln_id,bln_murid_id,bln_buku_level,bln_date_pembayaran,bln_date,bln_mon,bln_tahun,bln_status,bln_tc_id,bln_ibo_id,bln_kpo_id,bln_ak_id,bln_cara_bayar,bln_no_urut_inv,bln_no_invoice,bln_kur,bln_ganti_kur";
    public $bln_id;
    public $bln_murid_id;
    public $bln_buku_level;
    public $bln_date_pembayaran;
    public $bln_date;
    public $bln_mon;
    public $bln_tahun;
    public $bln_status;
    public $bln_tc_id;
    public $bln_ibo_id;
    public $bln_kpo_id;
    public $bln_ak_id;
    public $bln_cara_bayar;
    public $bln_no_urut_inv;
    public $bln_no_invoice;
    public $bln_kur;
    public $bln_ganti_kur;
    public $bln_invoice_type;

    function getLastNoUrutInvoice($thn, $bln, $tc_id)
    {
        $nourut = new $this();
        $nourut->getWhereOne("bln_tc_id=$tc_id AND MONTH(bln_date_pembayaran)=$bln AND YEAR((bln_date_pembayaran))=$thn AND bln_no_urut_inv != 0 ORDER BY bln_date_pembayaran DESC");
        $urut = $nourut->bln_no_urut_inv + 1;
        return $urut;
    }
}
