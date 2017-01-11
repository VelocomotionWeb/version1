if (store)
{
	$("#latitude").val(lat);
	$("#longitude").val(lng);
	$("#map_address").val(map_address);
}
$(document).ready(function(){
	$(".delete_store_logo_error").hide()
	$(".delete_store_logo_success").hide()
	// when edit store
	{if isset($store)}
		getStateJs({country_id}, {state_id});
		{if !empty(products)}
			getSellerProductsJs({id_seller}, {products});
		{/if}
	{/if}
	
	$("#countries").on("change", function(){
		var id_country = $(this).val();
		if (id_country == "")
			alert("Please select a country");
		else
			getStateJs(id_country);
	});


	$("#seller_name").on("change", function(){
		var id_seller = $(this).val();
		if (id_seller == 0)
		{
			alert("Please select seller");
			$("#store_products").empty();
			return false;
		}
		else
			getSellerProductsJs(id_seller);
	});

	$(document).on("click", ".delete_store_logo", function(e){
		e.preventDefault();
		var id_store = $(this).data("id_store");
		if (confirm("Are you sure?"))
		{
			$.ajax({
				url:admin_str_loc,
				dataType: "json",
				data: {
					ajax: "1",
					action: "deleteStoreLogo",
					id_store: id_store
				},
				success: function(result){
					if (result.status == "success")
					{
						$(".delete_store_logo_success").show();
						$(".delete_store_logo_success").html(result.msg);
						location.reload(true);
					}
					else
					{
						$(".delete_store_logo_error").show();
						$(".delete_store_logo_error").html(result.msg);
					}
				}
			})
		}
	});

	// filter state by country
	function getStateJs(id_country, id_state_selected)
	{
		$.ajax({
			url:admin_str_loc,
			dataType: "json",
			data: {
				ajax: "1",
				action: "filterStates",
				id_country: id_country
			},
			success: function(result){
				if (result != 'failed')
				{
					$("#state").empty();
					$("#state").append("<option value=''>Select</option>");
					$.each(result, function(index, value){
						if (id_state_selected == value.id_state)
							$("#state").append("<option value="+value.id_state+" selected>"+value.name+"</option>");
						else
							$("#state").append("<option value="+value.id_state+">"+value.name+"</option>");
					});
				}
			}
		});
	}

	// getting seller products
	function getSellerProductsJs(id_seller, id_products)
	{
		var selected_products = [];
		var all_products = [];
		$.ajax({
			url:admin_str_loc,
			dataType: "json",
			data: {
				ajax: "1",
				action: "getSellerProducts",
				id_seller: id_seller
			},
			success: function(result){
				$("#store_products").empty();
				if (result != 'failed')
				{
					$.each(result, function(index, value){
						if (id_products)
						{
							$.each(id_products, function(i, v){
								if (v.id_product == value.id_product)
								{
									$("#store_products").append("<option value="+value.id_product+" selected>"+value.product_name+"</option>");
									selected_products.push(value.id_product);
								}
							});
						}
						else
							$("#store_products").append("<option value="+value.id_product+">"+value.product_name+"</option>");

						all_products.push(value.id_product);
					});

					if (id_products)
					{
						var other = getArrayDiff(all_products, selected_products);
						$.each(result, function(index, value){
							$.each(other, function(i, v){
								if (value.id_product == v)
									$("#store_products").append("<option value="+value.id_product+">"+value.product_name+"</option>");
							});
						});
					}
				}
				else
					$("#store_products").append("<option value='0'>"+no_product+"</option>");
			}
		});
	}

	function getArrayDiff(large_array, small_array)
	{
		var diff = [];
		$.grep(large_array, function(el) {
	        if ($.inArray(el, small_array) == -1)
	        	diff.push(el);
		});

		return diff;
	}
});