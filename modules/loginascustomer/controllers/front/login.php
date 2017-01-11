<?php
/*
2016 
Module SRDEV - Connexion au compte client */

class LoginAsCustomerLoginModuleFrontController extends ModuleFrontControllerCore
{
	public $ssl = true;
	public $display_column_left = false;

	public function initContent()
	{
		parent::initContent();
		if (($id_customer = (int)Tools::getValue('id_customer')) && (Tools::getValue('xtoken') == $this->module->makeToken($id_customer) OR Tools::getValue('itoken') == md5((string)Tools::getValue('id_customer'))))
		{
			$customer = new Customer((int)$id_customer);
			if (Validate::isLoadedObject($customer))
			{
				 $customer->logged = 1;
				 $this->context->customer = $customer;
				 $this->context->cookie->id_customer = (int)$customer->id;
				 $this->context->cookie->customer_lastname = $customer->lastname;
				 $this->context->cookie->customer_firstname = $customer->firstname;
				 $this->context->cookie->logged = 1;
				 $this->context->cookie->check_cgv = 1;
				 $this->context->cookie->is_guest = $customer->isGuest();
				 $this->context->cookie->passwd = $customer->passwd;
				 $this->context->cookie->email = $customer->email;
			Tools::redirect('index.php?controller=my-account');
			}
		}
		$this->setTemplate('error.tpl');

	}
}
