<?php

/**
 * Created by PhpStorm.
 * User: elroy
 * Date: 8/2/16
 * Time: 9:51 AM
 */
class SempoaGuruModel extends SempoaModel
{

    var $table_name = "sempoa__guru";
    var $main_id = "guru_id";
    //Default Coloms for read
    public $default_read_coloms = "guru_id,kode_guru,nama_guru,id_level_training_guru,status,email_guru,guru_tc_id";
//    public $default_read_coloms = "guru_id,kode_guru,tanggal_masuk,nama_guru,nama_panggilan,jenis_kelamin,alamat,tempat_lahir,agama,tanggal_lahir,nomor_hp,pendidikan_terakhir,kode_tc,email_guru,status,id_level_training_guru,gambar,id_level_training_guru,guru_ak_id,guru_kpo_id,guru_ibo_id,guru_tc_id";
//allowed colom in CRUD filter
    public $coloumlist = "guru_id,kode_guru,tanggal_masuk,nama_guru,nama_panggilan,jenis_kelamin,alamat,tempat_lahir,agama,tanggal_lahir,nomor_hp,pendidikan_terakhir,kode_tc,email_guru,status,id_level_training_guru,gambar,id_level_training_guru,guru_catatan,guru_ak_id,guru_kpo_id,guru_ibo_id,guru_tc_id,guru_first_register";
    public $guru_id;
    public $kode_guru;
    public $tanggal_masuk;
    public $nama_guru;
    public $nama_panggilan;
    public $jenis_kelamin;
    public $alamat;
    public $tempat_lahir;
    public $agama;
    public $tanggal_lahir;
    public $nomor_hp;
    public $pendidikan_terakhir;
    public $kode_tc;
    public $email_guru;
    public $status;
    public $guru_app_pwd;
    public $id_level_training_guru;
    public $gambar;
    public $guru_first_register;
    public $guru_catatan;
    public $guru_ak_id;
    public $guru_kpo_id;
    public $guru_ibo_id;
    public $guru_tc_id;
    public $removeAutoCrudClick = array("guru_first_register", "profile");
    public $hideColoums = array("guru_ak_id", "guru_kpo_id", "guru_ibo_id","guru_app_pwd");
    public $crud_setting = array("add" => 0, "search" => 1, "viewall" => 0, "export" => 1, "toggle" => 1, "import" => 0, "webservice" => 0);
    public $APPWS = "kode_guru,nama_guru,email_guru,guru_tc_id,guru_id,nomor_hp";

    public function overwriteForm($return, $returnfull)
    {

        $myOrgType = AccessRight::getMyOrgType();
        $return = parent::overwriteForm($return, $returnfull);

        $arrAgama = Generic::getAllAgama();
        $myOrg = AccessRight::getMyOrgID();
        $myParentID = Generic::getMyParentID($myOrg);
        $myGrandParentID = Generic::getMyParentID($myParentID);
        $myRootID = Generic::getMyParentID($myGrandParentID);

        $arrLevelTrainer = Generic::getAllLevel();
        $arrStatusGuru = Generic::getAllStatusGuru();
        if ($myOrgType == KEY::$AK) {
            $return['guru_ak_id'] = new Leap\View\InputText("hidden", "guru_ak_id", "guru_ak_id", $myOrg);
            $return['guru_tc_id']->setReadOnly();
        } else if ($myOrgType == KEY::$KPO) {
            $return['guru_ak_id'] = new Leap\View\InputText("hidden", "guru_ak_id", "guru_ak_id", $myParentID);
            $return['guru_kpo_id'] = new Leap\View\InputText("hidden", "guru_kpo_id", "guru_kpo_id", $myOrg);
            $return['guru_ibo_id'] = new Leap\View\InputText("hidden", "guru_ibo_id", "guru_ibo_id", $this->guru_ibo_id);
            $return['guru_tc_id'] = new Leap\View\InputText("hidden", "guru_tc_id", "guru_tc_id", $this->guru_tc_id);
            $return['guru_tc_id']->setReadOnly();
        } else if ($myOrgType == KEY::$IBO) {
            $return['guru_ak_id'] = new Leap\View\InputText("hidden", "guru_ak_id", "guru_ak_id", $myGrandParentID);
            $return['guru_kpo_id'] = new Leap\View\InputText("hidden", "guru_kpo_id", "guru_kpo_id", $myParentID);
            $return['guru_ibo_id'] = new Leap\View\InputText("hidden", "guru_ibo_id", "guru_ibo_id", $myOrg);

            //if ibo bisa pilih TC
            $arrTc = Generic::getAllMyTC(AccessRight::getMyOrgID());
            $return['guru_tc_id'] = new Leap\View\InputSelect($arrTc, "guru_tc_id", "guru_tc_id", $this->guru_tc_id);

            $return['kode_guru'] = new Leap\View\InputText("text", "kode_guru", "kode_guru", $this->kode_guru);
            $return['kode_guru']->setReadOnly();
        } else if ($myOrgType == KEY::$TC) {
            $return['guru_ak_id'] = new Leap\View\InputText("hidden", "guru_ak_id", "guru_ak_id", $myRootID);
            $return['guru_kpo_id'] = new Leap\View\InputText("hidden", "guru_kpo_id", "guru_kpo_id", $myGrandParentID);
            $return['guru_ibo_id'] = new Leap\View\InputText("hidden", "guru_ibo_id", "guru_ibo_id", $myParentID);
            $return['guru_tc_id'] = new Leap\View\InputText("text", "guru_tc_id", "guru_tc_id", $myOrg);
            $return['kode_tc'] = new Leap\View\InputText("text", "kode_tc", "kode_tc", $myOrg);

            //if dia tc hidden
            $return['guru_tc_id'] = new Leap\View\InputText("hidden", "guru_tc_id", "guru_tc_id", $myOrg);
            if ($this->kode_guru == "") {
                $return['kode_guru'] = new Leap\View\InputText("text", "kode_guru", "kode_guru", Generic::fCreateKode(KEY::$GURU, $myOrg));
            } else {
                $return['kode_guru'] = new Leap\View\InputText("text", "kode_guru", "kode_guru", $this->kode_guru);
            }
            $return['kode_guru']->setReadOnly();
            $return['guru_catatan'] = new \Leap\View\InputTextRTE("guru_catatan", "guru_catatan", $this->guru_catatan);
        }

        if ($myOrgType == KEY::$IBO) {
            $return['id_level_training_guru'] = new Leap\View\InputSelect($arrLevelTrainer, "id_level_training_guru", "id_level_training_guru", $this->id_level_training_guru);
            if ($this->guru_first_register == 0) {
                $this->status = KEY::$STATUSGURUNONAKTIV;
                $return['status'] = new Leap\View\InputSelect($arrStatusGuru, "status", "status", $this->status, 'form-control', 'disabled');
            } else {
                $return['status'] = new Leap\View\InputSelect($arrStatusGuru, "status", "status", $this->status);
            }


        } else {
//            $return['id_level_training_guru'] = new Leap\View\InputSelect($arrLevelTrainer, "id_level_training_guru", "id_level_training_guru", $this->id_level_training_guru);

            $return['id_level_training_guru'] = new Leap\View\InputText("hidden", "id_level_training_guru", "id_level_training_guru", $this->id_level_training_guru);
            $return['id_level_training_guru_text'] = new Leap\View\InputText("text", "id_level_training_guru_text", "id_level_training_guru_text", $arrLevelTrainer[$this->id_level_training_guru]);
            $return['id_level_training_guru_text']->setReadOnly();

            if ($this->guru_first_register == 0) {

                $this->status = KEY::$STATUSGURUNONAKTIV;

            }

            $return['status'] = new Leap\View\InputText("hidden", "status", "status", $this->status);
            $return['status_text'] = new Leap\View\InputText("text", "status_text", "status_text", $arrStatusGuru[$this->status]);
            $return['status_text']->setReadOnly();
        }

        $return['agama'] = new Leap\View\InputSelect($arrAgama, "agama", "agama", $this->agama);
        $return['nomor_hp'] = new Leap\View\InputText("text", "nomor_hp", "nomor_hp", $this->nomor_hp);
        $return['gambar'] = new Leap\View\InputFoto("gambar", "gambar", $this->gambar);
        $return['kode_tc'] = new Leap\View\InputText("hidden", "kode_tc", "kode_tc", $this->kode_tc);
        $return['email_guru'] = new Leap\View\InputText("email", "email_guru", "email_guru", $this->email_guru);

        $return['guru_app_pwd']->setReadOnly();
        $return['guru_app_pwd'] = new Leap\View\InputText("hidden", "guru_app_pwd", "guru_app_pwd", $this->guru_app_pwd);

        $return['guru_ak_id']->setReadOnly();
        $return['guru_kpo_id']->setReadOnly();
        $return['guru_ibo_id']->setReadOnly();
        $return['jenis_kelamin'] = new Leap\View\InputSelect(array("m" => "Male", "f" => "Female"), "jenis_kelamin", "jenis_kelamin", $this->jenis_kelamin);
        $return['agama'] = new Leap\View\InputSelect($arrAgama, "agama", "agama", $this->agama);
        $return['tanggal_lahir'] = new Leap\View\InputText("date", "tanggal_lahir", "tanggal_lahir", $this->tanggal_lahir);
        $return['tanggal_masuk'] = new Leap\View\InputText("date", "tanggal_masuk", "tanggal_masuk", $this->tanggal_masuk);
        $return['guru_first_register'] = new Leap\View\InputText("hidden", "guru_first_register", "guru_first_register", $this->guru_first_register);
        $return['nama_guru'] = new Leap\View\InputTextPattern("text", "nama_guru", "nama_guru", $this->nama_guru, KEY::$PATTERN_NAME);
        $return['guru_catatan'] = new \Leap\View\InputTextRTE("guru_catatan", "guru_catatan", $this->guru_catatan);

        return $return;
    }

    public function overwriteRead($return)
    {

        $return = parent::overwriteRead($return);
        $myOrgType = AccessRight::getMyOrgType();
        $arrStatusGuru = Generic::getAllStatusGuru();
        $objs = $return['objs'];
        foreach ($objs as $obj) {

            if($myOrgType == KEY::$IBO){
                if (isset($obj->guru_tc_id)) {
                    $obj->guru_tc_id = Generic::getTCNamebyID($obj->guru_tc_id);
                }
            }

            if (isset($obj->status)) {
                $obj->status = $arrStatusGuru[$obj->status];
            }
            if (isset($obj->gambar)) {
                $obj->gambar = \Leap\View\InputFoto::getAndMakeFoto($obj->gambar, "gambar");
            }
            if (isset($obj->id_level_training_guru)) {
                $obj->id_level_training_guru = Generic::getLevelNameByID($obj->id_level_training_guru);
            }

            if (isset($obj->guru_ibo_id)) {
                $ibo = new TorgIBO();
                $ibo->getByID($obj->guru_ibo_id);
                $obj->guru_ibo_id = $ibo->nama;
            }
            if ($obj->email_guru != "") {
                $obj->email_guru = "<button onclick='window.location.href = \"mailto:" . $obj->email_guru . "\";'>Email</button>";
            }


            $obj->profile = "<button onclick=\"openLw('Profile_Guru','" . _SPPATH . "GuruWebHelper/guru_profile?guru_id=" . $obj->guru_id . "','fade');\">Profile</button>";
        }
        return $return;
    }

    function createStatusGuru()
    {

        $objStatusGuru = new StatusHisGuruModel();
    }

    public function constraints()
    {
        //err id => err msg
        $err = array();

        if (AccessRight::getMyOrgType() == "ibo") {
            $myOrg = AccessRight::getMyOrgID();
            if (!isset($this->kode_guru)) {
                if (isset($this->guru_tc_id)) {
                    $this->kode_guru = Generic::fCreateKode(KEY::$GURU, $this->guru_tc_id);
//
                }
            }
        }
        if (!isset($this->status)) {
            $this->status = '99';
        }
        if (!isset($this->nama_guru)) {
            $err['nama_guru'] = "Silahkan isi Nama Anda Anda";
        }

        if (!isset($this->alamat)) {
            $err['alamat'] = "Silahkan isi Alamat Anda";
        }

        if (!isset($this->tanggal_masuk)) {
            $err['tanggal_masuk'] = "Silahkan isi tanggal Masuk Anda";
        }

        if (!isset($this->tanggal_lahir)) {
            $err['tanggal_lahir'] = "Silahkan isi tanggal lahir Anda";
        }
        if (!isset($this->tempat_lahir)) {
            $err['tempat_lahir'] = "Silahkan isi tempat lahir Anda";
        }

        if (!isset($this->nomor_hp)) {
            $err['nomor_hp'] = "Silahkan isi nomor Handphone Anda";
        }

        if (!isset($this->email_guru)) {
            $err['email_guru'] = "Silahkan isi email Anda";
        }

        if (!isset($this->pendidikan_terakhir)) {
            $err['pendidikan_terakhir'] = "Silahkan isi Pendidikan Terakhir Anda";
        }
        return $err;
    }

    public function onSaveSuccess($id)
    {
        parent::onSaveSuccess($id);
        $guru = new $this();
        $guru->getByID($id);
        if ($guru->guru_first_register == 0) {
            $reg = new RegisterGuru();
            $reg->isInvoiceCreated($id);
            if (is_null($reg->transaksi_id )) {
                $biaya = Generic::getBiayaByJenis(KEY::$BIAYA_PENDAFTARAN_GURU, $guru->guru_tc_id);
                $reg->createInvoice($id, $biaya, $guru->guru_ak_id, $guru->guru_kpo_id, $guru->guru_ibo_id, $guru->guru_tc_id);
                if (AccessRight::getMyOrgType() == KEY::$IBO) {
                    SempoaInboxModel::sendMsg($guru->guru_tc_id, AccessRight::getMyOrgID(), "Ada pendaftaran Guru Baru di TC Anda", "Ada pendaftaran Guru di TC Anda bernama: " . $guru->nama_guru);
                    SempoaInboxModel::sendMsg(AccessRight::getMyOrgID(), AccessRight::getMyOrgID(), "Ada pendaftaran Guru Baru di : " . Generic::getTCNamebyID($guru->guru_tc_id), "Ada pendaftaran Guru di TC: " . Generic::getTCNamebyID($guru->guru_tc_id) . " yang bernama: " . $guru->nama_guru . " lakukan pengecekan");
//
                } elseif (AccessRight::getMyOrgType() == KEY::$TC) {
                    SempoaInboxModel::sendMsg(AccessRight::getMyOrgID(), $guru->guru_ibo_id, "Ada pendaftaran Guru Baru di TC Anda", "Ada pendaftaran Guru di TC Anda bernama: " . $guru->nama_guru);
                    SempoaInboxModel::sendMsg($guru->guru_ibo_id, $guru->guru_ibo_id, "Ada pendaftaran Guru Baru di : " . Generic::getTCNamebyID($guru->guru_tc_id), "Ada pendaftaran Guru di TC: " . Generic::getTCNamebyID($guru->guru_tc_id) . " yang bernama: " . $guru->nama_guru . " lakukan pengecekan");
//
                }

            }
        }
    }

    public function getCountAktivGuruByTC($tc_id)
    {
        $arrTc = $this->getWhere("guru_tc_id=$tc_id AND status=1");
        return count($arrTc);
    }

    public function getAllGuruAktivByTC($tc_id)
    {
        $arrTc = $this->getWhere("guru_tc_id=$tc_id AND status=1");
        return $arrTc;
    }

    function setFieldModel($field,$value){
        $this->getWhereOne("$this->main_id='$field''");
        $this->$field = $value;
        $this->save(1);
    }

    public function overwriteReadExcel($return)
    {
        $objs = $return['objs'];

        $jumlah = 0;

        $arrLevel = Generic::getAllLevel();
        $arrStatus = Generic::getAllStatusGuru();
        $arrAgama = Generic::getAllAgama();

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

            if (isset($obj->id_level_training_guru)) {
                $obj->id_level_training_guru = $arrLevel[$obj->id_level_training_guru];
            }


            if (isset($obj->status)) {
                $obj->status = $arrStatus[$obj->status];
            }
            if (isset($obj->agama)) {
                $obj->agama = $arrAgama[$obj->agama];
            }

            if (isset($obj->kode_tc)) {
                $obj->kode_tc = Generic::getTCNamebyID($obj->guru_tc_id);
            }
        }
        return $return;
    }

}
