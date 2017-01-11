{capture name=path}
	<a {if $logged}href="{$link->getModuleLink('marketplace', 'dashboard')|escape:'html':'UTF-8'}"{/if}>
		{l s='Marketplace' mod='mpcombination'}
	</a>
	<span class="navigation-pipe">{$navigationPipe|escape:'htmlall':'UTF-8'}</span>
	{if isset($id_attribute)}
		<span class="navigation_page">{l s='Edit Attribute Value' mod='mpcombination'}</span>
	{else}
		<span class="navigation_page">{l s='Create Attribute Value' mod='mpcombination'}</span>
	{/if}
{/capture}
{include file="$tpl_dir./errors.tpl"}
<div class="main_block">
	{hook h="DisplayMpmenuhook"}
	<div class="dashboard_content">
		<div class="page-title">
			{if isset($id_attribute)}
				<span>{l s='Edit Value' mod='mpcombination'}</span>
			{else}				
				<span>{l s='Create New Value' mod='mpcombination'}</span>
			{/if}
		</div>
		<div class="wk_right_col">
			<div class="wk-table-head">
				<a class="btn btn-default button button-small pull-right" style="margin-right:5px" href="{$link->getModuleLink('mpcombination', 'productattribute',['shop'=>$shop|escape:'htmlall':'UTF-8'])|escape:'htmlall':'UTF-8'}">
					<span >{l s='Back to list' mod='mpcombination'}</span>
				</a>
			</div>
			{if isset($id_attribute)}
				<form action="{$link->getModuleLink('mpcombination', 'createattributevalue', ['shop' => $shop|escape:'htmlall':'UTF-8', 'id_attribute'=>$id_attribute|escape:'htmlall':'UTF-8', 'id_group'=>$id_group|escape:'htmlall':'UTF-8'])|escape:'htmlall':'UTF-8'}" method="POST" class=" defaultForm">
			{else}
				<form action="{$link->getModuleLink('mpcombination', 'createattributevalue', ['shop' => $shop|escape:'htmlall':'UTF-8'])|escape:'htmlall':'UTF-8'}" method="post" class="form-horizontal">
			{/if}
				<div class="full left">
					<div class="full left form-group">
						<label class="control-label col-lg-4 text-right required">{l s='Attribute Type' mod='mpcombination'}:</label>
						<div class="col-lg-4">
							<select name="attrib_group" id="attrib_group" class="form-control">
								{if isset($id_attribute)}
									<option value="{$attrib_grp['id']|escape:'htmlall':'UTF-8'}">{$attrib_grp['name']|escape:'htmlall':'UTF-8'}</option>
								{else}
									{foreach $attrib_set as $attrib_set_each}
										<option value="{$attrib_set_each['id']|escape:'htmlall':'UTF-8'}" {if isset($id_group)}{if $id_group == $attrib_set_each['id']}selected{/if}{/if}>{$attrib_set_each['name']|escape:'htmlall':'UTF-8'}</option>
									{/foreach}
								{/if}
							</select>
						</div>
					</div>
					<div class="full left form-group">
						<label class="control-label col-lg-4 text-right required">{l s='Value' mod='mpcombination'}:</label>
						<div class="col-lg-4">
							<input type="text" name="attrib_value" {if isset($id_attribute)}value="{$attrib_valname|escape:'htmlall':'UTF-8'}"{/if} id="attrib_value" class="form-control"/>
						</div>
					</div>
					{if isset($attrib_color)}
						<div class="full left form-group">
							<label class="control-label col-lg-4 text-right">{l s='Color' mod='mpcombination'}</label>
							<div class="col-lg-4">
								<input type="color" value="{$attrib_color|escape:'htmlall':'UTF-8'}" name="attrib_value_color" id="attrib_value_color" class="form-control"/>
							</div>
						</div>						
					{else}
						<div id="attrib_value_color_div" class="full left form-group">
							<label class="control-label col-lg-4 text-right" >{l s='Color' mod='mpcombination'}</label>
							<div class="col-lg-4">
								<input type="color" name="attrib_value_color" id="attrib_value_color" class="form-control"/>
							</div>
						</div>
					{/if}
					<div class="left full create_button">
					{if isset($id_attribute)}
						<button class="btn btn-default button button-medium" id="attrib_value_valid" name="attrib_value_update" type="submit">
							<span>{l s='Update' mod='mpcombination'}</span>
						</button>
					{else}
						<button class="btn btn-default button button-medium" id="attrib_value_valid" name="attrib_value_add" type="submit">
							<span>{l s='Create' mod='mpcombination'}</span>
						</button>
					{/if}
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

{strip}
	{addJsDefL name=attr_val_req}{l s='Value should not be blank' mod='mpcombination'}{/addJsDefL}
	{addJsDef grouptypecolor_link = $link->getModuleLink('mpcombination', 'grouptypecolor')}
	{if isset($id_group)}
		{addJsDef id_groupp = $id_group}
	{else}
		{addJsDef id_groupp = 0}
	{/if}
{/strip}