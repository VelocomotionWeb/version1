{*
* 2007-2014 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
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
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<!-- MODULE Block cart -->
<div class="btn-group compact-hidden" id="cart_block">
	<a href="#"  class="btn-xs dropdown-toggle box-group" data-toggle="dropdown">
		<span class="fa fa-shopping-cart"></span>
		<span class="text-box">{l s='Cart' mod='blockcart'}</span>
		<span class="box ajax_cart_quantity">{$cart_qties}</span>
	</a>		
	<div class="dropdown-menu shoppingcart-box" role="menu">	          
        <span class="ajax_cart_no_product" {if $cart_qties != 0}style="display:none"{/if}>{l s='There is no product' mod='blockcart'}</span>		
		<ul class="list products" id="cart_block_list">
		{if $products}
		{foreach from=$products item='product' name='myLoop'}
			{assign var='productId' value=$product.id_product}
			{assign var='productAttributeId' value=$product.id_product_attribute}
			<li id="cart_block_product_{$product.id_product}_{if $product.id_product_attribute}{$product.id_product_attribute}{else}0{/if}_{if $product.id_address_delivery}{$product.id_address_delivery}{else}0{/if}" class="{if $smarty.foreach.myLoop.first}first_item item{elseif $smarty.foreach.myLoop.last}last_item item{else}item{/if}">
				{*
                <a class="preview-image" href="{$link->getProductLink($product.id_product, $product.link_rewrite, $product.category)|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}">
                *}
                <img alt="{$product.name|escape:'html':'UTF-8'}" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'cart_default')}" class="preview">
				{*
                </a>
                *}
				<div class="description"> 
				{*
					<a href="{$link->getProductLink($product, $product.link_rewrite, $product.category, null, null, $product.id_shop, $product.id_product_attribute)|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}">
                *}
                    {$product.name|truncate:'15':''|escape:'html':'UTF-8'}
                    </a>				 								
					<div class="price-quantity">
						<span class="price">{if $priceDisplay == $smarty.const.PS_TAX_EXC}{displayWtPrice p="`$product.total`"}{else}{displayWtPrice p="`$product.total_wt`"}{/if}</span>
						<!--<span class="quantity-formated"> x<span class="quantity">{$product.cart_quantity}</span></span>-->
					</div>
				</div>
				<span class="remove_link">
					{if !isset($customizedDatas.$productId.$productAttributeId) && (!isset($product.is_gift) || !$product.is_gift)}
						<a rel="nofollow" class="ajax_cart_block_remove_link" href="{$link->getPageLink('cart', true, NULL, "delete=1&amp;id_product={$product.id_product}&amp;ipa={$product.id_product_attribute}&amp;id_address_delivery={$product.id_address_delivery}&amp;token={$static_token}", true)|escape:'html'}" title="{l s='Please remove this product from my cart.' mod='blockcart'}">
							<span class="fa fa-times"></span>
						</a>
					{/if}
				</span>
				{hook h='displayProductListReviews' product=$product}
			</li>
		{/foreach}
		{/if}	
		</ul>			
{* 		<div class="shipping">{l s='Shipping' mod='blockcart'}:	<span id="cart_block_shipping_cost" class="ajax_cart_shipping_cost">{$shipping_cost}</span></div> *}
		<div class="total">{l s='Total' mod='blockcart'}: <span class="cart_block_total ajax_block_cart_total">{$total}</span></div>
		<a id="button_order_cart" class="btn btn-default" href="{$link->getPageLink("$order_process", true)|escape:"html":"UTF-8"}" title="{l s='Check out' mod='blockcart'}" rel="nofollow">
			<span>
				{l s='Check out' mod='blockcart'}
			</span>
		</a>        
	</div>
</div>	
{strip}
{addJsDef CUSTOMIZE_TEXTFIELD=$CUSTOMIZE_TEXTFIELD}
{addJsDef img_dir=$img_dir|addslashes}
{addJsDef generated_date=$smarty.now|intval}

{addJsDefL name=customizationIdMessage}{l s='Customization #' mod='blockcart' js=1}{/addJsDefL}
{addJsDefL name=removingLinkText}{l s='remove this product from my cart' mod='blockcart' js=1}{/addJsDefL}
{addJsDefL name=freeShippingTranslation}{l s='Free shipping!' mod='blockcart' js=1}{/addJsDefL}
{addJsDefL name=freeProductTranslation}{l s='Free!' mod='blockcart' js=1}{/addJsDefL}
{addJsDefL name=delete_txt}{l s='Delete' mod='blockcart' js=1}{/addJsDefL}
{/strip}
<!-- /MODULE Block cart -->