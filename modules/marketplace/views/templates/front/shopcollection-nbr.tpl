{if isset($p) AND $p}
	<!-- {if isset($smarty.get.id_category) && $smarty.get.id_category && isset($category)}
		{assign var='requestPage' value=$link->getPaginationLink('category', $category, false, false, true, false)}
		{assign var='requestNb' value=$link->getPaginationLink('category', $category, true, false, false, true)}
	{else}
		{assign var='requestPage' value=$link->getPaginationLink(false, false, false, false, true, false)}
		{assign var='requestNb' value=$link->getPaginationLink(false, false, true, false, false, true)}
	{/if} -->
	<!-- nbr product/page -->
	{if $nb_products > $nArray[0]}
		<form action="{$link->getModuleLink('marketplace', 'shopcollection', ['mp_shop_name' => $name_shop|escape:'htmlall':'UTF-8'])}" method="get" class="nbrItemPage">
			<div class="clearfix selector1">
				{if isset($search_query) AND $search_query}
					<input type="hidden" name="search_query" value="{$search_query|escape:'html':'UTF-8'}" />
				{/if}
				{if isset($tag) AND $tag AND !is_array($tag)}
					<input type="hidden" name="tag" value="{$tag|escape:'html':'UTF-8'}" />
				{/if}
				<label for="nb_item{if isset($paginationId)}_{$paginationId|escape:'htmlall':'UTF-8'}{/if}">
					{l s='Show' mod='marketplace'}
				</label>
				{if is_array($requestNb)}
					{foreach from=$requestNb item=requestValue key=requestKey}
						{if $requestKey != 'requestUrl'}
							<input type="hidden" name="{$requestKey|escape:'html':'UTF-8'}" value="{$requestValue|escape:'html':'UTF-8'}" />
						{/if}
					{/foreach}
				{/if}
				<select name="n" id="nb_item{if isset($paginationId)}_{$paginationId|escape:'htmlall':'UTF-8'}{/if}" class="form-control">
					{assign var="lastnValue" value="0"}
					{foreach from=$nArray item=nValue}
						{if $lastnValue <= $nb_products}
							<option value="{$nValue|escape:'html':'UTF-8'}" {if $n == $nValue}selected="selected"{/if}>{$nValue|escape:'html':'UTF-8'}</option>
						{/if}
						{assign var="lastnValue" value=$nValue}
					{/foreach}
				</select>
				<span>{l s='per page' mod='marketplace'}</span>
			</div>
		</form>
	{/if}
	<!-- /nbr product/page -->
{/if}