<div class="wk_catg_list">
	<ul>
		<li>
			<span class="wk_catg_head">
				{l s='Seller Category List' mod='marketplace'}
			</span>
		</li>
		{if isset($catg_details)}
			{foreach $catg_details as $catg}
			<li>
				<span>
					<a href="{$link->getModuleLink('marketplace', 'shopcollection', ['mp_shop_name' => $name_shop, 'id_category' => $catg.id_category])|escape:'html':'UTF-8'}">
						{$catg.Name|escape:'html':'UTF-8'} ({$catg.NoOfProduct|escape:'html':'UTF-8'})
					</a>
				</span>
			</li>
			{/foreach}
		{/if}
	</ul>
</div>