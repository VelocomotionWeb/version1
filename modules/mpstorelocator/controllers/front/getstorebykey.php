<?php
class mpstorelocatorGetStorebyKeyModuleFrontController extends ModuleFrontController
{
    public function init()
    {
        $this->display_header = false;
        $this->display_footer = false;
    }
    
    public function initContent()
    {
        // searcing by city now
        $key = Tools::getValue('search_key');
        $seller_stores = Tools::getValue('seller_stores');
        $id_product = Tools::getValue('id_product');
        $id_lang = $this->context->language->id;
        $this->context->smarty->assign('modules_dir', _MODULE_DIR_);

        if ($key)
        {
            if ($id_product)
            {
                $obj_mpproduct = new SellerProductDetail();
                $mp_product = $obj_mpproduct->getMarketPlaceShopProductDetail($id_product);
                $mp_id_product = $mp_product['marketplace_seller_id_product'];
                $id_seller = $obj_mpproduct->getSellerIdByProduct($mp_id_product);

                $stores = StoreLocator::getActiveStoreByCityAndIdSeller($key, $id_seller);
            }
            else
                $stores = StoreLocator::getStoreByCity($key);
            if ($stores)
            {
                $allstore = StoreLocator::getMoreStoreDetails($stores, $id_lang);
                if ($allstore)
                {
                    $id_customer = $this->context->customer->id;
                    if ($seller_stores) //edit link required when search by seller
                        $allstore = $this->storeDetailsWithLink($allstore);
                    else if ($id_customer)
                    {
                        $obj_mp_customer = new MarketplaceCustomer();
                        $is_seller = $obj_mp_customer->findMarketPlaceCustomer($id_customer);
                        if ($is_seller)
                        $allstore = $this->storeDetailsWithLink($allstore);
                    }

                    $this->context->smarty->assign('filtered_stores', $allstore);
                    $this->setTemplate('filtered_store.tpl'); 
                }
            }
            else
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

        $link = new Link();
        foreach ($store_details as $key => $details)
        {
            $store_details[$key]['edit_store_link'] = $link->getModuleLink('mpstorelocator', 'editstore',
                                                            array('id_store' => $details['id']));

        }

        return $store_details;
    }
}
?>