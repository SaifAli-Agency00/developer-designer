<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once __DIR__ . '/classes/PayYourPriceRepository.php';

class PayYourPrice extends Module
{
    public function __construct()
    {
        $this->name = 'payyourprice';
        $this->tab = 'pricing_promotion';
        $this->version = '1.0.0';
        $this->author = 'Your Company';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Pay Your Price');
        $this->description = $this->l('Allows customers to set a custom price per product within configured limits.');
        $this->ps_versions_compliancy = ['min' => '1.7.8.0', 'max' => _PS_VERSION_];
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('displayProductAdditionalInfo')
            && $this->registerHook('header')
            && $this->registerHook('actionCartUpdateQuantityBefore')
            && $this->registerHook('actionProductPriceCalculation')
            && $this->registerHook('actionPresentCart')
            && $this->registerHook('actionValidateOrder')
            && $this->registerHook('displayAdminOrderMainBottom')
            && $this->registerHook('actionObjectOrderDetailAddAfter')
            && $this->installDb();
    }

    public function uninstall()
    {
        return $this->uninstallDb() && parent::uninstall();
    }

    protected function installDb()
    {
        $sql = str_replace(['PREFIX_', 'ENGINE_TYPE'], [_DB_PREFIX_, _MYSQL_ENGINE_], file_get_contents(__DIR__ . '/sql/install.sql'));
        return $this->executeSqlBatch($sql)
            && Configuration::updateValue('PYP_ENABLED', 1)
            && Configuration::updateValue('PYP_PRODUCT_RULES', '');
    }

    protected function uninstallDb()
    {
        $sql = str_replace('PREFIX_', _DB_PREFIX_, file_get_contents(__DIR__ . '/sql/uninstall.sql'));
        return $this->executeSqlBatch($sql)
            && Configuration::deleteByName('PYP_ENABLED')
            && Configuration::deleteByName('PYP_PRODUCT_RULES');
    }

    public function getContent()
    {
        $output = '';
        if (Tools::isSubmit('submitPayYourPriceConfig')) {
            Configuration::updateValue('PYP_ENABLED', (int) Tools::getValue('PYP_ENABLED'));
            Configuration::updateValue('PYP_PRODUCT_RULES', trim((string) Tools::getValue('PYP_PRODUCT_RULES')));
            $output .= $this->displayConfirmation($this->l('Configuration updated'));
        }

        return $output . $this->renderForm();
    }

    protected function renderForm()
    {
        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->submit_action = 'submitPayYourPriceConfig';

        $helper->fields_value['PYP_ENABLED'] = (int) Configuration::get('PYP_ENABLED');
        $helper->fields_value['PYP_PRODUCT_RULES'] = (string) Configuration::get('PYP_PRODUCT_RULES');

        $fieldsForm = [
            'form' => [
                'legend' => ['title' => $this->l('Pay Your Price Settings')],
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => $this->l('Enable module'),
                        'name' => 'PYP_ENABLED',
                        'values' => [
                            ['id' => 'pyp_on', 'value' => 1, 'label' => $this->l('Enabled')],
                            ['id' => 'pyp_off', 'value' => 0, 'label' => $this->l('Disabled')],
                        ],
                    ],
                    [
                        'type' => 'textarea',
                        'label' => $this->l('Product rules'),
                        'name' => 'PYP_PRODUCT_RULES',
                        'desc' => $this->l('One line per product: product_id|min|max. Example: 12|25.00|100.00'),
                        'rows' => 8,
                        'cols' => 80,
                    ],
                ],
                'submit' => ['title' => $this->l('Save')],
            ],
        ];

        return $helper->generateForm([$fieldsForm]);
    }

    public function hookHeader()
    {
        if ((int) Configuration::get('PYP_ENABLED') !== 1) {
            return;
        }

        if ($this->context->controller->php_self !== 'product') {
            return;
        }

        Media::addJsDef([
            'pyp_label' => $this->l('Pay Your Price – Enter the amount you want to pay'),
        ]);

        $this->context->controller->registerJavascript(
            'module-pyp-product',
            'modules/' . $this->name . '/views/js/product.js',
            ['position' => 'bottom', 'priority' => 150]
        );
    }

    public function hookDisplayProductAdditionalInfo($params)
    {
        if ((int) Configuration::get('PYP_ENABLED') !== 1 || empty($params['product'])) {
            return '';
        }

        $product = $params['product'];
        $idProduct = (int) $product['id_product'];
        $rules = $this->getRulesByProduct($idProduct);
        if (!$rules) {
            return '';
        }

        $base = (float) Product::getPriceStatic($idProduct, true);
        $min = max($base, (float) $rules['min']);
        $max = (float) $rules['max'] > 0 ? (float) $rules['max'] : 0;

        $this->context->smarty->assign([
            'pyp_id_product' => $idProduct,
            'pyp_base_price' => $base,
            'pyp_min_price' => $min,
            'pyp_max_price' => $max,
            'pyp_currency_sign' => $this->context->currency->sign,
            'pyp_currency_iso' => $this->context->currency->iso_code,
            'pyp_product_name' => $product['name'],
        ]);

        return $this->fetch('module:payyourprice/views/templates/hook/product_input.tpl');
    }

    public function hookActionCartUpdateQuantityBefore($params)
    {
        if ((int) Configuration::get('PYP_ENABLED') !== 1) {
            return;
        }

        $idProduct = (int) ($params['id_product'] ?? 0);
        $amount = (float) Tools::getValue('pyp_custom_price');
        if ($idProduct <= 0 || $amount <= 0) {
            return;
        }

        $rules = $this->getRulesByProduct($idProduct);
        if (!$rules) {
            return;
        }

        $validation = $this->validateCustomPrice($idProduct, $amount, $rules);
        if ($validation !== true) {
            throw new PrestaShopException($validation);
        }

        $repo = new PayYourPriceRepository();
        $repo->upsertCartPrice(
            (int) $params['cart']->id,
            $idProduct,
            (int) ($params['id_product_attribute'] ?? 0),
            (int) ($params['id_customization'] ?? 0),
            $amount
        );
    }

    public function hookActionProductPriceCalculation(array $params)
    {
        if ((int) Configuration::get('PYP_ENABLED') !== 1) {
            return;
        }

        if (empty($params['id_cart']) || empty($params['id_product'])) {
            return;
        }

        $repo = new PayYourPriceRepository();
        $customPrice = $repo->getCartPrice(
            (int) $params['id_cart'],
            (int) $params['id_product'],
            (int) ($params['id_product_attribute'] ?? 0),
            (int) ($params['id_customization'] ?? 0)
        );

        if ($customPrice === null) {
            return;
        }

        $params['price'] = (float) $customPrice;
    }

    public function hookActionPresentCart(array &$params)
    {
        if (empty($params['presentedCart']['products'])) {
            return;
        }

        $repo = new PayYourPriceRepository();
        $idCart = (int) $this->context->cart->id;

        foreach ($params['presentedCart']['products'] as &$product) {
            $price = $repo->getCartPrice(
                $idCart,
                (int) $product['id_product'],
                (int) $product['id_product_attribute'],
                (int) $product['id_customization']
            );

            if ($price !== null) {
                $product['pyp_custom_price'] = (float) $price;
                $product['pyp_label'] = $this->l('Custom Price Applied');
            }
        }
    }

    public function hookActionValidateOrder($params)
    {
        $order = $params['order'];
        $cart = $params['cart'];
        if (!Validate::isLoadedObject($order) || !Validate::isLoadedObject($cart)) {
            return;
        }

        $repo = new PayYourPriceRepository();
        $repo->linkOrderFromCart((int) $cart->id, (int) $order->id);
    }

    public function hookActionObjectOrderDetailAddAfter($params)
    {
        $orderDetail = $params['object'];
        if (!Validate::isLoadedObject($orderDetail)) {
            return;
        }

        $repo = new PayYourPriceRepository();
        $customPrice = $repo->getOrderCustomPriceByOrderDetail((int) $orderDetail->id);
        if ($customPrice !== null) {
            Db::getInstance()->update(
                'order_detail',
                ['product_name' => pSQL($orderDetail->product_name . ' [' . $this->l('Custom Price Applied') . ']')],
                'id_order_detail=' . (int) $orderDetail->id
            );
        }
    }

    public function hookDisplayAdminOrderMainBottom($params)
    {
        $idOrder = (int) ($params['id_order'] ?? 0);
        if ($idOrder <= 0) {
            return '';
        }

        $repo = new PayYourPriceRepository();
        $rows = $repo->getOrderCustomPrices($idOrder);
        if (!$rows) {
            return '';
        }

        $this->context->smarty->assign(['pyp_rows' => $rows]);
        return $this->fetch('module:payyourprice/views/templates/admin/order_custom_prices.tpl');
    }



    private function executeSqlBatch($sql)
    {
        $queries = array_filter(array_map('trim', explode(";", (string) $sql)));
        foreach ($queries as $query) {
            if (!Db::getInstance()->execute($query)) {
                return false;
            }
        }

        return true;
    }

    private function getRulesByProduct($idProduct)
    {
        $rules = explode("\n", (string) Configuration::get('PYP_PRODUCT_RULES'));
        foreach ($rules as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }
            $parts = array_map('trim', explode('|', $line));
            if (count($parts) !== 3) {
                continue;
            }
            if ((int) $parts[0] === (int) $idProduct) {
                return ['min' => (float) $parts[1], 'max' => (float) $parts[2]];
            }
        }

        return null;
    }

    private function validateCustomPrice($idProduct, $amount, array $rules)
    {
        $base = (float) Product::getPriceStatic((int) $idProduct, true);
        $min = max($base, (float) $rules['min']);
        $max = (float) $rules['max'];

        if ($amount < $base) {
            return $this->l('Entered amount cannot be less than product base price.');
        }
        if ($amount < $min) {
            return $this->l('Entered amount is below configured minimum price.');
        }
        if ($max > 0 && $amount > $max) {
            return $this->l('Entered amount exceeds configured maximum price.');
        }

        return true;
    }
}
