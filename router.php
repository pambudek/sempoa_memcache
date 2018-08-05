<?php

/* **********************************

 * 

 *        Leap System eLearning

 *      @author elroy,efindi,budiarto

 *          www.leap-systems.com

 * 

 ************************************/
ini_set('display_errors', '1');
error_reporting(E_ALL);
/******************************
 *  LOAD All Frameworks
 *  using Leap loosely coupled Object Oriented Framework
 *  using PHP Framework Interop Group Standard
 *****************************/

require_once 'SplClassLoader.php';

//enginepath

$enginepath = 'framework';

//namespace or vendorname

$ns = "Leap";

//autoload all Classes in the FrameWork

$loader = new SplClassLoader($ns, $enginepath);

$loader->register();


//get the init class, kalau tidak ada perubahan juga bisa langsung pakai Init yang di framework

//use Leap\InitLeap;

require_once 'Init.php'; // pembedanya adalah yg disini untuk load yg local classes saja

//get global functions 

require_once 'functions.php';
include_once 'vp/Veritrans.php';
include_once 'phpexcel/Classes/PHPExcel.php';

// Include the composer autoloader
if(!file_exists(__DIR__ .'/vendor/autoload.php')) {
    echo "The 'vendor' folder is missing. You must run 'composer update' to resolve application dependencies.\nPlease see the README for more information.\n";
    exit(1);
}
require_once __DIR__ . '/vendor/autoload.php';

/******************************
 *  AUTO LOAD Apps
 *****************************/


// LOAD Leap eLearning Apps

/*$pathToApps = 'app';

//namespace

$nsToApps = "LeapElearning";

//autoload all Classes in the FrameWork

$loader = new SplClassLoader($nsToApps, $pathToApps);

$loader->register();

*/

$di = new RecursiveDirectoryIterator('app', RecursiveDirectoryIterator::SKIP_DOTS);
//pr($di);
$it = new RecursiveIteratorIterator($di);
//pr($it);

//sort function
$files2Load = array ();
foreach ($it as $file) {

    if (pathinfo($file, PATHINFO_EXTENSION) == "php") {
        $files2Load[] = $file->getPathname();
    }
}
sort($files2Load);
//pr($files2Load);

foreach ($files2Load as $file) {
    //penambahan mold
    if (strpos($file, 'BPlugin') && (strpos($file, 'Mold') || strpos($file, 'functions.php'))) {
        continue;
    }
    //echo $file;echo "<br>";
    require_once $file;

}

// include db setting, web setting, and paths

require_once 'include/access.php';


$init = new Init($mainClass, $DbSetting, $WebSetting, $timezone, $js, $css, $nameSpaceForApps);

//starting the session
session_start();
//pr($WebSetting);
//Init Languange

$lang = new Lang($WebSetting['lang']);

$lang->activateLangSession();

$lang->activateGetSetLang();
//pr($lang);
//pr($_SESSION);
$selected_lang = Lang::getLang();
if (!isset($selected_lang) || $selected_lang == "" || is_object($selected_lang)) {
    $selected_lang = "en";
}

//pr($selected_lang);

//echo "lang/".strtolower($selected_lang).".php";
require_once("lang/" . strtolower($selected_lang) . ".php");

//get globals

$db = $init->getDB();

$params = $init->getParams();

$template = $init->getTemplate();

$memcache = new LeapMemcache();

//theme selection
if (strpos($_GET['url'], 'PushHome') !== false){
    $themepath = 'adminlte2';
}else{
    $themepath = ThemeItem::getTheme();
}
//echo $themepath;
//overwrite all
//$themepath = 'adminlte2';

$init->setThemeDynamic($themepath);

//include the functions 
foreach ($files2Load as $file) {
    //penambahan mold
    if (strpos($file, 'BPlugin') && strpos($file, 'functions.php')) {
        require_once $file;
    }
    //echo $file;echo "<br>";
}
//set session for photopath and photourl
$_SESSION['photopath'] = _PHOTOPATH;
$_SESSION['photourl'] = _SPPATH._PHOTOURL;

//coba di load websettingnya
$ef = new Efiwebsetting();
$ef->loadToSession();
//pr($_SESSION);
//echo Efiwebsetting::getWebTitle();
//pr($init);
$template->setTitle(Efiwebsetting::getWebTitle());
$template->setMetaKey(Efiwebsetting::getWebMetaKey());
$template->setMetaDesc(Efiwebsetting::getWebMetaDesc());
//pr($template);




//tambahan sebelum run utk wadah registor Modal
$modalReg = new ModalRegistor();

$init->run();


