$("#latitude").val(lat);
$("#longitude").val(lng);
$("#map_address").val(map_address);

$(document).ready(function(){
	filterState(country_id);

	$(document).on("change", "#countries", function(){
		var id_country = $(this).val();
		filterState(id_country);
	});

	$(".delete_store_logo").on("click", function(){
		if (!confirm("Are you sure?"))
			return false;
	});
});

function filterState(id_country)
{
	$.ajax({
		url: url_file_state_edit,
		dataType: "json",
		data: {
			id_country: id_country
		},
		success: function(result){
			if (result != 'failed')
			{
				$("#state").empty();
				$("#state").append("<option value=''>Select</option>");
				$.each(result, function(index, value){
					if (id_state == value.id_state)
						$("#state").append("<option value="+value.id_state+" selected>"+value.name+"</option>");
					else
						$("#state").append("<option value="+value.id_state+">"+value.name+"</option>");
				});
			}
		}
	});
}