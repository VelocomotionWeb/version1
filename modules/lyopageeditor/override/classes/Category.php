<?php
/**
 * 2011-2016 JUML69
 *
 *  @author    JUML69 <contact@lyondev.fr>
 *  @copyright 2011-2016 JUML69
 *  @version   Release:1
 *  @license   One Domain Licence
 */

class Category extends CategoryCore
{

    public function __construct($id_category = null, $id_lang = null, $id_shop = null)
    {
        unset(self::$definition['fields']['description']['validate']);
        parent::__construct($id_category, $id_lang, $id_shop);
    }
}
