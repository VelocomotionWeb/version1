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

<!-- Block user information module HEADER -->
<div class="btn-group compact-hidden user-info">	
	{if $logged}			
		<a class="logout" href="{$link->getPageLink('index', true, NULL, "mylogout")|escape:'html'}" rel="nofollow" title="{l s='Log me out' mod='blockuserinfo'}">{l s='Sign out' mod='blockuserinfo'}</a>
        
		<a id="user-box-group" data-toggle="dropdown" class="btn-xs account box-group dropdown-toggle" href="{$link->getPageLink('my-account', true)}" title="{l s='View my customer account' mod='blockuserinfo'}" rel="nofollow">
			<i class="fa fa-user"></i>
			<span class="text-box1">{$cookie->customer_firstname} {$cookie->customer_lastname}</span>
		</a>
	{else}
		<a  id="user-box-group" class="btn-xs dropdown-toggle login box-group" href="{$link->getPageLink('my-account', true)}" title="{l s='Login to your customer account' mod='blockuserinfo'}" rel="nofollow">
			<i class="fa fa-user"></i>
			<span class="text-box">{* {l s='Login' mod='blockuserinfo'} *}Mon Compte</span>
		</a>
	{/if}
	<ul class="dropdown-menu" id="user-dropdown-box" role="menu">
		{if $logged}			
		<li>
			<a class="account" href="{$link->getPageLink('my-account', true)}" title="{l s='View my customer account' mod='blockuserinfo'}" rel="nofollow">
				<span class="text-box">{$cookie->customer_firstname} {$cookie->customer_lastname}</span>
			</a>
		</li>
{*
*}
		{/if}
		<li><a href="{$link->getPageLink('my-account', true)}" title="{l s='View my customer account' mod='blockuserinfo'}" class="account" rel="nofollow">{l s='My Account' mod='blockuserinfo'} </a></li>		
		<li><a href="{$link->getPageLink('order', true)}" title="{l s='View my customer account' mod='blockuserinfo'}" class="account" rel="nofollow">{l s='Checkout' mod='blockuserinfo'} </a></li>
		{if $logged}	
		<li><a href="{$link->getPageLink('index', true, NULL, "mylogout")}" title="{l s='Log me out' mod='blockuserinfo'}" class="logout" rel="nofollow">{l s='Log out' mod='blockuserinfo'}</a></li>
		{/if}
{*
		<li>	
			<a class="wishlist" href="index.php?fc=module&amp;module=blockwishlist&amp;controller=mywishlist" title="{l s='View my Wishlist' mod='blockuserinfo'}" rel="nofollow">
				<span class="text-box">{l s='My Wishlist' mod='blockuserinfo'}</span>
			</a>
		</li>
		<li>
			<a class="compare" href="{$link->getPageLink('compare', true)}" title="{l s='View my customer account' mod='blockuserinfo'}" rel="nofollow">
				<span>{l s='My Compare' mod='blockuserinfo'}</span>
			</a>
		</li>
*}
	</ul>
</div>


