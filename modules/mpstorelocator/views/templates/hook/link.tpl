<!-- 
	CSS of these classes(pg-seller-block,pg-seller-product) on this tpl file
	are already defined in Marketplace Responsive Theme,
	So please do not change class names and also design
-->
{if !isset($mptheme)}
<style type="text/css">
	.pg-seller-block{
		text-align: center;
		padding:10px;
		border-top: 1px solid #dfdede;
	}
</style>
{/if}

<div class="pg-seller-block">
	<h3>{l s='Store Locator' mod='mpstorelocator'}</h3>
	<div class="pg-seller-product">
		<a href="{$store_link}" class="btn btn-default button button-small">
			<span>{l s='Store' mod='mpstorelocator'}</span>
		</a>
	</div>
</div>