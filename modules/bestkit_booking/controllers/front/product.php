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

class bestkit_bookingproductModuleFrontController extends
    ModuleFrontController
{
	public function initContent()
	{
		$action = Tools::getValue('action') . 'Action';
		if (method_exists($this, $action)) {
			$this->$action();
		}

		Tools::display404Error();
	}

	protected function getTimeContainerAction()
	{
		$date_from = Tools::getValue('date_from');
		$date_to = Tools::getValue('date_to');
		if (!strtotime($date_from) || ($date_to && !strtotime($date_to))) {
			header('HTTP/1.1 404 Not Found');
			header('Status: 404 Not Found');
			die('Wrong date!');
		}

		die($this->module->hookdisplayProductButtons(array('timeContainer' => true, 'date_from' => $date_from, 'date_to' => $date_to)));
	}

	protected function checkReservationAction()
	{
		$id_product = (int)Tools::getValue('id_product');
		$date_from = Tools::getValue('date_from');
		$date_to = Tools::getValue('date_to', array());
		//$qty = (int)Tools::getValue('qty', 1);

		$time_from = $this->module->jsToPhpDateTime($date_from);
		$time_from += (($date_from[1] * 60 + $date_from[2]) * 60);

		$time_to = $this->module->jsToPhpDateTime($date_to);
		$time_to += (($date_to[1] * 60 + $date_to[2]) * 60);

		$from = date('Y-m-d H:i:s', $time_from);
		$to = date('Y-m-d H:i:s', $time_to);

		$is_available = $this->module->checkIsBookingDateAvailable($id_product, $from, $to, false);

		if ($is_available['flag']) {
			$return = array(
				'status' => 'success',
				'price' => $this->module->getBookingPrice(
					$id_product, 
					date(bestkit_booking::PHP_DATE_FORMAT . ' H:i:s', $time_from), 
					date(bestkit_booking::PHP_DATE_FORMAT . ' H:i:s', $time_to)
				),
			);
		} else {
			$return = array(
				'status' => 'error',
				'message' => $is_available['flag_reason'],
			);
		}

		die(Tools::jsonEncode($return));
	}

	protected function getDatePriceAction()
	{
		$id_product = (int)Tools::getValue('id_product');
		$dates = Tools::getValue('dates');
		$return = array();

		foreach ($dates as $date) {
			$_date = $this->module->jsToPhpDateTime(array($date));
			$_date = date('Y-m-d H:i:s', $_date);

			$return[$date] = $this->module->getBookingPrice($id_product, $_date);
		}

		die(Tools::jsonEncode($return));
	}
}
