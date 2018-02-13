<?php

/**
 * Created by PhpStorm.
 * User: efindiongso
 * Date: 1/23/18
 * Time: 1:34 PM
 */
class SempoaNotification extends Model
{

    var $table_name = "sempoa__app_notification";
    var $main_id = "notification_id";

//Default Coloms for read
    public $default_read_coloms = "notification_id,notification_belongs_id,notification_type,notification_title,notification_content,notification_created,notification_active,notification_updated";

//allowed colom in CRUD filter
    public $coloumlist = "notification_id,notification_belongs_id,notification_type,notification_title,notification_content,notification_created,notification_active,notification_updated";
    public $notification_id;
    public $notification_belongs_id;
    public $notification_type;
    public $notification_title;
    public $notification_content;
    public $notification_created;
    public $notification_active;
    public $notification_updated;

    public $crud_webservice_allowed =  "notification_id,notification_belongs_id,notification_type,notification_title,notification_content,notification_created,notification_updated";


    public function getMyNotif($my_id){
        $arrNotif = $this->getWhere("notification_belongs_id='$my_id' ORDER by notification_created DESC");
        return $arrNotif;
    }


    public function createNotif( $notification_belongs_id,$notification_type,$notification_title,$notification_content ){
        $this->notification_belongs_id = $notification_belongs_id;
        $this->notification_type = $notification_type;
        $this->notification_title = $notification_title;
        $this->notification_content = $notification_content;
        $this->notification_created = leap_mysqldate();
        $this->notification_updated = leap_mysqldate();
        $this->notification_active = 1;
        $this->save();
    }
}