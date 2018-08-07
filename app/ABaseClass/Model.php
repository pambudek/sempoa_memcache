<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Model
 *
 * @author User
 */
class Model extends Leap\Model\Model
{
    public $default_read_coloms;

    /*
     * @return array of objects
     */
    public function getWhere($whereClause, $selectedColom = "*")
    {
        global $db;
        $q = "SELECT {$selectedColom} FROM {$this->table_name} WHERE $whereClause";
//        pr($q);
        $muridkelas = $db->query($q, 2);
        $newMurid = array();
        $classname = get_called_class();
        foreach ($muridkelas as $databasemurid) {

            $m = new $classname();
            $m->fill(toRow($databasemurid));
            $newMurid[] = $m;
        }

        return $newMurid;
    }

    public function loadToSession($whereClause = '', $selectedColom = "*")
    {
        global $db;
        if ($whereClause != '') {
            $where = " WHERE " . $whereClause;
        }
        $q = "SELECT {$selectedColom} FROM {$this->table_name} $where";

        $_SESSION[get_class(this)] = $db->query($q, 2);
    }

    public function getFromSession()
    {
        return $_SESSION[get_class(this)];
    }

    public function getWhereFromMultipleTable($whereClause, $arrTables = array(), $selectedColom = "*")
    {
        global $db;
        //implode the tables
        if (count($arrTables) < 1) {
            die("please use normal getWhere");
        }
        foreach ($arrTables as $tableClassname) {
            $m = new $tableClassname();
            $imp[] = $m->table_name;
        }

        $implode = implode(",", $imp);

        $q = "SELECT {$selectedColom} FROM {$this->table_name},$implode WHERE $whereClause";

        $muridkelas = $db->query($q, 2);
        $newMurid = array();
        $classname = get_called_class();
        foreach ($muridkelas as $databasemurid) {

            $m = new $classname();
            $m->fill(toRow($databasemurid));
            $newMurid[] = $m;
        }

        return $newMurid;
    }

    public function exportIt($return)
    {
        return parent::exportIt($return);
        $filename = $return['classname'] . "_" . date('Ymd');
        $xls = new Excel($filename);


        //$filename = $return['classname'] . "_" . date('Ymd') . ".xls";

        //header("Content-Disposition: attachment; filename=\"$filename\"");
        //header("Content-Type: application/vnd.ms-excel");
        //$flag = false;
        $xls->home();
        foreach ($return['objs'] as $key => $obj) {

            foreach ($obj as $name => $value) {
                $xls->label(Lang::t($name));
                $xls->right();
            }
            break;
        }
        $xls->down();

        //print("\n");
        foreach ($return['objs'] as $key => $obj) {
            $xls->home();
            foreach ($obj as $name => $value) {
                $xls->label($value);
                $xls->right();
            }
            $xls->down();
        }
        $xls->send();
        exit;
    }

    public function saveAsDraft($app_id, $type)
    {

        $draft = new DraftModel();
        $draft->draft_id = $app_id . "_" . $type;
        $draft->draft_app_id = $app_id;
        $draft->draft_date = leap_mysqldate();
        $draft->draft_obj = serialize(Crud::clean2printEinzeln($this));
        $draft->draft_type = $type;
        $draft->draft_cl_name = get_called_class();
        return $draft->save(1);

    }

    public function loadMyDrafts($app_id)
    {

        $draft = new DraftModel();
        $arr = $draft->getWhere("draft_app_id = $app_id");
        return $arr;
    }

    public function loadObjectsFromDraft($app_id)
    {

        $arr = $this->loadMyDrafts($app_id);
//        pr($arr);
//        die();\
        $return = array();

        foreach ($arr as $draft) {
            $cname = $draft->draft_cl_name;
            if (class_exists($cname)) {
                //echo "exist".$nsClassname;
                $code = "\$new = new " . $cname . "();";
                eval($code);

                $new->fill(unserialize($draft->draft_obj));
//                $new->save(1);

//                $draft->delete($draft->draft_id);

                $return[] = $new;
            }
        }
        return $return;
    }


    public function updateRealDatabase($app_id)
    {

        $arr = $this->loadMyDrafts($app_id);
        pr($arr);
//        die();
        foreach ($arr as $draft) {
            $cname = $draft->draft_cl_name;
            if (class_exists($cname)) {
                //echo "exist".$nsClassname;
                $code = "\$new = new " . $cname . "();";
                eval($code);

                $new->fill(unserialize($draft->draft_obj));
                $new->save(1);

//                $draft->delete($draft->draft_id);

                pr($new);
            }
        }
    }

    //ADD Memcached 8 jan 2017
    public function getByID($id, $readcoloums = "*")
    {
        global $domain;
        global $folder;
        $cls = $domain . $folder . "/" . get_called_class() . "_c_";

        global $memcache;
        $mc = $memcache->memcache;
        $cacheAvailable = $memcache->cacheAvailable;

//        $memcache = new Memcache;
//        $cacheAvailable = $memcache->connect(MEMCACHED_HOST, MEMCACHED_PORT);
        // Initialize our $product variable
        $product = null;

// First we check that our cache server is available
// The $cacheAvailable variable was initialized when we connected to our cache server
        if ($cacheAvailable == true) {
            $key = $cls . '_' . $id;
            $product = $mc->get($key);
//            pr($product);
//            pr($key);

        }


        if (($product != null)) {

            $prod = toRow($product);
            $this->fill($prod);
            $this->load = 1;
//            echo "use memcache" . $key;

        } else {
//            echo "db ".$this->table_name;
            global $db;
            $q = "SELECT $readcoloums FROM {$this->table_name} WHERE {$this->main_id} = '$id'";
            pr($q);
            $obj = $db->query($q, 1);
            $row = toRow($obj);
            $this->fill($row);
            $this->load = 1;
            $main = $this->main_id;
            if ($this->$main != $id) {
                $this->not_found = 1;
            }

            if ($cacheAvailable == true) {
                $mc->set($key, $obj);
            }
        }
    }

    public function save($onDuplicateKey = 0, $printQuery = 0)
    {

        global $domain;
        global $folder;
        //default insert adalah tanpa syarat, kalau mau ada syarat sebaiknya di filter dulu sebelum di insert
        // filternya pakai subclasse method save
        $colomlist = $this->getColumnlist();
        $insertStr = array();
        $updateStr = array();
        $mainValue = "";
        $useQID = 0;
        $load = (isset($this->load) ? addslashes($this->load) : 0);
        foreach ($colomlist as $colom) {
            //cek if use query id
            if ($colom->Extra == "auto_increment") {
                if (!$load) {
                    $useQID = 1;
                }
            }

            $field = $colom->Field;
            $post = (isset($this->{$field}) ? addslashes($this->{$field}) : '');
            /*if ($post == '') {
                continue;
            }*/
            if ($field == $this->main_id) {
                $mainValue = $post;
                $this->qid = $post;
                if ($colom->Extra == "auto_increment") {
                    if (!$onDuplicateKey) {
                        continue;
                    }
                }
            }
            /*
             * cek apakah field ini adalah date
             * jika iya kita normalize
             *
             */
            if ($colom->Type == "date") {
                $post = date("Y-m-d", strtotime($post));
            }
            //kalau date time
            if ($colom->Type == "datetime") {
                $post = date("Y-m-d H:i:s", strtotime($post));
            }

            $insertStr[] = " {$field} = '$post' ";

            if ($field != $this->main_id) {
                $updateStr[] = " {$field} = '$post' ";
            }

        }
        $insertStrImp = implode(",", $insertStr);
        $updateStrImp = implode(",", $updateStr);

        //on duplicate key
        $onDuplicateKeyString = "";
        if ($onDuplicateKey) {
            $onDuplicateKeyString = " ON DUPLICATE KEY UPDATE $insertStrImp";
        }

        $q = "INSERT INTO {$this->table_name} SET $insertStrImp $onDuplicateKeyString";

        if ($load) {
            $q = "UPDATE {$this->table_name} SET $updateStrImp WHERE {$this->main_id} = '$mainValue' ";
        }

        if ($printQuery) echo $q;

        global $db;


        $cls = $domain . $folder . "/" . get_called_class() . "_c_";
        global $memcache;
        $mc = $memcache->memcache;
        $cacheAvailable = $memcache->cacheAvailable;

        //cara hack..jangan dikopi ke yang lain hrsnya pakai onSaveSuccess
        if (get_called_class() == "CarouselMobile") {
            if ($cacheAvailable == true) {
                $mc->delete($domain . $folder . "CarouselMobile_Webservice");
            }
        }
        if (get_called_class() == "LL_Program") {
            if ($cacheAvailable == true) {
                $mc->delete($domain . $folder . "Offer_Webservice");
            }
        }
        if (get_called_class() == "LL_News") {
            if ($cacheAvailable == true) {
                $mc->delete($domain . $folder . "News_Webservice");
            }
        }
        if (get_called_class() == "LL_AboutUs") {
            if ($cacheAvailable == true) {
                $mc->delete($domain . $folder . "aboutus_Webservice");
            }
        }
        if (get_called_class() == "Efiwebsetting") {
            if ($cacheAvailable == true) {
                $mc->delete($domain . $folder . "websetting_ws");
//                $mc->delete("version_ws"); //version
            }
        }

        //echo $q;
        //return 0,1 utk cek masuk ga hasilnya
        //use qid kalau id nya dibutuhkan
        if ($useQID) {

//            echo "masuk qid";
            //add memcached

//            $memcache = new Memcache;
//            $cacheAvailable = $memcache->connect(MEMCACHED_HOST, MEMCACHED_PORT);

            $this->qid = $db->qid($q);

            $this->onSaveSuccess($this->qid);
            $this->onSaveNewItemSuccess($this->qid);

            $this->{$this->main_id} = $this->qid;

            $key = $cls . '_' . $this->qid;

            // We store an associative array containing our product data
            $product = \Crud::clean2printEinzelnWithColoums($this);

            if ($cacheAvailable == true) {
                // And we ask Memcached to store that data
                $mc->set($key, $product);
            }


            return $this->qid;
        }


        //echo $q;
        $succ = $db->query($q, 0);
        if ($succ) {

            //add memcached
            if ($this->load) {

//                echo "masuk load";
                //add memcached

//                $memcache = new Memcache;
//                $cacheAvailable = $memcache->connect(MEMCACHED_HOST, MEMCACHED_PORT);

                $mid = $this->{$this->main_id};


                $key = $cls . '_' . $mid;

                // We store an associative array containing our product data
                $product = \Crud::clean2printEinzelnWithColoums($this);

                // And we ask Memcached to store that data
                if ($cacheAvailable == true) {
                    $mc->set($key, $product);
                }
            }
            else{
                $mid = $this->{$this->main_id};


                $key = $cls . '_' . $mid;

                // We store an associative array containing our product data
                $product = \Crud::clean2printEinzelnWithColoums($this);

                // And we ask Memcached to store that data
                if ($cacheAvailable == true) {
                    $mc->set($key, $product);
                }
            }

            if (!$load) $this->onSaveNewItemSuccess($this->qid);
            $this->onSaveSuccess($this->qid);
            return 1;
        } else
            return 0;

    }

    public function delete($id)
    {
        global $domain;
        global $folder;
        //$id = (isset($_GET['id'])?addslashes($_GET['id']):0);
        if (!isset($id)) {
            return 0;
        } else {
            if ($id < 0) {
                return 0;
            }
            if ($id == '') {
                return 0;
            }
        }
        //return 0,1 utk cek masuk ga hasilnya

        global $db;
        $q = "DELETE FROM {$this->table_name} WHERE {$this->main_id} = '$id'";

        //echo $q;
        $bool = $db->query($q, 0);

        //delete yang di cache
        $cls = $domain . $folder . "/" . get_called_class() . "_c_";

        global $memcache;
        $mc = $memcache->memcache;
        $cacheAvailable = $memcache->cacheAvailable;


        // Initialize our $product variable
        $product = null;

// First we check that our cache server is available
// The $cacheAvailable variable was initialized when we connected to our cache server
        if ($cacheAvailable == true) {
            $key = $cls . '_' . $id;
            $mc->delete($key);

            //cara hack..jangan dikopi ke yang lain hrsnya pakai onSaveSuccess
            if (get_called_class() == "CarouselMobile") {
                if ($cacheAvailable == true) {
                    $mc->delete("CarouselMobile_Webservice");
                }
            }
        }


        return $bool;
    }

}
