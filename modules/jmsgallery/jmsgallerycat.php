<?php
/**
* 2007-2014 PrestaShop
*
* Jms Gallery
*
*  @author    Joommasters <joommasters@gmail.com>
*  @copyright 2007-2014 Joommasters
*  @license   license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*  @Website: http://www.joommasters.com
*/

class JmsGalleryCat extends ObjectModel
{
	public $name_category;
	public $description;
	public $jmsparent;
	public $image;
	public $active;
	public static $definition = array(
		'table' => 'jmsgallery_category',
		'primary' => 'id_category',
		'multilang' => true,
		'fields' => array(
			'name_category' =>	array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'required' => true, 'size' => 255),
			'description' 	=>	array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 4000),
			'image' 		=>	array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 255),
			'jmsparent'		=>	array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
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
		$images = $this->image;
		if (preg_match('/jmsgallery/', $images) === 0)
			if ($images && file_exists(dirname(__FILE__).'/views/img/'.$images))
			{
				$res &= @unlink(dirname(__FILE__).'/views/img/'.$images);
				$res &= @unlink(dirname(__FILE__).'/views/img/resized_'.$images);
			}
		$res &= parent::delete();
		return $res;
	}

}