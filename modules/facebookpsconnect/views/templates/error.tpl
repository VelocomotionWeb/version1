<div class="alert error form-error" style="display: block;">
    {foreach from=$aErrors name=condition key=nKey item=aError}
    <h3>{$aError.msg|escape:"html"}</h3>
	{if $bDebug == true}
	<ol>
		{if !empty($aError.code)}<li>{l s='Error code' mod='facebookpsconnect'} : {$aError.code}</li>{/if}
        {if !empty($aError.file)}<li>{l s='Error file' mod='facebookpsconnect'} : {$aError.file}</li>{/if}
        {if !empty($aError.line)}<li>{l s='Error line' mod='facebookpsconnect'} : {$aError.line}</li>{/if}
        {if !empty($aError.context)}<li>{l s='Error context' mod='facebookpsconnect'} : {$aError.context}</li>{/if}
	</ol>
	{/if}
    {/foreach}
</div>
{if !empty($sLink)}
<div style="clear: both;"></div>
<div id="socialMessage">
    <button name="{$sModuleName|escape:"html"}Button" value="{l s='Reload' mod='facebookpsconnect'}"  onclick="document.location.href= '{$sLink|escape:"html"}';">{l s='Reload' mod='facebookpsconnect'}</button>
</div>
{/if}