{*
* 2003-2015 Business Tech
*
* @author Business Tech SARL <http://www.businesstech.fr/en/contact-us>
* @copyright  2003-2015 Business Tech SARL
*}
{literal}
<script type="text/javascript">
	$(document).ready(function()
	{
		$("a#hookEdit").fancybox({
			'hideOnContentClick' : false,
			'autoDimensions' : true
		});
	});
</script>
{/literal}

<h3 >{l s='Manage Hooks' mod='facebookpsconnect'}</h3>

<div class="ao_fpsc_clr_hr"></div>
<div class="ao_fpsc_clr_20"></div>

<div class="form-group">
	<label {if $bVersion15_16 == true}class="control-label col-lg-2"{else}class="control-label"{/if}></label>
	<div {if $bVersion15_16 == true}class="col-lg-7"{else}class="col-lg-12"{/if}>
		<table style="width: 100%;" id="fbpsctabs" class="table  table-bordered table-striped" cellpadding="0" cellspacing="0">
			<thead>
			<tr class="nodrag nodrop">
				<th class="center">{l s='Hook name' mod='facebookpsconnect'}</th>
				<th class="center">{l s='Hook actived' mod='facebookpsconnect'}</th>
				<th class="center">{l s='Connectors added' mod='facebookpsconnect'}</th>
				<th class="center">{l s='Edit' mod='facebookpsconnect'}</th>
			</tr>
			</thead>
			<tbody>
			{foreach from=$aHooks name=hook key=hName item=hValue}
				{if $hValue.use}
					<tr id="tr_{$smarty.foreach.connector.iteration|intval}">
						<td class="center"  style="width: 200px">
							{$hValue.title|escape:'htmlall':'UTF-8'}
						</td>
						<td class="center" style="width: 200px">
							{if empty($hValue.data)}
								<img src="{$smarty.const._PS_ADMIN_IMG_|escape:'htmlall':'UTF-8'}{if $iCompare == -1}delete.gif{else}module_notinstall.png{/if}" class="center" alt="{l s='Hooks not configured' mod='facebookpsconnect'}" title="{l s='Hooks not configured' mod='facebookpsconnect'}" />
							{else}
								<img src="{$smarty.const._PS_ADMIN_IMG_|escape:'htmlall':'UTF-8'}enabled.gif" class="center" alt="{l s='Hooks configured' mod='facebookpsconnect'}" title="{l s='Hooks configured' mod='facebookpsconnect'}" />
							{/if}
						</td>
						<td class="center">
							{if !empty($hValue.data)}
								{foreach from=$hValue.data name=connector key=name item=title}{$title|escape:'htmlall':'UTF-8'}{if !$smarty.foreach.connector.last}, {/if}{/foreach}
							{else}
								{l s='No connector configured' mod='facebookpsconnect'}
								<img src="{$smarty.const._PS_ADMIN_IMG_|escape:'htmlall':'UTF-8'}warning.gif" class="center" alt="{l s='No connector configured' mod='facebookpsconnect'}" title="{l s='No connector configured' mod='facebookpsconnect'}" />
							{/if}
						</td>
						<td class="center" style="width: 100px">
							<a id="hookEdit" href="{$sURI|escape:'UTF-8'}&sAction={$aQueryParams.hookForm.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.hookForm.type|escape:'htmlall':'UTF-8'}&sHookId={$hName|escape:'htmlall':'UTF-8'}" class="icon-edit"></a>
						</td>
					</tr>
				{/if}
			{/foreach}
			</tbody>
		</table>
	</div>
</div>

<div class="ao_fpsc_clr_20"></div>