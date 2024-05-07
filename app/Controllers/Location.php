<?php

namespace App\Controllers;

class Location extends BaseController
{
    function location(){
        helper('location');
        $location = get_location();
        if ($location){
            return $this->JSONResponse($location,200);
        } else {
            return $this->JSONResponse([
               "message" => "Not Available" 
            ],204);
        }
    }

    function keys(){
        return $this->JSONResponse([
            "GOOGLE_MAPS_API_KEY" => getenv('GOOGLE_MAPS_API_KEY')?:''
        ],200);
    }

}