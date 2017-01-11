Changes - 

1) Theme/current-theme/js/Product.js

find - function findCombination()

comment below code - 

$('#attributes select, #attributes input[type=hidden], ' + radio_inputs).each(function(){
	choice.push(parseInt($(this).val()));
});

and paste below code instead - 

/*Code for booking module*/
$('#attributes select, #attributes input[type=hidden], ' + radio_inputs).each(function(){
	//code for booking module
	if ($(this).attr('class') != 'customdate')
		choice.push(parseInt($(this).val()));
});
$('#attributes .attribute_date').each(function(){
	var date = $('.attribute_date').val();
	var date1 = parseInt($('#'+date).val());
	choice.push(date1);
});	
/*END*/


2) theme/current-theme/Product.tpl

Search - 

<select name="{$groupName}" id="group_{$id_attribute_group|intval}" class="form-control attribute_select no-print">

after end of </select> just paste the below code. (after line no 351)

{elseif ($group.group_type == 'date')}
	{hook h="DisplayAddProductAttributeGrouphook" key_attribute=$id_attribute_group  group=$group}


3) In ProductController.php

Search - 

foreach ($combinations as $id_product_attribute => $comb) {
	
}

after end of foreach just paste the below code - (after line 546)

Hook::exec('actionAssignAttributesGroups');