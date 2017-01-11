{if $mp_menu == 1}
	<li class="lnk_wishlist" >
		<a href="{$link->getModuleLink('mpcombination', 'productattribute', ['shop' => $id_shop|escape:'htmlall':'UTF-8'])|escape:'htmlall':'UTF-8'}" title="{l s='Product Attribute' mod='mpcombination'}">
			<i class="icon-asterisk"></i>
			<span>{l s='Product Attribute' mod='mpcombination'}</span>
		</a>
	</li>
	{else}
	<li {if $logic=='mp_prod_attribute'}class="menu_active"{/if}>
		<span>
			<a href="{$link->getModuleLink('mpcombination', 'productattribute', ['shop' => $id_shop|escape:'htmlall':'UTF-8'])|escape:'htmlall':'UTF-8'}" title="{l s='Product Attribute' mod='mpcombination'}">
				{l s='Product Attribute' mod='mpcombination'}
			</a>
		</span>
	</li>
{/if}