<?php
ini_set("max_execution_time",300);

$commands = [
    "test-daily" => "daily test",
    "daily" => "daily",
    "test-hourly" => "hourly test",
    "hourly" => "hourly",
    "test-cleanup" => "cleanup test",
    "cleanup" => "cleanup",
];
$cmd = @$commands[@$_GET['command']];
if (!@$cmd) {
    http_response_code(404);
    die();
}
chdir("..");
header("Content-type: text/plain");
$result = `php spark cron $cmd`;

echo $result;
die();