<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=KEY_REPLACEMENT&amp;sensor=false"></script>
<script type="text/javascript">
	var map;
        var markers = MARKERS_REPLACEMENT;
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
        
        var bounds = new google.maps.LatLngBounds();
      	
      	var myLatlng = new google.maps.LatLng(LAT_POI_REPLACEMENT, LNG_POI_REPLACEMENT);
      	var marker = new google.maps.Marker({
      		map: map,
		    position: myLatlng
        });        
        bounds.extend(marker.position);
        
        for (var i = 0; i < markers.length; i++) {
            var myLatlng = new google.maps.LatLng(markers[i][0], markers[i][1]);
            var marker = new google.maps.Marker({
                    map: map,
                    position: myLatlng,
                    title: markers[i][2],
                    icon: 'http://maps.google.com/mapfiles/ms/icons/purple-dot.png'
            });
            bounds.extend(marker.position);
        }
        
        google.maps.event.addListenerOnce(map, 'bounds_changed', function(event) {
            if (this.getZoom() > 15) {
                this.setZoom(15);
            }
        });

        google.maps.event.addListener(map, 'zoom_changed', function() {
            zoomChangeBoundsListener =
                google.maps.event.addListener(map, 'bounds_changed', function(event) {
                    if (this.getZoom() > 17 && this.initialZoom == true) {
                        // Change max/min zoom here
                        this.setZoom(17);
                        this.initialZoom = false;
                    }
                    google.maps.event.removeListener(zoomChangeBoundsListener);
                });
        });

        map.initialZoom = true;
        map.fitBounds(bounds);
    }
	
        
        
	google.maps.event.addDomListener(window, 'load', initialize);
</script>

<div id="map-canvas"></div>