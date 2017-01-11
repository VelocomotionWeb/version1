<?php
/**
* 2007-2014 PrestaShop
*
* Jms Gallery Item
*
*  @author    Joommasters <joommasters@gmail.com>
*  @copyright 2007-2014 Joommasters
*  @license   license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*  @Website: http://www.joommasters.com
*/

class JmsGalleryItem extends ObjectModel
{
	public $title;
	public $id_category;
	public $description;
	public $date_add;
	public $date_update;
	public $image;
	public $active;
	public static $definition = array(
		'table' => 'jmsgallery_item',
		'primary' => 'id_item',
		'multilang' => true,
		'fields' => array(
			'title' 		=>	array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'required' => true, 'size' => 255),
			'description' 	=>	array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'),
			'date_add' 		=>	array('type' => self::TYPE_DATE, 'validate' => 'isCleanHtml', 'size' => 255),
			'date_update' 	=>	array('type' => self::TYPE_DATE, 'validate' => 'isCleanHtml', 'size' => 255),
			'image' 		=>	array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 255),
			'id_category'	=>	array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
			'active' 		=>	array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
		)
	);

	public	function __construct($item_id = null, $id_lang = null, $id_shop = null)
	{
		parent::__construct($item_id, $id_lang, $id_shop);
	}

	public function add($autodate = true, $null_values = false)
	{
		$res = true;
		$res = parent::add($autodate, $null_values);
		return $res;
	}

	public function delete()
	{
		$res = true;
		$image = $this->image;
		if (preg_match('/jmsgallery/', $image) === 0)
			if ($image && file_exists(dirname(__FILE__).'/views/img/'.$image))
			{
				$res &= @unlink(dirname(__FILE__).'/views/img/'.$image);
				$res &= @unlink(dirname(__FILE__).'/views/img/resized_'.$image);
			}
		$res &= parent::delete();
		return $res;
	}
}