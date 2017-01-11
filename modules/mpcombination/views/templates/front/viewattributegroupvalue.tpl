{capture name=path}
	<a {if $logged}href="{$link->getModuleLink('marketplace', 'dashboard')|escape:'htmlall':'UTF-8'}"{/if}>
		{l s='Marketplace' mod='mpcombination'}
	</a>
	<span class="navigation-pipe">{$navigationPipe|escape:'htmlall':'UTF-8'}</span>
	<span class="navigation_page">{l s='Product Attribute Value' mod='mpcombination'}</span>
{/capture}
{include file="$tpl_dir./errors.tpl"}

{if isset($smarty.get.error_attr)}
	<div class="alert alert-danger">{l s='This Attribute group is already in use you cannot edit or delete it.' mod='mpcombination'}</div>
{/if}
{if isset($smarty.get.attr_value_update_success)}
	<div class="alert alert-success">{l s='Attribute Value updated successfully' mod='mpcombination'}</div>
{/if}
{if isset($smarty.get.attr_value_delete_success)}
	<div class="alert alert-success">{l s='Attribute Value deleted successfully' mod='mpcombination'}</div>
{/if}

<div class="main_block">
	{hook h="DisplayMpmenuhook"}
	<div class="dashboard_content">
		<div class="page-title">
			<span>{l s='Product Attribute Value' mod='mpcombination'}</span>
		</div>
		<div class="wk_right_col">
			<div class="wk-table-head">
				<a class="btn btn-default button button-small pull-right" href="{$link->getModuleLink('mpcombination', 'createattributevalue',['shop' => $shop, 'id_group' => $id_group])|escape:'htmlall':'UTF-8'}">
					<span>{l s='Add new value' mod='mpcombination'}</span>
				</a>
				<a class="btn btn-default button button-small pull-right" style="margin-right:5px" href="{$link->getModuleLink('mpcombination', 'productattribute',['shop'=>$shop])|escape:'htmlall':'UTF-8'}">
					<span >{l s='Back to list' mod='mpcombination'}</span>
				</a>
			</div>
			<table class="table table-hover">
				<thead>
					<tr>
						<th>{l s='ID' mod='mpcombination'}</th>
						<th>{l s='Value' mod='mpcombination'}</th>
						{if isset($is_color)}
							<th>{l s='Color' mod='mpcombination'}</th>
						{/if}
						<th>{l s='Action' mod='mpcombination'}</th>
					<tr>
				</thead>
				{if count($value_set) > 0}
					{foreach $value_set as $value_set_each}
					<tr>
						<td>{$value_set_each['id']|escape:'htmlall':'UTF-8'}</td>
						<td>{$value_set_each['name']|escape:'htmlall':'UTF-8'}</td>
						{if isset($is_color)}
							<td><div class="color_box" style="background-color: {$value_set_each['color']|escape:'htmlall':'UTF-8'}"></div></td>
						{/if}
						<td>
							<a class="edit_but" title="{l s='Edit' mod='mpcombination'}" edit="{$value_set_each['editable']|escape:'htmlall':'UTF-8'}" href="{$link->getModuleLink('mpcombination', 'createattributevalue',['shop'=>$shop|escape:'htmlall':'UTF-8','id_group'=>$id_group|escape:'htmlall':'UTF-8','id_attribute'=>$value_set_each['editable']|escape:'htmlall':'UTF-8'])|escape:'htmlall':'UTF-8'}">
								<i class="icon-edit"></i>
							</a>
							&nbsp;
							<a class="delete_button" title="{l s='Delete' mod='mpcombination'}" edit="{$value_set_each['editable']|escape:'htmlall':'UTF-8'}" href="{$link->getModuleLink('mpcombination', 'createattributevalue',['shop'=>$shop|escape:'htmlall':'UTF-8','id_group'=>$id_group|escape:'htmlall':'UTF-8','id_attribute'=>$value_set_each['editable']|escape:'htmlall':'UTF-8', 'delete_attr_value'=>1])|escape:'htmlall':'UTF-8'}">
								<i class="icon-trash"></i>
							</a>
						</td>
					</tr>
					{/foreach}
				{else}
					<tr>
						{if isset($is_color)}<td colspan="4">{else}<td colspan="3">{/if}
							{l s='No value found' mod='mpcombination'}
						</td>						
					</tr>
				{/if}
			</table>
		</div>
	</div>
</div>

{strip}
	{addJsDefL name=error_msg2}{l s='This Attribute is already in use you cannot edit or delete it' mod='mpcombination'}{/addJsDefL}
	{addJsDefL name=confirm_delete}{l s='Are you sure?' mod='mpcombination'}{/addJsDefL}
{/strip}