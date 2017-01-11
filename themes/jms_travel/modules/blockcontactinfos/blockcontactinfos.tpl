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

<!-- MODULE Block contact infos -->
<div id="block_contact_infos">
	<h4 class="title_block">
		<span class="title">{l s='Contact us' mod="blockcontactinfos"}</span>
	</h4>
	<ul>
		{if $blockcontactinfos_company != ''}<li><i class="fa fa-home"></i>{$blockcontactinfos_company|escape:'html':'UTF-8'}</li>{/if}
		{if $blockcontactinfos_address != ''}<li><i class="fa fa-map-marker"></i><span>{l s='Address :' mod='blockcontactinfos'}</span> {$blockcontactinfos_address|escape:'html':'UTF-8'|nl2br}</li>{/if}
		{if $blockcontactinfos_phone != ''}<li class="number_phone"><i class="fa fa-phone"></i><span>{l s='Phone :' mod='blockcontactinfos'}</span> {$blockcontactinfos_phone|escape:'html':'UTF-8'}</li>{/if}
		{if $blockcontactinfos_email != ''}<li class="email_contact"><i class="fa fa-envelope"></i><span>{l s='Email :' mod='blockcontactinfos'}</span> {mailto address=$blockcontactinfos_email|escape:'html':'UTF-8' encode="hex"}</li>{/if}
	</ul>
</div>
<!-- /MODULE Block contact infos -->
