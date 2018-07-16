<?php

/**
 * Created by PhpStorm.
 * User: efindiongso
 * Date: 05/07/18
 * Time: 04.08
 */
class MuridWebHelperExt extends WebService
{

    function setStatusCutiAktiv(){
        $id_murid = addslashes($_POST['id_murid']);
        $monthAktiv = addslashes($_POST['monthAktiv']);
        $bln_skrg =  date("n");
        $monthAktiv_hlp = substr($monthAktiv, strpos($monthAktiv, '-')+1);
        $murid = new MuridModel();
        $murid->getByID($id_murid);
        if (!is_null($murid->id_murid)) {

            if($monthAktiv_hlp == $bln_skrg){
                $murid->status = KEY::$KEY_STATUS_AKTIV;
            }
            else{
                $murid->murid_date_cuti_aktiv = $monthAktiv . "-" . "01";
            }


            $murid->save(1);
            $json['monthAktiv'] = $monthAktiv;
            $json['status_code'] = 1;
            $json['status_message'] = "Ok";
            echo json_encode($json);
            die();
        }

        $json['status_code'] = 0;
        $json['status_message'] = "Status gagal diganti";
        echo json_encode($json);
        die();
    }
}