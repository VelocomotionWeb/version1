$(document).ready(function() {
	$(document).on('change', '#attribute_price_impact', function(){
		var attribute_price_impact = $("#attribute_price_impact option:selected").val();
		if (attribute_price_impact == 0)
			$('#impact_price_span').css('display','none');
		else
			$('#impact_price_span').css('display','block');
	});

	$(document).on('change', '#attribute_weight_impact', function(){
		var attribute_weight_impact = $("#attribute_weight_impact option:selected").val();
		if (attribute_weight_impact == 0)
			$('#impact_weight_span').css('display','none');
		else
			$('#impact_weight_span').css('display','block');
	});

	$(document).on('change', '#attribute_unit_impact', function(){
		var attribute_unit_impact = $("#attribute_unit_impact option:selected").val();
		if (attribute_unit_impact == 0)
			$('#impact_unit_price_span').css('display','none');
		else
			$('#impact_unit_price_span').css('display','block');
	});

	$("#add_attr_button").on("click", function(){
		var error_msg = "";
		var attribute_group_name_id = $("#attribute_select option:selected").val();
		var attribute_val_id = $("#attribute_value_select option:selected").val();
		var attribute_group_name = $("#attribute_select option:selected").text();
		var attribute_val = $("#attribute_value_select option:selected").text();
		var new_attr = attribute_group_name+" : "+attribute_val;
		
		if(attribute_group_name_id == "")
		{
			error_msg = req_attr;
			alert(req_attr);
		}
		else if(attribute_val_id == "")
		{
			error_msg = req_attr_val;
			alert(error_msg);
		}
		else if (selected_attribute_group.indexOf(attribute_group_name_id) > -1)
		{
			error_msg = attr_already_selected;
			alert(error_msg);
		}
		else
			selected_attribute_group[selected_attribute_group.length] = attribute_group_name_id;

		if(error_msg=="")
			$("#product_att_list").append("<option  value="+attribute_val_id+" groupid="+attribute_group_name_id+">"+new_attr+"</option>");	
	});

	$("#del_attr_button").click(function(){
		var newarray = [];
		var index = 0;
		var del_attr_val = $("#product_att_list option:selected").val();
		var groupid = $('option:selected',"#product_att_list").attr('groupid');
		
		$.each(selected_attribute_group,function(i, item){
			if (item != groupid)
			{
				newarray[index] = item;
				index++;
			}
		});
		selected_attribute_group = newarray;

		$("#product_att_list option").each(function(){			
			if($(this).val() == del_attr_val)
				$(this).remove();
		});
	});

	$(document).on("change", "#attribute_select", function(){
		var attr_id = $(this).val();
		var pathh = $("#pathh").val();
		$.ajax({
			url:pathh,
			data: {
				attr_id:attr_id
			},
			dataType: 'json',
			success: function(data){
				$("#attribute_value_select").empty();
				$("#attribute_value_select").html("<option value=''>"+option_select+"</option>");
				if(data.length != 0){
				    $.each(data, function(key,item){
						$("#attribute_value_select").append("<option  value='"+item.id+"'>"+item.name+"</option>");
				     });
				}
			}
		});
	});

	//create attribute Validation form
	$(document).on('click', '#btn_combination_validate',function(){
		$("#product_att_list option").prop('selected', true);
		var attrib_id = $("#product_att_list option").val();
		var mp_quntity = $("#mp_quantity").val();
		var attribute_price_impact = $("#attribute_price_impact").val();
		var attribute_weight_impact = $("#attribute_weight_impact").val();
		var attribute_unit_impact = $("#attribute_unit_impact").val();
		var attribute_minimal_quantity = $("#attribute_minimal_quantity").val();
		if(isNaN(attrib_id))
		{
			alert(error_msg1);
			return false;
		}
		else if(isNaN(mp_quntity))
		{
			alert(error_msg2);
			return false;
		}
		else if(isNaN(attribute_price_impact))
		{
			alert(error_msg3);
			return false;
		}
		else if(isNaN(attribute_weight_impact))
		{
			alert(error_msg4);
			return false;
		}
		else if(isNaN(attribute_unit_impact))
		{
			alert(error_msg5);
			return false;
		}
		else if(isNaN(attribute_minimal_quantity) || (attribute_minimal_quantity == 0))
		{
			alert(error_msg6);
			return false;
		}
	});	

	$('.datepicker1').datepicker({
		dateFormat: 'yy-mm-dd'
	});

});


function calcImpactPriceTI()
{
	var tax = 0;
	var priceTE = parseFloat(document.getElementById('attribute_priceTEReal').value.replace(/,/g, '.'));
	var newPrice = priceTE * ((tax / 100) + 1);
	$('#attribute_priceTI').val((isNaN(newPrice) == true || newPrice < 0) ? '' : ps_round(newPrice, 2).toFixed(2));
	var total = ps_round((parseFloat($('#attribute_priceTI').val())*parseInt($('#attribute_price_impact').val())+parseFloat($('#finalPrice').html())), 2);
	if (isNaN(total) || total < 0)
		$('#attribute_new_total_price').html('0.00');
	else
		$('#attribute_new_total_price').html(total);
}
function calcImpactPriceTE()
{
	var tax = 0;
	var priceTI = parseFloat(document.getElementById('attribute_priceTI').value.replace(/,/g, '.'));
	priceTI = (isNaN(priceTI)) ? 0 : ps_round(priceTI);
	var newPrice = ps_round(priceTI, 2) / ((tax / 100) + 1);
	$('#attribute_price').val((isNaN(newPrice) == true || newPrice < 0) ? '' : ps_round(newPrice, 6).toFixed(6));
	$('#attribute_priceTEReal').val((isNaN(newPrice) == true || newPrice < 0) ? 0 : ps_round(newPrice, 9));
	var total = ps_round((parseFloat($('#attribute_priceTI').val())*parseInt($('#attribute_price_impact').val())+parseFloat($('#finalPrice').html())), 2);
	if (isNaN(total) || total < 0)
		$('#attribute_new_total_price').html('0.00');
	else
		$('#attribute_new_total_price').html(total);
}

