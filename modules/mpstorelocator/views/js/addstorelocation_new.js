$(document).ready(function(){
	$("#countries").on("change", function(){
		var id_country = $(this).val();
		$.ajax({
			url:url_filestate,
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
						$("#state").append("<option value="+value.id_state+">"+value.name+"</option>");
					});
				}
			}
		})
	});
});