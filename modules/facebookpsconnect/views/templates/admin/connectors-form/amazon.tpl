{*
* 2003-2015 Business Tech
*
* @author Business Tech SARL <http://www.businesstech.fr/en/contact-us>
* @copyright  2003-2015 Business Tech SARL
*}
{literal}
<script type="text/javascript">
	$(function() {
		$(".label-tooltip").tooltip();
	});
</script>
{/literal}
<div class="bootstrap" id="form_connector">
	<br/>
	<div class="form-group">
		<label class="control-label col-lg-3"><span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='You will want to check this box in most cases, unless you don\'t want this button displayed' mod='facebookpsconnect'}"><b> {l s='Activate the connector button' mod='facebookpsconnect'} : </b></span></label>
		<div class="col-xs-6">
			<input type="checkbox" name="activeConnector" id="activeConnector" {if !empty($aConnector.data)}{if $aConnector.data.activeConnector == true}checked="checked"{/if}{else}checked="checked"{/if} /> <label class="fbpsclabel" for="param_send">{l s='Activate button' mod='facebookpsconnect'}</label>
			<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='You will want to check this box in most cases, unless you don\'t want this button displayed' mod='facebookpsconnect'}">&nbsp;<span class="icon-question-sign"></span></span>
		</div>
	</div>
	<br/>
	<div class="separator"></div>

	<div class="form-group">
		<label class="control-label col-lg-3">
			{l s='Client ID' mod='facebookpsconnect'} :
		</label>
		<div class="col-xs-6">
			<input type="text" name="id" id="id" size="60" value="{if isset($aConnector.data.id)}{$aConnector.data.id|escape:'UTF-8'}{/if}"  />
		</div>
		<a href="http://login.amazon.com/manageApps" target="_blank"><span class="icon-info-circle">&nbsp;{l s='Get my client ID' mod='facebookpsconnect' }</span></a>
	</div>

	<div class="separator"></div>

	<div class="form-group">
		<label class="control-label col-lg-3">
			{l s='Client Secret' mod='facebookpsconnect'} :
		</label>
		<div class="col-xs-6">
			<input type="text" name="secret" id="secret" size="60" value="{if isset($aConnector.data.secret)}{$aConnector.data.secret|escape:'htmlall':'UTF-8'}{/if}"  />
		</div>
		<a href="http://login.amazon.com/manageApps" target="_blank"><span class="icon-info-circle">&nbsp;{l s='Get my client secret' mod='facebookpsconnect'}</span></a>
	</div>

	<div class="separator"></div>

	<div class="form-group">
		<label class="control-label col-lg-3"><span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='The callback URL is used to get the valid access token of your app' mod='facebookpsconnect'}"><b>{l s='Callback URL' mod='facebookpsconnect'}</b></span> :</label>
		<div class="col-xs-6">
			<input type="text" name="callback" id="callback" size="60" value="{if isset($aConnector.data.callback)}{$aConnector.data.callback|escape:'UTF-8'}{else}{$sCbkUri|replace:'http://':'https://'|escape:'UTF-8'}{/if}" />
		</div>
		<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='The callback URL is used to get the valid access token of your app' mod='facebookpsconnect'}">&nbsp;<span class="icon-question-sign"></span></span>
	</div>

	<div class="ao_fpsc_clr_20"></div>

	<div class="alert alert-warning">
		{l s='IMPORTANT NOTE: please verify if your callback URL is reachable with HTTPS because Amazon requires to declare an HTTPS URL as callback' mod='facebookpsconnect'}
	</div>

	<div class="separator"></div>

	<div class="form-group">
		<label class="control-label col-lg-3"><span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='The style to display button' mod='facebookpsconnect'}"><b>{l s='Display style' mod='facebookpsconnect'}</b></span> :</label>
		<div class="col-xs-6">
			<select name="display">
				<option value="inline" {if isset($aWidget.data.display)}{if $aWidget.data.display == 'inline'}selected="selected"{/if}{else}selected="selected"{/if}>{l s='inline' mod='facebookpsconnect'}</option>
				<option value="block" {if isset($aWidget.data.display) && $aWidget.data.display == 'block'}selected="selected"{/if}>{l s='block' mod='facebookpsconnect'}</option>
			</select>
		</div>
		<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='The style to display button' mod='facebookpsconnect'}">&nbsp;<span class="icon-question-sign"></span></span>
	</div>

	<div class="separator"></div>

	<div class="alert alert-info">
		{l s='To get available information around amazon app, follow this link' mod='facebookpsconnect'} : <a href="http://login.amazon.com/manageApps" target="_blank">http://login.amazon.com/manageApps</a>
	</div>
</div>