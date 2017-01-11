<?php
/**
 * 2011-2016 JUML69
 *
 *  @author    JUML69 <contact@lyondev.fr>
 *  @copyright 2011-2016 JUML69
 *  @version   Release:1
 *  @license   One Domain Licence
 */

if (! defined('_PS_VERSION_')) {
    exit();
}

class LyoPageEditor extends Module
{

    protected $config_form = false;

    const OVERLOAD = 1;

    const FRONT = 2;

    const NON_NATIVE = 3;

    const COLUMNS = 4;

    const ID_INDEX_COLS = 4;

    const ID_CMS_COLS = 28;

    const ID_PRODUCT_COLS = 29;

    public function __construct()
    {
        $this->name = 'lyopageeditor';
        $this->tab = 'content_management';
        $this->version = '1.1.5';
        $this->author = 'LyonDev';
        $this->need_instance = 0;
        $this->module_key = '43f735a37fa8a994287d1313be821fcc';
        
        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;
        
        parent::__construct();
        
        $this->displayName = $this->l('LyoPageEditor');
        $this->description = $this->l('Allows you to create HTML pages contained easily');
        
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
        
        $this->ps_versions_compliancy = array(
            'min' => '1.6',
            'max' => _PS_VERSION_
        );
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        Configuration::updateValue('LyoPageEditor_fullWidth', true);
        Configuration::updateValue('LyoPageEditor_jquery', false);
        Configuration::updateValue('LyoPageEditor_bootstrap', false);
        Configuration::updateValue('LyoPageEditor_stellar', true);
        Configuration::updateValue('LyoPageEditor_owl_carousel', true);
        Configuration::updateValue('LyoPageEditor_appear', true);
        Configuration::updateValue('LyoPageEditor_animate', true);
        Configuration::updateValue('LyoPageEditor_font_awesome', true);
        
        return parent::install() && $this->registerHook('header') && $this->registerHook('backOfficeHeader');
    }

    public function uninstall()
    {
        Configuration::deleteByName('LyoPageEditor_jquery');
        Configuration::deleteByName('LyoPageEditor_bootstrap');
        Configuration::deleteByName('LyoPageEditor_stellar');
        Configuration::deleteByName('LyoPageEditor_owl_carousel');
        Configuration::deleteByName('LyoPageEditor_appear');
        Configuration::deleteByName('LyoPageEditor_animate');
        Configuration::deleteByName('LyoPageEditor_font_awesome');
        Configuration::deleteByName('LyoPageEditor_fullWidth');
        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        $output = '';
        
        if (empty($this->module_key)) {
            $headerTempate = 'configure_zip.tpl';
        } else {
            $headerTempate = 'configure_addon.tpl';
        }
        
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool) Tools::isSubmit('submitLyoPageEditorModule')) == true) {
            if (! _PS_MODE_DEMO_) {
                $output .= $this->postProcess();
            } else {
                $output .= $this->displayError($this->l('This functionality has been disabled.'));
            }
        }
        
        $this->context->smarty->assign('module_dir', $this->_path);
        $readMefileLink = $this->local_path . 'readme/readme_' . $this->context->language->iso_code . '.pdf';
        $readMefileHttpLink = $this->_path . 'readme/readme_' . $this->context->language->iso_code . '.pdf';
        
        $licenceFileLink = $this->local_path . 'licences/licence_' . $this->context->language->iso_code . '.pdf';
        $licenceFileHTTPLink = $this->_path . 'licences/licence_' . $this->context->language->iso_code . '.pdf';
        
        if (is_file($readMefileLink)) {
            $this->context->smarty->assign('readMefileLink', $readMefileHttpLink);
        } else {
            $this->context->smarty->assign('readMefileLink', $this->_path . 'readme/readme_fr.pdf');
        }
        if (is_file($licenceFileLink)) {
            $this->context->smarty->assign('licenceFileLink', $licenceFileHTTPLink);
        } else {
            $this->context->smarty->assign('licenceFileLink', $this->_path . 'licences/licence_en.pdf');
        }
        
        $output .= $this->context->smarty->fetch($this->local_path . 'views/templates/admin/' . $headerTempate);
        
        return $output . $this->renderForms() . $this->context->smarty->fetch($this->local_path . 'views/templates/admin/info.tpl');
    }

    protected function renderForms()
    {
        $isUseHtmlPurifier = Configuration::get('PS_USE_HTMLPURIFIER');
        $isDisabledOverrides = Configuration::get('PS_DISABLE_OVERRIDES');
        $isDisabledNonNatif = Configuration::get('PS_DISABLE_NON_NATIVE_MODULE');
        
        if ($isDisabledNonNatif == true) {
            return $this->displayError("The component requires you to enable this option") . $this->renderForm(self::NON_NATIVE);
        } elseif ($isUseHtmlPurifier == true && $isDisabledOverrides == true) {
            return $this->displayError("The component requires you to enable overload or disable HTML5 purify") . $this->renderForm(self::OVERLOAD);
        } else {
            return $this->renderForm(self::COLUMNS) . $this->renderForm(self::FRONT);
        }
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm($type)
    {
        $helper = new HelperForm();
        
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitLyoPageEditorModule';
        
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues($type), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );
        
        return $helper->generateForm(array(
            $this->getConfigForm($type)
        ));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm($type)
    {
        switch ($type) {
            case self::OVERLOAD:
                return $this->getConfigFormOverload();
            case self::FRONT:
                return $this->getConfigFormFront();
            case self::NON_NATIVE:
                return $this->getConfigFormNonNatif();
            case self::COLUMNS:
                return $this->getConfigFormColumns();
        }
        return false;
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues($type)
    {
        switch ($type) {
            case self::OVERLOAD:
                return $this->getConfigFormValuesOverload();
            case self::FRONT:
                return $this->getConfigFormValuesFront();
            case self::NON_NATIVE:
                return $this->getConfigFormValuesNonNatif();
            case self::COLUMNS:
                return $this->getConfigFormValuesColumns();
        }
        return false;
    }

    protected function getConfigFormOverload()
    {
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('The component requires you to enable overload or disable HTML5 purify'),
                    'icon' => 'icon-ban'
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable all overrides'),
                        'name' => 'PS_DISABLE_OVERRIDES',
                        'is_bool' => true,
                        'desc' => $this->l('We recommend that you enable this option.'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        )
                    ),
                    
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Disable HTMLPurifier Library'),
                        'name' => 'PS_USE_HTMLPURIFIER',
                        'is_bool' => true,
                        'desc' => $this->l('Clean the HTML content on text fields. We recommend that you leave this option disabled.'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        )
                    ),
                    array(
                        'type' => 'hidden',
                        'name' => 'submit_type'
                    )
                ),
                'submit' => array(
                    'title' => $this->l('Save')
                )
            )
        );
    }

    protected function getConfigFormFront()
    {
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Adds or remove additional libraries in the head section of your pages (head section of html)'),
                    'icon' => 'icon-ban'
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable hack to fullWidth'),
                        'name' => 'LyoPageEditor_fullWidth',
                        'is_bool' => true,
                        'desc' => $this->l('Enable this option if your theme does not accept container-fluid'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable Jquery'),
                        'name' => 'LyoPageEditor_jquery',
                        'is_bool' => true,
                        'desc' => $this->l('Enable this option if your theme does not include jquery library'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable Bootstrap'),
                        'name' => 'LyoPageEditor_bootstrap',
                        'is_bool' => true,
                        'desc' => $this->l('Enable this option if your does not include Bootstrap responsive library'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable Stellar'),
                        'name' => 'LyoPageEditor_stellar',
                        'is_bool' => true,
                        'desc' => $this->l('Disable this option if your theme include Stellar Parallax responsive library'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable OwlCarousel'),
                        'name' => 'LyoPageEditor_owl_carousel',
                        'is_bool' => true,
                        'desc' => $this->l('Disable this option if your theme include Owl Carousel library'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable appear'),
                        'name' => 'LyoPageEditor_appear',
                        'is_bool' => true,
                        'desc' => $this->l('Disable this option if your theme include appear library'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable animate'),
                        'name' => 'LyoPageEditor_animate',
                        'is_bool' => true,
                        'desc' => $this->l('Disable this option if your theme include animate css'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable font awesome'),
                        'name' => 'LyoPageEditor_font_awesome',
                        'is_bool' => true,
                        'desc' => $this->l('Disable this option if your theme include Font Awesome library'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        )
                    ),
                    
                    array(
                        'type' => 'hidden',
                        'name' => 'submit_type'
                    )
                ),
                'submit' => array(
                    'title' => $this->l('Save')
                )
            )
        );
    }

    protected function getConfigFormNonNatif()
    {
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('The component requires you to enable this option'),
                    'icon' => 'icon-ban'
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable non Prestashop modules'),
                        'name' => 'PS_DISABLE_NON_NATIVE_MODULE',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        )
                    ),
                    array(
                        'type' => 'hidden',
                        'name' => 'submit_type'
                    )
                ),
                'submit' => array(
                    'title' => $this->l('Save')
                )
            )
        );
    }

    protected function getConfigFormColumns()
    {
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('To use the full screen option, we advise you to remove the columns on the relevant pages'),
                    'icon' => 'icon-ban'
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('disable columns of Home page'),
                        'name' => 'PS_DISABLE_HOME_PAGE_COLUMNS',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('disable columns of Products pages'),
                        'name' => 'PS_DISABLE_PRODUCT_PAGE_COLUMNS',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('disable columns of CMS pages'),
                        'name' => 'PS_DISABLE_CMS_PAGE_COLUMNS',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        )
                    ),
                    array(
                        'type' => 'hidden',
                        'name' => 'submit_type'
                    )
                ),
                'submit' => array(
                    'title' => $this->l('Save')
                )
            )
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValuesOverload()
    {
        return array(
            'PS_USE_HTMLPURIFIER' => ! Configuration::get('PS_USE_HTMLPURIFIER'),
            'PS_DISABLE_OVERRIDES' => ! Configuration::get('PS_DISABLE_OVERRIDES'),
            'submit_type' => self::OVERLOAD
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValuesFront()
    {
        return array(
            'LyoPageEditor_fullWidth' => Configuration::get('LyoPageEditor_fullWidth'),
            'LyoPageEditor_jquery' => Configuration::get('LyoPageEditor_jquery'),
            'LyoPageEditor_bootstrap' => Configuration::get('LyoPageEditor_bootstrap'),
            'LyoPageEditor_stellar' => Configuration::get('LyoPageEditor_stellar'),
            'LyoPageEditor_owl_carousel' => Configuration::get('LyoPageEditor_owl_carousel'),
            'LyoPageEditor_appear' => Configuration::get('LyoPageEditor_appear'),
            'LyoPageEditor_animate' => Configuration::get('LyoPageEditor_animate'),
            'LyoPageEditor_font_awesome' => Configuration::get('LyoPageEditor_font_awesome'),
            'submit_type' => self::FRONT
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValuesNonNatif()
    {
        return array(
            'PS_DISABLE_NON_NATIVE_MODULE' => ! Configuration::get('PS_DISABLE_NON_NATIVE_MODULE'),
            'submit_type' => self::NON_NATIVE
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValuesColumns()
    {
        $sql = "select id_theme_meta,left_column,right_column from " . _DB_PREFIX_ . 'theme_meta where id_theme_meta in (' . self::ID_INDEX_COLS . ',' . self::ID_CMS_COLS . ',' . self::ID_PRODUCT_COLS . ')';
        $data = Db::getInstance()->executeS($sql, true, false);
        
        $isHomeColumns = ($data[0]['left_column'] || $data[0]['right_column'] ? true : false);
        $isCMSColumns = ($data[1]['left_column'] || $data[1]['right_column'] ? true : false);
        $isProductColumns = ($data[2]['left_column'] || $data[2]['right_column'] ? true : false);
        
        return array(
            'PS_DISABLE_HOME_PAGE_COLUMNS' => ! $isHomeColumns,
            'PS_DISABLE_CMS_PAGE_COLUMNS' => ! $isCMSColumns,
            'PS_DISABLE_PRODUCT_PAGE_COLUMNS' => ! $isProductColumns,
            
            'submit_type' => self::COLUMNS
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        // d(Tools::getValue('submit_type'));
        switch (Tools::getValue('submit_type')) {
            case self::OVERLOAD:
                $override = (int) Tools::getValue('PS_DISABLE_OVERRIDES');
                $purifier = (int) Tools::getValue('PS_USE_HTMLPURIFIER');
                
                Configuration::updateGlobalValue('PS_DISABLE_OVERRIDES', ! $override);
                Tools::generateIndex();
                
                Configuration::updateValue("PS_USE_HTMLPURIFIER", ! $purifier);
                
                break;
            
            case self::FRONT:
                $form_values = $this->getConfigFormValues(self::FRONT);
                
                foreach (array_keys($form_values) as $key) {
                    if ("submit_type" == $key) {
                        continue;
                    }
                    // p(key . " : " . Tools::getValue($key, false));
                    Configuration::updateValue($key, Tools::getValue($key, false));
                }
                
                break;
            case self::NON_NATIVE:
                $nonNatif = (int) Tools::getValue('PS_DISABLE_NON_NATIVE_MODULE');
                
                Configuration::updateGlobalValue('PS_DISABLE_NON_NATIVE_MODULE', ! $nonNatif);
                Tools::generateIndex();
                
                break;
            
            case self::COLUMNS:
                $isHomeColumns = (int) Tools::getValue('PS_DISABLE_HOME_PAGE_COLUMNS');
                $isCMSColumns = (int) Tools::getValue('PS_DISABLE_CMS_PAGE_COLUMNS');
                $isProductColumns = (int) Tools::getValue('PS_DISABLE_PRODUCT_PAGE_COLUMNS');
                
                $sql = 'UPDATE ' . _DB_PREFIX_ . 'theme_meta SET left_column=' . ($isHomeColumns ? 0 : 1) . ',right_column=' . ($isHomeColumns ? 0 : 1) . ' WHERE id_theme_meta=' . self::ID_INDEX_COLS;
                Db::getInstance()->execute($sql);
                $sql = 'UPDATE ' . _DB_PREFIX_ . 'theme_meta SET left_column=' . ($isCMSColumns ? 0 : 1) . ',right_column=' . ($isCMSColumns ? 0 : 1) . ' WHERE id_theme_meta=' . self::ID_CMS_COLS;
                Db::getInstance()->execute($sql);
                $sql = 'UPDATE ' . _DB_PREFIX_ . 'theme_meta SET left_column=' . ($isProductColumns ? 0 : 1) . ',right_column=' . ($isProductColumns ? 0 : 1) . ' WHERE id_theme_meta=' . self::ID_PRODUCT_COLS;
                Db::getInstance()->execute($sql);
                
                break;
        }
        return $this->displayConfirmation($this->l('Your settings have been updated.'));
    }

    /**
     * Add the CSS & JavaScript files you want to be loaded in the BO.
     */
    public function hookBackOfficeHeader()
    {
        if (! Module::isEnabled($this->name)) {
            return;
        }
        $isCmsPage = false;
        $isProductPage = false;
        $isEditorialPage = false;
        $isPrestablog = false;
        $isCategPage = false;
        $isCategCMSPage = false;
        $isAdminBlogPost = false;
        $isAdminBlockConstructor = false;
        
        if (Tools::getValue('configure') == 'editorial') {
            $isEditorialPage = true;
        }
        if (Tools::getValue('controller') == 'AdminCmsContent' && (Tools::getIsset('addcms_category') || Tools::getIsset('submitAddcms_category') || Tools::getIsset('updatecms_category'))) {
            $isCategCMSPage = true;
        }
        if (Tools::getValue('controller') == 'AdminCmsContent' && (Tools::getValue('addcms') !== false || Tools::getValue('updatecms') !== false)) {
            $isCmsPage = true;
        }
        if (Tools::getValue('controller') == 'AdminProducts' && (Tools::getValue('addproduct') !== false || Tools::getValue('updateproduct') !== false)) {
            $isProductPage = true;
        }
        if (Tools::getValue('configure') == 'prestablog' && (Tools::getIsset('addNews') || Tools::getIsset('editNews') || Tools::getIsset('idN'))) {
            $isPrestablog = true;
        }
        if (Tools::getValue('controller') == 'AdminCategories' && (Tools::getIsset('submitAddcategory') || Tools::getIsset('updatecategory') || Tools::getIsset('addcategory'))) {
            $isCategPage = true;
        }
        if (Tools::getValue('controller') == 'AdminBlogPost' && (Tools::getValue('id_smart_blog_post') || Tools::getIsset('updatesmart_blog_post') || Tools::getIsset('addsmart_blog_post'))) {
            $isAdminBlogPost = true;
        }
        if (Tools::getValue('controller') == 'AdminBlockConstructor' && (Tools::getValue('submitAddbelvg_blockconstructor') || Tools::getIsset('addbelvg_blockconstructor') || Tools::getIsset('updatebelvg_blockconstructor'))) {
            $isAdminBlockConstructor = true;
        }
        if ($isEditorialPage || $isCmsPage || $isProductPage || $isPrestablog || $isCategPage || $isCategCMSPage || $isAdminBlogPost || $isAdminBlockConstructor) {
            $this->context->controller->addJquery();
            $this->context->controller->addJqueryUi('ui.dialog');
            $this->context->controller->addJS($this->_path . 'views/js/lyoEditor/cmsOverride.js');
            $this->context->controller->addCSS("https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css");
            $this->context->controller->addJS("https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js");
            
            $iso_code_language = $this->context->language->iso_code;
            
            $this->context->controller->addJS($this->_path . "/views/js/lyoEditor/lang/" . $iso_code_language . ".js");
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        if (Configuration::get('LyoPageEditor_jquery')) {
            $this->context->controller->addJquery();
        }
        
        if (Configuration::get('LyoPageEditor_bootstrap')) {
            $this->context->controller->addCSS($this->_path . "/views/css/bootstrap.min.css");
            $this->context->controller->addJS($this->_path . "/views/js/bootstrap.min.js");
        }
        if (Configuration::get('LyoPageEditor_stellar')) {
            $this->context->controller->addJS($this->_path . "/views/js/jquery.stellar.min.js");
        }
        if (Configuration::get('LyoPageEditor_owl_carousel')) {
            $this->context->controller->addCSS($this->_path . "/views/css/owl.carousel.css");
            $this->context->controller->addCSS($this->_path . "/views/css/owl.theme.css");
            $this->context->controller->addJS($this->_path . "/views/js/owl.carousel.min.js");
        }
        if (Configuration::get('LyoPageEditor_appear')) {
            $this->context->controller->addJS($this->_path . "/views/js/jquery.appear.js");
        }
        if (Configuration::get('LyoPageEditor_animate')) {
            $this->context->controller->addCSS($this->_path . "/views/css/animate.min.css");
        }
        if (Configuration::get('LyoPageEditor_font_awesome')) {
            $this->context->controller->addCSS($this->_path . "/views/css/font-awesome.min.css");
        }
        
        $this->context->controller->addCSS($this->_path . "/views/css/app_front.css");
        $this->context->controller->addJS($this->_path . "/views/js/lyoEditor/appli_front.js");
        if (Configuration::get('LyoPageEditor_fullWidth')) {
            $this->context->controller->addJS($this->_path . "/views/js/lyoEditor/hackFullWidth.js");
        }
    }
}
