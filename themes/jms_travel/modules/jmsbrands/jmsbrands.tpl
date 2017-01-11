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
<div class="slider-product-title">
	<h3 class="title">
		<span>{l s='Best airlines' mod='jmsbrands'}</span>
		<span class="icon-title"></span>
	</h3>
</div>
<div class="brand-carousel">
{foreach from=$brands item=brand name=jmsbrands}
	<div class="brand-item">	
		{if $brand.image}
		<a href="{$brand.url|escape:'html':'UTF-8'}" title="{$brand.title|escape:'html':'UTF-8'}">					
			<img class="jms-ss-item-img" src="{$root_url|escape:'html':'UTF-8'}modules/jmsbrands/views/img/{$brand.image|escape:'html':'UTF-8'}" />
		</a>
		{/if}
	</div>
{/foreach}
</div>