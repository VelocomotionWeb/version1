<div class="menu_item mnajah">
	{if $is_seller == -1}
		<div class="block_content">
			<ul class="bullet">
				<li><a href="{if isset($seller_request_link)}{$seller_request_link|escape:'html':'UTF-8'}{else}{$link->getModuleLink('marketplace', 'sellerrequest')|escape:'html':'UTF-8'}{/if}" title="seller request">{l s='Seller Request' mod='marketplace'}</a></li>
			</ul>
		</div>
	{else if $is_seller == 0}
		<div class="block_content">
			<h3>{l s='Your request for seller has been send to admin approval' mod='marketplace'}</h3>
			{hook h="displayMpMenuInactiveSeller"}
		</div>
	{else if $is_seller == 1}
		<div class="list_content">
			<ul>
				<li><span class="menutitle">{l s='Marketplace' mod='marketplace'}</span></li>
				<li {if $logic == 1}class="menu_active"{/if}>
					<span>
						<a href="{if isset($dashboard_link)}{$dashboard_link|escape:'html':'UTF-8'}{else}{$link->getModuleLink('marketplace', 'dashboard')|addslashes}{/if}">{l s='Account Dashboard' mod='marketplace'}</a>
					</span>
				</li>
				<li {if $logic == 2}class="menu_active"{/if}>
					<span>
						<a href="{if isset($edit_profile_link)}{$edit_profile_link|escape:'html':'UTF-8'}{else}{$link->getModuleLink('marketplace', 'editprofile')|addslashes}{/if}">{l s='Edit Profile' mod='marketplace'}</a>
					</span>
				</li>
				<li>
					<span>
						<a href="{if isset($seller_profile_link)}{$seller_profile_link|escape:'html':'UTF-8'}{else}{$link->getModuleLink('marketplace', 'sellerprices', ['mp_shop_name' => $name_shop|escape:'html':'UTF-8'])|addslashes}{/if}">{l s='Tariff list' mod='marketplace'}</a>
					</span>
				</li>
				<li>
					<span>
						<a href="{if isset($shop_link)}{$shop_link|escape:'html':'UTF-8'}{else}{$link->getModuleLink('marketplace', 'shopstore', ['mp_shop_name' => $name_shop|escape:'html':'UTF-8'])|addslashes}{/if}" target="_blank">{l s='Your shop' mod='marketplace'}</a>
					</span>
				</li>
				<li>
					<span>
						<a href="/module/marketplace/list?city={$name_shop}&latitude={$latitude}&longitude={$longitude}&distance=0.1" target="_blank">{l s='Preview products' mod='marketplace'}</a>
					</span>
				</li>
				<li {if $logic == 'add_product'}class="menu_active"{/if}>
					<span>
						<a href="{if isset($add_product_link)}{$add_product_link|escape:'html':'UTF-8'}{else}{$link->getModuleLink('marketplace', 'addproduct')|addslashes}{/if}">{l s='Add Product' mod='marketplace'}</a>
					</span>
				</li>
                {*
				<li {if $logic == 3}class="menu_active"{/if}>
					<span>
						<a href="{if isset($product_list_link)}{$product_list_link|escape:'html':'UTF-8'}{else}{$link->getModuleLink('marketplace', 'productlist')|addslashes}{/if}">{l s='Product List' mod='marketplace'}</a>
					</span>
				</li>
                *}
				<li {if $logic == 4}class="menu_active"{/if}>
					<span>
						<a href="{if isset($my_order_link)}{$my_order_link|escape:'html':'UTF-8'}{else}{$link->getModuleLink('marketplace', 'mporder')|addslashes}{/if}">{l s='Orders' mod='marketplace'}</a>
					</span>
				</li>
				<li {if $logic == 5}class="menu_active"{/if}>
					<span>
						<a href="{if isset($payment_detail_link)}{$payment_detail_link|escape:'html':'UTF-8'}{else}{$link->getModuleLink('marketplace', 'mppayment')|addslashes}{/if}">{l s='Payment Detail' mod='marketplace'}</a>
					</span>
				</li>					
				{hook h="DisplayMpmenuhookext"}
			</ul>
		</div>
	{/if}
</div>