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
<div class="form-horizontal" id="form_connector">
	<div class="form-group">
		<label class="control-label col-lg-5"><span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='You will want to check this box in most cases, unless you don\'t want this button displayed' mod='facebookpsconnect'}"><b>{l s='Activate the connector button' mod='facebookpsconnect'}</b></span> :</label>
		<div class="col-xs-3">
			<input type="checkbox" name="activeConnector" id="activeConnector" {if !empty($aConnector.data)}{if $aConnector.data.activeConnector == true}checked="checked"{/if}{else}checked="checked"{/if} /> <label class="fbpsclabel" for="param_send">{l s='Activate button' mod='facebookpsconnect'}</label>
			<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='You will want to check this box in most cases, unless you don\'t want this button displayed' mod='facebookpsconnect'}">&nbsp;<span class="icon-question-sign"></span></span>
		</div>
	</div>

	<div class="separator"></div>

	<div class="form-group">
		<label class="control-label col-lg-5">
			{l s='Facebook application ID' mod='facebookpsconnect'} :
		</label>
		<div class="col-xs-3">
			<input type="text" name="id" id="id" size="60" value="{if isset($aConnector.data.id)}{$aConnector.data.id|intval}{/if}"  />
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-lg-5"></label>
		<div class="col-xs-6">
			<a href="https://developers.facebook.com/apps" target="_blank"><span class="icon-info-circle">&nbsp;{l s='Get my app ID or create new App' mod='facebookpsconnect'}</span></a>
		</div>
	</div>

	<div class="form-group">
		<label class="control-label col-lg-5">
			{l s='Secret Application ID' mod='facebookpsconnect'}:
		</label>
		<div class="col-xs-4">
			<input type="text" name="secret" id="secret" size="60" value="{if isset($aConnector.data.secret)}{$aConnector.data.secret|escape:'htmlall':'UTF-8'}{/if}"  />
		</div>
	</div>

	<div class="form-group">
		<label class="control-label col-lg-5"></label>
		<div class="col-xs-6">
			<a href="https://developers.facebook.com/apps" target="_blank"><span class="icon-info-circle">&nbsp;{l s='Get my Secret ID' mod='facebookpsconnect'}</span></a>
		</div>
	</div>

	<div class="separator"></div>

	{*<div class="form-group">*}
		{*<label class="control-label col-lg-5"><span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='If you enable this option, then the application will ask your customers for the permission to access their list of friends. Though this may scare some people, it provides you with a powerful mechanism to obtain information about the friends of your customers and later cross their data with your customers, for example, to suggest birthday presents for friends automatically (will be available in our Facebook PS Analytics module to be released in August / September).' mod='facebookpsconnect'}"><b>{l s='Authorize friends permissions' mod='facebookpsconnect'}</b></span> :</label>*}
		{*<div class="col-xs-3">*}
			{*<input type="checkbox" name="permissions" id="permissions" {if !empty($aConnector.data)}{if $aConnector.data.permissions == true}checked="checked"{/if}{else}checked="checked"{/if} />*}
			{*<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='If you enable this option, then the application will ask your customers for the permission to access their list of friends. Though this may scare some people, it provides you with a powerful mechanism to obtain information about the friends of your customers and later cross their data with your customers, for example, to suggest birthday presents for friends automatically (will be available in our Facebook PS Analytics module to be released in August / September).' mod='facebookpsconnect'}">&nbsp;<span class="icon-question-sign"></span></span>*}
		{*</div>*}
	{*</div>*}

	<div class="form-group">
		<label class="control-label col-lg-5"><span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='The application permissions asked when the customer is logging in. In most cases, you will want to NOT touch this and leave the default values. But, for developers, you can include if you want a comma-separated list of any of the additional Permissions available on Facebook. In that case, follow this link' mod='facebookpsconnect'} : https://developers.facebook.com/docs/concepts/login/permissions-login-dialog/" href="https://developers.facebook.com/docs/concepts/login/permissions-login-dialog"}><b>{l s='Scope of App permissions' mod='facebookpsconnect'}</b></span> :</label>
		<div class="col-lg-5">
			<input type="text" name="scope" id="scope" value="{if isset($aConnector.data.scope)}{$aConnector.data.scope|escape:'htmlall':'UTF-8'}{else}user_birthday,user_likes,email{/if}" />
		</div>
		<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='The application permissions asked when the customer is logging in. In most cases, you will want to NOT touch this and leave the default values. But, for developers, you can include if you want a comma-separated list of any of the additional Permissions available on Facebook. In that case, follow this link' mod='facebookpsconnect'} : https://developers.facebook.com/docs/concepts/login/permissions-login-dialog/" href="https://developers.facebook.com/docs/concepts/login/permissions-login-dialog"}>&nbsp;<span class="icon-question-sign"></span></span>
	</div>

	<div class="separator"></div>

	<div class="form-group">
		<label class="control-label col-lg-5"><span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='The style to display button' mod='facebookpsconnect'}"><b>{l s='Display style' mod='facebookpsconnect'}</b></span> :</label>
		<div class="col-xs-3">
			<select name="display">
				<option value="inline" {if isset($aWidget.data.display)}{if $aWidget.data.display == 'inline'}selected="selected"{/if}{else}selected="selected"{/if}>{l s='inline' mod='facebookpsconnect'}</option>
				<option value="block" {if isset($aWidget.data.display) && $aWidget.data.display == 'block'}selected="selected"{/if}>{l s='block' mod='facebookpsconnect'}</option>
			</select>
		</div>
		<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='The style to display button' mod='facebookpsconnect'}">&nbsp;<span class="icon-question-sign"></span></span>
	</div>

	<div class="separator"></div>

	<div class="alert alert-info">
		{l s='To get available information around facebook app, follow this link' mod='facebookpsconnect'} : <a href="https://developers.facebook.com/apps/" target="_blank">https://developers.facebook.com/apps/</a>
	</div>
</div>

{literal}
<script type="text/javascript">
//	$("#permissions").bind($.browser.msie ? 'click' : 'change', function (event)
//	{
//		if ($(this).attr('checked') == 'checked' || $(this).attr('checked') == true) {
//			// add permissions to the FB scope
//			$("#scope").attr('value', "user_birthday,user_likes,email,friends_likes,friends_birthday,read_friendlists");
//		}
//		else {
//			// remove permissions to the FB scope
//			$("#scope").attr('value', "user_birthday,user_likes,email");
//		}
//	});
</script>
{/literal}