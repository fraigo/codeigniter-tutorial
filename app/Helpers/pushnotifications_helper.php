<?php
// This code uses cURL to send a POST request to the Apple Push Notification service (APNs) with the necessary headers and payload. 
// It also generates a JWT token using the provided p8 certificate file, key ID, and team ID.

function push_notification($deviceToken,$title,$body,$badge=0,$extra=[],$development=false,$type='ios'){
    if (getenv('PUSH_TEST_DEVICE_TOKEN')){
        $title .= " (TEST)";
        $body .= " (" . substr($deviceToken,-8) .")";
        $deviceToken = getenv('PUSH_TEST_DEVICE_TOKEN');
    }
    if (strpos($deviceToken,"IOS")===0){
        $type = 'ios';
        $deviceToken = substr($deviceToken,3);
    }
    if (strpos($deviceToken,"ANDROID")===0){
        $type = 'android';
        $deviceToken = substr($deviceToken,7);
    }
    if (strpos($deviceToken,"DEV")===0){
        $development = true;
        $deviceToken = substr($deviceToken,3);
    }

    if ($type=='ios'){
        return ios_push_notification($deviceToken,$title,$body,$badge,$extra,$development);
    }
    if ($type=='android'){
        return android_push_notification($deviceToken,$title,$body,$badge,$extra,$development);
    }
    return [
        "success" => "true",
        "message" => "not implemented on $type"
    ];
}

function ios_push_notification($deviceToken,$title,$body,$badge=0,$extra=[],$development=false){
    // Set the path to your p8 certificate file
    if (!getenv('PUSH_P8_FILE')) return ["success"=>true,"message"=>"No certificate file"];
    $certificateFile = ROOTPATH . getenv('PUSH_P8_FILE');
    if (!file_exists($certificateFile)){
        return [
            "success" => false,
            "message" => "Certificate file not found ".basename($certificateFile)
        ];
    }
    // Set the key ID, team ID, and bundle ID
    $keyId = getenv('PUSH_IOS_KEY_ID');
    if (!$keyId) return ["success"=>false,"message"=>"PUSH_IOS_KEY_ID not set"];
    $teamId = getenv('PUSH_IOS_TEAM_ID');
    if (!$teamId) return ["success"=>false,"message"=>"PUSH_IOS_TEAM_ID not set"];
    $bundleId = getenv('PUSH_IOS_BUNDLE_ID');
    if (!$bundleId) return ["success"=>false,"message"=>"PUSH_IOS_BUNDLE_ID not set"];

    $APIHOST = "api.push.apple.com";
    if ($development){
        $APIHOST = "api.sandbox.push.apple.com";
    }

    // Set the notification payload
    $payload = [
        'aps' => [
            'alert' => [
                'title' => $title,
                'body' => $body,
            ],
            "content-available" => 1,
            "sound"  => "",
            "badge" => $badge ?: 0
        ],
    ];
    if ($extra){
        foreach($extra as $key=>$value){
            $payload['aps'][$key] = $value;
        }
    }


    // Encode the payload as JSON
    $payloadJson = json_encode($payload);

    // Set the endpoint URL
    $url = "https://$APIHOST/3/device/$deviceToken";

    // Set the request headers
    $headers = [
        'Authorization: bearer ' . generateJwtToken($certificateFile, $keyId, $teamId),
        'Content-Type: application/json',
        "apns-push-type: alert",
        "apns-topic: ".$bundleId,
    ];

    // Create a new cURL resource
    $ch = curl_init();

    // Set the cURL options
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_PORT, 443);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_ENCODING, '');
    curl_setopt($ch, CURLOPT_MAXREDIRS, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payloadJson);
    //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2_0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Execute the cURL request
    $response = curl_exec($ch);

    // Check for errors
    if ($response === false) {
        $error = curl_error($ch);
        // Close the cURL resource
        curl_close($ch);
        // Handle the error
        return [
            "success" => false,
            "message" => "cURL Error: $error",
            "url" => $url,
            "headers" => $headers,
            "payload" => $payloadJson,
        ];
    } else {
        // Close the cURL resource
        curl_close($ch);
        return [
            "success" => true,
            "response" => $response,
            "url" => $url,
            "headers" => $headers,
            "payload" => $payloadJson,
        ];
    }

}


function android_push_notification($deviceToken,$title,$body,$badge=0,$extra=[],$development=false){
    helper('gapi');
    $projectId = getenv('FIREBASE_PROJECT_ID') ?: 'firebase_project_id';
    $accessToken = fcm_access_token();
    $url = 'https://fcm.googleapis.com/v1/projects/' . $projectId . '/messages:send';

    $data = [];
    $data['title'] = $title;
    $data['body'] = $body;
    $data['payload'] = json_encode($extra);
    if (@$extra['link']) $data['link'] = $extra['link'];

    // Create the message payload
    $message = [
        'message' => [
            'token' => $deviceToken,  // Targeted device token
            'android' => [
                'priority' => 'high',
            ],
            'data' => $data,
        ]
    ];

    // Set up the HTTP headers
    $headers = [
        'Authorization: Bearer ' . $accessToken,  // OAuth2 access token
        'Content-Type: application/json',
    ];

    // Initialize CURL
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message));

    // Execute CURL request
    $result = curl_exec($ch);
    if ($result === FALSE) {
        $error = curl_error($ch);
        curl_close($ch);
        return [
            "success" => false,
            "device_token" => $deviceToken,
            "message" => "cURL Error: $error",
            "url" => $url,
            "headers" => $headers,
            "payload" => $message,
        ];
    } else {
        curl_close($ch);
        return [
            "success" => true,
            "device_token" => $deviceToken,
            "response" => $result,
            "url" => $url,
            "headers" => $headers,
            "payload" => $message,
        ];
    }
}

function android_push_notification_old($deviceToken,$title,$body,$badge=0,$extra=[],$development=false){
    // Set the server key and device token
    if (!getenv('PUSH_ANDROID_KEY')) return ["success"=>true,"message"=>"No server key"];

    $serverKey = getenv('PUSH_ANDROID_KEY');

    // Set the notification message
    $message = [
        'title' => $title,
        'body' => $body,
    ];
    if ($extra){
        foreach($extra as $key=>$value){
            $message[$key] = $value;
        }
    }

    // Set the notification payload
    $payload = [
        'to' => $deviceToken,
    ];

    //$payload['notification'] = $message;
    $payload['data'] = $message;

    // Encode the payload as JSON
    $payloadJson = json_encode($payload);

    // Set the endpoint URL
    $url = 'https://fcm.googleapis.com/fcm/send';

    // Set the request headers
    $headers = [
        'Authorization: key=' . $serverKey,
        'Content-Type: application/json',
    ];

    // Create a new cURL resource
    $ch = curl_init();

    // Set the cURL options
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payloadJson);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    // Execute the cURL request
    $response = curl_exec($ch);

    // Check for errors
    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);
        // Handle the error
        return [
            "success" => false,
            "message" => "cURL Error: $error",
            "url" => $url,
            "headers" => $headers,
            "payload" => $payloadJson,
        ];
    } else {
        // Handle the response
        curl_close($ch);
        return [
            "success" => true,
            "response" => $response,
            "url" => $url,
            "headers" => $headers,
            "payload" => $payloadJson,
        ];
    }
}

// Function to generate the JWT token
function generateJwtToken($certificateFile, $keyId, $teamId)
{
    $tokenFile = ROOTPATH . "writable/jwt_token.txt";
    if (file_exists($tokenFile)){
        $fileTime = filemtime($tokenFile);
        $maxT     = time() - 1800; // 30 min.
        $minT     = time() - 3600; // 60 min.
        if($fileTime > $minT && $fileTime < $maxT )
        {
            $jwtToken = file_get_contents($tokenFile);
            return $jwtToken;
        } 
    }

    $header = base64UrlEncode(json_encode([
        'alg' => 'ES256',
        'kid' => $keyId,
//        'typ' => 'JWT',
    ]));

    $time = time();
    $claims = base64UrlEncode(json_encode([
        'iss' => $teamId,
        'iat' => $time,
    ]));

    // $cmd = "printf \"$claims\" | openssl dgst -binary -sha256 -sign \"$certificateFile\" | openssl base64 -e -A | tr -- '+/' '-_' | tr -d =";
    // $encodedSignature = `$cmd`;
    
    $pkey = openssl_pkey_get_private("file://$certificateFile");
    $signature = '';
    openssl_sign("$header.$claims", $signature, $pkey, OPENSSL_ALGO_SHA256);
    openssl_free_key($pkey);
    $encodedSignature = base64UrlEncode($signature);

    $jwtToken = "$header.$claims.$encodedSignature";
    file_put_contents($tokenFile, $jwtToken);

    return $jwtToken;
}

// Function to base64 URL encode a string
function base64UrlEncode($data)
{
    $base64 = base64_encode($data);
    $base64Url = strtr($base64, '+/', '-_');
    $base64Url = rtrim($base64Url, '=');
    return $base64Url;
}

