$(document).ready(function(){
	$("#id_attribute_group option").each(function(i,v){
		if($(this).html() == "date")
		{
			$(this).hide();
		
			if ($(this).attr('selected') == 'selected')
			{
				$(this).removeAttr('selected');
				$(this).hide();
			}
		}	
	});

	$('#submit_date_form').on('click',function(){
		if ($('#start_date').val() == '')
		{
			alert('start date can not be empty');
			return false;
		}
		else if ($('#end_date').val() == '')
		{
			alert('end date can not be empty');
			return false;
		}
	});	
	$('.edit, .delete').on('click', function(e){
		var attr = $.trim($(this).closest('tr').find('td:nth-child(3)').text());
		if (attr == 'date' || attr == 'shows' || attr == 'class')
		{
			e.preventDefault();
			alert('You can not edit or delete this attribute');
			return false;
		}
	});
});