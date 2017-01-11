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
		{if $PS_SC_FACEBOOK}
			<button data-type="facebook" type="button" class="btn-social btn-facebook social-sharing">
				<span class="fa fa-facebook"></span>
			</button>
		{/if}	
		{if $PS_SC_TWITTER}
			<button type="button" data-type="twitter" class="btn-social btn-twitter social-sharing">
				<span class="fa fa-twitter"></span>
			</button>
		{/if}
		{if $PS_SC_GOOGLE}
			<button type="button" data-type="google-plus" class="btn-social btn-google-plus social-sharing">
				<span class="fa fa-google-plus"></span>
			</button>
		{/if}
		{if $PS_SC_PINTEREST}
			<button type="button" data-type="pinterest" class="btn-social btn-pinterest social-sharing">
				<span class="fa fa-pinterest-p"></span>
			</button>
		{/if}	
{/if}