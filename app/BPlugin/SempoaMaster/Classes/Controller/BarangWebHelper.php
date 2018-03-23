<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BarangWebHelper
 *
 * @author efindiongso
 */
class BarangWebHelper extends WebService
{

//put your code here
    protected  $modelLogWebservices = '';
    protected $logStatus = 1;

    function __construct()
    {
        $this->modelLogWebservices= new LogWebServices();
        $this->modelLogWebservices->startLog(Account::getMyName(), Generic::get_client_ip(), get_browser());
        $logStatus = SempoaAuth::isLoggedTransaksi();
        if (!$logStatus) {
            die();
        }
    }

    function __destruct()
    {



//        global $modelLogWebservices;
//        // TODO: Implement __destruct() method.
//        $this->modelLogWebservices->endlog();
    }

    function accept_req()
    {

        if(!$this->logStatus){
            $this->modelLogWebservices->logFunction(__FUNCTION__,0);
            $this->modelLogWebservices->endLog();
        }
        else{
            $this->modelLogWebservices->logFunction(__FUNCTION__,1);
            $this->modelLogWebservices->endLog();
        }

        $obj = new POItemModel();
        $obj->printColumlistAsAttributes();
        $json['post'] = $_POST;

        pr($json);

        die();
        $jml_bundle = (int)addslashes($_POST['jml']);

        if (!is_int($jml_bundle) || $jml_bundle < 1) {
            $json['post'] = $_POST;
            $json['status_code'] = 0;
            $json['status_message'] = "Jumlah tidak boleh";
            echo json_encode($json);
            die();
        }

        $parent_id = Generic::getMyParentID(AccessRight::getMyOrgID());
        $req = new RequestModel();
        $req->req_pengirim_org_id = AccessRight::getMyOrgID();
        $req->req_penerima_org_id = $parent_id;
        $req->req_pengirim_user_id = Account::getMyID();
        $req->req_date = leap_mysqldate();
        $req->req_type = "kupon"; //kupon
        $req->req_status = 0;
        $req->req_jumlah = $jml_bundle;

        $succ = $req->save();
    }

    function add_cart()
    {

        if(!$this->logStatus){
            $this->modelLogWebservices->logFunction(__FUNCTION__,0);
            $this->modelLogWebservices->endLog();
        }
        else{
            $this->modelLogWebservices->logFunction(__FUNCTION__,1);
            $this->modelLogWebservices->endLog();
        }


        $qty = addslashes($_POST['qty']);
        $harga_barang = addslashes($_POST['harga_barang']);
        $id_barang = (int)addslashes($_POST['id_barang']);
        $stock = (int)addslashes($_POST['stock']);
        $pic = addslashes($_POST['pic']);
        $nama_barang = addslashes($_POST['nama_barang']);
        if ($pic == "") {
            $pic = _BPATH . _PHOTOURL . "noimage.jpg";
        }
        $cart = $_SESSION['cart'];
        if ($cart == null) {
            $cart[$id_barang]['qty'] = (int)$qty;
            $cart[$id_barang]['harga'] = (double)$harga_barang;
            $cart[$id_barang]['total_harga'] = (double)$cart[$id_barang]['qty'] * $cart[$id_barang]['harga'];
            $cart[$id_barang]['stock'] = (int)$stock;
            $cart[$id_barang]['pic'] = $pic;
            $cart[$id_barang]['nama_barang'] = $nama_barang;
        } else {
            if (array_key_exists($id_barang, $cart)) {
                $cart[$id_barang]['qty'] = (int)($cart[$id_barang]['qty'] + $qty);
                $cart[$id_barang]['harga'] = (double)$harga_barang;
                $cart[$id_barang]['total_harga'] = (double)($cart[$id_barang]['qty'] * $cart[$id_barang]['harga']);
                $cart[$id_barang]['stock'] = (int)$stock;
                $cart[$id_barang]['pic'] = $pic;
                $cart[$id_barang]['nama_barang'] = $nama_barang;
            } else {
                $cart[$id_barang]['qty'] = (int)$qty;
                $cart[$id_barang]['harga'] = (double)$harga_barang;
                $cart[$id_barang]['total_harga'] = (double)$cart[$id_barang]['qty'] * $cart[$id_barang]['harga'];
                $cart[$id_barang]['stock'] = (int)$stock;
                $cart[$id_barang]['pic'] = $pic;
                $cart[$id_barang]['nama_barang'] = $nama_barang;
            }
        }

        $_SESSION['cart'] = $cart;
        $json['status_code'] = 1;
        $json['status_message'] = "Saved!";

        $arrCart = $_SESSION['cart'];
        $jumlah = 0;
        if (count($arrCart) > 0) {
            foreach ($arrCart as $val) {
                $jumlah = $jumlah + $val['qty'];
            }
        }


        $json['qty'] = $jumlah;
        echo json_encode($json);
        die();
    }

    function update_cart_qty()
    {

        if(!$this->logStatus){
            $this->modelLogWebservices->logFunction(__FUNCTION__,0);
            $this->modelLogWebservices->endLog();
        }
        else{
            $this->modelLogWebservices->logFunction(__FUNCTION__,1);
            $this->modelLogWebservices->endLog();
        }

        $keys = explode(",", addslashes($_POST['keys']));
        $qtys = explode(",", addslashes($_POST['qtys']));
        if (count($keys) < 1) {
            $json['status_code'] = 1;
            $json['status_message'] = "Saved!";
            echo json_encode($json);
            die();
        }
        $cart = $_SESSION['cart'];
        foreach ($keys as $num => $key) {
            $cart[$key]['qty'] = $qtys[$num];
        }
        $_SESSION['cart'] = $cart;
        $json['status_code'] = 1;
        $json['status_message'] = "Saved!";
        echo json_encode($json);
        die();
    }

    function add_cart_modal()
    {

        if(!$this->logStatus){
            $this->modelLogWebservices->logFunction(__FUNCTION__,0);
            $this->modelLogWebservices->endLog();
        }
        else{
            $this->modelLogWebservices->logFunction(__FUNCTION__,1);
            $this->modelLogWebservices->endLog();
        }


        $qty = addslashes($_POST['qty']);
        $harga_barang = addslashes($_POST['harga_barang']);
        $id_barang = (int)addslashes($_POST['id_barang']);
        $stock = (int)addslashes($_POST['stock']);
        $pic = addslashes($_POST['pic']);
        $nama_barang = addslashes($_POST['nama_barang']);
        if ($pic == "") {
            $pic = _BPATH . _PHOTOURL . "noimage.jpg";
        }
        $cart = $_SESSION['cart'];

        if (array_key_exists($id_barang, $cart)) {
            $cart[$id_barang]['qty'] = (int)($qty);
            $cart[$id_barang]['harga'] = (double)$harga_barang;
            $cart[$id_barang]['total_harga'] = (double)($cart[$id_barang]['qty'] * $cart[$id_barang]['harga']);
            $cart[$id_barang]['stock'] = (int)$stock;
            $cart[$id_barang]['pic'] = $pic;
            $cart[$id_barang]['nama_barang'] = $nama_barang;
        } else {
            $cart[$id_barang]['qty'] = (int)$qty;
            $cart[$id_barang]['harga'] = (double)$harga_barang;
            $cart[$id_barang]['total_harga'] = (double)$cart[$id_barang]['qty'] * $cart[$id_barang]['harga'];
            $cart[$id_barang]['stock'] = (int)$stock;
            $cart[$id_barang]['pic'] = $pic;
            $cart[$id_barang]['nama_barang'] = $nama_barang;
        }


        $_SESSION['cart'] = $cart;
        $json['status_code'] = 1;
        $json['status_message'] = "Saved!";
        echo json_encode($json);
        die();
    }

    function add_cart_tmp()
    {

        if(!$this->logStatus){
            $this->modelLogWebservices->logFunction(__FUNCTION__,0);
            $this->modelLogWebservices->endLog();
        }
        else{
            $this->modelLogWebservices->logFunction(__FUNCTION__,1);
            $this->modelLogWebservices->endLog();
        }


//        $po_id = (int) addslashes($_GET['po_id']);
        $id_barang = (int)addslashes($_GET['id_barang']);
        $item_id = (int)addslashes($_GET['item_id']);
        $harga = addslashes($_GET['harga_barang']);
        $po_id = $_SESSION['po_id'];


        if ($po_id == "") {
// add PO Model
// Insert PO Item
            $po = new POModel();
            $po->po_tanggal = $po_tanggal;
            $po->po_tanggal = leap_mysqldate();
            $po->po_pengirim = AccessRight::getMyOrgID();
            $po->po_penerima = Generic::getMyParentID(AccessRight::getMyOrgID());
            $po->po_status = 0;
//            $po->po_keterangan = $po_keterangan;
            $po_id = $po->save();

            if ($po_id) {
                $_SESSION["po_id"] = $po_id;
                $json['po_id'] = $po_id;
                $poItem = new POItemModel();
                $poItem->po_id = $po_id;
                $poItem->id_barang = $id_barang;
                $poItem->qty = 1;
                $poItem->harga = $harga;
                $poItem->total_harga = $poItem->qty * $harga;
                $succ = $poItem->save();
                if ($succ) {
                    $json['item_id'] = $succ;
                    $_SESSION["item_" . $poItem->po_id . "_" . $poItem->id_barang] = $succ;
                    $json['id_barang'] = $id_barang;
                } else {
                    $json['status_code'] = 0;
                    $json['status_message'] = "Save failed!";
                    echo json_encode($json);
                    die();
                }
            }
        } else {
// insert po item
            $json['po_id'] = $po_id;
            $poItem = new POItemModel();
            $id_help = $_SESSION["item_" . $po_id . "_" . $id_barang];
            $arrPoItem = $poItem->getWhere("po_id = '$po_id' AND id_barang = '$id_barang' AND item_id='$id_help'");

            $json['item'] = $arrPoItem;
            if (count($arrPoItem) == 0) {
                $poItem = new POItemModel();
                $poItem->po_id = $po_id;
                $poItem->id_barang = $id_barang;
                $poItem->qty = $poItem->qty + 1000;
                $poItem->harga = $harga;
                $poItem->total_harga = $poItem->qty * $harga;
                $succ = $poItem->save();
                $_SESSION["item_" . $poItem->po_id . "_" . $poItem->id_barang] = $succ;
            } else {
                $arrPoItem[0]->qty = $arrPoItem[0]->qty + 1;
                $arrPoItem[0]->harga = $harga;
                $arrPoItem[0]->total_harga = $arrPoItem[0]->qty * $harga;
                $arrPoItem[0]->save(1);
            }
        }
        $json['status_code'] = 1;
        $json['status_message'] = "Saved!";
        $json['po_id'] = $po_id;
        echo json_encode($json);
        die();
    }

    public function add_cart_semua()
    {

        if(!$this->logStatus){
            $this->modelLogWebservices->logFunction(__FUNCTION__,0);
            $this->modelLogWebservices->endLog();
        }
        else{
            $this->modelLogWebservices->logFunction(__FUNCTION__,1);
            $this->modelLogWebservices->endLog();
        }


        $item = (int)addslashes($_POST['item']);
        $id_barang = (int)addslashes($_POST['id']);
        $qty = (int)addslashes($_POST['qty']);
        $total_harga = (double)addslashes($_POST['total_harga']);

        if (($item == "") || ($item == 0)) {
            $json['status_code'] = 0;
            $json['status_message'] = "Item not found!";
            echo json_encode($json);
            die();
        }

        $poItem = new POItemModel();
        $poItem->getByID($item);
        $poItem->qty = $qty;
        $poItem->qty = $qty;
        $poItem->total_harga = $total_harga;
        $poItem->save(1);
        $json['po'] = ($poItem);
        $json['post'] = $_POST;
        $json['status_code'] = 1;
        $json['status_message'] = "Sukses";
        echo json_encode($json);
        die();
    }

    public function checkout()
    {

        if(!$this->logStatus){
            $this->modelLogWebservices->logFunction(__FUNCTION__,0);
            $this->modelLogWebservices->endLog();
        }
        else{
            $this->modelLogWebservices->logFunction(__FUNCTION__,1);
            $this->modelLogWebservices->endLog();
        }

        $cart = $_SESSION['cart'];
        $total_harga = 0;
        if ($cart != null) {
            //TODO Check stock efindi jiayo
            $myOrgID = AccessRight::getMyOrgID();
            $kpo_id = Generic::getMyParentID($myOrgID);

            $objStockModel = new StockModel();
            $stock_hold = 0;
            $stock = 0;
            $qtyorder = 0;
            foreach ($cart as $key => $barang) {
                $arrStockModel = $objStockModel->getWhere("org_id='$kpo_id' AND id_barang='$key'");
                if (count($arrStockModel) > 0) {
                    $stock = $arrStockModel[0]->jumlah_stock;
                    $stock_hold = $arrStockModel[0]->jumlah_stock_hold;
                    $qtyorder = $barang['qty'];

                    // Jumlah stock lebih kecil dr yg di pesan
                    if (($stock_hold + $qtyorder) > $stock) {
                        $json['status_code'] = 0;
                        $json['status_message'] = "Stock barang " . Generic::getNamaBarangByIDBarang($key) . " tidak mencukupi! Barang yang tersedia " . ($stock - $stock_hold);
                        echo json_encode($json);
                        die();
                    } elseif (($stock_hold + $qtyorder) <= $stock) {
                        $arrStockModel[0]->jumlah_stock_hold = $stock_hold + $barang['qty'];
                        $arrStockModel[0]->save(1);
                    }
                }
            }


            foreach ($cart as $key => $barang) {
                $total_harga += $barang['total_harga'];
            }
            $PO = new POModel();
            $PO->po_tanggal = leap_mysqldate();
//            $PO->po_pengirim = $myOrgID;

            $PO->po_user_id_pengirim = Account::getMyID();
            $PO->po_pengirim = $myOrgID;
//            $PO->po_pengirim = Account::getMyID();
            $PO->po_penerima = $kpo_id;
            $PO->po_status = "0";
            $PO->po_total_harga = $total_harga;
            $succ = $PO->save();
            if ($succ)
                SempoaInboxModel::sendMsg($kpo_id, AccessRight::getMyOrgID(), "Ada pesanan barang", "Ada pesanan barang  <br> Tolong dicek");

            foreach ($cart as $key => $barang) {

                if ($succ) {
                    $poItems = new POItemModel();
                    $poItems->po_id = $succ;
                    $poItems->id_barang = $key;
                    $poItems->qty = $barang['qty'];
                    $poItems->harga = $barang['harga'];
//                    $poItems->total_harga = $barang['total_harga'];
                    $poItems->total_harga = $barang['qty'] * $barang['harga'];
                    $poItems->org_id = $kpo_id;
                    $itemPOID = $poItems->save();
                    if ($itemPOID != "") {

                    }
                }
            }

            unset($_SESSION['cart']);
        }

        unset($_SESSION['cart']);
        $json['status_code'] = 1;
        $json['status_message'] = "Sukses";
        echo json_encode($json);
        die();
    }

    public function coba()
    {

        if(!$this->logStatus){
            $this->modelLogWebservices->logFunction(__FUNCTION__,0);
            $this->modelLogWebservices->endLog();
        }
        else{
            $this->modelLogWebservices->logFunction(__FUNCTION__,1);
            $this->modelLogWebservices->endLog();
        }

//        pr($_SESSION);
        $cart = $_SESSION['cart'];


//        foreach ($cart as $key => $subArr) {
//            unset($cart[16]);
//        }
//        $_SESSION['cart'] = $cart;
        pr($_SESSION);
//        foreach ($cart as $key => $barang) {
//            pr($key);
//            pr($barang['qty']);
//            pr($barang['harga']);
//            pr($barang['total_harga']);
//            pr($barang['stock']);
//            pr($barang['pic']);
//        }
//        pr($_SESSION['cart']);
//unset($_SESSION['po_id']);
//
//        $myOrgID = AccessRight::getMyOrgID();
//        $kpo_id = Generic::getMyParentID($myOrgID);
//        //sempoa__barang_harga
//        $objBarang = new BarangWebModel();
//        $po = $_SESSION['po_id'];
//        $poItem = new POItemModel();
//        $objStockBarangMyParent = new StockModel();
//        global $db;
//        $q = "SELECT b.*, po.*, st.* FROM {$objBarang->table_name} b INNER JOIN {$poItem->table_name} po INNER JOIN {$objStockBarangMyParent->table_name} st ON  b.id_barang_harga = po.id_barang AND st.id_barang = po.id_barang WHERE po.po_id='$po' ";
//        pr($q);
//        $arrStockBarangMyParent = $db->query($q, 2);
//        pr($arrStockBarangMyParent);
//        global $db;
//        $q = "SELECT b.*, s.jumlah_stock FROM {$objBarang->table_name} b INNER JOIN {$objStockBarangMyParent->table_name} s ON  b.id_barang_harga = s.id_barang WHERE org_id='$kpo_id'";
//
//        $arrStockBarangMyParent = $db->query($q, 2);
    }

    function remove_session()
    {

        if(!$this->logStatus){
            $this->modelLogWebservices->logFunction(__FUNCTION__,0);
            $this->modelLogWebservices->endLog();
        }
        else{
            $this->modelLogWebservices->logFunction(__FUNCTION__,1);
            $this->modelLogWebservices->endLog();
        }



        unset($_SESSION['cart']);

        die();
        $cart = $_SESSION['cart'];
        foreach ($cart as $key => $subArr) {
            if ($key == 23) {
                unset($cart[23]);
            }
        }
//        pr($cart);
//        $cart = $_SESSION['cart'];
        $_SESSION['cart'] = $cart;
        pr($_SESSION['cart']);
//        $_SESSION['cart'] = $cart;
//        unset($_SESSION['cart']);
    }

    function cart_modal()
    {

        if(!$this->logStatus){
            $this->modelLogWebservices->logFunction(__FUNCTION__,0);
            $this->modelLogWebservices->endLog();
        }
        else{
            $this->modelLogWebservices->logFunction(__FUNCTION__,1);
            $this->modelLogWebservices->endLog();
        }

        $cart = $_SESSION['cart'];
        ?>

        <div class="modal-body-2" id="cart_body-dalam">
            <table class="table table-hover table-responsive" id="my-cart-table">
                <tbody>
                <?
                //                    pr($cart);
                //                    echo "cart Modal";
                ?>
                <?
                //                    pr($cart);
                foreach ($cart as $key => $barang) {
                    ?>
                    <script>
                        var obj_item = {
                            harga: parseInt(<?= $barang['harga']; ?>),
                            qty: parseInt(<?= $barang['qty']; ?>)
                        }
                        cart_item_by_id['<?= $key; ?>'] = obj_item;

                    </script>
                    <tr id="tr_barang_<?= $key; ?>" title="summary_<?= $key; ?>" data-id="1" data-price="10">
                        <td class="text-center" style="width: 50px;">
                            <img width="100%" src="<?= $barang['pic']; ?>" onerror="imgError(this);">
                        </td>
                        <td id='nama_barang_<?= $key; ?>'><?= $barang['nama_barang']; ?></td>
                        <td title="Unit Price" id='hargasatuan_<?= $key; ?>'
                            style="text-align: right;"><?= idr($barang['harga']); ?></td>
                        <td title="Quantity">
                            <input id='qty_<?= $key; ?>' type="number" min="1" style="text-align:right;width: 50px;"
                                   class="my-product-quantity" value="<?= $barang['qty']; ?>">
                        </td>
                        <td id='total_<?= $key; ?>' title="Total" class="my-product-total"
                            style="text-align: right;"><?= idr($barang['total_harga']); ?>
                        </td>
                        <td title="Remove from Cart" class="text-center" style="width: 30px;">
                            <a id='remove_<?= $key; ?>' class="btn btn-xs btn-danger my-product-remove">X</a>
                        </td>

                    </tr>
                    <script>
                        var cartArray = [];
                        var cartObj = {};
                        $('#qty_<?= $key; ?>').click(function () {
                            var qty = parseInt($('#qty_<?= $key; ?>').val());
//                            alert(qty);
                            cart_item_by_id['<?= $key; ?>'].qty = qty;
                            hitungUlangTotal();

                        });


                        $('#qty_<?= $key; ?>').on(function () {
                            var qty = parseInt($('#qty_<?= $key; ?>').val());
                            alert(qty);
                            cart_item_by_id['<?= $key; ?>'].qty = qty;
                            hitungUlangTotal();

                        });

                        $('#remove_<?= $key; ?>').click(function () {
                                if (confirm("Anda yakin akan menghapus item " + '<?= $barang['nama_barang']; ?>')) {

                                    $.get("<?= _SPPATH; ?>BarangWebHelper/remove_cart?id=<?= $key; ?>", function (data) {
                                        console.log(data);
                                        if (data.status_code) {
                                            $('#tr_barang_<?= $key; ?>').hide();
                                            delete cart_item_by_id["<?= $key; ?>"];
                                            hitungUlangTotal();

                                            //                                                lwrefresh(selected_page);
                                        } else {
                                            alert(data.status_message);
                                        }
                                    }, 'json');

                                }

                            }
                        );
                    </script>
                    <?
                }
                ?>
                </tbody>
                <tfoot>
                <tr>
                    <td></td>
                    <td><strong>Total</strong>
                    </td>
                    <td></td>
                    <td></td>
                    <td style="text-align: right;"><strong id="my-cart-grand-total">

                        </strong>
                    </td>
                    <td></td>
                </tr>
                </tfoot>
                <script>

                    function hitungUlangTotal() {
                        cart_hrg_total = 0;
                        cart_jumlah_total = 0;
                        for (key in cart_item_by_id) {
                            var obj_item = cart_item_by_id[key];
                            $('#total_' + key).html(toRp(obj_item.qty * obj_item.harga));
                            cart_hrg_total += obj_item.qty * obj_item.harga;
                            cart_jumlah_total += obj_item.qty;
                        }
                        $('#my-cart-grand-total').html(toRp(cart_hrg_total));

                        $('#jumlah_cart_barang_total').html(cart_jumlah_total);
                        if (cart_jumlah_total < 1) {
                            $('#btn_cart_barang_total').hide();
                            $('#my-cart-modal').modal('hide');
                        }

                    }
                    //                    console.log(cart_item_by_id);

                    hitungUlangTotal();
                </script>
            </table>
        </div>
        <?
    }

    function remove_cart()
    {

        if(!$this->logStatus){
            $this->modelLogWebservices->logFunction(__FUNCTION__,0);
            $this->modelLogWebservices->endLog();
        }
        else{
            $this->modelLogWebservices->logFunction(__FUNCTION__,1);
            $this->modelLogWebservices->endLog();
        }


        $id = (int)addslashes($_GET['id']);
        $json['id'] = $id;
        $cart = $_SESSION['cart'];
        foreach ($cart as $key => $subArr) {
            if ($key == $id) {
                unset($cart[$id]);
            }
        }
        if (count($cart) == 0) {
            unset($_SESSION['cart']);
        } else
            $_SESSION['cart'] = $cart;

        $json['status_code'] = 1;
        $json['status_message'] = "Data berhasil di hapus!";
        echo json_encode($json);
        die();
    }

    function setStatusPO()
    {

        if(!$this->logStatus){
            $this->modelLogWebservices->logFunction(__FUNCTION__,0);
            $this->modelLogWebservices->endLog();
        }
        else{
            $this->modelLogWebservices->logFunction(__FUNCTION__,1);
            $this->modelLogWebservices->endLog();
        }


        $id_status = (int)addslashes($_GET['id_status']);
        $json['id_status'] = $id_status;
        $po_id = addslashes($_GET['po_id']);
        $json['po_id'] = $po_id;


        // cek stocknya cukup gak

        $myID = AccessRight::getMyOrgID();

        if ($id_status != "") {
            $objPO = new POModel();
            $objPO->getByID($po_id);
            if($objPO->po_status != '0'){
                $json['status_code'] = 0;
                $json['status_message'] = "Status pernah di Update ";
                echo json_encode($json);
                die();
            }
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


                if (count($arrPOItems) > 0) {
                    if ($id_status == 1) {
                        // Check Jumlah no Buku
                        $objPO_buku = new POModel();
                        $arrPO_buku = $objPO_buku->getWhere("po_id='$po_id'");
//                        global $db;
//                        $q = "SELECT * FROM {$objPO->table_name} po  WHERE po.po_id= '$po_id'";
//                        $arrPO_buku = $db->query($q, 2);
                        $peminta = $arrPO_buku[0]->po_pengirim;
                        $objPOItem_buku = new POItemModel();
                        $arrPOItems_buku = $objPOItem_buku->getWhere("po_id='$po_id'");
                        $json['po_buku'] = $arrPOItems_buku;
                        $res = array();
                        unset($res);
                        foreach ($arrPOItems as $val) {
                            $res[$val->id_barang]['barang'] = $val->id_barang;
                            $res[$val->id_barang]['qty'] = $val->qty;
                            $res[$val->id_barang]['peminta'] = $peminta;
                            $res[$val->id_barang]['pemilik'] = $val->org_id;
                            $res[$val->id_barang]['po_id'] = $val->po_id;
                        }

                        $anzahlBuku = 0;
                        foreach ($res as $val) {
                            $anzahlBuku = self::getNoBuku($val['barang'], $val['qty'], $val['pemilik'], AccessRight::getMyOrgType());
                            $json['anzahl'] = $val['barang'] . " - " . $val['qty'] . " - " . $anzahlBuku;
                            if ($anzahlBuku == $val['qty']) {
//                                if ($anzahlBuku >= $val['qty']) {
                                self::setNoBuku($val['barang'], $val['qty'], $val['pemilik'], $val['peminta'], AccessRight::getMyOrgType(), $val['po_id']);
                            } else {

                            }

                        }


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
        $json['log'] = $this->modelLogWebservices;
        $json['id_status'] = $id_status;
        $json['status_code'] = 1;
        $json['status_message'] = "Status di Update";
        echo json_encode($json);
        die();
    }


    /*
     * Check Jumlah Buku
     */


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


    /*
     *  Set No buku yang kepake
     */

    public function setNoBuku($id_barang, $qty, $org_id_pemilik, $org_id_peminta, $org_type, $po_id)
    {
        $po = new POItemModel();
        $po->getWhereOne("item_id='$po_id'");
        if(($po->status == 1)|| ($po->status == 99)){
            $json['status_code'] = 0;
            $json['status_message'] = "PO sdh tidak bisa dikerjakan!";
            echo json_encode($json);
            die();
        }
//        pr($po);
        $stockBuku = new StockBuku();
        if ($org_type == KEY::$KPO) {
            $arrStockBuku = $stockBuku->getWhere("stock_id_buku=$id_barang AND stock_buku_status_kpo=1 AND stock_buku_kpo = $org_id_pemilik ORDER BY stock_buku_no ASC LIMIT $qty");
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

            $arrStockBuku = $stockBuku->getWhere("stock_id_buku=$id_barang AND stock_status_ibo=1 AND stock_buku_ibo = $org_id_pemilik ORDER BY stock_buku_no ASC LIMIT $qty");
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
            $arrStockBuku = $stockBuku->getWhere("stock_id_buku=$id_barang AND stock_status_tc=1 AND stock_buku_kpo = $org_id_pemilik ORDER BY stock_buku_no ASC LIMIT $qty");
            foreach ($arrStockBuku as $val) {
                $val->stock_buku_status_kpo = 0;
                $val->stock_buku_status_ibo = 0;
                $val->stock_buku_tgl_keluar_kpo = leap_mysqldate();
                $val->stock_buku_tgl_masuk_ibo = leap_mysqldate();
                $val->stock_buku_ibo = $org_id_peminta;
                $val->save(1);
            }
        }

    }


    public static function form_pemesanan($parent_id)
    {
        // buat form seperti ecom
        // buat PO
        $myOrgID = AccessRight::getMyOrgID();
        $kpo_id = Generic::getMyParentID($myOrgID);
        $objBarang = new BarangWebModel();
        $objStockBarangMyParent = new StockModel();
        global $db;
        $q = "SELECT b.*, s.jumlah_stock, s.jumlah_stock_hold FROM {$objBarang->table_name} b INNER JOIN {$objStockBarangMyParent->table_name} s ON  b.id_barang_harga = s.id_barang WHERE s.org_id='$parent_id'";
//pr($q);
        $arrStockBarangMyParent = $db->query($q, 2);
//        pr($arrStockBarangMyParent);
//        Generic::getMyRoot();
        $arrCart = $_SESSION['cart'];
        $jumlah = 0;
        if (count($arrCart) > 0) {
            foreach ($arrCart as $val) {
                $jumlah = $jumlah + $val['qty'];
            }
        }
        $t = time();
        ?>
        <button type="button" class="pull-right btn btn-danger"
                id="btn_cart_barang_total" <? if ($jumlah < 1) echo "style='display:none;'"; ?> >
            <span class="glyphicon glyphicon-shopping-cart"></span>
            <span class="badge badge-notify my-cart-badge" id='jumlah_cart_barang_total'><?= $jumlah; ?></span>
        </button>

        <div class="clearfix"></div>
        <style>
            .icon-shopping-cart:before {
                content: "\f07a";
            }

            .prod-info-main {
                border: 1px solid #f39c12;
                margin-bottom: 20px;
                margin-top: 10px;
                background: #fff;
                padding: 6px;
                -webkit-box-shadow: 0 1px 4px 0 rgba(21, 180, 255, 0.5);
                box-shadow: 0 1px 1px 0 rgba(21, 180, 255, 0.5);
            }

            * {
                -webkit-box-sizing: border-box;
                -moz-box-sizing: border-box;
                box-sizing: border-box;
            }

            /*user agent stylesheet*/
            div {
                display: block;
            }

            .price-container {
                font-size: 24px;
                margin: 0;
                font-weight: 300;
            }

            .product-deatil {
                /*border-bottom: 1px solid #dfe5e9;*/
                padding-bottom: 17px;
                padding-left: 16px;
                padding-top: 16px;
                position: relative;
                background: #fff;
            }

            .product-deatil .name a {
                margin-left: 0;
            }

            a {
                color: #337ab7;
                text-decoration: none;
            }

            a {
                background-color: transparent;
            }

            .product-block .product-deatil p.price-container span, .prod-info-main .product-deatil p.price-container span, .shipping table tbody tr td p.price-container span, .shopping-items table tbody tr td p.price-container span {
                color: #21c2f8;
                font-family: Lato, sans-serif;
                font-size: 24px;
                line-height: 20px;
            }

            .prod-wrap .product-image span.tag2 {

                position: absolute;

                top: 10px;

                right: 10px;

                width: 36px;

                height: 36px;

                border-radius: 50%;

                padding: 10px 0;

                color: #fff;

                font-size: 11px;

                text-align: center

            }

            .prod-wrap .product-image span.tag3 {

                position: absolute;

                top: 10px;

                right: 20px;

                width: 60px;

                height: 36px;

                border-radius: 50%;

                padding: 10px 0;

                color: #fff;

                font-size: 11px;

                text-align: center

            }

            .prod-wrap .product-image span.sale {

                background-color: #57889c;

            }

            .prod-wrap .product-image span.hot {

                background-color: #a90329;

            }

            .prod-wrap .product-image span.special {

                background-color: #3B6764;

            }

            .product-deatil .name a {
                margin-left: 0;
            }

            .product-image-roy {
                width: 100%;
                height: 173px;
                overflow: hidden;
            }

            .product-image-roy img {

                max-width: 100%;
                max-height: 100%;
                margin: auto;

            }

            .prod-info-roy {
                background-color: #fff;
                padding: 10px;
                margin: 10px;
            }


        </style>
        <?
        foreach ($arrStockBarangMyParent as $barang) {
            ?>
            <div class="col-xs-12 col-md-6 col-sm-12">
                <div class="prod-info-roy" id="<?= $barang->id_barang_harga; ?>">
                    <div class="pull-left" style="margin-right:20px;">
                        <div class="foto100">
                            <?
                            if ($barang->foto_barang == "") {
                                ?>
                                <img onload="OnImageLoad(event,100);" src="<?= _BPATH . "images/noimage.jpg"; ?>"
                                     onerror="imgError(this);">
                                <?
                            } else {
                                ?>
                                <img onload="OnImageLoad(event,100);"
                                     src="<?= _BPATH . _PHOTOURL . $barang->foto_barang; ?>" onerror="imgError(this);">
                                <?
                            }
                            ?>
                        </div>
                    </div>
                    <div class="pull-left">
                        <div class="product-deatil-roy">
                            <div class="product-deatil-roy-name"
                                 style="font-size:18px;"><?= $barang->nama_barang; ?></div>


                            <!--                                    <b><span>Stock: <?
                            //Generic::number($barang->jumlah_stock - $barang->jumlah_stock_hold);
                            ?></span></b>-->


                            <div class="price-container-roy">

                                <div style="margin-bottom:20px;"><b>IDR <?
                                        if (AccessRight::getMyOrgType() != KEY::$TC) {
//                                                echo idr(Generic::getHargaBarang($barang->id_barang_harga, $parent_id));
                                            echo idr(Generic::getHargaBarang($barang->id_barang_harga, AccessRight::getMyOrgID()));
                                        } else {
                                            $group_id = Generic::getMyGroupID($myOrgID);
                                            echo idr(Generic::getHargaBarangByGroup($barang->id_barang_harga, AccessRight::getMyOrgID()));
//                                        pr($group_id);
                                        }
                                        ?></b></div>
                            </div>
                            <div class="tag1-roy">
                                <?
                                if (Generic::number($barang->jumlah_stock - $barang->jumlah_stock_hold) <= 0) {

                                } else {
                                    ?>
                                    <button class="btn btn-warning"
                                            style="background-color:#888888; border-color: #888888;"
                                            id="add_cart_<?= $barang->id_barang_harga . "_" . $t; ?>">Add to cart &nbsp;
                                        <i class="glyphicon glyphicon-shopping-cart"></i></button>

                                    <?
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>

                <!-- end product -->
            </div>
            <script>
                // Add to chart, lalu masukin ke session
                var cartObj = {};
                var check = false;
                <?
                if (AccessRight::getMyOrgType() != KEY::$TC) {
                ?>
                $('#add_cart_<?= $barang->id_barang_harga . "_" . $t; ?>').click(function () {
                    cartObj.id_barang = parseInt('<?= $barang->id_barang_harga; ?>');
                    cartObj.nama_barang = ('<?= $barang->nama_barang; ?>');
                    cartObj.qty = 1;
//                        cartObj.harga_barang = parseInt('<?//= Generic::getHargaBarang($barang->id_barang_harga, $parent_id); ?>//');
                    cartObj.harga_barang = parseInt('<?= Generic::getHargaBarang($barang->id_barang_harga, AccessRight::getMyOrgID()); ?>');
                    cartObj.stock = '<?= Generic::number($barang->jumlah_stock); ?>';
                    if ('<?= $barang->foto_barang; ?>' == "") {
                        cartObj.pic = '<?= _BPATH . _PHOTOURL . "noimage.jpg"; ?>';
                    } else {
                        cartObj.pic = '<?= _BPATH . _PHOTOURL . "$barang->foto_barang"; ?>';
                    }
                    // Add ke session
                    $.post("<?= _SPPATH; ?>BarangWebHelper/add_cart", cartObj, function (data) {
                        if (data.status_code) {
                            $('#jumlah_cart_barang_total').html(data.qty);
                            //                            lwrefresh(selected_page);
                            if (data.qty > 0) {
                                $('#btn_cart_barang_total').show();
                            }

                        } else {
                            alert(data.status_message);
                        }
                    }, 'json');
                });

                <?
                } else {
                $group_id = Generic::getMyGroupID($myOrgID);
                //                echo idr(Generic::getHargaBarangByGroup($barang->id_barang_harga, AccessRight::getMyOrgID()));
                //                                        pr($group_id);
                ?>
                $('#add_cart_<?= $barang->id_barang_harga . "_" . $t; ?>').click(function () {
                    cartObj.id_barang = parseInt('<?= $barang->id_barang_harga; ?>');
                    cartObj.nama_barang = ('<?= $barang->nama_barang; ?>');
                    cartObj.qty = 1;
                    cartObj.harga_barang = parseInt('<?= Generic::getHargaBarangByGroup($barang->id_barang_harga, AccessRight::getMyOrgID()); ?>');
                    cartObj.stock = '<?= Generic::number($barang->jumlah_stock); ?>';
                    if ('<?= $barang->foto_barang; ?>' == "") {
                        cartObj.pic = '<?= _BPATH . _PHOTOURL . "noimage.jpg"; ?>';
                    } else {
                        cartObj.pic = '<?= _BPATH . _PHOTOURL . "$barang->foto_barang"; ?>';
                    }
                    // Add ke session
                    $.post("<?= _SPPATH; ?>BarangWebHelper/add_cart", cartObj, function (data) {
                        if (data.status_code) {
                            $('#jumlah_cart_barang_total').html(data.qty);
                            //                            lwrefresh(selected_page);
                            if (data.qty > 0) {
                                $('#btn_cart_barang_total').show();
                            }

                        } else {
                            alert(data.status_message);
                        }
                    }, 'json');
                });

                <?
                }
                ?>

            </script>
            <?
        }
        ?>
        <script>
            $('#btn_cart_barang_total').click(function () {

                $('#cart_body').load("<?= _SPPATH; ?>BarangWebHelper/cart_modal");
                $('#my-cart-modal').modal("show");


            });

        </script>
        <?
    }

    public static function lihat_pesanan_tmp()
    {
        $myOrgID = AccessRight::getMyOrgID();
        $objPO = new POModel();
        $objPOItems = new POItemModel();
        $acc = new SempoaAccount();
        $page = isset($_GET['page']) ? addslashes($_GET['page']) : 1;
        $limit = KEY::$LIMIT_PO;
        $begin = ($page - 1) * $limit;
        global $db;
//        $q = "SELECT po.*, poItem.* FROM {$objPO->table_name} po INNER JOIN {$objPOItems->table_name} poItem ON po.po_id = poItem.po_id WHERE  po.po_penerima='$myOrgID' ";
//        $arrPO = $db->query($q, 2);
        $q = "SELECT * FROM {$objPO->table_name} po  WHERE  po.po_penerima='$myOrgID' ORDER BY po.po_tanggal DESC LIMIT $begin,$limit ";
        $arrPO = $db->query($q, 2);
        $jumlahTotal = $objPO->getJumlah("po_penerima='$myOrgID'");
        $jumlahHalamanTotal = ceil($jumlahTotal / $limit);
        $t = time();
        ?>
        <section class="content-header">
            <h1>
                Pemesanan Barang
            </h1>

        </section>

        <section class="content">
            <style>
                .caret {
                    display: inline-block;
                    width: 0;
                    height: 0;
                    margin-left: 2px;
                    vertical-align: middle;
                    border-top: 4px solid;
                    border-right: 4px solid transparent;
                    border-left: 4px solid transparent;
                }
            </style>
            <div class="table-responsive">
                <table class="table table-bordered table-striped" style="background-color: #FFFFFF;">
                    <thead class='heading'>
                    <tr>
                        <th><b>No PO</b></th>
                        <th><b>Tanggal</b></th>
                        <th><b>Pemesan</b></th>
                        <th><b>Status</b></th>
                        <th class="palingdalam" style="visibility:hidden;display: none"><b>Qty</b></th>
                        <th class="palingdalam" style="visibility:hidden;display: none"><b>Barang</b></th>
                        <th class="palingdalam" style="visibility:hidden;display: none"><b>Harga</b></th>
                        <th class="palingdalam" style="visibility:hidden;display: none"><b>Total Harga</b></th>
                        <th><b>Grand Total</b></th>

                    <tr>
                    </thead>
                    <tbody id='body'>
                    <?
                    foreach ($arrPO as $po) {
//                            pr($po->po_status);
                        $arrPOItems = $objPOItems->getWhere("po_id = $po->po_id");
                        if ($po->po_status == KEY::$STATUS_NEW)
                            $warna = KEY::$WARNA_BIRU;
                        elseif ($po->po_status == KEY::$STATUS_PAID)
                            $warna = KEY::$WARNA_HIJAU;
                        elseif ($po->po_status == KEY::$STATUS_CANCEL)
                            $warna = KEY::$WARNA_MERAH;
                        $arrname = $acc->getWhere("admin_org_id = '$po->po_pengirim'");
                        //hitung total hrg manually roy 14 sep 2016
                        $total_satu_po = 0;
                        foreach ($arrPOItems as $items) {
                            $total_satu_po += $items->harga * $items->qty;
                        }
                        ?>
                        <tr class='<?= $po->po_id ?> atas_<?= $po->po_id ?>' style="background-color: <?= $warna; ?>;">

                            <td id='open_<?= $po->po_id ?>'
                                onclick="bukaPO('<?= $po->po_id ?>', '<?= $po->po_status; ?>');">
                                <?= $po->po_id ?>

                                <span class="caret" style="cursor: pointer;"></span>
                            </td>
                            <td><?= $po->po_tanggal ?></td>
                            <td><?= $arrname[0]->admin_nama_depan; ?></td>
                            <td id='status_po_<?= $po->po_id; ?>'><?
                                if ($po->po_status == 0) {
                                    echo "New";
                                } elseif ($po->po_status == 1) {
                                    echo "Paid";
                                } elseif ($po->po_status == 99) {
                                    echo "Cancel";
                                }
                                ?></td>

                            <td class='<?= $po->po_id; ?> palingdalam' style="visibility:hidden;display: none"></td>
                            <td class='<?= $po->po_id ?> palingdalam' style="visibility:hidden;display: none"></td>
                            <td class='<?= $po->po_id ?> palingdalam' style="visibility:hidden;display: none"></td>
                            <td class='<?= $po->po_id ?> palingdalam' style="visibility:hidden;display: none"></td>
                            <td><?= "IDR " . idr($total_satu_po); ?></td>

                        </tr>
                    <?
                    foreach ($arrPOItems as $items) {
                    ?>
                        <tr class='<?= $po->po_id ?>' style="visibility:hidden;display: none">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td id='status_po_<?= $po->po_id; ?>'>
                            </td>
                            <td class='<?= $po->po_id ?>'><?= $items->qty ?></td>
                            <td class='<?= $po->po_id ?> '><?= Generic::getNamaBarangByIDBarang($items->id_barang); ?></td>

                            <td class='<?= $po->po_id ?>'><?= "IDR " . idr($items->harga); ?></td>
                            <td class='<?= $po->po_id ?>'><?= "IDR " . idr($items->harga * $items->qty) ?></td>
                            <td class='<?= $po->po_id ?>'></td>
                        </tr>
                    <?
                    }
                    ?>

                        <script>
                            $("#status_po_<?= $po->po_id; ?>").dblclick(function () {
                                var current = $("#status_po_<?= $po->po_id; ?>").html();
                                if (current == 'New') {
                                    var html = "<select id='select_status_<?= $po->po_id; ?>'>" +
                                        "<option value='0'>New</option>" +
                                        "<option value='1'>Paid</option>" +
                                        "<option value='99'>Cancel</option>" +
                                        "</select>";
                                } else if (current == 'Paid') {
                                    var html = "<select id='select_status_<?= $po->po_id; ?>'>" +
                                        "<option value='1' selected>Paid</option>" +
                                        "</select>";
                                } else if (current == 'Cancel') {
                                    var html = "<select id='select_status_<?= $po->po_id; ?>'>" +
                                        "<option value='99' selected>Cancel</option>" +
                                        "</select>";
                                }
                                $("#status_po_<?= $po->po_id; ?>").html(html);
                                $('#select_status_<?= $po->po_id; ?>').change(function () {
                                    var id_status = $('#select_status_<?= $po->po_id; ?>').val();
                                    var po_id = "<?= $po->po_id; ?>";
                                    $.get("<?= _SPPATH; ?>BarangWebHelper/setStatusPO?id_status=" + id_status + "&po_id=" + po_id, function (data) {
                                        lwrefresh("status_po_<?= $po->po_id; ?>");
                                        if (data.status_code) {
                                            //success
                                            alert(data.status_message);
                                            console.log(data.po);
                                            console.log(data.stock);
                                            lwrefresh(selected_page);
                                            if (id_status == 0) {
                                                var html = "New";
                                            } else if (id_status == 1) {
                                                var html = "Paid";
                                            } else if (id_status == 99) {
                                                var html = "Cancel";
                                            }
                                            $("#status_po_<?= $po->po_id; ?>").html(html);
                                        } else {
                                            alert(data.status_message);
                                        }
                                    }, 'json');
                                });
                            });
                            var openPO_id = 0;
                            var listOpenPOID = [];
                            function bukaPO(po_id, id_status) {
                                var pos = jQuery.inArray(po_id, listOpenPOID);
                                console.log(pos);
                                if (pos == -1) {
                                    $("#body tr." + po_id).removeAttr("style");
                                    $("#body td." + po_id).removeAttr("style");
                                    if (id_status == <?= KEY::$STATUS_NEW ?>) {
                                        warna = '<?= KEY::$WARNA_BIRU ?>';
                                    } else if (id_status == <?= KEY::$STATUS_PAID ?>) {
                                        warna = '<?= KEY::$WARNA_HIJAU ?>';
                                    } else if (id_status == <?= KEY::$STATUS_CANCEL ?>) {
                                        warna = '<?= KEY::$WARNA_MERAH ?>';
                                    }
                                    $("#body tr." + po_id).css("background-color", warna);
                                    $("#body td." + po_id).css("background-color", warna);
                                    listOpenPOID.push(po_id);
                                } else {
                                    console.log("masuk");
                                    $("#body tr." + po_id).css("visibility", "hidden");
                                    $("#body td." + po_id).css("visibility", "hidden");
                                    $("#body tr." + po_id).css("display", "none");
                                    $("#body td." + po_id).css("display", "none");
                                    $(".atas_" + po_id).removeAttr("style");
                                    if (id_status == <?= KEY::$STATUS_NEW ?>) {
                                        warna = '<?= KEY::$WARNA_BIRU ?>';
                                    } else if (id_status == <?= KEY::$STATUS_PAID ?>) {
                                        warna = '<?= KEY::$WARNA_HIJAU ?>';
                                    } else if (id_status == <?= KEY::$STATUS_CANCEL ?>) {
                                        warna = '<?= KEY::$WARNA_MERAH ?>';
                                    }
                                    $("#body tr." + po_id).css("background-color", warna);
                                    $("#body td." + po_id).css("background-color", warna);
                                    listOpenPOID.pop(po_id);
                                    //
                                }
                                if (listOpenPOID.length != 0) {
                                    $(".palingdalam").removeAttr("style");
                                } else {
                                    $(".palingdalam").css("visibility", "display");
                                    $(".palingdalam").css("display", "none");
                                }
                            }
                        </script>
                        <?
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <div class="text-center">
                <button class="btn btn-default" id="loadmore_status_<?= $t; ?>">Load more</button>
            </div>


            <script>
                var page_status = <?= $page; ?>;
                var total_page_status = <?= $jumlahHalamanTotal; ?>;
                $('#loadmore_status_<?= $t; ?>').click(function () {
                    if (page_status < total_page_status) {
                        page_status++;
                        $.get("<?= _SPPATH; ?>BarangWebHelper/lihat_pesanan_load?page=" + page_status, function (data) {
                            $("#body").append(data);
                        });
                        if (page_status > total_page_status)
                            $('#loadmore_status_<?= $t; ?>').hide();
                    } else {
                        $('#loadmore_status_<?= $t; ?>').hide();
                    }
                });
            </script>
        </section>
        <?
    }

    function lihat_pesanan_load()
    {

        if(!$this->logStatus){
            $this->modelLogWebservices->logFunction(__FUNCTION__,0);
            $this->modelLogWebservices->endLog();
        }
        else{
            $this->modelLogWebservices->logFunction(__FUNCTION__,1);
            $this->modelLogWebservices->endLog();
        }


        $myOrgID = AccessRight::getMyOrgID();
        $objPO = new POModel();
        $objPOItems = new POItemModel();
        $acc = new SempoaAccount();
        $page = isset($_GET['page']) ? addslashes($_GET['page']) : 1;
        $limit = KEY::$LIMIT_PO;
        $begin = ($page - 1) * $limit;
        global $db;
        $q = "SELECT * FROM {$objPO->table_name} po  WHERE  po.po_penerima='$myOrgID' ORDER BY po.po_tanggal DESC LIMIT $begin,$limit ";
        $arrPO = $db->query($q, 2);
        $jumlahTotal = $objPO->getJumlah("po_penerima='$myOrgID'");
        $jumlahHalamanTotal = ceil($jumlahTotal / $limit);
        $t = time();
        foreach ($arrPO as $po) {
//                            pr($po->po_status);
            /*
             *

            $arrPOItems = $objPOItems->getWhere("po_id = $po->po_id");
            if ($po->po_status == KEY::$STATUS_NEW)
                $warna = KEY::$WARNA_BIRU;
            elseif ($po->po_status == KEY::$STATUS_PAID)
                $warna = KEY::$WARNA_HIJAU;
            elseif ($po->po_status == KEY::$STATUS_CANCEL)
                $warna = KEY::$WARNA_MERAH;

            $arrname = $acc->getWhere("admin_org_id = '$po->po_pengirim'");

            //hitung total hrg manually roy 14 sep 2016
            $total_satu_po = 0;
            foreach ($arrPOItems as $items) {
                $total_satu_po += $items->harga * $items->qty;
            }

 */

            $pembeli = new Account();
            $pembeli->getByID($po->po_user_id_pengirim);
            $arrPOItems = $objPOItems->getWhere("po_id = $po->po_id");
            if ($po->po_status == KEY::$STATUS_NEW)
                $warna = KEY::$WARNA_BIRU;
            elseif ($po->po_status == KEY::$STATUS_PAID)
                $warna = KEY::$WARNA_HIJAU;
            elseif ($po->po_status == KEY::$STATUS_CANCEL)
                $warna = KEY::$WARNA_MERAH;
            $arrname = $acc->getWhere("admin_org_id = '$po->po_pengirim'");
            //hitung total hrg manually roy 14 sep 2016
            $total_satu_po = 0;
            foreach ($arrPOItems as $items) {
                $total_satu_po += $items->harga * $items->qty;
            }
            ?>


            ?>
            <tr class='<?= $po->po_id ?> atas_<?= $po->po_id ?>' style="background-color: <?= $warna; ?>;">

                <td id='open_<?= $po->po_id ?>' onclick="bukaPO('<?= $po->po_id ?>', '<?= $po->po_status; ?>');">
                    <?= $po->po_id ?>
                    <?
                    //                        pr($arrPOItems);
                    ?>
                    <span class="caret" style="cursor: pointer;"></span>
                </td>
                <td><?= $po->po_tanggal ?></td>
                <td><?= Generic::getTCNamebyID($po->po_pengirim) . "/ " . $pembeli->admin_nama_depan; ?></td>
                <td id='status_po_<?= $po->po_id; ?>'><?
                    if ($po->po_status == 0) {
                        echo "New";
                    } elseif ($po->po_status == 1) {
                        echo "Paid";
                    } elseif ($po->po_status == 99) {
                        echo "Cancel";
                    }
                    ?></td>

                <td class='<?= $po->po_id; ?> palingdalam' style="visibility:hidden;display: none"></td>
                <td class='<?= $po->po_id ?> palingdalam' style="visibility:hidden;display: none"></td>
                <td class='<?= $po->po_id ?> palingdalam' style="visibility:hidden;display: none"></td>
                <td class='<?= $po->po_id ?> palingdalam' style="visibility:hidden;display: none"></td>
                <td><?= "IDR " . idr($total_satu_po); ?></td>

            </tr>
            <?
            foreach ($arrPOItems as $items) {
                ?>
                <tr class='<?= $po->po_id ?>' style="visibility:hidden;display: none">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td id='status_po_<?= $po->po_id; ?>'>
                    </td>
                    <td class='<?= $po->po_id ?>'><?= $items->qty ?></td>
                    <td class='<?= $po->po_id ?> '><?= Generic::getNamaBarangByIDBarang($items->id_barang); ?></td>

                    <td class='<?= $po->po_id ?>'><?= "IDR " . idr($items->harga); ?></td>
                    <td class='<?= $po->po_id ?>'><?= "IDR " . idr($items->harga * $items->qty) ?></td>
                    <td class='<?= $po->po_id ?>'></td>
                </tr>
                <?
            }
            ?>

            <script>
                $("#status_po_<?= $po->po_id; ?>").dblclick(function () {

                    var current = $("#status_po_<?= $po->po_id; ?>").html();
                    if (current == 'New') {
                        var html = "<select id='select_status_<?= $po->po_id; ?>'>" +
                            "<option value='0'>New</option>" +
                            "<option value='1'>Paid</option>" +
                            "<option value='99'>Cancel</option>" +
                            "</select>";
                    }

//                    else if (current == 'Paid') {
//                        var html = "<select id='select_status_<?//= $po->po_id; ?>//'>" +
//                            "<option value='1' selected>Paid</option>" +
//                            "</select>";
//                    } else if (current == 'Cancel') {
//                        var html = "<select id='select_status_<?//= $po->po_id; ?>//'>" +
//                            "<option value='99' selected>Cancel</option>" +
//                            "</select>";
//                    }

                    $("#status_po_<?= $po->po_id; ?>").html(html);
                    $('#select_status_<?= $po->po_id; ?>').change(function () {
                        var id_status = $('#select_status_<?= $po->po_id; ?>').val();
                        var po_id = "<?= $po->po_id; ?>";
                        $('modal_proses_<?=time();?>').modal('show');
                        $.get("<?= _SPPATH; ?>BarangWebHelper/setStatusPO?id_status=" + id_status + "&po_id=" + po_id, function (data) {

                            if (data.status_code) {
                                //success
                                alert(data.status_message);
                                $('modal_proses_<?=time();?>').modal('show');
                                console.log(data.po);
                                console.log(data.stock);
                                lwrefresh(selected_page);
                                if (id_status == 0) {
                                    var html = "New";
                                } else if (id_status == 1) {
                                    var html = "Paid";
                                } else if (id_status == 99) {
                                    var html = "Cancel";
                                }
                                $("#status_po_<?= $po->po_id; ?>").html(html);

                            } else {
                                alert(data.status_message);
                            }
                        }, 'json');
                    });

                });


                var openPO_id = 0;
                var listOpenPOID = [];
                function bukaPO(po_id, id_status) {
                    var pos = jQuery.inArray(po_id, listOpenPOID);
                    console.log(pos);
                    if (pos == -1) {
                        $("#body tr." + po_id).removeAttr("style");
                        $("#body td." + po_id).removeAttr("style");
                        if (id_status == <?= KEY::$STATUS_NEW ?>) {
                            warna = '<?= KEY::$WARNA_BIRU ?>';
                        } else if (id_status == <?= KEY::$STATUS_PAID ?>) {
                            warna = '<?= KEY::$WARNA_HIJAU ?>';
                        } else if (id_status == <?= KEY::$STATUS_CANCEL ?>) {
                            warna = '<?= KEY::$WARNA_MERAH ?>';
                        }
                        $("#body tr." + po_id).css("background-color", warna);
                        $("#body td." + po_id).css("background-color", warna);
                        listOpenPOID.push(po_id);
                    } else {
                        console.log("masuk");
                        $("#body tr." + po_id).css("visibility", "hidden");
                        $("#body td." + po_id).css("visibility", "hidden");
                        $("#body tr." + po_id).css("display", "none");
                        $("#body td." + po_id).css("display", "none");
                        $(".atas_" + po_id).removeAttr("style");

                        if (id_status == <?= KEY::$STATUS_NEW ?>) {
                            warna = '<?= KEY::$WARNA_BIRU ?>';
                        } else if (id_status == <?= KEY::$STATUS_PAID ?>) {
                            warna = '<?= KEY::$WARNA_HIJAU ?>';
                        } else if (id_status == <?= KEY::$STATUS_CANCEL ?>) {
                            warna = '<?= KEY::$WARNA_MERAH ?>';
                        }
                        $("#body tr." + po_id).css("background-color", warna);
                        $("#body td." + po_id).css("background-color", warna);
                        listOpenPOID.pop(po_id);
                        //
                    }

                    if (listOpenPOID.length != 0) {
                        $(".palingdalam").removeAttr("style");
                    } else {

                        $(".palingdalam").css("visibility", "display");
                        $(".palingdalam").css("display", "none");
                    }

                }
            </script>
            <?
        }
    }

    public static function lihat_pesanan_TC()
    {
        $myOrgID = AccessRight::getMyOrgID();
        $objPO = new POModel();
        $objPOItems = new POItemModel();

        $page = isset($_GET['page']) ? addslashes($_GET['page']) : 1;
        $limit = KEY::$LIMIT_PO;
        $begin = ($page - 1) * $limit;
        $arrPO = $objPO->getWhere(" po_pengirim='$myOrgID' ORDER BY po_tanggal DESC LIMIT $begin,$limit");
//        pr($arrPO);
        $jumlahTotal = $objPO->getJumlah("po_pengirim='$myOrgID'");
        $jumlahHalamanTotal = ceil($jumlahTotal / $limit);
        $t = time();
        ?>
        <section class="content-header">
            <h1>History Pemesanan <?= Generic::getTCNamebyID(AccessRight::getMyOrgID()); ?></h1>
        </section>
        <section class="content table-responsive">


            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th><b>No. PO</b></th>
                    <th><b>Tanggal</b></th>
                    <th><b>Pengirim PO</b></th>
                    <th><b>Penerima PO</b></th>
                    <th><b>Status</b></th>
                    <th class="palingdalam" style="visibility:hidden;display: none"><b>Barang</b></th>
                    <th class="palingdalam" style="visibility:hidden;display: none"><b>Qty</b></th>
                    <th class="palingdalam" style="visibility:hidden;display: none"><b>Harga</b></th>
                    <th><b>Total Harga</b></th>

                <tr>
                </thead>
                <tbody id="body_barang_tc_<?= $t; ?>">
                <?
                foreach ($arrPO as $po) {
                    $arrPOItems = $objPOItems->getWhere("po_id = $po->po_id");
//                    pr($arrPOItems);
                    if ($po->po_status == KEY::$STATUS_NEW)
                        $warna = KEY::$WARNA_BIRU;
                    elseif ($po->po_status == KEY::$STATUS_PAID)
                        $warna = KEY::$WARNA_HIJAU;
                    elseif ($po->po_status == KEY::$STATUS_CANCEL)
                        $warna = KEY::$WARNA_MERAH;
                    $acc = new Account();
                    $arrname = $acc->getWhere("admin_org_id = '$po->po_pengirim'");
                    //hitung total hrg manually roy 14 sep 2016
                    $total_satu_po = 0;

                    foreach ($arrPOItems as $items) {
                        $total_satu_po += $items->harga * $items->qty;
                    }
                    ?>
                    <tr class='<?= $po->po_id ?> atas_<?= $t; ?><?= $po->po_id ?>'
                        style="background-color: <?= $warna; ?>;">

                        <td id='open_<?= $t; ?><?= $po->po_id ?>'
                            onclick="bukaPO2('<?= $po->po_id ?>', '<?= $po->po_status; ?>');">
                            <?= $po->po_id ?>

                            <span class="caret" style="cursor: pointer;"></span>
                        </td>
                        <td><?= $po->po_tanggal ?></td>
                        <td><?= $acc->getMyName(); ?></td>
                        <td><?= Generic::getTCNamebyID($po->po_penerima); ?></td>


                        <td id='status_po_<?= $po->po_id; ?>'><?
                            if ($po->po_status == 0) {
                                echo "New";
                            } elseif ($po->po_status == 1) {
                                echo "Paid";
                            } elseif ($po->po_status == 99) {
                                echo "Cancel";
                            }
                            ?></td>

                        <td class='<?= $po->po_id; ?> palingdalam' style="visibility:hidden;display: none"></td>
                        <td class='<?= $po->po_id ?> palingdalam' style="visibility:hidden;display: none"></td>
                        <td class='<?= $po->po_id ?> palingdalam' style="visibility:hidden;display: none"></td>
                        <td><?= "IDR " . idr($total_satu_po); ?></td>

                    </tr>
                <?
                foreach ($arrPOItems as $items) {
                ?>
                    <tr class='<?= $po->po_id ?>' style="visibility:hidden;display: none">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class='<?= $po->po_id ?>'><?= $items->qty ?></td>
                        <td class='<?= $po->po_id ?> '><?= Generic::getNamaBarangByIDBarang($items->id_barang); ?></td>

                        <td class='<?= $po->po_id ?>'><?= "IDR " . idr($items->harga); ?></td>
                        <td class='<?= $po->po_id ?>'><?= "IDR " . idr($items->harga * $items->qty) ?></td>

                    </tr>
                <?
                }
                ?>
                    <script>
                        var openPO_id = 0;
                        var listOpenPOID = [];
                        function bukaPO2(po_id, id_status) {

                            var pos = jQuery.inArray(po_id, listOpenPOID);
                            console.log(pos);
                            if (pos == -1) {
                                $("#body_barang_tc_<?= $t; ?> tr." + po_id).removeAttr("style");
                                $("#body_barang_tc_<?= $t; ?> td." + po_id).removeAttr("style");
                                if (id_status == '<?= KEY::$STATUS_NEW ?>') {
                                    warna = '<?= KEY::$WARNA_BIRU ?>';
                                } else if (id_status == '<?= KEY::$STATUS_PAID ?>') {
                                    warna = '<?= KEY::$WARNA_HIJAU ?>';
                                } else if (id_status == '<?= KEY::$STATUS_CANCEL ?>') {
                                    warna = '<?= KEY::$WARNA_MERAH ?>';
                                }
                                $("#body_barang_tc_<?= $t; ?> tr." + po_id).css("background-color", warna);
                                $("#body_barang_tc_<?= $t; ?> td." + po_id).css("background-color", warna);
                                listOpenPOID.push(po_id);
                            } else {
                                console.log("masuk");
                                $("#body_barang_tc_<?= $t; ?> tr." + po_id).css("visibility", "hidden");
                                $("#body_barang_tc_<?= $t; ?> td." + po_id).css("visibility", "hidden");
                                $("#body_barang_tc_<?= $t; ?> tr." + po_id).css("display", "none");
                                $("#body_barang_tc_<?= $t; ?> td." + po_id).css("display", "none");
                                $(".atas_<?= $t; ?>" + po_id).removeAttr("style");
                                if (id_status == <?= KEY::$STATUS_NEW ?>) {
                                    warna = '<?= KEY::$WARNA_BIRU ?>';
                                } else if (id_status == <?= KEY::$STATUS_PAID ?>) {
                                    warna = '<?= KEY::$WARNA_HIJAU ?>';
                                } else if (id_status == <?= KEY::$STATUS_CANCEL ?>) {
                                    warna = '<?= KEY::$WARNA_MERAH ?>';
                                }
                                $("#body_barang_tc_<?= $t; ?> tr." + po_id).css("background-color", warna);
                                $("#body_barang_tc_<?= $t; ?> td." + po_id).css("background-color", warna);
                                listOpenPOID.pop(po_id);
                                //
                            }
                            console.log("panjang: " + listOpenPOID.length);
                            if (listOpenPOID.length != 0) {
                                $(".palingdalam").removeAttr("style");
                            } else {
                                $(".palingdalam").css("visibility", "display");
                                $(".palingdalam").css("display", "none");
                            }


                        }
                    </script>
                    <?
                }
                ?>
                <?
                ?>
                </tbody>
            </table>
            <!--            <div class="text-center">
                <button class="btn btn-default" id="loadmore_barang_tc_<?= $t; ?>">Load more</button>
            </div>-->
            <script>
                var page_barang_tc = <?= $page; ?>;
                var total_page_barang_tc = <?= $jumlahHalamanTotal; ?>;
                $('#loadmore_barang_tc_<?= $t; ?>').click(function () {

                    if (page_barang_tc < total_page_barang_tc) {
                        page_barang_tc++;
                        $.get("<?= _SPPATH; ?>BarangWebHelper/read_barang_tc_load?page=" + page_barang_tc, function (data) {
                            $("#body_barang_tc_<?= $t; ?>").append(data);
//                            console.log(data);
                        });
                        if (page_barang_tc > total_page_barang_tc)
                            $('#loadmore_barang_tc_<?= $t; ?>').hide();
                    } else {
                        $('#loadmore_barang_tc_<?= $t; ?>').hide();
                    }
                });
            </script>
        </section>
        <?
    }

    function read_barang_tc_load()
    {

        if(!$this->logStatus){
            $this->modelLogWebservices->logFunction(__FUNCTION__,0);
            $this->modelLogWebservices->endLog();
        }
        else{
            $this->modelLogWebservices->logFunction(__FUNCTION__,1);
            $this->modelLogWebservices->endLog();
        }

        $myOrgID = AccessRight::getMyOrgID();
        $objPO = new POModel();
        $objPOItems = new POItemModel();

        $page = isset($_GET['page']) ? addslashes($_GET['page']) : 1;
        $limit = KEY::$LIMIT_PO;
        $begin = ($page - 1) * $limit;

        $arrPO = $objPO->getWhere(" po_pengirim='$myOrgID' ORDER BY po_tanggal DESC LIMIT $begin,$limit");
//        pr($arrPO);
        $jumlahTotal = $objPO->getJumlah("po_pengirim='$myOrgID'");
        $jumlahHalamanTotal = ceil($jumlahTotal / $limit);
        $t = time();

        foreach ($arrPO as $po) {
            $arrPOItems = $objPOItems->getWhere("po_id = $po->po_id");
//                    pr($arrPOItems);
            if ($po->po_status == KEY::$STATUS_NEW)
                $warna = KEY::$WARNA_BIRU;
            elseif ($po->po_status == KEY::$STATUS_PAID)
                $warna = KEY::$WARNA_HIJAU;
            elseif ($po->po_status == KEY::$STATUS_CANCEL)
                $warna = KEY::$WARNA_MERAH;
            $acc = new Account();
            $arrname = $acc->getWhere("admin_org_id = '$po->po_pengirim'");
            //hitung total hrg manually roy 14 sep 2016
            $total_satu_po = 0;

            foreach ($arrPOItems as $items) {
                $total_satu_po += $items->harga * $items->qty;
            }
            ?>
            <tr class='<?= $po->po_id ?> atas_<?= $t; ?><?= $po->po_id ?>' style="background-color: <?= $warna; ?>;">

                <td id='open_<?= $t; ?><?= $po->po_id ?>'
                    onclick="bukaPO2('<?= $po->po_id ?>', '<?= $po->po_status; ?>');">
                    <?= $po->po_id ?>

                    <span class="caret" style="cursor: pointer;"></span>
                </td>
                <td><?= $po->po_tanggal ?></td>
                <td><?= $acc->getMyName(); ?></td>
                <td><?= Generic::getTCNamebyID($po->po_penerima); ?></td>


                <td id='status_po_<?= $po->po_id; ?>'><?
                    if ($po->po_status == 0) {
                        echo "New";
                    } elseif ($po->po_status == 1) {
                        echo "Paid";
                    } elseif ($po->po_status == 99) {
                        echo "Cancel";
                    }
                    ?></td>

                <td class='<?= $po->po_id; ?> palingdalam_<?= $t; ?>' style="visibility:hidden;display: none"></td>
                <td class='<?= $po->po_id ?> palingdalam_<?= $t; ?>' style="visibility:hidden;display: none"></td>
                <td class='<?= $po->po_id ?> palingdalam_<?= $t; ?>' style="visibility:hidden;display: none"></td>
                <td><?= "IDR " . idr($total_satu_po); ?></td>

            </tr>
            <?
            foreach ($arrPOItems as $items) {
                ?>
                <tr class='<?= $po->po_id ?>' style="visibility:hidden;display: none">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class='<?= $po->po_id ?>'><?= $items->qty ?></td>
                    <td class='<?= $po->po_id ?> '><?= Generic::getNamaBarangByIDBarang($items->id_barang); ?></td>

                    <td class='<?= $po->po_id ?>'><?= "IDR " . idr($items->harga); ?></td>
                    <td class='<?= $po->po_id ?>'><?= "IDR " . idr($items->harga * $items->qty) ?></td>

                </tr>
                <?
            }
            ?>
            <script>
                var openPO_id = 0;
                var listOpenPOID = [];
                function bukaPO2(po_id, id_status) {
                    var pos = jQuery.inArray(po_id, listOpenPOID);
                    console.log(pos);
                    if (pos == -1) {
                        $("#body_barang_tc_<?= $t; ?> tr." + po_id).removeAttr("style");
                        $("#body_barang_tc_<?= $t; ?> td." + po_id).removeAttr("style");
                        if (id_status == '<?= KEY::$STATUS_NEW ?>') {
                            warna = '<?= KEY::$WARNA_BIRU ?>';
                        } else if (id_status == '<?= KEY::$STATUS_PAID ?>') {
                            warna = '<?= KEY::$WARNA_HIJAU ?>';
                        } else if (id_status == '<?= KEY::$STATUS_CANCEL ?>') {
                            warna = '<?= KEY::$WARNA_MERAH ?>';
                        }
                        $("#body_barang_tc_<?= $t; ?> tr." + po_id).css("background-color", warna);
                        $("#body_barang_tc_<?= $t; ?> td." + po_id).css("background-color", warna);
                        listOpenPOID.push(po_id);
                    } else {
                        $("#body_barang_tc_<?= $t; ?> tr." + po_id).css("visibility", "hidden");
                        $("#body_barang_tc_<?= $t; ?> td." + po_id).css("visibility", "hidden");
                        $("#body_barang_tc_<?= $t; ?> tr." + po_id).css("display", "none");
                        $("#body_barang_tc_<?= $t; ?> td." + po_id).css("display", "none");
                        $(".atas_<?= $t; ?>" + po_id).removeAttr("style");
                        if (id_status == <?= KEY::$STATUS_NEW ?>) {
                            warna = '<?= KEY::$WARNA_BIRU ?>';
                        } else if (id_status == <?= KEY::$STATUS_PAID ?>) {
                            warna = '<?= KEY::$WARNA_HIJAU ?>';
                        } else if (id_status == <?= KEY::$STATUS_CANCEL ?>) {
                            warna = '<?= KEY::$WARNA_MERAH ?>';
                        }
                        $("#body_barang_tc_<?= $t; ?> tr." + po_id).css("background-color", warna);
                        $("#body_barang_tc_<?= $t; ?> td." + po_id).css("background-color", warna);
                        listOpenPOID.pop(po_id);
                        //
                    }
                    if (listOpenPOID.length != 0) {
                        $(".palingdalam_<?= $t; ?>").removeAttr("style");
                    } else {
                        $(".palingdalam_<?= $t; ?>").css("visibility", "display");
                        $(".palingdalam_<?= $t; ?>").css("display", "none");
                    }
                }
            </script>
            <?
        }
    }

    function read_buku_load()
    {

        if(!$this->logStatus){
            $this->modelLogWebservices->logFunction(__FUNCTION__,0);
            $this->modelLogWebservices->endLog();
        }
        else{
            $this->modelLogWebservices->logFunction(__FUNCTION__,1);
            $this->modelLogWebservices->endLog();
        }

        $myOrgID = AccessRight::getMyOrgID();
        $kpo_id = Generic::getMyParentID($myOrgID);
        $objPO = new POModel();
        $objPOItem = new POItemModel();
        $page = isset($_GET['page']) ? addslashes($_GET['page']) : 1;
        $limit = KEY::$LIMIT_PO;
        $begin = ($page - 1) * $limit;

        global $db;
        $q = "SELECT po.*, pi.* FROM {$objPO->table_name} po INNER JOIN {$objPOItem->table_name} pi ON po.po_id = pi.po_id WHERE po.po_penerima='$kpo_id' AND po.po_pengirim='$myOrgID' LIMIT $begin,$limit ";
        $arrPO = $db->query($q, 2);
        $jumlahTotal = $objPO->getJumlah("po_penerima='$kpo_id' AND po_pengirim='$myOrgID'");
        $jumlahHalamanTotal = ceil($jumlahTotal / $limit);
        $t = time();

        $acc = new Account();

        foreach ($arrPO as $key => $val) {
            if ($val->po_status == KEY::$STATUS_NEW)
                $warna = KEY::$WARNA_BIRU;
            elseif ($val->po_status == KEY::$STATUS_PAID)
                $warna = KEY::$WARNA_HIJAU;
            elseif ($val->po_status == KEY::$STATUS_CANCEL)
                $warna = KEY::$WARNA_MERAH;
            ?>
            <tr style="background-color: <?= $warna; ?>;">

                <td><?= $val->po_tanggal; ?></td>
                <td><?= $acc->getMyName(); ?></td>
                <td><?= $val->po_penerima; ?></td>
                <td id='<?= $val->po_id; ?>'><?
                    if ($val->po_status == KEY::$STATUS_NEW)
                        echo KEY::$NEW;
                    elseif ($val->po_status == KEY::$STATUS_PAID)
                        echo KEY::$Paid;
                    elseif ($val->po_status == KEY::$STATUS_CANCEL)
                        echo KEY::$Cancel;
                    ?></td>
                <td><?= Generic::getNamaBarangByIDKPOID($val->id_barang, $kpo_id); ?></td>
                <td><?= $val->qty; ?></td>
                <td><?= idr($val->harga); ?></td>
                <td><?= idr($val->qty * $val->harga); ?></td>
            </tr>
            <?
        }
    }

    public static function lihat_pesanan()
    {
        $myOrgID = AccessRight::getMyOrgID();
        $objPO = new POModel();
        $objPOItems = new POItemModel();
        $acc = new SempoaAccount();
        $page = isset($_GET['page']) ? addslashes($_GET['page']) : 1;
        $limit = KEY::$LIMIT_PO;
        $begin = ($page - 1) * $limit;
        global $db;
//        $q = "SELECT po.*, poItem.* FROM {$objPO->table_name} po INNER JOIN {$objPOItems->table_name} poItem ON po.po_id = poItem.po_id WHERE  po.po_penerima='$myOrgID' ";
//        $arrPO = $db->query($q, 2);
        $q = "SELECT * FROM {$objPO->table_name} po  WHERE  po.po_penerima='$myOrgID' ORDER BY po.po_tanggal DESC LIMIT $begin,$limit ";
        $arrPO = $db->query($q, 2);
//        pr($arrPO);
        $jumlahTotal = $objPO->getJumlah("po_penerima='$myOrgID'");
        $jumlahHalamanTotal = ceil($jumlahTotal / $limit);
        $t = time();
        ?>
        <section class="content-header">
            <h1>
                <? if (AccessRight::getMyOrgType() == "kpo") {
                    $orgType = "IBO";
                } else {
                    $orgType = "TC";
                }

                ?>
                Pemesanan Barang dan Buku <?= $orgType; ?> ke <?= Generic::getTCNamebyID($myOrgID) ?>
            </h1>

        </section>

        <section class="content">
            <style>
                .caret {
                    display: inline-block;
                    width: 0;
                    height: 0;
                    margin-left: 2px;
                    vertical-align: middle;
                    border-top: 4px solid;
                    border-right: 4px solid transparent;
                    border-left: 4px solid transparent;
                }
            </style>
            <div class="table-responsive">
                <table class="table table-bordered table-striped" style="background-color: #FFFFFF;">
                    <thead class='heading'>
                    <tr>
                        <th><b>No PO</b></th>
                        <th><b>Tanggal</b></th>
                        <th><b><?= $orgType; ?>/ Pemesan</b></th>
                        <th><b>Status</b></th>
                        <th class="palingdalam" style="visibility:hidden;display: none"><b>Qty</b></th>
                        <th class="palingdalam" style="visibility:hidden;display: none"><b>Barang</b></th>
                        <th class="palingdalam" style="visibility:hidden;display: none"><b>No. Buku</b></th>
                        <th class="palingdalam" style="visibility:hidden;display: none"><b>Harga</b></th>
                        <th class="palingdalam" style="visibility:hidden;display: none"><b>Total Harga</b></th>
                        <th><b>Grand Total</b></th>

                    <tr>
                    </thead>
                    <tbody id='body'>
                    <?
                    foreach ($arrPO as $po) {
                        $pembeli = new Account();
                        $pembeli->getByID($po->po_user_id_pengirim);
                        $arrPOItems = $objPOItems->getWhere("po_id = $po->po_id");
                        if ($po->po_status == KEY::$STATUS_NEW)
                            $warna = KEY::$WARNA_BIRU;
                        elseif ($po->po_status == KEY::$STATUS_PAID)
                            $warna = KEY::$WARNA_HIJAU;
                        elseif ($po->po_status == KEY::$STATUS_CANCEL)
                            $warna = KEY::$WARNA_MERAH;
                        $arrname = $acc->getWhere("admin_org_id = '$po->po_pengirim'");
                        //hitung total hrg manually roy 14 sep 2016
                        $total_satu_po = 0;
                        foreach ($arrPOItems as $items) {
                            $total_satu_po += $items->harga * $items->qty;
                        }
                        ?>
                        <tr class='<?= $po->po_id ?> atas_<?= $po->po_id ?>' style="background-color: <?= $warna; ?>;">

                            <td id='open_<?= $po->po_id ?>'
                                onclick="bukaPO(<?= $po->po_id ?>, '<?= $po->po_status; ?>');">
                                <?= $po->po_id ?>

                                <span class="caret" style="cursor: pointer;"></span>
                            </td>
                            <td><?= $po->po_tanggal ?></td>
                            <td><?= Generic::getTCNamebyID($po->po_pengirim) . "/ " . $pembeli->admin_nama_depan; ?></td>
                            <td id='status_po_<?= $po->po_id; ?>'><?
                                if ($po->po_status == 0) {
                                    echo "New";
                                } elseif ($po->po_status == 1) {
                                    echo "Paid";
                                } elseif ($po->po_status == 99) {
                                    echo "Cancel";
                                }
                                ?></td>

                            <td class='<?= $po->po_id; ?> palingdalam' style="visibility:hidden;display: none"></td>
                            <td class='<?= $po->po_id ?> palingdalam' style="visibility:hidden;display: none"></td>

                            <td class='<?= $po->po_id ?> palingdalam' style="visibility:hidden;display: none"></td>

                            <td class='<?= $po->po_id ?> palingdalam' style="visibility:hidden;display: none"></td>
                            <td class='<?= $po->po_id ?> palingdalam' style="visibility:hidden;display: none"></td>
                            <td><?= "IDR " . idr($total_satu_po); ?></td>

                        </tr>
                    <?
                    foreach ($arrPOItems as $items) {
                    ?>
                        <tr class='<?= $po->po_id ?>' style="visibility:hidden;display: none">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td id='status_po_<?= $po->po_id; ?>'>
                            </td>
                            <td class='<?= $po->po_id ?>'><?= $items->qty ?></td>
                            <td class='<?= $po->po_id ?> '><?= Generic::getNamaBarangByIDBarang($items->id_barang); ?></td>

                            <td class='<?= $po->po_id ?>'><?= self::getAwalAkhirNoBuku($po->po_id, AccessRight::getMyOrgType(), $items->id_barang) ?></td>

                            <td class='<?= $po->po_id ?>'><?= "IDR " . idr($items->harga); ?></td>
                            <td class='<?= $po->po_id ?>'><?= "IDR " . idr($items->harga * $items->qty) ?></td>
                            <td class='<?= $po->po_id ?>'></td>
                        </tr>
                    <?
                    }
                    ?>

                        <script>
                            $("#status_po_<?= $po->po_id; ?>").dblclick(function () {
                                var current = $("#status_po_<?= $po->po_id; ?>").html();
                                if (current == 'New') {
                                    var html = "<select id='select_status_<?= $po->po_id; ?>'>" +
                                        "<option value='0'>New</option>" +
                                        "<option value='1'>Paid</option>" +
                                        "<option value='99'>Cancel</option>" +
                                        "</select>";
                                }

                                $("#status_po_<?= $po->po_id; ?>").html(html);
                                $('#select_status_<?= $po->po_id; ?>').change(function () {
                                    var id_status = $('#select_status_<?= $po->po_id; ?>').val();
//                                    alert(id_status);
                                    var po_id = "<?= $po->po_id; ?>";
                                    if (id_status == 1) {
//                                        $('#modal_proses').modal('show');

                                    }
                                    $.get("<?= _SPPATH; ?>BarangWebHelper/setStatusPO?id_status=" + id_status + "&po_id=" + po_id, function (data) {
//                                        lwrefresh("status_po_<?//= $po->po_id; ?>//");
                                        if (data.status_code ) {
                                            lwrefresh("status_po_<?= $po->po_id; ?>");
                                            //success
//                                            alert(data.status_message);
                                            console.log(data);
                                            console.log(data.po);
//                                            console.log(data.stock);
                                            lwrefresh(selected_page);
                                            if (id_status == 0) {
                                                var html = "New";
                                            } else if (id_status == 1) {
                                                var html = "Paid";
                                            } else if (id_status == 99) {
                                                var html = "Cancel";
                                            }
                                            $("#status_po_<?= $po->po_id; ?>").html(html);
                                        }
                                        else {
                                            alert(data.status_message);
                                        }
                                    }, 'json').fail(function() {
                                        alert( "Tidak ada Koneksi internet!" );
                                    })
                                });
                            });
                            var openPO_id = 0;
                            var listOpenPOID = [];
                            function bukaPO(po_id, id_status) {
                                var pos = jQuery.inArray(po_id, listOpenPOID);
//                                console.log(pos);
//                                console.log("Panjang listOpenPOID before: " + listOpenPOID.length);
                                if (pos == -1) {
                                    // pertama kali masuk
                                    $("#body tr." + po_id).removeAttr("style");
                                    $("#body td." + po_id).removeAttr("style");
                                    if (id_status == <?= KEY::$STATUS_NEW ?>) {
                                        warna = '<?= KEY::$WARNA_BIRU ?>';
                                    } else if (id_status == <?= KEY::$STATUS_PAID ?>) {
                                        warna = '<?= KEY::$WARNA_HIJAU ?>';
                                    } else if (id_status == <?= KEY::$STATUS_CANCEL ?>) {
                                        warna = '<?= KEY::$WARNA_MERAH ?>';
                                    }
                                    $("#body tr." + po_id).css("background-color", warna);
                                    $("#body td." + po_id).css("background-color", warna);
                                    listOpenPOID.push(po_id);
                                } else {
//                                    console.log("masuk");
                                    $("#body tr." + po_id).css("visibility", "hidden");
                                    $("#body td." + po_id).css("visibility", "hidden");
                                    $("#body tr." + po_id).css("display", "none");
                                    $("#body td." + po_id).css("display", "none");
                                    $(".atas_" + po_id).removeAttr("style");
                                    if (id_status == <?= KEY::$STATUS_NEW ?>) {
                                        warna = '<?= KEY::$WARNA_BIRU ?>';
                                    } else if (id_status == <?= KEY::$STATUS_PAID ?>) {
                                        warna = '<?= KEY::$WARNA_HIJAU ?>';
                                    } else if (id_status == <?= KEY::$STATUS_CANCEL ?>) {
                                        warna = '<?= KEY::$WARNA_MERAH ?>';
                                    }
                                    $("#body tr." + po_id).css("background-color", warna);
                                    $("#body td." + po_id).css("background-color", warna);
                                    listOpenPOID.pop(po_id);
                                    //
                                }
                                console.log(listOpenPOID);
//                                console.log("Panjang listOpenPOID after: " + listOpenPOID.length);
                                if (listOpenPOID.length != 0) {
                                    $(".palingdalam").removeAttr("style");
                                } else {
                                    $(".palingdalam").css("visibility", "display");
                                    $(".palingdalam").css("display", "none");
                                }
                            }
                        </script>
                        <?
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <div class="text-center">
                <button class="btn btn-default" id="loadmore_status_<?= $t; ?>">Load more</button>
            </div>


            <script>
                var page_status = <?= $page; ?>;
                var total_page_status = <?= $jumlahHalamanTotal; ?>;
                $('#loadmore_status_<?= $t; ?>').click(function () {
                    if (page_status < total_page_status) {

                        page_status++;
                        $.get("<?= _SPPATH; ?>BarangWebHelper/lihat_pesanan_load?page=" + page_status, function (data) {
                            $("#body").append(data);
                        });
                        if (page_status > total_page_status)
                            $('#loadmore_status_<?= $t; ?>').hide();
                    } else {
                        $('#loadmore_status_<?= $t; ?>').hide();
                    }
                });
            </script>
        </section>
        <?
    }

    public static function lihat_pesanan_TC_tmp()
    {
        $myOrgID = AccessRight::getMyOrgID();
        $objPO = new POModel();
        $objPOItem = new POItemModel();

        $page = isset($_GET['page']) ? addslashes($_GET['page']) : 1;
        $limit = KEY::$LIMIT_PO;
        $begin = ($page - 1) * $limit;

        global $db;
        $q = "SELECT po.*, pi.* FROM {$objPO->table_name} po INNER JOIN {$objPOItem->table_name} pi ON po.po_id = pi.po_id WHERE po.po_pengirim='$myOrgID' ORDER BY po.po_tanggal DESC  LIMIT $begin,$limit";
        $arrPO = $db->query($q, 2);
        $jumlahTotal = $objPO->getJumlah("po_pengirim='$myOrgID'");
        $jumlahHalamanTotal = ceil($jumlahTotal / $limit);
        $t = time();
//        pr($q);
        ?>
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th><b>No.</b></th>
                <th><b>Tanggal</b></th>
                <th><b>Pengirim PO</b></th>
                <th><b>Penerima PO</b></th>
                <th><b>Status</b></th>
                <th><b>Barang</b></th>
                <th><b>Qty</b></th>
                <th><b>Harga</b></th>
                <th><b>Total Harga</b></th>

            <tr>
            </thead>
            <tbody id="body_barang_tc_<?= $t; ?>">


            <?
            $acc = new Account();

            foreach ($arrPO as $key => $val) {
                if ($val->po_status == KEY::$STATUS_NEW)
                    $warna = KEY::$WARNA_BIRU;
                elseif ($val->po_status == KEY::$STATUS_PAID)
                    $warna = KEY::$WARNA_HIJAU;
                elseif ($val->po_status == KEY::$STATUS_CANCEL)
                    $warna = KEY::$WARNA_MERAH;
                ?>
                <tr style="background-color: <?= $warna; ?>;">
                    <td id='<?= $val->po_id; ?>'><?= $val->po_id; ?></td>
                    <td><?= $val->po_tanggal; ?></td>
                    <td><?= $acc->getMyName(); ?></td>
                    <td><?= $val->po_penerima; ?></td>
                    <td id='<?= $val->po_id; ?>'><?
                        if ($val->po_status == KEY::$STATUS_NEW)
                            echo KEY::$NEW;
                        elseif ($val->po_status == KEY::$STATUS_PAID)
                            echo KEY::$Paid;
                        elseif ($val->po_status == KEY::$STATUS_CANCEL)
                            echo KEY::$Cancel;
                        ?></td>
                    <td><?= Generic::getNamaBarangByIDBarang($val->id_barang);
                        ?></td>
                    <td><?= $val->qty; ?></td>
                    <td><?= idr($val->harga); ?></td>
                    <td><?= idr($val->total_harga); ?></td>
                </tr>
                <?
            }
            ?>
            </tbody>
        </table>
        <div class="text-center">
            <button class="btn btn-default" id="loadmore_barang_tc_<?= $t; ?>">Load more</button>
        </div>


        <script>
            var page_barang_tc = <?= $page; ?>;
            var total_page_barang_tc = <?= $jumlahHalamanTotal; ?>;
            $('#loadmore_barang_tc_<?= $t; ?>').click(function () {
                if (page_barang_tc < total_page_barang_tc) {
                    page_barang_tc++;
                    $.get("<?= _SPPATH; ?>BarangWebHelper/read_barang_tc_load?page=" + page_barang_tc, function (data) {
                        $("#body_barang_tc_<?= $t; ?>").append(data);
                    });
                    if (page_barang_tc > total_page_barang_tc)
                        $('#loadmore_barang_tc_<?= $t; ?>').hide();
                } else {
                    $('#loadmore_barang_tc_<?= $t; ?>').hide();
                }
            });
        </script>
        <?
    }

    function read_barang_tc_load_tmp()
    {

        if(!$this->logStatus){
            $this->modelLogWebservices->logFunction(__FUNCTION__,0);
            $this->modelLogWebservices->endLog();
        }
        else{
            $this->modelLogWebservices->logFunction(__FUNCTION__,1);
            $this->modelLogWebservices->endLog();
        }

        $myOrgID = AccessRight::getMyOrgID();
        $objPO = new POModel();
        $objPOItem = new POItemModel();

        $page = isset($_GET['page']) ? addslashes($_GET['page']) : 1;
        $limit = KEY::$LIMIT_PO;
        $begin = ($page - 1) * $limit;

        global $db;
        $q = "SELECT po.*, pi.* FROM {$objPO->table_name} po INNER JOIN {$objPOItem->table_name} pi ON po.po_id = pi.po_id WHERE po.po_pengirim='$myOrgID' ORDER BY po.po_tanggal DESC  LIMIT $begin,$limit";
        $arrPO = $db->query($q, 2);

        $jumlahTotal = $objPO->getJumlah("po_pengirim='$myOrgID'");
        $jumlahHalamanTotal = ceil($jumlahTotal / $limit);
        $t = time();

        $acc = new Account();

        foreach ($arrPO as $key => $val) {
            if ($val->po_status == KEY::$STATUS_NEW)
                $warna = KEY::$WARNA_BIRU;
            elseif ($val->po_status == KEY::$STATUS_PAID)
                $warna = KEY::$WARNA_HIJAU;
            elseif ($val->po_status == KEY::$STATUS_CANCEL)
                $warna = KEY::$WARNA_MERAH;
            ?>
            <tr style="background-color: <?= $warna; ?>;">
                <td id='<?= $val->po_id; ?>'><?= $val->po_id; ?></td>
                <td><?= $val->po_tanggal; ?></td>
                <td><?= $acc->getMyName(); ?></td>
                <td><?= $val->po_penerima; ?></td>
                <td id='<?= $val->po_id; ?>'><?
                    if ($val->po_status == KEY::$STATUS_NEW)
                        echo KEY::$NEW;
                    elseif ($val->po_status == KEY::$STATUS_PAID)
                        echo KEY::$Paid;
                    elseif ($val->po_status == KEY::$STATUS_CANCEL)
                        echo KEY::$Cancel;
                    ?></td>
                <td><?= Generic::getNamaBarangByIDBarang($val->id_barang);
                    ?></td>
                <td><?= $val->qty; ?></td>
                <td><?= idr($val->harga); ?></td>
                <td><?= idr($val->total_harga); ?></td>
            </tr>
            <?
        }
    }


    public function getAwalAkhirNoBuku($po_id, $orgType, $barang_id)
    {

        $stockBuku = new StockBuku();
        if ($orgType == KEY::$KPO) {

            $arrStock = $stockBuku->getWhere("stock_po_pesanan_ibo=$po_id AND stock_id_buku = $barang_id ORDER BY stock_buku_id ASC");
        } elseif ($orgType == KEY::$IBO) {
            $arrStock = $stockBuku->getWhere("stock_po_pesanan_tc=$po_id AND stock_id_buku = $barang_id  ORDER BY stock_buku_id ASC");
        } elseif ($orgType == KEY::$TC) {
            $arrStock = $stockBuku->getWhere("stock_invoice_murid=$po_id AND stock_id_buku = $barang_id  ORDER BY stock_buku_id ASC");
        }
        if (count($arrStock) == 1) {
            return $arrStock[0]->stock_buku_no;
        } elseif (count($arrStock) == 0) {
            return "";
        } else {
//            pr($arrStock[0]->stock_buku_no . " - " . $arrStock[count($arrStock)-1]->stock_buku_no);
            return $arrStock[0]->stock_buku_no . " - " . $arrStock[count($arrStock) - 1]->stock_buku_no;
        }
    }
}
