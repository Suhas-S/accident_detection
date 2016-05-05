<?php
require('wp-load.php');

function getaddress($lat, $lng)
{
    $url = 'http://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($lat).','.trim($lng).'&sensor=false';
    $json = @file_get_contents($url);
    $data = json_decode($json);
    $status = $data->status;
    if($status == "OK") {
        return $data->results[0]->formatted_address;
    }
    return false;
}

function getNearbyHospitals($lat, $lng, $radius = 500) 
{
    $url = 'https://maps.googleapis.com/maps/api/place/nearbysearch/json?location='.trim($lat).','.trim($lng).'&sensor=false&types=hospital&key=AIzaSyDwFJMzYD_WZbNGylhuOJAcerGZ_LBdEQo&radius=' . $radius;
    $json = @file_get_contents($url);
    $data = json_decode($json);
    $status = $data->status;
    if($status == "ZERO_RESULTS") {
        return getNearbyHospitals($lat, $lng, $radius+500);
    }
    return $data->results[0]->name;
}

mail('eon@earncef.com', 'Accident', "First test message");
if(isset($_REQUEST) && $_REQUEST['lat'] != null && $_REQUEST['lng'] != null) {
    $lat = $_REQUEST['lat']; //latitude
    $lng = $_REQUEST['lng']; //longitude
    $address = getaddress($lat,$lng);
    $hospitalName = getNearbyHospitals($lat,$lng);
    if($address) {
        $msg = "Lat: $lat Lng: $lng\n$address\nHospital:$hospitalName";
        wp_mail('eon@earncef.com', 'Text', $msg);
        echo $msg;
    } else {
        echo "Address not found";
    }
} else {
    echo "Invalid co-ordinates";
}
