<?php
/**
 * 2008-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * Read in the module
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    Mediacom87 <support@mediacom87.net>
 * @copyright 2008-2015 Mediacom87
 * @license   define in the module
 * @version 1.7.0
 */

class blockzopim extends Module {
    /**
     * __construct function.
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        $this->name = 'blockzopim';
        $this->tab = version_compare(_PS_VERSION_, '1.4.0.0', '>=') ? 'advertising_marketing' : 'Mediacom87';
        $this->version = '1.7.0';
        $this->need_instance = 1;
        $this->author = 'Mediacom87';
        parent::__construct();
        $this->displayName = $this->l('Block Zopim');
        $this->description = $this->l('Integrate your Zopim script on your site.');

        $this->affiliateurl = 'http://bit.ly/zopimaff';

        if (version_compare(_PS_VERSION_, '1.6.0.0', '>=')) {
            $this->bootstrap = true;
        }
        if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
            $this->moduleList();
        }
    }

    /**
     * install function.
     *
     * @access public
     * @return void
     */
    function install()
    {
        $this->addAsTrusted();
        if (!parent::install() || !$this->registerHook('footer') || !Configuration::updateValue('ZOPIM', '') || !Configuration::updateValue('MED_MODULES_LIST', true) || !Configuration::updateValue('MED_MODULE_TIME', 0))
            return false;
        return true;
    }

    /**
     * uninstall function.
     *
     * @access public
     * @return void
     */
    function uninstall()
    {
        if (!Configuration::deleteByName('ZOPIM') || !parent::uninstall() || !Configuration::deleteByName('MED_MODULE_TIME'))
            return false;
        return true;
    }

    /**
     * getContent function.
     *
     * @access public
     * @param string $tab (default: 'AdminModules')
     * @return void
     */
    public function getContent($tab = 'AdminModules')
    {
        $output = '';
        if (Tools::isSubmit('submitblockzopim')) {
            if (Configuration::updateValue('ZOPIM', Tools::getValue('zopimscript'))) {
                $output .= $this->displayConfirmation($this->l('Settings updated'));
            }
        } elseif (Tools::isSubmit('saveModule')) {
            if (Configuration::get('MED_MODULES_LIST') != Tools::getValue('MED_MODULES_LIST')) {
                if (is_file(_PS_ROOT_DIR_.self::CACHE_FILE_MUST_HAVE_MODULES_LIST)) {
                    Tools::deleteFile(_PS_ROOT_DIR_.self::CACHE_FILE_MUST_HAVE_MODULES_LIST);
                    Configuration::deleteByName('MED_MODULE_TIME');
                }
            }
            if (Configuration::updateValue('MED_MODULES_LIST', Tools::getValue('MED_MODULES_LIST'))) {
                $output .= $this->displayConfirmation($this->l('Settings updated'));
            }
        }
        $output .= '
            <h2>'.$this->displayName.'</h2>
            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
        ';
        return $output.$this->displayForm();
    }

    /**
     * displayForm function.
     *
     * @access public
     * @return void
     */
    public function displayForm()
    {
        $message = '
            <p>
                '.$this->l('If you want to customize the Chat window directly in the PrestaShop Backoffice and adapt its design by shop when you use the multi shop mode.').'
            </p>
            <p>
                <a style="color: #900; text-decoration: underline;" href="http://www.prestatoolbox.'.$this->isoCode(true).'/index.php?controller=product&id_product=304" target="_blank"><img src="'.$this->_path.'logo.gif" width="16" height="16" /> <b>'.$this->l('Discover our enhanced version of this module.').'</b></a>
            </p>
        ';
        return '
			'.$this->panelHeading($this->l('Settings')).'
			<form method="post">
				<label><a href="'.$this->affiliateurl.'" target="_blank" title="'.$this->l('Zopim live chat solutions Create your account').'"><img src="'.$this->_path.'images/zopim-logo-135x48.gif" alt="'.$this->l('Zopim live chat solutions').'" /></a></label>
				<div class="margin-form">
				    <input type="text" name="zopimscript" value="'.Configuration::get('ZOPIM').'" placeholder="'.$this->l('Example:').' Ls3s2zqg6mTq4laFyAfVeLkTVnK5YPHn" size="50" />
					<p>'.$this->l('To configure this module, after registering on').' <a href="'.$this->affiliateurl.'" title="'.$this->l('Above all things, subscribe to').' ZOPIM" target="_blank" style="color:orange"><b>ZOPIM</b></a>, '.$this->l('get code to insert the script and find the ID of your site in bold red represent the example below. Enter the ID above.').'
					</p>
					<p class="clear">&lt;!--Start of Zopim Live Chat Script--&gt;<br />
                        &lt;script type=&quot;text/javascript&quot;&gt;<br />
                        window.$zopim||(function(d,s){var z=$zopim=function(c){z._.push(c)},$=z.s=<br />
                        d.createElement(s),e=d.getElementsByTagName(s)[0];z.set=function(o){z.set.<br />
                        _.push(o)};z._=[];z.set._=[];$.async=!0;$.setAttribute(\'charset\',\'utf-8\');<br />
                        $.src=\'//v2.zopim.com/?<b><span style="color:#900" title="\'.$this->l(\'Copy your site ID represented like this one\').\'">Ls3s2zqg6mTq4laFyAfVeLkTVnK5YPHn</span></b>\';z.t=+new Date;$.<br />
                        type=\'text/javascript\';e.parentNode.insertBefore($,e)})(document,\'script\');<br />
                        &lt;/script&gt;<br />
                        &lt;!--End of Zopim Live Chat Script--&gt;
                    </p>
				</div>
				'.$this->submitButton('submitblockzopim').'
            </form>
            '.$this->displayInfoMessage($message).'
            '.$this->panelEnding().'

            '.$this->panelHeading($this->l('Informations'), 'fa-info-circle').'
			<h3 style="text-align:center">'.$this->l('Discover our other dev on:').'</h3>
			<p style="text-align:center"><a href="http://www.prestatoolbox.'.$this->isoCode(true).'/1_mediacom87" target="_blank"><img src="http://i.imgur.com/JK49LYo.png" alt="PrestaToolbox" height="100" /></a> '.$this->l('Or').' <a href="http://addons.prestashop.com/'.$this->isoCode().'/2_community?contributor=322" target="_blank" title="PrestaShop Addons"><img src="http://i.imgur.com/9pVjllc.png" alt="PrestaShop Addons" /></a></p>
			'.$this->displayPaypalButton('MDQZ82DZ8UEQQ').'

		'.$this->panelEnding().'

		'.$this->panelHeading($this->l('Ads'), 'fa-plus-square').'
			<p><a href="http://www.prestatoolbox.'.$this->isoCode(true).'/1_mediacom87?utm_source=module&utm_medium=cpc&utm_campaign=skysabar" target="_blank" title="'.$this->l('Mediacom87 WebAgency').'">'.$this->l('You can also support our agency by clicking the advertising below').'.</a></p>
			<p style="text-align:center">
				'.$this->displayGoogleAds('5753334670').'
			</p>
		'.$this->panelEnding().'

		'.$this->displayListModules();
    }

    /**
     * hookFooter function.
     *
     * @access public
     * @param mixed $params
     * @return void
     */
    function hookFooter($params)
    {
        global $smarty;
        $zopim = Configuration::get('ZOPIM');
        if ($zopim) {
            $smarty->assign('zopim', $zopim);
            return $this->display(__FILE__, 'blockzopim.tpl');
        }
    }

    /**
     * displayInfoMessage function.
     *
     * @access private
     * @param mixed $message
     * @return void
     */
    private function displayInfoMessage($message)
    {
        return '
            <div class="'.(version_compare(_PS_VERSION_, '1.6.0.0', '<') ? 'info' : 'alert alert-info' ).'">
			    '.$message.'
			</div>
        ';
    }

    /**
     * addAsTrusted function.
     *
     * @access public
     * @return void
     */
    public function addAsTrusted()
    {
        if (defined('self::CACHE_FILE_TRUSTED_MODULES_LIST') == true) {
            if (isset($this->context->controller->controller_name) && $this->context->controller->controller_name == 'AdminModules') {
                $sxe = new SimpleXMLElement('<theme/>');

                $modules = $sxe->addChild('modules');
                $module = $modules->addChild('module');
                $module->addAttribute('action', 'install');
                $module->addAttribute('name', $this->name);

                $trusted = $sxe->saveXML();
                file_put_contents(_PS_ROOT_DIR_ . '/config/xml/themes/' . $this->name . '.xml', $trusted);
                if (is_file(_PS_ROOT_DIR_ . Module::CACHE_FILE_UNTRUSTED_MODULES_LIST)) {
                    Tools::deleteFile(_PS_ROOT_DIR_ . Module::CACHE_FILE_UNTRUSTED_MODULES_LIST);
                }
            }
        }
    }

    /**
     * displayListModules function.
     *
     * @access private
     * @return void
     */
    private function displayListModules()
    {
        if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
            return $this->panelHeading($this->l('Listing Modules')).'
    		    <form method="post" class="form-horizontal">
                    <div class="form-group">
    					<label class="control-label col-sm-2">'.$this->l('Replace the flow of products Addons by PrestatoolBox').'</label>
    					<div class="margin-form col-sm-9">
    						'.$this->radioButton('MED_MODULES_LIST', $this->l('Yes'), $this->l('No')).'
    					</div>
    				</div>

    				'.$this->submitButton('saveModule').'
                </form>
    		'.$this->panelEnding();
        }
    }

    /**
     * displayPaypalButton function.
     *
     * @access private
     * @param mixed $id
     * @return void
     */
    private function displayPaypalButton($id)
    {
        return '
            <p>
                <form action="https://www.paypal.com/cgi-bin/webscr" method="post" style="text-align:center" target="_blank">
                    <input type="hidden" name="cmd" value="_s-xclick">
                    <input type="hidden" name="hosted_button_id" value="'.$id.'">
                    <input type="image" src="https://www.paypalobjects.com/fr_FR/FR/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - la solution de paiement en ligne la plus simple et la plus sécurisée !">
                    <img alt="" border="0" src="https://www.paypalobjects.com/fr_FR/i/scr/pixel.gif" width="1" height="1">
                </form>
            </p>
        ';
    }

    /**
     * displayGoogleAds function.
     *
     * @access private
     * @param mixed $slot
     * @return void
     */
    private function displayGoogleAds($slot)
    {
        return '
            <script type="text/javascript"><!--
				google_ad_client = "ca-pub-1663608442612102";
				google_ad_slot = "'.$slot.'";
				google_ad_width = 728;
				google_ad_height = 90;
				//-->
			</script>
			<script type="text/javascript"
			src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
			</script>
        ';
    }

    /**
     * panelHeading function.
     *
     * @access private
     * @param mixed $title
     * @param string $icon (default: 'fa-cog')
     * @return void
     */
    private function panelHeading($title, $icon = 'fa-cog')
    {
        if (version_compare(_PS_VERSION_, '1.6.0.0', '<')) {
            return '<fieldset class="space"><legend><i class="fa '.$icon.'"></i> '.$title.'</legend>';
        } else {
            return '<div class="panel"><div class="panel-heading"><i class="fa '.$icon.'"></i> '.$title.'</div>';
        }
    }

    /**
     * moduleList function.
     *
     * @access private
     * @return void
     */
    private function moduleList()
    {
        $conf = Configuration::getMultiple(array('MED_MODULES_LIST', 'MED_MODULE_TIME'));
        if (isset($conf['MED_MODULES_LIST']) && $conf['MED_MODULES_LIST']) {
            $time = time() - (23 * 60 * 60);
            if ($time > $conf['MED_MODULE_TIME']) {
                $must_have_content = Tools::file_get_contents('http://xml-feed.mediacom87.netdna-cdn.com/'.$this->isoCode().'/must_have_modules_list.xml');
                $must_have_file = _PS_ROOT_DIR_.self::CACHE_FILE_MUST_HAVE_MODULES_LIST;
                if (file_put_contents($must_have_file, $must_have_content)) {
                    Configuration::updateValue('MED_MODULE_TIME', time());
                }
            }
        }
    }

    /**
     * panelEnding function.
     *
     * @access private
     * @return void
     */
    private function panelEnding()
    {
        if (version_compare(_PS_VERSION_, '1.6.0.0', '>=')) {
            return '</div>';
        } else {
            return '</fieldset>';
        }
    }


    /**
     * submitButton function.
     *
     * @access private
     * @param string $name (default: 'save')
     * @return void
     */
    private function submitButton($name = 'save')
    {
        if (version_compare(_PS_VERSION_, '1.6.0.0', '<')) {
            return '<p><input type="submit" class="button" name="'.$name.'" value="'.$this->l('Save').'" /></p>';
        } else {
            return '<div class="panel-footer"><button class="btn btn-default" name="'.$name.'" type="submit"><i class="process-icon-save"></i> '.$this->l('Save').'</button></div>';
        }
    }

    /**
     * isoCode function.
     *
     * @access private
     * @param bool $domain (default: false)
     * @return void
     */
    private function isoCode($domain = false)
    {
        if (version_compare(_PS_VERSION_, '1.5.0.0', '<')) {
            global $cookie;
            $language = new Language($cookie->id_lang);
            $iso = $language->iso_code;
        } else {
            $iso = $this->context->language->iso_code;
        }

        if ($iso == 'fr') {
            return 'fr';
        } else if ($domain) {
                return 'com';
            } else {
            return 'en';
        }
    }

    /**
     * radioButton function.
     *
     * @access private
     * @param mixed $sauv
     * @param mixed $ok
     * @param mixed $ko
     * @return void
     */
    private function radioButton($sauv, $ok, $ko)
    {
        $result = Configuration::get($sauv);
        if (!isset($result)) {
            $result = 0;
        }

        if (version_compare(_PS_VERSION_, '1.6.0.0', '<')) {
            return '
    			<input type="radio" name="'.$sauv.'" id="'.$sauv.'_on" value="1" '.($result ? 'checked="checked" ' : '').'/>
    			<label class="t" for="'.$sauv.'_on"> <img src="../img/admin/enabled.gif" alt="'.$ok.'" title="'.$ok.'" /></label>
    			<input type="radio" name="'.$sauv.'" id="'.$sauv.'_off" value="0" '.(!$result ? 'checked="checked" ' : '').'/>
    			<label class="t" for="'.$sauv.'_off"> <img src="../img/admin/disabled.gif" alt="'.$ko.'" title="'.$ko.'" /></label>
    		';
        } else {
            return '
                <span class="switch prestashop-switch fixed-width-lg">
        			<input type="radio" name="'.$sauv.'" id="'.$sauv.'_on" value="1" '.($result ? 'checked="checked" ' : '').'/>
        			<label class="t" for="'.$sauv.'_on">'.$ok.'</label>
        			<input type="radio" name="'.$sauv.'" id="'.$sauv.'_off" value="0" '.(!$result ? 'checked="checked" ' : '').'/>
        			<label class="t" for="'.$sauv.'_off">'.$ko.'</label>
        			<a class="slide-button btn"></a>
                </span>
    		';
        }
    }
}
