<?php

/**
 * Created by PhpStorm.
 * User: elroy
 * Date: 9/9/16
 * Time: 5:24 PM
 */
class KelasWebHelper extends WebService
{

    function updatejam_kelas()
    {
        $jam = addslashes($_POST['jam']);
        $hari = addslashes($_POST['hari']);

        SempoaWebSetting::setData('Jam_Kelas_' . $hari, $jam, AccessRight::getMyOrgID());

        $json['status_code'] = 1;
        $json['status_message'] = "OK";
        echo json_encode($json);
        die();
    }

    function load_murid_tc()
    {

        $tc_id = addslashes($_GET['tc_id']);
        $kelas_id = addslashes($_GET['kelas_id']);

        $kelas = new KelasWebModel();
        $kelas->getByID($kelas_id);


        $mk = new MuridKelasMatrix();
        $arrMuriddiKelas = $mk->getWhere(" kelas_id = '$kelas_id' AND active_status = 1");

        $ids = array();
        foreach ($arrMuriddiKelas as $mur) {
            $ids[] = "id_murid != '" . $mur->murid_id . "'";
        }
        $imp = implode(" AND ", $ids);
        if (count($ids) > 0)
            $imp = $imp . " AND ";

        $mur = new MuridModel();
        $arrMuridYangBlomKursus = $mur->getWhere($imp . " id_level_sekarang <= '{$kelas->level}' AND murid_tc_id = '$tc_id' AND status=1 ORDER BY nama_siswa ASC");

//        echo $imp." id_level_sekarang = '{$kelas->level}' AND murid_tc_id = '$tc_id'";
//        pr($arrMuridYangBlomKursus);
        ?>
        <select class="form-control" id="murid_kelas_select">
            <?
            foreach ($arrMuridYangBlomKursus as $num => $muri) {
                ?>
                <option
                    value="<?= $muri->id_murid; ?>"><?= $muri->nama_siswa . " - " . Generic::getLevelNameByID($muri->id_level_sekarang); ?></option>
                <?
            }
            ?>
        </select>
        <script>
            id_kelas = '<?= $kelas_id; ?>';
            <? if (count($arrMuridYangBlomKursus) > 0) { ?>
            id_murid = '<?= $arrMuridYangBlomKursus[0]->id_murid; ?>';
            <? } ?>
            $('#murid_kelas_select').change(function () {
                var slc = $('#murid_kelas_select').val();
                id_murid = slc;
                //                id_kelas = '<? //=$kelas_id;               ?>//';
            });
        </script>
        <?
    }

    function add_murid_to_kelas()
    {

        $id_murid = addslashes($_POST['id_murid']);
        $kelas_id = addslashes($_POST['id_kelas']);

        $objKelas = new KelasWebModel();
        $arrKelas = $objKelas->getWhere("id_kelas='$kelas_id'");

        $murid = new MuridModel();
        $murid->getByID($id_murid);

        $mk = new MuridKelasMatrix();
        $mk->kelas_id = $kelas_id;
        $mk->murid_id = $id_murid;
        $mk->active_status = 1;
        $mk->tc_id = AccessRight::getMyOrgID();
        $mk->active_date = leap_mysqldate();
        $mk->guru_id = $arrKelas[0]->guru_id;
        $mk->nama_guru = Generic::getGuruNamebyID($arrKelas[0]->guru_id);
        $mk->level_kelas = $arrKelas[0]->level;
        $mk->level_murid = $murid->id_level_sekarang;

        if ($mk->save()) {

            $json['status_code'] = 1;
            $json['status_message'] = "Ok";
            echo json_encode($json);
            die();
        }
        $json['status_code'] = 0;
        $json['status_message'] = "Save Failed";
        echo json_encode($json);
        die();
    }

    function remove_murid_from_kelas()
    {
        $mk_id = addslashes($_POST['mk_id']);
//        $kelas_id = addslashes($_POST['id_kelas']);

        $mk = new MuridKelasMatrix();
        $mk->getByID($mk_id);
        $mk->active_status = 0;
        $mk->nonactive_date = leap_mysqldate();

        if ($mk->save()) {

            $json['status_code'] = 1;
            $json['status_message'] = "Ok";
            echo json_encode($json);
            die();
        }
        $json['status_code'] = 0;
        $json['status_message'] = "Save Failed";
        echo json_encode($json);
        die();
    }

    function absen_murid_dikelas()
    {
        $mk_id = addslashes($_POST['mk_id']);
        $murid_id = addslashes($_POST['murid_id']);
        $kelas_id = addslashes($_POST['kelas_id']);

        $kelas = new KelasWebModel();
        $kelas->getByID($kelas_id);

        $hari_ini = date("Y-m-d");


        $abs = new AbsenEntryModel();
        //apa perlu cek apakah hari ini sdh absen ??
        $cnt = $abs->getJumlah("absen_date = '$hari_ini' AND absen_murid_id = '$murid_id' AND absen_kelas_id = '$kelas_id'");
        if ($cnt > 0) {
            $json['status_code'] = 0;
            $json['status_message'] = "Hari ini sudah absen";
            echo json_encode($json);
            die();
        }

//        $kelas_id = addslashes($_POST['id_kelas']);

        $abs->absen_date = date("Y-m-d");
        $abs->absen_pengabsen_id = Account::getMyID();
        $abs->absen_kelas_id = $kelas_id;
        $abs->absen_murid_id = $murid_id;
        $abs->absen_reg_date = leap_mysqldate();
        $abs->absen_mk_id = $mk_id;
        $abs->absen_tc_id = AccessRight::getMyOrgID();
        $abs->absen_guru_id = $kelas->guru_id;
        $murid = new MuridModel();
        $murid->getByID($murid_id);
        $abs->absen_ibo_id = $murid->murid_ibo_id;
        $abs->absen_kpo_id = $murid->murid_kpo_id;
        $abs->absen_ak_id = $murid->murid_ak_id;
        if ($abs->save()) {

            $rekapAbsenGuru = new RekapAbsenCoach();
            $date = new DateTime('today');
            $rekapAbsenGuru->addAbsenCouchFromMurid($kelas->guru_id, $murid->murid_ak_id, $murid->murid_kpo_id, $murid->murid_ibo_id, $murid->murid_tc_id, $date, $murid->id_level_sekarang);
            $json['status_code'] = 1;
            $json['status_message'] = "Murid sudah berhasil diabsen!";
            echo json_encode($json);
            die();
        }

        $json['status_code'] = 0;
        $json['status_message'] = "Save Failed";
        echo json_encode($json);
        die();
    }

    function absen_murid_dikelas_WS($mk_id, $murid_id, $kelas_id)
    {


        $kelas = new KelasWebModel();
        $kelas->getByID($kelas_id);

        $hari_ini = date("Y-m-d");


        $abs = new AbsenEntryModel();
        //apa perlu cek apakah hari ini sdh absen ??
        $cnt = $abs->getJumlah("absen_date = '$hari_ini' AND absen_murid_id = '$murid_id' AND absen_kelas_id = '$kelas_id'");
        if ($cnt > 0) {

            echo "Hari ini sudah absen";
            die();
        }

//        $kelas_id = addslashes($_POST['id_kelas']);

        $abs->absen_date = date("Y-m-d");
        $abs->absen_pengabsen_id = Account::getMyID();
        $abs->absen_kelas_id = $kelas_id;
        $abs->absen_murid_id = $murid_id;
        $abs->absen_reg_date = leap_mysqldate();
        $abs->absen_mk_id = $mk_id;
        $abs->absen_guru_id = $kelas->guru_id;
        //parent2 si bos
        $murid = new MuridModel();
        $murid->getByID($murid_id);
        $abs->absen_tc_id = $murid->murid_tc_id;
        $abs->absen_ibo_id = $murid->murid_ibo_id;
        $abs->absen_kpo_id = $murid->murid_kpo_id;
        $abs->absen_ak_id = $murid->murid_ak_id;
        if ($abs->save()) {

            $rekapAbsenGuru = new RekapAbsenCoach();
            $date = new DateTime('today');
            $rekapAbsenGuru->addAbsenCouchFromMurid($kelas->guru_id, $murid->murid_ak_id, $murid->murid_kpo_id, $murid->murid_ibo_id, $murid->murid_tc_id, $date, $murid->id_level_sekarang);

        }

    }

    public function absen_guru_dikelas()
    {
//        $mk_id = addslashes($_POST['mk_id']);
        $guru_id = addslashes($_POST['guru_id']);
        $kelas_id = addslashes($_POST['kelas_id']);

        $kelas = new KelasWebModel();
        $kelas->getByID($kelas_id);

        $hari_ini = date("Y-m-d");


        $abs = new AbsenGuruModel();
        //apa perlu cek apakah hari ini sdh absen ??
        $cnt = $abs->getJumlah("absen_date = '$hari_ini' AND absen_guru_id = '$guru_id' AND absen_kelas_id = '$kelas_id'");
        if ($cnt > 0) {
            $json['status_code'] = 0;
            $json['status_message'] = "Hari ini sudah absen";
            echo json_encode($json);
            die();
        }

        $abs->absen_date = date("Y-m-d");
        $abs->absen_pengabsen_id = Account::getMyID();
        $abs->absen_kelas_id = $kelas_id;
        $abs->absen_guru_id = $guru_id;
        $abs->absen_reg_date = leap_mysqldate();
//        $abs->absen_mk_id = $mk_id;

        $guru = new SempoaGuruModel();
        $guru->getByID($guru_id);
        $abs->absen_ak_id = $guru->guru_ak_id;
        $abs->absen_kpo_id = $guru->guru_kpo_id;
        $abs->absen_ibo_id = $guru->guru_ibo_id;
        $abs->absen_tc_id = $guru->guru_tc_id;

        if ($abs->save()) {

            $rekapAbsenGuru = new RekapAbsenCoach();
            $date = new DateTime('today');
            $week = $date->format("W");
            $id_absen_coach = $guru_id . "_" . $week;

            $count = $rekapAbsenGuru->searchMuridSdhAbsen($id_absen_coach, $date);
            if ($count > 0) {
                $rekapAbsenGuru->updateGuruName($id_absen_coach, $guru_id);
            } else {
                $rekapAbsenGuru->addAbsenCouchFromGuru($guru_id, $guru->guru_ak_id, $guru->guru_kpo_id, $guru->guru_ibo_id, $guru->guru_tc_id, $date);
            }

            $json['status_code'] = 1;
            $json['status_message'] = "Guru berhasil diabsen!";
            echo json_encode($json);
            die();
        }

        $json['status_code'] = 0;
        $json['status_message'] = "Save Failed";
        echo json_encode($json);
        die();
    }

    function absen_murid_dikelas_hari()
    {
        $mk_id = addslashes($_POST['mk_id']);
        $murid_id = addslashes($_POST['murid_id']);
        $kelas_id = addslashes($_POST['kelas_id']);
        $hari = addslashes($_POST['hari']);

        $kelas = new KelasWebModel();
        $kelas->getByID($kelas_id);

//        $hari_ini = date("Y-m-d");


        $abs = new AbsenEntryModel();
        //apa perlu cek apakah hari ini sdh absen ??
        $cnt = $abs->getJumlah("absen_date = '$hari' AND absen_murid_id = '$murid_id' AND absen_kelas_id = '$kelas_id'");
        if ($cnt > 0) {
            $json['status_code'] = 0;
            $json['status_message'] = "Hari ini sudah absen";
            echo json_encode($json);
            die();
        }

//        $kelas_id = addslashes($_POST['id_kelas']);

        $abs->absen_date = $hari;
        $abs->absen_pengabsen_id = Account::getMyID();
        $abs->absen_kelas_id = $kelas_id;
        $abs->absen_murid_id = $murid_id;
        $abs->absen_reg_date = leap_mysqldate();
        $abs->absen_mk_id = $mk_id;
        $abs->absen_guru_id = $kelas->guru_id;

        if (AccessRight::getMyOrgType() == "ibo") {
//             $myTCID = AccessRight::getMyOrgID();
            $myIBOID = AccessRight::getMyOrgID();
            $myKPOID = Generic::getMyParentID($myIBOID);
            $myAKID = Generic::getMyParentID($myKPOID);
//            $abs->absen_tc_id = $myTCID;
            $abs->absen_ibo_id = $myIBOID;
            $abs->absen_kpo_id = $myKPOID;
            $abs->absen_ak_id = $myAKID;
        } elseif (AccessRight::getMyOrgType() == "tc") {
            $myTCID = AccessRight::getMyOrgID();
            $myIBOID = Generic::getMyParentID($myTCID);
            $myKPOID = Generic::getMyParentID($myIBOID);
            $myAKID = Generic::getMyParentID($myKPOID);
            $abs->absen_tc_id = $myTCID;
            $abs->absen_ibo_id = $myIBOID;
            $abs->absen_kpo_id = $myKPOID;
            $abs->absen_ak_id = $myAKID;
        }


        if ($abs->save()) {
            $murid = new MuridModel();
            $murid->getByID($murid_id);
            $rekapAbsenGuru = new RekapAbsenCoach();
            $date = new DateTime($hari);
            $rekapAbsenGuru->addAbsenCouchFromMurid($kelas->guru_id, $murid->murid_ak_id, $murid->murid_kpo_id, $murid->murid_ibo_id, $murid->murid_tc_id, $date, $murid->id_level_sekarang);


            $json['status_code'] = 1;
            $json['status_message'] = "Murid berhasil diabsen!";
            echo json_encode($json);
            die();
        }

        $json['status_code'] = 0;
        $json['status_message'] = "Save Failed";
        echo json_encode($json);
        die();
    }

    function absen_lupa()
    {

        $id_murid = addslashes($_GET['id_murid']);
        $kelas_id = addslashes($_GET['id_kelas']);

        $dowMap = array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');


        $murid = new MuridModel();
        $murid->getByID($id_murid);

        $kelas = new KelasWebModel();
        $kelas->getByID($kelas_id);


        $id_mk = '';
        $objmk = new MuridKelasMatrix();
        $arrMK = $objmk->getWhere("murid_id='$id_murid' AND kelas_id='$kelas_id'");
        if (count($arrMK) == 0) {

        } else {
            $id_mk = $arrMK[0]->mk_id;
        }
//        pr(date("Y-m-d"));


        $mk = new MuridKelasMatrix();
        $arrMuriddiKelas = $mk->getWhere("murid_id = '$id_murid' AND active_status = 1");

//        pr($arrMuriddiKelas);
        ?>
        <div style="text-align: center; padding-bottom: 30px;">
            <h1><?= $murid->nama_siswa; ?></h1>
            <i>Absensi di kelas</i>
        </div>

        <form class="form-horizontal">
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Pilih Kelas</label>
                <div class="col-sm-10">
                    <select id="kelas_piliha" class="form-control">
                        <?
                        foreach ($arrMuriddiKelas as $num => $kls) {
                            $sel = "";
                            if ($kls->kelas_id == $kelas_id)
                                $sel = "selected";

                            $kelasku = new KelasWebModel();
                            $kelasku->getByID($kls->kelas_id);

                            $kls->kelas_object = $kelasku;
                            ?>
                            <option <?= $sel; ?> value="<?= $kelasku->id_kelas; ?>">
                                <b><?= $dowMap[$kelasku->hari_kelas]; ?>
                                    <?= date("h:i", strtotime($kelasku->jam_mulai_kelas)); ?>
                                    - <?= date("h:i", strtotime($kelasku->jam_akhir_kelas)); ?>
                                </b></option>
                            <?
                            //ambil default mk
                            if ($id_mk == '' && $num == 0) {
                                $id_mk = $kls->mk_id;
                            }
                            if ($kelas_id == '' && $num == 0) {
                                $kelas_id = $kls->kelas_id;
                            }
                        }
                        ?>
                    </select>
                    <script>
                        $('#kelas_piliha').change(function () {
                            var slc = $('#kelas_piliha').val();
                            mk_id = <?= $kls->mk_id; ?>;
                            $('.selector-tanggal').hide();
                            $('#select_container_date_<?= $id_murid; ?>_' + slc).show();

                        });
                    </script>
                </div>
            </div>
            <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label">Pilih Tanggal</label>
                <div class="col-sm-10">
                    <?
                    foreach ($arrMuriddiKelas as $kls) {

                        $kelasku = new KelasWebModel();
                        $kelasku->getByID($kls->kelas_id);

                        $arrHari = $this->getAllDaysInAMonth(date("Y"), date("n"), Generic::getWeekDay()[$kelasku->hari_kelas]);

                        $mk = new AbsenEntryModel();
                        $arrMatrix = $mk->getWhere("absen_kelas_id = '{$kls->kelas_id}' AND absen_murid_id = '$id_murid' ORDER BY absen_date DESC LIMIT 0,10");


                        $arrSudah = array();
                        foreach ($arrMatrix as $mat) {
                            $arrSudah[] = $mat->absen_date;
                        }
                        ?>
                        <div <? if ($kls->kelas_id != $kelas_id) { ?>style="display: none;" <? } ?>
                             id="select_container_date_<?= $id_murid . "_" . $kls->kelas_id; ?>"
                             class="selector-tanggal">

                            <select class="form-control " id="select_<?= $id_murid . "_" . $kls->kelas_id; ?>">
                                <?
                                foreach ($arrHari as $hari) {
                                    $sel = "";
                                    if (in_array($hari->format("Y-m-d"), $arrSudah)) {
                                        $sel = "disabled " . " Sudah diabsensi";
                                    }
                                    if (strtotime($hari->format('Y-m-d')) - time() > 0 && $hari->format('Y-m-d') != date("Y-m-d"))
                                        continue;
                                    ?>
                                    <option <?= $sel; ?>>
                                        <?= $hari->format('Y-m-d'); ?> <?= $sel; ?>
                                    </option>
                                <? } ?>
                            </select>
                        </div>
                    <? } ?>

                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="button" id="submitabsen_<?= $id_murid; ?>" class="btn btn-default">Submit Absen
                    </button>
                </div>
            </div>
        </form>


        <script>
            var mk_id = '<?= $id_mk; ?>';
            var murid_id = '<?= $id_murid; ?>';
            var kelas_id = '<?= $kelas_id; ?>';

            $('#submitabsen_<?= $id_murid; ?>').click(function () {

                var kelas_id = $('#kelas_piliha').val();
                var hari = $('#select_<?= $id_murid; ?>_' + kelas_id).val();
                if (mk_id != '' && hari !== null) {

                    absen_murid_dikelas_hari(mk_id, murid_id, kelas_id, hari);
                } else {
                    alert("Please select date");
                }

            });

            function absen_murid_dikelas_hari(mk_id, murid_id, kelas_id, hari) {
                if (confirm("Anda akan mengabsen Murid??"))
                    $.post("<?= _SPPATH; ?>KelasWebHelper/absen_murid_dikelas_hari",
                        {
                            mk_id: mk_id,
                            murid_id: murid_id,
                            kelas_id: kelas_id,
                            hari: hari
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

    function absen_lupa_guru()
    {
        $id_guru = addslashes($_GET['id_guru']);
        $kelas_id = addslashes($_GET['id_kelas']);
        $dowMap = array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
        $guru = new SempoaGuruModel();
        $guru->getByID($id_guru);

        $kelas = new KelasWebModel();
        $kelas->getByID($kelas_id);


        $id_mk = '';
        $objmk = new MuridKelasMatrix();
        $arrMK = $objmk->getWhere("guru_id='$id_guru' AND kelas_id='$kelas_id'");
        if (count($arrMK) == 0) {
            ?>
            <script>
                alert("Guru tidak mempunyai murid!");
            </script>
            <?
            die();
        } else {
            $id_mk = $arrMK[0]->mk_id;
        }
        $mk = new MuridKelasMatrix();
        $arrGurudiKelas = $mk->getWhere("guru_id='$id_guru' AND active_status = 1  GROUP BY kelas_id");
//        pr($arrGurudiKelas);
        ?>

        <div style="text-align: center; padding-bottom: 30px;">
            <h1><?= $guru->nama_guru; ?></h1>
            <i>Absensi di kelas</i>
        </div>

        <form class="form-horizontal">
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Pilih Kelas</label>
                <div class="col-sm-10">
                    <select id="kelas_pilihan_<?= $id_guru; ?>" class="form-control">
                        <?
                        foreach ($arrGurudiKelas as $num => $kls) {
                            $sel = "";
                            if ($kls->kelas_id == $kelas_id)
                                $sel = "selected";

                            $kelasku = new KelasWebModel();
                            $kelasku->getByID($kls->kelas_id);

                            $kls->kelas_object = $kelasku;
                            ?>
                            <option <?= $sel; ?> value="<?= $kelasku->id_kelas; ?>">
                                <b><?= $dowMap[$kelasku->hari_kelas]; ?>
                                    <?= date("h:i", strtotime($kelasku->jam_mulai_kelas)); ?>
                                    - <?= date("h:i", strtotime($kelasku->jam_akhir_kelas)); ?>
                                </b></option>
                            <?
                            //ambil default mk
                            if ($id_mk == '' && $num == 0) {
                                $id_mk = $kls->mk_id;
                            }
                            if ($kelas_id == '' && $num == 0) {
                                $kelas_id = $kls->kelas_id;
                            }
                        }
                        ?>
                    </select>
                    <script>
                        $('#kelas_pilihan_<?= $id_guru; ?>').change(function () {
                            var slc = $('#kelas_pilihan_<?= $id_guru; ?>').val();
                            mk_id = <?= $kls->mk_id; ?>;
                            $('.selector-tanggal').hide();
                            $('#select_container_date_<?= $id_guru; ?>_' + slc).show();

                        });
                    </script>
                </div>
            </div>

            <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label">Pilih Tanggal</label>
                <div class="col-sm-10">
                    <?
                    foreach ($arrGurudiKelas as $kls) {

                        $kelasku = new KelasWebModel();
                        $kelasku->getByID($kls->kelas_id);

                        $arrHari = $this->getAllDaysInAMonth(date("Y"), date("n"), Generic::getWeekDay()[$kelasku->hari_kelas]);
//                        pr($arrHari);
                        $mk = new AbsenGuruModel();
                        $arrMatrix = $mk->getWhere("absen_kelas_id = '{$kls->kelas_id}' AND absen_guru_id = '$id_guru' ORDER BY absen_date DESC LIMIT 0,10");

                        $arrSudah = array();
                        foreach ($arrMatrix as $mat) {
                            $arrSudah[] = $mat->absen_date;
                        }
//                        pr($arrSudah);
                        ?>
                        <div <? if ($kls->kelas_id != $kelas_id) { ?>style="display: none;" <? } ?>
                             id="select_container_date_<?= $id_guru . "_" . $kls->kelas_id; ?>"
                             class="selector-tanggal">

                            <select class="form-control " id="select_<?= $id_guru . "_" . $kls->kelas_id; ?>">
                                <?
                                foreach ($arrHari as $hari) {
                                    $sel = "";
                                    if (in_array($hari->format("Y-m-d"), $arrSudah)) {
                                        $sel = "disabled " . " Sudah diabsensi";
                                    }
                                    if (strtotime($hari->format('Y-m-d')) - time() > 0 && $hari->format('Y-m-d') != date("Y-m-d"))
                                        continue;
                                    ?>
                                    <option <?= $sel; ?>>
                                        <?= $hari->format('Y-m-d'); ?> <?= $sel; ?>
                                    </option>
                                <? } ?>
                            </select>
                        </div>
                    <? } ?>

                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="button" id="submitabsen_<?= $id_guru; ?>" class="btn btn-default">Submit Absen
                    </button>
                </div>
            </div>

            <script>
                var mk_id = '<?= $id_mk; ?>';
                var guru_id = '<?= $id_guru; ?>';
                var kelas_id = '<?= $kelas_id; ?>';

                $('#submitabsen_<?= $id_guru; ?>').click(function () {

                    var kelas_id = $('#kelas_pilihan_<?= $id_guru; ?>').val();
                    var hari = $('#select_<?= $id_guru; ?>_' + kelas_id).val();
                    if (mk_id != '' && hari !== null) {

                        absen_guru_dikelas_hari(mk_id, guru_id, kelas_id, hari);
                    } else {
                        alert("Please select date");
                    }

                });

                function absen_guru_dikelas_hari(mk_id, c, kelas_id, hari) {
                    if (confirm("Anda akan mengabsen Guru?"))
                        $.post("<?= _SPPATH; ?>KelasWebHelper/absen_guru_dikelas_hari",
                            {
                                mk_id: mk_id,
                                guru_id: guru_id,
                                kelas_id: kelas_id,
                                hari: hari
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
        </form>


        <?
    }

    function absen_guru_dikelas_hari()
    {
        $mk_id = addslashes($_POST['mk_id']);
        $guru_id = addslashes($_POST['guru_id']);
        $kelas_id = addslashes($_POST['kelas_id']);
        $hari = addslashes($_POST['hari']);

        $kelas = new KelasWebModel();
        $kelas->getByID($kelas_id);

        $abs = new AbsenGuruModel();
        //apa perlu cek apakah hari ini sdh absen ??
        $cnt = $abs->getJumlah("absen_date = '$hari' AND absen_guru_id = '$guru_id' AND absen_kelas_id = '$kelas_id'");
        if ($cnt > 0) {
            $json['status_code'] = 0;
            $json['status_message'] = "Hari ini sudah absen";
            echo json_encode($json);
            die();
        }

//        $kelas_id = addslashes($_POST['id_kelas']);

        $abs->absen_date = $hari;
        $abs->absen_pengabsen_id = Account::getMyID();
        $abs->absen_kelas_id = $kelas_id;
        $abs->absen_guru_id = $guru_id;
        $abs->absen_reg_date = leap_mysqldate();
        $abs->absen_mk_id = $mk_id;
        $abs->absen_guru_id = $kelas->guru_id;
        //parent2 si bos
        if (AccessRight::getMyOrgType() == "ibo") {
//             $myTCID = AccessRight::getMyOrgID();
            $myIBOID = AccessRight::getMyOrgID();
            $myKPOID = Generic::getMyParentID($myIBOID);
            $myAKID = Generic::getMyParentID($myKPOID);
//            $abs->absen_tc_id = $myTCID;
            $abs->absen_ibo_id = $myIBOID;
            $abs->absen_kpo_id = $myKPOID;
            $abs->absen_ak_id = $myAKID;
        } elseif (AccessRight::getMyOrgType() == "tc") {
            $myTCID = AccessRight::getMyOrgID();
            $myIBOID = Generic::getMyParentID($myTCID);
            $myKPOID = Generic::getMyParentID($myIBOID);
            $myAKID = Generic::getMyParentID($myKPOID);
            $abs->absen_tc_id = $myTCID;
            $abs->absen_ibo_id = $myIBOID;
            $abs->absen_kpo_id = $myKPOID;
            $abs->absen_ak_id = $myAKID;
        }


        if ($abs->save()) {

            $rekapAbsenGuru = new RekapAbsenCoach();
            $date = new DateTime($hari);
            $week = $date->format("W");
            $id_absen_coach = $guru_id . "_" . $week;
            $count = $rekapAbsenGuru->searchMuridSdhAbsen($id_absen_coach, $date);
            $guru = new SempoaGuruModel();
            $guru->getByID($guru_id);
            if ($count > 0) {
                $rekapAbsenGuru->updateGuruName($id_absen_coach, $guru_id);
            } else {
                $rekapAbsenGuru->addAbsenCouchFromGuru($guru_id, $guru->guru_ak_id, $guru->guru_kpo_id, $guru->guru_ibo_id, $guru->guru_tc_id, $date);
            }


            $json['status_code'] = 1;
            $json['status_message'] = "Guru berhasil diabsen!";
            echo json_encode($json);
            die();
        }

        $json['status_code'] = 0;
        $json['status_message'] = "Save Failed";
        echo json_encode($json);
        die();
    }

    function getAllDaysInAMonth($year, $month, $day = 'Monday', $daysError = 3)
    {
        $dateString = 'first ' . $day . ' of ' . $year . '-' . $month;

        if (!strtotime($dateString)) {
            throw new \Exception('"' . $dateString . '" is not a valid strtotime');
        }

        $startDay = new \DateTime($dateString);

        if ($startDay->format('j') > $daysError) {
            $startDay->modify('- 7 days');
        }

        $days = array();

        while ($startDay->format('Y-m') <= $year . '-' . str_pad($month, 2, 0, STR_PAD_LEFT)) {
            $days[] = clone($startDay);
            $startDay->modify('+ 7 days');
        }

        return $days;
    }

    function absen_profile()
    {

        $id_murid = addslashes($_GET['id_murid']);

        $dowMap = Generic::WeekMap();


        $murid = new MuridModel();
        $murid->getByID($id_murid);

        $kelas = new KelasWebModel();
        $arrKelas = $kelas->getWhere("murid_id='$id_murid'");
        $kelas_id = $arrKelas[0]->id_kelas;
        $kelas->getByID($kelas_id);


        $id_mk = '';
        $objmk = new MuridKelasMatrix();
        $arrMK = $objmk->getWhere("murid_id='$id_murid' AND kelas_id='$kelas_id'");
        $t = time();
        if (count($arrMK) == 0) {

        } else {
            $id_mk = $arrMK[0]->mk_id;
        }

        $mk = new MuridKelasMatrix();
        $arrMuriddiKelas = $mk->getWhere("murid_id = '$id_murid' AND active_status = 1");
        if (count($arrMuriddiKelas) == 0) {
            /*
            ?>
            <script>
                alert("Murid belum mempunyai kelas!");
                lwclose(selected_page);

//                openLw('Profile_Murid_<?//= $t; ?>//', '<?//= _SPPATH; ?>///MuridWebHelper/profile?id_murid=<?//= $id_murid; ?>//', 'fade')
//                lwrefresh(selected_page);
            </script>

            <?*/
            echo "Murid Belum Mempunyai Kelas";
            die();
        }
        ?>
        <div style="text-align: center; padding-bottom: 30px;">
            <h1><?= $murid->nama_siswa; ?>

                <div class="pull-right">
                    <button class="btn btn-default" onclick="back_to_profile_murid('<?= $id_murid; ?>');">back to
                        profile
                    </button>
                </div>
                <br>
                <i>Absensi di kelas</i>
            </h1>


        </div>

        <form class="form-horizontal">
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Pilih Kelas</label>
                <div class="col-sm-10">
                    <select id="kelas_piliha_<?= $t; ?>" class="form-control">
                        <?
                        foreach ($arrMuriddiKelas as $num => $kls) {
                            $sel = "";
                            if ($kls->kelas_id == $kelas_id)
                                $sel = "selected";

                            $kelasku = new KelasWebModel();
                            $kelasku->getByID($kls->kelas_id);

                            $kls->kelas_object = $kelasku;
                            ?>
                            <option <?= $sel; ?> value="<?= $kelasku->id_kelas; ?>">
                                <b><?= $dowMap[$kelasku->hari_kelas]; ?>
                                    <?= date("h:i", strtotime($kelasku->jam_mulai_kelas)); ?>
                                    - <?= date("h:i", strtotime($kelasku->jam_akhir_kelas)); ?>
                                </b></option>
                            <?
                            //ambil default mk
                            if ($id_mk == '' && $num == 0) {
                                $id_mk = $kls->mk_id;
                            }
                            if ($kelas_id == '' && $num == 0) {
                                $kelas_id = $kls->kelas_id;
                            }
                        }
                        ?>
                    </select>
                    <script>
                        $('#kelas_piliha_<?=$t;?>').change(function () {
                            var slc = $('#kelas_piliha_<?=$t;?>').val();
                            mk_id = <?= $kls->mk_id; ?>;
                            $('.selector-tanggal').hide();
                            $('#select_container_date_<?= $id_murid; ?>_' + slc).show();

                        });
                    </script>
                </div>
            </div>
            <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label">Pilih Tanggal</label>
                <div class="col-sm-10">
                    <?
                    foreach ($arrMuriddiKelas as $kls) {

                        $kelasku = new KelasWebModel();
                        $kelasku->getByID($kls->kelas_id);

                        $arrHari = $this->getAllDaysInAMonth(date("Y"), date("n"), Generic::getWeekDay()[$kelasku->hari_kelas]);

                        $mk = new AbsenEntryModel();
                        $arrMatrix = $mk->getWhere("absen_kelas_id = '{$kls->kelas_id}' AND absen_murid_id = '$id_murid' ORDER BY absen_date DESC LIMIT 0,10");


                        $arrSudah = array();
                        foreach ($arrMatrix as $mat) {
                            $arrSudah[] = $mat->absen_date;
                        }
                        ?>
                        <div <? if ($kls->kelas_id != $kelas_id) { ?>style="display: none;" <? } ?>
                             id="select_container_date_<?= $id_murid . "_" . $kls->kelas_id; ?>"
                             class="selector-tanggal">

                            <select class="form-control " id="select_<?= $id_murid . "_" . $kls->kelas_id; ?>">
                                <?
                                foreach ($arrHari as $hari) {
                                    $sel = "";
                                    if (in_array($hari->format("Y-m-d"), $arrSudah)) {
                                        $sel = "disabled " . " Sudah diabsensi";
                                    }
                                    if (strtotime($hari->format('Y-m-d')) - time() > 0 && $hari->format('Y-m-d') != date("Y-m-d"))
                                        continue;
                                    ?>
                                    <option <?= $sel; ?>>
                                        <?= $hari->format('Y-m-d'); ?> <?= $sel; ?>
                                    </option>
                                <? } ?>
                            </select>
                        </div>
                    <? } ?>

                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="button" id="submitabsen_<?= $id_murid . "_" . $t; ?>" class="btn btn-default">Submit
                        Absen
                    </button>
                </div>
            </div>
        </form>


        <script>
            var mk_id = '<?= $id_mk; ?>';
            var murid_id = '<?= $id_murid; ?>';
            var kelas_id = '<?= $kelas_id; ?>';

            $('#submitabsen_<?= $id_murid . "_" . $t; ?>').click(function () {

                var kelas_id = $('#kelas_piliha_<?=$t;?>').val();
                var hari = $('#select_<?= $id_murid; ?>_' + kelas_id).val();
                if (mk_id != '' && hari !== null) {

                    absen_murid_dikelas_hari(mk_id, murid_id, kelas_id, hari);
                } else {
                    alert("Please select date");
                }

            });

            function absen_murid_dikelas_hari(mk_id, murid_id, kelas_id, hari) {
                if (confirm("yakin?"))
                    $.post("<?= _SPPATH; ?>KelasWebHelper/absen_murid_dikelas_hari",
                        {
                            mk_id: mk_id,
                            murid_id: murid_id,
                            kelas_id: kelas_id,
                            hari: hari
                        },
                        function (data) {
                            if (data.status_code) {
                                lwrefresh(selected_page);

                            } else {
                                alert(data.status_message);
                            }
                        }, 'json');
            }
        </script>
        <?
    }


    function absen_guru_profile()
    {
        $id_guru = addslashes($_GET['id']);
        $dowMap = array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
        $guru = new SempoaGuruModel();
        $guru->getByID($id_guru);


        $kelas = new KelasWebModel();
        $arrKelas = $kelas->getWhere("guru_id='$id_guru'");
        $kelas_id = $arrKelas[0]->id_kelas;
        $kelas->getByID($kelas_id);


        $id_mk = '';
        $objmk = new MuridKelasMatrix();
        $arrMK = $objmk->getWhere("guru_id='$id_guru' AND kelas_id='$kelas_id'");
        if (count($arrMK) == 0) {
            ?>
            <script>
                alert("Guru tidak mempunyai murid!");
                openLw('Profile_Guru', '<?=_SPPATH;?>GuruWebHelper/guru_profile?guru_id=<?=$id_guru;?>', 'fade');
            </script>
            <?
            die();
        } else {
            $id_mk = $arrMK[0]->mk_id;
        }
        $mk = new MuridKelasMatrix();
        $arrGurudiKelas = $mk->getWhere("guru_id='$id_guru' AND active_status = 1  GROUP BY kelas_id");
        ?>

        <div style="text-align: center; padding-bottom: 30px;">
            <h1><?= $guru->nama_guru; ?>

                <div class="pull-right">
                    <button class="btn btn-default" onclick="back_to_profile_guru('<?= $id_guru; ?>');">back to profile
                    </button>
                </div>
                <br>
                <i>Absensi di kelas</i>
            </h1>
        </div>

        <form class="form-horizontal">
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Pilih Kelas</label>
                <div class="col-sm-10">
                    <select id="kelas_pilihan_<?= $id_guru; ?>" class="form-control">
                        <?
                        foreach ($arrGurudiKelas as $num => $kls) {
                            $sel = "";
                            if ($kls->kelas_id == $kelas_id)
                                $sel = "selected";

                            $kelasku = new KelasWebModel();
                            $kelasku->getByID($kls->kelas_id);

                            $kls->kelas_object = $kelasku;
                            ?>
                            <option <?= $sel; ?> value="<?= $kelasku->id_kelas; ?>">
                                <b><?= $dowMap[$kelasku->hari_kelas]; ?>
                                    <?= date("h:i", strtotime($kelasku->jam_mulai_kelas)); ?>
                                    - <?= date("h:i", strtotime($kelasku->jam_akhir_kelas)); ?>
                                </b></option>
                            <?
                            //ambil default mk
                            if ($id_mk == '' && $num == 0) {
                                $id_mk = $kls->mk_id;
                            }
                            if ($kelas_id == '' && $num == 0) {
                                $kelas_id = $kls->kelas_id;
                            }
                        }
                        ?>
                    </select>
                    <script>
                        $('#kelas_pilihan_<?= $id_guru; ?>').change(function () {
                            var slc = $('#kelas_pilihan_<?= $id_guru; ?>').val();
                            mk_id = <?= $kls->mk_id; ?>;
                            $('.selector-tanggal').hide();
                            $('#select_container_date_<?= $id_guru; ?>_' + slc).show();

                        });
                    </script>
                </div>
            </div>

            <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label">Pilih Tanggal</label>
                <div class="col-sm-10">
                    <?
                    foreach ($arrGurudiKelas as $kls) {

                        $kelasku = new KelasWebModel();
                        $kelasku->getByID($kls->kelas_id);

                        $arrHari = $this->getAllDaysInAMonth(date("Y"), date("n"), Generic::getWeekDay()[$kelasku->hari_kelas]);
//                        pr($arrHari);
                        $mk = new AbsenGuruModel();
                        $arrMatrix = $mk->getWhere("absen_kelas_id = '{$kls->kelas_id}' AND absen_guru_id = '$id_guru' ORDER BY absen_date DESC LIMIT 0,10");

                        $arrSudah = array();
                        foreach ($arrMatrix as $mat) {
                            $arrSudah[] = $mat->absen_date;
                        }
//                        pr($arrSudah);
                        ?>
                        <div <? if ($kls->kelas_id != $kelas_id) { ?>style="display: none;" <? } ?>
                             id="select_container_date_<?= $id_guru . "_" . $kls->kelas_id; ?>"
                             class="selector-tanggal">

                            <select class="form-control " id="select_<?= $id_guru . "_" . $kls->kelas_id; ?>">
                                <?
                                foreach ($arrHari as $hari) {
                                    $sel = "";
                                    if (in_array($hari->format("Y-m-d"), $arrSudah)) {
                                        $sel = "disabled " . " Sudah diabsensi";
                                    }
                                    if (strtotime($hari->format('Y-m-d')) - time() > 0 && $hari->format('Y-m-d') != date("Y-m-d"))
                                        continue;
                                    ?>
                                    <option <?= $sel; ?>>
                                        <?= $hari->format('Y-m-d'); ?> <?= $sel; ?>
                                    </option>
                                <? } ?>
                            </select>
                        </div>
                    <? } ?>

                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="button" id="submitabsen_<?= $id_guru; ?>" class="btn btn-default">Submit Absen
                    </button>
                </div>
            </div>

            <script>
                var mk_id = '<?= $id_mk; ?>';
                var guru_id = '<?= $id_guru; ?>';
                var kelas_id = '<?= $kelas_id; ?>';

                $('#submitabsen_<?= $id_guru; ?>').click(function () {

                    var kelas_id = $('#kelas_pilihan_<?= $id_guru; ?>').val();
                    var hari = $('#select_<?= $id_guru; ?>_' + kelas_id).val();
                    if (mk_id != '' && hari !== null) {

                        absen_guru_dikelas_hari(mk_id, guru_id, kelas_id, hari);
                    } else {
                        alert("Please select date");
                    }

                });

                function absen_guru_dikelas_hari(mk_id, c, kelas_id, hari) {
                    if (confirm("Anda akan mengabsen Guru?"))
                        $.post("<?= _SPPATH; ?>KelasWebHelper/absen_guru_dikelas_hari",
                            {
                                mk_id: mk_id,
                                guru_id: guru_id,
                                kelas_id: kelas_id,
                                hari: hari
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
        </form>

        <?
    }

    public function loadDaftarKelas()
    {
        $arrMyTC = Generic::getAllMyTC(AccessRight::getMyOrgID());
        $t = time();
        $kelas = new KelasWebModel();
        $tc_id = (isset($_GET['tc_id']) ? addslashes($_GET['tc_id']) : key($arrMyTC));
        $arr = $kelas->getWhere("ibo_id = '" . AccessRight::getMyOrgID() . "' AND  tc_id=$tc_id ORDER BY hari_kelas ASC,jam_mulai_kelas ASC");
        $dowMap = array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
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
    }

    public function create_invoice_ibo()
    {

        $id_murid = addslashes($_GET['id_murid']);
        $bln = isset($_GET['bln']) ? addslashes($_GET['bln']) : date("n");
        $thn = isset($_GET['thn']) ? addslashes($_GET['thn']) : date("Y");
        $t = time();
        $arrBulan = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12);

        ?>
        <div class="pull-middle" style="font-size: 13px;">
            Bulan :<select id="bulan_<?= $t; ?>">
                <?
                foreach ($arrBulan as $bln2) {
                    $sel = "";
                    if ($bln2 == date("n")) {
                        $sel = "selected";
                    }
                    ?>
                    <option value="<?= $bln2; ?>" <?= $sel; ?>><?= $bln2; ?></option>
                    <?
                }
                ?>
            </select>
            Tahun :<select id="tahun_<?= $t; ?>">
                <?
                for ($x = date("Y") - 2; $x < date("Y") + 2; $x++) {
                    $sel = "";
                    if ($x == date("Y")) {
                        $sel = "selected";
                    }
                    ?>
                    <option value="<?= $x; ?>" <?= $sel; ?>><?= $x; ?></option>

                    <?
                }
                ?>
                }
                ?>
            </select>
            <script>
                id_murid = '<?=$id_murid;?>';
                bln = $('#bulan_<?= $t; ?>').val();
                thn = $('#tahun_<?= $t; ?>').val();
                $('#bulan_<?= $t; ?>').change(function(){
                    bln = $('#bulan_<?= $t; ?>').val();
                    thn = $('#tahun_<?= $t; ?>').val();
                });
                $('#tahun_<?= $t; ?>').change(function(){
                    bln = $('#bulan_<?= $t; ?>').val();
                    thn = $('#tahun_<?= $t; ?>').val();
                });
            </script>
        </div>

        <?
    }

    function nonaktivkankelas()
    {
        $kelas_id = addslashes($_POST['kelas_id']);

        $kelas = new KelasWebModel();
        $kelas->getByID($kelas_id);

       if (is_null($kelas->id_kelas)) {
            $json['status_code'] = 0;
            $json['status_message'] = "Kelas tidak ditemukan!";
            echo json_encode($json);
            die();
        }

//        $kelas_id = addslashes($_POST['id_kelas']);

        $kelas->aktiv = 0;
        if ($kelas->save(1)) {

           $json['status_code'] = 1;
            $json['status_message'] = "Kelas sudah dinonaktivkan!";
            echo json_encode($json);
            die();
        }
        $json['status_code'] = 0;
        $json['status_message'] = "Save Failed";
        echo json_encode($json);
        die();
    }



    public function loadManageKelas(){

        $kelas = new KelasWebModel();
        $hariini = isset($_GET['hari']) ? addslashes($_GET['hari']) : date('w');
        $tc_id = isset($_GET['tc_id']) ? addslashes($_GET['tc_id']) : AccessRight::getMyOrgID();
        $arr = $kelas->getWhere("tc_id = $tc_id AND aktiv = 1 AND hari_kelas=$hariini ORDER BY jam_mulai_kelas ASC");
        $dowMap = array('Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu');
        $t = time();

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
    }
}
