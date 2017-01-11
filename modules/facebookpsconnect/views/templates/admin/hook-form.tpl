{*
* 2003-2015 Business Tech
*
* @author Business Tech SARL <http://www.businesstech.fr/en/contact-us>
* @copyright  2003-2015 Business Tech SARL
*}
<div class="bootstrap">
	<div id="fpc">
		<div id="{$sModuleName|escape:'htmlall':'UTF-8'}ConfigureHook">
			<h3>{l s='Manage hook' mod='facebookpsconnect'} : {$aHook.title|escape:'htmlall':'UTF-8'}</h3>
			<p>{l s='Simply drag and drop the desired connectors from the left-side list to the right-side drop area. You can then re-order them as desired by dragging them, or delete them by clicking the trash icon.' mod='facebookpsconnect'}</p>
			<div id="{$sModuleName|escape:'htmlall':'UTF-8'}DraggableConnector">
				<p><strong class="connectorTitle">{l s='Available connectors' mod='facebookpsconnect'}</strong></p>
				{if $bOneSet}
					<ul class="fbpscconnectorlist">
						{foreach from=$aConnectors name=connector key=cId item=cValue}
							{if !empty($cValue.data.activeConnector)}
								{assign var="bSetConnector" value=false}
								{if !empty($aHook.data)}
									{foreach from=$aHook.data name=hook key=cSetId item=cTitle}
										{if $cId == $cSetId}
											{assign var="bSetConnector" value=true}
										{/if}
									{/foreach}
								{/if}
								{if !$bSetConnector}
									<li id="{$cId|escape:'UTF-8'}" class="fbpscdragli">
										<img src="{$smarty.const._FPC_URL_IMG|escape:'htmlall':'UTF-8'}admin/connector_logo_{$cId|escape:'UTF-8'}.gif" width="16" height="16" alt="{$cValue.title|escape:'htmlall':'UTF-8'}" align="absmiddle" /> {$cValue.title|escape:'htmlall':'UTF-8'}
									</li>
								{/if}
							{/if}
						{/foreach}
					</ul>
				{else}
					{l s='Please, see to configure one widget at least .' mod='facebookpsconnect'}
				{/if}
			</div>

			<div id="{$sModuleName|escape:'htmlall':'UTF-8'}DroppableConnector">
				<p><strong class="connectorTitle">{l s='Active connectors' mod='facebookpsconnect'}</strong></p>
				<ul id="{$sModuleName|escape:'htmlall':'UTF-8'}Sortable">
					{if !empty($aHook.data)}
						{foreach from=$aHook.data name=hook key=cId item=cTitle}
							<li id="{$cId|escape:'UTF-8'}" class="ui-state-default fbpscsortli">
								<img src="{$smarty.const._FPC_URL_IMG|escape:'htmlall':'UTF-8'}admin/connector_logo_{$cId|escape:'UTF-8'}.gif" width="16" height="16" alt="{$cTitle|escape:'htmlall':'UTF-8'}" align="absmiddle" /> {$cTitle|escape:'htmlall':'UTF-8'}
								<img class="{$sModuleName|escape:'htmlall':'UTF-8'}Garbage" src="{$smarty.const._PS_ADMIN_IMG_|escape:'htmlall':'UTF-8'}delete.gif" alt="{l s='Delete' mod='facebookpsconnect'}" title="{l s='Delete' mod='facebookpsconnect'}" onclick="{$sModuleName|escape:'htmlall':'UTF-8'}.deleteConnector($('#{$cId}'));$('#{$cId}').draggable();"/>
							</li>
						{/foreach}
					{/if}
				</ul>
			</div>
			{if $bOneSet}
				<p class="clear">&nbsp;</p>
				<center>
					<input type="button" class="button btn btn-success" name="{$sModuleName|escape:'htmlall':'UTF-8'}HookButton" value="{l s='Update' mod='facebookpsconnect'}" onclick="{$sModuleName|escape:'htmlall':'UTF-8'}.updateHook('{$sURI|escape:'UTF-8'}&sAction={$aQueryParams.hook.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.hook.type|escape:'htmlall':'UTF-8'}&sHookId={$sHookId}', '{$sModuleName|escape:'htmlall':'UTF-8'}Sortable', '{$sModuleName|escape:'htmlall':'UTF-8'}HookList', '{$sModuleName|escape:'htmlall':'UTF-8'}HookList', true);return false;" />
				</center>
					{/if}
			<div id="{$sModuleName|escape:'htmlall':'UTF-8'}HookError">
				<div class="form-error"></div>
			</div>
		</div>

		{literal}
		<script type="text/javascript">
			// set draggable
			{/literal}{$sModuleName|escape:'htmlall':'UTF-8'}{literal}.draggableConnector();

			// set sortable
			{/literal}{$sModuleName|escape:'htmlall':'UTF-8'}{literal}.sortableConnector();

			$(function() {
				$( ".fbpscsortli" ).draggable();
			});

		</script>
		{/literal}
	</div>
</div>


