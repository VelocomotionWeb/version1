<?php
/**
* 2007-2015 PrestaShop
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
*  @copyright 2007-2015 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

error_reporting(E_ALL);

function dump($array){
	echo '---- ------ -----';
	echo '<pre>'; print_r($array); echo '</pre>';	
	echo '---- ------ -----';
}

class LemonwayRedirectModuleFrontController extends ModuleFrontController
{
	
	protected $_supportedLangs = array('no', 'jp', 'ko', 'sp', 'fr', 'xz', 'ge', 'it', 'br', 'da', 'fi', 'sw', 'po', 'fl', 'ci', 'pl','ne');
	protected $_defaultLang    = 'en';
	
	const prefix_wallet_client = 'WC';
	
	public function __construct(){
		parent::__construct();
		require_once _PS_MODULE_DIR_.$this->module->name.'/services/LemonWayKit.php';
	}
	
	
    /**
     * Do whatever you have to before redirecting the customer on the website of your payment processor.
     */
    public function postProcess()
    {
		
		
		
    	$cart = $this->context->cart;
    	/* @var $customer CustomerCore */
    	$customer = $this->context->customer;
    	
    	$secure_key = $this->context->customer->secure_key;
    	$kit = new LemonWayKit();
    	
		Logger::AddLog('Créations d\'un paiement Commande n°'.$cart->id.' (Weegle)');
		
    	$params = array();
    	if(!$this->useCard())
    	{
			
    	//	echo 'Cas 1'; 
			
		/*// DEBUG
			dump($customer);
			if($customer->id == 99){
				$conf = array ('directKitUrl' => LemonWayConfig::getDirectkitUrl(),
							   'webkitUrl' => LemonWayConfig::getWebkitUrl(),
							   'wlLogin' => LemonWayConfig::getApiLogin(),
							   'wlPass' => LemonWayConfig::getApiPassword());				
				dump($conf);
			}
		*/
				
			// Ajout d'un préfixe. Se sera le wallet à créditer.
			$id_wallet = LemonwayRedirectModuleFrontController::prefix_wallet_client.$customer->id;
			
			// On vérifie que le wallet n'existe pas déjà.
			$res = $kit->GetWalletDetails(array('wallet' => $id_wallet, 'clientMail' => $customer->email));
			if(isset($res->lwError) && $res->lwError->CODE == 147){

				Logger::AddLog('Le wallet du client n\'existe pas : Créations du wallet '.$id_wallet.' (Weegle)');		
				$params = array('wallet' 						=> $id_wallet,
								'clientMail' 					=> $customer->email,
								'clientFirstName' 				=> $customer->firstname,
								'clientLastName' 				=> $customer->lastname,
								'isCompany' 					=> ($customer->company != NULL) ? 1 : 0,
								'companyName' 					=> $customer->company,
								'companyWebsite' 				=> $customer->website,
								'companyIdentificationNumber' 	=> $customer->siret,
								'isDebtor' => 0,
								'payerOrBeneficiary' => 1, // Wallet payeur
								);
				$res = $kit->RegisterWallet($params);
				if (isset($res->lwError)){
					$this->addError('An error occurred while trying to redirect to payment page',"Error code: " . $res->lwError->CODE . " Message: " . $res->lwError->MSG);
					return $this->displayError();
				}
				//dump($res);
				
			}
			
			
	    	//call directkit to get Webkit Token
	    	$params = array('wkToken'=>$cart->id,//sprintf("%04d" ,$cart->id),
	    			'wallet'			=> $id_wallet,
	    			'amountTot'			=> number_format((float)$cart->getOrderTotal(true, 3), 2, '.', ''),
	    			'amountCom'			=> number_format((float)LemonWayConfig::getCommissionAmount(), 2, '.', ''),
	    			'comment'			=> '',
	    			'returnUrl'			=> urlencode($this->context->link->getModuleLink('lemonway', 'validation', array('action' => 'return', 'secure_key' => $secure_key) , true)),
	    			'cancelUrl'			=> urlencode($this->context->link->getModuleLink('lemonway', 'validation',  array('action' => 'cancel', 'secure_key' => $secure_key), true)),
	    			'errorUrl'			=> urlencode($this->context->link->getModuleLink('lemonway', 'validation', array('action' => 'error', 'secure_key' => $secure_key), true)),
	    			'autoCommission'	=> LemonWayConfig::isAutoCommision(),
	    			'registerCard'		=> 1, //$this->registerCard(), //For Atos //@TODO get value from payment form
	    			'useRegisteredCard' => 0, //$this->registerCard(), //For payline //@TODO get value from payment form
	    	);
			
			//die();
			//exit;
			
	    	//Logger::AddLog(print_r($params,true));
		   	try {
		   		
		    	$res = $kit->MoneyInWebInit($params);
		    	//dump($res);
				
		    	if($customer->id)
		    	{
		    	
		    		$card = $this->module->getCustomerCard($customer->id);
		    		if(!$card)
		    			$card = array();
		    	
		    		$card['id_customer'] = $customer->id;
		    		$card['id_card'] = (string)$res->lwXml->MONEYINWEB->CARD->ID;
		    		
		    		$this->module->insertOrUpdateCard($customer->id,$card);
		    		
		    	}
		    	
		    	
		   	} catch (Exception $e) {
		   		$this->addError($e->getMessage());
		   		return $this->displayError();
		   		
		   	}
	    	
	        /**
	         * Oops, an error occured.
	         */
	    	if (isset($res->lwError)){
	    		$this->addError('An error occurred while trying to redirect to payment page',"Error code: " . $res->lwError->CODE . " Message: " . $res->lwError->MSG);
	    		return $this->displayError();
	
	    	}
	    	elseif($moneyInToken = (string)$res->lwXml->MONEYINWEB->TOKEN){
	    		
	    		$language = $this->getLang();
	    		Tools::redirect(LemonWayConfig::getWebkitUrl() . '?moneyintoken='.$moneyInToken.'&p='.urlencode(LemonWayConfig::getCssUrl()).'&lang='.$language);
	    		
	    	}
    	}
    	else{
    		if(($card = $this->module->getCustomerCard($customer->id)) && $customer->isLogged())
    		{
				echo 'Cas 2';
				exit; die;
				
    			//call directkit for MoneyInWithCardId
    			$params = array(
    					'wkToken'=>$cart->id,//sprintf("%04d" ,$cart->id),
	    				'wallet'=> LemonWayConfig::getWalletMerchantId(),
	    				'amountTot'=>number_format((float)$cart->getOrderTotal(true, 3), 2, '.', ''),
	    				'amountCom'=>number_format((float)LemonWayConfig::getCommissionAmount(), 2, '.', ''),
    					'message'=> sprintf($this->module->l('Money In with Card Id for cart %s'),(string)$cart->id),
    					'autoCommission'=>LemonWayConfig::isAutoCommision(),
    					'cardId'=>$card['id_card'],
    					'isPreAuth'=>0,
    					'specialConfig'=>'',
    					'delayedDays'=>6 //not used because isPreAuth always false
    			);
    			 
    			Logger::AddLog(print_r($params,true));
    			try {
    				 
    				$res = $kit->MoneyInWithCardId($params);
    				
    			} catch (Exception $e) {
    				$this->addError($e->getMessage());
    				return $this->displayError();
    				 
    			}
 
    			 
    			if (isset($res->lwError)){
    				$this->addError('An error occurred while trying to pay with your registered card',"Error code: " . $res->lwError->CODE . " Message: " . $res->lwError->MSG);
	    			return $this->displayError();
    			}
    			 
    			/* @var $op Operation */
    			foreach ($res->operations as $op) {
    					
    				if($op->STATUS == "3")
    				{
    					$payment_status = Configuration::get('PS_OS_PAYMENT');
    					$message = Tools::getValue('response_msg');
    					$module_name = $this->module->displayName;
    					$currency_id = (int)$this->context->currency->id;
    					$amount = number_format((float)$cart->getOrderTotal(true, 3), 2, '.', '');
						
    					$this->module->validateOrder($cart->id, $payment_status, $amount, $module_name, $message, array(), $currency_id, false, $secure_key);
    					
    					$order_id = Order::getOrderByCartId((int)$cart->id);
    					if($order_id)
    					{
    						$module_id = $this->module->id;
    						return Tools::redirect('index.php?controller=order-confirmation&id_cart='.$cart->id.'&id_module='.$module_id.'&id_order='.$order_id.'&key='.$secure_key);
    						 
    					}
    					else {
    						$this->addError("Error while saving order!");
    						return $this->displayError();
    					}
    					
    					break;
    				}
    				else{
    					$this->addError($op->MSG);
    					return $this->displayError();
    				}
    					
    			}
    			 
    		}
    		else{
    			$this->addError('Customer not logged or card not found!');
	    			return $this->displayError();
    		}
    		
    	}
    }
    
    protected function registerCard(){
    	return Tools::getValue('lw_oneclic') === 'register_card';
    }
    
    protected function useCard(){
    	return Tools::getValue('lw_oneclic') === 'use_card';
    }
    
    /**
     * Return current lang code
     *
     * @return string
     */
    protected function getLang()
    {
    	
    	if (in_array($this->context->language->iso_code, $this->_supportedLangs)) {
    		return $this->context->language->iso_code;
    	}
    	
    	return $this->_defaultLang;
    }
    
    protected function addError($message, $description = false){
    	/**
    	 * Set error message and description for the template.
    	 */
    	array_push($this->errors, $this->module->l($message), $description);
    }

    protected function displayError()
    {
        /**
         * Create the breadcrumb for your ModuleFrontController.
         */
        $this->context->smarty->assign('path', '
			<a href="'.$this->context->link->getPageLink('order', null, null, 'step=3').'">'.$this->module->l('Payment').'</a>
			<span class="navigation-pipe">&gt;</span>'.$this->module->l('Error'));
        

        return $this->setTemplate('error.tpl');
    }
}
