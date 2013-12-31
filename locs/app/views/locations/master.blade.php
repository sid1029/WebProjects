<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>LocationBase</title>
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css" />
	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
	<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/underscore.js/1.5.2/underscore-min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/backbone.js/1.1.0/backbone-min.js"></script>
	<!--<script src="https://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyAjZUkShtwCRI93tehDC3cEGNDyK45U0qc&sensor=false"></script>-->
	<script type="text/javascript">
	    function pinOnMap(loc)
	    {
	      if (loc.lat && loc.lng)
	      {
	        // Just use the lat, lng to place marker.
	        var marker = new google.maps.Marker({
	                map: map,
	                position: new google.maps.LatLng(loc.lat, loc.lng, false)
	            });
	        timedAlert("new location marked on map", 1000);
	      }
	      else
	      {
	        // A reverse geocode is necessary
	        geocoder.geocode( {'address': loc}, function(results, status) {
	          if (status == google.maps.GeocoderStatus.OK) {
	            map.setCenter(results[0].geometry.location);
	            var marker = new google.maps.Marker({
	                map: map,
	                position: results[0].geometry.location
	            });
	          } else {
	            timedAlert("C", 1500);
	          }
	        });
	      }
	    }


	    function timedAlert(message, timeout)
	    {
	      $('#gMapsAlerts').html(message).addClass('in');
	      setTimeout(function () { $('#gMapsAlerts').removeClass('in'); }, timeout);
	    }
    </script>
</head>
<body>
<div class="container">
	<nav class="navbar navbar-inverse">
		<div class="navbar-header">
			<a class="navbar-brand" href="{{ URL::to('locations') }}">Location Alert</a>
		</div>
		<ul class="nav navbar-nav">
			<li><a href="{{ URL::to('locations') }}">View All Locations</a></li>
			<li><a href="{{ URL::to('locations/create') }}">Create a Location</a>
		</ul>
	</nav>
	@yield('content')
</div>
</body>
</html>