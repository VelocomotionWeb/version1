{*
* 2003-2015 Business Tech
*
* @author Business Tech SARL <http://www.businesstech.fr/en/contact-us>
* @copyright  2003-2015 Business Tech SARL
*}
{if !empty($bDisplay)}
	{literal}
	<script type="text/javascript">
		//<![CDATA[
		var oButtonsDiv = "{/literal}{include file="`$sConnectorButtonsIncl`" bJS=true}{literal}";

		{/literal}
		{* USE CASE - DISPLAY BUTTONS IN TOP OF THE PAGE  *}
		{if !empty($sPosition) && $sPosition == 'top'}
			{literal}
			$('body').prepend(oButtonsDiv);
			{/literal}
		{/if}
		{* USE CASE - DISPLAY BUTTONS IN BOTTOM OF THE PAGE  *}
		{if !empty($sPosition) && $sPosition == 'bottom'}
			{literal}
			$('body').append(oButtonsDiv);
			{/literal}
		{/if}
		{* USE CASE - DISPLAY BUTTONS IN BLOCK USER ABOVE MENU  *}
		{if !empty($sPosition) && $sPosition == 'blockUser'}
			{literal}
			$('#header_user').append(oButtonsDiv);
			{/literal}
		{/if}
		{* USE CASE - DISPLAY BUTTONS IN ACCOUNT PAGE  *}
		{if !empty($sPosition) && $sPosition == 'authentication'}
			{literal}
			if ($('#authentication').length != 0) {
				$('#center_column').append('<div class="ao_fpsc_clr_20"></div><div id="authenticationAlign">'+oButtonsDiv+'</div><div class="ao_fpsc_clr_20"></div>');
				{/literal}
				{if $bDisplayBlockInfoAccount == 1}
					{if !empty($sFriendlyText)}
						$('#center_column').append('<p id="connectorText" class="ao_custom_msg_info">{$sFriendlyText|escape:'html'}<br/><br/><br/></p>');
					{elseif !empty($sDefaultText)}
						$('#center_column').append('<p id="connectorText" class="ao_custom_msg_info">{$sDefaultText|escape:'html'}<br/><br/><br/></p>');
					{/if}
				{/if}
				{literal}
			}
			{/literal}
		{/if}
		{* USE CASE - DISPLAY BUTTONS IN ONE PAGE CHECKOUT  *}
		{if !empty($sPosition) && $sPosition == 'newaccount'}
		{literal}
		if ($('#opc_new_account').length != 0) {
			// set var for displaying connector text
			var sText = '<div class="ao_fpsc_clr_20"></div><div id="authenticationAlign">'+oButtonsDiv+'</div><div class="ao_fpsc_clr_20"></div>';
			{/literal}
			{if !empty($bDisplayBlockInfoCart)}
				{if !empty($sFriendlyText)}
				sText += '<p id="connectorText" class="ao_custom_msg_info">{$sFriendlyText|escape:'html'}</p>';
				{elseif !empty($sDefaultText)}
				sText += '<p id="connectorText" class="ao_custom_msg_info">{$sDefaultText|escape:'html'}</p>';
				{/if}
				sText += '<div class="ao_fpsc_clr_20"></div>';
			{/if}
			{literal}
			$('#opc_new_account').prepend(sText);
		}
		{/literal}
		{/if}
		{literal}
		// ]]>
	</script>
	{/literal}
{/if}