<?php
class AdminSellerOrdersController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'marketplace_seller_orders';
        $this->className = 'MarketplaceSellerOrders';
        $this->list_id = 'id';

        // these fields will also call in render_view so protecting by id
        /*if (!Tools::getValue('id'))
        {
    		$this->_select = 'ms.shop_name';
            $this->_join .= 'LEFT JOIN `'._DB_PREFIX_.'marketplace_shop` ms ON (ms.`id_customer` = a.`id_customer_seller`) ';
        }*/
    
        $this->fields_list = array(
            'id' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ),
            'seller_shop' => array(
                'title' => $this->l('Shop Name'),
                'align' => 'center',
                'type' => 'price'
            ),  
            'total_earn' => array(
                'title' => $this->l('Total Earn'),
                'align' => 'center',
                'type' => 'price'
            ),
            'total_admin_commission' => array(
                'title' => $this->l('Total Admin Commission'),
                'align' => 'center',
                'type' => 'price'
            ),
            'count_values' => array(
                'title' => $this->l('Number Of Orders'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
                'orderby' => false,
                'search' => false
            ),
        );
        $this->addRowAction('view');
        $this->identifier  = 'id';
        parent::__construct();
    }

    public function renderView()
    {
        if ($id = Tools::getValue('id'))
        {
            if (Tools::getValue('submitFilterid_order'))
                $this->_filter = " AND a.`id_seller_order` = ".(int)$id." AND a.`id_order` = ".(int)Tools::getValue('id_orderFilter_id_order');
            else
                $this->_filter = " AND a.`id_seller_order` = ".(int)$id;

            $this->table = 'marketplace_commision_calc';
            $this->className = 'MarketplaceSellerOrderDetails';
            $this->_select = 'a.id_order as id_order, ord.date_add as order_date, ord.reference as reference, ord.payment, CONCAT(c.`firstname`," ",c.`lastname`) as customer';
            $this->_join .= 'LEFT JOIN `'._DB_PREFIX_.'orders` ord ON (a.`id_order` = ord.`id_order`) ';
            $this->_join .= 'LEFT JOIN `'._DB_PREFIX_.'customer` c ON (ord.`id_customer` = c.`id_customer`) ';
            $this->_orderBy = 'id_order';
            $this->_orderWay = 'DESC';
            $this->list_id = 'id_order';
            $this->identifier = 'id';

            if (!Validate::isLoadedObject(new MarketplaceSellerOrders((int)$id)))
            {
                $this->errors[] = Tools::displayError('An error occurred while updating the status for an object.').' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
                return;
            }

            $this->fields_list = array(
                'id_order' => array(
                    'title' => $this->l('Id Order'),
                    'align' => 'text-center',
                    'havingFilter' => true,
                    'class' => 'fixed-width-xs'
                ),
                'customer' => array(
                    'title' => $this->l('Customer'),
                    'align' => 'center',
                    'havingFilter' => true
                ),           
                'product_name' => array(
                    'title' => $this->l('Product Name'),
                    'align' => 'center'
                ),
                'price' => array(
                    'title' => $this->l('Product Price'),
                    'align' => 'center',
                    'type' => 'price',
                ),
                'commision' => array(
                    'title' => $this->l('Admin Commission'),
                    'align' => 'center',
                    'type' => 'price',
                ),
                'order_date' => array(
                    'title' => $this->l('Date'),
                    'align' => 'text-right',
                    'type' => 'datetime',
                    'havingFilter' => true
                )
            );

            self::$currentIndex = self::$currentIndex.'&view_order=1&viewmarketplace_seller_orders';
            return parent::renderList();
        }
    }

    public function getList($id_lang, $order_by = null, $order_way = null, $start = 0, $limit = null, $id_lang_shop = false)
    {
        parent::getList($id_lang, $order_by, $order_way, $start, $limit, $id_lang_shop);

        $nb_items = count($this->_list);
        for ($i = 0; $i < $nb_items; ++$i)
        {
            $item = &$this->_list[$i];

            $query = new DbQuery();
            $query->select('COUNT(mcc.id) as count_values');
            $query->from('marketplace_commision_calc', 'mcc');
            $query->join(Shop::addSqlAssociation('marketplace_commision_calc', 'mcc'));
            $query->where('mcc.id_seller_order ='.(int)$item['id']);
            $query->orderBy('count_values DESC');
            $item['count_values'] = (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
            unset($query);
        }
    }

    public function postProcess()
    {
        if (Tools::getValue('view_order') == 1 && !Tools::getValue('submitFilterid_order')) // submitFilterid_order for filter when render view is show
        {
            $id = Tools::getValue('id');
            $obj_seller_order_details = new MarketplaceSellerOrderDetails($id);
            $id_order = $obj_seller_order_details->id_order;
            $redirect_order_view = $this->context->link->getAdminLink('AdminOrders').'&id_order='.$id_order.'&vieworder';
            Tools::redirectAdmin($redirect_order_view);
        }
        parent::postProcess();
    }

    public function initToolbar()
    {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);
    }
}
?>