<?php
class AdminReviewsController extends ModuleAdminController 
{
	public function __construct()
    {
		$this->bootstrap = true;
		$this->table = 'marketplace_seller_reviews';
		$this->className = 'Reviews';
		$this->list_id = 'id_review';

		if (!Tools::getValue('id_review'))
		{
			$this->_defaultOrderBy = 'id_review';
			$this->_select = 'msi.`business_email` AS seller_email,
			msi.`shop_name`,
			COUNT(a.`id_seller`) AS count_seller_reviews,
			COUNT(CASE WHEN a.`active` = 0 THEN 1 ELSE NULL END) AS inactive_review,
			AVG(a.`rating`) AS avg_seller_rating';

			$this->_join = 'LEFT JOIN `'._DB_PREFIX_.'marketplace_seller_info` msi ON (a.`id_seller` = msi.`id`)';
			$this->_group = 'GROUP BY a.`id_seller`';
		}

		$this->fields_list = array(
		    'id_review' => array(
				'title' => $this->l('Id'),
				'align' => 'center',
				'class' => 'fixed-width-xs'
			),
			'shop_name' => array(
				'title' => $this->l('Shop Name'),
				'align' => 'center',
				'havingFilter' => true
			),
			'seller_email' => array(
				'title' => $this->l('Seller Email'),
				'align' => 'center',
				'havingFilter' => true
			),
			'avg_seller_rating' => array(
				'title' => $this->l('Rating'),
				'align' => 'center',
				'havingFilter' => true
			),
			'count_seller_reviews' => array(
				'title' => $this->l('Review Count'),
				'align' => 'center',
				'havingFilter' => true
			),
			'inactive_review' => array(
				'title' => $this->l('Pending Reviews'),
				'align' => 'center',
				'havingFilter' => true,
				'badge_warning' => true
			),
		);

		$this->bulk_actions = array(
								'delete' => array('text' => $this->l('Delete selected'),
													'icon' => 'icon-trash',
												 'confirm' => $this->l('Delete selected items?')));

		$this->addRowAction('view');
		$this->addRowAction('delete');
		$this->identifier = 'id_review';
        parent::__construct();
	}

	public function initToolbar()
	{
		parent::initToolbar();
		unset($this->toolbar_btn['new']);
	}

	public function getList($id_lang, $orderBy = null, $orderWay = null, $start = 0, $limit = null, $id_lang_shop = null)
	{
		parent::getList($id_lang, $orderBy, $orderWay, $start, $limit, $id_lang_shop);

		if ($this->_list)
			foreach ($this->_list as &$row)
				$row['badge_warning'] = $row['active'] < 1;
	}

	public function renderView()
	{
		if (($id_review = Tools::getValue('id_review')) && Tools::getIsset('viewmarketplace_seller_reviews') && (!Tools::getValue('view_review') || Tools::getValue('submitFiltertemp_id_review')))
        {
        	$obj_review = new Reviews($id_review);
        	$id_seller = $obj_review->id_seller;
        	$this->_join .= 'JOIN `'._DB_PREFIX_.'marketplace_seller_info` msi ON (a.`id_seller` = msi.`id`)';
        	$this->_select .= 'msi.`business_email` AS seller_email,
			msi.`shop_name`,
			a.`id_seller` AS count_seller_reviews,
			a.`active` AS inactive_review,
			a.`rating` AS avg_seller_rating';

        	if (Tools::getValue('submitFiltertemp_id_review'))
                $this->_filter = " AND a.`id_review` = ".(int)Tools::getValue('temp_id_reviewFilter_id_review')." AND a.`id_seller` = ".(int)$id_seller;
            else
                $this->_filter = " AND a.`id_seller` = ".(int)$id_seller;

        	$this->table = 'marketplace_seller_reviews';
            $this->className  = 'Reviews';
            $this->identifier = 'id_review';
            $this->list_id = 'temp_id_review';

        	if (!Validate::isLoadedObject(new Reviews((int)$id_review)))
            {
                $this->errors[] = Tools::displayError('An error occurred while updating the status for an object.').' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
                return;
            }

            $this->fields_list = array(
			    'id_review' => array(
					'title' => $this->l('Id'),
					'align' => 'center',
					'class' => 'fixed-width-xs'
				),
				'customer_email' => array(
					'title' => $this->l('Customer Email'),
					'align' => 'center'
				),
				'rating' => array(
					'title' => $this->l('Rating'),
					'align' => 'center',
				),
				'review' => array(
					'title' => $this->l('Comment'),
					'align' => 'text-left',
					'maxlength' => 50
				),
				'date_add' => array(
                    'title' => $this->l('Date'),
                    'type' => 'datetime',
                ),
				'active' => array(
					'title' => $this->l('Status'),
					'active' => 'status',
					'type' => 'bool',
					'orderby' => false
				)
			);

            self::$currentIndex = self::$currentIndex.'&view_review=1&viewmarketplace_seller_reviews';
            return parent::renderList();
        }
        else
        	return parent::renderView();
	}

	public function postProcess()
    {
        if (Tools::getValue('view_review') == 1 && !Tools::getValue('submitFiltertemp_id_review')) 
        {
            $obj_review = new Reviews();
			$id_review = Tools::getValue('id_review');
			$review_detail = $obj_review->getSellerReviewById($id_review);

			// get seller information
			$obj_mp_seller = new SellerInfoDetail($review_detail['id_seller']);

			// get customer information
			if ($review_detail['id_customer']) // if not a guest
			{
				$obj_customer = new Customer($review_detail['id_customer']);
				$customer_name = $obj_customer->firstname.' '.$obj_customer->lastname;
				$this->context->smarty->assign('customer_name', $customer_name);
			}
			
			$this->context->smarty->assign(array(
											'review_detail' => $review_detail,
											'obj_mp_seller' => $obj_mp_seller,
											'module_dir' => _MODULE_DIR_,
											'id_review' => $id_review
										));
        }
        parent::postProcess();
    }
}
?>