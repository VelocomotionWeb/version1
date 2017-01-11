<?php
global $smarty;
include( '../../config/config.inc.php' );
require 'src/facebook.php';
	$result='';
	$fb_connect_appid = Configuration::get('PS_FBCONNECT_APPID');
	$fb_connect_secret = Configuration::get('PS_FBCONNECT_SECRET');
	if ($fb_connect_appid=='' or $fb_connect_secret=='') {
		$result='error';
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
				$fbconnect_email=$facebook_user_profile['email'];
				$fbconnect_firstname=$facebook_user_profile['first_name'];
				$fbconnect_lastname=$facebook_user_profile['last_name'];
				$fbconnect_gender=$facebook_user_profile['gender'];
				$fbconnect_id=$facebook_user_profile['id'];
				$context = Context::getContext();

				$date_add = date("Y-m-d H:i:s");
				$sql='SELECT `id_fbconnect` FROM `'._DB_PREFIX_.'fbconnect` 
				WHERE `id_facebook`=\''.$fbconnect_id.'\'';
				$fbconnects =Db::getInstance()->ExecuteS($sql);				
				if ($fbconnects) {
					$id_fbconnect=$fbconnects[0]['id_fbconnect'];
					$values= array(
						'id_facebook' => $fbconnect_id,
						'gender' => $fbconnect_gender,
						'firstname' => $fbconnect_firstname,
						'lastname' => $fbconnect_lastname,
						'email' => $fbconnect_email,
						'date_upd' => $date_add,
					);
					Db::getInstance()->update('fbconnect',$values,'`id_fbconnect` = '.(int)$id_fbconnect);
				} else {
					$values= array(
						'id_facebook' => $fbconnect_id,
						'gender' => $fbconnect_gender,
						'firstname' => $fbconnect_firstname,
						'lastname' => $fbconnect_lastname,
						'email' => $fbconnect_email,
						'date_add' => $date_add,
					);
					Db::getInstance()->insert('fbconnect',$values);
				}
				
				if ($fbconnect_email<>'') {
					$fbconnect_customer = new Customer();
					$fbconnect_authentication = $fbconnect_customer->getByEmail(trim($fbconnect_email));
					if (!$fbconnect_authentication || !$fbconnect_customer->id) {
						if ($fbconnect_gender=='male') {
							$id_gender=1;
						} elseif ($fbconnect_gender=='female') {
							$id_gender=2;
						} else {
							$id_gender=0;
						}
						$fbconnect_customer->lastname=$fbconnect_lastname;
						$fbconnect_customer->firstname=$fbconnect_firstname;
						$fbconnect_customer->email=$fbconnect_email;
						//$fbconnect_customer->passwd = md5(time()._COOKIE_KEY_); //mot de passe aléatoire sans utilité pour identification via facebook
						$passwd=substr(md5(time()._COOKIE_KEY_),0,8); //gen passwd 8 char alèatoire
						$fbconnect_customer->passwd = Tools::encrypt($passwd); 
						$fbconnect_customer->is_guest = (Tools::isSubmit('is_new_customer') ? !Tools::getValue('is_new_customer', 1) : 0);
						$fbconnect_customer->active = 1;
						$fbconnect_customer->add();
						$fbconnect_customer->transformToCustomer($context->language->id);
						$values= array(
							'id_gender' => $id_gender,
						);
						Db::getInstance()->update('customer',$values,'`id_customer` = '.(int)$fbconnect_customer->id);
						$mail_send = Mail::Send(
							$context->language->id,
							'account',
							Mail::l('Welcome!'),
							array(
								'{firstname}' => $fbconnect_customer->firstname,
								'{lastname}' => $fbconnect_customer->lastname,
								'{email}' => $fbconnect_customer->email,
								'{passwd}' => $passwd),
							$fbconnect_customer->email,
							$fbconnect_customer->firstname.' '.$fbconnect_customer->lastname
						);
						$context->customer = $fbconnect_customer;
						$context->smarty->assign('confirmation', 1);
						$context->cookie->id_customer = (int)$fbconnect_customer->id;
						$context->cookie->customer_lastname = $fbconnect_customer->lastname;
						$context->cookie->customer_firstname = $fbconnect_customer->firstname;
						$context->cookie->passwd = $fbconnect_customer->passwd;
						$context->cookie->logged = 1;
						if (!Configuration::get('PS_REGISTRATION_PROCESS_TYPE'))
							$context->cookie->account_created = 1;
						$fbconnect_customer->logged = 1;
						$context->cookie->email = $fbconnect_customer->email;
						$context->cookie->is_guest = !Tools::getValue('is_new_customer', 1);
					}
					else
					{
						$result=(int)($fbconnect_customer->id);
						$context->customer = $fbconnect_customer;

						$context->smarty->assign('confirmation', 1);
						$context->cookie->id_customer = (int)$fbconnect_customer->id;
						$context->cookie->customer_lastname = $fbconnect_customer->lastname;
						$context->cookie->customer_firstname = $fbconnect_customer->firstname;
						$context->cookie->passwd = $fbconnect_customer->passwd;
						$context->cookie->logged = 1;
						if (!Configuration::get('PS_REGISTRATION_PROCESS_TYPE'))
							$context->cookie->account_created = 1;
						$fbconnect_customer->logged = 1;
						$context->cookie->email = $fbconnect_customer->email;
						$context->cookie->is_guest = !Tools::getValue('is_new_customer', 1);
						$context->cart->secure_key = $fbconnect_customer->secure_key;
						if (Configuration::get('PS_CART_FOLLOWING') && (empty($context->cookie->id_cart) || Cart::getNbProducts($context->cookie->id_cart) == 0) && $id_cart = (int)Cart::lastNoneOrderedCart($fbconnect_customer->id)) {
							$context->cart = new Cart($id_cart);
							$context->cart->id_customer = (int)$fbconnect_customer->id;
							$context->cart->secure_key = $fbconnect_customer->secure_key;
							$context->cart->save();
							$context->cookie->id_cart = (int)$context->cart->id;
							$context->cart->autosetProductAddress();
						} else { 
						}
						$context->cookie->write();
					}
				}
			}
		}

		Tools::redirect('index.php');
	}
?>