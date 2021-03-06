{*
* 2007-2016 PrestaShop
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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2016 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{if $smarty.const._PS_VERSION_ >= 1.6}
<div class="row">
	<div class="col-lg-12">
		<div class="panel">
			<div class="panel-heading"><img src="{$base_url|escape:'htmlall':'UTF-8'}modules/{$module_name|escape:'htmlall':'UTF-8'}/logo.gif" alt="" /> {l s='PayPal Refund' mod='paypal'}</div>
			<table class="table" width="100%" cellspacing="0" cellpadding="0">
			  <tr>
			    <th>{l s='Capture date' mod='paypal'}</th>
			    <th>{l s='Capture Amount' mod='paypal'}</th> 
			    <th>{l s='Result Capture' mod='paypal'}</th>
			  </tr>
			{foreach from=$list_captures item=list}
			  <tr>
			    <td>{Tools::displayDate($list.date_add, $smarty.const.null,true)|escape:'htmlall':'UTF-8'}</td>
			    <td>{$list.capture_amount|escape:'htmlall':'UTF-8'}</td> 
			    <td>{$list.result|escape:'htmlall':'UTF-8'}</td>
			  </tr>
			{/foreach}
			</table>
			<form method="post" action="{$smarty.server.REQUEST_URI|escape:'htmlall':'UTF-8'}">
				<input type="hidden" name="id_order" value="{$params.id_order|intval}" />
				<p><b>{l s='Information:' mod='paypal'}</b> {l s='Payment accepted' mod='paypal'}</p>
				<p><b>{l s='Information:' mod='paypal'}</b> {l s='When you refund a product, a partial refund is made unless you select "Generate a voucher".' mod='paypal'}</p>
				<p class="center">
					<button type="submit" class="btn btn-default" name="submitPayPalRefund" onclick="if (!confirm('{l s='Are you sure?' mod='paypal'}'))return false;">
						<i class="icon-undo"></i>
						{l s='Refund total transaction' mod='paypal'}
					</button>
				</p>
			</form>
		</div>
	</div>
</div>
{else}
<br />
<fieldset {if isset($ps_version) && ($ps_version < '1.5')}style="width: 400px"{/if}>
	<legend><img src="{$base_url|escape:'htmlall':'UTF-8'}modules/{$module_name|escape:'htmlall':'UTF-8'}/logo.gif" alt="" />{l s='PayPal Refund' mod='paypal'}</legend>
	<p><b>{l s='Information:' mod='paypal'}</b> {l s='Payment accepted' mod='paypal'}</p>
	<p><b>{l s='Information:' mod='paypal'}</b> {l s='When you refund a product, a partial refund is made unless you select "Generate a voucher".' mod='paypal'}</p>
	<table class="table" width="100%" cellspacing="0" cellpadding="0">
		  <tr>
		    <th>{l s='Capture date' mod='paypal'}</th>
		    <th>{l s='Capture Amount' mod='paypal'}</th> 
		    <th>{l s='Result Capture' mod='paypal'}</th>
		  </tr>
		{foreach from=$list_captures item=list}
		  <tr>
		    <td>{$list.date|escape:'htmlall':'UTF-8'}</td>
		    <td>{$list.capture_amount|escape:'htmlall':'UTF-8'}</td>
		    <td>{$list.result|escape:'htmlall':'UTF-8'}</td>
		  </tr>
		{/foreach}
		</table>
	<form method="post" action="{$smarty.server.REQUEST_URI|escape:'htmlall':'UTF-8'}">
		<input type="hidden" name="id_order" value="{$params.id_order|intval}" />
		<p class="center">
			<input type="submit" class="button" name="submitPayPalRefund" value="{l s='Refund total transaction' mod='paypal'}" onclick="if (!confirm('{l s='Are you sure?' mod='paypal'}'))return false;" />
		</p>
	</form>
</fieldset>

{/if}
