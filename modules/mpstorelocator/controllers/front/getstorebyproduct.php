<?php
class mpstorelocatorGetStorebyProductModuleFrontController extends ModuleFrontController
{
    public function init()
    {
        $this->display_header = false;
        $this->display_footer = false;
    }
    
    public function initContent()
    {
        $id_product = Tools::getValue('id_product');
        $edit_store = Tools::getValue('edit_store');
        $id_seller = Tools::getValue('id_seller');
        $is_all_product = Tools::getValue('is_all_product');

        $id_lang = $this->context->language->id;
        $this->context->smarty->assign('modules_dir', _MODULE_DIR_);

        if ($id_product == 0) //if all products
        {
            if ($edit_store && $id_seller) // if request coming from edit store page
                $stores = StoreLocator::getSellerStore($id_seller);
            else
            {
                $obj_mpproduct = new SellerProductDetail();
                $mp_product = $obj_mpproduct->getMarketPlaceShopProductDetail($is_all_product);
                $mp_id_product = $mp_product['marketplace_seller_id_product'];
                $id_seller = $obj_mpproduct->getSellerIdByProduct($mp_id_product);
                $stores = StoreLocator::getSellerStore($id_seller, true);
            }

            if (isset($stores) && $stores)
            {
                $allstore = StoreLocator::getMoreStoreDetails($stores, $id_lang);
                if ($allstore)
                {
                    if ($this->validateLoginAndSeller())
                    {
                        $allstore = $this->storeDetailsWithLink($allstore);
                        $this->assignSmartyVar($allstore);
                    }
                    else
                        $this->assignSmartyVar($allstore);
                }
            }
        }
        else //if search for particular product
        {
            // may be multiple store for one product so "getProductStore" function called
            if ($edit_store && $id_seller) // if request coming from edit store page
                $product_stores = StoreProduct::getProductStore($id_product); 
            else
                $product_stores = StoreProduct::getProductStore($id_product, true); // get only active store product

            $allproduct_store = array();
            if ($product_stores)
            {
                foreach ($product_stores as $p_store)
                    $allproduct_store[] = StoreLocator::getStoreById($p_store['id_store']);

                $p_stores = StoreLocator::getMoreStoreDetails($allproduct_store, $id_lang);
                if ($p_stores)
                {
                    if ($this->validateLoginAndSeller())
                    {
                        $p_stores = $this->storeDetailsWithLink($p_stores);
                        $this->assignSmartyVar($p_stores);
                    }
                    else
                        $this->assignSmartyVar($p_stores);
                }
            }
            else // if no details found
                echo "<center><h2>No store found</h2></center>";
        }
    }

    /**
     * [storeDetailsWithLink add other index to array]
     * @param  [array] $store_details [details array]
     * @return [array]                [array]
     */
    public function storeDetailsWithLink($store_details)
    {
        if (!is_array($store_details))
            return false;

        foreach ($store_details as $key => $details)
            $store_details[$key]['edit_store_link'] = $this->context->link->getModuleLink('mpstorelocator', 'editstore', array('id_store' => $details['id']));

        return $store_details;
    }

    public function assignSmartyVar($store)
    {
        $this->context->smarty->assign('filtered_stores', $store);
        $this->setTemplate('filtered_store.tpl');
    }

    public function validateLoginAndSeller()
    {
        $id_customer = $this->context->customer->id;
        if ($id_customer)
        {
            $obj_mp_customer = new MarketplaceCustomer();
            $is_seller = $obj_mp_customer->findMarketPlaceCustomer($id_customer);
            if ($is_seller)
                return true;
        }
        return false;
    }
}
?>