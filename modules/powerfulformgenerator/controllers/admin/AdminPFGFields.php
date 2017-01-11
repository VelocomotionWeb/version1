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

include_once(dirname(__FILE__).'/../../classes/models/PFGFieldModel.php');
include_once(dirname(__FILE__).'/../../classes/models/PFGModel.php');

class AdminPFGFieldsController extends AdminController
{
    private $pfg_model;

    /**
     * Initialize the controller based on the given form id
     */
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'pfg_fields';
        $this->className = 'PFGFieldModel';

        $this->identifier = 'id_field';
        $this->position_identifier = 'id_field';

        $this->lang = true;
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')));

        if (!Tools::isSubmit('id_pfg')) {
            $link = new Link();
            Tools::redirectAdmin($link->getAdminLink('AdminPFG'));
        }

        $this->pfg_model = new PFGModel((int)Tools::getValue('id_pfg'));

        $this->_where = 'AND id_pfg = '.$this->pfg_model->id;

        $this->_defaultOrderBy = 'position';

        $this->fields_list = array(
            'id_field' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 30),
            'label'    => array('title' => $this->l('Label'),'width' => 'auto', 'align' => 'left', 'orderby' => true),
            'name'     => array('title' => $this->l('Name'), 'align' => 'left', 'width' => 'auto', 'orderby' => true, 'search' => false),
            'type'     => array('title' => $this->l('Type'), 'align' => 'center', 'width' => 'auto', 'orderby' => true,
                                    'search' => false, 'callback' => 'ucfirstTypes'),
            'related'  => array('title' => $this->l('Related to'),'width' => 'auto', 'align' => 'center', 'orderby' => false,
                                    'search' => false, 'callback' => 'ucFirstTypes'),
            'required' => array('title' => $this->l('Required'),'width' => 40, 'activeVisu' => 'www', 'align' => 'center',
                                    'type' => 'bool', 'orderby' => true),
            'position' => array('title' => $this->l('Position'), 'position' => 'position', 'filter_key' => 'position',
                                    'align' => 'center', 'search' => false),
        );

        parent::__construct();
        $this->tabAccess = Profile::getProfileAccess($this->context->employee->id_profile, Tab::getIdFromClassName('AdminPFG'));

        if (is_null($this->pfg_model->recaptcha_public) || is_null($this->pfg_model->recaptcha_private)) {
            $result = Db::getInstance()->executeS('SELECT id_field FROM '._DB_PREFIX_.'pfg_fields WHERE type = "recaptcha" AND id_pfg = '.pSQL((int)$this->pfg_model->id));

            if (count($result) > 0) {
                $this->warnings[] = $this->l('The Re:Captcha field won\'t be displayed because you first have to set yours keys in the Form configuration.');
            }
        }
    }

    /**
     * Update the position of two fields via an Ajax request
     */
    public function ajaxProcessUpdatePositions()
    {
        if (Tools::isSubmit('field')) {
            $index = 0;

            $id_pfg = null;
            if (Tools::isSubmit('id_pfg')) {
                $id_pfg = Tools::getValue('id_pfg');
            }

            foreach (Tools::getValue('field') as $field) {
                $field = Tools::substr($field, 3);
                $orders = explode('_', $field);
                PFGFieldModel::updatePositionField((is_null($id_pfg) ? $orders[0] : $id_pfg), $orders[1], $index);

                $index++;
            }
        } else {
            if (Tools::getIsset('id') && Tools::getIsset('way')) {
                $object = new PFGFieldModel((int)Tools::getValue('id'));
                $way = Tools::getValue('way');
                if ($object->id_pfg) {
                    $new_position = $object->position;
                    $way ? $new_position++ : $new_position--;

                    if ($object->updatePosition($way, $new_position)) {
                        exit();
                    }

                    echo '{"hasError": true, errors: "Cannot update position"}';
                    exit();
                }
            }

            echo '{"hasError": true, errors: "This item can not be loaded"}';
            exit();
        }
    }

    /**
     * Add some JS to improve the User Experience
     */
    public function setMedia()
    {
        parent::setMedia();
        $this->context->controller->addJS(__PS_BASE_URI__.'modules/powerfulformgenerator/views/js/pfg-fields.js');
    }

    /**
     * Set the rendered fields with a better view
     * By capitalizing the first letter of each words
     *
     * @param string $value The given text
     */
    public function ucfirstTypes($value)
    {
        if (empty($value)) {
            return null;
        }

        return Tools::ucfirst($value);
    }

    /**
     * Initialize breadcrumbs for better UX
     * Used by the parent class
     *
     * @param int $tab_id
     * @param array $tabs
     *
     * @see AdminController::initBreadcrumbs
     */
    public function initBreadcrumbs($tab_id = null, $tabs = null)
    {
        if (is_null($tab_id)) {
            parent::initBreadcrumbs();
        } else {
            parent::initBreadcrumbs($tab_id, $tabs);
        }

        $this->breadcrumbs[] = 'Powerful Form Generator';
        if (isset($this->pfg_model->title[$this->context->language->id])) {
            $this->breadcrumbs[] = $this->pfg_model->title[$this->context->language->id];
        } elseif (count($this->pfg_model->title) > 0) {
            $this->breadcrumbs[] = $this->pfg_model->title[key($this->pfg_model->title)];
        }
        $this->breadcrumbs[] = $this->l('Fields');
    }

    /**
     * Initialize the toolbar
     * Used by the parent class
     *
     * @see AdminController::initToolbar
     */
    public function initToolbar()
    {
        parent::initToolbar();
        switch ($this->display)
        {
            case 'add':
            case 'edit':
                $this->toolbar_btn['back'] = array(
                    'short' => 'Back',
                    'href'  => self::$currentIndex.'&token='.$this->token.'&managecontact_form&id_pfg='.(int)Tools::getValue('id_pfg'),
                    'desc'  => $this->l('Back to list'),
                );
                break;
            default:
                $back_url = $this->context->link->getAdminLink('AdminPFG');

                $this->toolbar_btn['back'] = array(
                    'short' => 'Back',
                    'href'  => $back_url,
                    'desc'  => $this->l('Back to the forms'),
                );
                break;
        }
    }

    /**
     * Initialize the process
     * Used by the parent class
     *
     * @see AdminController::initProcess
     */
    public function initProcess()
    {
        parent::initProcess();
        self::$currentIndex .= '&id_pfg='.(int)Tools::getValue('id_pfg');
    }

    /**
     * Add the form ID before adding it in the database
     *
     * @param object $object The PFGFieldModel instance
     */
    protected function beforeAdd($object)
    {
        $object->id_pfg = (int)Tools::getValue('id_pfg');
    }

    /**
     * Renders the list of existing fields
     *
     * @see AdminController::renderList
     */
    public function renderList()
    {
        $this->initToolbar();
        return parent::renderList();
    }

    /**
     * Renders the form using FormHelper
     *
     * @see AdminController::renderForm
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

        $field_types = array (
            array ('value' => 'text',          'name' => $this->l('Text')),
            array ('value' => 'number',        'name' => $this->l('Number')),
            array ('value' => 'email',         'name' => $this->l('Email')),
            array ('value' => 'url',           'name' => $this->l('URL')),
            array ('value' => 'textarea',      'name' => $this->l('Textarea')),
            array ('value' => 'select',        'name' => $this->l('Select')),
            array ('value' => 'radio',         'name' => $this->l('Radio')),
            array ('value' => 'checkbox',      'name' => $this->l('Checkbox')),
            array ('value' => 'multicheckbox', 'name' => $this->l('Multiple Checkbox')),
            array ('value' => 'file',          'name' => $this->l('File')),
            array ('value' => 'datepicker',    'name' => $this->l('Datepicker')),
            array ('value' => 'hidden',        'name' => $this->l('Hidden field')),
            array ('value' => 'separator',     'name' => $this->l('Separator')),
            array ('value' => 'static',        'name' => $this->l('Static field')),
            array ('value' => 'legend',        'name' => $this->l('Legend (Fieldset)')),
            array ('value' => 'recaptcha',     'name' => $this->l('Google RE:Captcha')),
        );

        if ($this->isGdEnabled()) {
            $field_types[] = array ('value' => 'captcha',  'name' => $this->l('Basic Captcha'));
        }

        $field_related = array (
            array('value' => '',           'name' => $this->l('Nothing')),
            array('value' => 'email',      'name' => $this->l('Email of the sender')),
            array('value' => 'subject',    'name' => $this->l('Subject of the email')),
            array('value' => 'newsletter', 'name' => $this->l('Newsletter opt-in')),
        );

        $this->fields_form = array (
            'tinymce' => false,
            'legend'  => array(
                'title' => $this->l('Powerful Form Generator'),
            ),
            'input' => array(
                array(
                    'type'     => 'text',
                    'label'    => $this->l('Label :'),
                    'name'     => 'label',
                    'lang'     => true,
                    'required' => true,
                    'class'    => 'fixed-width-xl label-field',
                    'size'     => 50
                ),
                array(
                    'type'     => 'text',
                    'label'    => $this->l('Name :'),
                    'name'     => 'name',
                    'required' => true,
                    'class'    => 'fixed-width-xl',
                    'size'     => 50,
                    'desc'     => html_entity_decode($this->l('This value will not be shown to your customer and will only be used internally or as variables for the messages sent/showed by the form.<br />Alphanumerical value only. (No space or special characters)'))
                ),
                array(
                    'type'     => 'select',
                    'label'    => $this->l('Type'),
                    'id'       => 'pfg-field-select-types',
                    'name'     => 'type',
                    'options'  => array(
                        'query' => $field_types,
                        'id'    => 'value',
                        'name'  => 'name'
                    ),
                    'desc'     => $this->l('Type of field.'),
                ),
                array(
                    'type'     => 'text',
                    'label'    => $this->l('Values :'),
                    'name'     => 'values',
                    'lang'     => true,
                    'required' => true,
                    'class'    => 'fixed-width-xl pfg-fields-values',
                    'desc'     => html_entity_decode($this->l("<span class='field-select-items field-select field-radio'>Comma separated list of options.</span><span class='field-select-items field-checkbox'>Message to display at the right of the checkbox.</span><span class='field-select-items field-file'>Comma separated list of accepted files formats (WITHOUT the extension).</span><span class='field-select-items field-static'>Message to show as a static field.</span>", 'AdminPFGFields', false, false)),
                    'size'     => 50
                ),
                array(
                    'type'     => 'radio',
                    'label'    => $this->l('Required:'),
                    'name'     => 'required',
                    'required' => false,
                    'is_bool'  => true,
                    'class'    => 't',
                    'values'   => array(
                        array(
                            'id'    => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes')
                        ),
                        array(
                            'id'    => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No')
                            )
                        ),
                ),
                array(
                    'type'     => 'text',
                    'label'    => $this->l('Class :'),
                    'name'     => 'classname',
                    'required' => false,
                    'class'    => 'fixed-width-xl',
                    'size'     => 50,
                    'desc'     => $this->l('This class will be affected to the container of the input, not the input directly.')
                ),
                array(
                    'type'     => 'text',
                    'label'    => $this->l('Style (css) :'),
                    'name'     => 'style',
                    'required' => false,
                    'class'    => 'fixed-width-xl',
                    'size'     => 50
                ),
                array(
                    'type'     => 'text',
                    'label'    => $this->l('Extra :'),
                    'name'     => 'extra',
                    'required' => false,
                    'class'    => 'fixed-width-xl',
                    'size'     => 50
                ),
                array(
                    'type'     => 'select',
                    'label'    => $this->l('Link this field to :'),
                    'name'     => 'related',
                    'options'  => array(
                        'query'   => $field_related,
                        'id'      => 'value',
                        'name'    => 'name'
                    ),
                    'desc'     => $this->l('This will link this field to a specific value of the form.'),
                    'class'    => 'fixed-width-xl',
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right'
            )
        );

        $this->tpl_form_vars = array(
            'required' => $this->object->required,
            'PS_ALLOW_ACCENTED_CHARS_URL', (int)Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL')
        );

        $languages = Language::getLanguages(true);
        if (count($languages) > 1) {
            $this->warnings[] = $this->l('You use more than one language on your shop. Don\t forget to mention a value for each language before submitting this form.');
        }

        if (!$this->isGdEnabled()) {
            $this->warnings[] = $this->l('Missing GD library with jpeg support. Captcha will not work.');
        }

        return parent::renderForm();
    }

    /**
     * Returns true weither the GD library is enabled or not
     *
     * @return boolean
     */
    private function isGdEnabled()
    {
        return (function_exists('ImageCreate') && function_exists('ImageJpeg'));
    }

    /**
     * Process to the validation of the submitted HelperForm
     */
    protected function _childValidation()
    {
        if (!preg_match('/^[a-z0-9_\[\]]+$/', Tools::getValue('name'))) {
            $this->errors[] = $this->l('Please use only alphanumerical (a-z, 0-9) and underscore ("_") caracters for the name field.');
        }

        // We check that the name does not exists already :
        if (PFGFieldModel::isNameAlreadyTaken(Tools::getValue('name'), Tools::getValue('id_pfg'), (Tools::isSubmit('id_field') ? Tools::getValue('id_field') : null))) {
            $this->errors[] = $this->l('This name is already taken.');
        }

        $languages = Language::getLanguages(true);
        // if select || radio : values required
        if (in_array(Tools::getValue('type'), array('select', 'radio', 'checkbox', 'multicheckbox', 'hidden'))) {
            foreach ($languages as $language) {
                $value = Tools::getValue('values_'.$language['id_lang']);
                if (empty($value)) {
                    $this->errors[] = sprintf($this->l('You must indicates at least one value for "%s".'), $language['name']);
                }
            }
        } elseif (Tools::getValue('type') === 'file') {
            foreach ($languages as $language) {
                $value = Tools::getValue('values_'.$language['id_lang']);
                if (empty($value)) {
                    $this->errors[] = sprintf($this->l('You must indicates at least one file extension (without the dot) for "%s".'), $language['name']);
                } else {
                    $extensions = explode(',', $value);
                    foreach ($extensions as $ext) {
                        $ext = trim($ext);
                        if (Tools::substr($ext, 0, 1) === '.') {
                            $this->errors[] = $this->l('Values must contains valid file extensions (without the dot).');
                            break;
                        }
                    }
                }
            }
        }
    }
}
