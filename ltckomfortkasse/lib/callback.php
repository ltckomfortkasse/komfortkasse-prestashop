<?php
/**
 * Komfortkasse
 * routing
 *
 * @author Komfortkasse Integration Team
 * @copyright 2018-2023 LTC Information Services GmbH
 * @license https://creativecommons.org/licenses/by/3.0
 * @version 1.10.8-prestashop
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

// Only the actions Komfortkasse calls; never dispatch to arbitrary public methods.
$allowedActions = array(
    'init',
    'test',
    'readorders',
    'updateorders',
    'info',
    'readrefunds',
    'updaterefunds',
    'readinvoicepdf',
    'readconfig',
    'apitest'
);
if (! is_string($action) || ! in_array($action, $allowedActions, true)) {
    header('HTTP/1.1 400 Bad Request');
    exit;
}

$kk = new Komfortkasse();

$kk->$action();

?>