<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=KEY_REPLACEMENT&amp;sensor=false"></script>
<script type="text/javascript">
	var map;
	var marker;
	
	var lat = LAT_REPLACEMENT;
	var lng = LNG_REPLACEMENT;
	
	var mapOptions = {
          center: new google.maps.LatLng(49.748398, 13.377652),
          zoom: 13
    };
    
    function placeMarker(location) {
	  if ( marker ) {
	    marker.setPosition(location);
	  } else {
	    marker = new google.maps.Marker({
	      position: location,
	      map: map
	    });
	  }
	}

	function initialize() {
		map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);
		
		// Skryje výchozí POI
		var styles = [ { featureType: "poi", stylers: [ { visibility: "off" } ] } ];
      	map.setOptions({styles: styles});
      	
      	// Zakáže "ptačí pohled"
      	map.setTilt(0);
		
		google.maps.event.addListener(map, 'click', function(event) {
			$('#latitude').val(event.latLng.lat());
			$('#longitude').val(event.latLng.lng());
			placeMarker(event.latLng);
		});
		
		
		if (lat != 0 && lng != 0) {
			placeMarker(new google.maps.LatLng(lat,lng));
		}
	}
	
	google.maps.event.addDomListener(window, 'load', initialize);
</script>