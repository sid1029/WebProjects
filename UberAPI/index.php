<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <link rel="shortcut icon" href="favicon.ico" />

    <title>LocationBase</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet" />

    <style type="text/css">
      .wrapper {
            min-height: 100%;
            height: auto !important;
            height: 100%;
            margin: 35 auto -63px;
        }
    </style>

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="js/html5shiv.js"></script>
      <script src="js/respond.min.js"></script>
    <![endif]-->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script type="text/javascript"
      src="https://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyAjZUkShtwCRI93tehDC3cEGNDyK45U0qc&sensor=false">
    </script>
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
    <!-- Begin page content -->
    <div class="container">
      <h1>My Uber Locations</h1>
      
      <!-- Map Canvas -->
      <div id="map-canvas" style="height: 40%;"></div>

      <div class="wrapper">
        <!-- Add new location view -->
        <div id="add-loc" class="panel panel-default">
          <div class="panel-heading">Place a marker on the map or enter an address below to add a favorite location.</div>
          <div class="panel-body">
            <form class="form-inline" id="add-loc-form" style="margin-bottom:0em" role="form">
              <div class="form-group" style="width:40%">
                <label class="sr-only" for="addressinput">Address</label>
                <input name="loc-address" type="text" class="form-control noEnterSubmit" id="address" placeholder="Enter address">
              </div>
              <div class="form-group" style="width:40%">
                <label class="sr-only" for="addressinput">Name</label>
                <input name="loc-name" type="text" class="form-control" id="place-name" placeholder="Name">
              </div>
              <div class="form-group" style="width:19%">
                <button type="submit" class="form-control btn btn-primary" id="add-location" class="btn btn-default">
                  <span class="glyphicon glyphicon-plus"></span> Add Location
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Location list view -->
        <div id="loc-content"></div>
      </div>

    </div>

    <!-- Location list view template -->
    <script type="text/template" id="location-template">
      <% if (!locations) { %>
        <h1 class='text-center'>You have no locations saved yet !</h1>
      <% } else { %>
        <table class="table table-striped table-hover">
          <thead>
              <tr>
                <th class='text-center'>Name</th>
                <th class='text-center'>Lat</th>
                <th class='text-center'>Lng</th>
                <th class='text-center'>Address</th>
              </tr>
            </thead>
          <tbody>
          <% _.each(locations, function(location) { %>
              <tr>
                <td class='text-center'><%= htmlEncode(location.get('name')) %></td>
                <td class='text-center'><%= htmlEncode(location.get('lat')) %></td>
                <td class='text-center'><%= htmlEncode(location.get('lng')) %></td>
                <td class='text-center'><%= htmlEncode(location.get('address')) %></td>
                <td class='text-center'><button id="#edit-button" type="button" class='btn btn-primary'><span class="glyphicon glyphicon-edit"></span> Edit</button></td>
                <td class='text-center'><button id="#show-button" type="button" class='btn btn-primary'><span class="glyphicon glyphicon-eye-open"></span> Show</button></td>
              </tr>
            <% }); %>
          </tbody>
        </table>
      <% } %>
    </script>

    <script src="js/underscore.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/backbone.js"></script>
    <script type="text/javascript">
      // Helpers
      function htmlEncode(value){
        return $('<div/>').text(value).html();
      }

      $.fn.serializeObject = function() {
        var o = {};
        var a = this.serializeArray();
        $.each(a, function() {
            if (o[this.name] !== undefined) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
      };

      $.ajaxPrefilter( function( options, originalOptions, jqXHR ) {
        options.url = "http://localhost/uberapi" + options.url;
      });

      // A location
      var UberLocation = Backbone.Model.extend({
        urlRoot: '/locations',
        defaults: { name: '', lat: 0, lng: 0, address: '' }
      });

      // Collection of locations
      var LocationList = Backbone.Collection.extend({
        model: UberLocation,
        url: '/locations',
        initialize: function () {
          this.on('add', function(model) {
            //pinOnMap({ lat: model.get('lat'), lng: model.get('lat') });
          });
        }
      });
      var locationList = new LocationList();

      // Add location view. Form that adds a new location.
      var AddLocView = Backbone.View.extend({
        el: '#add-loc',
        events: {
          'submit #add-loc-form' : 'saveLocation'
        },
        saveLocation: function (ev) {
          ev.preventDefault();
          if (ev.keyCode == 13) {
            return false;
          }
          console.log(ev);
          ev.preventDefault();
          pinOnMap($("#address").val());
          
          // Serialize form for submission to server.
          //var newLoc = $(ev.currentTarget).serializeObject();
          // A Google places API autocomplete request gives us all the details.
          // These are stored in the global object gMyNewLoc which is of type UberLocation.
          gMyNewLoc.save(gMyNewLoc.attributes, {
            success: function () {
              console.log(gMyNewLoc);
              console.log("Saved successfully");
              locationsView.render();
            }
          });
          return false;
        }
      });
      var addLocView = new AddLocView();

      // This view lists all the saved locations.
      var LocationsView = Backbone.View.extend({
        el: '#loc-content',
        render: function () {
          var self = this;
          locationList.fetch({
            success: function (locationList) {
              var template;
              if (locationList.length == 0)
                template = _.template($('#location-template').html(), {locations: null});
              else
                template = _.template($('#location-template').html(), {locations: locationList.models});
              self.$el.html(template);
            }
          });
        }
      });
      var locationsView = new LocationsView();

      var UberRouter = Backbone.Router.extend({
        routes: {
          '': 'home',
          'add/': 'add',
          'edit/:id': 'edit',
          'delete/:id': 'delete'
        },
        
        // Called when page loads.
        home: function () {
          // Initialize locationsView to display list of locations to user.
          locationsView.render();

          // No need to render the add locations view.
          addLocView.render();
        },

        add: function () {
          console.log("Trying to add a location");
        },

        edit: function (id) {
          console.log("Trying to edit a location");
        },

        delete: function (id) {
          console.log("Trying to delete a location");
        }

      });

      var uberRouter = new UberRouter();
      Backbone.history.start();

      $('document').ready(function () {
        // To prevent form submission when enter is pressed on the places auto complete field.
        $('.noEnterSubmit').keypress(function(e){
          if ( e.which == 13 )
            {
              e.preventDefault();
              return false;
            }
        });
      });
    </script>

    <script type="text/javascript">
      // Initialize gmaps.
      var geocoder, autocomplete, infowindow, map, gInput, gMyNewLoc;

      function initialize()
      {
        geocoder = new google.maps.Geocoder();
        var mapOptions = {
          center: new google.maps.LatLng(40.71, -74.00),
          zoom: 8,
          mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);
        gInput = document.getElementById("address");

        autocomplete = new google.maps.places.Autocomplete(gInput);
        infowindow = new google.maps.InfoWindow();
        autocomplete.bindTo('bounds', map);

        google.maps.event.addListener(autocomplete, 'place_changed', placeChanged);
      }
      google.maps.event.addDomListener(window, 'load', initialize);

      // Returns an UberLocation object of the place typed in (auto completed) or the place where the marker got dropped on the map. 
      function placeChanged () {
        infowindow.close();

        var marker = new google.maps.Marker({map: map});
        marker.setVisible(false);
        //gInput.className = '';
        var place = autocomplete.getPlace();
        
        $('#place-name').val(place.name);

        gMyNewLoc = new UberLocation({
          name: place.name,
          lat: place.geometry.location.lb,
          lng: place.geometry.location.mb,
          address: place.formatted_address
        });

        if (!place.geometry) {
          // Inform the user that the place was not found and return.
          //gInput.className = 'notfound';
          return;
        }

        // If the place has a geometry, then present it on a map.
        if (place.geometry.viewport) {
          map.fitBounds(place.geometry.viewport);
        } else {
          map.setCenter(place.geometry.location);
          map.setZoom(17);  // Why 17? Because it looks good.
        }

        marker.setIcon(/** @type {google.maps.Icon} */({
          url: place.icon,
          size: new google.maps.Size(71, 71),
          origin: new google.maps.Point(0, 0),
          anchor: new google.maps.Point(17, 34),
          scaledSize: new google.maps.Size(35, 35)
        }));
        marker.setPosition(place.geometry.location);
        marker.setVisible(true);

        var address = '';
        if (place.address_components) {
          address = [
            (place.address_components[0] && place.address_components[0].short_name || ''),
            (place.address_components[1] && place.address_components[1].short_name || ''),
            (place.address_components[2] && place.address_components[2].short_name || '')
          ].join(' ');
        }

        infowindow.setContent('<div><strong>' + place.name + '</strong><br>' + address);
        infowindow.open(map, marker);

        return gMyNewLoc;
      }
      </script>
  </body>
</html>
