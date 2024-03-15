#Send a Push Notification Using a Token

TEAM_ID=8SUQQ8935B
TOKEN_KEY_FILE_NAME=writable/certificate.p8
AUTH_KEY_ID=TB29CC5FKN
TOPIC=io.fraigo.appname
DEVICE_TOKEN=$1
APNS_HOST_NAME=api.sandbox.push.apple.com

#Test that you can connect to APNs using this command:
#openssl s_client -connect "${APNS_HOST_NAME}":443

#Set these additional shell variables just before sending a push notification:

JWT_ISSUE_TIME=$(date +%s)
JWT_HEADER=$(printf '{"alg":"ES256","kid":"%s"}' "${AUTH_KEY_ID}" | openssl base64 -e -A | tr -- '+/' '-_' | tr -d =)
JWT_CLAIMS=$(printf '{"iss":"%s","iat":%d}' "${TEAM_ID}" "${JWT_ISSUE_TIME}" | openssl base64 -e -A | tr -- '+/' '-_' | tr -d =)
JWT_HEADER_CLAIMS="${JWT_HEADER}.${JWT_CLAIMS}"
JWT_SIGNED_HEADER_CLAIMS=$(printf "${JWT_HEADER_CLAIMS}" | openssl dgst -binary -sha256 -sign "${TOKEN_KEY_FILE_NAME}" | openssl base64 -e -A | tr -- '+/' '-_' | tr -d =)
AUTHENTICATION_TOKEN="${JWT_HEADER}.${JWT_CLAIMS}.${JWT_SIGNED_HEADER_CLAIMS}"
echo Time:$JWT_ISSUE_TIME Token:$AUTHENTICATION_TOKEN
#Send the push notification using this command:

curl -v --header "apns-topic: $TOPIC" --header "apns-push-type: alert" --header "authorization: bearer $AUTHENTICATION_TOKEN" --data '{"aps":{"alert":"[Test]'$2'"}}' --http2 https://${APNS_HOST_NAME}/3/device/${DEVICE_TOKEN}

#The result is an HTTP status of 200 (request succeeded). A notification with the text “test” appears on your destination device.