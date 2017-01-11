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

<!-- MODULE Block best sellers -->
<div id="best-sellers_block_right"> 
	<div class="slider-product-title">
		<h3 class="title">
			<span>{l s='Best sellers' mod='blockbestsellers'}</span>
			<span class="icon-title"></span>
		</h3>
		<p>{l s='Donec hendrerit integer sollicitudin felis quis maximus ultrices' mod='blockbestsellers'}</p>
	</div>
    <div class="row">
        {if $best_sellers && $best_sellers|@count > 0}
            <div class="product_images product-carousel2">
                {foreach from=$best_sellers item=product name=myLoop}
                    <div class="item ajax_block_product {if $smarty.foreach.myLoop.first}first_item{elseif $smarty.foreach.myLoop.last}last_item{else}item{/if} clearfix" itemscope itemtype="http://schema.org/Product">
						<div class="product-preview {if $phover == 'image_swap'}image_swap{/if}">
							<div class="preview"> 
								<a href="{$product.link|escape:'html'}" class="preview-image product_img_link image-rollover" data-id-product="{$product.id_product}" itemprop="url">
									<img  class="img-responsive product-img1" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html'}" alt="{$product.legend|escape:'html':'UTF-8'}"/>
								</a>
								{if (!$PS_CATALOG_MODE && ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
								<div itemprop="offers" itemscope itemtype="https://schema.org/Offer">
									{if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}
										{if $product.price_without_reduction > 0 && isset($product.specific_prices) && $product.specific_prices && isset($product.specific_prices.reduction) && $product.specific_prices.reduction > 0}
											{hook h="displayProductPriceBlock" product=$product type="old_price"}
											{if $product.specific_prices.reduction_type == 'percentage'}
												<div class="wrapper-label wrapper-label-sale">
													<div class="label label-sale">
														-{$product.specific_prices.reduction * 100}%
													</div>
												</div>
											{/if}
										{/if}
									{/if}
								</div>
								{/if}
								<div class="preview-button">									
									<a data-link="{$product.link|escape:'html':'UTF-8'}" class="quick-view product-btn hidden-xs" title="{l s='Quick View'}">
										<span class="fa fa-external-link"></span>
									</a>
								</div>
							</div>
							<div class="product-info">
								<h3 class="title pull-left" itemprop="name">
									<a href="{$product.link|escape:'html'}" title="{$product.legend|escape:'html':'UTF-8'}" itemprop="url">
										{$product.name|truncate:18:''|strip_tags:'UTF-8'|escape:'html':'UTF-8'}
									</a>
								</h3>
								{if (!$PS_CATALOG_MODE && ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
								<div class="content_price pull-right" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
									{if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}
										<span itemprop="price" class="price product-price">
											{hook h="displayProductPriceBlock" product=$product type="before_price"}
											{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}
										</span>
										<meta itemprop="priceCurrency" content="{$currency->iso_code}" />
										{if $PS_STOCK_MANAGEMENT && isset($product.available_for_order) && $product.available_for_order && !isset($restricted_country_mode)}
											<span class="unvisible">
												{if ($product.allow_oosp || $product.quantity > 0)}
														<link itemprop="availability" href="https://schema.org/InStock" />{if $product.quantity <= 0}{if $product.allow_oosp}{if isset($product.available_later) && $product.available_later}{$product.available_later}{else}{l s='In Stock'}{/if}{/if}{else}{if isset($product.available_now) && $product.available_now}{$product.available_now}{else}{l s='In Stock'}{/if}{/if}
												{elseif (isset($product.quantity_all_versions) && $product.quantity_all_versions > 0)}
														<link itemprop="availability" href="https://schema.org/LimitedAvailability" />{l s='Product available with different options'}

												{else}
														<link itemprop="availability" href="https://schema.org/OutOfStock" />{l s='Out of stock'}
												{/if}
											</span>
										{/if}
										{hook h="displayProductPriceBlock" product=$product type="price"}
										{hook h="displayProductPriceBlock" product=$product type="unit_price"}
									{/if}
								</div>
								{/if}
								{hook h='displayProductListReviews' product=$product}
								<div class="product_description_short">
									{$product.description_short|strip_tags:'UTF-8'|truncate:60:''}	
								</div>
								{if ($product.id_product_attribute == 0 OR (isset($add_prod_display) AND ($add_prod_display == 1))) AND $product.available_for_order AND !isset($restricted_country_mode) AND $product.minimal_quantity == 1 AND $product.customizable != 2 AND !$PS_CATALOG_MODE}
									{if ($product.quantity > 0 OR $product.allow_oosp)}
										<a class="exclusive ajax_add_to_cart_button cart-button product-btn" data-id-product="{$product.id_product}" href="{$link->getPageLink('cart')|escape:'html':'UTF-8'}?qty=1&amp;id_product={$product.id_product}&amp;token={$static_token}&amp;add" title="{l s='Book now'}">
											<span>{l s='Add To Cart' mod='blockbestsellers'}</span>
											<span class="lnr lnr-sync"></span>
										</a>							
									{else}
										<a href="#" class="disable cart-button product-btn" title="{l s='Out of Stock'}">
											<span>{l s='Add To Cart' mod='blockbestsellers'}</span>
										</a>
									{/if}										
									{/if}
								{if isset($product.is_virtual) && !$product.is_virtual}{hook h="displayProductDeliveryTime" product=$product}{/if}
								{hook h="displayProductPriceBlock" product=$product type="weight"}
							</div>
						</div>
                    </div>
                {/foreach}
            </div>
        {else}
            <p>{l s='No best sellers at this time' mod='blockbestsellers'}</p>
        {/if}
    </div>
</div>
<!-- /MODULE Block best sellers -->
