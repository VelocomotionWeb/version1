<?php
/**
 * 2011-2016 JUML69
 *
 *  @author    JUML69 <contact@lyondev.fr>
 *  @copyright 2011-2016 JUML69
 *  @version   Release:1
 *  @license   One Domain Licence
 */
class AdminCmsCategoriesController extends AdminCmsCategoriesControllerCore
{
    /*
    * module: lyopageeditor
    * date: 2016-12-12 16:43:02
    * version: 1.1.5
    */
    public function renderForm()
    {
        parent::renderForm();
        $this->fields_form = $this->fields_form[0]['form'];
        $this->fields_form['tinymce']=true;
        $this->fields_form['input'][3]['autoload_rte']=true;
        $this->fields_form['input'][3]['type']='textarea';
        $this->fields_form['input'][3]['hint']='Your description';
        return AdminController::renderForm();
    }
}
