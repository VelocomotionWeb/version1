<?php
/**
* 2007-2016 hb50.fr
*
* NOTICE OF LICENSE
*
* DISCLAIMER
*
*  @author    Hubert <admin@hb50.fr>
*  @copyright 2007-2015 admin@hb50.fr
*  @license   hb50.fr

 * @last update  13/06/2015   admin@hb50.fr 
 
 * version 0.3
    - you can select countries to put in selector
 * version 0.4
    - module is now attached to displaytop
    - you can move this module to left, right, or Nav
 * version 0.4.1
    - retrocompatibility PS 1.4 and 15
 * version 0.4.2
    - fix bug list of countries
 * version 0.4.3
    - fix Notice
    - add retrocompatibility
 * version 0.5.0
    - detect browser Langue
 * version 0.6.0
    - compatibility PS 1.7
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class GglTranslate extends Module
{
    private $countries;
    
    public function __construct()
    {
        $this->name = 'ggltranslate';
        $this->tab = 'i18n_localization';
        $this->version = '0.6.0';
        $this->author = 'admin@hb50.fr';
        $this->module_key = 'fca8b8ffbbae9c35fe81414caebc3e2d';
        parent::__construct();
        $this->displayName = $this->l('Google Translation');
        $this->description = $this->l('Translate your website with google, about 60 languages available');
        $this->bootstrap = false;
        // Retrocompatibility < 1.5
        if (_PS_VERSION_ < '1.5') {
            require(_PS_MODULE_DIR_.$this->name.'/backward_compatibility/backward.php');
        }
    }
    
    public function install()
    {
        //version before 0.4
        Configuration::deleteByName('PS_GGL_TRANSLATION_META');
        
        if (!parent::install() || !$this->registerHook('top')) {
            return false;
        }

        $this->conf_keys = array('PS_GGL_TRANSLATION_COUNTRIES', 'PS_GGL_TRANSLATION_AUTODETECT');
        Configuration::updateValue('PS_GGL_TRANSLATION_COUNTRIES', 'All');
        Configuration::updateValue('PS_GGL_TRANSLATION_AUTODETECT', 1);
        if (Tools::substr(_PS_VERSION_, 0, 3) == '1.2') {
            Configuration::set('PS_GGL_TRANSLATION_COUNTRIES', 'All');
            Configuration::set('PS_GGL_TRANSLATION_AUTODETECT', 1);
        }
        return true;
    }

    public function uninstall()
    {
        Configuration::deleteByName('PS_GGL_TRANSLATION_COUNTRIES');
        Configuration::deleteByName('PS_GGL_TRANSLATION_AUTODETECT');

        return parent::uninstall();
    }

    /**
     * @name getModuleURL()
     * @return this string module URL
     */
    public function getModuleURL()
    {
        if (Tools::substr(_PS_VERSION_, 0, 3) == '1.2') {
            $this->moduleURL = 'http://'.htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').__PS_BASE_URI__;
        } else {
            $this->moduleURL = 'http://'.Tools::getHttpHost(false, true) . __PS_BASE_URI__;
        }
        return Tools::substr($this->moduleURL, 0, -1).$this->_path;
    }

    public function getAdminLink()
    {
        if (_PS_VERSION_ < '1.5') {
            $this->adminLink = $_SERVER['REQUEST_URI'];
            
        } else {
            $this->adminLink = $this->context->link->getAdminLink('AdminModules', false) . '&' .
                                    http_build_query(
                                        array('configure' => $this->name,
                                        'tab_module' => $this->tab, 'token' => Tools::getAdminTokenLite('AdminModules'))
                                    );
        }
        return $this->adminLink;
    }
    
    public function getGglCountries()
    {
        $countries = array(
                'af'=>$this->l('afrikaans')
                ,'sq'=>$this->l('albanian')
                ,'de'=>$this->l('german')
                ,'en'=>$this->l('english')
                ,'ar'=>$this->l('arabic')
                ,'hy'=>$this->l('armenian')
                ,'az'=>$this->l('azeri')
                ,'eu'=>$this->l('basque')
                ,'bn'=>$this->l('bengalese')
                ,'be'=>$this->l('belarus')
                ,'bg'=>$this->l('bulgarian')
                ,'ca'=>$this->l('catalan')
                ,'zh-CN'=>$this->l('chinese')
                ,'zh-TW'=>$this->l('chinese (oof traditional ideograms)')
                ,'co'=>$this->l('corean')
                ,'hr'=>$this->l('croatian')
                ,'da'=>$this->l('danish')
                ,'es'=>$this->l('spanish')
                ,'eo'=>$this->l('esperanto')
                ,'et'=>$this->l('estonian')
                ,'tl'=>$this->l('filipino')
                ,'fi'=>$this->l('finnish')
                ,'fr'=>$this->l('french')
                ,'gl'=>$this->l('galician')
                ,'cy'=>$this->l('welsh')
                ,'ka'=>$this->l('georgian')
                ,'el'=>$this->l('greek')
                ,'ht'=>$this->l('haitian')
                ,'iw'=>$this->l('hebrew')
                ,'hi'=>$this->l('hindi')
                ,'hu'=>$this->l('hungarian')
                ,'id'=>$this->l('indonesian')
                ,'ga'=>$this->l('irish')
                ,'is'=>$this->l('icelandic')
                ,'it'=>$this->l('italian')
                ,'ja'=>$this->l('japanese')
                ,'kn'=>$this->l('kannada')
                ,'lo'=>$this->l('lao')
                ,'la'=>$this->l('latin')
                ,'lv'=>$this->l('lettish')
                ,'lt'=>$this->l('lithanian')
                ,'mk'=>$this->l('macedonian')
                ,'ms'=>$this->l('malay')
                ,'mt'=>$this->l('maltese')
                ,'nl'=>$this->l('dutch')
                ,'no'=>$this->l('norwegian')
                ,'ur'=>$this->l('urdu')
                ,'fa'=>$this->l('persian')
                ,'pl'=>$this->l('polish')
                ,'pt'=>$this->l('portugese')
                ,'ro'=>$this->l('romanian')
                ,'ru'=>$this->l('russian')
                ,'sr'=>$this->l('serbian')
                ,'sk'=>$this->l('slovak')
                ,'sl'=>$this->l('slovenian')
                ,'sv'=>$this->l('swedish')
                ,'sw'=>$this->l('swahili')
                ,'ta'=>$this->l('tamil')
                ,'cs'=>$this->l('czech')
                ,'te'=>$this->l('telugu')
                ,'th'=>$this->l('thai')
                ,'tr'=>$this->l('turkish')
                ,'uk'=>$this->l('ukrainian')
                ,'vi'=>$this->l('vietnamese')
                ,'yi'=>$this->l('yiddish')
        );
        
        return $countries;
    }

    public function getContent()
    {
        $this->countries = $this->getGglCountries();
        $this->_html = '';
        if (Tools::getValue('update_value')) {
            $this->postProcess();
        }
        
        $this->_html .= '
            <form action="'.$this->getAdminLink().'" method="post" >
            <fieldset>
            <legend>'.$this->l('Google Translation Countries').' '.$this->version.'</legend>          
            
            <h2>'.$this->l('To show only some countries, select them below.').'</h2>
            <p><i>'.$this->l('To select all the countries, check nothing.').'</i></p>';
        asort($this->countries);
        $nb_p = count($this->countries);
        $nb_col = ceil($nb_p / 3);
        $this->_html .= '<table border="0">
                        <tr><td width="250" valign="top">';
        $i = 0;
        
        $PS_GGL_TRANSLATION_COUNTRIES = explode(',', Configuration::get('PS_GGL_TRANSLATION_COUNTRIES'));
        foreach ($this->countries as $code => $country) {
            $this->_html .= '<input type="checkbox" '.(in_array($code, $PS_GGL_TRANSLATION_COUNTRIES)?"checked='checked'":'').' name="countries[]" value="'.$code.'" /> '.$country.'<br/>';
            $i++;
            if ($i >= $nb_col) {
                $i = 0;
                $this->_html .= '</td><td width="250" valign="top">';
            }
        }
        $this->_html .= '</td></tr></table><br/>';
        $PS_GGL_TRANSLATION_AUTODETECT = Configuration::get('PS_GGL_TRANSLATION_AUTODETECT');
        $this->_html .= '<br/>'.
            $this->l('Detect browser language to automatically translate pages in his language?').' 
            <input type="checkbox" '.($PS_GGL_TRANSLATION_AUTODETECT == 1 ? 'checked="checked"' : '').' name="PS_GGL_TRANSLATION_AUTODETECT" value="1" /><br/><br/>';
        $this->_html .= '
            <div align="left">                
                <input type="submit"  name="update_value" value="'.$this->l('Update Google Translation Settings').'" />
            </div>
            </fieldset>
            </form><br/><br/>  ';

        $this->_html .= '
            <fieldset>
            <legend>'.$this->l('Style of your Block Google Translation').'</legend>         
            <h3>'.$this->l('Modify style and place of Block Translation').'</h3>
            <p>'.$this->l('You can modify style in file ').'ggltranslate.tpl</p><br/>    
            <p>'.$this->l('Default style is:').'</p>
            <pre style="border:1px dotted black;padding:2px;">style="float:right; margin:2px 0 6px;border:1px solid #646464;"</pre><br/>
            <p>'.$this->l('For example, you can choose to put your Block in top right of your website').'</p>
            <pre style="border:1px dotted black;padding:2px;">style="position:absolute; top:5px; right:5px ;border:1px solid #646464;"</pre><br/>                    
            </fieldset>
            
            <fieldset>
            <legend>'.$this->l('Move the module').'</legend>         
            <h3>'.$this->l('Place the module where you want').'</h3>
            <p>'.$this->l('Go to Modules Position').'</p><br/>    
            <p>'.$this->l('Delete Module From LeftColumn or DisplayTop').'</p>
            <p>'.$this->l('Transplant the Module where you want (DisplayTop, Nav, RightColumn)').'</p>
            </fieldset>
            
            <fieldset>
            <legend>'.$this->l('Help').'</legend>         
            <p>'.$this->l('French PDF: ').'<a href="'.$this->getModuleURL().'readme_fr.pdf">'.$this->getModuleURL().'readme_fr.pdf</a></p><br/>    
            <p>'.$this->l('English PDF: ').'<a href="'.$this->getModuleURL().'readme_en.pdf">'.$this->getModuleURL().'readme_en.pdf</a></p>
            </fieldset>
            ';

        return $this->_html;
    }


    public function postProcess()
    {
        if (Tools::getValue('update_value')) {
            $codes = '';
            if (Tools::getValue('countries')) {
                foreach (Tools::getValue('countries') as $code) {
                    $codes .= ($codes == ''?'':',').$code;
                }
                Configuration::updateValue('PS_GGL_TRANSLATION_COUNTRIES', $codes);
                //set all datas in memory for PS 1.2
                if (Tools::substr(_PS_VERSION_, 0, 3) == '1.2') {
                    Configuration::set('PS_GGL_TRANSLATION_COUNTRIES', $codes);
                }
            } else {
                Configuration::updateValue('PS_GGL_TRANSLATION_COUNTRIES', 'All');
                //set all datas in memory for PS 1.2
                if (Tools::substr(_PS_VERSION_, 0, 3) == '1.2') {
                    Configuration::set('PS_GGL_TRANSLATION_COUNTRIES', 'All');
                }
            }
            
            if (Tools::getValue('PS_GGL_TRANSLATION_AUTODETECT') == 1) {
                $PS_GGL_TRANSLATION_AUTODETECT = 1;
            } else {
                $PS_GGL_TRANSLATION_AUTODETECT = 0;
            }
            Configuration::updateValue('PS_GGL_TRANSLATION_AUTODETECT', $PS_GGL_TRANSLATION_AUTODETECT);
            //set all datas in memory for PS 1.2
            if (Tools::substr(_PS_VERSION_, 0, 3) == '1.2') {
                Configuration::set('PS_GGL_TRANSLATION_AUTODETECT', $PS_GGL_TRANSLATION_AUTODETECT);
            }

            $this->_html .= $this->displayConfirmation($this->l('Google Translation settings updated.'));
        }
        return $this->_html;
    }
    
    public function userLang()
    {
        $userLang = false;
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) && Configuration::get('PS_GGL_TRANSLATION_AUTODETECT') == 1) {
            $navLang = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
            $userLang = Tools::strtolower(Tools::substr($navLang[0], 0, 2));
        }
        
        if (!$userLang) {
            $userLang = Language::getIsoById((int)(Configuration::get('PS_LANG_DEFAULT')));
        }
        
        return $userLang;
    }

    public function hookLeftColumn($params)
    {
        $this->context->smarty->assign('PS_GGL_TRANSLATION_AUTODETECT', Configuration::get('PS_GGL_TRANSLATION_AUTODETECT'));
        $this->context->smarty->assign('LangBrowser', $this->userLang());
        $this->context->smarty->assign('PS_GGL_TRANSLATION_COUNTRIES', Configuration::get('PS_GGL_TRANSLATION_COUNTRIES'));
        if (version_compare(_PS_VERSION_, '1.7.0.0') >= 0) {
            $this->context->smarty->assign('lang_iso', $this->context->language->iso_code);
        }
        return $this->display(__FILE__, 'views/templates/front/ggltranslate.tpl');
    }

    public function hookRightColumn($params)
    {
        return $this->hookLeftColumn($params);
    }

    public function hookheadertop($params)
    {
        return $this->hookLeftColumn($params);
    }

    public function hookHeader($params)
    {
        return $this->hookTop($params);
    }
    public function hookTop($params)
    {
        return $this->hookLeftColumn($params);
    }
    public function hookDisplayNav($params)
    {
        return $this->hookLeftColumn($params);
    }
}
