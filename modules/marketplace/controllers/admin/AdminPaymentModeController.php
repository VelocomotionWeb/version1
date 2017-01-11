<?php
class AdminPaymentModeController extends ModuleAdminController
{
	public function __construct()
    {
    	$this->bootstrap = true;
      	$this->table     = 'marketplace_payment_mode';
        $this->className = 'MarketPaymentMode';
       
        $this->fields_list = array(
            'id' => array(
                'title' => $this->l('Id'),
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ),
            'payment_mode' => array(
                'title' => $this->l('Payment Mode'),
                'width' => '100'
            )
        );
        $this->bulk_actions = array(
								'delete' => array('text' => $this->l('Delete selected'),
													'icon' => 'icon-trash',
												 'confirm' => $this->l('Delete selected items?')));
        $this->identifier  = 'id';
        parent::__construct();

	}
	
	public function renderList()
	{
		$this->addRowAction('edit');
		$this->addRowAction('delete');

        $this->page_header_toolbar_btn['new'] = array(
            'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
            'desc' => $this->l('Add Payment Method')
        );
		return parent::renderList();
	}	
	
	public function renderForm()
	{
		$this->fields_form = array(
		  'legend' => array(       
			'title' => $this->l('Edit Payment Mode')        
		  ), 
		  
		  'input' => array(       
			array(           
			  'type' => 'text',
			  'name' => 'payment_mode',
			  'label' => $this->l('Payment Mode'),
			  'required' => true
			 ),
		  ),
		  
		  'submit' => array(
				'title' => $this->l('Save'),
			)
		);
		return parent::renderForm();
	}
	
}