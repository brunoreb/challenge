<?php
	$file = 'km.txt';
	$totalkm = file_get_contents($file);
	if($totalkm == "") {
	    $totalkm = 0;
	}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8' />
    <title></title>
    <meta name='viewport' content='initial-scale=1,maximum-scale=1,user-scalable=no' />
    <script src='https://api.tiles.mapbox.com/mapbox-gl-js/v1.10.1/mapbox-gl.js'></script>
    <link href='https://api.tiles.mapbox.com/mapbox-gl-js/v1.10.1/mapbox-gl.css' rel='stylesheet' />
    <script
            src="https://api.tiles.mapbox.com/mapbox.js/plugins/turf/v2.0.0/turf.min.js"
            charset="utf-8"
    ></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

    <style>
        body {
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        #map {
            position: relative;
            top: 0;
            bottom: 0;
            width: 100%;
            height: 800px;
            text-align: center!important;
        }
        #marker {
            background-image: url('marker.png');
            background-size: cover;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            cursor: pointer;
        }

        .mapboxgl-popup {
            max-width: 200px;
        }

        .responsiveimg {
          max-width: 100%;
          height: auto;
        }
    </style>
</head>
<body>
<div class="row">
    <div class="col text-center mb-1">
        <img src="header.jpg" class="responsiveimg">
    </div>
</div>
<div class="row">
    <div class="col text-center">
        <div id="map"></div>
    </div>
</div>
<script>

    /*
    https://api.mapbox.com/directions/v5/mapbox/driving/3.4388069189074537%2C43.62408939627776%3B3.86394%2C43.576061%3B3.800452%2C43.466173?alternatives=false&geometries=geojson&steps=false&access_token=pk.eyJ1Ijoibm9icnV4IiwiYSI6ImNqbXoyaDRuYTE3ODUza3FsbjVqNmZjeGUifQ.ZiVsjOx7jgEda1jznEeBUA
    */
    mapboxgl.accessToken = 'pk.eyJ1Ijoibm9icnV4IiwiYSI6ImNqbXoyaDRuYTE3ODUza3FsbjVqNmZjeGUifQ.ZiVsjOx7jgEda1jznEeBUA';
    var map = new mapboxgl.Map({
        container: 'map',
        style: 'mapbox://styles/mapbox/streets-v10',
        center: [6.127822,45.900026], // starting position
        zoom: 5
    });

    map.addControl(new mapboxgl.NavigationControl());
    var canvas = map.getCanvasContainer();
    var start = [6.127822,45.900026];
    var route;

    function addMarker(pLat, pLong , pDistance) {
        var popup = new mapboxgl.Popup({ offset: 25 }).setText(
            pDistance+ ' km parcourus'
        );

        var el = document.createElement('div');
        el.id = 'marker';

        var markr = new mapboxgl.Marker(el)
            .setLngLat([pLat, pLong])
            .setPopup(popup) // sets a popup on this marker
            .addTo(map)
            .togglePopup();

        /*var marker = new mapboxgl.Marker()
            .setLngLat([pLat, pLong])
            .addTo(map);*/
    }

    function getRoute(end) {
        var start = [6.127822,45.900026];
        var url = 'https://api.mapbox.com/directions/v5/mapbox/driving/6.127822%2C45.900026%3B5.066299%2C47.322268%3B7.734054%2C48.576372%3B6.192091%2C48.687013%3B4.015424%2C49.229013%3B3.03702%2C50.611911%3B1.111598%2C49.438808%3B-0.356648%2C49.205727%3B-4.486606%2C48.392479%3B-1.578501%2C47.228237%3B0.385495%2C46.559104%3B-0.604375%2C44.830764%3B1.437794%2C43.599678%3B3.849075%2C43.631439%3B5.401651%2C43.298909%3B7.283044%2C43.724534%3B6.869947%2C45.923774?alternatives=false&geometries=geojson&steps=false&access_token=' + mapboxgl.accessToken;
        var req = new XMLHttpRequest();
        req.open('GET', url, true);
        req.onload = function() {
            var json = JSON.parse(req.response);
            var data = json.routes[0];
            route = data.geometry.coordinates;
            var geojson = {
                type: 'Feature',
                properties: {},
                geometry: {
                    type: 'LineString',
                    coordinates: route
                }
            };

                var baproute = map.addLayer({
                    id: 'route',
                    type: 'line',
                    source: {
                        type: 'geojson',
                        data: geojson
                    },
                    layout: {
                        'line-join': 'round',
                        'line-cap': 'round'
                    },
                    paint: {
                        'line-color': '#3887be',
                        'line-width': 5,
                        'line-opacity': 0.75
                    }
                });
            counter = 0;
            var distance = <?php echo $totalkm; ?>;//747;
            var along = turf.along(geojson, distance , 'kilometers');
            addMarker(along.geometry.coordinates[0],along.geometry.coordinates[1],distance);

            var markerdebut = new mapboxgl.Marker()
                        .setLngLat(route[0])
                        .addTo(map);

            var markerfin = new mapboxgl.Marker()
                        .setLngLat(route[route.length -1])
                        .addTo(map)

        };
        req.send();
    }

    map.on('load', function() {
        getRoute(start);
    });
</script>

</body>
</html>
