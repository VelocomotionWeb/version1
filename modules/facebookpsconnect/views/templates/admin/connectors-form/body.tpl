{*
* 2003-2015 Business Tech
*
* @author Business Tech SARL <http://www.businesstech.fr/en/contact-us>
* @copyright  2003-2015 Business Tech SARL
*}
<h3 class="popup_title">{$aConnector.title|upper|escape:'UTF-8'} {l s='CONFIGURATION' mod='facebookpsconnect'}</h3>
<div class="bootstrap">
	{if $iTestCurlSsl != 2}
		{if $iConnectorId == 'facebook' && $iTestCurlSsl == 0 && $iApiRequestMethod == ''}
			<div class="alert alert-danger" id="{$sModuleName|escape:'htmlall':'UTF-8'}ConnectorError">
				{l s='Facebook needs to have a connection method selected. Please select a connection method in the "Basics" tab.' mod='facebookpsconnect'}
			</div>
		{elseif $iConnectorId == 'facebook' && $iTestCurlSsl == 0 && ( $iApiRequestMethod == ''  || $iApiRequestMethod == 'curl')}
			<div class="alert alert-danger" id="{$sModuleName|escape:'htmlall':'UTF-8'}ConnectorError">
				{l s='You have selected the "PHP CURL LIBRARY" connection method, but have not yet completed the cURL test. Please run the cURL test in the "Prerequisites Check" tab.' mod='facebookpsconnect'}
			</div>
		{elseif $iConnectorId != 'facebook' && $iTestCurlSsl == 0}
			<div class="alert alert-danger" id="{$sModuleName|escape:'htmlall':'UTF-8'}ConnectorError">
				{l s='You need check your cURL over SSL configuration. Please run the cURL test in the "Prerequisites Check" tab.' mod='facebookpsconnect'}
			</div>
		{elseif $iTestCurlSsl == 1}
			<div class="alert alert-danger" id="{$sModuleName|escape:'htmlall':'UTF-8'}ConnectorError">
				{l s=' The cURL over SSL test failed, you will need to contact your webhost, as the module needs cURL over SSL enabled.' mod='facebookpsconnect'}
			</div>
		{/if}
	{/if}
	<form action="{$sURI}" method="post" id="{$sModuleName|escape:'htmlall':'UTF-8'}ConnectorForm" name="{$sModuleName|escape:'htmlall':'UTF-8'}ConnectorForm" onsubmit="{$sModuleName|escape:'htmlall':'UTF-8'}.form('{$sModuleName|escape:'htmlall':'UTF-8'}ConnectorForm', '{$sURI|escape:'UTF-8'}', '', '{$sModuleName|escape:'htmlall':'UTF-8'}ConnectorList', '{$sModuleName|escape:'htmlall':'UTF-8'}ConnectorList', false, true, null, 'Connector');return false;">
		<input type="hidden" name="sAction" value="{$aQueryParams.connector.action|escape:'htmlall':'UTF-8'}" />
		<input type="hidden" name="sType" value="{$aQueryParams.connector.type|escape:'htmlall':'UTF-8'}" />
		<input type="hidden" name="iConnectorId" value="{$iConnectorId|escape:'UTF-8'}" />
		<div class="plugin_form">
			{include file="`$sTplToInclude`"}
		</div>
		<br/>
		<center>
			<span><input class="button btn btn-success" type="button" id="{$sModuleName|escape:'html':'UTF-8'}ConfigureConnector" name="{$sModuleName|escape:'html':'UTF-8'}ConfigureConnector" value="{l s='Update' mod='facebookpsconnect'}" onclick="{$sModuleName|escape:'htmlall':'UTF-8'}.form('{$sModuleName|escape:'htmlall':'UTF-8'}ConnectorForm', '{$sURI|escape:'UTF-8'}', '', '{$sModuleName|escape:'htmlall':'UTF-8'}ConnectorList', '{$sModuleName|escape:'htmlall':'UTF-8'}ConnectorList', false, true, null, 'Connector');return false;" /></span>
		</center>
		<br/>
	</form>
</div>













