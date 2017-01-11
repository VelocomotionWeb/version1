{*
* 2003-2015 Business Tech
*
* @author Business Tech SARL <http://www.businesstech.fr/en/contact-us>
* @copyright  2003-2015 Business Tech SARL
*}
<div class="alert error form-error" style="display: block;">
	{foreach from=$aErrors name=condition key=nKey item=aError}
	<h3>{$aError.msg|escape:'UTF-8'}</h3>
	{if $bDebug == true}
	<ol>
		{if !empty($aError.code)}<li>{l s='Error code' mod='facebookpsconnect'} : {$aError.code|intval}</li>{/if}
		{if !empty($aError.file)}<li>{l s='Error file' mod='facebookpsconnect'} : {$aError.file|escape:'htmlall':'UTF-8'}</li>{/if}
		{if !empty($aError.line)}<li>{l s='Error line' mod='facebookpsconnect'} : {$aError.line|intval}</li>{/if}
		{if !empty($aError.context)}<li>{l s='Error context' mod='facebookpsconnect'} : {$aError.context|escape:'UTF-8'}</li>{/if}
	</ol>
	{/if}
	{/foreach}
</div>
{if !empty($sLink)}
<div style="clear: both;"></div>
<div id="socialMessage">
	<button name="{$sModuleName|escape:'htmlall':'UTF-8'}Button" value="{l s='Reload' mod='facebookpsconnect'}"  onclick="document.location.href= '{$sLink|escape:'UTF-8'}';">{l s='Reload' mod='facebookpsconnect'}</button>
</div>
{/if}