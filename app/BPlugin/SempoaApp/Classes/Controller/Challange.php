<?php

/**
 * Created by PhpStorm.
 * User: efindiongso
 * Date: 13/02/18
 * Time: 15.09
 */
class Challange extends WebService
{

    function create_challange(){
        $_GET['cmd'] = 'edit';
        $this->read_challange();
    }

    function read_challange(){
        $ibo_id = AccessRight::getMyOrgID();
        $obj = new ChallangeModel();
        $crud = new CrudCustomSempoa();
        $crud->ar_add = AccessRight::hasRight("create_challange");
        $crud->ar_edit = AccessRight::hasRight("update_challange");
        $crud->ar_delete = AccessRight::hasRight("delete_challange");
        $crud->run_custom($obj, "Challange", "read_challange","challange_ibo='$ibo_id'");

//        $crud->run_custom($obj, "GuruWeb2", "read_guru_tc_hlp", " guru_tc_id = '$myOrgID'");
    }

    function update_challange(){

    }

    function delete_challange(){

    }
}