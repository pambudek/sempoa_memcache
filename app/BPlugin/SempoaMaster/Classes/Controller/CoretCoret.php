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

            } elseif ($val->org_id == 6) {
                $kartuStock->getWhereOne("stock_id_buku=$val->id_barang AND stock_status_ibo=1 AND stock_buku_ibo=$val->org_id");
                if (!is_null($kartuStock->stock_buku_id)) {
                    $jumlah = $kartuStock->getJumlah("stock_id_buku=$val->id_barang AND stock_status_ibo=1 AND stock_buku_ibo=$val->org_id");
                    $bweiter = true;
                    pr("IBO");
                } else {
                    $bweiter = false;
                }

            } elseif ($val->org_id == 113) {
                $kartuStock->getWhereOne("stock_id_buku=$val->id_barang AND stock_status_ibo=1 AND stock_buku_ibo=$val->org_id");
                if (!is_null($kartuStock->stock_buku_id)) {
                    $jumlah = $kartuStock->getJumlah("stock_id_buku=$val->id_barang AND stock_status_ibo=1 AND stock_buku_ibo=$val->org_id");
                    $bweiter = true;
                    pr("IBO");
                } else {
                    $bweiter = false;
                }

            } elseif ($val->org_id == 116) {
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

        //1550

        $a = new BarangWebHelper();
        pr($a->getNoBuku(8, 5, 3, "ibo"));
        $a->setNoBuku(8, 5, 3, 3, "ibo", 1550);

//        public function setNoBuku($id_barang, $qty, $org_id_pemilik, $org_id_peminta, $org_type, $po_id)

        die();

//        public function getNoBuku($id_barang, $qty, $org_id_pemilik, $org_type)

        $withDraw = new LogWebServices();
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


        $soal = new SoalChallangeModel();
        $arrChallangeSoal = $soal->getSoalChallangeByLevel(1);
        foreach ($arrChallangeSoal as $val) {

        }
        $label = implode(".", $arrChallangeSoal);


        pr($label);
        die();
        $start_date = new DateTime('now');
        $end_date = new DateTime("2018-2-2 06:45:00");
        $interval = $start_date->diff($end_date);
        $hours = $interval->format('%h');
        $minutes = $interval->format('%i');
        pr($interval);
        pr($interval->invert);
        pr($interval->days);
        pr($start_date);
        echo 'Diff. in minutes is: ' . ($hours * 60 + $minutes);

//        pr("now: " . $now);
//        pr("your_date: " . $your_date);
        die();
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
            foreach ($val as $jenisBuku => $hal) {
                $b = "progress_total_hal_" . $i;
                $c = "progress_nama_buku_" . $i;
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

    public function demoUpload()
    {


        $datas = array();
        foreach ($_POST as $key => $value) {
            $datas[$key] = $value;
        }
        $json['status_code'] = count($datas) > 0 ? 1 : 0;
        $json['result']['data'] = $datas;
        echo json_encode($json);
        die();
    }

    public function uploadFoto()
    {
        $attr = $_POST['image'];

        $file = self::savePic($_POST[$attr]);

        $json = array();
        $json['status_code'] = $file == 0 ? 0 : 1;
        $json['result']['url'] = $file;
        echo json_encode($json);
        die();

    }

    public static function savePic($data)
    {

        if ($_GET['ios'] == "1") {
            $data = base64_decode(str_replace(" ", " + ", $data));
        } else
            $data = base64_decode($data);

        $im = imagecreatefromstring($data);
        if ($im !== false) {
            $ff = md5(mt_rand()) . '.png';
            $filename = _PHOTOPATH . $ff;

            //header('Content-Type: image/png');
            $succ = imagepng($im, $filename);
            //imagedestroy($im);
            if ($succ) {
                return $filename;
            } else {
                return 0;
            }
        }
        return 0;
    }


    function crawl_sklh_satuan()
    {


        $email = new Leapmail();
        $subject = "coba";
        $content = "ini cuma email";
        $to = "efindi.ongso@gmail.com";


        $email->sendEmail($to, $subject, $content);


        die();
        $urlWeb = "http://akupintar.info/";
        $url = "http://www.akupintar.info/cari.php?jenjang=KB&mode=sekolah&GoSearch=Search&propinsi=Banten&kota=Tangerang+Selatan&nama=&alamat=";

        $html = file_get_html($url);

        $i = 0;
        foreach ($html->find(".center .search", 0) as $div) {
            echo $div->find("a", 1)->href . "<br>";
        }
//        foreach ($html->find("a",0) as $div) {
//
//            if($div->find("href", $i)->plaintext != ""){
//                echo  $div->find("href", $i)->plaintext;
//            }
//            $i++;
//        }

    }

    public function cobaIdBuku()
    {

        $a = new DateTime('now');
        $b = $a->format("Y-m-d");
        pr($b);
        //2018-02-06
        $progress = new ProgressModel();
        pr($progress->getMuridProgressByDate("1406030001", 10, "2018-02-06"));

        die();
        $brg = new BarangWebModel();
        pr($brg->getIdBarangByLevelKurikulum(2, 1));
    }


    public function halBuku2()
    {

        $murid_id = 12909;
        $objMurid = new MuridModel();
        $objMurid->getByID($murid_id);
        $level_murid = $objMurid->id_level_sekarang;
        $objMurid->id_level_sekarang = Generic2::getMyPreviousLevel($objMurid->id_level_sekarang);
        $objMurid->save(1);


        $mj = new MuridJourney();
        $mj->getWhereOne("journey_murid_id='$murid_id' AND journey_level_mulai = '$level_murid' ORDER BY journey_id DESC");
        $mj->journey_level_end = $objMurid->$level_murid;
        $mj->journey_end_date = leap_mysqldate();
        pr($mj);
        $mj->save(1);

        die();
        $id_level = 3;
        pr(Generic2::getMyPreviousLevel($id_level));
        die();
        $id_murid = 4364;

        $murid = new MuridModel();
        $murid->getByID($id_murid);
        $level_murid = $murid->id_level_sekarang;
        $kur = $murid->murid_kurikulum;

        // ambil jumlah buku
        $buku = new BarangWebModel();
        $buku->getWhereOne("level=$level_murid AND jenis_kurikulum=$kur AND jenis_biaya=1");
        $halBukuTotal = $buku->halaman_buku;
        $halBuku = json_decode($halBukuTotal);

        pr($halBuku);

        foreach ($halBuku as $val) {
            $progress = new ProgressModel();
            $i = 1;
            foreach ($val as $jenisBuku => $hal) {
                $b = "progress_total_hal_" . $i;
                $c = "progress_nama_buku_" . $i;
                $progress->$c = $jenisBuku;
                $progress->$b = $hal;
                $i++;
            }
            $progress->save();
        }
//        pr($b);
        die();
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

    public function cobaDate()
    {
        ?>
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
        <head>
            <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.1/jquery.js"></script>
            <script type="text/javascript"
                    src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/jquery-ui.min.js"></script>
            <link rel="stylesheet" type="text/css" media="screen"
                  href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/base/jquery-ui.css">
            <script type="text/javascript">
                $(function () {
                    $('.date-picker').datepicker({
                        changeMonth: true,
                        changeYear: true,
                        showButtonPanel: true,
                        dateFormat: 'MM yy',
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
            <style>
                .ui-datepicker-calendar {
                    display: none;
                }
            </style>
        </head>
        <body>
        <label for="startDate">Date :</label>
        <input name="startDate" id="startDate" class="date-picker"/>
        </body>
        </html>
        <?
    }

    public function setDateby()
    {
        $varbulan = "08 2018";
        $a = new LogStatusMurid();
        $arr = $a->getCountSiswaByStatusOrgTypeDet(7, 2018, "C", "ibo", 3);
        return $arr;
        pr($arr);
        foreach ($arr as $val) {
            echo $val[count($val) - 1];
        }

    }

    public function getDate()
    {

        $murid = new MuridModel();
        $murid->getByID(12522);
        pr($murid->getParentEmail());
//        pr($murid->getParentName(12522));

        die();
        $a = new Leapmail2();
        $a->sendHTMLEmail("efindi.ongso@gmail.com", "Test", "", "sasaas");
        die();
        $a = date("Y") . "-" . date("n");
        $username = substr($a, strpos($a, '-') + 1);
        pr($username);
    }


//    function printSPP2($id_murid, $id_kupon)
    function printSPP2()
    {

        $id_murid = 12522;
        $id_kupon = 6000090198;


        $invoice_id = 13437;
        $level = "Junior 2";

        // bagus
//        Generic2::sendEmailToParent($id_murid, "", "", KEY::$TYPE_EMAIL_FP);
//        die();
//        Generic2::sendEmailToParent($id_murid, $id_kupon, $invoice_id, KEY::$TYPE_EMAIL_SPP);
//        die();
        Generic2::sendEmailToParent($id_murid, $id_kupon, $invoice_id, KEY::$TYPE_EMAIL_BUKU);
        die();
//die();
//        Generic2::sendEmailToParent($id_murid, "", "", KEY::$TYPE_EMAIL_FP);
//        pr(Generic2::printBuku2($id_murid, $invoice_id, $level));

        die();

        pr(Generic2::sendEmailToParent($id_murid, $id_kupon));
        die();
        $murid = new MuridModel();
        $murid->getByID($id_murid);

        $kuponSatuan = new KuponSatuan();
        $nama = $murid->getNameMurid();
        $kuponSatuan->getByID($id_kupon);
        $arrjenisBiayaSPP = Generic::getJenisBiayaType();
        $jenisBiayaSPP = $arrjenisBiayaSPP[$murid->id_level_sekarang];
        $jenisbm = new JenisBiayaModel();
        $jenisbm->getByID(AccessRight::getMyOrgID() . "_" . $jenisBiayaSPP);
        $iuranBulanan = new IuranBulanan();
        $iuranBulanan->getWhereOne("bln_kupon_id=$kuponSatuan->kupon_id");
        $tc = new SempoaOrg();
        $tc->getWhereOne("org_id=$murid->murid_tc_id");
        $date = new DateTime($iuranBulanan->bln_date_pembayaran);
        $tanggal = $date->format("d-m-Y");
        ?>
        <html>

        <head>
            <meta charset="utf-8">
            <style>


                #data_tc {
                    text-align: center;
                }

                div.info_invoices {
                    font-size: 12px;
                }

                div.nama_siswa {
                    font-size: 12px;
                }

                table {
                    font-family: arial, sans-serif;
                    border-collapse: collapse;
                    width: 100%;
                    font-size: 12px;
                }

                td, th {

                    border: 1px solid #414141;
                    text-align: center;
                    padding: 8px;
                    font-size: 12px;
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

                .container {
                    font-size: 12px;
                }
            </style>

        </head>

        <body>

        <section class="sheet padding-10mm">
            <!-- Write HTML just like a web page -->
            <article>

                <div class="container" style="margin-left: 20px; margin-right: 20px;">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="invoice_orang_tua">
                                <div class="kop_invoices">
                                    <h4 id="data_tc">
                                        <?= $tc->nama; ?><br>
                                        <?= $tc->alamat; ?><br>
                                        Telp. <?= $tc->nomor_telp; ?>, Fax. <?= $tc->tc_no_fax_office; ?>,
                                        HP. <?= $tc->tc_no_hp_office; ?>
                                    </h4>
                                    <div class="info_invoices">
                                        <b>No. Invoice : <?= $iuranBulanan->bln_no_invoice; ?></b> <br>
                                        <b>Tanggal : <?= $tanggal; ?></b>
                                    </div>
                                    <div class="nama_siswa">
                                        <p>
                                            Telah diterima pembayaran oleh Murid :<br>
                                            <b>Nama Murid : <?= $nama; ?></b><br>
                                            <b>No Murid : <?= $murid->kode_siswa; ?></b><br>
                                        </p>
                                    </div>
                                    <table style="border-right: 20px;">
                                        <tr>
                                            <th>No Kupon</th>
                                            <th>Keterangan</th>
                                            <th>Harga</th>
                                        </tr>
                                        <tr>
                                            <td><?= $iuranBulanan->bln_kupon_id; ?></td>
                                            <td>Iuran Bulanan : <?= $iuranBulanan->bln_date; ?></td>
                                            <td><?= idr($jenisbm->harga); ?></td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td style="text-align:right;padding-right:15px;font-style:bold;">Jumlah
                                                Total
                                            </td>
                                            <td><?= idr($jenisbm->harga); ?></td>
                                        </tr>

                                    </table>

                    <span>
	<p>Pembayaran melalui mesin EDC atau via transfer ke :</p>
	<h4 id="sempoasip_pusat">SEMPOA SIP<br><?= $tc->tc_nama_bank; ?> <?= $tc->tc_cabang_bank; ?>
        <br><?= $tc->tc_acc_bank; ?></h4>

</span>
                                    <div class="clearfix"></div>

                                    <div class="col-md-3" style="text-align: right;">
                                        ....................., <?= $tanggal; ?><!--</div>-->

                                    </div>
                                    <div class="clearfix"></div>
                                    <br><br>
                                    <div>
                                        <p style="border-right: 20px; float: left;">Catatan :
                                            Setiap Training
                                            Centre
                                            beroperasional dan
                                            memiliki kepemilikan secara mandiri</p>

                                    </div>
                                    <br><br><br>

                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </article>
        </section>
        </body>
        </html>
        <?

    }

    public function invoice()
    {
        $logo = "http://bo.sempoasip.com/" . _PHOTOURL . "/Picture1.png";
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
                <div class="container">
                    <div class="row">
                        <div class="col-md-12 ">
                            <img src="<?= $logo; ?>" alt="logo_sempoa" class="img-responsive pull-right"
                                 style="max-width:20%"/>
                        </div>
                    </div>
                </div>


                <div class="container container-table">
                    <div class="row vertical-center-row">
                        <div class="text-center col-md-6 col-md-offset-3" style="">
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
                        <div class="col-md-4">
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
                    <div class="col-md-4">
                        Pembayaran melalui mesin EDC atau via transfer ke :
                    </div>
                </div>
            </div>

            <div class="container">
                <div class="vertical-center-row">
                    <div class="text-center col-md-6 col-md-offset-3" style="">
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
        </body>
        <?
    }

    public function hapusSession()
    {
//

        pr(Efiwebsetting::getData("bln_undo"));
        die();

        echo "saasas";
        ?>
        <head>
            <style type="text/css">@charset "UTF-8";
                [ng\:cloak], [ng-cloak], [data-ng-cloak], [x-ng-cloak], .ng-cloak, .x-ng-cloak, .ng-hide:not(.ng-hide-animate) {
                    display: none !important;
                }

                ng\:form {
                    display: block;
                }

                .ng-animate-shim {
                    visibility: hidden;
                }

                .ng-anchor {
                    position: absolute;
                }</style>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
            <title ng-bind="title" class="ng-binding">Sempoa SIP - Program pelatihan otak untuk anak-anak</title>
            <!-- STYLES -->
            <link href="/Bundle/bundle.min.css?v=1.1" rel="stylesheet">
            <link href="/Bundle/guest.min.css?v=1.1" rel="stylesheet">

            <!-- SEO -->
            <meta name="title" content="Selamat Datang di Website Resmi Sempoa SIP">
            <meta name="description"
                  content="SEMPOA SIP adalah Metode Pelatihan Otak dengan menggunakan Sempoa yang ditujukan untuk anak-anak mulai dari usia 3 – 12 tahun.">
            <meta name="keywords" content="sempoa, sempoa sip, matematika, pendidikan">
            <meta property="og:url" content="http://www.sempoasip.com/">
            <meta property="og:type" content="business.business">
            <meta property="og:title" content="Selamat Datang di Website Resmi Sempoa SIP">
            <meta property="og:description"
                  content="SEMPOA SIP adalah Metode Pelatihan Otak dengan menggunakan Sempoa yang ditujukan untuk anak-anak mulai dari usia 3 – 12 tahun.">
            <meta property="og:image" content="http://sempoasip.com/guest/img/og-image.jpg">

            <!-- Global site tag (gtag.js) - Google Analytics -->
            <script type="text/javascript" async="" src="https://www.google-analytics.com/analytics.js"></script>
            <script async="" src="https://www.googletagmanager.com/gtag/js?id=UA-57474160-8"></script>
            <script>
                window.dataLayer = window.dataLayer || [];
                function gtag() {
                    dataLayer.push(arguments);
                }
                gtag('js', new Date());
                gtag('config', 'UA-57474160-8');
            </script>
        </head>

        <body class="navside">
        <table class="fullpage">
            <tbody><tr>
                <td valign="top" height="1"><!-- ngInclude: --><ng-include src="view" ng-controller="headerController" class="ng-scope" style=""><button type="button" class="navside-overlay ng-scope" navside=""></button>
                        <navside class="ng-scope">
                            <button type="button" id="btn_nav" class="lines-button x" navside="">
                                <span class="lines"></span>
                            </button>
                            <div class="overflow">
                                <a class="brand" href="/"><img src="/Guest/img/sempa-sempi.png" class="img-responsive center-block" width="150"></a>
                                <ul class="nav-btn list-unstyled">
                                    <li ng-class="{active: activeTab == '/'}" class="" style=""><a href="/">HOME</a></li>
                                    <li ng-class="{active: activeTab == '/tentang-kami'}" class="" style=""><a href="/tentang-kami">TENTANG KAMI</a></li>
                                    <li ng-class="{active: activeTab == '/events'}" class="" style=""><a href="/events">KABAR TERKINI</a></li>
                                    <li ng-class="{active: activeTab == '/gallery'}" class="active" style=""><a href="/gallery">GALERI</a></li>
                                    <li ng-class="{active: activeTab == '/centers'}"><a href="/centers">CENTERS</a></li>
                                    <li ng-class="{active: activeTab == '/contact'}"><a href="/contact">HUBUNGI KAMI</a></li>
                                </ul>
                                <br>
                                <ul class="btn-social list-inline text-center">
                                    <li><a href="https://www.facebook.com/sempoasip.id" class="btn btn-default" target="_blank"><i class="fa fa-facebook-f fa-fw"></i></a></li>
                                    <li><a href="https://www.instagram.com/sempoasip.id/" class="btn btn-default" target="_blank"><i class="fa fa-instagram fa-fw"></i></a></li>
                                    <li><a href="https://www.youtube.com/channel/UCDsQp_holHNOfRv2nzUQSNQ?view_as=subscriber" class="btn btn-default" target="_blank"><i class="fa fa-youtube fa-fw"></i></a></li>
                                </ul>
                            </div>
                        </navside>
                        <header id="header" class="ng-scope">
                            <div class="container">
                                <!-- ngInclude: --><ng-include src="'/guest/animation/index.html?v=' + constant.ver" class="ng-scope" style=""><style type="text/css" class="ng-scope">
                                        .gwd-img-14ir { position: absolute; left: 30px; transform-style: preserve-3d; -webkit-transform-style: preserve-3d; -moz-transform-style: preserve-3d; top: 178px; width: 32px; height: 43px; transform-origin: 16.133px 21.4386px 0px; -webkit-transform-origin: 16.133px 21.4386px 0px; -moz-transform-origin: 16.133px 21.4386px 0px; transform: translate3d(-22px, 87px, 0px) rotateZ(-50.1981deg); -webkit-transform: translate3d(-22px, 87px, 0px) rotateZ(-50.1981deg); -moz-transform: translate3d(-22px, 87px, 0px) rotateZ(-50.1981deg); } .gwd-img-1bko { position: absolute; transform-style: preserve-3d; -webkit-transform-style: preserve-3d; -moz-transform-style: preserve-3d; left: 165px; top: 240px; width: 35px; height: 48px; transform-origin: 17.4162px 24.1452px 0px; -webkit-transform-origin: 17.4162px 24.1452px 0px; -moz-transform-origin: 17.4162px 24.1452px 0px; transform: translate3d(-59px, 65px, 0px) rotateZ(16.6863deg); -webkit-transform: translate3d(-59px, 65px, 0px) rotateZ(16.6863deg); -moz-transform: translate3d(-59px, 65px, 0px) rotateZ(16.6863deg); } .gwd-img-1ibe { position: absolute; transform-style: preserve-3d; -webkit-transform-style: preserve-3d; -moz-transform-style: preserve-3d; width: 56px; left: 168px; height: 35px; top: 237px; transform: translate3d(-59px, 65px, 0px) rotateZ(-12.6591deg); -webkit-transform: translate3d(-59px, 65px, 0px) rotateZ(-12.6591deg); -moz-transform: translate3d(-59px, 65px, 0px) rotateZ(-12.6591deg); } .gwd-img-a103 { position: absolute; top: 175px; transform-style: preserve-3d; -webkit-transform-style: preserve-3d; -moz-transform-style: preserve-3d; left: 32px; width: 122px; height: 91px; transform-origin: 61.0641px 45.6533px 0px; -webkit-transform-origin: 61.0641px 45.6533px 0px; -moz-transform-origin: 61.0641px 45.6533px 0px; transform: translate3d(-16px, 76px, 0px) rotateZ(-12.6591deg); -webkit-transform: translate3d(-16px, 76px, 0px) rotateZ(-12.6591deg); -moz-transform: translate3d(-16px, 76px, 0px) rotateZ(-12.6591deg); } .gwd-img-1c6q { position: absolute; transform-style: preserve-3d; -webkit-transform-style: preserve-3d; -moz-transform-style: preserve-3d; left: 63px; top: 213px; width: 49px; height: 36px; transform-origin: 24.4048px 17.7543px 0px; -webkit-transform-origin: 24.4048px 17.7543px 0px; -moz-transform-origin: 24.4048px 17.7543px 0px; transform: translate3d(-23px, 66px, 0px) rotateZ(-12.6591deg); -webkit-transform: translate3d(-23px, 66px, 0px) rotateZ(-12.6591deg); -moz-transform: translate3d(-23px, 66px, 0px) rotateZ(-12.6591deg); } .gwd-img-h3zf { position: absolute; transform-style: preserve-3d; -webkit-transform-style: preserve-3d; -moz-transform-style: preserve-3d; left: 32px; top: 275px; width: 58px; height: 32px; transform-origin: 29.0612px 16.1519px 0px; -webkit-transform-origin: 29.0612px 16.1519px 0px; -moz-transform-origin: 29.0612px 16.1519px 0px; transform: translate3d(-5px, 52px, 0px) rotateZ(-12.6591deg); -webkit-transform: translate3d(-5px, 52px, 0px) rotateZ(-12.6591deg); -moz-transform: translate3d(-5px, 52px, 0px) rotateZ(-12.6591deg); } .gwd-img-1kkx { position: absolute; transform-style: preserve-3d; -webkit-transform-style: preserve-3d; -moz-transform-style: preserve-3d; left: 109px; top: 279px; width: 54px; height: 45px; transform-origin: 27.0444px 22.5912px 0px; -webkit-transform-origin: 27.0444px 22.5912px 0px; -moz-transform-origin: 27.0444px 22.5912px 0px; transform: translate3d(-44px, 44px, 0px) rotateZ(5.64294deg); -webkit-transform: translate3d(-44px, 44px, 0px) rotateZ(5.64294deg); -moz-transform: translate3d(-44px, 44px, 0px) rotateZ(5.64294deg); } .gwd-rect-10tm { position: absolute; width: 117.431px; height: 140.367px; box-sizing: border-box; border-width: 1px; border-style: solid; border-color: rgb(0, 0, 0); background-color: rgb(255, 255, 255); left: -383px; top: -170px; }  @keyframes gwd-gen-1gfxgwdanimation_gwd-keyframes { 0% { transform: translate3d(-16px, 76px, 0px) rotateZ(-12.6591deg); -webkit-transform: translate3d(-16px, 76px, 0px) rotateZ(-12.6591deg); -moz-transform: translate3d(-16px, 76px, 0px) rotateZ(-12.6591deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 25% { transform: translate3d(-16px, 56px, 0px) rotateZ(3.52379deg); -webkit-transform: translate3d(-16px, 56px, 0px) rotateZ(3.52379deg); -moz-transform: translate3d(-16px, 56px, 0px) rotateZ(3.52379deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 50% { transform: translate3d(-16px, 76px, 0px) rotateZ(-12.6591deg); -webkit-transform: translate3d(-16px, 76px, 0px) rotateZ(-12.6591deg); -moz-transform: translate3d(-16px, 76px, 0px) rotateZ(-12.6591deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 75% { transform: translate3d(-16px, 56px, 0px) rotateZ(3.52379deg); -webkit-transform: translate3d(-16px, 56px, 0px) rotateZ(3.52379deg); -moz-transform: translate3d(-16px, 56px, 0px) rotateZ(3.52379deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 100% { transform: translate3d(-16px, 76px, 0px) rotateZ(-12.6591deg); -webkit-transform: translate3d(-16px, 76px, 0px) rotateZ(-12.6591deg); -moz-transform: translate3d(-16px, 76px, 0px) rotateZ(-12.6591deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } }  @-webkit-keyframes gwd-gen-1gfxgwdanimation_gwd-keyframes { 0% { -webkit-transform: translate3d(-16px, 76px, 0px) rotateZ(-12.6591deg); -webkit-animation-timing-function: ease-in-out; } 25% { -webkit-transform: translate3d(-16px, 56px, 0px) rotateZ(3.52379deg); -webkit-animation-timing-function: ease-in-out; } 50% { -webkit-transform: translate3d(-16px, 76px, 0px) rotateZ(-12.6591deg); -webkit-animation-timing-function: ease-in-out; } 75% { -webkit-transform: translate3d(-16px, 56px, 0px) rotateZ(3.52379deg); -webkit-animation-timing-function: ease-in-out; } 100% { -webkit-transform: translate3d(-16px, 76px, 0px) rotateZ(-12.6591deg); -webkit-animation-timing-function: ease-in-out; } }  @-moz-keyframes gwd-gen-1gfxgwdanimation_gwd-keyframes { 0% { -moz-transform: translate3d(-16px, 76px, 0px) rotateZ(-12.6591deg); -moz-animation-timing-function: ease-in-out; } 25% { -moz-transform: translate3d(-16px, 56px, 0px) rotateZ(3.52379deg); -moz-animation-timing-function: ease-in-out; } 50% { -moz-transform: translate3d(-16px, 76px, 0px) rotateZ(-12.6591deg); -moz-animation-timing-function: ease-in-out; } 75% { -moz-transform: translate3d(-16px, 56px, 0px) rotateZ(3.52379deg); -moz-animation-timing-function: ease-in-out; } 100% { -moz-transform: translate3d(-16px, 76px, 0px) rotateZ(-12.6591deg); -moz-animation-timing-function: ease-in-out; } }  .htmlNoPages .gwd-gen-1gfxgwdanimation { animation: gwd-gen-1gfxgwdanimation_gwd-keyframes 4s linear 0s 1 normal forwards; -webkit-animation: gwd-gen-1gfxgwdanimation_gwd-keyframes 4s linear 0s 1 normal forwards; -moz-animation: gwd-gen-1gfxgwdanimation_gwd-keyframes 4s linear 0s 1 normal forwards; }  @keyframes gwd-gen-9o8ugwdanimation_gwd-keyframes { 0% { transform: translate3d(-59px, 65px, 0px) rotateZ(16.6863deg); -webkit-transform: translate3d(-59px, 65px, 0px) rotateZ(16.6863deg); -moz-transform: translate3d(-59px, 65px, 0px) rotateZ(16.6863deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 25% { transform: translate3d(-53px, 47px, 0px) rotateZ(-10.2542deg); -webkit-transform: translate3d(-53px, 47px, 0px) rotateZ(-10.2542deg); -moz-transform: translate3d(-53px, 47px, 0px) rotateZ(-10.2542deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 50% { transform: translate3d(-59px, 65px, 0px) rotateZ(16.6863deg); -webkit-transform: translate3d(-59px, 65px, 0px) rotateZ(16.6863deg); -moz-transform: translate3d(-59px, 65px, 0px) rotateZ(16.6863deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 75% { transform: translate3d(-53px, 47px, 0px) rotateZ(-10.2542deg); -webkit-transform: translate3d(-53px, 47px, 0px) rotateZ(-10.2542deg); -moz-transform: translate3d(-53px, 47px, 0px) rotateZ(-10.2542deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 100% { transform: translate3d(-59px, 65px, 0px) rotateZ(16.6863deg); -webkit-transform: translate3d(-59px, 65px, 0px) rotateZ(16.6863deg); -moz-transform: translate3d(-59px, 65px, 0px) rotateZ(16.6863deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } }  @-webkit-keyframes gwd-gen-9o8ugwdanimation_gwd-keyframes { 0% { -webkit-transform: translate3d(-59px, 65px, 0px) rotateZ(16.6863deg); -webkit-animation-timing-function: ease-in-out; } 25% { -webkit-transform: translate3d(-53px, 47px, 0px) rotateZ(-10.2542deg); -webkit-animation-timing-function: ease-in-out; } 50% { -webkit-transform: translate3d(-59px, 65px, 0px) rotateZ(16.6863deg); -webkit-animation-timing-function: ease-in-out; } 75% { -webkit-transform: translate3d(-53px, 47px, 0px) rotateZ(-10.2542deg); -webkit-animation-timing-function: ease-in-out; } 100% { -webkit-transform: translate3d(-59px, 65px, 0px) rotateZ(16.6863deg); -webkit-animation-timing-function: ease-in-out; } }  @-moz-keyframes gwd-gen-9o8ugwdanimation_gwd-keyframes { 0% { -moz-transform: translate3d(-59px, 65px, 0px) rotateZ(16.6863deg); -moz-animation-timing-function: ease-in-out; } 25% { -moz-transform: translate3d(-53px, 47px, 0px) rotateZ(-10.2542deg); -moz-animation-timing-function: ease-in-out; } 50% { -moz-transform: translate3d(-59px, 65px, 0px) rotateZ(16.6863deg); -moz-animation-timing-function: ease-in-out; } 75% { -moz-transform: translate3d(-53px, 47px, 0px) rotateZ(-10.2542deg); -moz-animation-timing-function: ease-in-out; } 100% { -moz-transform: translate3d(-59px, 65px, 0px) rotateZ(16.6863deg); -moz-animation-timing-function: ease-in-out; } }  .htmlNoPages .gwd-gen-9o8ugwdanimation { animation: gwd-gen-9o8ugwdanimation_gwd-keyframes 4s linear 0s 1 normal forwards; -webkit-animation: gwd-gen-9o8ugwdanimation_gwd-keyframes 4s linear 0s 1 normal forwards; -moz-animation: gwd-gen-9o8ugwdanimation_gwd-keyframes 4s linear 0s 1 normal forwards; }  @keyframes gwd-gen-1ba7gwdanimation_gwd-keyframes { 0% { transform: translate3d(-23px, 66px, 0px) rotateZ(-12.6591deg); -webkit-transform: translate3d(-23px, 66px, 0px) rotateZ(-12.6591deg); -moz-transform: translate3d(-23px, 66px, 0px) rotateZ(-12.6591deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 25% { transform: translate3d(-23px, 42px, 0px) rotateZ(4.29778deg); -webkit-transform: translate3d(-23px, 42px, 0px) rotateZ(4.29778deg); -moz-transform: translate3d(-23px, 42px, 0px) rotateZ(4.29778deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 50% { transform: translate3d(-23px, 66px, 0px) rotateZ(-12.6591deg); -webkit-transform: translate3d(-23px, 66px, 0px) rotateZ(-12.6591deg); -moz-transform: translate3d(-23px, 66px, 0px) rotateZ(-12.6591deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 75% { transform: translate3d(-23px, 42px, 0px) rotateZ(4.29778deg); -webkit-transform: translate3d(-23px, 42px, 0px) rotateZ(4.29778deg); -moz-transform: translate3d(-23px, 42px, 0px) rotateZ(4.29778deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 100% { transform: translate3d(-23px, 66px, 0px) rotateZ(-12.6591deg); -webkit-transform: translate3d(-23px, 66px, 0px) rotateZ(-12.6591deg); -moz-transform: translate3d(-23px, 66px, 0px) rotateZ(-12.6591deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } }  @-webkit-keyframes gwd-gen-1ba7gwdanimation_gwd-keyframes { 0% { -webkit-transform: translate3d(-23px, 66px, 0px) rotateZ(-12.6591deg); -webkit-animation-timing-function: ease-in-out; } 25% { -webkit-transform: translate3d(-23px, 42px, 0px) rotateZ(4.29778deg); -webkit-animation-timing-function: ease-in-out; } 50% { -webkit-transform: translate3d(-23px, 66px, 0px) rotateZ(-12.6591deg); -webkit-animation-timing-function: ease-in-out; } 75% { -webkit-transform: translate3d(-23px, 42px, 0px) rotateZ(4.29778deg); -webkit-animation-timing-function: ease-in-out; } 100% { -webkit-transform: translate3d(-23px, 66px, 0px) rotateZ(-12.6591deg); -webkit-animation-timing-function: ease-in-out; } }  @-moz-keyframes gwd-gen-1ba7gwdanimation_gwd-keyframes { 0% { -moz-transform: translate3d(-23px, 66px, 0px) rotateZ(-12.6591deg); -moz-animation-timing-function: ease-in-out; } 25% { -moz-transform: translate3d(-23px, 42px, 0px) rotateZ(4.29778deg); -moz-animation-timing-function: ease-in-out; } 50% { -moz-transform: translate3d(-23px, 66px, 0px) rotateZ(-12.6591deg); -moz-animation-timing-function: ease-in-out; } 75% { -moz-transform: translate3d(-23px, 42px, 0px) rotateZ(4.29778deg); -moz-animation-timing-function: ease-in-out; } 100% { -moz-transform: translate3d(-23px, 66px, 0px) rotateZ(-12.6591deg); -moz-animation-timing-function: ease-in-out; } }  .htmlNoPages .gwd-gen-1ba7gwdanimation { animation: gwd-gen-1ba7gwdanimation_gwd-keyframes 4s linear 0s 1 normal forwards; -webkit-animation: gwd-gen-1ba7gwdanimation_gwd-keyframes 4s linear 0s 1 normal forwards; -moz-animation: gwd-gen-1ba7gwdanimation_gwd-keyframes 4s linear 0s 1 normal forwards; }  @keyframes gwd-gen-bvtjgwdanimation_gwd-keyframes { 0% { transform: translate3d(-44px, 44px, 0px) rotateZ(5.64294deg); -webkit-transform: translate3d(-44px, 44px, 0px) rotateZ(5.64294deg); -moz-transform: translate3d(-44px, 44px, 0px) rotateZ(5.64294deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 25% { transform: translate3d(-40px, 24px, 0px) rotateZ(-21.6107deg); -webkit-transform: translate3d(-40px, 24px, 0px) rotateZ(-21.6107deg); -moz-transform: translate3d(-40px, 24px, 0px) rotateZ(-21.6107deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 50% { transform: translate3d(-44px, 44px, 0px) rotateZ(5.64294deg); -webkit-transform: translate3d(-44px, 44px, 0px) rotateZ(5.64294deg); -moz-transform: translate3d(-44px, 44px, 0px) rotateZ(5.64294deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 75% { transform: translate3d(-40px, 24px, 0px) rotateZ(-21.6107deg); -webkit-transform: translate3d(-40px, 24px, 0px) rotateZ(-21.6107deg); -moz-transform: translate3d(-40px, 24px, 0px) rotateZ(-21.6107deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 100% { transform: translate3d(-44px, 44px, 0px) rotateZ(5.64294deg); -webkit-transform: translate3d(-44px, 44px, 0px) rotateZ(5.64294deg); -moz-transform: translate3d(-44px, 44px, 0px) rotateZ(5.64294deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } }  @-webkit-keyframes gwd-gen-bvtjgwdanimation_gwd-keyframes { 0% { -webkit-transform: translate3d(-44px, 44px, 0px) rotateZ(5.64294deg); -webkit-animation-timing-function: ease-in-out; } 25% { -webkit-transform: translate3d(-40px, 24px, 0px) rotateZ(-21.6107deg); -webkit-animation-timing-function: ease-in-out; } 50% { -webkit-transform: translate3d(-44px, 44px, 0px) rotateZ(5.64294deg); -webkit-animation-timing-function: ease-in-out; } 75% { -webkit-transform: translate3d(-40px, 24px, 0px) rotateZ(-21.6107deg); -webkit-animation-timing-function: ease-in-out; } 100% { -webkit-transform: translate3d(-44px, 44px, 0px) rotateZ(5.64294deg); -webkit-animation-timing-function: ease-in-out; } }  @-moz-keyframes gwd-gen-bvtjgwdanimation_gwd-keyframes { 0% { -moz-transform: translate3d(-44px, 44px, 0px) rotateZ(5.64294deg); -moz-animation-timing-function: ease-in-out; } 25% { -moz-transform: translate3d(-40px, 24px, 0px) rotateZ(-21.6107deg); -moz-animation-timing-function: ease-in-out; } 50% { -moz-transform: translate3d(-44px, 44px, 0px) rotateZ(5.64294deg); -moz-animation-timing-function: ease-in-out; } 75% { -moz-transform: translate3d(-40px, 24px, 0px) rotateZ(-21.6107deg); -moz-animation-timing-function: ease-in-out; } 100% { -moz-transform: translate3d(-44px, 44px, 0px) rotateZ(5.64294deg); -moz-animation-timing-function: ease-in-out; } }  .htmlNoPages .gwd-gen-bvtjgwdanimation { animation: gwd-gen-bvtjgwdanimation_gwd-keyframes 4s linear 0s 1 normal forwards; -webkit-animation: gwd-gen-bvtjgwdanimation_gwd-keyframes 4s linear 0s 1 normal forwards; -moz-animation: gwd-gen-bvtjgwdanimation_gwd-keyframes 4s linear 0s 1 normal forwards; }  @keyframes gwd-gen-1envgwdanimation_gwd-keyframes { 0% { transform: translate3d(-5px, 52px, 0px) rotateZ(-12.6591deg); -webkit-transform: translate3d(-5px, 52px, 0px) rotateZ(-12.6591deg); -moz-transform: translate3d(-5px, 52px, 0px) rotateZ(-12.6591deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 25% { transform: translate3d(-4px, 28px, 0px) rotateZ(-53.4676deg); -webkit-transform: translate3d(-4px, 28px, 0px) rotateZ(-53.4676deg); -moz-transform: translate3d(-4px, 28px, 0px) rotateZ(-53.4676deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 50% { transform: translate3d(-5px, 52px, 0px) rotateZ(-12.6591deg); -webkit-transform: translate3d(-5px, 52px, 0px) rotateZ(-12.6591deg); -moz-transform: translate3d(-5px, 52px, 0px) rotateZ(-12.6591deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 75% { transform: translate3d(-4px, 28px, 0px) rotateZ(-53.4676deg); -webkit-transform: translate3d(-4px, 28px, 0px) rotateZ(-53.4676deg); -moz-transform: translate3d(-4px, 28px, 0px) rotateZ(-53.4676deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 100% { transform: translate3d(-5px, 52px, 0px) rotateZ(-12.6591deg); -webkit-transform: translate3d(-5px, 52px, 0px) rotateZ(-12.6591deg); -moz-transform: translate3d(-5px, 52px, 0px) rotateZ(-12.6591deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } }  @-webkit-keyframes gwd-gen-1envgwdanimation_gwd-keyframes { 0% { -webkit-transform: translate3d(-5px, 52px, 0px) rotateZ(-12.6591deg); -webkit-animation-timing-function: ease-in-out; } 25% { -webkit-transform: translate3d(-4px, 28px, 0px) rotateZ(-53.4676deg); -webkit-animation-timing-function: ease-in-out; } 50% { -webkit-transform: translate3d(-5px, 52px, 0px) rotateZ(-12.6591deg); -webkit-animation-timing-function: ease-in-out; } 75% { -webkit-transform: translate3d(-4px, 28px, 0px) rotateZ(-53.4676deg); -webkit-animation-timing-function: ease-in-out; } 100% { -webkit-transform: translate3d(-5px, 52px, 0px) rotateZ(-12.6591deg); -webkit-animation-timing-function: ease-in-out; } }  @-moz-keyframes gwd-gen-1envgwdanimation_gwd-keyframes { 0% { -moz-transform: translate3d(-5px, 52px, 0px) rotateZ(-12.6591deg); -moz-animation-timing-function: ease-in-out; } 25% { -moz-transform: translate3d(-4px, 28px, 0px) rotateZ(-53.4676deg); -moz-animation-timing-function: ease-in-out; } 50% { -moz-transform: translate3d(-5px, 52px, 0px) rotateZ(-12.6591deg); -moz-animation-timing-function: ease-in-out; } 75% { -moz-transform: translate3d(-4px, 28px, 0px) rotateZ(-53.4676deg); -moz-animation-timing-function: ease-in-out; } 100% { -moz-transform: translate3d(-5px, 52px, 0px) rotateZ(-12.6591deg); -moz-animation-timing-function: ease-in-out; } }  .htmlNoPages .gwd-gen-1envgwdanimation { animation: gwd-gen-1envgwdanimation_gwd-keyframes 4s linear 0s 1 normal forwards; -webkit-animation: gwd-gen-1envgwdanimation_gwd-keyframes 4s linear 0s 1 normal forwards; -moz-animation: gwd-gen-1envgwdanimation_gwd-keyframes 4s linear 0s 1 normal forwards; }  @keyframes gwd-gen-6y8ogwdanimation_gwd-keyframes { 0% { transform: translate3d(-22px, 87px, 0px) rotateZ(-50.1981deg); -webkit-transform: translate3d(-22px, 87px, 0px) rotateZ(-50.1981deg); -moz-transform: translate3d(-22px, 87px, 0px) rotateZ(-50.1981deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 25% { transform: translate3d(-20px, 48px, 0px) rotateZ(-0.657515deg); -webkit-transform: translate3d(-20px, 48px, 0px) rotateZ(-0.657515deg); -moz-transform: translate3d(-20px, 48px, 0px) rotateZ(-0.657515deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 50% { transform: translate3d(-22px, 87px, 0px) rotateZ(-50.1981deg); -webkit-transform: translate3d(-22px, 87px, 0px) rotateZ(-50.1981deg); -moz-transform: translate3d(-22px, 87px, 0px) rotateZ(-50.1981deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 75% { transform: translate3d(-20px, 48px, 0px) rotateZ(-0.657515deg); -webkit-transform: translate3d(-20px, 48px, 0px) rotateZ(-0.657515deg); -moz-transform: translate3d(-20px, 48px, 0px) rotateZ(-0.657515deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 100% { transform: translate3d(-22px, 87px, 0px) rotateZ(-50.1981deg); -webkit-transform: translate3d(-22px, 87px, 0px) rotateZ(-50.1981deg); -moz-transform: translate3d(-22px, 87px, 0px) rotateZ(-50.1981deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } }  @-webkit-keyframes gwd-gen-6y8ogwdanimation_gwd-keyframes { 0% { -webkit-transform: translate3d(-22px, 87px, 0px) rotateZ(-50.1981deg); -webkit-animation-timing-function: ease-in-out; } 25% { -webkit-transform: translate3d(-20px, 48px, 0px) rotateZ(-0.657515deg); -webkit-animation-timing-function: ease-in-out; } 50% { -webkit-transform: translate3d(-22px, 87px, 0px) rotateZ(-50.1981deg); -webkit-animation-timing-function: ease-in-out; } 75% { -webkit-transform: translate3d(-20px, 48px, 0px) rotateZ(-0.657515deg); -webkit-animation-timing-function: ease-in-out; } 100% { -webkit-transform: translate3d(-22px, 87px, 0px) rotateZ(-50.1981deg); -webkit-animation-timing-function: ease-in-out; } }  @-moz-keyframes gwd-gen-6y8ogwdanimation_gwd-keyframes { 0% { -moz-transform: translate3d(-22px, 87px, 0px) rotateZ(-50.1981deg); -moz-animation-timing-function: ease-in-out; } 25% { -moz-transform: translate3d(-20px, 48px, 0px) rotateZ(-0.657515deg); -moz-animation-timing-function: ease-in-out; } 50% { -moz-transform: translate3d(-22px, 87px, 0px) rotateZ(-50.1981deg); -moz-animation-timing-function: ease-in-out; } 75% { -moz-transform: translate3d(-20px, 48px, 0px) rotateZ(-0.657515deg); -moz-animation-timing-function: ease-in-out; } 100% { -moz-transform: translate3d(-22px, 87px, 0px) rotateZ(-50.1981deg); -moz-animation-timing-function: ease-in-out; } }  .htmlNoPages .gwd-gen-6y8ogwdanimation { animation: gwd-gen-6y8ogwdanimation_gwd-keyframes 4s linear 0s 1 normal forwards; -webkit-animation: gwd-gen-6y8ogwdanimation_gwd-keyframes 4s linear 0s 1 normal forwards; -moz-animation: gwd-gen-6y8ogwdanimation_gwd-keyframes 4s linear 0s 1 normal forwards; }  @keyframes gwd-gen-8i4vgwdanimation_gwd-keyframes { 0% { transform: translate3d(-59px, 65px, 0px) rotateZ(-12.6591deg); -webkit-transform: translate3d(-59px, 65px, 0px) rotateZ(-12.6591deg); -moz-transform: translate3d(-59px, 65px, 0px) rotateZ(-12.6591deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 25% { transform: translate3d(-57px, 45px, 0px) rotateZ(-25.5159deg); -webkit-transform: translate3d(-57px, 45px, 0px) rotateZ(-25.5159deg); -moz-transform: translate3d(-57px, 45px, 0px) rotateZ(-25.5159deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 50% { transform: translate3d(-59px, 65px, 0px) rotateZ(-12.6591deg); -webkit-transform: translate3d(-59px, 65px, 0px) rotateZ(-12.6591deg); -moz-transform: translate3d(-59px, 65px, 0px) rotateZ(-12.6591deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 75% { transform: translate3d(-57px, 45px, 0px) rotateZ(-25.5159deg); -webkit-transform: translate3d(-57px, 45px, 0px) rotateZ(-25.5159deg); -moz-transform: translate3d(-57px, 45px, 0px) rotateZ(-25.5159deg); animation-timing-function: linear; -webkit-animation-timing-function: linear; -moz-animation-timing-function: linear; } 100% { transform: translate3d(-59px, 65px, 0px) rotateZ(-12.6591deg); -webkit-transform: translate3d(-59px, 65px, 0px) rotateZ(-12.6591deg); -moz-transform: translate3d(-59px, 65px, 0px) rotateZ(-12.6591deg); animation-timing-function: linear; -webkit-animation-timing-function: linear; -moz-animation-timing-function: linear; } }  @-webkit-keyframes gwd-gen-8i4vgwdanimation_gwd-keyframes { 0% { -webkit-transform: translate3d(-59px, 65px, 0px) rotateZ(-12.6591deg); -webkit-animation-timing-function: ease-in-out; } 25% { -webkit-transform: translate3d(-57px, 45px, 0px) rotateZ(-25.5159deg); -webkit-animation-timing-function: ease-in-out; } 50% { -webkit-transform: translate3d(-59px, 65px, 0px) rotateZ(-12.6591deg); -webkit-animation-timing-function: ease-in-out; } 75% { -webkit-transform: translate3d(-57px, 45px, 0px) rotateZ(-25.5159deg); -webkit-animation-timing-function: linear; } 100% { -webkit-transform: translate3d(-59px, 65px, 0px) rotateZ(-12.6591deg); -webkit-animation-timing-function: linear; } }  @-moz-keyframes gwd-gen-8i4vgwdanimation_gwd-keyframes { 0% { -moz-transform: translate3d(-59px, 65px, 0px) rotateZ(-12.6591deg); -moz-animation-timing-function: ease-in-out; } 25% { -moz-transform: translate3d(-57px, 45px, 0px) rotateZ(-25.5159deg); -moz-animation-timing-function: ease-in-out; } 50% { -moz-transform: translate3d(-59px, 65px, 0px) rotateZ(-12.6591deg); -moz-animation-timing-function: ease-in-out; } 75% { -moz-transform: translate3d(-57px, 45px, 0px) rotateZ(-25.5159deg); -moz-animation-timing-function: linear; } 100% { -moz-transform: translate3d(-59px, 65px, 0px) rotateZ(-12.6591deg); -moz-animation-timing-function: linear; } }  .htmlNoPages .gwd-gen-8i4vgwdanimation { animation: gwd-gen-8i4vgwdanimation_gwd-keyframes 4s linear 0s 1 normal forwards; -webkit-animation: gwd-gen-8i4vgwdanimation_gwd-keyframes 4s linear 0s 1 normal forwards; -moz-animation: gwd-gen-8i4vgwdanimation_gwd-keyframes 4s linear 0s 1 normal forwards; } .gwd-img-6jny { position: absolute; left: 163px; top: 122px; transform-style: preserve-3d; -webkit-transform-style: preserve-3d; -moz-transform-style: preserve-3d; opacity: 0; width: 362px; height: 114px; transform-origin: 180.277px 59.194px 0px; -webkit-transform-origin: 180.277px 59.194px 0px; -moz-transform-origin: 180.277px 59.194px 0px; transform: translate3d(57px, 18px, 0px); -webkit-transform: translate3d(57px, 18px, 0px); -moz-transform: translate3d(57px, 18px, 0px); }  @keyframes gwd-gen-uaqqgwdanimation_gwd-keyframes { 0% { width: 362px; height: 114px; transform-origin: 180.277px 59.194px 0px; -webkit-transform-origin: 180.277px 59.194px 0px; -moz-transform-origin: 180.277px 59.194px 0px; transform: translate3d(57px, 18px, 0px); -webkit-transform: translate3d(57px, 18px, 0px); -moz-transform: translate3d(57px, 18px, 0px); opacity: 0; animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 100% { width: 474px; height: 156px; transform-origin: 236.889px 77.8445px 0px; -webkit-transform-origin: 236.889px 77.8445px 0px; -moz-transform-origin: 236.889px 77.8445px 0px; transform: translate3d(0px, 0px, 0px); -webkit-transform: translate3d(0px, 0px, 0px); -moz-transform: translate3d(0px, 0px, 0px); opacity: 1; animation-timing-function: linear; -webkit-animation-timing-function: linear; -moz-animation-timing-function: linear; } }  @-webkit-keyframes gwd-gen-uaqqgwdanimation_gwd-keyframes { 0% { width: 362px; height: 114px; -webkit-transform-origin: 180.277px 59.194px 0px; -webkit-transform: translate3d(57px, 18px, 0px); opacity: 0; -webkit-animation-timing-function: ease-in-out; } 100% { width: 474px; height: 156px; -webkit-transform-origin: 236.889px 77.8445px 0px; -webkit-transform: translate3d(0px, 0px, 0px); opacity: 1; -webkit-animation-timing-function: linear; } }  @-moz-keyframes gwd-gen-uaqqgwdanimation_gwd-keyframes { 0% { width: 362px; height: 114px; -moz-transform-origin: 180.277px 59.194px 0px; -moz-transform: translate3d(57px, 18px, 0px); opacity: 0; -moz-animation-timing-function: ease-in-out; } 100% { width: 474px; height: 156px; -moz-transform-origin: 236.889px 77.8445px 0px; -moz-transform: translate3d(0px, 0px, 0px); opacity: 1; -moz-animation-timing-function: linear; } }  .htmlNoPages .gwd-gen-uaqqgwdanimation { animation: gwd-gen-uaqqgwdanimation_gwd-keyframes 0.5s linear 0.5s 1 normal forwards; -webkit-animation: gwd-gen-uaqqgwdanimation_gwd-keyframes 0.5s linear 0.5s 1 normal forwards; -moz-animation: gwd-gen-uaqqgwdanimation_gwd-keyframes 0.5s linear 0.5s 1 normal forwards; } .gwd-img-el9e { position: absolute; left: 317px; top: 174px; opacity: 0; transform-style: preserve-3d; -webkit-transform-style: preserve-3d; -moz-transform-style: preserve-3d; width: 415px; height: 120px; transform-origin: 207.177px 57.9223px 0px; -webkit-transform-origin: 207.177px 57.9223px 0px; -moz-transform-origin: 207.177px 57.9223px 0px; transform: translate3d(-122px, -35px, 0px); -webkit-transform: translate3d(-122px, -35px, 0px); -moz-transform: translate3d(-122px, -35px, 0px); }  @keyframes gwd-gen-1pawgwdanimation_gwd-keyframes { 0% { opacity: 0; width: 415px; height: 120px; transform-origin: 207.177px 57.9223px 0px; -webkit-transform-origin: 207.177px 57.9223px 0px; -moz-transform-origin: 207.177px 57.9223px 0px; transform: translate3d(-122px, -35px, 0px); -webkit-transform: translate3d(-122px, -35px, 0px); -moz-transform: translate3d(-122px, -35px, 0px); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 100% { opacity: 1; width: 438px; height: 128px; transform-origin: 218.392px 63.4323px 0px; -webkit-transform-origin: 218.392px 63.4323px 0px; -moz-transform-origin: 218.392px 63.4323px 0px; transform: translate3d(-135px, -39px, 0px); -webkit-transform: translate3d(-135px, -39px, 0px); -moz-transform: translate3d(-135px, -39px, 0px); animation-timing-function: linear; -webkit-animation-timing-function: linear; -moz-animation-timing-function: linear; } }  @-webkit-keyframes gwd-gen-1pawgwdanimation_gwd-keyframes { 0% { opacity: 0; width: 415px; height: 120px; -webkit-transform-origin: 207.177px 57.9223px 0px; -webkit-transform: translate3d(-122px, -35px, 0px); -webkit-animation-timing-function: ease-in-out; } 100% { opacity: 1; width: 438px; height: 128px; -webkit-transform-origin: 218.392px 63.4323px 0px; -webkit-transform: translate3d(-135px, -39px, 0px); -webkit-animation-timing-function: linear; } }  @-moz-keyframes gwd-gen-1pawgwdanimation_gwd-keyframes { 0% { opacity: 0; width: 415px; height: 120px; -moz-transform-origin: 207.177px 57.9223px 0px; -moz-transform: translate3d(-122px, -35px, 0px); -moz-animation-timing-function: ease-in-out; } 100% { opacity: 1; width: 438px; height: 128px; -moz-transform-origin: 218.392px 63.4323px 0px; -moz-transform: translate3d(-135px, -39px, 0px); -moz-animation-timing-function: linear; } }  .htmlNoPages .gwd-gen-1pawgwdanimation { animation: gwd-gen-1pawgwdanimation_gwd-keyframes 0.5s linear 1s 1 normal forwards; -webkit-animation: gwd-gen-1pawgwdanimation_gwd-keyframes 0.5s linear 1s 1 normal forwards; -moz-animation: gwd-gen-1pawgwdanimation_gwd-keyframes 0.5s linear 1s 1 normal forwards; } .gwd-img-1d1s { position: absolute; left: 118px; top: 99px; transform-style: preserve-3d; -webkit-transform-style: preserve-3d; -moz-transform-style: preserve-3d; transform: translate3d(30px, 53px, 0px) rotateZ(-39.2821deg); -webkit-transform: translate3d(30px, 53px, 0px) rotateZ(-39.2821deg); -moz-transform: translate3d(30px, 53px, 0px) rotateZ(-39.2821deg); width: 81px; height: 131px; transform-origin: 40.8122px 65.2821px 0px; -webkit-transform-origin: 40.8122px 65.2821px 0px; -moz-transform-origin: 40.8122px 65.2821px 0px; opacity: 0; }  @keyframes gwd-gen-18sogwdanimation_gwd-keyframes { 0% { transform: translate3d(30px, 53px, 0px) rotateZ(-39.2821deg); -webkit-transform: translate3d(30px, 53px, 0px) rotateZ(-39.2821deg); -moz-transform: translate3d(30px, 53px, 0px) rotateZ(-39.2821deg); width: 81px; height: 131px; transform-origin: 40.8122px 65.2821px 0px; -webkit-transform-origin: 40.8122px 65.2821px 0px; -moz-transform-origin: 40.8122px 65.2821px 0px; opacity: 0; animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 33.33% { transform: translate3d(0px, 0px, 0px) rotateZ(-13.6211deg); -webkit-transform: translate3d(0px, 0px, 0px) rotateZ(-13.6211deg); -moz-transform: translate3d(0px, 0px, 0px) rotateZ(-13.6211deg); width: 98px; height: 159px; transform-origin: 49.2122px 79.344px 0px; -webkit-transform-origin: 49.2122px 79.344px 0px; -moz-transform-origin: 49.2122px 79.344px 0px; opacity: 1; animation-timing-function: linear; -webkit-animation-timing-function: linear; -moz-animation-timing-function: linear; } 66.67% { transform: translate3d(0px, 0px, 0px) rotateZ(-26.3514deg); -webkit-transform: translate3d(0px, 0px, 0px) rotateZ(-26.3514deg); -moz-transform: translate3d(0px, 0px, 0px) rotateZ(-26.3514deg); width: 98px; height: 159px; transform-origin: 49.2122px 79.344px 0px; -webkit-transform-origin: 49.2122px 79.344px 0px; -moz-transform-origin: 49.2122px 79.344px 0px; opacity: 1; animation-timing-function: linear; -webkit-animation-timing-function: linear; -moz-animation-timing-function: linear; } 100% { transform: translate3d(0px, 0px, 0px) rotateZ(-13.6211deg); -webkit-transform: translate3d(0px, 0px, 0px) rotateZ(-13.6211deg); -moz-transform: translate3d(0px, 0px, 0px) rotateZ(-13.6211deg); width: 98px; height: 159px; transform-origin: 49.2122px 79.344px 0px; -webkit-transform-origin: 49.2122px 79.344px 0px; -moz-transform-origin: 49.2122px 79.344px 0px; opacity: 1; animation-timing-function: linear; -webkit-animation-timing-function: linear; -moz-animation-timing-function: linear; } }  @-webkit-keyframes gwd-gen-18sogwdanimation_gwd-keyframes { 0% { -webkit-transform: translate3d(30px, 53px, 0px) rotateZ(-39.2821deg); width: 81px; height: 131px; -webkit-transform-origin: 40.8122px 65.2821px 0px; opacity: 0; -webkit-animation-timing-function: ease-in-out; } 33.33% { -webkit-transform: translate3d(0px, 0px, 0px) rotateZ(-13.6211deg); width: 98px; height: 159px; -webkit-transform-origin: 49.2122px 79.344px 0px; opacity: 1; -webkit-animation-timing-function: linear; } 66.67% { -webkit-transform: translate3d(0px, 0px, 0px) rotateZ(-26.3514deg); width: 98px; height: 159px; -webkit-transform-origin: 49.2122px 79.344px 0px; opacity: 1; -webkit-animation-timing-function: linear; } 100% { -webkit-transform: translate3d(0px, 0px, 0px) rotateZ(-13.6211deg); width: 98px; height: 159px; -webkit-transform-origin: 49.2122px 79.344px 0px; opacity: 1; -webkit-animation-timing-function: linear; } }  @-moz-keyframes gwd-gen-18sogwdanimation_gwd-keyframes { 0% { -moz-transform: translate3d(30px, 53px, 0px) rotateZ(-39.2821deg); width: 81px; height: 131px; -moz-transform-origin: 40.8122px 65.2821px 0px; opacity: 0; -moz-animation-timing-function: ease-in-out; } 33.33% { -moz-transform: translate3d(0px, 0px, 0px) rotateZ(-13.6211deg); width: 98px; height: 159px; -moz-transform-origin: 49.2122px 79.344px 0px; opacity: 1; -moz-animation-timing-function: linear; } 66.67% { -moz-transform: translate3d(0px, 0px, 0px) rotateZ(-26.3514deg); width: 98px; height: 159px; -moz-transform-origin: 49.2122px 79.344px 0px; opacity: 1; -moz-animation-timing-function: linear; } 100% { -moz-transform: translate3d(0px, 0px, 0px) rotateZ(-13.6211deg); width: 98px; height: 159px; -moz-transform-origin: 49.2122px 79.344px 0px; opacity: 1; -moz-animation-timing-function: linear; } }  .htmlNoPages .gwd-gen-18sogwdanimation { animation: gwd-gen-18sogwdanimation_gwd-keyframes 3s linear 1s 1 normal forwards; -webkit-animation: gwd-gen-18sogwdanimation_gwd-keyframes 3s linear 1s 1 normal forwards; -moz-animation: gwd-gen-18sogwdanimation_gwd-keyframes 3s linear 1s 1 normal forwards; }  @keyframes gwd-gen-1rqogwdanimation_gwd-keyframes { 0% { transform: translate3d(440px, 53px, 0px) rotateZ(22.9471deg); -webkit-transform: translate3d(440px, 53px, 0px) rotateZ(22.9471deg); -moz-transform: translate3d(440px, 53px, 0px) rotateZ(22.9471deg); width: 81px; height: 131px; transform-origin: 40.8122px 65.2821px 0px; -webkit-transform-origin: 40.8122px 65.2821px 0px; -moz-transform-origin: 40.8122px 65.2821px 0px; opacity: 0; animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 33.33% { transform: translate3d(460px, 0px, 0px) rotateZ(12.0158deg); -webkit-transform: translate3d(460px, 0px, 0px) rotateZ(12.0158deg); -moz-transform: translate3d(460px, 0px, 0px) rotateZ(12.0158deg); width: 98px; height: 159px; transform-origin: 49.2122px 79.344px 0px; -webkit-transform-origin: 49.2122px 79.344px 0px; -moz-transform-origin: 49.2122px 79.344px 0px; opacity: 1; animation-timing-function: linear; -webkit-animation-timing-function: linear; -moz-animation-timing-function: linear; } 66.67% { transform: translate3d(460px, 0px, 0px) rotateZ(30.7723deg); -webkit-transform: translate3d(460px, 0px, 0px) rotateZ(30.7723deg); -moz-transform: translate3d(460px, 0px, 0px) rotateZ(30.7723deg); width: 98px; height: 159px; transform-origin: 49.2122px 79.344px 0px; -webkit-transform-origin: 49.2122px 79.344px 0px; -moz-transform-origin: 49.2122px 79.344px 0px; opacity: 1; animation-timing-function: linear; -webkit-animation-timing-function: linear; -moz-animation-timing-function: linear; } 100% { transform: translate3d(460px, 0px, 0px) rotateZ(12.0158deg); -webkit-transform: translate3d(460px, 0px, 0px) rotateZ(12.0158deg); -moz-transform: translate3d(460px, 0px, 0px) rotateZ(12.0158deg); width: 98px; height: 159px; transform-origin: 49.2122px 79.344px 0px; -webkit-transform-origin: 49.2122px 79.344px 0px; -moz-transform-origin: 49.2122px 79.344px 0px; opacity: 1; animation-timing-function: linear; -webkit-animation-timing-function: linear; -moz-animation-timing-function: linear; } }  @-webkit-keyframes gwd-gen-1rqogwdanimation_gwd-keyframes { 0% { -webkit-transform: translate3d(440px, 53px, 0px) rotateZ(22.9471deg); width: 81px; height: 131px; -webkit-transform-origin: 40.8122px 65.2821px 0px; opacity: 0; -webkit-animation-timing-function: ease-in-out; } 33.33% { -webkit-transform: translate3d(460px, 0px, 0px) rotateZ(12.0158deg); width: 98px; height: 159px; -webkit-transform-origin: 49.2122px 79.344px 0px; opacity: 1; -webkit-animation-timing-function: linear; } 66.67% { -webkit-transform: translate3d(460px, 0px, 0px) rotateZ(30.7723deg); width: 98px; height: 159px; -webkit-transform-origin: 49.2122px 79.344px 0px; opacity: 1; -webkit-animation-timing-function: linear; } 100% { -webkit-transform: translate3d(460px, 0px, 0px) rotateZ(12.0158deg); width: 98px; height: 159px; -webkit-transform-origin: 49.2122px 79.344px 0px; opacity: 1; -webkit-animation-timing-function: linear; } }  @-moz-keyframes gwd-gen-1rqogwdanimation_gwd-keyframes { 0% { -moz-transform: translate3d(440px, 53px, 0px) rotateZ(22.9471deg); width: 81px; height: 131px; -moz-transform-origin: 40.8122px 65.2821px 0px; opacity: 0; -moz-animation-timing-function: ease-in-out; } 33.33% { -moz-transform: translate3d(460px, 0px, 0px) rotateZ(12.0158deg); width: 98px; height: 159px; -moz-transform-origin: 49.2122px 79.344px 0px; opacity: 1; -moz-animation-timing-function: linear; } 66.67% { -moz-transform: translate3d(460px, 0px, 0px) rotateZ(30.7723deg); width: 98px; height: 159px; -moz-transform-origin: 49.2122px 79.344px 0px; opacity: 1; -moz-animation-timing-function: linear; } 100% { -moz-transform: translate3d(460px, 0px, 0px) rotateZ(12.0158deg); width: 98px; height: 159px; -moz-transform-origin: 49.2122px 79.344px 0px; opacity: 1; -moz-animation-timing-function: linear; } }  .htmlNoPages .gwd-gen-1rqogwdanimation { animation: gwd-gen-1rqogwdanimation_gwd-keyframes 3s linear 1s 1 normal forwards; -webkit-animation: gwd-gen-1rqogwdanimation_gwd-keyframes 3s linear 1s 1 normal forwards; -moz-animation: gwd-gen-1rqogwdanimation_gwd-keyframes 3s linear 1s 1 normal forwards; } .gwd-img-18go { opacity: 0; transform: translate3d(440px, 53px, 0px) rotateZ(22.9471deg); -webkit-transform: translate3d(440px, 53px, 0px) rotateZ(22.9471deg); -moz-transform: translate3d(440px, 53px, 0px) rotateZ(22.9471deg); } .gwd-img-1lq4 { position: absolute; transform-origin: 73.1373px 20.1884px 0px; -webkit-transform-origin: 73.1373px 20.1884px 0px; -moz-transform-origin: 73.1373px 20.1884px 0px; width: 135px; height: 37px; left: 635px; top: 311px; } .gwd-img-1khb { position: absolute; transform-origin: 28.5948px 19.3739px 0px; -webkit-transform-origin: 28.5948px 19.3739px 0px; -moz-transform-origin: 28.5948px 19.3739px 0px; width: 53px; height: 36px; left: 704px; top: 332px; transform-style: preserve-3d; -webkit-transform-style: preserve-3d; -moz-transform-style: preserve-3d; transform: translate3d(-2px, -4px, 0px) rotateZ(-13.4055deg); -webkit-transform: translate3d(-2px, -4px, 0px) rotateZ(-13.4055deg); -moz-transform: translate3d(-2px, -4px, 0px) rotateZ(-13.4055deg); } .gwd-img-103r { position: absolute; width: 113px; height: 98px; left: 643px; top: 247px; transform-style: preserve-3d; -webkit-transform-style: preserve-3d; -moz-transform-style: preserve-3d; transform: translate3d(10px, 0px, 0px) rotateZ(4.12183deg); -webkit-transform: translate3d(10px, 0px, 0px) rotateZ(4.12183deg); -moz-transform: translate3d(10px, 0px, 0px) rotateZ(4.12183deg); } .gwd-img-165o { position: absolute; transform-origin: 23.4632px 32.926px 0px; -webkit-transform-origin: 23.4632px 32.926px 0px; -moz-transform-origin: 23.4632px 32.926px 0px; width: 43px; height: 60px; left: 750px; top: 239px; transform-style: preserve-3d; -webkit-transform-style: preserve-3d; -moz-transform-style: preserve-3d; transform: translate3d(4px, 27px, 0px) rotateZ(34.3748deg); -webkit-transform: translate3d(4px, 27px, 0px) rotateZ(34.3748deg); -moz-transform: translate3d(4px, 27px, 0px) rotateZ(34.3748deg); } .gwd-img-5v80 { position: absolute; width: 48px; height: 65px; transform-origin: 23.9377px 32.2968px 0px; -webkit-transform-origin: 23.9377px 32.2968px 0px; -moz-transform-origin: 23.9377px 32.2968px 0px; left: 620px; top: 247px; transform-style: preserve-3d; -webkit-transform-style: preserve-3d; -moz-transform-style: preserve-3d; transform: translate3d(15px, -6px, 0px) rotateZ(1.91837deg); -webkit-transform: translate3d(15px, -6px, 0px) rotateZ(1.91837deg); -moz-transform: translate3d(15px, -6px, 0px) rotateZ(1.91837deg); }  @keyframes gwd-gen-54q9gwdanimation_gwd-keyframes { 0% { transform: translate3d(10px, 0px, 0px) rotateZ(4.12183deg); -webkit-transform: translate3d(10px, 0px, 0px) rotateZ(4.12183deg); -moz-transform: translate3d(10px, 0px, 0px) rotateZ(4.12183deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 12.5% { transform: translate3d(5px, -11px, 0px) rotateZ(-0.36487deg); -webkit-transform: translate3d(5px, -11px, 0px) rotateZ(-0.36487deg); -moz-transform: translate3d(5px, -11px, 0px) rotateZ(-0.36487deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 25% { transform: translate3d(0px, 0px, 0px) rotateZ(-4.85157deg); -webkit-transform: translate3d(0px, 0px, 0px) rotateZ(-4.85157deg); -moz-transform: translate3d(0px, 0px, 0px) rotateZ(-4.85157deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 37.5% { transform: translate3d(5px, -11px, 0px) rotateZ(-0.36487deg); -webkit-transform: translate3d(5px, -11px, 0px) rotateZ(-0.36487deg); -moz-transform: translate3d(5px, -11px, 0px) rotateZ(-0.36487deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 50% { transform: translate3d(10px, 0px, 0px) rotateZ(4.12183deg); -webkit-transform: translate3d(10px, 0px, 0px) rotateZ(4.12183deg); -moz-transform: translate3d(10px, 0px, 0px) rotateZ(4.12183deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 62.5% { transform: translate3d(5px, -11px, 0px) rotateZ(-0.36487deg); -webkit-transform: translate3d(5px, -11px, 0px) rotateZ(-0.36487deg); -moz-transform: translate3d(5px, -11px, 0px) rotateZ(-0.36487deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 75% { transform: translate3d(0px, 0px, 0px) rotateZ(-4.85157deg); -webkit-transform: translate3d(0px, 0px, 0px) rotateZ(-4.85157deg); -moz-transform: translate3d(0px, 0px, 0px) rotateZ(-4.85157deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 87.5% { transform: translate3d(5px, -11px, 0px) rotateZ(-0.36487deg); -webkit-transform: translate3d(5px, -11px, 0px) rotateZ(-0.36487deg); -moz-transform: translate3d(5px, -11px, 0px) rotateZ(-0.36487deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 100% { transform: translate3d(10px, 0px, 0px) rotateZ(4.12183deg); -webkit-transform: translate3d(10px, 0px, 0px) rotateZ(4.12183deg); -moz-transform: translate3d(10px, 0px, 0px) rotateZ(4.12183deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } }  @-webkit-keyframes gwd-gen-54q9gwdanimation_gwd-keyframes { 0% { -webkit-transform: translate3d(10px, 0px, 0px) rotateZ(4.12183deg); -webkit-animation-timing-function: ease-in-out; } 12.5% { -webkit-transform: translate3d(5px, -11px, 0px) rotateZ(-0.36487deg); -webkit-animation-timing-function: ease-in-out; } 25% { -webkit-transform: translate3d(0px, 0px, 0px) rotateZ(-4.85157deg); -webkit-animation-timing-function: ease-in-out; } 37.5% { -webkit-transform: translate3d(5px, -11px, 0px) rotateZ(-0.36487deg); -webkit-animation-timing-function: ease-in-out; } 50% { -webkit-transform: translate3d(10px, 0px, 0px) rotateZ(4.12183deg); -webkit-animation-timing-function: ease-in-out; } 62.5% { -webkit-transform: translate3d(5px, -11px, 0px) rotateZ(-0.36487deg); -webkit-animation-timing-function: ease-in-out; } 75% { -webkit-transform: translate3d(0px, 0px, 0px) rotateZ(-4.85157deg); -webkit-animation-timing-function: ease-in-out; } 87.5% { -webkit-transform: translate3d(5px, -11px, 0px) rotateZ(-0.36487deg); -webkit-animation-timing-function: ease-in-out; } 100% { -webkit-transform: translate3d(10px, 0px, 0px) rotateZ(4.12183deg); -webkit-animation-timing-function: ease-in-out; } }  @-moz-keyframes gwd-gen-54q9gwdanimation_gwd-keyframes { 0% { -moz-transform: translate3d(10px, 0px, 0px) rotateZ(4.12183deg); -moz-animation-timing-function: ease-in-out; } 12.5% { -moz-transform: translate3d(5px, -11px, 0px) rotateZ(-0.36487deg); -moz-animation-timing-function: ease-in-out; } 25% { -moz-transform: translate3d(0px, 0px, 0px) rotateZ(-4.85157deg); -moz-animation-timing-function: ease-in-out; } 37.5% { -moz-transform: translate3d(5px, -11px, 0px) rotateZ(-0.36487deg); -moz-animation-timing-function: ease-in-out; } 50% { -moz-transform: translate3d(10px, 0px, 0px) rotateZ(4.12183deg); -moz-animation-timing-function: ease-in-out; } 62.5% { -moz-transform: translate3d(5px, -11px, 0px) rotateZ(-0.36487deg); -moz-animation-timing-function: ease-in-out; } 75% { -moz-transform: translate3d(0px, 0px, 0px) rotateZ(-4.85157deg); -moz-animation-timing-function: ease-in-out; } 87.5% { -moz-transform: translate3d(5px, -11px, 0px) rotateZ(-0.36487deg); -moz-animation-timing-function: ease-in-out; } 100% { -moz-transform: translate3d(10px, 0px, 0px) rotateZ(4.12183deg); -moz-animation-timing-function: ease-in-out; } }  .htmlNoPages .gwd-gen-54q9gwdanimation { animation: gwd-gen-54q9gwdanimation_gwd-keyframes 4s linear 0s 1 normal forwards; -webkit-animation: gwd-gen-54q9gwdanimation_gwd-keyframes 4s linear 0s 1 normal forwards; -moz-animation: gwd-gen-54q9gwdanimation_gwd-keyframes 4s linear 0s 1 normal forwards; }  @keyframes gwd-gen-1nx1gwdanimation_gwd-keyframes { 0% { transform: translate3d(15px, -6px, 0px) rotateZ(1.91837deg); -webkit-transform: translate3d(15px, -6px, 0px) rotateZ(1.91837deg); -moz-transform: translate3d(15px, -6px, 0px) rotateZ(1.91837deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 12.5% { transform: translate3d(-4px, -5px, 0px) rotateZ(-20.7177deg); -webkit-transform: translate3d(-4px, -5px, 0px) rotateZ(-20.7177deg); -moz-transform: translate3d(-4px, -5px, 0px) rotateZ(-20.7177deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 25% { transform: translate3d(-9px, 19px, 0px) rotateZ(-44.3443deg); -webkit-transform: translate3d(-9px, 19px, 0px) rotateZ(-44.3443deg); -moz-transform: translate3d(-9px, 19px, 0px) rotateZ(-44.3443deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 37.5% { transform: translate3d(-4px, -5px, 0px) rotateZ(-20.7177deg); -webkit-transform: translate3d(-4px, -5px, 0px) rotateZ(-20.7177deg); -moz-transform: translate3d(-4px, -5px, 0px) rotateZ(-20.7177deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 50% { transform: translate3d(15px, -6px, 0px) rotateZ(1.91837deg); -webkit-transform: translate3d(15px, -6px, 0px) rotateZ(1.91837deg); -moz-transform: translate3d(15px, -6px, 0px) rotateZ(1.91837deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 62.5% { transform: translate3d(-4px, -5px, 0px) rotateZ(-20.7177deg); -webkit-transform: translate3d(-4px, -5px, 0px) rotateZ(-20.7177deg); -moz-transform: translate3d(-4px, -5px, 0px) rotateZ(-20.7177deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 75% { transform: translate3d(-9px, 19px, 0px) rotateZ(-44.3443deg); -webkit-transform: translate3d(-9px, 19px, 0px) rotateZ(-44.3443deg); -moz-transform: translate3d(-9px, 19px, 0px) rotateZ(-44.3443deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 87.5% { transform: translate3d(-4px, -5px, 0px) rotateZ(-20.7177deg); -webkit-transform: translate3d(-4px, -5px, 0px) rotateZ(-20.7177deg); -moz-transform: translate3d(-4px, -5px, 0px) rotateZ(-20.7177deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 100% { transform: translate3d(15px, -6px, 0px) rotateZ(1.91837deg); -webkit-transform: translate3d(15px, -6px, 0px) rotateZ(1.91837deg); -moz-transform: translate3d(15px, -6px, 0px) rotateZ(1.91837deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } }  @-webkit-keyframes gwd-gen-1nx1gwdanimation_gwd-keyframes { 0% { -webkit-transform: translate3d(15px, -6px, 0px) rotateZ(1.91837deg); -webkit-animation-timing-function: ease-in-out; } 12.5% { -webkit-transform: translate3d(-4px, -5px, 0px) rotateZ(-20.7177deg); -webkit-animation-timing-function: ease-in-out; } 25% { -webkit-transform: translate3d(-9px, 19px, 0px) rotateZ(-44.3443deg); -webkit-animation-timing-function: ease-in-out; } 37.5% { -webkit-transform: translate3d(-4px, -5px, 0px) rotateZ(-20.7177deg); -webkit-animation-timing-function: ease-in-out; } 50% { -webkit-transform: translate3d(15px, -6px, 0px) rotateZ(1.91837deg); -webkit-animation-timing-function: ease-in-out; } 62.5% { -webkit-transform: translate3d(-4px, -5px, 0px) rotateZ(-20.7177deg); -webkit-animation-timing-function: ease-in-out; } 75% { -webkit-transform: translate3d(-9px, 19px, 0px) rotateZ(-44.3443deg); -webkit-animation-timing-function: ease-in-out; } 87.5% { -webkit-transform: translate3d(-4px, -5px, 0px) rotateZ(-20.7177deg); -webkit-animation-timing-function: ease-in-out; } 100% { -webkit-transform: translate3d(15px, -6px, 0px) rotateZ(1.91837deg); -webkit-animation-timing-function: ease-in-out; } }  @-moz-keyframes gwd-gen-1nx1gwdanimation_gwd-keyframes { 0% { -moz-transform: translate3d(15px, -6px, 0px) rotateZ(1.91837deg); -moz-animation-timing-function: ease-in-out; } 12.5% { -moz-transform: translate3d(-4px, -5px, 0px) rotateZ(-20.7177deg); -moz-animation-timing-function: ease-in-out; } 25% { -moz-transform: translate3d(-9px, 19px, 0px) rotateZ(-44.3443deg); -moz-animation-timing-function: ease-in-out; } 37.5% { -moz-transform: translate3d(-4px, -5px, 0px) rotateZ(-20.7177deg); -moz-animation-timing-function: ease-in-out; } 50% { -moz-transform: translate3d(15px, -6px, 0px) rotateZ(1.91837deg); -moz-animation-timing-function: ease-in-out; } 62.5% { -moz-transform: translate3d(-4px, -5px, 0px) rotateZ(-20.7177deg); -moz-animation-timing-function: ease-in-out; } 75% { -moz-transform: translate3d(-9px, 19px, 0px) rotateZ(-44.3443deg); -moz-animation-timing-function: ease-in-out; } 87.5% { -moz-transform: translate3d(-4px, -5px, 0px) rotateZ(-20.7177deg); -moz-animation-timing-function: ease-in-out; } 100% { -moz-transform: translate3d(15px, -6px, 0px) rotateZ(1.91837deg); -moz-animation-timing-function: ease-in-out; } }  .htmlNoPages .gwd-gen-1nx1gwdanimation { animation: gwd-gen-1nx1gwdanimation_gwd-keyframes 4s linear 0s 1 normal forwards; -webkit-animation: gwd-gen-1nx1gwdanimation_gwd-keyframes 4s linear 0s 1 normal forwards; -moz-animation: gwd-gen-1nx1gwdanimation_gwd-keyframes 4s linear 0s 1 normal forwards; }  @keyframes gwd-gen-139ngwdanimation_gwd-keyframes { 0% { transform: translate3d(4px, 27px, 0px) rotateZ(34.3748deg); -webkit-transform: translate3d(4px, 27px, 0px) rotateZ(34.3748deg); -moz-transform: translate3d(4px, 27px, 0px) rotateZ(34.3748deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 12.5% { transform: translate3d(-7px, -1px, 0px) rotateZ(8.88789deg); -webkit-transform: translate3d(-7px, -1px, 0px) rotateZ(8.88789deg); -moz-transform: translate3d(-7px, -1px, 0px) rotateZ(8.88789deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 25% { transform: translate3d(-17px, 4px, 0px) rotateZ(-2.69669deg); -webkit-transform: translate3d(-17px, 4px, 0px) rotateZ(-2.69669deg); -moz-transform: translate3d(-17px, 4px, 0px) rotateZ(-2.69669deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 37.5% { transform: translate3d(-7px, -1px, 0px) rotateZ(8.88789deg); -webkit-transform: translate3d(-7px, -1px, 0px) rotateZ(8.88789deg); -moz-transform: translate3d(-7px, -1px, 0px) rotateZ(8.88789deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 50% { transform: translate3d(4px, 27px, 0px) rotateZ(34.3748deg); -webkit-transform: translate3d(4px, 27px, 0px) rotateZ(34.3748deg); -moz-transform: translate3d(4px, 27px, 0px) rotateZ(34.3748deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 62.5% { transform: translate3d(-7px, -1px, 0px) rotateZ(8.88789deg); -webkit-transform: translate3d(-7px, -1px, 0px) rotateZ(8.88789deg); -moz-transform: translate3d(-7px, -1px, 0px) rotateZ(8.88789deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 75% { transform: translate3d(-17px, 4px, 0px) rotateZ(-2.69669deg); -webkit-transform: translate3d(-17px, 4px, 0px) rotateZ(-2.69669deg); -moz-transform: translate3d(-17px, 4px, 0px) rotateZ(-2.69669deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 87.5% { transform: translate3d(-7px, -1px, 0px) rotateZ(8.88789deg); -webkit-transform: translate3d(-7px, -1px, 0px) rotateZ(8.88789deg); -moz-transform: translate3d(-7px, -1px, 0px) rotateZ(8.88789deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 100% { transform: translate3d(4px, 27px, 0px) rotateZ(34.3748deg); -webkit-transform: translate3d(4px, 27px, 0px) rotateZ(34.3748deg); -moz-transform: translate3d(4px, 27px, 0px) rotateZ(34.3748deg); animation-timing-function: linear; -webkit-animation-timing-function: linear; -moz-animation-timing-function: linear; } }  @-webkit-keyframes gwd-gen-139ngwdanimation_gwd-keyframes { 0% { -webkit-transform: translate3d(4px, 27px, 0px) rotateZ(34.3748deg); -webkit-animation-timing-function: ease-in-out; } 12.5% { -webkit-transform: translate3d(-7px, -1px, 0px) rotateZ(8.88789deg); -webkit-animation-timing-function: ease-in-out; } 25% { -webkit-transform: translate3d(-17px, 4px, 0px) rotateZ(-2.69669deg); -webkit-animation-timing-function: ease-in-out; } 37.5% { -webkit-transform: translate3d(-7px, -1px, 0px) rotateZ(8.88789deg); -webkit-animation-timing-function: ease-in-out; } 50% { -webkit-transform: translate3d(4px, 27px, 0px) rotateZ(34.3748deg); -webkit-animation-timing-function: ease-in-out; } 62.5% { -webkit-transform: translate3d(-7px, -1px, 0px) rotateZ(8.88789deg); -webkit-animation-timing-function: ease-in-out; } 75% { -webkit-transform: translate3d(-17px, 4px, 0px) rotateZ(-2.69669deg); -webkit-animation-timing-function: ease-in-out; } 87.5% { -webkit-transform: translate3d(-7px, -1px, 0px) rotateZ(8.88789deg); -webkit-animation-timing-function: ease-in-out; } 100% { -webkit-transform: translate3d(4px, 27px, 0px) rotateZ(34.3748deg); -webkit-animation-timing-function: linear; } }  @-moz-keyframes gwd-gen-139ngwdanimation_gwd-keyframes { 0% { -moz-transform: translate3d(4px, 27px, 0px) rotateZ(34.3748deg); -moz-animation-timing-function: ease-in-out; } 12.5% { -moz-transform: translate3d(-7px, -1px, 0px) rotateZ(8.88789deg); -moz-animation-timing-function: ease-in-out; } 25% { -moz-transform: translate3d(-17px, 4px, 0px) rotateZ(-2.69669deg); -moz-animation-timing-function: ease-in-out; } 37.5% { -moz-transform: translate3d(-7px, -1px, 0px) rotateZ(8.88789deg); -moz-animation-timing-function: ease-in-out; } 50% { -moz-transform: translate3d(4px, 27px, 0px) rotateZ(34.3748deg); -moz-animation-timing-function: ease-in-out; } 62.5% { -moz-transform: translate3d(-7px, -1px, 0px) rotateZ(8.88789deg); -moz-animation-timing-function: ease-in-out; } 75% { -moz-transform: translate3d(-17px, 4px, 0px) rotateZ(-2.69669deg); -moz-animation-timing-function: ease-in-out; } 87.5% { -moz-transform: translate3d(-7px, -1px, 0px) rotateZ(8.88789deg); -moz-animation-timing-function: ease-in-out; } 100% { -moz-transform: translate3d(4px, 27px, 0px) rotateZ(34.3748deg); -moz-animation-timing-function: linear; } }  .htmlNoPages .gwd-gen-139ngwdanimation { animation: gwd-gen-139ngwdanimation_gwd-keyframes 4s linear 0s 1 normal forwards; -webkit-animation: gwd-gen-139ngwdanimation_gwd-keyframes 4s linear 0s 1 normal forwards; -moz-animation: gwd-gen-139ngwdanimation_gwd-keyframes 4s linear 0s 1 normal forwards; }  @keyframes gwd-gen-1pkcgwdanimation_gwd-keyframes { 0% { transform: translate3d(-2px, -4px, 0px) rotateZ(-13.4055deg); -webkit-transform: translate3d(-2px, -4px, 0px) rotateZ(-13.4055deg); -moz-transform: translate3d(-2px, -4px, 0px) rotateZ(-13.4055deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 12.5% { transform: translate3d(-7px, -13px, 0px) rotateZ(19.9712deg); -webkit-transform: translate3d(-7px, -13px, 0px) rotateZ(19.9712deg); -moz-transform: translate3d(-7px, -13px, 0px) rotateZ(19.9712deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 25% { transform: translate3d(8px, -8px, 0px) rotateZ(-15.6332deg); -webkit-transform: translate3d(8px, -8px, 0px) rotateZ(-15.6332deg); -moz-transform: translate3d(8px, -8px, 0px) rotateZ(-15.6332deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 37.5% { transform: translate3d(-7px, -13px, 0px) rotateZ(19.9712deg); -webkit-transform: translate3d(-7px, -13px, 0px) rotateZ(19.9712deg); -moz-transform: translate3d(-7px, -13px, 0px) rotateZ(19.9712deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 50% { transform: translate3d(-2px, -4px, 0px) rotateZ(-13.4055deg); -webkit-transform: translate3d(-2px, -4px, 0px) rotateZ(-13.4055deg); -moz-transform: translate3d(-2px, -4px, 0px) rotateZ(-13.4055deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 62.5% { transform: translate3d(-7px, -13px, 0px) rotateZ(19.9712deg); -webkit-transform: translate3d(-7px, -13px, 0px) rotateZ(19.9712deg); -moz-transform: translate3d(-7px, -13px, 0px) rotateZ(19.9712deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 75% { transform: translate3d(8px, -8px, 0px) rotateZ(-15.6332deg); -webkit-transform: translate3d(8px, -8px, 0px) rotateZ(-15.6332deg); -moz-transform: translate3d(8px, -8px, 0px) rotateZ(-15.6332deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 87.5% { transform: translate3d(-7px, -13px, 0px) rotateZ(19.9712deg); -webkit-transform: translate3d(-7px, -13px, 0px) rotateZ(19.9712deg); -moz-transform: translate3d(-7px, -13px, 0px) rotateZ(19.9712deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 100% { transform: translate3d(-2px, -4px, 0px) rotateZ(-13.4055deg); -webkit-transform: translate3d(-2px, -4px, 0px) rotateZ(-13.4055deg); -moz-transform: translate3d(-2px, -4px, 0px) rotateZ(-13.4055deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } }  @-webkit-keyframes gwd-gen-1pkcgwdanimation_gwd-keyframes { 0% { -webkit-transform: translate3d(-2px, -4px, 0px) rotateZ(-13.4055deg); -webkit-animation-timing-function: ease-in-out; } 12.5% { -webkit-transform: translate3d(-7px, -13px, 0px) rotateZ(19.9712deg); -webkit-animation-timing-function: ease-in-out; } 25% { -webkit-transform: translate3d(8px, -8px, 0px) rotateZ(-15.6332deg); -webkit-animation-timing-function: ease-in-out; } 37.5% { -webkit-transform: translate3d(-7px, -13px, 0px) rotateZ(19.9712deg); -webkit-animation-timing-function: ease-in-out; } 50% { -webkit-transform: translate3d(-2px, -4px, 0px) rotateZ(-13.4055deg); -webkit-animation-timing-function: ease-in-out; } 62.5% { -webkit-transform: translate3d(-7px, -13px, 0px) rotateZ(19.9712deg); -webkit-animation-timing-function: ease-in-out; } 75% { -webkit-transform: translate3d(8px, -8px, 0px) rotateZ(-15.6332deg); -webkit-animation-timing-function: ease-in-out; } 87.5% { -webkit-transform: translate3d(-7px, -13px, 0px) rotateZ(19.9712deg); -webkit-animation-timing-function: ease-in-out; } 100% { -webkit-transform: translate3d(-2px, -4px, 0px) rotateZ(-13.4055deg); -webkit-animation-timing-function: ease-in-out; } }  @-moz-keyframes gwd-gen-1pkcgwdanimation_gwd-keyframes { 0% { -moz-transform: translate3d(-2px, -4px, 0px) rotateZ(-13.4055deg); -moz-animation-timing-function: ease-in-out; } 12.5% { -moz-transform: translate3d(-7px, -13px, 0px) rotateZ(19.9712deg); -moz-animation-timing-function: ease-in-out; } 25% { -moz-transform: translate3d(8px, -8px, 0px) rotateZ(-15.6332deg); -moz-animation-timing-function: ease-in-out; } 37.5% { -moz-transform: translate3d(-7px, -13px, 0px) rotateZ(19.9712deg); -moz-animation-timing-function: ease-in-out; } 50% { -moz-transform: translate3d(-2px, -4px, 0px) rotateZ(-13.4055deg); -moz-animation-timing-function: ease-in-out; } 62.5% { -moz-transform: translate3d(-7px, -13px, 0px) rotateZ(19.9712deg); -moz-animation-timing-function: ease-in-out; } 75% { -moz-transform: translate3d(8px, -8px, 0px) rotateZ(-15.6332deg); -moz-animation-timing-function: ease-in-out; } 87.5% { -moz-transform: translate3d(-7px, -13px, 0px) rotateZ(19.9712deg); -moz-animation-timing-function: ease-in-out; } 100% { -moz-transform: translate3d(-2px, -4px, 0px) rotateZ(-13.4055deg); -moz-animation-timing-function: ease-in-out; } }  .htmlNoPages .gwd-gen-1pkcgwdanimation { animation: gwd-gen-1pkcgwdanimation_gwd-keyframes 4s linear 0s 1 normal forwards; -webkit-animation: gwd-gen-1pkcgwdanimation_gwd-keyframes 4s linear 0s 1 normal forwards; -moz-animation: gwd-gen-1pkcgwdanimation_gwd-keyframes 4s linear 0s 1 normal forwards; } .gwd-img-dbmx { position: absolute; width: 53px; height: 35px; transform-origin: 26.6072px 17.2079px 0px; -webkit-transform-origin: 26.6072px 17.2079px 0px; -moz-transform-origin: 26.6072px 17.2079px 0px; left: 652px; top: 326px; transform-style: preserve-3d; -webkit-transform-style: preserve-3d; -moz-transform-style: preserve-3d; transform: translate3d(-11px, -2px, 0px) rotateZ(2.45146deg); -webkit-transform: translate3d(-11px, -2px, 0px) rotateZ(2.45146deg); -moz-transform: translate3d(-11px, -2px, 0px) rotateZ(2.45146deg); }  @keyframes gwd-gen-vmmygwdanimation_gwd-keyframes { 0% { transform: translate3d(-11px, -2px, 0px) rotateZ(2.45146deg); -webkit-transform: translate3d(-11px, -2px, 0px) rotateZ(2.45146deg); -moz-transform: translate3d(-11px, -2px, 0px) rotateZ(2.45146deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 12.5% { transform: translate3d(2px, -6px, 0px) rotateZ(-10.562deg); -webkit-transform: translate3d(2px, -6px, 0px) rotateZ(-10.562deg); -moz-transform: translate3d(2px, -6px, 0px) rotateZ(-10.562deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 25% { transform: translate3d(-5px, 3px, 0px) rotateZ(9.20948deg); -webkit-transform: translate3d(-5px, 3px, 0px) rotateZ(9.20948deg); -moz-transform: translate3d(-5px, 3px, 0px) rotateZ(9.20948deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 37.5% { transform: translate3d(2px, -6px, 0px) rotateZ(-10.562deg); -webkit-transform: translate3d(2px, -6px, 0px) rotateZ(-10.562deg); -moz-transform: translate3d(2px, -6px, 0px) rotateZ(-10.562deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 50% { transform: translate3d(-11px, -2px, 0px) rotateZ(2.45146deg); -webkit-transform: translate3d(-11px, -2px, 0px) rotateZ(2.45146deg); -moz-transform: translate3d(-11px, -2px, 0px) rotateZ(2.45146deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 62.5% { transform: translate3d(2px, -6px, 0px) rotateZ(-10.562deg); -webkit-transform: translate3d(2px, -6px, 0px) rotateZ(-10.562deg); -moz-transform: translate3d(2px, -6px, 0px) rotateZ(-10.562deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 75% { transform: translate3d(-5px, 3px, 0px) rotateZ(9.20948deg); -webkit-transform: translate3d(-5px, 3px, 0px) rotateZ(9.20948deg); -moz-transform: translate3d(-5px, 3px, 0px) rotateZ(9.20948deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 87.5% { transform: translate3d(2px, -6px, 0px) rotateZ(-10.562deg); -webkit-transform: translate3d(2px, -6px, 0px) rotateZ(-10.562deg); -moz-transform: translate3d(2px, -6px, 0px) rotateZ(-10.562deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 100% { transform: translate3d(-11px, -2px, 0px) rotateZ(2.45146deg); -webkit-transform: translate3d(-11px, -2px, 0px) rotateZ(2.45146deg); -moz-transform: translate3d(-11px, -2px, 0px) rotateZ(2.45146deg); animation-timing-function: linear; -webkit-animation-timing-function: linear; -moz-animation-timing-function: linear; } }  @-webkit-keyframes gwd-gen-vmmygwdanimation_gwd-keyframes { 0% { -webkit-transform: translate3d(-11px, -2px, 0px) rotateZ(2.45146deg); -webkit-animation-timing-function: ease-in-out; } 12.5% { -webkit-transform: translate3d(2px, -6px, 0px) rotateZ(-10.562deg); -webkit-animation-timing-function: ease-in-out; } 25% { -webkit-transform: translate3d(-5px, 3px, 0px) rotateZ(9.20948deg); -webkit-animation-timing-function: ease-in-out; } 37.5% { -webkit-transform: translate3d(2px, -6px, 0px) rotateZ(-10.562deg); -webkit-animation-timing-function: ease-in-out; } 50% { -webkit-transform: translate3d(-11px, -2px, 0px) rotateZ(2.45146deg); -webkit-animation-timing-function: ease-in-out; } 62.5% { -webkit-transform: translate3d(2px, -6px, 0px) rotateZ(-10.562deg); -webkit-animation-timing-function: ease-in-out; } 75% { -webkit-transform: translate3d(-5px, 3px, 0px) rotateZ(9.20948deg); -webkit-animation-timing-function: ease-in-out; } 87.5% { -webkit-transform: translate3d(2px, -6px, 0px) rotateZ(-10.562deg); -webkit-animation-timing-function: ease-in-out; } 100% { -webkit-transform: translate3d(-11px, -2px, 0px) rotateZ(2.45146deg); -webkit-animation-timing-function: linear; } }  @-moz-keyframes gwd-gen-vmmygwdanimation_gwd-keyframes { 0% { -moz-transform: translate3d(-11px, -2px, 0px) rotateZ(2.45146deg); -moz-animation-timing-function: ease-in-out; } 12.5% { -moz-transform: translate3d(2px, -6px, 0px) rotateZ(-10.562deg); -moz-animation-timing-function: ease-in-out; } 25% { -moz-transform: translate3d(-5px, 3px, 0px) rotateZ(9.20948deg); -moz-animation-timing-function: ease-in-out; } 37.5% { -moz-transform: translate3d(2px, -6px, 0px) rotateZ(-10.562deg); -moz-animation-timing-function: ease-in-out; } 50% { -moz-transform: translate3d(-11px, -2px, 0px) rotateZ(2.45146deg); -moz-animation-timing-function: ease-in-out; } 62.5% { -moz-transform: translate3d(2px, -6px, 0px) rotateZ(-10.562deg); -moz-animation-timing-function: ease-in-out; } 75% { -moz-transform: translate3d(-5px, 3px, 0px) rotateZ(9.20948deg); -moz-animation-timing-function: ease-in-out; } 87.5% { -moz-transform: translate3d(2px, -6px, 0px) rotateZ(-10.562deg); -moz-animation-timing-function: ease-in-out; } 100% { -moz-transform: translate3d(-11px, -2px, 0px) rotateZ(2.45146deg); -moz-animation-timing-function: linear; } }  .htmlNoPages .gwd-gen-vmmygwdanimation { animation: gwd-gen-vmmygwdanimation_gwd-keyframes 4s linear 0s 1 normal forwards; -webkit-animation: gwd-gen-vmmygwdanimation_gwd-keyframes 4s linear 0s 1 normal forwards; -moz-animation: gwd-gen-vmmygwdanimation_gwd-keyframes 4s linear 0s 1 normal forwards; } </style> <style type="text/css" data-gwd-secondary-animations="" class="ng-scope"> .htmlNoPages.startLoop .gwd-gen-8i4vgwdanimation { animation: gwd-gen-8i4vgwdanimation_gwd-keyframes_startLoop 4s linear -2s 1 normal forwards; -webkit-animation: gwd-gen-8i4vgwdanimation_gwd-keyframes_startLoop 4s linear -2s 1 normal forwards; -moz-animation: gwd-gen-8i4vgwdanimation_gwd-keyframes_startLoop 4s linear -2s 1 normal forwards; }  @keyframes gwd-gen-8i4vgwdanimation_gwd-keyframes_startLoop { 0% { transform: translate3d(-59px, 65px, 0px) rotateZ(-12.6591deg); -webkit-transform: translate3d(-59px, 65px, 0px) rotateZ(-12.6591deg); -moz-transform: translate3d(-59px, 65px, 0px) rotateZ(-12.6591deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 25% { transform: translate3d(-57px, 45px, 0px) rotateZ(-25.5159deg); -webkit-transform: translate3d(-57px, 45px, 0px) rotateZ(-25.5159deg); -moz-transform: translate3d(-57px, 45px, 0px) rotateZ(-25.5159deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 50% { transform: translate3d(-59px, 65px, 0px) rotateZ(-12.6591deg); -webkit-transform: translate3d(-59px, 65px, 0px) rotateZ(-12.6591deg); -moz-transform: translate3d(-59px, 65px, 0px) rotateZ(-12.6591deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 75% { transform: translate3d(-57px, 45px, 0px) rotateZ(-25.5159deg); -webkit-transform: translate3d(-57px, 45px, 0px) rotateZ(-25.5159deg); -moz-transform: translate3d(-57px, 45px, 0px) rotateZ(-25.5159deg); animation-timing-function: linear; -webkit-animation-timing-function: linear; -moz-animation-timing-function: linear; } 100% { transform: translate3d(-59px, 65px, 0px) rotateZ(-12.6591deg); -webkit-transform: translate3d(-59px, 65px, 0px) rotateZ(-12.6591deg); -moz-transform: translate3d(-59px, 65px, 0px) rotateZ(-12.6591deg); animation-timing-function: linear; -webkit-animation-timing-function: linear; -moz-animation-timing-function: linear; } }  @-webkit-keyframes gwd-gen-8i4vgwdanimation_gwd-keyframes_startLoop { 0% { -webkit-transform: translate3d(-59px, 65px, 0px) rotateZ(-12.6591deg); -webkit-animation-timing-function: ease-in-out; } 25% { -webkit-transform: translate3d(-57px, 45px, 0px) rotateZ(-25.5159deg); -webkit-animation-timing-function: ease-in-out; } 50% { -webkit-transform: translate3d(-59px, 65px, 0px) rotateZ(-12.6591deg); -webkit-animation-timing-function: ease-in-out; } 75% { -webkit-transform: translate3d(-57px, 45px, 0px) rotateZ(-25.5159deg); -webkit-animation-timing-function: linear; } 100% { -webkit-transform: translate3d(-59px, 65px, 0px) rotateZ(-12.6591deg); -webkit-animation-timing-function: linear; } }  @-moz-keyframes gwd-gen-8i4vgwdanimation_gwd-keyframes_startLoop { 0% { -moz-transform: translate3d(-59px, 65px, 0px) rotateZ(-12.6591deg); -moz-animation-timing-function: ease-in-out; } 25% { -moz-transform: translate3d(-57px, 45px, 0px) rotateZ(-25.5159deg); -moz-animation-timing-function: ease-in-out; } 50% { -moz-transform: translate3d(-59px, 65px, 0px) rotateZ(-12.6591deg); -moz-animation-timing-function: ease-in-out; } 75% { -moz-transform: translate3d(-57px, 45px, 0px) rotateZ(-25.5159deg); -moz-animation-timing-function: linear; } 100% { -moz-transform: translate3d(-59px, 65px, 0px) rotateZ(-12.6591deg); -moz-animation-timing-function: linear; } }  .htmlNoPages.startLoop .gwd-gen-6y8ogwdanimation { animation: gwd-gen-6y8ogwdanimation_gwd-keyframes_startLoop 4s linear -2s 1 normal forwards; -webkit-animation: gwd-gen-6y8ogwdanimation_gwd-keyframes_startLoop 4s linear -2s 1 normal forwards; -moz-animation: gwd-gen-6y8ogwdanimation_gwd-keyframes_startLoop 4s linear -2s 1 normal forwards; }  @keyframes gwd-gen-6y8ogwdanimation_gwd-keyframes_startLoop { 0% { transform: translate3d(-22px, 87px, 0px) rotateZ(-50.1981deg); -webkit-transform: translate3d(-22px, 87px, 0px) rotateZ(-50.1981deg); -moz-transform: translate3d(-22px, 87px, 0px) rotateZ(-50.1981deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 25% { transform: translate3d(-20px, 48px, 0px) rotateZ(-0.657515deg); -webkit-transform: translate3d(-20px, 48px, 0px) rotateZ(-0.657515deg); -moz-transform: translate3d(-20px, 48px, 0px) rotateZ(-0.657515deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 50% { transform: translate3d(-22px, 87px, 0px) rotateZ(-50.1981deg); -webkit-transform: translate3d(-22px, 87px, 0px) rotateZ(-50.1981deg); -moz-transform: translate3d(-22px, 87px, 0px) rotateZ(-50.1981deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 75% { transform: translate3d(-20px, 48px, 0px) rotateZ(-0.657515deg); -webkit-transform: translate3d(-20px, 48px, 0px) rotateZ(-0.657515deg); -moz-transform: translate3d(-20px, 48px, 0px) rotateZ(-0.657515deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 100% { transform: translate3d(-22px, 87px, 0px) rotateZ(-50.1981deg); -webkit-transform: translate3d(-22px, 87px, 0px) rotateZ(-50.1981deg); -moz-transform: translate3d(-22px, 87px, 0px) rotateZ(-50.1981deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } }  @-webkit-keyframes gwd-gen-6y8ogwdanimation_gwd-keyframes_startLoop { 0% { -webkit-transform: translate3d(-22px, 87px, 0px) rotateZ(-50.1981deg); -webkit-animation-timing-function: ease-in-out; } 25% { -webkit-transform: translate3d(-20px, 48px, 0px) rotateZ(-0.657515deg); -webkit-animation-timing-function: ease-in-out; } 50% { -webkit-transform: translate3d(-22px, 87px, 0px) rotateZ(-50.1981deg); -webkit-animation-timing-function: ease-in-out; } 75% { -webkit-transform: translate3d(-20px, 48px, 0px) rotateZ(-0.657515deg); -webkit-animation-timing-function: ease-in-out; } 100% { -webkit-transform: translate3d(-22px, 87px, 0px) rotateZ(-50.1981deg); -webkit-animation-timing-function: ease-in-out; } }  @-moz-keyframes gwd-gen-6y8ogwdanimation_gwd-keyframes_startLoop { 0% { -moz-transform: translate3d(-22px, 87px, 0px) rotateZ(-50.1981deg); -moz-animation-timing-function: ease-in-out; } 25% { -moz-transform: translate3d(-20px, 48px, 0px) rotateZ(-0.657515deg); -moz-animation-timing-function: ease-in-out; } 50% { -moz-transform: translate3d(-22px, 87px, 0px) rotateZ(-50.1981deg); -moz-animation-timing-function: ease-in-out; } 75% { -moz-transform: translate3d(-20px, 48px, 0px) rotateZ(-0.657515deg); -moz-animation-timing-function: ease-in-out; } 100% { -moz-transform: translate3d(-22px, 87px, 0px) rotateZ(-50.1981deg); -moz-animation-timing-function: ease-in-out; } }  .htmlNoPages.startLoop .gwd-gen-1envgwdanimation { animation: gwd-gen-1envgwdanimation_gwd-keyframes_startLoop 4s linear -2s 1 normal forwards; -webkit-animation: gwd-gen-1envgwdanimation_gwd-keyframes_startLoop 4s linear -2s 1 normal forwards; -moz-animation: gwd-gen-1envgwdanimation_gwd-keyframes_startLoop 4s linear -2s 1 normal forwards; }  @keyframes gwd-gen-1envgwdanimation_gwd-keyframes_startLoop { 0% { transform: translate3d(-5px, 52px, 0px) rotateZ(-12.6591deg); -webkit-transform: translate3d(-5px, 52px, 0px) rotateZ(-12.6591deg); -moz-transform: translate3d(-5px, 52px, 0px) rotateZ(-12.6591deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 25% { transform: translate3d(-4px, 28px, 0px) rotateZ(-53.4676deg); -webkit-transform: translate3d(-4px, 28px, 0px) rotateZ(-53.4676deg); -moz-transform: translate3d(-4px, 28px, 0px) rotateZ(-53.4676deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 50% { transform: translate3d(-5px, 52px, 0px) rotateZ(-12.6591deg); -webkit-transform: translate3d(-5px, 52px, 0px) rotateZ(-12.6591deg); -moz-transform: translate3d(-5px, 52px, 0px) rotateZ(-12.6591deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 75% { transform: translate3d(-4px, 28px, 0px) rotateZ(-53.4676deg); -webkit-transform: translate3d(-4px, 28px, 0px) rotateZ(-53.4676deg); -moz-transform: translate3d(-4px, 28px, 0px) rotateZ(-53.4676deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 100% { transform: translate3d(-5px, 52px, 0px) rotateZ(-12.6591deg); -webkit-transform: translate3d(-5px, 52px, 0px) rotateZ(-12.6591deg); -moz-transform: translate3d(-5px, 52px, 0px) rotateZ(-12.6591deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } }  @-webkit-keyframes gwd-gen-1envgwdanimation_gwd-keyframes_startLoop { 0% { -webkit-transform: translate3d(-5px, 52px, 0px) rotateZ(-12.6591deg); -webkit-animation-timing-function: ease-in-out; } 25% { -webkit-transform: translate3d(-4px, 28px, 0px) rotateZ(-53.4676deg); -webkit-animation-timing-function: ease-in-out; } 50% { -webkit-transform: translate3d(-5px, 52px, 0px) rotateZ(-12.6591deg); -webkit-animation-timing-function: ease-in-out; } 75% { -webkit-transform: translate3d(-4px, 28px, 0px) rotateZ(-53.4676deg); -webkit-animation-timing-function: ease-in-out; } 100% { -webkit-transform: translate3d(-5px, 52px, 0px) rotateZ(-12.6591deg); -webkit-animation-timing-function: ease-in-out; } }  @-moz-keyframes gwd-gen-1envgwdanimation_gwd-keyframes_startLoop { 0% { -moz-transform: translate3d(-5px, 52px, 0px) rotateZ(-12.6591deg); -moz-animation-timing-function: ease-in-out; } 25% { -moz-transform: translate3d(-4px, 28px, 0px) rotateZ(-53.4676deg); -moz-animation-timing-function: ease-in-out; } 50% { -moz-transform: translate3d(-5px, 52px, 0px) rotateZ(-12.6591deg); -moz-animation-timing-function: ease-in-out; } 75% { -moz-transform: translate3d(-4px, 28px, 0px) rotateZ(-53.4676deg); -moz-animation-timing-function: ease-in-out; } 100% { -moz-transform: translate3d(-5px, 52px, 0px) rotateZ(-12.6591deg); -moz-animation-timing-function: ease-in-out; } }  .htmlNoPages.startLoop .gwd-gen-bvtjgwdanimation { animation: gwd-gen-bvtjgwdanimation_gwd-keyframes_startLoop 4s linear -2s 1 normal forwards; -webkit-animation: gwd-gen-bvtjgwdanimation_gwd-keyframes_startLoop 4s linear -2s 1 normal forwards; -moz-animation: gwd-gen-bvtjgwdanimation_gwd-keyframes_startLoop 4s linear -2s 1 normal forwards; }  @keyframes gwd-gen-bvtjgwdanimation_gwd-keyframes_startLoop { 0% { transform: translate3d(-44px, 44px, 0px) rotateZ(5.64294deg); -webkit-transform: translate3d(-44px, 44px, 0px) rotateZ(5.64294deg); -moz-transform: translate3d(-44px, 44px, 0px) rotateZ(5.64294deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 25% { transform: translate3d(-40px, 24px, 0px) rotateZ(-21.6107deg); -webkit-transform: translate3d(-40px, 24px, 0px) rotateZ(-21.6107deg); -moz-transform: translate3d(-40px, 24px, 0px) rotateZ(-21.6107deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 50% { transform: translate3d(-44px, 44px, 0px) rotateZ(5.64294deg); -webkit-transform: translate3d(-44px, 44px, 0px) rotateZ(5.64294deg); -moz-transform: translate3d(-44px, 44px, 0px) rotateZ(5.64294deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 75% { transform: translate3d(-40px, 24px, 0px) rotateZ(-21.6107deg); -webkit-transform: translate3d(-40px, 24px, 0px) rotateZ(-21.6107deg); -moz-transform: translate3d(-40px, 24px, 0px) rotateZ(-21.6107deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 100% { transform: translate3d(-44px, 44px, 0px) rotateZ(5.64294deg); -webkit-transform: translate3d(-44px, 44px, 0px) rotateZ(5.64294deg); -moz-transform: translate3d(-44px, 44px, 0px) rotateZ(5.64294deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } }  @-webkit-keyframes gwd-gen-bvtjgwdanimation_gwd-keyframes_startLoop { 0% { -webkit-transform: translate3d(-44px, 44px, 0px) rotateZ(5.64294deg); -webkit-animation-timing-function: ease-in-out; } 25% { -webkit-transform: translate3d(-40px, 24px, 0px) rotateZ(-21.6107deg); -webkit-animation-timing-function: ease-in-out; } 50% { -webkit-transform: translate3d(-44px, 44px, 0px) rotateZ(5.64294deg); -webkit-animation-timing-function: ease-in-out; } 75% { -webkit-transform: translate3d(-40px, 24px, 0px) rotateZ(-21.6107deg); -webkit-animation-timing-function: ease-in-out; } 100% { -webkit-transform: translate3d(-44px, 44px, 0px) rotateZ(5.64294deg); -webkit-animation-timing-function: ease-in-out; } }  @-moz-keyframes gwd-gen-bvtjgwdanimation_gwd-keyframes_startLoop { 0% { -moz-transform: translate3d(-44px, 44px, 0px) rotateZ(5.64294deg); -moz-animation-timing-function: ease-in-out; } 25% { -moz-transform: translate3d(-40px, 24px, 0px) rotateZ(-21.6107deg); -moz-animation-timing-function: ease-in-out; } 50% { -moz-transform: translate3d(-44px, 44px, 0px) rotateZ(5.64294deg); -moz-animation-timing-function: ease-in-out; } 75% { -moz-transform: translate3d(-40px, 24px, 0px) rotateZ(-21.6107deg); -moz-animation-timing-function: ease-in-out; } 100% { -moz-transform: translate3d(-44px, 44px, 0px) rotateZ(5.64294deg); -moz-animation-timing-function: ease-in-out; } }  .htmlNoPages.startLoop .gwd-gen-1gfxgwdanimation { animation: gwd-gen-1gfxgwdanimation_gwd-keyframes_startLoop 4s linear -2s 1 normal forwards; -webkit-animation: gwd-gen-1gfxgwdanimation_gwd-keyframes_startLoop 4s linear -2s 1 normal forwards; -moz-animation: gwd-gen-1gfxgwdanimation_gwd-keyframes_startLoop 4s linear -2s 1 normal forwards; }  @keyframes gwd-gen-1gfxgwdanimation_gwd-keyframes_startLoop { 0% { transform: translate3d(-16px, 76px, 0px) rotateZ(-12.6591deg); -webkit-transform: translate3d(-16px, 76px, 0px) rotateZ(-12.6591deg); -moz-transform: translate3d(-16px, 76px, 0px) rotateZ(-12.6591deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 25% { transform: translate3d(-16px, 56px, 0px) rotateZ(3.52379deg); -webkit-transform: translate3d(-16px, 56px, 0px) rotateZ(3.52379deg); -moz-transform: translate3d(-16px, 56px, 0px) rotateZ(3.52379deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 50% { transform: translate3d(-16px, 76px, 0px) rotateZ(-12.6591deg); -webkit-transform: translate3d(-16px, 76px, 0px) rotateZ(-12.6591deg); -moz-transform: translate3d(-16px, 76px, 0px) rotateZ(-12.6591deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 75% { transform: translate3d(-16px, 56px, 0px) rotateZ(3.52379deg); -webkit-transform: translate3d(-16px, 56px, 0px) rotateZ(3.52379deg); -moz-transform: translate3d(-16px, 56px, 0px) rotateZ(3.52379deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 100% { transform: translate3d(-16px, 76px, 0px) rotateZ(-12.6591deg); -webkit-transform: translate3d(-16px, 76px, 0px) rotateZ(-12.6591deg); -moz-transform: translate3d(-16px, 76px, 0px) rotateZ(-12.6591deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } }  @-webkit-keyframes gwd-gen-1gfxgwdanimation_gwd-keyframes_startLoop { 0% { -webkit-transform: translate3d(-16px, 76px, 0px) rotateZ(-12.6591deg); -webkit-animation-timing-function: ease-in-out; } 25% { -webkit-transform: translate3d(-16px, 56px, 0px) rotateZ(3.52379deg); -webkit-animation-timing-function: ease-in-out; } 50% { -webkit-transform: translate3d(-16px, 76px, 0px) rotateZ(-12.6591deg); -webkit-animation-timing-function: ease-in-out; } 75% { -webkit-transform: translate3d(-16px, 56px, 0px) rotateZ(3.52379deg); -webkit-animation-timing-function: ease-in-out; } 100% { -webkit-transform: translate3d(-16px, 76px, 0px) rotateZ(-12.6591deg); -webkit-animation-timing-function: ease-in-out; } }  @-moz-keyframes gwd-gen-1gfxgwdanimation_gwd-keyframes_startLoop { 0% { -moz-transform: translate3d(-16px, 76px, 0px) rotateZ(-12.6591deg); -moz-animation-timing-function: ease-in-out; } 25% { -moz-transform: translate3d(-16px, 56px, 0px) rotateZ(3.52379deg); -moz-animation-timing-function: ease-in-out; } 50% { -moz-transform: translate3d(-16px, 76px, 0px) rotateZ(-12.6591deg); -moz-animation-timing-function: ease-in-out; } 75% { -moz-transform: translate3d(-16px, 56px, 0px) rotateZ(3.52379deg); -moz-animation-timing-function: ease-in-out; } 100% { -moz-transform: translate3d(-16px, 76px, 0px) rotateZ(-12.6591deg); -moz-animation-timing-function: ease-in-out; } }  .htmlNoPages.startLoop .gwd-gen-1ba7gwdanimation { animation: gwd-gen-1ba7gwdanimation_gwd-keyframes_startLoop 4s linear -2s 1 normal forwards; -webkit-animation: gwd-gen-1ba7gwdanimation_gwd-keyframes_startLoop 4s linear -2s 1 normal forwards; -moz-animation: gwd-gen-1ba7gwdanimation_gwd-keyframes_startLoop 4s linear -2s 1 normal forwards; }  @keyframes gwd-gen-1ba7gwdanimation_gwd-keyframes_startLoop { 0% { transform: translate3d(-23px, 66px, 0px) rotateZ(-12.6591deg); -webkit-transform: translate3d(-23px, 66px, 0px) rotateZ(-12.6591deg); -moz-transform: translate3d(-23px, 66px, 0px) rotateZ(-12.6591deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 25% { transform: translate3d(-23px, 42px, 0px) rotateZ(4.29778deg); -webkit-transform: translate3d(-23px, 42px, 0px) rotateZ(4.29778deg); -moz-transform: translate3d(-23px, 42px, 0px) rotateZ(4.29778deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 50% { transform: translate3d(-23px, 66px, 0px) rotateZ(-12.6591deg); -webkit-transform: translate3d(-23px, 66px, 0px) rotateZ(-12.6591deg); -moz-transform: translate3d(-23px, 66px, 0px) rotateZ(-12.6591deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 75% { transform: translate3d(-23px, 42px, 0px) rotateZ(4.29778deg); -webkit-transform: translate3d(-23px, 42px, 0px) rotateZ(4.29778deg); -moz-transform: translate3d(-23px, 42px, 0px) rotateZ(4.29778deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 100% { transform: translate3d(-23px, 66px, 0px) rotateZ(-12.6591deg); -webkit-transform: translate3d(-23px, 66px, 0px) rotateZ(-12.6591deg); -moz-transform: translate3d(-23px, 66px, 0px) rotateZ(-12.6591deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } }  @-webkit-keyframes gwd-gen-1ba7gwdanimation_gwd-keyframes_startLoop { 0% { -webkit-transform: translate3d(-23px, 66px, 0px) rotateZ(-12.6591deg); -webkit-animation-timing-function: ease-in-out; } 25% { -webkit-transform: translate3d(-23px, 42px, 0px) rotateZ(4.29778deg); -webkit-animation-timing-function: ease-in-out; } 50% { -webkit-transform: translate3d(-23px, 66px, 0px) rotateZ(-12.6591deg); -webkit-animation-timing-function: ease-in-out; } 75% { -webkit-transform: translate3d(-23px, 42px, 0px) rotateZ(4.29778deg); -webkit-animation-timing-function: ease-in-out; } 100% { -webkit-transform: translate3d(-23px, 66px, 0px) rotateZ(-12.6591deg); -webkit-animation-timing-function: ease-in-out; } }  @-moz-keyframes gwd-gen-1ba7gwdanimation_gwd-keyframes_startLoop { 0% { -moz-transform: translate3d(-23px, 66px, 0px) rotateZ(-12.6591deg); -moz-animation-timing-function: ease-in-out; } 25% { -moz-transform: translate3d(-23px, 42px, 0px) rotateZ(4.29778deg); -moz-animation-timing-function: ease-in-out; } 50% { -moz-transform: translate3d(-23px, 66px, 0px) rotateZ(-12.6591deg); -moz-animation-timing-function: ease-in-out; } 75% { -moz-transform: translate3d(-23px, 42px, 0px) rotateZ(4.29778deg); -moz-animation-timing-function: ease-in-out; } 100% { -moz-transform: translate3d(-23px, 66px, 0px) rotateZ(-12.6591deg); -moz-animation-timing-function: ease-in-out; } }  .htmlNoPages.startLoop .gwd-gen-9o8ugwdanimation { animation: gwd-gen-9o8ugwdanimation_gwd-keyframes_startLoop 4s linear -2s 1 normal forwards; -webkit-animation: gwd-gen-9o8ugwdanimation_gwd-keyframes_startLoop 4s linear -2s 1 normal forwards; -moz-animation: gwd-gen-9o8ugwdanimation_gwd-keyframes_startLoop 4s linear -2s 1 normal forwards; }  @keyframes gwd-gen-9o8ugwdanimation_gwd-keyframes_startLoop { 0% { transform: translate3d(-59px, 65px, 0px) rotateZ(16.6863deg); -webkit-transform: translate3d(-59px, 65px, 0px) rotateZ(16.6863deg); -moz-transform: translate3d(-59px, 65px, 0px) rotateZ(16.6863deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 25% { transform: translate3d(-53px, 47px, 0px) rotateZ(-10.2542deg); -webkit-transform: translate3d(-53px, 47px, 0px) rotateZ(-10.2542deg); -moz-transform: translate3d(-53px, 47px, 0px) rotateZ(-10.2542deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 50% { transform: translate3d(-59px, 65px, 0px) rotateZ(16.6863deg); -webkit-transform: translate3d(-59px, 65px, 0px) rotateZ(16.6863deg); -moz-transform: translate3d(-59px, 65px, 0px) rotateZ(16.6863deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 75% { transform: translate3d(-53px, 47px, 0px) rotateZ(-10.2542deg); -webkit-transform: translate3d(-53px, 47px, 0px) rotateZ(-10.2542deg); -moz-transform: translate3d(-53px, 47px, 0px) rotateZ(-10.2542deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 100% { transform: translate3d(-59px, 65px, 0px) rotateZ(16.6863deg); -webkit-transform: translate3d(-59px, 65px, 0px) rotateZ(16.6863deg); -moz-transform: translate3d(-59px, 65px, 0px) rotateZ(16.6863deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } }  @-webkit-keyframes gwd-gen-9o8ugwdanimation_gwd-keyframes_startLoop { 0% { -webkit-transform: translate3d(-59px, 65px, 0px) rotateZ(16.6863deg); -webkit-animation-timing-function: ease-in-out; } 25% { -webkit-transform: translate3d(-53px, 47px, 0px) rotateZ(-10.2542deg); -webkit-animation-timing-function: ease-in-out; } 50% { -webkit-transform: translate3d(-59px, 65px, 0px) rotateZ(16.6863deg); -webkit-animation-timing-function: ease-in-out; } 75% { -webkit-transform: translate3d(-53px, 47px, 0px) rotateZ(-10.2542deg); -webkit-animation-timing-function: ease-in-out; } 100% { -webkit-transform: translate3d(-59px, 65px, 0px) rotateZ(16.6863deg); -webkit-animation-timing-function: ease-in-out; } }  @-moz-keyframes gwd-gen-9o8ugwdanimation_gwd-keyframes_startLoop { 0% { -moz-transform: translate3d(-59px, 65px, 0px) rotateZ(16.6863deg); -moz-animation-timing-function: ease-in-out; } 25% { -moz-transform: translate3d(-53px, 47px, 0px) rotateZ(-10.2542deg); -moz-animation-timing-function: ease-in-out; } 50% { -moz-transform: translate3d(-59px, 65px, 0px) rotateZ(16.6863deg); -moz-animation-timing-function: ease-in-out; } 75% { -moz-transform: translate3d(-53px, 47px, 0px) rotateZ(-10.2542deg); -moz-animation-timing-function: ease-in-out; } 100% { -moz-transform: translate3d(-59px, 65px, 0px) rotateZ(16.6863deg); -moz-animation-timing-function: ease-in-out; } }  .htmlNoPages.startLoop .gwd-gen-vmmygwdanimation { animation: gwd-gen-vmmygwdanimation_gwd-keyframes_startLoop 4s linear -2s 1 normal forwards; -webkit-animation: gwd-gen-vmmygwdanimation_gwd-keyframes_startLoop 4s linear -2s 1 normal forwards; -moz-animation: gwd-gen-vmmygwdanimation_gwd-keyframes_startLoop 4s linear -2s 1 normal forwards; }  @keyframes gwd-gen-vmmygwdanimation_gwd-keyframes_startLoop { 0% { transform: translate3d(-11px, -2px, 0px) rotateZ(2.45146deg); -webkit-transform: translate3d(-11px, -2px, 0px) rotateZ(2.45146deg); -moz-transform: translate3d(-11px, -2px, 0px) rotateZ(2.45146deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 12.5% { transform: translate3d(2px, -6px, 0px) rotateZ(-10.562deg); -webkit-transform: translate3d(2px, -6px, 0px) rotateZ(-10.562deg); -moz-transform: translate3d(2px, -6px, 0px) rotateZ(-10.562deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 25% { transform: translate3d(-5px, 3px, 0px) rotateZ(9.20948deg); -webkit-transform: translate3d(-5px, 3px, 0px) rotateZ(9.20948deg); -moz-transform: translate3d(-5px, 3px, 0px) rotateZ(9.20948deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 37.5% { transform: translate3d(2px, -6px, 0px) rotateZ(-10.562deg); -webkit-transform: translate3d(2px, -6px, 0px) rotateZ(-10.562deg); -moz-transform: translate3d(2px, -6px, 0px) rotateZ(-10.562deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 50% { transform: translate3d(-11px, -2px, 0px) rotateZ(2.45146deg); -webkit-transform: translate3d(-11px, -2px, 0px) rotateZ(2.45146deg); -moz-transform: translate3d(-11px, -2px, 0px) rotateZ(2.45146deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 62.5% { transform: translate3d(2px, -6px, 0px) rotateZ(-10.562deg); -webkit-transform: translate3d(2px, -6px, 0px) rotateZ(-10.562deg); -moz-transform: translate3d(2px, -6px, 0px) rotateZ(-10.562deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 75% { transform: translate3d(-5px, 3px, 0px) rotateZ(9.20948deg); -webkit-transform: translate3d(-5px, 3px, 0px) rotateZ(9.20948deg); -moz-transform: translate3d(-5px, 3px, 0px) rotateZ(9.20948deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 87.5% { transform: translate3d(2px, -6px, 0px) rotateZ(-10.562deg); -webkit-transform: translate3d(2px, -6px, 0px) rotateZ(-10.562deg); -moz-transform: translate3d(2px, -6px, 0px) rotateZ(-10.562deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 100% { transform: translate3d(-11px, -2px, 0px) rotateZ(2.45146deg); -webkit-transform: translate3d(-11px, -2px, 0px) rotateZ(2.45146deg); -moz-transform: translate3d(-11px, -2px, 0px) rotateZ(2.45146deg); animation-timing-function: linear; -webkit-animation-timing-function: linear; -moz-animation-timing-function: linear; } }  @-webkit-keyframes gwd-gen-vmmygwdanimation_gwd-keyframes_startLoop { 0% { -webkit-transform: translate3d(-11px, -2px, 0px) rotateZ(2.45146deg); -webkit-animation-timing-function: ease-in-out; } 12.5% { -webkit-transform: translate3d(2px, -6px, 0px) rotateZ(-10.562deg); -webkit-animation-timing-function: ease-in-out; } 25% { -webkit-transform: translate3d(-5px, 3px, 0px) rotateZ(9.20948deg); -webkit-animation-timing-function: ease-in-out; } 37.5% { -webkit-transform: translate3d(2px, -6px, 0px) rotateZ(-10.562deg); -webkit-animation-timing-function: ease-in-out; } 50% { -webkit-transform: translate3d(-11px, -2px, 0px) rotateZ(2.45146deg); -webkit-animation-timing-function: ease-in-out; } 62.5% { -webkit-transform: translate3d(2px, -6px, 0px) rotateZ(-10.562deg); -webkit-animation-timing-function: ease-in-out; } 75% { -webkit-transform: translate3d(-5px, 3px, 0px) rotateZ(9.20948deg); -webkit-animation-timing-function: ease-in-out; } 87.5% { -webkit-transform: translate3d(2px, -6px, 0px) rotateZ(-10.562deg); -webkit-animation-timing-function: ease-in-out; } 100% { -webkit-transform: translate3d(-11px, -2px, 0px) rotateZ(2.45146deg); -webkit-animation-timing-function: linear; } }  @-moz-keyframes gwd-gen-vmmygwdanimation_gwd-keyframes_startLoop { 0% { -moz-transform: translate3d(-11px, -2px, 0px) rotateZ(2.45146deg); -moz-animation-timing-function: ease-in-out; } 12.5% { -moz-transform: translate3d(2px, -6px, 0px) rotateZ(-10.562deg); -moz-animation-timing-function: ease-in-out; } 25% { -moz-transform: translate3d(-5px, 3px, 0px) rotateZ(9.20948deg); -moz-animation-timing-function: ease-in-out; } 37.5% { -moz-transform: translate3d(2px, -6px, 0px) rotateZ(-10.562deg); -moz-animation-timing-function: ease-in-out; } 50% { -moz-transform: translate3d(-11px, -2px, 0px) rotateZ(2.45146deg); -moz-animation-timing-function: ease-in-out; } 62.5% { -moz-transform: translate3d(2px, -6px, 0px) rotateZ(-10.562deg); -moz-animation-timing-function: ease-in-out; } 75% { -moz-transform: translate3d(-5px, 3px, 0px) rotateZ(9.20948deg); -moz-animation-timing-function: ease-in-out; } 87.5% { -moz-transform: translate3d(2px, -6px, 0px) rotateZ(-10.562deg); -moz-animation-timing-function: ease-in-out; } 100% { -moz-transform: translate3d(-11px, -2px, 0px) rotateZ(2.45146deg); -moz-animation-timing-function: linear; } }  .htmlNoPages.startLoop .gwd-gen-1pkcgwdanimation { animation: gwd-gen-1pkcgwdanimation_gwd-keyframes_startLoop 4s linear -2s 1 normal forwards; -webkit-animation: gwd-gen-1pkcgwdanimation_gwd-keyframes_startLoop 4s linear -2s 1 normal forwards; -moz-animation: gwd-gen-1pkcgwdanimation_gwd-keyframes_startLoop 4s linear -2s 1 normal forwards; }  @keyframes gwd-gen-1pkcgwdanimation_gwd-keyframes_startLoop { 0% { transform: translate3d(-2px, -4px, 0px) rotateZ(-13.4055deg); -webkit-transform: translate3d(-2px, -4px, 0px) rotateZ(-13.4055deg); -moz-transform: translate3d(-2px, -4px, 0px) rotateZ(-13.4055deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 12.5% { transform: translate3d(-7px, -13px, 0px) rotateZ(19.9712deg); -webkit-transform: translate3d(-7px, -13px, 0px) rotateZ(19.9712deg); -moz-transform: translate3d(-7px, -13px, 0px) rotateZ(19.9712deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 25% { transform: translate3d(8px, -8px, 0px) rotateZ(-15.6332deg); -webkit-transform: translate3d(8px, -8px, 0px) rotateZ(-15.6332deg); -moz-transform: translate3d(8px, -8px, 0px) rotateZ(-15.6332deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 37.5% { transform: translate3d(-7px, -13px, 0px) rotateZ(19.9712deg); -webkit-transform: translate3d(-7px, -13px, 0px) rotateZ(19.9712deg); -moz-transform: translate3d(-7px, -13px, 0px) rotateZ(19.9712deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 50% { transform: translate3d(-2px, -4px, 0px) rotateZ(-13.4055deg); -webkit-transform: translate3d(-2px, -4px, 0px) rotateZ(-13.4055deg); -moz-transform: translate3d(-2px, -4px, 0px) rotateZ(-13.4055deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 62.5% { transform: translate3d(-7px, -13px, 0px) rotateZ(19.9712deg); -webkit-transform: translate3d(-7px, -13px, 0px) rotateZ(19.9712deg); -moz-transform: translate3d(-7px, -13px, 0px) rotateZ(19.9712deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 75% { transform: translate3d(8px, -8px, 0px) rotateZ(-15.6332deg); -webkit-transform: translate3d(8px, -8px, 0px) rotateZ(-15.6332deg); -moz-transform: translate3d(8px, -8px, 0px) rotateZ(-15.6332deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 87.5% { transform: translate3d(-7px, -13px, 0px) rotateZ(19.9712deg); -webkit-transform: translate3d(-7px, -13px, 0px) rotateZ(19.9712deg); -moz-transform: translate3d(-7px, -13px, 0px) rotateZ(19.9712deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 100% { transform: translate3d(-2px, -4px, 0px) rotateZ(-13.4055deg); -webkit-transform: translate3d(-2px, -4px, 0px) rotateZ(-13.4055deg); -moz-transform: translate3d(-2px, -4px, 0px) rotateZ(-13.4055deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } }  @-webkit-keyframes gwd-gen-1pkcgwdanimation_gwd-keyframes_startLoop { 0% { -webkit-transform: translate3d(-2px, -4px, 0px) rotateZ(-13.4055deg); -webkit-animation-timing-function: ease-in-out; } 12.5% { -webkit-transform: translate3d(-7px, -13px, 0px) rotateZ(19.9712deg); -webkit-animation-timing-function: ease-in-out; } 25% { -webkit-transform: translate3d(8px, -8px, 0px) rotateZ(-15.6332deg); -webkit-animation-timing-function: ease-in-out; } 37.5% { -webkit-transform: translate3d(-7px, -13px, 0px) rotateZ(19.9712deg); -webkit-animation-timing-function: ease-in-out; } 50% { -webkit-transform: translate3d(-2px, -4px, 0px) rotateZ(-13.4055deg); -webkit-animation-timing-function: ease-in-out; } 62.5% { -webkit-transform: translate3d(-7px, -13px, 0px) rotateZ(19.9712deg); -webkit-animation-timing-function: ease-in-out; } 75% { -webkit-transform: translate3d(8px, -8px, 0px) rotateZ(-15.6332deg); -webkit-animation-timing-function: ease-in-out; } 87.5% { -webkit-transform: translate3d(-7px, -13px, 0px) rotateZ(19.9712deg); -webkit-animation-timing-function: ease-in-out; } 100% { -webkit-transform: translate3d(-2px, -4px, 0px) rotateZ(-13.4055deg); -webkit-animation-timing-function: ease-in-out; } }  @-moz-keyframes gwd-gen-1pkcgwdanimation_gwd-keyframes_startLoop { 0% { -moz-transform: translate3d(-2px, -4px, 0px) rotateZ(-13.4055deg); -moz-animation-timing-function: ease-in-out; } 12.5% { -moz-transform: translate3d(-7px, -13px, 0px) rotateZ(19.9712deg); -moz-animation-timing-function: ease-in-out; } 25% { -moz-transform: translate3d(8px, -8px, 0px) rotateZ(-15.6332deg); -moz-animation-timing-function: ease-in-out; } 37.5% { -moz-transform: translate3d(-7px, -13px, 0px) rotateZ(19.9712deg); -moz-animation-timing-function: ease-in-out; } 50% { -moz-transform: translate3d(-2px, -4px, 0px) rotateZ(-13.4055deg); -moz-animation-timing-function: ease-in-out; } 62.5% { -moz-transform: translate3d(-7px, -13px, 0px) rotateZ(19.9712deg); -moz-animation-timing-function: ease-in-out; } 75% { -moz-transform: translate3d(8px, -8px, 0px) rotateZ(-15.6332deg); -moz-animation-timing-function: ease-in-out; } 87.5% { -moz-transform: translate3d(-7px, -13px, 0px) rotateZ(19.9712deg); -moz-animation-timing-function: ease-in-out; } 100% { -moz-transform: translate3d(-2px, -4px, 0px) rotateZ(-13.4055deg); -moz-animation-timing-function: ease-in-out; } }  .htmlNoPages.startLoop .gwd-gen-139ngwdanimation { animation: gwd-gen-139ngwdanimation_gwd-keyframes_startLoop 4s linear -2s 1 normal forwards; -webkit-animation: gwd-gen-139ngwdanimation_gwd-keyframes_startLoop 4s linear -2s 1 normal forwards; -moz-animation: gwd-gen-139ngwdanimation_gwd-keyframes_startLoop 4s linear -2s 1 normal forwards; }  @keyframes gwd-gen-139ngwdanimation_gwd-keyframes_startLoop { 0% { transform: translate3d(4px, 27px, 0px) rotateZ(34.3748deg); -webkit-transform: translate3d(4px, 27px, 0px) rotateZ(34.3748deg); -moz-transform: translate3d(4px, 27px, 0px) rotateZ(34.3748deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 12.5% { transform: translate3d(-7px, -1px, 0px) rotateZ(8.88789deg); -webkit-transform: translate3d(-7px, -1px, 0px) rotateZ(8.88789deg); -moz-transform: translate3d(-7px, -1px, 0px) rotateZ(8.88789deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 25% { transform: translate3d(-17px, 4px, 0px) rotateZ(-2.69669deg); -webkit-transform: translate3d(-17px, 4px, 0px) rotateZ(-2.69669deg); -moz-transform: translate3d(-17px, 4px, 0px) rotateZ(-2.69669deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 37.5% { transform: translate3d(-7px, -1px, 0px) rotateZ(8.88789deg); -webkit-transform: translate3d(-7px, -1px, 0px) rotateZ(8.88789deg); -moz-transform: translate3d(-7px, -1px, 0px) rotateZ(8.88789deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 50% { transform: translate3d(4px, 27px, 0px) rotateZ(34.3748deg); -webkit-transform: translate3d(4px, 27px, 0px) rotateZ(34.3748deg); -moz-transform: translate3d(4px, 27px, 0px) rotateZ(34.3748deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 62.5% { transform: translate3d(-7px, -1px, 0px) rotateZ(8.88789deg); -webkit-transform: translate3d(-7px, -1px, 0px) rotateZ(8.88789deg); -moz-transform: translate3d(-7px, -1px, 0px) rotateZ(8.88789deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 75% { transform: translate3d(-17px, 4px, 0px) rotateZ(-2.69669deg); -webkit-transform: translate3d(-17px, 4px, 0px) rotateZ(-2.69669deg); -moz-transform: translate3d(-17px, 4px, 0px) rotateZ(-2.69669deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 87.5% { transform: translate3d(-7px, -1px, 0px) rotateZ(8.88789deg); -webkit-transform: translate3d(-7px, -1px, 0px) rotateZ(8.88789deg); -moz-transform: translate3d(-7px, -1px, 0px) rotateZ(8.88789deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 100% { transform: translate3d(4px, 27px, 0px) rotateZ(34.3748deg); -webkit-transform: translate3d(4px, 27px, 0px) rotateZ(34.3748deg); -moz-transform: translate3d(4px, 27px, 0px) rotateZ(34.3748deg); animation-timing-function: linear; -webkit-animation-timing-function: linear; -moz-animation-timing-function: linear; } }  @-webkit-keyframes gwd-gen-139ngwdanimation_gwd-keyframes_startLoop { 0% { -webkit-transform: translate3d(4px, 27px, 0px) rotateZ(34.3748deg); -webkit-animation-timing-function: ease-in-out; } 12.5% { -webkit-transform: translate3d(-7px, -1px, 0px) rotateZ(8.88789deg); -webkit-animation-timing-function: ease-in-out; } 25% { -webkit-transform: translate3d(-17px, 4px, 0px) rotateZ(-2.69669deg); -webkit-animation-timing-function: ease-in-out; } 37.5% { -webkit-transform: translate3d(-7px, -1px, 0px) rotateZ(8.88789deg); -webkit-animation-timing-function: ease-in-out; } 50% { -webkit-transform: translate3d(4px, 27px, 0px) rotateZ(34.3748deg); -webkit-animation-timing-function: ease-in-out; } 62.5% { -webkit-transform: translate3d(-7px, -1px, 0px) rotateZ(8.88789deg); -webkit-animation-timing-function: ease-in-out; } 75% { -webkit-transform: translate3d(-17px, 4px, 0px) rotateZ(-2.69669deg); -webkit-animation-timing-function: ease-in-out; } 87.5% { -webkit-transform: translate3d(-7px, -1px, 0px) rotateZ(8.88789deg); -webkit-animation-timing-function: ease-in-out; } 100% { -webkit-transform: translate3d(4px, 27px, 0px) rotateZ(34.3748deg); -webkit-animation-timing-function: linear; } }  @-moz-keyframes gwd-gen-139ngwdanimation_gwd-keyframes_startLoop { 0% { -moz-transform: translate3d(4px, 27px, 0px) rotateZ(34.3748deg); -moz-animation-timing-function: ease-in-out; } 12.5% { -moz-transform: translate3d(-7px, -1px, 0px) rotateZ(8.88789deg); -moz-animation-timing-function: ease-in-out; } 25% { -moz-transform: translate3d(-17px, 4px, 0px) rotateZ(-2.69669deg); -moz-animation-timing-function: ease-in-out; } 37.5% { -moz-transform: translate3d(-7px, -1px, 0px) rotateZ(8.88789deg); -moz-animation-timing-function: ease-in-out; } 50% { -moz-transform: translate3d(4px, 27px, 0px) rotateZ(34.3748deg); -moz-animation-timing-function: ease-in-out; } 62.5% { -moz-transform: translate3d(-7px, -1px, 0px) rotateZ(8.88789deg); -moz-animation-timing-function: ease-in-out; } 75% { -moz-transform: translate3d(-17px, 4px, 0px) rotateZ(-2.69669deg); -moz-animation-timing-function: ease-in-out; } 87.5% { -moz-transform: translate3d(-7px, -1px, 0px) rotateZ(8.88789deg); -moz-animation-timing-function: ease-in-out; } 100% { -moz-transform: translate3d(4px, 27px, 0px) rotateZ(34.3748deg); -moz-animation-timing-function: linear; } }  .htmlNoPages.startLoop .gwd-gen-1nx1gwdanimation { animation: gwd-gen-1nx1gwdanimation_gwd-keyframes_startLoop 4s linear -2s 1 normal forwards; -webkit-animation: gwd-gen-1nx1gwdanimation_gwd-keyframes_startLoop 4s linear -2s 1 normal forwards; -moz-animation: gwd-gen-1nx1gwdanimation_gwd-keyframes_startLoop 4s linear -2s 1 normal forwards; }  @keyframes gwd-gen-1nx1gwdanimation_gwd-keyframes_startLoop { 0% { transform: translate3d(15px, -6px, 0px) rotateZ(1.91837deg); -webkit-transform: translate3d(15px, -6px, 0px) rotateZ(1.91837deg); -moz-transform: translate3d(15px, -6px, 0px) rotateZ(1.91837deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 12.5% { transform: translate3d(-4px, -5px, 0px) rotateZ(-20.7177deg); -webkit-transform: translate3d(-4px, -5px, 0px) rotateZ(-20.7177deg); -moz-transform: translate3d(-4px, -5px, 0px) rotateZ(-20.7177deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 25% { transform: translate3d(-9px, 19px, 0px) rotateZ(-44.3443deg); -webkit-transform: translate3d(-9px, 19px, 0px) rotateZ(-44.3443deg); -moz-transform: translate3d(-9px, 19px, 0px) rotateZ(-44.3443deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 37.5% { transform: translate3d(-4px, -5px, 0px) rotateZ(-20.7177deg); -webkit-transform: translate3d(-4px, -5px, 0px) rotateZ(-20.7177deg); -moz-transform: translate3d(-4px, -5px, 0px) rotateZ(-20.7177deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 50% { transform: translate3d(15px, -6px, 0px) rotateZ(1.91837deg); -webkit-transform: translate3d(15px, -6px, 0px) rotateZ(1.91837deg); -moz-transform: translate3d(15px, -6px, 0px) rotateZ(1.91837deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 62.5% { transform: translate3d(-4px, -5px, 0px) rotateZ(-20.7177deg); -webkit-transform: translate3d(-4px, -5px, 0px) rotateZ(-20.7177deg); -moz-transform: translate3d(-4px, -5px, 0px) rotateZ(-20.7177deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 75% { transform: translate3d(-9px, 19px, 0px) rotateZ(-44.3443deg); -webkit-transform: translate3d(-9px, 19px, 0px) rotateZ(-44.3443deg); -moz-transform: translate3d(-9px, 19px, 0px) rotateZ(-44.3443deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 87.5% { transform: translate3d(-4px, -5px, 0px) rotateZ(-20.7177deg); -webkit-transform: translate3d(-4px, -5px, 0px) rotateZ(-20.7177deg); -moz-transform: translate3d(-4px, -5px, 0px) rotateZ(-20.7177deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 100% { transform: translate3d(15px, -6px, 0px) rotateZ(1.91837deg); -webkit-transform: translate3d(15px, -6px, 0px) rotateZ(1.91837deg); -moz-transform: translate3d(15px, -6px, 0px) rotateZ(1.91837deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } }  @-webkit-keyframes gwd-gen-1nx1gwdanimation_gwd-keyframes_startLoop { 0% { -webkit-transform: translate3d(15px, -6px, 0px) rotateZ(1.91837deg); -webkit-animation-timing-function: ease-in-out; } 12.5% { -webkit-transform: translate3d(-4px, -5px, 0px) rotateZ(-20.7177deg); -webkit-animation-timing-function: ease-in-out; } 25% { -webkit-transform: translate3d(-9px, 19px, 0px) rotateZ(-44.3443deg); -webkit-animation-timing-function: ease-in-out; } 37.5% { -webkit-transform: translate3d(-4px, -5px, 0px) rotateZ(-20.7177deg); -webkit-animation-timing-function: ease-in-out; } 50% { -webkit-transform: translate3d(15px, -6px, 0px) rotateZ(1.91837deg); -webkit-animation-timing-function: ease-in-out; } 62.5% { -webkit-transform: translate3d(-4px, -5px, 0px) rotateZ(-20.7177deg); -webkit-animation-timing-function: ease-in-out; } 75% { -webkit-transform: translate3d(-9px, 19px, 0px) rotateZ(-44.3443deg); -webkit-animation-timing-function: ease-in-out; } 87.5% { -webkit-transform: translate3d(-4px, -5px, 0px) rotateZ(-20.7177deg); -webkit-animation-timing-function: ease-in-out; } 100% { -webkit-transform: translate3d(15px, -6px, 0px) rotateZ(1.91837deg); -webkit-animation-timing-function: ease-in-out; } }  @-moz-keyframes gwd-gen-1nx1gwdanimation_gwd-keyframes_startLoop { 0% { -moz-transform: translate3d(15px, -6px, 0px) rotateZ(1.91837deg); -moz-animation-timing-function: ease-in-out; } 12.5% { -moz-transform: translate3d(-4px, -5px, 0px) rotateZ(-20.7177deg); -moz-animation-timing-function: ease-in-out; } 25% { -moz-transform: translate3d(-9px, 19px, 0px) rotateZ(-44.3443deg); -moz-animation-timing-function: ease-in-out; } 37.5% { -moz-transform: translate3d(-4px, -5px, 0px) rotateZ(-20.7177deg); -moz-animation-timing-function: ease-in-out; } 50% { -moz-transform: translate3d(15px, -6px, 0px) rotateZ(1.91837deg); -moz-animation-timing-function: ease-in-out; } 62.5% { -moz-transform: translate3d(-4px, -5px, 0px) rotateZ(-20.7177deg); -moz-animation-timing-function: ease-in-out; } 75% { -moz-transform: translate3d(-9px, 19px, 0px) rotateZ(-44.3443deg); -moz-animation-timing-function: ease-in-out; } 87.5% { -moz-transform: translate3d(-4px, -5px, 0px) rotateZ(-20.7177deg); -moz-animation-timing-function: ease-in-out; } 100% { -moz-transform: translate3d(15px, -6px, 0px) rotateZ(1.91837deg); -moz-animation-timing-function: ease-in-out; } }  .htmlNoPages.startLoop .gwd-gen-54q9gwdanimation { animation: gwd-gen-54q9gwdanimation_gwd-keyframes_startLoop 4s linear -2s 1 normal forwards; -webkit-animation: gwd-gen-54q9gwdanimation_gwd-keyframes_startLoop 4s linear -2s 1 normal forwards; -moz-animation: gwd-gen-54q9gwdanimation_gwd-keyframes_startLoop 4s linear -2s 1 normal forwards; }  @keyframes gwd-gen-54q9gwdanimation_gwd-keyframes_startLoop { 0% { transform: translate3d(10px, 0px, 0px) rotateZ(4.12183deg); -webkit-transform: translate3d(10px, 0px, 0px) rotateZ(4.12183deg); -moz-transform: translate3d(10px, 0px, 0px) rotateZ(4.12183deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 12.5% { transform: translate3d(5px, -11px, 0px) rotateZ(-0.36487deg); -webkit-transform: translate3d(5px, -11px, 0px) rotateZ(-0.36487deg); -moz-transform: translate3d(5px, -11px, 0px) rotateZ(-0.36487deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 25% { transform: translate3d(0px, 0px, 0px) rotateZ(-4.85157deg); -webkit-transform: translate3d(0px, 0px, 0px) rotateZ(-4.85157deg); -moz-transform: translate3d(0px, 0px, 0px) rotateZ(-4.85157deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 37.5% { transform: translate3d(5px, -11px, 0px) rotateZ(-0.36487deg); -webkit-transform: translate3d(5px, -11px, 0px) rotateZ(-0.36487deg); -moz-transform: translate3d(5px, -11px, 0px) rotateZ(-0.36487deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 50% { transform: translate3d(10px, 0px, 0px) rotateZ(4.12183deg); -webkit-transform: translate3d(10px, 0px, 0px) rotateZ(4.12183deg); -moz-transform: translate3d(10px, 0px, 0px) rotateZ(4.12183deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 62.5% { transform: translate3d(5px, -11px, 0px) rotateZ(-0.36487deg); -webkit-transform: translate3d(5px, -11px, 0px) rotateZ(-0.36487deg); -moz-transform: translate3d(5px, -11px, 0px) rotateZ(-0.36487deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 75% { transform: translate3d(0px, 0px, 0px) rotateZ(-4.85157deg); -webkit-transform: translate3d(0px, 0px, 0px) rotateZ(-4.85157deg); -moz-transform: translate3d(0px, 0px, 0px) rotateZ(-4.85157deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 87.5% { transform: translate3d(5px, -11px, 0px) rotateZ(-0.36487deg); -webkit-transform: translate3d(5px, -11px, 0px) rotateZ(-0.36487deg); -moz-transform: translate3d(5px, -11px, 0px) rotateZ(-0.36487deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 100% { transform: translate3d(10px, 0px, 0px) rotateZ(4.12183deg); -webkit-transform: translate3d(10px, 0px, 0px) rotateZ(4.12183deg); -moz-transform: translate3d(10px, 0px, 0px) rotateZ(4.12183deg); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } }  @-webkit-keyframes gwd-gen-54q9gwdanimation_gwd-keyframes_startLoop { 0% { -webkit-transform: translate3d(10px, 0px, 0px) rotateZ(4.12183deg); -webkit-animation-timing-function: ease-in-out; } 12.5% { -webkit-transform: translate3d(5px, -11px, 0px) rotateZ(-0.36487deg); -webkit-animation-timing-function: ease-in-out; } 25% { -webkit-transform: translate3d(0px, 0px, 0px) rotateZ(-4.85157deg); -webkit-animation-timing-function: ease-in-out; } 37.5% { -webkit-transform: translate3d(5px, -11px, 0px) rotateZ(-0.36487deg); -webkit-animation-timing-function: ease-in-out; } 50% { -webkit-transform: translate3d(10px, 0px, 0px) rotateZ(4.12183deg); -webkit-animation-timing-function: ease-in-out; } 62.5% { -webkit-transform: translate3d(5px, -11px, 0px) rotateZ(-0.36487deg); -webkit-animation-timing-function: ease-in-out; } 75% { -webkit-transform: translate3d(0px, 0px, 0px) rotateZ(-4.85157deg); -webkit-animation-timing-function: ease-in-out; } 87.5% { -webkit-transform: translate3d(5px, -11px, 0px) rotateZ(-0.36487deg); -webkit-animation-timing-function: ease-in-out; } 100% { -webkit-transform: translate3d(10px, 0px, 0px) rotateZ(4.12183deg); -webkit-animation-timing-function: ease-in-out; } }  @-moz-keyframes gwd-gen-54q9gwdanimation_gwd-keyframes_startLoop { 0% { -moz-transform: translate3d(10px, 0px, 0px) rotateZ(4.12183deg); -moz-animation-timing-function: ease-in-out; } 12.5% { -moz-transform: translate3d(5px, -11px, 0px) rotateZ(-0.36487deg); -moz-animation-timing-function: ease-in-out; } 25% { -moz-transform: translate3d(0px, 0px, 0px) rotateZ(-4.85157deg); -moz-animation-timing-function: ease-in-out; } 37.5% { -moz-transform: translate3d(5px, -11px, 0px) rotateZ(-0.36487deg); -moz-animation-timing-function: ease-in-out; } 50% { -moz-transform: translate3d(10px, 0px, 0px) rotateZ(4.12183deg); -moz-animation-timing-function: ease-in-out; } 62.5% { -moz-transform: translate3d(5px, -11px, 0px) rotateZ(-0.36487deg); -moz-animation-timing-function: ease-in-out; } 75% { -moz-transform: translate3d(0px, 0px, 0px) rotateZ(-4.85157deg); -moz-animation-timing-function: ease-in-out; } 87.5% { -moz-transform: translate3d(5px, -11px, 0px) rotateZ(-0.36487deg); -moz-animation-timing-function: ease-in-out; } 100% { -moz-transform: translate3d(10px, 0px, 0px) rotateZ(4.12183deg); -moz-animation-timing-function: ease-in-out; } }  .htmlNoPages.startLoop .gwd-gen-uaqqgwdanimation { animation: gwd-gen-uaqqgwdanimation_gwd-keyframes_startLoop 0.5s linear -0.5s 1 normal forwards; -webkit-animation: gwd-gen-uaqqgwdanimation_gwd-keyframes_startLoop 0.5s linear -0.5s 1 normal forwards; -moz-animation: gwd-gen-uaqqgwdanimation_gwd-keyframes_startLoop 0.5s linear -0.5s 1 normal forwards; }  @keyframes gwd-gen-uaqqgwdanimation_gwd-keyframes_startLoop { 0% { width: 362px; height: 114px; transform-origin: 180.277px 59.194px 0px; -webkit-transform-origin: 180.277px 59.194px 0px; -moz-transform-origin: 180.277px 59.194px 0px; transform: translate3d(57px, 18px, 0px); -webkit-transform: translate3d(57px, 18px, 0px); -moz-transform: translate3d(57px, 18px, 0px); opacity: 0; animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 100% { width: 474px; height: 156px; transform-origin: 236.889px 77.8445px 0px; -webkit-transform-origin: 236.889px 77.8445px 0px; -moz-transform-origin: 236.889px 77.8445px 0px; transform: translate3d(0px, 0px, 0px); -webkit-transform: translate3d(0px, 0px, 0px); -moz-transform: translate3d(0px, 0px, 0px); opacity: 1; animation-timing-function: linear; -webkit-animation-timing-function: linear; -moz-animation-timing-function: linear; } }  @-webkit-keyframes gwd-gen-uaqqgwdanimation_gwd-keyframes_startLoop { 0% { width: 362px; height: 114px; -webkit-transform-origin: 180.277px 59.194px 0px; -webkit-transform: translate3d(57px, 18px, 0px); opacity: 0; -webkit-animation-timing-function: ease-in-out; } 100% { width: 474px; height: 156px; -webkit-transform-origin: 236.889px 77.8445px 0px; -webkit-transform: translate3d(0px, 0px, 0px); opacity: 1; -webkit-animation-timing-function: linear; } }  @-moz-keyframes gwd-gen-uaqqgwdanimation_gwd-keyframes_startLoop { 0% { width: 362px; height: 114px; -moz-transform-origin: 180.277px 59.194px 0px; -moz-transform: translate3d(57px, 18px, 0px); opacity: 0; -moz-animation-timing-function: ease-in-out; } 100% { width: 474px; height: 156px; -moz-transform-origin: 236.889px 77.8445px 0px; -moz-transform: translate3d(0px, 0px, 0px); opacity: 1; -moz-animation-timing-function: linear; } }  .htmlNoPages.startLoop .gwd-gen-18sogwdanimation { animation: gwd-gen-18sogwdanimation_gwd-keyframes_startLoop 3s linear -1s 1 normal forwards; -webkit-animation: gwd-gen-18sogwdanimation_gwd-keyframes_startLoop 3s linear -1s 1 normal forwards; -moz-animation: gwd-gen-18sogwdanimation_gwd-keyframes_startLoop 3s linear -1s 1 normal forwards; }  @keyframes gwd-gen-18sogwdanimation_gwd-keyframes_startLoop { 0% { transform: translate3d(30px, 53px, 0px) rotateZ(-39.2821deg); -webkit-transform: translate3d(30px, 53px, 0px) rotateZ(-39.2821deg); -moz-transform: translate3d(30px, 53px, 0px) rotateZ(-39.2821deg); width: 81px; height: 131px; transform-origin: 40.8122px 65.2821px 0px; -webkit-transform-origin: 40.8122px 65.2821px 0px; -moz-transform-origin: 40.8122px 65.2821px 0px; opacity: 0; animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 33.33% { transform: translate3d(0px, 0px, 0px) rotateZ(-13.6211deg); -webkit-transform: translate3d(0px, 0px, 0px) rotateZ(-13.6211deg); -moz-transform: translate3d(0px, 0px, 0px) rotateZ(-13.6211deg); width: 98px; height: 159px; transform-origin: 49.2122px 79.344px 0px; -webkit-transform-origin: 49.2122px 79.344px 0px; -moz-transform-origin: 49.2122px 79.344px 0px; opacity: 1; animation-timing-function: linear; -webkit-animation-timing-function: linear; -moz-animation-timing-function: linear; } 66.67% { transform: translate3d(0px, 0px, 0px) rotateZ(-26.3514deg); -webkit-transform: translate3d(0px, 0px, 0px) rotateZ(-26.3514deg); -moz-transform: translate3d(0px, 0px, 0px) rotateZ(-26.3514deg); width: 98px; height: 159px; transform-origin: 49.2122px 79.344px 0px; -webkit-transform-origin: 49.2122px 79.344px 0px; -moz-transform-origin: 49.2122px 79.344px 0px; opacity: 1; animation-timing-function: linear; -webkit-animation-timing-function: linear; -moz-animation-timing-function: linear; } 100% { transform: translate3d(0px, 0px, 0px) rotateZ(-13.6211deg); -webkit-transform: translate3d(0px, 0px, 0px) rotateZ(-13.6211deg); -moz-transform: translate3d(0px, 0px, 0px) rotateZ(-13.6211deg); width: 98px; height: 159px; transform-origin: 49.2122px 79.344px 0px; -webkit-transform-origin: 49.2122px 79.344px 0px; -moz-transform-origin: 49.2122px 79.344px 0px; opacity: 1; animation-timing-function: linear; -webkit-animation-timing-function: linear; -moz-animation-timing-function: linear; } }  @-webkit-keyframes gwd-gen-18sogwdanimation_gwd-keyframes_startLoop { 0% { -webkit-transform: translate3d(30px, 53px, 0px) rotateZ(-39.2821deg); width: 81px; height: 131px; -webkit-transform-origin: 40.8122px 65.2821px 0px; opacity: 0; -webkit-animation-timing-function: ease-in-out; } 33.33% { -webkit-transform: translate3d(0px, 0px, 0px) rotateZ(-13.6211deg); width: 98px; height: 159px; -webkit-transform-origin: 49.2122px 79.344px 0px; opacity: 1; -webkit-animation-timing-function: linear; } 66.67% { -webkit-transform: translate3d(0px, 0px, 0px) rotateZ(-26.3514deg); width: 98px; height: 159px; -webkit-transform-origin: 49.2122px 79.344px 0px; opacity: 1; -webkit-animation-timing-function: linear; } 100% { -webkit-transform: translate3d(0px, 0px, 0px) rotateZ(-13.6211deg); width: 98px; height: 159px; -webkit-transform-origin: 49.2122px 79.344px 0px; opacity: 1; -webkit-animation-timing-function: linear; } }  @-moz-keyframes gwd-gen-18sogwdanimation_gwd-keyframes_startLoop { 0% { -moz-transform: translate3d(30px, 53px, 0px) rotateZ(-39.2821deg); width: 81px; height: 131px; -moz-transform-origin: 40.8122px 65.2821px 0px; opacity: 0; -moz-animation-timing-function: ease-in-out; } 33.33% { -moz-transform: translate3d(0px, 0px, 0px) rotateZ(-13.6211deg); width: 98px; height: 159px; -moz-transform-origin: 49.2122px 79.344px 0px; opacity: 1; -moz-animation-timing-function: linear; } 66.67% { -moz-transform: translate3d(0px, 0px, 0px) rotateZ(-26.3514deg); width: 98px; height: 159px; -moz-transform-origin: 49.2122px 79.344px 0px; opacity: 1; -moz-animation-timing-function: linear; } 100% { -moz-transform: translate3d(0px, 0px, 0px) rotateZ(-13.6211deg); width: 98px; height: 159px; -moz-transform-origin: 49.2122px 79.344px 0px; opacity: 1; -moz-animation-timing-function: linear; } }  .htmlNoPages.startLoop .gwd-gen-1rqogwdanimation { animation: gwd-gen-1rqogwdanimation_gwd-keyframes_startLoop 3s linear -1s 1 normal forwards; -webkit-animation: gwd-gen-1rqogwdanimation_gwd-keyframes_startLoop 3s linear -1s 1 normal forwards; -moz-animation: gwd-gen-1rqogwdanimation_gwd-keyframes_startLoop 3s linear -1s 1 normal forwards; }  @keyframes gwd-gen-1rqogwdanimation_gwd-keyframes_startLoop { 0% { transform: translate3d(440px, 53px, 0px) rotateZ(22.9471deg); -webkit-transform: translate3d(440px, 53px, 0px) rotateZ(22.9471deg); -moz-transform: translate3d(440px, 53px, 0px) rotateZ(22.9471deg); width: 81px; height: 131px; transform-origin: 40.8122px 65.2821px 0px; -webkit-transform-origin: 40.8122px 65.2821px 0px; -moz-transform-origin: 40.8122px 65.2821px 0px; opacity: 0; animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 33.33% { transform: translate3d(460px, 0px, 0px) rotateZ(12.0158deg); -webkit-transform: translate3d(460px, 0px, 0px) rotateZ(12.0158deg); -moz-transform: translate3d(460px, 0px, 0px) rotateZ(12.0158deg); width: 98px; height: 159px; transform-origin: 49.2122px 79.344px 0px; -webkit-transform-origin: 49.2122px 79.344px 0px; -moz-transform-origin: 49.2122px 79.344px 0px; opacity: 1; animation-timing-function: linear; -webkit-animation-timing-function: linear; -moz-animation-timing-function: linear; } 66.67% { transform: translate3d(460px, 0px, 0px) rotateZ(30.7723deg); -webkit-transform: translate3d(460px, 0px, 0px) rotateZ(30.7723deg); -moz-transform: translate3d(460px, 0px, 0px) rotateZ(30.7723deg); width: 98px; height: 159px; transform-origin: 49.2122px 79.344px 0px; -webkit-transform-origin: 49.2122px 79.344px 0px; -moz-transform-origin: 49.2122px 79.344px 0px; opacity: 1; animation-timing-function: linear; -webkit-animation-timing-function: linear; -moz-animation-timing-function: linear; } 100% { transform: translate3d(460px, 0px, 0px) rotateZ(12.0158deg); -webkit-transform: translate3d(460px, 0px, 0px) rotateZ(12.0158deg); -moz-transform: translate3d(460px, 0px, 0px) rotateZ(12.0158deg); width: 98px; height: 159px; transform-origin: 49.2122px 79.344px 0px; -webkit-transform-origin: 49.2122px 79.344px 0px; -moz-transform-origin: 49.2122px 79.344px 0px; opacity: 1; animation-timing-function: linear; -webkit-animation-timing-function: linear; -moz-animation-timing-function: linear; } }  @-webkit-keyframes gwd-gen-1rqogwdanimation_gwd-keyframes_startLoop { 0% { -webkit-transform: translate3d(440px, 53px, 0px) rotateZ(22.9471deg); width: 81px; height: 131px; -webkit-transform-origin: 40.8122px 65.2821px 0px; opacity: 0; -webkit-animation-timing-function: ease-in-out; } 33.33% { -webkit-transform: translate3d(460px, 0px, 0px) rotateZ(12.0158deg); width: 98px; height: 159px; -webkit-transform-origin: 49.2122px 79.344px 0px; opacity: 1; -webkit-animation-timing-function: linear; } 66.67% { -webkit-transform: translate3d(460px, 0px, 0px) rotateZ(30.7723deg); width: 98px; height: 159px; -webkit-transform-origin: 49.2122px 79.344px 0px; opacity: 1; -webkit-animation-timing-function: linear; } 100% { -webkit-transform: translate3d(460px, 0px, 0px) rotateZ(12.0158deg); width: 98px; height: 159px; -webkit-transform-origin: 49.2122px 79.344px 0px; opacity: 1; -webkit-animation-timing-function: linear; } }  @-moz-keyframes gwd-gen-1rqogwdanimation_gwd-keyframes_startLoop { 0% { -moz-transform: translate3d(440px, 53px, 0px) rotateZ(22.9471deg); width: 81px; height: 131px; -moz-transform-origin: 40.8122px 65.2821px 0px; opacity: 0; -moz-animation-timing-function: ease-in-out; } 33.33% { -moz-transform: translate3d(460px, 0px, 0px) rotateZ(12.0158deg); width: 98px; height: 159px; -moz-transform-origin: 49.2122px 79.344px 0px; opacity: 1; -moz-animation-timing-function: linear; } 66.67% { -moz-transform: translate3d(460px, 0px, 0px) rotateZ(30.7723deg); width: 98px; height: 159px; -moz-transform-origin: 49.2122px 79.344px 0px; opacity: 1; -moz-animation-timing-function: linear; } 100% { -moz-transform: translate3d(460px, 0px, 0px) rotateZ(12.0158deg); width: 98px; height: 159px; -moz-transform-origin: 49.2122px 79.344px 0px; opacity: 1; -moz-animation-timing-function: linear; } }  .htmlNoPages.startLoop .gwd-gen-1pawgwdanimation { animation: gwd-gen-1pawgwdanimation_gwd-keyframes_startLoop 0.5s linear -0.5s 1 normal forwards; -webkit-animation: gwd-gen-1pawgwdanimation_gwd-keyframes_startLoop 0.5s linear -0.5s 1 normal forwards; -moz-animation: gwd-gen-1pawgwdanimation_gwd-keyframes_startLoop 0.5s linear -0.5s 1 normal forwards; }  @keyframes gwd-gen-1pawgwdanimation_gwd-keyframes_startLoop { 0% { opacity: 0; width: 415px; height: 120px; transform-origin: 207.177px 57.9223px 0px; -webkit-transform-origin: 207.177px 57.9223px 0px; -moz-transform-origin: 207.177px 57.9223px 0px; transform: translate3d(-122px, -35px, 0px); -webkit-transform: translate3d(-122px, -35px, 0px); -moz-transform: translate3d(-122px, -35px, 0px); animation-timing-function: ease-in-out; -webkit-animation-timing-function: ease-in-out; -moz-animation-timing-function: ease-in-out; } 100% { opacity: 1; width: 438px; height: 128px; transform-origin: 218.392px 63.4323px 0px; -webkit-transform-origin: 218.392px 63.4323px 0px; -moz-transform-origin: 218.392px 63.4323px 0px; transform: translate3d(-135px, -39px, 0px); -webkit-transform: translate3d(-135px, -39px, 0px); -moz-transform: translate3d(-135px, -39px, 0px); animation-timing-function: linear; -webkit-animation-timing-function: linear; -moz-animation-timing-function: linear; } }  @-webkit-keyframes gwd-gen-1pawgwdanimation_gwd-keyframes_startLoop { 0% { opacity: 0; width: 415px; height: 120px; -webkit-transform-origin: 207.177px 57.9223px 0px; -webkit-transform: translate3d(-122px, -35px, 0px); -webkit-animation-timing-function: ease-in-out; } 100% { opacity: 1; width: 438px; height: 128px; -webkit-transform-origin: 218.392px 63.4323px 0px; -webkit-transform: translate3d(-135px, -39px, 0px); -webkit-animation-timing-function: linear; } }  @-moz-keyframes gwd-gen-1pawgwdanimation_gwd-keyframes_startLoop { 0% { opacity: 0; width: 415px; height: 120px; -moz-transform-origin: 207.177px 57.9223px 0px; -moz-transform: translate3d(-122px, -35px, 0px); -moz-animation-timing-function: ease-in-out; } 100% { opacity: 1; width: 438px; height: 128px; -moz-transform-origin: 218.392px 63.4323px 0px; -moz-transform: translate3d(-135px, -39px, 0px); -moz-animation-timing-function: linear; } }  .htmlNoPages .event-1-animation { animation: gwd-empty-animation 4s linear 0s 1 normal forwards; -webkit-animation: gwd-empty-animation 4s linear 0s 1 normal forwards; -moz-animation: gwd-empty-animation 4s linear 0s 1 normal forwards; }  @keyframes gwd-empty-animation { 0% { opacity: 0.001; } 100% { opacity: 0; } }  @-webkit-keyframes gwd-empty-animation { 0% { opacity: 0.001; } 100% { opacity: 0; } }  @-moz-keyframes gwd-empty-animation { 0% { opacity: 0.001; } 100% { opacity: 0; } }  .htmlNoPages.startLoop .event-1-animation { animation: gwd-empty-animation_startLoop 4s linear -2s 1 normal forwards; -webkit-animation: gwd-empty-animation_startLoop 4s linear -2s 1 normal forwards; -moz-animation: gwd-empty-animation_startLoop 4s linear -2s 1 normal forwards; }  @keyframes gwd-empty-animation_startLoop { 0% { opacity: 0.001; } 100% { opacity: 0; } }  @-webkit-keyframes gwd-empty-animation_startLoop { 0% { opacity: 0.001; } 100% { opacity: 0; } }  @-moz-keyframes gwd-empty-animation_startLoop { 0% { opacity: 0.001; } 100% { opacity: 0; } }
                                    </style>
                                    <script type="text/javascript" gwd-events="support" class="ng-scope">var gwd = gwd || {}; gwd.actions = gwd.actions || {}; gwd.actions.events = gwd.actions.events || {}; gwd.actions.events.getElementById = function (id) { var element = document.getElementById(id); if (!element) { var pageDeck = document.querySelector("[is=gwd-pagedeck]"); if (pageDeck) { if (typeof pageDeck.getElementById === "function") { element = pageDeck.getElementById(id) } } } if (!element) { switch (id) { case "document.body": element = document.body; break; case "document": element = document; break; case "window": element = window; break; default: break } } return element }; gwd.actions.events.addHandler = function (eventTarget, eventName, eventHandler, useCapture) { var targetElement = gwd.actions.events.getElementById(eventTarget); if (targetElement) { targetElement.addEventListener(eventName, eventHandler, useCapture) } }; gwd.actions.events.removeHandler = function (eventTarget, eventName, eventHandler, useCapture) { var targetElement = gwd.actions.events.getElementById(eventTarget); if (targetElement) { targetElement.removeEventListener(eventName, eventHandler, useCapture) } }; gwd.actions.events.setInlineStyle = function (id, styles) { var element = gwd.actions.events.getElementById(id); if (!element || !styles) { return } var transitionProperty = element.style.transition !== undefined ? "transition" : "-webkit-transition"; var prevTransition = element.style[transitionProperty]; var splitStyles = styles.split(/\s*;\s*/); var nameValue; splitStyles.forEach(function (splitStyle) { if (splitStyle) { var regex = new RegExp("[:](?![/]{2})"); nameValue = splitStyle.split(regex); nameValue[1] = nameValue[1] ? nameValue[1].trim() : null; if (!(nameValue[0] && nameValue[1])) { return } element.style.setProperty(nameValue[0], nameValue[1]) } }); function restoreTransition(event) { var el = event.target; el.style.transition = prevTransition; el.removeEventListener(event.type, restoreTransition, false) } element.addEventListener("transitionend", restoreTransition, false); element.addEventListener("webkitTransitionEnd", restoreTransition, false) }; gwd.actions.timeline = gwd.actions.timeline || {}; gwd.actions.timeline.dispatchTimedEvent = function (event) { var customEventTarget = event.target; if (customEventTarget) { var customEventName = customEventTarget.getAttribute("data-event-name"); if (customEventName) { event.stopPropagation(); var event = document.createEvent("CustomEvent"); event.initCustomEvent(customEventName, true, true, null); customEventTarget.dispatchEvent(event) } } }; gwd.actions.timeline.captureAnimationEnd = function (element) { if (!element) { return } var animationEndEvents = ["animationend", "webkitAnimationEnd"]; for (var i = 0; i < animationEndEvents.length; i++) { element.addEventListener(animationEndEvents[i], gwd.actions.timeline.dispatchTimedEvent, true) } }; gwd.actions.timeline.releaseAnimationEnd = function (element) { if (!element) { return } var animationEndEvents = ["animationend", "webkitAnimationEnd"]; for (var i = 0; i < animationEndEvents.length; i++) { element.removeEventListener(animationEndEvents[i], gwd.actions.timeline.dispatchTimedEvent, true) } }; gwd.actions.timeline.pauseAnimationClassName = "gwd-pause-animation"; gwd.actions.timeline.CURRENT_LABEL_ANIMATION = "data-gwd-current-label"; gwd.actions.timeline.reflow = function (el) { el.offsetWidth = el.offsetWidth }; gwd.actions.timeline.pause = function (id) { var el = gwd.actions.events.getElementById(id); el && el.classList && el.classList.add(gwd.actions.timeline.pauseAnimationClassName) }; gwd.actions.timeline.play = function (id) { var el = gwd.actions.events.getElementById(id); el && el.classList && el.classList.remove(gwd.actions.timeline.pauseAnimationClassName) }; gwd.actions.timeline.togglePlay = function (id) { var el = gwd.actions.events.getElementById(id); el && el.classList && el.classList.toggle(gwd.actions.timeline.pauseAnimationClassName) }; gwd.actions.timeline.gotoAndPlay = function (id, animClass) { var el = gwd.actions.events.getElementById(id); if (!(el && el.classList && id && animClass)) { return false } var currentLabelAnimClass = el.getAttribute(gwd.actions.timeline.CURRENT_LABEL_ANIMATION); if (currentLabelAnimClass) { el.classList.remove(currentLabelAnimClass); el.removeAttribute(gwd.actions.timeline.CURRENT_LABEL_ANIMATION) } gwd.actions.timeline.play(id); if (currentLabelAnimClass == animClass) { gwd.actions.timeline.reflow(el) } el.classList.add(animClass); el.setAttribute(gwd.actions.timeline.CURRENT_LABEL_ANIMATION, animClass); return true }; gwd.actions.timeline.gotoAndPause = function (id, animClass) { var el = gwd.actions.events.getElementById(id); if (!(el && el.classList)) { return false } if (gwd.actions.timeline.gotoAndPlay(id, animClass)) { var timeoutId = window.setTimeout(function () { el.classList.add(gwd.actions.timeline.pauseAnimationClassName) }, 40) } return !!timeoutId }; gwd.actions.timeline.gotoAndPlayNTimes = function (id, animClass, count, eventName) { var el = gwd.actions.events.getElementById(id); el.gwdGotoCounters = el.gwdGotoCounters || {}; var counters = el.gwdGotoCounters; var counterName = eventName + "_" + animClass + "_counter"; if (typeof counters[counterName] == "undefined") { counters[counterName] = 0 } if (counters[counterName] < count) { gwd.actions.timeline.gotoAndPlay(id, animClass) } counters[counterName]++ }</script>
                                    <script type="text/javascript" gwd-events="handlers" class="ng-scope">gwd.auto_BodyEvent_1 = function (event) { gwd.actions.timeline.gotoAndPlay('animation01', 'startLoop'); };</script>
                                    <script type="text/javascript" gwd-events="registration" class="ng-scope">gwd.actions.events.registerEventHandlers = function (event) { gwd.actions.events.addHandler('animation01', 'event-1', gwd.auto_BodyEvent_1, false); gwd.actions.timeline.captureAnimationEnd(document.getElementById("animation01")); }; gwd.actions.events.registerEventHandlers();</script>

                                    <div id="animation01" class="htmlNoPages ng-scope startLoop" data-gwd-current-label="startLoop">
                                        <img src="/guest/animation/wing1.png" class="gwd-img-1ibe gwd-gen-8i4vgwdanimation" id="wing">
                                        <img src="/guest/animation/hand1.png" class="gwd-img-14ir gwd-gen-6y8ogwdanimation" id="hand1L">
                                        <img src="/guest/animation/shoesL.png" class="gwd-img-h3zf gwd-gen-1envgwdanimation" id="foot1R">
                                        <img src="/guest/animation/shoesR.png" class="gwd-img-1kkx gwd-gen-bvtjgwdanimation" id="foot1L">
                                        <img src="/guest/animation/body.png" class="gwd-img-a103 gwd-gen-1gfxgwdanimation" id="body">
                                        <img src="/guest/animation/eyes1.png" class="gwd-img-1c6q gwd-gen-1ba7gwdanimation" id="eyes1">
                                        <img src="/guest/animation/hand2.png" class="gwd-img-1bko gwd-gen-9o8ugwdanimation" id="hand1R">
                                        <!--<svg data-gwd-shape="rectangle" class="gwd-rect-10tm"></svg>-->
                                        <img src="/guest/animation/wings-sempi1.png" class="gwd-img-1lq4" id="wing_sempi">
                                        <img src="/guest/animation/foot_sempi1-flipped.png" class="gwd-img-dbmx gwd-gen-vmmygwdanimation" id="foot_sempiL">
                                        <img src="/guest/animation/foot_sempi1.png" class="gwd-img-1khb gwd-gen-1pkcgwdanimation" id="foot_sempiR">
                                        <img src="/guest/animation/hand_sempi1_2.png" class="gwd-img-165o gwd-gen-139ngwdanimation" id="hand_sempiR">
                                        <img src="/guest/animation/hand_sempi1L.png" class="gwd-img-5v80 gwd-gen-1nx1gwdanimation" id="hand_sempiL">
                                        <img src="/guest/animation/body_sempi.png" class="gwd-img-103r gwd-gen-54q9gwdanimation" id="body_sempi">
                                        <img src="/guest/animation/base-logo_4.png" class="gwd-img-6jny gwd-gen-uaqqgwdanimation" id="base-logo">
                                        <img src="/guest/animation/logo-wingL_2.png" class="gwd-img-1d1s gwd-gen-18sogwdanimation" id="wing1">
                                        <img src="/guest/animation/logo-wingR_2.png" class="gwd-img-1d1s gwd-gen-1rqogwdanimation gwd-img-18go" id="wing1_1">
                                        <img src="/guest/animation/logo_1.png" class="gwd-img-el9e gwd-gen-1pawgwdanimation" id="logo">
                                        <div class="gwd-animation-event event-1-animation" data-event-name="event-1" data-event-time="4000"></div>
                                    </div>

                                    <style class="ng-scope">
                                        /*ANIMATION*/
                                        #animation01.htmlNoPages { position: relative; width: 800px; height: 400px; display: block; margin: 0 auto; margin-top:-5%; -webkit-transform-origin: 0 0; transform-origin: 0 0; }

                                        @media (max-width: 1199px) {
                                            #animation01.htmlNoPages { -webkit-transform: scale(.6); transform: scale(.6); width:480px; height:240px; }
                                        }

                                        @media (max-width: 767px) {
                                            #animation01.htmlNoPages { -webkit-transform: scale(.4); transform: scale(.4); width:320px; height:160px; }
                                        }
                                    </style>
                                </ng-include>
                            </div>
                        </header></ng-include></td>
            </tr>
            <tr>
                <td valign="top"><!-- ngView: --><main ng-view="" class="ng-scope" style=""><div class="container ng-scope">
                            <section class="box text-center">
                                <h1>Galeri</h1>
                                <br>
                                <!-- ngIf: globalLoading -->
                                <div class="articles row">
                                    <!-- ngRepeat: data in datas | itemsPerPage:12 --><div class="col-sm-4 ng-scope" ng-repeat="data in datas | itemsPerPage:12" style="">
                                        <a ng-href="/article/meeting-director-jabodetabek" href="/article/meeting-director-jabodetabek">
                                            <div class="img-square" style="background-image:url(/uploads/events/28/direktor.jpg)"></div>
                                            <div><small class="ng-binding"><i class="fa fa-calendar"></i> Aug 8, 2018</small></div>
                                            <h4 class="truncate center-block ng-binding">Meeting Director Jabodetabek</h4>
                                        </a>
                                    </div><!-- end ngRepeat: data in datas | itemsPerPage:12 --><div class="col-sm-4 ng-scope" ng-repeat="data in datas | itemsPerPage:12">
                                        <a ng-href="/article/kunjungan-isdf" href="/article/kunjungan-isdf">
                                            <div class="img-square" style="background-image:url(/uploads/events/25/1.jpg)"></div>
                                            <div><small class="ng-binding"><i class="fa fa-calendar"></i> Jul 30, 2018</small></div>
                                            <h4 class="truncate center-block ng-binding">Kunjungan Internasional Soroban Difusion Foundation (ISDF)</h4>
                                        </a>
                                    </div><!-- end ngRepeat: data in datas | itemsPerPage:12 --><div class="col-sm-4 ng-scope" ng-repeat="data in datas | itemsPerPage:12">
                                        <a ng-href="/article/kunjungan-team-waama-ke-ibo-jabar" href="/article/kunjungan-team-waama-ke-ibo-jabar">
                                            <div class="img-square" style="background-image:url(/uploads/events/24/1.jpg)"></div>
                                            <div><small class="ng-binding"><i class="fa fa-calendar"></i> Jul 29, 2018</small></div>
                                            <h4 class="truncate center-block ng-binding">Kunjungan Team WAAMA ke IBO JABAR</h4>
                                        </a>
                                    </div><!-- end ngRepeat: data in datas | itemsPerPage:12 --><div class="col-sm-4 ng-scope" ng-repeat="data in datas | itemsPerPage:12">
                                        <a ng-href="/article/waama-di-bandung" href="/article/waama-di-bandung">
                                            <div class="img-square" style="background-image:url(/uploads/events/27/1.jpg)"></div>
                                            <div><small class="ng-binding"><i class="fa fa-calendar"></i> Jul 28, 2018</small></div>
                                            <h4 class="truncate center-block ng-binding">WAAMA di Bandung 28 Juli 2018</h4>
                                        </a>
                                    </div><!-- end ngRepeat: data in datas | itemsPerPage:12 --><div class="col-sm-4 ng-scope" ng-repeat="data in datas | itemsPerPage:12">
                                        <a ng-href="/article/kunjungan-tcoc" href="/article/kunjungan-tcoc">
                                            <div class="img-square" style="background-image:url(/uploads/events/26/1.jpg)"></div>
                                            <div><small class="ng-binding"><i class="fa fa-calendar"></i> Jul 26, 2018</small></div>
                                            <h4 class="truncate center-block ng-binding">Kunjungan Taiwan Chamber of Commerce (TCOC) 26 Juli 2018</h4>
                                        </a>
                                    </div><!-- end ngRepeat: data in datas | itemsPerPage:12 --><div class="col-sm-4 ng-scope" ng-repeat="data in datas | itemsPerPage:12">
                                        <a ng-href="/article/lomba-abacusking-di-penang-malaysia" href="/article/lomba-abacusking-di-penang-malaysia">
                                            <div class="img-square" style="background-image:url(/uploads/events/23/1.jpg)"></div>
                                            <div><small class="ng-binding"><i class="fa fa-calendar"></i> Jul 22, 2018</small></div>
                                            <h4 class="truncate center-block ng-binding">Lomba AbacusKing di Penang Malaysia</h4>
                                        </a>
                                    </div><!-- end ngRepeat: data in datas | itemsPerPage:12 --><div class="col-sm-4 ng-scope" ng-repeat="data in datas | itemsPerPage:12">
                                        <a ng-href="/article/wisuda-kelulusan-2018" href="/article/wisuda-kelulusan-2018">
                                            <div class="img-square" style="background-image:url(/uploads/events/17/1.jpg)"></div>
                                            <div><small class="ng-binding"><i class="fa fa-calendar"></i> Jun 30, 2018</small></div>
                                            <h4 class="truncate center-block ng-binding">Wisuda Kelulusan 2018</h4>
                                        </a>
                                    </div><!-- end ngRepeat: data in datas | itemsPerPage:12 --><div class="col-sm-4 ng-scope" ng-repeat="data in datas | itemsPerPage:12">
                                        <a ng-href="/article/gallery-bobnas-2018" href="/article/gallery-bobnas-2018">
                                            <div class="img-square" style="background-image:url(/uploads/events/18/1.jpg)"></div>
                                            <div><small class="ng-binding"><i class="fa fa-calendar"></i> Jun 30, 2018</small></div>
                                            <h4 class="truncate center-block ng-binding">BOBNAS 2018</h4>
                                        </a>
                                    </div><!-- end ngRepeat: data in datas | itemsPerPage:12 --><div class="col-sm-4 ng-scope" ng-repeat="data in datas | itemsPerPage:12">
                                        <a ng-href="/article/20th-sempoa-sip" href="/article/20th-sempoa-sip">
                                            <div class="img-square" style="background-image:url(/uploads/events/19/1.jpg)"></div>
                                            <div><small class="ng-binding"><i class="fa fa-calendar"></i> Jun 30, 2018</small></div>
                                            <h4 class="truncate center-block ng-binding">20th Sempoa SIP</h4>
                                        </a>
                                    </div><!-- end ngRepeat: data in datas | itemsPerPage:12 --><div class="col-sm-4 ng-scope" ng-repeat="data in datas | itemsPerPage:12">
                                        <a ng-href="/article/lomba-demo-ibo-jabodetabek" href="/article/lomba-demo-ibo-jabodetabek">
                                            <div class="img-square" style="background-image:url(/uploads/events/20/1.jpg)"></div>
                                            <div><small class="ng-binding"><i class="fa fa-calendar"></i> Jun 30, 2018</small></div>
                                            <h4 class="truncate center-block ng-binding">Lomba Demo IBO Jabodetabek</h4>
                                        </a>
                                    </div><!-- end ngRepeat: data in datas | itemsPerPage:12 --><div class="col-sm-4 ng-scope" ng-repeat="data in datas | itemsPerPage:12">
                                        <a ng-href="/article/lomba-demo-ibo-jatim" href="/article/lomba-demo-ibo-jatim">
                                            <div class="img-square" style="background-image:url(/uploads/events/21/1.jpg)"></div>
                                            <div><small class="ng-binding"><i class="fa fa-calendar"></i> Jun 30, 2018</small></div>
                                            <h4 class="truncate center-block ng-binding">Lomba Demo IBO Jawa Timur</h4>
                                        </a>
                                    </div><!-- end ngRepeat: data in datas | itemsPerPage:12 --><div class="col-sm-4 ng-scope" ng-repeat="data in datas | itemsPerPage:12">
                                        <a ng-href="/article/lomba-demo-ibo-sumbar" href="/article/lomba-demo-ibo-sumbar">
                                            <div class="img-square" style="background-image:url(/uploads/events/22/1.jpg)"></div>
                                            <div><small class="ng-binding"><i class="fa fa-calendar"></i> Jun 30, 2018</small></div>
                                            <h4 class="truncate center-block ng-binding">Lomba Demo IBO Sumatera Barat</h4>
                                        </a>
                                    </div><!-- end ngRepeat: data in datas | itemsPerPage:12 -->
                                </div>
                                <dir-pagination-controls class="ng-isolate-scope"><!-- ngIf: 1 < pages.length || !autoHide --><ul class="pagination ng-scope" ng-if="1 < pages.length || !autoHide"><!-- ngIf: boundaryLinks --><!-- ngIf: directionLinks --><li ng-if="directionLinks" ng-class="{ disabled : pagination.current == 1 }" class="ng-scope disabled"><a href="" ng-click="setCurrent(pagination.current - 1)">‹</a></li><!-- end ngIf: directionLinks --><!-- ngRepeat: pageNumber in pages track by tracker(pageNumber, $index) --><li ng-repeat="pageNumber in pages track by tracker(pageNumber, $index)" ng-class="{ active : pagination.current == pageNumber, disabled : pageNumber == '...' || ( ! autoHide &amp;&amp; pages.length === 1 ) }" class="ng-scope active"><a href="" ng-click="setCurrent(pageNumber)" class="ng-binding">1</a></li><!-- end ngRepeat: pageNumber in pages track by tracker(pageNumber, $index) --><li ng-repeat="pageNumber in pages track by tracker(pageNumber, $index)" ng-class="{ active : pagination.current == pageNumber, disabled : pageNumber == '...' || ( ! autoHide &amp;&amp; pages.length === 1 ) }" class="ng-scope"><a href="" ng-click="setCurrent(pageNumber)" class="ng-binding">2</a></li><!-- end ngRepeat: pageNumber in pages track by tracker(pageNumber, $index) --><!-- ngIf: directionLinks --><li ng-if="directionLinks" ng-class="{ disabled : pagination.current == pagination.last }" class="ng-scope"><a href="" ng-click="setCurrent(pagination.current + 1)">›</a></li><!-- end ngIf: directionLinks --><!-- ngIf: boundaryLinks --></ul><!-- end ngIf: 1 < pages.length || !autoHide --></dir-pagination-controls>
                            </section>
                        </div></main></td>
            </tr>
            <tr>
                <td height="1"><!-- ngInclude: --><ng-include src="view" ng-controller="footerController" class="ng-scope" style=""><!--<img src="/Guest/img/curve.svg" style="width:100%; margin-top:-140px;" />-->
                        <footer class="ng-scope">
                            <div class="container">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <h3>Program Kami</h3>
                                        <div class="row program">
                                            <div class="col-sm-12 col-xs-6">
                                                <a href="/tentang-kami"><img src="/Guest/img/program-sempoasip.png" class="img-responsive"></a>
                                            </div>
                                            <div class="col-sm-12 col-xs-6">
                                                <a href="/program"><img src="/Guest/img/program-bacatulis.png" class="img-responsive"></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <h3>Site Map</h3>
                                        <ul class="list-unstyled">
                                            <li><a href="/faq">FAQ</a></li>
                                            <li><a href="/usaha">Peluang Mitra Usaha</a></li>
                                            <li><a href="/karir">Kesempatan Berkarir</a></li>
                                            <li><a href="/contact">Hubungi Kami</a></li>
                                        </ul>
                                    </div>
                                    <div class="col-sm-3">
                                        <h3>Kabar Terkini</h3>
                                        <ul class="list-unstyled articles">
                                            <!-- ngRepeat: data in events --><li ng-repeat="data in events" class="ng-scope">
                                                <a ng-href="/article/636705972062248510225b1c" href="/article/636705972062248510225b1c">
                                                    <div class="row">
                                                        <div class="col-xs-5">
                                                            <div class="img-square" style="background-image:url(https://scontent.cdninstagram.com/vp/69e3f662001a9c6e0497ed9cccc4ee1a/5C18EA24/t51.2885-15/sh0.08/e35/s640x640/39576225_278421112808993_2980744390466600960_n.jpg)"></div>
                                                        </div>
                                                        <div class="col-xs-7">
                                                            <div><small class="ng-binding"><i class="fa fa-calendar"></i> Aug 23, 2018</small></div>
                                                            <!-- ngIf: data.Title -->
                                                            <!-- ngIf: data.Description --><div ng-if="data.Description" class="ng-binding ng-scope">Selamat untuk para Coach yang sudah semangat selalu...</div><!-- end ngIf: data.Description -->
                                                        </div>
                                                    </div>
                                                </a>
                                            </li><!-- end ngRepeat: data in events --><li ng-repeat="data in events" class="ng-scope">
                                                <a ng-href="/article/636705108067446861260f01" href="/article/636705108067446861260f01">
                                                    <div class="row">
                                                        <div class="col-xs-5">
                                                            <div class="img-square" style="background-image:url(https://scontent.cdninstagram.com/vp/9d6fb301f1e962eb3c0977b19bef7cc2/5C15E9CC/t51.2885-15/sh0.08/e35/s640x640/38910945_298078364335403_2137509149095755776_n.jpg)"></div>
                                                        </div>
                                                        <div class="col-xs-7">
                                                            <div><small class="ng-binding"><i class="fa fa-calendar"></i> Aug 22, 2018</small></div>
                                                            <!-- ngIf: data.Title -->
                                                            <!-- ngIf: data.Description --><div ng-if="data.Description" class="ng-binding ng-scope">Halo mami, papi dan adik-adik semua... 😊😊😊
                                                                .
                                                                .
                                                                Segenap...</div><!-- end ngIf: data.Description -->
                                                        </div>
                                                    </div>
                                                </a>
                                            </li><!-- end ngRepeat: data in events --><li ng-repeat="data in events" class="ng-scope">
                                                <a ng-href="/article/636705108070572152da72bf" href="/article/636705108070572152da72bf">
                                                    <div class="row">
                                                        <div class="col-xs-5">
                                                            <div class="img-square" style="background-image:url(https://scontent.cdninstagram.com/vp/403bf20153812380439c7febb3189a10/5C39AF7A/t51.2885-15/sh0.08/e35/s640x640/39326304_225245338156976_6630109953631715328_n.jpg)"></div>
                                                        </div>
                                                        <div class="col-xs-7">
                                                            <div><small class="ng-binding"><i class="fa fa-calendar"></i> Aug 20, 2018</small></div>
                                                            <!-- ngIf: data.Title -->
                                                            <!-- ngIf: data.Description --><div ng-if="data.Description" class="ng-binding ng-scope">Ayo Indonesia kami mendukung mu.. ✌👏🤝💪
                                                                .
                                                                .
                                                                18th Asian...</div><!-- end ngIf: data.Description -->
                                                        </div>
                                                    </div>
                                                </a>
                                            </li><!-- end ngRepeat: data in events -->
                                        </ul>
                                    </div>
                                    <div class="col-sm-3">
                                        <!-- ngInclude: --><ng-include src="'/Guest/views/shared-contact.html?v=' + constant.ver" class="ng-scope" style=""><h3 class="ng-scope">Kantor Pusat Operasional</h3>
                                            <ul class="fa-ul ng-scope">
                                                <li><i class="fa-li fa fa-phone"></i><a href="tel:622155731358">+62 21 5573 1358</a><br><a href="tel:622155762446">+62 21 5576 2446</a></li>
                                                <li><i class="fa-li fa fa-envelope-o"></i><a href="mailto:info@sempoasip.co.id">info@sempoasip.com</a></li>
                                                <li><i class="fa-li fa fa-map-marker"></i><a href="https://www.google.com/maps?ll=-6.22372,106.618373&amp;z=16&amp;t=m&amp;hl=en-US&amp;gl=ID&amp;mapclient=embed&amp;cid=5967550938334483559" target="_blank">Karawaci Office Park Blok L/26, Jl. Imam Bonjol, Tangerang<br>Banten 15811</a></li>
                                            </ul></ng-include>
                                    </div>
                                </div>
                            </div>
                        </footer></ng-include></td>
            </tr>
            </tbody></table>
        <!-- SCRIPTS -->
        <script src="/Bundle/bundle.min.js?v=1.1"></script>
        <script src="/Bundle/guest.min.js?v=1.1"></script>

        <div id="fbplus-tmp"></div><div id="fbplus-loading"><div></div></div><div id="fbplus-overlay"></div><div id="fbplus-wrap" class="fbplus-ie"><div id="fbplus-outer"><div class="fbplus-bg" id="fbplus-bg-n"></div><div class="fbplus-bg" id="fbplus-bg-ne"></div><div class="fbplus-bg" id="fbplus-bg-e"></div><div class="fbplus-bg" id="fbplus-bg-se"></div><div class="fbplus-bg" id="fbplus-bg-s"></div><div class="fbplus-bg" id="fbplus-bg-sw"></div><div class="fbplus-bg" id="fbplus-bg-w"></div><div class="fbplus-bg" id="fbplus-bg-nw"></div><div id="fbplus-content"></div><a id="fbplus-close"></a><div id="fbplus-title"></div><a href="javascript:;" id="fbplus-left"><span class="fancy-ico" id="fbplus-left-ico"></span></a><a href="javascript:;" id="fbplus-right"><span class="fancy-ico" id="fbplus-right-ico"></span></a></div></div><div class="sweet-overlay" tabindex="-1" style="opacity: -0.99; display: none;"></div><div class="sweet-alert hideSweetAlert" style="opacity: -0.98; display: none;"><div class="sa-icon sa-error">
      <span class="sa-x-mark">
        <span class="sa-line sa-left"></span>
        <span class="sa-line sa-right"></span>
      </span>
            </div><div class="sa-icon sa-warning">
                <span class="sa-body"></span>
                <span class="sa-dot"></span>
            </div><div class="sa-icon sa-info"></div><div class="sa-icon sa-success">
                <span class="sa-line sa-tip"></span>
                <span class="sa-line sa-long"></span>

                <div class="sa-placeholder"></div>
                <div class="sa-fix"></div>
            </div><div class="sa-icon sa-custom"></div><h2>Title</h2>
            <p>Text</p>
            <fieldset>
                <input type="text" tabindex="3">
                <div class="sa-input-error"></div>
            </fieldset><div class="sa-error-container">
                <div class="icon">!</div>
                <p>Not valid!</p>
            </div><div class="sa-button-container">
                <button class="cancel" tabindex="2">Cancel</button>
                <div class="sa-confirm-button-container">
                    <button class="confirm" tabindex="1">OK</button><div class="la-ball-fall">
                        <div></div>
                        <div></div>
                        <div></div>
                    </div>
                </div>
            </div></div></body>
        <?

    }

    public function setKelasAktiv(){
        $kls = new KelasWebModel();
        $arrKelas = $kls->getAll();
        foreach($arrKelas as $val){
            $val->aktiv = 1;
            $val->save(1);
        }
    }
}