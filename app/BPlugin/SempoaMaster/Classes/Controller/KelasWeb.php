<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of KelasWeb
 *
 * @author efindiongso
 */
class KelasWeb extends WebService
{

    //put your code here
    //
    // TC
//    [KelasWeb] => Array
//                        (
//                            [0] => create_kelas_tc
//                            [1] => read_kelas_tc
//                            [2] => update_kelas_tc
//                            [3] => delete_kelas_tc
//                            
    public function create_kelas_tc()
    {
//        $_GET['cmd'] = 'edit';
//        $this->read_kelas_tc();
        $obj = new KelasWebModel();
        $crud = new CrudCustom();
//        $obj->printColumlistAsAttributes();
        $crud->ar_add = AccessRight::hasRight("create_kelas_tc");
        $crud->ar_edit = AccessRight::hasRight("update_kelas_tc");
        $crud->ar_delete = AccessRight::hasRight("delete_kelas_tc");
        $crud->run_custom($obj, "KelasWeb", "create_kelas_tc");

        die();
        ?>
        <h1>Atur Jam Kelas</h1>
        <? for ($x = 0; $x < 7; $x++) { ?>
        <input class="form-control" type="text" value="<?= SempoaWebSetting::getData('Jam_Kelas_' . $x); ?>"
               id="jam_kelas_<?= $x; ?>">

        <button class="btn btn-default" id="update_jam_kelas_<?= $x; ?>">Update Jam Kelas</button>
        <script>
            $('#update_jam_kelas_<?= $x; ?>').click(function () {
                var slc = $('#jam_kelas_<?= $x; ?>').val();
                $.post("<?= _SPPATH; ?>KelasWebHelper/updatejam_kelas", {
                    jam: slc,
                    hari: '<?= $x; ?>'
                }, function (data) {
                    if (data.status_code) {

                    } else {
                        alert(data.status_message);
                    }
                }, 'json');
            });
        </script>
        <br><br>
    <? } ?>
        <?
    }

    public function read_kelas_tc()
    {


        $obj = new KelasWebModel();
        $obj->read_filter_array = array("tc_id" => AccessRight::getMyOrgID());
        $crud = new CrudCustom();
//        $obj->printColumlistAsAttributes();
        $crud->ar_add = AccessRight::hasRight("create_kelas_tc");
        $crud->ar_edit = AccessRight::hasRight("update_kelas_tc");
        $crud->ar_delete = AccessRight::hasRight("delete_kelas_tc");

        $crud->btn_extra = '<a class="btn btn-default" onclick="openLw(\'create_operasional_kelas\', \'' . _SPPATH . 'KelasWeb/create_operasional_kelas\', \'fade\');activkanMenuKiri(\'create_operasional_kelas\');" style="cursor: pointer;">manage kelas</a>';

        $crud->run_custom($obj, "KelasWeb", "read_kelas_tc");

        die();
//        $exp = explode(",",SempoaWebSetting::getData('Jam_Kelas'));
//
//        if(count($exp)<1)die("Please set jam kelas");
//        $dowMap = array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
//        
        ?>
        <!--        <div class="table-responsive">-->
        <!--            <table class="table table-striped table-bordered">-->
        <!--                <thead>-->
        <!--                    <tr>-->
        <!--                        <th>-->
        <!--                            Hari dan Jam-->
        <!--                        </th>-->
        <!--                        <th>-->
        <!--                            Guru-->
        <!--                        </th>-->
        <!--                        <th>-->
        <!--                            Murid-->
        <!--                        </th>-->
        <!--                    </tr>-->
        <!--                </thead>-->
        <!--                <tbody>-->
        <!--                --><?
        //
//                for($x=0;$x<7;$x++){
//                    $exp = explode(",",SempoaWebSetting::getData('Jam_Kelas_'.$x));
//
//                    
        ?>
        <!--                    <tr style="background-color: #cccccc;text-align: center; font-weight: bold;">-->
        <!--                        <td colspan="3">--><? //=$dowMap[$x];
        ?><!--</td>-->
        <!--                    </tr>-->
        <!--                    --><?
        //
//                    foreach($exp as $e) {
//                        
        ?>
        <!--                        <tr>-->
        <!--                            <td>-->
        <!--                                 --><? //=$e;
        ?>
        <!---->
        <!--                            </td>-->
        <!--                            <td>-->
        <!--                                <button class="btn btn-default">Add Guru</button>-->
        <!--                            </td>-->
        <!--                            <td>-->
        <!--                                <button class="btn btn-default">Add Murid</button>-->
        <!--                            </td>-->
        <!--                        </tr>-->
        <!--                    --><?
        //
//                    }
//                }
//                
        ?>
        <!--                </tbody>-->
        <!--            </table>-->
        <!--        </div>-->
        <!---->
        <!--        --><?
        //
//        pr(SempoaWebSetting::getData('Jam_Kelas'));
//        die();
        $objKelas = new KelasWebModel();
        $tc_id = AccessRight::getMyOrgID();
//        pr($tc_id);
        $ibo_id = Generic::getMyParentID($tc_id);
        $kpo_id = Generic::getMyParentID($ibo_id);
        $ak_id = Generic::getMyParentID($kpo_id);
        $customJudul = $objKelas->customColumnList;
        $arrCustomJudul = explode(",", $customJudul);
        $arrMyKelas = $objKelas->getWhere("tc_id = '$tc_id' AND ibo_id='$ibo_id' AND kpo_id='$kpo_id' AND ak_id = '$ak_id'");
        // Weekday
        $arrWeekdays = Generic::getWeekDay();

        // Ruangan
//        $arrRuanganTC = Generic::getRuanganByTC($tc_id);
        // Guru
        $objGuru = new GuruWeb();

        $t = time();
        ?>
        <a onclick="openLw('TambahKelas', '<?= _SPPATH; ?>KelasWeb/create_kelas_tc', 'fade');">add class</a>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover crud-table" style="background-color: white;">
                <tbody>
                <tr>
                    <?
                    foreach ($arrCustomJudul as $val) {
                        if ($val == "murid_id") {
                            ?>
                            <th id="<?= $val . "_" . $t; ?>" class="clickable"><?= $val; ?>
                                <button type="button" class="btn btn-default  btn-xs pull-right">
                                    <span class="glyphicon glyphicon-plus"></span> Add Murid
                                </button>
                            </th>
                            <?
                        } elseif ($val == "guru_id") {
                            ?>
                            <th id="<?= $val . "_" . $t; ?>" class="clickable row"><?= $val; ?>


                            </th>
                            <?
                        } else {
                            ?>
                            <th id="<?= $val . "_" . $t; ?>" class="clickable"><?= $val; ?></th>
                            <?
                        }
                    }
                    ?>

                </tr>
                <?
                foreach ($arrMyKelas as $kelas) {
                    ?>
                    <tr id="KelasWebModel_<?= $kelas->id_kelas; ?>">
                        <?
                        foreach ($arrCustomJudul as $val) {
                            if ($val == "hari_kelas") {
                                ?>
                                <td id="<?= $val . "_" . $kelas->id_kelas; ?>" class="clickable">
                                    <?= $arrWeekdays[$kelas->$val];
                                    ?>
                                </td>
                            <?
                            } elseif ($val == "id_room") {
                            ?>

                                <td id="<?= $val . "_" . $kelas->id_kelas; ?>" class="clickable">
                                    <?
                                    if ($kelas->id_room != "") {
//                                            
                                        ?>

                                        <input id="<?= $val . "_input_" . $kelas->id_kelas; ?>"
                                               value="<?= $arrRuanganTC[$kelas->id_room] ?>"
                                               style="border: none; border-style: none;" readonly="readonly">

                                        <?
                                    }
                                    //
                                    ?>
                                    <select id="<?= $val . "_select_" . $kelas->id_kelas; ?>" style="display:none">
                                        <?
                                        foreach ($arrRuanganTC as $key => $value) {
                                            if ($key == $kelas->id_room) {
                                                ?>
                                                <option value="<?= $key; ?>" selected><?= $value; ?></option>
                                                <?
                                            } else {
                                                ?>
                                                <option value="<?= $key; ?>"><?= $value; ?></option>
                                                <?
                                            }
                                        }
                                        ?>

                                    </select>
                                </td>
                                <script>
                                    $('#<?= $val . "_" . $kelas->id_kelas; ?>').dblclick(function () {
                                        $('#<?= $val . "_select_" . $kelas->id_kelas; ?>').show();
                                        $('#<?= $val . "_input_" . $kelas->id_kelas; ?>').hide();

                                        $('#<?= $val . "_" . $kelas->id_kelas; ?>').change(function () {
                                            var val = $('#<?= $val . "_select_" . $kelas->id_kelas; ?>').val();
                                            // save ke db

                                        });

                                    });

                                </script>
                            <?
                            } elseif ($val == "murid_id") {
                            $search = "";
                            $arrDataMurid = explode(",", $kelas->$val);
                            for ($i = 0, $size = count($arrDataMurid); $i < $size; ++$i) {
                                if ($i == $size - 1) {
                                    $search .= "id_murid = '$arrDataMurid[$i]'";
                                } else {
                                    $search .= "id_murid = '$arrDataMurid[$i]' OR ";
                                }
                            }
                            $objDataMurid = new MuridModel();
                            $arrMurids = $objDataMurid->getWhere($search);
                            ?>

                                <td class="row" id="<?= $val . "_" . $kelas->id_kelas; ?>">

                                    <div data-toggle="buttons">
                                        <?
                                        foreach ($arrMurids as $murid) {
                                            ?>
                                            <button class="btn btn-block btn-warning" id="<?= $murid->id_murid; ?>">
                                                <?= $murid->nama_siswa; ?>
                                            </button>
                                            <?
                                        }
                                        ?>

                                    </div>
                                </td>
                            <?
                            } elseif ($val == "guru_id") {
                            ?>
                                <td class="row" id="<?= $val . "_" . $kelas->id_kelas; ?>">

                                    <div data-toggle="buttons">
                                        <?
                                        foreach ($arrMurids as $murid) {
                                            ?>
                                            <button class="btn btn-block btn-warning" id="<?= $murid->id_murid; ?>">
                                                <?= $murid->nama_siswa; ?>
                                            </button>
                                            <?
                                        }
                                        ?>

                                    </div>
                                </td>
                            <?
                            } else {
                            ?>
                                <td id="<?= $val . "_" . $kelas->id_kelas; ?>" class="clickable">
                                    <?= $kelas->$val;
                                    ?>
                                </td>
                                <?
                            }
                        }
                        ?>
                    </tr>
                    <?
                }
                ?>
                <!--                    <tr id="KelasWebModel_1">
                <td id="id_kelas_1" class="clickable">
                1
                </td>
                <td id="hari_kelas_1" class="clickable">
                Wednesday                            </td>
                <td id="jam_mulai_kelas_1" class="clickable">
                08:00:00                            </td>
                <td id="jam_akhir_kelas_1" class="clickable">
                09:30:00                            </td>
                <td id="id_room_1" class="clickable">
                0                            </td>
                <td id="murid_id_1" class="clickable">
                <button onclick="window.location.href = & quot; mailto:0 & quot; ;">Email</button>                            </td>
                <td id="guru_id_1" class="clickable">
                <button onclick="window.location.href = & quot; mailto:0 & quot; ;">Email</button>                            </td>
                </tr>-->
                </tbody>
            </table>

        </div>
        <?
    }

    public function update_kelas_tc()
    {

    }

    public function delete_kelas_tc()
    {

    }

    // IBO
    public function get_kelas_ibo()
    {
        $arrMyTC = Generic::getAllMyTC(AccessRight::getMyOrgID());
        $t = time();
        $kelas = new KelasWebModel();
        $tc_id = Key($arrMyTC);
        $arr = $kelas->getWhere("ibo_id = '" . AccessRight::getMyOrgID() . "' AND  tc_id=$tc_id ORDER BY hari_kelas ASC,jam_mulai_kelas ASC");
        $dowMap = array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
        ?>
        <section class="content-header">
            <h1><?= KEY::$TITLEDAFTARKELAS; ?></h1>
            <div class="box-tools pull-right">
                <label for="exampleInputName2">Pilih TC:</label>
                <select id="pilih_TC_<?= $t; ?>">

                    <?
                    foreach ($arrMyTC as $key => $val) {
                        ?>
                        <option value="<?= $key; ?>"><?= $val; ?></option>
                        <?
                    }
                    ?>
                </select>
                <button id="submit_daftar_kelas_<?= $t; ?>">submit</button>
                <script>
                    $('#submit_daftar_kelas_<?= $t; ?>').click(function () {
                        var tc_id = $('#pilih_TC_<?= $t; ?>').val();
                        $('#content_dafar_kelas_<?= $t; ?>').load("<?= _SPPATH; ?>KelasWebHelper/loadDaftarKelas?tc_id=" + tc_id, function () {

                        }, 'json');
                    });
                </script>
            </div>
        </section>
        <div class="clearfix">
        </div>
        <section class="content">
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>
                            Hari dan Jam
                        </th>
                        <th>
                            Ruang
                        </th>
                        <th>
                            Level
                        </th>
                        <th>
                            Guru
                        </th>
                        <th>
                            Murid
                        </th>
                    </tr>
                    </thead>
                    <tbody id="content_dafar_kelas_<?= $t; ?>">
                    <?
                    foreach ($arr as $e) {

                        $guru = new SempoaGuruModel();
                        $guru->getByID($e->guru_id);
                        $mk = new MuridKelasMatrix();
                        ?>
                        <tr>
                            <td>
                                <?= $dowMap[$e->hari_kelas]; ?>
                                <br>
                                <?= date("h:i", strtotime($e->jam_mulai_kelas)); ?>
                                - <?= date("h:i", strtotime($e->jam_akhir_kelas)); ?>

                            </td>
                            <td><?= $e->id_room; ?></td>
                            <td><?= Generic::getLevelNameByID($guru->id_level_training_guru); ?></td>
                            <td><?= $guru->nama_guru; ?> </td>

                            <td>
                                <?
                                $arrMuriddiKelas = $mk->getWhereFromMultipleTable("id_murid = murid_id AND kelas_id = '{$e->id_kelas}' AND active_status = 1", array("MuridModel"));
                                foreach ($arrMuriddiKelas as $mur) {
                                    ?>
                                    <?= $mur->nama_siswa; ?>
                                    <br>
                                    <?
                                }
                                ?>
                            </td>

                        </tr>
                        <?
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </section>

        <?
    }

    function create_operasional_kelas()
    {
        $kelas = new KelasWebModel();
        $hariini = isset($_GET['hari']) ? addslashes($_GET['hari']) : date('w');
        $tc_id = isset($_GET['tc_id']) ? addslashes($_GET['tc_id']) : AccessRight::getMyOrgID();
        $arr = $kelas->getWhere("tc_id = $tc_id AND aktiv = 1 AND hari_kelas=$hariini ORDER BY jam_mulai_kelas ASC");
        $dowMap = array('Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu');
        $t = time();

        ?>

        <section class="content-header">
            <h1>
                <div class="pull-left" style="font-size: 13px;">
                    Hari :<select id="hari_<?= $t; ?>">
                        <?
                        foreach ($dowMap as $key => $hari) {
                            $sel = "";
                            if ($key == date("w")) {
                                $sel = "selected";
                            }
                            ?>
                            <option value="<?= $key; ?>" <?= $sel; ?>><?= $hari; ?></option>
                            <?
                        }
                        ?>
                    </select>

                    <button id="submit_hari_<?= $t; ?>">submit</button>
                    <script>
                        $('#submit_hari_<?= $t; ?>').click(function () {
                            var hari = $('#hari_<?= $t; ?>').val();
                            alert(hari);
                            var tc_id = '<?= $tc_id; ?>';
                            $('#container_kelas_<?=$t;?>').load("<?= _SPPATH; ?>KelasWebHelper/loadManageKelas?hari=" + hari + "&tc_id=" + tc_id, function () {
                            }, 'json');
                        });
                    </script>
                </div>

            </h1>

        </section>

        <div class="clearfix"></div>


        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th>
                        Hari dan Jam
                    </th>
                    <th>
                        Ruang
                    </th>
                    <th>
                        Level
                    </th>
                    <th>
                        Guru
                    </th>
                    <th>
                        Murid
                    </th>
                    <th>
                        Action
                    </th>
                    <th>
                        Status Kelas
                    </th>
                </tr>
                </thead>
                <tbody id="container_kelas_<?= $t; ?>">
                <?
                foreach ($arr as $e) {

                    $guru = new SempoaGuruModel();
                    $guru->getByID($e->guru_id);
                    $lvl = new SempoaLevel();
                    $lvl->getByID($guru->id_level_training_guru);

                    $mk = new MuridKelasMatrix();
                    ?>
                    <tr>
                        <td>
                            <?= $dowMap[$e->hari_kelas]; ?>
                            <br>
                            <?= date("h:i", strtotime($e->jam_mulai_kelas)); ?>
                            - <?= date("h:i", strtotime($e->jam_akhir_kelas)); ?>

                        </td>
                        <td><?= $e->id_room; ?></td>
                        <td><?= $lvl->level; ?></td>
                        <td><b data-toggle="tooltip" title="open teacher profile" style=" cursor:pointer;"
                               onclick="openLw('Profile_Guru','<?= _SPPATH; ?>GuruWebHelper/guru_profile?guru_id=<?= $e->guru_id; ?>','fade');"><?= $guru->nama_guru; ?></b>
                            <?/*     <br>
                                Lupa Absen ? Klik <a onclick="openLw('absen_lupa_guru', '<?= _SPPATH; ?>KelasWebHelper/absen_lupa_guru?id_guru=<?= $guru->guru_id; ?>&id_kelas=<?= $e->id_kelas; ?>', 'fade');">disini..</a>
                                <hr style="padding: 2px;margin: 0px;"> */ ?>
                        </td>

                        <td>
                            <?
                            $arrMuriddiKelas = $mk->getWhereFromMultipleTable("id_murid = murid_id AND kelas_id = '{$e->id_kelas}' AND active_status = 1", array("MuridModel"));

                            foreach ($arrMuriddiKelas as $mur) {
                                ?>
                                <b data-toggle="tooltip" title="open student profile" style=" cursor:pointer;"
                                   onclick="openLw('Profile_Murid','<?= _SPPATH; ?>MuridWebHelper/profile?id_murid=<?= $mur->id_murid; ?>','fade');"><?= $mur->nama_siswa; ?></b>
                                <i data-toggle="tooltip" class="glyphicon glyphicon-remove"
                                   title="remove this student from classroom" style="cursor: pointer"
                                   onclick="remove_murid_from_kelas('<?= $mur->mk_id; ?>', '<?= $mur->id_murid; ?>', '<?= $e->id_kelas; ?>');"></i>
                                <br>
                                <?/*
                                    Lupa Absen ? Klik <a onclick="openLw('absen_lupa', '<?= _SPPATH; ?>KelasWebHelper/absen_lupa?id_murid=<?= $mur->id_murid; ?>&id_kelas=<?= $e->id_kelas; ?>', 'fade');">disini..</a>
                                    <hr style="padding: 2px;margin: 0px;">
*/ ?>
                                <?
                            }
                            ?>
                        </td>

                        <td>
                            <button class="btn btn-default" onclick="add_murid_to_kelass('<?= $e->id_kelas; ?>');">
                                Add Murid
                            </button>

                        </td>
                        <td>
                            <? if ($e->aktiv == 1) {
                                ?>
                                <button class="btn btn-default"
                                        onclick="set_kelas_nonaktiv('<?= $e->id_kelas; ?>');">
                                    Non Aktiv
                                </button>
                                <?
                            } ?>


                        </td>
                    </tr>
                    <?
                }
                ?>
                </tbody>
            </table>
        </div>
        <script>

            function set_kelas_nonaktiv(kelas_id) {
                if (confirm("Anda akan menonaktivkan kelas?"))
                    $.post("<?= _SPPATH; ?>KelasWebHelper/nonaktivkankelas", {kelas_id: kelas_id}, function (data) {
                        if (data.status_code) {
                            lwrefresh("container_kelas_<?= $t; ?>");

                        } else {
                            alert(data.status_message);
                        }
                    }, 'json');
            }


            function add_murid_to_kelass(id_kelas) {
                $('#modal_add_murid .modal-body').load("<?= _SPPATH; ?>KelasWebHelper/load_murid_tc?kelas_id=" + id_kelas + "&tc_id=<?= AccessRight::getMyOrgID(); ?>");
                $('#modal_add_murid').modal('show');
                //                $('#modal_add_murid').modal('show');
            }
            function remove_murid_from_kelas(mk_id, murid_id, kelas_id) {
                if (confirm("Anda akan menghapus Murid dari Kelas?"))
                    $.post("<?= _SPPATH; ?>KelasWebHelper/remove_murid_from_kelas", {mk_id: mk_id}, function (data) {
                        if (data.status_code) {
                            lwrefresh(selected_page);

                        } else {
                            alert(data.status_message);
                        }
                    }, 'json');
            }

            function absen_guru_dikelas(murid_id, kelas_id) {
                if (confirm("Anda akan mengabsen Guru?"))
                    $.post("<?= _SPPATH; ?>KelasWebHelper/absen_guru_dikelas",
                        {
                            guru_id: murid_id,
                            kelas_id: kelas_id
                        },
                        function (data) {
                            if (data.status_code) {
                                alert(data.status_message);
                                lwrefresh(selected_page);

                            } else {
                                alert(data.status_message);
                            }
                        }, 'json');
            }
            function absen_murid_dikelas(mk_id, murid_id, kelas_id) {
                if (confirm("Anda akan mengabsen Murid?"))
                    $.post("<?= _SPPATH; ?>KelasWebHelper/absen_murid_dikelas",
                        {
                            mk_id: mk_id,
                            murid_id: murid_id,
                            kelas_id: kelas_id
                        },
                        function (data) {
                            if (data.status_code) {
                                alert(data.status_message);
                                lwrefresh(selected_page);

                            } else {
                                alert(data.status_message);
                            }
                        }, 'json');
            }
        </script>
        <?
    }

    function create_operasional_kelas_load()
    {
        $kelas = new KelasWebModel();
        $hariini = isset($_GET['hari']) ? addslashes($_GET['hari']) : date('w');
        $tc_id = isset($_GET['tc_id']) ? addslashes($_GET['tc_id']) : AccessRight::getMyOrgID();
        $arr = $kelas->getWhere("tc_id = $tc_id AND aktiv = 1 AND hari_kelas=$hariini ORDER BY jam_mulai_kelas ASC");
        $dowMap = array('Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu');
        $t = time();

        ?>

        <tbody id="container_kelas_<?= $t; ?>">
        <?
        foreach ($arr as $e) {

            $guru = new SempoaGuruModel();
            $guru->getByID($e->guru_id);
            $lvl = new SempoaLevel();
            $lvl->getByID($guru->id_level_training_guru);

            $mk = new MuridKelasMatrix();
            ?>
            <tr>
                <td>
                    <?= $dowMap[$e->hari_kelas]; ?>
                    <br>
                    <?= date("h:i", strtotime($e->jam_mulai_kelas)); ?>
                    - <?= date("h:i", strtotime($e->jam_akhir_kelas)); ?>

                </td>
                <td><?= $e->id_room; ?></td>
                <td><?= $lvl->level; ?></td>
                <td><b data-toggle="tooltip" title="open teacher profile" style=" cursor:pointer;"
                       onclick="openLw('Profile_Guru','<?= _SPPATH; ?>GuruWebHelper/guru_profile?guru_id=<?= $e->guru_id; ?>','fade');"><?= $guru->nama_guru; ?></b>
                    <?/*     <br>
                                Lupa Absen ? Klik <a onclick="openLw('absen_lupa_guru', '<?= _SPPATH; ?>KelasWebHelper/absen_lupa_guru?id_guru=<?= $guru->guru_id; ?>&id_kelas=<?= $e->id_kelas; ?>', 'fade');">disini..</a>
                                <hr style="padding: 2px;margin: 0px;"> */ ?>
                </td>

                <td>
                    <?
                    $arrMuriddiKelas = $mk->getWhereFromMultipleTable("id_murid = murid_id AND kelas_id = '{$e->id_kelas}' AND active_status = 1", array("MuridModel"));

                    foreach ($arrMuriddiKelas as $mur) {
                        ?>
                        <b data-toggle="tooltip" title="open student profile" style=" cursor:pointer;"
                           onclick="openLw('Profile_Murid','<?= _SPPATH; ?>MuridWebHelper/profile?id_murid=<?= $mur->id_murid; ?>','fade');"><?= $mur->nama_siswa; ?></b>
                        <i data-toggle="tooltip" class="glyphicon glyphicon-remove"
                           title="remove this student from classroom" style="cursor: pointer"
                           onclick="remove_murid_from_kelas('<?= $mur->mk_id; ?>', '<?= $mur->id_murid; ?>', '<?= $e->id_kelas; ?>');"></i>
                        <br>
                        <?/*
                                    Lupa Absen ? Klik <a onclick="openLw('absen_lupa', '<?= _SPPATH; ?>KelasWebHelper/absen_lupa?id_murid=<?= $mur->id_murid; ?>&id_kelas=<?= $e->id_kelas; ?>', 'fade');">disini..</a>
                                    <hr style="padding: 2px;margin: 0px;">
*/ ?>
                        <?
                    }
                    ?>
                </td>

                <td>
                    <button class="btn btn-default" onclick="add_murid_to_kelass('<?= $e->id_kelas; ?>');">
                        Add Murid
                    </button>

                </td>
                <td>
                    <? if ($e->aktiv == 1) {
                        ?>
                        <button class="btn btn-default"
                                onclick="set_kelas_nonaktiv('<?= $e->id_kelas; ?>');">
                            Non Aktiv
                        </button>
                        <?
                    } ?>


                </td>
            </tr>
            <?
        }
        ?>
        </tbody>
        </table>
        </div>
        <script>

            function set_kelas_nonaktiv(kelas_id) {
                if (confirm("Anda akan menonaktivkan kelas?"))
                    $.post("<?= _SPPATH; ?>KelasWebHelper/nonaktivkankelas", {kelas_id: kelas_id}, function (data) {
                        if (data.status_code) {
                            lwrefresh(selected_page);

                        } else {
                            alert(data.status_message);
                        }
                    }, 'json');
            }


            function add_murid_to_kelass(id_kelas) {
                $('#modal_add_murid .modal-body').load("<?= _SPPATH; ?>KelasWebHelper/load_murid_tc?kelas_id=" + id_kelas + "&tc_id=<?= AccessRight::getMyOrgID(); ?>");
                $('#modal_add_murid').modal('show');
                //                $('#modal_add_murid').modal('show');
            }
            function remove_murid_from_kelas(mk_id, murid_id, kelas_id) {
                if (confirm("Anda akan menghapus Murid dari Kelas?"))
                    $.post("<?= _SPPATH; ?>KelasWebHelper/remove_murid_from_kelas", {mk_id: mk_id}, function (data) {
                        if (data.status_code) {
                            lwrefresh(selected_page);

                        } else {
                            alert(data.status_message);
                        }
                    }, 'json');
            }

            function absen_guru_dikelas(murid_id, kelas_id) {
                if (confirm("Anda akan mengabsen Guru?"))
                    $.post("<?= _SPPATH; ?>KelasWebHelper/absen_guru_dikelas",
                        {
                            guru_id: murid_id,
                            kelas_id: kelas_id
                        },
                        function (data) {
                            if (data.status_code) {
                                alert(data.status_message);
                                lwrefresh(selected_page);

                            } else {
                                alert(data.status_message);
                            }
                        }, 'json');
            }
            function absen_murid_dikelas(mk_id, murid_id, kelas_id) {
                if (confirm("Anda akan mengabsen Murid?"))
                    $.post("<?= _SPPATH; ?>KelasWebHelper/absen_murid_dikelas",
                        {
                            mk_id: mk_id,
                            murid_id: murid_id,
                            kelas_id: kelas_id
                        },
                        function (data) {
                            if (data.status_code) {
                                alert(data.status_message);
                                lwrefresh(selected_page);

                            } else {
                                alert(data.status_message);
                            }
                        }, 'json');
            }
        </script>
        <?
    }

    function create_operasional_kelas_tmp()
    {

        $date = new DateTime('today');
        $todayweek = $date->format("W");
        $hari = $date->format("w");
        $kelas = new KelasWebModel();
        $arr = $kelas->getWhere("tc_id = '" . AccessRight::getMyOrgID() . "' AND aktiv = 1 ORDER BY hari_kelas ASC,jam_mulai_kelas ASC");
        $dowMap = array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
        $days_now = date("Y-m-d");

        ?>
        <div style="background-color: #FFFFFF; padding: 20px; margin-top: 20px;">

            <section class="content-header">
                <div style="position: absolute;">
                    <a class="btn btn-default"
                       onclick="openLw('read_kelas_tc', '<?= _SPPATH; ?>KelasWeb/read_kelas_tc', 'fade');activkanMenuKiri('read_kelas_tc');"
                       style="cursor: pointer;">lihat kelas</a>
                </div>
                <h1 style="text-align: center; margin-bottom: 20px;">Manage Classroom</h1>
            </section>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>
                            Hari dan Jam
                        </th>
                        <th>
                            Ruang
                        </th>
                        <th>
                            Level
                        </th>
                        <th>
                            Guru
                        </th>
                        <th>
                            Murid
                        </th>
                        <th>
                            Action
                        </th>
                        <th>
                            Status Kelas
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <?
                    //pr($arr);
                    foreach ($arr as $e) {

                        $guru = new SempoaGuruModel();
                        $guru->getByID($e->guru_id);
                        $lvl = new SempoaLevel();
                        $lvl->getByID($guru->id_level_training_guru);

                        $mk = new MuridKelasMatrix();
                        ?>
                        <tr>
                            <td>
                                <?= $dowMap[$e->hari_kelas]; ?>
                                <br>
                                <?= date("h:i", strtotime($e->jam_mulai_kelas)); ?>
                                - <?= date("h:i", strtotime($e->jam_akhir_kelas)); ?>

                            </td>
                            <td><?= $e->id_room; ?></td>
                            <td><?= $lvl->level; ?></td>
                            <td><b data-toggle="tooltip" title="open teacher profile" style=" cursor:pointer;"
                                   onclick="openLw('Profile_Guru','<?= _SPPATH; ?>GuruWebHelper/guru_profile?guru_id=<?= $e->guru_id; ?>','fade');"><?= $guru->nama_guru; ?></b>
                                <?/*     <br>
                                Lupa Absen ? Klik <a onclick="openLw('absen_lupa_guru', '<?= _SPPATH; ?>KelasWebHelper/absen_lupa_guru?id_guru=<?= $guru->guru_id; ?>&id_kelas=<?= $e->id_kelas; ?>', 'fade');">disini..</a>
                                <hr style="padding: 2px;margin: 0px;"> */ ?>
                            </td>

                            <td>
                                <?
                                $arrMuriddiKelas = $mk->getWhereFromMultipleTable("id_murid = murid_id AND kelas_id = '{$e->id_kelas}' AND active_status = 1", array("MuridModel"));

                                foreach ($arrMuriddiKelas as $mur) {
                                    ?>
                                    <b data-toggle="tooltip" title="open student profile" style=" cursor:pointer;"
                                       onclick="openLw('Profile_Murid','<?= _SPPATH; ?>MuridWebHelper/profile?id_murid=<?= $mur->id_murid; ?>','fade');"><?= $mur->nama_siswa; ?></b>
                                    <i data-toggle="tooltip" class="glyphicon glyphicon-remove"
                                       title="remove this student from classroom" style="cursor: pointer"
                                       onclick="remove_murid_from_kelas('<?= $mur->mk_id; ?>', '<?= $mur->id_murid; ?>', '<?= $e->id_kelas; ?>');"></i>
                                    <br>
                                    <?/*
                                    Lupa Absen ? Klik <a onclick="openLw('absen_lupa', '<?= _SPPATH; ?>KelasWebHelper/absen_lupa?id_murid=<?= $mur->id_murid; ?>&id_kelas=<?= $e->id_kelas; ?>', 'fade');">disini..</a>
                                    <hr style="padding: 2px;margin: 0px;">
*/ ?>
                                    <?
                                }
                                ?>
                            </td>

                            <td>
                                <button class="btn btn-default" onclick="add_murid_to_kelass('<?= $e->id_kelas; ?>');">
                                    Add Murid
                                </button>

                            </td>
                            <td>
                                <? if ($e->aktiv == 1) {
                                    ?>
                                    <button class="btn btn-default"
                                            onclick="set_kelas_nonaktiv('<?= $e->id_kelas; ?>');">
                                        Non Aktiv
                                    </button>
                                    <?
                                } ?>


                            </td>
                        </tr>
                        <?
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <script>

                function set_kelas_nonaktiv(kelas_id) {
                    if (confirm("Anda akan menonaktivkan kelas?"))
                        $.post("<?= _SPPATH; ?>KelasWebHelper/nonaktivkankelas", {kelas_id: kelas_id}, function (data) {
                            if (data.status_code) {
                                lwrefresh(selected_page);

                            } else {
                                alert(data.status_message);
                            }
                        }, 'json');
                }


                function add_murid_to_kelass(id_kelas) {
                    $('#modal_add_murid .modal-body').load("<?= _SPPATH; ?>KelasWebHelper/load_murid_tc?kelas_id=" + id_kelas + "&tc_id=<?= AccessRight::getMyOrgID(); ?>");
                    $('#modal_add_murid').modal('show');
                    //                $('#modal_add_murid').modal('show');
                }
                function remove_murid_from_kelas(mk_id, murid_id, kelas_id) {
                    if (confirm("Anda akan menghapus Murid dari Kelas?"))
                        $.post("<?= _SPPATH; ?>KelasWebHelper/remove_murid_from_kelas", {mk_id: mk_id}, function (data) {
                            if (data.status_code) {
                                lwrefresh(selected_page);

                            } else {
                                alert(data.status_message);
                            }
                        }, 'json');
                }

                function absen_guru_dikelas(murid_id, kelas_id) {
                    if (confirm("Anda akan mengabsen Guru?"))
                        $.post("<?= _SPPATH; ?>KelasWebHelper/absen_guru_dikelas",
                            {
                                guru_id: murid_id,
                                kelas_id: kelas_id
                            },
                            function (data) {
                                if (data.status_code) {
                                    alert(data.status_message);
                                    lwrefresh(selected_page);

                                } else {
                                    alert(data.status_message);
                                }
                            }, 'json');
                }
                function absen_murid_dikelas(mk_id, murid_id, kelas_id) {
                    if (confirm("Anda akan mengabsen Murid?"))
                        $.post("<?= _SPPATH; ?>KelasWebHelper/absen_murid_dikelas",
                            {
                                mk_id: mk_id,
                                murid_id: murid_id,
                                kelas_id: kelas_id
                            },
                            function (data) {
                                if (data.status_code) {
                                    alert(data.status_message);
                                    lwrefresh(selected_page);

                                } else {
                                    alert(data.status_message);
                                }
                            }, 'json');
                }
            </script>

        </div>
        <?
    }

    function absen_hari_ini_tc()
    {

        $date = new DateTime('today');
        $todayweek = $date->format("W");
        $hari = $date->format("w");
        $kelas = new KelasWebModel();
        $arr = $kelas->getWhere("tc_id = '" . AccessRight::getMyOrgID() . "' AND hari_kelas = '$hari' ORDER BY jam_mulai_kelas ASC");
        $dowMap = array('Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu');
        $days_now = date("Y-m-d");


        ?>
        <div style="background-color: #FFFFFF; padding: 20px; margin-top: 20px;">
            <? if ($_GET['back']) {
                ?>
                <!--                <div  style="width: 100px; position: absolute; z-index:100;">-->
                <!--                    <button class="btn btn-default" onclick="lwclr();">BACK</button>-->
                <!--                </div>-->
                <?
            }
            ?>
            <section class="content-header">
                <h1 style="text-align: center; margin-bottom: 20px;">
                    Absensi <?= $dowMap[$hari]; ?> <?= date("d-m-Y"); ?></h1>
            </section>
            <style>
                .absen-today th {
                    color: #888888;
                }
            </style>

            <div class="table-responsive absen-today">
                <table class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>
                            Kelas
                        </th>

                        <th>
                            Guru
                        </th>
                        <th>
                            Murid
                        </th>

                    </tr>
                    </thead>
                    <tbody>
                    <?
                    foreach ($arr as $e) {

                        $guru = new SempoaGuruModel();
                        $guru->getByID($e->guru_id);
                        $lvl = new SempoaLevel();
                        $lvl->getByID($guru->id_level_training_guru);


                        $mk = new MuridKelasMatrix();
                        ?>
                        <tr>
                            <td>


                                <b style="font-size: 20px;"><?= date("h:i", strtotime($e->jam_mulai_kelas)); ?>
                                    - <?= date("h:i", strtotime($e->jam_akhir_kelas)); ?></b>
                                <br>
                                <?= $e->id_room; ?><br>
                                <i><?= $lvl->level; ?></i>
                            </td>

                            <td><b style="font-size: 20px; cursor:pointer;"
                                   onclick="openLw('Profile_Guru','<?= _SPPATH; ?>GuruWebHelper/guru_profile?guru_id=<?= $e->guru_id; ?>','fade');"><?= $guru->nama_guru; ?></b>
                                &nbsp;
                                <?
                                if (date("w") == $e->hari_kelas) {
                                    $absenGuru = new AbsenGuruModel();
                                    $absenGuru->getWhereOne("absen_guru_id=$e->guru_id AND absen_date='$days_now' AND absen_kelas_id=$e->id_kelas");
                                    if ($absenGuru->absen_id == "") {
                                        ?>

                                        <i class="glyphicon glyphicon-check blink_me" style="cursor: pointer;"
                                           onclick="absen_guru_dikelas('<?= $e->guru_id; ?>', '<?= $e->id_kelas; ?>');"></i>
                                    <? }
                                }
                                ?>
                                <?/*
                            <br>
                            <small><i>Lupa Absen ? Klik <a style="cursor: pointer;" onclick="openLw('absen_lupa_guru', '<?= _SPPATH; ?>KelasWebHelper/absen_lupa_guru?id_guru=<?= $guru->guru_id; ?>&id_kelas=<?= $e->id_kelas; ?>', 'fade');">disini..</a></i></small>
                            <hr style="padding: 2px;margin: 0px;">*/ ?>
                            </td>

                            <td>
                                <?
                                $arrMuriddiKelas = $mk->getWhereFromMultipleTable("id_murid = murid_id AND kelas_id = '{$e->id_kelas}' AND active_status = 1", array("MuridModel"));

                                foreach ($arrMuriddiKelas as $mur) {
                                    ?>
                                    <div class="murid-absen-item">
                                        <?
                                        if (date("w") == $e->hari_kelas) {

                                            $absenMurid = new AbsenEntryModel();
                                            $absenMurid->getWhereOne("absen_mk_id=$mur->mk_id AND DATE(absen_date) = '" . date("Y-m-d") . "'");
//                                        pr($absenMurid);

                                            ?>
                                            <b style="font-size: 20px; cursor:pointer;<? if ($absenMurid->absen_id != "") { ?>color:#bbbbbb;<?
                                            } ?>"
                                               onclick="openLw('Profile_Murid','<?= _SPPATH; ?>MuridWebHelper/profile?id_murid=<?= $mur->id_murid; ?>','fade');"><?= $mur->nama_siswa; ?></b>

                                            <?
                                            if ($absenMurid->absen_id == "") {
                                                ?>
                                                &nbsp; <i style="cursor: pointer;"
                                                          class="glyphicon glyphicon-check blink_me"
                                                          onclick="absen_murid_dikelas('<?= $mur->mk_id; ?>', '<?= $mur->id_murid; ?>', '<?= $e->id_kelas; ?>');"></i>
                                                <?
                                            }
                                        }
                                        ?>
                                        <? /*
                                <i class="glyphicon glyphicon-remove" onclick="remove_murid_from_kelas('<?= $mur->mk_id; ?>', '<?= $mur->id_murid; ?>', '<?= $e->id_kelas; ?>');"></i><br>

                                Lupa Absen ? Klik <a onclick="openLw('absen_lupa', '<?= _SPPATH; ?>KelasWebHelper/absen_lupa?id_murid=<?= $mur->id_murid; ?>&id_kelas=<?= $e->id_kelas; ?>', 'fade');">disini..</a>
                                <hr style="padding: 2px;margin: 0px;">
*/ ?></div>
                                    <?
                                }
                                ?>
                            </td>


                        </tr>
                        <?
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <script>
                function add_murid_to_kelass(id_kelas) {
                    $('#modal_add_murid .modal-body').load("<?= _SPPATH; ?>KelasWebHelper/load_murid_tc?kelas_id=" + id_kelas + "&tc_id=<?= AccessRight::getMyOrgID(); ?>");
                    $('#modal_add_murid').modal('show');
                    //                $('#modal_add_murid').modal('show');
                }
                function remove_murid_from_kelas(mk_id, murid_id, kelas_id) {
                    if (confirm("Anda akan menghapus Murid dari Kelas?"))
                        $.post("<?= _SPPATH; ?>KelasWebHelper/remove_murid_from_kelas", {mk_id: mk_id}, function (data) {
                            if (data.status_code) {
                                lwrefresh(selected_page);

                            } else {
                                alert(data.status_message);
                            }
                        }, 'json');
                }

                function absen_guru_dikelas(murid_id, kelas_id) {
                    if (confirm("Anda akan mengabsen Guru?"))
                        $.post("<?= _SPPATH; ?>KelasWebHelper/absen_guru_dikelas",
                            {
                                guru_id: murid_id,
                                kelas_id: kelas_id
                            },
                            function (data) {
                                if (data.status_code) {
                                    alert(data.status_message);
                                    lwrefresh(selected_page);

                                } else {
                                    alert(data.status_message);
                                }
                            }, 'json');
                }
                function absen_murid_dikelas(mk_id, murid_id, kelas_id) {
                    if (confirm("Anda akan mengabsen Murid?"))
                        $.post("<?= _SPPATH; ?>KelasWebHelper/absen_murid_dikelas",
                            {
                                mk_id: mk_id,
                                murid_id: murid_id,
                                kelas_id: kelas_id
                            },
                            function (data) {
                                if (data.status_code) {
                                    alert(data.status_message);
                                    lwrefresh(selected_page);

                                } else {
                                    alert(data.status_message);
                                }
                            }, 'json');
                }
            </script>
        </div>
        <?
    }
}
