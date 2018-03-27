<?php

/**
 * Created by PhpStorm.
 * User: efindiongso
 * Date: 21/03/18
 * Time: 10.18
 */
class Generic2
{

    public static function getIDBukuByLevelKur($level, $kurikulum){
        $barang = new BarangWebModel();

    }

    static function getKodeSiswaByMuridId($murid_id){
        $murid = new MuridModel();
        $murid->getWhereOne("id_murid='$murid_id'");
        return $murid->kode_siswa;
    }
}