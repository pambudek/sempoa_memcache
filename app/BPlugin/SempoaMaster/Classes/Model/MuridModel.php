<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MuridModel
 *
 * @author efindiongso
 */
class MuridModel extends SempoaModel
{

    //put your code here
    var $table_name = "sempoa__siswa";
    var $main_id = "id_murid";
    public $default_read_coloms = "id_murid,kode_siswa,nama_siswa,jenis_kelamin,id_level_sekarang,pay_firsttime,murid_tc_id,status";
//allowed colom in CRUD filter
    public $coloumlist = "id_murid,kode_siswa,nama_siswa,jenis_kelamin,alamat,agama,tempat_lahir,tanggal_lahir,telepon,nama_sekolah,nama_ortu,gambar,tanggal_masuk,email_ortu,id_level_masuk,id_level_sekarang,nomor_pendaftaran,kode_guru,status,murid_ak_id,murid_kpo_id,murid_ibo_id,murid_tc_id,pay_firsttime";
    public $id_murid;
    public $kode_siswa;
    public $nama_siswa;
    public $jenis_kelamin;
    public $alamat;
    public $agama;
    public $tempat_lahir;
    public $tanggal_lahir;
    public $telepon;
    public $nama_sekolah;
    public $nama_ortu;
    public $gambar;
    public $tanggal_masuk;
    public $email_ortu;
    public $id_level_masuk;
    public $id_level_sekarang;
    public $nomor_pendaftaran;
    public $kode_guru;
    public $status;
    public $murid_kurikulum;
    public $murid_ak_id;
    public $murid_kpo_id;
    public $murid_ibo_id;
    public $murid_tc_id;
    public $pay_firsttime;
    public $no_pay_firsttime;
    public $murid_parent_id;
    public $murid_app_pwd;
    public $murid_created_date;
    public $murid_updated;
    public $murid_active;
    public $murid_date_cuti_aktiv;
    public $removeAutoCrudClick = array("pay_firsttime", "profile", "no_pay_firsttime");
    public $statushelp;
    public $hideColoums = array("murid_ak_id", "murid_kpo_id", "murid_ibo_id", "murid_kurikulum", "murid_parent_id","murid_date_cuti_aktiv");

    public $APPWS = "id_murid,kode_siswa,nama_siswa,jenis_kelamin,alamat,agama,tempat_lahir,id_level_sekarang,tanggal_lahir,murid_ak_id,murid_kpo_id,murid_ibo_id,murid_tc_id,murid_app_pwd";


    // Webservice
    public $crud_setting = array("add" => 1, "search" => 1, "viewall" => 0, "export" => 1, "toggle" => 1, "import" => 0, "webservice" => 0);
    public $crud_webservice_allowed = "id_murid,kode_siswa,nama_siswa,jenis_kelamin,alamat,agama,tempat_lahir,id_level_sekarang,tanggal_lahir,telepon,nama_sekolah,nama_ortu,gambar,email_ortu,murid_app_pwd";
    public $crud_add_photourl = array("gambar");

//    public $crud_read_gabungan="id_murid,kode_siswa,nama_siswa,jenis_kelamin,alamat,agama,tempat_lahir,tanggal_lahir,telepon,nama_sekolah,nama_ortu,gambar,tanggal_masuk,email_ortu,id_level_masuk,id_level_sekarang,nomor_pendaftaran,kode_guru,status,murid_ak_id,murid_kpo_id,murid_ibo_id,murid_tc_id,pay_firsttime";


    function setFieldMurid($id_murid, $murid_field, $murid_value)
    {
        $this->getWhereOne("id_murid='$id_murid''");
        $this->$murid_field = $murid_value;
        $this->save(1);
    }

    public function overwriteForm($return, $returnfull)
    {
        $t = time();


        $return = parent::overwriteForm($return, $returnfull);
        $myOrg = AccessRight::getMyOrgID();
        $myParentID = Generic::getMyParentID($myOrg);
        $myGrandParentID = Generic::getMyParentID($myParentID);
        $myRootID = Generic::getMyParentID($myGrandParentID);

        $arrStatusMurid = Generic::getAllStatusMurid();
        if (AccessRight::getMyOrgType() == "ak") {
            $return['murid_ak_id'] = new Leap\View\InputText("hidden", "murid_ak_id", "murid_ak_id", $myOrg);
        } else if (AccessRight::getMyOrgType() == "kpo") {
            $return['murid_ak_id'] = new Leap\View\InputText("hidden", "murid_ak_id", "murid_ak_id", $myParentID);
            $return['murid_kpo_id'] = new Leap\View\InputText("hidden", "murid_kpo_id", "murid_kpo_id", $myOrg);
            $return['murid_ibo_id'] = new Leap\View\InputText("hidden", "murid_ibo_id", "murid_ibo_id", $this->murid_ibo_id);
            $return['murid_tc_id'] = new Leap\View\InputText("hidden", "murid_tc_id", "murid_tc_id", $this->murid_tc_id);
        } else if (AccessRight::getMyOrgType() == "ibo") {
            $return['murid_ak_id'] = new Leap\View\InputText("hidden", "murid_ak_id", "murid_ak_id", $myGrandParentID);
            $return['murid_kpo_id'] = new Leap\View\InputText("hidden", "murid_kpo_id", "murid_kpo_id", $myParentID);
            $return['murid_ibo_id'] = new Leap\View\InputText("hidden", "murid_ibo_id", "murid_ibo_id", $myOrg);

            $arrTc = Generic::getAllMyTC(AccessRight::getMyOrgID());
            $return['murid_tc_id'] = new Leap\View\InputSelect($arrTc, "murid_tc_id", "murid_tc_id", $this->murid_tc_id);
        } else if (AccessRight::getMyOrgType() == "tc") {
            $return['murid_ak_id'] = new Leap\View\InputText("hidden", "murid_ak_id", "murid_ak_id", $myRootID);
            $return['murid_kpo_id'] = new Leap\View\InputText("hidden", "murid_kpo_id", "murid_kpo_id", $myGrandParentID);
            $return['murid_ibo_id'] = new Leap\View\InputText("hidden", "murid_ibo_id", "murid_ibo_id", $myParentID);
            $return['murid_tc_id'] = new Leap\View\InputText("hidden", "murid_tc_id", "murid_tc_id", $myOrg);
            if ($this->kode_siswa == "") {
                $return['kode_siswa'] = new Leap\View\InputText("text", "kode_siswa", "kode_siswa", Generic::fCreateKodeSiswa());
            } else {
                $return['kode_siswa'] = new Leap\View\InputText("text", "kode_siswa", "kode_siswa", $this->kode_siswa);
            }
            $return['murid_tc_id']->setReadOnly();
        }
        $return['kode_siswa']->setReadOnly();

        $return['status'] = new Leap\View\InputSelect($arrStatusMurid, "status", "status", $this->status);
        if ($this->status == "") {
            $return['status'] = new Leap\View\InputSelect(array("0" => "Non Aktiv"), "status", "status", $this->status);
        }

        $return['jenis_kelamin'] = new Leap\View\InputSelect(array("" => "Not Set", "m" => "Male", "f" => "Female"), "jenis_kelamin", "jenis_kelamin", strtolower($this->jenis_kelamin));
        $arrAgama = Generic::getAllAgama();
        $return['agama'] = new Leap\View\InputSelect($arrAgama, "agama", "agama", $this->agama);
        $return['kode_guru'] = new Leap\View\InputText("hidden", "kode_guru", "kode_guru", $this->kode_guru);
        $return['tanggal_lahir'] = new Leap\View\InputText("date", "tanggal_lahir", "tanggal_lahir", $this->tanggal_lahir);
        $return['tanggal_masuk'] = new Leap\View\InputText("date", "tanggal_masuk", "tanggal_masuk", $this->tanggal_masuk);
        $return['telepon'] = new Leap\View\InputText("text", "telepon", "telepon", $this->telepon);
        $return['gambar'] = new Leap\View\InputFoto("foto", "gambar", $this->gambar);

        $arrLevelTrainer = Generic::getAllLevel();
//        $arClassSetting = Generic::getClassSettingByKPOID($myGrandParentID);
        $return['id_level_masuk'] = new Leap\View\InputSelect($arrLevelTrainer, "id_level_masuk", "id_level_masuk", $this->id_level_masuk);
        $return['id_level_sekarang'] = new Leap\View\InputSelect($arrLevelTrainer, "id_level_sekarang", "id_level_sekarang", $this->id_level_sekarang);

        $return['murid_ak_id']->setReadOnly();
        $return['murid_kpo_id']->setReadOnly();
        $return['murid_ibo_id']->setReadOnly();
        $return['pay_firsttime'] = new Leap\View\InputText("hidden", "pay_firsttime", "pay_firsttime", $this->pay_firsttime);
        $return['nama_siswa'] = new Leap\View\InputTextPattern("text", "nama_siswa", "nama_siswa", $this->nama_siswa, KEY::$PATTERN_NAME);
        $return['status'] = new Leap\View\InputText("hidden", "status", "status", $this->status);

        $return['murid_kurikulum'] = new Leap\View\InputText("hidden", "murid_kurikulum", "murid_kurikulum", $this->murid_kurikulum);
        if ($this->pay_firsttime == 0) {
            $return['status'] = new Leap\View\InputText("hidden", "status", "status", $this->status);
            $return['status_text'] = new Leap\View\InputText("text", "status_text", "status_text", $arrStatusMurid[$this->status]);
            $return['status_text']->setReadOnly();
            $return['id_level_sekarang'] = new Leap\View\InputText('hidden', "id_level_sekarang", "id_level_sekarang", $this->id_level_masuk);

        } else {
            $return['id_level_sekarang'] = new Leap\View\InputText("hidden", "id_level_sekarang", "id_level_sekarang", $this->id_level_sekarang);

        }

        $return['parent_id'] = new Leap\View\InputText("hidden", "parent_id", "parent_id", $this->parent_id);
        $return['murid_app_pwd'] = new Leap\View\InputText("hidden", "murid_app_pwd", "murid_app_pwd", $this->murid_app_pwd);
        $return['murid_created_date'] = new Leap\View\InputText("hidden", "murid_created_date", "murid_created_date", $this->murid_created_date);
        $return['murid_updated'] = new Leap\View\InputText("hidden", "murid_updated", "murid_updated", $this->murid_updated);
        $return['murid_active'] = new Leap\View\InputText("hidden", "murid_active", "murid_active", $this->murid_active);
        $return['murid_parent_id'] = new Leap\View\InputText("hidden", "murid_parent_id", "murid_parent_id", $this->murid_parent_id);
        $return['no_pay_firsttime'] = new Leap\View\InputText("hidden", "no_pay_firsttime", "no_pay_firsttime", $this->no_pay_firsttime);

        return $return;
    }

    public function constraints()
    {
        //err id => err msg
        $err = array();

        if (!isset($this->id_level_sekarang)) {
            $this->id_level_sekarang = $this->id_level_masuk;
        }

        if ($this->pay_firsttime == 0) {
            $this->id_level_sekarang = $this->id_level_masuk;
        }

        if (!isset($this->nama_siswa)) {
            $err['nama_siswa'] = Lang::t('Please provide Name');
        }
        if (!isset($this->jenis_kelamin)) {
            $err['jenis_kelamin'] = Lang::t('Please provide Gender');
        }

        if (!isset($this->alamat)) {
            $err['alamat'] = Lang::t('Please provide address');
        }

        if (!isset($this->tanggal_lahir)) {
            $err['tanggal_lahir'] = Lang::t('Please provide Birthday');
        }
        if ($this->tanggal_lahir != "") {
            $dateDiff = date_diff(date_create($this->tanggal_lahir), date_create('today'))->y;

            if ($dateDiff < 1) {
                $err['tanggal_lahir'] = Lang::t('Min Age is 2 y.o.');
            }
        }
//        if (!isset($this->telepon)) {
//            $err['telepon'] = Lang::t('Please provide Phone No.');
//        }

        if (!isset($this->tanggal_masuk)) {
            $err['tanggal_masuk'] = Lang::t('Please provide Entry Date');
        }

        if (!isset($this->id_level_masuk)) {
            $err['id_level_masuk'] = Lang::t('Please provide ID Level Masuk');
        }

//        if($this->pay_firsttime != 1){
//            $err['status'] = Lang::t('Murid belum melakukan pembayaran pertama');
//        }

        if (!isset($this->id_murid)) {
//            $logMurid = new LogStatusMurid();
//            $logMurid->createLogMurid($this->id_murid);

        }

        return $err;
    }

    public function overwriteRead($return)
    {
        $objs = $return['objs'];
        $arrLevel = Generic::getAllLevel();
        foreach ($objs as $obj) {

            if (isset($obj->murid_tc_id)) {
                $obj->murid_tc_id = Generic::getTCNamebyID($obj->murid_tc_id);
            }


            if (isset($obj->id_level_sekarang) && $obj->id_level_sekarang > 0) {
                $obj->id_level_sekarang = $arrLevel[$obj->id_level_sekarang];
            }
            if (isset($obj->id_level_masuk) && $obj->id_level_masuk > 0) {
                $obj->id_level_masuk = $arrLevel[$obj->id_level_masuk];
            }
            if (isset($obj->jenis_kelamin)) {
                if ($obj->jenis_kelamin == 'm') {
                    $obj->jenis_kelamin = "Male";
                } elseif ($obj->jenis_kelamin == 'f') {
                    $obj->jenis_kelamin = "Female";
                } else {
                    $obj->jenis_kelamin = "<i>Not Set</i>";
                }
            }

            if (isset($obj->status)) {
                $arrStatusMurid = Generic::getAllStatusMurid();
                $arrStatusMurid[KEY::$STATUSINDEXALL] = KEY::$STATUSALL;

                $obj->status = $arrStatusMurid[$obj->status];
            }


            if ($obj->pay_firsttime == '0') {
                if (AccessRight::getMyOrgType() == KEY::$IBO) {
                    if ($obj->no_pay_firsttime == '') {
                        $obj->no_pay_firsttime = "<button onclick=\"setnofirstpayment($obj->id_murid);\">Tidak ada biaya pertama kali</button>";

                    } elseif ($obj->no_pay_firsttime == '0') {
                        $obj->no_pay_firsttime = "<button onclick=\"setnofirstpayment($obj->id_murid);\">Tidak ada biaya pertama kali</button>";

                    } else {
                        $obj->no_pay_firsttime = "";
                    }
                }
                else{
                    $obj->no_pay_firsttime = "";
                }

                $obj->pay_firsttime = "<button onclick=\"openLw('Payment_Murid','" . _SPPATH . "MuridWebHelper/firsttime_payment?id_murid=" . $obj->id_murid . "','fade');\">Payment First Time</button>";
            } else {

                $obj->no_pay_firsttime = "";

                $obj->pay_firsttime = "<a target=\"_blank\" href=" . _SPPATH . "MuridWebHelper/printRegister2?id_murid=" . $obj->id_murid . "><span  style=\"vertical-align:middle\" class=\"glyphicon glyphicon-print\"  aria-hidden=\"true\"></span>
                                            </a>";
            }


            $obj->profile = "<button onclick=\"openLw('Profile_Murid','" . _SPPATH . "MuridWebHelper/profile?id_murid=" . $obj->id_murid . "','fade');\">Profile</button>";

        }

        ?>
        <script>
            function setnofirstpayment(murid_id) {
                if (murid_id != "") {
                    $.post("<?= _SPPATH; ?>MuridController/setNoFirstPayment", {
                            murid_id: murid_id
                        },
                        function (data) {
                            alert(data.status_message);
                            if (data.status_code) {
                                lwrefresh(selected_page);
                            } else {
                            }
                            console.log(data);
                            //                                                                                                                                                                                     ?>//').html(data);
                        }, 'json').fail(function () {
                        alert("Tidak ada Koneksi internet!");
                    });
                }
            }
        </script>
        <?
        //pr($return);
        return $return;
    }

    public function onSaveSuccess($id)
    {
        parent::onSaveSuccess($id);
        $objMurid = new MuridModel();
        $objMurid->getByID($id);
        $objMurid->id_level_sekarang = $objMurid->id_level_masuk;
        // Pertama kali

        if ($objMurid->pay_firsttime == 0) {
            $objStatus = new StatusHisMuridModel();
            $objStatus->getWhereOne("status_murid_id='$id' ORDER BY status_tanggal_mulai DESC");
            if (is_null($objStatus->status_id)) {
                $statusMurid = new StatusHisMuridModel();
                $statusMurid->createHistory($id);
                $logMurid = new LogStatusMurid();
                $logMurid->createLogMurid($id);

            } else {
                $statusMurid = new StatusHisMuridModel();
                $statusMurid->updateHistoryMurid($id);
                $newHistory = new StatusHisMuridModel();
                $newHistory->createHistory($id);
                $logMurid = new LogStatusMurid();
                $logMurid->createLogMurid($id);
            }
        } // sdh melakukan pembayaran pertama
        else {
            $objStatus = new StatusHisMuridModel();
            $objStatus->getWhereOne("status_murid_id='$id' ORDER BY status_tanggal_mulai DESC");
            if ($objStatus->status != $objMurid->status) {
                $update = new StatusHisMuridModel();
                $update->updateHistoryMurid($id);
                $newHistory = new StatusHisMuridModel();
                $newHistory->createHistory($id);
                $logMurid = new LogStatusMurid();
                $logMurid->createLogMurid($id);
            }
        }

    }

    public function getMuridAktiv($tc_id)
    {
        $arrMurid = $this->getWhere("status=1 AND murid_tc_id=$tc_id ");
        $res = 0;
        $res = count($arrMurid);
        return $res;
    }

    public function getJumlahMuridAktivByMonthIBO($ibo_id, $bln, $thn)
    {
        $count = $this->getJumlah("murid_ibo_id=$ibo_id AND status=1");
        return $count;
    }


    public function overwriteReadExcel($return)
    {
        $objs = $return['objs'];

        $jumlah = 0;

        $arrLevel = Generic::getAllLevel();
        $arrStatus = Generic::getAllStatusMurid();
        $arrAgama = Generic::getAllAgama();
        $arrKurikulum = Generic::getJenisKurikulum();

        foreach ($objs as $obj) {

            if (isset($obj->jenis_kelamin)) {
                if ($obj->jenis_kelamin == 'm') {
                    $obj->jenis_kelamin = "Male";
                } elseif ($obj->jenis_kelamin == 'f') {
                    $obj->jenis_kelamin = "Female";
                } else {
                    $obj->jenis_kelamin = "";
                }
            }

            if (isset($obj->id_level_sekarang)) {
                $obj->id_level_sekarang = $arrLevel[$obj->id_level_sekarang];
            }

            if (isset($obj->id_level_masuk)) {
                $obj->id_level_masuk = $arrLevel[$obj->id_level_masuk];
            }

            if (isset($obj->status)) {
                $obj->status = $arrStatus[$obj->status];
            }
            if (isset($obj->agama)) {
                $obj->agama = $arrAgama[$obj->agama];
            }

            if (isset($obj->murid_kurikulum)) {
                $obj->murid_kurikulum = $arrKurikulum[$obj->murid_kurikulum];
            }
            $obj->TC = Generic::getTCNamebyID($obj->murid_tc_id);
        }
        return $return;
    }
}
