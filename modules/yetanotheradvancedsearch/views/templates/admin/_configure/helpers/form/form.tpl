{*
* Copyright (c) 2014 - Leny GRISEL
*
*  @author    Leny GRISEL <email@lenygrisel.com>
*  @copyright 2014 Leny GRISEL
*  @license   Leny GRISEL - All rights reserved.
*  International Registered Trademark & Property of Leny GRISEL
*}

{extends file="helpers/form/form.tpl"}
{block name="input"}
    {if $input.type == 'criteria'}
        <script type="text/javascript">
            var come_from = '{$name_controller|escape}';
            var token = '{$token|escape}';
            var alternate = 1;
        </script>
        <style type="text/css">
            td.nodecoration a {
                    text-decoration: none;
            }
        </style>

        {assign var=criteriaList value=$input.values}
        {if isset($criteriaList) && count($criteriaList) > 0}

				<table cellspacing="0" cellpadding="0" style="min-width:60em;" class="table" id="criteria_list">
					<thead>
						<tr class="nodrag nodrop">
							<th>{$criteriaList[0]->translate('ID')|escape}</th>
							<th>{$criteriaList[0]->translate('Criterion')|escape}</th>
							<th>{$criteriaList[0]->translate('Layout')|escape}</th>
							<th>{$criteriaList[0]->translate('Sort?')|escape}</th>
							<th>{$criteriaList[0]->translate('Allow multiple')|escape}</th>
							<th>{$criteriaList[0]->translate('Expanded')|escape}</th>
							<th>{$criteriaList[0]->translate('Position')|escape}</th>
							<th>{$criteriaList[0]->translate('Actions')|escape}</th>
						</tr>
					</thead>
					<tbody>
						{foreach $criteriaList[1] as $key => $criterion}
							<tr class="{if $key%2}alt_row{else}not_alt_row{/if} row_hover" id="tr_{$key%2}_{$criterion['id_criteria']|escape}_{$criterion['position']|escape}">
								<td>{$criterion['id_criteria']|escape}</td>
								<td>{$criterion['display']|escape}</td>
								<td>{$criterion['display_layout']|escape}</td>
								<td>{$criterion['display_sort_type']|escape}</td>
								<td>{if $criterion['allow_multiple'] == '1'}{$criteriaList[0]->translate('Yes')|escape}{else}{$criteriaList[0]->translate('No')|escape}{/if}</td>
								<td>{if $criterion['expanded'] == '1'}{$criteriaList[0]->translate('Yes')|escape}{else}{$criteriaList[0]->translate('No')|escape}{/if}</td>
								<td class="center nodecoration" id="td_{$key%2}_{$criterion['id_criteria']|escape}">
									<a
										{if (($key == (sizeof($criteriaList[1]) - 1)) || (sizeof($criteriaList[1]) == 1))}
												style="display: none;"
										{/if}
												href="{$current|escape}&id_criteria={$criterion['id_criteria']|escape}&way=1&new_position={(int)$criterion['position'] + 1}&token={$token|escape}">
										<img src="{$smarty.const._PS_ADMIN_IMG_}down.gif" alt="{$criteriaList[0]->translate('Down')|escape}" title="{$criteriaList[0]->translate('Down')}" />
									</a>
									<a style="{if (($criterion['position'] == 0) || ($key == 0))};display: none;{/if}" href="{$current|escape}&id_criteria={$criterion['id_criteria']|escape}&way=0&new_position={(int)$criterion['position'] - 1}&token={$token|escape}">
										<img src="{$smarty.const._PS_ADMIN_IMG_}up.gif" alt="{$criteriaList[0]->translate('Up')|escape}" title="{$criteriaList[0]->translate('Up')|escape}" />
									</a>
								</td>
								<td>
									<a href="{$current|escape}&token={$token|escape}&editCriteria&id_criteria={(int)$criterion['id_criteria']}" title="{$criteriaList[0]->translate('Edit')|escape}"><img src="{$smarty.const._PS_ADMIN_IMG_}edit.gif" alt="" /></a>
									<a href="{$current|escape}&token={$token|escape}&deleteCriteria&id_criteria={(int)$criterion['id_criteria']}" title="{$criteriaList[0]->translate('Delete')|escape}"><img src="{$smarty.const._PS_ADMIN_IMG_}delete.gif" alt="" /></a>
								</td>
							</tr>
						{/foreach}
					</tbody>
				</table>
        {/if}
    {else}
		{if $input.type == 'cron'}
			 {assign var=cronList value=$input.values}
			 {if isset($cronList) && count($cronList) > 0}
				<span style="color: green; font-weight: bold">{{$cronList[0]->translate('CRON')|escape}}</span>
				<div style="padding:10px; ">{$cronList[1]|escape}</div>
			 {/if}
		{else}
                    {if $input.type == 'corresponding'}
                        <script type="text/javascript">var yaasCorresponding = {$input.values|escape:'quotes' };</script>
                    {else} {$smarty.block.parent} {/if}
		{/if}
    {/if}

{/block}
