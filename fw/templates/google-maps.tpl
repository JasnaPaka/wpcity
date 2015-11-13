<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=KEY_REPLACEMENT&amp;sensor=false"></script>
<script type="text/javascript">
	var map;
	var marker;
	
	var mapOptions = {
          center: new google.maps.LatLng(LAT_REPLACEMENT, LNG_REPLACEMENT),
          zoom: ZOOM_REPLACEMENT,
          mapTypeId: google.maps.MapTypeId.SATELLITE
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
      	
      	var myLatlng = new google.maps.LatLng(LAT_REPLACEMENT, LNG_REPLACEMENT);
      	var marker = new google.maps.Marker({
      		map: map,
		    position: myLatlng
		});
		map.setZoom(15);
		map.panTo(marker.position);
	}
	
	google.maps.event.addDomListener(window, 'load', initialize);
</script>

<div id="map-canvas"></div>