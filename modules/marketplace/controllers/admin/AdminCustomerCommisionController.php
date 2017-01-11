<?php
class AdminCustomerCommisionController extends ModuleAdminController
{
    public function __construct()
    {
        $this->table = 'marketplace_commision';
        $this->className = 'MarketplaceCommision';
        $this->bootstrap = true;

        $tax_distributor = array(array('id' => 'admin', 'name' => 'Admin'),
                    array('id' => 'seller', 'name' => 'Seller'),
                    array('id' => 'distribute_both', 'name' => 'Distribute between seller and admin'));

        $this->fields_options = array(
            'global' => array(
                'title' =>  $this->l('Global Commission'),
                'icon' =>   'icon-globe',
                'fields' => array(
                    'MP_GLOBAL_COMMISSION' => array(
                        'title' => $this->l('Commission Rate'),
                        'hint' => $this->l('The default commission rate apply on all sellers.'),
                        'validation' => 'isFloat',
                        'required' => true,
                        'type' => 'text',
                        'suffix' => $this->l('%')
                    ),
                ),
                'submit' => array('title' => $this->l('Save'))
            ),
            'tax_distribution' => array(
                'title' =>  $this->l('Tax Distribution'),
                'icon' =>   'icon-globe',
                'fields' => array(
                    'MP_PRODUCT_TAX_DISTRIBUTION' => array(
                            'title' => $this->l('Product Tax'),
                            'type' => 'select',
                            'list' => $tax_distributor,
                            'identifier' => 'id'
                        )
                ),
                'submit' => array('title' => $this->l('Save'))
            )
        );

        $this->fields_list = array(
            'id' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
               'class' => 'fixed-width-xs'
            ),
            'customer_name' => array(
                'title' => $this->l('Seller Name'),
                'align' => 'center'
            ),
            'commision' => array(
                'title' => $this->l('Commission Rate'),
                'align' => 'center',
                'suffix' => $this->l('%')
            )
        );
        $this->identifier  = 'id';
        $this->bulk_actions = array(
									'delete' => array('text' => $this->l('Delete selected'),
														'icon' => 'icon-trash',
														'confirm' => $this->l('Delete selected items?')),
									);
        parent::__construct();
    }

    public function initContent()
    {
        parent::initContent();
        $this->content .= $this->renderList();
    }

    public function renderList() 
    {
		$this->addRowAction('edit');
		$this->addRowAction('delete');

        $this->page_header_toolbar_btn['new'] = array(
            'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
            'desc' => $this->l('Add Admin Commission')
        );
        return parent::renderList();
    }

    public function renderForm()
    {
        $obj_mp_comm = new MarketplaceCommision();
        $remain_seller = array();
        if ($id = Tools::getValue('id'))
        {
            $obj_mp_commission = new MarketplaceCommision($id);
            $obj_customer = new Customer($obj_mp_commission->customer_id);
            $remain_seller[] = array('id_customer' => $obj_mp_commission->customer_id,
                                    'email' => $obj_customer->email);
        }
        else
            $remain_seller = $obj_mp_comm->getSellerNotHaveCommissionSet();

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Admin Commission'),
                'icon' =>   'icon-money'
            ),
            'input' => array(
                array(
                    'label' => $this->l('Select Seller'),
                    'name' => 'customer_id',
                    'type' => 'select',
                    'required' => true,
                    'identifier' => 'id',
                    'options' => array(
                        'query' => $remain_seller,
                        'id' => 'id_customer',
                        'name' => 'email'
                    )
                ),
                array(
                    'label' => $this->l('Commision'),
                    'name' => 'add',
                    'type' => 'hidden',
                    'value' => '1'
                ),
                array(
                    'label' => $this->l('Admin Commission'),
                    'name' => 'commision',
                    'type' => 'text',
                    'required' => true,
                    'default' => '10',
                    'suffix' => $this->l('%')
                )
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            )
        );

		if (!$remain_seller) //if no seller fond or active and commission set for all
			$this->displayWarning($this->l('No active marketplace seller OR you have already set commission for all sellers.'));
		else
			return parent::renderForm();
    }

    public function processSave()
    {
    	$commission = Tools::getValue('commision');
    	$id_customer = Tools::getValue('customer_id');

    	$id = Tools::getValue('id'); //if edit

		if ($commission == "")
            $this->errors[] = Tools::displayError('Commission field is required.');
        elseif (!Validate::isFloat($commission))
            $this->errors[] = Tools::displayError('Invalid commission rate.');

        if (empty($this->errors))
        {
        	$obj_customer = new Customer($id_customer);
        	if ($id)
            	$obj_mp_commission = new MarketplaceCommision($id);
            else
            	$obj_mp_commission = new MarketplaceCommision();

            $customer_name = $obj_customer->firstname." ".$obj_customer->lastname;
            $obj_mp_commission->customer_id = $id_customer;
            $obj_mp_commission->commision = $commission;
            $obj_mp_commission->customer_name = $customer_name;
            $obj_mp_commission->save();
           
            if ($obj_mp_commission->id)
            {
            	if ($id)
            		Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
            	else
                	Tools::redirectAdmin(self::$currentIndex.'&conf=3&token='.$this->token);
            }
        }
        else
        	$this->display = 'add';
    }
}
?>