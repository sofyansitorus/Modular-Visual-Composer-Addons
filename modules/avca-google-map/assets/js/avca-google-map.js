/**
 * This jQuery plugin was inspired and based on jQuery-Google-Map by Thibault Henry (https://github.com/Tilotiti/jQuery-Google-Map).
 * @name Anvanced Google Map jQuery
 * @author Sofyan Sitorus - https://github.com/sofyansitorus
 * @version 0.1
 * @date Mar 7, 2015
 * @category jQuery plugin
 * @license CCAttribution-ShareAlike 2.5 Brazil - http://creativecommons.org/licenses/by-sa/2.5/br/deed.en_US
 */

(function($) {

    $.fn.AVCA_GoogleMap = function(settings) {
        var params = $.extend({
            coords: [48.895651, 2.290569],
            map_type: "ROADMAP",
            debug: false,
            map_style: "",
            zoom: 10,
            zoomControl: false,
            scrollwheel: false,
            disableDoubleClickZoom: true,
            streetViewControl: false,
            mapTypeControl: false,
            panControl: false,
            draggable: false
        }, settings);

        params.center = new google.maps.LatLng(params.coords[0], params.coords[1]);

        this.each(function() {

            var map = new google.maps.Map(this, params);

            switch (params.map_type) {
                case 'SATELLITE':
                    map.setMapTypeId(google.maps.MapTypeId.SATELLITE);
                    break;
                case 'HYBRID':
                    map.setMapTypeId(google.maps.MapTypeId.HYBRID);
                    break;
                case 'TERRAIN':
                    map.setMapTypeId(google.maps.MapTypeId.TERRAIN);
                    break;
                default:
                    map.setMapTypeId(google.maps.MapTypeId.ROADMAP);
                    if (params.map_style) {
                        var styledMap = new google.maps.StyledMapType(params.map_style, {
                            name: "Styled Map"
                        });
                        map.mapTypes.set('map_style', styledMap);
                        map.setMapTypeId('map_style');
                    }
                    break;
            }

            $(this).data('googleMap', map);
            $(this).data('googleDebug', params.debug);
            $(this).data('googleMarker', new Array());
            $(this).data('googleBound', new google.maps.LatLngBounds());
        });

        return this;
    }

    $.fn.AVCA_AddMapMarker = function(settings) {
        var params = $.extend({
            coords: false,
            id: false,
            icon: false,
            draggable: false,
            info_window_type: "default",
            info_window_heading: "",
            info_window_address: "",
            url: false,
            animation: false,
            success: function() {}
        }, settings);

        this.each(function() {
            $this = $(this);

            if (!$this.data('googleMap')) {
                if ($this.data('googleDebug'))
                    console.error("jQuery googleMap : Unable to add a marker where there is no map !");
                return false;
            }

            if (!params.coords) {
                if ($this.data('googleDebug'))
                    console.error("jQuery googleMap : Unable to add a marker if you don't tell us where !");
                return false;
            }


            $this.data('googleBound').extend(new google.maps.LatLng(params.coords[0], params.coords[1]));

            if (params.icon) {
                var marker = new google.maps.Marker({
                    map: $this.data('googleMap'),
                    position: new google.maps.LatLng(params.coords[0], params.coords[1]),
                    title: params.title,
                    icon: params.icon
                });
            } else {
                var marker = new google.maps.Marker({
                    map: $this.data('googleMap'),
                    position: new google.maps.LatLng(params.coords[0], params.coords[1]),
                    title: params.title
                });
            }
            
            switch (params.animation) {
                case 'BOUNCE':
                    marker.setAnimation(google.maps.Animation.BOUNCE);
                    break;
                case 'DROP':
                    marker.setAnimation(google.maps.Animation.DROP);
                    break;
                default:
                    // Do nothing
                    break;
            }

            if (params.info_window_heading != "" || params.info_window_address != "") {
                var info_window_content;
                var pixelOffset = 0;
                if(params.info_window_type == 'custom' && typeof InfoBox == 'function'){
                    info_window_content = "<div class=\"info-window-custom\"'>";
                    if (params.info_window_heading != "") {
                        info_window_content = info_window_content + "<div class=\"info-window-heading\"'>" + params.info_window_heading + "</div>";
                        pixelOffset = pixelOffset - 60;
                    }
                    if (params.info_window_address != "") {
                        info_window_content = info_window_content + "<div class=\"info-window-address\"'>" + params.info_window_address + "</div>";
                        pixelOffset = pixelOffset - 100;
                    }
                    info_window_content = info_window_content + "</div>";
                    var InfoBoxOptions = {
                        alignBottom: true,
                        pixelOffset: new google.maps.Size(-36, pixelOffset),
                        disableAutoPan: true,
                        enableEventPropagation: true,
                        closeBoxURL: '',
                        infoBoxClearance: new google.maps.Size(1, 1),
                        maxWidth: 0,
                        pane: "floatPane",
                        zIndex: 9999,
                        content: info_window_content
                    };
                    var ib = new InfoBox(InfoBoxOptions);
                    marker.infobox = false;
                    google.maps.event.addListener(marker, 'click', function() {
                        if(marker.infobox){
                            ib.close();
                            marker.infobox = false;
                        }else{
                            ib.open($this.data('googleMap'), marker);
                            marker.infobox = true;
                        }
                    });
                }else{
                    info_window_content = "<div class=\"info-window-default\"'>";
                    if (params.info_window_heading != "") {
                        info_window_content = info_window_content + "<div class=\"info-window-heading\"'>" + params.info_window_heading + "</div>";
                    }
                    if (params.info_window_address != "") {
                        info_window_content = info_window_content + "<div class=\"info-window-address\"'>" + params.info_window_address + "</div>";
                    }
                    info_window_content = info_window_content + "</div>";
                    var infowindow = new google.maps.InfoWindow({
                        content: info_window_content
                    });

                    var map = $this.data('googleMap');

                    google.maps.event.addListener(marker, 'click', function() {
                        infowindow.open(map, marker);
                    });
                }
            }else if (params.url) {
                google.maps.event.addListener(marker, 'click', function() {
                    document.location = params.url;
                });
            }

            if (!params.id) {
                $this.data('googleMarker').push(marker);
            } else {
                $this.data('googleMarker')[params.id] = marker;
            }

            if ($this.data('googleMarker').length == 1) {
                $this.data('googleMap').setCenter(new google.maps.LatLng(params.coords[0], params.coords[1]));
                $this.data('googleMap').setZoom($this.data('googleMap').getZoom());
            } else {
                $this.data('googleMap').fitBounds($this.data('googleBound'));
            }

            params.success({
                lat: params.coords[0],
                lon: params.coords[1]
            });
        });

        return this;
    }

    $.fn.AVCA_RemoveMapMarker = function(id) {
        this.each(function() {
            var $this = $(this);

            if (!$this.data('googleMap')) {
                if ($this.data('googleDebug'))
                    console.log("jQuery googleMap : Unable to delete a marker where there is no map !");
                return false;
            }

            var $markers = $this.data('googleMarker');

            if (typeof $markers[id] != 'undefined') {
                $markers[id].setMap(null);
                if ($this.data('googleDebug'))
                    console.log('jQuery googleMap : marker deleted');
            } else {
                if ($this.data('googleDebug'))
                    console.error("jQuery googleMap : Unable to delete a marker if it not exists !");
                return false;
            }
        });
    }

    $.fn.AVCA_AddMapRoute = function(settings) {
        var params = $.extend({
            start: false,
            end: false,
            step: [],
            route: false,
            langage: 'english'
        }, settings);

        var direction = new google.maps.DirectionsService({
            region: "us"
        });

        var way = new google.maps.DirectionsRenderer({
            draggable: true,
            map: $(this).data('googleMap'),
            panel: document.getElementById(params.route),
            provideTripAlternatives: true
        });

        var waypoints = [];

        for (var i in params.step) {
            var step;
            if (typeof params.step[i] == "object") {
                step = new google.maps.LatLng(params.step[i][0], params.step[i][1]);
            } else {
                step = params.step[i];
            }

            waypoints.push({
                location: step,
                stopover: true
            });
        }

        if (typeof params.end != "object") {
            var geocoder = new google.maps.Geocoder();

            geocoder.geocode({
                address: params.end,
                bounds: $(this).data('googleBound'),
                language: params.langage
            }, function(results, status) {
                if (status == google.maps.GeocoderStatus.OK) {

                    var request = {
                        origin: params.start,
                        destination: results[0].geometry.location,
                        travelMode: google.maps.DirectionsTravelMode.DRIVING,
                        region: "us",
                        waypoints: waypoints
                    };

                    direction.route(request, function(response, status) {
                        if (status == google.maps.DirectionsStatus.OK) {
                            way.setDirections(response);
                        } else {
                            if ($(this).data('googleDebug'))
                                console.error("jQuery googleMap : Unable to find the place asked for the route (" + response + ")");
                        }
                    });

                } else {
                    if ($(this).data('googleDebug'))
                        console.error("jQuery googleMap : Address not found");
                }
            });
        } else {
            var request = {
                origin: params.start,
                destination: new google.maps.LatLng(params.end[0], params.end[1]),
                travelMode: google.maps.DirectionsTravelMode.DRIVING,
                region: "us",
                waypoints: waypoints
            };

            direction.route(request, function(response, status) {
                if (status == google.maps.DirectionsStatus.OK) {
                    way.setDirections(response);
                } else {
                    if ($(this).data('googleDebug'))
                        console.error("jQuery googleMap : Address not found");
                }
            });
        }

        return this;
    }
})(jQuery);