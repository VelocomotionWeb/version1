

<input id="autocomplete" placeholder="Enter your address" onFocus="geolocate()" type="text"></input>

<script>
function initAutocomplete() {
  // Create the autocomplete object, restricting the search to geographical
  autocomplete = new google.maps.places.Autocomplete( 
	(document.getElementById('autocomplete')),
	{types: ['geocode'], componentRestrictions: {country: 'fr'}}
  );
  // When the user selects an address from the dropdown, populate the address
  autocomplete.addListener('place_changed', locator);
}
function locator() {
	// Get the place details from the autocomplete object.
	var place = autocomplete.getPlace();
	var lat = place.geometry.location.lat();
	var lng = place.geometry.location.lng();

}
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
<script type="text/javascript" src="js/jquery/jquery-1.11.0.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDAAx0cPh2MP4DbPzmVs_V2HslsSAoUSaQ&signed_in=true&libraries=places&callback=initAutocomplete"
        async defer></script>
