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

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'bestkit_booking/includer.php';

class bestkit_booking extends Module
{
    const PREFIX = 'bestkit_booking_';

    const JS_DATE_FORMAT = 'mm/dd/yy';
    const PHP_DATE_FORMAT = 'm/d/Y';

    protected $_hooks = array(
        'displayHeader',
        'displayAdminProductsExtra',
        'actionProductUpdate',
        'actionObjectProductDeleteBefore',
        'displayProductPriceBlock',
        'displayProductButtons',
        'actionCartSave',
        'displayShoppingCart',
        'actionDispatcher',
        'displayAdminOrder',
        //'actionObjectBestkitBookingDeleteBefore',
        //'displayProductListFunctionalButtons',
        //'displayRightColumnProduct',
    );

    protected $_moduleParams = array(
        'statuses' => array(),
        'api_key' => '',
    );
    protected $_moduleParamsLang = array();

    protected $_tabs = array(
        array(
            'class_name' => 'AdminBestkitBookingOrders',
            'parent' => 'AdminOrders',
            'name' => 'Booking Orders'
        ),
        array(
            'class_name' => 'AdminBestkitBookingProducts',
            'parent' => 'AdminCatalog',
            'name' => 'Booking Products'
        ),
    );

    public function __construct()
    {
        $this->name = 'bestkit_booking';
        $this->tab = 'front_office_features';
        $this->version = '1.6.4';
        $this->author = 'best-kit';
        $this->need_instance = 0;
        $this->module_key = 'b9b4dd436c20a2341a8a28b2b1ba82d0';
        $this->bootstrap = TRUE;

        parent::__construct();

        $this->displayName = $this->l('Booking / Reservations / Rent');
        $this->description = $this->l('Offer and manage all kind of bookable products. Flexible booking options, user-friendly interface. Package for PrestaShop v.1.6.x');
    }

    public function getDir($file = '')
    {
        return _PS_MODULE_DIR_ . $this->name . DIRECTORY_SEPARATOR . $file;
    }

    public function install()
    {
        if (parent::install()) {
            $sql = array();
            include ($this->getDir('sql/install.php'));
            foreach ($sql as $_sql) {
                Db::getInstance()->Execute($_sql);
            }

            foreach ($this->_hooks as $hook) {
                if (!$this->registerHook($hook)) {
                    return FALSE;
                }
            }

			$this->updatePosition(Hook::getIdByName('displayProductButtons'), 0, 1);

            $languages = Language::getLanguages();
            foreach ($this->_tabs as $tab) {
                $_tab = new Tab();
                $_tab->class_name = $tab['class_name'];
                $_tab->id_parent = Tab::getIdFromClassName($tab['parent']);
                if (empty($_tab->id_parent)) {
                    $_tab->id_parent = 0;
                }

                $_tab->module = $this->name;
                foreach ($languages as $language) {
                    $_tab->name[$language['id_lang']] = $this->l($tab['name']);
                }

                $_tab->add();
            }

            if (!$this->installConfiguration()) {
                return FALSE;
            }

            return TRUE;
        }

        return FALSE;
    }

    public function uninstall()
    {
        if (parent::uninstall()) {
            $sql = array();
            include ($this->getDir('sql/uninstall.php'));
            foreach ($sql as $_sql) {
                Db::getInstance()->Execute($_sql);
            }

            foreach ($this->_tabs as $tab) {
                $_tab_id = Tab::getIdFromClassName($tab['class_name']);
                $_tab = new Tab($_tab_id);
                $_tab->delete();
            }
        }

        return TRUE;
    }

    public function installConfiguration()
    {
        foreach ($this->_moduleParams as $param => $value) {
            if (!$this->setConfig($param, $value)) {
                return FALSE;
            }
        }

        foreach ($this->_moduleParamsLang as $param => $value) {
            $values = array();
            foreach (Language::getLanguages(FALSE) as $lang) {
                $values[$lang['id_lang']] = $value;
            }

            if (!$this->setConfig($param, $values)) {
                return FALSE;
            }
        }

        return TRUE;
    }

    public function getConfig($name)
    {
        if (array_key_exists($name, $this->_moduleParamsLang)) {
            $values = array();
            foreach (Language::getLanguages(FALSE) as $lang) {
                $values[$lang['id_lang']] = Configuration::get(self::PREFIX . $name, $lang['id_lang']);
            }

            return $values;
        } else {
            return Configuration::get(self::PREFIX . $name);
        }
    }

    public function setConfig($name, $value)
    {
        return Configuration::updateValue(self::PREFIX . $name, $value, TRUE);
    }

    public function getTemplate($area, $file)
    {
        return _PS_MODULE_DIR_ . $this->name . '/' . 'views/templates/' . $area . '/' . $file;
    }

    private function initForm() {
        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->identifier = $this->identifier;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->toolbar_scroll = TRUE;
        $helper->toolbar_btn = $this->initToolbar();
        $helper->title = $this->displayName;
        $helper->submit_action = 'submitUpdate';

        $languages = Language::getLanguages(FALSE);
        foreach ($languages as $k => $language) {
            $languages[$k]['is_default'] = (int)($language['id_lang'] == Configuration::get('PS_LANG_DEFAULT'));
        }

        $helper->languages = $languages;
        $helper->default_form_language = (int)Configuration::get('PS_LANG_DEFAULT');
		
		$order_states = OrderState::getOrderStates($this->context->language->id);
        $ps_order_states = array();
        foreach ($order_states as $order_state) {
            $ps_order_states[] = array(
                'id' => $order_state['id_order_state'],
                'name' => $order_state['name'],
			);
        }
		
        $this->fields_form[0]['form'] = array(
            'tinymce' => TRUE,
            'legend' => array(
                'title' => $this->l('Configuration'),
                'icon' => 'icon-cogs'
            ),
            'submit' => array(
                'name' => 'submitUpdate',
                'title' => $this->l('   Save   '),
            ),
            'input' => array(
                array(
                    'type' => 'select',
                    'label' => $this->l('Valid order statuses'),
                    'desc' => $this->l('Orders with this statuses will be valid'),
                    'name' => 'statuses[]',
                    'multiple' => TRUE,
                    'size' => 10,
                    'options' => array(
                        'query' => $ps_order_states,
                        'id' => 'id',
                        'name' => 'name'
					),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Google Maps API key'),
                    'desc' => $this->l('it needs if you use the "Show map" option for products'),
                    'name' => 'api_key',
                ),
            )
        );

        return $helper;
    }

    public function getContent() {
        $this->postProcess();
        $helper = $this->initForm();
        foreach ($this->fields_form as $fieldset) {
            foreach ($fieldset['form']['input'] as $input) {
				//if (strpos($input['name'], '[]')) {
				if (preg_match('/\[\]$/', $input['name'])) {
					$tmp_name = preg_replace('/\[\]$/', '', $input['name']);
					$helper->fields_value[$input['name']] = unserialize($this->getConfig($tmp_name));
				} else {
					$helper->fields_value[$input['name']] = $this->getConfig($input['name']);
				}
            }
        }

        return $helper->generateForm($this->fields_form);
    }

    protected function postProcess() {
        if (Tools::isSubmit('submitUpdate')) {
            $errors = '';
            //$data = $_POST;
            $data = array();
            $data['statuses'] = Tools::getValue('statuses');
            $data['api_key'] = Tools::getValue('api_key');
            if (is_array($data)) {
                foreach ($data as $key => $value) {
                    if (array_key_exists($key, $this->_moduleParams)) {
						if (is_array($value)) {
							$this->setConfig($key, serialize($value));
						} else {
							$this->setConfig($key, $value);
						}
                    }
                }
            }

            //lang
            $languages = Language::getLanguages(FALSE);
            foreach (array_keys($this->_moduleParamsLang) as $key) {
                $values = array();
                foreach ($languages as $lang) {
                    $values[$lang['id_lang']] = $data[$key . '_' . $lang['id_lang']];
                }

                $this->setConfig($key, $values);
            }


            if (empty($errors)) {
                Tools::redirectAdmin('index.php?tab=AdminModules&conf=4&configure=' . $this->name . '&token=' . Tools::getAdminToken('AdminModules' . (int) (Tab::getIdFromClassName('AdminModules')) . (int) $this->context->employee->id));
            }

            foreach ($errors as $error) {
                $this->_html .= '<div class="error">' . $error . '</div>';
            }
        }
    }

    private function initToolbar() {
        $this->toolbar_btn['save'] = array(
            'href' => '#',
            'desc' => $this->l('Save')
        );

        return $this->toolbar_btn;
    }
	
	
	/* Alex [begin] */
	protected function checkBookingPeriodAvailable(BestkitBookingProduct $bookingObj, $to_time, $from_time, $iteration)
	{
		$flag = true;
		$_flag_reason = sprintf($this->l('Booking ID=%s is available'), $bookingObj->id);
		
		/*if ($this->isValidTimeStamp($to)) 
			$to_time = $to;
		else
			$to_time = strtotime($to);
			
		if ($this->isValidTimeStamp($from)) 
			$from_time = $from;
		else
			$from_time = strtotime($from);*/
		
		
		$human_format = date('Y-m-d H:i:s', $iteration);
		
		$diff_time = $to_time - $from_time;
		
		if ($diff_time <= 0) {
			$flag = false;
			$_flag_reason = sprintf($this->l('Please, choose another from/to, because difference between them is too small = %s; from time = %s (%s); to time = %s (%s)'), $diff_time, $from_time, date("Y-m-d H:i:s", $from_time), $to_time, date("Y-m-d H:i:s", $to_time));
		}

		if ($bookingObj->date_from != '0000-00-00') { //Date from
			$available_from = strtotime($bookingObj->date_from);
			if ($available_from > $from_time) {
				$flag = false;
				$_flag_reason = sprintf($this->l('Please, choose bigger `from` param, your `from` param is less than `available from` (%s)'), date("Y-m-d H:i:s", $available_from));
			}
		}
	
		if ($bookingObj->date_to != '0000-00-00') { //Date to
			$available_to = strtotime($bookingObj->date_to);
			if ($available_to < $to_time) {
				$flag = false;
				//$_flag_reason = 'available_to < to_time';
				$_flag_reason = sprintf($this->l('Please, choose less `to` param, your `to` param is bigger than `available to` (%s)'), date("Y-m-d H:i:s", $available_to));
			}
		}
		
		$available_from_time = explode(':', $bookingObj->time_from); //Time from
		if ($available_from_time[0] > 0 || $available_from_time[1] > 0 || $available_from_time[2] > 0) {
			$date1 = new DateTime($human_format);
			$date2 = new DateTime($human_format);
			$date2->setTime($available_from_time[0], $available_from_time[1], $available_from_time[2]);
			if ($date1 < $date2) {
				$flag = false;
				//$_flag_reason = sprintf('available_from_time: %s, %s, %s', $available_from_time[0], $available_from_time[1], $available_from_time[2]);
				$_flag_reason = sprintf($this->l('Please, choose another `from` time param: available from time: %s:%s:%s'), $available_from_time[0], $available_from_time[1], $available_from_time[2]);
			}
			unset($date1);
			unset($date2);
		}
		
		$available_to_time = explode(':', $bookingObj->time_to); //Time to
		if ($available_to_time[0] > 0 || $available_to_time[1] > 0 || $available_to_time[2] > 0) {
			$date1 = new DateTime($human_format);
			$date2 = new DateTime($human_format);
			$date2->setTime($available_to_time[0], $available_to_time[1], $available_to_time[2]);
			if ($date1 > $date2) {
				$flag = false;
				//$_flag_reason = sprintf('available_to_time: %s, %s, %s', $available_to_time[0], $available_to_time[1], $available_to_time[2]);
				$_flag_reason = sprintf($this->l('Please, choose another `to` time param: available to time: %s:%s:%s'), $available_to_time[0], $available_to_time[1], $available_to_time[2]);
			}
			unset($date1);
			unset($date2);
		}
		
		$excluded_days = unserialize($bookingObj->excluded_days);
		if (is_array($excluded_days)) {
			foreach ($excluded_days as $excluded_day) {
				$date1 = new DateTime($human_format);

				switch ($excluded_day['type']) {
					case 'single': //Single day
						$date2 = new DateTime($excluded_day['date']);
						$date1->setTime($date2->format('H'), $date2->format('i'), $date2->format('s'));
						if (!$date2->diff($date1)->format("%a")) {
							//$_flag_reason = 1;
							$_flag_reason = sprintf($this->l('Please, choose another date. This date has been excluded from available booking periods. [reason #1, single]'));
							$flag = false;
						}

						break;
					case 'recurrent_day': //Recurrent day of week
						if ($date1->format("w") == $excluded_day['day']) {
							//$_flag_reason = 2;
							$_flag_reason = sprintf($this->l('Please, choose another date. This date has been excluded from available booking periods. [reason #2, recurrent_day: %s]'), $date1->format("l"));
							$flag = false;
						}

						break;
					case 'period': //Period, from-to
						$date1 = new DateTime($excluded_day['from']);
						$date2 = new DateTime($excluded_day['to']);
						$date3 = new DateTime($human_format);
						if ($date1 > $date3 && $date3 < $date2) {
							//$_flag_reason = 3;
							$_flag_reason = sprintf($this->l('Please, choose another date. This date has been excluded from available booking periods. [reason #3, period: %s - %s]'), $date1->format("Y-m-d H:i:s"), $date2->format("Y-m-d H:i:s"));
							$flag = false;
						}

						break;
					case 'time': //time
						$date1 = new DateTime($human_format);
						
						$date2 = clone($date1);
						$excl_from = explode(':', $excluded_day['from']);
						$date2->setTime($excl_from[0], $excl_from[1], $date2->format('s'));
						
						$date3 = clone($date1);
						$excl_to = explode(':', $excluded_day['to']);
						$date3->setTime($excl_to[0], $excl_to[1], $date3->format('s'));
						
						if ($date1 >= $date2 && $date1 <= $date3) {
							//$_flag_reason = sprintf('excluded_days time [period: %s, from: %s, to: %s]', $date1->format('Y-m-d H:i:s'), $date2->format('Y-m-d H:i:s'), $date3->format('Y-m-d H:i:s'));
							$_flag_reason = sprintf($this->l('Please, choose another time. This time has been excluded from available booking periods. [reason #4, time: %s, from: %s, to: %s]'), $date1->format('Y-m-d H:i:s'), $date2->format('Y-m-d H:i:s'), $date3->format('Y-m-d H:i:s'));
							$flag = false;
						}
						
						break;
					case 'recurrent_date': //Recurrent date
					default:
						$date1 = new DateTime($human_format);
						$date2 = new DateTime($excluded_day['date']);
						$date2->setDate($date1->format('Y'), $date2->format('m'), $date2->format('d'));
						if (!$date2->diff($date1)->format("%a")) {
							//$_flag_reason = 5;
							$_flag_reason = sprintf($this->l('Please, choose another date. This date has been excluded from available booking periods. [reason #5, recurrent_date: %s]'), $date2->format("Y-m-d H:i:s"));
							$flag = false;
						}

						break;
				}
			}
			unset($date1);
			unset($date2);
			unset($date3);

			//single => Single day
			//recurrent_day => Recurrent day of week
			//recurrent_date => Recurrent date
			//period => Period, from-to 
		}
		
		return array(
			'iteration' => $iteration,
			'_flag_reason' => $_flag_reason,
			'flag' => $flag,
		);
	}
	
	public function checkIsBookingDateAvailable($id_product, $from, $to = null, $return_bool = true, $strict_test = false)
	{
		$bookingObj = BestkitBookingProduct::loadByIdProduct($id_product);
		$billable_period = $bookingObj->billable_interval * BestkitBookingProduct::getSecondsByBillablePeriod($bookingObj->qratio_multiplier); //Billable period + Billable interval
		$_flag_reason = sprintf($this->l('Product ID=%s is available'), $id_product);
		$flag = true;

		if (!$bookingObj->id) {
			//$_flag_reason = -1;
			$_flag_reason = sprintf($this->l('Unfortunately, product ID=%s does not available for booking'), $id_product);
			$flag = 0;
		}
		
		/*the $strict_test - check cases: 
		 - is date in the past
		 - is date in the distant future
		 - is date has been already purchased @todo: 
		*/
		if ($strict_test) {
			$prestashop_time = new DateTime();
			$prestashop_time->setTimezone(new DateTimeZone(Configuration::get('PS_TIMEZONE'))); //'Asia/Kuala_Lumpur'
			$from_datetime = new DateTime($from);
			$to_datetime = new DateTime($to);
			
			// - is date in the past
			if ($prestashop_time > $from_datetime) {
				//$_flag_reason = sprintf('the FROM "%s" in the past, server time: "%s"', $from_datetime->format("D, d M Y H:i:s"), $prestashop_time->format("D, d M Y H:i:s"));
				$_flag_reason = sprintf($this->l('Please, choose other FROM date, your current FROM date is in the past: %s. Server time is %s'), $from_datetime->format("D, d M Y H:i:s"), $prestashop_time->format("D, d M Y H:i:s"));
				$flag = 0;
			} else if ($prestashop_time > $to_datetime) {
				//$_flag_reason = sprintf('the TO "%s" in the past, server time: "%s"', $to_datetime->format("D, d M Y H:i:s"), $prestashop_time->format("D, d M Y H:i:s"));
				$_flag_reason = sprintf($this->l('Please, choose other TO date, your current TO date is in the past: %s. Server time is %s'), $to_datetime->format("D, d M Y H:i:s"), $prestashop_time->format("D, d M Y H:i:s"));
				$flag = 0;
			}
			
			// - is date in the distant future
			if ($bookingObj->available_period) {
				$tmp_diff = $prestashop_time->diff($from_datetime);
				if ($tmp_diff->format('%a') > $bookingObj->available_period) {
					//$_flag_reason = sprintf('difference between FROM "%s" and server time "%s" is too large %s', $from_datetime->format("D, d M Y H:i:s"), $prestashop_time->format("D, d M Y H:i:s"), $tmp_diff->format('%a'));
					$_flag_reason = sprintf($this->l('Please, choose other FROM date, difference between FROM "%s" and server time "%s" is too large %s'), $from_datetime->format("D, d M Y H:i:s"), $prestashop_time->format("D, d M Y H:i:s"), $tmp_diff->format('%a'));
					$flag = 0;
				}
				unset($tmp_diff);
				
				$tmp_diff = $prestashop_time->diff($to_datetime);
				if ($tmp_diff->format('%a') > $bookingObj->available_period) {
					//$_flag_reason = sprintf('difference between TO "%s" and server time "%s" is too large %s', $to_datetime->format("D, d M Y H:i:s"), $prestashop_time->format("D, d M Y H:i:s"), $tmp_diff->format('%a'));
					$_flag_reason = sprintf($this->l('Please, choose other TO date, difference between TO "%s" and server time "%s" is too large %s'), $to_datetime->format("D, d M Y H:i:s"), $prestashop_time->format("D, d M Y H:i:s"), $tmp_diff->format('%a'));
					$flag = 0;
				}
				unset($tmp_diff);
			}
			
			// - is date has been already purchased @todo:
			if ($bookingObj->id && $flag) {
				$_flag_reason = '';
				$bo_orders = BestkitBookingOrder::getBookingOrdersByProduct($bookingObj->id_product, 1, $from_datetime->format("Y-m-d H:i:s"), $to_datetime->format("Y-m-d H:i:s"));
				if (count($bo_orders)) {
					$to_time = strtotime($to);
					$from_time = strtotime($from);
					$iteration = $from_time;
					if ($to === null)
						$to_time = $from_time + $billable_period;

					$bo_flag = $bookingObj->quantity;
					do {
						foreach ($bo_orders as $bo_order) {
							$bo_to_time = strtotime($bo_order['from']);
							$bo_from_time = strtotime($bo_order['to']);
							
							if ($bo_to_time >= $iteration && $iteration <= $bo_from_time) {
								//this need for the troubleshooting reasons
								/*$_flag_reason .= sprintf('%s >= %s && %s <= %s [flag=%s]', $bo_to_time, $iteration, $iteration, $bo_from_time, $bo_flag);
								$_flag_reason .= chr(10);
								$_flag_reason .= sprintf('%s >= %s && %s <= %s [flag=%s]', date("Y-m-d H:i:s", $bo_to_time), date("Y-m-d H:i:s", $iteration), date("Y-m-d H:i:s", $iteration), date("Y-m-d H:i:s", $bo_from_time), $bo_flag);
								$_flag_reason .= chr(10) . chr(10);*/
								
								$_flag_reason = sprintf($this->l('Please, choose other date, this date has been already sold'));
								$bo_flag--;
							}
						}
						unset($bo_order);
						
						if ($bo_flag <= 0) {
							$flag = 0;
						}
					
						$iteration += $billable_period;
					} while( $iteration < $to_time && $bo_flag > 0);
				}
			}
		}
		
		if ($flag) {
			$to_time = strtotime($to);
			$from_time = strtotime($from);
			$iteration = $from_time;
			//upd: make the `to` param is not required
			if ($to === null) {
				$to_time = $from_time + $billable_period;
			}

			do {
				$_iteration_results = $this->checkBookingPeriodAvailable($bookingObj, $to_time, $from_time, $iteration);
				
				$iteration = $_iteration_results['iteration'];
				$flag = $_iteration_results['flag'];
				$_flag_reason = $_iteration_results['_flag_reason'];
				
				$iteration += $billable_period;
			} while( $iteration < $to_time );
		}
		
		if ($return_bool) 
			return $flag;
			
		return array(
			'flag' => $flag,
			'flag_reason' => $_flag_reason,
		);
	}

	/**
	* Do not execute this function directly, it is just return the price, but do not check is this ...
	*/
	public function getBookingPrice($id_product, $from, $to = null, $only_price = true)
	{
		$result_price = 0;
		$bookingObj = BestkitBookingProduct::loadByIdProduct($id_product);
		if (!$bookingObj->id) 
			return -1;

		if ($bookingObj->range_type == 'date_fromto') {
			$bookingObj->time_from = '00:00:00';
			$bookingObj->time_to = '23:59:59';
		}

		$billable_period = $bookingObj->billable_interval * BestkitBookingProduct::getSecondsByBillablePeriod($bookingObj->qratio_multiplier); //Billable period + Billable interval
		
		/*if ($this->isValidTimeStamp($to)) 
			$to_time = $to;
		else
			$to_time = strtotime($to);
			
		if ($this->isValidTimeStamp($from)) 
			$from_time = $from;
		else
			$from_time = strtotime($from);*/
		$to_time = strtotime($to);
		$from_time = strtotime($from);
		
		$productObj = new Product($id_product, true, $this->context->language->id);
		$available_array = array();
		$iteration = $from_time;
		//upd: make the `to` param is not required
		if ($to === null) {
			$to_time = $from_time + $billable_period;
		}

		//$available_from_time = explode(':', $bookingObj->time_from); //Time from
		//$available_to_time = explode(':', $bookingObj->time_to); //Time to
		$flag = true;
		$_flag_reason = 0;
		do {
			$_iteration_results = $this->checkBookingPeriodAvailable($bookingObj, $to_time, $from_time, $iteration);

			$iteration = $_iteration_results['iteration'];
			$flag = $_iteration_results['flag'];
			$_flag_reason = $_iteration_results['_flag_reason'];

			$human_format = date('Y-m-d H:i:s', $iteration);
            $_price = $productObj->price;
            $_pricerule_case = 0;
            $_pricerules = BestkitBookingPriceRules::getPriceRulesByIdProduct($productObj->id);
            if (is_array($_pricerules)) {
//print_r($_pricerules);
                foreach ($_pricerules as $_pricerule) {
                    $now_date = new DateTime($human_format);

                    switch ($_pricerule['type']) {
                        case 'from_to_datetime':
                            $date1 = new DateTime($_pricerule['date_from']);
                            $date2 = new DateTime($_pricerule['date_to']);
                            $time1 = explode(':', $_pricerule['time_from']);
                            $time2 = explode(':', $_pricerule['time_to']);
							$date1->setTime($time1[0], $time1[1], $time1[2]);
							$date2->setTime($time2[0], $time2[1], $time2[2]);
							
                            if ($now_date >= $date1 && $now_date <= $date2) {
                                if ($now_date >= $date1 && $now_date <= $date2) 
								{
									$_pricerule_case = 1;
                                    $_price = $_pricerule['price'];
                                }
                            }

                            break;
                        case 'from_to_date':
                            $date1 = new DateTime($_pricerule['date_from']);
                            $date2 = new DateTime($_pricerule['date_to']);
                            if ($now_date >= $date1 && $now_date <= $date2) {
								$_pricerule_case = 2;
                                $_price = $_pricerule['price'];
                            }

                            break;
                        case 'from_to_time':
                            $time1 = explode(':', $_pricerule['time_from']);
                            $time2 = explode(':', $_pricerule['time_to']);
							
							$date1 = clone($now_date);
							$date2 = clone($now_date);
							$date1->setTime($time1[0], $time1[1], $time1[2]);
							$date2->setTime($time2[0], $time2[1], $time2[2]);
/*
print_r($date1); 
print_r($date2); 
print_r($now_date); 
die;
*/
                            if ($now_date >= $date1 && $now_date <= $date2)
                            {
								$_pricerule_case = 3;
                                $_price = $_pricerule['price'];
                            }
                            break;
                        case 'recurrent_day':
                            if ($now_date->format("w") == $_pricerule['day']) {
								$_pricerule_case = 4;
                                $_price = $_pricerule['price'];
                            }

                            break;
                        case 'recurrent_date':
                            $date1 = new DateTime($_pricerule['recurrent_date']);
                            $date1->setDate($now_date->format('Y'), $date1->format('m'), $date1->format('d'));
                            if (!$date1->diff($now_date)->format("%a")) {
								$_pricerule_case = 5;
                                $_price = $_pricerule['price'];
                            }

                            break;
                    }
                    unset($date1);
                    unset($date2);
                    //unset($date3);
                    //unset($date4);
                    unset($time1);
                    unset($time2);
                }
                unset($_pricerule);
            }
			
			$tmp_item = array(
				'flag' => (int)$flag,
				'flag_reason' => pSQL($_flag_reason),
				'iteration' => $iteration,
				'human_format' => $human_format,
				'pricerule_case' => $_pricerule_case,
				'price' => $_price,
			);
			if ($flag)
				$result_price += $tmp_item['price'];
			$available_array[] = $tmp_item;
			
			$iteration += $billable_period;
		} while( $iteration < $to_time );
		unset($tmp_item);
		unset($human_format);
/*
print_r($bookingObj->time_from . chr(10)); 
print_r($bookingObj->time_to . chr(10)); 
print_r($available_array); 
print_r($result_price); 
die;
*/
		if ($only_price)
			return $result_price;
	    else 
			return array(
				'result_price' => $result_price,
				'results' => $available_array,
			);
	}

	/* Alex [end] */
	
	
	
/* @todo: need to review IS IT DEPRECATED FUNCTIONS? [begin] */
	/* return 0/1 */
	public static function isDateAvailable($id_product, array $date_from, array $date_to = array())
	{
		$is_available = self::validateAvailableDate($id_product, $date_from, $date_to);
		if (isset($is_available['available']) && $is_available['available']) {
			return TRUE;
		} 
		
		return FALSE;
	}

	public static function isTimeAvailable($id_product, $date, $hour = 0, $minute = 0)
	{
		$time = strtotime($date);
		$date_from = array(
			'year' => date('Y', $time),
			'day' => date('d', $time),
			'month' => date('m', $time),
			'second' => 0,
			'minute' => $minute,
			'hour' => $hour,
		);

		$is_available = self::validateAvailableDate($id_product, $date_from, $date_from);
		if (isset($is_available['available']) && $is_available['available']) {
			return TRUE;
		}

		return FALSE;
	}
	
	public function jsToPhpDateTime($date_from, $type = 'timestamp')
	{
		switch ($type) {
			case 'h': //hour
				return $date_from[1];
			case 'm': //hour
				return $date_from[2];
			default:
				return $date_from[0] / 1000; //to do: make some actions with this value, for example: x=x/3600, etc.
		}
	}
	
	public static function build_mktime($date)
	{
		$mktime = mktime($date['hour'], $date['minute'], $date['second'], $date['month'], $date['day'], $date['year']);
		return $mktime;
	}

	public static function build_DateTime_obj($mktime)
	{
		return new DateTime(date("Y-m-d H:i:s", $mktime));	
	}

	/*
		$date_from => 
		Array
		(
			[y] => y
			[m] => m
			[d] => d
	        ...
		)
	*/
	public static function validateAvailableDate($id_product, array $date_from, array $date_to)
	{//The Date interval is small!
		$module_instance = new self();
        //booking should be active for this product [begin]
		$bookingObj = BestkitBookingProduct::loadByIdProduct($id_product);
		if (!isset($bookingObj->id) || !($bookingObj->active)) {
			return array(
				'available' => FALSE,
				'reason' => $module_instance->l('Sorry, booking isn\'t available for this product'),
			);
		}
        //booking should be active for this product [end]

        //from-to should be inside date_from -> date_to interval [begin]
        $mktime_from = self::build_mktime($date_from);
        $mktime_to = self::build_mktime($date_to);
		$dt_choose_from = self::build_DateTime_obj($mktime_from);
		$dt_choose_to = self::build_DateTime_obj($mktime_to);
        if (strtotime($bookingObj->date_from) > 0 || strtotime($bookingObj->date_to) > 0 || $bookingObj->time_from != '00:00:00' || $bookingObj->time_to != '00:00:00') {
            $bookingObj->date_from = date('Y-m-d', strtotime($bookingObj->date_from));
            $bookingObj->date_to = date('Y-m-d', strtotime($bookingObj->date_to));

            if ($bookingObj->range_type == 'date_fromto') {
                $mktime_from_product = strtotime($date_from['year'] . '-' . $date_from['month'] . '-' . $date_from['day'] . ' 00:00:00');
                $mktime_to_product = strtotime($date_to['year'] . '-' . $date_to['month'] . '-' . $date_to['day'] . ' 23:59:59');

				$dt_allow_from = self::build_DateTime_obj($mktime_from_product);
				$dt_allow_to = self::build_DateTime_obj($mktime_to_product);
				$dt_allow_str = $module_instance->l('from') . ' ' . $dt_allow_from->format('Y-m-d H:i') . $module_instance->l('to') . ' ' . $dt_allow_to->format('Y-m-d H:i');
            } elseif ($bookingObj->range_type == 'time_fromto') {
                $mktime_from_product = strtotime($dt_choose_from->format('Y-m-d') . ' ' . $bookingObj->time_from);
                $mktime_to_product = strtotime($dt_choose_to->format('Y-m-d') . ' ' . $bookingObj->time_to);

				$dt_allow_from = self::build_DateTime_obj($mktime_from_product);
				//$dt_allow_to = self::build_DateTime_obj($mktime_to_product);
				$dt_allow_str = $module_instance->l('from') . ' ' . $dt_allow_from->format('Y-m-d H:i') . ' ' . $module_instance->l('to') . ' ' . $dt_allow_from->format('Y-m-d H:i');
            } elseif ($bookingObj->range_type == 'datetime_fromto') {
                $mktime_from_product = strtotime($bookingObj->date_from . ' ' . $bookingObj->time_from);
                $mktime_to_product = strtotime($bookingObj->date_to . ' ' . $bookingObj->time_to);

				$dt_allow_from = self::build_DateTime_obj($mktime_from_product);
				$dt_allow_to = self::build_DateTime_obj($mktime_to_product);
				$dt_allow_str = $module_instance->l('from') . ' ' . $dt_allow_from->format('Y-m-d H:i') . $module_instance->l('to') . ' ' . $dt_allow_to->format('Y-m-d H:i');
            }

            if (!($mktime_from_product <= $mktime_from && $mktime_to_product >= $mktime_to)) {
                return array(
                    'available' => FALSE,
                    'reason' => $module_instance->l('Sorry, your date or time is out of the allowed range.') . ' ' . $module_instance->l('Your have choose from ') . $dt_choose_from->format('Y-m-d H:i') . ' ' . $module_instance->l('to') . ' ' . $dt_choose_to->format('Y-m-d H:i') . ', ' . $module_instance->l('but we are allow: ') . $dt_allow_str,
                );
            }
        }
        //from-to should be inside date_from -> date_to interval [end]

        //booking interval should be more than the `billable_interval` param [begin]
        if ($bookingObj->billable_interval) {
            $tmp_mktime_diff = $mktime_to - $mktime_from;
            if ($bookingObj->qratio_multiplier == 'days') {
                $tmp_mktime_diff = $tmp_mktime_diff / 86400 /*% 7*/; //days
            } elseif ($bookingObj->qratio_multiplier == 'hours') {
                $tmp_mktime_diff = $tmp_mktime_diff / 3600 /*% 24*/; //hours
            } elseif ($bookingObj->qratio_multiplier == 'minutes') {
                $tmp_mktime_diff = $tmp_mktime_diff / 60 /*% 60*/; //minutes
            }

            if ($tmp_mktime_diff < $bookingObj->billable_interval) {
                return array(
                    'available' => FALSE,
                    'reason' => $module_instance->l('Sorry, minimal avaliable period = ') . $bookingObj->billable_interval,
                );
            }
        }
        //booking interval should be more than the `billable_interval` param [end]

		
		return array(
			'available' => TRUE,
			'reason' => NULL,
		);
	}

/* @todo: need to review IS IT DEPRECATED FUNCTIONS? [end] */

    public function hookDisplayHeader($params)
    {
        return $this->display(__FILE__, 'header.tpl');
    }

    public function buildExcludedDaysControl()
    {
        $id_product = Tools::getValue('id_product');
        $booking_obj = BestkitBookingProduct::loadByIdProduct($id_product);
        $this->context->smarty->assign(array(
			'booking_excludeddays' => unserialize($booking_obj->excluded_days),
		));
        $tpl_path = $this->getTemplate('admin', 'buildExcludedDaysControl.tpl');
        $data = $this->context->smarty->createTemplate($tpl_path, $this->context->smarty);
        return $data->fetch();
    }

    public function buildPriceRulesControl()
    {
        $id_product = Tools::getValue('id_product');
        $_pricerules = BestkitBookingPriceRules::getPriceRulesByIdProduct($id_product);
        $this->context->smarty->assign(array(
			'booking_pricerules' => $_pricerules,
		));
        $tpl_path = $this->getTemplate('admin', 'buildPriceRulesControl.tpl');
        $data = $this->context->smarty->createTemplate($tpl_path, $this->context->smarty);
        return $data->fetch();
    }

	protected function getMapFormContent($booking_obj) {
		$tpl_path = $this->getTemplate('admin', 'buildMapForm.tpl');
        $this->context->smarty->assign(array(
			'booking_map' => array(
				'show_map' => $booking_obj->show_map,
				'address1' => $booking_obj->address1,
				'latitude' => $booking_obj->latitude,
				'longitude' => $booking_obj->longitude,
				'zoom' => $booking_obj->zoom,
			),
		));
		$data = $this->context->smarty->createTemplate($tpl_path, $this->context->smarty);
		return $data->fetch();
    }

    public function hookDisplayAdminProductsExtra()
    {
        $id_product = Tools::getValue('id_product');
        if ($id_product) {
        	$_product = new Product($id_product);
        	if (!$_product->is_virtual) {
	        	return $this->l('The booking can be configured with Virtual products only.');
        	}

	        $booking_obj = BestkitBookingProduct::loadByIdProduct($id_product);
	        $booking_obj->time_from = explode(':', $booking_obj->time_from);
	        $booking_obj->time_to = explode(':', $booking_obj->time_to);
	        $this->smarty->assign(array(
	            'booking_id_product' => $id_product,
	            'booking_obj' => $booking_obj,
	            'booking_excludeddays_html' => $this->buildExcludedDaysControl(),
	            'booking_pricerules_html' => $this->buildPriceRulesControl(),
	            'booking_map_settings' => $this->getMapFormContent($booking_obj),
	        ));

	        return $this->display(__FILE__, 'productAdminTab.tpl');
		}

		return $this->l('Please save this product to continue');
    }

	public function hookdisplayProductButtons($params)
	{
		$html = false;
		$id_product = (int)Tools::getValue('id_product');
		$booking_obj = BestkitBookingProduct::loadByIdProduct($id_product);

		if (isset($booking_obj->id) && $booking_obj->active) {
	        $this->context->controller->addJqueryUI('ui.datepicker');
	        //$this->context->controller->addCss($this->_path . 'css/jquery.datetimepicker.css', 'all');
	        //$this->context->controller->addJs($this->_path . 'js/jquery.datetimepicker.full.min.js');

	        $this->context->controller->addCss($this->_path . 'views/css/front.css', 'all');
	        $this->context->controller->addJs($this->_path . 'views/js/front.js');

			$exclude_weekdays = array();
			$exclude_dates = array();
			$exclude_recurrent_dates = array();
			$exclude_periods = array();

			$excludes = $booking_obj->getExcludedDays();

			foreach ($excludes as $exclude) {
				if ($exclude['type'] == 'recurrent_day') {
					$exclude_weekdays[] = (int)$exclude['day'];
				}

				if ($exclude['type'] == 'single') {
					$exclude_dates[] = date(self::PHP_DATE_FORMAT, strtotime($exclude['date']));
				}

				if ($exclude['type'] == 'recurrent_date') {
					$datetime = strtotime($exclude['date']);
					$exclude_recurrent_dates[] = array(
						'month' => (int)date('m', $datetime),
						'day' => (int)date('d', $datetime),
					);
				}

				if ($exclude['type'] == 'period') {
					$exclude_periods[] = array(
						'from' => date(self::PHP_DATE_FORMAT, strtotime($exclude['from'])),
						'to' => date(self::PHP_DATE_FORMAT, strtotime($exclude['to'])),
					);
				}
			}

			$date_from = strtotime($booking_obj->date_from);
			$date_from = (time() < $date_from ? date(self::PHP_DATE_FORMAT, $date_from) : 0);
			$date_to = strtotime($booking_obj->date_to);
			$max_day = strtotime('+' . (int)$booking_obj->available_period . ' days', time());
			if ($date_to < time() || $date_to > $max_day) {
				$date_to = $booking_obj->available_period;
			} else {
				$date_to = date(self::PHP_DATE_FORMAT, $date_to);
			}

			$this->context->smarty->assign(array('bestkit_booking' => array(
				'id_product' => $id_product,
				'range_type' => $booking_obj->range_type, //date_fromto, time_fromto, datetime_fromto
				'billing_type' => $booking_obj->qratio_multiplier, //days, hours, minutes
				'max_day' => $booking_obj->available_period,
				'time_from' => $booking_obj->time_from,
				'time_to' => $booking_obj->time_to,
				'date_from' => $date_from,
				'date_to' => $date_to,
				'interval' => ($booking_obj->billable_interval ? $booking_obj->billable_interval : 1),
				'exclude_weekdays' => Tools::jsonEncode($exclude_weekdays),
				'exclude_dates' => Tools::jsonEncode($exclude_dates),
				'exclude_recurrent_dates' => Tools::jsonEncode($exclude_recurrent_dates),
				'exclude_periods' => Tools::jsonEncode($exclude_periods),
				'booked_days' => Tools::jsonEncode(array('04/11/2016', '04/19/2016')),
				'module_path' => $this->_path,
				'current_day' => strtotime(date('d-m-Y', time())) * 1000,
				'module' => $this,
				'booking_obj' => $booking_obj,
				'api_key' => $this->getConfig('api_key'),
			)));

			if (isset($params['timeContainer'])) {
				$choosed_days = array($params['date_from'], $params['date_to']);
				$this->context->smarty->assign(array('choosed_date' => array(
					'from' => $params['date_from'],
					'to' => $params['date_to'],
					'days' => $choosed_days,
				)));

				$html = $this->display(__FILE__, 'timeContainer.tpl');
			} else {
				$html = $this->display(__FILE__, 'productPage.tpl');
			}
		}

		return $html;
	}

    public function hookActionProductUpdate()
    {
        if (in_array('Bestkit_Booking', Tools::getValue('submitted_tabs'))) {
            $id_product = Tools::getValue('id_product');
            $booking_post = Tools::getValue('bestkit_booking');
            $return = TRUE;

            if (!empty($booking_post) && is_array($booking_post) && $booking_post['active'] && $id_product) {
                $booking_obj = BestkitBookingProduct::loadByIdProduct($id_product);
                $booking_obj->id_product = $id_product;
                $booking_obj->quantity = $booking_post['quantity'];
                $booking_obj->date_from = $booking_post['date_from'];
                $booking_obj->date_to = $booking_post['date_to'];
                $booking_obj->range_type = $booking_post['range_type'];
                $booking_obj->time_from = implode(":", $booking_post['time_from']);
                $booking_obj->time_to = implode(":", $booking_post['time_to']);
                $booking_obj->qratio_multiplier = $booking_post['qratio_multiplier'];
                $booking_obj->available_period  = $booking_post['available_period'];
                $booking_obj->billable_interval  = $booking_post['billable_interval'];
                $booking_obj->show_map  = $booking_post['show_map'];
                $booking_obj->address1  = $booking_post['address1'];
                $booking_obj->latitude  = $booking_post['latitude'];
                $booking_obj->longitude  = $booking_post['longitude'];
                $booking_obj->zoom  = $booking_post['zoom'];
                $booking_obj->active = $booking_post['active'];

				//validation 
				$date_from = new DateTime($booking_obj->date_from);
				$date_to = new DateTime($booking_obj->date_to);
				if ($date_to->format('U') > 0 && $date_from > $date_to) 
					throw new PrestaShopException(sprintf($this->l('Date to %s should be greater then date from %s'), $booking_obj->date_to, $booking_obj->date_from));
				
//print_r($booking_post); die;

                //excluded days
                $excludeddays_arr = array();
                $excludeddays = $booking_post['excludeddays'];
                for ($i = 1; $i < count($excludeddays['period_type']); $i++) {
                    switch ($excludeddays['period_type'][$i]) {
                        case 'period':
                            $excludeddays_arr[] = array(
                                'type' => 'period',
                                'from' => $excludeddays['date_1'][$i],
                                'to' => $excludeddays['date_2'][$i]
                            );
                            break;
                        case 'recurrent_date':
                            $excludeddays_arr[] = array(
                                'type' => 'recurrent_date',
                                'date' => $excludeddays['date_1'][$i],
                            );
                            break;
                        case 'recurrent_day':
                            $excludeddays_arr[] = array(
                                'type' => 'recurrent_day',
                                'day' => $excludeddays['recurrent_day'][$i],
                            );
                            break;
                        case 'time':
                            $excludeddays_arr[] = array(
                                'type' => 'time',
                                'from' => $excludeddays['time_from'][$i],
                                'to' => $excludeddays['time_to'][$i]
                            );
                            break;
                        case 'single':
                        default:
                            $excludeddays_arr[] = array(
                                'type' => 'single',
                                'date' => $excludeddays['date_1'][$i],
                            );
                    }
                }
                $booking_obj->excluded_days = serialize($excludeddays_arr);

                $booking_obj->save();
//print_r($booking_obj); die;

				if ($booking_obj->id) {
					//price rules
					BestkitBookingPriceRules::clearByIdProduct($id_product);
					$price_rules = $booking_post['pricerules'];
					for ($i = 1; $i < count($price_rules['price']); $i++) {
						$priceRuleObj = new BestkitBookingPriceRules();
						$priceRuleObj->id_bestkit_booking_product = $booking_obj->id;
						$priceRuleObj->date_from = $price_rules['date_from'][$i];
						$priceRuleObj->date_to = $price_rules['date_to'][$i];
						$priceRuleObj->time_from = $price_rules['time_from'][$i];
						$priceRuleObj->time_to = $price_rules['time_to'][$i];
						$priceRuleObj->day = $price_rules['day'][$i];
						$priceRuleObj->recurrent_date = $price_rules['recurrent_date'][$i];
						$priceRuleObj->type = $price_rules['type'][$i];
						$priceRuleObj->price = $price_rules['price'][$i];
//print_r($priceRuleObj); die;
						$priceRuleObj->add();
					}
				}
            }
//print_r($_POST);
//print_r($booking_post); 
//print_r($price_rules); die;

            return $return;
        }
    }

	public function hookActionDispatcher($params)
	{
		if (isset($params['controller_class']) && $params['controller_class'] == 'CartController') {
			if (Tools::getValue('add')) {
				$id_product = (int)Tools::getValue('id_product');
				$id_product_attribute = (int)Tools::getValue('id_product_attribute', Tools::getValue('ipa'));

				$hasInCart = (int)Db::getInstance()->getValue('
					SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'bestkit_booking_order`
					WHERE `id_cart` = ' . (int)$params['cookie']->id_cart . '
					AND `id_product` = ' . (int)$id_product . '
					AND `id_product_attribute` = ' . (int)$id_product_attribute . '
				');

				if (!Tools::getIsset('bestkit_booking') && $hasInCart > 0) {
					return null;
				}

				$booking_obj = BestkitBookingProduct::loadByIdProduct($id_product);
				if (isset($booking_obj->id) && $booking_obj->active) {
					if (Tools::getIsset('bestkit_booking')) {
						$from = (int)Tools::getValue('from');
						$to = (int)Tools::getValue('to');

						if ($from && $to) {

							$from = date('Y-m-d H:i:s', $this->jsToPhpDateTime(array($from)));
							$to = date('Y-m-d H:i:s', $this->jsToPhpDateTime(array($to)));

							$is_available = $this->checkIsBookingDateAvailable($id_product, $from, $to, false);

							if (!$is_available['flag']) {
								$return = array(
									'hasError' => true,
									'errors' => array($is_available['flag_reason']),
								);

								die(Tools::jsonEncode($return));
							} else {
								Db::getInstance()->Execute('
									DELETE FROM `' . _DB_PREFIX_ . 'cart_product`
									WHERE `id_cart` = ' . (int)$params['cookie']->id_cart . '
									AND `id_product` = ' . (int)$id_product . '
									AND `id_product_attribute` = ' . (int)$id_product_attribute . '
								');
							}
						}
					} else {
						$product_page = Context::getContext()->link->getProductLink($id_product);
						if (Tools::getIsset('ajax')) {
							die('<script>$.fancybox.close(); window.location="' . $product_page . '";</script>');
						} else {
							Tools::redirectAdmin($product_page);
						}
					}
				}
			}
		}
	}

	public function hookActionCartSave($params)
	{
		if (Tools::getIsset('bestkit_booking') || Tools::getIsset('delete')) {
			if (isset($params['cart'])) {
				$cart = $params['cart'];
			} else if (isset($params['object'])) {
				$cart = $params['object'];
			} else if (isset(Context::getContext()->cart->id)) {
				$cart = Context::getContext()->cart;
			} else {
				return null;
			}

			$id_product = (int)Tools::getValue('id_product', null);
			$id_product_attribute = (int)Tools::getValue('id_product_attribute', Tools::getValue('ipa'));

			if (Tools::getIsset('delete')) {

				Db::getInstance()->Execute('
					DELETE FROM `' . _DB_PREFIX_ . 'bestkit_booking_order`
					WHERE `id_cart` = ' . (int)$cart->id . '
					AND `id_product` = ' . (int)$id_product . '
					AND `id_product_attribute` = ' . (int)$id_product_attribute . '
				');

			} else {

				$booking_obj = BestkitBookingProduct::loadByIdProduct($id_product);
				if (isset($booking_obj->id) && $booking_obj->active) {
					$from = (int)Tools::getValue('from');
					$to = (int)Tools::getValue('to');
	
					if ($from && $to) {
						$from = $this->jsToPhpDateTime(array($from));
						$to = $this->jsToPhpDateTime(array($to));

						Db::getInstance()->Execute('
							DELETE FROM `' . _DB_PREFIX_ . 'bestkit_booking_order`
							WHERE `id_cart` = ' . (int)$cart->id . '
							AND `id_product` = ' . (int)$id_product . '
							AND `id_product_attribute` = ' . (int)$id_product_attribute . '
						');

						$data = array(
							'id_cart' => $cart->id,
							'id_product' => $id_product,
							'id_product_attribute' => $id_product_attribute,
							'from' => date('Y-m-d H:i:s', $from),
							'to' => date('Y-m-d H:i:s', $to),
							'range_type' => $booking_obj->range_type,
							'qratio_multiplier' => $booking_obj->qratio_multiplier,
							'billable_interval' => $booking_obj->billable_interval,
						);
	
						Db::getInstance()->autoExecute(_DB_PREFIX_ . 'bestkit_booking_order', $data, 'INSERT');
					}
				}
			}
		}
	}

	public function hookDisplayShoppingCart($params)
	{
		$cart = Context::getContext()->cart;

		$bookings = Db::getInstance()->ExecuteS('
			SELECT * FROM `' . _DB_PREFIX_ . 'bestkit_booking_order`
			WHERE `id_cart` = ' . (int)$cart->id
		);

		if (count($bookings)) {
			$this->context->controller->addJs($this->_path . 'views/js/cart.js');
			
			foreach ($bookings as &$booking) {
				$from = $booking['from'];
				$to = $booking['to'];

				if (in_array($booking['range_type'], array('time_fromto', 'datetime_fromto'))) {
					$booking['from'] = Tools::displayDate($from, null, true);
					$booking['to'] = Tools::displayDate($to, null, true);
				} else {
					$booking['from'] = Tools::displayDate($from);
					$booking['to'] = Tools::displayDate($to);
				}
			}

			$this->context->smarty->assign('bestkit_bookings', $bookings);
			return $this->display(__FILE__, 'shopping-cart.tpl');
		}
	}
	
	public function hookDisplayAdminOrder($params)
    {
		$this->context->smarty->assign(array('bestkit_booking' => array(
			'info' => BestkitBookingOrder::getBookingOrdersByOrder($params['id_order']),
			'module' => $this,
		)));
		
		return $this->display(__FILE__, 'displayAdminOrder.tpl');
	}
	
	public function getHumanRangeType($range_type)
	{
		switch($range_type) {
			case 'date_fromto':
				return $this->l('Date');
			case 'time_fromto':
				return $this->l('Time');
			case 'datetime_fromto':
				return $this->l('Date and Time');
		}
	}
	
	public function getHumanBillablePeriod($qratio_multiplier)
	{
		switch($qratio_multiplier) {
			case 'days':
				return $this->l('Day(s)');
			case 'hours':
				return $this->l('Hour(s)');
			case 'minutes':
				return $this->l('Minute(s)');
		}
	}
}
