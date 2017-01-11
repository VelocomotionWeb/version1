{*
* 2007-2014 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<div class="slider-product-title">
	<h3 class="title">
		<span>{l s='Our location' mod='jmsmaplocation'}</span>
		<span class="icon-title"></span>
	</h3>
</div>
<section class="maploc-stores margin-box hidden-xs"><div>
<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?libraries=places&sensor=true"></script>
<script>
	 var map, markers, infowindow,current_pos,directionsService,directionsDisplay;	 
	 function attachSecretMessage(marker, message) {
		infowindow = new google.maps.InfoWindow(
			{ content: message
			});
		google.maps.event.addListener(marker, 'click', function() {					
			infowindow.setContent(message);
			infowindow.open(map,marker);			
		});    
	}
	 function InitMap() {
		 	var mapOptions = {	
		          center: new google.maps.LatLng(46.856614, 2.3522219),
		          zoom: 6,
		          mapTypeId: google.maps.MapTypeId.ROADMAP
		    }; 
			map = new google.maps.Map(document.getElementById('locations_map'),mapOptions);
			directionsService = new google.maps.DirectionsService();
		    directionsDisplay = new google.maps.DirectionsRenderer();	
			var geocoder = new google.maps.Geocoder();
			var bounds = new google.maps.LatLngBounds();		
			markers = new Array();
			{foreach from=$locs item=loc name=jmsmaplocation}
				var pos = new google.maps.LatLng({$loc.latitude|escape:'html'}, {$loc.longitude|escape:'html'});
				map.setCenter(pos);
				bounds.extend(pos);
				var marker = new google.maps.Marker({
			            map: map,
						code:{$loc.id_loc|escape:'html'},		
			            title: '{$loc.title|escape:'html'}',			            			            
			            position: pos,
			            brief: '<strong>{$loc.title|escape:'html'}</strong><br />{$loc.address|escape:''}'
			    });	         
			    var image = {
						url: '{$base_url|escape:'html'}modules/jmsmaplocation/views/img/point.png',	 
		                 origin: new google.maps.Point(0, 0),
		                 anchor: new google.maps.Point(17, 34),
		                 scaledSize: new google.maps.Size(17, 25)
		    	};
		   		marker.setIcon(image);	
			    marker.setTitle('{$loc.title|escape:'html'}');	     
			    markers.push(marker);
			    attachSecretMessage(marker, marker.brief);
			{/foreach}			
			
		    map.fitBounds(bounds);				
		    var cpos = new google.maps.LatLng(46.856614, 2.3522219) 
		    map.setCenter(cpos);   
	}
	 
    google.maps.event.addDomListener(window, 'load', InitMap);      
</script>
<div id="locations_map" style="width:100%; height:250px;"></div>
{if $maploc_show_dropdown}
<div class="maploc-find-stores {$maploc_dropdown_pos|escape:'html'}">
		<h4>Find our stores</h4>
		<select id="maploc-stores-list" class="form-control">
		<option value="">{l s='Choose Store Location' mod='jmsmaplocation'}</option>
		{foreach from=$locs item=loc name=jmsmaplocation}
		<option value="{$loc.id_loc|escape:'html'}">{$loc.title|escape:'html'}</option>
		{/foreach}	
	</select>
</div>
{/if}
</div>
{if $maploc_show_dropdown}
<script>
function openInfo (id) {
	var mark;
	for (var i=0; i < markers.length; i++) {
		if (markers[i].code == id) {
			mark = markers[i];	
		}
	}
						
	if (infowindow) {
	    infowindow.close();
	}			
			
	map.setCenter(mark.position);
	map.setZoom(13);
	infowindow.setContent(mark.brief);
	infowindow.open(map,mark);	
}

$(document).ready(function(){	
	$('#maploc-stores-list').change(function(e) {
		var loc_id = $(this).val();
		var cpos;
		for (var i=0; i < markers.length; i++) {		
			if (markers[i].code == loc_id) {
				latlng = markers[i].position;									
				map.setCenter(latlng); 
				map.setZoom(20);
				openInfo(loc_id);
			}
		}		
	});
	
});
</script>
{/if}
</section>
<div class="contact_address">
	<h3 class="title">
		<span>{l s='Travel national hotel'}</span>
	</h3>
	<ul>
		<li>
			<i class="fa fa-map-marker"></i>
			{l s='Address:'}&nbsp;<span>{l s='No. 1122 South Large Street, California City, United Stated.'}</span>
		</li>
		<li>
			<i class="fa fa-phone"></i>
			{l s='Phone:'}&nbsp;<span>{l s='+00 1 1312 567 / Email: Support247@travelnational.com'}</span>
		</li>
		<li>
			<i class="fa fa-clock-o"></i>
			{l s='Working:'}&nbsp;<span>8h30 - 20h30, everyday</span>
		</li>
	</ul>
</div>
