<?php
/**
 * Komfortkasse
* routing
*
* @version 1.4.3-prestashop
*/

ini_set('default_charset', 'utf-8');

// error_reporting(E_ALL);
// ini_set('display_errors', '1');

require('../../../config/config.inc.php');
include_once 'Komfortkasse.php';

$action = Komfortkasse_Config::getRequestParameter('action');

$kk = new Komfortkasse();
$kk->$action();

?>