{*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<header {if $class_header}class="{$class_header}"{/if}>
{foreach from=$rows_header item=row}
	<div {if $row.class}class="{$row.class}"{/if}>
		<div {if $row.fullwidth == 0}class="container"{/if}>
			<div class="header-row row {if $row.fullwidth == 1}fullwidth{/if}">
			{foreach from=$row.positions item=position}
				<div class="header-position {$position.class_suffix|escape:''} col-lg-{$position.col_lg|escape:''} col-sm-{$position.col_sm|escape:''} col-md-{$position.col_md|escape:''} col-xs-{$position.col_xs|escape:''}">
					{foreach from=$position.blocks item=block}
						<div class="header-block">
							{if $block.show_title}<h4 class="title_block"><span>{$block.title|escape:'html'}</span></h4>{/if}							
								{$block.return_value|escape:''}							
						</div>
					{/foreach}
				</div>
			{/foreach}
			</div>
		</div>
	</div>
{/foreach}
</header>