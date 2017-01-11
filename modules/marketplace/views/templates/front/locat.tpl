<div class="main_block">
    {include file="$tpl_dir./../../modules/blocksearchhome/blocksearch-home_top1.tpl"}

    <a name="top"></a>

	<div id = "loading" class = "se-pre-con" style="display:none"></div>
    <input type="hidden" name="mp_shop_name" id1="mp_shop_name" value="{$name_shop}">
    <div id="map_canvas" class="maplocat"></div>

</div>

<div id="name_shop" style="display:none">{$name_shop}</div>
<div id="shop_link_rewrite" style="display:none">{$shop_link_rewrite_ec}</div>
<div id="s_date_depart" style="display:none">{$date_depart}</div>
<div id="s_date_fin" style="display:none">{$date_fin}</div>

{strip}
	{addJsDef requestSortProducts=$link->getModuleLink('marketplace', 'shopcollection', ['mp_shop_name' => $name_shop])}
	{addJsDef id_store=$id_store}
	<span style="display:none" id="message_confirm_delete">{l s='Rental added to cart, continue reservations' mod='marketplace'}</span>
{/strip}
{if isset($name_shop)}
{/if}
<script>



{literal}

$(document).ready(function(){

	setTimeout(function() {
		if($('#active').val() != '') {
			$('body').scrollTo(0);
		}

	},1000);

	$("a.fancybox").fancybox();

{/literal}


	//$('.typeloc .dropdown-menu').css("height",{$nbtype_locs}*30)

	map_reset();


	function locateStorePOINT(address, lat, lng)
	{




		var myLatlng = new google.maps.LatLng(lat, lng);
		var mapOptions = {
			zoom: 17,
			center: myLatlng
		};

		//var map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);

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

	function map_reset() {

		var isDrag = $(window).width()<780? false:true;

		var distance = {$distance};

		if (distance >= 3000) zoom = 4;
		else if (distance >= 1500) zoom = 5;
		else if (distance >= 800) zoom = 6;
		else if (distance >= 600) zoom = 7;
		else if (distance >= 400) zoom = 8;
		else if (distance >= 200) zoom = 9;
		else if (distance >= 100) zoom = 10;
		else zoom  = 11;

		var lat = "{$latitude}";

		var lng = "{$longitude}";

		var myLatlng = new google.maps.LatLng(lat,lng);

		//var myLatlng = new google.maps.LatLng(45.758796,4.834607);
		 {literal}
		//

		var myMapOptions = {
			zoom: zoom,
			draggable: isDrag,
			center: myLatlng,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		};

		// Création de la carte
		var myMap = new google.maps.Map(
			document.getElementById('map_canvas'),
			myMapOptions
		);

		// Création du marker

		// Création de l'icône

        var myMarkerImage = new google.maps.MarkerImage('/img/FAVICON_small.png');
		{/literal}

		 {foreach $store_locations as $store}
			{if $store.distance_user<=$distance}
				var id = "{$store.id}";
				var name = "{$store.name}";
				var address = ""; //"{$store.map_address}";
				var lat = "{$store.latitude}";
				var lng = "{$store.longitude}";
				{literal}
				var myLatlng = new google.maps.LatLng(lat,lng);
				var myMarker_{/literal}{$store.id}{literal} = new google.maps.Marker({
					position: myLatlng,
					map: myMap,
					icon: myMarkerImage,
					title:  name
				});

				var myWindowOptions_{/literal}{$store.id}{literal} = {

					content:

					'<h6>'+name+'</h6>'+

					''+address+''+

					'<br><input type="button" link="{/literal}{$store.link_rewrite}{literal}" sid="{/literal}{$store.id}{literal}" value="Voir les produits" name="see_map_prod" class="submit see_map_prod" id="see_map_prod_{/literal}{$store.id}{literal}" onclick="$(\'div[id='+id+']\').click();">'
				};

				// Création de la fenêtre

				var myInfoWindow_{/literal}{$store.id}{literal} = new google.maps.InfoWindow(myWindowOptions_{/literal}{$store.id}{literal});

				google.maps.event.addListener(myMarker_{/literal}{$store.id}{literal}, 'click', function() {

					$('input.see_map_prod').parent().parent().parent().parent().css('visibility', 'hidden');

					myInfoWindow_{/literal}{$store.id}{literal}.open(myMap,myMarker_{/literal}{$store.id}{literal});

				});

				{/literal}


		{/if}
		{/foreach}	{literal}


		var store_loc_lat = $('#store_loc_lat').val();
	}

	function locateStore(address, lat, lng)
	{
		var myLatlng = new google.maps.LatLng(lat, lng);
		var mapOptions = {
			zoom: 17,
			center: myLatlng
		};

		var map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);
	}

	$("#map_full, #map_small").on("click", function() {
		if ($('#map_canvas').hasClass('map_large'))
		{
		  $('#map_canvas, #map_small').removeClass('map_large');
		}
		else
		{
		  $('#map_canvas, #map_small').addClass('map_large');
		}
		map_reset();
	});


	// while filter by product name
	$("#select_search_products***").on("change", function()
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
				if(result != '<center><h2>Pas de loueur trouvé...</h2></center>')
				{
					$("#wrapper_content_center").html(result);
					var id = $('.wk_store').attr('id');
					var address = $('.wk_store').attr('addr');
					var lat = $('.wk_store').attr('lat');
					var lng = $('.wk_store').attr('lng');
					locateStore(address, lat, lng);
				}
				else
				{
					$('#map_canvas').empty();
					$("#wrapper_content_center").html(result);
				}
			}
		});
	});

	// while filter by city

	$("#go_btn***").on("click", function(){
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
					if(result != '<center><h2>Pas de loueur trouvé...</h2></center>')
					{
						$("#wrapper_content_center").html(result);
						var id = $('.wk_store').attr('id');
						var address = $('.wk_store').attr('addr');
						var lat = $('.wk_store').attr('lat');
						var lng = $('.wk_store').attr('lng');
						locateStore(address, lat, lng);
					}
					else
					{
						$('#map_canvas').empty();
						$("#wrapper_content_center").html(result);
					}
				}
			});
		}
	});


	$("#reset_btn").on("click", function(){
		location.reload(true);
	});

	setTimeout(function(){

		$(".see_prod").on("click", function()
		{

			//load_product($(this).attr('sid'));

			$('#id_store').val($(this).attr('sid'));
			var cur_id = $(this).attr('id');
			var lat = $('div[id='+$(this).attr('id')+']').attr('lat');
			var lng = $('div[id='+$(this).attr('id')+']').attr('lng');
			link = $('div[id='+$(this).attr('id')+']').attr('link');
			store_name = $('div[id='+$(this).attr('id')+']').attr('store_name');
			$('#refresh_'+cur_id).submit();

			//return false;
			/*$('#group-seller').animate({scrollTop: ($('div[id='+cur_id+']').attr('counter')-1) * 186});
			$('div.see_prod').css('background', 'transparent');
			$('div[id='+cur_id+']').css('background', '#ddd');
			$('#wrapper_content_center').html('<br><br><center><img src=/img/loadingAnimation.gif></center>');
			$('#wrapper_content_center').fadeIn(1000);
			*/

			$('#map_canvas, #map_small').removeClass('map_large');

			url = '/module/marketplace/list?mp_shop_name='+link+
			'&city={/literal}{$city}{literal}'+
			'&latitude='+lat+'&longitude='+lng+
			'&quantite={/literal}{$quantite}{literal}'+
			'&date_depart={/literal}{$date_depart}{literal}&date_fin={/literal}{$date_fin}{literal}' +'';

			//window.location.href=url;
			//alert(url);
			return false;
			/*
			$.ajax({
				url: '/module/marketplace/shopcollection?mp_shop_name='+link+'&quantite={/literal}{$quantite}{literal}&date_depart={/literal}{$date_depart}{literal}&date_fin={/literal}{$date_fin}{literal}&l_type_loc={/literal}{$l_type_loc}{literal}&content_only=1',
				success: function(result)
				{
					String.prototype.replaceAll = function(search, replacement) {
						var target = this;
						return target.split(search).join(replacement);
					};

					//result = result.replaceAll('/js/jquery/ui/jquery.ui', '');
					//result = result.replaceAll('/js/jquery/ui/i18n/jquery.ui', '');
					//result = result.replaceAll('/themes/jms_travel/js/modules/blockcart/ajax-cart.js', '');
					//result = result.replaceAll('/js/jquery/jquery-1.11.0.min.js', '');
					//result = result.replaceAll('/js/jquery/jquery-migrate-1.2.1.min.js', '');
					//result = result.replaceAll('/modules/jmsajaxsearch/views/js/ajaxsearch.js', '');

					//result = result.replaceAll('megamenu', '');
					//$('#wrapper_content_center').hide();
					$('#wrapper_content_center').html(result);
					//result = result.search('<body>')-1;
					result = result.substring(0, result.search('body')-1);


					// pour pallier un soucis de rechargement pair, on doit afficher 2 fois le contenu du result mais dans une zone cachée et sans le body

					//if($('body').attr('first')!=1)
					$('#wrapper_content_center_cache').html($('#wrapper_content_center').html());
					$('body').attr('first',0);
				}
			});
			*/
		});
	}, 1);

	// charge la liste des produits par defaut du premier loueur

	/*
	var trouve = 0;
	var id_store1 = {/literal}{$store_locations[0]['id']}{literal};
	var id_store = {/literal}{$id_store*1}{literal};
	$('#wrapper_content_left > div').each(function(){
		if (id_store==$(this).attr('id')) trouve =1;
	});

	if (trouve==0) id_store = id_store1;
	setTimeout(function(){ $('body').attr('first',1); $('div[id='+id_store+']').click(); $('div[id='+id_store+']').css('background', '#ddd'); }, 1000);
	*/

	// reposition du bloc canvas (et du bloc image)

	/*if ($(document).width()>768)
		setInterval(function()
		{
			if ($(document).scrollTop() > $('#map_canvas').parent().offset().top && $(document).scrollTop() < $('#wrapper_content').height() )
				$('#map_canvas').animate({top: $(document).scrollTop() - $('#map_canvas').parent().offset().top}, 'slow');
			if ($(document).scrollTop() < $('#map_canvas').parent().offset().top )
				$('#map_canvas').animate({top: 0}, 'slow');
			if ( $(document).scrollTop() > $('#wrapper_content').height() )
				$('#map_canvas').animate({top: $('#wrapper_content').height() -  $('#map_canvas').height()}, 'slow');
		}, 1000);*/
});


{/literal}


</script>