<?php
class MarketplaceProductImageEditModuleFrontController extends ModuleFrontController
{
	public function init()
	{
		$this->display_header = false;
		$this->display_footer = false;
	}

	public function initContent()
	{
		//parent::initContent();
		$id_lang = $this->context->cookie->id_lang;
		$link = new Link();
		$obj_mp_product = new SellerProductDetail();

		$seller_product_id = Tools::getValue('id_product');
		$image_id = Tools::getValue('id_image');
		$is_delete = Tools::getValue('is_delete');
		$unactive_img = Tools::getValue('unactive');
		$changecover = Tools::getValue('changecover');

		$img_ps_dir = _MODULE_DIR_."marketplace/views/img/";
		$modules_dir = _MODULE_DIR_;

		if ($seller_product_id) 
		{
			
			$is_product_onetime_activate = $obj_mp_product->getMarketPlaceShopProductDetailBYmspid($seller_product_id);
			if ($is_product_onetime_activate) 
			{
				$id_product = $is_product_onetime_activate['id_product'];
				$product = new Product($id_product, false, $id_lang);
				$image_detail = $product->getImages($id_lang);

				if (!empty($image_detail))
				{
					$image_type = Tools::getValue('image_type');
					foreach($image_detail as $key => $image)
					{
						$obj_image = new Image($image['id_image']);
						$image_detail[$key]['image_path'] = _THEME_PROD_DIR_.$obj_image->getExistingImgPath().'.jpg';
						$image_detail[$key]['image_link'] = $link->getImageLink($product->link_rewrite, $id_product.'-'.$image['id_image'], $image_type);
						$image_detail[$key]['image_fancybox'] = $link->getImageLink($product->link_rewrite, $id_product.'-'.$image['id_image']);
					}

					$this->context->smarty->assign('image_detail', $image_detail);
					$this->context->smarty->assign('id_product', $id_product);
				}

				$unactive_image = $obj_mp_product->unactiveImage($seller_product_id);
				if ($unactive_image)
					$this->context->smarty->assign('unactive_image', $unactive_image);

				$this->context->smarty->assign('product_activated', 1);
			}
			else
			{
				$unactive_image_only = $obj_mp_product->unactiveImage($seller_product_id);
				if ($unactive_image_only)
					$this->context->smarty->assign("unactive_image_only", $unactive_image_only);
			}

			$this->context->smarty->assign('img_ps_dir', $img_ps_dir);
			$this->context->smarty->assign('modules_dir', $modules_dir);
			$this->setTemplate('imageedit.tpl');
		}

		//Delete active image
		if ($image_id && $is_delete)
		{
			$id_image = Tools::getValue('id_image');
			$is_cover = Tools::getValue('is_cover');
			$id_product = Tools::getValue('id_pro');
			$image = new Image($id_image);
			$status = $image->delete();
			Product::cleanPositions($id_image );
			$delete =  Db::getInstance()->delete('image','id_image='.$id_image .' and id_product='.$id_image);
			if ($status)
			{
				// if cover image deleting, make first image as a cover
				if ($is_cover)
				{
					$images = Image::getImages($id_lang, $id_product);
					if ($images)
					{
						$obj_image = new Image($images[0]['id_image']);
						$obj_image->cover = 1;
						$obj_image->save();	
					}
					echo 2; // if cover image deleted
				}
				else
					echo 1;
			}
			else
				echo 0;
		}

		//Delete unactive image
		if ($image_id && $unactive_img)
		{
			$id_image = Tools::getValue('id_image');
			$img_name = Tools::getValue('img_name');
			$delete =  Db::getInstance()->delete("marketplace_product_image","id=".$id_image." and seller_product_image_id	='".$img_name."'");
			$dir = _PS_MODULE_DIR_.'marketplace/views/img/product_img/';
			
			if ($delete) 
			{
				unlink($dir.$img_name.'.jpg');
				echo 1;
			} 
			else
				echo 0;
		}

		//Change covor status
		if ($image_id && $changecover)
		{
			$id_image = Tools::getValue('id_image');
			if ($id_image)
			{
				$is_cover = Tools::getValue('is_cover');
				$id_pro = Tools::getValue('id_pro');
				/*$product = new Product($id_pro);
				$product->setCoverWs($id_image);*/
				Image::deleteCover((int)$id_pro);
				$img = new Image((int)$id_image);
				$img->cover = 1;
				$img->save();
				echo 1;
			} 
			else
				echo 0;
		}
	}

	public function setMedia() 
 	{
		parent::setMedia();
		$this->addCSS(_MODULE_DIR_.'marketplace/views/css/image_edit.css');
 	} 
}
?>