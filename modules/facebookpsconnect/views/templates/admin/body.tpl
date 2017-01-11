{*
* 2003-2015 Business Tech
*
* @author Business Tech SARL <http://www.businesstech.fr/en/contact-us>
* @copyright  2003-2015 Business Tech SARL
*}
{* HEADER *}
{include file="`$sHeaderInclude`" iBackOffice=true}
{* /HEADER *}

<img src="{$smarty.const._FPC_URL_IMG|escape:'htmlall':'UTF-8'}admin/bt-logo-admin.jpg" alt="Facebook Ps Connect" /><br /><br />

{* USE CASE - module update not ok  *}
{if !empty($aUpdateErrors)}
	<div class="error">
		{foreach from=$aUpdateErrors name=condition key=nKey item=aError}
			<h3>{l s='An error occured while SQL was executed for ' mod='facebookpsconnect'} {if isset($aError.table)}{l s='table' mod='facebookpsconnect'} "{$aError.table|escape:'htmlall':'UTF-8'}" {else}{l s='field' mod='facebookpsconnect'} "{$aError.field|escape:'htmlall':'UTF-8'}" {l s='in table' mod='facebookpsconnect'} "{$aError.linked|escape:'htmlall':'UTF-8'}"{/if} </h3>
			<ol>
				<li>{l s='SQL file' mod='facebookpsconnect'} : {$aError.file|escape:'htmlall':'UTF-8'}</li>
			</ol>
		{/foreach}
		<p>{l s='Please reload this page and try again to update the SQL tables and fields or see with your web host why you are getting an SQL error' mod='facebookpsconnect'}.</p>
	</div>
{* USE CASE - display configuration ok *}
{else}
	<script>
		var id_language = Number({$iCurrentLang|intval});
		{literal}
		// load the last jquery UI js for FireFox with sortable description
		var sUserAgent = window.navigator.userAgent;

		if (sUserAgent.search("Firefox/38") != -1) {
			var element = document.createElement("script");
			element.src = "{/literal}{$smarty.const._FPC_URL_JS|escape:'UTF-8'}{literal}jquery-ui-1.11.4.min.js";
			document.body.appendChild(element);
		}
		{/literal}
	</script>
	<div id="{$sModuleName|escape:'htmlall':'UTF-8'}" class="bootstrap form">
		<ul class="nav nav-tabs " id="workTabs">
			<li class="active"><a data-toggle="tab" href="#tab-0"><span class="icon-home"></span>&nbsp;{l s='Welcome' mod='facebookpsconnect'}</a></li>
			<li><a data-toggle="tab" href="#tab-1"><span class="icon-ok-sign"></span>&nbsp;{l s='Prerequisites check' mod='facebookpsconnect'}</a></li>
				<li><a data-toggle="tab" href="#tab-2"><span class="icon-heart"></span>&nbsp;{l s='Basics' mod='facebookpsconnect'}</a></li>
				<li><a data-toggle="tab" href="#tab-3"><span class="icon-wrench"></span>&nbsp;{l s='Connectors' mod='facebookpsconnect'}</a></li>
				<li><a data-toggle="tab" href="#tab-4"><span class="icon-link"></span>&nbsp;{l s='Hooks' mod='facebookpsconnect'}</a></li>
			<li><a data-toggle="tab" href="#tab-5"><span class="icon-file"></span>&nbsp;{l s='Help / FAQ' mod='facebookpsconnect'}</a></li>
		</ul>

		<div class="tab-content">
			<div id="tab-0" class="tab-pane fade in active information">
				<br/>
				<div class="alert alert-success">
					{l s='Welcome and thank you for purchasing our module. Please read the documentation carefully (visit the Documentation & Help tab)' mod='facebookpsconnect'}
				</div>
				<iframe class="btxsellingiframe" src="{$smarty.const._FPC_BT_API_MAIN_URL|escape:'htmlall':'UTF-8'}?ts={$sTs}&sName={$smarty.const._FPC_MODULE_SET_NAME|escape:'htmlall':'UTF-8'}&sLang={$sCurrentIso|escape:'htmlall':'UTF-8'}"></iframe>
			</div>

			<div id="tab-1" class="tab-pane fade">
				<div id="{$sModuleName|escape:'htmlall':'UTF-8'}">
					{include file="`$sPrerequisitesCheck`"}
				</div>
			</div>

			<div id="tab-2" class="tab-pane fade">
				{if $iTestSsl == 0 && $iCurlSslCheck == 2}
					<div id="{$sModuleName|escape:'htmlall':'UTF-8'}BasicSettings">
						{include file="`$sBasicsInclude`"}
					</div>
				{else}
					<div class="ao_fpsc_clr_20"></div>
					<div class="alert alert-danger">
						{l s='You have selected the "PHP CURL LIBRARY" connection method, but have not yet completed the cURL test. Please run the cURL test in the "Prerequisites Check" tab.' mod='facebookpsconnect'}
					</div>
				{/if}
			</div>

			<div id="tab-3" class="tab-pane fade">
				{if $iTestSsl == 0 && $iCurlSslCheck == 2}
					<div id="{$sModuleName|escape:'htmlall':'UTF-8'}ConnectorList">
						{include file="`$sConnectorInclude`"}
					</div>
				{else}
					<div class="ao_fpsc_clr_20"></div>
					<div class="alert alert-danger">
						{l s='You have selected the "PHP CURL LIBRARY" connection method, but have not yet completed the cURL test. Please run the cURL test in the "Prerequisites Check" tab.' mod='facebookpsconnect'}
					</div>
				{/if}
			</div>

			<div id="tab-4" class="tab-pane fade">
				{if $iTestSsl == 0 && $iCurlSslCheck == 2}
					<div id="{$sModuleName|escape:'htmlall':'UTF-8'}HookList">
						{include file="`$sHookInclude`"}
					</div>
				{else}
					<div class="ao_fpsc_clr_20"></div>
					<div class="alert alert-danger">
						{l s='You have selected the "PHP CURL LIBRARY" connection method, but have not yet completed the cURL test. Please run the cURL test in the "Prerequisites Check" tab.' mod='facebookpsconnect'}
					</div>
				{/if}
			</div>

			<div id="tab-5" class="tab-pane fade">
				<h3>{l s='Help / FAQ' mod='facebookpsconnect'}</h3>

				<div class="ao_fpsc_clr_hr"></div>
				<div class="ao_fpsc_clr_20"></div>

				<p><strong style="font-weight: bold;">{l s='MODULE PDF DOCUMENTATION' mod='facebookpsconnect'} :</strong> <a target="_blank" href="{$sDocUri|escape:'htmlall':'UTF-8'}{$sDocName|escape:'htmlall':'UTF-8'}">{$sDocName|escape:'htmlall':'UTF-8'}</a></p>
				<p><strong style="font-weight: bold;">{l s='ONLINE FAQ' mod='facebookpsconnect'} :</strong> <a target="_blank" href="{$smarty.const._FPC_BT_FAQ_MAIN_URL|escape:'htmlall':'UTF-8'}faq.php?id=65&lg={$sCurrentIso|escape:'htmlall':'UTF-8'}">{$smarty.const._FPC_BT_FAQ_MAIN_URL|escape:'htmlall':'UTF-8'}</a></p>
				<p><strong style="font-weight: bold;">{l s='CONTACT' mod='facebookpsconnect'} :</strong> <a target="_blank" href="{$sContactUs|escape:'htmlall':'UTF-8'}">{$sContactUs|escape:'htmlall':'UTF-8'}</a></p>
			</div>
		</div>
	</div>

	{literal}
	<script>
		$(function() {
			$("#{/literal}{$sModuleName}{literal}Tabs").tabs({selected : {/literal}{if isset($iActiveTab)}{$iActiveTab}{else}0{/if}{literal}});
		});

		$(".icon-question-sign").tooltip();
		$(".label-tooltip").tooltip();
	</script>
	{/literal}
{/if}