$(document).ready(function(){	
	$(document).on('click', '#attrib_valid', function(){
		var error_msg = "";
		var name = $('#attrib_name').val();
		var public_name = $('#attrib_public_name').val();
		if ((!isNaN(public_name)) || public_name.length < 1)
			error_msg = inv_public_name;
		if ((!isNaN(name)) || name.length < 1)
			error_msg = inv_name;
		if (error_msg != "")
		{
			alert(error_msg);
			return false;
		}
	});

	// productattribute.tpl
	$(document).on('click', '.group_entry', function(){
		var url = $(this).data('value-url');
		window.location.href = url;
	});

	$(document).on('click','.edit_button', function(){
		var editable = $(this).attr('edit');
		if (editable == 0)
		{
			alert(error_msg1);
			return false;
		}
	});

	$(document).on('click','.delete_button', function(){
		var editable = $(this).attr('edit');
		if (editable == 0)
		{
			alert(error_msg1);
			return false;
		}
		else if (!confirm(confirm_delete))
			return false;
	});

	// viewattributegroupvalue.tpl
	$(document).on('click','.edit_but', function(){
		var editable = $(this).attr('edit');
		if (editable == 0)
		{
			alert(error_msg2);
			return false;
		}
	});

	$(document).on('click','.del_attr_val', function(){
		var editable = $(this).attr('edit');
		if (editable == 0)
		{
			alert(error_msg2);
			return false;
		}
		else if (!confirm(confirm_delete))
			return false;
	});


	$(document).on('change', '#attrib_group', function(){
		var group_id = 0;
		group_id = $(this).val();
		colorCheck(group_id);
		
	});

	$(document).on('click', '#attrib_value_valid', function(e){
		var error_msg = "";
		var attrib_value = $('#attrib_value').val();
		if (attrib_value.length < 1)
			error_msg = attr_val_req;
		if (error_msg != "")
		{
			alert(error_msg);
			return false;
		}
	});
});

if (typeof id_groupp != 'undefined' )
		colorCheck(id_groupp);

function colorCheck(id_group)
{
	if (id_group != 0)
	{		
		$.ajax({
		 	url : grouptypecolor_link,
		 	type : 'POST',
		 	data : {group_id : id_group},
		 	error : function(XHR, textStatus, errorThrown){
					console.log(textStatus);
					console.log(errorThrown);
			},
			success : function(data){
				if(data == 1)
					$('#attrib_value_color_div').show();
				else
					$('#attrib_value_color_div').hide();
			}
		});
	}
}