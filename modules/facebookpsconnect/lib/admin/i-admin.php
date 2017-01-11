<?php
/**
 * i-admin.php file defines mandatory method to manage module's admin
 */
interface BT_IAdmin
{
	/**
	 * run() method process display or updating or etc ... admin
	 * @param mixed $aParam => $_GET or $_POST
	 * @category admin collection
	 * @return bool
	 */
	public function run(array $aParam = null);
}