<?php
if (! defined('_PS_VERSION_')) {
    exit();
}

/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 * @author Komfortkasse Integration Team
 * @copyright 2018 LTC Information Services GmbH
 * @license https://creativecommons.org/licenses/by/3.0
 */
function upgrade_module_1_7_9($module)
{

    // shop specific config
    $shops = Db::getInstance()->executeS('SELECT s.id_shop FROM ' . _DB_PREFIX_ . 'shop s');
    foreach ($shops as $shop) {
        Configuration::updateValue('KOMFORTKASSE_PAYMENT_CODES_INV', Configuration::get('KOMFORTKASSE_PAYMENT_CODES_INVOICE', null, null, $shop['id_shop']), false, null, $shop['id_shop']);
        Configuration::updateValue('KOMFORTKASSE_STATUS_CANCELLED_IN', Configuration::get('KOMFORTKASSE_STATUS_CANCELLED_INVOICE', null, null, $shop['id_shop']), false, null, $shop['id_shop']);
        Configuration::updateValue('KOMFORTKASSE_STATUS_CANCELLED_CO', Configuration::get('KOMFORTKASSE_STATUS_CANCELLED_COD', null, null, $shop['id_shop']), false, null, $shop['id_shop']);
    }

    // global config
    Shop::setContext(Shop::CONTEXT_ALL);
    Configuration::updateValue('KOMFORTKASSE_PAYMENT_CODES_INV', Configuration::get('KOMFORTKASSE_PAYMENT_CODES_INVOICE', null, null, null));
    Configuration::updateValue('KOMFORTKASSE_STATUS_CANCELLED_IN', Configuration::get('KOMFORTKASSE_STATUS_CANCELLED_INVOICE', null, null, null));
    Configuration::updateValue('KOMFORTKASSE_STATUS_CANCELLED_CO', Configuration::get('KOMFORTKASSE_STATUS_CANCELLED_COD', null, null, null));

    return true;
}
