<?php

/**
 * Created by PhpStorm.
 * User: elroy
 * Date: 8/31/16
 * Time: 11:18 AM
 */
class SettingWeb2Helper extends WebService
{

    public function updateAllBiayaKPO()
    {

        $ibo = $_POST['ibo'];
        $ibo_id = Generic::getOrgIDByName($ibo);
        $json['ibo_id'] = $ibo_id;
        $mytype = AccessRight::getMyOrgType();
        $set = new SettingJenisBiayaModel();
        $arr = $set->getAll();


        $err = array();

        foreach ($arr as $biaya) {
            $val = $_POST['biaya_' . $biaya->id_jenis_biaya];

            $rule = $mytype . "_rule";
            list($action, $constraint) = explode(";", $biaya->$rule);

            if ($action == "No")
                continue;
            if ($action == "Edit") {

                $jenisbm_parent = new JenisBiayaModel();
                $jenisbm_parent->getByID($ibo_id . "_" . $biaya->id_jenis_biaya);

                if ($constraint == ">=") {
                    //ambil harga dari ref (parent nya)
                    //cek hrg baru apa lebih besar
                    if ($val >= $jenisbm_parent->harga) {

                    } else {
                        //error
                        $err["biaya_" . $biaya->id_jenis_biaya] = "Harga harus lebih besar sama dengan";
                        continue;
                    }
                }
                if ($constraint == "=") {
                    //ambil harga dari ref (parent nya)
                    //cek hrg baru apa lebih besar

                    if ($val == $jenisbm_parent->harga) {

                    } else {
                        //error
                        $err["biaya_" . $biaya->id_jenis_biaya] = "Harga harus  sama dengan";
                        continue;
                    }
                }
                if ($constraint == "<=") {
                    //ambil harga dari ref (parent nya)
                    //cek hrg baru apa lebih besar

                    if ($val <= $jenisbm_parent->harga) {

                    } else {
                        //error
                        $err["biaya_" . $biaya->id_jenis_biaya] = "Harga harus lebih kecil sama dengan";
                        continue;
                    }
                }
                if ($constraint == ">") {
                    //ambil harga dari ref (parent nya)
                    //cek hrg baru apa lebih besar
                    if ($val > $jenisbm_parent->harga) {

                    } else {
                        //error
                        $err["biaya_" . $biaya->id_jenis_biaya] = "Harga harus lebih besar";
                        continue;
                    }
                }
                if ($constraint == "<") {
                    //ambil harga dari ref (parent nya)
                    //cek hrg baru apa lebih besar
                    if ($val < $jenisbm_parent->harga) {

                    } else {
                        //error
                        $err["biaya_" . $biaya->id_jenis_biaya] = "Harga harus lebih besar sama dengan";
                        continue;
                    }
                }
            }

            $jenisbm = new JenisBiayaModel();
            $jenisbm->id_setting_biaya = $ibo_id . "_" . $biaya->id_jenis_biaya;
            $jenisbm->harga = $val;
            $jenisbm->setting_org_id = $ibo_id;
            $jenisbm->jenis_biaya = $biaya->id_jenis_biaya;
            $suc = $jenisbm->save(1);

        }

        $json['status_code'] = 1;
        if (count($err) > 0) {
            $json['status_code'] = 0;
        }

        $json['err'] = $err;
        $json['post'] = $_POST;
        echo json_encode($json);
        die();
    }

    public function updateAllBiaya()
    {
//
//        ini_set('display_errors', '1');
//error_reporting(E_ALL);

        $mytype = AccessRight::getMyOrgType();
        $org = new SempoaOrg();
        $org->getByID(AccessRight::getMyOrgID());

        //AK blom dihandle
        $id = AccessRight::getMyOrgID();


        if (strtolower($org->org_type) == "ibo") {
            //naik sekali
            $id = $org->org_parent_id;
        }
        if (strtolower($org->org_type) == "tc") {
            //naik dua kali
            $ibo_id = $org->org_parent_id;
            $org = new SempoaOrg();
            $org->getByID($ibo_id);
            $id = $org->org_parent_id;
            $id = AccessRight::getMyOrgID();
        }

        $set = new SettingJenisBiayaModel();
//        $arr = $set->getWhere("kpo_id = '$id'");
        $arr = $set->getAll();


        $children = Generic::getChildren(AccessRight::getMyOrgID());

        $json['children'] = $children;

        $err = array();

        foreach ($arr as $biaya) {
            $val = $_POST['biaya_' . $biaya->id_jenis_biaya];

            $rule = $mytype . "_rule";
            list($action, $constraint) = explode(";", $biaya->$rule);

            if ($action == "No")
                continue;
            if ($action == "Edit") {

                $jenisbm_parent = new JenisBiayaModel();
                $jenisbm_parent->getByID($id . "_" . $biaya->id_jenis_biaya);

                if ($constraint == ">=") {
                    //ambil harga dari ref (parent nya)
                    //cek hrg baru apa lebih besar
                    if ($val >= $jenisbm_parent->harga) {

                    } else {
                        //error
                        $err["biaya_" . $biaya->id_jenis_biaya] = "Harga harus lebih besar sama dengan";
                        continue;
                    }
                }
                if ($constraint == "=") {
                    //ambil harga dari ref (parent nya)
                    //cek hrg baru apa lebih besar

                    if ($val == $jenisbm_parent->harga) {

                    } else {
                        //error
                        $err["biaya_" . $biaya->id_jenis_biaya] = "Harga harus  sama dengan";
                        continue;
                    }
                }
                if ($constraint == "<=") {
                    //ambil harga dari ref (parent nya)
                    //cek hrg baru apa lebih besar

                    if ($val <= $jenisbm_parent->harga) {

                    } else {
                        //error
                        $err["biaya_" . $biaya->id_jenis_biaya] = "Harga harus lebih kecil sama dengan";
                        continue;
                    }
                }
                if ($constraint == ">") {
                    //ambil harga dari ref (parent nya)
                    //cek hrg baru apa lebih besar
                    if ($val > $jenisbm_parent->harga) {

                    } else {
                        //error
                        $err["biaya_" . $biaya->id_jenis_biaya] = "Harga harus lebih besar";
                        continue;
                    }
                }
                if ($constraint == "<") {
                    //ambil harga dari ref (parent nya)
                    //cek hrg baru apa lebih besar
                    if ($val < $jenisbm_parent->harga) {

                    } else {
                        //error
                        $err["biaya_" . $biaya->id_jenis_biaya] = "Harga harus lebih besar sama dengan";
                        continue;
                    }
                }
            }

            //untuk sendiri
            $jenisbm = new JenisBiayaModel();
            $jenisbm->id_setting_biaya = AccessRight::getMyOrgID() . "_" . $biaya->id_jenis_biaya;
            $jenisbm->harga = $val;
            $jenisbm->setting_org_id = AccessRight::getMyOrgID();
            $jenisbm->jenis_biaya = $biaya->id_jenis_biaya;

            $suc = $jenisbm->save(1);
            $json['suc'] = $suc;
//            $jenisbm->refID = 0;
            //untuk anak2nya
            foreach ($children as $child) {

                $org_id = $child->org_id;

                $jenisbm = new JenisBiayaModel();
                $jenisbm->id_setting_biaya = $org_id . "_" . $biaya->id_jenis_biaya;
                $jenisbm->harga = $val;
                $jenisbm->setting_org_id = $org_id;
                $jenisbm->jenis_biaya = $biaya->id_jenis_biaya;

                $suc = $jenisbm->save(1);

                //inform ke anak2 kalau ada perubahan harga

                SempoaInboxModel::sendMsg($org_id, AccessRight::getMyOrgID(), "Ada perubahan biaya", "Ada perubahan biaya <br> Tolong sesuaikan harga anda");
            }
        }

        foreach ($children as $child) {

            $org_id = $child->org_id;
            SempoaInboxModel::sendMsg($org_id, AccessRight::getMyOrgID(), "Ada perubahan biaya", "Ada perubahan biaya <br> Tolong sesuaikan harga anda");
        }

        $json['status_code'] = 1;
        if (count($err) > 0) {
            $json['status_code'] = 0;
        }

        $json['err'] = $err;
        $json['post'] = $_POST;
        echo json_encode($json);
        die();
    }

    public function updateAllBiayaGroup()
    {
//
//        ini_set('display_errors', '1');
//error_reporting(E_ALL);

        $mytype = AccessRight::getMyOrgType();

        $org = new SempoaOrg();
        $org->getByID(AccessRight::getMyOrgID());

        //AK blom dihandle
        $id = AccessRight::getMyOrgID();
        if (strtolower($org->org_type) == "ibo") {
            //naik sekali
            $id = $org->org_parent_id;
        }
        if (strtolower($org->org_type) == "tc") {
            //naik dua kali
            $ibo_id = $org->org_parent_id;
            $org = new SempoaOrg();
            $org->getByID($ibo_id);

            $id = $org->org_parent_id;
        }

        $set = new SettingJenisBiayaModel();
//        $arr = $set->getWhere("kpo_id = '$id'");
        $arr = $set->getAll();
        $group = new GroupsModel();
        $children = $group->getWhere("parent_id = '" . AccessRight::getMyOrgID() . "'");

//        $children = Generic::getChildren(AccessRight::getMyOrgID());

        if (strtolower($org->org_type) == "ibo") {
            //naik sekali
            $id = AccessRight::getMyOrgID();
        }
        $json['children'] = $children;

        $err = array();

        foreach ($arr as $biaya) {

            foreach ($children as $child) {

                $val = $_POST['biaya_' . $child->id_group . "_" . $biaya->id_jenis_biaya];


                $rule = $mytype . "_rule";
                list($action, $constraint) = explode(";", $biaya->$rule);

                if ($action == "No")
                    continue;
                if ($action == "Edit") {

                    $jenisbm_parent = new JenisBiayaModel();
                    $jenisbm_parent->getByID($id . "_" . $biaya->id_jenis_biaya);

                    if ($constraint == ">=") {
                        //ambil harga dari ref (parent nya)
                        //cek hrg baru apa lebih besar
                        if ($val >= $jenisbm_parent->harga) {

                        } else {
                            //error
                            $err["biaya_" . $child->id_group . "_" . $biaya->id_jenis_biaya] = "Harga harus lebih besar sama dengan";
                            continue;
                        }
                    }
                    if ($constraint == "=") {
                        //ambil harga dari ref (parent nya)
                        //cek hrg baru apa lebih besar

                        if ($val == $jenisbm_parent->harga) {

                        } else {
                            //error
                            $err["biaya_" . $child->id_group . "_" . $biaya->id_jenis_biaya] = "Harga harus  sama dengan";
                            continue;
                        }
                    }
                    if ($constraint == "<=") {
                        //ambil harga dari ref (parent nya)
                        //cek hrg baru apa lebih besar

                        if ($val <= $jenisbm_parent->harga) {

                        } else {
                            //error
                            $err["biaya_" . $child->id_group . "_" . $biaya->id_jenis_biaya] = "Harga harus lebih kecil sama dengan";
                            continue;
                        }
                    }
                    if ($constraint == ">") {
                        //ambil harga dari ref (parent nya)
                        //cek hrg baru apa lebih besar
                        if ($val > $jenisbm_parent->harga) {

                        } else {
                            //error
                            $err["biaya_" . $child->id_group . "_" . $biaya->id_jenis_biaya] = "Harga harus lebih besar";
                            continue;
                        }
                    }
                    if ($constraint == "<") {
                        //ambil harga dari ref (parent nya)
                        //cek hrg baru apa lebih besar
                        if ($val < $jenisbm_parent->harga) {

                        } else {
                            //error
                            $err["biaya_" . $child->id_group . "_" . $biaya->id_jenis_biaya] = "Harga harus lebih besar sama dengan";
                            continue;
                        }
                    }
                }

                //untuk sendiri
                $jenisbm = new JenisBiayaModelGroup();
                $jenisbm->id_setting_biaya = $child->id_group . "_" . $biaya->id_jenis_biaya;
                $jenisbm->harga = $val;
                $jenisbm->setting_org_id = AccessRight::getMyOrgID();
                $jenisbm->jenis_biaya = $biaya->id_jenis_biaya;

                $suc = $jenisbm->save(1);
//            $jenisbm->refID = 0;
                //ambil anak explode group
                $exp = explode(",", $child->groups);
                //untuk anak2nya
                foreach ($exp as $org_id) {

//                    $org_id = $child->org_id;

                    $jenisbm = new JenisBiayaModel();
                    $jenisbm->getByID(trim(rtrim($org_id)) . "_" . $biaya->id_jenis_biaya);
                    if (is_null($jenisbm->id_setting_biaya)) {
                        $jenisbm = new JenisBiayaModel();
                        $jenisbm->id_setting_biaya = trim(rtrim($org_id)) . "_" . $biaya->id_jenis_biaya;
                        $jenisbm->harga = $val;
                        $jenisbm->setting_org_id = $org_id;
                        $jenisbm->jenis_biaya = $biaya->id_jenis_biaya;
                        $jenisbm->save();
                    } else {
                        if (($jenisbm->harga == 0) || ($jenisbm->harga < $val)) {
                            $jenisbm->harga = $val;
                            $jenisbm->setting_org_id = $org_id;
                            $jenisbm->jenis_biaya = $biaya->id_jenis_biaya;
                            $suc = $jenisbm->save(1);
                        }
                    }


                    //inform ke anak2 kalau ada perubahan harga
                    SempoaInboxModel::sendMsg($org_id, AccessRight::getMyOrgID(), "Ada perubahan biaya", "Ada perubahan biaya <br> Tolong sesuaikan harga anda");
                }
            }
        }

        $children2 = Generic::getChildren(AccessRight::getMyOrgID());
        foreach ($children2 as $child) {

            $org_id = $child->org_id;
            SempoaInboxModel::sendMsg($org_id, AccessRight::getMyOrgID(), "Ada perubahan biaya", "Ada perubahan biaya <br> Tolong sesuaikan harga anda");
        }


        $json['status_code'] = 1;
        if (count($err) > 0) {
            $json['status_code'] = 0;
        }

        $json['err'] = $err;
        $json['post'] = $_POST;
        echo json_encode($json);
        die();
    }


    public static function formBiaya_KPO()
    {

        $mytype = AccessRight::getMyOrgType();

        $org = new SempoaOrg();
        $org->getByID(AccessRight::getMyOrgID());

        //AK blom dihandle
        $id = AccessRight::getMyOrgID();
        $arrMyIBO = Generic::getAllMyIBO(AccessRight::getMyOrgID());

        $set = new SettingJenisBiayaModel();
        $arr = $set->getAll();
        //nanti load biaya aktual
        $jenisbm = new JenisBiayaModel();
        $arr2 = $jenisbm->getWhere("setting_org_id = '" . AccessRight::getMyOrgID() . "'");

        $byID = array();
        foreach ($arr2 as $isinya) {
            $byID[$isinya->jenis_biaya] = $isinya;
        }

        $ibo_id = key($arrMyIBO);
        $t = time();
        ?>

        <?
        ?>
        <form id="biaya_<?= $mytype; ?>_<?= AccessRight::getMyOrgID(); ?>" xmlns="http://www.w3.org/1999/html">
            <div class="form-group">
                <label for=ibo">IBO</label>
                <select id="ibo" name="ibo" class="form-control">
                    <?
                    foreach ($arrMyIBO as $idIbo => $iboName) {
                        if ($ibo_id == $idIbo) {
                            $selected = "selected";
                        } else {
                            $selected = "";
                        }
                        ?>
                        <option <?= $selected; ?> name="<?= $idIbo; ?>" value="<?= $idIbo; ?>"><?= $iboName; ?></option>
                        <?
                    }

                    ?>
                </select>
            </div>
            <?
            foreach ($arr as $biaya) {
                $val = $byID[$biaya->id_jenis_biaya]->harga;
                if ($val == "")
                    $val = 0;

                $rule = $mytype . "_rule";
                list($action, $constraint) = explode(";", $biaya->$rule);
                ?>
                <div class="form-group">
                    <label for="exampleInputEmail1"><?= $biaya->jenis_biaya; ?></label>
                    <input <? if ($action == "No") echo "disabled"; ?> name="biaya_<?= $biaya->id_jenis_biaya; ?>"
                                                                       type="number" value="<?= $val; ?>"
                                                                       class="form-control"
                                                                       id="biaya_<?= $biaya->id_jenis_biaya; ?>"
                                                                       placeholder="<?= $biaya->jenis_biaya; ?>">

                    <div class="err_text" id="biaya_<?= $biaya->id_jenis_biaya; ?>_text"></div>
                </div>


                <?
            }
            ?>
            <button class="btn btn-default" type="submit">Submit</button>
        </form>
        <style>
            .err_text {
                padding: 5px;
                margin: 5px;
                background-color: #dedede;
                display: none;
            }

            .input_bordered {
                border-color: #ff0000;
            }
        </style>
        <script>
            $("#biaya_<?= $mytype; ?>_<?= AccessRight::getMyOrgID(); ?>").submit(function (event) {
                event.preventDefault();

                var post = $("#biaya_<?= $mytype; ?>_<?= AccessRight::getMyOrgID(); ?>").serialize();
                console.log(post);
                $('.err_text').hide();
                $('.input_bordered').removeClass("input_bordered");
                $.post("<?= _SPPATH; ?>SettingWeb2Helper/updateAllBiayaKPO", post, function (data) {
                    console.log(data);
                    if (data.status_code) {
                        alert("Data berhasil tersimpan");
                    } else {
                        var err = data.err;

                        for (x in err) {
                            $('#' + x).addClass("input_bordered");
                            var text = err[x];
                            $('#' + x + "_text").html(text).show();
                        }
                    }
                }, 'json');
            });
        </script>
        <?

    }

    public static function formBiaya()
    {

        $mytype = AccessRight::getMyOrgType();

        $org = new SempoaOrg();
        $org->getByID(AccessRight::getMyOrgID());

        //AK blom dihandle
        $id = AccessRight::getMyOrgID();
        if (strtolower($org->org_type) == "ibo") {
            //naik sekali
            $id = $org->org_parent_id;
        }
        if (strtolower($org->org_type) == "tc") {
            //naik dua kali
            $ibo_id = $org->org_parent_id;
            $org = new SempoaOrg();
            $org->getByID($ibo_id);

            $id = $org->org_parent_id;
        }


        $set = new SettingJenisBiayaModel();
        $arr = $set->getAll();
        //nanti load biaya aktual
        $jenisbm = new JenisBiayaModel();
        $arr2 = $jenisbm->getWhere("setting_org_id = '" . AccessRight::getMyOrgID() . "'");

        $byID = array();
        foreach ($arr2 as $isinya) {
            $byID[$isinya->jenis_biaya] = $isinya;
        }
        if (strtolower($mytype) == "ibo") {

            //nanti load biaya aktual
            $jenisbm = new JenisBiayaModelGroup();
            $arr2 = $jenisbm->getWhere("setting_org_id = '" . AccessRight::getMyOrgID() . "'");

            $byID_anak = array();
            foreach ($arr2 as $isinya) {
                $byID_anak[$isinya->jenis_biaya] = $isinya;
            }


            $group = new GroupsModel();
            $children = $group->getWhere("parent_id = '" . AccessRight::getMyOrgID() . "'");
            //create table harga
            ?>
            <form id="biaya_<?= $mytype; ?>_<?= AccessRight::getMyOrgID(); ?>">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>TC Group \ Biaya</th>
                            <?
                            foreach ($children as $child) {
                                ?>
                                <th><?= $child->nama; ?></th>
                                <?
                            }
                            ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?
                        foreach ($arr as $biaya) {

                            $rule = $mytype . "_rule";
                            list($action, $constraint) = explode(";", $biaya->$rule);
                            ?>
                            <tr>
                                <td><?= $biaya->jenis_biaya; ?></td>
                                <?
                                foreach ($children as $child) {
                                    $jenisbm = new JenisBiayaModelGroup();
                                    $jenisbm->getByID($child->id_group . "_" . $biaya->id_jenis_biaya);

                                    $val = $jenisbm->harga;
                                    if ($val == "") {
                                        $val = $byID[$biaya->id_jenis_biaya]->harga;
                                    }
                                    ?>
                                    <td>
                                        <input <? if ($action == "No") echo "disabled"; ?>
                                            name="biaya_<?= $child->id_group; ?>_<?= $biaya->id_jenis_biaya; ?>"
                                            type="number" value="<?= $val; ?>"
                                            class="form-control"
                                            id="biaya_<?= $child->id_group; ?>_<?= $biaya->id_jenis_biaya; ?>"
                                            placeholder="<?= $biaya->jenis_biaya; ?>">

                                        <div class="err_text"
                                             id="biaya_<?= $child->id_group; ?>_<?= $biaya->id_jenis_biaya; ?>_text"></div>

                                    </td>
                                    <?
                                }
                                ?>
                            </tr>
                            <?
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
                <button class="btn btn-default" type="submit">Submit</button>
            </form>

            <style>
                .err_text {
                    padding: 5px;
                    margin: 5px;
                    background-color: #dedede;
                    display: none;
                }

                .input_bordered {
                    border-color: #ff0000;
                }
            </style>
            <script>
                $("#biaya_<?= $mytype; ?>_<?= AccessRight::getMyOrgID(); ?>").submit(function (event) {
                    //                alert( "Handler for .submit() called." );
                    event.preventDefault();

                    var post = $("#biaya_<?= $mytype; ?>_<?= AccessRight::getMyOrgID(); ?>").serialize();
                    $('.err_text').hide();
                    $('.input_bordered').removeClass("input_bordered");
                    $.post("<?= _SPPATH; ?>SettingWeb2Helper/updateAllBiayaGroup", post, function (data) {
                        console.log(data);
                        if (data.status_code) {
                            alert("Data berhasil tersimpan");
                        } else {
                            var err = data.err;

                            for (x in err) {
                                $('#' + x).addClass("input_bordered");
                                var text = err[x];
                                $('#' + x + "_text").html(text).show();
                            }
                        }
                    }, 'json');
                });
            </script>


            <?
        } else {
            ?>
            <form id="biaya_<?= $mytype; ?>_<?= AccessRight::getMyOrgID(); ?>">
                <?
                foreach ($arr as $biaya) {
                    $val = $byID[$biaya->id_jenis_biaya]->harga;
                    if ($val == "")
                        $val = 0;

                    $rule = $mytype . "_rule";
                    list($action, $constraint) = explode(";", $biaya->$rule);
                    ?>
                    <div class="form-group">
                        <label for="exampleInputEmail1"><?= $biaya->jenis_biaya; ?></label>
                        <input <? if ($action == "No") echo "disabled"; ?> name="biaya_<?= $biaya->id_jenis_biaya; ?>"
                                                                           type="number" value="<?= $val; ?>"
                                                                           class="form-control"
                                                                           id="biaya_<?= $biaya->id_jenis_biaya; ?>"
                                                                           placeholder="<?= $biaya->jenis_biaya; ?>">

                        <div class="err_text" id="biaya_<?= $biaya->id_jenis_biaya; ?>_text"></div>
                    </div>


                    <?
                }
                ?>
                <button class="btn btn-default" type="submit">Submit</button>
            </form>
            <style>
                .err_text {
                    padding: 5px;
                    margin: 5px;
                    background-color: #dedede;
                    display: none;
                }

                .input_bordered {
                    border-color: #ff0000;
                }
            </style>
            <script>
                $("#biaya_<?= $mytype; ?>_<?= AccessRight::getMyOrgID(); ?>").submit(function (event) {
                    //                alert( "Handler for .submit() called." );
                    event.preventDefault();

                    var post = $("#biaya_<?= $mytype; ?>_<?= AccessRight::getMyOrgID(); ?>").serialize();
                    $('.err_text').hide();
                    $('.input_bordered').removeClass("input_bordered");
                    $.post("<?= _SPPATH; ?>SettingWeb2Helper/updateAllBiaya", post, function (data) {
                        console.log(data);
                        if (data.status_code) {
                            alert("Data berhasil tersimpan");
                        } else {
                            var err = data.err;

                            for (x in err) {
                                $('#' + x).addClass("input_bordered");
                                var text = err[x];
                                $('#' + x + "_text").html(text).show();
                            }
                        }
                    }, 'json');

//                    else{
//                        openLw('read_biaya_pendaftaran_minimal_semua_ibo', '<?//=_SPPATH;?>//SettingWeb2Helper/read_biaya_pendaftaran_minimal_semua_ibo', 'fade');
//                    }


                });
            </script>
            <?
        }
    }

    public static function formBiaya_backup()
    {

        $mytype = AccessRight::getMyOrgType();

        $org = new SempoaOrg();
        $org->getByID(AccessRight::getMyOrgID());

        //AK blom dihandle
        $id = AccessRight::getMyOrgID();
        if (strtolower($org->org_type) == "ibo") {
            //naik sekali
            $id = $org->org_parent_id;
        }
        if (strtolower($org->org_type) == "tc") {
            //naik dua kali
            $ibo_id = $org->org_parent_id;
            $org = new SempoaOrg();
            $org->getByID($ibo_id);

            $id = $org->org_parent_id;
        }


        $set = new SettingJenisBiayaModel();
        $arr = $set->getWhere("kpo_id = '$id'");


        //nanti load biaya aktual
        $jenisbm = new JenisBiayaModel();
        $arr2 = $jenisbm->getWhere("setting_org_id = '" . AccessRight::getMyOrgID() . "'");

        $byID = array();
        foreach ($arr2 as $isinya) {
            $byID[$isinya->jenis_biaya] = $isinya;
        }


        if (strtolower($mytype) == "ibo") {

            //nanti load biaya aktual
            $jenisbm = new JenisBiayaModelGroup();
            $arr2 = $jenisbm->getWhere("setting_org_id = '" . AccessRight::getMyOrgID() . "'");

            $byID_anak = array();
            foreach ($arr2 as $isinya) {
                $byID_anak[$isinya->jenis_biaya] = $isinya;
            }


            $group = new GroupsModel();
            $children = $group->getWhere("parent_id = '" . AccessRight::getMyOrgID() . "'");
            //create table harga
            ?>
            <form id="biaya_<?= $mytype; ?>_<?= AccessRight::getMyOrgID(); ?>">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>TC Group \ Biaya</th>
                            <?
                            foreach ($children as $child) {
                                ?>
                                <th><?= $child->nama; ?></th>
                                <?
                            }
                            ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?
                        foreach ($arr as $biaya) {

                            $rule = $mytype . "_rule";
                            list($action, $constraint) = explode(";", $biaya->$rule);
                            ?>
                            <tr>
                                <td><?= $biaya->jenis_biaya; ?></td>
                                <?
                                foreach ($children as $child) {
                                    $jenisbm = new JenisBiayaModelGroup();
                                    $jenisbm->getByID($child->id_group . "_" . $biaya->id_jenis_biaya);

                                    $val = $jenisbm->harga;
                                    if ($val == "") {
                                        $val = $byID[$biaya->id_jenis_biaya]->harga;
                                    }
                                    ?>
                                    <td>
                                        <input <? if ($action == "No") echo "disabled"; ?>
                                            name="biaya_<?= $child->id_group; ?>_<?= $biaya->id_jenis_biaya; ?>"
                                            type="number" value="<?= $val; ?>"
                                            class="form-control"
                                            id="biaya_<?= $child->id_group; ?>_<?= $biaya->id_jenis_biaya; ?>"
                                            placeholder="<?= $biaya->jenis_biaya; ?>">

                                        <div class="err_text"
                                             id="biaya_<?= $child->id_group; ?>_<?= $biaya->id_jenis_biaya; ?>_text"></div>

                                    </td>
                                    <?
                                }
                                ?>
                            </tr>
                            <?
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
                <button class="btn btn-default" type="submit">Submit</button>
            </form>

            <style>
                .err_text {
                    padding: 5px;
                    margin: 5px;
                    background-color: #dedede;
                    display: none;
                }

                .input_bordered {
                    border-color: #ff0000;
                }
            </style>
            <script>
                $("#biaya_<?= $mytype; ?>_<?= AccessRight::getMyOrgID(); ?>").submit(function (event) {
                    //                alert( "Handler for .submit() called." );
                    event.preventDefault();

                    var post = $("#biaya_<?= $mytype; ?>_<?= AccessRight::getMyOrgID(); ?>").serialize();
                    $('.err_text').hide();
                    $('.input_bordered').removeClass("input_bordered");
                    $.post("<?= _SPPATH; ?>SettingWeb2Helper/updateAllBiayaGroup", post, function (data) {
                        console.log(data);
                        if (data.status_code) {
                            alert("Data berhasil tersimpan");
                        } else {
                            var err = data.err;

                            for (x in err) {
                                $('#' + x).addClass("input_bordered");
                                var text = err[x];
                                $('#' + x + "_text").html(text).show();
                            }
                        }
                    }, 'json');
                });
            </script>


            <?
        } else {
            ?>
            <form id="biaya_<?= $mytype; ?>_<?= AccessRight::getMyOrgID(); ?>">
                <?
                foreach ($arr as $biaya) {
                    $val = $byID[$biaya->id_jenis_biaya]->harga;
                    if ($val == "")
                        $val = 0;

                    $rule = $mytype . "_rule";
                    list($action, $constraint) = explode(";", $biaya->$rule);
                    ?>
                    <div class="form-group">
                        <label for="exampleInputEmail1"><?= $biaya->jenis_biaya; ?></label>
                        <input <? if ($action == "No") echo "disabled"; ?> name="biaya_<?= $biaya->id_jenis_biaya; ?>"
                                                                           type="number" value="<?= $val; ?>"
                                                                           class="form-control"
                                                                           id="biaya_<?= $biaya->id_jenis_biaya; ?>"
                                                                           placeholder="<?= $biaya->jenis_biaya; ?>">

                        <div class="err_text" id="biaya_<?= $biaya->id_jenis_biaya; ?>_text"></div>
                    </div>


                    <?
                }
                ?>
                <button class="btn btn-default" type="submit">Submit</button>
            </form>
            <style>
                .err_text {
                    padding: 5px;
                    margin: 5px;
                    background-color: #dedede;
                    display: none;
                }

                .input_bordered {
                    border-color: #ff0000;
                }
            </style>
            <script>
                $("#biaya_<?= $mytype; ?>_<?= AccessRight::getMyOrgID(); ?>").submit(function (event) {
                    //                alert( "Handler for .submit() called." );
                    event.preventDefault();

                    var post = $("#biaya_<?= $mytype; ?>_<?= AccessRight::getMyOrgID(); ?>").serialize();
                    $('.err_text').hide();
                    $('.input_bordered').removeClass("input_bordered");
                    $.post("<?= _SPPATH; ?>SettingWeb2Helper/updateAllBiaya", post, function (data) {
                        console.log(data);
                        if (data.status_code) {
                            alert("Data berhasil tersimpan");
                        } else {
                            var err = data.err;

                            for (x in err) {
                                $('#' + x).addClass("input_bordered");
                                var text = err[x];
                                $('#' + x + "_text").html(text).show();
                            }
                        }
                    }, 'json');
                });
            </script>
            <?
        }
//        echo $mytype;
//        pr($arr);
    }

    public static function table_harga_anak2_tmp()
    {

        $children = Generic::getChildren(AccessRight::getMyOrgID());

        $mytype = AccessRight::getMyOrgType();

        $org = new SempoaOrg();
        $org->getByID(AccessRight::getMyOrgID());

        $id = AccessRight::getMyOrgID();
        if (strtolower($org->org_type) == "ibo") {
            //naik sekali
            $id = $org->org_parent_id;
        }
        if (strtolower($org->org_type) == "tc") {
            //naik dua kali
            $ibo_id = $org->org_parent_id;
            $org = new SempoaOrg();
            $org->getByID($ibo_id);

            $id = $org->org_parent_id;
        }

        $set = new SettingJenisBiayaModel();
        $arr = $set->getAll();
        ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th><?= $children[0]->org_type; ?> \ Biaya</th>
                    <?
                    foreach ($arr as $biaya) {
                        ?>
                        <th><?= $biaya->jenis_biaya; ?></th>
                        <?
                    }
                    ?>
                </tr>
                </thead>
                <tbody>
                <?
                foreach ($children as $child) {
                    ?>
                    <tr>
                        <td><?= $child->nama; ?></td>
                        <?
                        foreach ($arr as $biaya) {
                            $jenisbm = new JenisBiayaModel();
                            $jenisbm->getByID($child->org_id . "_" . $biaya->id_jenis_biaya);
                            ?>
                            <td>IDR <?= idr($jenisbm->harga); ?></td>
                            <?
                        }
                        ?>
                    </tr>
                    <?
                }
                ?>
                </tbody>
            </table>
        </div>

        <?
    }

    public static function table_harga_anak2()
    {

        $children = Generic::getChildren(AccessRight::getMyOrgID());

        $mytype = AccessRight::getMyOrgType();

        $org = new SempoaOrg();
        $org->getByID(AccessRight::getMyOrgID());

        $id = AccessRight::getMyOrgID();
        if (strtolower($org->org_type) == "ibo") {
            //naik sekali
            $id = $org->org_parent_id;
        }
        if (strtolower($org->org_type) == "tc") {
            //naik dua kali
            $ibo_id = $org->org_parent_id;
            $org = new SempoaOrg();
            $org->getByID($ibo_id);

            $id = $org->org_parent_id;
        }

        $set = new SettingJenisBiayaModel();
        $arr = $set->getAll();
        ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th><?= $children[0]->org_type; ?> \ Biaya</th>
                    <?
                    foreach ($arr as $biaya) {
                        ?>
                        <th><?= $biaya->jenis_biaya; ?></th>
                        <?
                    }
                    ?>
                </tr>
                </thead>
                <tbody>
                <?
                foreach ($children as $child) {
                    ?>
                    <tr style="background-color: <?= KEY::$WARNA_ABU; ?>">
                        <td><b><?= $child->nama; ?></b></td>
                        <?
                        foreach ($arr as $biaya) {
                            $jenisbm = new JenisBiayaModel();
                            $jenisbm->getByID($child->org_id . "_" . $biaya->id_jenis_biaya);
                            ?>
                            <td>IDR <?= idr($jenisbm->harga); ?></td>
                            <?
                        }
                        ?>
                    </tr>
                    <?
                    $myChild = Generic::getAllMyTC($child->org_id);
                    foreach ($myChild as $key => $my) {
                        ?>
                        <tr>
                            <td><?= $my; ?></td>
                            <?
                            foreach ($arr as $biaya) {
                                $jenisbm = new JenisBiayaModel();
                                $jenisbm->getByID($key . "_" . $biaya->id_jenis_biaya);
                                ?>
                                <td>IDR <?= idr($jenisbm->harga); ?></td>
                                <?
                            }
                            ?>
                        </tr>
                        <?
                    }
                }
                ?>
                </tbody>
            </table>
        </div>

        <?
    }


    public static function table_harga_Buku_anak2_tmp()
    {

        $children = Generic::getChildren(AccessRight::getMyOrgID());

        $mytype = AccessRight::getMyOrgType();

        $org = new SempoaOrg();
        $org->getByID(AccessRight::getMyOrgID());

        $id = AccessRight::getMyOrgID();
        if (strtolower($org->org_type) == "ibo") {
            //naik sekali
            $id = $org->org_parent_id;
        }
        if (strtolower($org->org_type) == "tc") {
            //naik dua kali
            $ibo_id = $org->org_parent_id;
            $org = new SempoaOrg();
            $org->getByID($ibo_id);

            $id = $org->org_parent_id;
        }

        $set = new BarangWebModel();
//        $arr = $set->getAll();
        $arr = $set->getWhere("kpo_id=$id");
        ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th><?= strtoupper($children[0]->org_type); ?></th>
                    <?php
                    foreach ($children as $child) {
                        ?>
                        <th><b><?= $child->nama; ?></b></th>
                        <?
                    }
                    ?>
                </tr>


                </thead>
                <tbody>
                <?
                foreach ($children as $child) {
                    ?>
                    <tr style="background-color: <?= KEY::$WARNA_ABU; ?>">

                        <?
                        foreach ($arr as $biaya) {

                            $jenisbm = new SettingHargaBarang();
                            $jenisbm->getByID($child->org_id . "_" . $biaya->id_barang_harga);
//                                pr($child->org_id . "_" . $biaya->id_barang_harga);
//                                pr($jenisbm);
                            ?>
                            <td><b><?= $biaya->nama_barang; ?></b></td>
                            <td>IDR <?= idr($jenisbm->harga); ?></td>
                            <?
                        }
                        ?>
                    </tr>
                    <?
                    $myChild = Generic::getAllMyTC($child->org_id);
                    foreach ($myChild as $key => $my) {
                        ?>
                        <tr>
                            <td><?= $my; ?></td>
                            <?
                            foreach ($arr as $biaya) {
                                $jenisbm = new JenisBiayaModel();
                                $jenisbm->getByID($key . "_" . $biaya->id_jenis_biaya);
                                ?>
                                <td>IDR <?= idr($jenisbm->harga); ?></td>
                                <?
                            }
                            ?>
                        </tr>
                        <?
                    }
                }
                ?>
                </tbody>
            </table>
        </div>

        <?
    }

    public static function table_harga_Buku_anak2()
    {

        $children = Generic::getChildren(AccessRight::getMyOrgID());

        $mytype = AccessRight::getMyOrgType();

        $org = new SempoaOrg();
        $org->getByID(AccessRight::getMyOrgID());

        $id = AccessRight::getMyOrgID();
        if (strtolower($org->org_type) == "ibo") {
            //naik sekali
            $id = $org->org_parent_id;
        }
        if (strtolower($org->org_type) == "tc") {
            //naik dua kali
            $ibo_id = $org->org_parent_id;
            $org = new SempoaOrg();
            $org->getByID($ibo_id);

            $id = $org->org_parent_id;
        }

        $set = new BarangWebModel();
//        $arr = $set->getAll();
        $arr = $set->getWhere("kpo_id=$id");
        ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th><?= strtoupper($children[0]->org_type); ?></th>
                    <?
                    foreach ($arr as $biaya) {
                        ?>
                        <th><?= $biaya->nama_barang; ?></th>
                        <?
                    }
                    ?>
                </tr>
                </thead>
                <tbody>
                <?
                foreach ($children as $child) {
                    ?>
                    <tr style="background-color: <?= KEY::$WARNA_ABU; ?>">
                        <td><b><?= $child->nama; ?></b></td>
                        <?
                        foreach ($arr as $biaya) {
                            $jenisbm = new SettingHargaBarang();
                            $jenisbm->getByID($child->org_id . "_" . $biaya->id_barang_harga);
//                                pr($child->org_id . "_" . $biaya->id_barang_harga);
//                                pr($jenisbm);
                            ?>
                            <td>IDR <?= idr($jenisbm->harga); ?></td>
                            <?
                        }
                        ?>
                    </tr>
                    <?
                    $myChild = Generic::getAllMyTC($child->org_id);
                    foreach ($myChild as $key => $my) {
                        ?>
                        <tr>
                            <td><?= $my; ?></td>
                            <?
                            foreach ($arr as $biaya) {
                                $jenisbm = new JenisBiayaModel();
                                $jenisbm->getByID($key . "_" . $biaya->id_jenis_biaya);
                                ?>
                                <td>IDR <?= idr($jenisbm->harga); ?></td>
                                <?
                            }
                            ?>
                        </tr>
                        <?
                    }
                }
                ?>
                </tbody>
            </table>
        </div>

        <?
    }

    public function editBiaya()
    {

        $id_ibo = addslashes($_GET['id_ibo']);

        $set = new SettingJenisBiayaModel();
        $arr = $set->getAll();
        //nanti load biaya aktual
        $jenisbm = new JenisBiayaModel();
        $arr2 = $jenisbm->getWhere("setting_org_id = '$id_ibo'");

        $byID = array();
        foreach ($arr2 as $isinya) {
            $byID[$isinya->jenis_biaya] = $isinya;
        }

        $arrMyIBO = Generic::getAllMyIBO(AccessRight::getMyOrgID());
        ?>
        <form id="biaya_<?= $id_ibo; ?>">
            <div class="form-group">
                <label for=ibo">IBO</label>
                <input name="ibo" class="form-control" id="ibo_<?= $id_ibo; ?>"
                       value="<?= Generic::getTCNamebyID($id_ibo); ?>" readonly>

            </div>

            <?

            foreach ($arr as $biaya) {
                $val = $byID[$biaya->id_jenis_biaya]->harga;
                if ($val == "")
                    $val = 0;

                $rule = $id_ibo . "_rule";
                list($action, $constraint) = explode(";", $biaya->$rule);
                ?>
                <div class="form-group">
                    <label for="exampleInputEmail1"><?= $biaya->jenis_biaya; ?></label>
                    <input <? if ($action == "No") echo "disabled"; ?> name="biaya_<?= $biaya->id_jenis_biaya; ?>"
                                                                       type="number" value="<?= $val; ?>"
                                                                       class="form-control"
                                                                       id="biaya_<?= $biaya->id_jenis_biaya; ?>"
                                                                       placeholder="<?= $biaya->jenis_biaya; ?>">

                    <div class="err_text" id="biaya_<?= $biaya->id_jenis_biaya; ?>_text"></div>
                </div>


                <?
            }
            ?>
            <button class="btn btn-default" type="submit">Submit</button>
            <button class="btn btn-default" type="button" id="close_<?= $id_ibo ?>">Close</button>
            <script>

                $("#biaya_<?= $id_ibo; ?>").submit(function (event) {
                    event.preventDefault();
                    var post = $("#biaya_<?= $id_ibo; ?>").serialize();
                    console.log(post);
                    $('.err_text').hide();
                    $('.input_bordered').removeClass("input_bordered");
                    $.post("<?= _SPPATH; ?>SettingWeb2Helper/updateAllBiayaKPO", post, function (data) {
                        console.log(data);
                        if (data.status_code) {
                            alert("Data berhasil tersimpan");
                        } else {
                            var err = data.err;

                            for (x in err) {
                                $('#' + x).addClass("input_bordered");
                                var text = err[x];
                                $('#' + x + "_text").html(text).show();
                            }
                        }
                    }, 'json');


                });

                //                updateAllBiayaKPO
                $('#close_<?=$id_ibo?>').click(function () {
                    lwclose(window.selected_page);
                });
            </script>
        </form>
        <style>
            .err_text {
                padding: 5px;
                margin: 5px;
                background-color: #dedede;
                display: none;
            }

            .input_bordered {
                border-color: #ff0000;
            }
        </style>
        <?

//        pr($byID);
    }
}
