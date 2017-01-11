<?php
/**
 * 2011-2016 JUML69
 *
 *  @author    JUML69 <contact@lyondev.fr>
 *  @copyright 2011-2016 JUML69
 *  @version   Release:1
 *  @license   One Domain Licence
 */

class Product extends ProductCore
{

    public function __construct($id_product = null, $full = false, $id_lang = null, $id_shop = null, Context $context = null)
    {
        unset(self::$definition['fields']['description']['validate']);
        parent::__construct($id_product, $full, $id_lang, $id_shop, $context);
    }
}
