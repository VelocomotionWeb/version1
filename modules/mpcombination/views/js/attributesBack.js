var storeUsedGroups = {};

function populate_attrs()
{
	var attr_group = getE('attribute_group');
	if (!attr_group)
		return;
	var attr_name = getE('attribute');
	var number = attr_group.options.length ? attr_group.options[attr_group.selectedIndex].value : 0;

	if (!number)
	{
		attr_name.options.length = 0;
		attr_name.options[0] = new Option('---', 0);
		return;
	}

	var list = attrs[number];
	attr_name.options.length = 0;
	if (typeof list !== 'undefined')
	{
		for(i = 0; i < list.length; i += 2)
			attr_name.options[i / 2] = new Option(list[i + 1], list[i]);
	}
}

function attr_selectall()
{
	var elem = getE('product_att_list');
	if (elem)
	{
		var i;
		for (i = 0; i < elem.length; i++)
			elem.options[i].selected = true;
	}
}

function del_attr_multiple()
{
	var attr = getE('attribute_group');

	if (!attr)
		return ;
	var length = attr.length;
	var target;

	for (var i = 0; i < length; ++i)
	{
		elem = attr.options[i];
		if (elem.selected)
		{
			target = getE('table_' + elem.parentNode.getAttribute('name'));
			if (target && getE('result_' + elem.getAttribute('name')))
			{
				target.removeChild(getE('result_' + elem.getAttribute('name')));
				if (!target.lastChild || !target.lastChild.id)
					toggle(target.parentNode, false);
			}
		}
	}
}

function create_attribute_row(id, id_group, name, price, weight)
{	
	var html = '';
	html += '<tr id="result_'+id+'">';
	html += '<td><input type="hidden" value="'+id+'" name="options['+id_group+']['+id+']" />'+name+'</td>';
	html += '<td>'+i18n_tax_exc+'<input id="related_to_price_impact_ti_'+id+'" class="price_impact form-control" style="width:50px" type="text" value="'+price+'" name="price_impact_'+id+'" onkeyup="calcPrice($(this), false)"></td>';
	html += '<td>'+i18n_tax_inc+'<input id="related_to_price_impact_'+id+'" class="price_impact_ti form-control" style="width:50px" type="text" value="'+price+'" name="price_impact_ti_'+id+'" onkeyup="calcPrice($(this), true)"></td>';
	html += '<td><input style="width:50px;margin-top:15px;" type="text" class="form-control" value="'+weight+'" name="weight_impact_'+id+'"></td>';
	html += '</tr>';

	return html;
}

function add_attr_multiple()
{
	var attr = getE('attribute_group');
	if (!attr)
		return ;
	var length = attr.length;
	var target;
	var new_elem;

	for (var i = 0; i < length; ++i)
	{
		elem = attr.options[i];
		if (elem.selected)
		{
			name = elem.parentNode.getAttribute('name');
			target = $('#table_' + name);
			if (target && !getE('result_' + elem.getAttribute('name')))
			{
				new_elem = create_attribute_row(elem.getAttribute('name'), elem.parentNode.getAttribute('name'), elem.value, '0.00', '0');
				target.append(new_elem);
				toggle(target.parent()[0], true);
			}
		}
	}
}

/**
 * Delete one or several attributes from the declination multilist
 */
function del_attr()
{
	$('#product_att_list option:selected').each(function()
	{
		delete storeUsedGroups[$(this).attr('groupid')];
		$(this).remove();
	});
}

/**
 * Add an attribute from a group in the declination multilist
 */
function add_attr()
{
	var attr_group = $('#attribute_group option:selected');
	if (attr_group.val() == 0)
		return jAlert(msg_combination_1);

	var attr_name = $('#attribute option:selected');
	if (attr_name.val() == 0)
		return jAlert(msg_combination_2);
	
	if (attr_group.val() in storeUsedGroups)
		return jAlert(msg_combination_3);

	storeUsedGroups[attr_group.val()] = true;
	$('<option></option>')
		.attr('value', attr_name.val())
		.attr('groupid', attr_group.val())
		.text(attr_group.text() + ' : ' + attr_name.text())
		.appendTo('#product_att_list');
}

function openCloseLayer(whichLayer)
{
	if (document.getElementById)
		var style = document.getElementById(whichLayer).style;
	else if (document.all)
		var style = document.all[whichLayer].style;
	else if (document.layers)
		var style = document.layers[whichLayer].style;
	style.display = style.display == 'none' ? 'block' : 'none';
}

$(document).ready(function(){
	$('#product_form').submit(function(){
		attr_selectall();
		// If the new combination form is hidden, remove it so that empty fields are not submitted
		if ($('#add_new_combination').is(':hidden'))
			$('#add_new_combination').remove();
	});
	$('#hideError').live('click',function() {
		$('.error').hide('slow');
	});
});

function getE(element) {
	return document.getElementById(element)
}

var edit_flag = true;	
var selected_attribute_group = [];
var selected_attribute_value = [];	
$(document).ready(function() {
	var group_colour_flag = false;	
	var group_shoesize_flag = false;	
	var group_size_flag = false;
	var selected_attribute_group = [];		
});

// for  update_product_combination.tpl
$(document).ready(function() {
	$("#generate_combination").click(function(e){
		return confirm(generate_combination_confirm_msg);
	});

	$('.delete_attribute').click(function(e) {
		e.preventDefault();
		var mp_product_attr_id = $(this).attr('alt');
		var r=confirm(confirmation_msg);
		if (r==true)
		{
			deleteCombination(mp_product_attr_id);
		}
	});
	
	$('.default_attribute').click(function(e) {
		e.preventDefault();
		var mp_product_attr_id = $(this).attr('alt');
		updateDefaultAttribute(mp_product_attr_id);
	});

	//create attribute close
	
});
	
function deleteCombination(mp_product_attr_id) {
	var data1 = {	ajax: 1,
					mp_product_attr_id:mp_product_attr_id ,
					fun:'del_com'
				}
				 
	$.ajax(attribute_delete_ajax_link, {
		type: 'POST',
		'data': data1,
		dataType: 'json',
		success: function(data, status, xhr)
		{
			$('#delete_attribute'+mp_product_attr_id).parent().parent().remove();
		},
		error: function(xhr, status, error) {
			return 0;
		}
	});
}

function updateDefaultAttribute(mp_product_attr_id) {
	var data1 = {	ajax: 1,
					mp_product_attr_id:mp_product_attr_id ,
					fun:'upd_default'
				}
	var default_product_attribute = $('#default_product_attribute').val();
	$('#default_attribute'+default_product_attribute).css('display','block');
	$('#default_attribute'+mp_product_attr_id).css('display','none');
	$('#default_product_attribute').attr('value',mp_product_attr_id);
	$.ajax(attribute_delete_ajax_link, {
		type: 'POST',
		'data': data1,
		dataType: 'json',
		success: function(data, status, xhr)
		{
			
		},
		error: function(xhr, status, error) {
			return 0;
		}
	});
}



