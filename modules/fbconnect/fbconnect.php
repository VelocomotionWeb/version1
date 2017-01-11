<?php
/*
*  @author  dSchoorens
*  @copyright  2015 PrestaMod
*/

if (!defined('_PS_VERSION_'))
	exit;

require 'src/facebook.php';

class FbConnect extends Module
{
	/* @var boolean error */
	protected $error = false;
	
	public function __construct()
	{
		$this->name = 'fbconnect';
		$this->tab = 'front_office_features';
		$this->version = '1.0';
		$this->author = 'PrestaMod';
		$this->need_instance = 0;

	 	parent::__construct();

		$this->displayName = $this->l('Connect with Facebook');
		$this->description = $this->l('Add the ability to connect with a Facebook account.');
		$this->confirmUninstall = $this->l('Are you sure you want to uninstall the module ?');
	}
	
	public function install()
	{
		if (!parent::install() ||
			!$this->registerHook('rightColumn') ||
			!Configuration::updateValue('PS_FBCONNECT_TITLE', 'Connexion avec Facebook') ||
			!Configuration::updateValue('PS_FBCONNECT_APPID', '') ||
			!Configuration::updateValue('PS_FBCONNECT_SECRET', ''))
			return false;
			$this->installDB();
		return true;
	}

	function installDB()
	{
		global $cookie;
		Db::getInstance()->Execute('
		CREATE TABLE IF NOT EXISTS  `'._DB_PREFIX_.'fbconnect` (
			`id_fbconnect` INT NOT NULL AUTO_INCREMENT,
			`id_facebook` varchar(32) NOT NULL,
			`gender` varchar(24) NOT NULL,
			`firstname` varchar(32) NOT NULL,
			`lastname` varchar(32) NOT NULL,
			`email` varchar(128) NOT NULL,
			`date_add` DATETIME NOT NULL,
			`date_upd` DATETIME NOT NULL,
			PRIMARY KEY (`id_fbconnect`),
			INDEX `id_facebook` (`id_facebook`)
		);');
	}
	
	public function uninstall()
	{
		if (!parent::uninstall() ||
			!Configuration::deleteByName('PS_FBCONNECT_TITLE') ||
			!Configuration::deleteByName('PS_FBCONNECT_APPID') ||
			!Configuration::deleteByName('PS_FBCONNECT_SECRET'))
			return false;
			Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'fbconnect`');
		return true;
	}

	private function fbconnect($logout_url,$login_url)
	{
		$fb_connect_appid = Configuration::get('PS_FBCONNECT_APPID');
		$fb_connect_secret = Configuration::get('PS_FBCONNECT_SECRET');
		if ($fb_connect_appid=='' or $fb_connect_secret=='') {
			return false;
		} else {
			$facebook = new Facebook(array(
			  'appId'  => $fb_connect_appid,
			  'secret' => $fb_connect_secret,
			));
			$facebook_user = $facebook->getUser();
			if ($facebook_user) {
			  try {
				$facebook_user_profile = $facebook->api('/me');
			  } catch (FacebookApiException $e) {
				error_log($e);
				$facebook_user = null;
				return false;
			  }
			}
			if ($facebook_user) {
				if (isset($facebook_user_profile['email'])) {
					$result= array (
					'facebook_email' =>  $facebook_user_profile['email'],
					'facebook_firstname' =>  $facebook_user_profile['first_name'],
					'facebook_lastname' =>  $facebook_user_profile['last_name'],
					'facebook_gender' =>  $facebook_user_profile['gender'],
					'facebook_logoutUrl' =>  $facebook->getLogoutUrl(array(),$logout_url),
					'facebook_loginUrl' =>  ''
					);
					return $result;
				} else {
					$result= array (
					'facebook_email' =>  '',
					'facebook_firstname' =>  '',
					'facebook_lastname' =>  '',
					'facebook_gender' =>  '',
					'facebook_logoutUrl' =>  '',
					'facebook_loginUrl' =>  $facebook->getLoginUrl(array('scope' => 'email','auth_type'=>'rerequest'),$login_url)
					);
					return $result;
				}
			} else {
				$result= array (
				'facebook_email' =>  '',
				'facebook_firstname' =>  '',
				'facebook_lastname' =>  '',
				'facebook_gender' =>  '',
				'facebook_logoutUrl' =>  '',
				'facebook_loginUrl' =>  $facebook_loginUrl = $facebook->getLoginUrl(array('scope' => 'email'),$login_url)
				);
				return $result;
			}
		}
	}
	
	public function hookLeftColumn($params)
	{
		$logout_url= $this->context->smarty->tpl_vars['base_dir']->value .'modules/fbconnect/logout.php';
		$login_url= $this->context->smarty->tpl_vars['base_dir']->value .'modules/fbconnect/login.php';
		$fbconnect_result=$this->fbconnect($logout_url,$login_url);
		if ($fbconnect_result!=false) {
			if ($fbconnect_result['facebook_loginUrl']<>'' and !$this->context->customer->isLogged()) {
				$fbconnect_link=$fbconnect_result['facebook_loginUrl'];
				$fbconnect_logout='';
				$fbconnect_email='';
				$fbconnect_firstname='';
				$fbconnect_lastname='';
				$fbconnect_gender='';
				$fbconnect_image='facebook_connect_fr.png';
			} elseif ($fbconnect_result['facebook_loginUrl']<>'' and $this->context->customer->isLogged()) {
				$fbconnect_link='';
				$fbconnect_logout='';
				$fbconnect_email='';
				$fbconnect_firstname='';
				$fbconnect_lastname='';
				$fbconnect_gender='';
				$fbconnect_image='';
			} else {
				$fbconnect_link='';
				$fbconnect_logout=$fbconnect_result['facebook_logoutUrl'];
				$fbconnect_email=$fbconnect_result['facebook_email'];
				$fbconnect_firstname=$fbconnect_result['facebook_firstname'];
				$fbconnect_lastname=$fbconnect_result['facebook_lastname'];
				$fbconnect_gender=$fbconnect_result['facebook_gender'];
				$fbconnect_image='facebook_logout_fr.png';
			}

			$this->smarty->assign(array(
				'fbconnect_title' => Configuration::get('PS_FBCONNECT_TITLE'),
				'fbconnect_title_logout' => 'Déconnexion Facebook',
				'fbconnect_image' => $fbconnect_image,
				'fbconnect_link' => $fbconnect_link,
				'fbconnect_logout' => $fbconnect_logout
			));
				return $this->display(__FILE__, 'fbconnect.tpl');
		}
	}

	public function hookRightColumn($params)
	{
		return $this->hookLeftColumn($params);
	}
	
	
	public function updateParameters()
	{
		if (!Configuration::updateValue('PS_FBCONNECT_TITLE', Tools::getValue('title')) ||
			!Configuration::updateValue('PS_FBCONNECT_APPID', Tools::getValue('appid')) ||
			!Configuration::updateValue('PS_FBCONNECT_SECRET', Tools::getValue('secret')))
			return false;
		return true;
	}

	public function getContent()
	{
		$this->_html = '<h2>'.$this->displayName.'</h2>';

		if (isset($_POST['submitParameters']))
		{

			if (!$this->updateParameters())
				$this->_html .= $this->displayError($this->l('An error occurred during updating.'));
			else
				$this->_html .= $this->displayConfirmation($this->l('No error during updating.'));
		}

		$this->_displayForm();

		return $this->_html;
	}
	
	private function _displayForm()
	{
		$fb_connect_title = Configuration::get('PS_FBCONNECT_TITLE');
		$fb_connect_appid = Configuration::get('PS_FBCONNECT_APPID');
		$fb_connect_secret = Configuration::get('PS_FBCONNECT_SECRET');
		if ($fb_connect_appid=='' or $fb_connect_secret=='') {
			$this->_html .= $this->displayError($this->l('AppID and Secret can\'t be empty.'));
		}
		$this->_html .= '
		<fieldset>
			<legend>'.$this->l('Setup Facebook AppID and AppSecret').'</legend>
			<form method="post" action="index.php?controller=adminmodules&configure='.Tools::safeOutput(Tools::getValue('configure')).'&token='.Tools::safeOutput(Tools::getValue('token')).'&tab_module='.Tools::safeOutput(Tools::getValue('tab_module')).'&module_name='.Tools::safeOutput(Tools::getValue('module_name')).'" enctype="multipart/form-data">';
			$this->_html .= '
				<label>'.$this->l('Bloc title:').'</label>
				<div class="margin-form"><input type="text" name="title" id="title" value="'.$fb_connect_title.'" /><sup> *</sup></div>
				<label>'.$this->l('AppID:').'</label>
				<div class="margin-form"><input type="text" name="appid" id="appid" value="'.$fb_connect_appid.'" /><sup> *</sup></div>
				<label>'.$this->l('AppSecret:').'</label>
				<div class="margin-form"><input type="text" name="secret" id="secret" value="'.$fb_connect_secret.'" /><sup> *</sup></div>
				';
			$this->_html .= '
				<div class="margin-form">
					<input type="submit" class="button" name="submitParameters" value="'.$this->l('Update').'" />
				</div>
			</form>';
	}
}