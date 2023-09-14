<?php


function apple_signin($redirect, $code){

    if (!getenv('APPLE_SIGNIN_P8_FILE')) return ["success"=>true,"message"=>"No certificate file"];
    $certificateFile = ROOTPATH . getenv('APPLE_SIGNIN_P8_FILE');
    $keyId = getenv('APPLE_SIGNIN_KEY_ID');
    $teamId = getenv('APPLE_SIGNIN_TEAM_ID');
    $clientId = getenv('APPLE_SIGNIN_CLIENT_ID');

    $data = $_GET;
    // Create a new cURL resource
    $ch = curl_init();

    $url = "https://appleid.apple.com/auth/token";
    $headers = [
        'Content-Type' => 'application/x-www-form-urlencoded'
    ];
    $clientSecret = apple_client_secret($certificateFile,$keyId,$teamId,$clientId);
    $requestBody = [
        'grant_type' => 'authorization_code',
        'code' => $code,
        'redirect_uri' => $redirect,
        'client_id' => $clientId,
        'client_secret' => $clientSecret,
        'scope' => getenv('APPLE_SIGNIN_SCOPE') ?: 'name email',
    ];
    
    // Set the cURL options
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($requestBody));
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
            "message" => "Connection Error: $error",
            "url" => $url,
            "headers" => $headers,
            "payload" => $requestBody,
        ];
    } else {
        // Handle the response
        curl_close($ch);
        $response_data = json_decode($response,true);
        $token_data = explode(".",$response_data['id_token']);
        foreach($token_data as $idx=>$part){
            $token_data[$idx] = json_decode(base64_decode($part),true);
        }
        return [
            "success" => true,
            "response" => $response,
            "access_token" => $response_data['access_token'],
            "token_data" => $token_data,
            "url" => $url,
            "headers" => $headers,
            "payload" => $requestBody,
            "postdata" => http_build_query($requestBody),
        ];
    }

}


function apple_client_secret($certificateFile,$keyId,$teamId,$clientId){
    $signature = '';
    $time = time();
    $keyContent = file_get_contents($certificateFile);
    $data = [
        'iss' => $teamId,
        'iat' => $time,
	    'exp' => $time + 86400 * 180,
        'aud' => 'https://appleid.apple.com',
        'sub' => $clientId,
    ];
    return \Firebase\JWT\JWT::encode($data, $keyContent, 'ES256', $keyId);
    //echo "$keyId,$teamId,$clientId,$certificateFile";
}

function der_unpack($der){
    // DER unpacking from https://github.com/firebase/php-jwt
    $components = [];
    $pos = 0;
    $size = strlen($der);
    while ($pos < $size) {
        $constructed = (ord($der[$pos]) >> 5) & 0x01;
        $type = ord($der[$pos++]) & 0x1f;
        $len = ord($der[$pos++]);
        if ($len & 0x80) {
            $n = $len & 0x1f;
            $len = 0;
            while ($n-- && $pos < $size) $len = ($len << 8) | ord($der[$pos++]);
        }

        if ($type == 0x03) {
            $pos++;
            $components[] = substr($der, $pos, $len - 1);
            $pos += $len - 1;
        } else if (!$constructed) {
            $components[] = substr($der, $pos, $len);
            $pos += $len;
        }
    }
    foreach ($components as &$c) $c = str_pad(ltrim($c, "\x00"), 32, "\x00", STR_PAD_LEFT);
    return implode('', $components);
}

function apple_base64UrlEncode($data)
{
    $base64 = base64_encode($data);
    $base64Url = strtr($base64, '+/', '-_');
    $base64Url = rtrim($base64Url, '=');
    return $base64Url;
}