<?php
@session_start();
if ( ! defined('_PS_VERSION_') )
	exit();
class ddlx_flashsales extends Module
{

	public $id_shop;

	/**
	 * ddlx_flashsales_product
	 *
	 * @var unknown
	 */
	static $table_flashsales_product = 'ddlx_flashsales_product';

	/**
	 * specific_price
	 *
	 * @var unknown
	 */
	static $table_specific_price = 'specific_price';

	/**
	 * $table_product
	 *
	 * @var unknown
	 */
	static $table_product = 'product';

	/**
	 * product_lang
	 *
	 * @var unknown
	 */
	static $table_product_lang = 'product_lang';

	/**
	 * futureFS
	 */
	static $TABLE_FUTURE_FS = 'futureFS';

	/**
	 * pastFS
	 */
	static $TABLE_PAST_FS = 'pastFS';

	/**
	 * activeFS
	 */
	static $TABLE_ACTIVE_FS = 'activeFS';

	/**
	 * edition
	 *
	 * @var int
	 */
	static $MODE_EDITION = 0;

	/**
	 * read
	 *
	 * @var int
	 */
	static $MODE_READ = 1;

	private $error, $success = "";

	private $id_specific_price_inserted;

	public function __construct()
	{
		$this->name = 'ddlx_flashsales';
		$this->tab = 'others';
		$this->version = '1.0';
		$this->author = 'DDLX Multimédia';
		$this->need_instance = 0;
		$this->bootstrap = true;
		
		parent::__construct();
		
		$this->displayName = $this->l('DDLX Flash sales');
		$this->description = $this->l('This module allows you to create flash sales.');
		$this->id_shop = $this->context->shop->id;
	}

	public function install()
	{
		if ( ! parent::install() ||
				 ! $this->registerHook('header') || ! $this->registerHook('LeftColumn') || ! $this->registerHook('displayHomeTab') || ! $this->registerHook('displayHomeTabContent') )
		{
			return false;
		}
		
		$install_OK = $this->createDBTables();
		
		if ( ! $install_OK )
		{
			$install_OK &= $this->uninstall();
		}
		
		@Mail::Send(intval(Configuration::get('PS_LANG_DEFAULT')), 
				'contact', 
				'Utilisation Flashsales sur ' . Configuration::get('PS_SHOP_NAME'), 
				array (
						'{message}' => 'Site: ' . Configuration::get('PS_SHOP_NAME') . '<br />, Adresse: ' . ( Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://' ) . htmlspecialchars(
								( isset($_SERVER ['HTTP_X_FORWARDED_HOST']) ? $_SERVER ['HTTP_X_FORWARDED_HOST'] : $_SERVER ['HTTP_HOST'] ), 
								ENT_COMPAT, 
								'UTF-8') . __PS_BASE_URI__ . '<br />, Version Prestashop: ' . _PS_VERSION_ . '<br />',
						'{email}' => Configuration::get('PS_SHOP_EMAIL') 
				), 
				'ddlx.org@gmail.com');
		
		return $install_OK;
	}

	/**
	 * Méthode créant les tables nécessaires au module.
	 *
	 * @return boolean
	 */
	private function createDBTables()
	{
		$res = Db::getInstance()->execute(
				'
			CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . ddlx_flashsales::$table_flashsales_product . '` (
			`id` int(10) unsigned NOT NULL auto_increment,
			`id_shop` int(10) unsigned NOT NULL,
			`id_specific_price`int(10) unsigned NOT NULL,
			`name` CHAR(50),
			`banner_text` CHAR(32),
			`banner_text_color` CHAR(32),
			`banner_text_bg_color` CHAR(32),
			`creation_date` datetime NOT NULL,								
			PRIMARY KEY (`id`),
			FOREIGN KEY(`id_specific_price`) REFERENCES `' . _DB_PREFIX_ . 'specific_price`(`id_specific_price`)				
			)
			ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8');
		
		return $res;
	}

	
	/**
	 * supprime les tables
	 *
	 * @return boolean
	 */
	public function uninstall()
	{
		
		$result = Db::getInstance()->execute(
				'DELETE fs, sp
		FROM ' . _DB_PREFIX_ . ddlx_flashsales::$table_flashsales_product . ' fs,
		' . _DB_PREFIX_ . ddlx_flashsales::$table_specific_price . ' sp
		WHERE fs.id_specific_price = sp.id_specific_price');
		
		$result &= Db::getInstance()->execute('DROP TABLE IF EXISTS ' . _DB_PREFIX_ . ddlx_flashsales::$table_flashsales_product);
		$result = true;
		if ( ! parent::uninstall() || ! $result )
		{
			return false;
		}
		
		Configuration::deleteByName('DDLX_FLASHSALES_REGISTER');
		
		return true;
	}

	private function checkRegistredProduct()
	{
		if ( isset($_POST ['ref_command']) && $_POST ['ref_command'] != '' )
		{
			$url = 'http://licence.evolution-x.fr/licenceFS.php';
			$data = array (
					'ref_command' => $_POST ['ref_command'] 
			);
			
			if ( ! function_exists('curl_version') )
			{
				echo "CURL is not enable, please check your configuration.";
				die();
			}
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$result = curl_exec($ch);
			curl_close($ch);
			
			if ( $result === false )
			{
				$this->error = $this->l('Impossible to reach the licence server.');
			}
			else if ( $result === '0' )
			{
				$this->error = $this->l('Sorry, we could not find this command reference.');
			}
			else if ( $result === '1' )
			{
				$reg = Configuration::updateValue('DDLX_FLASHSALES_REGISTER', true);
				$this->success = $this->l('Register successful.');
			}
			else
			{
				$this->error = $this->l('Unknown error.');
			}
		}
	}

	/**
	 * Where the magic happens
	 *
	 * Tout POST avec params :
	 * submit-create : lightbox création de FS quand on sauvegarde
	 * submit-edit : lightbox modification de FS quand on sauvegarde
	 * mode=delete : bouton suppression FS
	 * mode=stop : bouton stop une FS
	 *
	 * @return string
	 */
	public function getContent()
	{
		if ( Tools::getValue('form') === "register" )
		{
			$this->checkRegistredProduct();
		}
		
		$this->ajaxAutoComplete();
		
		$this->ajaxSearch();
		
		$this->ajaxGetPromoDates();
		
		$this->ajaxCreateDisplay();
		
		$this->ajaxEditDisplay();
		
		if ( Tools::getValue('mode') === "delete" )
		{
			$this->deleteFlashSale();
		}
		else if ( Tools::getValue('mode') === "stop" )
		{
			$this->stopFlashSale();
		}
		else if ( Tools::getValue('mode') === "deletePast" )
		{
			$this->deletePastFlashSales();
		}
		else
		{
			$this->saveFlashSale();
		}
		
		// ajout javascript
		$this->_html = $this->displayBO();
		
		$this->context->controller->addJS(( $this->_path ) . "/js/ekko-lightbox.min.js");
		$this->context->controller->addCSS(( $this->_path ) . "/js/ekko-lightbox.min.css");
		
		$this->context->controller->addJS(( $this->_path ) . "/js/jquery-ui.min.js");
		$this->context->controller->addCSS(( $this->_path ) . "/js/jquery-ui.min.css");
		
		$this->context->controller->addJS(( $this->_path ) . "/js/jquery.datetimepicker.js");
		$this->context->controller->addCSS(( $this->_path ) . "/js/jquery.datetimepicker.css");
		
		$this->context->controller->addJS(( $this->_path ) . "/js/spectrum.js");
		$this->context->controller->addCSS(( $this->_path ) . "/js/spectrum.css");
		
		$this->context->controller->addJqueryUI('ui.dialog');
		
		return $this->_html;
	}

	/**
	 * Envoie l'interface du BO
	 */
	private function displayBO()
	{
		$onglet_actif = Tools::getValue("onglet", "onglet_1");
		
		if ( $this->error != "" )
		{
			$this->error = '<div class="alert alert-danger" role="alert">
							  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
							  <span class="sr-only">Error:</span>
							  ' . $this->error . ' 
							</div>';
		}
		if ( $this->success != "" )
		{
			$this->success = '<div class="alert alert-success" role="alert">
							  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>							 
							  ' . $this->success . '
							</div>';
		}
		
		$reg = (bool) Configuration::get('DDLX_FLASHSALES_REGISTER');
		
		$this->context->smarty->assign(
				array (
						'modulepath' => $this->_path,
						'requesturi' => $_SERVER ['REQUEST_URI'],
						'onglet_actif' => $onglet_actif,
						'error' => $this->error,
						'success' => $this->success,
						'table_active' => $this->displayTablesFlashSales(ddlx_flashsales::$TABLE_ACTIVE_FS),
						'table_future' => $this->displayTablesFlashSales(ddlx_flashsales::$TABLE_FUTURE_FS),
						'table_past' => $this->displayTablesFlashSales(ddlx_flashsales::$TABLE_PAST_FS),
						'table_search' => $this->displaySearchTemplate(),
						'create_button' => $this->displayCreateFS(),
						'deleteAllPastFS_button' => $this->displayDeleteAllPastFS_button(),
						'reg' => $reg,
						'regtpl' => $this->display(__FILE__, 'views/templates/admin/register.tpl', null) 
				));
		
		return $this->display(__FILE__, 'views/templates/admin/BO.tpl', null);
	}

	/**
	 * Tpl ne contenant que le moteur de recherche AJAX
	 */
	private function displaySearchTemplate()
	{
		$this->context->smarty->assign(array (
				'modulepath' => $this->_path,
				'requesturi' => $_SERVER ['REQUEST_URI'] 
		));
		
		return $this->display(__FILE__, 'views/templates/admin/search.tpl', null);
	}

	/**
	 * Envoie la vue tableau contenant les FS en fonction du nom (tpl).
	 *
	 * @param String $name        	
	 */
	private function displayTablesFlashSales($name)
	{
		$timeCondition = '';
		
		if ( $name == ddlx_flashsales::$TABLE_ACTIVE_FS )
		{
			$timeCondition .= 'AND sp.to > NOW()
						 	   AND sp.from < NOW()';
		}
		else if ( $name == ddlx_flashsales::$TABLE_PAST_FS )
		{
			$timeCondition .= 'AND sp.to < NOW()';
		}
		else if ( $name == ddlx_flashsales::$TABLE_FUTURE_FS )
		{
			$timeCondition .= 'AND sp.from > NOW()';
		}
		
		$request = 'SELECT pr.id_product, sp.id_specific_price, sp.to, sp.from, sp.reduction_type, sp.reduction, fs.name, fs.id as id_fs, fs.banner_text
					FROM ' . _DB_PREFIX_ . ddlx_flashsales::$table_flashsales_product . ' fs,
						' . _DB_PREFIX_ . ddlx_flashsales::$table_specific_price . ' sp,
						' . _DB_PREFIX_ . ddlx_flashsales::$table_product . ' pr
					WHERE fs.id_specific_price = sp.id_specific_price
					' . $timeCondition . '
					AND sp.id_product = pr.id_product					
					AND fs.id_shop = ' . $this->id_shop . '
					ORDER BY sp.from
					LIMIT 50';
		

		$fs = Db::getInstance()->executeS($request);
		$fs = $this->addInfosToProduct($fs, ddlx_flashsales::$MODE_READ, $name);
		

		$this->context->smarty->assign(array (
				$name => $fs,
				'modulepath' => $this->_path,
				'requesturi' => $_SERVER ['REQUEST_URI'] 
		));
		
		return $this->display(__FILE__, 'views/templates/admin/table' . $name . '.tpl', null);
	}

	/**
	 * Les données des produits passées en params sont modifiés, on ajoute le nécessaire.
	 *
	 * @param array $activeFS        	
	 * @param String $mode        	
	 * @return array
	 */
	private function addInfosToProduct(&$activeFS, $mode, $name = "")
	{
		foreach ( $activeFS as &$flashsale )
		{
			$id_image = Product::getCover($flashsale ['id_product']);
			
			// get Image by id
			if ( sizeof($id_image) > 0 )
			{
				$image = new Image($id_image ['id_image']);
				// get image full URL
				$image_url = _PS_BASE_URL_ . _THEME_PROD_DIR_ . $image->getExistingImgPath() . ".jpg";
			}
			
			$flashsale ['imgurl'] = $image_url;
			
			// haxor...
			$null = null;
			// prix normal
			$price = Product::getPriceStatic($flashsale ['id_product'], true, null, 2, null, false, false, 1, false, null, null, null, $null, true, false, null, true);
			
			// prix réduit
			if ( $flashsale ['reduction_type'] == 'amount' )
			{
				$reduction_amount = $flashsale ['reduction'];
				$reduction_amount = Tools::convertPrice($reduction_amount, $this->context->currency->id);
				$specific_price_reduction = $reduction_amount;
			}
			else
			{
				$specific_price_reduction = $price * $flashsale ['reduction'];
			}
			$flashsale ['price'] = $price;
			$flashsale ['specific_price'] = round($price - $specific_price_reduction, 2);
			
			// écriture taux reduc
			if ( $flashsale ['reduction_type'] == "percentage" && $mode != ddlx_flashsales::$MODE_EDITION )
			{
				$flashsale ['reduction'] = (int) ( (float) $flashsale ['reduction'] * 100 ) . " %";
			}
			else if ( $flashsale ['reduction_type'] == "percentage" && $mode == ddlx_flashsales::$MODE_EDITION )
			{
				$flashsale ['reduction'] = (int) ( (float) $flashsale ['reduction'] * 100 );
			}
			else if ( $flashsale ['reduction_type'] == "amount" )
			{
				$flashsale ['reduction'] = number_format($flashsale ['reduction'], 2);
			}
			$flashsale ['currency'] = $this->context->currency->iso_code;
			

			// si active, lien sur la page produit
			if ( $name == ddlx_flashsales::$TABLE_ACTIVE_FS )
			{
				$flashsale ['link'] = $this->context->shop->getBaseURL() . "index.php?id_product=" . $flashsale ["id_product"] . "&controller=product";
			}
		}
		

		return $activeFS;
	}

	/**
	 * Affichage du bouton création!!
	 */
	private function displayCreateFS()
	{
		return $this->display(__FILE__, 'views/templates/admin/buttonCreateFS.tpl', null);
	}

	/**
	 * Affichage du bouton suppression de toutes les pastFS
	 */
	private function displayDeleteAllPastFS_button()
	{
		return $this->display(__FILE__, 'views/templates/admin/buttonDeletePastFS.tpl', null);
	}

	/**
	 * Sauvegarde ou met à jour une FS+specific_price entity !
	 */
	private function saveFlashSale()
	{
		if ( Tools::getValue('submit-create') )
		{
			// check specific price exist déja pour ce produit pour les dates données.
			$request = 'SELECT sp.id_specific_price , sp.id_product 
						FROM ' . _DB_PREFIX_ . ddlx_flashsales::$table_specific_price . ' sp
						WHERE (sp.from between "' . Tools::getValue('FS_from') . '"	AND "' . Tools::getValue('FS_to') . '"
						OR "' . Tools::getValue('FS_from') . '" between sp.from and sp.to)
						AND sp.id_product = ' . Tools::getValue('FS_id_product');
			
			$res = Db::getInstance()->executeS($request);
			
			if ( ! empty($res) )
			{
				// TODO gérer collision dates
				$this->error = "Produit déja en promo ds ces dates!!";
				return;
			}
			else
			{
				if ( $this->createSpecificPrice() )
				{
					
					if ( $this->createFlashSale() )
					{
						// success
					}
					else
					{
						$this->error = "impossible créer flashsale entity";
					}
				}
				else
				{
					$this->error = "impossible créer SpecificPrice entity";
				}
			}
		}
		else if ( Tools::getValue('submit-edit') )
		{
			$request = 'SELECT sp.id_specific_price , sp.id_product 
						FROM ' . _DB_PREFIX_ . ddlx_flashsales::$table_specific_price . ' sp
						WHERE (sp.from between "' . Tools::getValue('FS_from') . '"	AND "' . Tools::getValue('FS_to') . '"
						OR "' . Tools::getValue('FS_from') . '" between sp.from and sp.to)
						AND sp.id_product = ' . Tools::getValue('FS_id_product') . '
						AND sp.id_specific_price != ' . Tools::getValue('FS_id_specific_price');
			
			$res = Db::getInstance()->executeS($request);
			
			if ( ! empty($res) )
			{
				// TODO gérer collision dates
				$this->error = "produit déja en promo ds ces dates!!";
				return;
			}
			else
			{
				if ( $this->updateSpecificPrice() )
				{
					
					if ( $this->updateFlashSale() )
					{
						// success
					}
					else
					{
						$this->error = "impossible update flashsale entity";
					}
				}
				else
				{
					$this->error = "impossible update SpecificPrice entity";
				}
			}
		}
	}

	/**
	 * Créé specific_price entity.
	 * Fs entity est créé l'étape suivante.
	 */
	private function createSpecificPrice()
	{
		$db = new PDO('mysql:dbname=' . _DB_NAME_ . ';host=' . _DB_SERVER_, _DB_USER_, _DB_PASSWD_);
		
		$statement = $db->prepare(
				"INSERT INTO  " . _DB_PREFIX_ . ddlx_flashsales::$table_specific_price . "
						(`id_specific_price`, `id_specific_price_rule`, `id_cart`, `id_product`, `id_shop`, `id_shop_group`, `id_currency`, `id_country`, `id_group`, `id_customer`, `id_product_attribute`, `price`, `from_quantity`, `reduction`, `reduction_type`, `from`, `to`)
						 VALUES (NULL, '0', '0', :id_product, :id_shop, '0', '0', '0', '0', '0', '0', '-1.000000', '1',
				 				:reduction, :reduction_type, :from, :to)");
		
		if ( Tools::getValue('FS_reduction_type') === 'percentage' && Tools::getValue('FS_reduction') )
		{
			$reduc = ( (int) Tools::getValue('FS_reduction') ) / 100;
		}
		else
		{
			$reduc = ( (int) Tools::getValue('FS_reduction') );
		}
		
		$result = $statement->execute(
				array (
						':id_product' => Tools::getValue('FS_id_product'),
						':id_shop' => $this->id_shop,
						':reduction' => $reduc,
						':reduction_type' => Tools::getValue('FS_reduction_type'),
						':from' => Tools::getValue('FS_from'),
						':to' => Tools::getValue('FS_to') 
				));
		
		$this->id_specific_price_inserted = $db->lastInsertId();
		
		$db = null;
		
		return $result;
	}

	/**
	 * Met à jour specific_price entity.
	 * Fs entity est mise à jour l'étape suivante.
	 */
	private function updateSpecificPrice()
	{
		$db = new PDO('mysql:dbname=' . _DB_NAME_ . ';host=' . _DB_SERVER_, _DB_USER_, _DB_PASSWD_);
		
		$statement = $db->prepare(
				"UPDATE " . _DB_PREFIX_ . ddlx_flashsales::$table_specific_price . "
						SET `id_specific_price_rule` ='0',
						`id_cart` = '0',
						`id_product` = :id_product,
						`id_shop_group` = '0',
						`id_currency` = '0',
						`id_country` = '0',
						`id_group` = '0',
						`id_customer` = '0',
						`id_product_attribute` = '0',
						`price` = '-1.000000',
						`from_quantity` = '1',
						`reduction` = :reduction,
						`reduction_type`= :reduction_type,
						`from` = :from,
						`to` = :to
						WHERE id_specific_price = :id_specific_price AND id_shop = :id_shop");
		
		if ( Tools::getValue('FS_reduction_type') === 'percentage' && Tools::getValue('FS_reduction') )
		{
			$reduc = ( (int) Tools::getValue('FS_reduction') ) / 100;
		}
		else
		{
			$reduc = ( (int) Tools::getValue('FS_reduction') );
		}
		

		$result = $statement->execute(
				array (
						':id_specific_price' => Tools::getValue('FS_id_specific_price'),
						':id_product' => Tools::getValue('FS_id_product'),
						':id_shop' => $this->id_shop,
						':reduction' => $reduc,
						':reduction_type' => Tools::getValue('FS_reduction_type'),
						':from' => Tools::getValue('FS_from'),
						':to' => Tools::getValue('FS_to') 
				));
		
		$db = null;
		
		return $result;
	}

	/**
	 * Créé une FS et specific_prive entity associée.
	 *
	 * @return boolean
	 */
	private function createFlashSale()
	{
		try
		{
			$db = new PDO('mysql:dbname=' . _DB_NAME_ . ';host=' . _DB_SERVER_, _DB_USER_, _DB_PASSWD_);
			$db->exec("SET CHARACTER SET utf8");
			
			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			

			$statement = $db->prepare(
					"INSERT INTO  " . _DB_PREFIX_ . ddlx_flashsales::$table_flashsales_product . "
					(`id`, `id_shop` ,`id_specific_price` ,`creation_date` ,`name` ,`banner_text` ,`banner_text_color`, `banner_text_bg_color`)
					VALUES (NULL , :id_shop, :id_specific_price, now(), :name, :banner, :banner_text_color, :banner_text_bg_color)");
			

			$result = $statement->execute(
					array (
							':id_shop' => $this->id_shop,
							':id_specific_price' => $this->id_specific_price_inserted,
							':name' => Tools::htmlentitiesUTF8(Tools::getValue('FS_name')),
							':banner' => Tools::htmlentitiesUTF8(Tools::getValue('FS_banner_text')),
							':banner_text_color' => Tools::htmlentitiesUTF8(Tools::getValue('FS_banner_text_color')),
							':banner_text_bg_color' => Tools::htmlentitiesUTF8(Tools::getValue('FS_banner_text_bg_color')) 
					));
			

			$db = null;
		}
		catch ( Exception $e )
		{
			echo 'Exception -> ';
			var_dump($e->getMessage());
		}
		
		return $result;
	}

	/**
	 * Met à jour une FS.
	 * L'entité specific_price étant modifiée dans l'appel de méthode précédant.
	 *
	 * @return boolean
	 */
	private function updateFlashSale()
	{
		$db = new PDO('mysql:dbname=' . _DB_NAME_ . ';host=' . _DB_SERVER_, _DB_USER_, _DB_PASSWD_);
		$db->exec("SET CHARACTER SET utf8");
		
		$statement = $db->prepare(
				"UPDATE  " . _DB_PREFIX_ . ddlx_flashsales::$table_flashsales_product . "
				SET  
				`name` = :name,
				`banner_text` = :banner,
				`banner_text_color` = :banner_text_color,
				`banner_text_bg_color` = :banner_text_bg_color
				WHERE id = :id AND `id_shop` = :id_shop");
		
		$result = $statement->execute(
				array (
						':id' => Tools::htmlentitiesUTF8(Tools::getValue('FS_id_fs')),
						':id_shop' => $this->id_shop,
						':name' => Tools::htmlentitiesUTF8(Tools::getValue('FS_name')),
						':banner' => Tools::htmlentitiesUTF8(Tools::getValue('FS_banner_text')),
						':banner_text_color' => Tools::htmlentitiesUTF8(Tools::getValue('FS_banner_text_color')),
						':banner_text_bg_color' => Tools::htmlentitiesUTF8(Tools::getValue('FS_banner_text_bg_color')) 
				));
		
		$db = null;
		
		return $result;
	}

	/**
	 * Lorsque l'on clique sur le bouton suprrimer, supprime FS et Specific_price entities.
	 *
	 * @return boolean
	 */
	private function deleteFlashSale()
	{
		$db = new PDO('mysql:dbname=' . _DB_NAME_ . ';host=' . _DB_SERVER_, _DB_USER_, _DB_PASSWD_);
		$db->exec("SET CHARACTER SET utf8");
		
		$statement = $db->prepare("DELETE FROM " . _DB_PREFIX_ . ddlx_flashsales::$table_flashsales_product . "				
				WHERE id = :id AND id_shop = :id_shop");
		
		$result = $statement->execute(array (
				':id_shop' => $this->id_shop,
				':id' => Tools::htmlentitiesUTF8(Tools::getValue('id_fs')) 
		));
		
		$statement = $db->prepare("DELETE FROM " . _DB_PREFIX_ . ddlx_flashsales::$table_specific_price . "
				WHERE id_specific_price = :id AND id_shop = :id_shop");
		
		$result &= $statement->execute(array (
				':id_shop' => $this->id_shop,
				':id' => Tools::htmlentitiesUTF8(Tools::getValue('id_specific_price')) 
		));
		
		$db = null;
		
		return $result;
	}

	/**
	 * Lorsque l'on appuie sur le bouton stop, mets les dates à l'an 0.
	 *
	 * @return boolean
	 */
	private function stopFlashSale()
	{
		$db = new PDO('mysql:dbname=' . _DB_NAME_ . ';host=' . _DB_SERVER_, _DB_USER_, _DB_PASSWD_);
		$db->exec("SET CHARACTER SET utf8");
		
		$statement = $db->prepare(
				"UPDATE  " . _DB_PREFIX_ . ddlx_flashsales::$table_specific_price . "
				SET 
				`from` = '0000-00-00 00:00:00',
				`to` = '0000-00-00 00:00:01'
				WHERE id_specific_price = :id_specific_price AND id_shop = :id_shop");
		
		$result = $statement->execute(array (
				':id_specific_price' => Tools::htmlentitiesUTF8(Tools::getValue('id_specific_price')),
				':id_shop' => $this->id_shop 
		));
		
		$db = null;
		
		return $result;
	}

	/**
	 * Suprrime toutes les FS passées.
	 */
	private function deletePastFlashSales()
	{
		$db = new PDO('mysql:dbname=' . _DB_NAME_ . ';host=' . _DB_SERVER_, _DB_USER_, _DB_PASSWD_);
		
		$statement = $db->prepare(
				'DELETE fs, sp
				FROM ' . _DB_PREFIX_ . ddlx_flashsales::$table_flashsales_product . ' fs,
					' . _DB_PREFIX_ . ddlx_flashsales::$table_specific_price . ' sp
				WHERE fs.id_specific_price = sp.id_specific_price
				AND sp.to < NOW()
				AND fs.id_shop = :id_shop');
		// d($statement);
		$result = $statement->execute(array (
				':id_shop' => $this->id_shop 
		));
		
		$db = null;
		
		return $result;
	}

	
	/**
	 *
	 * @param unknown $params        	
	 */
	public function hookHeader($params)
	{
		$timeCondition = 'AND sp.to > NOW()
						 	   AND sp.from < NOW()';
		

		$request = 'SELECT pr.id_product, sp.to, sp.from, fs.banner_text, fs.banner_text_color, fs.banner_text_bg_color
					FROM ' . _DB_PREFIX_ . ddlx_flashsales::$table_flashsales_product . ' fs,
						' . _DB_PREFIX_ . ddlx_flashsales::$table_specific_price . ' sp,
						' . _DB_PREFIX_ . ddlx_flashsales::$table_product . ' pr
					WHERE fs.id_specific_price = sp.id_specific_price
					' . $timeCondition . '
					AND sp.id_product = pr.id_product
					AND fs.id_shop = ' . $this->id_shop . '
					ORDER BY sp.from
					LIMIT 50';
		$fs = Db::getInstance()->executeS($request);
		

		$this->context->smarty->assign(array (
				'activeFS' => $fs,
				'path' => Tools::getShopDomainSsl() 
		));
		
		$this->context->controller->addJS(( $this->_path ) . "/js/jquery.countdown.js");
		
		// $this->controller->addJS(( $this->_path ) . "/js/jquery.countdown.min.js");
		
		return $this->display(__FILE__, 'views/templates/front/ddlx_flashsales.tpl', null);
	}

	public function hookDisplayHomeTab($params)
	{
		return $this->display(__FILE__, 'views/templates/front/tab.tpl', null);
	}

	public function hookdisplayHomeTabContent($params)
	{
		$products = ddlx_flashsales::displayFSProductsPage();
		
		$products = ProductCore::getProductsProperties($this->context->language->id, $products);
		
		$this->context->smarty->assign(array (
				'products' => $products,
				'homeSize' => Image::getSize('home_default') 
		));
		
		return $this->display(__FILE__, 'views/templates/front/ddlx_flashsales-home.tpl',null);
	}

	public function hookLeftColumn($params)
	{
		return $this->hookRightColumn($params);
	}

	public function hookRightColumn($params)
	{
		global $smarty, $cookie, $link;
		
		$timeCondition = 'AND sp.to > NOW() AND sp.from < NOW()';
		
		$request = 'SELECT pr.id_product
					FROM ' . _DB_PREFIX_ . ddlx_flashsales::$table_flashsales_product . ' fs,
						' . _DB_PREFIX_ . ddlx_flashsales::$table_specific_price . ' sp,
						' . _DB_PREFIX_ . ddlx_flashsales::$table_product . ' pr
					WHERE fs.id_specific_price = sp.id_specific_price
					' . $timeCondition . '
					AND sp.id_product = pr.id_product
					AND fs.id_shop = ' . $this->id_shop . '
					GROUP by pr.id_product
					ORDER BY pr.id_product
					LIMIT 50';
		
		$fs_result = Db::getInstance()->executeS($request);
		$fs_selected = array ();
		

		if ( sizeof($fs_result) > 1 )
		{
			$display = rand(0, sizeof($fs_result) - 1);
			$display2 = $display;
			
			while ( $display === $display2 )
			{
				$display2 = rand(0, sizeof($fs_result) - 1);
			}
			
			array_push($fs_selected, $fs_result [$display] ['id_product'], $fs_result [$display2] ['id_product']);
		}
		else if ( sizeof($fs_result) == 1 )
		{
			array_push($fs_selected, $fs_result [0] ['id_product']);
		}
		
		$second_prod = '';
		if ( sizeof($fs_selected) > 1 )
		{
			$second_prod = ', ' . $fs_selected [1];
		}
		

		$fs = array ();
		foreach ( $fs_selected as $key => $one_product )
		{
			array_push($fs, ddlx_flashsales::getProductsByIds($one_product));
		}
		
		$this->context->smarty->assign(array (
				'activeFS' => $fs,
				'mediumSize' => Image::getSize(ImageType::getFormatedName('medium')),
				'path' => $this->_path 
		));
		

		$this->context->controller->addCSS(( $this->_path ) . 'ddlx_fs.css', 'all');
		return $this->display(__FILE__, 'views/templates/front/ddlx_flashsales_column.tpl', null);
	}

	static function getProductsByIds($id_product)
	{
		$context = Context::getContext();
		
		$sql = 'SELECT p.*, product_shop.*, stock.`out_of_stock` out_of_stock, pl.`description`, pl.`description_short`,
						pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`, pl.`available_now`, pl.`available_later`,
						p.`ean13`, p.`upc`, MAX(image_shop.`id_image`) id_image, il.`legend`
					FROM `' . _DB_PREFIX_ . 'product` p
					LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (
						p.`id_product` = pl.`id_product`
						AND pl.`id_lang` = ' . (int) $context->language->id . Shop::addSqlRestrictionOnLang('pl') . '
					)
					' . Shop::addSqlAssociation('product', 'p') . '
					LEFT JOIN `' . _DB_PREFIX_ . 'image` i ON (i.`id_product` = p.`id_product`)' .
				 Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1') . '
					LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int) $context->language->id . ')
					' . Product::sqlStock('p', 0) . '
					WHERE p.id_product = ' . (int) $id_product . '
					GROUP BY product_shop.id_product';
		
		$row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
		
		if ( ! $row )
		{
			return false;
		}
		
		return Product::getProductProperties((int) $context->language->id, $row);
	}

	/**
	 * AJAX
	 */
	private function ajaxCreateDisplay()
	{
		if ( Tools::getValue('mode') == 'create' )
		{
			$explodedURI = explode("&mode=", $_SERVER ['REQUEST_URI']);
			
			$this->context->smarty->assign(array (
					'requesturi' => $explodedURI [0],
					'realpath' => $this->_path 
			));
			
			echo $this->display('ddlx_flashsales.php', 'views/templates/admin/createFS.tpl', null);
			die();
		}
	}

	/**
	 * demande edition
	 */
	private function ajaxEditDisplay()
	{
		if ( Tools::getValue('mode') == 'edit' && Tools::getValue('id_fs') != null )
		{
			// recupère le flashsales entity avec product et specific_rpice
			$request = 'SELECT pr.id_product, pl.name as pr_name, sp.to, sp.from, sp.reduction_type, sp.id_specific_price, sp.reduction,
					fs.name, fs.id as id_fs, fs.banner_text, fs.banner_text_color, fs.banner_text_bg_color
					FROM ' . _DB_PREFIX_ . ddlx_flashsales::$table_flashsales_product . ' fs,
						' . _DB_PREFIX_ . ddlx_flashsales::$table_specific_price . ' sp,
						' . _DB_PREFIX_ . ddlx_flashsales::$table_product . ' pr,
						' . _DB_PREFIX_ . ddlx_flashsales::$table_product_lang . ' pl
					WHERE fs.id_specific_price = sp.id_specific_price
					AND sp.id_product = pr.id_product
					AND pr.id_product = pl.id_product
					AND fs.id_shop = ' . $this->id_shop . '					
					AND fs.id = ' . Db::getInstance()->escape(Tools::getValue('id_fs'));
			$fs = Db::getInstance()->executeS($request);
			$fs = $this->addInfosToProduct($fs, ddlx_flashsales::$MODE_EDITION);
			
			$explodedURI = explode("&mode=", $_SERVER ['REQUEST_URI']);
			
			$this->context->smarty->assign(array (
					'requesturi' => $explodedURI [0],
					'flashsale' => $fs [0],
					'realpath' => $this->_path 
			));
			
			$request = 'SELECT sp.id_specific_price , sp.id_product , sp.from, sp.to
						FROM ' . _DB_PREFIX_ . ddlx_flashsales::$table_flashsales_product . ' fs,
							 ' . _DB_PREFIX_ . ddlx_flashsales::$table_product . ' pr,
							 ' . _DB_PREFIX_ . ddlx_flashsales::$table_specific_price . ' sp
						WHERE fs.id_specific_price = sp.id_specific_price
							  AND sp.id_product = pr.id_product
							  AND fs.id_shop = ' . $this->id_shop . '	
							  AND fs.id = ' . Db::getInstance()->escape(Tools::getValue('id_fs'));
			
			$res = Db::getInstance()->executeS($request);
			
			$this->context->smarty->assign(array (
					'datePromo' => $res 
			));
			
			echo $this->display('ddlx_flashsales.php', 'views/templates/admin/editFS.tpl', null);
			die();
		}
	}

	/**
	 * Autocompletion du formulaire, par nom ou id produit
	 *
	 * @return JSON
	 */
	private function ajaxAutoComplete()
	{
		if ( Tools::getValue('getProducts') == 'name' && Tools::getValue('term') )
		{
			// recupère le flashsales entity avec product et specific_rpice
			$request = 'SELECT pr.id_product, pl.name, pr.price
					FROM ' . _DB_PREFIX_ . ddlx_flashsales::$table_product . ' pr,
						 ' . _DB_PREFIX_ . ddlx_flashsales::$table_product_lang . ' pl	
					WHERE pr.id_product = pl.id_product
					AND pl.id_shop = ' . $this->id_shop . '
					AND pl.name LIKE \'%' . Db::getInstance()->escape(Tools::getValue('term')) . '%\' ';
			
			$fs = Db::getInstance()->executeS($request);
			
			foreach ( $fs as &$flashsale )
			{
				$id_image = Product::getCover($flashsale ['id_product']);
				
				// get Image by id
				if ( sizeof($id_image) > 0 )
				{
					$image = new Image($id_image ['id_image']);
					// get image full URL
					$image_url = _PS_BASE_URL_ . _THEME_PROD_DIR_ . $image->getExistingImgPath() . ".jpg";
				}
				

				$flashsale ['imgurl'] = $image_url;
			}
			

			$json = json_encode($fs);
			echo $json;
			die();
		}
		
		if ( Tools::getValue('getProducts') == 'id' && Tools::getValue('term') )
		{
			// recupère le flashsales entity avec product et specific_rpice
			$request = 'SELECT pr.id_product, pl.name, pr.price
					FROM ' . _DB_PREFIX_ . ddlx_flashsales::$table_product . ' pr,
						 ' . _DB_PREFIX_ . ddlx_flashsales::$table_product_lang . ' pl
					WHERE pr.id_product = pl.id_product
					AND pl.id_shop = ' . $this->id_shop . '
					AND pr.id_product = ' . Db::getInstance()->escape(Tools::getValue('term'));
			
			$fs = Db::getInstance()->executeS($request);
			
			foreach ( $fs as &$flashsale )
			{
				$id_image = Product::getCover($flashsale ['id_product']);
				
				// get Image by id
				if ( sizeof($id_image) > 0 )
				{
					$image = new Image($id_image ['id_image']);
					// get image full URL
					$image_url = _PS_BASE_URL_ . _THEME_PROD_DIR_ . $image->getExistingImgPath() . ".jpg";
				}
				
				$null = null;
				// prix normal
				$price = Product::getPriceStatic($flashsale ['id_product'], true, null, 2, null, false, false, 1, false, null, null, null, $null, true, false, null, true);
				
				$flashsale ['price'] = $price;
				$flashsale ['imgurl'] = $image_url;
			}
			
			$json = json_encode($fs);
			echo $json;
			die();
		}
	}

	private function ajaxGetPromoDates()
	{
		if ( Tools::getValue('mode') == 'getpromo' && Tools::getValue('id_product') != '' )
		{
			
			$request = 'SELECT  sp.from, sp.to 
						FROM ' . _DB_PREFIX_ . ddlx_flashsales::$table_specific_price . ' sp 
						WHERE  sp.id_product = ' . Db::getInstance()->escape(Tools::getValue('id_product')) . ' 
						AND sp.id_shop = ' . $this->id_shop;
			
			$res = Db::getInstance()->executeS($request);
			

			$json = json_encode($res);
			echo $json;
			die();
		}
	}

	private function ajaxSearch()
	{
		if ( Tools::getValue('mode') == 'search' )
		{
			echo $this->displayTablesSearchedFlashSales();
			
			die();
		}
	}

	private function displayTablesSearchedFlashSales()
	{
		$condition = '';
		
		if ( Tools::getValue('type') == 'FS_name' )
		{
			$condition = ' fs.name LIKE \'%' . Db::getInstance()->escape(Tools::getValue('searchText')) . '%\'';
		}
		else if ( Tools::getValue('type') == 'product_name' )
		{
			$condition = ' pl.name LIKE \'%' . Db::getInstance()->escape(Tools::getValue('searchText')) . '%\' ';
		}
		
		$request = 'SELECT pr.id_product, sp.id_specific_price, sp.to, sp.from, sp.reduction_type, sp.reduction, fs.name, fs.id as id_fs, fs.banner_text
					FROM ' . _DB_PREFIX_ . ddlx_flashsales::$table_flashsales_product . ' fs,
						' . _DB_PREFIX_ . ddlx_flashsales::$table_specific_price . ' sp,
						' . _DB_PREFIX_ . ddlx_flashsales::$table_product . ' pr,
						' . _DB_PREFIX_ . ddlx_flashsales::$table_product_lang . ' pl	
					WHERE ' . $condition . '
					AND pr.id_product = pl.id_product
					AND fs.id_specific_price = sp.id_specific_price
					AND sp.id_product = pr.id_product
					AND fs.id_shop = ' . $this->id_shop . '
					ORDER BY sp.from
					LIMIT 50';
		

		$fs = Db::getInstance()->executeS($request);
		$fs = $this->addInfosToProduct($fs, ddlx_flashsales::$MODE_READ);
		
		$this->context->smarty->assign(array (
				'activeFS' => $fs,
				'requesturi' => $_SERVER ['REQUEST_URI'] 
		));
		
		return $this->display(__FILE__, 'views/templates/admin/tableactiveFS.tpl', null);
	}

	
	/**
	 * Call by FRONT DISPLAY controller
	 */
	public static function displayFSProductsPage()
	{
		global $context;
		
		$timeCondition = 'AND sp.to > NOW() AND sp.from < NOW()';
		
		$request = 'SELECT pr.id_product
					FROM ' . _DB_PREFIX_ . ddlx_flashsales::$table_flashsales_product . ' fs,
						' . _DB_PREFIX_ . ddlx_flashsales::$table_specific_price . ' sp,
						' . _DB_PREFIX_ . ddlx_flashsales::$table_product . ' pr
					WHERE fs.id_specific_price = sp.id_specific_price
					' . $timeCondition . '
					AND sp.id_product = pr.id_product
					AND fs.id_shop = ' . $context->shop->id . '
					GROUP by pr.id_product
					ORDER BY pr.id_product
					LIMIT 50';
		
		$fs_result = Db::getInstance()->executeS($request);
		
		$fs = array ();
		foreach ( $fs_result as $key => $one_product )
		{
			// d($key . '=>' . $one_product);
			array_push($fs, ddlx_flashsales::getProductsByIds($one_product ['id_product']));
		}
		
		return $fs;
	}

}

