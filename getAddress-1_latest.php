<?php

/**
* Get the textual address of a latitude and longitude
* 
* @param lat
* @param lng
*/
function getaddress($lat, $lng)
{
    $url = 'http://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($lat).','.trim($lng).'&sensor=false';
//echo $url;
    $json = @file_get_contents($url);
    $data = json_decode($json);
    $status = $data->status;
    if($status == "OK") {
        return $data->results[0]->formatted_address;
    }
    return false;
}


/**
* Get the nearest hopitals to a particular latitude and longitude
* ordered near to far.
* 
* @param lat
* @param lng
* @param radius
*/
function getNearbyHospitals($lat, $lng, $radius = 500) 
{
    $url = 'https://maps.googleapis.com/maps/api/place/nearbysearch/json?location='.trim($lat).','.trim($lng).'&sensor=false&types=hospital&rankby=distance&key=AIzaSyDwFJMzYD_WZbNGylhuOJAcerGZ_LBdEQo';
    $json = @file_get_contents($url);
    $data = json_decode($json);

    $status = $data->status;
    if($status == "ZERO_RESULTS") {
        return getNearbyHospitals($lat, $lng, $radius+500);
    }
    
    $msg = '';
    $i = 1;
    foreach ($data->results as $hospital) {
        if(preg_match('#\b([Hh]ospital)\b#', $hospital->name)) {
            $msg .= "\n$hospital->name";
            if($i++ == 5) {
                break;
            }
        }
    }
    return $msg;
}


if(isset($_REQUEST) && $_REQUEST['lat'] != null && $_REQUEST['lng'] != null) {
    $lat = $_REQUEST['lat'];
    $lng = $_REQUEST['lng'];
    $address = getaddress($lat,$lng);
    
    //if($address) {
        $msg = "Lat: $lat Lng: $lng\n$address";
        if($_REQUEST['type'] === 'accident_detection') {
            $hospitalName = getNearbyHospitals($lat,$lng);
            $msg .= "\nHospital:$hospitalName";
        }

        echo $msg;
    //} else {
       // echo "Address not found";
    //}
} else {
    echo "Invalid co-ordinates";
}
