<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of StokWeb
 *
 * @author efindiongso
 */
class StokWeb extends WebService
{

    // KPO
    public function create_stok_buku_dan_barang_kpo()
    {
        $kpo_id = AccessRight::getMyOrgID();
        $barang = new BarangWebModel();
        $arrBarang = $barang->getWhere("kpo_id='$kpo_id'");
        $org = new SempoaOrg();
        $acc = new SempoaAccount();
        $t = time();
        $arrJenisBarang = Generic::getJenisBiaya();
        ?>
        <h1>Kartu Stock</h1>
        <form id="kartuStock_<?= $kpo_id . $acc->getMyID(); ?>">
            <div class="form-group">
                <label for="namaBarang">Jenis Barang:</label>
                <select class="form-control" id="jenis_barang_<?= $t; ?>">
                    <?
                    foreach ($arrJenisBarang as $key => $jenisBarang) {
                        ?>
                        <option id='<?= $jenisBarang . "_" . $t; ?>'
                                value="<?= $key; ?>"><?= $jenisBarang; ?></option>
                        <?
                    }
                    ?>
                </select>

                <label for="namaBarang">Nama Barang:</label>
                <select class="form-control" id="buku_<?= $t; ?>">
                    <?
                    foreach ($arrBarang as $barang) {
                        ?>
                        <option id='<?= $barang->nama_barang . "_" . $t; ?>'
                                value="<?= $barang->id_barang_harga; ?>"><?= $barang->nama_barang; ?></option>
                        <?
                    }
                    ?>
                </select>
                <label for="Penginput">Penginput:</label>
                <input type="text" class="form-control" id="id_nama_<?= $t; ?>" value="<?= $acc->getMyName(); ?>"
                       readonly>

                <label for="NamaOrganisasi">Nama Organisasi:</label>
                <input type="text" class="form-control" id="id_pemilik_barang_<?= $t; ?>" placeholder="Organisasi"
                       value="<?= Generic::getTCNamebyID(AccessRight::getMyOrgID()); ?>" readonly>

                <label for="tanggal">Tanggal:</label>
                <input type="datetime" class="form-control" id="tanggal_<?= $t; ?>" value="<?= leap_mysqldate(); ?>"
                       readonly>
                <label for="stockMasuk">Stock Masuk:</label>
                <input type="text" class="form-control" id="stockMasuk_<?= $t; ?>" placeholder="Stock masuk">

                <label id="no_buku_<?= $t; ?>" for="noBuku">No Buku Mulai:</label>
                <input id="no_buku_masuk_<?= $t; ?>" type="number" min="0"
                       onKeyPress="if(this.value.length==5) return false;" class="form-control"
                       id="no_buku_masuk_<?= $t; ?>"
                       placeholder="No. Buku dimulai" required>


                <label for="keterangan">Keterangan:</label>
                <input type="text" class="form-control" id="keterangan_<?= $t; ?>" placeholder="Keterangan">

                <div class="err_text" id="biaya_"></div>
            </div>
            <button class="btn btn-default" id="btn_kartuStock_<?= $kpo_id . $acc->getMyID(); ?>" type="button">Submit
            </button>
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

            $(document).ready(function () {
                var jenis_barang = $('#jenis_barang_<?= $t; ?> option:selected').val();
                getBarangByIdJenis(jenis_barang);
                $("#no_buku_<?= $t; ?>").hide();
                $("#no_buku_masuk_<?= $t; ?>").hide();
            });

            $("#jenis_barang_<?= $t; ?>").change(function () {
                var jenis_barang = $('#jenis_barang_<?= $t; ?> option:selected').val();
                getBarangByIdJenis(jenis_barang);
                if (jenis_barang == <?=KEY::$JENIS_BUKU;?>) {
                    $("#no_buku_<?= $t; ?>").show();
                    $("#no_buku_masuk_<?= $t; ?>").show();
//                    alert($('#jenis_barang_<?//= $t; ?>// option:selected').val());
//                    alert($('#jenis_barang_<?//= $t; ?>// option:selected').text());
                }
                else {
                    $("#no_buku_<?= $t; ?>").hide();
                    $("#no_buku_masuk_<?= $t; ?>").hide();
                }

            });

            function getBarangByIdJenis(slc) {
                $.ajax({
                    type: "GET",
                    url: "<?= _SPPATH; ?>StockWebHelper/loadJenisBarang?",
                    data: 'jenis_barang=' + slc,
                    success: function (data) {
                        console.log(data);
                        $("#buku_<?= $t; ?>").html(data);
                    }
                });
            }

            $('#btn_kartuStock_<?= $kpo_id . $acc->getMyID(); ?>').click(function () {
                if (confirm("Anda yakin?")) {
                    var stk_masuk = $('#stockMasuk_<?= $t; ?>').val();
                    var id_barang = $('#buku_<?= $t; ?> option:selected').val();
                    var name = $('#id_nama_<?= $t; ?>').val();
                    var id_pemilik_barang = '<?=AccessRight::getMyOrgID();?>';
//                    alert(id_pemilik_barang);
                    var tanggal = $('#tanggal_<?= $t; ?>').val();
                    var keterangan = $('#keterangan_<?= $t; ?>').val();
                    var name_barang = $('#buku_<?= $t; ?> option:selected').text();
                    var jenis_barang = $('#jenis_barang_<?= $t; ?> option:selected').val();

                    if (jenis_barang != <?=KEY::$JENIS_BUKU;?>) {
                        $.get("<?= _SPPATH; ?>StockWebHelper/insertKartuStock?id_barang=" + id_barang + "&stk_masuk=" + stk_masuk + "&tanggal=" + tanggal + "&pemilik=" + id_pemilik_barang + "&id_nama=" + name + "&keterangan=" + keterangan + "&name_barang=" + name_barang, function (data) {
                            console.log(data);
                            if (data.status_code) {
                                console.log(data.id_pemilik_barang);
                                alert("Success!");
                                //                            lwrefresh("read_stok_buku_dan_barang_kpo");
                                //                            lwrefresh("read_kartu_stok_kpo");
                            } else {
                                alert(data.status_message);
                            }
                        }, 'json');
                    }
                    else {
                        var no_buku = $('#no_buku_masuk_<?= $t; ?>').val();
                        no_buku = parseInt(no_buku);
                        $.get("<?= _SPPATH; ?>StockWebHelper/insertKartuStockBuku?id_barang=" + id_barang + "&no_buku=" + no_buku + "&stk_masuk=" + stk_masuk + "&tanggal=" + tanggal + "&pemilik=" + id_pemilik_barang + "&id_nama=" + name + "&keterangan=" + keterangan + "&name_barang=" + name_barang, function (data) {
                            console.log(data);
                            if (data.status_code) {
                                console.log(data);
                                alert("Success!");
                                //                            lwrefresh("read_stok_buku_dan_barang_kpo");
                                //                            lwrefresh("read_kartu_stok_kpo");
                            } else {
                                alert(data.status_message);
                            }
                        }, 'json');
                    }
                }

            });

        </script>
        <?
    }

    public function read_stok_buku_dan_barang_kpo_tmp()
    {
        $myID = AccessRight::getMyOrgID();
        $arrMyIBO = (Generic::getAllsMyIBO($myID));
        $myStock = new StockModel();
        $arrMyStocks = $myStock->getWhere("org_id='$myID'");
//        pr($arrMyStocks);
        $objStock = new StockModel();
        ?>
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>Nama\Jumlah</th>

                <?
                foreach ($arrMyStocks as $myStock) {
                    $reihenFolge[] = $myStock->id_barang;
                    ?>
                    <th id='<?= $myStock->id_barang; ?>'>
                        <b><?= Generic::getNamaBarangByIDKPOID($myStock->id_barang, $myID); ?></b></th>
                    <?
                }
                ?>
            </tr>
            </thead>
            <tbody>

            <tr>
                <td id='<?= $key . "_" . $ibo; ?>'>
                    Stock
                </td>
                <?
                foreach ($reihenFolge as $keyreihen => $reihen) {
                    $arrStok = $objStock->getWhere("org_id='$myID' AND id_barang='$reihen'");
                    ?>
                    <td id='<?= $key . "_" . $reihen; ?>'><?= $arrStok[0]->jumlah_stock; ?></td>
                    <?
                }
                ?>

            </tr>

            <tr>
                <td id='<?= $key . "_" . $ibo; ?>'>
                    Stock Hold
                </td>
                <?
                foreach ($reihenFolge as $keyreihen => $reihen) {
                    $arrStok = $objStock->getWhere("org_id='$myID' AND id_barang='$reihen'");
                    ?>
                    <td id='<?= $key . "_" . $reihen; ?>'><?= $arrStok[0]->jumlah_stock_hold; ?></td>
                    <?
                }
                ?>

            </tr>
            </tbody>
        </table>
        <center>
            <button id="refresh_stock_<?= time(); ?>" class="btn btn-success text-center">Update Stock</button>
        </center>
        <script>
            $('#refresh_stock_<?= time(); ?>').click(function () {
                lwrefresh(selected_page);
            });
        </script>

        <?
    }

    public function read_stok_buku_dan_barang_kpo()
    {
//        pr("asasas");
        $myID = AccessRight::getMyOrgID();
//        $arrMyIBO = (Generic::getAllsMyIBO($myID));
        $myStock = new StockModel();
        $arrMyStocks = $myStock->getWhere("org_id='$myID'");
//        pr($arrMyStocks);
        $objStock = new StockModel();
        ?>
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>Nama</th>
                <th>Jumlah Stock</th>
                <th>Jumlah Stock Hold</th>
            </tr>
            </thead>
            <tbody>
            <?
            $arrStok = $objStock->getWhere("org_id='$myID'");
            $arrNamaBarang = Generic::getNamaBarang();
            foreach ($arrStok as $keyreihen => $reihen) {
                ?>
                <tr>
                    <td>
                        <?= $arrNamaBarang[$reihen->id_barang]; ?>
                    </td>
                    <td style="text-align: left;">
                        <?= $reihen->jumlah_stock; ?>
                    </td>
                    <td style="text-align: left;">
                        <?= $reihen->jumlah_stock_hold; ?>
                    </td>
                </tr>
                <?
            }
            ?>


            </tr>
            </tbody>
        </table>
        <center>
            <button id="refresh_stock_<?= time(); ?>" class="btn btn-success text-center">Update Stock</button>
        </center>
        <script>
            $('#refresh_stock_<?= time(); ?>').click(function () {
                lwrefresh(selected_page);
            });
        </script>

        <?
    }

    public static function formBarang()
    {
        $kpo_id = AccessRight::getMyOrgID();
        $barang = new BarangWebModel();
        $arrBarang = $barang->getWhere("kpo_id='$kpo_id'");
        $org = new SempoaOrg();
        $acc = new SempoaAccount();
        $t = time();
        ?>
        <h1>Kartu Stock</h1>
        <form id="kartuStock_<?= $kpo_id . $acc->getMyID(); ?>">
            <div class="form-group">
                <label for="namaBarang">Nama Barang:</label>
                <select class="form-control" id="buku_<?= $t; ?>">
                    <?
                    foreach ($arrBarang as $barang) {
                        ?>
                        <option id='<?= $barang->nama_barang . "_" . $t; ?>'
                                value="<?= $barang->id_barang_harga; ?>"><?= $barang->nama_barang; ?></option>
                        <?
                    }
                    ?>
                </select>
                <label for="Penginput">Penginput:</label>
                <input type="text" class="form-control" id="id_nama_<?= $t; ?>" value="<?= $acc->getMyName(); ?>"
                       readonly>

                <label for="NamaOrganisasi">Nama Organisasi:</label>
                <input type="text" class="form-control" id="id_pemilik_barang_<?= $t; ?>" placeholder="Organisasi"
                       value="<?= AccessRight::getMyOrgID(); ?>" readonly>

                <label for="tanggal">Tanggal:</label>
                <input type="datetime" class="form-control" id="tanggal_<?= $t; ?>" value="<?= leap_mysqldate(); ?>"
                       readonly>
                <label for="stockMasuk">Stock Masuk:</label>
                <input type="text" class="form-control" id="stockMasuk_<?= $t; ?>" placeholder="Stock masuk" readonly>

                <label for="keterangan">Keterangan:</label>
                <input type="text" class="form-control" id="keterangan_<?= $t; ?>" placeholder="Keterangan">

                <div class="err_text" id="biaya_"></div>
            </div>
            <button class="btn btn-default" id="btn_kartuStock_<?= $kpo_id . $acc->getMyID(); ?>" type="submit">Submit
            </button>
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

            $('#btn_kartuStock_<?= $kpo_id . $acc->getMyID(); ?>').click(function () {
                if (confirm("Anda yakin?")) {
                    //                    var post = {};
                    var stk_masuk = $('#stockMasuk_<?= $t; ?>').val();
                    var stk_keluar = $('#stockKeluar_<?= $t; ?>').val();
                    var id_barang = $('#buku_<?= $t; ?> option:selected').val();
                    var name = $('#id_nama_<?= $t; ?>').val();
                    var id_pemilik_barang = $('#id_pemilik_barang_<?= $t; ?>').val();
                    var tanggal = $('#tanggal_<?= $t; ?>').val();
                    var keterangan = $('#keterangan_<?= $t; ?>').val();
                    //                    var post = $("#barang_<?= $mytype; ?>_<?= AccessRight::getMyOrgID(); ?>").serialize();
                    $.get("<?= _SPPATH; ?>StockWebHelper/insertKartuStock?id_barang=" + id_barang + "&stk_masuk=" + stk_masuk + "&tanggal=<?=  leap_mysqldate();?>&pemilik=" + id_pemilik_barang + "&id_nama=" + name + "&keterangan=" + keterangan, function (data) {
                        console.log(data);
                        if (data.status_code) {
                            //success
                            alert(data.status_message);
                            openLw("Stock", "<?= _SPPATH; ?>StokWeb/read_stok_buku_dan_barang_kpo", fade);
                        } else {
                            alert(data.status_message);
                        }
                    }, 'json');
                }

            });

        </script>
        <?
    }

    public function get_my_stok_barang_dan_buku_ibo()
    {
        $myID = AccessRight::getMyOrgID();
        $arrMyStocks = Generic::getMyStock($myID);
        $arrNamaBarang = Generic::getNamaBarang();
        ?>
        <h1>Jumlah Stock <?= Account::getMyName(); ?></h1>
        <table class="table table-bordered table-striped table-responsivet">
            <thead>
            <tr>
                <th>Nama</th>
                <th>Jumlah Stock</th>
                <th>Jumlah Stock Hold</th>
            </tr>
            </thead>
            <tbody>
            <?
            foreach ($arrMyStocks as $val) {
                ?>
                <tr>
                    <td><?= $arrNamaBarang[$val->id_barang]; ?></td>
                    <td><?= $val->jumlah_stock; ?></td>
                    <td><?= $val->jumlah_stock_hold; ?></td>
                </tr>
                <?
            }
            ?>

            </tbody>
        </table>

        <center>
            <button id="update_stock_ibo_<?= time(); ?>" class="btn btn-success text-center">Update</button>
        </center>
        <script>
            $('#update_stock_ibo_<?= time(); ?>').click(function () {
                lwrefresh(selected_page);
            });
        </script>
        <?
    }

    // KPO
    public function read_kartu_stok_kpo()
    {
        $myOrgID = AccessRight::getMyOrgID();
        $objStocks = new KartuStockModel();
        $arrStocks = $objStocks->getWhere("id_pemilik_barang=$myOrgID ORDER BY 	tanggal_input DESC");
        ?>
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th><b>ID</b></th>
                <th><b>Barang</b></th>
                <th><b>Tanggal</b></th>
                <th><b>stock Masuk</b></th>
                <th><b>keterangan</b></th>
                <th><b>Penanggung Jawab</b></th>
            </tr>
            </thead>
            <tbody>
            <?
            foreach ($arrStocks as $val) {
                ?>
                <tr class="<?= $val->kartu_id; ?>">
                    <td class="<?= $val->kartu_id; ?>">
                        <?= $val->kartu_id; ?>
                    </td>
                    <td>
                        <?= Generic::getNamaBarangByIDKPOID($val->id_barang, $myOrgID); ?>
                    </td>
                    <td>
                        <?= $val->tanggal_input; ?>
                    </td>
                    <td>
                        <?= $val->stock_masuk; ?>
                    </td>
                    <td>
                        <?= $val->keterangan; ?>
                    </td>

                    <td>
                        <?= $val->nama_penerima_barang; ?>
                    </td>
                </tr>

                <?
            }
            ?>
            </tbody>
        </table>
        <center>
            <button id="refresh_stock_<?= time(); ?>" class="btn btn-success text-center">Update</button>
        </center>
        <script>
            $('#refresh_stock_<?= time(); ?>').click(function () {
                lwrefresh(selected_page);
            });
        </script>
        <?
    }

    // TC

    public function get_my_stok_barang_dan_buku_tc()
    {
        $myID = AccessRight::getMyOrgID();
        $arrMyStocks = Generic::getMyStock($myID);
//        pr($arrMyStocks);
        ?>
        <h1>Jumlah Stock <?= Account::getMyName(); ?></h1>
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>Nama Barang</th>
                <th>Jumlah Stock</th>
            </tr>
            </thead>
            <thead>


            <?
            foreach ($arrMyStocks as $key => $myStock) {
                ?>
                <tr>
                    <td><?= Generic::getNamaBarangByIDBarang($myStock->id_barang); ?></td>
                    <td><?= $myStock->jumlah_stock; ?></td>
                </tr>

                <?
            }
            ?>


            </thead>
        </table>
        <center>
            <button id="refresh_stock_<?= time(); ?>" class="btn btn-success text-center">Update</button>
        </center>
        <script>
            $('#refresh_stock_<?= time(); ?>').click(function () {
                lwrefresh(selected_page);
            });
        </script>
        <?
    }


    public function read_buku_by_no_available()
    {

        $myorgid = AccessRight::getMyOrgID();
        $myOrgType = AccessRight::getMyOrgType();
        $arrJenisBarang = Generic::getLevelByBarangID();
        $arrStatusBuku = Generic::getStatusBuku();
        $stockNo = new StockBuku();
        $brg_id = key($arrJenisBarang);
        if ($myOrgType == KEY::$KPO) {
            $arrStock = $stockNo->getWhere("stock_buku_status_kpo = 1 AND stock_id_buku = $brg_id AND stock_buku_kpo =$myorgid ORDER by stock_buku_no ASC");
        } elseif ($myOrgType == KEY::$IBO) {
            $arrStock = $stockNo->getWhere("stock_status_ibo = 1 AND stock_id_buku = $brg_id AND stock_buku_ibo =$myorgid ORDER by stock_buku_no ASC");
        } elseif ($myOrgType == KEY::$TC) {

            $arrStock = $stockNo->getWhere("stock_status_tc = 1 AND stock_id_buku = $brg_id AND stock_buku_tc =$myorgid ORDER by stock_buku_no ASC");
        }
        $i = 0;
        $t = time();
        ?>

        <div id="container_level_<?= $t; ?>">
            <section class="content-header">
                <h1>
                    <div class="pull-right" style="font-size: 13px;">


                        <label for="exampleInputName2">Pilih Level:</label>
                        <select id="pilih_level_<?= $t; ?>">

                            <?
                            $selected = "";
                            foreach ($arrJenisBarang as $key => $val) {
                                ?>
                                <option value="<?= $key; ?>" <?= $selected ?>><?= $val; ?></option>
                                <?
                            }
                            ?>
                        </select>

                        <button class="btn btn-default" id="submit_pilih_level_<?= $t; ?>">Submit</button>
                    </div>
                </h1>
            </section>

            <script>
                $("#submit_pilih_level_<?= $t; ?>").click(function () {
                    var slc = $('#pilih_level_<?= $t; ?>').val();


                    $('#sum_barang_<?=$t;?>').load('<?= _SPPATH; ?>StockWebHelper/get_anzahl_available_buku?brg_id=' + slc, function () {

                    }, 'json');
                    $('#content_load_buku_<?=$t;?>').load('<?= _SPPATH; ?>StockWebHelper/get_available_buku?brg_id=' + slc, function () {

                    }, 'json');


                });
            </script>
            <div class="clearfix"></div>
            <section class="content">

                <div id="sum_barang_<?= $t; ?>"
                     style="text-align: left; font-size: 16px;"><?= "<b>Jumlah buku yang tersedia: " . count($arrStock) . "</b><br>"; ?>

                </div>
                <table id="container_buku_tersedia_<?= $t; ?>" class="table table-bordered table-striped"
                       style="margin-top: 20px;">
                    <thead>
                    <tr>
                        <th><b>ID</b></th>
                        <th><b>No. Buku</b></th>
                        <th><b>Level</b></th>
                        <th><b>Tanggal Masuk</b></th>
                        <th><b>Status</b></th>
                    </tr>
                    </thead>
                    <tbody id="content_load_buku_<?= $t; ?>">
                    <?
                    foreach ($arrStock as $val) {
                        $i++;
                        ?>
                        <tr>
                            <td id="no_<?= $val->stock_buku_id . $t; ?>"><?= $i; ?></td>
                            <td id="no_buku_<?= $val->stock_buku_id . $t; ?>"><?= $val->stock_buku_no; ?></td>
                            <td id="jenis_buku_level_<?= $val->stock_buku_id . $t; ?>"><?= $arrJenisBarang[$val->stock_id_buku]; ?></td>
                            <td id="tanggal_masuk_<?= $val->stock_buku_id . $t; ?>"><?
                                if ($myOrgType == KEY::$KPO) {
                                    echo $val->stock_buku_tgl_masuk_kpo;
                                } elseif ($myOrgType == KEY::$IBO) {
                                    echo $val->stock_buku_tgl_masuk_ibo;
                                } elseif ($myOrgType == KEY::$TC) {
                                    echo $val->stock_buku_tgl_masuk_tc;
                                }
                                ?></td>
                            <td id="status_<?= $val->stock_buku_id . $t; ?>"><?
                                if ($myOrgType == KEY::$KPO) {
                                    echo $arrStatusBuku[$val->stock_buku_status_kpo];
                                } elseif ($myOrgType == KEY::$IBO) {
                                    echo $arrStatusBuku[$val->stock_status_ibo];
                                } elseif ($myOrgType == KEY::$TC) {
                                    echo $arrStatusBuku[$val->stock_status_tc];
                                }
                                ?></td>
                            <script>

                                $('#status_<?=$val->stock_buku_id . $t;?>').dblclick(function () {
                                    var current = $("#status_<?=$val->stock_buku_id . $t;?>").html();
                                    if (current == '<?=KEY::$BUKU_AVAILABLE_TEXT?>') {
                                        var html = "<select id='select_status_<?= $t; ?>'>" +
                                            "<option value=<?=KEY::$BUKU_AVAILABLE_ALIAS;?>><?=KEY::$BUKU_AVAILABLE_TEXT;?></option>" +
                                            "<option value=<?=KEY::$BUKU_RUSAK_ALIAS;?>><?=KEY::$BUKU_RUSAK_TEXT;?></option></select>";
                                        $("#status_<?=$val->stock_buku_id . $t;?>").html(html);
                                        $('#status_<?=$val->stock_buku_id . $t;?>').change(function () {
                                            var id_status = $('#select_status_<?= $t; ?>').val();
                                            var buku_no = $('#no_buku_<?=$val->stock_buku_id . $t;?>').text();
                                            $.get("<?= _SPPATH; ?>StockWebHelper/setStatusBukuAvailableKeRusak?id_status=" + id_status + "&buku_no=" + buku_no, function (data) {

                                                if (data.status_code) {
                                                    //success
                                                    alert(data.status_message);
                                                    lwrefresh(selected_page);
                                                } else {
                                                    alert(data.status_message);
                                                }
                                            }, 'json');
                                        });
                                    }
                                });
                            </script>
                        </tr>
                        <?
                    }
                    ?>
                    </tbody>
                </table>

            </section>

        </div>


        <?

    }


    // persedian tidak tersedia
    public function read_buku_by_no_not_available()
    {

        $myorgid = AccessRight::getMyOrgID();
        $myOrgType = AccessRight::getMyOrgType();

        $t = time();

        $bln = isset($_GET['bln']) ? addslashes($_GET['bln']) : date("n");
        $thn = isset($_GET['thn']) ? addslashes($_GET['thn']) : date("Y");
        $arrJenisBarang = Generic::getLevelByBarangID();
        $arrStatusBuku = Generic::getStatusBuku();
        $stockNo = new StockBuku();
        $brg_id = key($arrJenisBarang);
        if ($myOrgType == KEY::$KPO) {
            $arrIbos = Generic::getAllMyIBO($myorgid);
            pr($arrIbos);
            pr(Key($arrIbos));

            $ibo_id = isset($_GET['ibo_id']) ? addslashes($_GET['ibo_id']) : Key($arrIbos);
            pr($ibo_id);
            $arrStock = $stockNo->getWhere("stock_buku_status_kpo = 0  AND stock_id_buku = $brg_id AND stock_buku_kpo =$myorgid AND stock_buku_ibo=$ibo_id AND MONTH(stock_buku_tgl_keluar_kpo)='$bln'  AND YEAR(stock_buku_tgl_keluar_kpo)= '$thn'  ORDER by stock_buku_id ASC");
//            $arrStock2 = $stockNo->getWhere("stock_buku_status_kpo = 0  AND stock_buku_kpo =$myorgid ORDER by stock_buku_id ASC");
            $arrStock2 = $stockNo->getWhere("stock_buku_status_kpo = 0  AND stock_buku_kpo =$myorgid ORDER by stock_buku_no ASC");

        } elseif ($myOrgType == KEY::$IBO) {
            $arrMyTC = Generic::getAllMyTC(AccessRight::getMyOrgID());
            $tc_id = isset($_GET['tc_id']) ? addslashes($_GET['tc_id']) : Key($arrMyTC);
            $arrStock = $stockNo->getWhere("stock_status_ibo = 0 AND stock_id_buku = $brg_id AND stock_buku_ibo =$myorgid AND stock_buku_tc=$tc_id AND MONTH(stock_buku_tgl_keluar_ibo)='$bln'  AND YEAR(stock_buku_tgl_keluar_ibo)= '$thn'  ORDER by stock_buku_id ASC");
//            $arrStock2 = $stockNo->getWhere("stock_status_ibo = 0 AND stock_buku_ibo =$myorgid ORDER by stock_buku_id ASC");
            $arrStock2 = $stockNo->getWhere("stock_status_ibo = 0 AND stock_buku_ibo =$myorgid ORDER by stock_buku_no ASC");

        } elseif ($myOrgType == KEY::$TC) {
            //SELECT * FROM 	sempoa__stock_buku where MONTH(`stock_buku_tgl_keluar_tc`)=8 AND YEAR(`stock_buku_tgl_keluar_tc`)=2017
            $arrStock = $stockNo->getWhere("stock_status_tc = 0 AND stock_murid =1 AND stock_id_buku = $brg_id AND stock_buku_tc =$myorgid  AND MONTH(stock_buku_tgl_keluar_tc)='$bln'  AND YEAR(stock_buku_tgl_keluar_tc)= '$thn' ORDER by stock_buku_id ASC");
//            $arrStock2 = $stockNo->getWhere("stock_status_tc = 0 AND stock_murid =1 AND  stock_buku_tc =$myorgid  ORDER by stock_buku_id ASC");
            $arrStock2 = $stockNo->getWhere("stock_status_tc = 0 AND stock_murid =1 AND  stock_buku_tc =$myorgid  ORDER by stock_buku_no ASC");

        }
        $arrkuponHlp = array();
        foreach ($arrStock2 as $stock) {
            $arrkuponHlp[] = $stock->stock_buku_no;

        }
        $arrBulan = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12);
        $i = 0;
        ?>

        <div id="container_level_<?= $t; ?>">
            <section class="content-header">
                <h1>
                    <div class="pull-left" style="font-size: 13px;">
                        <input class="input-sm" type="text" style="border-radius: 0px;" id="suche_no_buku_2_<?= $t; ?>"
                               placeholder="Search No. Buku">
                        <script>
                            $(function () {
                                var availableTags = <? echo json_encode($arrkuponHlp);?>;
                                $("#suche_no_buku_2_<?=$t;?>").autocomplete({
                                    source: availableTags
                                });

                            });
                            $("#suche_no_buku_2_<?=$t;?>").change(function () {
                                var no_buku = $("#suche_no_buku_2_<?=$t;?>").val();
                                $('#content_load_buku_<?=$t;?>').load('<?= _SPPATH; ?>StockWebHelper/get_non_available_buku_search?no_buku_search=' + no_buku, function () {

                                }, 'json');
                            });

                        </script>
                    </div>

                    <div class="pull-right" style="font-size: 13px;">


                        <? if ($myOrgType == KEY::$IBO) {
                            ?>
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
                            <?
                        } elseif ($myOrgType == KEY::$KPO) {
                            ?>
                            <label for="exampleInputName2">Pilih IBO:</label>
                            <select id="pilih_IBO_<?= $t; ?>">

                                <?
                                foreach ($arrIbos as $key => $val) {
                                    ?>
                                    <option value="<?= $key; ?>"><?= $val; ?></option>
                                    <?
                                }
                                ?>
                            </select>
                            <?
                        }

                        ?>

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

                        <label for="exampleInputName2">Pilih Level:</label>
                        <select id="pilih_level_<?= $t; ?>">

                            <?
                            $selected = "";
                            foreach ($arrJenisBarang as $key => $val) {
                                ?>
                                <option value="<?= $key; ?>" <?= $selected ?>><?= $val; ?></option>
                                <?
                            }
                            ?>
                        </select>

                        <button class="btn btn-default" id="submit_pilih_level_<?= $t; ?>">Submit</button>
                    </div>
                </h1>
            </section>

            <script>
                $("#submit_pilih_level_<?= $t; ?>").click(function () {
                    var slc = $('#pilih_level_<?= $t; ?>').val();
                    var bln = $('#bulan_<?= $t; ?>').val();
                    var thn = $('#tahun_<?= $t; ?>').val();
                    var tc_id = $('#pilih_TC_<?= $t; ?>').val();
                    var ibo_id = $('#pilih_IBO_<?= $t; ?>').val();
                    $('#content_load_buku_<?=$t;?>').load('<?= _SPPATH; ?>StockWebHelper/get_non_available_buku?brg_id=' + slc + "&bln=" + bln + "&thn=" + thn + "&tc_id=" + tc_id + "&ibo_id=" + ibo_id, function () {

                    }, 'json');
                });

            </script>
            <div class="clearfix"></div>
            <section class="content">

                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th><b>ID</b></th>
                        <th><b>No. Buku</b></th>
                        <th><b>Level</b></th>
                        <th><b>Tanggal Keluar</b></th>
                        <th><b>Pembeli</b></th>
                        <th><b>Status</b></th>
                    </tr>
                    </thead>
                    <tbody id="content_load_buku_<?= $t; ?>">
                    <?
                    foreach ($arrStock as $val) {
                        $i++;
                        ?>
                        <tr>
                            <td id="no_<?= $val->stock_buku_id . $t; ?>"><?= $i; ?></td>
                            <td id="no_buku_<?= $val->stock_buku_id . $t; ?>"> <?= $val->stock_buku_no; ?></td>
                            <td id="level_buku_<?= $val->stock_buku_id . $t; ?>"><?= $arrJenisBarang[$val->stock_id_buku]; ?></td>
                            <td id="tgl_keluar_<?= $val->stock_buku_id . $t; ?>"><?
                                if ($myOrgType == KEY::$KPO) {
                                    echo $val->stock_buku_tgl_keluar_kpo;
                                } elseif ($myOrgType == KEY::$IBO) {
                                    echo $val->stock_buku_tgl_keluar_ibo;
                                } elseif ($myOrgType == KEY::$TC) {
                                    echo $val->stock_buku_tgl_keluar_tc;
                                }
                                ?></td>

                            <td id="pembeli_<?= $val->stock_buku_id . $t; ?>"><?
                                if ($myOrgType == KEY::$KPO) {
                                    echo Generic::getTCNamebyID($val->stock_buku_ibo);
                                } elseif ($myOrgType == KEY::$IBO) {
                                    echo Generic::getTCNamebyID($val->stock_buku_tc);
                                } elseif ($myOrgType == KEY::$TC) {
                                    echo Generic::getMuridNamebyID($val->stock_murid_id);
                                }
                                ?></td>
                            <td id="status_<?= $val->stock_buku_id . $t; ?>"><?
                                if ($myOrgType == KEY::$KPO) {
                                    echo $arrStatusBuku[$val->stock_buku_status_kpo];
                                } elseif ($myOrgType == KEY::$IBO) {
                                    echo $arrStatusBuku[$val->stock_status_ibo];
                                } elseif ($myOrgType == KEY::$TC) {
                                    echo $arrStatusBuku[$val->stock_status_tc];
                                }
                                ?></td>
                            <script>
                                <?
                                if($myOrgType == KEY::$TC){
                                ?>
                                $('#status_<?= $val->stock_buku_id . $t; ?>').dblclick(function () {
                                        var current = $("#status_<?=$val->stock_buku_id . $t;?>").html();
                                        if (current == '<?=KEY::$BUKU_NON_AVAILABLE_TEXT;?>') {
                                            var html = "<select id='select_status_<?= $t; ?>'>" +
                                                "<option value=<?=KEY::$BUKU_NON_AVAILABLE_ALIAS;?>><?=KEY::$BUKU_NON_AVAILABLE_TEXT;?></option>" +
                                                "<option value=<?=KEY::$BUKU_RUSAK_ALIAS;?>><?=KEY::$BUKU_RUSAK_TEXT;?></option></select>";
                                            $("#status_<?=$val->stock_buku_id . $t;?>").html(html);
                                            $('#status_<?=$val->stock_buku_id . $t;?>').change(function () {
                                                var id_status = $('#select_status_<?= $t; ?>').val();
                                                var buku_no = $('#no_buku_<?= $val->stock_buku_id . $t; ?>').text();
                                                $.get("<?= _SPPATH; ?>StockWebHelper/setStatusBukuNonAvailableKeRusak?id_status=" + id_status + "&buku_no=" + buku_no, function (data) {
                                                    console.log(data);
                                                    if (data.status_code) {
                                                        //success
                                                        alert(data.status_message);
                                                        lwrefresh(selected_page);
                                                    } else {
                                                        alert(data.status_message);
                                                    }
                                                }, 'json');
                                            });
                                        }
                                    }
                                );
                                <?
                                }
                                ?>

                            </script>
                        </tr>
                        <?
                    }
                    ?>
                    </tbody>
                </table>

            </section>

        </div>


        <?

    }


    public function read_buku_by_no_available_ibo()
    {
        $this->read_buku_by_no_available();
    }

    public function read_buku_by_no_not_available_ibo()
    {
        $this->read_buku_by_no_not_available();
    }


    public function read_buku_by_no_available_tc()
    {
        $this->read_buku_by_no_available();
    }

    public function read_buku_by_no_not_available_tc()
    {
        $this->read_buku_by_no_not_available();
    }

    public function read_buku_by_no_rusak_tc()
    {
        $this->read_buku_by_no_rusak();
    }

    public function read_buku_by_no_rusak()
    {

        $myorgid = AccessRight::getMyOrgID();
        $myOrgType = AccessRight::getMyOrgType();
        $arrJenisBarang = Generic::getLevelByBarangID();
        $arrStatusBuku = Generic::getAllStatusBuku();
        $stockNo = new StockBuku();
        $brg_id = key($arrJenisBarang);
        if ($myOrgType == KEY::$KPO) {
            $arrStock = $stockNo->getWhere("stock_buku_status_kpo = 99  AND stock_buku_kpo =$myorgid ORDER by stock_buku_tgl_status_rusak DESC");
        } elseif ($myOrgType == KEY::$IBO) {
            $arrStock = $stockNo->getWhere("stock_status_ibo = 99  AND stock_buku_ibo =$myorgid ORDER by stock_buku_tgl_status_rusak DESC");
        } elseif ($myOrgType == KEY::$TC) {

            $arrStock = $stockNo->getWhere("(stock_status_tc = 99 OR stock_murid = 99 )AND stock_buku_tc =$myorgid ORDER by stock_buku_tgl_status_rusak DESC");
        }
        $i = 0;
        $t = time();
        ?>
        <style>
            .table-fixed thead {
                width: 97%;
            }
            .table-fixed tbody {
                height: 230px;
                overflow-y: auto;
                width: 100%;
            }
            .table-fixed thead, .table-fixed tbody, .table-fixed tr, .table-fixed td, .table-fixed th {
                display: block;
            }
            .table-fixed tbody td, .table-fixed thead > tr> th {
                float: left;
                border-bottom-width: 0;
            }
        </style>
        <div id="container_level_<?= $t; ?>">
            <section class="content-header">
                <h1>
                    Rusak
                </h1>
            </section>

            <script>

            </script>
            <div class="clearfix"></div>
            <section class="content">

                <table id="container_buku_rusak_<?= $t; ?>" class="table table-bordered table-striped "
                       style="margin-top: 20px;">
                    <thead>
                    <tr>
                        <th><b>ID</b></th>
                        <th><b>No. Buku</b></th>
                        <th><b>Level</b></th>
                        <th><b>Tanggal Masuk</b></th>
                        <th><b>Status</b></th>
                    </tr>
                    </thead>
                    <tbody id="content_load_buku_<?= $t; ?>">
                    <?
                    foreach ($arrStock as $val) {
                        $i++;
                        ?>
                        <tr>
                            <td id="no_<?= $t; ?>"><?= $i; ?></td>
                            <td id="no_buku_<?= $t; ?>"><?= $val->stock_buku_no; ?></td>
                            <td id="jenis_buku_level_<?= $t; ?>"><?= $arrJenisBarang[$val->stock_id_buku]; ?></td>
                            <td id="tanggal_masuk_<?= $t; ?>"><?
                                if ($myOrgType == KEY::$KPO) {
                                    echo $val->stock_buku_tgl_status_rusak;
                                } elseif ($myOrgType == KEY::$IBO) {
                                    echo $val->stock_buku_tgl_status_rusak;
                                } elseif ($myOrgType == KEY::$TC) {
                                    echo $val->stock_buku_tgl_status_rusak;
                                }
                                ?></td>
                            <td id="status_<?= $t; ?>"><?
                                if ($myOrgType == KEY::$KPO) {
                                    echo $arrStatusBuku[$val->stock_buku_status_kpo];
                                } elseif ($myOrgType == KEY::$IBO) {
                                    echo $arrStatusBuku[$val->stock_status_ibo];
                                } elseif ($myOrgType == KEY::$TC) {
                                    echo $arrStatusBuku[$val->stock_status_tc];
                                }
                                ?></td>
                        </tr>
                        <?
                    }
                    ?>
                    </tbody>
                </table>

            </section>

        </div>


        <?

    }

    public function read_retour_tc()
    {
        $myorgid = AccessRight::getMyOrgID();
        $myOrgType = AccessRight::getMyOrgType();
        $arrStatusRetour = Generic::getAllStatusRetour();
        $retour = new RetourBukuModel();

        if ($myOrgType == KEY::$KPO) {
//            $arrStock = $stockNo->getWhere("stock_buku_status_kpo = 99  AND stock_buku_kpo =$myorgid ORDER by stock_buku_id ASC");
        } elseif ($myOrgType == KEY::$IBO) {
//            $arrStock = $stockNo->getWhere("stock_status_ibo = 99  AND stock_buku_ibo =$myorgid ORDER by stock_buku_id ASC");
        } elseif ($myOrgType == KEY::$TC) {

//            $arrStock = $stockNo->getWhere("stock_status_tc = 99 AND stock_buku_tc =$myorgid ORDER by stock_buku_id ASC");
            $arrRetour = $retour->getWhere("retour_tc=$myorgid AND retour_tc != 0 AND retour_murid = 0  ORDER by retour_tgl_keluar_tc DESC");
        }
        $i = 0;
        $t = time();
        ?>
        <div id="container_level_<?= $t; ?>">
            <section class="content-header">
                <h1>
                    Laporan Retour TC
                </h1>
            </section>

            <script>

            </script>
            <div class="clearfix"></div>
            <section class="content">

                <table id="container_buku_rusak_<?= $t; ?>" class="table table-bordered table-striped"
                       style="margin-top: 20px;">
                    <thead>
                    <tr>
                        <th><b>ID</b></th>
                        <th><b>No. Retour</b></th>
                        <th><b>No. Buku Rusak</b></th>
                        <th><b>No. Buku Pengganti</b></th>
                        <th><b>Nama Buku</b></th>
                        <th><b>Tanggal Claim</b></th>
                        <th><b>Tanggal Claimed</b></th>
                        <th><b>Penanggung Jawab/TC</b></th>
                        <th><b>Penanggung Jawab/IBO</b></th>
                        <th><b>Status</b></th>
                    </tr>
                    </thead>
                    <tbody id="content_load_buku_<?= $t; ?>">
                    <?
                    foreach ($arrRetour as $val) {
                        $i++;
                        ?>
                        <tr>
                            <td id="no_<?= $t; ?>"><?= $i; ?></td>
                            <td id="no_retour_<?= $t; ?>"><?= $val->retour_no; ?></td>
                            <td id="no_buku_<?= $t; ?>"><?= $val->retour_buku_no; ?></td>
                            <td id="no_buku_<?= $t; ?>"><?= $val->retour_buku_no_pengganti_tc; ?></td>
                            <td id="nama_buku_<?= $t; ?>"><?
                                $stokNoBuku = new StockBuku();
                                echo $stokNoBuku->getNamaBukuByNoBuku($val->retour_buku_no); ?></td>
                            <td id="tgl_masuk_claim_<?= $t; ?>"><?
                                if ($myOrgType == KEY::$KPO) {
                                    echo $val->retour_tgl_keluar_kpo;
                                } elseif ($myOrgType == KEY::$IBO) {
                                    echo $val->retour_tgl_keluar_ibo;
                                } elseif ($myOrgType == KEY::$TC) {
                                    echo $val->retour_tgl_keluar_tc;
                                }
                                ?></td>
                            <td id="tgl_masuk_claim_<?= $t; ?>"><?
                                if ($val->retour_tgl_keluar_ibo != KEY::$TGL_KOSONG)
                                    echo $val->retour_tgl_keluar_ibo;
                                ?></td>
                            <td id="responsibility_<?= $t; ?>"><?= $val->retour_respon_tc; ?></td>
                            <td id="responsibility_<?= $t; ?>"><?= $val->retour_respon_ibo; ?></td>
                            <td id="status_retour_<?= $t; ?>"><?
                                if ($val->retour_jenis == KEY::$BUKU_AVAILABLE_ALIAS) {
                                    if ($val->retour_buku_no_pengganti_tc != "") {
                                        echo KEY::$RETOUR_STATUS_CLAIMED_TEXT;
                                    } else {
                                        echo KEY::$RETOUR_STATUS_CLAIM_TEXT;
                                    }


                                } else {
                                    ?>
                                    <button class="btn btn-default" id="claim_tc_<?= $val->retour_id . $t; ?>">Claim
                                    </button>
                                    <?
                                }

                                ?></td>
                        </tr>
                        <?
                    }
                    ?>
                    </tbody>
                </table>

            </section>

        </div>


        <?

    }

    public function read_retour_ibo()
    {
        $myorgid = AccessRight::getMyOrgID();
        $myOrgType = AccessRight::getMyOrgType();
        $arrStatusRetour = Generic::getAllStatusRetour();
        $retour = new RetourBukuModel();
        $arrRetour = $retour->getWhere("retour_ibo=$myorgid  AND retour_tc != 0  ORDER by retour_tgl_keluar_tc DESC");

        $i = 0;
        $t = time();

        ?>
        <div id="container_level_<?= $t; ?>">
            <section class="content-header">
                <h1>Laporan Retour
                </h1>
            </section>

            <script>

            </script>
            <div class="clearfix"></div>
            <section class="content">

                <table id="container_buku_rusak_<?= $t; ?>" class="table table-bordered table-striped"
                       style="margin-top: 20px;">
                    <thead>
                    <tr>
                        <th><b>ID</b></th>
                        <th><b>No. Retour</b></th>
                        <th><b>No. Buku Rusak</b></th>
                        <th><b>No. Buku Pengganti utk TC</b></th>
                        <th><b>No. Buku Pengganti dari KPO</b></th>
                        <th><b>Nama Buku</b></th>
                        <th><b>Tanggal Claim dari TC</b></th>
                        <th><b>Tanggal Buku dikirim ke TC</b></th>
                        <th><b>TC</b></th>
                        <th><b>Penanggung Jawab/TC</b></th>
                        <th><b>Penanggung Jawab/IBO</b></th>
                        <th><b>Status TC</b></th>
                        <th><b>Status IBO</b></th>
                    </tr>
                    </thead>
                    <tbody id="content_load_buku_<?= $t; ?>">
                    <?
                    foreach ($arrRetour as $val) {
                        $i++;

                        ?>
                        <tr>
                            <td id="no_<?= $t; ?>"><?= $i; ?></td>
                            <td id="no_retour_<?= $t; ?>"><?= $val->retour_no; ?></td>
                            <td id="no_buku_<?= $t; ?>"><?= $val->retour_buku_no; ?></td>
                            <td id="no_buku_tc_<?= $t; ?>"><?= $val->retour_buku_no_pengganti_tc; ?></td>
                            <td id="no_buku_pengganti_<?= $t; ?>"><?= $val->retour_buku_no_pengganti_ibo; ?></td>
                            <td id="nama_buku_<?= $t; ?>"><?
                                $stokNoBuku = new StockBuku();
                                echo $stokNoBuku->getNamaBukuByNoBuku($val->retour_buku_no); ?></td>
                            <td id="tgl_masuk_claim_<?= $t; ?>"><?
                                if ($myOrgType == KEY::$KPO) {
                                    echo $val->retour_tgl_keluar_kpo;
                                } elseif ($myOrgType == KEY::$IBO) {
                                    echo $val->retour_tgl_masuk_ibo;
                                } elseif ($myOrgType == KEY::$TC) {
                                    echo $val->retour_tgl_keluar_tc;
                                }
                                ?></td>
                            <td id="tgl_masuk_claim_atasan_<?= $val->retour_id . $t; ?>"><?
                                if ($myOrgType == KEY::$KPO) {
                                    echo $val->retour_tgl_keluar_kpo;
                                } elseif ($myOrgType == KEY::$IBO) {
                                    if ($val->retour_tgl_keluar_ibo != KEY::$TGL_KOSONG)
                                        echo $val->retour_tgl_keluar_ibo;
                                } elseif ($myOrgType == KEY::$TC) {
                                    echo $val->retour_tgl_masuk_ibo;
                                }
                                ?></td>
                            <td id="org_id_<?= $val->retour_id . $t; ?>"><?= Generic::getTCNamebyID($val->retour_tc); ?></td>
                            <td id="responsibility_<?= $val->retour_id . $t; ?>"><?= $val->retour_respon_tc; ?></td>
                            <td id="responsibility_<?= $val->retour_id . $t; ?>"><?= $val->retour_respon_ibo; ?></td>
                            <td id="status_retour_tc_<?= $val->retour_id . $t; ?>"><?
                                if ($val->retour_jenis == KEY::$BUKU_AVAILABLE_ALIAS) {
                                    if ($myOrgType == KEY::$IBO) {
                                        if ($val->retour_buku_no_pengganti_tc != "") {
                                            echo KEY::$RETOUR_STATUS_CLAIMED_TEXT;
                                        } else {
                                            ?>
                                            <button class="btn btn-default" id="claim_ibo_<?= $val->retour_id . $t; ?>">
                                                Claim
                                            </button>
                                            <?

                                        }
                                    }
                                } else {
                                    if ($val->retour_buku_no_pengganti_tc != "") {
                                        echo KEY::$RETOUR_STATUS_CLAIMED_TEXT;
                                    } else {
                                        ?>
                                        <button class="btn btn-default" id="claim_ibo_<?= $val->retour_id . $t; ?>">
                                            Claim
                                        </button>
                                        <?

                                    }
                                }

                                ?></td>


                            <td id="status_retour_ibo_<?= $val->retour_id . $t; ?>"><?
                                if ($val->retour_jenis == KEY::$BUKU_AVAILABLE_ALIAS) {
                                    if ($myOrgType == KEY::$IBO) {
                                        if ($val->retour_buku_no_pengganti_ibo != "") {
                                            echo KEY::$RETOUR_STATUS_CLAIMED_TEXT;
                                        } else {
                                            echo KEY::$RETOUR_STATUS_CLAIM_TEXT;
                                        }
                                    }
                                }

                                ?></td>
                            <script>
                                $('#claim_ibo_<?= $val->retour_id . $t; ?>').click(function (data) {
                                    var id_retour = '<?= $val->retour_id; ?>';
                                    var org_id_pengclaim = '<?= $val->retour_tc; ?>';
                                    var org_id_diclaim = '<?= $val->retour_ibo; ?>';
                                    $.get("<?= _SPPATH; ?>StockWebHelper/claim_tc_ibo?id_retour=" + id_retour + "&org_id_pengclaim=" + org_id_pengclaim + "&org_id_diclaim=" + org_id_diclaim, function (data) {
                                        console.log(data);
                                        if (data.status_code) {
                                            lwrefresh("read_retour_ibo");
                                        } else {
                                            alert(data.status_message);
                                        }
                                    }, 'json');
                                });
                            </script>
                        </tr>
                        <?
                    }
                    ?>
                    </tbody>
                </table>

            </section>

        </div>


        <?
    }

    public function read_retour_kpo()
    {
        $myorgid = AccessRight::getMyOrgID();
        $myOrgType = AccessRight::getMyOrgType();
        $arrStatusRetour = Generic::getAllStatusRetour();
        $retour = new RetourBukuModel();

        if ($myOrgType == KEY::$KPO) {

        }
        $arrRetour = $retour->getWhere("retour_kpo=$myorgid AND retour_status_kpo = 1  OR retour_status_ibo = 1 ORDER by retour_tgl_keluar_ibo DESC");
        $i = 0;
        $t = time();
        ?>
        <div id="container_level_<?= $t; ?>">
            <section class="content-header">
                <h1>Laporan Retour KPO
                </h1>
            </section>

            <script>

            </script>
            <div class="clearfix"></div>
            <section class="content">

                <table id="container_buku_rusak_<?= $t; ?>" class="table table-bordered table-striped"
                       style="margin-top: 20px;">
                    <thead>
                    <tr>
                        <th><b>ID</b></th>
                        <th><b>No. Retour</b></th>
                        <th><b>No. Buku</b></th>
                        <th><b>No. Buku pengganti utk IBO</b></th>
                        <th><b>Nama Buku</b></th>
                        <th><b>Tanggal Claim dari IBO</b></th>
                        <th><b>Tanggal Buku dikirim ke IBO</b></th>
                        <th><b>IBO</b></th>
                        <th><b>Penanggung Jawab/IBO</b></th>
                        <th><b>Penanggung Jawab/KPO</b></th>
                        <th><b>Status</b></th>
                    </tr>
                    </thead>
                    <tbody id="content_load_buku_<?= $t; ?>">
                    <?
                    foreach ($arrRetour as $val) {
                        $i++;
                        ?>
                        <tr>
                            <td id="no_<?= $t; ?>"><?= $i; ?></td>
                            <td id="no_retour_<?= $t; ?>"><?= $val->retour_no; ?></td>
                            <td id="no_buku_<?= $t; ?>"><?= $val->retour_buku_no; ?></td>
                            <td id="no_buku_pengganti<?= $t; ?>"><?= $val->retour_buku_no_pengganti_ibo; ?></td>
                            <td id="nama_buku_<?= $t; ?>"><?
                                $stokNoBuku = new StockBuku();
                                echo $stokNoBuku->getNamaBukuByNoBuku($val->retour_buku_no); ?></td>
                            <td id="tgl_masuk_claim_<?= $t; ?>"><?
                                if ($myOrgType == KEY::$KPO) {
                                    echo $val->retour_tgl_masuk_kpo;
                                } elseif ($myOrgType == KEY::$IBO) {
                                    echo $val->retour_tgl_masuk_ibo;
                                } elseif ($myOrgType == KEY::$TC) {
                                    echo $val->retour_tgl_keluar_tc;
                                }
                                ?></td>
                            <td id="tgl_masuk_claim_atasan_<?= $val->retour_id . $t; ?>"><?
                                if ($val->retour_tgl_masuk_ibo != KEY::$TGL_KOSONG)
                                    echo $val->retour_tgl_masuk_ibo;

                                ?></td>
                            <td id="org_id_<?= $val->retour_id . $t; ?>"><?= Generic::getTCNamebyID($val->retour_ibo); ?></td>
                            <td id="responsibility_<?= $val->retour_id . $t; ?>"><?= $val->retour_respon_ibo; ?></td>
                            <td id="responsibility_<?= $val->retour_id . $t; ?>"><?= $val->retour_respon_kpo; ?></td>
                            <td id="status_retour_ibo_<?= $val->retour_id . $t; ?>"><?
                                if ($val->retour_jenis == KEY::$BUKU_AVAILABLE_ALIAS) {
                                    if ($myOrgType == KEY::$KPO) {
                                        if ($val->retour_status_ibo == 1 && $val->retour_status_kpo == 0) {
                                            ?>
                                            <button class="btn btn-default" id="claim_kpo_<?= $val->retour_id . $t; ?>">
                                                Claim
                                            </button>
                                            <?
                                        } else {
                                            echo KEY::$RETOUR_STATUS_CLAIMED_TEXT;
                                        }
                                    }
                                } else {
                                    if ($val->retour_status_ibo == 1 && $val->retour_status_kpo == 0) {
                                        ?>
                                        <button class="btn btn-default" id="claim_kpo_<?= $val->retour_id . $t; ?>">
                                            Claim
                                        </button>
                                        <?
                                    } else {
                                        echo KEY::$RETOUR_STATUS_CLAIMED_TEXT;
                                    }
                                }

                                ?></td>
                            <script>
                                $('#claim_kpo_<?= $val->retour_id . $t; ?>').click(function (data) {
                                    var id_retour = '<?= $val->retour_id; ?>';
                                    var org_id_pengclaim = '<?= $val->retour_ibo; ?>';
                                    var org_id_diclaim = '<?= $val->retour_kpo; ?>';
                                    $.get("<?= _SPPATH; ?>StockWebHelper/claim_tc_ibo?id_retour=" + id_retour + "&org_id_pengclaim=" + org_id_pengclaim + "&org_id_diclaim=" + org_id_diclaim, function (data) {
                                        console.log(data);
                                        if (data.status_code) {
                                            lwrefresh("read_retour_kpo");
                                        } else {
                                            alert(data.status_message);
                                        }
                                    }, 'json');
                                });
                            </script>
                        </tr>
                        <?
                    }
                    ?>
                    </tbody>
                </table>

            </section>

        </div>


        <?
    }

    public function read_retour_ibo_dr_ibo()
    {
        $myorgid = AccessRight::getMyOrgID();
        $myOrgType = AccessRight::getMyOrgType();
        $arrStatusRetour = Generic::getAllStatusRetour();
        $retour = new RetourBukuModel();
        $arrRetour = $retour->getWhere("retour_ibo=$myorgid  AND retour_tc = 0  ORDER by retour_tgl_keluar_tc DESC");

        $i = 0;
        $t = time();

        ?>
        <div id="container_level_<?= $t; ?>">
            <section class="content-header">
                <h1>Laporan Retour
                </h1>
            </section>

            <script>

            </script>
            <div class="clearfix"></div>
            <section class="content">

                <table id="container_buku_rusak_<?= $t; ?>" class="table table-bordered table-striped"
                       style="margin-top: 20px;">
                    <thead>
                    <tr>
                        <th><b>ID</b></th>
                        <th><b>No. Retour</b></th>
                        <th><b>No. Buku</b></th>
                        <th><b>No. Buku Pengganti dari KPO</b></th>
                        <th><b>Nama Buku</b></th>
                        <th><b>Tanggal Claim dari IBO</b></th>
                        <th><b>Tanggal Buku dikirim ke IBO</b></th>
                        <th><b>Penanggung Jawab/IBO</b></th>
                        <th><b>Penanggung Jawab/KPO</b></th>
                        <th><b>Status IBO</b></th>
                    </tr>
                    </thead>
                    <tbody id="content_load_buku_<?= $t; ?>">
                    <?
                    foreach ($arrRetour as $val) {
                        $i++;

                        ?>
                        <tr>
                            <td id="no_<?= $t; ?>"><?= $i; ?></td>
                            <td id="no_retour_<?= $t; ?>"><?= $val->retour_no; ?></td>
                            <td id="no_buku_<?= $t; ?>"><?= $val->retour_buku_no; ?></td>
                            <td id="no_buku_pengganti_<?= $t; ?>"><?= $val->retour_buku_no_pengganti_ibo; ?></td>
                            <td id="nama_buku_<?= $t; ?>"><?
                                $stokNoBuku = new StockBuku();
                                echo $stokNoBuku->getNamaBukuByNoBuku($val->retour_buku_no); ?></td>
                            <td id="tgl_masuk_claim_<?= $t; ?>"><?
                                echo $val->retour_tgl_keluar_ibo;
                                ?></td>
                            <td id="tgl_masuk_claim_atasan_<?= $val->retour_id . $t; ?>"><?
                                if ($val->retour_tgl_masuk_ibo != KEY::$TGL_KOSONG)
                                    echo $val->retour_tgl_masuk_ibo;
                                ?></td>
                            <td id="responsibility_<?= $val->retour_id . $t; ?>"><?= $val->retour_respon_ibo; ?></td>
                            <td id="responsibility_<?= $val->retour_id . $t; ?>"><?= $val->retour_respon_kpo; ?></td>
                            <td id="status_retour_tc_<?= $val->retour_id . $t; ?>"><?
                                if ($val->retour_jenis == KEY::$BUKU_AVAILABLE_ALIAS) {
                                    if ($myOrgType == KEY::$IBO) {
                                        if ($val->retour_status_ibo == 1 && $val->retour_status_kpo == 0 && $val->retour_tc == 0) {
                                            echo KEY::$RETOUR_STATUS_CLAIM_TEXT;
                                        } else {
                                            echo KEY::$RETOUR_STATUS_CLAIMED_TEXT;
                                        }
                                    }
                                }

                                ?></td>

                            <script>
                                $('#claim_ibo_<?= $val->retour_id . $t; ?>').click(function (data) {
                                    var id_retour = '<?= $val->retour_id; ?>';
                                    var org_id_pengclaim = '<?= $val->retour_tc; ?>';
                                    var org_id_diclaim = '<?= $val->retour_ibo; ?>';
                                    $.get("<?= _SPPATH; ?>StockWebHelper/claim_tc_ibo?id_retour=" + id_retour + "&org_id_pengclaim=" + org_id_pengclaim + "&org_id_diclaim=" + org_id_diclaim, function (data) {
                                        console.log(data);
                                        if (data.status_code) {
                                            lwrefresh("read_retour_ibo");
                                        } else {
                                            alert(data.status_message);
                                        }
                                    }, 'json');
                                });
                            </script>
                        </tr>
                        <?
                    }
                    ?>
                    </tbody>
                </table>

            </section>

        </div>


        <?
    }

    public function read_retour_tc_murid()
    {
        $myorgid = AccessRight::getMyOrgID();
        $myOrgType = AccessRight::getMyOrgType();
        $arrStatusRetour = Generic::getAllStatusRetour();
        $retour = new RetourBukuModel();

        if ($myOrgType == KEY::$KPO) {
//            $arrStock = $stockNo->getWhere("stock_buku_status_kpo = 99  AND stock_buku_kpo =$myorgid ORDER by stock_buku_id ASC");
        } elseif ($myOrgType == KEY::$IBO) {
//            $arrStock = $stockNo->getWhere("stock_status_ibo = 99  AND stock_buku_ibo =$myorgid ORDER by stock_buku_id ASC");
        } elseif ($myOrgType == KEY::$TC) {

//            $arrStock = $stockNo->getWhere("stock_status_tc = 99 AND stock_buku_tc =$myorgid ORDER by stock_buku_id ASC");
            $arrRetour = $retour->getWhere("retour_tc=$myorgid AND retour_tc != 0 AND retour_murid!= 0 ORDER by retour_tgl_masuk_tc DESC");
        }
        $i = 0;
        $t = time();
        ?>
        <div id="container_level_<?= $t; ?>">
            <section class="content-header">
                <h1>
                    Retour Murid
                </h1>
            </section>

            <script>

            </script>
            <div class="clearfix"></div>
            <section class="content">

                <table id="container_buku_rusak_<?= $t; ?>" class="table table-bordered table-striped"
                       style="margin-top: 20px;">
                    <thead>
                    <tr>
                        <th><b>ID</b></th>
                        <th><b>No. Retour</b></th>
                        <th><b>No. Buku</b></th>
                        <th><b>No. Buku Pengganti utk Murid</b></th>
                        <th><b>No. Buku Pengganti dari IBO</b></th>
                        <th><b>Nama Buku</b></th>
                        <th><b>Tanggal Claim</b></th>
                        <th><b>Tanggal Claim</b></th>
                        <th><b>Nama Murid</b></th>
                        <th><b>Penanggung Jawab/TC</b></th>
                        <th><b>Penanggung Jawab/IBO</b></th>
                        <th><b>Status Murid</b></th>
                        <th><b>Status IBO</b></th>
                    </tr>
                    </thead>
                    <tbody id="content_load_buku_<?= $t; ?>">
                    <?
                    foreach ($arrRetour as $val) {
                        $i++;
                        ?>
                        <tr>
                            <td id="no_<?= $t; ?>"><?= $i; ?></td>
                            <td id="no_retour_<?= $t; ?>"><?= $val->retour_no; ?></td>
                            <td id="no_buku_<?= $t; ?>"><?= $val->retour_buku_no; ?></td>
                            <td id="no_buku_pengganti<?= $t; ?>"><?= $val->retour_buku_no_pengganti_murid; ?></td>
                            <td id="no_buku_pengganti_tc<?= $t; ?>"><?= $val->retour_buku_no_pengganti_tc; ?></td>
                            <td id="nama_buku_<?= $t; ?>"><?
                                $stokNoBuku = new StockBuku();
                                echo $stokNoBuku->getNamaBukuByNoBuku($val->retour_buku_no); ?></td>
                            <td id="tgl_masuk_claim_<?= $t; ?>"><?
                                if ($myOrgType == KEY::$KPO) {
                                    echo $val->retour_tgl_keluar_kpo;
                                } elseif ($myOrgType == KEY::$IBO) {
                                    echo $val->retour_tgl_keluar_ibo;
                                } elseif ($myOrgType == KEY::$TC) {
                                    echo $val->retour_tgl_keluar_murid;
                                }
                                ?></td>
                            <td id="tgl_masuk_claim_<?= $t; ?>"><?
                                if ($myOrgType == KEY::$KPO) {
                                    echo $val->retour_tgl_keluar_kpo;
                                } elseif ($myOrgType == KEY::$IBO) {
                                    echo $val->retour_tgl_keluar_ibo;
                                } elseif ($myOrgType == KEY::$TC) {
                                    echo $val->retour_tgl_keluar_tc;
                                }
                                ?></td>
                            <td id="responsibility_murid_<?= $t; ?>"><?= Generic::getMuridNamebyID($val->retour_murid); ?></td>
                            <td id="responsibility_tc_<?= $t; ?>"><?= $val->retour_respon_tc; ?></td>
                            <td id="responsibility_ibo_<?= $t; ?>"><?= $val->retour_respon_ibo; ?></td>
                            <td id="status_retour_<?= $t; ?>"><?
                                if ($val->retour_jenis == KEY::$BUKU_AVAILABLE_ALIAS) {
                                    if ($val->retour_buku_no_pengganti_tc != "") {
                                        echo KEY::$RETOUR_STATUS_CLAIMED_TEXT;
                                    } else {
                                        ?>
                                        <button class="btn btn-default" id="claim_tc_<?= $val->retour_id . $t; ?>">Claim
                                        </button>
                                        <?
                                    }
                                } else {
                                    if ($val->retour_buku_no_pengganti_murid != "") {
                                        echo KEY::$RETOUR_STATUS_CLAIMED_TEXT;
                                    } else {
                                        ?>
                                        <button class="btn btn-default" id="claim_tc_<?= $val->retour_id . $t; ?>">Claim
                                        </button>
                                        <?
                                    }


                                }

                                ?></td>

                            <td id="status_retour_ibo_<?= $t; ?>"><?
                                if ($val->retour_jenis == KEY::$BUKU_AVAILABLE_ALIAS) {
                                    if ($val->retour_buku_no_pengganti_tc != "") {
                                        echo KEY::$RETOUR_STATUS_CLAIMED_TEXT;
                                    } else {
                                        ?>
                                        <button class="btn btn-default" id="claim_tc_<?= $val->retour_id . $t; ?>">Claim
                                        </button>
                                        <?
                                    }
                                } else {
                                    if ($val->retour_buku_no_pengganti_tc != "") {
                                        echo KEY::$RETOUR_STATUS_CLAIMED_TEXT;
                                    } elseif ($val->retour_buku_no_pengganti_murid == "") {

                                    } else {
                                        echo KEY::$RETOUR_STATUS_CLAIM_TEXT;
                                    }


                                }

                                ?></td>
                            <script>
                                $("#claim_tc_<?= $val->retour_id . $t; ?>").click(function () {
                                    var id_retour = '<?= $val->retour_id; ?>';
                                    $.get("<?= _SPPATH; ?>StockWebHelper/claim_murid_tc?id_retour=" + id_retour, function (data) {
                                        console.log(data);
                                        if (data.status_code) {
                                            lwrefresh("read_retour_tc_murid");
                                        } else {
                                            alert(data.status_message);
                                        }
                                    }, 'json');
                                })
                            </script>
                        </tr>
                        <?
                    }
                    ?>
                    </tbody>
                </table>

            </section>

        </div>


        <?
    }

    public function read_buku_by_no_rusak_ibo()
    {
        $this->read_buku_by_no_rusak();
    }

    public function read_buku_by_no_rusak_kpo()
    {
        $this->read_buku_by_no_rusak();
    }


    public function create_no_buku(){
        $_GET['cmd'] = 'edit';
        $this->read_no_buku();
    }

    public function read_no_buku(){
        $obj = new StockBuku();
        $crud = new CrudCustom();
        $crud->ar_add = AccessRight::hasRight("create_no_buku");
        $crud->ar_edit = AccessRight::hasRight("update_no_buku");
        $crud->ar_delete = AccessRight::hasRight("delete_no_buku");
        $crud->run_custom($obj, "StokWeb", "read_no_buku");
    }

    public function delete_no_buku(){

    }

    public function update_no_buku(){

    }
}
