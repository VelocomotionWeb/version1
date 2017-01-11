{capture name=path}{l s='Store Location' mod='mpstorelocator'}{/capture}
<link rel="stylesheet" type="text/css" href="/modules/blocksearchhome/blocksearchhome_top.css">

<div id="wrapper_store">
	{*
    <button class="btn btn-default button button-small other_btn" id="reset_btn" style="float:left;">
		<span>{l s='Reset' mod='mpstorelocator'}</span>
	</button>
	*}
    
    {include file="$tpl_dir./../../modules/blocksearchhome/blocksearch-home_top.tpl"}
    
	<a name="vs"></a>
    <div class="page-title" style="background-color:{$title_bg_color|escape:'html':'UTF-8'};">
		<span style="color:{$title_text_color|escape:'html':'UTF-8'};">
			<i class="icon-map-marker"></i>
			{*l s='Store Locator' mod='mpstorelocator'*}
            {l s='Seller found on' mod='mpstorelocator'} {$city} {l s='around ' mod='mpstorelocator'}
		</span>
	</div>
	<div id="wrapper_content">
		        
		<div class="wrapper_left_div">
			{*
            <div id="search_city_block" style="border-bottom:1px solid #dbdbdb;">
				<div id="search_city_field">
					<input id="search_city" class="form-control" type="text" placeholder="{l s='Enter City Name' mod='mpstorelocator'}" />
					<div id="wk_sl_search_spyglass">
						<!-- <img src="{$modules_dir|escape:'html':'UTF-8'}mpstorelocator/views/img/icon-searchimg.png" class="img-responsive"> -->
					</div>
				</div>
				<button class="btn btn-default button button-small" id="go_btn">
					<span>{l s='Go' mod='mpstorelocator'}</span>
				</button>
			</div>
			*}
			<div id="wrapper_content_left">
				{if isset($store_locations)}
                	{assign var='distance_max' value=0}
                    {assign var='trouve' value=0}
                    {foreach $store_locations as $store}
                    	{if $store.distance_user<=$distance}
                        	{assign var='trouve' value=1}
                            {if $distance_max<$store.distance_user}
                            	{$distance_max=$store.distance_user}
                            {/if}
                            <div class="wk_store" style="border-bottom:1px solid #dbdbdb" id="{$store.id|escape:'htmlall':'UTF-8'}" addr="{$store.map_address|escape:'htmlall':'UTF-8'}" lat="{$store.latitude|escape:'htmlall':'UTF-8'}" lng="{$store.longitude|escape:'htmlall':'UTF-8'}" name_shop="{$store.link_rewrite}">
                                <div class="wk_store_img">
                                    {if $store.img_exist}
                                        <img src="{$modules_dir|escape:'html':'UTF-8'}mpstorelocator/views/img/store_logo/{$store.id|escape:'htmlall':'UTF-8'}.jpg"/>
                                    {else}
                                        <img src="{$modules_dir|escape:'html':'UTF-8'}mpstorelocator/views/img/store_logo/default.jpg"/>
                                    {/if}
                                        {if $store.distance_user>1}
                                        	<span class="aff_distance">{$store.distance_user} Km</span>
                                        {/if}
                                </div>
                                <div class="wk_store_details">
                                    <ul>
                                        <li class="store_name">{$store.name|escape:'html':'UTF-8'}</li>
                                        <li>{$store.street}</li>
                                        {*<li>{$store.state_name|escape:'html':'UTF-8'} {$store.zip_code|escape:'html':'UTF-8'}</li>*}
                                        {*<li>{$store.country_name|escape:'html':'UTF-8'}</li>*}
                                    </ul>
                                    {*<input id="see_map_{$store.id|escape:'htmlall':'UTF-8'}" class="submit see_map" type="button" name="see_map" value="Voir plan" 
                                    sid="{$store.id|escape:'htmlall':'UTF-8'}">
                                    *}
                                    <input id="see_prod_{$store.id|escape:'htmlall':'UTF-8'}" class="submit see_prod" type="button" name="see_prod" 
                                    value="{l s='View products' mod='mpstorelocator'}" 
                                    sid="{$store.id|escape:'htmlall':'UTF-8'}" link="{$store.link_rewrite}" >
                                    <a href="/module/marketplace/shopstore?mp_shop_name={$store.link_rewrite}" target="_blank">
                                    	<input id="see_seller_{$store.id|escape:'htmlall':'UTF-8'}" class="submit see_seller" type="button" name="see_seller" 
                                    	value="{l s='Seller detail' mod='mpstorelocator'}" >
                                    </a>
                                </div>
                            </div>
                        {/if}
                    {/foreach}
                    {if $trouve}
                        <input type="hidden" name="store_loc_lat" id="store_loc_lat" value="{$store_locations['0'].latitude}">
                        <input type="hidden" name="first_lat" id="first_lat" value="{$store_locations['0'].latitude}">
                        <input type="hidden" name="first_lng" id="first_lng" value="{$store_locations['0'].longitude}">
                        <input type="hidden" name="address" id="address" value="{$store_locations['0'].map_address}">
                        <input type="hidden" name="first_id" id="first_id" value="{$store_locations['0'].id}">
					{/if}
                {/if}
			</div>
		</div>
        <div id="wrapper_content_center">
			<div>
			</div>
		</div>
        <div id="wrapper_content_center_cache" style="display:none">
		</div>
		<div id="wrapper_content_right">
			<div id="map-canvas"></div>
			<div id="map-img"></div>
		</div>
		<div style="clear:both;"></div>
        
        
	</div>
	
</div>
{strip}
	{addJsDefL name='no_store_msg'}{l s='No store found.' js=1 mod='mpstorelocator'}{/addJsDefL}
	{addJsDef url_getstore_by_product=$link->getModuleLink('mpstorelocator', 'getstorebyproduct')}
	{addJsDef url_getstorebykey=$link->getModuleLink('mpstorelocator', 'getstorebykey')}
{/strip}
<script>
{literal}
$(document).ready(function(){
	
	{/literal}
		
	$('.typeloc .dropdown-menu').css("height",{$nbtype_locs}*30)

			
	zoom  = {floor(10 - $distance_max/100 )-1};
	var lat = "{$latitude}";
	var lng = "{$longitude}";
	var myLatlng = new google.maps.LatLng(lat,lng);
	//var myLatlng = new google.maps.LatLng(45.758796,4.834607);
	 {literal}
	 
	// 
	var myMapOptions = {
		zoom: zoom,
		center: myLatlng,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	};
	 
	// Création de la carte
	var myMap = new google.maps.Map(
		document.getElementById('map-canvas'),
		myMapOptions
		);
	 
	
	// Création du marker
	// Création de l'icône
	//var myMarkerImage = new google.maps.MarkerImage('../../img/admin/add_2.gif');
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
				//icon: myMarkerImage,
				title:  name
			});
			var myWindowOptions_{/literal}{$store.id}{literal} = {
				content:
				'<h6>'+name+'</h6>'+
				''+address+''+
				'<br><input type="button" link="{/literal}{$store.link_rewrite}{literal}" sid="{/literal}{$store.id}{literal}" value="Voir les produits" name="see_map_prod" class="submit see_map_prod" id="see_map_prod_{/literal}{$store.id}{literal}" onclick="document.getElementById(\'see_prod_'+id+'\').click();">'
			};
			 
			// Création de la fenêtre
			var myInfoWindow_{/literal}{$store.id}{literal} = new google.maps.InfoWindow(myWindowOptions_{/literal}{$store.id}{literal});	
			google.maps.event.addListener(myMarker_{/literal}{$store.id}{literal}, 'click', function() {
				{/literal}{foreach $store_locations as $storeclose}{if $storeclose.distance_user<=$distance}myInfoWindow_{$storeclose.id}.close();{/if}{/foreach}{literal}
				myInfoWindow_{/literal}{$store.id}{literal}.open(myMap,myMarker_{/literal}{$store.id}{literal});
			});
            {/literal}
		
	{/if}
	{/foreach}
	{literal}
	 
	var store_loc_lat = $('#store_loc_lat').val();
	function locateStore(address, lat, lng)
	{
	var myLatlng = new google.maps.LatLng(lat, lng);
	var mapOptions = {
		zoom: 9,
		center: myLatlng
	};
	
	var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
	}
	
	function locateStorePOINT(address, lat, lng)
	{
	
	var myLatlng = new google.maps.LatLng(lat, lng);
	var mapOptions = {
		zoom: 4,
		center: myLatlng
	};
	//var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
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
				$('#map-canvas').empty();
				$("#wrapper_content_center").html(result);
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
					$('#map-canvas').empty();
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
	$(".see_prod").on("click", function(){
	//load_product($(this).attr('sid'));
	$('#id_store').val($(this).attr('sid'));
	link = $('#see_prod_'+$(this).attr('sid')).attr('link');
	$.ajax({
		url: '/module/marketplace/shopcollection?mp_shop_name='+link+'&quantite={/literal}{$quantite}{literal}&date_depart={/literal}{$date_depart}{literal}&date_fin={/literal}{$date_fin}{literal}&l_type_loc={/literal}{$l_type_loc}{literal}&content_only=1',
		/*data: {
			id_product: id_product,
			is_all_product: is_all_product
		},*/
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
			$('#wrapper_content_center').html(result);
			//result = result.search('<body>')-1;
			result = result.substring(0, result.search('body')-1);
			// pour pallier un soucis de rechargement pair, on doit afficher 2 fois le contenu du result mais dans une zone cachée et sans le body
			
			$('#wrapper_content_center_cache').html(result);
			$('input.see_prod').parent().parent().css('background', 'transparent');
			$('div[name_shop='+link+']').css('background', '#ddd');
			/*
			setTimeout(function(){ 
				$('.typeloc .dropdown-menu').hide();						
				$('#cart_block .dropdown-menu').hide();						
	
				if (!$('.typeloc').attr('click') == 1)
				{
					$('#cart_block .dropdown-toggle').click(function(){ $('#cart_block .dropdown-menu').toggle();e.preventDefault(); });
					$('.typeloc').click(function(){ 
						$('.typeloc .dropdown-menu').toggle();
					});
					$('.typeloc').attr('click', 1);
				}
				//$('body').not($('#cart_block .dropdown-toggle')).not($('.typeloc')).click(function(){cache_element()});
				
			}, 1);
			*/
		}
	});
	});}, 1);
	
	// charge la liste des produits par defaut du premier loueur
	var trouve = 0;
	var id_store1 = {/literal}{$store_locations[0]['id']}{literal};
	var id_store = {/literal}{$id_store*1}{literal};
	$('#wrapper_content_left > div').each(function(){
		if (id_store==$(this).attr('id')) trouve =1;
	});
	if (trouve==0) id_store = id_store1;
	
	setTimeout(function(){ $(".see_prod[id=see_prod_"+id_store+"]").click(); }, 1000);
	
	// reposition du bloc canvas (et du bloc image)
	if ($(document).width()>768)
		setInterval(function()
		{
			if ($(document).scrollTop() > $('#map-canvas').parent().offset().top && $(document).scrollTop() < $('#wrapper_content').height() )
				$('#map-canvas').animate({top: $(document).scrollTop() - $('#map-canvas').parent().offset().top}, 'slow');
			if ($(document).scrollTop() < $('#map-canvas').parent().offset().top )
				$('#map-canvas').animate({top: 0}, 'slow');
			if ( $(document).scrollTop() > $('#wrapper_content').height() )
				$('#map-canvas').animate({top: $('#wrapper_content').height() -  $('#map-canvas').height()}, 'slow');
		}, 1000);

});
{/literal}
</script>
