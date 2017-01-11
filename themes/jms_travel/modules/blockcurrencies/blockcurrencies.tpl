{*
* 2007-2014 PrestaShop
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
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<!-- Block currencies module -->
{if count($currencies) > 1}
<div class="btn-group compact-hidden currency-info">	
	<a href="#" id="currency-box-group"  class="currency-box box-group">		
		{foreach from=$currencies key=k item=f_currency}
			{if $cookie->id_currency == $f_currency.id_currency}
				<span class="text-top">{l s='Currency' mod='blockcurrencies'} :</span>
				<span class="selected_curencies text_select">{$f_currency.name|truncate:"3":""}</span>	
				<span class="fa fa-angle-down icon-down"></span>				
			{/if}
		{/foreach}	
	</a>
	<ul class="dropdown-box" id="currency-dropdown-box">
		{foreach from=$currencies key=k item=f_currency}
			<li {if $cookie->id_currency == $f_currency.id_currency}class="selected"{/if}>
				<a href="javascript:setCurrency({$f_currency.id_currency});" rel="nofollow" title="{$f_currency.name}">
					<span class="icon_curencies">{$f_currency.sign}</span>
					<span class="text_name">{$f_currency.name}</span>
				</a>
			</li>
		{/foreach}
	</ul>
</div>
{/if}
<!-- /Block currencies module -->