
$(document).ready(function(){
	/*show the first array lat and lag on the page when page load*/
	locateStore(address, first_lat, first_lng);

	/* when clicking on any store*/
	$(document).on("click", ".wk_store", function(){ locateSeller($(this).attr('id'))});
		/*var id = $(this).attr('id');
		var address = $(this).attr('addr');
		var lat = $(this).attr('lat');
		var lng = $(this).attr('lng');
		locateStore(address, lat, lng);
		*/
	

	function locateSeller(id)
	{
		//var id = $(this).attr('id');
		var address = $('#'+id).attr('addr');
		var lat = $('#'+id).attr('lat');
		var lng = $('#'+id).attr('lng');
		locateStore(address, lat, lng);
	};
	
	function locateStore(address, lat, lng)
	{
		var myLatlng = new google.maps.LatLng(lat, lng);
		var mapOptions = {
			zoom: 4,
			center: myLatlng
		};

		var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
		var contentString = '<div id="content">'+address+'</div>';

		var infowindow = new google.maps.InfoWindow({
			content: contentString,
			maxWidth: 250
		});

		var marker = new google.maps.Marker({
			position: myLatlng,
			map: map
		});
		map.setZoom(17); // Why 17? Because it looks good.
		google.maps.event.addListener(marker, 'click', function() {
			infowindow.open(map,marker);
		});
		infowindow.open(map,marker);
	}

	// while filter by seller name
	$(document).on("change", "#select_search_seller", function(){
		var id_seller = $(this).val();
		$.ajax({
			url: url_getstore_by_seller,
			data: {
				id_seller: id_seller
			},
			success: function(result){
				$("#wrapper_content_left").html(result);
			}
		});
	});

	// while filter by product name
	$(document).on("change", "#select_search_products", function(){
		var id_product = $(this).val();
		var id_seller = $("#id_seller").val();
		$.ajax({
			url: url_getstorebyproduct,
			data: {
				id_product: id_product,
				id_seller: id_seller,
				edit_store: "1"
			},
			success: function(result)
			{
				if(result != '<center><h2>No store found</h2></center>')
				{
					$("#wrapper_content_left").html(result);
					var id = $('.wk_store').attr('id');
					var address = $('.wk_store').attr('addr');
					var lat = $('.wk_store').attr('lat');
					var lng = $('.wk_store').attr('lng');
					locateStore(address, lat, lng);
				}
				else
				{
					$('#map-canvas').empty();
					$("#wrapper_content_left").html(result);
				}
				//$("#wrapper_content_left").html(result);
			}
		});
	});

	// while filter by city
	$(document).on("click", "#go_btn", function(){
		var key = $("#search_city").val();
		if (key != '')
		{
			$.ajax({
				url: url_getstorebykey,
				data: {
					search_key: key,
					seller_stores: "1"
				},
				success: function(result)
				{
					if(result != '<center><h2>No store found</h2></center>')
					{
						$("#wrapper_content_left").html(result);
						var id = $('.wk_store').attr('id');
						var address = $('.wk_store').attr('addr');
						var lat = $('.wk_store').attr('lat');
						var lng = $('.wk_store').attr('lng');
						locateStore(address, lat, lng);
					}
					else
					{
						$('#map-canvas').empty();
						$("#wrapper_content_left").html(result);
					}
					//$("#wrapper_content_left").html(result);
				}
			});
		}
	});

	$(document).on("click", ".delete_store", function(){
		if(!confirm("Are you sure?"))
			return false;
	});

	$(document).on("click", "#reset_btn", function(){
		location.reload(true);
	});
	$('#icon_id i').show();
});

