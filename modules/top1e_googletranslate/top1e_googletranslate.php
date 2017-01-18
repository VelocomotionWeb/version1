<?php
/**
* 2007-2014 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2014 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class Top1e_GoogleTranslate extends Module
{
public function __construct()
{
		$this->name = 'top1e_googletranslate';
		$this->tab = 'others';
		$this->version = '1.0';
		$this->author = 'Themesvip';
		$this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->l('Google Translate - Themesvip.com');
		$this->description = $this->l('Add google translate on your site');
		$this->confirmUninstall = $this->l('Are you sure that you want to delete your Google Translate?');
}

public function install()
{
		if (!parent::install() || !$this->registerHook('displayHeader'))
			return false;
		return true;
}

public function uninstall()
{
		if (!parent::uninstall())
			return false;		
	return true;	
}

private function _displayHelp()
{
		$this->_html .= '
		<br/>
	 	<fieldset>
			<legend><img src="'.$this->_path.'img/help.png" alt="" title="" /> '.$this->l('Help').'</legend>		
			For customizations or assistance, please contact: <strong>contact@themesvip.com</strong>
			<br>
			<a href="http://themesvip.com/" alt="top1extensions" title="top1extensions">http://themesvip.com/</a>
		</fieldset>';
}

private function _displayAboutUs()
{
		$this->_html .= '
		<br/>
	 	<fieldset>
			<legend><img src="'.$this->_path.'img/aboutus.png" alt="" title="" /> '.$this->l('About themesvip').'</legend>		
            <p>
			We are a teams of seasoned web professionals with many years of hands-on experience  and alway use new technology, so we will bring best solutions for your site.
			</p>
		</fieldset>';
}

public function _displayAdvertising()
{
		$this->_html .= '
		<br/>
		<fieldset>
			<legend><img src="'.$this->_path.'img/more.png" alt="" title="" /> '.$this->l('More Themes & Modules').'</legend>	
			<iframe src="http://documentation.themesvip.com/advertising/prestashop_advertising.html" width="100%" height="420px;" border="0" style="border:none;"></iframe>
			</fieldset>';
}

public function getContent()
{
		$this->_html = '<h2><img src="'.$this->_path.'logo.png" alt="" /> '.$this->displayName.'</h2>';	
		$this->_displayHelp();
		$this->_displayAboutUs();
		$this->_displayAdvertising();
		return $this->_html;
}

public function hookdisplayHeader($params)
{
		$id_lang = (int)$this->context->language->id;
		$this->context->smarty->assign(array(
						'current_language_code' => $id_lang
					));
		$this->context->controller->addCss($this->_path.'css/top1e_googletranslate.css');
		return $this->display(__FILE__, 'views/templates/hook/top1e_googletranslate.tpl');
		
}

public function hookdisplayTop($params)
{
		$id_lang = (int)$this->context->language->id;
		$this->context->smarty->assign(array(
						'current_language_code' => $id_lang
					));
		return $this->display(__FILE__, 'views/templates/hook/top1e_googletranslate.tpl');
}

public function hookdisplayNav($params)
{
		$id_lang = (int)$this->context->language->id;
		$this->context->smarty->assign(array(
						'current_language_code' => $id_lang
					));
		return $this->display(__FILE__, 'views/templates/hook/top1e_googletranslate.tpl');
}
}
?>



