<?php
class MpcombinationGroupTypeColorModuleFrontController extends ModuleFrontController
{
	public function init()
	{
		$this->display_header = false;
		$this->display_footer = false;
	}

	public function initContent()
	{
		$group_id = Tools::getValue('group_id');
		$flag = Mpproductattribute::ifColorAttributegroup($group_id);
		if ($flag)
			die("1");
		die("0");
	}
}
?>