{*
* 2007-2013 PrestaShop
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
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{if isset($orderby) AND isset($orderway)}

{* On 1.5 the var request is setted on the front controller. The next lines assure the retrocompatibility with some modules *}
{if !isset($request)}
	<!-- Sort products -->
	{if isset($smarty.get.id_category) && $smarty.get.id_category}
		{assign var='request' value=$link->getPaginationLink('category', $category, false, true)}
	{elseif isset($smarty.get.id_manufacturer) && $smarty.get.id_manufacturer}
		{assign var='request' value=$link->getPaginationLink('manufacturer', $manufacturer, false, true)}
	{elseif isset($smarty.get.id_supplier) && $smarty.get.id_supplier}
		{assign var='request' value=$link->getPaginationLink('supplier', $supplier, false, true)}
	{else}
		{assign var='request' value=$link->getPaginationLink(false, false, false, true)}
	{/if}
{/if}

<script type="text/javascript">
//<![CDATA[
$(document).ready(function()
{
	$('.dropdown-menu li > a').click(function(e){
		var requestSortProducts = '{$request}';		
		var splitData = $(this).data('value').split(':');
		document.location.href = requestSortProducts + ((requestSortProducts.indexOf('?') < 0) ? '?' : '&') + 'orderby=' + splitData[0] + '&orderway=' + splitData[1];
	});
	/*$('.selectProductSort').change(function()
	{
		var requestSortProducts = '{$request}';
		var splitData = $(this).val().split(':');
		document.location.href = requestSortProducts + ((requestSortProducts.indexOf('?') < 0) ? '?' : '&') + 'orderby=' + splitData[0] + '&orderway=' + splitData[1];
	});*/
});
//]]>
</script>

{*
<form id="productsSortForm" action="{$request|escape:'htmlall':'UTF-8'}">
		<div class="btn-group btn-select sort-select sort-isotope">
			<label>{l s='Sort by'} : </label>
			<a href="#" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
				<span class="value">
				{if $orderby eq $orderbydefault}{l s='Default'}{/if}
				{if $orderby eq 'price' AND $orderway eq 'asc'}{l s='Price: Lowest first'}{/if}
				{if $orderby eq 'price' AND $orderway eq 'desc'}{l s='Price: Highest first'}{/if}
				{if $orderby eq 'name' AND $orderway eq 'asc'}{l s='Product Name: A to Z'}{/if}
				{if $orderby eq 'name' AND $orderway eq 'desc'}{l s='Product Name: Z to A'}{/if}
				{if $orderby eq 'quantity' AND $orderway eq 'desc'}{l s='In stock'}{/if}
				{if $orderby eq 'reference' AND $orderway eq 'asc'}{l s='Reference: Lowest first'}{/if}
				{if $orderby eq 'reference' AND $orderway eq 'desc'}{l s='Reference: Highest first'}{/if}				
				</span>
				<span class="arrow_triangle-down fa fa-caret-down"></span>
			</a>
			<ul class="dropdown-menu">
				<li><a href="#" data-value="{$orderbydefault|escape:'html':'UTF-8'}:{$orderwaydefault|escape:'html':'UTF-8'}">{l s='Sort by'}</a></li>
                <li><a href="#" data-value="price:asc">{l s='Price: Lowest first'}</a></li>
                <li><a href="#" data-value="price:desc">{l s='Price: Highest first'}</a></li>
                <li><a href="#" data-value="name:asc">{l s='Product Name: A to Z'}</a></li>
                <li><a href="#" data-value="name:desc">{l s='Product Name: Z to A'}</a></li>
                {if !$PS_CATALOG_MODE}
                <li><a href="#" data-value="quantity:desc">{l s='In stock'}</a></li>
                {/if}
                <li><a href="#" data-value="reference:asc">{l s='Reference: Lowest first'}</a></li>
                <li><a href="#" data-value="reference:desc">{l s='Reference: Highest first'}</a></li>
            </ul>	
		</div>
</form>
*}
<!-- /Sort products -->
{/if}
