<?php

/**
 * Created by PhpStorm.
 * User: efindiongso
 * Date: 3/11/17
 * Time: 1:20 PM
 */
class CoretCoret extends WebService
{

    function potongBulan()
    {

//        SELECT * FROM sempoa__iuran_bulanan where bln_murid_id=6681 ORDER BY `bln_tahun`, `bln_mon` DESC
        $a = "03-2017";
        $ib = new IuranBulanan();
        $ib->getWhereOne("bln_murid_id=6681 ORDER BY bln_tahun DESC");
        if (is_null($ib->bln_id)) {
//            $bln = date("n");
//        $thn = date("Y");
            $thn = date("Y");
        } else {
            $thn = $ib->bln_tahun;
        }
        $ib->getWhereOne("bln_murid_id=6681 AND bln_tahun=$thn ORDER BY bln_mon DESC");
        pr($ib->bln_mon . " " . $ib->bln_tahun);

        $arr = explode("-", $a);
        pr($arr);
    }

    public function viewRole()
    {

        pr($_SESSION);
        $lvl = "ibo";
        $webclass = "UserWeb3";
        $obj = new SempoaRole();
        $id = AccessRight::getMyOrgID();
        $obj->read_filter_array = array("role_org_id" => AccessRight::getMyOrgID());
        $obj->hideColoums = array("role_org_id", "role_level");
        $obj->role_level = strtolower($lvl);
        $obj->cname = $webclass;
        $obj->fktname = "update_user_grup_" . $lvl;
        $obj->removeAutoCrudClick = array("role_edit_ar");

        pr($obj);
    }

    public function testLaporanWeb()
    {


        global $memcache;
        $mc = $memcache->memcache;
        $cacheAvailable = $memcache->cacheAvailable;
        echo "Memcached <br>";
        pr($cacheAvailable);
        if ($cacheAvailable) {
            echo "true";
        }

        echo "akhir <br>";
        $a = new LaporanWeb();
        $a->create_operasional_pembayaran_iuran_bulanan_tc();

    }

    public function stockBuku()
    {
//        $a = new StockBuku();
//        $no = $a->getMyLastNummer(28);
//        pr($no);

        $i = 0;
        $stk_masuk = 100;
        for ($i = 0; $i < $stk_masuk; $i++) {
            $stockBuku = new StockBuku();
            $no = $stockBuku->getMyLastNummer(28);
            $stockBuku->createNoBuku(28, $no, AccessRight::getMyOrgID());
            pr($no);
        }

    }

    public function setStatusPO()
    {
        $po_id = 544;
        $po_id = 547;
        $objPO = new POModel();
        global $db;
        $q = "SELECT * FROM {$objPO->table_name} po  WHERE   po.po_id= $po_id";
        $arrPO = $db->query($q, 2);
        $peminta = $arrPO[0]->po_pengirim;
        $objPOItem = new POItemModel();
        $arrPOItems = $objPOItem->getWhere("po_id='$po_id'");
        foreach ($arrPOItems as $val) {
            $res[$val->id_barang]['barang'] = $val->id_barang;
            $res[$val->id_barang]['qty'] = $val->qty;
            $res[$val->id_barang]['peminta'] = $peminta;
            $res[$val->id_barang]['pemilik'] = $val->org_id;
            $res[$val->id_barang]['po_id'] = $val->po_id;
        }
        pr($res);
        pr(AccessRight::getMyOrgType());
        foreach ($res as $val) {
            $anzahlBuku = self::getNoBuku($val['barang'], $val['qty'], $val['pemilik'], AccessRight::getMyOrgType());
            pr($anzahlBuku);
            if ($anzahlBuku >= $val['qty']) {
                echo "asas <br>";
                self::setNoBuku($val['barang'], $val['qty'], $val['pemilik'], $val['peminta'], AccessRight::getMyOrgType(), $val['po_id']);
            }

        }
//        pr($res);

    }

    public function getNoBuku($id_barang, $qty, $org_id_pemilik, $org_type)
    {

        $stockBuku = new StockBuku();
        if ($org_type == KEY::$KPO) {
            $arrStockBuku = $stockBuku->getWhere("stock_id_buku=$id_barang AND stock_buku_status_kpo=1 AND stock_buku_kpo = $org_id_pemilik ORDER BY stock_buku_id ASC LIMIT $qty");

            return count($arrStockBuku);
        } elseif ($org_type == KEY::$IBO) {
            $arrStockBuku = $stockBuku->getWhere("stock_id_buku=$id_barang AND stock_status_ibo=1 AND stock_buku_ibo = $org_id_pemilik ORDER BY stock_buku_id ASC LIMIT $qty");
            return count($arrStockBuku);
        } elseif ($org_type == KEY::$TC) {
            $arrStockBuku = $stockBuku->getWhere("stock_id_buku=$id_barang AND stock_status_tc=1 AND stock_buku_tc = $org_id_pemilik ORDER BY stock_buku_id ASC LIMIT $qty");
            return count($arrStockBuku);
        }

    }

    public function setNoBuku($id_barang, $qty, $org_id_pemilik, $org_id_peminta, $org_type, $po_id)
    {

        $stockBuku = new StockBuku();
        if ($org_type == KEY::$KPO) {
            $arrStockBuku = $stockBuku->getWhere("stock_id_buku=$id_barang AND stock_buku_status_kpo=1 AND stock_buku_kpo = $org_id_pemilik ORDER BY stock_buku_id ASC LIMIT $qty");
            foreach ($arrStockBuku as $val) {
                $val->stock_buku_status_kpo = 0;
                $val->stock_status_ibo = 1;
                $val->stock_buku_tgl_keluar_kpo = leap_mysqldate();
                $val->stock_buku_tgl_masuk_ibo = leap_mysqldate();
                $val->stock_buku_ibo = $org_id_peminta;
                $val->stock_po_pesanan_ibo = $po_id;
                $val->save(1);
            }
        } elseif ($org_type == KEY::$IBO) {

            $arrStockBuku = $stockBuku->getWhere("stock_id_buku=$id_barang AND stock_status_ibo=1 AND stock_buku_ibo = $org_id_pemilik ORDER BY stock_buku_id ASC LIMIT $qty");
            foreach ($arrStockBuku as $val) {
                $val->stock_status_ibo = 0;
                $val->stock_status_tc = 1;
                $val->stock_buku_tgl_keluar_ibo = leap_mysqldate();
                $val->stock_buku_tgl_masuk_tc = leap_mysqldate();
                $val->stock_buku_tc = $org_id_peminta;
                $val->stock_po_pesanan_tc = $po_id;
                $val->save(1);
            }


        } elseif ($org_type == KEY::$TC) {
            $arrStockBuku = $stockBuku->getWhere("stock_id_buku=$id_barang AND stock_status_tc=1 AND stock_buku_kpo = $org_id_pemilik ORDER BY stock_buku_id ASC LIMIT $qty");
            foreach ($arrStockBuku as $val) {
                $val->stock_buku_status_kpo = 0;
                $val->stock_buku_status_ibo = 1;
                $val->stock_buku_tgl_keluar_kpo = leap_mysqldate();
                $val->stock_buku_tgl_masuk_ibo = leap_mysqldate();
                $val->stock_buku_ibo = $org_id_peminta;
                $val->save(1);
            }
        }

    }


    public function getLastNomorBuku()
    {
        $no = "31799999";
        pr($no);
        $awalan = substr($no, 0, 3);
        pr($awalan);
        $help = substr($no, 3, strlen($no));
        pr($help);
        $c = ((int)$help);
        $c++;
        pr($c);
        if (strlen($c) == 1) {
            // 0 ada 4
            pr($awalan . "0000" . $c);
        } else if (strlen($c) == 2) {
            // 0 ada 3
            pr($awalan . "000" . $c);
        } else if (strlen($c) == 3) {
            // 0 ada 2
            pr($awalan . "00" . $c);
        } else if (strlen($c) == 4) {
            // 0 ada 1
            pr($awalan . "0" . $c);
        } else {
            pr($awalan . $c);
        }

        pr(strlen($c));
    }

    public function getLevelByBarangID()
    {


        $id_barang = $_GET['id'];

        $obj = new BarangWebModel();
        $obj->getWhereOne("id_barang_harga=$id_barang");

        if (!(is_null($obj->id_barang_harga))) {
            $kur = $obj->jenis_kurikulum;
            $arrKur = Generic::returnKurikulum();
            echo Generic::getLevelNameByID($obj->level) . " - " . $arrKur[$kur];
        }

//        public $jenis_kurikulum;
//        public $level;

    }

    public function getAllBuku()
    {
        $stockNoBuku = new StockBuku();
        $arrBuku = $stockNoBuku->getWhere("stock_buku_id >=1 GROUP BY stock_id_buku");
        $res = array();
        foreach ($arrBuku as $val) {
            $res[] = $val->stock_id_buku;
        }
        pr($res);
    }

    public function ambilidbarang()
    {
        $arrResLevel = Generic::getLevelKurikulumLama();


        pr($arrResLevel);
        die();

        $brg = new BarangWebModel();

        pr($brg->getNamaBukuByID(3));

        die();
        $next_level = Generic::getMyNextLevelLama(2);

        pr($next_level->id_level_lama);

        die();

        $weiter = true;
        pr($weiter);
        $weiter = $weiter & false;

        pr($weiter);

        die();


        $res = array();

        pr(count($res));

        die();
        // Check No Buku
        $setNoBuku = new StockBuku();
        $resStokBuku = $setNoBuku->setStatusBuku(2, 4, 55, 0);
        pr($resStokBuku);
    }

    public function getBkuNo()
    {

        $a = "21500002";
        $awalan = substr($a, 0, 3);
        pr($awalan);
        $stock = new StockBuku();
        pr($stock->getBukuNoByInvoiceID(2381));
    }

    public function loginidorg()
    {
//        unset($_SESSION);
        pr(Generic::getAllLevel());
        die();

//        pr(AccessRight::getMyOrgID() . " - " . AccessRight::getMyOrgType());
//        pr(AccessRight::getMyAR_All());
//        pr($_SESSION);
//        die();
//        Auth::logout();
        $userid = 104;
        $acc = new SempoaAccount();
        $acc->getByID($userid);
        $row = array();

        foreach ($acc as $key => $value) {
            $row[$key] = $value;
        }


        AuthSempoa::loginSempoaIBO($row);
        if (Auth::isLogged()) {
            pr($_SESSION);
            die();
            header("Location:" . _BPATH . "home?st=dashboard_tc");

        }

    }

    function murid_iuranBuku_load()
    {
        $page = isset($_GET['page']) ? addslashes($_GET['page']) : 1;
        $limit = KEY::$LIMIT_PROFILE;
        $begin = ($page - 1) * $limit;
        $id = addslashes($_GET['id']);
        $iuranBuku = new IuranBuku();
        $arrIuranBuku = $iuranBuku->getWhere("bln_murid_id='$id' ORDER by bln_date_pembayaran DESC LIMIT $begin,$limit");
        $jumlahTotal = $iuranBuku->getJumlah("bln_murid_id='$id'");
//        pr($arrIuranBuku);
        $jumlahHalamanTotal = ceil($jumlahTotal / $limit);
        $arrPembayaran = Generic::getJenisPembayaran();
        $arrSTatus = array("<b>Unpaid</b>", "Paid");
        $murid = new MuridModel();
        $murid->getByID($id);
        foreach ($arrIuranBuku as $key => $val) {
            ?>
            <tr>
            <td><?= $val->bln_date_pembayaran; ?></td>
            <td><?= Generic::getLevelNameByID($val->bln_buku_level); ?></td>
            <td><?
                if ($val->bln_status)
                    echo $arrSTatus[$val->bln_status];
                else {

                    if (AccessRight::getMyOrgType() == "tc") {
                        ?>
                        <button class="btn btn-default belumbayar_<?= $val->bln_id; ?>"
                                id='pay_now_bulanan_<?= $val->bln_id; ?>'>Pay Now
                        </button>

                        <?
                    } else {
                        echo "<b>Unpaid</b>";
                    }
                }
                ?></td>

            <td><?
                if ($val->bln_status)
                    echo $arrPembayaran[$val->bln_cara_bayar];
                else {
                    if (AccessRight::getMyOrgType() == "tc") {
                        ?>
                        <select id="jenis_pmbr_invoice_<?= $val->bln_id ?>">
                            <?
                            foreach ($arrPembayaran as $key => $by) {
                                ?>
                                <option value="<?= $key; ?>"><?= $by; ?></option>
                                <?
                            }
                            ?>
                        </select>
                        <?
                    }
                }
                ?></td>
            <td><?
                if ($val->bln_status)
                    echo $arrPembayaran[$val->bln_cara_bayar];
                else {
                    if (AccessRight::getMyOrgType() == "tc") {
                        ?>
                        <select id="jenis_pmbr_invoice_<?= $val->bln_id ?>">
                            <?
                            foreach ($arrPembayaran as $key => $by) {
                                ?>
                                <option value="<?= $key; ?>"><?= $by; ?></option>
                                <?
                            }
                            ?>
                        </select>
                        <?
                    }
                }
                ?></td>


            <td>
                <?
                if ($val->bln_status == 0) {
//                                                echo $mk->bln_kupon_id;
                } else {
                    if ($murid->id_level_masuk == $val->bln_buku_level) {
                        echo "<a target=\"_blank\" href=" . _SPPATH . "MuridWebHelper/printRegister?id_murid=" . $id . "><span  style=\"vertical-align:middle\" class=\"glyphicon glyphicon-print\"  aria-hidden=\"true\"></span>
                                            </a>";
                    } else {
                        ?>
                        <a target="_blank"
                           href="<?= _SPPATH; ?>MuridWebHelper/printBuku?nama=<?= Generic::getMuridNamebyID($id); ?>&id_murid=<?= $id; ?>&tgl=<?= $val->bln_date_pembayaran; ?>&level=<?= Generic::getLevelNameByID($val->bln_buku_level); ?>">

                            <span class="glyphicon glyphicon-print" aria-hidden="true"></span>
                        </a>
                        <?
                    }


                }
                ?>

            </td>
            <script>
                <?
                if ($val->bln_status) {
                ?>
                $('#belumbayar_<?= $val->bln_id; ?>').hide();
                <?
                } else {
                ?>
                $('#belumbayar_<?= $val->bln_id; ?>').show();
                <?
                }
                ?>
                $('#pay_now_bulanan_<?= $val->bln_id; ?>').click(function () {
                    var jpb = $('#jenis_pmbr_invoice_<?= $val->bln_id ?>').val();
                    var bln_id = <?= $val->bln_id; ?>;
                    $.post("<?= _SPPATH; ?>LaporanWebHelper/pay_iuran_buku_roy", {
                            bln_id: bln_id,
                            cara_pby: jpb
                        },
                        function (data) {
                            alert(data.status_message);
                            if (data.status_code) {
                                $('#belumbayar_<?= $val->bln_id; ?>').hide();
                                $('.sudahbayar').show();
                                $('#jenis_pmbr_invoice_<?= $val->bln_id ?>').attr("disabled", "true");
                                lwrefresh(selected_page);
                                // Refresh profile muridnya
                                lwrefresh("Profile_Murid");
                            } else {
                            }
                            console.log(data);
                            //                                                                $('#balikan_<? //= $val->bln_id;                                                                                                                          ?>//').html(data);
                        }, 'json');
                });
                //


            </script>

            <?
        }


    }


    public function getTC()
    {

        $noInvoice = "FP/2017/8/7";

        $a = substr($noInvoice, 0, 2);

        pr($a);
        die();


        $iuranBuku = new IuranBuku();
        $iuranBuku->getWhereOne("bln_no_invoice='$noInvoice'");
        $id_invoice = $iuranBuku->bln_id;

        pr($id_invoice);
        die();
        $stockBuku = new StockBuku();
        $arrStockBuku = $stockBuku->getWhere("stock_invoice_murid='$id_invoice'");
        pr($arrStockBuku);
        die();

        $invoice_id = 2416;
        $stockBuku = new StockBuku();
        $stockBuku->retourBukuMurid($invoice_id);


        die();
//        $iuranBulanan = new IuranBulanan();
//        $iuranBulanan->getWhereOne("bln_no_invoice='$noInvoice'");
//        $id_invoice = $iuranBulanan->bln_id;
//        $stockBuku = new StockBuku();
//        $arrStockBuku = $stockBuku->getWhere("stock_invoice_murid='$id_invoice'");
//        foreach($arrStockBuku as $buku){
//            $stockBuku = new StockBuku();
//            $stockBuku->retourBukuMurid($id_invoice);
//        }
//

        $id_buku = "3,4";
        $arrIDBuku = explode(",", $id_buku);
        pr($arrIDBuku);
        foreach ($arrIDBuku as $val) {
            $stockBarangBuku = new StockModel();
            $stockBarangBuku->getWhereOne("id_barang = '$val' AND org_id='4'");
            $stockBarangBuku->jumlah_stock = $stockBarangBuku->jumlah_stock - 1;
//            $stockBarangBuku->save();
            $setNoBuku = new StockBuku();
            $resBuNo = $setNoBuku->getBukuYgdReservMurid(2, 4, 13029, 0);
            pr($resBuNo);
//            $setNoBuku->setStatusBuku($resBuNo, $murid_id);
        }
        die();
        pr(_PHOTOPATH);
        pr(_SPPATH . _PHOTOURL);
        $acc = new SempoaAccount();
        $acc->getWhereOne("admin_id='104'");
        pr($acc->admin_username);

        die();
        return $acc->admin_username;


        $obj = new SempoaOrg();

//        pr($IBOid);
        $arr = $obj->getWhere("org_type='tc' AND org_parent_id='3' ORDER BY nama ASC");
//        pr($arr);
        if (count($arr) > 0) {
            foreach ($arr as $val) {
                $arrTC[$val->org_id] = $val->nama;
            }
        }
        pr($arrTC);

        die();
        $arrSortTC = $arrTC;
        sort($arrSortTC);
        $arrNewSort = array();
        foreach ($arrSortTC as $val) {
            $arrNewSort[array_search($val, $arrTC)] = $val;
//            pr($val);
        }
        pr($arrNewSort);
        pr($arrTC);
    }

    public function prompt()
    {
        pr(Generic::getAllStatusBuku());

        die();

        ?>
        <button id="btn_ha">Click</button>
        <script>
            $("#btn_ha").click(function () {
                alert("saas");
            });
        </script>
        <?
    }

    public function jenisBiaya()
    {

        $a = new RetourBukuModel();
        $a->printColumlistAsAttributes();
        die();
        $buku = new StockBuku();
        $buku->getBukuYgdReservMurid(7, 5, 831, 1, KEY::$JENIS_BUKU);
        pr($buku);


        die();
        $jumlahBukuIst = Generic::getIdBarangByLevel(1, 0);

        pr($jumlahBukuIst);
        die();
        $no_mulai = 010;


        $stk_masuk = 10;
        for ($i = 0; $i < $stk_masuk; $i++) {
            $stockBuku = new StockBuku();
//                if (strlen($no_buku_mulai) == 1) {
//                    // 0 ada 4
//                    $noKuponAwal = $tigaDigitNobuku . "0000" . $no_buku_mulai;
//                } else if (strlen($no_buku_mulai) == 2) {
//                    // 0 ada 3
//                    $noKuponAwal  = $tigaDigitNobuku . "000" . $no_buku_mulai;
//                } else if (strlen($no_buku_mulai) == 3) {
//                    // 0 ada 2
//                    $noKuponAwal  = $tigaDigitNobuku . "00" . $no_buku_mulai;
//                } else if (strlen($no_buku_mulai) == 4) {
//                    // 0 ada 1
//                    $noKuponAwal  = $tigaDigitNobuku . "0" . $no_buku_mulai;
//                } else {
//                    $noKuponAwal  = $tigaDigitNobuku . $no_buku_mulai;
//                }

//            $stockBuku->createNoBuku($id_barang, $noKuponAwal, AccessRight::getMyOrgID(), $name_barang);
//            $noKuponAwal++;
        }


        die();
        $barang = new BarangWebModel();

        pr($barang->getStockByIdJenisBarang(1));

        die();
        $arr = $barang->getWhere("1 GROUP BY  jenis_biaya");
        foreach ($arr as $val) {
            $res[] = $val->jenis_biaya;
        }
        pr($res);
    }

    function printRegis()
    {
        ?>

        <head>
            <title>Invoice Iuran Buku</title>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
            <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        </head>
        <body>


        <div class="Invoice_org_tua">
            <div class="kop_surat">
                <div class="container container-table">
                    <div class="row vertical-center-row">
                        <div class="text-center col-sm-6 col-sm-offset-3" style="">
                            <h4 id="data_tc">
                                TC Taman Semanan Indah<br>
                                Ruko Blok F No 7, Taman Semanan Indah Jakarta Barat<br>
                                Telp. 021-5444398 Fax. 021-5444397 HP 08159923311
                            </h4>
                        </div>
                    </div>
                </div>
                <div class="container">
                    <div class="row">
                        <div class="col-sm-4">
                            <b>No. Invoice :</b><br>
                            <b>Tanggal :</b><br><br>
                            Telah diterima pembayaran oleh Murid :<br>
                            <b>Nama Murid :</b><br>
                            <b>ID Murid :</b>
                        </div>
                    </div>

                    <br>
                </div>

            </div>

            <div class="container">
                <div class="table-responsive">
                    <table class="table table-bordered" id="table_invoice">
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>Keterangan</th>
                            <th>Harga</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>No Pendaftran</td>
                            <td>Biaya Registrasi</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Kode Kupon</td>
                            <td>Iuran Bulanan : Agustus 2017</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>No Buku</td>
                            <td>Uang Buku Junior 1</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Biaya Perlengkapan</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Jumlah</td>
                            <td></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="container">
                <div class="row">
                    <div class="col-sm-4">
                        Pembayaran melalui mesin EDC atau via transfer ke :
                    </div>
                </div>
            </div>

            <div class="container">
                <div class="vertical-center-row">
                    <div class="text-center col-sm-6 col-sm-offset-3" style="">
                        <h4 id="sempoasip_pusat">SEMPOA SIP<br>BCA cabang Supermal Karawaci<br>a/c. 1234567890</h4>
                    </div>
                </div>
            </div>

            <div class="container">
                <div class="vertical-center-row">
                    <div class="text-center col-md-6 col-md-offset-3" style="">
                        <h2>Terima Kasih</h2>
                    </div>
                </div>
            </div>


            <div class="container">
                <div class="vertical-center-row">
                    <div class="col-sm-3"></div>

                    <div class="col-sm-3">
                        <div class="row">
                            <div class="col-sm-12 text-right">.................., 31 Agustus 2017</div>
                        </div>
                    </div>
                </div>
            </div>

            <br>

            <div class="container">
                <div class="vertical-center-row">
                    <div class="col-md-8">
                        Catatan : Setiap Training Centre beroperasional dan memiliki kepemilikan secara mandiri
                    </div>

                    <div class="col-md-4 text-right">
                        Training Center
                    </div>
                </div>
            </div>

        </div>
        <hr>
        <!--        <div class="Invoice_tc">-->
        <!--            <div class="kop_surat">-->
        <!--                <img src="file:///Users/marselinuskristian/Documents/Sempoa/Picture1.png" alt="logo_sempoa"-->
        <!--                     class="img-responsive center-block"/>-->
        <!---->
        <!--                <div class="container container-table">-->
        <!--                    <div class="row vertical-center-row">-->
        <!--                        <div class="text-center col-md-6 col-md-offset-3" style="">-->
        <!--                            <h4 id="data_tc">-->
        <!--                                TC Taman Semanan Indah<br>-->
        <!--                                Ruko Blok F No 7, Taman Semanan Indah Jakarta Barat<br>-->
        <!--                                Telp. 021-5444398 Fax. 021-5444397 HP 08159923311-->
        <!--                            </h4>-->
        <!--                        </div>-->
        <!--                    </div>-->
        <!--                </div>-->
        <!--                <div class="container">-->
        <!--                    <div class="row">-->
        <!--                        <div class="col-md-4">-->
        <!--                            <b>No. Invoice :</b><br>-->
        <!--                            <b>Tanggal :</b><br><br>-->
        <!--                            Telah diterima pembayaran oleh Murid :<br>-->
        <!--                            <b>Nama Murid :</b><br>-->
        <!--                            <b>ID Murid :</b>-->
        <!--                        </div>-->
        <!--                    </div>-->
        <!---->
        <!--                    <br>-->
        <!--                </div>-->
        <!---->
        <!--            </div>-->
        <!---->
        <!--            <div class="container">-->
        <!--                <div class="table-responsive">-->
        <!--                    <table class="table table-bordered" id="table_invoice">-->
        <!--                        <thead>-->
        <!--                        <tr>-->
        <!--                            <th>No</th>-->
        <!--                            <th>Keterangan</th>-->
        <!--                            <th>Harga</th>-->
        <!--                        </tr>-->
        <!--                        </thead>-->
        <!--                        <tbody>-->
        <!--                        <tr>-->
        <!--                            <td>No Pendaftran</td>-->
        <!--                            <td>Biaya Registrasi</td>-->
        <!--                            <td></td>-->
        <!--                        </tr>-->
        <!--                        <tr>-->
        <!--                            <td>Kode Kupon</td>-->
        <!--                            <td>Iuran Bulanan : Agustus 2017</td>-->
        <!--                            <td></td>-->
        <!--                        </tr>-->
        <!--                        <tr>-->
        <!--                            <td>No Buku</td>-->
        <!--                            <td>Uang Buku Junior 1</td>-->
        <!--                            <td></td>-->
        <!--                        </tr>-->
        <!--                        <tr>-->
        <!--                            <td></td>-->
        <!--                            <td>Biaya Perlengkapan</td>-->
        <!--                            <td></td>-->
        <!--                        </tr>-->
        <!--                        <tr>-->
        <!--                            <td></td>-->
        <!--                            <td>Jumlah</td>-->
        <!--                            <td></td>-->
        <!--                        </tr>-->
        <!--                        </tbody>-->
        <!--                    </table>-->
        <!--                </div>-->
        <!--            </div>-->
        <!---->
        <!--            <div class="container">-->
        <!--                <div class="row">-->
        <!--                    <div class="col-md-4">-->
        <!--                        Pembayaran melalui mesin EDC atau via transfer ke :-->
        <!--                    </div>-->
        <!--                </div>-->
        <!--            </div>-->
        <!---->
        <!--            <div class="container">-->
        <!--                <div class="vertical-center-row">-->
        <!--                    <div class="text-center col-md-6 col-md-offset-3" style="">-->
        <!--                        <h4 id="sempoasip_pusat">SEMPOA SIP<br>BCA cabang Supermal Karawaci<br>a/c. 1234567890</h4>-->
        <!--                    </div>-->
        <!--                </div>-->
        <!--            </div>-->
        <!---->
        <!--            <div class="container">-->
        <!--                <div class="vertical-center-row">-->
        <!--                    <div class="text-center col-md-6 col-md-offset-3" style="">-->
        <!--                        <h2>Terima Kasih</h2>-->
        <!--                    </div>-->
        <!--                </div>-->
        <!--            </div>-->
        <!---->
        <!---->
        <!--            <div class="container">-->
        <!--                <div class="vertical-center-row">-->
        <!--                    <div class="col-md-3"></div>-->
        <!--                    <div class="col-md-6">-->
        <!--                        <div class="col-md-6"><img style="display:block; margin:auto; width: 100px; height: 100px "-->
        <!--                                                   src="file:///Users/marselinuskristian/Documents/Sempoa/Sempa%2020.png">-->
        <!--                        </div>-->
        <!--                        <div class="col-md-6"><span><img-->
        <!--                                    style="display:block; margin:auto; width: 100px; height: 100px "-->
        <!--                                    src="file:///Users/marselinuskristian/Documents/Sempoa/Sempi%2020.png"></span>-->
        <!--                        </div>-->
        <!--                    </div>-->
        <!--                    <div class="col-md-3">-->
        <!--                        <div class="row">-->
        <!--                            <div class="col-md-12 text-right">.................., 31 Agustus 2017</div>-->
        <!--                        </div>-->
        <!--                    </div>-->
        <!--                </div>-->
        <!--            </div>-->
        <!---->
        <!--            <br>-->
        <!---->
        <!--            <div class="container">-->
        <!--                <div class="vertical-center-row">-->
        <!--                    <div class="col-md-8">-->
        <!--                        Catatan : Setiap Training Centre beroperasional dan memiliki kepemilikan secara mandiri-->
        <!--                    </div>-->
        <!---->
        <!--                    <div class="col-md-4 text-right">-->
        <!--                        Training Center-->
        <!--                    </div>-->
        <!--                </div>-->
        <!--            </div>-->
        <!---->
        <!--        </div>-->
        </body>
        <?
    }


    public function createRetour()
    {

        $stokNoBuku = new StockBuku();
//        $level, $id_buku)
        $nobukubaru = $stokNoBuku->getNoBukuTerkecilByLevelYgAvail("kpo", 2, 10, 2);

        pr($nobukubaru);

        die();

        $a = new RetourBukuModel();
        pr($a->createRetourNo(5, "tc"));
        die();

        pr(Account::getMyName());

        die();
        $retour = new RetourBukuModel();


//
//////        $retour->retour_no
        $retour->retour_jenis = KEY::$BUKU_AVAILABLE_ALIAS;
        $succ = $retour->save();
        $retour->retour_status_ibo = 0;
        $retour->retour_buku_no = "12121";
        $retour->retour_tgl_keluar_tc = leap_mysqldate();
        $retour->retour_kpo = 2;
        $retour->retour_ibo = 3;
        $retour->retour_tc = 5;
        $retour->save();
    }

    function printregist2()
    {
        ?>
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="utf-8">
            <title>A5</title>

            <!-- Normalize or reset CSS with your favorite library -->
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/3.0.3/normalize.css">

            <!-- Load paper.css for happy printing -->
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.2.3/paper.css">

            <!-- Set page size here: A5, A4 or A3 -->
            <!-- Set also "landscape" if you need -->


            <style>


                @page {
                    size: A6;
                    margin: 0;
                }

                @media print {
                    html, body {
                        width: 105mm;
                        height: 148mm;
                    }
                }

                .invoice_orang_tua {
                    font-size: 12px;
                }

                #data_tc {
                    text-align: center;
                }

                div.info_invoices {
                    padding: 20px;
                    font-size: 20px;
                }

                div.nama_siswa {
                    padding: 20px;
                    font-size: 18px;
                }

                table {
                    font-family: arial, sans-serif;
                    border-collapse: collapse;
                    width: 100%;
                    margin-right: 20px;
                }

                td, th {
                    border: 1px solid #414141;
                    text-align: center;
                    padding: 8px;
                }

                th {
                    background-color: #dddddd;
                }

                #sempoasip_pusat {
                    text-align: center;
                }

                #logo_sempoa {
                    display: block;
                    margin: auto;
                }

                div.penutup_invoices {
                    text-align: center;
                    margin-left: 450px;
                    margin-right: 450px;
                }

            </style>
        </head>

        <!-- Set "A5", "A4" or "A3" for class name -->
        <!-- Set also "landscape" if you need -->
        <body class="A5">

        <!-- Each sheet element should have the class "sheet" -->
        <!-- "padding-**mm" is optional: you can set 10, 15, 20 or 25 -->
        <section class="sheet padding-10mm">

            <!-- Write HTML just like a web page -->
            <article>

                <div class="invoice_orang_tua">
                    <div class="kop_invoices">
                        <h5 id="data_tc">
                            TC Taman Semanan Indah<br>
                            Ruko Blok F No 7, Taman Semanan Indah - Jakarta Barat<br>
                            Telp. 021 - 5444398, Fax. 021 - 5444397, HP. 08159923311
                        </h5>
                        <div class="info_invoices">
                            <b>No. Invoice :</b><br>
                            <b>Tanggal :</b>
                        </div>
                        <div class="nama_siswa">
                            <p>
                                Telah diterima pembayaran oleh Murid :<br>
                                <b>Nama Murid :</b><br>
                                <b>ID Murid :</b>
                            </p>
                        </div>
                        <table>
                            <tr>
                                <th>No</th>
                                <th>Keterangan</th>
                                <th>Harga</th>
                            </tr>
                            <tr>
                                <td>No Pendaftaran</td>
                                <td>Biaya Registrasi</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Kode Kupon</td>
                                <td>Iuran Bulanan : Juli 2107</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>No.Buku</td>
                                <td>Uang Buku Junior 1</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>Biaya Perlengkapan Junior</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td style="text-align:right;padding-right:15px;font-style:bold;">Jumlah Total</td>
                                <td></td>
                            </tr>

                        </table>

<span>
	<p>Pembayaran melalui mesin EDC atau via transfer ke :</p>
	<h5 id="sempoasip_pusat">SEMPOA SIP<br>BCA cabang Supermal Karawaci<br>a/c. 1234567890</h5>

	<p style="float: right; margin-right: 20px;">....................., 11 Juli 2017</p>
</span>
                        <div>
                            <p style="float: left;margin-left: 20px;">Catatan : Setiap Training Centre beroperasional
                                dan memiliki kepemilikan secara mandiri</p>
                            <p style="float: right;margin-right: 20px;">Training Center</p>
                        </div>
                        <br><br><br>

                    </div>
                </div>

            </article>

        </section>

        </body>

        </html>
        <?
    }

    public function tablefix()
    {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <title>Invoice <?= Lang::t("Iuran Buku") ?></title>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
            <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        </head>
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

            .table-fixed tbody td, .table-fixed thead > tr > th {
                float: left;
                border-bottom-width: 0;
            }
        </style>
        <body>
        <div class="container">
            <div class="row">
                <div class="panel panel-default">

                    <table class="table table-fixed">
                        <thead>
                        <tr>
                            <th class="col-xs-2">#</th>
                            <th class="col-xs-8">Name</th>
                            <th class="col-xs-2">Points</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="col-xs-2">1</td>
                            <td class="col-xs-8">Mike Adams</td>
                            <td class="col-xs-2">23</td>
                        </tr>
                        <tr>
                            <td class="col-xs-2">2</td>
                            <td class="col-xs-8">Holly Galivan</td>
                            <td class="col-xs-2">44</td>
                        </tr>
                        <tr>
                            <td class="col-xs-2">3</td>
                            <td class="col-xs-8">Mary Shea</td>
                            <td class="col-xs-2">86</td>
                        </tr>
                        <tr>
                            <td class="col-xs-2">4</td>
                            <td class="col-xs-8">Jim Adams</td>
                            <td>23</td>
                        </tr>
                        <tr>
                            <td class="col-xs-2">5</td>
                            <td class="col-xs-8">Henry Galivan</td>
                            <td class="col-xs-2">44</td>
                        </tr>
                        <tr>
                            <td class="col-xs-2">6</td>
                            <td class="col-xs-8">Bob Shea</td>
                            <td class="col-xs-2">26</td>
                        </tr>
                        <tr>
                            <td class="col-xs-2">7</td>
                            <td class="col-xs-8">Andy Parks</td>
                            <td class="col-xs-2">56</td>
                        </tr>
                        <tr>
                            <td class="col-xs-2">8</td>
                            <td class="col-xs-8">Bob Skelly</td>
                            <td class="col-xs-2">96</td>
                        </tr>
                        <tr>
                            <td class="col-xs-2">9</td>
                            <td class="col-xs-8">William Defoe</td>
                            <td class="col-xs-2">13</td>
                        </tr>
                        <tr>
                            <td class="col-xs-2">10</td>
                            <td class="col-xs-8">Will Tripp</td>
                            <td class="col-xs-2">16</td>
                        </tr>
                        <tr>
                            <td class="col-xs-2">11</td>
                            <td class="col-xs-8">Bill Champion</td>
                            <td class="col-xs-2">44</td>
                        </tr>
                        <tr>
                            <td class="col-xs-2">12</td>
                            <td class="col-xs-8">Lastly Jane</td>
                            <td class="col-xs-2">6</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        </body>
        </html>
        <?
    }

    public function testNaikLevelKur()
    {


        $s = new StockBuku();
        $s->printColumlistAsAttributes();


        die();
        $level = 6;
        $level = 7;
        $level = 2;
//        $level = 8;
//        $level = 9;
//        $level = 6;
        $help = Generic::getMyNextLevelKurLamaSpezial($level);
        pr($help);
    }

    public function hitungUlangStockTC()
    {
        $bweiter = true;
        $kartuStock = new StockModel();
        $arrKaruStock = $kartuStock->getAll();
        foreach ($arrKaruStock as $val) {
            $kartuStock = new StockBuku();

            if ($val->org_id == 2) {
                $kartuStock->getWhereOne("stock_id_buku=$val->id_barang AND stock_buku_status_kpo=1 AND stock_buku_kpo=$val->org_id");
                if (!is_null($kartuStock->stock_buku_id)) {
                    $jumlah = $kartuStock->getJumlah("stock_id_buku=$val->id_barang AND stock_buku_status_kpo=1 AND stock_buku_kpo=$val->org_id");
                    $bweiter = true;
                    pr("KPO");
                } else {
                    $bweiter = false;
                }

            } elseif ($val->org_id == 3) {
                $kartuStock->getWhereOne("stock_id_buku=$val->id_barang AND stock_status_ibo=1 AND stock_buku_ibo=$val->org_id");
                if (!is_null($kartuStock->stock_buku_id)) {
                    $jumlah = $kartuStock->getJumlah("stock_id_buku=$val->id_barang AND stock_status_ibo=1 AND stock_buku_ibo=$val->org_id");
                    $bweiter = true;
                    pr("IBO");
                } else {
                    $bweiter = false;
                }

            } else {
                $kartuStock->getWhereOne("stock_id_buku=$val->id_barang AND stock_status_tc=1 AND stock_buku_tc=$val->org_id");
                if (!is_null($kartuStock->stock_buku_id)) {
                    $jumlah = $kartuStock->getJumlah("stock_id_buku=$val->id_barang AND stock_status_tc=1 AND stock_buku_tc=$val->org_id");
                    $bweiter = true;
                } else {
                    $bweiter = false;
                }
            }

            if ($bweiter) {
                $kartuStock = new StockModel();
                $kartuStock->getWhereOne("org_id=$val->org_id AND id_barang=$val->id_barang");
                if (!is_null($kartuStock->stock_id)) {
                    $kartuStock->jumlah_stock = $jumlah;
                    $kartuStock->save(1);
                    echo "save!";
                }
                pr($val->org_id . " - " . $val->id_barang);
                pr($jumlah);
            }

        }
    }

    public function hitungUlangStockTC2()
    {

        $kartuStock = new StockBuku();


        $kartuStock = new StockModel();
        $arrKaruStock = $kartuStock->getAll();
        foreach ($arrKaruStock as $val) {
            $kartuStock = new StockBuku();

            if ($val->org_id == 2) {
                $jumlah = $kartuStock->getJumlah("stock_id_buku=$val->id_barang AND stock_buku_status_kpo=1 AND stock_buku_kpo=$val->org_id");
                pr("KPO");
            } elseif ($val->org_id == 3) {
                $jumlah = $kartuStock->getJumlah("stock_id_buku=$val->id_barang AND stock_status_ibo=1 AND stock_buku_ibo=$val->org_id");
                pr("IBO");
            } else {
                $jumlah = $kartuStock->getJumlah("stock_id_buku=$val->id_barang AND stock_status_tc=1 AND stock_buku_tc=$val->org_id");

            }

            $kartuStock = new StockModel();
            $kartuStock->getWhereOne("org_id=$val->org_id AND id_barang=$val->id_barang");
            if (!is_null($kartuStock->stock_id)) {
                $kartuStock->jumlah_stock = $jumlah;
                $kartuStock->save(1);
                echo "save!";
            }
            pr($val->org_id . " - " . $val->id_barang);
            pr($jumlah);
        }
//        pr($arrKaruStock);
    }

    function setStatusPO_tmp()
    {

        $id_status = 1;
        $po_id = 1262;

        if ($id_status != "") {
            $objPO = new POModel();
            $objPO->getByID($po_id);
            $status_sebelum = $objPO->po_status;
            $objPO->po_status = $id_status;
            $update = $objPO->save(1);
            $json['update'] = $update;
            if ($update) {
                $objStock = new StockModel();
                $myOrg_id = AccessRight::getMyOrgID();
                $objPOItem = new POItemModel();
                $arrPOItems = $objPOItem->getWhere("po_id='$po_id'");
//                $json['po'] = $arrPOItems;

                if (count($arrPOItems > 0)) {
                    if ($id_status == 1) {

                        // Check Jumlah no Buku

                        $objPO_buku = new POModel();
                        global $db;
                        $q = "SELECT * FROM {$objPO->table_name} po  WHERE   po.po_id= $po_id";
                        $arrPO_buku = $db->query($q, 2);
                        $peminta = $arrPO_buku[0]->po_pengirim;
                        $objPOItem_buku = new POItemModel();
                        $arrPOItems_buku = $objPOItem_buku->getWhere("po_id='$po_id'");

                        $json['po_buku'] = $arrPOItems_buku;
                        foreach ($arrPOItems as $val) {
                            $res[$val->id_barang]['barang'] = $val->id_barang;
                            $res[$val->id_barang]['qty'] = $val->qty;
                            $res[$val->id_barang]['peminta'] = $peminta;
                            $res[$val->id_barang]['pemilik'] = $val->org_id;
                            $res[$val->id_barang]['po_id'] = $val->po_id;
                        }

                        foreach ($res as $val) {
                            $anzahlBuku = self::getNoBuku($val['barang'], $val['qty'], $val['pemilik'], AccessRight::getMyOrgType());
                            pr($anzahlBuku . " - " . $val['barang'] . " - " . $val['qty']);
//                            if ($anzahlBuku >= $val['qty']) {
//                                self::setNoBuku($val['barang'], $val['qty'], $val['pemilik'], $val['peminta'], AccessRight::getMyOrgType(), $val['po_id']);
//                            } else {
//
//                            }

                        }
                        die();

                        //update stock KPO
                        foreach ($arrPOItems as $val) {
                            $arrStock = $objStock->getWhere("org_id='$myOrg_id' AND id_barang='$val->id_barang' ");
                            $json['stock'] = $arrStock;
                            $json['count'] = count($arrStock);
                            if (count($arrStock) > 0) {
                                $json['count'] = count($arrStock);
                                if ($arrStock[0]->jumlah_stock_hold - $val->qty < 0) {
                                    $arrStock[0]->jumlah_stock_hold = 0;
                                } else {
                                    $arrStock[0]->jumlah_stock_hold = $arrStock[0]->jumlah_stock_hold - $val->qty;
                                }

                                $arrStock[0]->jumlah_stock = $arrStock[0]->jumlah_stock - $val->qty;
                                $arrStock[0]->save(1);
                            }
                        }

                        $objStockPengirim = new StockModel();
                        foreach ($arrPOItems as $val) {
                            $arrStockPengirim = $objStockPengirim->getWhere("org_id='$objPO->po_pengirim' AND id_barang='$val->id_barang' ");
                            if (count($arrStockPengirim) == 0) {
                                $objStockPengirim->org_id = $objPO->po_pengirim;
                                $objStockPengirim->jumlah_stock = $val->qty;
                                $objStockPengirim->id_barang = $val->id_barang;
                                $objStockPengirim->save();
                                $json['stock'] = ($objStockPengirim);
                            } else {
                                $arrStockPengirim[0]->jumlah_stock = $arrStockPengirim[0]->jumlah_stock + $val->qty;
                                $arrStockPengirim[0]->save(1);
                            }
                        }

                        // KartuStock KPO
                        $arrKartuStock = $objStock->getWhere("id_barang='$key' AND id_pemilik_barang = '$myOrg_id'");
                        if (count($arrKartuStock) == 0) {
                            // Error
                        } else {
//                            $arrKartuStock[0]->stock_keluar = $arrKartuStock[0]->stock_keluar + $val->qty;
                            $arrKartuStock[0]->tanggal_input = leap_mysqldate();
                            $arrKartuStock[0]->nama_pengeluar_barang = Account::getMyName();
                            $arrKartuStock[0]->id_pemilik_barang = Account::getMyID();
                            $arrKartuStock[0]->save(1);
                        }
                        // KartuStock IBO
                        $arrKartuStock = $objStock->getWhere("id_barang='$key' AND id_pemilik_barang = '$key->po_pengirim'");
                        if (count($arrKartuStock) == 0) {
                            $objStock->stock_masuk = $val->qty;
                            $objStock->tanggal_input = leap_mysqldate();
                            $objStock->nama_pengeluar_barang = Account::getMyName();
                            $objStock->id_pemilik_barang = Account::getMyID();
                            $objStock->save();
                        } else {
                            $objAccount = new Account();
                            $arrAccount = $objAccount->getByID("admin_org_id='$key->po_pengirim'");
                            $arrKartuStock[0]->stock_masuk = $arrKartuStock[0]->stock_masuk + $val->qty;
                            $arrKartuStock[0]->tanggal_input = leap_mysqldate();
                            $arrKartuStock[0]->nama_pengeluar_barang = Account::getMyName();
                            $arrKartuStock[0]->id_pemilik_barang = Account::getMyID();
                            $arrKartuStock[0]->save(1);
                        }


                        $arrJenisBarangHlp = Generic::getJenisBarangType();
                        $PO_Object = new POModel();
                        $PO_Object->getByID($po_id);
                        $peminta = "";
                        $pemilik = "";

                        foreach ($arrPOItems as $key => $val) {
                            $po_penerima = $PO_Object->po_penerima;
                            $po_pengirim = $PO_Object->po_pengirim;
                            $peminta = Generic::getTCNamebyID($PO_Object->po_pengirim);
                            $peminta = $peminta . " request " . Generic::getNamaBarangByIDBarang($val->id_barang) . " sebanyak: " . $val->qty;
                            $pemilik = Generic::getTCNamebyID($PO_Object->po_penerima);
                            $pemilik = $pemilik . " mengirimkan barang " . Generic::getNamaBarangByIDBarang($val->id_barang) . " sebanyak: " . $val->qty . " ke " . Generic::getTCNamebyID($PO_Object->po_pengirim);

                            $jenis_object = $arrJenisBarangHlp[$val->id_barang];
                            if (AccessRight::getMyOrgType() == KEY::$TC) {


                            } elseif (AccessRight::getMyOrgType() == KEY::$IBO) {
                                if ($jenis_object == KEY::$JENIS_BIAYA_BARANG) {
                                    Generic::createLaporanDebet($po_penerima, $po_pengirim, KEY::$DEBET_BARANG_IBO, $val->id_barang, $pemilik, $val->qty, 0, "");
                                    Generic::createLaporanKredit($po_pengirim, $po_pengirim, KEY::$KREDIT_BARANG_TC, $val->id_barang, $peminta, $val->qty, 0, "");
                                } elseif ($jenis_object == KEY::$JENIS_BIAYA_BUKU) {
                                    Generic::createLaporanDebet($po_penerima, $po_pengirim, KEY::$DEBET_BUKU_IBO, $val->id_barang, $pemilik, $val->qty, 0, "");
                                    Generic::createLaporanKredit($po_pengirim, $po_pengirim, KEY::$KREDIT_BUKU_TC, $val->id_barang, $peminta, $val->qty, 0, "");
                                } elseif ($jenis_object == KEY::$JENIS_BIAYA_PERLENGKAPAN) {
                                    Generic::createLaporanDebet($po_penerima, $po_pengirim, KEY::$DEBET_PERLENGKAPAN_IBO, $val->id_barang, $pemilik, $val->qty, 0, "");
                                    Generic::createLaporanKredit($po_pengirim, $po_pengirim, KEY::$KREDIT_PERLENGKAPAN_TC, $val->id_barang, $peminta, $val->qty, 0, "");
                                }
                            } elseif (AccessRight::getMyOrgType() == KEY::$KPO) {
                                if ($jenis_object == KEY::$JENIS_BIAYA_BARANG) {
                                    Generic::createLaporanDebet($po_penerima, $po_pengirim, KEY::$DEBET_BARANG_KPO, $val->id_barang, $pemilik, $val->qty, 0, "");
                                    Generic::createLaporanKredit($po_pengirim, $po_pengirim, KEY::$KREDIT_BARANG_IBO, $val->id_barang, $peminta, $val->qty, 0, "");
//                                    Generic::createLaporanDebet($po_penerima, $po_penerima, KEY::$DEBET_BARANG_KPO, $val->id_barang, $pemilik, $val->qty, 0, "");
//                                    Generic::createLaporanKredit($po_pengirim,$po_pengirim,  KEY::$KREDIT_BARANG_IBO, $val->id_barang, $peminta, $val->qty, 0, "");

                                } elseif ($jenis_object == KEY::$JENIS_BIAYA_BUKU) {
                                    Generic::createLaporanDebet($po_penerima, $po_pengirim, KEY::$DEBET_BUKU_KPO, $val->id_barang, $pemilik, $val->qty, 0, "");
                                    Generic::createLaporanKredit($po_pengirim, $po_pengirim, KEY::$KREDIT_BUKU_IBO, $val->id_barang, $peminta, $val->qty, 0, "");
//                                    Generic::createLaporanDebet($po_penerima, $po_penerima,KEY::$DEBET_BUKU_KPO, $val->id_barang, $pemilik, $val->qty, 0, "");
//                                    Generic::createLaporanKredit($po_pengirim,$po_pengirim, KEY::$KREDIT_BUKU_IBO, $val->id_barang, $peminta, $val->qty, 0, "");

                                } elseif ($jenis_object == KEY::$JENIS_BIAYA_PERLENGKAPAN) {
                                    Generic::createLaporanDebet($po_penerima, $po_pengirim, KEY::$DEBET_PERLENGKAPAN_KPO, $val->id_barang, $pemilik, $val->qty, 0, "");
                                    Generic::createLaporanKredit($po_pengirim, $po_pengirim, KEY::$KREDIT_PERLENGKAPAN_IBO, $val->id_barang, $peminta, $val->qty, 0, "");
//                                    Generic::createLaporanDebet($po_penerima, $po_penerima,KEY::$DEBET_BUKU_KPO, $val->id_barang, $pemilik, $val->qty, 0, "");
//                                    Generic::createLaporanKredit($po_pengirim,$po_pengirim, KEY::$KREDIT_BUKU_IBO, $val->id_barang, $peminta, $val->qty, 0, "");

                                }
                            }
                        }

                        // update stock IBO
                    } elseif ($id_status == 99) {
// update stock KPO
                        foreach ($arrPOItems as $val) {
                            $arrStock = $objStock->getWhere("org_id='$myOrg_id' AND id_barang='$val->id_barang' ");
                            if (count($arrStock) > 0) {
                                $arrStock[0]->jumlah_stock_hold = $arrStock[0]->jumlah_stock_hold - $val->qty;
                                $arrStock[0]->save(1);
                                SempoaInboxModel::sendMsg($val->org_id, AccessRight::getMyOrgID(), "Cancel pemesanan barang", "Pemesanan " . Generic::getNamaBarangByIDBarang($val->id_barang) . "barang anda di cancel!");
                            }
                        }
                    }
                    $arrPOItems[0]->status = $id_status;
                    $arrPOItems[0]->save(1);
                } else {

                    $objPO->po_status = $status_sebelum;
                    $update = $objPO->save(1);
                    $json['status_code'] = 0;
                    $json['status_message'] = "Status gagal di Update";
                    echo json_encode($json);
                    die();
                }
            }
        } else {
            $json['status_code'] = 0;
            $json['status_message'] = "Status gagal di Update";
            echo json_encode($json);
            die();
        }
        $json['id_status'] = $id_status;
        $json['status_code'] = 1;
        $json['status_message'] = "Status di Update";
        echo json_encode($json);
        die();
    }

    public function cekbuku()
    {
        $setNoBuku = new StockBuku();
//        getBukuYgdReservMurid($level, $org_id_pemilik, $id_murid, $kurikulum, $jenis_biaya)
        $resBuku = $setNoBuku->getBukuYgdReservMurid(7, 28, 4525, 1, KEY::$JENIS_BUKU);
//        $resBuku = $setNoBuku->getBukuYgdReservMurid($level_baru, $myOrg, $iuranBuku->bln_murid_id, $iuranBuku->bln_kur,KEY::$JENIS_BUKU);

        pr($resBuku);

    }

    public function hitungUlangStockKPO()
    {
        $bweiter = true;
        $kartuStock = new StockModel();
        $arrKaruStock = $kartuStock->getAll();
        foreach ($arrKaruStock as $val) {
            $kartuStockHelp = new StockBuku();

            if ($val->org_id == 2) {
                $kartuStockHelp->getWhereOne("stock_id_buku=$val->id_barang AND stock_buku_status_kpo=1 AND stock_buku_kpo=$val->org_id");
                if (!is_null($kartuStockHelp->stock_buku_id)) {
                    $jumlah = $kartuStockHelp->getJumlah("stock_id_buku=$val->id_barang AND stock_buku_status_kpo=1 AND stock_buku_kpo=$val->org_id");
                    $bweiter = true;
                    pr("KPO");
                } else {
                    $bweiter = false;
                }

                $kartuStock = new StockModel();
                $kartuStock->getWhereOne("org_id=$val->org_id AND id_barang=$val->id_barang");
                if ($bweiter) {

                    if (!is_null($kartuStock->stock_id)) {
                        $kartuStock->jumlah_stock = $jumlah;
                        $kartuStock->save(1);
                        echo "save!";
                    }
                    pr($val->org_id . " - " . $val->id_barang);
                } else {
                    if (!is_null($kartuStock->stock_id)) {
                        $kartuStock->jumlah_stock = 0;
                        $kartuStock->save(1);
                    }
                }
            }


        }
    }

    public function syncStokKPO()
    {
        $kartuStock = new StockModel();
        $arrKaruStock = $kartuStock->getWhere("org_id=2");
        foreach ($arrKaruStock as $val) {
            $kartuStockHelp = new StockBuku();
            $kartuStockHelp->getWhereOne("stock_id_buku=$val->id_barang AND stock_buku_status_kpo=1 AND stock_buku_kpo=$val->org_id");


        }
        //SELECT * FROM `sempoa__stock_buku` where `stock_buku_status_kpo` = 1 Group by `stock_id_buku`
    }

    public function coret()
    {
        $iuranBuku = new IuranBulanan();
        $iuranBuku->getWhereOne("bln_id='10000_10_2017'");
//        pr($iuranBuku);
        $obj = array();
        foreach ($iuranBuku as $key => $val) {
            $obj[$key] = $val;
        }
        pr(serialize($obj));

    }

    public function printObj()
    {

        $withDraw = new WithdrawModel();
        $withDraw->printColumlistAsAttributes();
        die();
        $date = new DateTime('today');
        $todayweek = $date->format("W");
        $hari = $date->format("w");

        pr($hari);
        $a = '^(^\+62\s?|^0)(\d{3,4}-?){2}\d{3,4}$';

        pr($a);
        $nr = "+6287880748880";
        if (preg_match($a, $nr)) {
            echo "true";
        } else {
            echo "false";
        }
//        pr($_SERVER);
//        pr($_SERVER['HTTP_CLIENT_IP']);
    }

    public function halBuku()
    {


//        $date = new DateTime('now');
//
//        pr($date->format("Y-m-d"));
//        $pro = new ProgressModel();
//        pr($pro->getProgressByDate("1111",1,$date->format("Y-m-d")));
//
//        die();
        $id_murid = 4364;

        $murid = new MuridModel();
        $murid->getByID($id_murid);
        $level_murid = $murid->id_level_sekarang;
        $kur = $murid->murid_kurikulum;


        // ambil jumlah buku
        $buku = new BarangWebModel();
        $buku->getWhereOne("level=$level_murid AND jenis_kurikulum=$kur AND jenis_biaya=1");
        $halBukuTotal = $buku->halaman_buku;
        $halBuku = \GuzzleHttp\json_decode($halBukuTotal);

        pr($halBuku);

        foreach ($halBuku as $val) {
            $progress = new ProgressModel();
            $i = 1;
            foreach($val as $jenisBuku => $hal){
                $b = "progress_total_hal_" . $i;
                $c = "progress_nama_buku_". $i;
                $progress->$c = $jenisBuku;
                $progress->$b = $hal;
                $i++;
            }
            $progress->save();
        }
//        pr($b);
//        die();
        $a = new ProgressModel();
        $a->printColumlistAsAttributes();
        die();
        $a = "19121121k";
        echo $a;
        if (is_numeric($a)) {
            echo "ja";
        } else {
            echo "nein";
        }
//        pr(Generic::checkNumeric($a));
//
//        pr(Efiwebsetting::getData("lama_undo"));
////        pr(SempoaWebSetting::getData("lama_undo"));


//        pr(Generic::generateRandomString(5));
//        $arrBuku[1]['A'] = 52;
//        $arrBuku[1]['B'] = 42;
////        $arrBuku[1]['C'] = 58;?
//        pr($arrBuku);
//        $j = json_encode($arrBuku);
//
//        $b = \GuzzleHttp\json_decode($j);
//        pr($j);
//        pr($b);
////        pr(json_encode($arrBuku));
//        $a = serialize($arrBuku);
//        pr($a);
//        pr(unserialize($a));
    }





    function crawl_sklh_satuan(){


        $urlWeb = "http://akupintar.info/";
        $url = "http://www.akupintar.info/cari.php?jenjang=KB&mode=sekolah&GoSearch=Search&propinsi=Banten&kota=Tangerang+Selatan&nama=&alamat=";

        $html = file_get_html($url);

        $i = 0;
        foreach ($html->find(".center .search",0) as $div) {
            echo  $div->find("a", 1)->href . "<br>";
        }
//        foreach ($html->find("a",0) as $div) {
//
//            if($div->find("href", $i)->plaintext != ""){
//                echo  $div->find("href", $i)->plaintext;
//            }
//            $i++;
//        }

    }
}