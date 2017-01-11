{*
* Copyright (c) 2014 - Leny GRISEL
*
*  @author    Leny GRISEL <email@lenygrisel.com>
*  @copyright 2014 Leny GRISEL
*  @license   Leny GRISEL - All rights reserved.
*  International Registered Trademark & Property of Leny GRISEL
*}
{capture name=path}{l s='Advanced Search' mod="yetanotheradvancedsearch"}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}

<h1>{l s='Advanced Search' mod="yetanotheradvancedsearch"}</h1>

{if $products}
	<div class="content_sortPagiBar">
		{include file="$tpl_dir./pagination.tpl"}

		<div class="sortPagiBar clearfix">
			{include file="$theme_dir/product-sort.tpl"}
			{include file="$theme_dir/product-compare.tpl"}
			{include file="$theme_dir/nbr-product-page.tpl"}
		</div>
	</div>

	{include file="$theme_dir/product-list.tpl" products=$products}

	<div class="content_sortPagiBar">
		<div class="sortPagiBar clearfix">
			{include file="$theme_dir/product-sort.tpl"} {include file="$theme_dir/product-compare.tpl"} {include file="$theme_dir/nbr-product-page.tpl"}
		</div>
		{include file="$theme_dir/pagination.tpl"}
	</div>
{else}
	<p class="warning">{l s='No result.' mod="yetanotheradvancedsearch"}</p>
{/if}
