<?php
/**
 * Komfortkasse
 * routing
 *
 * @author Komfortkasse Integration Team
 * @copyright 2018-2023 LTC Information Services GmbH
 * @license https://creativecommons.org/licenses/by/3.0
 * @version 1.10.6-prestashop
 */

// error_reporting(E_ALL);
// ini_set('display_errors', '1');

require ('../../../config/config.inc.php');

if (!defined('_PS_VERSION_')) {
    exit;
}

ini_set('default_charset', 'utf-8');

include_once 'Komfortkasse.php';

$action = Komfortkasse_Config::getRequestParameter('action');

$kk = new Komfortkasse();

$kk->$action();

?>