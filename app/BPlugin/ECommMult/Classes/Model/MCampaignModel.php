<?php
/**
 * Created by PhpStorm.
 * User: elroy
 * Date: 12/3/15
 * Time: 1:49 PM
 */

class MCampaignModel extends Model{

    //Nama Table
    public $table_name = "ecommultiple__campaign";

    //Primary
    public $main_id = 'camp_id';

    //Default Coloms for read
    public $default_read_coloms = 'camp_id,camp_pic,camp_name,camp_descr,camp_active,camp_type,camp_app_id';



    //allowed colom in CRUD filter
    public $coloumlist = 'camp_id,camp_pic,camp_name,camp_begin,camp_end,camp_descr,camp_active,camp_type,camp_app_id,camp_updatedate';

    public $camp_id;
    public $camp_name;
    public $camp_begin;
    public $camp_end;
    public $camp_descr;
    public $camp_active;
    public $camp_pic;
    public $camp_type;
    public $camp_app_id;
    public $camp_updatedate;


    public $ratio_weight = 16;
    public $ratio_height = 10;
    public $arrtype = array("carousel"=>"Carousel","campaign"=>"Campaign");


    public $hideColoums = array("camp_app_id");

    public $crud_setting = array("add"=>1,"search"=>1,"viewall"=>0,"export"=>0,"toggle"=>1,"import"=>0,"webservice"=>0);


    public $crud_webservice_allowed = 'camp_id,camp_pic,camp_name,camp_descr,camp_active,camp_type,camp_app_id';
    public $crud_add_photourl = array("camp_pic");
    //untuk app
//    public $multi_user = 1;
//    public $multi_user_field = "camp_app_id";

    public function __construct(){
//        echo AppAccount::getAppID();
        $arrs = array("camp_app_id"=>AppAccount::getAppID());
        $this->read_filter_array = $arrs;
//        pr($this->read_filter_array);
    }

    public function overwriteForm ($return, $returnfull)
    {
        $return  = parent::overwriteForm($return, $returnfull);

        $return['camp_begin'] = new Leap\View\InputText("date", "camp_begin", "camp_begin",
            $this->camp_begin);
        $return['camp_end'] = new Leap\View\InputText("date", "camp_end", "camp_end",
            $this->camp_end);
        $return['camp_descr'] = new Leap\View\InputTextArea("camp_descr", "camp_descr",
            $this->camp_descr);
        $return['camp_active'] = new Leap\View\InputSelect($this->arrayYesNO,"camp_active", "camp_active",
            $this->camp_active);


        $return['camp_pic'] = new Leap\View\InputFotoCropper($this->ratio_weight.":".$this->ratio_height,"camp_pic", "camp_pic",
            $this->camp_pic);

        $return['camp_app_id'] = new Leap\View\InputText("hidden", "camp_app_id", "camp_app_id",
            $this->camp_app_id);
        $return['camp_updatedate'] = new Leap\View\InputText("hidden", "camp_updatedate", "camp_updatedate",
            $this->camp_updatedate);


        $return['camp_type'] = new Leap\View\InputSelect($this->arrtype,"camp_type", "camp_type",
            $this->camp_type);
        return $return;
    }
    public function overwriteRead ($return)
    {
        $return = parent::overwriteRead($return);

        $objs = $return['objs'];
        foreach ($objs as $obj) {

            if (isset($obj->camp_pic)) {
                $obj->camp_pic = \Leap\View\InputFoto::getAndMakeFoto($obj->camp_pic, "camp2_image_" . $obj->camp_id);
            }

            if (isset($obj->camp_active)) {
                $obj->camp_active = $this->arrayYesNO[$obj->camp_active];
            }
            if (isset($obj->camp_type)) {
                $obj->camp_type = $this->arrtype[$obj->camp_type];
            }
        }
        return $return;
    }
    public function constraints ()
    {
        //err id => err msg
        $err = array ();

        if (!isset($this->camp_pic)) {
            $err['camp_pic'] = Lang::t('Picture must be provided');
        }
        else{
            $src = _PHOTOPATH.$this->camp_pic;
            list($iWidth,$iHeight,$type)    = getimagesize($src);
            if(round($iWidth/$iHeight,1) != round($this->ratio_weight/$this->ratio_height,1)){
                $err['camp_pic'] = Lang::t('Proportion is not right, please crop using our tool');
            }
        }



        if (!isset($this->camp_name)) {
            $err['camp_name'] = Lang::t('Please provide carousel photo');

        }

        if (!isset($this->camp_descr)) {
            $err['camp_descr'] = Lang::t('Description cannot be empty');
        }

        if (!isset($this->camp_begin)) {
            $err['camp_begin'] = Lang::t('Validity cannot be empty');
        }

        if (!isset($this->camp_end)) {
            $err['camp_end'] = Lang::t('Validity cannot be empty');
        }

        $this->camp_app_id = AppAccount::getAppID();
        $this->camp_updatedate = leap_mysqldate();


        //cek apakah paket sesuai dengan yg diperbolehkan
        $app = AppAccount::getActiveAppObject();

        //basic
        $type = $this->arrtype[$this->camp_type];
        //carousel 1_5
        $carousel_id = $app->app_paket_id."_5";
        $banner_id = $app->app_paket_id."_6";

        $mm = new PaketMatrix();

        if($type == "Carousel") {
            $mm->getByID($carousel_id);

        }
        if($type == "Campaign") {
            $mm->getByID($banner_id);
        }
        $limit = $mm->ps_isi;

        //get all campaign dengan app_id dan type yg diperbolehkan
        $nr = $this->getJumlah("camp_app_id = '{$this->camp_app_id}' AND camp_active = 1 AND camp_type = '{$this->camp_type}'");

        if($this->camp_active)
        if($nr>=$limit){
            $err['camp_active'] = Lang::t('Too many campaign active for '.$type.', please deactivate the others. Limit is '.$limit);
        }



        return $err;
    }
} 