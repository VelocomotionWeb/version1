<?php
/**
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
 *         DISCLAIMER   *
 * *************************************** */
/* Do not edit or add to this file if you wish to upgrade Prestashop to newer
* versions in the future.
* ****************************************************
*
*  @author     BEST-KIT
*  @copyright  best-kit
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

require_once (_PS_MODULE_DIR_ . 'bestkit_booking/includer.php');

class AdminBestkitBookingOrdersController extends ModuleAdminController
{
    public function __construct()
    {
        $this->context = Context::getContext();
        $this->table = 'bestkit_booking_order';
        $this->className = 'BestkitBookingOrder';
        $this->_defaultOrderBy = 'id_bestkit_booking_order';
        $this->lang = FALSE;
        $this->bootstrap = TRUE;

        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'),
            'confirm' => $this->l('Delete selected items?')), );

        $this->_select .= 'pl.`name` as product_name';
        $this->_join .= '
		    LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (pl.`id_product` = a.`id_product`)';
        $this->_where .= ' AND pl.id_lang = ' . (int)Context::getContext()->language->id;
        $this->_group .= 'GROUP BY a.id_bestkit_booking_order';

        $this->fields_list = array(
            'id_bestkit_booking_order' => array('title' => $this->l('ID'), 'width' => 70, 'align' => 'center'),
            'id_cart' => array('title' => $this->l('ID Cart'), 'width' => 70, 'align' => 'center'),
            'id_product' => array('title' => $this->l('ID Product'), 'width' => 70, 'align' => 'center'),
            'product_name' => array('title' => $this->l('Product name'), 'width' => 125, 'filter_key' => 'pl!name'),
            'date_from' => array('title' => $this->l('Date from'), 'width' => 50, 'type' => 'datetime'),
            'date_to' => array('title' => $this->l('Date to'), 'width' => 50, 'type' => 'datetime'),
            'time_from' => array('title' => $this->l('Time from'), 'width' => 70, /*'type' => 'time'*/),
            'time_to' => array('title' => $this->l('Time to'), 'width' => 70, /*'type' => 'time'*/),
        );

        parent::__construct();
    }
	
	public function renderView()
	{
		return $this->renderForm();
	}
	
	public function renderDetails()
	{
		return $this->renderForm();
	}

    public function renderForm()
    {
        $bookingOrderObj = new BestkitBookingOrder(Tools::getValue('id_bestkit_booking_order'));
		$id_order = Order::getOrderByCartId($bookingOrderObj->id_cart);
		
        Tools::redirectAdmin(Dispatcher::getInstance()->createUrl(
            'AdminOrders',
            $this->context->language->id,
            array(
                'id_order' => $id_order,
                'vieworder' => 1,
                'token' => Tools::getAdminTokenLite('AdminOrders'),
            ),
            FALSE
        ));
    }

}