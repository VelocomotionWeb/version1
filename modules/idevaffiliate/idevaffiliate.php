<?php
/**
 * This file is part of module iDevAffiliate
 *
 *  @author    Bellini Services <bellini@bellini-services.com>
 *  @copyright 2007-2015 bellini-services.com
 *  @license   readme
 *
 * Your purchase grants you usage rights subject to the terms outlined by this license.
 *
 * You CAN use this module with a single, non-multi store configuration, production installation and unlimited test installations of PrestaShop.
 * You CAN make any modifications necessary to the module to make it fit your needs. However, the modified module will still remain subject to this license.
 *
 * You CANNOT redistribute the module as part of a content management system (CMS) or similar system.
 * You CANNOT resell or redistribute the module, modified, unmodified, standalone or combined with another product in any way without prior written (email) consent from bellini-services.com.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

if (!defined('_PS_VERSION_'))
	exit;

class iDevAffiliate extends Module
{

	const BACKWARD_REQUIREMENT = '0.4';
	public $module_key = '295e37b041a48499dfa08cb8522ad635';

	public function __construct()
	{
		$this->name = 'idevaffiliate';
		$this->tab = 'analytics_stats';
		$this->version = '1.6.0';
		$this->author = 'Bellini Services';
		$this->displayName = 'iDevAffiliate';

		parent::__construct();

		if ($this->id && !Configuration::get('IDEVAFFILIATE_PROFILEID') && !Configuration::get('IDEVAFFILIATE_INSTALLFOLDER'))
			$this->warning = $this->l('You have not yet set your iDevAffiliate settings');

		$this->description = $this->l('Integrate idevaffiliate tracking code into your shop');
		$this->confirmUninstall = $this->l('Are you sure you want to delete your details ?');

		if (_PS_VERSION_ < '1.5')
			require(_PS_MODULE_DIR_.$this->name.'/backward_compatibility/backward.php');

		if (self::isInstalled($this->name))
			$this->loadDefaults();
	}

	public function install()
	{
		if (_PS_VERSION_ < '1.5')
			$this->backwardCompatibilityChecks();

		Configuration::updateValue('AFFILIATE_IMAGE', 'idevaffiliate.jpg');
		Configuration::updateValue('IDEVAFFILIATE_AMOUNT_MODE', 0);
		Configuration::updateValue('IDEVAFFILIATE_VERSION', $this->version);

		if (!parent::install() || !$this->registerHook('leftColumn') || !$this->registerHook('orderConfirmation') )
			return false;
		return true;
	}

	public function uninstall()
	{
		if (!Configuration::deleteByName('IDEVAFFILIATE_PROFILEID') || !Configuration::deleteByName('IDEVAFFILIATE_INSTALLFOLDER') || !Configuration::deleteByName('AFFILIATE_IMAGE') || !Configuration::deleteByName('IDEVAFFILIATE_SIGNUP_PAGE') || !Configuration::deleteByName('IDEVAFFILIATE_AMOUNT_MODE') || !parent::uninstall())
			return false;
		return true;
	}

	/**
	 * Launch upgrade process
	 */
	public function runUpgrades()
	{
		//we only need to execute if we are PS v1.4, as PS v1.5+ includes upgrade functionality automatically.
		if (version_compare(_PS_VERSION_, '1.5', '>'))
			return;

		//represents the module version, for each upgrade file created, we need to include that version number in the array
		foreach (array('1.5') as $version)
		{
			$file = dirname(__FILE__).'/upgrade/install-'.$version.'.php';
			if (Configuration::get('IDEVAFFILIATE_VERSION') < $version && file_exists($file))
			{
				include_once($file);
				call_user_func('upgrade_module_'.str_replace('.', '_', $version), $this);
			}
		}
	}

	/**
	 * Initialize default values
	 */
	protected function loadDefaults()
	{
		//only execute if the module was loaded from the back office
		if (defined('_PS_ADMIN_DIR_'))
		{
			/* Upgrade and compatibility checks */
			$this->runUpgrades();
		}
	}

	public function getContent()
	{
		$output = '<h2>iDevAffiliate</h2>';
		if (Tools::isSubmit('submitiDevAffiliate'))
		{
			$adsp = Tools::getValue('idevaffiliate_signup_page');
			$adpi = Tools::getValue('idevaffiliate_profileid');
			$adif = Tools::getValue('idevaffiliate_installfolder');
			$adam = Tools::getValue('idevaffiliate_amount_mode', 0);

			Configuration::updateValue('IDEVAFFILIATE_PROFILEID', $adpi);
			Configuration::updateValue('IDEVAFFILIATE_INSTALLFOLDER', $adif);
			Configuration::updateValue('IDEVAFFILIATE_SIGNUP_PAGE', $adsp);
			Configuration::updateValue('IDEVAFFILIATE_AMOUNT_MODE', $adam);

//			echo 'aff_image: '.$_FILES['aff_image'];
			if (isset($_FILES['aff_image']) && isset($_FILES['aff_image']['tmp_name']) && !empty($_FILES['aff_image']['tmp_name'])) 
				if (move_uploaded_file($_FILES['aff_image']['tmp_name'], dirname(__FILE__).'/views/img/'.$_FILES['aff_image']['name']))
					Configuration::updateValue('AFFILIATE_IMAGE', $_FILES['aff_image']['name']);

			$output .= '
			<div class="conf confirm">
				<img src="../img/admin/ok.gif" alt="" title="" />
				'.$this->l('Settings updated').'
			</div>';
		}
		return $output.$this->displayForm();
	}

	public function displayForm()
	{
		$idevaffiliate_amount_mode = Tools::getValue('idevaffiliate_amount_mode', Configuration::get('IDEVAFFILIATE_AMOUNT_MODE'));
		$output = '
		<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post" enctype="multipart/form-data">
			<fieldset class="width2">
				<legend><img src="../img/admin/cog.gif" alt="" class="middle" />'.$this->l('Settings').'</legend>
				<label>'.$this->l('Your Profile Id').'</label>
				<div class="margin-form">
					<input type="text" name="idevaffiliate_profileid" value="'.Tools::safeOutput(Tools::getValue('idevaffiliate_profileid', Configuration::get('IDEVAFFILIATE_PROFILEID'))).'" />
					<p class="clear">'.$this->l('Example:').' 81</p>
				</div>
				<label>'.$this->l('Your Installation Folder').'</label>
				<div class="margin-form">
					<input type="text" name="idevaffiliate_installfolder" value="'.Tools::safeOutput(Tools::getValue('idevaffiliate_installfolder', Configuration::get('IDEVAFFILIATE_INSTALLFOLDER'))).'" />
					<p class="clear">'.$this->l('Example:').' idevaffiliate</p>
				</div>
				<label>'.$this->l('Affiliate image').'</label>
                <div class="margin-form">'; 
				if (Configuration::get('AFFILIATE_IMAGE') && file_exists(dirname(__FILE__) . '/views/img/' . Configuration::get('AFFILIATE_IMAGE'))) 
					$output .= '<img src="' . $this->_path .'views/img/'. Configuration::get('AFFILIATE_IMAGE') . '" />';

				$output .= '<input type="file" name="aff_image" /></div>
				<label>'.$this->l('Affiliate Signup Link').'</label>
                <div class="margin-form">
					<input type="text" name="idevaffiliate_signup_page" style="width:250px" value="'.Tools::safeOutput(Tools::getValue('idevaffiliate_signup_page', Configuration::get('IDEVAFFILIATE_SIGNUP_PAGE'))).'" />
					<p class="clear">'.$this->l('Example:').' http://www.google.com</p>
				</div>

				<label>'.$this->l('Affiliate Sale Amount Mode').'</label>
                <div class="margin-form">
					<input type="radio" name="idevaffiliate_amount_mode" value="0" '.(!$idevaffiliate_amount_mode ? 'checked="checked"' : '').' /> '.$this->l('Standard').'
					<input type="radio" name="idevaffiliate_amount_mode" value="1" '.($idevaffiliate_amount_mode ? 'checked="checked"' : '').' /> '.$this->l('Products').'
					<p class="clear">'.$this->l('Standard Mode: The order amount used to calculate commission excludes Shipping').'</p>
					<p class="clear">'.$this->l('Products Mode: The order amount used to calculate commission excludes Shipping and Taxes').'</p>
				</div>
	
				<center><input type="submit" name="submitiDevAffiliate" value="'.$this->l('Update Settings').'" class="button" /></center>
			</fieldset>
		</form>';

		return $output;
	}

    public function hookleftColumn($params)
	{
		$this->context->smarty->assign(array(
			'aff_img'=>__PS_BASE_URI__.'modules/idevaffiliate/views/img/'.Configuration::get('AFFILIATE_IMAGE'), 
			'aff_link'=>Configuration::get('IDEVAFFILIATE_SIGNUP_PAGE'))
		);

		return $this->fetchTemplate('idevaffiliate_left.tpl');
    }

	public function hookOrderConfirmation($params)
	{
		$order = $params['objOrder'];
		if (Validate::isLoadedObject($order))
		{
			$conf = Configuration::getMultiple(array('IDEVAFFILIATE_PROFILEID', 'IDEVAFFILIATE_INSTALLFOLDER', 'IDEVAFFILIATE_AMOUNT_MODE'));
			$idevaffiliate_profileid = $conf['IDEVAFFILIATE_PROFILEID'];
			$idevaffiliate_folder = $conf['IDEVAFFILIATE_INSTALLFOLDER'];
			$idevaffiliate_amount_mode = $conf['IDEVAFFILIATE_AMOUNT_MODE'];

			$currency = new Currency($order->id_currency);

			$sale_amount=$order->total_paid - $order->total_shipping;
			if ($idevaffiliate_amount_mode) //product only
			{
				//sale amount should exclude taxes and shipping
				$prodTotalNoTaxes=$order->getTotalProductsWithoutTaxes();
				$prodTotalWithTaxes=$order->getTotalProductsWithTaxes();
				$taxAmount=$prodTotalWithTaxes-$prodTotalNoTaxes;
				$sale_amount=$order->total_paid-$order->total_shipping-$taxAmount;
			}

			$this->context->smarty->assign('idevaffiliate_profileid', $idevaffiliate_profileid);
			$this->context->smarty->assign('idevaffiliate_folder', $idevaffiliate_folder);
			$this->context->smarty->assign('sale_amount', $sale_amount);
			$this->context->smarty->assign('idev_ordernum', $order->id);
			if (Validate::isLoadedObject($currency))
				$this->context->smarty->assign('idev_currency', $currency->iso_code);

			if (version_compare(_PS_VERSION_, '1.5', '>'))
			{
				//this is an array 
				$discounts = $order->getCartRules();

				if ($discounts && count($discounts)==1)
				{
					$discount = $discounts[0];
					$cart_rule = new CartRule($discount['id_cart_rule']);
					if (Validate::isLoadedObject($cart_rule))
						$this->context->smarty->assign('idev_coupon', $cart_rule->code);
				}
			}
			else
			{
				//this is an array 
				$discounts = $order->getDiscounts(false);

				if ($discounts && count($discounts)==1)
				{
					$discount = $discounts[0];
					$this->context->smarty->assign('idev_coupon', $discount['name']);
				}

			}

			return $this->fetchTemplate('orderconfirmation.tpl');
		}
	}

	/* Check status of backward compatibility module*/
	protected function backwardCompatibilityChecks()
	{
		if (Module::isInstalled('backwardcompatibility'))
		{
			$backward_module = Module::getInstanceByName('backwardcompatibility');
			if (!$backward_module->active)
				$this->warning .= $this->l('To work properly the module requires the backward compatibility module enabled').'<br />';
			elseif ($backward_module->version < idevaffiliate::BACKWARD_REQUIREMENT)
				$this->warning .= $this->l('To work properly the module requires at least the backward compatibility module v').idevaffiliate::BACKWARD_REQUIREMENT.'.<br />';
		}
		else
			$this->warning .= $this->l('In order to use the module you need to install the backward compatibility.').'<br />';
	}

	public function fetchTemplate($name)
	{
		if (version_compare(_PS_VERSION_, '1.4', '<'))
			$this->context->smarty->currentTemplate = $name;
		elseif (version_compare(_PS_VERSION_, '1.5', '<'))
		{
			$views = 'views/templates/';
			if (@filemtime(dirname(__FILE__).'/'.$name))
				return $this->display(__FILE__, $name);
			elseif (@filemtime(dirname(__FILE__).'/'.$views.'hook/'.$name))
				return $this->display(__FILE__, $views.'hook/'.$name);
			elseif (@filemtime(dirname(__FILE__).'/'.$views.'front/'.$name))
				return $this->display(__FILE__, $views.'front/'.$name);
			elseif (@filemtime(dirname(__FILE__).'/'.$views.'admin/'.$name))
				return $this->display(__FILE__, $views.'admin/'.$name);
		}

		return $this->display(__FILE__, $name);
	}

}
