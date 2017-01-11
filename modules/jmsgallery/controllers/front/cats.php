<?php
/**
* 2007-2014 PrestaShop
*
* Jms Blog
*
*  @author    Joommasters <joommasters@gmail.com>
*  @copyright 2007-2014 Joommasters
*  @license   license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*  @Website: http://www.joommasters.com
*/

/**
 * @since 1.5.0
 */
class JMSBlogCatsModuleFrontController extends ModuleFrontController
{
	public $ssl = true;
	public $display_column_left = false;

	/**
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		parent::initContent();		
		$cats = $this->getCats();
		$this->context->controller->addCSS($this->module->getPathUri().'css/style.css', 'all');				
		$this->context->smarty->assign(array(
			'cats' => $cats,
			'image_baseurl' => $this->module->getPathUri().'img/'
		));
		$this->setTemplate('cats.tpl');
	}
	public function getCats($parent = 0)
	{
		$this->context = Context::getContext();		
		$id_lang = $this->context->language->id;		
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT hss.`cat_id` as cat_id, hssl.`image`, hss.`ordering`, hss.`active`, hssl.`title`,
			hssl.`description`
			FROM '._DB_PREFIX_.'jmsblog_cats hss			
			LEFT JOIN '._DB_PREFIX_.'jmsblog_cats_lang hssl ON (hss.`cat_id` = hssl.`cat_id`)
			WHERE hssl.`id_lang` = '.(int)$id_lang.
			' AND hss.`parent` = '.$parent.' 
			ORDER BY hss.`ordering`'
		);
	}
}
