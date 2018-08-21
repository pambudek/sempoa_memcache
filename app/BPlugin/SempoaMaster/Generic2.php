<?php

/**
 * Created by PhpStorm.
 * User: efindiongso
 * Date: 21/03/18
 * Time: 10.18
 */
class Generic2
{

    public static function getIDBukuByLevelKur($level, $kurikulum)
    {
        $barang = new BarangWebModel();

    }

    static function getKodeSiswaByMuridId($murid_id)
    {
        $murid = new MuridModel();
        $murid->getWhereOne("id_murid='$murid_id'");
        return $murid->kode_siswa;
    }

    public static function getMyPreviousLevel($myLevel)
    {
        $objLevel = new SempoaLevel();
        $arrAll = $objLevel->getAll();
        foreach ($arrAll as $key => $level) {
            if ($level->id_level == $myLevel) {
                $keymylevel = $key;
                break;
            }
        }
        return $keymylevel;
    }


    public static function getMyPreviousLevelLama($myLevel)
    {
        $objLevel = new SempoaLevelLama();
        $arrAll = $objLevel->getAll();
        $keymylevel = "";
        foreach ($arrAll as $key => $level) {
            if ($level->id_level_lama == $myLevel) {
                $keymylevel = $key;
                break;
            }
        }
        return $keymylevel;
    }

    public static function sendEmailSempoa($to, $subject, $isi)
    {
        $mail = new Leapmail2();
        $mail->sendHTMLEmail($to, $subject, "", $isi);
    }


    public static function sendEmailToParent($id_murid, $idKuponSatuan = "", $idInvoice = "", $type)
    {

        $murid = new MuridModel();
        $murid->getByID($id_murid);

        if ($murid->getParentEmail() != "") {
            if ($type == KEY::$TYPE_EMAIL_SPP) {
                self::sendEmailSempoa($murid->getParentEmail(), KEY::$SUBJECT_SPP, self::printSPP2($id_murid, $idKuponSatuan));
            }
            if ($type == KEY::$TYPE_EMAIL_BUKU) {
                self::sendEmailSempoa($murid->getParentEmail(), KEY::$SUBJECT_PEMBELIAN_BUKU, self::printBuku2($id_murid, $idInvoice, Generic::getLevelNameByID($murid->id_level_masuk)));;
            }

            if ($type == KEY::$TYPE_EMAIL_FP) {
                self::sendEmailSempoa($murid->getParentEmail(), KEY::$SUBJECT_FIRST_PAYMENT, self::printRegister2($id_murid));
            }
        }

    }

    static function printSPP2($id_murid, $idKuponSatuan)
    {
        $kuponSatuan = new KuponSatuan();
        $kuponSatuan->getByID($idKuponSatuan);
        $murid = new MuridModel();
        $murid->getByID($id_murid);
        $nama = $murid->nama_siswa;
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
        if ($murid->getParentName() != "") {
            $sambut = "Dear " . $murid->getParentName() . ", ";
        } else {
            $sambut = "Dear Mami/Papi " . $murid->getNameMurid() . ", ";
        }
        $level = Generic::getLevelNameByID($murid->id_level_sekarang);
        $jumlah = idr($jenisbm->harga);
        $logo = "http://bo.sempoasip.com/" . _PHOTOURL . "/Picture1.png";

        $return = "<html>
        <head>
            <style>

                #data_tc {
                    text-align: center;
                }

                div.info_invoices {

                }

                div.nama_siswa {

                }

                table {
                    font-family: arial, sans-serif;
                    border-collapse: collapse;
                    width: 100%;
                    margin-right: 20px;
                    font-size: 12px;
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

                .invoice_orang_tua {
                    margin-left: 20px;
                    margin-right: 20px;
                    font-size: 12px;
                }

                div.penutup_invoices {
                    text-align: center;
                    margin-left: 450px;
                    margin-right: 450px;
                }
            </style>
        </head>
        <body>
        <section class=\"sheet padding-10mm\">
            <article>
                <div class=\"invoice_orang_tua\">";

        $return = $return . $sambut;
        $return = $return . "<div class=\"kop_invoices\">
 <img src= $logo alt=\"logo_sempoa\"  align=\"right\" style=\"max-width:20%;\"/>
<div class=\"clearfix\"></div>
                 <h4 id=\"data_tc\">
                                        $tc->nama<br>
                                        $tc->alamat<br>
                                        Telp. $tc->nomor_telp, Fax. $tc->tc_no_fax_office,
                                        HP. $tc->tc_no_hp_office
                                    </h4>
                                    <div class=\"info_invoices\">
                                        <b>No. Invoice : $iuranBulanan->bln_no_invoice</b> <br>
                                        <b>Tanggal : $tanggal</b>
                                    </div>
                                    <div class=\"nama_siswa\">
                                        <p>
                                            Telah diterima pembayaran:<br>
                                            <b>Nama Murid : $nama</b><br>
                                            <b>No Murid : $murid->kode_siswa</b><br>
                                            <b>Level : $level</b><br>

                                        </p>
                                    </div>
                                    <table style=\"border-right: 20px;\">
                                        <tr>
                                            <th>No Kupon</th>
                                            <th>Keterangan</th>
                                            <th>Harga</th>
                                        </tr>
                                        <tr>
                                            <td>$iuranBulanan->bln_kupon_id</td>
                                            <td>Iuran Bulanan : $iuranBulanan->bln_date</td>
                                            <td> $jumlah</td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td style=\"text-align:right;padding-right:15px;font-style:bold;\">Jumlah
                                                Total
                                            </td>
                                            <td>$jumlah</td>
                                        </tr>

                                    </table>




                                    <div class=\"clearfix\"></div>
                                    <br><br>
                                    <div>
                                        <p style=\"border-right: 20px; float: left;\"><b>Catatan :</b>
                                            Setiap Training
                                            Centre
                                            beroperasional dan
                                            memiliki kepemilikan secara mandiri</p>

                                    </div>
                                    <br><br><br>";
        $return = $return . "Hormat Kami, <br><br>";
        $return = $return . "Sempoa SIP <br>";
        $return = $return . "</div>
                            </div>
                        </div>
                    </div>
                </div>

            </article>
        </section>
        </body>
        </html>";


        return $return;
    }


    static function printBuku2($id_murid, $invoice_id, $level)
    {
        $jenisbm = new JenisBiayaModel();
        $jenisbm->getByID(AccessRight::getMyOrgID() . "_" . KEY::$BIAYA_IURAN_BUKU);
        $murid = new MuridModel();
        $murid->getByID($id_murid);
        $nama = $murid->nama_siswa;
        $iuranBuku = new IuranBuku();
        $arrLevel = Generic::getAllLevel();
        $a = array_keys($arrLevel, $level);
        $iuranBuku->getWhereOne("bln_murid_id=$id_murid AND bln_buku_level=$a[0] AND bln_status = 1 ORDER BY bln_id DESC");
        $tc = new SempoaOrg();
        $tc->getWhereOne("org_id=$murid->murid_tc_id");
        $bukuDgnNo = new StockBuku();
        $arbukuDgnNo = $bukuDgnNo->getWhere("stock_invoice_murid=$iuranBuku->bln_id");
        $arbukuDgnNo = $bukuDgnNo->getWhere("stock_invoice_murid=$invoice_id");
        $level2 = Generic::getLevelNameByID($murid->id_level_sekarang);

        if ($murid->getParentName() != "") {
            $sambut = "Dear " . $murid->getParentName() . ", ";
        } else {
            $sambut = "Dear Mami/Papi " . $murid->getNameMurid() . ", ";
        }
        $logo = "http://bo.sempoasip.com/" . _PHOTOURL . "/Picture1.png";
        $jumlah = idr($jenisbm->harga);

        $return = "<html>
             <head>
            <style>

                #data_tc {
                    text-align: center;
                }

                div.info_invoices {

                }

                div.nama_siswa {

                }

                table {
                    font-family: arial, sans-serif;
                    border-collapse: collapse;
                    width: 100%;
                    margin-right: 20px;
                    font-size: 12px;
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

                .invoice_orang_tua {
                    margin-left: 20px;
                    margin-right: 20px;
                    font-size: 12px;
                }

                div.penutup_invoices {
                    text-align: center;
                    margin-left: 450px;
                    margin-right: 450px;
                }
            </style>
        </head>


        <body>
        <section class=\"sheet padding-10mm\">
            <article>
                <div class=\"invoice_orang_tua\">";

        $return = $return . $sambut;
        $return = $return . "<div class=\"kop_invoices\">
              <img src= $logo alt=\"logo_sempoa\"  align=\"right\" style=\"max-width:20%;\"/>
<div class=\"clearfix\"></div>

                                    <h4 id=\"data_tc\">
                                        $tc->nama<br>
                                        $tc->alamat<br>
                                        Telp. $tc->nomor_telp, Fax. $tc->tc_no_fax_office,
                                        HP. $tc->tc_no_hp_office
                                    </h4>
                                    <div class=\"info_invoices\">
                                        <b>No. Invoice : $iuranBuku->bln_no_invoice</b> <br>
                                        <b>Tanggal : $iuranBuku->bln_date_pembayaran</b>
                                    </div>
                                    <div class=\"nama_siswa\">
                                        Telah diterima pembayaran oleh Murid :<br>
                                        <b>Nama Murid : $nama</b><br>
                                        <b>No Murid : $murid->kode_siswa</b><br>
                                        <b>Level : $level2</b><br><br>
                                        <table style=\"border-right: 20px;\">
                                            <thead>
                                            <th>No Buku</th>
                                            <th>Keterangan</th>
                                            <th>Harga</th>
                                            </thead>
                                            <tbody>";


        foreach ($arbukuDgnNo as $val) {
            $date = new DateTime($val->stock_buku_tgl_keluar_tc);
            $tanggal = $date->format("d-m-Y");
            $return = $return . "<tr>
                                                    <td>$val->stock_buku_no</td>
                                                    <td>$val->stock_name_buku</td>
                                                    <td>$jumlah</td>
                                                </tr>";
        }
        $return = $return . "<tr>
                                                <td></td>
                                                <td style=\"text-align:right;padding-right:15px;font-style:bold;\">Jumlah
                                                    Total
                                                </td>
                                                <td>$jumlah</td>
                                            </tr>
                                            </tbody>
                                        </table>

                                        <div class=\"clearfix\"></div>
                                        <br><br>
                                        <div>
                                             <p style=\"float: left;\"><b>Catatan :</b>
                                                Setiap Training
                                                Centre
                                                beroperasional dan
                                                memiliki kepemilikan secara mandiri</p>

                                        </div>
                                        <br><br><br>";
        $return = $return . "Hormat Kami, <br><br>";
        $return = $return . "Sempoa SIP <br>";
        $return = $return . "</div>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
        </body>
        </article>
        </section>
        </html>";

        return $return;
    }

    static function printRegister2($id_murid)
    {

        $murid = new MuridModel();
        $murid->getByID($id_murid);
        $arrjenisBiayaSPP = Generic::getJenisBiayaType();
        $jenisBiayaSPP = $arrjenisBiayaSPP[$murid->id_level_sekarang];
        $pay = new PaymentFirstTimeLog();
        $pay->getByID($id_murid);
        $arrPay = unserialize($pay->murid_biaya_serial);
        foreach ($arrPay as $key => $val) {

            if ($val['id_biaya'] == KEY::$BIAYA_REGISTRASI) {
                $Registrasi = idr($val['harga']);
            }
            if ($val['id_biaya'] == KEY::$BIAYA_IURAN_BUKU) {
                $ibuku = idr($val['harga']);
            }
            if ($val['id_biaya'] == $jenisBiayaSPP) {
                $SPP = idr($val['harga']);
            }
            if ($val['id_biaya'] == KEY::$BIAYA_PERLENGKAPAN_JUNIOR) {
                $harga = idr($val['harga']);
                $level = "Junior";
            }
            if ($val['id_biaya'] == KEY::$BIAYA_PERLENGKAPAN_FOUNDATION) {
                $harga = idr($val['harga']);
                $level = "Foundation";
            }

            if ($key == "kupon") {
                $nomor = $val['nomor'];
                $bln = $val['kapan'];
            }
        }
        $total = idr($pay->murid_pay_value);
        $tc = new SempoaOrg();
        $tc->getWhereOne("org_id = $murid->murid_tc_id");
        $date = new DateTime($pay->murid_pay_date);
        $tanggal = $date->format("d - m - Y");
        $name = $murid->nama_siswa;
        $nobuku = Generic:: getNoBukuByIuranBulananIDWithTC($pay->bln_no_invoice, $pay->murid_tc_id);
        $levelmasuk = Generic::getLevelNameByID($murid->id_level_masuk);

        if ($murid->getParentName() != "") {
            $sambut = "Dear " . $murid->getParentName() . ", ";
        } else {
            $sambut = "Dear Mami / Papi " . $murid->getNameMurid() . ", ";
        }

        $logo = "http://bo.sempoasip.com/" . _PHOTOURL . "/Picture1.png";
//        echo _SPPATH._PHOTOURL;


        $return = "<html>
        <head>
            <style>

                #data_tc {
                    text-align: center;
                }

                div.info_invoices {

                }

                div.nama_siswa {

                }

                table {
                    font-family: arial, sans-serif;
                    border-collapse: collapse;
                    width: 100%;
                    margin-right: 20px;
                    font-size: 12px;
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

                .invoice_orang_tua {
                    margin-left: 20px;
                    margin-right: 20px;
                    font-size: 12px;
                }

                div.penutup_invoices {
                    text-align: center;
                    margin-left: 450px;
                    margin-right: 450px;
                }
            </style>
        </head>

        <body>
        <section class=\"sheet padding-10mm\">
            <article>
                <div class=\"invoice_orang_tua\">";
        $return = $return . $sambut;
        $return = $return . "<div class=\"kop_invoices\">
              <img src= $logo alt=\"logo_sempoa\"  align=\"right\" style=\"max-width:20%;\"/>
<div class=\"clearfix\"></div>
                        <h4 id=\"data_tc\">
                            $tc->nama<br>
                            $tc->alamat<br>
                            Telp. $tc->nomor_telp, Fax. $tc->tc_no_fax_office,
                            HP. $tc->tc_no_hp_office
                        </h4>
                        <div class=\"info_invoices\">
                            <b>No. Invoice : $pay->bln_no_invoice</b> <br>
                            <b>Tanggal : $tanggal</b>
                        </div>
                        <div class=\"nama_siswa\">
                            <p>
                                Telah diterima pembayaran :<br>
                                <b>Nama Murid : $name</b><br>
                                <b>No Murid : $murid->kode_siswa</b><br>
                                <b>Level : $levelmasuk</b><br>

                            </p>
                        </div>
                        <table>
                            <tr>
                                <th>No</th>
                                <th>Keterangan</th>
                                <th>Harga</th>
                            </tr>
                            <tr>
                                <td></td>
                                <td>Biaya Registrasi</td>
                                <td style=\"text-align:right;\"> $Registrasi</td>
                            </tr>
                            <tr>
                                <td>$nomor</td>
                                <td>Iuran Bulanan : $bln </td>
                                <td style=\"text-align:right;\">$SPP</td>
                            </tr>
                            <tr>
                                <td>$nobuku</td>
                                <td>Uang Buku $levelmasuk</td>
                                <td style=\"text-align:right;\">$ibuku</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>Biaya Perlengkapan  $level</td>
                                <td style=\"text-align:right;\">$harga</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td style=\"text-align:right;padding-right:15px;font-style:bold;\">Jumlah Total</td>
                                <td style=\"text-align:right;\">$total</td>
                            </tr>

                        </table>

                        <br><br>
                        <div>
                            <p style=\"float: left;\"><b>Catatan :</b> Setiap Training Centre beroperasional
                                dan
                                memiliki kepemilikan secara mandiri</p>
                         </div>
                                    <br><br><br>

                                </div>";
        $return = $return . "Hormat Kami, <br><br>";
        $return = $return . "Sempoa SIP <br>";
        $return = $return . "</div>
            </article>
        </section>

        </body>
        </html>";
        return $return;

    }


    static function printSPP3($id_murid, $idKuponSatuan)
    {

        $kuponSatuan = new KuponSatuan();
        $kuponSatuan->getByID($idKuponSatuan);
        $murid = new MuridModel();
        $murid->getByID($id_murid);
        $nama = $murid->nama_siswa;
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
        if ($murid->getParentName() != "") {
            $sambut = "Dear " . $murid->getParentName() . ", ";
        } else {
            $sambut = "Dear Mami/Papi " . $murid->getNameMurid() . ", ";
        }
        $level = Generic::getLevelNameByID($murid->id_level_sekarang);
        $jumlah = idr($jenisbm->harga);
        $logo = "http://bo.sempoasip.com/" . _PHOTOURL . "/Picture1.png";

        $return = "<head>
            <title>Invoice Iuran Buku</title>
            <meta charset=\"utf-8\">
            <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
            <link rel=\"stylesheet\" href=\"http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css\">
            <script src=\"https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js\"></script>
            <script src=\"http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js\"></script>
        </head>
        <body>";
        $return = $return . $sambut;
        $return = $return . "<br><br>";
        $return = $return . " <div class=\"Invoice_org_tua\">
            <div class=\"kop_surat\">
                <div class=\"container\">
                    <div class=\"row\">
                        <div class=\"col-md-12 \">
                            <img src=\"$logo\" alt=\"logo_sempoa\" class=\"img-responsive pull-right\"
                                 style=\"max-width:20%\"/>
                        </div>
                    </div>
                </div>
                <div class=\"container container-table\">
                    <div class=\"row vertical-center-row\">
                        <div class=\"text-center col-md-6 col-md-offset-3\" style=\"\">
                            <h4 id=\"data_tc\">
                                $tc->nama<br>
                                $tc->alamat<br>
                                Telp. $tc->nomor_telp, Fax. $tc->tc_no_fax_office,
                                HP. $tc->tc_no_hp_office
                            </h4>
                        </div>
                    </div>
                </div>
                <div class=\"container\">
                    <div class=\"row\">
                        <div class=\"col-md-4\">
                            <b>No. Invoice : $iuranBulanan->bln_no_invoice</b><br>
                            <b>Tanggal : $tanggal</b><br><br>
                            Telah diterima pembayaran:<br>
                            <b>Nama Murid : $nama</b><br>
                            <b>No Murid : $murid->kode_siswa</b><br>
                            <b>Level : $level</b><br>
                        </div>
                    </div>

                    <br>
                </div>

            </div>

            <div class=\"container\">
                <div class=\"table-responsive\">
                    <table class=\"table table-bordered text-center\" id=\"table_invoice\">
                        <thead>
                        <tr>
                            <th class=\"text-center\">No Kupon</th>
                            <th class=\"text-center\">Keterangan</th>
                            <th class=\"text-center\">Harga</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>$iuranBulanan->bln_kupon_id</td>
                            <td>Iuran Bulanan : $iuranBulanan->bln_date</td>
                            <td>$jumlah</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Jumlah Total</td>
                            <td>$jumlah</td>

                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>


            <div class=\"container\">
                <div class=\"row\">
                    <div class=\"col-md-12\">
                        <b>Catatan :</b> Setiap Training Centre beroperasional dan memiliki kepemilikan secara mandiri
                    </div>
                    <br>
                    <br>
                    <div class=\"clearfix\"></div>
                    <div class=\"col-md-4 text-left\">
                        Hormat kami, <br><br>
                        Sempoa SIP
                    </div>
                </div>
            </div>

        </div>
        </body>";
        pr($return);
//        return $return;
//
        ?>
        <!---->
        <!--        <head>-->
        <!--            <title>Invoice Iuran Buku</title>-->
        <!--            <meta charset="utf-8">-->
        <!--            <meta name="viewport" content="width=device-width, initial-scale=1">-->
        <!--            <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">-->
        <!--            <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>-->
        <!--            <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>-->
        <!--        </head>-->
        <!--        <body>-->
        <!---->
        <!---->
        <!--        <div class="Invoice_org_tua">-->
        <!--            <div class="kop_surat">-->
        <!--                <div class="container">-->
        <!--                    <div class="row">-->
        <!--                        <div class="col-md-12 ">-->
        <!--                            <img src="--><?//= $logo;
        ?><!--" alt="logo_sempoa" class="img-responsive pull-right"-->
        <!--                                 style="max-width:20%"/>-->
        <!--                        </div>-->
        <!--                    </div>-->
        <!--                </div>-->
        <!--                <div class="container container-table">-->
        <!--                    <div class="row vertical-center-row">-->
        <!--                        <div class="text-center col-md-6 col-md-offset-3" style="">-->
        <!--                            <h4 id="data_tc">-->
        <!--                                --><?//= $tc->nama;
        ?><!--<br>-->
        <!--                                --><?//= $tc->alamat;
        ?><!--<br>-->
        <!--                                Telp. --><?//= $tc->nomor_telp;
        ?><!--, Fax. --><?//= $tc->tc_no_fax_office;
        ?><!--,-->
        <!--                                HP. --><?//= $tc->tc_no_hp_office;
        ?>
        <!--                            </h4>-->
        <!--                        </div>-->
        <!--                    </div>-->
        <!--                </div>-->
        <!--                <div class="container">-->
        <!--                    <div class="row">-->
        <!--                        <div class="col-md-4">-->
        <!--                            <b>No. Invoice :--><?//= $iuranBulanan->bln_no_invoice;
        ?><!--</b><br>-->
        <!--                            <b>Tanggal :--><?//= $tanggal;
        ?><!--</b><br><br>-->
        <!--                            Telah diterima pembayaran:<br>-->
        <!--                            <b>Nama Murid : --><?//= $nama;
        ?><!--</b><br>-->
        <!--                            <b>No Murid : --><?//= $murid->kode_siswa;
        ?><!--</b><br>-->
        <!--                            <b>Level : --><?//= $level;
        ?><!--</b><br>-->
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
        <!--                    <table class="table table-bordered text-center" id="table_invoice">-->
        <!--                        <thead>-->
        <!--                        <tr>-->
        <!--                            <th class="text-center">No Kupon</th>-->
        <!--                            <th class="text-center">Keterangan</th>-->
        <!--                            <th class="text-center">Harga</th>-->
        <!--                        </tr>-->
        <!--                        </thead>-->
        <!--                        <tbody>-->
        <!--                        <tr>-->
        <!--                            <td>--><?//= $iuranBulanan->bln_kupon_id;
        ?><!--</td>-->
        <!--                            <td>Iuran Bulanan : --><?//= $iuranBulanan->bln_date;
        ?><!--</td>-->
        <!--                            <td>--><?//= $jumlah;
        ?><!--</td>-->
        <!--                        </tr>-->
        <!--                        <tr>-->
        <!--                            <td></td>-->
        <!--                            <td>Jumlah Total</td>-->
        <!--                            <td>--><?//= $jumlah;
        ?><!--</td>-->
        <!---->
        <!--                        </tr>-->
        <!--                        </tbody>-->
        <!--                    </table>-->
        <!--                </div>-->
        <!--            </div>-->
        <!---->
        <!---->
        <!--            <div class="container">-->
        <!--                <div class="row">-->
        <!--                    <div class="col-md-12">-->
        <!--                        <b>Catatan :</b> Setiap Training Centre beroperasional dan memiliki kepemilikan secara mandiri-->
        <!--                    </div>-->
        <!--                    <br>-->
        <!--                    <br>-->
        <!--                    <div class="clearfix"></div>-->
        <!--                    <div class="col-md-4 text-left">-->
        <!--                        Hormat kami, <br><br>-->
        <!--                        Sempoa SIP-->
        <!--                    </div>-->
        <!--                </div>-->
        <!--            </div>-->
        <!---->
        <!--        </div>-->
        <!--        </body>-->
        <!--        --><?//
    }

}