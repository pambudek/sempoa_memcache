<?php

/**
 * Created by PhpStorm.
 * User: efindiongso
 * Date: 23/03/18
 * Time: 13.01
 */
class MuridController extends WebService
{

    public function setNoFirstPayment(){

        $murid_id = $_POST['murid_id'];

        if($murid_id == ""){
            $json['status_code'] = 0;
            $json['status_message'] = "Murid ID kosong, silahkan hubungin Admin Anda!";
            echo json_encode($json);
            die();
        }

        $murid = new MuridModel();
        $murid->getByID($murid_id);
        if(is_null($murid->id_murid)){
            $json['status_code'] = 0;
            $json['status_message'] = "Data Murid tidak ditemukan didalam System!";
            echo json_encode($json);
            die();
        }
        $murid->pay_firsttime = 1;
        $murid->no_pay_firsttime = 1;
        $murid->status = 1;
        $murid->save(1);
        $json['status_code'] = 1;
        $json['status_message'] = "First payment berhasil di set ke bayar!";
        echo json_encode($json);
        die();
    }
}