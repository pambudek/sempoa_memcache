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

    public static function getMyPreviousLevel($myLevel)
    {
        $objLevel = new SempoaLevel();
        $arrAll = $objLevel->getAll();
        foreach ($arrAll as $key => $level) {
            if ($level->id_level == $myLevel) {
                $keymylevel = $key;
                break;
            }
        }
        return $keymylevel;
    }



    public static function getMyPreviousLevelLama($myLevel)
    {
        $objLevel = new SempoaLevelLama();
        $arrAll = $objLevel->getAll();
        $keymylevel = "";
        foreach ($arrAll as $key => $level) {
            if ($level->id_level_lama == $myLevel) {
                $keymylevel = $key;
                break;
            }
        }
        return $keymylevel;
    }

}