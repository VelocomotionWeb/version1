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

{capture name=path}{l s='My account'}{/capture}

<h1 class="page-heading">{l s='My account'}</h1>
{if isset($account_created)}
	<p class="success">
		{l s='Your account has been created.'}
	</p>
{/if}
<p class="title_block">{l s='Welcome to your account. Here you can manage al of your personal information and orders.'}</p>
<ul class="myaccount_lnk_list">
	{if $has_customer_an_address}
	<li><a href="{$link->getPageLink('address', true)}" title="{l s='Add my first address'}"><i class="fa fa-map-signs"></i>{l s='Add my first address'}</a></li>
	{/if}
	<li><a href="{$link->getPageLink('history', true)}" title="{l s='Orders'}"><i class="fa fa-history"></i> {l s='Historique  '}</a></li>
	{if $returnAllowed}
		<li><a href="{$link->getPageLink('order-follow', true)}" title="{l s='Merchandise returns'}"><span class="icon_clock_alt"></span> {l s='My merchandise returns'}</a></li>
	{/if}
	<li><a href="{$link->getPageLink('order-slip', true)}" title="{l s='Credit slips'}"><i class="fa fa-book"></i>{l s='My credit slips'}</a></li>
	<li><a href="{$link->getPageLink('addresses', true)}" title="{l s='Addresses'}"><i class="fa fa-map-marker"></i> {l s='My addresses'}</a></li>
	<li><a href="{$link->getPageLink('identity', true)}" title="{l s='Information'}"><i class="fa fa-user"></i>{l s='My personal information'}</a></li>
	{if $voucherAllowed}
		<li><a href="{$link->getPageLink('discount', true)}" title="{l s='Vouchers'}"><i class="fa fa-gift"></i>{l s='My vouchers'}</a></li>
	{/if}
	{$HOOK_CUSTOMER_ACCOUNT}
    
	<li><a href="{$base_dir}" title="{l s='Home'}"><i class="fa fa-home"></i>{l s='Home'}</a></li>
</ul>
