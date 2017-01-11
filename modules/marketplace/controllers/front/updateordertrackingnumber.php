<?php
class MarketplaceUpdateOrderTrackingNumberModuleFrontController extends ModuleFrontController
{
	public function initContent()
	{
		$id_order = Tools::getValue('id_order');
		$tracking_number = Tools::getValue('tracking_number');
		$id_order_carrier = Tools::getValue('id_order_carrier');

		if (isset($id_order) && $id_order != "" && isset($tracking_number) && $tracking_number != "" && isset($id_order_carrier) && $id_order_carrier != "")
		{
			$order = new Order($id_order);
			$order_carrier = new OrderCarrier($id_order_carrier);
			// update shipping number
			$order->shipping_number = Tools::getValue('tracking_number');
			$is_order_updated = $order->update();

			// Update order_carrier
			$order_carrier->tracking_number = pSQL(Tools::getValue('tracking_number'));
			$is_carrier_updated = $order_carrier->update();
			if ($is_order_updated && $is_carrier_updated)
				die(true);
			die(false);
		}
	}
}
?>