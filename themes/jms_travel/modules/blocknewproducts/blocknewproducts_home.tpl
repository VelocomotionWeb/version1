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
{if isset($new_products) && $new_products}
<div id="blocknewproducts" class="newproducts tab-pane active">  
	<div class="row">
		<div class="product-carousel">
			{assign var='liHeight' value=250}
			{assign var='nbItemsPerLine' value=4}
			{assign var='nbLi' value=$new_products|@count}
			{math equation="nbLi/nbItemsPerLine" nbLi=$nbLi nbItemsPerLine=$nbItemsPerLine assign=nbLines}
			{math equation="nbLines*liHeight" nbLines=$nbLines|ceil liHeight=$liHeight assign=ulHeight}
			{foreach from=$new_products item=product name=jms_newproducts}
				<div class="item ajax_block_product animation">
					<div class="product-preview">
						<div class="preview"> 
							<a href="{$product.link|escape:'html'}" class="preview-image product_img_link"><img class="img-responsive animate scale" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html'}" alt="" /></a>
								{if isset($product.on_sale) && $product.on_sale && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}
									<div class="label label-new">
										<span>{l s='New' mod='jms_newproducts'}</span>
									</div>																	
								{/if}
								<a rel="{$product.link|escape:'html'}" class="quick-view product-btn hidden-xs" title="{l s='Quick View'}"><i class="fa fa-external-link"></i></a>                				
								{if ($product.id_product_attribute == 0 OR (isset($add_prod_display) AND ($add_prod_display == 1))) AND $product.available_for_order AND !isset($restricted_country_mode) AND $product.minimal_quantity == 1 AND $product.customizable != 2 AND !$PS_CATALOG_MODE}
									{if ($product.quantity > 0 OR $product.allow_oosp)}
										<a class="ajax_add_to_cart_button product-btn cart-button" rel="ajax_id_product_{$product.id_product}" href="{$link->getPageLink('cart')|escape:'html'}?qty=1&amp;id_product={$product.id_product}&amp;token={$static_token}&amp;add" title="{l s='Add to cart' mod='jms_newproducts'}"><i class="fa fa-shopping-cart"></i><i class="fa fa-check"></i></a>
									{else}
										<a href="#" class="disable product-btn cart-button" title="{l s='Add to cart' mod='jms_newproducts'}"><i class="fa fa-shopping-cart"></i></a>
									{/if}																		
								{/if}
						</div>
						<div class="product-info">			
							<div class="pull-left">								
								<h3 class="title"><a href="{$product.link|escape:'html'}">{$product.name|truncate:25:'...'|escape:'html':'UTF-8'}</a></h3>								
								{hook h='displayProductListReviews' product=$product}
							</div>	
							<div class="pull-right">
								{if isset($product.specific_prices) && $product.specific_prices && isset($product.specific_prices.reduction) && $product.specific_prices.reduction > 0}
								{hook h="displayProductPriceBlock" product=$product type="old_price"}								
								<span class="old price">
									{displayWtPrice p=$product.price_without_reduction}
								</span>	
								{/if}
								{if $product.show_price AND !isset($restricted_country_mode) AND !$PS_CATALOG_MODE}<span class="price new ">{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}</span>{else}{/if}	
							</div>
						</div>				 
					</div>
				</div>
			{/foreach}
		</div>
	</div>
</div>    
{/if}
