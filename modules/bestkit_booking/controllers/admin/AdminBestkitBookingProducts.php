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
*  @copyright  BEST-KIT 
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

require_once (_PS_MODULE_DIR_ . 'bestkit_booking/includer.php');

class AdminBestkitBookingProductsController extends ModuleAdminController
{
    //protected $module_instance = NULL;

    public function __construct()
    {
        $this->context = Context::getContext();
        $this->table = 'bestkit_booking_product';
        $this->className = 'BestkitBookingProduct';
        $this->_defaultOrderBy = 'id_bestkit_booking_product';
        $this->lang = FALSE;
        $this->bootstrap = TRUE;

        $this->addRowAction('edit');
        $this->bulk_actions = array();

        $this->_select .= 'pl.`name` as product_name';
        $this->_join .= '
		    LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (pl.`id_product` = a.`id_product`)';
        $this->_where .= ' AND a.`active` = 1
            AND pl.id_lang = ' . (int)Context::getContext()->language->id;
        $this->_group .= 'GROUP BY a.id_bestkit_booking_product';

        //$this->module_instance = new bestkit_booking();
        //$this->productObj = new Product();

        $this->fields_list = array(
            'id_product' => array('title' => $this->l('ID product'), 'width' => 100),
            'product_name' => array('title' => $this->l('Product name'), 'filter_key' => 'pl!name'),
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
        $bookingObj = new BestkitBookingProduct(Tools::getValue('id_bestkit_booking_product'));
        Tools::redirectAdmin(Dispatcher::getInstance()->createUrl(
            'AdminProducts',
            $this->context->language->id,
            array(
                'id_product' => $bookingObj->id_product,
                'updateproduct' => 1,
                'token' => Tools::getAdminTokenLite('AdminProducts'),
                'key_tab' => 'ModuleBestkit_booking',
            ),
            FALSE
        ));
    }
}