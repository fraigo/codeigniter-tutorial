<?php

use Aws\Sns\SnsClient; 
use Aws\Exception\AwsException;

function send_sms($phone,$message){
    
    
    /**
     * Sends a a text message (SMS message) directly to a phone number using Amazon SNS.
     *
     * This code expects that you have AWS credentials set up per:
     * https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/guide_credentials.html
     */
     
    $SnSclient = new SnsClient([
        // 'profile' => 'sns',
        'region' => 'us-east-1',
        'version' => '2010-03-31'
    ]);
    
    try {
        $result = $SnSclient->publish([
            'Message' => $message,
            'PhoneNumber' => $phone,
        ]);
       return [
            "success"=>true,
            "result"=>$result
        ];
    } catch (AwsException $e) {
        return [
            "success"=>false,
            "error"=>$e->getMessage()
        ];
    }
}

