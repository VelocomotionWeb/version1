<?php
/**
 * i-hook.php file defines mandatory method to manage hook's display
 */
interface BT_IFpcHook
{
	/**
	 * run() method execute content in different site part
	 *
	 * @return bool
	 */
	public function run();
}