<?php

/**
 * Created by PhpStorm.
 * User: efindiongso
 * Date: 1/17/18
 * Time: 3:17 PM
 */
class SoalChallange extends WebService
{

    function delete_soal_challange()
    {

    }

    function create_soal_challange()
    {

        $_GET['cmd'] = 'edit';
        $this->read_soal_challange();
    }

    function update_soal_challange()
    {

    }

    function read_soal_challange()
    {
        $obj = new SoalChallangeModel();
        $crud = new CrudCustom();
        $crud->ar_add = AccessRight::hasRight("create_soal_challange");
        $crud->ar_edit = AccessRight::hasRight("update_soal_challange");
        $crud->ar_delete = AccessRight::hasRight("delete_soal_challange");
        $crud->run_custom($obj, "SoalChallange", "read_soal_challange");
    }



}