{capture name=path}
	<a {if $logged}href="{$link->getModuleLink('marketplace', 'dashboard')|escape:'html':'UTF-8'}"{/if}>
		{l s='Marketplace' mod='mpcombination'}
	</a>
	<span class="navigation-pipe">{$navigationPipe|escape:'htmlall':'UTF-8'}</span>
	<span class="navigation_page">{l s='Product Attribute' mod='mpcombination'}</span>
{/capture}
{include file="$tpl_dir./errors.tpl"}
{if isset($smarty.get.error_attr)}
	<div class="alert alert-danger">{l s='This Attribute group is already in use you cannot edit or delete it.' mod='mpcombination'}</div>
{/if}
{if isset($smarty.get.attr_add_success)}
	<div class="alert alert-success">{l s='Attribute added successfully' mod='mpcombination'}</div>
{/if}
{if isset($smarty.get.attr_update_success)}
	<div class="alert alert-success">{l s='Attribute updated successfully' mod='mpcombination'}</div>
{/if}
{if isset($smarty.get.attr_delete_success)}
	<div class="alert alert-success">{l s='Attribute deleted successfully' mod='mpcombination'}</div>
{/if}

{if isset($smarty.get.attr_value_add_success)}
	<div class="alert alert-success">{l s='Attribute Value added successfully' mod='mpcombination'}</div>
{/if}


<div class="main_block">
	{hook h="DisplayMpmenuhook"}
	<div class="dashboard_content">
		<div class="page-title">
			<span>{l s='Product Attribute' mod='mpcombination'}</span>
		</div>
		<div class="wk_right_col">
			<div class="wk-table-head">
				<a class="btn btn-default button button-small pull-right" href="{$link->getModuleLink('mpcombination', 'createattributevalue',['shop'=>$shop|escape:'htmlall':'UTF-8'])|escape:'htmlall':'UTF-8'}">
					<span>{l s='Add new value' mod='mpcombination'}</span>
				</a>
				<a class="btn btn-default button button-small pull-right" style="margin-right:5px" href="{$link->getModuleLink('mpcombination', 'createattribute',['shop'=>$shop|escape:'htmlall':'UTF-8'])|escape:'htmlall':'UTF-8'}">
					<span >{l s='Add new attribute' mod='mpcombination'}</span>
				</a>
			</div>
			<div class="table-responsive">
			<table class="table table-hover" >
				<thead>
					<tr>
						<th>{l s='ID' mod='mpcombination'}</th>
						<th>{l s='Name' mod='mpcombination'}</th>
						<th>{l s='Value Count' mod='mpcombination'}</th>
						<th>{l s='Action' mod='mpcombination'}</th>
					<tr>
				</thead>
				{if count($attrib_set) > 0}
					{foreach $attrib_set as $attrib_set_each}
						<tr class="group_entry" data-value-url="{$link->getModuleLink('mpcombination', 'viewattributegroupvalue',['shop' => $shop|escape:'htmlall':'UTF-8', 'id_group' => $attrib_set_each['id']|escape:'htmlall':'UTF-8'])|escape:'htmlall':'UTF-8'}">
							<td>{$attrib_set_each['id']|escape:'htmlall':'UTF-8'}</td>
							<td>{$attrib_set_each['name']|escape:'htmlall':'UTF-8'}</td>
							<td>{$attrib_set_each['count_value']|escape:'htmlall':'UTF-8'}</td>
							<td>
								<a class="edit_button" title="{l s='Edit' mod='mpcombination'}" edit="{$attrib_set_each['editable']|escape:'htmlall':'UTF-8'}" href="{$link->getModuleLink('mpcombination', 'createattribute',['shop'=>$shop|escape:'htmlall':'UTF-8', 'id_group'=>$attrib_set_each['editable']|escape:'htmlall':'UTF-8'])|escape:'htmlall':'UTF-8'}">
									<i class="icon-edit"></i>
								</a>
								&nbsp;
								<a class="delete_button" title="{l s='Delete' mod='mpcombination'}" edit="{$attrib_set_each['editable']|escape:'htmlall':'UTF-8'}" href="{$link->getModuleLink('mpcombination', 'createattribute',['shop'=>$shop|escape:'htmlall':'UTF-8', 'id_group'=>$attrib_set_each['editable']|escape:'htmlall':'UTF-8', 'delete_attr'=>1])|escape:'htmlall':'UTF-8'}">
									<i class="icon-trash"></i>
								</a>
							</td>
						</tr>
					{/foreach}
				{else}
					<tr>
						<td colspan="4">{l s='No attribute found' mod='mpcombination'}</td>
					</tr>
				{/if}
			</table>
			</div>
		</div>
	</div>
</div>

{strip}
	{addJsDefL name=confirm_delete}{l s='Are you sure?' mod='mpcombination'}{/addJsDefL}
	{addJsDefL name=error_msg1}{l s='This Attribute group is already in use you cannot edit or delete it' mod='mpcombination'}{/addJsDefL}
{/strip}