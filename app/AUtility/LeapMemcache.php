<?php
/**
 * Created by PhpStorm.
 * User: elroy
 * Date: 1/8/17
 * Time: 1:23 PM
 */

class LeapMemcache {

    var $memcache;
    var $cacheAvailable = false;

    public function __construct() {
        // Connection constants
        define('MEMCACHED_HOST', '127.0.0.1');
        define('MEMCACHED_PORT', '11211');

// Connection creation
//        $memcache = new Memcache;
//        $cacheAvailable = $memcache->connect(MEMCACHED_HOST, MEMCACHED_PORT);
//
//        $this->cacheAvailable = $cacheAvailable;
//        $this->memcache = $memcache;
//        $this->cacheAvailable=false;
    }

    public static function addInboxUsingAccID($client_camp_id,$acc_id,$cardNr,$message,$url,$useGateway=1){

        global $memcache;

        $cacheAvailable = $memcache->cacheAvailable;

        $key = 'inbox_' . $acc_id;


        if ($cacheAvailable == true)
        {
            $mc = $memcache->memcache;

            $product = $mc->get($key);


//            pr($product);
//            pr(is_array($product));
            if(($product!=null)) {
                $arr = array();
                $cnt = 0;
                foreach($product as $keyarr=>$obj){

                    if($obj['camp_id'] == $client_camp_id){
//                        unset($product[$key]);
                        continue;
                    }
                    $arr[] = $obj;
                    if($cnt>20){
//                        unset($product[$key]);
                        break;
                    }
                    $cnt++;
                }
                echo "in";
                echo $cnt;
            }else{
                $arr = array();
            }

            $obj = array();
            $obj['lid'] = $client_camp_id;
            $obj['camp_title'] = $message;
            $obj['camp_date'] = leap_mysqldate(); //pakai log date
            $obj['camp_id'] = $client_camp_id;
            $obj['url'] = $url;
            $obj['use_gateway'] = $useGateway;
            $obj['use_cache'] = 1;

            //ambil append dan close
//            array_push($product,$obj);
            $arr[] = $obj;
//            pr($arr);
            //ambil baru
            $mc->set($key, $arr);

//            $a = $mc->get($key);
//            pr($a);
        }
    }
} 