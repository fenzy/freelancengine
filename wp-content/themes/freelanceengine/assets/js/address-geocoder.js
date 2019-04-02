(function($) {

    $(function() {

        var map,
            latlng,
            marker,
            markersArray = [];

        // Setup the default map
        latlng = '';
        latlng = ('' != latlng) ? latlng.substring(1, latlng.length-1).split(', ') : [30, 20];
        latlng = new google.maps.LatLng(latlng[0], latlng[1]);

        if (document.getElementById('geocodepreview') != null) {
            map = new google.maps.Map(document.getElementById('geocodepreview'), {
                zoomcontrol: true,
                mapTypeControl: false,
                streetViewControl: false,
                zoom: 2,
                center: latlng,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            });
            map.setCenter(latlng);

        }

        clearMarkers = function() {
            for (var i = 0; i < markersArray.length; i++) {
                markersArray[i].setMap(null);
            }
            markersArray.length = 0;
        }

        $('#project_submit').on('click', function(e) {
            event.preventDefault();

            var address = $('#fre-address').val() + ',' + $('#fre-city').val() + ',' + $('#country_chosen span').text();

            var geocoder = new google.maps.Geocoder();

            geocoder.geocode( { 'address': address }, function(results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    $('#job_latlng').attr('value', results[0].geometry.location);
                }
                else {
                    $('#job_latlng').attr('value', latlng);
                }
                $('form.post').submit();
            });
            
        });

        // Trigger geocode
        // $(document).on('change', '#country', function() {
        //     var address = $('#fre-address').val() + ',' + $('#fre-city').val() + ',' + $('#country_chosen span').text();
        //     get_geocode(address);
        // });

        $('.project-title-col .secondary-color, .project-list-title .secondary-color').each(function() {
            var title = $(this).html();
            var url = $(this).attr('href');
            var position = $(this).next('.latlng').html();
            position = ('' != position) ? position.substring(1, position.length-1).split(', ') : [-30, 150];
            position = new google.maps.LatLng(position[0], position[1]);

            marker = new google.maps.Marker({
                map: map,
                position: position,
                title: title,
                url: url
            });

            google.maps.event.addListener(marker, 'click', (function(marker) {
                return function() {
                    window.open(marker.url);
                }
            })(marker));

        });

    });

})(jQuery);