<!-- block seach mobile -->
<script type="text/javascript" src="js/jquery/jquery-1.11.0.min.js"></script>
<!--
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<link rel="prefetch stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700,800,600">-->
<link rel="stylesheet" href="/js/autocomplete-master/dist/teleport-autocomplete.css">
<script src="/js/autocomplete-master/dist/teleport-autocomplete_TEST.js"></script>

<section class="main">
<div id="search_block_home" >



<div class="search-form">
		<div>
	        <h1>Louez vos vélos en ligne</h1>
			<p class="slogan">Plus de choix, disponibilité garantie, plus pratique </p>
	        <form style="position:relative" method="post" action="/results_search.php" id="form" name="form" target="_blank">
	            <div id="city">
                	<input type="text" placeholder="Localisation" name="ville" id="ville" autocomplete="off" style="cursor: auto;">
                </div>
				<!--<input type="text" class="my-input" name="field" tabindex="1" autocomplete="off">-->
                <span id="two-inputs">
					<input type="text" size="1" style="cursor: auto;" autocomplete="off" id="distance" name="distance" placeholder="Distance en KM (ex : 20)">
					<input type="text" placeholder="Date début" id="date_depart" name="date_depart" value="">
					<input type="text" placeholder="Date fin" id="date_fin" name="date_fin">
				</span>
				
				<!-- Single button -->
				<div class="btn-group dropup">
				  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				    Type de vélo <i class="fa fa-sort-asc" aria-hidden="true"></i>
				  </button>
				  <div class="dropdown-menu">
				    <span id="type_loc">
                    	{foreach from=$type_locs item=type_loc name=myLoop}
                       		<input type="checkbox" name="type_loc" id="{$type_loc["id_attribute"]}" value="{$type_loc["name"]}" />{$type_loc["name"]}<br>
						{/foreach}
					</span>
				  </div>
				</div>
				
				
                
                
                <span id="envoyer">
                    <input type="button" class="submit" value="Trouver" id="search_loc" name="search" onclick1="form.submit()">
                </span>
                
                
{* 				<div id="error">Adresse de location</div> *}	
                			   
               <input type="hidden" id="adresse" name="adresse">
               <input type="hidden" value="com_autos" name="option">
               <input type="hidden" name="controller" value="autossearch">
               <input type="hidden" name="view" value="autossearch">
               <input type="hidden" name="vehicule" value="auto">
               <input type="hidden" name="latitude" id="latitude" value="">
               <input type="hidden" name="longitude" id="longitude" value="">
               <input type="hidden" name="template" value="freemobility">
               <input type="hidden" name="resetsearch" value="1">
               <input type="hidden" value="1" name="7a85967e0bd23dc3ab6a7f14a0bcfb10">				                 
	        </form>
            <div class="results" style1="display:none"></div>
            <div id="results_search" style="display:none"></div>
        </div>
        <style>
			.pac-container .pac-item:first-child{
				background-color: #CFDEF9;
			}		
			.pac-container .pac-item:first-child .pac-icon-marker {
				background-position: -18px -161px;
			}			
		</style>
        <script type="text/javascript">
		
			$(document).ready(function(){
				/*$(function(){
					$('#date_depart, #date_fin').datepicker({
						dateFormat: "dd-mm-yy",
						duration: 'normal',
						showAnim: 'clip',
						numberOfMonths: 2,
					});
				});*/

				var ua = window.navigator.userAgent;
				var msie = ua.indexOf('MSIE ');
				var trident = ua.indexOf('Trident/');

				if (msie < 0 && trident < 0) {
					$('#ville').focus();
				}
			
				var nbclick = 0;
				
				$('#form .submit').on('click',function(){
					if( ($('#ville').val() != "") && nbclick>1){
						nbclick = 3;
						$('#form').submit();
					}
				});
				
				$('#form').on('submit',function(){
					
					if( ($('#ville').val() == "") ){
						$('#error').slideDown(200).delay(3000).slideUp(200);
						return false;
					}
					nbclick++;	
					if(nbclick>2){
						return true;
					}
					return false;
				});
				
				/*
				$("body").live("keypress", function(e) {
					var latitude  = autos("#latitude");
					var longitude = autos("#longitude");
				
					if(e.keyCode == 13){
						nbclick++;	
					}
					if((e.keyCode == 13 || e.keyCode == 9) && $("#ville").val()!='') {											
						var $firstResult = $('.pac-item:first').children();
						var placeName = $firstResult[1].textContent;
						var placeAddress = $firstResult[2].textContent;
						$("#ville").blur();
						$("#ville").val(placeName + ", " + placeAddress);

						var geocoder = new google.maps.Geocoder();
						geocoder.geocode({"address":placeName + ", " + placeAddress }, function(results, status) {
							if (status == google.maps.GeocoderStatus.OK) {
								latitude.val(results[0].geometry.location.lat());
								longitude.val(results[0].geometry.location.lng());	
								$('#form .submit').focus();
							}
						});
					}
				});				
				*/
				$('#date_depart').datepicker({
				 	lang:'fr',
				 	timepicker:false,
					format:'d/m/Y',
					closeOnDateSelect:true,
					showAnim: 'clip',
					numberOfMonths: 1,
					dayOfWeekStart:1,
				 	onShow:function( ct )
				 	{
				 		this.setOptions({
							formatDate:'d/m/Y',
							minDate:1,
							maxDate:jQuery('#date_fin').val() ? jQuery('#date_fin').val() : false
				   		})
				  	}
				});

				$('#date_fin').datepicker({
				 	lang:'fr',
				 	timepicker:false,
					format:'d/m/Y',
					dayOfWeekStart:1,
					showAnim: 'clip',
					numberOfMonths: 1,
					closeOnDateSelect:true,
				 	onShow:function( ct ){
				 		this.setOptions({
							formatDate:'d/m/Y',
							minDate:jQuery('#date_depart').val() ? jQuery('#date_depart').val() : false
						})
					}
				});			
				/*
				$('.pac-container .pac-item').live({
					mouseenter: function() {		
						$('.pac-container .pac-item:first-child').css({'background-color': '#fff'});
						$('.pac-container .pac-item:first-child .pac-icon-marker').css({'background-position': '-1px -161px'});
						$(this).css({'background-color': '#CFDEF9'});
						$(this).find('.pac-icon-marker').css({'background-position': '-18px -161px'});
					}, 
					mouseleave: function() {
						$(this).css({'background-color': '#fff'});
						$(this).find('.pac-icon-marker').css({'background-position': '-1px -161px'});
						//$( this ).removeClass( "pac-item-selected" );
					}
				});
				*/
			});
				
	    </script>
		<script>
          var $results = document.querySelector('.results');
          var appendToResult = $results.insertAdjacentHTML.bind($results, 'afterend');
    	  new TeleportAutocomplete({ el: '#ville', limit: 2, embeds: 'city'}).on('change', function(value)
 		  {
			  	//$('#ville').val(value.name);
				$('#ville').attr("ville",value.name);
				$('#adresse').val(value.title);
				$('#latitude').attr("latitude",value.latitude);
				$('#longitude').attr("longitude",value.longitude);
            	appendToResult('<pre>' + JSON.stringify(value, null, 2) + '</pre>');
				//setTimeout(function(){$('#ville').val($('#ville').attr("ville"));}, 50);
				//,success : function(value) {}
				;
          });
         
		  /*
		  TeleportAutocomplete.init('#ville', 2, true, 'city	').on('change', function(value) 
		  {
			  	//$('#ville').val(value.name);
				$('#ville').attr("ville",value.name);
				$('#adresse').val(value.title);
            	//appendToResult('<pre>' + JSON.stringify(value, null, 2) + '</pre>');
				//setTimeout(function(){$('#ville').val($('#ville').attr("ville"));}, 50);
				//,success : function(value) {}
				;
          });
		  */
		  $("form").click(function(){ $("#results_search").show(1000); });
		  $(document).ready(function(e) {
			  $("#search_loc").click(function(){
				  	// renvoi vers la page
					window.location.href = '/module/mpstorelocator/allsellerstores?city='+$('#ville').attr("ville")+
					'&latitude='+$('#latitude').attr('latitude')+'&longitude='+$('#longitude').attr('longitude')+
					'&distance='+$('#distance').val();
					
				  	/*url = '/results_search.php';
					url2 = '/modules/mpstorelocator/controllers/front/allsellerstores.php';
					$.ajax({
						url: url2,
						method: 'POST',
						data: {
							city : $('#ville').val(),
							latitude : $('#latitude').attr('latitude'),
							longitude : $('#longitude').attr('longitude'),
							distance : $('#distance').val()
							//date_depart: $('#date_depart').val(),
							//date_fin: $('#date_fin').val() 
						},
						success: function(result)
						{
							window.location.href = '/module/mpstorelocator/allsellerstores';
							//$("#results_search").show(1000);
							//$("#results_search").html(result);
						}
					});
					*/
			  });
			  
		  });
        </script>
        
    </div>	
    

	
</div>
</section>
<!-- /Block search module TOP -->
