<?php

$db_host = 'localhost';
$db_user = 'victor';
$db_pass = '0272';
$db_db = 'school46';
$db_driver = 'mysqli';

$url_name = 'http://soap.local/soap.php';

// русский стандарт времени
setlocale(LC_TIME, "ru_RU");

require_once "adodb5/adodb.inc.php";
require_once "adodb5/tohtml.inc.php";