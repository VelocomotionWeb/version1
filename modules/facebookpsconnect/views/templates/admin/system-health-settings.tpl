{*
* 2003-2015 Business Tech
*
* @author Business Tech SARL <http://www.businesstech.fr/en/contact-us>
* @copyright  2003-2015 Business Tech SARL
*}

{*<h3 class="subtitle">{l s='System Health: Transform your PrestaShop into a social CRM and selling machine' mod='facebookpsconnect'}</h3>*}
{**}
{*<div class="hint" style="display: block !important; width: 95% !important;">*}
{**}
	{*<p>{l s='Facebook PS Connect can do much more than just provide your customers with an easy sign-in method. It also interacts with our other Facebook modules:' mod='facebookpsconnect'} <a target="_blank" class="ao_special_link" href="{l s='http://addons.prestashop.com/en/social-commerce-facebook-prestashop-modules/5025-facebook-ps-essentials-facebook-like-twitter-etc.html' mod='facebookpsconnect'}">Facebook PS Essentials</a> {l s='and' mod='facebookpsconnect'} <a target="_blank" class="ao_special_link" href="{l s='http://addons.prestashop.com/en/social-commerce-facebook-prestashop-modules/1048-facebook-ps-shop-tab.html' mod='facebookpsconnect'}">Facebook PS Shop Tab</a>.</p>*}

	{*<p>{l s='When you install these other modules, every click on a "Like" or "Want" button from these modules will record the action in the database.' mod='facebookpsconnect'}</p>*}

	{*<p>{l s='Soon, we will release a new module called "Facebook PS Analytics and CRM" which will allow you to cross your PrestaShop data for each customer (products ordered etc...) with their social actions and automatically send targeted offers and promotions based on this data. This tool will be incredibly powerful and will help you truly leverage the power of Facebook to increase your sales and customer loyalty.' mod='facebookpsconnect'}</p>*}

	{*<p>{l s='Below, you will see if these modules are installed on your shop and have the minimum required version. If you have not purchased them, a link to PrestaShop Addons is included (for those of you who are used to buying on our own shop, you can also find them there of course). If you have purchased them but do not have the minimum version, you will need to download the updated versions from your account, Order History section, on PrestaShop Addons or our own shop, provided you have purchased the updates extension period and are still covered. Otherwise, you will need to purchase them again.' mod='facebookpsconnect'}</p>*}
{**}
{*</div>*}

{*{foreach from=$aModules name=module key=sName item=aModule}*}
	{*<div class="moduleListing" style="background-image:url({$aModule.img});">*}
		{*<div class="contentTxt">*}
			{*<h4>{l s='Module' mod='facebookpsconnect'} : {$aModule.name}</h4>*}
			{*{if !empty($aModule.minVersion)}*}
				 {*<span class="pastille_big vert"></span>*}
				{*{l s='The module is installed and activated with the minimum version required (' mod='facebookpsconnect'}{$aModule.min}{l s='). You can fully benefit from the extra Facebook PS Connect functionality.' mod='facebookpsconnect'}*}
			{*{elseif !empty($aModule.installed)}*}
				{*<span class="pastille_big orange"></span>*}
				{*{l s='The module is installed and activated, but you do not have the minimum version required (' mod='facebookpsconnect'}{$aModule.min}{l s='). Please update the module to benefit from the extra Facebook PS Connect functionality.' mod='facebookpsconnect'}*}
			{*{else}*}
				{*<span class="pastille_big rouge"></span>*}
				{*{l s='The module is not installed. Please purchase the module to benefit from the extra Facebook PS Connect functionality.' mod='facebookpsconnect'}*}
			{*{/if}*}
			{*<br /><br />*}
		{*</div>*}
		{*<p align="center"><a target="_blank" class="ao_bt_fpsc ao_bt_fpsc_buy" href="{$aModule.addons}"><span class="picto"></span><span class="title">{l s='See / buy this module' mod='facebookpsconnect'}</span></a></p>*}
	{*</div>*}
{*{/foreach}*}
{*<div class="ao_fpsc_clr_20"></div>*}
{**}
{*<p>*}
	{*<br /><span><span class="pastille_small vert"></span> {l s='Module installed and version OK' mod='facebookpsconnect'}</span>*}
	{*<br /><span><span class="pastille_small orange"></span> {l s='Module installed but insufficient version: please update' mod='facebookpsconnect'}</span>*}
	{*<br /><span><span class="pastille_small rouge"></span> {l s='Module not installed: please purchase' mod='facebookpsconnect'}</span>*}
{*</p>*}

{*<div class="ao_fpsc_clr_20"></div>*}

{*code de base bootstrap*}

{*<div class="form-group">*}
	{*<label class="control-label col-lg-2"><span class="label-tooltip" data-toggle="tooltip" title data-original-title=""><b>{l s='' mod='facebookpsconnect'}</b></span> :</label>*}
	{*<div class="col-xs-2">*}

	{*</div>*}
	{*<span class="label-tooltip" data-toggle="tooltip" title data-original-title="">&nbsp;<span class="icon-question-sign"></span></span>*}
{*</div>*}