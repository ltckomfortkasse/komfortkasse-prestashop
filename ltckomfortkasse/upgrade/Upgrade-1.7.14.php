<?php
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
if (! defined('_PS_VERSION_')) {
    exit();
}

function upgrade_module_1_7_14($module)
{
    // shop specific config
    $shops = Db::getInstance()->executeS('SELECT s.id_shop FROM ' . _DB_PREFIX_ . 'shop s');
    foreach ($shops as $shop) {
        Configuration::updateValue('KOMFORTKASSE_ORDERNUMBERS', 'number', false, null, $shop['id_shop']);
    }

    // global config
    Shop::setContext(Shop::CONTEXT_ALL);
    Configuration::updateValue('KOMFORTKASSE_ORDERNUMBERS', 'number');

    return true;
}
