<?php
class MarketplaceValidateUniqueShopModuleFrontController extends ModuleFrontController
{
	public function init()
	{
		$this->display_header = false;
		$this->display_footer = false;
	}

	public function initContent()
	{
		$shop_name = Tools::getValue('shop_name');
		$seller_email = Tools::getValue('seller_email');
		$id_seller = Tools::getValue('id_seller');

		if ($shop_name)
		{
			if (SellerInfoDetail::isShopNameExist($shop_name, $id_seller))
				echo 1;
			else
			{
				if (!Validate::isCatalogName($shop_name))
					echo 2;
				else
					echo 0;
			}
		}

		if ($seller_email)
		{
			if (SellerInfoDetail::isSellerEmailExist($seller_email, $id_seller))
				echo 1;
			else
				echo 0;
		}
		
	}
}