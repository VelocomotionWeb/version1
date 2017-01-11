<style type="text/css">
.col-lg-4{
	clear:both !important;
}
</style>
{hook h="displayMpMyAccountMenuTop"}
<h1 class="page-heading">{l s='Marketplace Account' mod='marketplace'}</h1>
<p class="info-account">{l s='Here you can manage marketplace shop.' mod='marketplace'}</p>
{if $is_seller == 1}
	<li>
		<a title="{l s='Account Dashboard' mod='marketplace'}" href="{if isset($dashboard_link)}{$dashboard_link|escape:'html':'UTF-8'}{else}{$link->getModuleLink('marketplace', 'dashboard')|escape:'html':'UTF-8'}{/if}">
			<i class="icon-dashboard"></i>
			<span>{l s='Account Dashboard' mod='marketplace'}</span>
		</a>
	</li>
	<li>
		<a title="{l s='Seller Profile' mod='marketplace'}" href="{if isset($seller_profile_link)}{$seller_profile_link|escape:'html':'UTF-8'}{else}{$link->getModuleLink('marketplace', 'sellerprofile', ['mp_shop_name' => $name_shop])|escape:'html':'UTF-8'}{/if}">
			<i class="icon-file"></i>
			<span>{l s='Seller Profile' mod='marketplace'}</span>
		</a>
	</li>
	<li>
		<a title="{l s='View Shop' mod='marketplace'}" href="{if isset($shop_link)}{$shop_link|escape:'html':'UTF-8'}{else}{$link->getModuleLink('marketplace', 'shopstore', ['mp_shop_name' => $name_shop|escape:'html':'UTF-8'])|escape:'html':'UTF-8'}{/if}">
			<i class="icon-shopping-cart"></i>
			<span>{l s='Shop' mod='marketplace'}</span>
		</a>
	</li>
	<li>
		<a title="{l s='View Collection' mod='marketplace'}" href="{if isset($collection_link)}{$collection_link|escape:'html':'UTF-8'}{else}{$link->getModuleLink('marketplace', 'shopcollection', ['mp_shop_name' => $name_shop])|escape:'html':'UTF-8'}{/if}">
			<i class="icon-tags"></i>
			<span>{l s='Collection' mod='marketplace'}</span>
		</a>
	</li>
	<li>
		<a title="{l s='Add product' mod='marketplace'}" href="{if isset($add_product_link)}{$add_product_link|escape:'html':'UTF-8'}{else}{$link->getModuleLink('marketplace', 'addproduct')|escape:'html':'UTF-8'}{/if}">
			<i class="icon-plus"></i>
			<span>{l s='Add Product' mod='marketplace'}</span>
		</a>
	</li>
	<li>
		<a title="{l s='Product List' mod='marketplace'}" href="{if isset($product_list_link)}{$product_list_link|escape:'html':'UTF-8'}{else}{$link->getModuleLink('marketplace', 'productlist')|escape:'html':'UTF-8'}{/if}">
			<i class="icon-list"></i>
			<span>{l s='Product List' mod='marketplace'}</span>
		</a>
	</li>
	<li>
		<a title="{l s='My Order' mod='marketplace'}" href="{if isset($my_order_link)}{$my_order_link|escape:'html':'UTF-8'}{else}{$link->getModuleLink('marketplace', 'mporder')|escape:'html':'UTF-8'}{/if}">
			<i class="icon-gift"></i>
			<span>{l s='Orders' mod='marketplace'}</span>
		</a>
	</li>
	<li>
		<a title="{l s='Payment Detail' mod='marketplace'}" href="{if isset($payment_detail_link)}{$payment_detail_link|escape:'html':'UTF-8'}{else}{$link->getModuleLink('marketplace', 'mppayment')|escape:'html':'UTF-8'}{/if}">
			<i class="icon-money"></i>
			<span>{l s='Payment Detail' mod='marketplace'}</span>
		</a>
	</li>
	{hook h="displayMpMyAccountMenuActiveSeller"}
{else if $is_seller == 0}
	<div class="alert alert-info" role="alert">
		<span>{l s='Your request has been already sent to admin. Please wait for admin approval' mod='marketplace'}</span>
	</div>
	{hook h="displayMpMyAccountMenuInactiveSeller"}
{else if $is_seller == -1}
	<li>
		<a title="{l s='Click Here for Seller Request' mod='marketplace'}" href="{$link->getModuleLink('marketplace', 'sellerrequest')|escape:'html':'UTF-8'}">
			<i class="icon-mail-reply-all"></i>
			<span>{l s='Click Here for Seller Request' mod='marketplace'}</span>
		</a>
	</li>
	{hook h="displayMpMyAccountMenuSellerRequest"}
{/if}
