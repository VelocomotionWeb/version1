<?php
/**
* Copyright (c) 2014 - Leny GRISEL
*
*  @author    Leny GRISEL <email@lenygrisel.com>
*  @copyright 2014 Leny GRISEL
*  @license   Leny GRISEL - All rights reserved.
*  International Registered Trademark & Property of Leny GRISEL
*/

class YetanotheradvancedsearchReindexModuleFrontController extends ModuleFrontController {

	/**
	 * Main entry point.
	 */
	public function initContent()
	{
		$token = Tools::GetValue('token');
		$waited = YetAnotherAdvancedSearchModel::getConfig(CriteriaConfigEnum::URL_UPDATE);
		if ($token == $waited)
		{
			$yaas = new YetAnotherAdvancedSearch();
			$time1 = time();
			YetAnotherAdvancedSearchModel::reindex($yaas);
			$time2 = time();
			echo 'Ok\nReindexed in '.($time2 - $time1).'s';
		}
		else echo 'forbidden';
		die();
	}

}

?>
