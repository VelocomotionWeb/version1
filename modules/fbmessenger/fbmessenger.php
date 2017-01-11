<?php
/**
* Facebook Messenger - Live chat
*
* NOTICE OF LICENSE
*
* This product is licensed for one customer to use on one installation (test stores and multishop included).
* Site developer has the right to modify this module to suit their needs, but can not redistribute the module in
* whole or in part. Any other use of this module constitues a violation of the user agreement.
*
* DISCLAIMER
*
* NO WARRANTIES OF DATA SAFETY OR MODULE SECURITY
* ARE EXPRESSED OR IMPLIED. USE THIS MODULE IN ACCORDANCE
* WITH YOUR MERCHANT AGREEMENT, KNOWING THAT VIOLATIONS OF
* PCI COMPLIANCY OR A DATA BREACH CAN COST THOUSANDS OF DOLLARS
* IN FINES AND DAMAGE A STORES REPUTATION. USE AT YOUR OWN RISK.
*
*  @author    idnovate.com <info@idnovate.com>
*  @copyright 2016 idnovate.com
*  @license   See above
*/

class FbMessenger extends Module
{
    public function __construct()
    {
        $this->name = 'fbmessenger';
        $this->tab = 'front_office_features';
        $this->version = '1.0.1';
        $this->author = 'idnovate';
        $this->bootstrap = true;
        $this->module_key = '1b9b3b14f47109ad8bbb7de9e95137c4';

        parent::__construct();

        $this->displayName = $this->l('Facebook Messenger - Live chat');
        $this->description = $this->l('Facebook Messenger - Live chat');
        $this->ps_versions_compliancy = array('min' => '1.4', 'max' => _PS_VERSION_);
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall module?');

        /* Backward compatibility */
        if (version_compare(_PS_VERSION_, '1.5', '<')) {
            require(_PS_MODULE_DIR_.$this->name.'/backward_compatibility/backward.php');
            $this->local_path = _PS_MODULE_DIR_.$this->name.'/';
        }

        $this->warning = $this->getWarnings(false);
    }

    public function install()
    {
        return parent::install() && $this->registerHook('footer');
    }

    public function uninstall()
    {
        return parent::uninstall()
            && Configuration::deleteByName('FB_MESSENGER_PAGE_ID')
            && Configuration::deleteByName('FB_MESSENGER_TYPE')
            && Configuration::deleteByName('FB_MESSENGER_POSITION');
    }

    public function hookDisplayFooter($params)
    {
        $pageId = Configuration::get('FB_MESSENGER_PAGE_ID');

        if (!empty($pageId)) {
            $language_code = explode('-', $this->context->language->language_code);
            $this->context->smarty->assign(array(
                'position'  => Configuration::get('FB_MESSENGER_POSITION'),
                'page_id'   => $pageId,
                'locale'    => strtolower($language_code[0]).'_'.strtoupper($language_code[1]),
            ));

            return $this->display(__FILE__, 'widget.tpl');
        }
    }

    public function getContent()
    {
        $html = '';

        if ($warnings = $this->getWarnings()) {
            $html .= $this->displayError($warnings);
        }

        if (((bool)Tools::isSubmit('submitFbMessengerModule')) == true) {
            $html .= $this->postProcess();
        }

        if (version_compare(_PS_VERSION_, '1.5', '<')) {
            return $html.$this->renderForm14();
        } else {
            return $html.$this->renderForm();
        }
    }

    protected function renderForm()
    {
        $html = '';

        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitFbMessengerModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(),
            'languages'     => $this->context->controller->getLanguages(),
            'id_language'   => $this->context->language->id,
        );

        $html .= $helper->generateForm($this->getConfigForm());

        return $html;
    }

    protected function renderForm14()
    {
        $html = '';

        $helper = new Helper();

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(),
            'languages' => Language::getLanguages(false),
            'id_language' => $this->context->language->id,
            'THEME_LANG_DIR' => _PS_IMG_.'l/'
        );

        $html .= $helper->generateForm($this->getConfigForm());

        return $html;
    }

    protected function postProcess()
    {
        $html = '';
        $errors = array();

        if (!Tools::getValue('FB_MESSENGER_PAGE_ID') || !is_numeric(Tools::getValue('FB_MESSENGER_PAGE_ID'))) {
            $errors[] = $this->l('Page ID is incorrect');
        }


        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $html .= $this->displayError($error);
            }
        } else {
            $form_values = $this->getConfigFormValues();

            foreach (array_keys($form_values) as $key) {
                Configuration::updateValue($key, Tools::getValue($key));
            }

            $html .= $this->displayConfirmation($this->l('Configuration saved successfully.'));
        }

        return $html;
    }

    protected function getConfigForm()
    {
        $fields = array();

        $fields[]['form'] = array(
            'legend' => array(
                'title' => $this->l('Configuration settings'),
                'icon' => 'icon-cogs',
            ),
            'input' => array(
                /*array(
                    'type' => 'select',
                    'label' => $this->l('Page type'),
                    'name' => 'FB_MESSENGER_TYPE',
                    'class' => 't',
                    'options' => array(
                        'query' => array(
                            array(
                                'id' => 1,
                                'name' => $this->l('Personal page')
                            ),
                            array(
                                'id' => 2,
                                'name' => $this->l('Fan page'),
                            )
                        ),
                        'id' => 'id',
                        'name' => 'name'
                    ),
                ),*/
                array(
                    'type'  => 'text',
                    'label' => $this->l('Page ID'),
                    'name'  => 'FB_MESSENGER_PAGE_ID',
                    'desc'  => $this->l('You can get your page ID at http://www.findmyfbid.com'),
                    'col'   => 2,
                    'class' => 't',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Badge position'),
                    'name' => 'FB_MESSENGER_POSITION',
                    'class' => 't',
                    'options' => array(
                        'query' => array(
                            array(
                                'id' => 1,
                                'name' => $this->l('Bottom left')
                            ),
                            array(
                                'id' => 2,
                                'name' => $this->l('Bottom right'),
                            )
                        ),
                        'id' => 'id',
                        'name' => 'name'
                    ),
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'type' => 'submit',
                'name' => 'submitFbMessengerModule',
            ),
        );

        return $fields;
    }

    protected function getConfigFormValues()
    {
        $fields = array();

        $fields['FB_MESSENGER_PAGE_ID'] = Tools::getValue(
            'FB_MESSENGER_PAGE_ID',
            Configuration::get('FB_MESSENGER_PAGE_ID')
        );
        $fields['FB_MESSENGER_TYPE'] = Tools::getValue(
            'FB_MESSENGER_TYPE',
            Configuration::get('FB_MESSENGER_TYPE')
        );
        $fields['FB_MESSENGER_POSITION'] = Tools::getValue(
            'FB_MESSENGER_POSITION',
            Configuration::get('FB_MESSENGER_POSITION')
        );

        return $fields;
    }

    public function getWarnings($getAll = true)
    {
        $warning = array();

        if (version_compare(_PS_VERSION_, '1.6', '>=')) {
            if (Configuration::get('PS_DISABLE_NON_NATIVE_MODULE')) {
                $warning[] = $this->l('You have to enable non PrestaShop modules at ADVANCED PARAMETERS - PERFORMANCE');
            }
        }

        if (count($warning) && !$getAll) {
            return $warning[0];
        }

        return $warning;
    }
}
