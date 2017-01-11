$(document).ready(function(){
	// form validation
	$('#submit_store').on('click', function()
	{
		var seller_name = $('#seller_name').val();
		var shop_name = $('#shop_name').val();
		var street = $('#street').val();
		var city_name = $('#city_name').val().trim();
		var countries = $('#countries').val();
		var zip_code = $('#zip_code').val();
		var latitude = $('#latitude').val();
		var special_char = /^[^<>;=#{}]*$/;

		if(seller_name == '' || seller_name == 0)
		{
			alert(req_seller_name);
			$('#seller_name').focus();
			return false;
		}
		else if(shop_name == '')
		{
			alert(req_shop_name);
			$('#shop_name').focus();
			return false;
		}
		else if(!isNaN(shop_name) || !special_char.test(shop_name))
		{
			alert(inv_shop_name);
			$('#shop_name').focus();
			return false;
		}
		else if(street == '')
		{
			alert(req_street);
			$('#street').focus();
			return false;
		}
		else if(city_name == '')
		{
			alert(req_city_name);
			$('#city_name').focus();
			return false;
		}
		else if(!isNaN(city_name) || !special_char.test(city_name))
		{
			alert(inv_city_name);
			$('#city_name').focus();
			return false;
		}
		else if(countries == '')
		{
			alert(req_countries);
			$('#countries').focus();
			return false;
		}
		else if(zip_code == '')
		{
			alert(req_zip_code);
			$('#product_quantity').focus();
			return false;
		}
		else if(zip_code.length >12)
		{
			alert(inv_zip_code);
			$('#zip_code').focus();
			return false;
		}
		else if(latitude == '')
		{
			alert(req_latitude);
			return false;
		}
	});

	$("#btn_store_search").on("click", function(){
		codeAddress();
	});

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

	var geocoder;
	var map;
	var infowindow = new google.maps.InfoWindow();
	var marker;

	// map initialization on add store page
	function initialize()
	{
		geocoder = new google.maps.Geocoder();
		if (typeof id_store != 'undefined') //if edit store seller store
		{
			var mapOptions = {
				center: new google.maps.LatLng(lat, lng),
				zoom: 17
			};
		}
		else
		{
			var mapOptions = {
				center: new google.maps.LatLng(-33.8688, 151.2195),
				zoom: 13
			};
		}

		map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

	  	var input = document.getElementById('pac-input');
		var types = document.getElementById('type-selector');
		map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
		map.controls[google.maps.ControlPosition.TOP_LEFT].push(types);

		var autocomplete = new google.maps.places.Autocomplete(input);
		autocomplete.bindTo('bounds', map);
		

		if (typeof id_store != 'undefined') //if edit store seller store
		{
			var marker = new google.maps.Marker({
				map: map,
				anchorPoint: new google.maps.Point(0, -29),
				position: mapOptions.center
			});
			infowindow.setContent(map_address);
			infowindow.open(map, marker);
		}
		else
		{
			var marker = new google.maps.Marker({
				map: map,
				anchorPoint: new google.maps.Point(0, -29)
			});
		}

		google.maps.event.addListener(autocomplete, 'place_changed', function() {
			infowindow.close();
			marker.setVisible(false);
			var place = autocomplete.getPlace();

			if (!place.geometry) {
			  return;
			}

			// If the place has a geometry, then present it on a map.
			if (place.geometry.viewport) {
			  map.fitBounds(place.geometry.viewport);
			} else {
			  map.setCenter(place.geometry.location);
			  map.setZoom(17);  // Why 17? Because it looks good.
			}
			marker.setIcon(({
			  url: place.icon,
			  size: new google.maps.Size(71, 71),
			  origin: new google.maps.Point(0, 0),
			  anchor: new google.maps.Point(17, 34),
			  scaledSize: new google.maps.Size(35, 35)
			}));
			marker.setPosition(place.geometry.location);
			marker.setVisible(true);

			var address = '';
			if (place.address_components) {
			  address = [
			    (place.address_components[0] && place.address_components[0].short_name || ''),
			    (place.address_components[1] && place.address_components[1].short_name || ''),
			    (place.address_components[2] && place.address_components[2].short_name || '')
			  ].join(' ');
			}

			//get lat, lng and address from map
			$("#latitude").val(place.geometry.location.lat());
			$("#longitude").val(place.geometry.location.lng());
			$("#map_address").val('<div><strong>' + place.name + '</strong><br>' + address + '</div>');
			$("#map_address_text").val($("#pac-input").val());

			infowindow.setContent('<div><strong>' + place.name + '</strong><br>' + address);
			infowindow.open(map, marker);
		});
	}

	function codeAddress()
	{
		var address = document.getElementById('pac-input').value;
		var marker = new google.maps.Marker({
				map: map,
				anchorPoint: new google.maps.Point(0, -29)
			});

		infowindow.close();
		marker.setVisible(false);

		geocoder.geocode( { 'address': address}, function(results, status) {
			if (status == google.maps.GeocoderStatus.OK)
			{
				var place = results[0];

				map.setCenter(place.geometry.location);
				marker.setPosition(place.geometry.location);
				marker.setVisible(true);

				$("#latitude").val(place.geometry.location.lat());
				$("#longitude").val(place.geometry.location.lng());
				$("#map_address").val('<div>'+place.formatted_address+'</div>');
				$("#map_address_text").val($("#pac-input").val());

				infowindow.setContent(place.formatted_address);
        		infowindow.open(map, marker);
			}
			else
			{
				alert('Geocode was not successful for the following reason: ' + status);
			}
		});
	}

	google.maps.event.addDomListener(window, 'load', initialize);