<?php

class Category extends CategoryCore
{
    /*
    * module: pagecache
    * date: 2016-06-06 13:15:54
    * version: 3.02
    */
    public function checkAccess($id_customer)
    {
        $context = Context::getContext();
        if (!$id_customer
            && isset($context->cookie)
            && isset($context->cookie->pc_groups)) {
            $groups = explode(',', $context->cookie->pc_groups);
            if ($groups !== false && count($groups) > 0) {
                $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
                    SELECT ctg.`id_group`
                    FROM '._DB_PREFIX_.'category_group ctg
                    WHERE ctg.`id_category` = '.(int)$this->id . ' AND ctg.`id_group` IN(' . implode(',', $groups) . ')'
                );
                if ($result && isset($result['id_group']) && $result['id_group'])
                    return true;
                return false;
            }
        }
        return parent::checkAccess($id_customer);
    }
    /*
    * module: lyopageeditor
    * date: 2016-12-12 16:43:02
    * version: 1.1.5
    */
    public function __construct($id_category = null, $id_lang = null, $id_shop = null)
    {
        unset(self::$definition['fields']['description']['validate']);
        parent::__construct($id_category, $id_lang, $id_shop);
    }
}
