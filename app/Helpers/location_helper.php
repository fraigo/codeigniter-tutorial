<?php

function get_location($ip=null,$cache=true){

    $ip = $ip ?: $_SERVER["REMOTE_ADDR"];
    $cachePath = ROOTPATH."/writable/logs/location-$ip.json";
    if ($cache && file_exists($cachePath)){
        $locationInfo = file_get_contents($cachePath);
        $locationData = json_decode($locationInfo, true);
        $locationData['created'] = date("Y-m-d H:i:s",filemtime($cachePath));
        return $locationData;
    }
    if (getenv('IPINFO_TOKEN')){
        $requestURI = "https://ipinfo.io/$ip?token=".getenv('IPINFO_TOKEN');
        $locationInfo = @file_get_contents($requestURI);
        if ($locationInfo){
            $locationData = json_decode($locationInfo, true);
            if ($locationData){
                $latlng = explode(',',@$locationData['loc']?:',');
                $locationData['lat'] = $latlng[0]*1.0;
                $locationData['lng'] = $latlng[1]*1.0;
                file_put_contents($cachePath,json_encode($locationData));
                return $locationData;
            }
        }
    }
    return null;
}

