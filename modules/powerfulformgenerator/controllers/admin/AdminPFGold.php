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

include_once(dirname(__FILE__).'/../../classes/models/PFGModel.php');
include_once(dirname(__FILE__).'/../../classes/models/PFGFieldModel.php');

class AdminPFGController extends AdminController
{
    private $field_controller;
    private $submission_controller;

    /**
     * Create the necessary elements for rendering the forms
     * using a HelperList from Prestashop :)
     */
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'pfg';
        $this->className = 'PFGModel';

        $this->lang = true;
        $this->addRowAction('view');
        $this->addRowAction('manage');
        $this->addRowAction('duplicate');
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')));

        $this->fields_list = array(
            'id_pfg' => array('title' => $this->l('ID'),        'align' => 'center', 'width' => 30),
            'title'  => array('title' => $this->l('Title'),     'align' => 'left', 'width' => 'auto', 'orderby' => true, 'search' => false),
            'active' => array('title' => $this->l('Displayed'), 'width' => 40, 'active' => 'status', 'align' => 'center', 'type' => 'bool', 'orderby' => false),
        );

        parent::__construct();

        switch (Shop::getContext()) {
            case Shop::CONTEXT_GROUP:
                $id_shop_group = (int)Shop::getContextShopGroupID();
                $this->_join = 'INNER JOIN `'._DB_PREFIX_.'pfg_shop` cfs ON a.id_pfg = cfs.id_pfg INNER JOIN `'._DB_PREFIX_.'shop` ps ON (cfs.id_shop = ps.id_shop AND ps.`id_shop_group` = '.pSQL((int)$id_shop_group).')';
                $this->_group = 'GROUP BY a.id_pfg';
                break;
            case Shop::CONTEXT_SHOP:
            case Shop::CONTEXT_ALL:
                $id_shop = (int)Shop::getContextShopID();
                if (!empty($id_shop)) {
                    $this->_join = 'INNER JOIN `'._DB_PREFIX_.'pfg_shop` cfs ON (a.id_pfg = cfs.id_pfg AND cfs.`id_shop` = '.pSQL((int)$id_shop).')';
                }
                break;
        }
    }

    /**
     * Add some JS to improve user experience
     */
    public function setMedia()
    {
        parent::setMedia();
        $this->context->controller->addJS(__PS_BASE_URI__.'modules/powerfulformgenerator/views/js/pfg.js');
    }

    /**
     * Display one more link in each line of the generated table
     * This link points to editing the field in each forms.
     *
     * @param string $token Current session token
     * @param int $id Id of the current form
     *
     * @return string
     */
    public function displayManageLink($token = null, $id)
    {
        $url = $this->context->link->getAdminLink('AdminPFGFields');
        $url .= '&id_pfg='.$id;
        if (Tools::substr(_PS_VERSION_, 0, 3) === '1.6') {
            return '<a href="'.$url.'" title="'.$this->l('Manage the different fields of this form.').'" class="manage">
                        <i class="icon-cog"></i> '.$this->l('Manage').'
                    </a>';
        } else {
            return '<a href="'.$url.'" title="'.$this->l('Manage the different fields of this form.').'">
                <img src="../img/admin/cog.gif" alt="'.$this->l('Manage').'">
            </a>';
        }
    }

    /**
     * Display one more link in each line of the generated table
     * This link points to duplicating the form.
     *
     * @param string $token Current session token
     * @param int $id Id of the current form
     *
     * @return string
     */
    public function displayDuplicateLink($token = null, $id)
    {
        $url = $this->context->link->getAdminLink('AdminPFG');
        $url .= '&id_pfg='.$id.'&action=duplicate';
        if (Tools::substr(_PS_VERSION_, 0, 3) === '1.6') {
            return '<a href="'.$url.'" title="'.$this->l('Duplicate this form.').'" class="duplicate">
                        <i class="icon-code-fork"></i> '.$this->l('Duplicate').'
                    </a>';
        } else {
            return '<a href="'.$url.'" title="'.$this->l('Duplicate this form.').'">
                <img src="../img/admin/cog.gif" alt="'.$this->l('Duplicate').'">
            </a>';
        }
    }

    /**
     * Redirect to the Submission HelperList from AdminPFGSubmissions class
     */
    public function renderView()
    {
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminPFGSubmissions').'&id_pfg='.(int)Tools::getValue('id_pfg'));
        exit();
    }

    /**
     * Generate the table based on the HelperList
     * @see AdminController::renderList()
     */
    public function renderList()
    {
        if (Tools::isSubmit('action') && Tools::getValue('action') === 'duplicate') {
            $id_pfg = Tools::getValue('id_pfg');
            if (!empty($id_pfg)) {
                Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'pfg (`send_mail_to`, `action_sender`, `action_admin`, `active`, `is_only_connected`, `accessible`) SELECT `send_mail_to`, `action_sender`, `action_admin`, `active`, `is_only_connected`, `accessible` FROM '._DB_PREFIX_.'pfg WHERE `id_pfg` = '.pSQL((int)$id_pfg).' LIMIT 1');

                $new_id_pfg = Db::getInstance()->Insert_ID();

                Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'pfg_lang (`id_pfg`, `id_lang`, `title`, `subject_sender`, `subject_admin`, `header`, `footer`, `success`, `send_label`, `message_sender`, `message_admin`, `unauth_redirect_url`) SELECT '.pSQL((int)$new_id_pfg).', `id_lang`, `title`, `subject_sender`, `subject_admin`, `header`, `footer`, `success`, `send_label`, `message_sender`, `message_admin`, `unauth_redirect_url` FROM '._DB_PREFIX_.'pfg_lang WHERE `id_pfg` = '.pSQL((int)$id_pfg));

                Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'pfg_shop (`id_pfg`, `id_shop`) SELECT '.pSQL((int)$new_id_pfg).', `id_shop` FROM '._DB_PREFIX_.'pfg_shop WHERE `id_pfg` = '.pSQL((int)$id_pfg));

                $pfg_fields = Db::getInstance()->ExecuteS('SELECT id_field, `type`, `name`, `required`, `classname`, `style`, `extra`, `related`, `position` FROM '._DB_PREFIX_.'pfg_fields WHERE `id_pfg` = '.pSQL((int)$id_pfg));
                foreach ($pfg_fields as $row) {
                    Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'pfg_fields (`id_pfg`, `type`, `name`, `required`, `classname`, `style`, `extra`, `related`, `position`) VALUES ('.pSQL((int)$new_id_pfg).', "'.$row['type'].'", "'.$row['name'].'", "'.$row['required'].'", "'.$row['classname'].'", "'.$row['style'].'", "'.$row['extra'].'", "'.$row['related'].'", "'.$row['position'].'")');
                    $new_id_pfg_field = Db::getInstance()->Insert_ID();

                    Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'pfg_fields_lang (`id_field`, `id_lang`, `label`, `values`) SELECT '.pSQL((int)$new_id_pfg_field).', `id_lang`, `label`, `values` FROM `'._DB_PREFIX_.'pfg_fields_lang` WHERE `id_field` = '.pSQL((int)$row['id_field']));
                }

                Tools::redirectAdmin($this->context->link->getAdminLink('AdminPFG').'&id_pfg='.(int)$new_id_pfg.'&updatepfg');
                exit();
            }
        }

        $this->initToolbar();

        if (!is_dir(_PS_ROOT_DIR_.'/upload') || !is_writable(_PS_ROOT_DIR_.'/upload')) {
            $this->warnings[] = $this->l('Your /upload/ folder must be writeable if you plan to use file upload.');
        }

        return parent::renderList();
    }

    /**
     * Render the form using HelperForm
     *
     * @see AdminController::renderForm()
     */
    public function renderForm()
    {
        if (!$this->loadObject(true)) {
            return;
        }

        if (Validate::isLoadedObject($this->object)) {
            $this->display = 'edit';
        } else {
            $this->display = 'add';
        }

        $this->initToolbar();

        $context = Context::getContext();
        $context->controller->addJS(array(
            _PS_JS_DIR_.'tiny_mce/tiny_mce.js',
            _PS_JS_DIR_.'tinymce.inc.js'
        ));

        $actions = array (
            array ('value' => 'form', 'name' => $this->l('Send the form')),
            array ('value' => 'message', 'name' => $this->l('Send a specific message')),
            array ('value' => null, 'name' => $this->l('Do nothing'))
        );

        $this->fields_form = array (
            'tinymce' => false,
            'legend' => array(
                'title' => $this->l('Powerful Form Generator'),
            ),
            'input' => array(
                array(
                    'type'     => 'text',
                    'label'    => $this->l('Form title :'),
                    'name'     => 'title',
                    'lang'     => true,
                    'required' => true,
                    'class'    => 'fixed-width-xl',
                    'size'     => 50
                ),
                array(
                    'type'     => 'select',
                    'label'    => $this->l('Type of email for the sender'),
                    'name'     => 'action_sender',
                    'options'  => array(
                        'query'   => $actions,
                        'id'      => 'value',
                        'name'    => 'name'
                    ),
                    'desc'     => $this->l('What kind of email to send to the sender after the form has been successfully submitted.'),
                    'class'    => 'fixed-width-xl',
                ),
                array(
                    'type'     => 'text',
                    'label'    => $this->l('Send form to :'),
                    'name'     => 'send_mail_to',
                    'required' => true,
                    'class'    => 'fixed-width-xl',
                    'desc'     => $this->l('List of the admins emails, separated by comma (",").<br />Ex: email@example.com,second@example.com'),
                    'size'     => 50
                ),
                array(
                    'type'     => 'select',
                    'label'    => $this->l('Type of email for the admin(s)'),
                    'name'     => 'action_admin',
                    'options'  => array(
                        'query'   => $actions,
                        'id'      => 'value',
                        'name'    => 'name'
                    ),
                    'desc'     => $this->l('What kind of email to send to the admin(s) after the form has been successfully submitted.'),
                    'class'    => 'fixed-width-xl',
                ),
                array(
                    'type'     => 'radio',
                    'label'    => $this->l('Active:'),
                    'name'     => 'active',
                    'required' => false,
                    'is_bool'  => true,
                    'class'    => 't',
                    'values'   => array(
                        array(
                            'id'    => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id'    => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                            )
                        ),
                ),
                array(
                    'type'     => 'select',
                    'label'    => $this->l('Allow only to :'),
                    'name'     => 'is_only_connected',
                    'required' => false,
                    'options'  => array(
                        'query'   => array (
                            array ('value' => '0', 'name' => $this->l('Everybody')),
                            array ('value' => '1', 'name' => $this->l('Connected user only.')),
                        ),
                        'id'      => 'value',
                        'name'    => 'name'
                    ),
                    'class'    => 'fixed-width-xl',
                ),
                array(
                    'type'     => 'text',
                    'label'    => $this->l('Redirect URL :'),
                    'name'     => 'unauth_redirect_url',
                    'lang'     => true,
                    'required' => false,
                    'desc'     => $this->l('Redirect URL for non authenticated users. Leave empty to show a 404 page instead.'),
                    'class'    => 'fixed-width-xxl redirect_url',
                    'size'     => 98
                ),
                array(
                    'type'     => 'select',
                    'label'    => $this->l('Accessible via :'),
                    'name'     => 'accessible',
                    'required' => false,
                    'options'  => array(
                        'query'   => array (
                            array ('value' => '1', 'name' => $this->l('Via URL only.')),
                            array ('value' => '2', 'name' => $this->l('Via HOOK only.')),
                            array ('value' => '0', 'name' => $this->l('Both URL and HOOK.')),
                        ),
                        'id'      => 'value',
                        'name'    => 'name'
                    ),
                    'class'    => 'fixed-width-xl',
                ),
                array(
                    'type'     => 'text',
                    'label'    => $this->l('Default subject (sender) :'),
                    'name'     => 'subject_sender',
                    'lang'     => true,
                    'required' => false,
                    'desc'     => html_entity_decode($this->l('Default subject for the email sent to the sender if no subject field is defined.<br /><strong>You can use variables from the fields you configured in this form.</strong> Like this : <code>&lcub;$firstname&rcub;</code> for a field with the name "firstname".')),
                    'class'    => 'fixed-width-xxl',
                    'size'     => 98
                ),
                array(
                    'type'     => 'text',
                    'label'    => $this->l('Default subject (admin) :'),
                    'name'     => 'subject_admin',
                    'lang'     => true,
                    'required' => false,
                    'desc'     => html_entity_decode($this->l('Default subject for the email sent to the admin if no subject field is defined.<br /><strong>You can use variables from the fields you configured in this form.</strong> Like this : <code>&lcub;$firstname&rcub;</code> for a field with the name "firstname".')),
                    'class'    => 'fixed-width-xxl',
                    'size'     => 98
                ),
                array(
                    'type'         => 'textarea',
                    'label'        => $this->l('Header message :'),
                    'name'         => 'header',
                    'autoload_rte' => true,
                    'lang'         => true,
                    'required'     => false,
                    'rows'         => '5',
                    'cols'         => '48',
                    'desc'         => $this->l('Message to display before the form.'),
                ),
                array(
                    'type'         => 'textarea',
                    'label'        => $this->l('Footer message :'),
                    'name'         => 'footer',
                    'autoload_rte' => true,
                    'lang'         => true,
                    'required'     => false,
                    'rows'         => '5',
                    'cols'         => '48',
                    'desc'         => $this->l('Message to display after the form.'),
                ),
                array(
                    'type'         => 'textarea',
                    'label'        => $this->l('Success message :'),
                    'name'         => 'success',
                    'autoload_rte' => true,
                    'lang'         => true,
                    'required'     => false,
                    'rows'         => '5',
                    'cols'         => '48',
                    'desc'         => html_entity_decode($this->l('Message to display after the form has been submitted. If your message starts with http, the user will be redirected to that address.<br /><strong>You can use variables from the fields you configured in this form.</strong> Like this : <code>&lcub;$firstname&rcub;</code> for a field with the name "firstname".')),
                ),
                array(
                    'type'     => 'text',
                    'label'    => $this->l('Send button value :'),
                    'name'     => 'send_label',
                    'lang'     => true,
                    'required' => false,
                    'desc'     => $this->l('The value to show in the "send" button.'),
                    'class'    => 'fixed-width-xxl',
                    'size'     => 98
                ),
                array(
                    'type'         => 'textarea',
                    'label'        => $this->l('Message to send to the sender :'),
                    'name'         => 'message_sender',
                    'autoload_rte' => true,
                    'lang'         => true,
                    'required'     => false,
                    'rows'         => '5',
                    'cols'         => '48',
                    'desc'         => html_entity_decode($this->l('Message to send to the sender.<br /><strong>You can use variables from the fields you configured in this form.</strong> Like this : <code>&lcub;$firstname&rcub;</code> for a field with the name "firstname".')),
                    'class'        => 'message_senders'
                ),
                array(
                    'type'         => 'textarea',
                    'label'        => $this->l('Message to send to the admins :'),
                    'name'         => 'message_admin',
                    'autoload_rte' => true,
                    'lang'         => true,
                    'required'     => false,
                    'rows'         => '5',
                    'cols'         => '48',
                    'desc'         => html_entity_decode($this->l('Message to send to the admins.<br /><strong>You can use variables from the fields you configured in this form.</strong> Like this : <code>&lcub;$firstname&rcub;</code> for a field with the name "firstname".')),
                    'class'        => 'message_admins'
                ),
                array(
                    'type'     => 'text',
                    'label'    => $this->l('Google RE:Captcha Website key :'),
                    'name'     => 'recaptcha_public',
                    'required' => true,
                    'class'    => 'fixed-width-xl',
                    'desc'     => $this->l('Your public website key from Google Re:Captcha'),
                    'size'     => 50
                ),
                array(
                    'type'     => 'text',
                    'label'    => $this->l('Google RE:Captcha Private key :'),
                    'name'     => 'recaptcha_private',
                    'required' => true,
                    'class'    => 'fixed-width-xl',
                    'desc'     => $this->l('Your private key from Google Re:Captcha'),
                    'size'     => 50
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right'
            )
        );

        $this->fields_value['active'] = true;

        $this->tpl_form_vars = array(
            'active' => $this->object->active,
            'PS_ALLOW_ACCENTED_CHARS_URL', (int)Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL')
        );

        $languages = Language::getLanguages(true);
        if (count($languages) > 1) {
            $this->warnings[] = $this->l('You use more than one language on your shop. Don\t forget to mention a value for each language before submitting this form.');
        }

        return parent::renderForm();
    }

    /**
     * Process to the validation of the submitted HelperForm
     */
    protected function _childValidation()
    {
        $languages = Language::getLanguages(true);

        // if action == 'message', related message required
        if (Tools::getValue('action_sender') === 'message') {
            foreach ($languages as $language) {
                $value = Tools::getValue('message_sender_'.$language['id_lang']);
                if (empty($value)) {
                    $this->errors[] = $this->l('Please indicate a message to send to the sender.');
                }
            }
        }

        // if action == 'message', related message required
        if (Tools::getValue('action_admin') === 'message') {
            foreach ($languages as $language) {
                $value = Tools::getValue('message_admin_'.$language['id_lang']);
                if (empty($value)) {
                    $this->errors[] = $this->l('Please indicate the message to send to the admin(s).');
                }
            }
        }

        // check every admin email,
        $send_mail_to = Tools::getValue('send_mail_to');
        if (empty($send_mail_to)) {
            $this->errors[] = $this->l('"Send form to" field is required.');
        } else {
            $emails = explode(',', Tools::getValue('send_mail_to'));
            foreach ($emails as $email) {
                $email = trim($email);

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $this->errors[] = $this->l('Invalid email provided in "Send form to". (Please separate emails with a comma)');
                }
            }
        }

        $fields = array();
        foreach (PFGFieldModel::findFields(Tools::getValue('id_pfg')) as $field) {
            $fields[] = $field['name'];
        }

        foreach (array('subject_sender', 'subject_admin', 'success', 'message_sender', 'message_admin') as $variable_name) {
            foreach ($languages as $language) {
                $matches = array();

                preg_match_all('/(\{\$([a-z0-9_]+)(\[\])?\})/', Tools::getValue($variable_name.'_'.$language['id_lang']), $matches, PREG_SET_ORDER);
                if (count($matches) > 0) {
                    $matches = $this->pregMatchReorder($matches);
                    foreach ($matches as $match) {
                        if (!in_array($match, $fields)) {
                            $this->errors[] = sprintf($this->l('Invalid variable "%s". This name does not exists. (You need to create the field first)'), $match);
                            return;
                        }
                    }
                }
            }
        }
    }

    private function pregMatchReorder($matches)
    {
        $result = array();
        foreach ($matches as $match) {
            $result[] = $match[2];
        }

        return $result;
    }
}
