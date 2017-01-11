<?php
/**
 * 2011-2016 JUML69
 *
 *  @author    JUML69 <contact@lyondev.fr>
 *  @copyright 2011-2016 JUML69
 *  @version   Release:1
 *  @license   One Domain Licence
 */

class CMS extends CMSCore
{

    /**
     * Builds the object
     *
     * @param int|null $id
     *            If specified, loads and existing object from DB (optional).
     * @param int|null $id_lang
     *            Required if object is multilingual (optional).
     * @param int|null $id_shop
     *            ID shop for objects with multishop tables.
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        unset(self::$definition['fields']['content']['validate']);
        return parent::__construct($id, $id_lang, $id_shop);
    }
}
