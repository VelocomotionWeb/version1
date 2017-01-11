
{* CASE : admin global display, hook category / product page *}
{if isset($iCompare) && $iCompare == -1}
	<script type="text/javascript" src="{$smarty.const._FPC_URL_JS|escape:"html"}jquery-1.4.4.min.js"></script>
	{if !empty($bHookDisplay)}
		<script type="text/javascript">
			var jQuery144 = $.noConflict(true);
		</script>
	{else}
		<script type="text/javascript">
			var jQuery144 = $;
		</script>
	{/if}
{else}
	<script type="text/javascript">
			var jQuery144 = $;
		</script>
{/if}

{* ADMIN DISPLAY *}

{if !empty($iBackOffice)}
	<link rel="stylesheet" type="text/css" href="{$smarty.const._FPC_BT_API_MAIN_URL|escape:"html"}css/styles.css?ts={$sTs}">
	<link rel="stylesheet" type="text/css" href="{$smarty.const._FPC_URL_CSS|escape:"html"}jquery.ui.core.css">
	<link rel="stylesheet" type="text/css" href="{$smarty.const._FPC_URL_CSS|escape:"html"}admin.css">


	<script type="text/javascript" src="{$smarty.const._FPC_URL_JS|escape:"html"}module.js"></script>

	{if $bVerion15_16 == true}
		<script type="text/javascript" src="{$smarty.const._FPC_URL_JS|escape:"html"}jquery-ui-1.10.4.min.js"></script>
	{else}
		{if !empty($bUseJqueryUI)}
			<script type="text/javascript" src="{$smarty.const._FPC_URL_JS|escape:"html"}jquery-ui-1.8.18.custom.min.js"></script>
		{/if}
		<link rel="stylesheet" type="text/css" href="{$smarty.const._FPC_URL_CSS|escape:"html"}bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="{$smarty.const._FPC_URL_CSS|escape:"html"}jquery.ui.core.css">
		<link rel="stylesheet" type="text/css" href="{$smarty.const._FPC_URL_CSS|escape:"html"}admin-15-14.css">
		<link rel="stylesheet" type="text/css" href="{$smarty.const._FPC_URL_CSS|escape:"html"}bootstrap.css">
		<link rel="stylesheet" type="text/css" href="{$smarty.const._FPC_URL_CSS|escape:"html"}admin-theme.css">
		{*<script type="text/javascript" src="{$smarty.const._FPC_URL_JS}jquery19-for-147lower.js"></script>*}
	{/if}
	<script type="text/javascript" src="{$smarty.const._FPC_URL_JS|escape:"html"}bootstrap.min.js"></script>
{/if}

{if !empty($bAddJsCss)}
	<link rel="stylesheet" type="text/css" href="{$smarty.const._FPC_URL_CSS|escape:"html"}hook.css">
	<link rel="stylesheet" type="text/css" href="{$smarty.const._FPC_URL_CSS|escape:"html"}jquery.fancybox-1.3.4.css">
	<script type="text/javascript" src="{$smarty.const._FPC_URL_JS|escape:"html"}jquery.fancybox-modified-1.3.4.pack.js"></script>
	<script type="text/javascript" src="{$smarty.const._FPC_URL_JS|escape:"html"}jquery.mousewheel-3.0.4.pack.js"></script>
{/if}

<script type="text/javascript">
		// instantiate object
		var {$sModuleName|escape:"html"} = {$sModuleName|escape:"html"} || new FpcModule('{$sModuleName|escape:"html"}');

		// get errors translation
		{if !empty($oJsTranslatedMsg)}
			{$sModuleName|escape:"html"}.msgs = {$oJsTranslatedMsg|escape:"UTF-8"};
		{/if}

		{if isset($iCompare) && $iCompare == -1}{$sModuleName}.oldVersion = true;{/if}

		// set URL of admin img
		{$sModuleName|escape:"html"}.sImgUrl = '{$smarty.const._FPC_URL_IMG|escape:"html"}';

		// set URL of admin img
		{$sModuleName|escape:"html"}.sAdminImgUrl = '{$smarty.const._PS_ADMIN_IMG_|escape:"html"}';

		// set URL of module's web service
		{if !empty($sModuleURI)}
			{$sModuleName|escape:"html"}.sWebService = '{$sModuleURI|escape:"html"}';
		{/if}

</script>









