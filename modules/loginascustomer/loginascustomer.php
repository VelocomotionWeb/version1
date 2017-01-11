<?php

class LoginAsCustomer extends Module
{
	private $_html = '';
	private $_postErrors = array();

	public function __construct()
	{
		$this->name = 'loginascustomer';
		$this->tab = 'back_office_features';
		$this->version = '0.7.2';
		$this->author = 'PrestashopModul.Com';
		$this->controllers = array('login');
		
		$this->bootstrap = true;
		parent::__construct();	

		$this->displayName = $this->l('Login As Customer');
		$this->description = $this->l('Allows admins to login as customer');
	}

	public function install()
	{
		if (!parent::install() || !$this->registerHook('displayAdminCustomers'))
			return false;
		return true;
	}

	public function hookDisplayAdminCustomers($params)
	{
	   $customer = New CustomerCore ((int)Tools::getValue("id_customer"));
	   $link = $this->context->link->getModuleLink($this->name, 'login', array('id_customer' => $customer->id, 'xtoken' => $this->makeToken($customer->id)));
	   if(!Validate::isLoadedObject($customer)) return;
		
      return '
		<div class="panel" style="float:left; width:99.1%; margin:5px;margin-bottom: 20px;">
		    <div class="panel-heading">
			    <i class="icon-user"></i> '.$this->l("Login As Customer").' <span class="badge"></span>
		    </div>
		    <p class="text-muted text-center" style="float: left !important;">
				 <a class="btn pull-right btn-success" href="'.$link.'" target="_blank">
					  <i class="icon-user white"></i> '.$this->l("Connexion").'
				 </a>
		    </p>
		    <div class="info-heading" style="float:right">
			    <a href="" target="_blank">SRDEV Info</a> - <a href="mailto:contact@srdev.fr" target="_blank">contact@srdev.fr</a> 
		    </div>
		</div>';
	}
    
    public function makeToken($id_customer) {
        return md5(_COOKIE_KEY_.$id_customer.date("Ymd"));
    }

}
