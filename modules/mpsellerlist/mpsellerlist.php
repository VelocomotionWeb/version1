<?php

if (!defined('_PS_VERSION_')) {
    exit;
}
require_once 'classes/sellerlisthelper.php';
class MpSellerList extends Module
{
    public function __construct()
    {
        $this->name = 'mpsellerlist';
        $this->tab = 'front_office_features';
        $this->version = '2.0.0';
        $this->author = 'Webkul';
        $this->need_instance = 1;
        $this->bootstrap = true;
        $this->dependencies = array('marketplace');
        parent::__construct();
        $this->displayName = $this->l('Marketplace Seller List');
        $this->description = $this->l('Listing seller in your shop related to marketplace');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall this module.');
    }

    private function _displayForm()
    {
        $mp_seller_text = Configuration::getGlobalValue('MP_SELLER_TEXT');
        if (!$mp_seller_text) {
            $mp_seller_text = "There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn't anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable. The generated Lorem Ipsum is therefore always free from repetition, injected humour, or non-characteristic words etc.";
            Configuration::updateGlobalValue('MP_SELLER_TEXT', htmlentities($mp_seller_text, ENT_QUOTES));
        }
        $this->_html .= '<!doctype html>
			<head>
				<link rel="stylesheet"  href=""/>					
			</head>
			<body>
				<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post">
					<div class="panel">
						<div class="panel-heading">
							<span>'.$this->l('Message Setting').'</span>
						</div>
						<div class="form-wrapper">
							<div class="form-group">
								<label>'.$this->l('Message on seller list page').'</label>
								<textarea style="width:80%;" rows="5" cols="8" class="form-control" name="mp_sellerlist_text">'.Configuration::getGlobalValue('MP_SELLER_TEXT').'
								</textarea>
							</div>
							<input class="btn btn-default" style="background-color:#00aff0;border-color:#00aff0;color:#FFF;font-size:14px;" name="btnSubmit" value="'.$this->l('Update settings').'" type="submit" />
						</div>
					</div>
				</form>
			</body>
		</html>';
    }

    public function getContent()
    {
        $this->_html = '<h2>'.$this->displayName.'</h2>';
        $this->_postProcess();
        $this->_displayForm();

        return $this->_html;
    }

    private function _postProcess()
    {
        if (Tools::isSubmit('btnSubmit')) {
            $mp_seller_text = Tools::getValue('mp_sellerlist_text');
            Configuration::updateGlobalValue('MP_SELLER_TEXT', htmlentities($mp_seller_text, ENT_QUOTES));
            $this->_html .= $this->displayConfirmation($this->l('seller text updated'));
        }
    }

    public function hookTop()
    {
        $link = new link();
        $seller_listlink = $link->getModuleLink('mpsellerlist', 'sellerlist');
        $this->context->smarty->assign('seller_listlink', $seller_listlink);

        return $this->display(__FILE__, 'mplink.tpl');
    }

    public function hookHeader()
    {
        $this->context->controller->addCSS(($this->_path).'views/css/hook_style.css', 'all');
    }

    public function install()
    {
        $ismpinstall = Module::isInstalled('marketplace');
        if ($ismpinstall) {
            return (parent::install() && $this->registerHook('top') && $this->registerHook('header'));
        } else {
            $this->errors[] = Tools::displayError($this->l('Marketplace Module Not install.'));

            return false;
        }
    }

    public function uninstall()
    {
        if (parent::uninstall() == false) {
            return false;
        }

        return true;
    }
}
