<form id="productsSortForm{if isset($paginationId)}_{$paginationId|escape:'html':'UTF-8'}{/if}" action="{$request|escape:'html':'UTF-8'}" class="productsSortForm">
	<div class="select selector1">
		<label for="selectMpProductSort{if isset($paginationId)}_{$paginationId|escape:'html':'UTF-8'}{/if}">{l s='Sort by' mod='marketplace'}</label>
		<select id="selectMpProductSort{if isset($paginationId)}_{$paginationId|escape:'html':'UTF-8'}{/if}" class="selectMpProductSort form-control">
			<option value="{$defaultorederby|escape:'html':'UTF-8'}"{if $orderby eq 'id' AND $orderway eq 'desc'} selected="selected"{/if}>--</option>
			{if !$PS_CATALOG_MODE}
				<option value="price:asc"{if $orderby eq 'price' AND $orderway eq 'asc'} selected="selected"{/if}>{l s='Price: Lowest first' mod='marketplace'}</option>
				<option value="price:desc"{if $orderby eq 'price' AND $orderway eq 'desc'} selected="selected"{/if}>{l s='Price: Highest first' mod='marketplace'}</option>
			{/if}
			<option value="name:asc"{if $orderby eq 'name' AND $orderway eq 'asc'} selected="selected"{/if}>{l s='Product Name: A to Z' mod='marketplace'}</option>
			<option value="name:desc"{if $orderby eq 'name' AND $orderway eq 'desc'} selected="selected"{/if}>{l s='Product Name: Z to A' mod='marketplace'}</option>
			{if $PS_STOCK_MANAGEMENT && !$PS_CATALOG_MODE}
				<option value="quantity:desc"{if $orderby eq 'quantity' AND $orderway eq 'desc'} selected="selected"{/if}>{l s='In stock' mod='marketplace'}</option>
			{/if}
		</select>
	</div>
</form>

<!-- /Sort products -->
{if !isset($paginationId) || $paginationId == ''}
	{addJsDef request=$request}
{/if}