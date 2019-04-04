<?php

/**
 * Komfortkasse
 * Config Class
 * @version 1.7.14-prestashop */
class Komfortkasse_Config
{

    // max 32 char
    const ordernumbers =                'KOMFORTKASSE_ORDERNUMBERS';
    const activate_export =             'KOMFORTKASSE_ACTIVATE_EXPORT';
    const activate_update =             'KOMFORTKASSE_ACTIVATE_UPDATE';
    const payment_methods =             'KOMFORTKASSE_PAYMENT_CODES';
    const status_open =                 'KOMFORTKASSE_STATUS_OPEN';
    const status_paid =                 'KOMFORTKASSE_STATUS_PAID';
    const status_cancelled =            'KOMFORTKASSE_STATUS_CANCELLED';
    const payment_methods_invoice =     'KOMFORTKASSE_PAYMENT_CODES_INV';
    const status_open_invoice =         'KOMFORTKASSE_STATUS_OPEN_INVOICE';
    const status_paid_invoice =         'KOMFORTKASSE_STATUS_PAID_INVOICE';
    const status_cancelled_invoice =    'KOMFORTKASSE_STATUS_CANCELLED_IN';
    const payment_methods_cod =         'KOMFORTKASSE_PAYMENT_CODES_COD';
    const status_open_cod =             'KOMFORTKASSE_STATUS_OPEN_COD';
    const status_paid_cod =             'KOMFORTKASSE_STATUS_PAID_COD';
    const status_cancelled_cod =        'KOMFORTKASSE_STATUS_CANCELLED_CO';
    const encryption =                  'KOMFORTKASSE_ENCRYPTION';
    const accesscode =                  'KOMFORTKASSE_ACCESSCODE';
    const apikey =                      'KOMFORTKASSE_APIKEY';
    const publickey =                   'KOMFORTKASSE_PUBLICKEY';
    const privatekey =                  'KOMFORTKASSE_PRIVATEKEY';


    /**
     * Set Config.
     *
     *
     * @param string $constantKey Constant Key
     * @param string $value Value
     *
     * @return void
     */
    public static function setConfig($constantKey, $value)
    {
        Configuration::updateValue($constantKey, $value);
    }

    // end setConfig()


    /**
     * Get Config.
     *
     *
     * @param string $constantKey Constant Key
     *
     * @return mixed
     */
    public static function getConfig($constantKey, $order = null)
    {
        $id_shop = null;
        if ($order != null) {
            $id_shop = $order ['store_id'];
        }

        return Configuration::get($constantKey, null, null, $id_shop);
    }

    // end getConfig()


    /**
     * Get Request Parameter.
     *
     *
     * @param string $key Key
     *
     * @return string
     */
    public static function getRequestParameter($key)
    {
        $var = Tools::getValue($key);
        if (is_array($var)) {
            return $var;
        } else {
            return urldecode($var);
        }
    }

    // end getRequestParameter()


    /**
     * Get Magento Version.
     *
     *
     * @return string
     */
    public static function getVersion()
    {
        return _PS_VERSION_;
    }
    // end getVersion()

    public static function output($s)
    {
        echo $s;
    }
}//end class