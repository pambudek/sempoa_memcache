<?php

/**
 * Created by PhpStorm.
 * User: efindiongso
 * Date: 21/02/18
 * Time: 12.20
 */
class SempoaAuth extends WebService
{


    public static function isLoggedTransaksi ()
    {
        if (!isset($_SESSION["admin_session"])) {
            $json['status_code'] = 0;
            $json['status_message'] = "Session Anda sudah habis dan Anda harus login lagi!";
            echo json_encode($json);
            return false;


        }
        if ($_SESSION["admin_session"] == 1) {
            return true;
        }
        else {
            $json['status_code'] = 0;
            $json['status_message'] =  "Session Anda sudah habis dan Anda harus login lagi!";
            echo json_encode($json);
            return false;

        }

    }

}