{*
SRDEV
*}
<!-- block seach mobile -->
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<link rel="prefetch stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700,800,600">
<link rel="stylesheet" type="text/css" href="/modules/blocksearchhome/blocksearchcms.css">
<!--<link rel="stylesheet" href="/js/autocomplete-master/dist/teleport-autocomplete.css">
<script src="/js/autocomplete-master/dist/teleport-autocomplete.js"></script>-->
<!-- Block search module home -->
<section class="main">
<div id="search_block_cms" style1="display:none">



<div class="search-form">
		<div>
	        <form style="position:relative" method="post" action="/results.php" id="form" name="form" target="_blank">
	            <div id="city">
                	<input name="ville" id="ville" type="text" placeholder="{l s='Où voulez-vous louer un vélo ?' mod='blocksearchhome'}" autocomplete="off" style="cursor: auto;" value="{$city}" ville="{$city}">
                </div>
				<!--<input type="text" class="my-input" name="field" tabindex="1" autocomplete="off">-->
                <span id="two-inputs">
					<input type="text" size="1" style="cursor: auto; display:none" autocomplete="off" id="distance" name="distance" placeholder="{l s='Distance in KM (ex: 20)' mod='blocksearchhome'}" value="{$distance}">
					<input type="text" placeholder="{l s='Début' mod='blocksearchhome'}" id="date_depart" name="date_depart" readonly>
					<input type="text" placeholder="{l s='Fin' mod='blocksearchhome'}" id="date_fin" name="date_fin" readonly>
				</span>


				<!-- Single button -->
				<div class="btn-group dropup typeloc">
				  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				    <span>{l s='Bike type' mod='blocksearchhome'} </span> <i class="fa fa-sort-asc" aria-hidden="true"></i>
				  </button>
				  <div class="dropdown-menu">
				    <span id="type_loc">
                    	{foreach from=$type_locs item=type_loc name=myLoop}
                       		<input type="checkbox" name="type_loc" id="{$type_loc["id_category"]}" value="{$type_loc["name"]}" {if preg_match("/{$type_loc["id_category"]}/", $l_type_loc) == 1} checked {/if} />{$type_loc["name"]}<br>

						{/foreach}
					</span>
				  </div>
				</div>




                <span id="envoyer">
                    <input type="button" class="submit" value="Rechercher" id="search_loc" name="search" onclick1="form.submit()">
                </span>


{* 				<div id="error">Adresse de location</div> *}

               <input type="hidden" id="adresse" name="adresse">
               <input type="hidden" value="com_autos" name="option">
               <input type="hidden" name="controller" value="autossearch">
               <input type="hidden" name="view" value="autossearch">
               <input type="hidden" name="vehicule" value="auto">
               <input type="hidden" name="latitude" id="latitude" latitude="{$latitude}">
               <input type="hidden" name="longitude" id="longitude" longitude="{$longitude}">
               <input type="hidden" name="template" value="freemobility">
               <input type="hidden" name="resetsearch" value="1">
               <input type="hidden" value="1" name="7a85967e0bd23dc3ab6a7f14a0bcfb10">
               <input type="hidden" id="id_store" value="{$id_store}" name="id_store">

	        </form>
            <div class="results" style="display:none"></div>
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
        {literal}
        <script type="text/javascript">

			$(document).ready(function(){

				//$('.typeloc .dropdown-menu').css("height",{$type_locs|@count}*30)

			   var dd = $('#date_depart').val();
			  var df = $('#date_fin').val();
			  var today = new Date();
			  today.setDate(today.getDate());
			  var dt = today.toLocaleDateString();

			  var tomorrow = new Date();
			  tomorrow.setDate(today.getDate() + 1);
			  var dtomorow = tomorrow.toLocaleDateString();

			  var p_Date=dtomorow;
			  if (dtomorow.match('/')) p_Date=dtomorow.split('/');
			  jour = p_Date[0]; mois = p_Date[1]; annee = p_Date[2];
			  if (mois<10) mois='0'+mois*1;
			  if (jour<10) jour='0'+jour*1;
			  dtomorow = jour + '/' + mois + '/' + annee;

			  if (dd!='')
			  {
				  var p_Date=dd;
				  if (dd.match('/')) p_Date=dd.split('/');
				  jour = p_Date[0]; mois = p_Date[1];	annee = p_Date[2];
				  if (mois<10) mois='0'+mois*1;
				  if (jour<10) jour='0'+jour*1;
				  dd = new Date(annee + '-' + mois + '-' + jour);

				  var p_Date=dt;
				  if (dt.match('/')) p_Date=dt.split('/');
				  jour = p_Date[0]; mois = p_Date[1];	annee = p_Date[2];
				  dt = new Date(annee + '-' + mois + '-' + jour);

				  var p_Date=df;
				  if (df.match('/')) p_Date=df.split('/');
				  jour = p_Date[0]; mois = p_Date[1];	annee = p_Date[2];
				  if (mois<10) mois='0'+mois*1;
				  if (jour<10) jour='0'+jour*1;
				  df = new Date(annee + '-' + mois + '-' + jour);

				  var ddiff = dateDiff(dd,dt);
				  if(ddiff.day>0) $('#date_depart').attr('value',dtomorow);

				  var ddiff = dateDiff(dd,df);
				  if(ddiff.day<0) $('#date_fin').attr('value',$('#date_depart').val());

			  }


				var ua = window.navigator.userAgent;
				var msie = ua.indexOf('MSIE ');
				var trident = ua.indexOf('Trident/');

				if (msie < 0 && trident < 0) {
					//$('#ville').focus();
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

				$("#distance").keydown(function (e) {
					// Touches autorisées: backspace, Suppr, tab, Echap, Entrée
					if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 190]) !== -1 ||
						 //Ctrl+A
						(e.keyCode == 65 && e.ctrlKey === true) ||
						 //Ctrl+C
						(e.keyCode == 67 && e.ctrlKey === true) ||
						 //Ctrl+X
						(e.keyCode == 88 && e.ctrlKey === true) ||
						 // Touches autorisées: home, end, gauche, droite
						(e.keyCode >= 35 && e.keyCode <= 39)) {
							 return;
					}
					//Autorise seulement la saisie des nombres
					if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
						e.preventDefault();
					}
				});

				$('#date_depart').datepicker({
				 	lang:'fr',
				 	timepicker:false,
					format:'d/m/Y',
					closeOnDateSelect:true,
					showAnim: 'clip',
					numberOfMonths: 2,
					dayOfWeekStart:1,
					minDate: 0,
					//maxDate:jQuery('#date_fin').val() ? jQuery('#date_fin').val() : false,
				 	onSelect:function( ct ){
						function dateDiff(date1, date2){
							var diff = {}                           // Initialisation du retour
							var tmp = date2 - date1;

							tmp = Math.floor(tmp/1000);             // Nombre de secondes entre les 2 dates
							diff.sec = tmp % 60;                    // Extraction du nombre de secondes

							tmp = Math.floor((tmp-diff.sec)/60);    // Nombre de minutes (partie entière)
							diff.min = tmp % 60;                    // Extraction du nombre de minutes

							tmp = Math.floor((tmp-diff.min)/60);    // Nombre d'heures (entières)
							diff.hour = tmp % 24;                   // Extraction du nombre d'heures

							tmp = Math.floor((tmp-diff.hour)/24);   // Nombre de jours restants
							diff.day = tmp;

							return diff;
						}
						function addDaysToDate(old_date, delta_days){
						   // Date plus quelques jours
						   var split_date = old_date.split('/');
						   // Les mois vont de 0 a 11 donc on enleve 1, cast avec *1
						   var new_date = new Date(split_date[2], split_date[1]*1 - 1, split_date[0]*1 + delta_days);
						   var new_day = new_date.getDate();
							   new_day = ((new_day < 10) ? '0' : '') + new_day; // ajoute un zéro devant pour la forme
						   var new_month = new_date.getMonth() + 1;
							   new_month = ((new_month < 10) ? '0' : '') + new_month; // ajoute un zéro devant pour la forme
						   var new_year = new_date.getYear();
							   new_year = ((new_year < 200) ? 1900 : 0) + new_year; // necessaire car IE et FF retourne pas la meme chose
						   var new_date_text = new_day + '/' + new_month + '/' + new_year;
						   return new_date_text;
						}

						var date_depart=$('#date_depart').val();
						if (date_depart.match('/')) p_Date=date_depart.split('/');
						jour = p_Date[0]; mois = p_Date[1];	annee = p_Date[2];
						date_depart = new Date(annee + '-' + mois + '-' + jour);

						var date_fin=$('#date_fin').val();
						//var date_fin = $('#date_fin').val()
						if (date_fin.match('/')) p_Date=date_fin.split('/');
						jour = p_Date[0]; mois = p_Date[1];	annee = p_Date[2];
						date_fin = new Date(annee + '-' + mois + '-' + jour);
						diff = dateDiff(date_depart, date_fin);
						var quantite = diff.day;
						if (quantite<1) $('#date_fin').val($('#date_depart').val());
					},
				 	onShow:function( ct )
				 	{
				 		this.setOptions({
							formatDate:'d/m/Y',
							minDate:0,
							//maxDate:jQuery('#date_fin').val() ? jQuery('#date_fin').val() : false
				   		})
				  	}
				});


				$('#date_fin').datepicker({
				 	lang:'fr',
				 	timepicker:false,
					format:'d/m/Y',
					dayOfWeekStart:1,
					showAnim: 'clip',
					numberOfMonths: 2,
					closeOnDateSelect:true,
					minDate:0,
					//minDate:jQuery('#date_depart').val() ? jQuery('#date_depart').val() : false,
				 	onSelect:function( ct ){
						function dateDiff(date1, date2){
							var diff = {}                           // Initialisation du retour
							var tmp = date2 - date1;

							tmp = Math.floor(tmp/1000);             // Nombre de secondes entre les 2 dates
							diff.sec = tmp % 60;                    // Extraction du nombre de secondes

							tmp = Math.floor((tmp-diff.sec)/60);    // Nombre de minutes (partie entière)
							diff.min = tmp % 60;                    // Extraction du nombre de minutes

							tmp = Math.floor((tmp-diff.min)/60);    // Nombre d'heures (entières)
							diff.hour = tmp % 24;                   // Extraction du nombre d'heures

							tmp = Math.floor((tmp-diff.hour)/24);   // Nombre de jours restants
							diff.day = tmp;

							return diff;
						}
						function addDaysToDate(old_date, delta_days){
						   // Date plus plus quelques jours
						   var split_date = old_date.split('/');
						   // Les mois vont de 0 a 11 donc on enleve 1, cast avec *1
						   var new_date = new Date(split_date[2], split_date[1]*1 - 1, split_date[0]*1 + delta_days);
						   var new_day = new_date.getDate();
							   new_day = ((new_day < 10) ? '0' : '') + new_day; // ajoute un zéro devant pour la forme
						   var new_month = new_date.getMonth() + 1;
							   new_month = ((new_month < 10) ? '0' : '') + new_month; // ajoute un zéro devant pour la forme
						   var new_year = new_date.getYear();
							   new_year = ((new_year < 200) ? 1900 : 0) + new_year; // necessaire car IE et FF retourne pas la meme chose
						   var new_date_text = new_day + '/' + new_month + '/' + new_year;
						   return new_date_text;
						}

						var date_depart=$('#date_depart').val();
						if (date_depart.match('/')) p_Date=date_depart.split('/');
						jour = p_Date[0]; mois = p_Date[1];	annee = p_Date[2];
						date_depart = new Date(annee + '-' + mois + '-' + jour);

						var date_fin=$('#date_fin').val();
						//var date_fin = $('#date_fin').val()
						if (date_fin.match('/')) p_Date=date_fin.split('/');
						jour = p_Date[0]; mois = p_Date[1];	annee = p_Date[2];
						date_fin = new Date(annee + '-' + mois + '-' + jour);
						diff = dateDiff(date_depart, date_fin);
						var quantite = diff.day;
						if (quantite<0) $('#date_fin').val(addDaysToDate($('#date_depart').val(),0));
					},
				 	onShow:function( ct ){
				 		this.setOptions({
							formatDate:'d/m/Y',
							minDate:jQuery('#date_depart').val() ? jQuery('#date_depart').val() : false
						})
					}
				});

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
			});

	    </script>
		<script>
          var $results = document.querySelector('.results');
          var appendToResult = $results.insertAdjacentHTML.bind($results, 'afterend');
    	  /*new TeleportAutocomplete({ el: '#ville', limit: 2, embeds: 'city' }).on('change', function(value)
 		  {
			  	//$('#ville').val(value.name);
				if(!!value)
				{
					$('#ville').attr("ville",value.name);
					$('#adresse').val(value.title);
					$('#latitude').attr("latitude",value.latitude);
					$('#longitude').attr("longitude",value.longitude);
					//appendToResult('<pre>' + JSON.stringify(value, null, 2) + '</pre>');
					setTimeout(function(){$('#ville').val($('#ville').attr("ville"));}, 50);
					//,success : function(value) {}
				}
          });*/

		  $("form").click(function(){ $("#results_search").show(1000); });
		  $(document).ready(function(e) {


			  $("#search_loc").click(function(){
				  	// verif si la ville est bien saisie avec les coordonnées affectées
				    var date_depart = $('#date_depart').val();
					var message = '';
				   	// calcule le nombre de jour entre les deux dates
					if (date_depart!='' && date_fin!='' )
					{
						var p_Date=date_depart;
						if (date_depart.match('/')) p_Date=date_depart.split('/');
						jour = p_Date[0]; mois = p_Date[1];	annee = p_Date[2];
						date_depart = new Date(annee + '-' + mois + '-' + jour);

						var p_Date=date_fin;
						var date_fin = $('#date_fin').val()
						if (date_fin.match('/')) p_Date=date_fin.split('/');
						jour = p_Date[0]; mois = p_Date[1];	annee = p_Date[2];
						date_fin = new Date(annee + '-' + mois + '-' + jour);
						diff = dateDiff(date_depart, date_fin);
						var quantite = diff.day+1;
					}
					else message="Veuillez bien sélectionner la période ! ";

					if (quantite<1) quantite=1;
					if (!isFinite($('#longitude').attr("longitude"))) message="Veuillez bien sélectionner la ville dans la liste !";
					if (!isFinite(quantite)) message="Veuillez bien sélectionner la période ! ";
					if (quantite<1) message="Veuillez bien sélectionner la période !" ;

					if (message!="") {
						$.fancybox({
							width: 600,
							height: 310,
							autoSize : true,
							maxWidth : '100%',
							content: '<p class="fancybox-error">'+message+'</p>'
						});
						return false;
					}
					var l_type_loc = '';
					$('[name=type_loc]:checked').each(function(){
						l_type_loc += $(this).attr('id') + ',';
					});
				  	// renvoi vers la page
					window.location.href = '/module/marketplace/list?city='+$('#ville').attr("ville")+
					'&latitude='+$('#latitude').attr('latitude')+'&longitude='+$('#longitude').attr('longitude')+
					'&date_depart='+$('#date_depart').val()+
					'&date_fin='+$('#date_fin').val()+
					'&quantite='+quantite+
					'&l_type_loc='+l_type_loc+
					'&id_store='+$('#id_store').val()+
					'#vs';
					/*
				  	url = '/results_search.php';

					$.ajax({
						url: url,
						method: 'POST',
						data: {
							ville: $('#ville').val(),
							date_depart: $('#date_depart').val(),
							date_fin: $('#date_fin').val()
						},
						success: function(result)
						{
							$("#results_search").show(1000);
							$("#results_search").html(result);
						}
					});
					*/
				function dateDiff(date1, date2){
					var diff = {}                           // Initialisation du retour
					var tmp = date2 - date1;

					tmp = Math.floor(tmp/1000);             // Nombre de secondes entre les 2 dates
					diff.sec = tmp % 60;                    // Extraction du nombre de secondes

					tmp = Math.floor((tmp-diff.sec)/60);    // Nombre de minutes (partie entière)
					diff.min = tmp % 60;                    // Extraction du nombre de minutes

					tmp = Math.floor((tmp-diff.min)/60);    // Nombre d'heures (entières)
					diff.hour = tmp % 24;                   // Extraction du nombre d'heures

					tmp = Math.floor((tmp-diff.hour)/24);   // Nombre de jours restants
					diff.day = tmp;

					return diff;
				}
				function addDaysToDate(old_date, delta_days)
				{
				   // Date plus plus quelques jours
				   var split_date = old_date.split('/');
				   // Les mois vont de 0 a 11 donc on enleve 1, cast avec *1
				   var new_date = new Date(split_date[2], split_date[1]*1 - 1, split_date[0]*1 + delta_days);
				   var new_day = new_date.getDate();
					   new_day = ((new_day < 10) ? '0' : '') + new_day; // ajoute un zéro devant pour la forme
				   var new_month = new_date.getMonth() + 1;
					   new_month = ((new_month < 10) ? '0' : '') + new_month; // ajoute un zéro devant pour la forme
				   var new_year = new_date.getYear();
					   new_year = ((new_year < 200) ? 1900 : 0) + new_year; // necessaire car IE et FF retourne pas la meme chose
				   var new_date_text = new_day + '/' + new_month + '/' + new_year;
				   return new_date_text;
				}
			  });
		  });
        </script>
        {/literal}

    </div>



</div>
</section>

{literal}
<script>
$(document).ready(function(e) {

	var searchBox = new google.maps.places.SearchBox( (document.getElementById('ville')) );

	autocomplete = new google.maps.places.Autocomplete( (document.getElementById('ville')),
		{types: ['(cities)'], componentRestrictions: {country: 'fr'}});
/**/

	autocomplete.addListener('place_changed', function(){

		var place = autocomplete.getPlace();
		if (place){
			var lat = place.geometry.location.lat();
			var lng = place.geometry.location.lng();
			$('#ville').attr("ville",place.name);
			$('#latitude').attr("latitude",lat);
			$('#longitude').attr("longitude",lng);
			setTimeout(function(){$('#ville').val($('#ville').attr("ville"));}, 50);
		}
	});

	searchBox.addListener('places_changed', function() {

		var places = searchBox.getPlaces();
		if (places.length == 0) {
			return;
		}


		places.forEach(function(place)
		{
			if (place)
			{
				formatted_address = place.formatted_address;
				//if (place.types[0]=='locality' && formatted_address.match('France') )
				if (!!formatted_address.match('France'))
				{
					var lat = place.geometry.location.lat();
					var lng = place.geometry.location.lng();
					$('#ville').attr("ville",place.name);
					$('#latitude').attr("latitude",lat);
					$('#longitude').attr("longitude",lng);
					setTimeout(function(){$('#ville').val($('#ville').attr("ville"));}, 50);
				}
			}
		});
	});

	$('#ville').keydown(function(){
		pl = $('body > .pac-container')[0];
		$(pl).css('visibility', 'hidden');
	});
	$('#ville').mouseover(function(){
		pl = $('body > .pac-container')[0];
		$(pl).css('visibility', 'hidden');
	});

});

function geolocate() {
if (navigator.geolocation) {
navigator.geolocation.getCurrentPosition(function(position) {
var geolocation = {
lat: position.coords.latitude,
lng: position.coords.longitude
};
var circle = new google.maps.Circle({
center: geolocation,
radius: position.coords.accuracy
});
autocomplete.setBounds(circle.getBounds());
});
}
}

</script>
{/literal}
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA2pe_F5IYnsW2eAWBzmK3rRrUO_UCRBik&signed_in=true&libraries=places&callback1=initAutocomplete"
        async defer></script>

{include file="$tpl_dir./../../modules/blocksearchhome/blocksearch-instantsearch.tpl"}
<!-- /Block search module TOP -->
