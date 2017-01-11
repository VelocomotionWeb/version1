<?php
class mpstorelocatorFilterStateModuleFrontController extends ModuleFrontController
{
    public function init()
    {
        parent::init();
        $this->display_header = false;
        $this->display_footer = false;
    }
    
    public function initContent()
    {
        $id_country = Tools::getValue('id_country');
        $states = State::getStatesByIdCountry((int)$id_country);
        if ($states)
            $jsondata = Tools::jsonEncode($states);
        else
            $jsondata = Tools::jsonEncode(array('failed'));
        
        die($jsondata);
    }
}
?>