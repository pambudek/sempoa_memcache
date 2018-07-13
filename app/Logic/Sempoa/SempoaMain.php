<?php
/**
 * Created by PhpStorm.
 * User: elroy
 * Date: 7/22/16
 * Time: 3:03 PM
 */

class SempoaMain extends WebApps{

    function index(){
//        header("Location:"._SPPATH."loginpage");
        header("Location:"._SPPATH."maintenancePage");
        die();
    }


    /*
	 * login webview
	 */
    function login ()
    {
        /*
         * login logic
         */
        $acc = new AccountLogin();
        $acc->login_hook = array (
            "PortalHierarchy" => "getSelectedHierarchy",
            "NewsChannel"     => "loadSubscription",
            "Role"            => "loadRoleToSession",
            "AccessRight"     => "loadAccessRight", //load TC id, IBO id, KPO id..sehingga ketahuan bawahnya...
            "SempoaWebSetting" => "loadToSession"
        );

        $acc->process_login();


    }

    function ses(){
        pr(Account::getMyRole());
        pr($_SESSION);

        die();
    }

    /*
     * Web View For logout
     */
    function logout ()
    {
        $acc = new AccountLogin();
        $acc->process_logout();
    }




    function loginpage ()
    {
        header("Location:"._SPPATH."maintenancePage");
        die();
//        if(Auth::isLogged()){
//            //loginin pakai cookie
//
//            header("Location:"._SPPATH."myapps");
//            exit();
//        }

//        pr($_COOKIE);
//        pr($_SESSION);



        ?>
        <style>
            @media (max-width: 768px) {

                .monly {
                    display: initial;
                }

                .donly {
                    display: none;
                }

                .loginbox{
                    margin: 0 auto;
                    margin-top: 50px;
                    width: 300px;

                }


            }

            @media (min-width: 768px) {
                .monly {
                    display: none;
                }

                .donly {
                    display: initial;
                }

                .loginbox{
                    margin: 0 auto;
                    margin-top: 80px;
                    width: 300px;
                }


            }
        </style>
        <div class="container attop" >

            <div class=" loginbox " style="text-align: center; ">
                <div style="padding: 20px; padding-bottom: 30px;">
                    <img class="animated zoomIn" src="<?=_SPPATH;?>images/logo_sempoa.png" style="max-width: 100%;">
                </div>

                <div class="berpadding">
                    <?
                    $acc = new AccountLogin();
                    $acc->loginForm();
                    ?>
                    <style>
                        .checkbox input[type=checkbox], .checkbox-inline input[type=checkbox], .radio input[type=radio], .radio-inline input[type=radio] {
                            margin-left: 0px;
                            display: none;
                        }
                        .checkboxspan{
                            margin-left: 20px;
                            display: none;
                        }
                        .btn-primary {
                            color: #fff;
                            background-color: #008247;
                            border-color: #008247;
                        }
                        .btn-primary:hover,.btn-primary:focus{
                            background-color: #00a157;
                            border-color: #00a157;
                        }
                        a{
                            color: #005c32;
                            text-decoration: underline;
                        }
                        a:hover{
                            color: #008d4c;
                        }
                        .pure-toggle-label{
                            display: none;
                        }

                        body,.pure-pusher,#maincontent{
                            font-weight: normal;
                            background-color: #f3a637;
                            color: #73879C;
                        }
                    </style>
                    <?/*
            <div style="margin-top: 10px; text-align: right;color: #005c32;">
<!--                <a class="btn btn-default"  href="--><?//=_SPPATH;?><!--forgotpassword">forgot password</a>-->
                <a  href="<?=_SPPATH;?>register">register</a> <i class="glyphicon glyphicon-option-vertical"></i> <a  href="<?=_SPPATH;?>enquiry">learn more</a> <i class="glyphicon glyphicon-option-vertical"></i> <a  href="<?=_SPPATH;?>forgotpassword">forgot password</a>
            </div>
<!--            <h1 class="hype" style="margin-bottom: 30px;">OR</h1>-->
<!--            <a class="btn btn-lg btn-success btn-block" href="--><?//=_SPPATH;?><!--register">Register</a>-->
<!--            -->
*/?>
                </div>
            </div>

            <div class="clearfix" ></div>
            <!--        <div style="text-align: center; padding-top: 30px;">-->
            <!--            Copyright &copy; PT. Indo Mega Byte-->
            <!--        </div>-->

        </div>
    <?
    }


    function maintenancePage ()
    {

        ?>


        <?
    }
    var $access_home = "normal_user";
    function home(){

//        $myOrgType = AccessRight::getMyOrgType();
////        echo $myOrgType;
//        if($myOrgType == KEY::$TC){
////            echo "halo";
//            self::operationalDaily();
//        }

        SempoaSkeleton::redirectOpenLWSempoa();
        ?><?
//        pr($_SESSION);

    }

    public static function operationalDaily(){

        /*if($_GET['st']=="" && AccessRight::getMyOrgType() ==  KEY::$TC){
        ?>

        <script>
                        $(document).ready(function(){
                            $("body").addClass("sidebar-collapse");
                        });

        </script>
            <? } */?>
        <style>
            .icon-item i{
                font-size: 100px;
                margin-bottom: 20px;


            }
            .icon-item{
                text-align: center;
                color: #777777;
                margin-bottom: 30px;
                margin-top: 20px;
                cursor: pointer;
                font-size: 20px;
            }
            .icon-item:hover{
                color: #222222;

            }
        </style>
        <div class="icons" style="background-color: white; padding: 20px; margin: 20px;">
            <div class="col-md-3 icon-item" onclick='activkanMenuKiri("absen_hari_ini_tc");openLw("absen_hari_ini_tc","<?=_SPPATH;?>KelasWeb/absen_hari_ini_tc?back=1","fade");'>
                <i class="fa  fa-clipboard"></i>
                <br>Absensi Murid
            </div>
            <div class="col-md-3 icon-item" onclick='activkanMenuKiri("create_murid_tc");openLw("create_murid_tc","<?=_SPPATH;?>MuridWeb/create_murid_tc?back=1","fade");'>
                <i class="fa  fa-user-plus"></i>
                <br>Pendaftaran Murid Baru
            </div>

            <div class="col-md-3 icon-item" onclick='activkanMenuKiri("create_operasional_kelas");openLw("create_operasional_kelas","<?=_SPPATH;?>KelasWeb/create_operasional_kelas?back=1","fade");'>
                <i class="fa fa-columns"></i>
                <br>Atur Kelas
            </div>


            <div class="col-md-3 icon-item" onclick='activkanMenuKiri("read_murid_tc");openLw("read_murid_tc","<?=_SPPATH;?>MuridWeb/read_murid_tc?back=1","fade");'>
                <i class="fa fa-user-circle-o"></i>
                <br>Lihat Murid
            </div>

            <div class="col-md-3 icon-item" onclick='activkanMenuKiri("read_guru_tc");openLw("read_guru_tc","<?=_SPPATH;?>GuruWeb2/read_guru_tc?back=1","fade");'>
                <i class="fa fa-address-book-o"></i>
                <br>Lihat Guru
            </div>

            <div class="col-md-3 icon-item" >
                <i class="fa fa-dollar" onclick='pilih_pembayaran();'></i>
                <br><span onclick='pilih_pembayaran();'>Pembayaran</span>
                <div id="pilihpembayaran" style="position: absolute; margin-top: -50px; padding: 10px; display: none; z-index:1000;  background-color: #BBBBBB;">
                    <button onclick="$('#pilihpembayaran').hide();openLw('create_operasional_pembayaran_iuran_bulanan_tc', '<?=_SPPATH;?>LaporanWeb/create_operasional_pembayaran_iuran_bulanan_tc'+'?now='+$.now(), 'fade');activkanMenuKiri('create_operasional_pembayaran_iuran_bulanan_tc');" class="btn btn-default" style="margin-bottom: 10px;">Iuran Bulanan</button>
                    <button onclick="$('#pilihpembayaran').hide();openLw('create_operasional_pembayaran_iuran_buku_tc', '<?=_SPPATH;?>LaporanWeb/create_operasional_pembayaran_iuran_buku_tc'+'?now='+$.now(), 'fade');activkanMenuKiri('create_operasional_pembayaran_iuran_buku_tc');" class="btn btn-default">Iuran Buku</button>
                </div>
            </div>

            <div class="col-md-3 icon-item" >
                <i class="fa fa-search" onclick='isi_search();'></i>
                <br><span onclick='isi_search();'>Cari</span>
                <div id="isi_search" style="position: absolute; margin-top: -50px; padding: 10px; display: none; z-index:1000;  background-color: #BBBBBB;">
                    <input type="text" value="<?=$_GET['q'];?>" class="form-control" id="isi_search_text" style="margin-bottom: 5px;">
                    <button id="isi_search_button"  class="btn btn-default">Submit</button>
                </div>
            </div>
            <script>
                $('#isi_search_button').click(function(event){
                    event.preventDefault();
                    $('#isi_search').hide();
                    $('#searchtextmenu').val($('#isi_search_text').val());
                    var slc = encodeURIComponent($('#isi_search_text').val());
//        alert("halo"+slc);
                    openLw('searchMenu','<?=_SPPATH;?>SearchMenuRoleAR/search?q='+slc,'fade');

                });
                $('#isi_search_text').keypress(function (e) {
                    if (e.which == 13) {
//                        event.preventDefault();
                        $('#isi_search').hide();
                        var slc = encodeURIComponent($('#isi_search_text').val());
//        alert("halo"+slc);
                        $('#searchtextmenu').val($('#isi_search_text').val());
                        openLw('searchMenu','<?=_SPPATH;?>SearchMenuRoleAR/search?q='+slc,'fade');
                        return false;    //<---- Add this line
                    }
                });
            </script>

            <div class="clearfix"></div>
        </div>
        <script>
            function pilih_pembayaran(){
                $('#pilihpembayaran').toggle();
            }
            function isi_search(){
                $('#isi_search').toggle();
            }
        </script>
    <?


    }


    function test(){

        $name = $_GET['name'];
        $id = $_GET['id'];


        $org = new SempoaOrg();
        $org->getByID($id);
        ?>


<h1 class="header" onmouseover="$(this).html('mouse over');" onmouseout="$(this).html('<?=$h1;?>');" id="h1"><?=$h1;?></h1>
<h2>Nama ANda : <?=$name;?></h2>
<h3>Org anda = <?=$org->nama;?>
</h3>
<h3><a href="<?=_SPPATH;?>inbox?id=<?=$id;?>">My INBOX</a></h3>
<a href="http://www.nba.com">ini link</a>

<!-- Button trigger modal -->
<button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal">
    Launch demo modal
</button>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Modal title</h4>
            </div>
            <div class="modal-body">
                <?

                $jenis = new SettingJenisBiayaModel();
                $arr = $jenis->getAll();

                foreach($arr as $r){
                    echo $r->jenis_biaya."<br>";
                }
                ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>

<style type="text/css">
    .btn-primary{
        background-color: green !important;
    }
    .modal-dialog{
        width: 100% !important;
    }

</style><?
    }

    function inbox(){

        $id = $_GET['id'];


        $org = new SempoaOrg();
        $org->getByID($id);
        ?>



        <h3>Org anda = <?=$org->nama;?>
        </h3>
        <h3><a href="<?=_SPPATH;?>test?id=<?=$id;?>">My PROFILE</a></h3>

        <?
        $inbox = new SempoaInboxModel();
        $kris = $inbox->getWhere("inbox_org_id = '$id'");
        foreach($kris as $roww){

            echo "<a href=''>".$roww->inbox_title." dari ".$roww->inbox_sender_id."</a>";
            ?>
            <br>
            <?
        }
        ?>

        <?
    }
} 