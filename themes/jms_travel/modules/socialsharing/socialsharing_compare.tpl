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
{if $PS_SC_TWITTER || $PS_SC_FACEBOOK || $PS_SC_GOOGLE || $PS_SC_PINTEREST}
	<div id="social-share-compare">
		<p>{l s="Share this comparison with your friends:" mod='socialsharing'}</p>
		<p class="socialsharing_product">
			{if $PS_SC_TWITTER}
				<button type="button" class="btn-social btn-twitter social-sharing" data-type="twitter">
					<i class="fa fa-twitter"></i>
				</button>
			{/if}
			{if $PS_SC_FACEBOOK}
				<button type="button" class="btn-social btn-facebook social-sharing" data-type="facebook">
					<i class="fa fa-facebook"></i>
				</button>
			{/if}
			{if $PS_SC_GOOGLE}
				<button type="button" class="btn-social btn-google-plus social-sharing" data-type="google-plus">
					<i class="fa fa-google-plus"></i>
				</button>
			{/if}
			{if $PS_SC_PINTEREST}				
				<button type="button" class="btn-social btn-pinterest social-sharing" data-type="pinterest">
					<i class="fa fa-pinterest"></i>
				</button>
			{/if}
		</p>
	</div>
{/if}