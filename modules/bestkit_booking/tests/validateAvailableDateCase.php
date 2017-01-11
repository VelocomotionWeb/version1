<?php

include_once(dirname(__FILE__) . '/../../../config/config.inc.php');
require_once dirname(__FILE__) . '/../includer.php';

Context::getContext()->cart = new Cart(1);
Context::getContext()->currency = new Currency(1);
$module = Module::getInstanceByName('bestkit_booking');

/*price*/
$b_items = array(
	array('id' => 1, 'date_from' => '2016-03-25 00:00:00', 'date_to' => '2016-03-25 00:00:00', 'expected' => -1), 
	array('id' => 25, 'date_from' => '2016-03-25 12:00:00', 'date_to' => '2016-03-25 13:00:00', 'expected' => 40), 
	array('id' => 25, 'date_from' => '2016-03-25 12:30:00', 'date_to' => '2016-03-25 13:00:00', 'expected' => 20), 
	array('id' => 25, 'date_from' => '2016-03-25 12:45:00', 'date_to' => '2016-03-25 13:00:00', 'expected' => 10), 
	//array('id' => 25, 'date_from' => strtotime('2016-03-25 12:45:00'), 'date_to' => strtotime('2016-03-25 13:00:00'), 'expected' => 10), 
	array('id' => 25, 'date_from' => '2016-04-08 8:00:00', 'date_to' => '2016-04-08 9:00:00', 'expected' => 0), //unavailable for this time
	array('id' => 25, 'date_from' => '2016-04-08 8:00:00', 'date_to' => '2016-04-08 9:10:00', 'expected' => 10), //available only 10 mins
	array('id' => 25, 'date_from' => '2016-04-08 8:00:00', 'date_to' => '2016-04-08 9:15:00', 'expected' => 10), //available only 15 mins
	array('id' => 25, 'date_from' => '2016-04-08 8:45:00', 'date_to' => '2016-04-08 11:00:00', 'expected' => 80),  //unavailable part of this time
	array('id' => 25, 'date_from' => '2016-03-31 12:00:00', 'date_to' => '2016-03-31 13:00:00', 'expected' => 0), // unavailable date / -1
	array('id' => 25, 'date_from' => '2016-03-31 12:00:00', 'date_to' => '2016-03-31 11:00:00', 'expected' => 0),  //date_to < date_from
	array('id' => 25, 'date_from' => '2015-03-31 12:00:00', 'date_to' => '2015-03-31 11:00:00', 'expected' => 0), //expired
	array('id' => 25, 'date_from' => '2016-03-28 17:00:00', 'date_to' => '2016-03-28 19:00:00', 'expected' => 24), //because of `2016-03-28` has special price = 3
	array('id' => 25, 'date_from' => '2016-03-28 17:00:00', 'date_to' => '2016-03-31 19:00:00', 'expected' => 1380), 
	array('id' => 25, 'date_from' => '2016-03-28 17:00:00', 'date_to' => null, 'expected' => 3), 
	array('id' => 26, 'date_from' => '2016-04-10 17:00:00', 'date_to' => '2016-04-10 17:30:00', 'expected' => 20), 
	array('id' => 26, 'date_from' => '2016-04-11 23:00:00', 'date_to' => '2016-04-12 02:00:00', 'expected' => 90), 
	array('id' => 26, 'date_from' => '2016-04-11 23:00:00', 'date_to' => '2016-04-12 03:00:00', 'expected' => 90), 
	array('id' => 27, 'date_from' => '2016-04-20 00:00:00', 'date_to' => '2016-04-20 00:00:01', 'expected' => 84.745763), 
);
foreach ($b_items as $b_item) {
	$price = $module->getBookingPrice($b_item['id'], $b_item['date_from'], $b_item['date_to']);
	
	if ($price == $b_item['expected']) {
		print_r(sprintf('[PASS] Result: %s for product #%s, from: %s, to: %s', $price, $b_item['id'], $b_item['date_from'], $b_item['date_to'])); 
	} else {
		print_r(sprintf('[FAIL] Result: %s, but expected %s for product #%s, from: %s, to: %s', $price, $b_item['expected'], $b_item['id'], $b_item['date_from'], $b_item['date_to'])); 
		print_r(chr(10)); 
		print_r($module->getBookingPrice($b_item['id'], $b_item['date_from'], $b_item['date_to'], false));
	}
	print_r(chr(10) . chr(10)); 
}
unset($b_item);


/*isAvailable*/
print_r(chr(10) . chr(10) . 'isAvailable:' . chr(10) . chr(10)); 
$b_items = array(
	array('id' => 1, 'date_from' => '2016-03-25 00:00:00', 'date_to' => '2016-03-25 00:00:00', 'expected' => 0), 
	array('id' => 26, 'date_from' => '2016-04-11 23:00:00', 'date_to' => '2016-04-12 03:00:00', 'expected' => 0), 
	array('id' => 26, 'date_from' => '2016-04-07 23:00:00', 'date_to' => '2016-04-07 23:30:00', 'expected' => 1), 
	//array('id' => 26, 'date_from' => strtotime('2016-04-07 23:00:00'), 'date_to' => strtotime('2016-04-07 23:30:00'), 'expected' => 1), 
	array('id' => 26, 'date_from' => '2016-04-06 23:00:00', 'date_to' => '2016-04-06 23:30:00', 'expected' => 1), 
	array('id' => 26, 'date_from' => '2016-04-17 23:00:00', 'date_to' => '2016-04-18 1:30:00', 'expected' => 1),  //future
	array('id' => 27, 'date_from' => '2016-04-20 00:00:00', 'date_to' => '2016-04-20 00:00:01', 'expected' => 1), 
	array('id' => 27, 'date_from' => '2016-04-20 00:00:00', 'date_to' => '2016-04-20 00:00:00', 'expected' => 0), 
	array('id' => 27, 'date_from' => '2016-04-20 00:00:00', 'date_to' => null, 'expected' => 1), 
);
foreach ($b_items as $b_item) {
	$available_flag = $module->checkIsBookingDateAvailable($b_item['id'], $b_item['date_from'], $b_item['date_to']);

	if ($available_flag == $b_item['expected']) {
		print_r(sprintf('[PASS] Result: %s for product #%s, from: %s, to: %s', (int)$available_flag, $b_item['id'], $b_item['date_from'], $b_item['date_to'])); 
	} else {
		print_r(sprintf('[FAIL] Result: %s, but expected %s for product #%s, from: %s, to: %s', (int)$available_flag, (int)$b_item['expected'], $b_item['id'], $b_item['date_from'], $b_item['date_to'])); 
		print_r(chr(10)); 
		print_r($module->checkIsBookingDateAvailable($b_item['id'], $b_item['date_from'], $b_item['date_to'], false));
	}

	print_r(chr(10) . chr(10)); 
}
unset($b_item);


/*isAvailableForPurchase*/
print_r(chr(10) . chr(10) . 'isAvailableForPurchase:' . chr(10) . chr(10)); 
$b_items = array(
	array('id' => 1, 'date_from' => '2016-03-25 00:00:00', 'date_to' => '2016-03-25 00:00:00', 'expected' => 0),  //past
	array('id' => 26, 'date_from' => '2016-04-11 23:00:00', 'date_to' => '2016-04-12 03:00:00', 'expected' => 0),  //unavailable time 
	array('id' => 26, 'date_from' => '2016-04-06 23:00:00', 'date_to' => '2016-04-06 23:30:00', 'expected' => 0),  //past 
	array('id' => 26, 'date_from' => '2016-04-06 23:00:00', 'date_to' => '2016-04-06 23:30:00', 'expected' => 0),  //past
	array('id' => 26, 'date_from' => '2016-04-17 23:00:00', 'date_to' => '2016-04-17 23:30:00', 'expected' => 1),  //future 
	array('id' => 26, 'date_from' => '2016-04-17 23:00:00', 'date_to' => '2016-04-17 2:30:00', 'expected' => 0),  //future , but unavailable time exists
	array('id' => 26, 'date_from' => '2016-04-17 23:00:00', 'date_to' => '2016-04-18 2:00:00', 'expected' => 1),  //future
	array('id' => 26, 'date_from' => '2017-04-17 23:00:00', 'date_to' => '2017-04-18 2:00:00', 'expected' => 0),  //not near future
	array('id' => 26, 'date_from' => '2016-04-27 00:40:00', 'date_to' => '2016-04-27 01:40:00', 'expected' => 1),  //PART ORDERED, QTY allowed for booking = 3
	array('id' => 26, 'date_from' => '2016-04-27 00:40:00', 'date_to' => '2016-04-28 00:00:00', 'expected' => 1),  //ORDERED, QTY allowed for booking = 3
);
foreach ($b_items as $b_item) {
	$available_flag = $module->checkIsBookingDateAvailable($b_item['id'], $b_item['date_from'], $b_item['date_to'], true, true);

	if ($available_flag == $b_item['expected']) {
		print_r(sprintf('[PASS] Result: %s for product #%s, from: %s, to: %s', (int)$available_flag, $b_item['id'], $b_item['date_from'], $b_item['date_to'])); 
	} else {
		print_r(sprintf('[FAIL] Result: %s, but expected %s for product #%s, from: %s, to: %s', (int)$available_flag, (int)$b_item['expected'], $b_item['id'], $b_item['date_from'], $b_item['date_to'])); 
		print_r(chr(10)); 
		print_r($module->checkIsBookingDateAvailable($b_item['id'], $b_item['date_from'], $b_item['date_to'], false, true));
	}

	print_r(chr(10) . chr(10)); 
}
unset($b_item);

die('~');

/*
 insert into `dev_bestkit_booking_order`
(id_cart, date_from, date_to, time_from, time_to, range_type, qratio_multiplier, billable_interval)
VALUES (58, '2016-05-10', '2016-05-10', '10:20:00', '11:00:00', 'datetime_fromto', 'minutes', 20)
 */