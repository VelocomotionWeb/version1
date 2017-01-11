{*
* 2007-2014 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{if isset($categoryProducts) && count($categoryProducts) > 0 && $categoryProducts !== false}
<section class="slider-products content-box">	
	<div class="slider-product-title">
		<h3 class="title">
			<span>{l s='Related Tours' mod='homefeatured'}</span>
			<span class="icon-title"></span>
		</h3>
	</div>
	<div class="products_category">
		<div class="row">
			<div class="product-carousel">		
				{foreach from=$categoryProducts item='categoryProduct' name=categoryProduct}
				<div class="item ajax_block_product"  itemscope itemtype="http://schema.org/Product">
					<div class="product-preview {if $phover == 'image_swap'}image_swap{/if}">
						<div class="preview"> 
							<a href="{$link->getProductLink($categoryProduct.id_product, $categoryProduct.link_rewrite, $categoryProduct.category, $categoryProduct.ean13)}" class="preview-image product_img_link image-rollover" data-id-product="{$categoryProduct.id_product}" itemprop="url">
								<img class="img-responsive product-img1" src="{$link->getImageLink($categoryProduct.link_rewrite, $categoryProduct.id_image, 'home_default')|escape:'html'}" alt="" itemprop="image">
							</a>
							{if isset($categoryProduct.on_sale) && $categoryProduct.on_sale && isset($categoryProduct.show_price) && $categoryProduct.show_price && !$PS_CATALOG_MODE}
								<div class="wrapper-label wrapper-label-sale">
									<div class="label label-sale">
										{l s='Sale' mod='productscategory'}
									</div>
								</div>
							{elseif isset($categoryProduct.new) && $categoryProduct.new == 1}
								<div class="wrapper-label wrapper-label-new">
									<div class="label label-new">
										{l s='New' mod='productscategory'}
									</div>
								</div>
							{/if}
							<div class="preview-button">								
								<a data-link="{$categoryProduct.link|escape:'html'}" class="quick-view product-btn hidden-xs" title="{l s='Quick View'}">
									<span class="fa fa-external-link"></span>
								</a>
							</div>
						</div>
						<div class="product-info">	
							<h3 class="title pull-left" itemprop="name">
								<a href="{$link->getProductLink($categoryProduct.id_product, $categoryProduct.link_rewrite, $categoryProduct.category, $categoryProduct.ean13)}" itemprop="url">{$categoryProduct.name|escape:'html':'UTF-8'}</a>						
							</h3>
							<div class="content_price pull-right" itemprop="offers" itemscope itemtype="http://schema.org/Offer">						
								{if $categoryProduct.show_price AND !isset($restricted_country_mode) AND !$PS_CATALOG_MODE}					
								{if isset($categoryProduct.specific_prices) && $categoryProduct.specific_prices && isset($categoryProduct.specific_prices.reduction) && $categoryProduct.specific_prices.reduction > 0}
									<!--<span class="old price">
										{displayWtPrice p=$categoryProduct.price_without_reduction}
									</span>-->							
								{/if}
								<span class="price new"  itemprop="price">{if !$priceDisplay}{convertPrice price=$categoryProduct.price}{else}{convertPrice price=$categoryProduct.price_tax_exc}{/if}</span>	
								{/if}
								<meta itemprop="priceCurrency" content="{$currency->iso_code}" />
							</div>
							<div class="product_description_short">
								{$categoryProduct.description_short|strip_tags:'UTF-8'|truncate:80:''}	
							</div>
							{if ($categoryProduct.id_product_attribute == 0 OR (isset($add_prod_display) AND ($add_prod_display == 1))) AND $categoryProduct.available_for_order AND !isset($restricted_country_mode) AND $categoryProduct.minimal_quantity == 1 AND $categoryProduct.customizable != 2 AND !$PS_CATALOG_MODE}
								{if ($categoryProduct.quantity > 0 OR $categoryProduct.allow_oosp)}
									<a class="exclusive ajax_add_to_cart_button cart-button product-btn hvr-sweep-to-right" data-id-product="{$categoryProduct.id_product}" href="{$link->getPageLink('cart')|escape:'html'}?qty=1&amp;id_product={$categoryProduct.id_product}&amp;token={$static_token}&amp;add" title="{l s='Add to cart'}">
										<span>{l s='Add To Cart' mod='productscategory'}</span>
									</a>							
								{else}
									<a href="#" class="disable product-btn cart-button" title="{l s='Add to cart'}">
										<span>{l s='Add To Cart' mod='productscategory'}</span>
									</a>
								{/if}										
							{/if}
						</div>
					</div>				
				</div>
				{/foreach}
			</div>
		</div>
	</div>
</section>
{/if}
