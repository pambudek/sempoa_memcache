<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= $title; ?></title>
    <?= $metaKey; ?>
    <?= $metaDescription; ?>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.5 -->
    <link rel="stylesheet" href="<?= _SPPATH . _THEMEPATH; ?>/bootstrap/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- jvectormap -->
    <link rel="stylesheet" href="<?= _SPPATH . _THEMEPATH; ?>/plugins/jvectormap/jquery-jvectormap-1.2.2.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?= _SPPATH . _THEMEPATH; ?>/dist/css/AdminLTE.min.css">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="<?= _SPPATH . _THEMEPATH; ?>/dist/css/skins/_all-skins.min.css">

    <link rel="stylesheet" href="<?= _SPPATH; ?><?= _THEMEPATH; ?>/css/jqueryui.css">

    <!-- Morris charts -->
    <link rel="stylesheet" href="<?= _SPPATH; ?><?= _THEMEPATH; ?>/plugins/morris/morris.css">

    <!--set all paths as javascript-->
    <? $this->printPaths2JS(); ?>


    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>

    <![endif]-->

    <!-- jQuery 2.1.4 -->
    <script src="<?= _SPPATH . _THEMEPATH; ?>/plugins/jQuery/jQuery-2.1.4.min.js"></script>
    <script src="<?= _SPPATH; ?>js/ckeditor_plus3/ckeditor/ckeditor.js"></script>
    <script>
        /*
         * untuk menu kiri
         */
        var registorMenuKiri = [];
        function registerkanMenuKiri(id) {
            registorMenuKiri.push(id);
        }
    </script>
    <? $this->getHeadfiles(); ?>

    <style>
        .h1, h1 {
            font-size: 26px;
            margin-bottom: 20px;

        }

        .CrudViewPagebutton {
            cursor: pointer;
            padding: 10px;
            margin: 1px;
            border-radius: 5px;
            background-color: #dedede;
        }
        /*body{*/
            /*background-image: url('images/maintenance-page.png');*/
            /*background-repeat: no-repeat;*/
            /*background-position: center;*/
            /*background-size: cover;*/
        /*}*/

    </style>
</head>
<body class="hold-transition skin-yellow-light sidebar-mini ">
<script type="text/javascript" src="<?= _SPPATH; ?>js/tokenfield/bootstrap-tokenfield.js"></script>
<link rel="stylesheet" href="<?= _SPPATH; ?>js/tokenfield/css/bootstrap-tokenfield.css">

<div id="loadingtop" style="display:none; opacity: 0.3; position: fixed; width:100%; top:40%;z-index:200000000;">
    <div id="loadingtop2"
         style="text-align:center; padding: 10px; width:100px; border-radius:10px; color: white; background-color: #5c6e7d; font-weight: bold; margin:0 auto;">
        <img style="margin-bottom:10px;" src="<?= _SPPATH; ?>images/leaploader.gif"/>

        <div><?= Lang::t('lang_loading'); ?></div>
    </div>
</div>


<div id="oktop" style="display:none;opacity: 0.3; position: fixed; width:100%; top:40%;z-index:20000000;">
    <div id="oktop2"
         style="text-align:center; padding: 10px; width:100px; border-radius:10px; color: white; background-color: #7db660; font-weight: bold; margin:0 auto;">
        <img style="margin-bottom:10px;" src="<?= _SPPATH; ?>images/ok.gif"/>

        <div style='font-size:48px;'><?= Lang::t('OK'); ?></div>
    </div>
</div>

<div class="wrapper">

    <header class="main-header">

        <!-- Logo -->
        <a href="<?= _SPPATH; ?>home" class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini"><?= substr(Efiwebsetting::getData("backend_title_short"), 0, 3); ?></span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg"><?= substr(Efiwebsetting::getData("backend_title"), 0, 18); ?></span>
        </a>

        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top" role="navigation">
            <!-- Sidebar toggle button-->
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>

            <? SempoaSkeleton::header_notif(); ?>

        </nav>
    </header>
    <!-- Left side column. contains the logo and sidebar -->
    <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <? SempoaSkeleton::leftmenu(); ?>
        <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">

        <!-- baru dari roy 17 dec 2015 -->
        <div id="success_permanent" class="lw_alert" style="display: none;">
            <div id="success_permanent_alert" class="alert alert-success">
                <a href="#" class="close" data-hide="alert" aria-label="close">&times;</a>
                <span id="success_permanent_span"></span>
            </div>
        </div>

        <div id="info_permanent" class="lw_alert" style="display: none;">
            <div id="info_permanent_alert" class="alert alert-info">
                <a href="#" class="close" data-hide="alert" aria-label="close">&times;</a>
                <span id="info_permanent_span"></span>
            </div>
        </div>

        <div id="warning_permanent" class="lw_alert" style="display: none;">
            <div id="warning_permanent_alert" class="alert alert-warning">
                <a href="#" class="close" data-hide="alert" aria-label="close">&times;</a>
                <span id="warning_permanent_span"></span>
            </div>
        </div>

        <div id="danger_permanent" class="lw_alert" style="display: none;">
            <div id="danger_permanent_alert" class="alert alert-danger">
                <a href="#" class="close" data-hide="alert" aria-label="close">&times;</a>
                <span id="danger_permanent_span"></span>
            </div>
        </div>
        <!--end baru dari roy 17 dec 2015 -->

        <div id="lw_content" style="padding-top: 20px;"></div>
        <div id="content_utama">
            <?= $content; ?>


        </div>

        <div class="clearfix" style="padding-bottom: 30px;"></div>
    </div>
    <!-- /.content-wrapper -->

    <footer class="main-footer">
        <div class="pull-right hidden-xs">
            <b>Version</b> 0.0.1
        </div>
        <strong>Copyright &copy; 2014-<?= date("Y"); ?> <a href="http://www.indomegabyte.com">PT. Indo Mega
                Byte</a>.</strong> All rights
        reserved.
    </footer>

    <!-- Control Sidebar -->
    <? SempoaSkeleton::control_sidebar(); ?>

    <div class="modal fade" id="modal_bundle_kupon" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="modal_bundle_kupon_title">Masukan Nomor Seri Bundle Anda</h4>
                </div>
                <div class="modal-body">
                    <div style="float: right;display: none;">
                        <button id="add_bundle" class="btn btn-primary btn-xs"><i class="glyphicon glyphicon-plus"></i>
                        </button>
                    </div>
                    <div class="clearfix"></div>
                    <div id="bundle_form_container">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="save_req_2();">Save changes</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        var jml_bundle = 0;
        var bundle_obj = [];
        var req_id = 0;

        var jml_bundle_yang_dicari = 0;

        $('#add_bundle').click(function () {
            jml_bundle++;
            var html = '<div class="bundle_form" id="bundleform_' + jml_bundle + '">' +
                jml_bundle + '. Kode Start <input type="number" min="0" id="bundle_' + jml_bundle + '_start">' +
                'Kode End <input type="number" min="0" id="bundle_' + jml_bundle + '_end">' +
                '<i class="glyphicon glyphicon-remove" onclick="remove_bundle(' + jml_bundle + ');"></i></div>';

            $('#bundle_form_container').append(html);

            bundle_obj.push(jml_bundle);
        });

        function remove_bundle(id_bundle) {
            $('#bundleform_' + id_bundle).remove();

            var index = bundle_obj.indexOf(id_bundle);
            if (index > -1) {
                bundle_obj.splice(index, 1);
            }
            reload_bundle();

        }
        function save_req() {
            if (req_id == 0) {

            } else {

                var jml_bundlean = 0;
                var jml_kupon = 0;
                var err = [];

                var post = {};
                post.req_id = req_id;
                //pindahkan value dari bundle start dan end masing2 row ke post object
                for (key in bundle_obj) {
                    var jml_bundle = bundle_obj[key];

                    var end = $('#bundle_' + jml_bundle + '_end').val();
                    var start = $('#bundle_' + jml_bundle + '_start').val();


                    post["bundle_" + jml_bundle + "_end"] = end;
                    post["bundle_" + jml_bundle + "_start"] = start;

                    jml_bundlean++;

                    if (parseInt(end) - parseInt(start) != 24) {

                        //error
                        err.push("Bundle No " + jml_bundlean + " Jumlah harus 25");
                    }


                    //validasi input yang lain : Efindi

                }

                if (bundle_obj.length < 1) {
                    err.push("Min isi 1 bundle");
                }

                if (err.length > 0) {
                    alert(err.join("\n\r"));
                } else {

                    post.arr_bundle = bundle_obj.join(",");
                    jml_kupon = jml_bundlean * 25;

                    if (confirm("Anda akan mengirimkan " + jml_bundlean + " Bundle (" + jml_kupon + " pcs)"))
                        $.post("<?= _SPPATH; ?>KuponWebHelper/accept_req", post,
                            function (data) {
                                console.log(data);
                                if (data.status_code) {
                                    alert(data.status_message);
                                    lwrefresh(selected_page);
                                } else {
                                    alert(data.status_message);
                                }
                            }, 'json');
                }

            }
        }

        function save_req_2() {
            if (req_id == 0) {

            } else {

                var jml_bundlean = 0;
                var jml_kupon = 0;
                var err = [];

                var arr_bundle = [];

                var post = {};
                post.req_id = req_id;
                //pindahkan value dari bundle start dan end masing2 row ke post object
                for (var x = 1; x <= jml_bundle_yang_dicari; x++) {
                    var jml_bundle = x;

                    arr_bundle.push(x);

                    var end = $('#bundle_' + jml_bundle + '_end').val();
                    var start = $('#bundle_' + jml_bundle + '_start').val();


                    post["bundle_" + jml_bundle + "_end"] = end;
                    post["bundle_" + jml_bundle + "_start"] = start;

                    jml_bundlean++;

                    if (parseInt(end) - parseInt(start) != 24) {

                        //error
                        err.push("Bundle No " + jml_bundlean + " Jumlah harus 25");
                    }


                    //validasi input yang lain : Efindi

                }


                if (err.length > 0) {
                    alert(err.join("\n\r"));
                } else {

                    post.arr_bundle = arr_bundle.join(",");
                    jml_kupon = jml_bundlean * 25;

                    if (confirm("Anda akan mengirimkan " + jml_bundlean + " Bundle (" + jml_kupon + " pcs)"))
                        $.post("<?= _SPPATH; ?>KuponWebHelper/accept_req", post,
                            function (data) {
                                console.log(data);
                                if (data.status_code) {
                                    alert(data.status_message);
                                    $('#modal_bundle_kupon').modal('hide');
                                    lwrefresh(selected_page);

                                } else {
                                    alert(data.status_message);
                                }
                            }, 'json');
                }

            }
        }
        function create_bundle() {


            $('#bundle_form_container').empty();


            for (var x = 1; x <= jml_bundle_yang_dicari; x++) {

                var jml_bundle = x;

                var html = '<div class="bundle_form" id="bundleform_' + jml_bundle + '">' +
                    x + '. Kode Start <input type="number" min="0" id="bundle_' + jml_bundle + '_start">' +
                    'Kode End <input type="number" min="0" id="bundle_' + jml_bundle + '_end">' +
                    '</div>';

                $('#bundle_form_container').append(html);

            }

        }

        function reload_bundle() {


            var bundle_arr_obj = {};
            for (key in bundle_obj) {
                var jml_bundle = bundle_obj[key];

                var end = $('#bundle_' + jml_bundle + '_end').val();
                var start = $('#bundle_' + jml_bundle + '_start').val();

                bundle_arr_obj[jml_bundle] = {
                    start: start,
                    end: end
                };
            }

            console.log(bundle_arr_obj);
            $('#bundle_form_container').empty();

            var num = 0;
            for (key in bundle_obj) {
                num++;
                var jml_bundle = bundle_obj[key];

                var html = '<div class="bundle_form" id="bundleform_' + jml_bundle + '">' +
                    num + '. Kode Start <input type="number" min="0" id="bundle_' + jml_bundle + '_start">' +
                    'Kode End <input type="number" min="0" id="bundle_' + jml_bundle + '_end">' +
                    '<i class="glyphicon glyphicon-remove" onclick="remove_bundle(' + jml_bundle + ');"></i></div>';

                $('#bundle_form_container').append(html);

                $('#bundle_' + jml_bundle + '_end').val(bundle_arr_obj[jml_bundle].end);
                $('#bundle_' + jml_bundle + '_start').val(bundle_arr_obj[jml_bundle].start);

            }
            jml_bundle = num;

            console.log(bundle_obj);
        }
    </script>


    <div class="modal fade" id="modal_bundle_kupon_selector" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="modal_bundle_kupon_title_selector">Pilih Nomor Seri Bundle Anda</h4>
                </div>
                <div class="modal-body">
                    <div style="float: right;display: none;">
                        <button id="add_bundle" class="btn btn-primary btn-xs"><i class="glyphicon glyphicon-plus"></i>
                        </button>
                    </div>
                    <div class="clearfix"></div>
                    <form id="bundle_selector_form">
                        <div id="bundle_form_container_selector">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" onclick="save_req_selector();" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        function save_req_selector() {
            if (req_id == 0) {

            } else {

                var val = [];
                var jml_bundlean = 0;
                $('.ads_Checkbox:checked').each(function () {
                    //                arr[i++] = $(this).val();
                    jml_bundlean++;
                });
                var jml_kupon = jml_bundlean * 25;

                if (parseInt(jml_bundle_yang_dicari) != jml_bundlean) {
                    alert("Jumlah Bundle Tidak sesuai permintaan");
                } else {
                    var post = $("#bundle_selector_form").serialize();
                    if (confirm("Anda akan mengirimkan " + jml_bundlean + " Bundle (" + jml_kupon + " pcs)"))
                        $.post("<?= _SPPATH; ?>KuponWebHelper/accept_req_selector?req_id=" + req_id, post,
                            function (data) {
                                console.log(data);
                                if (data.status_code) {
                                    alert(data.status_message);
                                    $('#modal_bundle_kupon_selector').modal('hide');
                                    lwrefresh(selected_page);

                                } else {
                                    alert(data.status_message);
                                }
                            }, 'json');
                }


            }
        }
    </script>
    <?
    //            $myOrgID = AccessRight::getMyOrgID();
    //            $kpo_id = Generic::getMyParentID($myOrgID);
    //            //sempoa__barang_harga
    //            $objBarang = new BarangWebModel();
    //            $po = $_SESSION['po_id'];
    //            $poItem = new POItemModel();
    //            global $db;
    //            $q = "SELECT b.*, po.* FROM {$objBarang->table_name} b INNER JOIN {$poItem->table_name} po ON  b.id_barang_harga = po.id_barang WHERE po_id='$po' ";
    //
    //            $arrStockBarangMyParent = $db->query($q, 2);
    //            $t = time();
    //
    //            $cart = $_SESSION['cart'];
    ?>
    <script>
        var cart_jumlah_total = 0;
        var cart_hrg_total = 0;
        var cart_item_by_id = {};
    </script>
    <div class="modal fade" id="my-cart-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">

                    <h4 class="modal-title" id="myModalLabel">
                        <span class="glyphicon glyphicon-shopping-cart"></span> My Cart
                    </h4>
                </div>
                <div class="modal-body" id="cart_body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary my-cart-checkout" id='checkout_<?= $t; ?>'>Checkout
                    </button>
                    <script>
                        $('#checkout_<?= $t; ?>').click(function () {
                                var post = "";
                                if (confirm("Anda yakin?")) {

                                    // do something…
                                    var keys = [];
                                    var qtys = [];
                                    //save everything to session
                                    for (key in cart_item_by_id) {
                                        var obj_item = cart_item_by_id[key];
                                        keys.push(key);
                                        qtys.push(obj_item.qty);
                                    }
                                    $.post("<?= _SPPATH; ?>BarangWebHelper/update_cart_qty", {
                                        keys: keys.join(","),
                                        qtys: qtys.join(",")
                                    }, function (data) {
                                        if (data.status_code) {
                                            $.post("<?= _SPPATH; ?>BarangWebHelper/checkout", post,
                                                function (data) {
                                                    console.log(data);

                                                    if (data.status_code) {
                                                        cart_item_by_id = {};
                                                        cart_jumlah_total = 0;
                                                        cart_hrg_total = 0;

                                                        $('#my-cart-modal').modal('hide');
//                                                            $('#cart_body').html("");
                                                        //("<?= _SPPATH; ?>BarangWebHelper/cart_modal");
                                                        lwrefresh(selected_page);

//                                                            hitungUlangTotal();
                                                    } else {
                                                        alert(data.status_message);

                                                    }
                                                }, 'json');
                                        } else {
                                            alert(data.status_message);
                                        }
                                    }, 'json');


                                }


                            }
                        );
                        $('#my-cart-modal').on('hidden.bs.modal', function () {
                            // do something…
                            var keys = [];
                            var qtys = [];

                            //save everything to session
                            for (key in cart_item_by_id) {
                                var obj_item = cart_item_by_id[key];
                                keys.push(key);
                                qtys.push(obj_item.qty);
                            }
                            if (keys.length > 0)
                                $.post("<?= _SPPATH; ?>BarangWebHelper/update_cart_qty", {
                                    keys: keys.join(","),
                                    qtys: qtys.join(",")
                                }, function (data) {
                                    if (data.status_code) {

                                    } else {
                                        alert(data.status_message);
                                    }
                                }, 'json');
                        });
                    </script>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_add_murid" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="modal_bundle_kupon_title_selector">Pilih Murid</h4>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" onclick="save_murid_to_kelas();" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        var id_kelas = 0;
        var id_murid = 0;

        function save_murid_to_kelas() {
            $.post("<?= _SPPATH; ?>KelasWebHelper/add_murid_to_kelas", {id_kelas: id_kelas, id_murid: id_murid},
                function (data) {
                    if (data.status_code) {
                        lwrefresh(selected_page);
                        $('#modal_add_murid').modal('hide');
                    } else {
                        alert(data.status_message);
                    }
                }, 'json');
        }
    </script>

    <!-- Modal utk add guru di training-->
    <div class="modal fade" id="modal_add_guru_training" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="modal_bundle_kupon_title_selector">Pilih Guru</h4>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" onclick="save_guru_training();" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        var id_guru = 0;
        var training_id = 0;
        var org_id = '<?=  AccessRight::getMyOrgID();?>';
        function save_guru_training() {
            $.post("<?= _SPPATH; ?>TrainingWebHelper/add_guru_to_training", {
                    id_guru: id_guru,
                    training_id: training_id,
                    org_id: org_id
                },
                function (data) {
                    if (id_guru == 0) {
                        $('#modal_add_guru_training').modal('hide');
                    }
                    if (data.status_code) {
                        lwrefresh(selected_page);
                        $('#modal_add_guru_training').modal('hide');
                    } else {
                        alert(data.status_message);
                    }
                }, 'json');
        }
    </script>


    <div class="modal fade" id="modal_add_murid_ujian" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="modal_bundle_kupon_title_selector">Pilih Murid</h4>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" onclick="save_murid_to_ujian();" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        var ujian_id = 0;
        var id_murid = 0;

        function save_murid_to_ujian() {
            $.post("<?= _SPPATH; ?>UjianWebHelper/add_murid_to_ujian", {ujian_id: ujian_id, id_murid: id_murid},
                function (data) {
                    if (data.status_code) {
                        lwrefresh(selected_page);
                        $('#modal_add_murid_ujian').modal('hide');
                    } else {
                        alert(data.status_message);
                    }
                }, 'json');
        }
    </script>


    <!-- Modal utk add Trainer di training-->
    <div class="modal fade" id="modal_add_trainer_training" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="modal_bundle_kupon_title_selector">Pilih Trainer</h4>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" onclick="save_trainer_training();" class="btn btn-primary">Save changes
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script>
        var id_trainer = 0;
        var training_id = 0;
        var org_id = '<?=  AccessRight::getMyOrgID();?>';
        function save_trainer_training() {
            $.post("<?= _SPPATH; ?>TrainingWebHelper/add_trainer_to_training", {
                    id_trainer: id_trainer,
                    training_id: training_id,
                    org_id: org_id
                },
                function (data) {
                    if (id_trainer == 0) {
                        $('#modal_add_trainer_training').modal('hide');
                    }
                    if (data.status_code) {
                        lwrefresh(selected_page);
                        $('#modal_add_trainer_training').modal('hide');
                    } else {
                        alert(data.status_message);
                    }
                }, 'json');
        }
    </script>


    <div id='modal_ibo' class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Buat Invoice Bulanan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="createinvoicejs();">Create</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
                <script>
                    var id_murid = 0;
                    var bln = 0;
                    var thn = 0;
                    function createinvoicejs() {
                        if (confirm("Anda yakin akan mencetak Invoice?")) {
                            $.post("<?= _SPPATH; ?>MuridWebHelper/create_invoice_ibo", {
                                    bln: bln,
                                    thn: thn,
                                    id_murid: id_murid
                                },
                                function (data) {
                                    if (data.status_code) {
                                        lwrefresh(selected_page);
                                        $('#modal_ibo').modal('hide');
                                    } else {
                                        alert(data.status_message);
                                    }
                                }, 'json');

                        }
                    }
                </script>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modal_proses" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">

                <div class="modal-body">
                    Loadinggg
                </div>

            </div>
        </div>
    </div>


    <!-- Modal utk add guru di training-->
    <? $t = time();?>
    <div class="modal fade" id="modal_cuti_aktiv" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal_title_murid_aktiv_lagi">Pilih Kapan Mulai Aktiv lagi</h4>
                </div>
                <div class="modal-body">
                    <label for="startDate">Date :</label>
                    <input name="startDate" id="startDate_<?=$t;?>" class="date-picker_<?=$t;?>"/>
                    <input type="hidden" name="id_murid_<?=$t;?>" id="id_murid" value="" />
                </div>
                <script type="text/javascript">
                    $(function () {
                        $('.date-picker_<?=$t;?>').datepicker({
                            changeMonth: true,
                            changeYear: true,
                            minDate: '+1m',
                            maxDate: '+3m',
                            showButtonPanel: true,
                            dateFormat: 'yy-mm',
                            onClose: function (dateText, inst) {
                                var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                                var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                                $(this).datepicker('setDate', new Date(year, month, 1));
                            },
                            beforeShow: function (input, inst) {
                                var datestr;
                                if ((datestr = $(this).val()).length > 0) {
                                    year = datestr.substring(datestr.length - 4, datestr.length);
                                    month = jQuery.inArray(datestr.substring(0, datestr.length - 5), $(this).datepicker('option', 'monthNamesShort'));
                                    $(this).datepicker('option', 'defaultDate', new Date(year, month, 1));
                                    $(this).datepicker('setDate', new Date(year, month, 1));
                                }
                            }
                        });
                    });
                </script>
<!--                <style>-->
<!--                    .ui-datepicker-calendar {-->
<!--                        display: none;-->
<!--                    }-->
<!--                </style>-->
                <div class="modal-footer">
                    <button type="button" onclick="setDateAktiv();" class="btn btn-primary">Save</button>
                </div>
                <script>
                    function setDateAktiv(){
                        var monthAktiv = $('#startDate_<?=$t;?>').val();
                        var id_murid = $('#id_murid').val();
//                        alert(monthAktiv);
//                        alert(id_murid);
                        $.post("<?= _SPPATH; ?>MuridWebHelperExt/setStatusCutiAktiv", {id_murid: id_murid, monthAktiv: monthAktiv},
                            function (data) {
                                if (data.status_code) {
                                    lwrefresh(selected_page);
                                    $('#modal_cuti_aktiv').modal('hide');
                                } else {
                                    alert(data.status_message);
                                }
                            }, 'json');


                    }
                </script>
            </div>
        </div>
    </div>


    <!-- ./wrapper -->
    <!-- jQuery UI 1.10.3 -->
    <script src="<?= _SPPATH; ?>js/jqueryui.js"></script>

    <!-- Bootstrap 3.3.5 -->
    <script src="<?= _SPPATH . _THEMEPATH; ?>/bootstrap/js/bootstrap.min.js"></script>

    <!-- Morris.js charts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
    <script src="<?= _SPPATH . _THEMEPATH; ?>/plugins/morris/morris.min.js"></script>

    <!-- FastClick -->
    <script src="<?= _SPPATH . _THEMEPATH; ?>/plugins/fastclick/fastclick.js"></script>
    <!-- AdminLTE App -->
    <script src="<?= _SPPATH . _THEMEPATH; ?>/dist/js/app.js"></script>
    <!-- Sparkline -->
    <script src="<?= _SPPATH . _THEMEPATH; ?>/plugins/sparkline/jquery.sparkline.min.js"></script>
    <!-- jvectormap -->
    <script src="<?= _SPPATH . _THEMEPATH; ?>/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
    <script src="<?= _SPPATH . _THEMEPATH; ?>/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
    <!-- SlimScroll 1.3.0 -->
    <script src="<?= _SPPATH . _THEMEPATH; ?>/plugins/slimScroll/jquery.slimscroll.min.js"></script>
    <!-- ChartJS 1.0.1 -->
    <? /* <script src="<?=_SPPATH._THEMEPATH;?>/plugins/chartjs/Chart.min.js"></script> */ ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js"></script>
    <? /*
              <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
              <script src="<?=_SPPATH._THEMEPATH;?>/dist/js/pages/dashboard2.js"></script>
             */ ?>
    <!-- AdminLTE for demo purposes -->
    <script src="<?= _SPPATH . _THEMEPATH; ?>/dist/js/demo.js"></script>

    <style>
        .icon-item {
            cursor: pointer;
            font-size: 15px;
        }
    </style>


    <!-- add new -->
    <script src="<?= _SPPATH; ?>js/viel-windows-jquery.js?t=<?= time(); ?>"></script>

    <script>
        function activkanMenuKiri(id) {
            kosongkanMenuKiri();
            $("#RegistorMenu_" + id).addClass('activkanMenu');
        }

        function kosongkanMenuKiri() {
            console.log(registorMenuKiri);
            for (var key in registorMenuKiri) {
                var id = registorMenuKiri[key];
                $("#RegistorMenu_" + id).removeClass('activkanMenu');
            }
        }

        (function ($) {

            /**
             * @function
             * @property {object} jQuery plugin which runs handler function once specified element is inserted into the DOM
             * @param {function} handler A function to execute at the time when the element is inserted
             * @param {bool} shouldRunHandlerOnce Optional: if true, handler is unbound after its first invocation
             * @example $(selector).waitUntilExists(function);
             */

            $.fn.waitUntilExists = function (handler, shouldRunHandlerOnce, isChild) {
                var found = 'found';
                var $this = $(this.selector);
                var $elements = $this.not(function () {
                    return $(this).data(found);
                }).each(handler).data(found, true);

                if (!isChild) {
                    (window.waitUntilExists_Intervals = window.waitUntilExists_Intervals || {})[this.selector] =
                        window.setInterval(function () {
                            $this.waitUntilExists(handler, shouldRunHandlerOnce, true);
                        }, 500)
                    ;
                } else if (shouldRunHandlerOnce && $elements.length) {
                    window.clearInterval(window.waitUntilExists_Intervals[this.selector]);
                }

                return $this;
            }

        }(jQuery));
    </script>
    <style>
        .clickable {
            cursor: pointer;
        }

        li.activkanMenu > a {
            font-weight: bold;
            color: #000000;
        }

        span.highlight {
            background-color: #ffff00;
        }
    </style>
</body>
</html>
