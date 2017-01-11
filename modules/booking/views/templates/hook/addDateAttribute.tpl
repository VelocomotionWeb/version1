	<input type="text" style="width:120px;"  name="{$group.name}" id="group_{$id_attribute_group|intval}" class="attribute_date form-control"  onchange="findCombination();getProductAttribute();{if $colors|@count > 0}$('#wrapResetImages').show('slow');{/if};" value="{$end_date}" data-values="{$selected_dates}">
	{foreach from=$date key=k item=v}
		<input type="hidden" id="{$v.date}" value="{$v.id}" class="customdate">
	{/foreach}
	{strip}
		{addJsDef selected_dates = $selected_dates mod='booking'}
	{/strip}
	<style>
	#ui-datepicker-div
	{
		z-index: 67676767!important;
	}
	</style>