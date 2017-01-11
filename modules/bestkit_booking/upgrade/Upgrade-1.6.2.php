<?php

/*
* File: /upgrade/Upgrade-1.6.2.php
*/
function upgrade_module_1_6_2($object) {
	return $object->registerHook('displayAdminOrder');
}