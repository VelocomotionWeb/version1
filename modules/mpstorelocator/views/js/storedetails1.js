$(document).ready(function(){
	var store_loc_lat = $('#store_loc_lat').val();
	

	if (typeof store_loc_lat != 'undefined')
	{	
		var id = $('#first_id').val();
		var address = $('#address').val();
		var first_lat = $('#first_lat').val();
		var first_lng = $('#first_lng').val();
		
		locateStore(address, first_lat, first_lng);
		load_product(id);
		
		function locateStore1(address, lat, lng)
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
		function locateStorePOINT1(address, lat, lng)
		{
			var myLatlng = new google.maps.LatLng(lat, lng);
			var mapOptions = {
				zoom: 4,
				center: myLatlng
			};

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
	}	
	else
	{
		var html_var = '<center><h2>'+no_store_msg+'</h2></center>';
		$('#wrapper_content_left').html(html_var);
	}
	$(document).on("click", ".wk_store", function(){ locateSeller($(this).attr('id'))});

	function locateSeller(id)
	{
		//var id = $(this).attr('id');
		var address = $('#'+id).attr('addr');
		var lat = $('#'+id).attr('lat');
		var lng = $('#'+id).attr('lng');
		console.log("locateStorePOINT("+address+","+lat+","+lng+")");
		locateStorePOINT(address, lat, lng);
	};

	/*
	$(document).on("hover", ".wk_store", function(){
		var id = $(this).attr('id');
		var address = $(this).attr('addr');
		var lat = $(this).attr('lat');
		var lng = $(this).attr('lng');
		locateStore(address, lat, lng);
	});
	*/
		var map = new google.maps.Map(document.getElementById('map-canvas'));
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

	function locateStorePOINT(address, lat, lng)
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
		map.setZoom(12); // Why 17? Because it looks good.
		google.maps.event.addListener(marker, 'click', function() {
			infowindow.open(map,marker);
		});
		infowindow.open(map,marker);
	}


	// while filter by product name
	$("#select_search_products").on("change", function()
	{
		var id_product = $(this).val();
		var is_all_product = 0;
		if(id_product == 0)
			is_all_product = $('#select_search_products option[value = 0]').data('all_product');
		$.ajax({
			url: url_getstore_by_product,
			data: {
				id_product: id_product,
				is_all_product: is_all_product
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
			}
		});
	});

	// while filter by city
	$("#go_btn").on("click", function(){
		var key = $("#search_city").val();
		var id_product = $('#select_search_products option[value = 0]').data('all_product');
		if (key != '')
		{
			$.ajax({
				url:url_getstorebykey,
				data: {
					search_key: key,
					id_product: id_product
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
				}
			});
		}
	});

	$("#reset_btn").on("click", function(){
		location.reload(true);
	});
	$(".see_prod").on("click", function(){
		//alert($(this).attr('sid'));
		load_product($(this).attr('sid'));
		//$.scrollTo('#bloc_seller_prod', 50);
	});
	function load_product(id)
	{
		link = $('#see_prod_'+id).attr('link');
		$.ajax({
			url: '/module/marketplace/shopcollection?mp_shop_name='+link+'&content_only=1',
			/*data: {
				id_product: id_product,
				is_all_product: is_all_product
			},*/
			success: function(result)
			{
				$("#wrapper_content_left").html(result);
			}
		});
	}
});
