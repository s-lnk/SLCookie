<?php
/**
 * Cookie Popup for GDPR Cookie Consent.
 *
 * @author    Sergei
 * @copyright 2023 LINK Company
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class Slcookie extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'slcookie';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'LINK Company LLC';
        $this->need_instance = 1;

        // Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('SLCookie');
        $this->description = $this->l('Cookie Popup for GDPR Cookie Consent');

        $this->confirmUninstall = $this->l('Are You sure you want to uninstall module?');

        $this->ps_versions_compliancy = ['min' => '1.7', 'max' => _PS_VERSION_];
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update.
     */
    public function install()
    {
        include dirname(__FILE__) . '/sql/install.php';

        return parent::install() && $this->registerHook('displayFooter');
    }

    public function uninstall()
    {
        include dirname(__FILE__) . '/sql/uninstall.php';

        return parent::uninstall();
    }

    /**
     * Load the configuration form.
     */
    public function getContent()
    {
        if (((bool) Tools::isSubmit('submitSlcookieModule')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        $output = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/configure.tpl');

        return $output . $this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitSlcookieModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
                . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm([$this->getConfigForm()]);
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return [
            'form' => [
                'legend' => [
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'text',
                        'label' => $this->l('Pop-up Title'),
                        'name' => 'SLCOOKIE_TITLE',
                        'lang' => true,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Pop-up Message'),
                        'name' => 'SLCOOKIE_TEXT',
                        'lang' => true,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Cookie URL Message'),
                        'name' => 'SLCOOKIE_URL_TEXT',
                        'lang' => true,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Cookie URL'),
                        'name' => 'SLCOOKIE_URL',
                        'lang' => true,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Confirm Button Caption'),
                        'name' => 'SLCOOKIE_BUTTON',
                        'lang' => true,
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
        ];
    }

    /**
     * Set values for the inputs.
     * TODO: Switch to ObjectModel.
     */
    protected function getConfigFormValues()
    {
        $languages = Language::getLanguages(false);
        $db = \Db::getInstance();
        $ar_title = [];
        $ar_text = [];
        $ar_text_url = [];
        $ar_url = [];
        $ar_button = [];

        foreach ($languages as $lang) {
            $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'slcookie WHERE id_lang = ' . (int) $lang['id_lang'] . ' LIMIT 0,1';
            $result = $db->ExecuteS($sql);
            foreach ($result as $row) {
                $ar_title[$lang['id_lang']] = $row['slc_title'];
                $ar_text[$lang['id_lang']] = $row['slc_text'];
                $ar_text_url[$lang['id_lang']] = $row['slc_text_url'];
                $ar_url[$lang['id_lang']] = $row['slc_url'];
                $ar_button[$lang['id_lang']] = $row['slc_btn_confirm'];
            }
        }

        return [
            'SLCOOKIE_TITLE' => $ar_title,
            'SLCOOKIE_TEXT' => $ar_text,
            'SLCOOKIE_URL_TEXT' => $ar_text_url,
            'SLCOOKIE_URL' => $ar_url,
            'SLCOOKIE_BUTTON' => $ar_button,
        ];
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $languages = Language::getLanguages(false);
        $db = \Db::getInstance();

        foreach ($languages as $lang) {
            // Get translated values from form
            $slc_title = Tools::getValue('SLCOOKIE_TITLE_' . (int) $lang['id_lang']);
            $slc_text = Tools::getValue('SLCOOKIE_TEXT_' . (int) $lang['id_lang']);
            $slc_text_url = Tools::getValue('SLCOOKIE_URL_TEXT_' . (int) $lang['id_lang']);
            $slc_url = Tools::getValue('SLCOOKIE_URL_' . (int) $lang['id_lang']);
            $slc_btn_confirm = Tools::getValue('SLCOOKIE_BUTTON_' . (int) $lang['id_lang']);

            // Check if parameter exists in DB and insert or update record
            $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'slcookie WHERE id_lang = ' . (int) $lang['id_lang'];
            $result = $db->ExecuteS($sql);
            $cnt = $db->numRows();
            if ($cnt > 0) {
                $sql = 'UPDATE ' . _DB_PREFIX_ . 'slcookie SET 
					slc_title = `' . $slc_title . '`,
					slc_text = `' . $slc_text . '`,
					slc_text_url = `' . $slc_text_url . '`,
					slc_url = `' . $slc_url . '`,
					slc_btn_confirm = `' . $slc_btn_confirm . '`
					WHERE id_lang = ' . (int) $lang['id_lang'];
            } else {
                $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'slcookie (slc_title, slc_text, slc_text_url, slc_url, slc_btn_confirm, id_lang)  VALUES 
					(`' . $slc_title . '`,`' . $slc_text . '`,`' . $slc_text_url . '`,`' . $slc_url . '`,`' . $slc_btn_confirm . '`,' . (int) $lang['id_lang'] . ')';
            }
            $result = $db->ExecuteS($sql);
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be loaded in the BO.
     */
    public function hookDisplayBackOfficeHeader()
    {
        if (Tools::getValue('configure') == $this->name) {
            $this->context->controller->addJS($this->_path . 'views/js/back.js');
            $this->context->controller->addCSS($this->_path . 'views/css/back.css');
        }
    }

    public function hookDisplayFooter()
    {
        $slc_title = 'Cookies';
        $slc_text = 'Browsing our webpage You agree to use cookies. More info ';
        $slc_text_url = 'here.';
        $slc_url = 'https://www.link.ee';
        $slc_btn_confirm = 'Agree';

        $id_lang = $this->context->language->id;
        $result = Db::getInstance()->ExecuteS('SELECT * FROM ' . _DB_PREFIX_ . 'slcookie WHERE id_lang = `' . pSQL($id_lang) . '` LIMIT 0,1');
        foreach ($result as $row) {
            $slc_title = $row['slc_title'];
            $slc_text = strip_tags($row['slc_text']);
            $slc_text_url = strip_tags($row['slc_text_url']);
            $slc_url = $row['slc_url'];
            $slc_btn_confirm = $row['slc_btn_confirm'];
        }

        $this->context->smarty->assign('TITLE', $slc_title);
        $this->context->smarty->assign('TEXT', $slc_text);
        $this->context->smarty->assign('TEXT_URL', $slc_text_url);
        $this->context->smarty->assign('URL', $slc_url);
        $this->context->smarty->assign('BTN_CONFIRM', $slc_btn_confirm);

        $this->context->controller->addJquery();
        $this->context->controller->bootstrap = true;

        return $this->display(__FILE__, './views/templates/front/slcookie.tpl');
    }

    protected function writeLog($string)
    {
        $logger = new FileLogger(0); // 0 == debug level, logDebug() won’t work without this.
        $logger->setFilename(_PS_ROOT_DIR_ . '/modules/slcookie/debug.log');
        $logger->logDebug($string);

        return true;
    }
}
