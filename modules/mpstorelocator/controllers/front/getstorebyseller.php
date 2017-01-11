<?php
class mpstorelocatorGetStorebySellerModuleFrontController extends ModuleFrontController
{
    public function init()
    {
        $this->display_header = false;
        $this->display_footer = false;
    }
    
    public function initContent()
    {
        $id_seller = Tools::getValue('id_seller');
        $id_lang = $this->context->language->id;
        $this->context->smarty->assign('modules_dir', _MODULE_DIR_);

        if ($id_seller == 0) //if all seller
        {
            $stores = StoreLocator::getAllStore();
            $allstore = StoreLocator::getMoreStoreDetails($stores, $id_lang);
            $this->context->smarty->assign('filtered_stores', $allstore);
            $this->setTemplate('filtered_store.tpl');
        }
        else //if search for particular seller
        {
            $seller_stores = StoreLocator::getSellerStore($id_seller, true);
            if ($seller_stores)
            {
                $seller_stores = StoreLocator::getMoreStoreDetails($seller_stores, $id_lang);
                $this->context->smarty->assign('filtered_stores', $seller_stores);
                $this->setTemplate('filtered_store.tpl');
            }
            else // if no details found
                echo "No store found";
        }
    }
}
?>