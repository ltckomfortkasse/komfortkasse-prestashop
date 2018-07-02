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
 * @author    Komfortkasse Integration Team
 * @copyright 2016 LTC Information Services GmbH
 * @license   https://creativecommons.org/licenses/by/3.0
 */

if (!defined('_PS_VERSION_')) {
    exit();
}

require_once 'lib/Komfortkasse_Config.php';
require_once 'lib/Komfortkasse_Order.php';

class LtcKomfortkasse extends Module
{

    public function __construct()
    {
        $this->name = 'ltckomfortkasse';
        $this->tab = 'payments_gateways';
        $this->version = '1.7.13';
        $this->author = 'Komfortkasse';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array ('min' => '1.5','max' => _PS_VERSION_
        );
        $this->bootstrap = true;
        $this->module_key = 'f53765d2dfdbf6f114064602c7283449';

        parent::__construct();

        $this->displayName = $this->l('Komfortkasse');
        $this->description = $this->l('Processing for payment by bank transfer (prepayment, cod, invoice)');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
    }

    public function getContent()
    {
        require_once 'lib/Komfortkasse_Config.php';

        $output = null;
        if (Tools::isSubmit('submit' . $this->name)) {
            if (Configuration::updateValue(Komfortkasse_Config::activate_export, Komfortkasse_Config::getRequestParameter(Komfortkasse_Config::activate_export)) && Configuration::updateValue(Komfortkasse_Config::activate_update, Komfortkasse_Config::getRequestParameter(Komfortkasse_Config::activate_update)) && Configuration::updateValue(Komfortkasse_Config::payment_methods, implode(',', Komfortkasse_Config::getRequestParameter(Komfortkasse_Config::payment_methods))) && Configuration::updateValue(Komfortkasse_Config::status_open, implode(',', Komfortkasse_Config::getRequestParameter(Komfortkasse_Config::status_open))) && Configuration::updateValue(Komfortkasse_Config::status_paid, Komfortkasse_Config::getRequestParameter(Komfortkasse_Config::status_paid)) && Configuration::updateValue(Komfortkasse_Config::status_cancelled, Komfortkasse_Config::getRequestParameter(Komfortkasse_Config::status_cancelled)) && Configuration::updateValue(Komfortkasse_Config::payment_methods_invoice, implode(',', Komfortkasse_Config::getRequestParameter(Komfortkasse_Config::payment_methods_invoice))) && Configuration::updateValue(Komfortkasse_Config::status_open_invoice, implode(',', Komfortkasse_Config::getRequestParameter(Komfortkasse_Config::status_open_invoice))) && Configuration::updateValue(Komfortkasse_Config::status_paid_invoice, Komfortkasse_Config::getRequestParameter(Komfortkasse_Config::status_paid_invoice)) && Configuration::updateValue(Komfortkasse_Config::status_cancelled_invoice, Komfortkasse_Config::getRequestParameter(Komfortkasse_Config::status_cancelled_invoice)) && Configuration::updateValue(Komfortkasse_Config::payment_methods_cod, implode(',', Komfortkasse_Config::getRequestParameter(Komfortkasse_Config::payment_methods_cod))) && Configuration::updateValue(Komfortkasse_Config::status_open_cod, implode(',', Komfortkasse_Config::getRequestParameter(Komfortkasse_Config::status_open_cod))) && Configuration::updateValue(Komfortkasse_Config::status_paid_cod, Komfortkasse_Config::getRequestParameter(Komfortkasse_Config::status_paid_cod)) && Configuration::updateValue(Komfortkasse_Config::status_cancelled_cod, Komfortkasse_Config::getRequestParameter(Komfortkasse_Config::status_cancelled_cod)) && Configuration::updateValue(Komfortkasse_Config::encryption, Komfortkasse_Config::getRequestParameter(Komfortkasse_Config::encryption)) && Configuration::updateValue(Komfortkasse_Config::accesscode, Komfortkasse_Config::getRequestParameter(Komfortkasse_Config::accesscode)) && Configuration::updateValue(Komfortkasse_Config::apikey, Komfortkasse_Config::getRequestParameter(Komfortkasse_Config::apikey)) && Configuration::updateValue(Komfortkasse_Config::publickey, Komfortkasse_Config::getRequestParameter(Komfortkasse_Config::publickey)) && Configuration::updateValue(Komfortkasse_Config::privatekey, Komfortkasse_Config::getRequestParameter(Komfortkasse_Config::privatekey))) {
                $output .= $this->displayConfirmation($this->l('Settings updated'));
            } else {
                $output .= $this->displayConfirmation($this->l('Error'));
            }
        }

        return $output . $this->displayForm();
    }

    public function displayForm()
    {
        require_once 'lib/Komfortkasse_Config.php';

        $language = Context::getContext()->language->id;

        // Array für die Anzeige der Zahlungsarten vorbereiten
        $payment_method_array = array ();
        $payment_method_array [] = array ('id_option' => null,'name' => $this->l('(no selection)')
        );
        $modules = Module::getModulesOnDisk(true);
        foreach ($modules as $module) {
            if ($module->tab == 'payments_gateways' && $module->active && $module->name != 'ltckomfortkasse') {
                $payment_method_array [] = array ('id_option' => $module->name,'name' => $module->displayName
                );
            }
        }

        // Array für die Anzeige der Bestellstatus vorbereiten
        $order_status_array = array ();
        $order_status_array [] = array ('id_option' => null,'name' => $this->l('(no selection)')
        );
        $states = OrderState::getOrderStates($language);
        foreach ($states as $state) {
            $order_status_array [] = array ('id_option' => $state ['id_order_state'],'name' => $state ['name']
            );
        }

        // Array für Encryption Option
        $encryption_array = array (array ('id_option' => null,'name' => $this->l('(no selection)')
        ),array ('id_option' => 'base64','name' => 'Base64'
        ),array ('id_option' => 'mcrypt','name' => 'MCrypt'
        ),array ('id_option' => 'openssl','name' => 'OpenSSL'
        )
        );

        // Init Fields form array
        $fields_form = array ();
        $fields_form [0] ['form'] = array ('legend' => array ('title' => $this->l('Settings')
        ),'input' => array (array ('type' => 'radio','label' => $this->l('Export orders'),'desc' => $this->l('Activate export of orders to Komfortkasse'),'name' => Komfortkasse_Config::activate_export,'required' => true,'is_bool' => true,'values' => array (array ('id' => 'active_on','value' => 1,'label' => $this->l('Enabled')
        ),array ('id' => 'active_off','value' => 0,'label' => $this->l('Disabled')
        )
        )
        ),array ('type' => 'radio','label' => $this->l('Update order status'),'desc' => $this->l('Activate update of order status (paid/cancelled)'),'name' => Komfortkasse_Config::activate_update,'required' => true,'is_bool' => true,'values' => array (array ('id' => 'active_on','value' => 1,'label' => $this->l('Enabled')
        ),array ('id' => 'active_off','value' => 0,'label' => $this->l('Disabled')
        )
        )
        ),array ('type' => 'select','label' => $this->l('Prepayment: relevant payment methods'),'desc' => $this->l('All payment methods that should be exported for prepayment orders.'),'name' => Komfortkasse_Config::payment_methods . '[]','multiple' => true,'class' => 'fixed-width-xxl','options' => array ('query' => $payment_method_array,'id' => 'id_option','name' => 'name'
        )
        ),array ('type' => 'select','label' => $this->l('Prepayment: States open'),'desc' => $this->l('Order states that should be exported (open orders)'),'name' => Komfortkasse_Config::status_open . '[]','multiple' => true,'class' => 'fixed-width-xxl','options' => array ('query' => $order_status_array,'id' => 'id_option','name' => 'name'
        )
        ),array ('type' => 'select','label' => $this->l('Prepayment: State paid'),'desc' => $this->l('Order state that should be set when prepayment has been received'),'name' => Komfortkasse_Config::status_paid,'class' => 'fixed-width-xxl','options' => array ('query' => $order_status_array,'id' => 'id_option','name' => 'name'
        )
        ),array ('type' => 'select','label' => $this->l('Prepayment: State cancelled'),'desc' => $this->l('Order state that should be set when a prepayment order has been cancelled'),'name' => Komfortkasse_Config::status_cancelled,'class' => 'fixed-width-xxl','options' => array ('query' => $order_status_array,'id' => 'id_option','name' => 'name'
        )
        ),

        array ('type' => 'select','label' => $this->l('Invoice: relevant payment methods'),'desc' => $this->l('All payment methods that should be exported for invoice orders.'),'name' => Komfortkasse_Config::payment_methods_invoice . '[]','multiple' => true,'class' => 'fixed-width-xxl','options' => array ('query' => $payment_method_array,'id' => 'id_option','name' => 'name'
        )
        ),array ('type' => 'select','label' => $this->l('Invoice: States open'),'desc' => $this->l('Order states that should be exported (shipped orders)'),'name' => Komfortkasse_Config::status_open_invoice . '[]','multiple' => true,'class' => 'fixed-width-xxl','options' => array ('query' => $order_status_array,'id' => 'id_option','name' => 'name'
        )
        ),array ('type' => 'select','label' => $this->l('Invoice: State paid'),'desc' => $this->l('Order state that should be set when an invoice has been paid'),'name' => Komfortkasse_Config::status_paid_invoice,'class' => 'fixed-width-xxl','options' => array ('query' => $order_status_array,'id' => 'id_option','name' => 'name'
        )
        ),array ('type' => 'select','label' => $this->l('Invoice: State no payment/debt collection'),'desc' => $this->l('Order state that should be set when an invoice was not paid'),'name' => Komfortkasse_Config::status_cancelled_invoice,'class' => 'fixed-width-xxl','options' => array ('query' => $order_status_array,'id' => 'id_option','name' => 'name'
        )
        ),

        array ('type' => 'select','label' => $this->l('COD: relevant payment methods'),'desc' => $this->l('All payment methods that should be exported for COD (cash on delivery) orders.'),'name' => Komfortkasse_Config::payment_methods_cod . '[]','multiple' => true,'class' => 'fixed-width-xxl','options' => array ('query' => $payment_method_array,'id' => 'id_option','name' => 'name'
        )
        ),array ('type' => 'select','label' => $this->l('COD: States open'),'desc' => $this->l('Order states that should be exported (dispatched COD parcel)'),'name' => Komfortkasse_Config::status_open_cod . '[]','multiple' => true,'class' => 'fixed-width-xxl','options' => array ('query' => $order_status_array,'id' => 'id_option','name' => 'name'
        )
        ),array ('type' => 'select','label' => $this->l('COD: State paid'),'desc' => $this->l('Order state that should be set when a COD order has been paid'),'name' => Komfortkasse_Config::status_paid_cod,'class' => 'fixed-width-xxl','options' => array ('query' => $order_status_array,'id' => 'id_option','name' => 'name'
        )
        ),array ('type' => 'select','label' => $this->l('COD: State payment unresolved'),'desc' => $this->l('Order state that should be set when a COD order was not paid'),'name' => Komfortkasse_Config::status_cancelled_cod,'class' => 'fixed-width-xxl','options' => array ('query' => $order_status_array,'id' => 'id_option','name' => 'name'
        )
        ),array ('type' => 'select','label' => $this->l('Encryption'),'desc' => $this->l('Encryption technology. Do not change! Is set automatically by komfortkasse'),'name' => Komfortkasse_Config::encryption,'class' => 'fixed-width-xxl','options' => array ('query' => $encryption_array,'id' => 'id_option','name' => 'name'
        )
        ),array ('type' => 'text','label' => $this->l('Access code (encrypted)'),'desc' => $this->l('Encrypted access code. Do not change! Is set automatically by komfortkasse'),'name' => Komfortkasse_Config::accesscode,'class' => 'fixed-width-xxl'
        ),array ('type' => 'text','label' => $this->l('API Key'),'desc' => $this->l('Key for accessing the API. Do not change! Is set automatically by komfortkasse'),'name' => Komfortkasse_Config::apikey,'class' => 'fixed-width-xxl'
        ),array ('type' => 'text','label' => $this->l('Public key'),'desc' => $this->l('Key for encrypting data that is sent to komfortkasse. Do not change! Is set automatically by komfortkasse'),'name' => Komfortkasse_Config::publickey,'class' => 'fixed-width-xxl'
        ),array ('type' => 'text','label' => $this->l('Private key'),'desc' => $this->l('Key for decrypting data that is received from komfortkasse. Do not change! Is set automatically by komfortkasse'),'name' => Komfortkasse_Config::privatekey,'class' => 'fixed-width-xxl'
        )
        ),'submit' => array ('title' => $this->l('Save'),'class' => 'button'
        )
        );

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;

        // Language
        $helper->default_form_language = $language;
        $helper->allow_employee_form_lang = $language;

        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->submit_action = 'submit' . $this->name;
        $helper->toolbar_btn = array ('save' => array ('desc' => $this->l('Save'),'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&save' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules')
        ),'back' => array ('href' => AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules'),'desc' => $this->l('Back to list')
        )
        );

        // Load current value
        $helper->fields_value [Komfortkasse_Config::activate_export] = Komfortkasse_Config::getConfig(Komfortkasse_Config::activate_export);
        $helper->fields_value [Komfortkasse_Config::activate_update] = Komfortkasse_Config::getConfig(Komfortkasse_Config::activate_update);
        $helper->fields_value [Komfortkasse_Config::payment_methods . '[]'] = explode(',', Komfortkasse_Config::getConfig(Komfortkasse_Config::payment_methods));
        $helper->fields_value [Komfortkasse_Config::status_open . '[]'] = explode(',', Komfortkasse_Config::getConfig(Komfortkasse_Config::status_open));
        $helper->fields_value [Komfortkasse_Config::status_paid] = Komfortkasse_Config::getConfig(Komfortkasse_Config::status_paid);
        $helper->fields_value [Komfortkasse_Config::status_cancelled] = Komfortkasse_Config::getConfig(Komfortkasse_Config::status_cancelled);
        $helper->fields_value [Komfortkasse_Config::payment_methods_invoice . '[]'] = explode(',', Komfortkasse_Config::getConfig(Komfortkasse_Config::payment_methods_invoice));
        $helper->fields_value [Komfortkasse_Config::status_open_invoice . '[]'] = explode(',', Komfortkasse_Config::getConfig(Komfortkasse_Config::status_open_invoice));
        $helper->fields_value [Komfortkasse_Config::status_paid_invoice] = Komfortkasse_Config::getConfig(Komfortkasse_Config::status_paid_invoice);
        $helper->fields_value [Komfortkasse_Config::status_cancelled_invoice] = Komfortkasse_Config::getConfig(Komfortkasse_Config::status_cancelled_invoice);
        $helper->fields_value [Komfortkasse_Config::payment_methods_cod . '[]'] = explode(',', Komfortkasse_Config::getConfig(Komfortkasse_Config::payment_methods_cod));
        $helper->fields_value [Komfortkasse_Config::status_open_cod . '[]'] = explode(',', Komfortkasse_Config::getConfig(Komfortkasse_Config::status_open_cod));
        $helper->fields_value [Komfortkasse_Config::status_paid_cod] = Komfortkasse_Config::getConfig(Komfortkasse_Config::status_paid_cod);
        $helper->fields_value [Komfortkasse_Config::status_cancelled_cod] = Komfortkasse_Config::getConfig(Komfortkasse_Config::status_cancelled_cod);
        $helper->fields_value [Komfortkasse_Config::encryption] = Komfortkasse_Config::getConfig(Komfortkasse_Config::encryption);
        $helper->fields_value [Komfortkasse_Config::accesscode] = Komfortkasse_Config::getConfig(Komfortkasse_Config::accesscode);
        $helper->fields_value [Komfortkasse_Config::apikey] = Komfortkasse_Config::getConfig(Komfortkasse_Config::apikey);
        $helper->fields_value [Komfortkasse_Config::publickey] = Komfortkasse_Config::getConfig(Komfortkasse_Config::publickey);
        $helper->fields_value [Komfortkasse_Config::privatekey] = Komfortkasse_Config::getConfig(Komfortkasse_Config::privatekey);

        return $helper->generateForm($fields_form);
    }

    public function install()
    {
        require_once 'lib/Komfortkasse_Config.php';

        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        if (!parent::install() ||
                !$this->registerHook('actionOrderStatusPostUpdate') ||
                !Configuration::updateValue(Komfortkasse_Config::activate_export, true) ||
                !Configuration::updateValue(Komfortkasse_Config::activate_update, true) ||
                !Configuration::updateValue(Komfortkasse_Config::payment_methods, 'bankwire') ||
                !Configuration::updateValue(Komfortkasse_Config::status_open, '10') ||
                !Configuration::updateValue(Komfortkasse_Config::status_paid, '2') ||
                !Configuration::updateValue(Komfortkasse_Config::status_cancelled, '6') ||
                !Configuration::updateValue(Komfortkasse_Config::payment_methods_invoice, '') ||
                !Configuration::updateValue(Komfortkasse_Config::status_open_invoice, '4') ||
                !Configuration::updateValue(Komfortkasse_Config::status_paid_invoice, '') ||
                !Configuration::updateValue(Komfortkasse_Config::status_cancelled_invoice, '') ||
                !Configuration::updateValue(Komfortkasse_Config::payment_methods_cod, '') ||
                !Configuration::updateValue(Komfortkasse_Config::status_open_cod, '4') ||
                !Configuration::updateValue(Komfortkasse_Config::status_paid_cod, '') ||
                !Configuration::updateValue(Komfortkasse_Config::status_cancelled_cod, '') ||
                !Configuration::updateValue(Komfortkasse_Config::encryption, '') ||
                !Configuration::updateValue(Komfortkasse_Config::accesscode, '') ||
                !Configuration::updateValue(Komfortkasse_Config::apikey, '') ||
                !Configuration::updateValue(Komfortkasse_Config::publickey, '') ||
                !Configuration::updateValue(Komfortkasse_Config::privatekey, '')) {
            return false;
        } else {
            return true;
        }
    }

    public function uninstall()
    {
        require_once 'lib/Komfortkasse_Config.php';

        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        if (!parent::uninstall() || !Configuration::deleteByName(Komfortkasse_Config::activate_export) || !Configuration::deleteByName(Komfortkasse_Config::activate_update) || !Configuration::deleteByName(Komfortkasse_Config::payment_methods) || !Configuration::deleteByName(Komfortkasse_Config::status_open) || !Configuration::deleteByName(Komfortkasse_Config::status_paid) || !Configuration::deleteByName(Komfortkasse_Config::status_cancelled) || !Configuration::deleteByName(Komfortkasse_Config::payment_methods_invoice) || !Configuration::deleteByName(Komfortkasse_Config::status_open_invoice) || !Configuration::deleteByName(Komfortkasse_Config::status_paid_invoice) || !Configuration::deleteByName(Komfortkasse_Config::status_cancelled_invoice) || !Configuration::deleteByName(Komfortkasse_Config::payment_methods_cod) || !Configuration::deleteByName(Komfortkasse_Config::status_open_cod) || !Configuration::deleteByName(Komfortkasse_Config::status_paid_cod) || !Configuration::deleteByName(Komfortkasse_Config::status_cancelled_cod) || !Configuration::deleteByName(Komfortkasse_Config::encryption) || !Configuration::deleteByName(Komfortkasse_Config::accesscode) || !Configuration::deleteByName(Komfortkasse_Config::apikey) || !Configuration::deleteByName(Komfortkasse_Config::publickey) || !Configuration::deleteByName(Komfortkasse_Config::privatekey)) {
            return false;
        } else {
            return true;
        }
    }

    public function hookactionOrderStatusPostUpdate($params)
    {
        require_once 'lib/Komfortkasse.php';
        Komfortkasse::notifyorder($params['id_order']);
    }
}
