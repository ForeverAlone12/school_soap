<?php

//Header("Expires: 0");
//Header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
//Header("Cache-Control: no-cache, must-revalidate");
//Header("Cache-Control: post-check=0,pre-check=0", false);
//Header("Cache-Control: max-age=0", false);
//Header("Pragma: no-cache");

require_once("../include/config.inc.php");
require_once("../include/soap.inc.php");

// 
$server = new SoapServer(null, array('uri' => $url_name,
    'encoding' => "utf8",
    'trace' => TRUE));

// подключение к БД
$conn = newAdoConnection($db_driver);
$conn->PConnect($db_host, $db_user, $db_pass, $db_db) or die("Нет соединения с БД(((");

//установка кодировки
$sql_cod = 'SET NAMES utf8';
$conn->Execute($sql_cod);

//использование русских дат
$sql_date = "SET lc_time_names='ru_RU'";
$conn->Execute($sql_date);

$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';

// Поиск вызываемой функции
//$pattern = '/<ns1:(\w*)[\/]{0,1}>/';
//preg_match($pattern, $HTTP_RAW_POST_DATA, $matches);
//if (count($matches) > 0) {
//    // Примитивная защита от инъекций с целью подключить другой файл
//    $matches[1] = str_replace("/", "\\", str_replace(".", "", strip_tags($matches[1])));
//    // Подключении функции
//    $filename = "../functions/func_" . $soap_functions[$matches[1]] . ".php";
//    if (file_exists($filename)) {
//        include($filename);
//        $server->addFunction($matches[1]);
//    } else {
//       
//    }
//} else {
//    
//}
////
//// подключение файлов с функциями
include ('../functions/func_data.php');
include ('../functions/func_users.php');

$server->addFunction("GetUzvers");
$server->addFunction("createUzvers");
$server->addFunction("createUzverExpa");
$server->addFunction("CheckUserLogin");
$server->addFunction("UzverCommonInfo");
$server->addFunction("executeQuery");
$server->addFunction("getListPosition");
$server->addFunction("getListSystem");
$server->addFunction("getPosition");
$server->addFunction("isSetPosition");
$server->addFunction("setPosition");
$server->addFunction("updatePosition");
$server->addFunction("delUzver");
$server->handle();
