<?php
/**
 * Page Cache powered by Jpresta (jpresta . com)
 *
 *    @author    Jpresta
 *    @copyright Jpresta
 *    @license   You are just allowed to modify this copy for your own use. You must not redistribute it. License
 *               is permitted for one Prestashop instance only but you can install it on your test instances.
 */
class ManufacturerController extends ManufacturerControllerCore
{
    /*
    * module: pagecache
    * date: 2016-06-06 13:15:56
    * version: 3.02
    */
    public function displayAjax()
    {
        $result = array();
        $index = 0;
        do
        {
            $val = Tools::getValue('hook_' . $index);
            if ($val !== false)
            {
                list($hook_name, $id_module) = explode('|', $val);
                $result[$hook_name . '_' . $id_module] = Hook::exec($hook_name, array() , (int)$id_module);
            }
            $index++;
        } while ($val !== false);
        if (Tools::version_compare(_PS_VERSION_,'1.6','>')) {
            Media::addJsDef(array(
                'isLogged' => (bool)$this->context->customer->isLogged(),
                'isGuest' => (bool)$this->context->customer->isGuest(),
                'comparedProductsIds' => $this->context->smarty->getTemplateVars('compared_products'),
            ));
            $this->context->smarty->assign(array(
                    'js_def' => Media::getJsDef(),
            ));
            $result['js'] = $this->context->smarty->fetch(_PS_ALL_THEMES_DIR_.'javascript.tpl');
        }
        $this->context->cookie->write();
        die(Tools::jsonEncode($result));
    }
}
