{*
* 2003-2015 Business Tech
*
* @author Business Tech SARL <http://www.businesstech.fr/en/contact-us>
* @copyright  2003-2015 Business Tech SARL
*}

{* CASE : admin global display, hook category / product page *}
{if isset($iCompare) && $iCompare == -1}
	<script type="text/javascript" src="{$smarty.const._FPC_URL_JS|escape:'htmlall':'UTF-8'}jquery-1.4.4.min.js"></script>
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

{if !empty($bAddJsCss)}
	<link rel="stylesheet" type="text/css" href="{$smarty.const._FPC_URL_CSS|escape:'htmlall':'UTF-8'}hook.css">
	<link rel="stylesheet" type="text/css" href="{$smarty.const._FPC_URL_CSS|escape:'htmlall':'UTF-8'}jquery.fancybox-1.3.4.css">
	<script type="text/javascript" src="{$smarty.const._FPC_URL_JS|escape:'htmlall':'UTF-8'}jquery.fancybox-modified-1.3.4.pack.js"></script>
	<script type="text/javascript" src="{$smarty.const._FPC_URL_JS|escape:'htmlall':'UTF-8'}jquery.mousewheel-3.0.4.pack.js"></script>
{/if}

<script type="text/javascript">
		// instantiate object
		var {$sModuleName|escape:'htmlall':'UTF-8'} = {$sModuleName|escape:'htmlall':'UTF-8'} || new FpcModule('{$sModuleName|escape:'htmlall':'UTF-8'}');

		// get errors translation
		{if !empty($oJsTranslatedMsg)}
			{$sModuleName|escape:'htmlall':'UTF-8'}.msgs = {$oJsTranslatedMsg|escape:'UTF-8'};
		{/if}

		{if isset($iCompare) && $iCompare == -1}{$sModuleName|escape:'htmlall':'UTF-8'}.oldVersion = true;{/if}

		// set URL of admin img
		{$sModuleName|escape:'htmlall':'UTF-8'}.sImgUrl = '{$smarty.const._FPC_URL_IMG|escape:'htmlall'}';

		// set URL of admin img
		{$sModuleName|escape:'htmlall':'UTF-8'}.sAdminImgUrl = '{$smarty.const._PS_ADMIN_IMG_|escape:'htmlall'}';

		// set URL of module's web service
		{if !empty($sModuleURI)}
			{$sModuleName|escape:'htmlall':'UTF-8'}.sWebService = '{$sModuleURI|escape:'htmlall':'UTF-8'}';
		{/if}
</script>









