<?php

/**
 * Komfortkasse Order Class
 * in KK, an Order is an Array providing the following members:
 * number, date, email, customer_number, payment_method, amount, currency_code, exchange_rate, language_code, invoice_number, store_id
 * status: data type according to the shop system
 * delivery_ and billing_: _firstname, _lastname, _company, _street, _postcode, _city, _countrycode
 * products: an Array of item numbers
 *
 * @version 1.8.4-prestashop
 */
$order_extension = false;
if (file_exists("Komfortkasse_Order_Extension.php") === true) {
    $order_extension = true;
    include_once "Komfortkasse_Order_Extension.php";
}
class Komfortkasse_Order
{


    /**
     * Get open order IDs.
     *
     * @return string all order IDs that are "open" and relevant for transfer to kk
     */
    public static function getOpenIDs()
    {
        $ret = array ();

        $status_prepayment = Komfortkasse_Config::getConfig(Komfortkasse_Config::status_open);
        $methods_prepayment = Komfortkasse_Config::getConfig(Komfortkasse_Config::payment_methods);
        $status_invoice = Komfortkasse_Config::getConfig(Komfortkasse_Config::status_open_invoice);
        $methods_invoice = Komfortkasse_Config::getConfig(Komfortkasse_Config::payment_methods_invoice);
        $status_cod = Komfortkasse_Config::getConfig(Komfortkasse_Config::status_open_cod);
        $methods_cod = Komfortkasse_Config::getConfig(Komfortkasse_Config::payment_methods_cod);

        $use_prepayment = $methods_prepayment && $status_prepayment;
        $use_invoice = $methods_invoice && $status_invoice;
        $use_cod = $methods_cod && $status_cod;

        if (!$use_prepayment && !$use_invoice && !$use_cod)
            return ret;

        $sql = 'SELECT id_order, reference
				FROM ' . (string)_DB_PREFIX_ . 'orders o
				WHERE 0 ';
        if ($use_prepayment)
            $sql .= ' or (o.current_state in (' . (string)$status_prepayment . ') and o.module in (' . (string)self::quote("$methods_prepayment") . '))';
        if ($use_invoice)
            $sql .= ' or (o.current_state in (' . (string)$status_invoice . ') and o.module in (' . (string)self::quote("$methods_invoice") . '))';
        if ($use_cod)
            $sql .= ' or (o.current_state in (' . (string)$status_cod . ') and o.module in (' . (string)self::quote("$methods_cod") . ')';

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        $use_id = Komfortkasse_Config::getConfig(Komfortkasse_Config::ordernumbers) == 'id';
        foreach ($result as $order) {
            $ret [] = $use_id ? $order ['id_order'] : $order ['reference'];
        }

        return $ret;

    }


    // end getOpenIDs()
    private static function quote($csv)
    {
        return '\'' . str_replace(',', '\',\'', $csv) . '\'';

    }


    /**
     * Get refund IDS.
     *
     * @return string all refund IDs that are "open" and relevant for transfer to kk
     */
    public static function getRefundIDs()
    {
        $ret = array ();
        return $ret;

    }


    // end getRefundIDs()

    /**
     * Get order.
     *
     * @param string $number order number
     *
     * @return array order
     */
    public static function getOrder($number)
    {
        if (empty($number) === true) {
            return null;
        }

        $use_id = Komfortkasse_Config::getConfig(Komfortkasse_Config::ordernumbers) == 'id';
        if ($use_id) {
            $order = new Order($number);
        } else {
            $orderColl = Order::getByReference($number);
            if ($orderColl->count() != 1)
                return null;
            $id = $orderColl->getFirst()->id;
            $order = new Order($id);
        }


        if (empty($order) === true) {
            // try with the other number
            if (!$use_id) {
                $order = new Order($number);
            } else {
                $orderColl = Order::getByReference($number);
                if ($orderColl->count() != 1)
                    return null;
                $id = $orderColl->getFirst()->id;
                $order = new Order($id);
            }
        }

        if (empty($order) === true) {
            return null;
        }

        $ret = array ();
        $ret ['store_id'] = $order->id_shop;
        if (Komfortkasse_Config::getConfig(Komfortkasse_Config::ordernumbers) == 'id') {
            $ret ['number'] = $order->id;
            $ret ['id'] = $order->reference;
        } else {
            $ret ['number'] = $order->reference;
            $ret ['id'] = $order->id;
        }
        $ret ['status'] = $order->getCurrentState();
        $status_full = $order->getCurrentStateFull(Configuration::get('PS_LANG_DEFAULT'));
        if (is_array($status_full))
            $ret ['status_name'] = $status_full ['name'];
        $ret ['date'] = date('d.m.Y', strtotime($order->date_add));
        $ret ['email'] = $order->getCustomer()->email;
        $ret ['customer_number'] = $order->id_customer;
        $ret ['payment_method'] = $order->module;
        $ret ['amount'] = $order->total_paid_tax_incl;
        $currency = new Currency($order->id_currency);
        if ($currency)
            $ret ['currency_code'] = $currency->iso_code;
        $ret ['exchange_rate'] = $order->conversion_rate;

        // Rechnungsnummer und -datum
        foreach ($order->getInvoicesCollection() as $invoice) {
            if (is_object($invoice) && $invoice->number) {
                // nicht getInvoiceNumberFormatted() verwenden, da wir sonst nie wieder auf die ID zurückkommen
                $ret ['invoice_number'] [] = str_pad($invoice->number, 6, '0', STR_PAD_LEFT);
                $invoiceDate = date('d.m.Y', strtotime($invoice->date_add));
                if (!array_key_exists('invoice_date', $ret) || $ret ['invoice_date'] == null || strtotime($ret ['invoice_date']) < strtotime($invoiceDate)) {
                    $ret ['invoice_date'] = $invoiceDate;
                }
            }
        }

        $shippingAddress = new Address($order->id_address_delivery);
        if ($shippingAddress) {
            $ret ['delivery_firstname'] = utf8_encode($shippingAddress->firstname);
            $ret ['delivery_lastname'] = utf8_encode($shippingAddress->lastname);
            $ret ['delivery_company'] = utf8_encode($shippingAddress->company);
            $ret ['delivery_street'] = utf8_encode(trim($shippingAddress->address1 . ' ' . $shippingAddress->address2));
            $ret ['delivery_postcode'] = utf8_encode($shippingAddress->postcode);
            $ret ['delivery_city'] = utf8_encode($shippingAddress->city);
            $country = new Country($shippingAddress->id_country);
            if ($country)
                $ret ['delivery_countrycode'] = utf8_encode($country->iso_code);
        }

        $ret ['language_id'] = $order->id_lang;
        $lang = new Language($order->id_lang);
        if ($lang) {
            $ret ['language_code'] = $lang->iso_code;
        }

        $billingAddress = new Address($order->id_address_invoice);
        if ($billingAddress) {
            $ret ['billing_firstname'] = utf8_encode($billingAddress->firstname);
            $ret ['billing_lastname'] = utf8_encode($billingAddress->lastname);
            $ret ['billing_company'] = utf8_encode($billingAddress->company);
            $ret ['billing_street'] = utf8_encode(trim($billingAddress->address1 . ' ' . $billingAddress->address2));
            $ret ['billing_postcode'] = utf8_encode($billingAddress->postcode);
            $ret ['billing_city'] = utf8_encode($billingAddress->city);
            $country = new Country($billingAddress->id_country);
            if ($country && $country->iso_code) {
                $ret ['billing_countrycode'] = utf8_encode($country->iso_code);
                if ($ret ['language_code'] && $country->iso_code)
                    $ret ['language_code'] .= '-' . $country->iso_code;
            }
        }

        foreach ($order->getProductsDetail() as $item) {
            if ($item ['product_reference']) {
                $ret ['products'] [] = $item ['product_reference'];
            } else {
                $ret ['products'] [] = $item ['product_name'];
            }
        }

        // order history
        foreach ($order->getHistory($order->id_lang) as $history) {
            $ret ['history'] [] = $history ['date_add'] . '/' . $history ['date_upd'] . ': ' . $history ['id_order_state'] . ' (' . $history ['id_employee'] . ')';
        }


        if (isset($order_extension) && $order_extension && method_exists('Komfortkasse_Order_Extension', 'extendOrder') === true) {
            $ret = Komfortkasse_Order_Extension::extendOrder($order, $ret);
        }

        return $ret;

    }


    // end getOrder()

    /**
     * Get refund.
     *
     * @param string $number refund number
     *
     * @return array refund
     */
    public static function getRefund($number)
    {
        return null;

    }


    // end getRefund()

    /**
     * Update order.
     *
     * @param array $order order
     * @param string $status status
     * @param string $callbackid callback ID
     *
     * @return void
     */
    public static function updateOrder($order, $status, $callbackid)
    {
        if (!Komfortkasse_Config::getConfig(Komfortkasse_Config::activate_update, $order)) {
            return;
        }

        // Hint: PAID and CANCELLED are supported as of now.

        if (Komfortkasse_Config::getConfig(Komfortkasse_Config::ordernumbers) == 'id') {
            $order = new Order($order ['number']);
        } else {
            $order = new Order($order ['id']);
        }

        // workaround for some prestashop bugs - see komfortkasse ticket #39445/#39738
        require_once _PS_CONFIG_DIR_ . '/config.inc.php';
        global $kernel;
        if (!$kernel) {
            require_once _PS_ROOT_DIR_ . '/app/AppKernel.php';
            $kernel = new \AppKernel('prod', false);
            $kernel->boot();
        }
        if (!Context::getContext()->currency)
            Context::getContext()->currency = new Currency($order->id_currency);
        if (!Context::getContext()->employee)
            Context::getContext()->employee = new Employee(null, $order->id_lang, $order->id_shop);
        $shop = new Shop($order->id_shop);
        Shop::setContext($shop::CONTEXT_SHOP, $shop->id);
        Context::getContext()->shop = $shop;
        Context::getContext()->cookie->id_shop = $shop->id;

        // copied from AdminOrdersController

        $history = new OrderHistory();
        $history->id_order = $order->id;
        $use_existings_payment = !$order->hasInvoice();

        try  {
            $history->changeIdOrderState($status, $order, $use_existings_payment);
        } catch (\PrestaShopBundle\Exception\NotImplementedException $e) {

            // copied from OrderHistory#changeIdOrderState

            $new_os = new OrderState((int) $status, $order->id_lang);
            // $old_os = $order->getCurrentOrderState();

            // executes hook
            if (in_array($new_os->id, [Configuration::get('PS_OS_PAYMENT'), Configuration::get('PS_OS_WS_PAYMENT')])) {
                Hook::exec('actionPaymentConfirmation', ['id_order' => (int) $order->id], null, false, true, false, $order->id_shop);
            }

            // executes hook
            Hook::exec('actionOrderStatusUpdate', ['newOrderStatus' => $new_os, 'id_order' => (int) $order->id], null, false, true, false, $order->id_shop);

            $history->id_order_state = $status;

            // set orders as paid
            if ($new_os->paid == 1) {
                $invoices = $order->getInvoicesCollection();
                if ($order->total_paid != 0) {
                    $payment_method = Module::getInstanceByName($order->module);
                }

                foreach ($invoices as $invoice) {
                    /** @var OrderInvoice $invoice */
                    $rest_paid = $invoice->getRestPaid();
                    if ($rest_paid > 0) {
                        $payment = new OrderPayment();
                        $payment->order_reference = Tools::substr($order->reference, 0, 9);
                        $payment->id_currency = $order->id_currency;
                        $payment->amount = $rest_paid;

                        if ($order->total_paid != 0) {
                            $payment->payment_method = $payment_method->displayName;
                        } else {
                            $payment->payment_method = null;
                        }

                        // Update total_paid_real value for backward compatibility reasons
                        if ($payment->id_currency == $order->id_currency) {
                            $order->total_paid_real += $payment->amount;
                        } else {
                            $order->total_paid_real += Tools::ps_round(Tools::convertPrice($payment->amount, $payment->id_currency, false), Context::getContext()->getComputingPrecision());
                        }
                        $order->save();

                        $payment->conversion_rate = ($order ? $order->conversion_rate : 1);
                        $payment->save();
                        Db::getInstance()->execute('
                    INSERT INTO `' . _DB_PREFIX_ . 'order_invoice_payment` (`id_order_invoice`, `id_order_payment`, `id_order`)
                    VALUES(' . (int) $invoice->id . ', ' . (int) $payment->id . ', ' . (int) $order->id . ')');
                    }
                }
            }

            // executes hook
            Hook::exec('actionOrderStatusPostUpdate', ['newOrderStatus' => $new_os, 'id_order' => (int) $order->id], null, false, true, false, $order->id_shop);

        }

        $carrier = new Carrier($order->id_carrier, $order->id_lang);
        $templateVars = array ();
        if ($history->id_order_state == Configuration::get('PS_OS_SHIPPING') && $order->shipping_number) {
            $templateVars = array ('{followup}' => str_replace('@', $order->shipping_number, $carrier->url)
            );
        }

        // Save all changes
        if ($history->addWithemail(true, $templateVars)) {
            // synchronizes quantities if needed..
            if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
                foreach ($order->getProducts() as $product) {
                    if (StockAvailable::dependsOnStock($product ['product_id'])) {
                        StockAvailable::synchronize($product ['product_id'], (int)$product ['id_shop']);
                    }
                }
            }
        }

    }


    // end updateOrder()

    /**
     * Update order.
     *
     * @param string $refundIncrementId Increment ID of refund
     * @param string $status status
     * @param string $callbackid callback ID
     *
     * @return void
     */
    public static function updateRefund($refundIncrementId, $status, $callbackid)
    {

    }


    // end updateRefund()
    public static function getInvoicePdfPrepare()
    {

    }


    public static function getInvoicePdf($invoiceNumber)
    {
        // get newest id with that number (for numbers that reset every year)
        $sql = 'SELECT id_order_invoice FROM ' . (string)_DB_PREFIX_ . 'order_invoice o WHERE number=\'' . (string)pSQL($invoiceNumber) . '\' order by date_add desc limit 0, 1';
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        if (empty($result))
            return;

        $id = $result [0] ['id_order_invoice'];
        if (!$id)
            return;

        $order_invoice = new OrderInvoice((int)$id);
        if (!Validate::isLoadedObject($order_invoice))
            return;

        Hook::exec('actionPDFInvoiceRender', array ('order_invoice_list' => array ($order_invoice
        )
        ));

        // workaround for some prestashop bugs - see komfortkasse ticket #39445
        global $kernel;
        if (!$kernel) {
            require_once _PS_ROOT_DIR_ . '/app/AppKernel.php';
            $kernel = new \AppKernel('prod', false);
            $kernel->boot();
        }
        if (!Context::getContext()->currency)
            Context::getContext()->currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));

        $pdf = new PDF($order_invoice, PDF::TEMPLATE_INVOICE, Context::getContext()->smarty);
        $output = $pdf->render('S');

        // set http response code manually (could be 500 because of php notices/warnings)
        http_response_code(200);

        echo $output;

    }
}//end class
