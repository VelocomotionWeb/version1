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
{include file="$tpl_dir./errors.tpl"}

{if isset($category)}
	{if $category->id AND $category->active}	
		<!--Filter-->
		{if isset($category)}
			{if $category->id AND $category->active}					
				{if $products}
				<div class="filters-panel">
					<div class="view-mode pull-left">
						<div class="view-modes hidden-sm hidden-xs">
							<a class="view-grid" href="#">
								<span class="fa fa-th"></span>
							</a> 
							<a class="view-list" href="#">
								<span class="fa fa-list-ul"></span>
							</a>
						</div>
						<div class="product-sort">
							{include file="./product-sort.tpl"}
						</div>
					</div>
					<div class="product-sort pull-right">	
						{include file="./pagination.tpl"}
					</div>
				</div>
				{/if}
			{elseif $category->id}
				<div class="alert alert-warning"><button data-dismiss="alert" type="button" class="close">X</button>{l s='This category is currently unavailable.'}</div>
			{/if}
		{/if}
		{if $products}
		{include file="./product-list.tpl" products=$products}
		<div class="filters-panel-bottom">
			<div class="nbr-product">	
				{include file="./nbr-product-page.tpl"}												
			</div>	
			{include file="./pagination.tpl"}
		</div>
		{/if}
	{elseif $category->id}
		<div class="alert alert-warning"><button data-dismiss="alert" type="button" class="close">X</button>{l s='This category is currently unavailable.'}</div>
	{/if}
{/if}
