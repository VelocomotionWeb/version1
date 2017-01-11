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
<footer {if $class_footer}class="{$class_footer}"{/if}>
{foreach from=$rows_footer item=row}
	<div {if $row.class}class="{$row.class}"{/if}>
		<div {if $row.fullwidth == 0}class="container"{/if}>
			<div class="footer-row row">
			{foreach from=$row.positions item=position}
				<div class="footer-position {$position.class_suffix|escape:''} col-lg-{$position.col_lg|escape:''} col-sm-{$position.col_sm|escape:''} col-md-{$position.col_md|escape:''} col-xs-{$position.col_xs|escape:''}">
					{foreach from=$position.blocks item=block}
						<div class="footer-block">
							{if $block.show_title}<h4 class="title_block"><span>{$block.title|escape:'html'}</span></h4>{/if}
							<div class="block-content">
							{$block.return_value|escape:''}
							</div>
						</div>
					{/foreach}
				</div>
			{/foreach}
			</div>
		</div>
	</div>
{/foreach}
</footer>
{if $phover == 'image_swap'}
<script type="text/javascript">	
    $(document).ready(function(){	
		var JmsAjax = new $.JmsAjaxFunc();
        JmsAjax.processAjax();		
    });
</script>
{/if}