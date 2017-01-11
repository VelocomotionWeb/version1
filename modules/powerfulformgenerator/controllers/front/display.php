<?php
/**
 * Powerful Form Generator
 *
 * This modules aims to provide for your customer any kind of form you want.
 *
 * If you find errors, bugs or if you want to share some improvments,
 * feel free to contact at contact@prestaddons.net ! :)
 * Si vous trouvez des erreurs, des bugs ou si vous souhaitez
 * tout simplement partager un conseil ou une amélioration,
 * n'hésitez pas à me contacter à contact@prestaddons.net
 *
 * @package   modules
 * @author    Cyril Nicodème <contact@prestaddons.net>
 * @copyright Copyright (C) April 2014 prestaddons.net <@email:contact@prestaddons.net>. All rights reserved.
 * @since     2014-04-15
 * @version   2.6.2
 * @license   Nicodème Cyril
 */

require_once(dirname(__FILE__).'/../../classes/PFGRenderer.php');

class PowerfulFormGeneratorDisplayModuleFrontController extends ModuleFrontController
{
    /**
     * Initialize the content to be displayed by calling the specific hook in the template file.
     *
     * @see ModuleFrontController::initContent
     *
     * @return string
     */
    public function initContent()
    {
        parent::initContent();

        $renderer = new PFGRenderer(Tools::getValue('id'), false);

        if (!$renderer->isAllowed(true)) {
            $redirect_url = $renderer->getForm()->unauth_redirect_url[Context::getContext()->language->id];
            if (!empty($redirect_url)) {
                Tools::redirect($redirect_url);
            } else {
                Controller::getController('PageNotFoundController')->run();
            }
            exit();
        }

        $this->context->smarty->assign(
            array(
                'idPfg' => Tools::getValue('id')
            )
        );

        return $this->setTemplate('display.tpl');
    }
}
