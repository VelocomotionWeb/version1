{capture name=path}
	<a {if $logged}href="{$link->getModuleLink('marketplace', 'dashboard')|escape:'html':'UTF-8'}"{/if}>
		{l s='Marketplace' mod='mpcombination'}
	</a>
	<span class="navigation-pipe">{$navigationPipe|escape:'htmlall':'UTF-8'}</span>
	{if isset($id_group)}
		<span class="navigation_page">{l s='Edit Attribute' mod='mpcombination'}</span>
	{else}
		<span class="navigation_page">{l s='Create Attribute' mod='mpcombination'}</span>
	{/if}
{/capture}

{include file="$tpl_dir./errors.tpl"}
<div class="main_block">
	{hook h="DisplayMpmenuhook"}
	<div class="dashboard_content">
		<div class="page-title">
			{if isset($id_group)}
				<span>{l s='Edit Attribute' mod='mpcombination'}</span>
			{else}
				<span>{l s='Create Attribute' mod='mpcombination'}</span>
			{/if}
		</div>
		<div class="wk_right_col">
			<div class="wk-table-head">
				<a class="btn btn-default button button-small pull-right" style="margin-right:5px" href="{$link->getModuleLink('mpcombination', 'productattribute',['shop'=>$shop|escape:'htmlall':'UTF-8'])|escape:'htmlall':'UTF-8'}">
					<span >{l s='Back to list' mod='mpcombination'}</span>
				</a>
			</div>
			{if isset($id_group)}
				<form action="{$link->getModuleLink('mpcombination', 'createattribute', ['shop' => $shop|escape:'htmlall':'UTF-8', 'id_group' => $id_group|escape:'htmlall':'UTF-8'])|escape:'htmlall':'UTF-8'}" method="POST" class="defaultForm">
			{else}
				<form action="{$link->getModuleLink('mpcombination', 'createattribute', ['shop' => $shop|escape:'htmlall':'UTF-8'])|escape:'htmlall':'UTF-8'}" method="POST" class=" form-horizontal">
			{/if}
				<div class="full left">
					<div class="full left form-group">
						<label class="control-label col-lg-5 required">{l s='Name' mod='mpcombination'}</label>
						<div class="col-lg-4">
							<input type="text" name="attrib_name" id="attrib_name" 
							{if isset($id_group)}value="{if isset($smarty.post.attrib_name)}{$smarty.post.attrib_name|escape:'htmlall':'UTF-8'}{else}{$name|escape:'htmlall':'UTF-8'}{/if}"{else}value="{if isset($smarty.post.attrib_name)}{$smarty.post.attrib_name|escape:'htmlall':'UTF-8'}{/if}"{/if} 
							class="form-control"/>
						</div>
					</div>
					<div class="full left form-group">
						<label class="control-label col-lg-5 required">{l s='Public name' mod='mpcombination'}</label>
						<div class="col-lg-4">
							<input type="text" name="attrib_public_name" id="attrib_public_name" 
							{if isset($id_group)}value="{if isset($smarty.post.attrib_public_name)}{$smarty.post.attrib_public_name|escape:'htmlall':'UTF-8'}{else}{$public_name|escape:'htmlall':'UTF-8'}{/if}"{else}value="{if isset($smarty.post.attrib_public_name)}{$smarty.post.attrib_public_name|escape:'htmlall':'UTF-8'}{/if}"{/if} 
							class="form-control"/>
						</div>
					</div>
					<div class="full left form-group">
						<label class="control-label col-lg-5 required">{l s='Attribute type' mod='mpcombination'}</label>
						<div class="col-lg-3">
							<select name="attrib_type" class="form-control">
								<option value="select" {if isset($id_group)}{if ($group_type == 'select')}Selected="Selected"{/if}{/if}>{l s='Drop-down list' mod='mpcombination'}</option>
								<option value="radio" {if isset($id_group)}{if ($group_type == 'radio')}Selected="Selected"{/if}{/if}>{l s='Radio buttons' mod='mpcombination'}</option>
								<option value="color" {if isset($id_group)}{if ($group_type == 'color')}Selected="Selected"{/if}{/if}>{l s='Color or texture' mod='mpcombination'}</option>
							</select>
						</div>
					</div>
					<div class="left full create_button">
						{if isset($id_group)}
							<button class="btn btn-default button button-medium" id="attrib_valid" name="attrib_update" type="submit">
								<span>{l s='Update' mod='mpcombination'}</span>
							</button>
						{else}
							<button class="btn btn-default button button-medium" id="attrib_valid" name="attrib_add" type="submit">
								<span>{l s='Create' mod='mpcombination'}</span>
							</button>
						{/if}
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

{addJsDefL name=inv_public_name}{l s='Please provide a valid Public name' mod='mpcombination'}{/addJsDefL}
{addJsDefL name=inv_name}{l s='Please provide a valid name' mod='mpcombination'}{/addJsDefL}