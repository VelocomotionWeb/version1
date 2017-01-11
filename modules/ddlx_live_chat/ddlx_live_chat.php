<?php
@session_start();
if ( ! defined('_PS_VERSION_') )
	exit();
class ddlx_live_chat extends Module
{

	public $path, $id_shop;

	static $table_message = 'ddlx_live_chat_message';

	static $table_message_status = 'ddlx_live_chat_message_status';

	static $table_merchant_status = 'ddlx_live_chat_merchant_status';

	static $table_client_connected = 'ddlx_live_chat_client_connected';

	static $table_client = 'ddlx_live_chat_client';

	public function __construct()
	{
		$this->name = 'ddlx_live_chat';
		$this->tab = 'others';
		$this->version = '1.0';
		$this->author = 'DDLX Multimédia';
		$this->need_instance = 0;
		
		parent::__construct();
		
		$this->displayName = $this->l('DDLX live chat');
		$this->description = $this->l('This module allows you to chat with client connected to your store.');
		$this->id_shop = $this->context->shop->id;
		$path = $this->_path;
	}

	public function install()
	{
		if ( ! parent::install() || ! $this->registerHook('header') )
		{
			return false;
		}
		
		$install_OK = $this->createDBTables();
		
		if ( ! $install_OK )
		{
			$install_OK &= $this->uninstall();
		}
		
		return $install_OK;
	}

	/**
	 * Méthode créant les tables nécessaires au module.
	 *
	 * @return boolean
	 */
	private function createDBTables()
	{
		$res = Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . ddlx_live_chat::$table_message . '` (
			`id_message` int(10) unsigned NOT NULL auto_increment,
			`id_shop` int(10) unsigned NOT NULL,
			`from_id_customer` int(10) unsigned,
			`from_id_session` VARCHAR(64),
			`from_id_employee` int(10) unsigned,
			`to_id_customer` int(10) unsigned,
			`to_id_session` VARCHAR(64),
			`to_id_employee` int(10) unsigned,
			`message` TEXT,
			
			`date` datetime NOT NULL,								
			PRIMARY KEY (`id_message`)
			)
			ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8');
		
		$res2 = Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . ddlx_live_chat::$table_merchant_status . '` (
			`id_employee` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`id_shop` int(10) unsigned NOT NULL,
			`status` VARCHAR(128) NOT NULL,
			`date` datetime NOT NULL,
				
			PRIMARY KEY(`id_employee`),		
			FOREIGN KEY(`id_employee`) REFERENCES `' . _DB_PREFIX_ . 'employee`(`id_employee`)
			)
			ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8');
		
		$res3 = Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . ddlx_live_chat::$table_message_status . '` (
			`id_live_chat_message_read` int(10) unsigned NOT NULL auto_increment,
			`id_message` int(10) unsigned NOT NULL,
			`read_by_id_employee` int(10) unsigned,
			`read_by_id_customer` int(10) unsigned,
			`read_by_id_session` int(10) unsigned,
			`date` datetime NOT NULL,
				
			PRIMARY KEY (`id_live_chat_message_read`),
			FOREIGN KEY(`id_message`) REFERENCES `' . _DB_PREFIX_ . ddlx_live_chat::$table_message . '`(`id_message`)
				ON DELETE CASCADE
			)
			ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8');
		
		$res4 = Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . ddlx_live_chat::$table_client_connected . '` (
			`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`ip_adress` VARCHAR(64),
			`id_shop` int(10) unsigned,	
			`id_customer` int(10) unsigned UNIQUE,
			`id_session` VARCHAR(64) UNIQUE,
			`name` VARCHAR(128),
			`date` datetime,
			PRIMARY KEY (`id`))
			ENGINE=MEMORY DEFAULT CHARSET=utf8');
		
		$res5 = Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . ddlx_live_chat::$table_client . '` (
			`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`ip_adress` VARCHAR(64),
			`id_shop` int(10) unsigned,
			`id_customer` int(10) unsigned UNIQUE,
			`id_session` VARCHAR(64) UNIQUE,
			`name` VARCHAR(128),
			`browser` VARCHAR(128),
			`comment` VARCHAR(1024),
			`banned` BOOLEAN,
			`date` datetime,
			PRIMARY KEY (`id`))
			ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8');
		
		$res &= $res2;
		$res &= $res3;
		$res &= $res4;
		$res &= $res5;
		
		return $res;
	}

	
	/**
	 * supprime les tables
	 *
	 * @return boolean
	 */
	public function uninstall()
	{
		$result = Db::getInstance()->execute('DROP TABLE IF EXISTS ' . _DB_PREFIX_ . ddlx_live_chat::$table_message_status . ',
											 ' . _DB_PREFIX_ . ddlx_live_chat::$table_message . ',
											 ' . _DB_PREFIX_ . ddlx_live_chat::$table_client_connected . ',
											 ' . _DB_PREFIX_ . ddlx_live_chat::$table_merchant_status . ',
											 ' . _DB_PREFIX_ . ddlx_live_chat::$table_client);
		
		if ( ! parent::uninstall() || ! $result )
		{
			return false;
		}
		
		return true;
	}

	
	/**
	 * Where the magic happens
	 *
	 * @return string
	 */
	public function getContent()
	{
		global $cookie;
		$id_employee = (int) $cookie->id_employee;
		
		// sauvegarde online en BD, puisque connection!
		$d = new DateTime("now");
		$dt = $d->format("Y-m-d H:i:s");
		
		// insère en bd la première fois. Ou update les fois suivantes
		$check_empty = Db::getInstance()->getRow('SELECT status, date
													FROM ' . _DB_PREFIX_ . ddlx_live_chat::$table_merchant_status . '
													WHERE id_employee = ' . $id_employee);
		
		if ( empty($check_empty) )
		{
			Db::getInstance()->insert(ddlx_live_chat::$table_merchant_status, array (
					'status' => "online",
					'id_shop' => $this->id_shop,
					'date' => $dt,
					'id_employee' => $id_employee 
			));
		}
		else
		{
			$res = Db::getInstance()->update(ddlx_live_chat::$table_merchant_status, array (
					'status' => "online",
					'id_shop' => $this->id_shop,
					'date' => $dt 
			), 0, 0, 0);
		}
		
		// ajout javascript
		
		$this->context->smarty->assign(array (
				'modulepath' => $this->_path 
		));
		
		$this->_html = $this->displayChat();
		
		$this->_html .= $this->displayControlInfo();
		
		$this->_html .= $this->display(__FILE__, 'views/templates/admin/merchant-chat-js.tpl');
		
		$this->context->controller->addJS(( $this->_path ) . "/js/ion.sound.js");
		$this->context->controller->addJqueryUI('ui.dialog');
		
		return $this->_html;
	}

	/**
	 * ajout interface onglet visiteur
	 */
	private function displayChat()
	{
		$onglet_actif = Tools::getValue("onglet", "onglet_1");
		
		$this->context->smarty->assign(array (
				'onglet_actif' => $onglet_actif 
		));
		
		$html = '
    <link rel="Stylesheet" type="text/css" href="' . $this->_path . 'ddlxadmin.css">
	<br />

	
	
			
	<div class="annonce">
	<a href="http://evolution-x.fr" target="_blank"><img src="http://www.evolution-x.fr/wp-content/uploads/2014/07/logox300.png"/>
	<div class="evox1">
	' . $this->l('The best template générator for Prestashop 1.6 ! ') . '
					
						</div>
						<div >
						<a class="evox1b" href="http://sitetest.evolution-x.fr/admin2test/" target="_blank">	' . $this->l('Discover the Evolution X ') . ' </a>

						<a class="evox1c" href="http://sitetest.evolution-x.fr/admin2test/" target="_blank">	' . $this->l('Test the Evolution X') . ' </a>
						<a class="evox1a" href="http://www.ddlx.org/les-forums/" target="_blank">	' . $this->l('Forum DDLX') . ' </a>
						<a class="evox1a" href="http://www.ddlx.org/creation-site-e-commerce-prestashop/" target="_blank">	' . $this->l('Prestashop pro hosting') . ' </a>
						</div>
						</div>
						
						
	<h3 style="text-align:center;">' . $this->l('DDLX Live Chat !') . '</h3><br />

	<h4>' . $this->l('Module developped by DDLX Multimédia, web agency & web hosting.') . '</h4>
			
	<div class="systeme_onglets">
        <div class="onglets">
            <span class="onglet tab_inactif" id="onglet_1">' . $this->l('Live users') . '</span>
        
            <span class="onglet tab_inactif" id="onglet_2">' . $this->l('Banned IP') . '</span>
            <!--
            <span class="onglet tab_inactif" id="onglet_3"> . this->l("Search"") . </span> 
            -->
        </div>
		
        <div class="contenu_onglets">
		
            <div class="contenu_onglet" id="cont_onglet_1">
            	' . $this->displayUsers() . '
            </div>
		
		 
			<div class="contenu_onglet" id="cont_onglet_2">
           		' . $this->displayBannedIP() . '
            </div>
		<!--	
            <div class="contenu_onglet" id="cont_onglet_3">
            	" . $this->displaySearch() . "
            </div>
 		-->
        </div>
    </div>
		
    <script type="text/javascript">
    $( ".tab_inactif" ).click(function()
      {
         $(".onglet").removeClass( "tab_actif" ).addClass( "tab_inactif" );
         $(".contenu_onglet").hide();
         $( "#cont_" + $(this).attr("id") ).show();
         $(this).removeClass( "tab_inactif" ).addClass( "tab_actif" );
      }
	 );';
		
		$html .= '
     $( document ).ready(function() {
    	$("#cont_' . $onglet_actif . '").show();
        $("#' . $onglet_actif . '").removeClass( "tab_inactif" ).addClass( "tab_actif" );
		
        $("#cont_onglet_8").show();
        $("#cont_onglet_8").hide();
	});
      </script>';
		
		return $html;
	}

	private function displayControlInfo()
	{
		// token initial
		$_SESSION ["ddlxtokenstatus"] = rand(10000, 9999999);
		$content = '<span id="status_token" style="display:none;">' . $_SESSION ["ddlxtokenstatus"] . '</span>';
		
		return $content;
	}

	private function displayBannedIP()
	{
		$banned_ips = Db::getInstance()->executeS('SELECT * FROM ' . _DB_PREFIX_ . ddlx_live_chat::$table_client . '
												 WHERE banned = true;');
		
		$this->context->smarty->assign(array (
				'banned_ips' => $banned_ips 
		));
		
		return $this->display(__FILE__, 'views/templates/admin/bannedip.tpl', $this->getCacheId());
	}

	private function displayUsers()
	{
		if ( ! isset($_SESSION ['chatID']) )
		{
			$_SESSION ['chatID'] = rand(1000, 1000000);
		}
		
		return $this->display(__FILE__, 'views/templates/admin/live-users.tpl', $this->getCacheId());
	}

	private function displayHistory()
	{
	}

	private function displaySearch()
	{
	}

	/**
	 * Le template contient le lien vers la page de chat.
	 * Ouverture nouvelle fenêtre de chat.
	 *
	 * @param unknown $params        	
	 */
	public function hookHeader( $params )
	{
		return $this->display(__FILE__, 'ddlx_live_chat.tpl', $this->getCacheId());
	}

}
