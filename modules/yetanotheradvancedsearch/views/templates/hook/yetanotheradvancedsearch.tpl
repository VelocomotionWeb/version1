{*
* Copyright (c) 2014 - Leny GRISEL
*
*  @author    Leny GRISEL <email@lenygrisel.com>
*  @copyright 2014 Leny GRISEL
*  @license   Leny GRISEL - All rights reserved.
*  International Registered Trademark & Property of Leny GRISEL
*}

<div class="block yaas_block" data-loader-img="{$waiter_img|escape}" data-search-link="{$search_link|escape}" data-scroll-top="{$scroll_top|escape}" data-default-filter="{$default_filter|escape}" data-display-count="{$display_count|escape}">

  <style type="text/css">
	.yaas_block ul li ul li .selected a {
		color: {$active_color|escape}
	}
	.yaas_block_content li a, .jslider-value span, .jslider-value  {
		color: {$color|escape}
	}
	.yaasCount {
		visibility: {$display_count|escape}
	}
  </style>

  <h4>{$yaas->translate('Advanced Search')|escape}</h4>
  <div class="yaas_block_content">
    <ul>
       {$uuid = uniqid()}
       {$i = 0}
       {foreach $criteria as $criterion}
              
		<li class="item yaas_group_container {{if end($criteria) === $criterion}}last{{/if}}">
			
		    <!-- Title -->
		    <a href="javascript:void(0);" class="{if $criterion['expanded']}selected{/if} yaas_group_title" id="yaas-group-title-{$i|escape}-{$uuid|escape}" data-id="{$i|escape}">
			{if 'title_override'|array_key_exists:$criterion}
				{$criterion['title_override']|escape}
			{else}
				{$criterion['name']|escape}
				{if $criterion['id_criteria_type'] == CriteriaTypeEnum::ATTRIBUTE}
					<i>({$yaas->translate('option')|escape})</i>
				{/if}
			 {/if}
		    </a>

			<!-- Content -->
			<ul id="yaas-group-{$i|escape}-{$uuid|escape}" {if !$criterion['expanded']}style="display:none"{/if}>
				{$contentByCriterion[$criterion['id_criteria']]|escape:'quotes'}
			</ul>

		</li>
		{$i=$i+1}
       {/foreach}
    </ul>     
    {if $display_reinit == "true"}
		<br /><center><input type="button" class="yaas_reinit button button_disabled" value="{$yaas->translate('Reinit')|escape}"></input></center>
	{/if}
  </div>
</div>
