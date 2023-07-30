<?php
// This code uses cURL to send a POST request to the Apple Push Notification service (APNs) with the necessary headers and payload. 
// It also generates a JWT token using the provided p8 certificate file, key ID, and team ID.

function push_notification($deviceToken,$title,$body,$extra=[],$development=false){
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

    if (getenv('PUSH_TEST_DEVICE_TOKEN')){
        $testPush = true;
        $title .= " (TEST)";
        $body .= " (" . substr($deviceToken,0,8) .")";
        $deviceToken = getenv('PUSH_TEST_DEVICE_TOKEN');
    }

    $APIHOST = "api.push.apple.com/";
    if (strpos($deviceToken,"DEV")==0){
        $development = true;
        $deviceToken = substr($deviceToken,3);
    }
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

