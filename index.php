<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thailand map</title>
    <link rel="stylesheet" href="https://openlayers.org/en/v4.6.5/css/ol.css" type="text/css" />
    <script src="https://openlayers.org/en/v4.6.5/build/ol.js" type="text/javascript"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js" type="text/javascript"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css"
        integrity="sha512-YWzhKL2whUzgiheMoBFwW8CKV4qpHQAEuvilg9FAn5VJUDwKZZxkJNuGM4XkWuk94WCrrwslk8yWNGmY1EduTA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="style.css">
    <style>
    .ol-popup {
        position: absolute;
        background-color: white;
        -webkit-filter: drop-shadow(0 1px 4px rgba(0, 0, 0, 0.2));
        filter: drop-shadow(0 1px 4px rgba(0, 0, 0, 0.2));
        padding: 15px;
        border-radius: 10px;
        border: 1px solid #cccccc;
        bottom: 12px;
        left: -50px;
        min-width: 180px;
    }

    .ol-popup:after,
    .ol-popup:before {
        top: 100%;
        border: solid transparent;
        content: " ";
        height: 0;
        width: 0;
        position: absolute;
        pointer-events: none;
    }

    .ol-popup:after {
        border-top-color: white;
        border-width: 10px;
        left: 48px;
        margin-left: -10px;
    }

    .ol-popup:before {
        border-top-color: #cccccc;
        border-width: 11px;
        left: 48px;
        margin-left: -11px;
    }

    .ol-popup-closer {
        text-decoration: none;
        position: absolute;
        top: 2px;
        right: 8px;
    }
    </style>
</head>

<body onload="initialize_map();">
    <table>
        <tr>
            <td>
                <div class="map" id="map">
                    <div class="search">
                        <div class="list">
                            <i class="fas fa-bars"></i>
                            <div class="option">
                                <div class="option_group">
                                    <div class="option_group_checbox">
                                        <input onclick="oncheckprovince()" type="checkbox" id="province" name="province"
                                            value="province">
                                        <label for="province">Province</label>
                                    </div>
                                    <div class="option_group_checbox">
                                        <input onclick="oncheckdistrict()" type="checkbox" id="district" name="district"
                                            value="district">
                                        <label for="district">District</label>
                                    </div>
                                    <div class="option_group_checbox">
                                        <input onclick="oncheckrail();" type="checkbox" id="rail" name="rail"
                                            value="rail">Rail <br />
                                    </div>
                                    <div class="option_group_checbox">
                                        <input onclick="oncheckwaterway();" type="checkbox" id="waterway"
                                            name="waterway" value="waterway">Waterway <br />
                                    </div>
                                    <div class="option_group_checbox">
                                        <input onclick="oncheckroad();" type="checkbox" id="road"
                                            name="road" value="road">Road <br />
                                    </div>
                                </div>
                                <div class="option_load">
                                    <button class="button_load_map" id="button_load_map">Load</button>
                                </div>
                            </div>
                        </div>
                        <input type="text" class="input_search" id="input_search" placeholder="Nhập tìm kiếm...">
                        <label for="checksearch" class="btn_search" id="btn_search"><i class="fas fa-search"></i>
                        </label>
                        <input type="checkbox" hidden id="checksearch" class="checksearch">
                        <div class="detail_search">
                            <div class="detait_header">
                                <span>Kết quả tìm kiếm: </span>
                                <label for="checksearch" class="detail_icon-close"><i class="fas fa-times"></i></label>
                            </div>
                            <div class="detail_body" id="detail_body">

                            </div>
                        </div>
                    </div>
                </div>
                <div id="popup" class="ol-popup">
                    <a href="#" id="popup-closer" class="ol-popup-closer"></a>
                    <div id="popup-content"></div>
                </div>
            </td>
            <td>
                <div id="info"></div>
            </td>
        </tr>
    </table>
    <?php  include "pgsqlAPI.php" ?>
    <script>
    var format = 'image/png';
    var map;
    var minX = 97.3451919555664;
    var minY = 5.616042137146;
    var maxX = 105.639137268066;
    var maxY = 20.4632129669189;
    var cenX = (minX + maxX) / 2;
    var cenY = (minY + maxY) / 2;
    var mapLat = cenY;
    var mapLng = cenX;
    var mapDefaultZoom = 6;
    var input_search = document.getElementById("input_search");
    var container = document.getElementById('popup');
    var content = document.getElementById('popup-content');
    var closer = document.getElementById('popup-closer');
    var vectorLayer;
    var layerprovince;
    var layerdistrict;
    var layer_rail;
    var layer_waterway;
    var layer_road;
    var value = '';

    // start add hight light map -----------------------------------------            
    var styles = {
        'Point': new ol.style.Style({
            stroke: new ol.style.Stroke({
                color: 'yellow',
                width: 3
            })
        }),
        'MultiLineString': new ol.style.Style({

            stroke: new ol.style.Stroke({
                color: 'red',
                width: 3
            })
        }),
        'Polygon': new ol.style.Style({
            stroke: new ol.style.Stroke({
                color: 'red',
                width: 3
            })
        }),
        'MultiPolygon': new ol.style.Style({
            fill: new ol.style.Fill({
                color: 'orange'
            }),
            stroke: new ol.style.Stroke({
                color: 'yellow',
                width: 2
            })
        })
    };
    var styleFunction = function(feature) {
        return styles[feature.getGeometry().getType()];
    };
    vectorLayer = new ol.layer.Vector({
        //source: vectorSource,
        style: styleFunction
    });
    // end add highlight map------------------------------------


    // start add map------------------------------------------------
    function handleOnCheck(id, layer) {
        if (document.getElementById(id).checked) {
            value = document.getElementById(id).value;

            // map.setLayerGroup(new ol.layer.Group())
            map.addLayer(layer)
            // vectorLayer = new ol.layer.Vector({});
            map.addLayer(vectorLayer);
        } else {
            map.removeLayer(layer);
            map.removeLayer(vectorLayer);
        }
    }


    function oncheckprovince() {
        handleOnCheck('province', layerprovince);
    }

    function oncheckdistrict() {
        handleOnCheck('district', layerdistrict);
    }

    function oncheckrail() {
        handleOnCheck('rail', layer_rail);
    }

    function oncheckwaterway() {
        handleOnCheck('waterway', layer_waterway);
    }

    function oncheckroad() {
        handleOnCheck('road', layer_road);
    }

    function initialize_map() {

        layerBG = new ol.layer.Tile({
            source: new ol.source.OSM({})
        });

        layerprovince = new ol.layer.Image({
            source: new ol.source.ImageWMS({
                ratio: 1,
                url: 'http://localhost:8080/geoserver/example/wms?',
                params: {
                    'FORMAT': format,
                    'VERSION': '1.1.1',
                    STYLES: '',
                    LAYERS: 'gadm36_tha_1',
                }
            })

        });
        layerdistrict = new ol.layer.Image({
            source: new ol.source.ImageWMS({
                ratio: 1,
                url: 'http://localhost:8080/geoserver/example/wms?',
                params: {
                    'FORMAT': format,
                    'VERSION': '1.1.1',
                    STYLES: '',
                    LAYERS: 'gadm36_tha_2',
                }
            })

        });
        layer_rail = new ol.layer.Image({
            source: new ol.source.ImageWMS({
                ratio: 1,
                url: 'http://localhost:8080/geoserver/example/wms?',
                params: {
                    'FORMAT': format,
                    'VERSION': '1.1.1',
                    STYLES: '',
                    LAYERS: 'gis_osm_railways_free_1',
                }
            })
        });
        layer_road = new ol.layer.Image({
            source: new ol.source.ImageWMS({
                ratio: 1,
                url: 'http://localhost:8080/geoserver/example/wms?',
                params: {
                    'FORMAT': format,
                    'VERSION': '1.1.1',
                    STYLES: '',
                    LAYERS: '	gis_osm_roads_free_1',
                }
            })
        });

        layer_waterway = new ol.layer.Image({
            source: new ol.source.ImageWMS({
                ratio: 1,
                url: 'http://localhost:8080/geoserver/example/wms?',
                params: {
                    'FORMAT': format,
                    'VERSION': '1.1.1',
                    STYLES: '',
                    LAYERS: 'gis_osm_waterways_free_1',
                }
            })
        });
        var overlay = new ol.Overlay( /** @type {olx.OverlayOptions} */ ({
            element: container,
            autoPan: true,
            autoPanAnimation: {
                duration: 250
            }
        }));
        closer.onclick = function() {
            overlay.setPosition(undefined);
            closer.blur();
            return false;
        };

        var viewMap = new ol.View({
            center: ol.proj.fromLonLat([mapLng, mapLat]),
            zoom: mapDefaultZoom
        });
        map = new ol.Map({
            target: "map",
            layers: [layerBG],
            view: viewMap,
            overlays: [overlay], //them khai bao overlays
        });

        function displayObjInfo(result, coordinate) {
            $("#popup-content").html(result);
            overlay.setPosition(coordinate);

        }


        // end add map-----------------------------------------------------------------
        document.getElementById("button_load_map").addEventListener("click", () => {
            location.reload();
        })



        // start event --------------------------------------------------------------------
        map.on('singleclick', function(evt) {
            var lonlat = ol.proj.transform(evt.coordinate, 'EPSG:3857', 'EPSG:4326');
            var lon = lonlat[0];
            var lat = lonlat[1];
            var myPoint = 'POINT(' + lon + ' ' + lat + ')';

            if (value == "province") { //Là map province
                $.ajax({
                    type: "POST",
                    url: "pgsqlAPI.php",
                    //dataType: 'json',

                    data: {
                        functionname: 'getGeoProvinceToAjax',
                        paPoint: myPoint
                    },
                    success: function(result, status, erro) {
                        highLightObj(result);

                    },
                    error: function(req, status, error) {
                        alert(req + " " + status + " " + error);
                    }
                });
            }
            if (value == "district") { //Là map province
                $.ajax({
                    type: "POST",
                    url: "pgsqlAPI.php",
                    //dataType: 'json',

                    data: {
                        functionname: 'getGeoDistricToAjax',
                        paPoint: myPoint
                    },
                    success: function(result, status, erro) {
                        highLightObj(result);

                    },
                    error: function(req, status, error) {
                        alert(req + " " + status + " " + error);
                    }
                });
            }
            if (value == "rail") {
                $.ajax({
                    type: "POST",
                    url: "pgsqlAPI.php",
                    //dataType: 'json',
                    //data: {functionname: 'reponseGeoToAjax', paPoint: myPoint},
                    data: {
                        functionname: 'getInfoRailToAjax',
                        paPoint: myPoint
                    },
                    success: function(result, status, erro) {
                        displayObjInfo(result, evt.coordinate);
                    },
                    error: function(req, status, error) {
                        alert(req + " " + status + " " + error);
                    }
                });
                $.ajax({
                    type: "POST",
                    url: "pgsqlAPI.php",
                    data: {
                        functionname: 'getRailToAjax',
                        paPoint: myPoint
                    },
                    success: function(result, status, erro) {
                        highLightObj(result);
                    },
                    error: function(req, status, error) {
                        alert(req + " " + status + " " + error);
                    }
                });
            }

            if (value == "waterway") {
                $.ajax({
                    type: "POST",
                    url: "pgsqlAPI.php",
                    //dataType: 'json',
                    //data: {functionname: 'reponseGeoToAjax', paPoint: myPoint},
                    data: {
                        functionname: 'getInfoWaterWayToAjax',
                        paPoint: myPoint
                    },
                    success: function(result, status, erro) {
                        displayObjInfo(result, evt.coordinate);
                    },
                    error: function(req, status, error) {
                        alert(req + " " + status + " " + error);
                    }
                });
                $.ajax({
                    type: "POST",
                    url: "pgsqlAPI.php",
                    data: {
                        functionname: 'getWaterWayToAjax',
                        paPoint: myPoint
                    },
                    success: function(result, status, erro) {
                        highLightObj(result);
                    },
                    error: function(req, status, error) {
                        alert(req + " " + status + " " + error);
                    }
                });
            }

            if (value == "road") {
                $.ajax({
                    type: "POST",
                    url: "pgsqlAPI.php",
                    //dataType: 'json',
                    //data: {functionname: 'reponseGeoToAjax', paPoint: myPoint},
                    data: {
                        functionname: 'getInfoRoadToAjax',
                        paPoint: myPoint
                    },
                    success: function(result, status, erro) {
                        displayObjInfo(result, evt.coordinate);
                    },
                    error: function(req, status, error) {
                        alert(req + " " + status + " " + error);
                    }
                });
                $.ajax({
                    type: "POST",
                    url: "pgsqlAPI.php",
                    data: {
                        functionname: 'getRoadToAjax',
                        paPoint: myPoint
                    },
                    success: function(result, status, erro) {
                        highLightObj(result);
                    },
                    error: function(req, status, error) {
                        alert(req + " " + status + " " + error);
                    }
                });
            }


        });
        document.getElementById("btn_search").addEventListener("click", () => {
            values_search = input_search.value;
            if (value == '') {
                showinfo("Vui lòng chọn bản đồ!");
            }
            if (value == "province") { //Là map province
                $.ajax({
                    type: "POST",
                    url: "pgsqlAPI.php",
                    //dataType: 'json',
                    data: {
                        name_search: 'getGeoProvinceToAjax',
                        values_search: values_search
                    },

                    success: function(result, status, erro) {
                        if (result != 'null') {
                            highLightObj(result);
                        } else {
                            showinfo("Không tìm thấy dữ liệu!");
                        }

                    },
                    error: function(req, status, error) {
                        alert(req + " " + status + " " + error);
                    }
                });
                $.ajax({
                    type: "POST",
                    url: "pgsqlAPI.php",
                    //dataType: 'json',
                    data: {
                        name_search: 'getInforProvinceToAjax',
                        values_search: values_search
                    },

                    success: function(result, status, erro) {
                        if (result != 'null') {
                            showinfo(result);
                        } else {
                            showinfo("Không tìm thấy dữ liệu!");
                        }

                    },
                    error: function(req, status, error) {
                        alert(req + " " + status + " " + error);
                    }
                });

            }
            if (value == "district") {
                $.ajax({
                    type: "POST",
                    url: "pgsqlAPI.php",
                    //dataType: 'json',
                    data: {
                        name_search: 'getGeoDistricToAjax',
                        values_search: values_search
                    },

                    success: function(result, status, erro) {
                        if (result != 'null') {
                            highLightObj(result);
                        } else {
                            showinfo("Không tìm thấy dữ liệu");
                        }

                    },
                    error: function(req, status, error) {
                        alert(req + " " + status + " " + error);
                    }
                });
                $.ajax({
                    type: "POST",
                    url: "pgsqlAPI.php",
                    //dataType: 'json',
                    data: {
                        name_search: 'getInforDistrictToAjax',
                        values_search: values_search
                    },

                    success: function(result, status, erro) {
                        if (result != 'null') {
                            showinfo(result);
                        } else {
                            showinfo("Không tìm thấy dữ liệu!");
                        }

                    },
                    error: function(req, status, error) {
                        alert(req + " " + status + " " + error);
                    }
                });
            }
        });


        // start edit json---------------------------------------------------
        function createJsonObj(result) {
            var geojsonObject = '{' +
                '"type": "FeatureCollection",' +
                '"crs": {' +
                '"type": "name",' +
                '"properties": {' +
                '"name": "EPSG:4326"' +
                '}' +
                '},' +
                '"features": [{' +
                '"type": "Feature",' +
                '"geometry": ' + result +
                '}]' +
                '}';
            return geojsonObject;
        }

        function highLightGeoJsonObj(paObjJson) {
            var vectorSource = new ol.source.Vector({
                features: (new ol.format.GeoJSON()).readFeatures(paObjJson, {
                    dataProjection: 'EPSG:4326',
                    featureProjection: 'EPSG:3857'
                })
            });
            vectorLayer.setSource(vectorSource);

        }

        function highLightObj(result) {
            // alert("result: " + result);
            var strObjJson = createJsonObj(result);
            // alert(strObjJson);
            var objJson = JSON.parse(strObjJson);
            // alert(JSON.stringify(objJson));
            // alert(objJson);
            //drawGeoJsonObj(objJson);
            highLightGeoJsonObj(objJson);

        }

        function showinfo(result) {
            $("#detail_body").html(result);
        }


        // end edit json file-----------------------------------------------------------

    };
    </script>
</body>

</html>