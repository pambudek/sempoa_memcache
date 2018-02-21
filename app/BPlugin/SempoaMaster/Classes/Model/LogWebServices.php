<?php

/**
 * Created by PhpStorm.
 * User: efindiongso
 * Date: 21/02/18
 * Time: 12.26
 */
class LogWebServices extends  Model
{

    var $table_name = "log__webservices";
    var $main_id = "log_id";

//Default Coloms for read
    public $default_read_coloms = "log_id,log_execute_start,log_nama_fungsi,log_tanggung_jawab,log_execute_end,log_status,log_ip,log_browser";

//allowed colom in CRUD filter
    public $coloumlist = "log_id,log_execute_start,log_nama_fungsi,log_tanggung_jawab,log_execute_end,log_status,log_ip,log_browser";
    public $log_id;
    public $log_execute_start;
    public $log_nama_fungsi;
    public $log_tanggung_jawab;
    public $log_execute_end;
    public $log_status;
    public $log_ip;
    public $log_browser;



    public function __construct(){

    }

    /**
     * LogWebServices constructor.
     * @param string $table_name
     * @param string $main_id
     * @param string $default_read_coloms
     * @param string $coloumlist
     * @param $log_id
     * @param $log_execute_start
     * @param $log_nama_fungsi
     * @param $log_tanggung_jawab
     * @param $log_execute_end
     * @param $log_status
     * @param $log_ip
     * @param $log_browser
     */

    public function startLog($log_tanggung_jawab,$log_ip,$log_browser){
        $this->log_execute_start = leap_mysqldate();
        $this->log_tanggung_jawab = $log_tanggung_jawab;
        $this->log_status =0;
        $this->log_ip = $log_ip;
        $this->log_browser = $log_browser;
        $this->save();
    }

    public function logFunction($log_nama_fungsi, $log_status){
        $this->log_nama_fungsi = $log_nama_fungsi;
        $this->log_status = $log_status;
        $this->save(1);
    }

    public function endLog(){
        $this->log_execute_end = leap_mysqldate();
//        $this->save(1);$this-> //TODO exec time kuraingn
        $this->save(1);

    }

    /**
     * @return mixed
     */
    public function getLogBrowser()
    {
        return $this->log_browser;
    }

    /**
     * @return mixed
     */
    public function getLogIp()
    {
        return $this->log_ip;
    }

    /**
     * @return mixed
     */
    public function getLogStatus()
    {
        return $this->log_status;
    }

    /**
     * @return mixed
     */
    public function getLogExecuteEnd()
    {
        return $this->log_execute_end;
    }

    /**
     * @return mixed
     */
    public function getLogTanggungJawab()
    {
        return $this->log_tanggung_jawab;
    }

    /**
     * @return mixed
     */
    public function getLogNamaFungsi()
    {
        return $this->log_nama_fungsi;
    }

    /**
     * @return mixed
     */
    public function getLogExecuteStart()
    {
        return $this->log_execute_start;
    }


}